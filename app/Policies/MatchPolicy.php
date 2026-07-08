<?php

namespace App\Policies;

use App\Models\TeamMatch;
use App\Models\User;

class MatchPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TeamMatch $match): bool
    {
        return $match->isPublished() || $user->hasAnyRole([
            User::ROLE_ADMIN,
            User::ROLE_EMPLOYEE,
            User::ROLE_ATHLETE,
            User::ROLE_TRAINER,
        ]);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE]);
    }

    public function update(User $user, TeamMatch $match): bool
    {
        return $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE]);
    }

    public function delete(User $user, TeamMatch $match): bool
    {
        return $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE]);
    }
}
