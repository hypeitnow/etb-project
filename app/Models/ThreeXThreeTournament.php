<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThreeXThreeTournament extends Model
{
    use HasFactory;

    public const STATUS_UPCOMING = 'upcoming';

    public const STATUS_FINISHED = 'finished';

    protected $fillable = [
        'name',
        'date',
        'location',
        'description',
        'status',
        'organizer',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_UPCOMING);
    }

    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FINISHED);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ThreeXThreeTournamentCategory::class);
    }
}
