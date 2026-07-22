<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    public const TYPE_STRATEGIC = 'strategic';

    public const TYPE_PARTNER = 'partner';

    public const TYPE_SPONSOR = 'sponsor';

    public const TYPE_TECHNOLOGY = 'technology';

    protected $fillable = [
        'name',
        'type',
        'url',
        'logo_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_STRATEGIC => 'Partner strategiczny',
            self::TYPE_SPONSOR => 'Sponsorzy',
            self::TYPE_PARTNER => 'Partnerzy',
            self::TYPE_TECHNOLOGY => 'Partner technologiczny',
        ];
    }

    public function typeLabel(): string
    {
        return self::types()[$this->type] ?? $this->type;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
