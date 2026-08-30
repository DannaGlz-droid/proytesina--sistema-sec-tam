<?php

use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('generates a stable unique folio when a publication is created', function (): void {
    $user = User::factory()->create();

    $first = Publication::create([
        'user_id' => $user->id,
        'publication_type' => 'alcoholimetria',
        'topic' => 'Primer reporte',
        'publication_date' => '2026-08-25',
        'activity_date' => '2026-08-25',
        'status' => 'pendiente',
    ]);

    $second = Publication::create([
        'user_id' => $user->id,
        'publication_type' => 'alcoholimetria',
        'topic' => 'Segundo reporte',
        'publication_date' => '2026-08-25',
        'activity_date' => '2026-08-25',
        'status' => 'pendiente',
    ]);

    expect($first->folio)
        ->toBe(sprintf('EXP-%s-%04d-ALC', $first->created_at->format('Y'), $first->id))
        ->not->toBe($second->folio);
});
