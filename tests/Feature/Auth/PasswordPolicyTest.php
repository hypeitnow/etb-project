<?php

use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

test('registration rejects passwords shorter than the privacy baseline', function () {
    Mail::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'short-password',
        'password_confirmation' => 'short-password',
        'accepted_terms' => '1',
        'accepted_privacy' => '1',
    ]);

    $response->assertSessionHasErrors('password');
    expect(PendingRegistration::where('email', 'test@example.com')->exists())->toBeFalse();
    Mail::assertNothingSent();
});

test('registration rejects common long passwords', function () {
    Mail::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'passwordpassword',
        'password_confirmation' => 'passwordpassword',
        'accepted_terms' => '1',
        'accepted_privacy' => '1',
    ]);

    $response->assertSessionHasErrors('password');
    expect(PendingRegistration::where('email', 'test@example.com')->exists())->toBeFalse();
    Mail::assertNothingSent();
});

test('password update requires the same privacy baseline', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->put('/password', [
            'current_password' => 'password',
            'password' => 'short-password',
            'password_confirmation' => 'short-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'password')
        ->assertRedirect('/profile');

    expect(Hash::check('short-password', $user->refresh()->password))->toBeFalse();
});

test('password reset requires the same privacy baseline', function () {
    $user = User::factory()->create();
    $token = app('auth.password.broker')->createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'short-password',
        'password_confirmation' => 'short-password',
    ]);

    $response->assertSessionHasErrors('password');
    expect(Hash::check('short-password', $user->refresh()->password))->toBeFalse();
});
