<?php

namespace App\Http\Requests;

use App\Enums\ThreeXThreeCategory;
use App\Models\ThreeXThreeTournament;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreThreeXThreeTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE]) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'string', Rule::in([ThreeXThreeTournament::STATUS_UPCOMING, ThreeXThreeTournament::STATUS_FINISHED])],
            'organizer' => ['nullable', 'string', 'max:255'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', Rule::in(ThreeXThreeCategory::values())],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
