<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('password reset request uses a generic response for unknown email addresses', function () {
    Notification::fake();

    $response = $this
        ->from('/forgot-password')
        ->post('/forgot-password', ['email' => 'missing@example.com']);

    $response
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', 'Jeśli podany adres istnieje w naszej bazie, wyślemy link do resetu hasła.')
        ->assertRedirect('/forgot-password');

    Notification::assertNothingSent();
});

test('password reset request keeps the same generic response when email exists', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this
        ->from('/forgot-password')
        ->post('/forgot-password', ['email' => $user->email]);

    $response
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', 'Jeśli podany adres istnieje w naszej bazie, wyślemy link do resetu hasła.')
        ->assertRedirect('/forgot-password');

    Notification::assertSentTo($user, ResetPassword::class);
});
