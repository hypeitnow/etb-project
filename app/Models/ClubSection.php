<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClubSection extends Model
{
    use HasFactory;

    public const SECTIONS = [
        'history' => 'Historia',
        'board' => 'Władze klubu',
        'venue' => 'Obiekt',
        'business' => 'Oferta biznesowa',
        'success' => 'Sukcesy',
        'sponsors' => 'Sponsorzy',
        'contact' => 'Kontakt',
    ];

    protected $fillable = [
        'slug',
        'title',
        'body',
        'sort_order',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(ClubSectionImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public static function syncDefaults(): void
    {
        foreach (array_keys(self::SECTIONS) as $index => $slug) {
            self::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => self::SECTIONS[$slug],
                    'sort_order' => $index,
                ]
            );
        }
    }
}
