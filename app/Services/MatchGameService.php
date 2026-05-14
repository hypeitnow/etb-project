<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\Opponent;
use App\Models\SportsHall;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MatchGameService
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data, ?UploadedFile $opponentLogo, ?UploadedFile $homeLogo): MatchGame
    {
        return MatchGame::query()->create($this->prepareData($data, $opponentLogo, $homeLogo));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(MatchGame $match, array $data, ?UploadedFile $opponentLogo, ?UploadedFile $homeLogo): MatchGame
    {
        $prepared = $this->prepareData($data, $opponentLogo, $homeLogo, $match);

        if ($opponentLogo && $match->opponent_logo) {
            Storage::disk('public')->delete($match->opponent_logo);
        }

        if ($homeLogo && $match->home_logo) {
            Storage::disk('public')->delete($match->home_logo);
        }

        $match->update($prepared);

        return $match;
    }

    public function delete(MatchGame $match): void
    {
        if ($match->opponent_logo) {
            Storage::disk('public')->delete($match->opponent_logo);
        }

        if ($match->home_logo) {
            Storage::disk('public')->delete($match->home_logo);
        }

        $match->delete();
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function prepareData(
        array $data,
        ?UploadedFile $opponentLogo,
        ?UploadedFile $homeLogo,
        ?MatchGame $match = null
    ): array {
        $status = $data['status'];
        $opponentName = trim((string) $data['opponent_name']);
        $locationName = trim((string) $data['location']);
        $opponent = $this->findOrCreateOpponent($opponentName);
        $sportsHall = $this->findOrCreateSportsHall($locationName);

        if ($opponentLogo) {
            if ($opponent->logo_path) {
                Storage::disk('public')->delete($opponent->logo_path);
            }

            $opponent->update([
                'logo_path' => $opponentLogo->store('opponents', 'public'),
            ]);
        }

        unset($data['opponent'], $data['opponent_logo'], $data['home_logo']);

        if ($status === MatchGame::STATUS_UPCOMING) {
            $data['our_score'] = null;
            $data['opponent_score'] = null;
        }

        if (! (bool) ($data['include_in_lzkosz'] ?? false)) {
            $data['lzkosz_round'] = null;
        }

        if (! (bool) ($data['is_ticketed'] ?? false)) {
            $data['ticket_url'] = null;
        }

        $data['opponent_name'] = $opponentName;
        $data['location'] = $locationName;
        $data['opponent_id'] = $opponent->id;
        $data['sports_hall_id'] = $sportsHall->id;
        $data['opponent_logo'] = $opponent->logo_path ?? $match?->opponent_logo;

        if ($homeLogo) {
            $data['home_logo'] = $homeLogo->store('team-logos', 'public');
        } elseif ($match) {
            $data['home_logo'] = $match->home_logo;
        }

        return $data;
    }

    private function findOrCreateOpponent(string $name): Opponent
    {
        return Opponent::query()->firstOrCreate([
            'name' => trim($name),
        ]);
    }

    private function findOrCreateSportsHall(string $name): SportsHall
    {
        return SportsHall::query()->firstOrCreate([
            'name' => trim($name),
        ]);
    }
}
