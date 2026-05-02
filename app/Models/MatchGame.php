<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'match_date',
        'location',
        'is_home',
        'our_score',
        'opponent_score',
        'opponent_logo',
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
}
