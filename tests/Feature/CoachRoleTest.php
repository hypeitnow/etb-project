<?php

use App\Models\TeamMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->trainer = User::factory()->create(['role' => User::ROLE_TRAINER]);
    $this->fan = User::factory()->create(['role' => User::ROLE_FAN]);
});

it('has ROLE_TRAINER constant defined', function () {
    expect(User::ROLE_TRAINER)->toBe('trainer');
});

it('includes trainer in valid roles list', function () {
    expect(User::roles())->toContain('trainer');
});

it('allows trainer to view published matches', function () {
    $match = TeamMatch::factory()->create([
        'publish_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($this->trainer)
        ->get(route('matches.show', $match));

    $response->assertOk();
});

it('denies trainer from creating matches', function () {
    $response = $this->actingAs($this->trainer)
        ->post(route('matches.store'), [
            'status' => TeamMatch::STATUS_UPCOMING,
            'opponent_name' => 'Test',
            'match_date' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'location' => 'Arena',
            'is_home' => true,
        ]);

    $response->assertForbidden();
});

it('grants trainer access to own data endpoint', function () {
    $response = $this->actingAs($this->trainer)
        ->get(route('trainer.data'));

    $response->assertOk();
});

it('denies fan access to trainer data endpoint', function () {
    $response = $this->actingAs($this->fan)
        ->get(route('trainer.data'));

    $response->assertForbidden();
});
