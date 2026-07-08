<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamStaffRequest;
use App\Http\Requests\UpdateTeamStaffRequest;
use App\Models\TeamStaff;
use App\Models\User;
use App\Services\AdminNotificationService;
use App\Services\MediaCardService;
use Illuminate\Http\RedirectResponse;

class TeamStaffController extends Controller
{
    public function __construct(
        private readonly MediaCardService $mediaCardService,
        private readonly AdminNotificationService $notificationService
    ) {}

    public function store(StoreTeamStaffRequest $request): RedirectResponse
    {
        $staff = $this->mediaCardService->create(TeamStaff::class, $request->safe()->except('photo'), $request->file('photo'), 'staff');
        $this->notificationService->record($request->user(), 'created', $staff, "Sztab: {$staff->name}");

        return back()->with('success', 'Członek sztabu został zapisany.');
    }

    public function update(UpdateTeamStaffRequest $request, TeamStaff $staff): RedirectResponse
    {
        $this->mediaCardService->update($staff, $request->safe()->except('photo'), $request->file('photo'), 'staff');
        $this->notificationService->record($request->user(), 'updated', $staff, "Sztab: {$staff->name}");

        return back()->with('success', 'Członek sztabu został zaktualizowany.');
    }

    public function destroy(TeamStaff $staff): RedirectResponse
    {
        abort_unless(request()->user()?->role === User::ROLE_ADMIN, 403);

        $label = "Sztab: {$staff->name}";
        $id = $staff->id;
        $this->mediaCardService->delete($staff);
        $this->notificationService->recordDeleted(request()->user(), TeamStaff::class, $id, $label);

        return back()->with('success', 'Członek sztabu został usunięty.');
    }
}
