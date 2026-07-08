<?php

use App\Http\Controllers\Admin\AdminMatchController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\MatchSuggestionController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Admin\UserSearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\PublicScheduleController;
use App\Http\Controllers\PublicTeamController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\TeamStaffController;
use App\Http\Controllers\ThreeXThreeMemberController;
use App\Http\Controllers\ThreeXThreeTournamentController;
use App\Http\Controllers\UserDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('players', PlayerController::class)->except(['index', 'show']);

    Route::resource('news', NewsController::class)->except(['index', 'show']);
    Route::get('/admin/news/{news}/preview', [NewsController::class, 'preview'])->name('admin.news.preview');
    Route::patch('/admin/news/{news}/publish', [NewsController::class, 'publish'])->name('admin.news.publish');

    Route::middleware('role:admin,employee')->group(function () {
        Route::middleware('can:manage-matches')->group(function () {
            Route::get('/admin/matches/create', [AdminMatchController::class, 'create'])->name('admin.matches.create');
            Route::post('/admin/matches', [AdminMatchController::class, 'store'])->name('admin.matches.store');
        });

        Route::get('/admin/match-suggestions/locations', [MatchSuggestionController::class, 'locations'])->name('admin.match-suggestions.locations');
        Route::get('/admin/match-suggestions/opponents', [MatchSuggestionController::class, 'opponents'])->name('admin.match-suggestions.opponents');
        Route::patch('/admin/notifications/{notification}/read', [AdminNotificationController::class, 'read'])->name('admin.notifications.read');
        Route::patch('/admin/notifications/{notification}/accept', [AdminNotificationController::class, 'accept'])->name('admin.notifications.accept');
        Route::delete('/admin/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('admin.notifications.destroy');
        Route::resource('/admin/staff', TeamStaffController::class)->only(['store', 'update', 'destroy'])->parameters(['staff' => 'staff']);
        Route::resource('/admin/3x3/members', ThreeXThreeMemberController::class)->only(['store', 'update', 'destroy'])->parameters(['members' => 'member']);
        Route::resource('/admin/3x3/tournaments', ThreeXThreeTournamentController::class)->only(['store', 'update', 'destroy'])->parameters(['tournaments' => 'tournament']);
        Route::resource('/admin/sponsors', SponsorController::class)->only(['store', 'update', 'destroy']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::patch('/admin/users/{user}/role', [UserRoleController::class, 'update'])->name('admin.users.role.update');
        Route::get('/admin/users/search', UserSearchController::class)->name('admin.users.search');
    });

    Route::middleware('role:athlete')->group(function () {
        Route::get('/athlete/data', [UserDataController::class, 'athlete'])->name('athlete.data');
    });

    Route::middleware('role:fan')->group(function () {
        Route::get('/fan/data', [UserDataController::class, 'fan'])->name('fan.data');
    });

    Route::middleware('role:employee')->group(function () {
        Route::get('/employee/data', [UserDataController::class, 'employee'])->name('employee.data');
    });
});

Route::resource('players', PlayerController::class)->only(['index', 'show']);

Route::get('/news', [PublicNewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [PublicNewsController::class, 'show'])->name('news.show');

Route::resource('matches', MatchController::class);

/*
|--------------------------------------------------------------------------
| Strony główne menu
|--------------------------------------------------------------------------
*/
Route::view('/club', 'pages.club')->name('club');
Route::get('/schedule', [PublicScheduleController::class, 'index'])->name('schedule');
Route::get('/schedule/matches/{match}', [PublicScheduleController::class, 'show'])->name('schedule.matches.show');
Route::view('/team', 'pages.team')->name('team');
Route::view('/contact', 'pages.contact')->name('contact');

/* Klub */
Route::view('/club/history', 'pages.club-history')->name('club.history');
Route::view('/club/board', 'pages.club-board')->name('club.board');
Route::view('/club/venue', 'pages.club-venue')->name('club.venue');
Route::view('/club/business', 'pages.club-business')->name('club.business');
Route::view('/club/investors', 'pages.club-investors')->name('club.investors');
Route::view('/club/success', 'pages.club-success')->name('club.success');
Route::view('/club/sponsors', 'pages.club-sponsors')->name('club.sponsors');

/* Rozgrywki */
Route::get('/schedule/lzkosz', [PublicScheduleController::class, 'lzkosz'])->name('schedule.lzkosz');
Route::redirect('/schedule/third-league', 'https://www.lzkosz.pl/liga/215.html')->name('schedule.third-league');
Route::view('/schedule/table', 'pages.schedule-table')->name('schedule.table');
Route::get('/schedule/3x3', [ThreeXThreeTournamentController::class, 'index'])->name('schedule.3x3');
Route::redirect('/schedule/3x3-tournaments', '/schedule/3x3')->name('schedule.3x3.tournaments.old');
Route::redirect('/schedule/3x3/tournaments', '/schedule/3x3')->name('schedule.3x3.tournaments');
Route::view('/schedule/3x3/team', 'pages.schedule-3x3-team')->name('schedule.3x3.team');

/* Drużyna */
Route::get('/team/players', [PublicTeamController::class, 'players'])->name('team.players');
Route::get('/team/players/{player}', [PublicTeamController::class, 'player'])->name('team.players.show');
Route::get('/team/staff', [PublicTeamController::class, 'staff'])->name('team.staff');
Route::get('/team/3x3', [PublicTeamController::class, 'threeXThree'])->name('team.3x3');
Route::redirect('/team-3x3/players', '/team/3x3')->name('team3x3.players');
Route::get('/3x3/tournaments', [ThreeXThreeTournamentController::class, 'index'])->name('three-x-three.tournaments.index');
Route::get('/3x3/tournaments/{tournament}', [ThreeXThreeTournamentController::class, 'show'])->name('three-x-three.tournaments.show');

/* CTA */
Route::view('/tickets', 'pages.tickets')->name('tickets');
Route::view('/shop', 'pages.shop')->name('shop');
Route::view('/academy', 'pages.academy')->name('academy');
