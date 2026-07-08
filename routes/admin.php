<?php

use App\Http\Controllers\Admin\AdminMatchController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\MatchSuggestionController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Admin\UserSearchController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\TeamStaffController;
use App\Http\Controllers\ThreeXThreeMemberController;
use App\Http\Controllers\ThreeXThreeTournamentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,employee'])->group(function () {
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

    Route::resource('/admin/products', ProductController::class)->names('admin.products');
    Route::post('/admin/products/{product}/variants', [ProductController::class, 'addVariant'])->name('admin.products.variants.store');
    Route::delete('/admin/products/{product}/variants/{variant}', [ProductController::class, 'removeVariant'])->name('admin.products.variants.destroy');
    Route::resource('/admin/categories', CategoryController::class)->names('admin.categories');
    Route::resource('/admin/orders', OrderController::class)->names('admin.orders')->only(['index', 'show']);
    Route::patch('/admin/orders/{order}/transition', [OrderController::class, 'transition'])->name('admin.orders.transition');
    Route::get('/admin/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('admin.orders.invoice');
    Route::post('/admin/orders/{order}/label', [OrderController::class, 'generateLabel'])->name('admin.orders.label');
    Route::get('/admin/export/jpk', [ExportController::class, 'jpk'])->name('admin.export.jpk');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::patch('/admin/users/{user}/role', [UserRoleController::class, 'update'])->name('admin.users.role.update');
    Route::get('/admin/users/search', UserSearchController::class)->name('admin.users.search');
});
