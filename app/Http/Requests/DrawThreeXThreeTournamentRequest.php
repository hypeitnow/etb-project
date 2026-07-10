<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class DrawThreeXThreeTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'groups_count' => ['required', 'integer', 'min:1', 'max:12'],
            'teams_per_group' => ['required', 'integer', 'min:2', 'max:8'],
            'qualifiers_per_group' => ['required', 'integer', 'min:1', 'max:4'],
            'generate_group_matches' => ['nullable', 'boolean'],
            'generate_playoff' => ['nullable', 'boolean'],
        ];
    }
}
