<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\PublicProductController;
use App\Http\Controllers\PublicScheduleController;
use App\Http\Controllers\PublicTeamController;
use App\Http\Controllers\UserDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('players', PlayerController::class)->except(['index', 'show']);

    Route::resource('news', NewsController::class)->except(['index', 'show']);
    Route::get('/admin/news/{news}/preview', [NewsController::class, 'preview'])->name('admin.news.preview');
    Route::patch('/admin/news/{news}/publish', [NewsController::class, 'publish'])->name('admin.news.publish');

    Route::middleware('role:athlete')->group(function () {
        Route::get('/athlete/data', [UserDataController::class, 'athlete'])->name('athlete.data');
    });

    Route::middleware('role:fan')->group(function () {
        Route::get('/fan/data', [UserDataController::class, 'fan'])->name('fan.data');
    });

    Route::middleware('role:employee')->group(function () {
        Route::get('/employee/data', [UserDataController::class, 'employee'])->name('employee.data');
    });

    Route::middleware('role:trainer')->group(function () {
        Route::get('/trainer/data', [UserDataController::class, 'trainer'])->name('trainer.data');
    });
});

Route::resource('players', PlayerController::class)->only(['index', 'show']);

Route::get('/news', [PublicNewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [PublicNewsController::class, 'show'])->name('news.show');

Route::resource('matches', MatchController::class);

Route::get('/shop', [PublicProductController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [PublicProductController::class, 'show'])->name('shop.show');

Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/badge', [App\Http\Controllers\CartController::class, 'badge'])->name('cart.badge');

Route::middleware('auth')->group(function () {
    Route::get('/checkout/shipping', [App\Http\Controllers\CheckoutController::class, 'shipping'])->name('checkout.shipping');
    Route::post('/checkout/shipping', [App\Http\Controllers\CheckoutController::class, 'storeShipping']);
    Route::get('/checkout/payment', [App\Http\Controllers\CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/place', [App\Http\Controllers\CheckoutController::class, 'place'])->name('checkout.place');
    Route::get('/checkout/confirmation/{order}', [App\Http\Controllers\CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
});

Route::post('/payment/przelewy24/webhook', [App\Http\Controllers\CheckoutController::class, 'webhook'])->name('payment.przelewy24.webhook');

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
Route::view('/academy', 'pages.academy')->name('academy');
