<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $season = $request->string('season')->toString();
        $view = $request->string('view', 'all')->toString();
        $sort = $request->string('sort', 'asc')->toString() === 'desc' ? 'desc' : 'asc';

        $query = MatchGame::query()
            ->with(['opponent', 'sportsHall'])
            ->where(function ($query): void {
                $query->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            });

        if ($season !== '') {
            $query->where('season', $season);
        }

        if (in_array($view, [MatchGame::STATUS_UPCOMING, MatchGame::STATUS_FINISHED], true)) {
            $query->where('status', $view);
        }

        $matches = $query->orderBy('match_date', $sort)->get();

        $seasons = MatchGame::query()
            ->whereNotNull('season')
            ->distinct()
            ->orderByDesc('season')
            ->pluck('season');

        return view('pages.schedule', [
            'matches' => $matches,
            'upcomingMatches' => $matches->where('status', MatchGame::STATUS_UPCOMING),
            'finishedMatches' => $matches->where('status', MatchGame::STATUS_FINISHED),
            'seasons' => $seasons,
            'selectedSeason' => $season,
            'selectedView' => $view,
            'selectedSort' => $sort,
        ]);
    }

    public function show(MatchGame $match): View
    {
        abort_unless($match->isPublished(), 404);

        $match->load(['opponent', 'sportsHall']);

        return view('pages.schedule-show', compact('match'));
    }

    public function lzkosz(): View
    {
        $matches = MatchGame::query()
            ->with(['opponent', 'sportsHall'])
            ->where('include_in_lzkosz', true)
            ->where(function ($query): void {
                $query->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->orderBy('match_date')
            ->get();

        return view('pages.schedule-lzkosz', [
            'roundOneMatches' => $matches->where('lzkosz_round', MatchGame::LZKOSZ_ROUND_ONE),
            'roundTwoMatches' => $matches->where('lzkosz_round', MatchGame::LZKOSZ_ROUND_TWO),
        ]);
    }
}
