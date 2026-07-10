<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreThreeXThreeTournamentGroupRequest;
use App\Models\ThreeXThreeTournament;
use App\Models\ThreeXThreeTournamentGroup;
use Illuminate\Http\RedirectResponse;

class ThreeXThreeTournamentGroupController extends Controller
{
    public function store(StoreThreeXThreeTournamentGroupRequest $request, ThreeXThreeTournament $tournament): RedirectResponse
    {
        $tournament->groups()->create($request->validated());

        return back()->with('success', 'Grupa turnieju została dodana.');
    }

    public function update(StoreThreeXThreeTournamentGroupRequest $request, ThreeXThreeTournament $tournament, ThreeXThreeTournamentGroup $group): RedirectResponse
    {
        abort_unless($group->three_x_three_tournament_id === $tournament->id, 404);

        $group->update($request->validated());

        return back()->with('success', 'Grupa turnieju została zaktualizowana.');
    }

    public function destroy(ThreeXThreeTournament $tournament, ThreeXThreeTournamentGroup $group): RedirectResponse
    {
        abort_unless($group->three_x_three_tournament_id === $tournament->id, 404);

        $group->delete();

        return back()->with('success', 'Grupa turnieju została usunięta.');
    }
}
