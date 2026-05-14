<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMatchRequest;
use App\Http\Requests\UpdateMatchRequest;
use App\Models\MatchGame;
use App\Services\MatchGameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function __construct(private readonly MatchGameService $matchGameService)
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
        $this->matchGameService->create(
            $request->validated(),
            $request->file('opponent_logo'),
            $request->file('home_logo')
        );

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

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Mecz został zaktualizowany.');
    }

    public function destroy(MatchGame $match): RedirectResponse
    {
        $this->authorize('delete', $match);

        $this->matchGameService->delete($match);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Mecz został usunięty.');
    }

}
