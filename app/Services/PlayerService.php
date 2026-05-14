<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PlayerService
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data, ?UploadedFile $photo): Player
    {
        if ($photo) {
            $data['photo_path'] = $photo->store('players', 'public');
        }

        return Player::query()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Player $player, array $data, ?UploadedFile $photo): Player
    {
        if ($photo) {
            if ($player->photo_path) {
                Storage::disk('public')->delete($player->photo_path);
            }

            $data['photo_path'] = $photo->store('players', 'public');
        }

        $player->update($data);

        return $player;
    }

    public function delete(Player $player): void
    {
        if ($player->photo_path) {
            Storage::disk('public')->delete($player->photo_path);
        }

        $player->delete();
    }
}
