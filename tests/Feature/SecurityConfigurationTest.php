<?php

test('persistent sessions are encrypted by default', function () {
    expect(config('session.encrypt'))->toBeTrue();
});

test('application prefers argon2id password hashes by default', function () {
    expect(config('hashing.driver'))->toBe('argon2id');
    expect(config('hashing.rehash_on_login'))->toBeTrue();
});

test('web responses include baseline privacy and hardening headers', function () {
    $response = $this->get('/login');

    $response
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
});
