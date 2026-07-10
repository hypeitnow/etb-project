<?php

namespace App\Services;

use App\Models\ThreeXThreeTournament;
use App\Models\ThreeXThreeTournamentGroup;
use App\Models\ThreeXThreeTournamentMatch;
use App\Models\ThreeXThreeTournamentTeam;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ThreeXThreeTournamentFlowService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function groupTable(ThreeXThreeTournamentGroup $group): array
    {
        $teams = $group->teams->values();
        $rows = $teams->mapWithKeys(fn (ThreeXThreeTournamentTeam $team) => [$team->id => [
            'team' => $team,
            'played' => 0,
            'wins' => 0,
            'losses' => 0,
            'points_for' => 0,
            'points_against' => 0,
            'point_diff' => 0,
            'fiba_points' => 0,
            'form' => [],
        ]]);

        foreach ($group->matches->where('stage', ThreeXThreeTournamentMatch::STAGE_GROUP)->sortBy('played_at') as $match) {
            if (! $match->hasResult() || ! $match->team_one_id || ! $match->team_two_id) {
                continue;
            }

            foreach ([
                [$match->team_one_id, $match->team_one_score, $match->team_two_score],
                [$match->team_two_id, $match->team_two_score, $match->team_one_score],
            ] as [$teamId, $for, $against]) {
                if (! isset($rows[$teamId])) {
                    continue;
                }

                $won = $for > $against;
                $rows[$teamId]['played']++;
                $rows[$teamId]['wins'] += $won ? 1 : 0;
                $rows[$teamId]['losses'] += $won ? 0 : 1;
                $rows[$teamId]['points_for'] += $for;
                $rows[$teamId]['points_against'] += $against;
                $rows[$teamId]['fiba_points'] += $won ? 2 : 1;
                $rows[$teamId]['form'][] = $won ? 'W' : 'L';
            }
        }

        return $rows
            ->map(function (array $row): array {
                $row['point_diff'] = $row['points_for'] - $row['points_against'];
                $row['form'] = array_slice($row['form'], -5);

                return $row;
            })
            ->sort(function (array $a, array $b): int {
                return [$b['fiba_points'], $b['wins'], $b['point_diff'], $b['points_for'], $a['team']->name]
                    <=> [$a['fiba_points'], $a['wins'], $a['point_diff'], $a['points_for'], $b['team']->name];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function tournamentTeamStats(string $teamName): array
    {
        $normalized = mb_strtolower(trim($teamName));
        $teams = ThreeXThreeTournamentTeam::query()
            ->with(['tournament', 'players', 'group'])
            ->whereRaw('LOWER(name) = ?', [$normalized])
            ->get();

        $teamIds = $teams->pluck('id');
        $matches = ThreeXThreeTournamentMatch::query()
            ->with(['tournament', 'group', 'teamOne.players', 'teamTwo.players'])
            ->whereIn('team_one_id', $teamIds)
            ->orWhereIn('team_two_id', $teamIds)
            ->orderByDesc('played_at')
            ->orderByDesc('id')
            ->get();

        $wins = $matches->filter(fn (ThreeXThreeTournamentMatch $match) => $teamIds->contains($match->winnerId()))->count();
        $losses = $matches->filter(fn (ThreeXThreeTournamentMatch $match) => $teamIds->contains($match->loserId()))->count();
        $finished = $wins + $losses;

        return [
            'teams' => $teams,
            'matches' => $matches,
            'wins' => $wins,
            'losses' => $losses,
            'win_rate' => $finished > 0 ? round(($wins / $finished) * 100, 1) : 0,
            'latest_roster' => $teams->sortByDesc(fn (ThreeXThreeTournamentTeam $team) => $team->tournament?->date)->first()?->players ?? collect(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function drawTournament(
        ThreeXThreeTournament $tournament,
        int $groupsCount,
        int $teamsPerGroup,
        int $qualifiersPerGroup,
        bool $generateGroupMatches = true,
        bool $generatePlayoff = true,
    ): array {
        $teams = $tournament->teams()->orderBy('name')->get();
        $minimumTeams = $groupsCount * min($teamsPerGroup, 1);

        if ($teams->count() < $minimumTeams) {
            throw ValidationException::withMessages([
                'groups_count' => 'Za mało drużyn do podziału na wskazaną liczbę grup.',
            ]);
        }

        $hasScoredMatches = $tournament->matches()
            ->where(fn ($query) => $query->whereNotNull('team_one_score')->orWhereNotNull('team_two_score'))
            ->exists();

        if ($hasScoredMatches) {
            throw ValidationException::withMessages([
                'groups_count' => 'Nie można losować od nowa po wpisaniu wyników. Usuń wyniki albo dopisz mecze ręcznie.',
            ]);
        }

        return DB::transaction(function () use ($tournament, $groupsCount, $teamsPerGroup, $qualifiersPerGroup, $teams, $generateGroupMatches, $generatePlayoff): array {
            $tournament->matches()->delete();
            $tournament->groups()->delete();

            $groups = collect(range(1, $groupsCount))->map(function (int $index) use ($tournament): ThreeXThreeTournamentGroup {
                return $tournament->groups()->create([
                    'name' => 'Grupa '.$this->groupLetter($index),
                    'sort_order' => $index,
                ]);
            });

            $drawnTeams = $teams->shuffle()->values();
            foreach ($drawnTeams as $index => $team) {
                $group = $groups[(int) floor($index / $teamsPerGroup)] ?? $groups[$index % $groups->count()];
                $team->update(['group_id' => $group->id]);
            }

            if ($generateGroupMatches) {
                $this->generateGroupMatches($groups);
            }

            if ($generatePlayoff) {
                $this->generatePlayoffSkeleton($tournament->fresh(['groups.teams']), $qualifiersPerGroup);
            }

            return [
                'groups' => $groups->count(),
                'teams' => $teams->count(),
                'matches' => $tournament->matches()->count(),
            ];
        });
    }

    public function refreshPlayoff(ThreeXThreeTournament $tournament, int $qualifiersPerGroup): void
    {
        DB::transaction(function () use ($tournament, $qualifiersPerGroup): void {
            $tournament->matches()
                ->where('stage', ThreeXThreeTournamentMatch::STAGE_PLAYOFF)
                ->whereNull('team_one_score')
                ->whereNull('team_two_score')
                ->delete();

            $this->generatePlayoffSkeleton($tournament->fresh(['groups.teams', 'groups.matches']), $qualifiersPerGroup);
        });
    }

    /**
     * @param  Collection<int, ThreeXThreeTournamentGroup>|EloquentCollection<int, ThreeXThreeTournamentGroup>  $groups
     */
    private function generateGroupMatches(Collection|EloquentCollection $groups): void
    {
        foreach ($groups as $group) {
            $teams = $group->teams()->orderBy('name')->get()->values();
            $position = 1;

            for ($i = 0; $i < $teams->count(); $i++) {
                for ($j = $i + 1; $j < $teams->count(); $j++) {
                    $group->matches()->create([
                        'three_x_three_tournament_id' => $group->three_x_three_tournament_id,
                        'stage' => ThreeXThreeTournamentMatch::STAGE_GROUP,
                        'team_one_id' => $teams[$i]->id,
                        'team_two_id' => $teams[$j]->id,
                        'sort_order' => $position++,
                    ]);
                }
            }
        }
    }

    private function generatePlayoffSkeleton(ThreeXThreeTournament $tournament, int $qualifiersPerGroup): void
    {
        $groups = $tournament->groups()->with(['teams', 'matches'])->orderBy('sort_order')->get();
        $slots = [];

        foreach ($groups as $group) {
            $standings = $this->groupTable($group);

            for ($place = 1; $place <= $qualifiersPerGroup; $place++) {
                $row = $standings[$place - 1] ?? null;
                $slots[] = [
                    'team_id' => $row['team']->id ?? null,
                    'label' => $place.$this->groupLetter((int) $group->sort_order ?: $groups->search($group) + 1),
                ];
            }
        }

        if (count($slots) < 2) {
            return;
        }

        $bracketSize = $this->nextPowerOfTwo(count($slots));
        while (count($slots) < $bracketSize) {
            $slots[] = ['team_id' => null, 'label' => 'Wolny los'];
        }

        $rounds = (int) log($bracketSize, 2);
        $firstRoundLabel = $this->roundLabel($bracketSize);
        $pairs = array_chunk($this->seedSlots($slots), 2);

        foreach ($pairs as $index => [$slotOne, $slotTwo]) {
            $tournament->matches()->create([
                'stage' => ThreeXThreeTournamentMatch::STAGE_PLAYOFF,
                'round_label' => $firstRoundLabel,
                'bracket_round_order' => 1,
                'bracket_position' => $index + 1,
                'team_one_id' => $slotOne['team_id'],
                'team_two_id' => $slotTwo['team_id'],
                'team_one_placeholder' => $slotOne['label'],
                'team_two_placeholder' => $slotTwo['label'],
                'sort_order' => 1000 + $index,
            ]);
        }

        for ($round = 2; $round <= $rounds; $round++) {
            $matchesInRound = $bracketSize / (2 ** $round);

            for ($position = 1; $position <= $matchesInRound; $position++) {
                $tournament->matches()->create([
                    'stage' => ThreeXThreeTournamentMatch::STAGE_PLAYOFF,
                    'round_label' => $this->roundLabel((int) ($bracketSize / (2 ** ($round - 1)))),
                    'bracket_round_order' => $round,
                    'bracket_position' => $position,
                    'team_one_placeholder' => 'W'.($round - 1).'-'.(($position * 2) - 1),
                    'team_two_placeholder' => 'W'.($round - 1).'-'.($position * 2),
                    'sort_order' => 1000 + ($round * 100) + $position,
                ]);
            }
        }
    }

    /**
     * @param  array<int, array{team_id: int|null, label: string}>  $slots
     * @return array<int, array{team_id: int|null, label: string}>
     */
    private function seedSlots(array $slots): array
    {
        $result = [];
        $left = 0;
        $right = count($slots) - 1;

        while ($left < $right) {
            $result[] = $slots[$left++];
            $result[] = $slots[$right--];
        }

        return $result;
    }

    private function nextPowerOfTwo(int $value): int
    {
        $power = 1;

        while ($power < $value) {
            $power *= 2;
        }

        return $power;
    }

    private function groupLetter(int $index): string
    {
        return chr(64 + max(1, min(26, $index)));
    }

    private function roundLabel(int $teamsInRound): string
    {
        return match ($teamsInRound) {
            2 => 'Finał',
            4 => 'Półfinał',
            8 => 'Ćwierćfinał',
            16 => '1/8 finalu',
            32 => '1/16 finalu',
            default => 'Faza pucharowa',
        };
    }
}
