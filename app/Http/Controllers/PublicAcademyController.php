<?php

namespace App\Http\Controllers;

use App\Models\AcademyCalendarNote;
use App\Models\AcademyGroup;
use App\Models\AcademyTraining;
use App\Services\PolishHolidayService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicAcademyController extends Controller
{
    public function index(Request $request, PolishHolidayService $holidayService): View
    {
        $month = $request->date('month')?->startOfMonth() ?? now()->startOfMonth();
        $calendarStart = $month->copy()->startOfMonth()->startOfWeek();
        $calendarEnd = $month->copy()->endOfMonth()->endOfWeek();
        $groups = AcademyGroup::query()
            ->active()
            ->with('trainers')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $trainings = AcademyTraining::query()
            ->with('group')
            ->whereHas('group', fn ($query) => $query->active())
            ->whereBetween('starts_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->orderBy('starts_at')
            ->get();
        $calendarNotes = AcademyCalendarNote::query()
            ->whereDate('starts_on', '<=', $month->copy()->endOfMonth())
            ->whereDate('ends_on', '>=', $month->copy()->startOfMonth())
            ->orderBy('starts_on')
            ->get();
        $publicHolidays = $holidayService->between($calendarStart, $calendarEnd);
        $upcomingTrainings = AcademyTraining::query()
            ->with('group')
            ->whereHas('group', fn ($query) => $query->active())
            ->upcoming()
            ->take(6)
            ->get();

        return view('pages.academy', [
            'groups' => $groups,
            'trainings' => $trainings,
            'calendarNotes' => $calendarNotes,
            'publicHolidays' => $publicHolidays,
            'holidaySourceUrl' => config('services.nager_date.base_url', 'https://date.nager.at'),
            'upcomingTrainings' => $upcomingTrainings,
            'month' => $month,
        ]);
    }

    public function show(AcademyGroup $group): View
    {
        abort_unless($group->is_active, 404);

        $group->load([
            'trainers',
            'messages' => fn ($query) => $query->published(),
        ]);

        $trainings = $group->trainings()
            ->where('starts_at', '>=', now()->subDay())
            ->orderBy('starts_at')
            ->take(12)
            ->get();

        return view('pages.academy-group', [
            'group' => $group,
            'trainings' => $trainings,
        ]);
    }
}
