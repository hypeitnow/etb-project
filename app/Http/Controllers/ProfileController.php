<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AdminNotification;
use App\Models\AcademyCalendarNote;
use App\Models\AcademyGroup;
use App\Models\AcademyTraining;
use App\Models\AppSetting;
use App\Models\ClubSection;
use App\Models\LeagueStanding;
use App\Models\MatchGame;
use App\Models\News;
use App\Models\Player;
use App\Models\Sponsor;
use App\Models\TeamStaff;
use App\Models\ThreeXThreeMember;
use App\Models\ThreeXThreeTournament;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->role === User::ROLE_ADMIN;
        $isEmployee = $user->role === User::ROLE_EMPLOYEE;
        $isPanelUser = $isAdmin || $isEmployee;
        $adminSections = [
            'dashboard',
            'users',
            'matches',
            'club-content',
            'academy',
            'league-table',
            'news',
            'players',
            'staff',
            'three-x-three',
            'tournaments',
            'sponsors',
            'notifications-history',
            'account',
        ];
        $activeSection = $isPanelUser && in_array($request->query('section'), $adminSections, true)
            ? (string) $request->query('section')
            : 'dashboard';
        $userRoleFilter = in_array($request->query('user_role'), User::roles(), true)
            ? (string) $request->query('user_role')
            : 'all';
        $marketingConsentFilter = in_array($request->query('marketing_consent'), ['yes', 'no'], true)
            ? (string) $request->query('marketing_consent')
            : 'all';

        ClubSection::syncDefaults();

        $upcomingMatches = MatchGame::query()
            ->with(['opponent', 'sportsHall'])
            ->where('status', MatchGame::STATUS_UPCOMING)
            ->orderBy('match_date')
            ->get();

        $finishedMatches = MatchGame::query()
            ->with(['opponent', 'sportsHall'])
            ->where('status', MatchGame::STATUS_FINISHED)
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
        $threeXThreeTournaments = ThreeXThreeTournament::query()
            ->with([
                'categories',
                'teams.players',
                'teams.group',
                'groups.teams.players',
                'groups.matches.teamOne',
                'groups.matches.teamTwo',
                'matches.teamOne',
                'matches.teamTwo',
                'matches.group',
            ])
            ->orderByDesc('date')
            ->get();
        $sponsors = Sponsor::query()->orderBy('type')->orderBy('sort_order')->orderBy('name')->get();
        $academyGroups = AcademyGroup::query()
            ->with(['trainers', 'messages', 'trainings'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $academyTrainingDate = $this->dateFilter($request->query('academy_training_date'));
        $academyTrainings = AcademyTraining::query()
            ->with('group')
            ->when($academyTrainingDate, fn ($query) => $query->whereDate('starts_at', $academyTrainingDate))
            ->orderByDesc('starts_at')
            ->take(80)
            ->get();
        $academyCalendarNotes = AcademyCalendarNote::query()
            ->orderByDesc('starts_on')
            ->take(30)
            ->get();
        $leagueStandings = LeagueStanding::query()
            ->with('opponent')
            ->orderBy('position')
            ->get();
        $clubSections = ClubSection::query()
            ->with('images')
            ->whereIn('slug', array_keys(ClubSection::SECTIONS))
            ->orderBy('sort_order')
            ->get();
        $adminNotifications = collect();
        $notificationHistory = new LengthAwarePaginator([], 0, 25);
        $unreadNotificationsCount = 0;

        if ($isPanelUser) {
            $adminNotifications = AdminNotification::query()
                ->with(['actor', 'acceptedBy'])
                ->latest()
                ->take(20)
                ->get();
            $notificationHistory = AdminNotification::query()
                ->withTrashed()
                ->with(['actor', 'acceptedBy'])
                ->latest()
                ->paginate(25, ['*'], 'notifications_page')
                ->withQueryString();
            $unreadNotificationsCount = AdminNotification::query()
                ->whereNull('read_at')
                ->count();
        }

        return view('profile.edit', [
            'user' => $user,
            'isAdmin' => $isAdmin,
            'isEmployee' => $isEmployee,
            'isAthlete' => $user->role === User::ROLE_ATHLETE,
            'users' => $user->role === User::ROLE_ADMIN
                ? User::query()
                    ->with('fanProfile')
                    ->when($userRoleFilter !== 'all', fn ($query) => $query->where('role', $userRoleFilter))
                    ->when($marketingConsentFilter !== 'all', function ($query) use ($marketingConsentFilter): void {
                        $query->whereHas('fanProfile', fn ($profileQuery) => $profileQuery->where('marketing_email_consent', $marketingConsentFilter === 'yes'));
                    })
                    ->orderBy('name')
                    ->paginate(10, ['id', 'name', 'email', 'role'])
                    ->withQueryString()
                : collect(),
            'athleteProfile' => $user->role === User::ROLE_ATHLETE
                ? $user->athleteProfile()->first()
                : null,
            'availableRoles' => User::roles(),
            'activeSection' => $activeSection,
            'userRoleFilter' => $userRoleFilter,
            'marketingConsentFilter' => $marketingConsentFilter,
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
            'academyGroups' => $academyGroups,
            'academyTrainings' => $academyTrainings,
            'academyTrainingDate' => $academyTrainingDate,
            'academyCalendarNotes' => $academyCalendarNotes,
            'leagueStandings' => $leagueStandings,
            'defaultHomeLogo' => AppSetting::getValue('default_home_logo'),
            'clubSections' => $clubSections,
            'adminNotifications' => $adminNotifications,
            'notificationHistory' => $notificationHistory,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ]);
    }

    private function dateFilter(mixed $value): ?string
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        return $value;
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($request->user()->role === User::ROLE_FAN) {
            $request->user()->fanProfile()->updateOrCreate(
                ['user_id' => $request->user()->id],
                [
                    'can_buy_tickets' => true,
                    'can_buy_merch' => true,
                    'marketing_email_consent' => $request->boolean('marketing_email_consent'),
                ],
            );
        }

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
