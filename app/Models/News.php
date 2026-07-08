<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'author_id',
        'publish_at',
        'is_visible',
        'main_image_path',
    ];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
            'is_visible' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(NewsImage::class)->orderBy('sort_order');
    }

    public function isScheduled(): bool
    {
        return $this->publish_at !== null && $this->publish_at->isFuture();
    }

    public function isPubliclyVisible(): bool
    {
        return $this->is_visible && ($this->publish_at === null || $this->publish_at->isPast());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereNull('publish_at')->orWhere('publish_at', '<=', now());
        });
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('is_visible', true)->where('publish_at', '>', now());
    }
}
