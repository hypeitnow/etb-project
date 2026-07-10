<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DrawThreeXThreeTournamentRequest;
use App\Models\ThreeXThreeTournament;
use App\Services\ThreeXThreeTournamentFlowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ThreeXThreeTournamentDrawController extends Controller
{
    public function __construct(private readonly ThreeXThreeTournamentFlowService $flowService) {}

    public function draw(DrawThreeXThreeTournamentRequest $request, ThreeXThreeTournament $tournament): RedirectResponse
    {
        $result = $this->flowService->drawTournament(
            $tournament,
            (int) $request->integer('groups_count'),
            (int) $request->integer('teams_per_group'),
            (int) $request->integer('qualifiers_per_group'),
            $request->boolean('generate_group_matches', true),
            $request->boolean('generate_playoff', true),
        );

        return back()->with('success', "Wylosowano {$result['groups']} grup, przypisano {$result['teams']} drużyn i utworzono {$result['matches']} meczów.");
    }

    public function refreshPlayoff(Request $request, ThreeXThreeTournament $tournament): RedirectResponse
    {
        $validated = $request->validate([
            'qualifiers_per_group' => ['required', 'integer', 'min:1', 'max:4'],
        ]);

        $this->flowService->refreshPlayoff($tournament, (int) $validated['qualifiers_per_group']);

        return back()->with('success', 'Drabinka fazy pucharowej została odświeżona na podstawie tabel grupowych.');
    }
}
