<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AdminNotificationController extends Controller
{
    public function read(AdminNotification $notification): RedirectResponse
    {
        $notification->update(['read_at' => now()]);

        return back()->with('success', 'Powiadomienie oznaczono jako przeczytane.');
    }

    public function accept(AdminNotification $notification): RedirectResponse
    {
        $notification->update([
            'read_at' => $notification->read_at ?? now(),
            'accepted_by' => request()->user()->id,
            'accepted_at' => now(),
        ]);

        return back()->with('success', 'Powiadomienie zaakceptowane.');
    }

    public function destroy(AdminNotification $notification): RedirectResponse
    {
        $user = request()->user();
        abort_unless($user instanceof User, 403);
        abort_unless($user->isAdmin() || $notification->actor_id === $user->id, 403);

        $notification->delete();

        return back()->with('success', 'Powiadomienie usunięte.');
    }
}
