<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMatchRequest;
use App\Http\Requests\UpdateMatchRequest;
use App\Models\TeamMatch;
use App\Services\AdminNotificationService;
use App\Services\MatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function __construct(
        private readonly MatchService $matchService,
        private readonly AdminNotificationService $notificationService
    ) {}

    public function index(): View
    {
        $matches = TeamMatch::query()
            ->with(['opponent', 'sportsHall'])
            ->where(function ($query) {
                $query->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })
            ->orderBy('match_date')
            ->get();

        return view('matches.index', compact('matches'));
    }

    public function show(TeamMatch $match): View
    {
        if (! $match->isPublished() && auth()->guest()) {
            abort(403);
        }

        return view('matches.show', ['match' => $match]);
    }

    public function create(): View
    {
        $this->authorize('create', TeamMatch::class);

        return view('matches.create');
    }

    public function store(StoreMatchRequest $request): RedirectResponse
    {
        $match = $this->matchService->create(
            $request->validated(),
            $request->file('opponent_logo'),
            $request->file('home_logo')
        );
        $this->notificationService->record($request->user(), 'created', $match, "Mecz: ETB - {$match->opponent_name}");

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Mecz został dodany.');
    }

    public function edit(TeamMatch $match): View
    {
        $this->authorize('update', $match);

        return view('matches.edit', ['match' => $match]);
    }

    public function update(UpdateMatchRequest $request, TeamMatch $match): RedirectResponse
    {
        $this->matchService->update(
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

    public function destroy(TeamMatch $match): RedirectResponse
    {
        $this->authorize('delete', $match);

        $label = "Mecz: ETB - {$match->opponent_name}";
        $id = $match->id;
        $this->matchService->delete($match);
        $this->notificationService->recordDeleted(request()->user(), TeamMatch::class, $id, $label);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Mecz został usunięty.');
    }
}
