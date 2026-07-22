<?php

namespace App\Services;

use App\Models\ThreeXThreeTournament;
use App\Models\ThreeXThreeTournamentTeam;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ThreeXThreeTournamentTeamService
{
    /**
     * @param array<string, mixed> $data
     */
    public function register(ThreeXThreeTournament $tournament, int $userId, array $data, ?UploadedFile $logo): ThreeXThreeTournamentTeam
    {
        $players = $data['players'];
        unset($data['players'], $data['logo']);

        if ($logo) {
            $storedPath = Storage::disk('public')->putFile('3x3-team-logos', $logo);

            if (! is_string($storedPath) || $storedPath === '') {
                $storedPath = '3x3-team-logos/'.$logo->hashName();
                Storage::disk('public')->put($storedPath, (string) file_get_contents($logo->getRealPath()));
            }

            $data['logo_path'] = $storedPath;
        }

        $data['user_id'] = $userId;

        $team = $tournament->teams()->create($data);

        foreach (array_values($players) as $index => $player) {
            $team->players()->create([
                'name' => $player['name'],
                'sort_order' => $index,
            ]);
        }

        return $team;
    }

    public function delete(ThreeXThreeTournamentTeam $team): void
    {
        if ($team->logo_path) {
            Storage::disk('public')->delete($team->logo_path);
        }

        $team->delete();
    }
}
