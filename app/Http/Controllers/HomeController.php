<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Player;
use App\Models\TeamMatch;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $latestNews = News::query()
            ->with(['author', 'images'])
            ->active()
            ->published()
            ->latest('publish_at')
            ->latest()
            ->take(11)
            ->get();

        $lastFinishedMatch = TeamMatch::query()
            ->with(['opponent', 'sportsHall'])
            ->where('status', TeamMatch::STATUS_FINISHED)
            ->where(function ($query): void {
                $query->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->latest('match_date')
            ->first();

        $upcomingMatches = TeamMatch::query()
            ->with(['opponent', 'sportsHall'])
            ->where('status', TeamMatch::STATUS_UPCOMING)
            ->where('match_date', '>=', now())
            ->where(function ($query): void {
                $query->whereNull('publish_at')->orWhere('publish_at', '<=', now());
            })
            ->orderBy('match_date')
            ->take(2)
            ->get();

        $startingFive = Player::query()
            ->where('is_starting_five', true)
            ->orderBy('number')
            ->get()
            ->sortBy(fn (Player $player): array => [$player->positionOrder(), $player->number])
            ->take(5)
            ->values();

        return view('home', [
            'heroNews' => $latestNews->take(5),
            'featuredArticles' => $latestNews->slice(5, 2),
            'moreArticles' => $latestNews->slice(7, 4),
            'lastFinishedMatch' => $lastFinishedMatch,
            'upcomingMatches' => $upcomingMatches,
            'startingFive' => $startingFive,
        ]);
    }
}
