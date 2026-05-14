<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamStaff extends Model
{
    use HasFactory;

    protected $table = 'team_staff';

    protected $fillable = [
        'name',
        'role',
        'description',
        'photo_path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
