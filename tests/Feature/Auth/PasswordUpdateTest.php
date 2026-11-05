<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('administrator can update a user password', function () {
    $administrator = User::factory()->create([
        'role_id' => Role::query()->firstOrCreate(['name' => 'Administrador'])->id,
    ]);
    $user = User::factory()->create();

    $response = $this
        ->actingAs($administrator)
        ->from(route('user.user-gestion'))
        ->put(route('user.update-password.update', $user), [
            'password' => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('user.user-gestion'));

    $this->assertTrue(Hash::check('NewSecurePass123!', $user->refresh()->password));
});

test('password confirmation must match when an administrator updates a password', function () {
    $administrator = User::factory()->create([
        'role_id' => Role::query()->firstOrCreate(['name' => 'Administrador'])->id,
    ]);
    $user = User::factory()->create();
    $originalPassword = $user->password;

    $response = $this
        ->actingAs($administrator)
        ->from(route('user.user-gestion'))
        ->put(route('user.update-password.update', $user), [
            'password' => 'NewSecurePass123!',
            'password_confirmation' => 'DifferentSecurePass123!',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('user.user-gestion'));

    $this->assertSame($originalPassword, $user->refresh()->password);
});
