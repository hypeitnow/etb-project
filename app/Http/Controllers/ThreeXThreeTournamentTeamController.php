<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThreeXThreeTournamentTeamRequest;
use App\Models\ThreeXThreeTournament;
use App\Services\ThreeXThreeTournamentTeamService;
use Illuminate\Http\RedirectResponse;

class ThreeXThreeTournamentTeamController extends Controller
{
    public function __construct(private readonly ThreeXThreeTournamentTeamService $teamService)
    {
    }

    public function store(StoreThreeXThreeTournamentTeamRequest $request, ThreeXThreeTournament $tournament): RedirectResponse
    {
        $this->teamService->register(
            $tournament,
            $request->user()->id,
            $request->validated(),
            $request->file('logo')
        );

        return redirect()
            ->route('three-x-three.tournaments.show', $tournament)
            ->with('success', 'Druzyna zostala zgloszona do turnieju.');
    }
}
