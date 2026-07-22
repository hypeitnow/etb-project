<?php

namespace App\Models;

use Database\Factories\TeamMatchFactory;

class MatchGame extends TeamMatch
{
    protected static function newFactory(): TeamMatchFactory
    {
        return TeamMatchFactory::new();
    }
}
