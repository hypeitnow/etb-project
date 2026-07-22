<?php

namespace App\Providers;

use App\Models\News;
use App\Models\Player;
use App\Models\TeamMatch;
use App\Policies\MatchPolicy;
use App\Policies\NewsPolicy;
use App\Policies\PlayerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Player::class => PlayerPolicy::class,
        News::class => NewsPolicy::class,
        TeamMatch::class => MatchPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
