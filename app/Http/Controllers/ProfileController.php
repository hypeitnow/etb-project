<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AdminNotification;
use App\Models\News;
use App\Models\Player;
use App\Models\Sponsor;
use App\Models\TeamMatch;
use App\Models\TeamStaff;
use App\Models\ThreeXThreeMember;
use App\Models\ThreeXThreeTournament;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        $upcomingMatches = TeamMatch::query()
            ->with(['opponent', 'sportsHall'])
            ->where('status', TeamMatch::STATUS_UPCOMING)
            ->orderBy('match_date')
            ->get();

        $finishedMatches = TeamMatch::query()
            ->with(['opponent', 'sportsHall'])
            ->where('status', TeamMatch::STATUS_FINISHED)
            ->orderByDesc('match_date')
            ->get();

        $publishedNews = News::query()
            ->with(['author', 'images'])
            ->active()
            ->published()
            ->latest()
            ->get();

        $scheduledNews = News::query()
            ->with(['author', 'images'])
            ->scheduled()
            ->orderBy('publish_at')
            ->get();

        $players = Player::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $staff = TeamStaff::query()->orderBy('sort_order')->orderBy('name')->get();
        $threeXThreeMembers = ThreeXThreeMember::query()->orderByDesc('is_coach')->orderBy('sort_order')->orderBy('name')->get();
        $threeXThreeTournaments = ThreeXThreeTournament::query()->with('categories')->orderByDesc('date')->get();
        $sponsors = Sponsor::query()->orderBy('type')->orderBy('sort_order')->orderBy('name')->get();
        $adminNotifications = AdminNotification::query()
            ->with(['actor', 'acceptedBy'])
            ->latest()
            ->get();
        $unreadNotificationsCount = $adminNotifications->whereNull('read_at')->count();

        return view('profile.edit', [
            'user' => $user,
            'isAdmin' => $user->role === User::ROLE_ADMIN,
            'isEmployee' => $user->role === User::ROLE_EMPLOYEE,
            'isAthlete' => $user->role === User::ROLE_ATHLETE,
            'users' => $user->role === User::ROLE_ADMIN
                ? User::query()->orderBy('name')->paginate(5, ['id', 'name', 'email', 'role'])
                : collect(),
            'athleteProfile' => $user->role === User::ROLE_ATHLETE
                ? $user->athleteProfile()->first()
                : null,
            'availableRoles' => User::roles(),
            'upcomingMatches' => $upcomingMatches,
            'finishedMatches' => $finishedMatches,
            'publishedNews' => $publishedNews,
            'scheduledNews' => $scheduledNews,
            'players' => $players,
            'staff' => $staff,
            'threeXThreeMembers' => $threeXThreeMembers,
            'threeXThreeTournaments' => $threeXThreeTournaments,
            'sponsors' => $sponsors,
            'sponsorTypes' => Sponsor::types(),
            'adminNotifications' => $adminNotifications,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
