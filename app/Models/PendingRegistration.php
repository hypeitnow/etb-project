<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'accepted_terms',
        'accepted_privacy',
        'marketing_email_consent',
        'verification_code',
        'verification_attempts',
        'code_expires_at',
    ];

    protected $hidden = [
        'password',
        'verification_code',
    ];

    protected function casts(): array
    {
        return [
            'accepted_terms' => 'boolean',
            'accepted_privacy' => 'boolean',
            'marketing_email_consent' => 'boolean',
            'verification_attempts' => 'integer',
            'code_expires_at' => 'datetime',
        ];
    }
}
