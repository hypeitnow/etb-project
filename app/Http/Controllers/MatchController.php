<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMatchRequest;
use App\Http\Requests\UpdateMatchRequest;
use App\Models\MatchGame;
use App\Services\AdminNotificationService;
use App\Services\MatchGameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function __construct(
        private readonly MatchGameService $matchGameService,
        private readonly AdminNotificationService $notificationService
    )
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', MatchGame::class);

        $matches = MatchGame::query()
            ->with(['opponent', 'sportsHall'])
            ->where(function ($query): void {
                $query->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })
            ->orderBy('match_date')
            ->get();

        return view('matches.index', compact('matches'));
    }

    public function show(MatchGame $match): View
    {
        $this->authorize('view', $match);

        return view('matches.show', ['match' => $match]);
    }

    public function create(): View
    {
        $this->authorize('create', MatchGame::class);

        return view('matches.create');
    }

    public function store(StoreMatchRequest $request): RedirectResponse
    {
        $match = $this->matchGameService->create(
            $request->validated(),
            $request->file('opponent_logo'),
            $request->file('home_logo')
        );
        $this->notificationService->record($request->user(), 'created', $match, "Mecz: ETB - {$match->opponent_name}");

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Mecz został dodany.');
    }

    public function edit(MatchGame $match): View
    {
        $this->authorize('update', $match);

        return view('matches.edit', ['match' => $match]);
    }

    public function update(UpdateMatchRequest $request, MatchGame $match): RedirectResponse
    {
        $this->matchGameService->update(
            $match,
            $request->validated(),
            $request->file('opponent_logo'),
            $request->file('home_logo')
        );
        $this->notificationService->record($request->user(), 'updated', $match, "Mecz: ETB - {$match->opponent_name}");

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Mecz został zaktualizowany.');
    }

    public function destroy(MatchGame $match): RedirectResponse
    {
        $this->authorize('delete', $match);

        $label = "Mecz: ETB - {$match->opponent_name}";
        $id = $match->id;
        $this->matchGameService->delete($match);
        $this->notificationService->recordDeleted(request()->user(), MatchGame::class, $id, $label);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Mecz został usunięty.');
    }

}
