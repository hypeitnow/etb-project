<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_path',
        'source_team_url',
        'is_league_team',
    ];

    protected $casts = [
        'is_league_team' => 'boolean',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(TeamMatch::class);
    }
}
