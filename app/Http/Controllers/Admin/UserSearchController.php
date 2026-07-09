<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $role = in_array($request->query('role'), User::roles(), true)
            ? (string) $request->query('role')
            : 'all';
        $marketingConsent = in_array($request->query('marketing_consent'), ['yes', 'no'], true)
            ? (string) $request->query('marketing_consent')
            : 'all';

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $orderedIds = User::query()
            ->when($role !== 'all', fn ($builder) => $builder->where('role', $role))
            ->when($marketingConsent !== 'all', function ($builder) use ($marketingConsent): void {
                $builder->whereHas('fanProfile', fn ($profileQuery) => $profileQuery->where('marketing_email_consent', $marketingConsent === 'yes'));
            })
            ->orderBy('name')
            ->pluck('id')
            ->values();

        $users = User::query()
            ->with('fanProfile')
            ->when($role !== 'all', fn ($builder) => $builder->where('role', $role))
            ->when($marketingConsent !== 'all', function ($builder) use ($marketingConsent): void {
                $builder->whereHas('fanProfile', fn ($profileQuery) => $profileQuery->where('marketing_email_consent', $marketingConsent === 'yes'));
            })
            ->where(function ($builder) use ($query): void {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'email', 'role'])
            ->map(function (User $user) use ($orderedIds): array {
                $index = $orderedIds->search($user->id);

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'page' => $index === false ? 1 : (int) floor($index / 5) + 1,
                ];
            });

        return response()->json($users);
    }
}
