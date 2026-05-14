<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_id',
        'action',
        'subject_type',
        'subject_id',
        'subject_label',
        'description',
        'payload',
        'read_at',
        'accepted_by',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'read_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }
}
