<?php

namespace Tests\Feature;

use App\Models\Notification;
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
}
