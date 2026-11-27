<?php

namespace App\Http\Controllers;

use App\Models\Death;
use App\Models\DeathCause;
use App\Models\DeathLocation;
use App\Models\District;
use App\Models\ImportDifference;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ImportDifferenceController extends Controller
{
    private const EDITABLE_FIELDS = [
        'name', 'first_last_name', 'second_last_name',
        'age', 'age_years', 'age_months', 'age_days', 'sex', 'death_date',
        'residence_municipality_id', 'death_municipality_id',
        'district_id', 'death_district_id', 'death_location_id', 'death_cause_id',
        'cie_cause_code', 'cie_cause_description',
    ];

    private const FIELD_LABELS = [
        'name' => 'Nombre(s)',
        'first_last_name' => 'Apellido paterno',
        'second_last_name' => 'Apellido materno',
        'age' => 'Edad',
        'age_years' => 'Edad en años',
        'age_months' => 'Edad en meses',
        'age_days' => 'Edad en días',
        'sex' => 'Sexo',
        'death_date' => 'Fecha de defunción',
        'residence_municipality_id' => 'Municipio de residencia',
        'death_municipality_id' => 'Municipio de defunción',
        'district_id' => 'Distrito de residencia',
        'death_district_id' => 'Distrito de defunción',
        'death_location_id' => 'Lugar de defunción',
        'death_cause_id' => 'Causa de defunción',
        'cie_cause_code' => 'Código CIE',
        'cie_cause_description' => 'Descripción CIE',
    ];

    private const DECISION_GROUPS = [
        'name' => 'identity',
        'first_last_name' => 'identity',
        'second_last_name' => 'identity',
        'age' => 'age',
        'age_years' => 'age',
        'age_months' => 'age',
        'age_days' => 'age',
        'residence_municipality_id' => 'residence',
        'district_id' => 'residence',
        'death_municipality_id' => 'death_area',
        'death_district_id' => 'death_area',
        'death_cause_id' => 'cause_cie',
        'cie_cause_code' => 'cause_cie',
        'cie_cause_description' => 'cause_cie',
    ];

    private const DECISION_GROUP_LABELS = [
        'identity' => 'Identidad de la persona',
        'age' => 'Edad',
        'residence' => 'Residencia',
        'death_area' => 'Ubicación de la defunción',
        'cause_cie' => 'Causa y clasificación CIE',
    ];

    public function index(int $importId)
    {
        $import = DB::table('imports')->find($importId);
        abort_unless($import, 404);

        return view('estadisticas.import-differences', [
            'importId' => $importId,
            'importFileName' => $import->original_name ?? "Importación #{$importId}",
            'expectedDifferenceCount' => (int) ($import->rows_changed_existing ?? 0),
            'isPreview' => ($import->status ?? null) === 'preview',
        ]);
    }

    public function list(Request $request, int $importId)
    {
        $perPage = min(max((int) $request->query('per_page', 4), 1), 10);
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'pending');
        $allowedStatuses = ['pending', 'resolved', 'applied', 'kept', 'mixed', 'all'];
        $status = in_array($status, $allowedStatuses, true) ? $status : 'pending';

        $query = ImportDifference::query()
            ->with('resolver:id,name,first_last_name,second_last_name')
            ->where('import_id', $importId)
            ->when($status === 'resolved', fn ($builder) => $builder->whereIn('status', ['applied', 'kept', 'mixed']))
            ->when(! in_array($status, ['all', 'resolved'], true), fn ($builder) => $builder->where('status', $status))
            ->when($search !== '', function ($builder) use ($search) {
                $term = '%'.$search.'%';
                $builder->where(function ($nested) use ($term) {
                    $nested->where('gov_folio', 'like', $term)
                        ->orWhere('previous_values', 'like', $term)
                        ->orWhere('proposed_values', 'like', $term);
                });
            })
            ->orderByRaw("case when status = 'pending' then 0 else 1 end")
            ->orderBy('id');

        $differences = $query->paginate($perPage);
        $differences->getCollection()->transform(fn (ImportDifference $difference) => $this->present($difference));

        $counts = ImportDifference::query()
            ->where('import_id', $importId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'ok' => true,
            'data' => $differences,
            'counts' => [
                'pending' => (int) ($counts['pending'] ?? 0),
                'applied' => (int) ($counts['applied'] ?? 0),
                'kept' => (int) ($counts['kept'] ?? 0),
                'mixed' => (int) ($counts['mixed'] ?? 0),
                'resolved' => (int) ($counts['applied'] ?? 0) + (int) ($counts['kept'] ?? 0) + (int) ($counts['mixed'] ?? 0),
                'total' => (int) $counts->sum(),
            ],
        ]);
    }

    public function resolve(Request $request, int $differenceId)
    {
        $validated = $request->validate([
            'decision' => ['nullable', Rule::in(['apply', 'keep'])],
            'decisions' => ['nullable', 'array'],
            'decisions.*' => [Rule::in(['apply', 'keep'])],
        ]);

        if (empty($validated['decision']) && empty($validated['decisions'])) {
            return response()->json([
                'ok' => false,
                'message' => 'Seleccione qué valor conservar en todos los campos.',
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($differenceId, $validated, $request) {
                $difference = ImportDifference::query()->lockForUpdate()->findOrFail($differenceId);

                if ($difference->status !== 'pending') {
                    return ['status' => 409, 'message' => 'Esta diferencia ya fue atendida.'];
                }

                $death = Death::query()->lockForUpdate()->find($difference->death_id);
                if (! $death) {
                    return ['status' => 409, 'message' => 'El registro original ya no está disponible.'];
                }

                $changedFields = array_values(array_intersect(
                    $difference->changed_fields ?? [],
                    self::EDITABLE_FIELDS
                ));

                if ($changedFields === []) {
                    return [
                        'status' => 409,
                        'message' => 'Esta diferencia no contiene campos disponibles para revisar.',
                    ];
                }

                $decisions = [];
                if (! empty($validated['decision'])) {
                    $decisions = array_fill_keys($changedFields, $validated['decision']);
                } else {
                    foreach ($changedFields as $field) {
                        $decision = $validated['decisions'][$field] ?? null;
                        if (! in_array($decision, ['apply', 'keep'], true)) {
                            return [
                                'status' => 422,
                                'message' => 'Seleccione qué valor conservar en todos los campos.',
                            ];
                        }
                        $decisions[$field] = $decision;
                    }

                    $groupedDecisions = [];
                    foreach ($decisions as $field => $decision) {
                        $group = self::DECISION_GROUPS[$field] ?? $field;
                        $groupedDecisions[$group][] = $decision;
                    }

                    foreach ($groupedDecisions as $group => $groupDecisions) {
                        if (count(array_unique($groupDecisions)) > 1) {
                            return [
                                'status' => 422,
                                'message' => 'Los campos relacionados deben conservarse o reemplazarse juntos.',
                            ];
                        }
                    }
                }

                foreach ($changedFields as $field) {
                    $currentMatchesPrevious = $this->valuesMatch(
                        $field,
                        $death->{$field},
                        $difference->previous_values[$field] ?? null
                    );
                    $currentMatchesProposed = $this->valuesMatch(
                        $field,
                        $death->{$field},
                        $difference->proposed_values[$field] ?? null
                    );

                    if (! $currentMatchesPrevious && ! $currentMatchesProposed) {
                        return [
                            'status' => 409,
                            'message' => 'El registro cambió después de la importación. Recargue la revisión antes de decidir.',
                        ];
                    }
                }

                $updates = [];
                $resolutionChanges = [];
                foreach ($changedFields as $field) {
                    if ($decisions[$field] === 'apply' && ! $this->valuesMatch(
                        $field,
                        $death->{$field},
                        $difference->proposed_values[$field] ?? null
                    )) {
                        $resolutionChanges[$field] = $death->getRawOriginal($field);
                        $updates[$field] = $difference->proposed_values[$field] ?? null;
                    }
                }
                if ($updates !== []) {
                    $death->fill($updates)->save();
                }

                $uniqueDecisions = array_values(array_unique($decisions));
                $difference->status = count($uniqueDecisions) > 1
                    ? 'mixed'
                    : ($uniqueDecisions[0] === 'apply' ? 'applied' : 'kept');
                $difference->field_decisions = $decisions;
                $difference->resolution_changes = $resolutionChanges;
                $difference->resolved_by_user_id = $request->user()?->id;
                $difference->resolved_at = now();
                $difference->save();

                return [
                    'status' => 200,
                    'message' => $difference->status === 'mixed'
                        ? 'Se guardaron las decisiones seleccionadas para cada campo.'
                        : ($difference->status === 'applied'
                            ? 'Se aplicaron los valores del archivo importado.'
                            : 'Se conservaron los valores registrados actualmente.'),
                    'data' => $this->present($difference->fresh('resolver')),
                ];
            });

            return response()->json([
                'ok' => $result['status'] === 200,
                'message' => $result['message'],
                'data' => $result['data'] ?? null,
            ], $result['status']);
        } catch (\Throwable $e) {
            Log::error('Error resolving import difference', [
                'difference_id' => $differenceId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'No se pudo guardar la decisión. Inténtelo nuevamente.',
            ], 500);
        }
    }

    public function resolveBatch(Request $request, int $importId)
    {
        $validated = $request->validate([
            'default_decision' => ['required', Rule::in(['apply', 'keep'])],
            'exceptions' => ['sometimes', 'array', 'max:500'],
            'exceptions.*.id' => ['required', 'integer', 'distinct'],
            'exceptions.*.decision' => ['required', Rule::in(['apply', 'keep'])],
        ]);

        $exceptionDecisions = collect($validated['exceptions'] ?? [])->mapWithKeys(
            fn (array $exception) => [(int) $exception['id'] => $exception['decision']]
        );

        try {
            $result = DB::transaction(function () use ($importId, $validated, $exceptionDecisions, $request) {
                $differences = ImportDifference::query()
                    ->where('import_id', $importId)
                    ->where('status', 'pending')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                if ($differences->isEmpty()) {
                    throw new \UnexpectedValueException('Ya no hay diferencias pendientes para confirmar.');
                }

                $pendingIds = $differences->pluck('id')->map(fn ($id) => (int) $id);
                if ($exceptionDecisions->keys()->diff($pendingIds)->isNotEmpty()) {
                    throw new \UnexpectedValueException('Algunas diferencias cambiaron mientras se preparaban las decisiones. Recargue la revisión.');
                }

                $counts = ['apply' => 0, 'keep' => 0];
                foreach ($differences as $difference) {
                    $decision = $exceptionDecisions->get((int) $difference->id, $validated['default_decision']);
                    $this->resolvePendingRecord($difference, $decision, $request->user()?->id);
                    $counts[$decision]++;
                }

                return [
                    'total' => $differences->count(),
                    'applied' => $counts['apply'],
                    'kept' => $counts['keep'],
                ];
            });

            return response()->json([
                'ok' => true,
                'message' => "Se confirmaron {$result['total']} decisiones: {$result['applied']} con el archivo y {$result['kept']} conservando el registro actual.",
                'data' => $result,
            ]);
        } catch (\UnexpectedValueException $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 409);
        } catch (\Throwable $e) {
            Log::error('Error resolving import differences in batch', [
                'import_id' => $importId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'No se pudieron guardar las decisiones. No se aplicó ningún cambio.',
            ], 500);
        }
    }

    public function undo(Request $request, int $differenceId)
    {
        try {
            $result = DB::transaction(function () use ($differenceId, $request) {
                $difference = ImportDifference::query()->lockForUpdate()->findOrFail($differenceId);

                if (! in_array($difference->status, ['applied', 'kept', 'mixed'], true)) {
                    return ['status' => 409, 'message' => 'Esta decisión ya no se puede deshacer.'];
                }

                if (! $difference->resolved_at || $difference->resolved_at->lt(now()->subMinutes(2))) {
                    return ['status' => 409, 'message' => 'El tiempo para deshacer esta decisión terminó.'];
                }

                if ((int) $difference->resolved_by_user_id !== (int) $request->user()?->id) {
                    return ['status' => 403, 'message' => 'Solo quien tomó la decisión puede deshacerla.'];
                }

                if (
                    in_array($difference->status, ['applied', 'mixed'], true)
                    && $difference->getRawOriginal('resolution_changes') === null
                ) {
                    return ['status' => 409, 'message' => 'Esta decisión no tiene información suficiente para deshacerse.'];
                }

                $death = Death::query()->lockForUpdate()->find($difference->death_id);
                if (! $death) {
                    return ['status' => 409, 'message' => 'El registro original ya no está disponible.'];
                }

                $resolutionChanges = $difference->resolution_changes ?? [];
                foreach ($resolutionChanges as $field => $previousValue) {
                    if (! in_array($field, self::EDITABLE_FIELDS, true)) {
                        continue;
                    }

                    if (! $this->valuesMatch(
                        $field,
                        $death->{$field},
                        $difference->proposed_values[$field] ?? null
                    )) {
                        return [
                            'status' => 409,
                            'message' => 'El registro cambió después de la decisión y ya no es seguro deshacerla.',
                        ];
                    }
                }

                if ($resolutionChanges !== []) {
                    $death->fill($resolutionChanges)->save();
                }

                $difference->status = 'pending';
                $difference->field_decisions = null;
                $difference->resolution_changes = null;
                $difference->resolved_by_user_id = null;
                $difference->resolved_at = null;
                $difference->save();

                return [
                    'status' => 200,
                    'message' => 'La decisión se deshizo y el registro volvió a pendientes.',
                    'data' => $this->present($difference->fresh('resolver')),
                ];
            });

            return response()->json([
                'ok' => $result['status'] === 200,
                'message' => $result['message'],
                'data' => $result['data'] ?? null,
            ], $result['status']);
        } catch (\Throwable $e) {
            Log::error('Error undoing import difference resolution', [
                'difference_id' => $differenceId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'No se pudo deshacer la decisión. Inténtelo nuevamente.',
            ], 500);
        }
    }

    private function resolvePendingRecord(ImportDifference $difference, string $decision, ?int $userId): void
    {
        if ($difference->status !== 'pending') {
            throw new \UnexpectedValueException('Una de las diferencias ya fue atendida. Recargue la revisión.');
        }

        $death = Death::query()->lockForUpdate()->find($difference->death_id);
        if (! $death) {
            throw new \UnexpectedValueException('Uno de los registros originales ya no está disponible.');
        }

        $changedFields = array_values(array_intersect(
            $difference->changed_fields ?? [],
            self::EDITABLE_FIELDS
        ));

        if ($changedFields === []) {
            throw new \UnexpectedValueException('Una diferencia no contiene campos disponibles para revisar.');
        }

        foreach ($changedFields as $field) {
            $currentMatchesPrevious = $this->valuesMatch(
                $field,
                $death->{$field},
                $difference->previous_values[$field] ?? null
            );
            $currentMatchesProposed = $this->valuesMatch(
                $field,
                $death->{$field},
                $difference->proposed_values[$field] ?? null
            );

            if (! $currentMatchesPrevious && ! $currentMatchesProposed) {
                throw new \UnexpectedValueException('Un registro cambió después de la importación. No se aplicó ninguna decisión; recargue la revisión.');
            }
        }

        $updates = [];
        $resolutionChanges = [];
        if ($decision === 'apply') {
            foreach ($changedFields as $field) {
                if (! $this->valuesMatch(
                    $field,
                    $death->{$field},
                    $difference->proposed_values[$field] ?? null
                )) {
                    $resolutionChanges[$field] = $death->getRawOriginal($field);
                    $updates[$field] = $difference->proposed_values[$field] ?? null;
                }
            }
        }

        if ($updates !== []) {
            $death->fill($updates)->save();
        }

        $difference->status = $decision === 'apply' ? 'applied' : 'kept';
        $difference->field_decisions = array_fill_keys($changedFields, $decision);
        $difference->resolution_changes = $resolutionChanges;
        $difference->resolved_by_user_id = $userId;
        $difference->resolved_at = now();
        $difference->save();
    }

    private function present(ImportDifference $difference): array
    {
        $previous = $difference->previous_values ?? [];
        $proposed = $difference->proposed_values ?? [];
        $fields = array_values(array_intersect($difference->changed_fields ?? [], array_keys(self::FIELD_LABELS)));
        $decisions = $difference->field_decisions ?? [];
        if ($decisions === [] && in_array($difference->status, ['applied', 'kept'], true)) {
            $legacyDecision = $difference->status === 'applied' ? 'apply' : 'keep';
            $decisions = array_fill_keys($fields, $legacyDecision);
        }
        $collapseYears = in_array('age', $fields, true)
            && in_array('age_years', $fields, true)
            && $this->valuesMatch('age', $previous['age'] ?? null, $previous['age_years'] ?? null)
            && $this->valuesMatch('age', $proposed['age'] ?? null, $proposed['age_years'] ?? null);

        $presentedFields = [];
        foreach ($fields as $field) {
            if ($collapseYears && $field === 'age_years') {
                continue;
            }

            $rawFields = $collapseYears && $field === 'age' ? ['age', 'age_years'] : [$field];
            $group = self::DECISION_GROUPS[$field] ?? $field;
            $groupFields = array_values(array_filter(
                $fields,
                fn (string $candidate) => (self::DECISION_GROUPS[$candidate] ?? $candidate) === $group
            ));
            $groupDecisions = array_values(array_unique(array_filter(
                array_map(fn (string $candidate) => $decisions[$candidate] ?? null, $groupFields)
            )));

            $presentedFields[] = [
                'field' => $field,
                'raw_fields' => $rawFields,
                'label' => $field === 'age' && $collapseYears ? 'Edad' : self::FIELD_LABELS[$field],
                'previous' => $this->displayValue($field, $previous[$field] ?? null),
                'proposed' => $this->displayValue($field, $proposed[$field] ?? null),
                'decision_group' => $group,
                'decision_group_label' => self::DECISION_GROUP_LABELS[$group] ?? self::FIELD_LABELS[$field],
                'decision_group_fields' => $groupFields,
                'decision' => count($groupDecisions) === 1 ? $groupDecisions[0] : null,
            ];
        }

        $undoExpiresAt = $difference->resolved_at?->copy()->addMinutes(2);
        $canUndo = in_array($difference->status, ['applied', 'kept', 'mixed'], true)
            && (int) $difference->resolved_by_user_id === (int) auth()->id()
            && $undoExpiresAt?->isFuture();

        return [
            'id' => $difference->id,
            'import_id' => $difference->import_id,
            'gov_folio' => $difference->gov_folio,
            'status' => $difference->status,
            'person_name' => $this->personName($previous ?: $proposed),
            'changed_fields' => $presentedFields,
            'field_count' => count($presentedFields),
            'field_decisions' => $decisions,
            'resolved_at' => optional($difference->resolved_at)?->toIso8601String(),
            'resolved_by' => $difference->resolver?->full_name,
            'can_undo' => $canUndo,
            'undo_expires_at' => $canUndo ? $undoExpiresAt?->toIso8601String() : null,
        ];
    }

    private function personName(array $values): string
    {
        return trim(implode(' ', array_filter([
            Death::formatPersonName($values['name'] ?? null),
            Death::formatPersonName($values['first_last_name'] ?? null),
            Death::formatPersonName($values['second_last_name'] ?? null),
        ]))) ?: 'Persona sin nombre';
    }

    private function displayValue(string $field, mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'Sin dato';
        }

        return match ($field) {
            'sex' => $value === 'F' ? 'Femenino' : ($value === 'M' ? 'Masculino' : (string) $value),
            'death_date' => \Carbon\Carbon::parse($value)->format('d/m/Y'),
            'residence_municipality_id', 'death_municipality_id' => Municipality::find($value)?->name ?? "Registro #{$value}",
            'district_id', 'death_district_id' => District::find($value)?->display_name ?? District::find($value)?->name ?? "Registro #{$value}",
            'death_location_id' => DeathLocation::find($value)?->name ?? "Registro #{$value}",
            'death_cause_id' => DeathCause::find($value)?->name ?? "Registro #{$value}",
            default => (string) $value,
        };
    }

    private function valuesMatch(string $field, mixed $current, mixed $expected): bool
    {
        if (str_ends_with($field, '_id') || in_array($field, ['age', 'age_years', 'age_months', 'age_days'], true)) {
            return ($current === null || $current === '') && ($expected === null || $expected === '')
                ? true
                : (int) $current === (int) $expected;
        }

        if ($field === 'death_date') {
            return optional($current)->format('Y-m-d') === ($expected ? \Carbon\Carbon::parse($expected)->format('Y-m-d') : null);
        }

        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', (string) $current)))
            === mb_strtolower(trim(preg_replace('/\s+/u', ' ', (string) $expected)));
    }
}
