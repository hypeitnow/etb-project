<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller
{
    public function __construct(private readonly AdminNotificationService $notificationService) {}

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(User::roles())],
        ]);

        $previousRole = $user->role;
        $user->update(['role' => $validated['role']]);

        $this->notificationService->record(
            $request->user(),
            'updated',
            $user,
            "Użytkownik: {$user->name}",
            "{$request->user()->name} zmienił rolę użytkownika {$user->name} z {$previousRole} na {$validated['role']}."
        );

        return back()->with('success', 'Rola użytkownika została zaktualizowana.');
    }
}
