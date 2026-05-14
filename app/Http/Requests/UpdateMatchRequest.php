<?php

namespace App\Http\Requests;

use App\Models\MatchGame;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMatchRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_home' => $this->boolean('is_home'),
        ]);
    }

    public function authorize(): bool
    {
        $match = $this->route('match');

        return $this->user()?->can('update', $match instanceof MatchGame ? $match : MatchGame::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in([MatchGame::STATUS_UPCOMING, MatchGame::STATUS_FINISHED])],
            'opponent_name' => ['required', 'string', 'max:255'],
            'match_date' => [
                'required',
                'date',
                Rule::when($this->input('status') === MatchGame::STATUS_UPCOMING, ['after_or_equal:today']),
            ],
            'location' => ['required', 'string', 'max:255'],
            'is_home' => ['boolean'],
            'our_score' => [
                'nullable',
                'integer',
                'min:0',
                'max:999',
                Rule::requiredIf($this->input('status') === MatchGame::STATUS_FINISHED),
            ],
            'opponent_score' => [
                'nullable',
                'integer',
                'min:0',
                'max:999',
                Rule::requiredIf($this->input('status') === MatchGame::STATUS_FINISHED),
            ],
            'opponent_logo' => ['nullable', 'image', 'max:2048'],
            'home_logo' => ['nullable', 'image', 'max:2048'],
            'publish_at' => ['nullable', 'date'],
        ];
    }
}
