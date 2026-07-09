<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreThreeXThreeTournamentMatchRequest;
use App\Models\ThreeXThreeTournament;
use App\Models\ThreeXThreeTournamentMatch;
use Illuminate\Http\RedirectResponse;

class ThreeXThreeTournamentMatchController extends Controller
{
    public function store(StoreThreeXThreeTournamentMatchRequest $request, ThreeXThreeTournament $tournament): RedirectResponse
    {
        $tournament->matches()->create($request->validated());

        return back()->with('success', 'Mecz turnieju zostal dodany.');
    }

    public function update(StoreThreeXThreeTournamentMatchRequest $request, ThreeXThreeTournament $tournament, ThreeXThreeTournamentMatch $match): RedirectResponse
    {
        abort_unless($match->three_x_three_tournament_id === $tournament->id, 404);

        $match->update($request->validated());

        return back()->with('success', 'Mecz turnieju zostal zaktualizowany.');
    }

    public function destroy(ThreeXThreeTournament $tournament, ThreeXThreeTournamentMatch $match): RedirectResponse
    {
        abort_unless($match->three_x_three_tournament_id === $tournament->id, 404);

        $match->delete();

        return back()->with('success', 'Mecz turnieju zostal usuniety.');
    }
}
