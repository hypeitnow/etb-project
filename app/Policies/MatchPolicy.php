<?php

namespace App\Policies;

use App\Models\MatchGame;
use App\Models\User;

class MatchPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, MatchGame $gameMatch): bool
    {
        if ($gameMatch->publish_at === null || $gameMatch->publish_at->isPast()) {
            return true;
        }

        return $user?->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE]) ?? false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE]);
    }

    public function update(User $user, MatchGame $gameMatch): bool
    {
        return $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE]);
    }

    public function delete(User $user, MatchGame $gameMatch): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }
}
