<?php

use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Admin\AcademyCalendarNoteController;
use App\Http\Controllers\Admin\AcademyGroupController;
use App\Http\Controllers\Admin\AcademyMessageController;
use App\Http\Controllers\Admin\AcademyTrainerController;
use App\Http\Controllers\Admin\AcademyTrainingController;
use App\Http\Controllers\Admin\UserSearchController;
use App\Http\Controllers\Admin\UserEmailExportController;
use App\Http\Controllers\Admin\MatchSuggestionController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\ClubSectionController;
use App\Http\Controllers\Admin\LeagueTableController;
use App\Http\Controllers\Admin\ThreeXThreeTournamentGroupController;
use App\Http\Controllers\Admin\ThreeXThreeTournamentMatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PublicAcademyController;
use App\Http\Controllers\PublicClubController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\PublicScheduleController;
use App\Http\Controllers\PublicTeamController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\TeamStaffController;
use App\Http\Controllers\ThreeXThreeMemberController;
use App\Http\Controllers\ThreeXThreeTournamentController;
use App\Http\Controllers\ThreeXThreeTournamentTeamController;
use App\Models\AppSetting;
use App\Models\Game;
use App\Models\MatchGame;
use App\Models\News;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $latestNews = News::query()
        ->with(['author', 'images'])
        ->active()
        ->published()
        ->latest('publish_at')
        ->latest()
        ->take(11)
        ->get();

    $lastFinishedMatch = MatchGame::query()
        ->with(['opponent', 'sportsHall'])
        ->where('status', MatchGame::STATUS_FINISHED)
        ->where(function ($query): void {
            $query->whereNull('publish_at')->orWhere('publish_at', '<=', now());
        })
        ->latest('match_date')
        ->first();

    $upcomingMatches = MatchGame::query()
        ->with(['opponent', 'sportsHall'])
        ->where('status', MatchGame::STATUS_UPCOMING)
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
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::resource('players', PlayerController::class)->only(['index', 'show']);

Route::middleware(['auth'])->group(function () {
    Route::resource('players', PlayerController::class)->except(['index', 'show']);
});


Route::get('/news', [PublicNewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [PublicNewsController::class, 'show'])->name('news.show');

Route::middleware(['auth'])->group(function () {
    Route::resource('news', NewsController::class)->except(['index', 'show']);
    Route::get('/admin/news/{news}/preview', [NewsController::class, 'preview'])->name('admin.news.preview');
    Route::patch('/admin/news/{news}/publish', [NewsController::class, 'publish'])->name('admin.news.publish');
});

Route::resource('matches', MatchController::class);

/*
|--------------------------------------------------------------------------
| Strony główne menu
|--------------------------------------------------------------------------
*/
Route::get('/club', [PublicClubController::class, 'index'])->name('club');
Route::get('/schedule', [PublicScheduleController::class, 'index'])->name('schedule');
Route::get('/schedule/matches', [PublicScheduleController::class, 'matches'])->name('schedule.matches.index');
Route::get('/schedule/matches/{match}', [PublicScheduleController::class, 'show'])->name('schedule.matches.show');
Route::get('/team', [PublicTeamController::class, 'index'])->name('team');
Route::get('/contact', [PublicClubController::class, 'contact'])->name('contact');


/* Klub */
Route::get('/club/history', fn (PublicClubController $controller) => $controller->show('history'))->name('club.history');
Route::get('/club/board', fn (PublicClubController $controller) => $controller->show('board'))->name('club.board');
Route::get('/club/venue', fn (PublicClubController $controller) => $controller->show('venue'))->name('club.venue');
Route::get('/club/business', fn (PublicClubController $controller) => $controller->show('business'))->name('club.business');
Route::get('/club/success', fn (PublicClubController $controller) => $controller->show('success'))->name('club.success');
Route::get('/club/sponsors', fn (PublicClubController $controller) => $controller->show('sponsors'))->name('club.sponsors');
Route::get('/club/contact', fn (PublicClubController $controller) => $controller->show('contact'))->name('club.contact');

/* Rozgrywki */
Route::get('/schedule/lzkosz', [PublicScheduleController::class, 'lzkosz'])->name('schedule.lzkosz');
Route::view('/schedule/third-league', 'pages.schedule-third-league')->name('schedule.third-league');
Route::get('/schedule/table', [PublicScheduleController::class, 'table'])->name('schedule.table');
Route::get('/schedule/3x3', [ThreeXThreeTournamentController::class, 'schedule'])->name('schedule.3x3');
Route::redirect('/schedule/3x3-tournaments', '/schedule/3x3/tournaments')->name('schedule.3x3.tournaments.old');
Route::get('/schedule/3x3/tournaments', [ThreeXThreeTournamentController::class, 'index'])->name('schedule.3x3.tournaments');
Route::view('/schedule/3x3/team', 'pages.schedule-3x3-team')->name('schedule.3x3.team');

/* Drużyna */
Route::get('/team/players', [PublicTeamController::class, 'players'])->name('team.players');
Route::get('/team/players/{player}', [PublicTeamController::class, 'player'])->name('team.players.show');
Route::get('/team/staff', [PublicTeamController::class, 'staff'])->name('team.staff');
Route::get('/team/3x3', [PublicTeamController::class, 'threeXThree'])->name('team.3x3');
Route::redirect('/team-3x3/players', '/team/3x3')->name('team3x3.players');
Route::get('/3x3/tournaments', [ThreeXThreeTournamentController::class, 'index'])->name('three-x-three.tournaments.index');
Route::get('/3x3/tournaments/{tournament}', [ThreeXThreeTournamentController::class, 'show'])->name('three-x-three.tournaments.show');
Route::post('/3x3/tournaments/{tournament}/teams', [ThreeXThreeTournamentTeamController::class, 'store'])->middleware('auth')->name('three-x-three.tournaments.teams.store');

/* CTA */
Route::view('/tickets', 'pages.tickets')->name('tickets');
Route::view('/shop', 'pages.shop')->name('shop');
Route::get('/academy', [PublicAcademyController::class, 'index'])->name('academy');
Route::get('/academy/{group}', [PublicAcademyController::class, 'show'])->name('academy.groups.show');

Route::middleware(['auth', 'role:admin,employee', 'can:manage-matches'])->group(function () {
    Route::get('/admin/matches/create', function () {
        $defaultHomeLogo = AppSetting::getValue('default_home_logo');

        return view('admin.create-match', compact('defaultHomeLogo'));
    })->name('admin.matches.create');

    Route::post('/admin/matches', function (Request $request) {
        $validated = $request->validate([
            'opponent' => ['required', 'string', 'max:255'],
            'match_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'exact_address' => ['nullable', 'string', 'max:500'],
            'is_home' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
            'away_logo' => ['nullable', 'image', 'max:5120'],
            'default_home_logo' => ['nullable', 'image', 'max:5120'],
        ]);

        $defaultHomeLogo = AppSetting::getValue('default_home_logo');

        if ($request->hasFile('default_home_logo')) {
            $defaultHomeLogo = $request->file('default_home_logo')->store('logos', 'public');
            AppSetting::setValue('default_home_logo', $defaultHomeLogo);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('matches', 'public');
        }

        if ($request->hasFile('away_logo')) {
            $validated['away_logo'] = $request->file('away_logo')->store('logos', 'public');
        }

        $validated['home_logo'] = $defaultHomeLogo;
        $validated['is_home'] = $request->boolean('is_home');

        Game::create($validated);

        return redirect()->route('admin.matches.create')->with('status', 'Mecz został zapisany.');
    })->name('admin.matches.store');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::patch('/admin/users/{user}/role', [UserRoleController::class, 'update'])->name('admin.users.role.update');
    Route::get('/admin/users/search', UserSearchController::class)->name('admin.users.search');
    Route::get('/admin/users/emails/export', UserEmailExportController::class)->name('admin.users.emails.export');
});

Route::middleware(['auth', 'role:admin,employee'])->group(function () {
    Route::post('/admin/academy/groups', [AcademyGroupController::class, 'store'])->name('admin.academy.groups.store');
    Route::put('/admin/academy/groups/{group}', [AcademyGroupController::class, 'update'])->name('admin.academy.groups.update');
    Route::delete('/admin/academy/groups/{group}', [AcademyGroupController::class, 'destroy'])->name('admin.academy.groups.destroy');
    Route::post('/admin/academy/groups/{group}/trainers', [AcademyTrainerController::class, 'store'])->name('admin.academy.groups.trainers.store');
    Route::get('/admin/academy/trainers/suggestions', [AcademyTrainerController::class, 'suggestions'])->name('admin.academy.trainers.suggestions');
    Route::put('/admin/academy/trainers/{trainer}', [AcademyTrainerController::class, 'update'])->name('admin.academy.trainers.update');
    Route::delete('/admin/academy/trainers/{trainer}', [AcademyTrainerController::class, 'destroy'])->name('admin.academy.trainers.destroy');
    Route::post('/admin/academy/groups/{group}/messages', [AcademyMessageController::class, 'store'])->name('admin.academy.groups.messages.store');
    Route::put('/admin/academy/messages/{message}', [AcademyMessageController::class, 'update'])->name('admin.academy.messages.update');
    Route::delete('/admin/academy/messages/{message}', [AcademyMessageController::class, 'destroy'])->name('admin.academy.messages.destroy');
    Route::post('/admin/academy/trainings', [AcademyTrainingController::class, 'store'])->name('admin.academy.trainings.store');
    Route::put('/admin/academy/trainings/{training}', [AcademyTrainingController::class, 'update'])->name('admin.academy.trainings.update');
    Route::patch('/admin/academy/trainings/{training}/cancel', [AcademyTrainingController::class, 'cancel'])->name('admin.academy.trainings.cancel');
    Route::patch('/admin/academy/trainings/{training}/restore', [AcademyTrainingController::class, 'restore'])->name('admin.academy.trainings.restore');
    Route::delete('/admin/academy/trainings/{training}', [AcademyTrainingController::class, 'destroy'])->name('admin.academy.trainings.destroy');
    Route::post('/admin/academy/calendar-notes', [AcademyCalendarNoteController::class, 'store'])->name('admin.academy.calendar-notes.store');
    Route::delete('/admin/academy/calendar-notes/{note}', [AcademyCalendarNoteController::class, 'destroy'])->name('admin.academy.calendar-notes.destroy');
    Route::get('/admin/match-suggestions/locations', [MatchSuggestionController::class, 'locations'])->name('admin.match-suggestions.locations');
    Route::get('/admin/match-suggestions/opponents', [MatchSuggestionController::class, 'opponents'])->name('admin.match-suggestions.opponents');
    Route::post('/admin/league-table/sync', [LeagueTableController::class, 'sync'])->name('admin.league-table.sync');
    Route::patch('/admin/opponents/{opponent}', [LeagueTableController::class, 'updateOpponent'])->name('admin.opponents.update');
    Route::put('/admin/club-sections/{section}', [ClubSectionController::class, 'update'])->name('admin.club-sections.update');
    Route::patch('/admin/club-sections/{section}/images/{image}', [ClubSectionController::class, 'updateImage'])->name('admin.club-sections.images.update');
    Route::delete('/admin/club-sections/{section}/images/{image}', [ClubSectionController::class, 'destroyImage'])->name('admin.club-sections.images.destroy');
    Route::patch('/admin/notifications/{notification}/read', [AdminNotificationController::class, 'read'])->name('admin.notifications.read');
    Route::patch('/admin/notifications/{notification}/accept', [AdminNotificationController::class, 'accept'])->name('admin.notifications.accept');
    Route::delete('/admin/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::resource('/admin/staff', TeamStaffController::class)->only(['store', 'update', 'destroy'])->parameters(['staff' => 'staff']);
    Route::resource('/admin/3x3/members', ThreeXThreeMemberController::class)->only(['store', 'update', 'destroy'])->parameters(['members' => 'member']);
    Route::resource('/admin/3x3/tournaments', ThreeXThreeTournamentController::class)->only(['store', 'update', 'destroy'])->parameters(['tournaments' => 'tournament']);
    Route::post('/admin/3x3/tournaments/{tournament}/groups', [ThreeXThreeTournamentGroupController::class, 'store'])->name('admin.3x3.tournaments.groups.store');
    Route::put('/admin/3x3/tournaments/{tournament}/groups/{group}', [ThreeXThreeTournamentGroupController::class, 'update'])->name('admin.3x3.tournaments.groups.update');
    Route::delete('/admin/3x3/tournaments/{tournament}/groups/{group}', [ThreeXThreeTournamentGroupController::class, 'destroy'])->name('admin.3x3.tournaments.groups.destroy');
    Route::post('/admin/3x3/tournaments/{tournament}/matches', [ThreeXThreeTournamentMatchController::class, 'store'])->name('admin.3x3.tournaments.matches.store');
    Route::put('/admin/3x3/tournaments/{tournament}/matches/{match}', [ThreeXThreeTournamentMatchController::class, 'update'])->name('admin.3x3.tournaments.matches.update');
    Route::delete('/admin/3x3/tournaments/{tournament}/matches/{match}', [ThreeXThreeTournamentMatchController::class, 'destroy'])->name('admin.3x3.tournaments.matches.destroy');
    Route::resource('/admin/sponsors', SponsorController::class)->only(['store', 'update', 'destroy']);
});

Route::middleware(['auth', 'role:athlete'])->group(function () {
    Route::get('/athlete/data', function () {
        return response()->json(request()->user()->athleteProfile);
    })->name('athlete.data');
});

Route::middleware(['auth', 'role:fan'])->group(function () {
    Route::get('/fan/data', function () {
        return response()->json(request()->user()->fanProfile);
    })->name('fan.data');
});

Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/employee/data', function () {
        return response()->json(request()->user()->employeeProfile);
    })->name('employee.data');
});
