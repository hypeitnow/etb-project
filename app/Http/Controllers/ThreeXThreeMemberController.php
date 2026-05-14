<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThreeXThreeMemberRequest;
use App\Http\Requests\UpdateThreeXThreeMemberRequest;
use App\Models\ThreeXThreeMember;
use App\Models\User;
use App\Services\AdminNotificationService;
use App\Services\MediaCardService;
use Illuminate\Http\RedirectResponse;

class ThreeXThreeMemberController extends Controller
{
    public function __construct(
        private readonly MediaCardService $mediaCardService,
        private readonly AdminNotificationService $notificationService
    )
    {
    }

    public function store(StoreThreeXThreeMemberRequest $request): RedirectResponse
    {
        $member = $this->mediaCardService->create(ThreeXThreeMember::class, $request->safe()->except('photo'), $request->file('photo'), '3x3-team');
        $this->notificationService->record($request->user(), 'created', $member, "Drużyna 3x3: {$member->name}");

        return back()->with('success', 'Osoba z drużyny 3x3 została zapisana.');
    }

    public function update(UpdateThreeXThreeMemberRequest $request, ThreeXThreeMember $member): RedirectResponse
    {
        $this->mediaCardService->update($member, $request->safe()->except('photo'), $request->file('photo'), '3x3-team');
        $this->notificationService->record($request->user(), 'updated', $member, "Drużyna 3x3: {$member->name}");

        return back()->with('success', 'Osoba z drużyny 3x3 została zaktualizowana.');
    }

    public function destroy(ThreeXThreeMember $member): RedirectResponse
    {
        abort_unless(request()->user()?->role === User::ROLE_ADMIN, 403);

        $label = "Drużyna 3x3: {$member->name}";
        $id = $member->id;
        $this->mediaCardService->delete($member);
        $this->notificationService->recordDeleted(request()->user(), ThreeXThreeMember::class, $id, $label);

        return back()->with('success', 'Osoba z drużyny 3x3 została usunięta.');
    }
}
