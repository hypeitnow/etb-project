<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubSectionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_section_id',
        'image_path',
        'alt',
        'caption',
        'sort_order',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ClubSection::class, 'club_section_id');
    }
}
