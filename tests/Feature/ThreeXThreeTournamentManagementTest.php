<?php

use App\Enums\ThreeXThreeCategory;
use App\Models\ThreeXThreeTournament;
use App\Models\User;
use App\Services\ThreeXThreeTournamentFlowService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('separates tournaments we play from tournaments we organize on public pages', function () {
    ThreeXThreeTournament::query()->create([
        'name' => 'City Tour 3x3',
        'date' => now()->addMonth()->toDateString(),
        'location' => 'Warszawa',
        'status' => ThreeXThreeTournament::STATUS_UPCOMING,
        'organizer' => 'FIBA',
        'type' => ThreeXThreeTournament::TYPE_PARTICIPATING,
    ]);

    ThreeXThreeTournament::query()->create([
        'name' => 'Letnie Granie 3x3 ETB',
        'date' => now()->addMonth()->toDateString(),
        'location' => 'Lodz',
        'status' => ThreeXThreeTournament::STATUS_UPCOMING,
        'organizer' => 'ETB',
        'type' => ThreeXThreeTournament::TYPE_ORGANIZED,
        'registration_mode' => ThreeXThreeTournament::REGISTRATION_INTERNAL,
        'registration_enabled' => true,
        'team_size' => 4,
    ]);

    $scheduleResponse = $this->get(route('schedule.3x3'));
    $scheduleResponse->assertOk();
    $scheduleResponse->assertSee('City Tour 3x3');
    $scheduleResponse->assertDontSee('Letnie Granie 3x3 ETB');

    $organizedResponse = $this->get(route('three-x-three.tournaments.index'));
    $organizedResponse->assertOk();
    $organizedResponse->assertSee('Letnie Granie 3x3 ETB');
    $organizedResponse->assertDontSee('City Tour 3x3');
});

it('lets a signed in user register a team for an internal tournament', function () {
    Storage::fake('public');

    $fan = User::factory()->create(['role' => User::ROLE_FAN]);
    $tournament = ThreeXThreeTournament::query()->create([
        'name' => 'Letnie Granie 3x3 ETB',
        'date' => now()->addMonth()->toDateString(),
        'location' => 'Lodz',
        'status' => ThreeXThreeTournament::STATUS_UPCOMING,
        'type' => ThreeXThreeTournament::TYPE_ORGANIZED,
        'registration_mode' => ThreeXThreeTournament::REGISTRATION_INTERNAL,
        'registration_enabled' => true,
        'team_size' => 4,
    ]);
    $tournament->categories()->create(['category' => ThreeXThreeCategory::OpenM->value]);

    $response = $this->actingAs($fan)->post(route('three-x-three.tournaments.teams.store', $tournament), [
        'name' => 'Lodz Ballers',
        'category' => ThreeXThreeCategory::OpenM->value,
        'logo' => UploadedFile::fake()->create('team.png', 12, 'image/png'),
        'players' => [
            ['name' => 'Jan Kowalski'],
            ['name' => 'Adam Nowak'],
            ['name' => 'Piotr Zielinski'],
            ['name' => 'Marek Wisniewski'],
        ],
    ]);

    $response->assertRedirect(route('three-x-three.tournaments.show', $tournament));

    $this->assertDatabaseHas('three_x_three_tournament_teams', [
        'three_x_three_tournament_id' => $tournament->id,
        'user_id' => $fan->id,
        'name' => 'Lodz Ballers',
        'category' => ThreeXThreeCategory::OpenM->value,
    ]);

    expect($tournament->teams()->firstOrFail()->players)->toHaveCount(4);
    Storage::disk('public')->assertExists($tournament->teams()->firstOrFail()->logo_path);
});

it('rejects vulgar team names when registering for a tournament', function () {
    $fan = User::factory()->create(['role' => User::ROLE_FAN]);
    $tournament = ThreeXThreeTournament::query()->create([
        'name' => 'Letnie Granie 3x3 ETB',
        'date' => now()->addMonth()->toDateString(),
        'location' => 'Lodz',
        'status' => ThreeXThreeTournament::STATUS_UPCOMING,
        'type' => ThreeXThreeTournament::TYPE_ORGANIZED,
        'registration_mode' => ThreeXThreeTournament::REGISTRATION_INTERNAL,
        'registration_enabled' => true,
        'team_size' => 3,
    ]);
    $tournament->categories()->create(['category' => ThreeXThreeCategory::OpenM->value]);

    $response = $this->actingAs($fan)->post(route('three-x-three.tournaments.teams.store', $tournament), [
        'name' => 'Kurwa Team',
        'category' => ThreeXThreeCategory::OpenM->value,
        'players' => [
            ['name' => 'Jan Kowalski'],
            ['name' => 'Adam Nowak'],
            ['name' => 'Piotr Zielinski'],
        ],
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertDatabaseCount('three_x_three_tournament_teams', 0);
});

it('lets admins manage groups and matches for an organized tournament', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $tournament = ThreeXThreeTournament::query()->create([
        'name' => 'Letnie Granie 3x3 ETB',
        'date' => now()->addMonth()->toDateString(),
        'location' => 'Lodz',
        'status' => ThreeXThreeTournament::STATUS_UPCOMING,
        'type' => ThreeXThreeTournament::TYPE_ORGANIZED,
        'registration_mode' => ThreeXThreeTournament::REGISTRATION_INTERNAL,
        'team_size' => 3,
    ]);

    $teamA = $tournament->teams()->create([
        'user_id' => $admin->id,
        'name' => 'ETB Yellow',
        'category' => ThreeXThreeCategory::OpenM->value,
    ]);
    $teamB = $tournament->teams()->create([
        'user_id' => $admin->id,
        'name' => 'ETB Black',
        'category' => ThreeXThreeCategory::OpenM->value,
    ]);

    $groupResponse = $this->actingAs($admin)->post(route('admin.3x3.tournaments.groups.store', $tournament), [
        'name' => 'Grupa A',
        'sort_order' => 1,
    ]);
    $groupResponse->assertRedirect();
    $group = $tournament->groups()->firstOrFail();

    $matchResponse = $this->actingAs($admin)->post(route('admin.3x3.tournaments.matches.store', $tournament), [
        'stage' => 'group',
        'group_id' => $group->id,
        'team_one_id' => $teamA->id,
        'team_two_id' => $teamB->id,
        'team_one_score' => 21,
        'team_two_score' => 17,
        'played_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
    ]);

    $matchResponse->assertRedirect();
    $this->assertDatabaseHas('three_x_three_tournament_matches', [
        'three_x_three_tournament_id' => $tournament->id,
        'group_id' => $group->id,
        'team_one_id' => $teamA->id,
        'team_two_id' => $teamB->id,
        'team_one_score' => 21,
        'team_two_score' => 17,
        'stage' => 'group',
    ]);
});

it('draws teams into groups and creates a FIBA style tournament flow', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $tournament = ThreeXThreeTournament::query()->create([
        'name' => 'FIBA Style 3x3',
        'date' => now()->addMonth()->toDateString(),
        'location' => 'Lodz',
        'status' => ThreeXThreeTournament::STATUS_UPCOMING,
        'type' => ThreeXThreeTournament::TYPE_ORGANIZED,
        'registration_mode' => ThreeXThreeTournament::REGISTRATION_INTERNAL,
        'team_size' => 3,
    ]);

    foreach (['Alpha', 'Bravo', 'Charlie', 'Delta'] as $name) {
        $tournament->teams()->create([
            'user_id' => $admin->id,
            'name' => $name,
            'category' => ThreeXThreeCategory::OpenM->value,
        ]);
    }

    $response = $this->actingAs($admin)->post(route('admin.3x3.tournaments.draw', $tournament), [
        'groups_count' => 2,
        'teams_per_group' => 2,
        'qualifiers_per_group' => 1,
        'generate_group_matches' => 1,
        'generate_playoff' => 1,
    ]);

    $response->assertRedirect();
    expect($tournament->fresh()->groups)->toHaveCount(2);
    expect($tournament->fresh()->teams()->whereNotNull('group_id')->count())->toBe(4);
    expect($tournament->fresh()->matches()->where('stage', 'group')->count())->toBe(2);
    expect($tournament->fresh()->matches()->where('stage', 'playoff')->where('round_label', 'Finał')->count())->toBe(1);
});

it('calculates a group table from scored 3x3 matches', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $tournament = ThreeXThreeTournament::query()->create([
        'name' => 'Tabela 3x3',
        'date' => now()->addMonth()->toDateString(),
        'location' => 'Lodz',
        'status' => ThreeXThreeTournament::STATUS_UPCOMING,
        'type' => ThreeXThreeTournament::TYPE_ORGANIZED,
        'team_size' => 3,
    ]);
    $group = $tournament->groups()->create([
        'name' => 'Grupa A',
        'sort_order' => 1,
    ]);
    $teamA = $tournament->teams()->create([
        'user_id' => $admin->id,
        'group_id' => $group->id,
        'name' => 'ETB Yellow',
        'category' => ThreeXThreeCategory::OpenM->value,
    ]);
    $teamB = $tournament->teams()->create([
        'user_id' => $admin->id,
        'group_id' => $group->id,
        'name' => 'ETB Black',
        'category' => ThreeXThreeCategory::OpenM->value,
    ]);

    $group->matches()->create([
        'three_x_three_tournament_id' => $tournament->id,
        'stage' => 'group',
        'team_one_id' => $teamA->id,
        'team_two_id' => $teamB->id,
        'team_one_score' => 21,
        'team_two_score' => 17,
        'played_at' => now(),
    ]);

    $table = app(ThreeXThreeTournamentFlowService::class)->groupTable($group->fresh(['teams', 'matches']));

    expect($table)->toHaveCount(2)
        ->and($table[0]['team']->is($teamA))->toBeTrue()
        ->and($table[0]['played'])->toBe(1)
        ->and($table[0]['wins'])->toBe(1)
        ->and($table[0]['fiba_points'])->toBe(2)
        ->and($table[0]['form'])->toBe(['W'])
        ->and($table[1]['losses'])->toBe(1)
        ->and($table[1]['form'])->toBe(['L']);
});

it('renders the profile tournament section with scored 3x3 group matches', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $tournament = ThreeXThreeTournament::query()->create([
        'name' => 'Panel 3x3',
        'date' => now()->addMonth()->toDateString(),
        'location' => 'Lodz',
        'status' => ThreeXThreeTournament::STATUS_UPCOMING,
        'type' => ThreeXThreeTournament::TYPE_ORGANIZED,
        'team_size' => 3,
    ]);
    $group = $tournament->groups()->create([
        'name' => 'Grupa A',
        'sort_order' => 1,
    ]);
    $teamA = $tournament->teams()->create([
        'user_id' => $admin->id,
        'group_id' => $group->id,
        'name' => 'ETB Yellow',
        'category' => ThreeXThreeCategory::OpenM->value,
    ]);
    $teamB = $tournament->teams()->create([
        'user_id' => $admin->id,
        'group_id' => $group->id,
        'name' => 'ETB Black',
        'category' => ThreeXThreeCategory::OpenM->value,
    ]);

    $group->matches()->create([
        'three_x_three_tournament_id' => $tournament->id,
        'stage' => 'group',
        'team_one_id' => $teamA->id,
        'team_two_id' => $teamB->id,
        'team_one_score' => 21,
        'team_two_score' => 17,
        'played_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('profile.edit', ['section' => 'tournaments']));

    $response->assertOk();
    $response->assertSee('Panel 3x3');
    $response->assertSee('ETB Yellow');
    $response->assertSee('value="21"', false);
    $response->assertSee('value="17"', false);
});

it('shows team history with wins losses win rate and latest roster', function () {
    $fan = User::factory()->create(['role' => User::ROLE_FAN]);
    $tournament = ThreeXThreeTournament::query()->create([
        'name' => 'Letnie Granie 3x3 ETB',
        'date' => now()->subWeek()->toDateString(),
        'location' => 'Lodz',
        'status' => ThreeXThreeTournament::STATUS_FINISHED,
        'type' => ThreeXThreeTournament::TYPE_ORGANIZED,
        'team_size' => 3,
    ]);

    $teamA = $tournament->teams()->create([
        'user_id' => $fan->id,
        'name' => 'Lodz Ballers',
        'category' => ThreeXThreeCategory::OpenM->value,
    ]);
    $teamB = $tournament->teams()->create([
        'user_id' => $fan->id,
        'name' => 'City Hoopers',
        'category' => ThreeXThreeCategory::OpenM->value,
    ]);
    foreach (['Jan Kowalski', 'Adam Nowak', 'Piotr Zielinski'] as $index => $name) {
        $teamA->players()->create(['name' => $name, 'sort_order' => $index]);
    }

    $tournament->matches()->create([
        'stage' => 'group',
        'team_one_id' => $teamA->id,
        'team_two_id' => $teamB->id,
        'team_one_score' => 21,
        'team_two_score' => 14,
        'played_at' => now()->subDays(3),
    ]);

    $response = $this->get(route('three-x-three.teams.show', $teamA));

    $response->assertOk();
    $response->assertSee('Lodz Ballers');
    $response->assertSee('21:14');
    $response->assertSee('100%');
    $response->assertSee('Jan Kowalski');
});
