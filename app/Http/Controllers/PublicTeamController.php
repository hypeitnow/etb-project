<?php

namespace App\Http\Controllers;

use App\Enums\BasketballPosition;
use App\Models\Player;
use App\Models\TeamStaff;
use App\Models\ThreeXThreeMember;
use Illuminate\View\View;

class PublicTeamController extends Controller
{
    public function players(): View
    {
        $players = Player::query()
            ->get()
            ->sortBy([
                fn (Player $a, Player $b): int => $a->positionOrder() <=> $b->positionOrder(),
                fn (Player $a, Player $b): int => $a->number <=> $b->number,
            ])
            ->groupBy('position');

        return view('pages.team-players', [
            'playersByPosition' => $players,
            'positions' => BasketballPosition::cases(),
        ]);
    }

    public function player(Player $player): View
    {
        abort_unless($player->publish_description, 404);

        return view('pages.team-player-show', compact('player'));
    }

    public function staff(): View
    {
        $staff = TeamStaff::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('pages.team-staff', compact('staff'));
    }

    public function threeXThree(): View
    {
        $members = ThreeXThreeMember::query()->orderByDesc('is_coach')->orderBy('sort_order')->orderBy('name')->get();

        return view('pages.team-3x3-players', compact('members'));
    }
}
