<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotificationRetentionTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_new_notifications_receive_a_three_month_expiration_date(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse('2026-07-28 20:45:00', config('notifications.timezone'))
        );
        $user = User::factory()->create();

        $notification = Notification::create([
            'recipient_user_id' => $user->id,
            'type' => 'system',
            'title' => 'Aviso',
            'message' => 'Mensaje',
        ]);

        $this->assertSame(
            '2026-10-28 20:45:00',
            $notification->expires_at->setTimezone(config('notifications.timezone'))->format('Y-m-d H:i:s')
        );
    }

    public function test_prune_removes_expired_and_legacy_notifications_only(): void
    {
        $now = CarbonImmutable::parse('2026-07-28 20:45:00', config('notifications.timezone'));
        CarbonImmutable::setTestNow($now);
        $user = User::factory()->create();

        $expired = Notification::create([
            'recipient_user_id' => $user->id,
            'type' => 'system',
            'title' => 'Vencida',
            'message' => 'Debe eliminarse',
            'expires_at' => $now->subMinute(),
        ]);
        $current = Notification::create([
            'recipient_user_id' => $user->id,
            'type' => 'system',
            'title' => 'Vigente',
            'message' => 'Debe conservarse',
            'expires_at' => $now->addDay(),
        ]);

        $legacyId = DB::table('notifications')->insertGetId([
            'recipient_user_id' => $user->id,
            'type' => 'system',
            'title' => 'Antigua',
            'message' => 'Registro sin expires_at',
            'read' => false,
            'created_at' => $now->subMonthsNoOverflow(3)->subMinute(),
            'updated_at' => $now->subMonthsNoOverflow(3)->subMinute(),
            'expires_at' => null,
        ]);

        $this->artisan('notifications:prune')->assertSuccessful();

        $this->assertDatabaseMissing('notifications', ['id' => $expired->id]);
        $this->assertDatabaseMissing('notifications', ['id' => $legacyId]);
        $this->assertDatabaseHas('notifications', ['id' => $current->id]);
    }
}
