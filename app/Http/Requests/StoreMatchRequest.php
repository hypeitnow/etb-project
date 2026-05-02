<?php

namespace App\Http\Requests;

use App\Models\MatchGame;
use Illuminate\Foundation\Http\FormRequest;

class StoreMatchRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_home' => $this->boolean('is_home'),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('create', MatchGame::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'opponent_name' => ['required', 'string', 'max:255'],
            'match_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'is_home' => ['boolean'],
            'our_score' => ['nullable', 'integer', 'min:0', 'max:999', 'required_with:opponent_score'],
            'opponent_score' => ['nullable', 'integer', 'min:0', 'max:999', 'required_with:our_score'],
            'opponent_logo' => ['nullable', 'image', 'max:2048'],
            'publish_at' => ['nullable', 'date'],
        ];
    }
}
