<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThreeXThreeTournamentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sort_order',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(ThreeXThreeTournament::class, 'three_x_three_tournament_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(ThreeXThreeTournamentMatch::class, 'group_id');
    }
}
