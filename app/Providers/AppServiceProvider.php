<?php

namespace App\Providers;

use App\Models\Sponsor;
use App\Models\User;
use App\Rules\NotCommonPassword;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Password::defaults(fn (): Password => Password::min((int) config('security.password.min_length', 15))
            ->max((int) config('security.password.max_length', 128))
            ->rules([new NotCommonPassword]));

        Gate::define('assign-roles', fn (User $user): bool => $user->isAdmin());

        Gate::define('access-admin-panel', fn (User $user): bool => $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE])
        );

        Gate::define('manage-players', fn (User $user): bool => $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE])
        );

        Gate::define('manage-news', fn (User $user): bool => $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE])
        );

        Gate::define('manage-matches', fn (User $user): bool => $user->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE])
        );

        View::composer('partials.footer', function ($view): void {
            $view->with('footerSponsorsByType', Sponsor::query()
                ->active()
                ->orderBy('type')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->groupBy('type'));
        });
    }
}
