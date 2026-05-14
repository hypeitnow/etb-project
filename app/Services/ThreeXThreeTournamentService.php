<?php

namespace App\Services;

use App\Models\ThreeXThreeTournament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ThreeXThreeTournamentService
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data, ?UploadedFile $image): ThreeXThreeTournament
    {
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        if ($image) {
            $data['image_path'] = $image->store('3x3-tournaments', 'public');
        }

        $tournament = ThreeXThreeTournament::query()->create($data);
        $this->syncCategories($tournament, $categories);

        return $tournament;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(ThreeXThreeTournament $tournament, array $data, ?UploadedFile $image): ThreeXThreeTournament
    {
        $categories = $data['categories'] ?? [];
        unset($data['categories']);

        if ($image) {
            if ($tournament->image_path) {
                Storage::disk('public')->delete($tournament->image_path);
            }

            $data['image_path'] = $image->store('3x3-tournaments', 'public');
        }

        $tournament->update($data);
        $this->syncCategories($tournament, $categories);

        return $tournament;
    }

    public function delete(ThreeXThreeTournament $tournament): void
    {
        if ($tournament->image_path) {
            Storage::disk('public')->delete($tournament->image_path);
        }

        $tournament->delete();
    }

    /**
     * @param array<int, string> $categories
     */
    private function syncCategories(ThreeXThreeTournament $tournament, array $categories): void
    {
        $tournament->categories()->delete();

        foreach (array_values(array_unique($categories)) as $category) {
            $tournament->categories()->create(['category' => $category]);
        }
    }
}
