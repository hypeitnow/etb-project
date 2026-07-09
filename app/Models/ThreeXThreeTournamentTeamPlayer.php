<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreeXThreeTournamentTeamPlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sort_order',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(ThreeXThreeTournamentTeam::class, 'three_x_three_tournament_team_id');
    }
}
