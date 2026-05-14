<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchGame extends Model
{
    use HasFactory;

    public const STATUS_UPCOMING = 'upcoming';
    public const STATUS_FINISHED = 'finished';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'opponent_name',
        'opponent_id',
        'match_date',
        'location',
        'sports_hall_id',
        'is_home',
        'our_score',
        'opponent_score',
        'opponent_logo',
        'home_logo',
        'status',
        'publish_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'match_date' => 'datetime',
            'is_home' => 'boolean',
            'our_score' => 'integer',
            'opponent_score' => 'integer',
            'publish_at' => 'datetime',
        ];
    }

    public function hasResult(): bool
    {
        return $this->our_score !== null && $this->opponent_score !== null;
    }

    public function resultLabel(): string
    {
        if (! $this->hasResult()) {
            return 'Do rozegrania';
        }

        return "{$this->our_score}:{$this->opponent_score}";
    }

    public function isPublished(): bool
    {
        return $this->publish_at === null || $this->publish_at->isPast();
    }

    public function opponent(): BelongsTo
    {
        return $this->belongsTo(Opponent::class);
    }

    public function sportsHall(): BelongsTo
    {
        return $this->belongsTo(SportsHall::class);
    }
}
