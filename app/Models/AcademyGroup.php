<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'color',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function trainers(): HasMany
    {
        return $this->hasMany(AcademyTrainer::class)->orderBy('sort_order')->orderBy('name');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AcademyMessage::class)->latest('published_at')->latest();
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(AcademyTraining::class)->orderBy('starts_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
