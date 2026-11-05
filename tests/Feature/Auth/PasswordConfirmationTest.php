<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

test('password confirmation self service routes are disabled', function () {
    expect(Route::has('password.confirm'))->toBeFalse()
        ->and(Route::has('password.confirmation'))->toBeFalse();
});

test('confirm password screen is not exposed', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/confirm-password')
        ->assertNotFound();
});

test('confirm password submissions are not accepted', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/confirm-password', ['password' => 'password'])
        ->assertNotFound();

    $this->assertAuthenticatedAs($user);
});
