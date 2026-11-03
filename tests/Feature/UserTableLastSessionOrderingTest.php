<?php

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

function userTableLastSessionResponse(string $direction): array
{
    $request = Request::create('/usuario/gestion-de-usuarios/datatable', 'POST', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
        'order' => [[
            'column' => 14,
            'dir' => $direction,
        ]],
    ]);

    return app(UserController::class)->dataTable($request)->getData(true)['data'];
}

it('orders last session using the same online date and empty states shown in the table', function (): void {
    $online = User::factory()->create([
        'username' => 'online-user',
        'last_session' => null,
    ]);
    $dated = User::factory()->create([
        'username' => 'dated-user',
        'last_session' => now()->subDays(2),
    ]);
    $never = User::factory()->create([
        'username' => 'never-user',
        'last_session' => null,
    ]);

    DB::table('sessions')->insert([
        'id' => 'active-user-table-session',
        'user_id' => $online->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Pest',
        'payload' => '',
        'last_activity' => now()->timestamp,
    ]);

    $recentFirst = userTableLastSessionResponse('desc');
    expect(array_column($recentFirst, 'username'))->toBe([
        'online-user',
        'dated-user',
        'never-user',
    ]);
    expect($recentFirst[0]['last_session'])->toContain('En línea');
    expect($recentFirst[2]['last_session'])->toContain('Sin registro');

    $oldestFirst = userTableLastSessionResponse('asc');
    expect(array_column($oldestFirst, 'username'))->toBe([
        'never-user',
        'dated-user',
        'online-user',
    ]);
});
