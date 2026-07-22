<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThreeXThreeTournamentRequest;
use App\Http\Requests\UpdateThreeXThreeTournamentRequest;
use App\Models\ThreeXThreeTournament;
use App\Models\User;
use App\Services\AdminNotificationService;
use App\Services\ThreeXThreeTournamentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThreeXThreeTournamentController extends Controller
{
    public function __construct(
        private readonly ThreeXThreeTournamentService $tournamentService,
        private readonly AdminNotificationService $notificationService
    ) {}

    public function index(): View
    {
        $upcomingTournaments = ThreeXThreeTournament::query()
            ->with('categories')
            ->organized()
            ->upcoming()
            ->orderBy('date')
            ->get();
        $finishedTournaments = ThreeXThreeTournament::query()
            ->with('categories')
            ->organized()
            ->finished()
            ->orderByDesc('date')
            ->get();

        return view('pages.schedule-3x3-tournaments', compact('upcomingTournaments', 'finishedTournaments'));
    }

    public function participating(): View
    {
        $upcomingTournaments = ThreeXThreeTournament::query()
            ->with('categories')
            ->participating()
            ->upcoming()
            ->orderBy('date')
            ->get();
        $finishedTournaments = ThreeXThreeTournament::query()
            ->with('categories')
            ->participating()
            ->finished()
            ->orderByDesc('date')
            ->get();

        return view('pages.schedule-3x3-tournaments', compact('upcomingTournaments', 'finishedTournaments'));
    }

    public function show(ThreeXThreeTournament $tournament): View
    {
        $tournament->load('categories');

        return view('pages.schedule-3x3-tournament-show', compact('tournament'));
    }

    public function store(StoreThreeXThreeTournamentRequest $request): RedirectResponse
    {
        $tournament = $this->tournamentService->create($request->safe()->except('image'), $request->file('image'));
        $this->notificationService->record($request->user(), 'created', $tournament, "Turniej 3x3: {$tournament->name}");

        return back()->with('success', 'Turniej 3x3 został zapisany.');
    }

    public function update(UpdateThreeXThreeTournamentRequest $request, ThreeXThreeTournament $tournament): RedirectResponse
    {
        $this->tournamentService->update($tournament, $request->safe()->except('image'), $request->file('image'));
        $this->notificationService->record($request->user(), 'updated', $tournament, "Turniej 3x3: {$tournament->name}");

        return back()->with('success', 'Turniej 3x3 został zaktualizowany.');
    }

    public function destroy(ThreeXThreeTournament $tournament): RedirectResponse
    {
        abort_unless(request()->user()?->role === User::ROLE_ADMIN, 403);

        $label = "Turniej 3x3: {$tournament->name}";
        $id = $tournament->id;
        $this->tournamentService->delete($tournament);
        $this->notificationService->recordDeleted(request()->user(), ThreeXThreeTournament::class, $id, $label);

        return back()->with('success', 'Turniej 3x3 został usunięty.');
    }
}
