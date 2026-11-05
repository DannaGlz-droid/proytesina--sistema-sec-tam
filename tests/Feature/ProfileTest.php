<?php

use App\Models\Role;
use App\Models\User;

test('profile page displays the authenticated account', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('usuario.miperfil'))
        ->assertOk()
        ->assertSee('Mi perfil')
        ->assertSee($user->email);
});

test('legacy profile url redirects to the current profile page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile')
        ->assertRedirect(route('usuario.miperfil'));
});

test('legacy profile endpoint does not update account information', function () {
    $user = User::factory()->create();
    $originalName = $user->name;
    $originalEmail = $user->email;

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => 'Nombre modificado',
            'email' => 'modified@example.com',
        ])
        ->assertRedirect(route('usuario.miperfil'));

    $user->refresh();
    $this->assertSame($originalName, $user->name);
    $this->assertSame($originalEmail, $user->email);
});

test('legacy profile endpoint does not delete the account', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete('/profile', ['password' => 'password'])
        ->assertRedirect(route('usuario.miperfil'));

    $this->assertAuthenticatedAs($user);
    $this->assertNotNull($user->fresh());
});

test('administrator profile links to current account management', function () {
    $administrator = User::factory()->create([
        'role_id' => Role::query()->firstOrCreate(['name' => 'Administrador'])->id,
    ]);

    $this->actingAs($administrator)
        ->get(route('usuario.miperfil'))
        ->assertOk()
        ->assertSee('Administrar esta cuenta')
        ->assertSee(route('user.edit', ['user' => $administrator, 'from' => 'profile']), false);
});
