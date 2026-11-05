<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\District;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_mass_delete_removes_notifications_before_deleting_users(): void
    {
        $administrator = User::factory()->create([
            'role_id' => Role::query()->firstOrCreate(['name' => 'Administrador'])->id,
        ]);
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $unrelatedUser = User::factory()->create();

        $receivedNotification = Notification::create([
            'recipient_user_id' => $firstUser->id,
            'sender_user_id' => $unrelatedUser->id,
            'type' => 'system',
            'title' => 'Recibida',
            'message' => 'Notificación recibida por el usuario eliminado.',
        ]);
        $sentNotification = Notification::create([
            'recipient_user_id' => $unrelatedUser->id,
            'sender_user_id' => $secondUser->id,
            'type' => 'system',
            'title' => 'Enviada',
            'message' => 'Notificación enviada por el usuario eliminado.',
        ]);
        $archivedNotification = Notification::create([
            'recipient_user_id' => $secondUser->id,
            'type' => 'system',
            'title' => 'Archivada',
            'message' => 'Notificación eliminada lógicamente.',
        ]);
        $archivedNotification->delete();

        $response = $this->actingAs($administrator)->postJson(route('user.massDelete'), [
            'ids' => [$firstUser->id, $secondUser->id],
        ]);

        $response->assertOk()
            ->assertJson([
                'ok' => true,
                'deleted' => 2,
            ]);

        $this->assertDatabaseMissing('users', ['id' => $firstUser->id]);
        $this->assertDatabaseMissing('users', ['id' => $secondUser->id]);
        $this->assertDatabaseHas('users', ['id' => $unrelatedUser->id]);
        $this->assertDatabaseMissing('notifications', ['id' => $receivedNotification->id]);
        $this->assertDatabaseMissing('notifications', ['id' => $sentNotification->id]);
        $this->assertDatabaseMissing('notifications', ['id' => $archivedNotification->id]);
    }

    public function test_authenticated_user_cannot_include_their_own_account_in_mass_delete(): void
    {
        $administrator = $this->administrator();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($administrator)->postJson(route('user.massDelete'), [
            'ids' => [$administrator->id, $otherUser->id],
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'ok' => false,
                'message' => 'No puedes eliminar tu propia cuenta.',
            ]);

        $this->assertDatabaseHas('users', ['id' => $administrator->id]);
        $this->assertDatabaseHas('users', ['id' => $otherUser->id]);
    }

    public function test_authenticated_user_cannot_delete_their_own_account_directly(): void
    {
        $administrator = $this->administrator();

        $this->actingAs($administrator)
            ->deleteJson(route('user.destroy', $administrator))
            ->assertStatus(422)
            ->assertJson([
                'ok' => false,
                'message' => 'No puedes eliminar tu propia cuenta.',
            ]);

        $this->assertDatabaseHas('users', ['id' => $administrator->id]);
    }

    public function test_authenticated_user_cannot_deactivate_their_own_account(): void
    {
        $administrator = $this->administrator();
        $district = District::query()->create(['name' => 'I - VICTORIA']);
        $position = Position::query()->firstOrCreate(['name' => 'Pruebas']);
        $administrator->update([
            'district_id' => $district->id,
            'position_id' => $position->id,
        ]);

        $this->actingAs($administrator)
            ->putJson(route('user.update', $administrator), [
                'name' => $administrator->name,
                'first_last_name' => $administrator->first_last_name,
                'second_last_name' => $administrator->second_last_name,
                'email' => $administrator->email,
                'phone' => $administrator->phone,
                'username' => $administrator->username,
                'position_id' => $position->id,
                'district_id' => $district->id,
                'role_id' => $administrator->role_id,
                'is_active' => false,
                'is_system_contact' => false,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('is_active');

        $this->assertTrue($administrator->refresh()->is_active);
    }

    public function test_user_table_marks_the_current_account_and_hides_its_delete_action(): void
    {
        $administrator = $this->administrator();
        $otherUser = User::factory()->create();

        $data = $this->actingAs($administrator)
            ->postJson(route('user.datatable'), [
                'draw' => 1,
                'start' => 0,
                'length' => 25,
            ])
            ->assertOk()
            ->json('data');

        $currentRow = collect($data)->firstWhere('id', $administrator->id);
        $otherRow = collect($data)->firstWhere('id', $otherUser->id);

        $this->assertTrue($currentRow['is_current_user']);
        $this->assertStringNotContainsString('Eliminar', $currentRow['actions']);
        $this->assertFalse($otherRow['is_current_user']);
        $this->assertStringContainsString('Eliminar', $otherRow['actions']);
    }

    public function test_own_edit_form_keeps_status_active_and_disabled(): void
    {
        $administrator = $this->administrator();

        $this->actingAs($administrator)
            ->get(route('user.edit', $administrator))
            ->assertOk()
            ->assertSee('Tu propia cuenta debe permanecer activa.')
            ->assertSee('name="is_active" value="1"', false)
            ->assertSee('id="status_select"', false)
            ->assertSee('disabled', false);
    }

    public function test_system_contact_cannot_be_deleted_individually_or_in_bulk(): void
    {
        $administrator = $this->administrator();
        $systemContact = $this->systemContactAdministrator();

        $this->actingAs($administrator)
            ->deleteJson(route('user.destroy', $systemContact))
            ->assertStatus(422)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('message', 'No puedes eliminar al responsable principal. Designa primero a otro administrador activo.');

        $this->actingAs($administrator)
            ->postJson(route('user.massDelete'), ['ids' => [$systemContact->id]])
            ->assertStatus(422)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('message', 'No puedes eliminar al responsable principal. Designa primero a otro administrador activo.');

        $this->assertDatabaseHas('users', ['id' => $systemContact->id]);
    }

    public function test_system_contact_cannot_be_deactivated_demoted_or_unassigned_directly(): void
    {
        $administrator = $this->administrator();
        $systemContact = $this->systemContactAdministrator();
        $operatorRole = Role::query()->firstOrCreate(['name' => 'Operador']);

        $this->actingAs($administrator)
            ->putJson(route('user.update', $systemContact), $this->validUpdateData($systemContact, [
                'is_active' => false,
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('is_active');

        $this->actingAs($administrator)
            ->putJson(route('user.update', $systemContact), $this->validUpdateData($systemContact, [
                'role_id' => $operatorRole->id,
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('role_id');

        $this->actingAs($administrator)
            ->putJson(route('user.update', $systemContact), $this->validUpdateData($systemContact, [
                'is_system_contact' => false,
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('is_system_contact');

        $systemContact->refresh();
        $this->assertTrue($systemContact->is_active);
        $this->assertTrue($systemContact->is_system_contact);
        $this->assertSame('Administrador', $systemContact->role->name);
    }

    public function test_assigning_another_active_administrator_transfers_system_contact_protection(): void
    {
        $administrator = $this->administrator();
        $formerSystemContact = $this->systemContactAdministrator();
        $newSystemContact = $this->systemContactAdministrator(['is_system_contact' => false]);

        $this->actingAs($administrator)
            ->putJson(route('user.update', $newSystemContact), $this->validUpdateData($newSystemContact, [
                'is_system_contact' => true,
            ]))
            ->assertRedirect(route('user.user-gestion'));

        $this->assertFalse($formerSystemContact->refresh()->is_system_contact);
        $this->assertTrue($newSystemContact->refresh()->is_system_contact);

        $this->actingAs($administrator)
            ->deleteJson(route('user.destroy', $formerSystemContact))
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseMissing('users', ['id' => $formerSystemContact->id]);
    }

    public function test_system_contact_is_locked_in_the_table_and_edit_form(): void
    {
        $administrator = $this->administrator();
        $systemContact = $this->systemContactAdministrator();

        $data = $this->actingAs($administrator)
            ->postJson(route('user.datatable'), [
                'draw' => 1,
                'start' => 0,
                'length' => 25,
            ])
            ->assertOk()
            ->json('data');

        $systemContactRow = collect($data)->firstWhere('id', $systemContact->id);
        $this->assertTrue($systemContactRow['is_system_contact']);
        $this->assertStringNotContainsString('Eliminar', $systemContactRow['actions']);

        $this->actingAs($administrator)
            ->get(route('user.edit', $systemContact))
            ->assertOk()
            ->assertSee('El responsable principal debe conservar el rol de Administrador.')
            ->assertSee('El responsable principal debe permanecer activo.')
            ->assertSee('name="is_system_contact" value="1"', false);
    }

    private function administrator(): User
    {
        return User::factory()->create([
            'role_id' => Role::query()->firstOrCreate(['name' => 'Administrador'])->id,
        ]);
    }

    private function systemContactAdministrator(array $attributes = []): User
    {
        $district = District::query()->firstOrCreate(['name' => 'I - VICTORIA']);
        $position = Position::query()->firstOrCreate(['name' => 'Líder estatal']);

        return User::factory()->create(array_merge([
            'role_id' => Role::query()->firstOrCreate(['name' => 'Administrador'])->id,
            'district_id' => $district->id,
            'position_id' => $position->id,
            'is_active' => true,
            'is_system_contact' => true,
        ], $attributes));
    }

    private function validUpdateData(User $user, array $overrides = []): array
    {
        return array_merge([
            'name' => $user->name,
            'first_last_name' => $user->first_last_name,
            'second_last_name' => $user->second_last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'username' => $user->username,
            'position_id' => $user->position_id,
            'district_id' => $user->district_id,
            'role_id' => $user->role_id,
            'is_active' => true,
            'is_system_contact' => true,
        ], $overrides);
    }
}
