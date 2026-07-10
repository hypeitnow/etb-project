<?php

namespace App\Http\Requests;

use App\Models\ThreeXThreeTournamentMatch;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreThreeXThreeTournamentMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole([User::ROLE_ADMIN, User::ROLE_EMPLOYEE]) ?? false;
    }

    public function rules(): array
    {
        $tournament = $this->route('tournament');
        $tournamentId = $tournament?->id;

        return [
            'stage' => ['required', 'string', Rule::in([ThreeXThreeTournamentMatch::STAGE_GROUP, ThreeXThreeTournamentMatch::STAGE_PLAYOFF])],
            'group_id' => ['nullable', 'integer', Rule::exists('three_x_three_tournament_groups', 'id')->where('three_x_three_tournament_id', $tournamentId)],
            'team_one_id' => ['nullable', 'integer', Rule::exists('three_x_three_tournament_teams', 'id')->where('three_x_three_tournament_id', $tournamentId)],
            'team_two_id' => ['nullable', 'integer', Rule::exists('three_x_three_tournament_teams', 'id')->where('three_x_three_tournament_id', $tournamentId), 'different:team_one_id'],
            'team_one_placeholder' => ['nullable', 'string', 'max:255'],
            'team_two_placeholder' => ['nullable', 'string', 'max:255'],
            'team_one_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'team_two_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'round_label' => ['nullable', 'string', 'max:255'],
            'bracket_round_order' => ['nullable', 'integer', 'min:1', 'max:8'],
            'bracket_position' => ['nullable', 'integer', 'min:1', 'max:64'],
            'played_at' => ['nullable', 'date'],
            'court' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }
}
