<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\LeagueStanding;
use App\Models\ThreeXThreeTournament;
use App\Services\LzkoszLeagueTableService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $lzkoszMatches = $this->lzkoszMatches();

        return view('pages.schedule', [
            ...$this->scheduleData($request),
            'roundOneMatches' => $lzkoszMatches->where('lzkosz_round', MatchGame::LZKOSZ_ROUND_ONE),
            'roundTwoMatches' => $lzkoszMatches->where('lzkosz_round', MatchGame::LZKOSZ_ROUND_TWO),
            'leagueStandings' => $this->leagueStandings(),
            'participatingUpcomingTournaments' => ThreeXThreeTournament::query()->with('categories')->participating()->upcoming()->orderBy('date')->get(),
            'participatingFinishedTournaments' => ThreeXThreeTournament::query()->with('categories')->participating()->finished()->orderByDesc('date')->get(),
            'organizedUpcomingTournaments' => ThreeXThreeTournament::query()->with('categories')->organized()->upcoming()->orderBy('date')->get(),
            'organizedFinishedTournaments' => ThreeXThreeTournament::query()->with('categories')->organized()->finished()->orderByDesc('date')->get(),
        ]);
    }

    public function matches(Request $request): View
    {
        return view('pages.schedule-matches', $this->scheduleData($request));
    }

    public function show(MatchGame $match): View
    {
        abort_unless($match->isPublished(), 404);

        $match->load(['opponent', 'sportsHall']);

        return view('pages.schedule-show', compact('match'));
    }

    public function lzkosz(): View
    {
        $matches = $this->lzkoszMatches();

        return view('pages.schedule-lzkosz', [
            'roundOneMatches' => $matches->where('lzkosz_round', MatchGame::LZKOSZ_ROUND_ONE),
            'roundTwoMatches' => $matches->where('lzkosz_round', MatchGame::LZKOSZ_ROUND_TWO),
        ]);
    }

    public function table(): View
    {
        return view('pages.schedule-table', [
            'leagueStandings' => $this->leagueStandings(),
        ]);
    }

    private function scheduleData(Request $request): array
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

        return [
            'matches' => $matches,
            'upcomingMatches' => $matches->where('status', MatchGame::STATUS_UPCOMING),
            'finishedMatches' => $matches->where('status', MatchGame::STATUS_FINISHED),
            'seasons' => $seasons,
            'selectedSeason' => $season,
            'selectedView' => $view,
            'selectedSort' => $sort,
        ];
    }

    private function lzkoszMatches()
    {
        return MatchGame::query()
            ->with(['opponent', 'sportsHall'])
            ->where('include_in_lzkosz', true)
            ->where(function ($query): void {
                $query->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->orderBy('match_date')
            ->get();
    }

    private function leagueStandings()
    {
        return LeagueStanding::query()
            ->with('opponent')
            ->where('league_id', LzkoszLeagueTableService::LEAGUE_ID)
            ->where('season', LzkoszLeagueTableService::SEASON)
            ->orderBy('position')
            ->get();
    }
}
