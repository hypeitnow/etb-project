<?php

use App\Models\AdminNotification;
use App\Models\User;

function adminNotificationForTest(?User $actor, array $overrides = []): AdminNotification
{
    return AdminNotification::query()->create(array_merge([
        'actor_id' => $actor?->id,
        'action' => 'updated',
        'subject_type' => User::class,
        'subject_id' => $actor?->id,
        'subject_label' => 'Historia testowa',
        'description' => 'Testowa zmiana w systemie.',
    ], $overrides));
}

it('lets panel users mark every unread admin notification as read at once', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $actor = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);

    $firstUnread = adminNotificationForTest($actor, ['subject_label' => 'Pierwsza zmiana']);
    $secondUnread = adminNotificationForTest($actor, ['subject_label' => 'Druga zmiana']);
    $alreadyRead = adminNotificationForTest($actor, [
        'subject_label' => 'Stara zmiana',
        'read_at' => now()->subDay(),
    ]);

    $this
        ->actingAs($admin)
        ->from(route('profile.edit'))
        ->patch(route('admin.notifications.read-all'))
        ->assertRedirect(route('profile.edit'));

    expect($firstUnread->refresh()->read_at)->not->toBeNull()
        ->and($secondUnread->refresh()->read_at)->not->toBeNull()
        ->and($alreadyRead->refresh()->read_at)->not->toBeNull();
});

it('shows the notification history section to employees on the profile page', function () {
    $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
    $actor = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'System Admin']);

    adminNotificationForTest($actor, [
        'subject_label' => 'Akademia: U15M',
        'description' => 'System Admin zaktualizował: Akademia U15M',
        'created_at' => now()->subDays(3),
    ]);

    $response = $this
        ->actingAs($employee)
        ->get(route('profile.edit', ['section' => 'notifications-history']));

    $response->assertOk();
    $response->assertSee('Historia zmian');
    $response->assertSee('Akademia: U15M');
    $response->assertSee('System Admin zaktualizował: Akademia U15M');
});

it('keeps removed notifications visible in the full notification history', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $notification = adminNotificationForTest($admin, [
        'subject_label' => 'Zmiana zachowana w historii',
        'description' => 'Ta zmiana nie znika z historii.',
    ]);

    $this
        ->actingAs($admin)
        ->delete(route('admin.notifications.destroy', $notification))
        ->assertRedirect();

    $response = $this
        ->actingAs($admin)
        ->get(route('profile.edit', ['section' => 'notifications-history']));

    $response->assertOk();
    $response->assertSee('Zmiana zachowana w historii');
    $response->assertSee('Ta zmiana nie znika z historii.');
});

it('keeps notification actions and history hidden from non panel users', function () {
    $fan = User::factory()->create(['role' => User::ROLE_FAN]);
    $actor = User::factory()->create(['role' => User::ROLE_ADMIN]);

    adminNotificationForTest($actor, [
        'subject_label' => 'Ukryta historia',
        'description' => 'Tego fan nie powinien zobaczyc.',
    ]);

    $this
        ->actingAs($fan)
        ->patch(route('admin.notifications.read-all'))
        ->assertForbidden();

    $response = $this
        ->actingAs($fan)
        ->get(route('profile.edit', ['section' => 'notifications-history']));

    $response->assertOk();
    $response->assertDontSee('Historia zmian');
    $response->assertDontSee('Ukryta historia');
});
