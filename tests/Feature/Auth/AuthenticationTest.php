<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

test('login screen can be rendered with defensive headers', function () {
    $response = $this->get('/login');

    $response->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY');

    expect($response->headers->get('Cache-Control'))->toContain('no-store');
});

test('active users can authenticate with their username', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'login' => $user->username,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('reportes.index', absolute: false));
});

test('active users can authenticate with their email', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'login' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
});

test('json endpoints are not used as post-login destinations', function () {
    $user = User::factory()->create();

    $response = $this
        ->withSession(['url.intended' => url('/notificaciones')])
        ->post('/login', [
            'login' => $user->username,
            'password' => 'password',
        ]);

    $response->assertRedirect(route('reportes.index', absolute: false));
});

test('external URLs are not used as post-login destinations', function () {
    $user = User::factory()->create();

    $response = $this
        ->withSession(['url.intended' => 'https://example.com/phishing'])
        ->post('/login', [
            'login' => $user->username,
            'password' => 'password',
        ]);

    $response->assertRedirect(route('reportes.index', absolute: false));
});

test('authenticated users are redirected away from the login screen', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect(route('reportes.index'));
});

test('inactive users cannot authenticate', function () {
    $user = User::factory()->create(['is_active' => false]);

    $this->post('/login', [
        'login' => $user->username,
        'password' => 'password',
    ])->assertSessionHasErrors('login');

    $this->assertGuest();
});

test('an authenticated user is logged out after being deactivated', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $user->update(['is_active' => false]);

    $this->get('/reportes/publicaciones')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('users cannot authenticate with an invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'login' => $user->username,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('login attempts are rate limited', function () {
    RateLimiter::clear('login:test-user|127.0.0.1');
    $payload = ['login' => 'test-user', 'password' => 'wrong-password'];

    foreach (range(1, 5) as $attempt) {
        $this->post('/login', $payload);
    }

    $this->post('/login', $payload)
        ->assertSessionHasErrors('login');

    expect(RateLimiter::tooManyAttempts('login:test-user|127.0.0.1', 5))->toBeTrue();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});

test('expired logout forms redirect to login instead of showing 419', function () {
    $response = $this
        ->withMiddleware()
        ->withSession(['_token' => 'current-token'])
        ->post('/logout', ['_token' => 'expired-token']);

    $response->assertRedirect(route('login'));
});

test('stale logout forms can close an authenticated session', function () {
    $user = User::factory()->create();

    $response = $this
        ->withMiddleware()
        ->actingAs($user)
        ->withSession(['_token' => 'current-token'])
        ->post('/logout', ['_token' => 'expired-token']);

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});

test('cross-origin logout requests are rejected', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->withHeader('Origin', 'https://example.com')
        ->post('/logout')
        ->assertForbidden();

    $this->assertAuthenticatedAs($user);
});
