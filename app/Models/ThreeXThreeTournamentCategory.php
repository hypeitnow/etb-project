<?php

namespace App\Models;

use App\Enums\ThreeXThreeCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreeXThreeTournamentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
    ];

    protected function casts(): array
    {
        return [
            'category' => ThreeXThreeCategory::class,
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(ThreeXThreeTournament::class, 'three_x_three_tournament_id');
    }
}
