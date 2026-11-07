<?php

use App\Support\FilterDateRange;
use App\Http\Controllers\DeathController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

it('accepts empty and one-sided filter date ranges', function () {
    expect(FilterDateRange::validate(Request::create('/', 'GET'), 'from', 'to'))->toBe([]);

    $validated = FilterDateRange::validate(Request::create('/', 'GET', [
        'from' => now()->subMonth()->toDateString(),
    ]), 'from', 'to');

    expect($validated['from'])->toBe(now()->subMonth()->toDateString());
});

it('rejects future dates in filter ranges', function () {
    FilterDateRange::validate(Request::create('/', 'GET', [
        'from' => now()->addDay()->toDateString(),
    ]), 'from', 'to');
})->throws(ValidationException::class);

it('rejects a filter range whose final date precedes its initial date', function () {
    FilterDateRange::validate(Request::create('/', 'GET', [
        'from' => now()->subDay()->toDateString(),
        'to' => now()->subDays(2)->toDateString(),
    ]), 'from', 'to');
})->throws(ValidationException::class);

it('rejects filter dates that do not use the expected format', function () {
    FilterDateRange::validate(Request::create('/', 'GET', [
        'from' => '06/11/2026',
    ]), 'from', 'to');
})->throws(ValidationException::class);

it('requires and validates the year expression for grouped death date filters', function (array $filters) {
    app(DeathController::class)->dataTable(Request::create('/', 'POST', $filters));
})->with([
    'missing year' => [['dateRange' => 'years']],
    'malformed year list' => [['dateRange' => 'years', 'year' => '2024, texto']],
    'descending period' => [['dateRange' => 'years', 'year' => '2025-2024']],
    'future year' => [['dateRange' => 'years', 'year' => (string) (now()->year + 1)]],
])->throws(ValidationException::class);

it('requires a month or quarter for their corresponding death date filter', function (array $filters) {
    app(DeathController::class)->dataTable(Request::create('/', 'POST', $filters));
})->with([
    'missing month' => [['dateRange' => 'months', 'year' => (string) now()->year]],
    'missing quarter' => [['dateRange' => 'quarter', 'year' => (string) now()->year]],
])->throws(ValidationException::class);

it('rejects future months and quarters in the current year', function (array $filters) {
    Carbon::setTestNow(Carbon::create(2026, 6, 15, 12));

    try {
        app(DeathController::class)->dataTable(Request::create('/', 'POST', $filters));
    } finally {
        Carbon::setTestNow();
    }
})->with([
    'future month' => [[
        'dateRange' => 'months',
        'year' => '2026',
        'selectedMonths' => ['07'],
    ]],
    'future quarter' => [[
        'dateRange' => 'quarter',
        'year' => '2026',
        'quarter' => '3',
    ]],
])->throws(ValidationException::class);

it('accepts a single selected month sent as a scalar value', function () {
    $date = now()->subMonthNoOverflow();
    $response = app(DeathController::class)->dataTable(Request::create('/', 'POST', [
        'dateRange' => 'months',
        'year' => $date->format('Y'),
        'selectedMonths' => $date->format('m'),
        'length' => 1,
    ]));

    expect($response->getStatusCode())->toBe(200);
});
