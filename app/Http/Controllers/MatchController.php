<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMatchRequest;
use App\Http\Requests\UpdateMatchRequest;
use App\Models\MatchGame;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', MatchGame::class);

        $matches = MatchGame::query()
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
        $data = $this->validatedMatchData($request->validated());

        if ($request->hasFile('opponent_logo')) {
            $data['opponent_logo'] = $request->file('opponent_logo')->store('match-opponents', 'public');
        }

        MatchGame::query()->create($data);

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
        $data = $this->validatedMatchData($request->validated());

        if ($request->hasFile('opponent_logo')) {
            if ($match->opponent_logo) {
                Storage::disk('public')->delete($match->opponent_logo);
            }

            $data['opponent_logo'] = $request->file('opponent_logo')->store('match-opponents', 'public');
        }

        $match->update($data);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Mecz został zaktualizowany.');
    }

    public function destroy(MatchGame $match): RedirectResponse
    {
        $this->authorize('delete', $match);

        if ($match->opponent_logo) {
            Storage::disk('public')->delete($match->opponent_logo);
        }

        $match->delete();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Mecz został usunięty.');
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function validatedMatchData(array $data): array
    {
        unset($data['opponent_logo']);

        $data['our_score'] = $data['our_score'] ?? null;
        $data['opponent_score'] = $data['opponent_score'] ?? null;

        $hasResult = $data['our_score'] !== null && $data['opponent_score'] !== null;
        $data['status'] = $hasResult ? MatchGame::STATUS_FINISHED : MatchGame::STATUS_UPCOMING;

        return $data;
    }
}
