<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

test('email verification self service routes are disabled', function () {
    expect(Route::has('verification.notice'))->toBeFalse()
        ->and(Route::has('verification.verify'))->toBeFalse()
        ->and(Route::has('verification.send'))->toBeFalse();
});

test('email verification endpoint is not exposed', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/verify-email')
        ->assertNotFound();
});

test('unverified authenticated users can access their profile', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('usuario.miperfil'))
        ->assertOk();
});
