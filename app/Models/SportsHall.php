<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SportsHall extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(MatchGame::class);
    }
}
