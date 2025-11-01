<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class StatisticsController extends Controller
{
    /**
     * Mostrar la vista de estadísticas con los datos agregados para los gráficos.
     */
    public function index(Request $request)
    {
        $data = Cache::remember('stats_charts_v1', 60, function () {
            // 1) Distribución por municipios (nombre + total)
            // Use death_municipality_id (column name used in this app) to count by municipality of death
            $municipios = DB::table('deaths')
                ->leftJoin('municipalities', 'municipalities.id', '=', 'deaths.death_municipality_id')
                ->select('municipalities.name as name', DB::raw('COUNT(deaths.id) as total'))
                ->groupBy('municipalities.name')
                ->orderByDesc('total')
                ->get();

            // 2) Tendencia mensual (por mes del campo created_at)
            // Use death_date if available (the model uses death_date), otherwise fallback to created_at
            $dateColumn = Schema::hasColumn('deaths', 'death_date') ? 'death_date' : 'created_at';

            $meses = DB::table('deaths')
                ->select(DB::raw("MONTH(deaths.{$dateColumn}) as month_number"),
                         DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%b') as month_name"),
                         DB::raw('COUNT(*) as total'))
                ->groupBy(DB::raw("MONTH(deaths.{$dateColumn})"), DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%b')"))
                ->orderBy(DB::raw("MONTH(deaths.{$dateColumn})"))
                ->get();

            // 3) Género
            // Sex column in this project is stored as 'sex'
            $generos = DB::table('deaths')
                ->select('sex', DB::raw('COUNT(*) as total'))
                ->groupBy('sex')
                ->get();

            // 4) Causas (usa tabla death_causes)
            $causas = DB::table('deaths')
                ->leftJoin('death_causes', 'death_causes.id', '=', 'deaths.death_cause_id')
                ->select('death_causes.name as name', DB::raw('COUNT(deaths.id) as total'))
                ->groupBy('death_causes.name')
                ->orderByDesc('total')
                ->get();

            // 5) Edades -> usar enfoque epidemiológico (bins detallados). Preferir birth_date, sino age
            $edades = collect();
            // bins: 0-4,5-14,15-24,25-34,35-44,45-54,55-64,65-74,75+
            $ageOrder = ['0-4','5-14','15-24','25-34','35-44','45-54','55-64','65-74','75+','Desconocido'];
            if (Schema::hasColumn('deaths', 'birth_date')) {
                $edades = DB::table('deaths')
                    ->select(DB::raw("CASE
                        WHEN birth_date IS NULL THEN 'Desconocido'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 0 AND 4 THEN '0-4'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 5 AND 14 THEN '5-14'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 15 AND 24 THEN '15-24'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 25 AND 34 THEN '25-34'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 35 AND 44 THEN '35-44'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 45 AND 54 THEN '45-54'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 55 AND 64 THEN '55-64'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 65 AND 74 THEN '65-74'
                        ELSE '75+' END as range"), DB::raw('COUNT(*) as total'))
                    ->groupBy('range')
                    ->get();

                // Reorder and ensure all bins present
                $ordered = collect();
                foreach ($ageOrder as $lbl) {
                    $found = $edades->firstWhere('range', $lbl);
                    $ordered->push((object)['range' => $lbl, 'total' => $found ? (int)$found->total : 0]);
                }
                $edades = $ordered;
            } elseif (Schema::hasColumn('deaths', 'age')) {
                // Agrupar en PHP si sólo hay columna age
                $byAge = DB::table('deaths')
                    ->select('age')
                    ->whereNotNull('age')
                    ->get()
                    ->pluck('age')
                    ->toArray();

                $ranges = ['0-4'=>0,'5-14'=>0,'15-24'=>0,'25-34'=>0,'35-44'=>0,'45-54'=>0,'55-64'=>0,'65-74'=>0,'75+'=>0,'Desconocido'=>0];

                foreach ($byAge as $a) {
                    if ($a <= 4) $ranges['0-4']++;
                    elseif ($a <= 14) $ranges['5-14']++;
                    elseif ($a <= 24) $ranges['15-24']++;
                    elseif ($a <= 34) $ranges['25-34']++;
                    elseif ($a <= 44) $ranges['35-44']++;
                    elseif ($a <= 54) $ranges['45-54']++;
                    elseif ($a <= 64) $ranges['55-64']++;
                    elseif ($a <= 74) $ranges['65-74']++;
                    else $ranges['75+']++;
                }

                $edades = collect();
                foreach ($ageOrder as $r) {
                    $edades->push((object)['range' => $r, 'total' => $ranges[$r] ?? 0]);
                }
            }

            return [
                'municipios' => $municipios,
                'meses' => $meses,
                'generos' => $generos,
                'causas' => $causas,
                'edades' => $edades,
            ];
        });

        // Preparar arrays simples para JS (labels y datos)
        $municipiosLabels = collect($data['municipios'])->pluck('name')->map(function ($v) { return $v ?? 'Sin dato'; })->toArray();
        $municipiosCounts = collect($data['municipios'])->pluck('total')->map(function ($v) { return (int)$v; })->toArray();

        $mesLabels = collect($data['meses'])->pluck('month_name')->toArray();
        $mesCounts = collect($data['meses'])->pluck('total')->map(function ($v) { return (int)$v; })->toArray();

    $generoLabels = collect($data['generos'])->pluck('sex')->map(function ($v) { return $v ?? 'Sin dato'; })->toArray();
        $generoCounts = collect($data['generos'])->pluck('total')->map(function ($v) { return (int)$v; })->toArray();

        $causaLabels = collect($data['causas'])->pluck('name')->map(function ($v) { return $v ?? 'Sin dato'; })->toArray();
        $causaCounts = collect($data['causas'])->pluck('total')->map(function ($v) { return (int)$v; })->toArray();

        $edadLabels = collect($data['edades'])->pluck('range')->toArray();
        $edadCounts = collect($data['edades'])->pluck('total')->map(function ($v) { return (int)$v; })->toArray();

        // Also pass lists for select controls
        $lists = $this->commonLists();

        return view('estadisticas.graficas', array_merge($lists, compact(
            'municipiosLabels','municipiosCounts',
            'mesLabels','mesCounts',
            'generoLabels','generoCounts',
            'causaLabels','causaCounts',
            'edadLabels','edadCounts'
        )));
    }

    /**
     * Helper para obtener listas de municipios y causas y pasarlas a la vista (para selects)
     */
    protected function commonLists()
    {
    $municipalities = DB::table('municipalities')->select('id','name','jurisdiction_id')->orderBy('name')->get();
        $causes = DB::table('death_causes')->select('id','name')->orderBy('name')->get();
        // Jurisdictions list (if the table exists)
        $jurisdictions = [];
        if (Schema::hasTable('jurisdictions')) {
            $jurisdictions = DB::table('jurisdictions')->select('id','name')->orderBy('name')->get();
        }

        // Sex values: prefer distinct values stored in deaths table, but map common codes to readable labels
        $sexes = [];
        if (Schema::hasTable('deaths')) {
            $raw = DB::table('deaths')->select('sex')->distinct()->pluck('sex')->filter()->values();
            // Normalize into value/label pairs
            $sexes = $raw->map(function ($s) {
                $label = $s;
                // common codes mapping
                if (is_string($s)) {
                    $low = mb_strtolower($s);
                    if ($low === 'm' || $low === 'masculino' || $low === 'hombre') $label = 'Hombre';
                    elseif ($low === 'f' || $low === 'femenino' || $low === 'mujer') $label = 'Mujer';
                    elseif ($low === 'otro' || $low === 'o') $label = 'Otro';
                }
                return (object)['value' => $s, 'label' => $label];
            })->values();
        }

        // If the `jurisdictions` table contains the 12 jurisdicciones de Tamaulipas
        // (como indicas), preferir mostrar sólo municipios pertenecientes a esas
        // jurisdicciones. Esto evita mostrar municipios de otros estados.
        try {
            if (!empty($jurisdictions) && is_iterable($jurisdictions) && count($jurisdictions) === 12) {
                $jurIds = collect($jurisdictions)->pluck('id')->values()->all();
                $municipalities = DB::table('municipalities')
                    ->select('id','name')
                    ->whereIn('jurisdiction_id', $jurIds)
                    ->orderBy('name')
                    ->get();
            }
        } catch (\Throwable $e) {
            // if anything goes wrong, keep the original municipalities list
        }

        return compact('municipalities','causes','jurisdictions','sexes');
    }

    /**
     * Endpoint JSON que devuelve los mismos datasets pero aplicando filtros opcionales.
     * Query params soportados: start_date, end_date, municipality_id, cause_id, sex
     */
    public function chartsData(Request $request)
    {
        try {
            // Accept flexible filter keys: start_date,end_date, municipio (name), municipality_id (id), causa (name), cause_id (id), sex, limit
            $filters = $request->all();

            // resolver columna de fecha
            $dateColumn = Schema::hasColumn('deaths', 'death_date') ? 'death_date' : 'created_at';

            // columna usada para agrupar por municipio principal (por defecto defunción)
            $munCol = 'death_municipality_id';
            if (!empty($filters['municipio_kind']) && $filters['municipio_kind'] === 'residence') {
                $munCol = 'residence_municipality_id';
            }

            // helper para aplicar filtros a un query builder
            $applyFilters = function ($query) use ($filters, $dateColumn, $munCol) {
                // Fecha
                if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                    $query->whereBetween($dateColumn, [$filters['start_date'], $filters['end_date']]);
                }

                // Municipio: accept numeric id (municipality_id) or name (municipio)
                if (!empty($filters['municipality_id']) && is_numeric($filters['municipality_id'])) {
                    $query->where($munCol, (int)$filters['municipality_id']);
                } elseif (!empty($filters['municipio'])) {
                    // filter by municipality name: find matching ids
                    $names = (array) $filters['municipio'];
                    $query->whereIn($munCol, DB::table('municipalities')->select('id')->whereIn('name', $names));
                } elseif (!empty($filters['municipioDefuncion'])) {
                    $names = (array) $filters['municipioDefuncion'];
                    $query->whereIn($munCol, DB::table('municipalities')->select('id')->whereIn('name', $names));
                }

                // Causa: numeric id or name
                if (!empty($filters['cause_id']) && is_numeric($filters['cause_id'])) {
                    $query->where('death_cause_id', (int)$filters['cause_id']);
                } elseif (!empty($filters['causa'])) {
                    $names = (array) $filters['causa'];
                    $query->whereIn('death_cause_id', DB::table('death_causes')->select('id')->whereIn('name', $names));
                }

                // Sexo: accept 'M'/'F' or Spanish words
                if (!empty($filters['sex'])) {
                    $s = $filters['sex'];
                    if (!is_string($s)) $s = (string) $s;
                    $sLower = mb_strtolower($s);
                    if ($sLower === 'hombre' || $sLower === 'm') $s = 'M';
                    elseif ($sLower === 'mujer' || $sLower === 'f') $s = 'F';
                    $query->where('sex', $s);
                } elseif (!empty($filters['sexo'])) {
                    $s = $filters['sexo'];
                    $sLower = mb_strtolower($s);
                    if ($sLower === 'hombre' || $sLower === 'm') $s = 'M';
                    elseif ($sLower === 'mujer' || $sLower === 'f') $s = 'F';
                    $query->where('sex', $s);
                }

                // Edad: soporta formatos "25" o "20-30" o "5,10,15" (aplica sobre 'age' si existe, o calcula desde birth_date)
                if (!empty($filters['edad'])) {
                    $edadRaw = trim((string) $filters['edad']);
                    // solo números, rango o lista separados por coma
                    if (Schema::hasColumn('deaths', 'age')) {
                        if (preg_match('/^\d+$/', $edadRaw)) {
                            $query->where('age', (int)$edadRaw);
                        } elseif (preg_match('/^(\d+)\s*-\s*(\d+)$/', $edadRaw, $m)) {
                            $low = (int)$m[1]; $high = (int)$m[2];
                            if ($low > $high) { $tmp = $low; $low = $high; $high = $tmp; }
                            $query->whereBetween('age', [$low, $high]);
                        } else {
                            $parts = array_filter(array_map('trim', explode(',', $edadRaw)), fn($v) => $v !== '');
                            $nums = array_map('intval', $parts);
                            if (!empty($nums)) $query->whereIn('age', $nums);
                        }
                    } elseif (Schema::hasColumn('deaths', 'birth_date')) {
                        // calcular edad en años en SQL
                        if (preg_match('/^\d+$/', $edadRaw)) {
                            $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) = ?', [(int)$edadRaw]);
                        } elseif (preg_match('/^(\d+)\s*-\s*(\d+)$/', $edadRaw, $m)) {
                            $low = (int)$m[1]; $high = (int)$m[2];
                            if ($low > $high) { $tmp = $low; $low = $high; $high = $tmp; }
                            $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN ? AND ?', [$low, $high]);
                        } else {
                            $parts = array_filter(array_map('trim', explode(',', $edadRaw)), fn($v) => $v !== '');
                            $nums = array_map('intval', $parts);
                            if (!empty($nums)) {
                                $placeholders = implode(',', array_fill(0, count($nums), '?'));
                                $query->whereRaw("TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) IN ($placeholders)", $nums);
                            }
                        }
                    }
                }
            };

            // Municipios: build a list that includes ALL municipalities (so ones with 0 deaths
            // are shown when user selects 'Todos'). We'll compute counts from deaths applying
            // the same filters, then map counts onto the municipalities list.
            $munCountsQ = DB::table('deaths')
                ->select(DB::raw("{$munCol} as muni_id"), DB::raw('COUNT(*) as total'))
                ->groupBy(DB::raw("{$munCol}"));
            $applyFilters($munCountsQ);
            // Note: do not apply a limit to the counts query; limits are handled client-side by grouping.
            $munCountsRaw = $munCountsQ->get()->pluck('total', 'muni_id')->all();

            // Build municipalities list - prefer restricting to the Tamaulipas jurisdicciones if available
            $municipalitiesQ = DB::table('municipalities')->select('id','name','jurisdiction_id')->orderBy('name');
            // If jurisdictions table looks like the 12 Tamaulipas jurisdictions, restrict to them
            if (Schema::hasTable('jurisdictions') && DB::table('jurisdictions')->count() === 12) {
                $jurIds = DB::table('jurisdictions')->pluck('id')->all();
                $municipalitiesQ->whereIn('jurisdiction_id', $jurIds);
            }
            $municipalitiesFull = $municipalitiesQ->get();

            // Map counts onto the full municipalities list and sort by total desc for display
            $municipios = $municipalitiesFull->map(function($m) use ($munCountsRaw) {
                $total = isset($munCountsRaw[$m->id]) ? (int)$munCountsRaw[$m->id] : 0;
                return (object)['name' => $m->name ?? 'Sin dato', 'total' => $total, 'id' => $m->id];
            })->sortByDesc('total')->values();

            // Jurisdicciones (por el campo jurisdiction_id en deaths, si existe la tabla)
            $jurisdictions = collect();
            if (Schema::hasTable('jurisdictions')) {
                $jurQ = DB::table('deaths')
                    ->leftJoin('jurisdictions', 'jurisdictions.id', '=', 'deaths.jurisdiction_id')
                    ->select('jurisdictions.name as name', DB::raw('COUNT(deaths.id) as total'))
                    ->groupBy('jurisdictions.name')
                    ->orderByDesc('total');
                $applyFilters($jurQ);
                if (!empty($filters['limit']) && is_numeric($filters['limit'])) {
                    $jurQ->limit((int)$filters['limit']);
                }
                $jurisdictions = $jurQ->get();
            }

            // Tendencia temporal: agrupar por día/mes/año según parámetro 'group_by'
            $groupBy = !empty($filters['group_by']) ? $filters['group_by'] : 'month';

            if ($groupBy === 'day') {
                $mesesQ = DB::table('deaths')
                    ->select(DB::raw("DATE(deaths.{$dateColumn}) as period"),
                             DB::raw("MIN(DATE_FORMAT(deaths.{$dateColumn}, '%d %b %Y')) as period_label"),
                             DB::raw('COUNT(*) as total'))
                    ->groupBy(DB::raw("DATE(deaths.{$dateColumn})"))
                    ->orderBy(DB::raw("DATE(deaths.{$dateColumn})"));
            } elseif ($groupBy === 'year') {
                $mesesQ = DB::table('deaths')
                    ->select(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y') as period"),
                             DB::raw("MIN(DATE_FORMAT(deaths.{$dateColumn}, '%Y')) as period_label"),
                             DB::raw('COUNT(*) as total'))
                    ->groupBy(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y')"))
                    ->orderBy(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y')"));
            } else {
                // default -> month
                $mesesQ = DB::table('deaths')
                    ->select(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y-%m') as period"),
                             DB::raw("MIN(DATE_FORMAT(deaths.{$dateColumn}, '%b %Y')) as period_label"),
                             DB::raw('COUNT(*) as total'))
                    ->groupBy(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y-%m')"))
                    ->orderBy(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y-%m')"));
            }
            $applyFilters($mesesQ);
            $meses = $mesesQ->get();

            // Genero (sex)
            $generosQ = DB::table('deaths')
                ->select('sex', DB::raw('COUNT(*) as total'))
                ->groupBy('sex');
            $applyFilters($generosQ);
            $generos = $generosQ->get();

            // Causas
            $causasQ = DB::table('deaths')
                ->leftJoin('death_causes', 'death_causes.id', '=', 'deaths.death_cause_id')
                ->select('death_causes.name as name', DB::raw('COUNT(deaths.id) as total'))
                ->groupBy('death_causes.name')
                ->orderByDesc('total');
            $applyFilters($causasQ);
            if (!empty($filters['limit']) && is_numeric($filters['limit'])) {
                $causasQ->limit((int)$filters['limit']);
            }
            $causas = $causasQ->get();

            // Edades (siempre calculadas sobre birth_date si existe, o sobre age si existe)
            $edades = collect();
            $ageOrder = ['0-4','5-14','15-24','25-34','35-44','45-54','55-64','65-74','75+','Desconocido'];
            if (Schema::hasColumn('deaths', 'birth_date')) {
                $edadesQ = DB::table('deaths')
                    ->select(DB::raw("CASE
                        WHEN birth_date IS NULL THEN 'Desconocido'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 0 AND 4 THEN '0-4'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 5 AND 14 THEN '5-14'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 15 AND 24 THEN '15-24'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 25 AND 34 THEN '25-34'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 35 AND 44 THEN '35-44'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 45 AND 54 THEN '45-54'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 55 AND 64 THEN '55-64'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 65 AND 74 THEN '65-74'
                        ELSE '75+' END as range"), DB::raw('COUNT(*) as total'))
                    ->groupBy('range');
                $applyFilters($edadesQ);
                $edadesRaw = $edadesQ->get();

                // Reorder and fill missing bins
                $ordered = collect();
                foreach ($ageOrder as $lbl) {
                    $found = $edadesRaw->firstWhere('range', $lbl);
                    $ordered->push((object)['range' => $lbl, 'total' => $found ? (int)$found->total : 0]);
                }
                $edades = $ordered;
            } elseif (Schema::hasColumn('deaths', 'age')) {
                $byAgeQ = DB::table('deaths')->select('age')->whereNotNull('age');
                $applyFilters($byAgeQ);
                $byAge = $byAgeQ->get()->pluck('age')->toArray();

                $ranges = ['0-4'=>0,'5-14'=>0,'15-24'=>0,'25-34'=>0,'35-44'=>0,'45-54'=>0,'55-64'=>0,'65-74'=>0,'75+'=>0,'Desconocido'=>0];
                foreach ($byAge as $a) {
                    if ($a <= 4) $ranges['0-4']++;
                    elseif ($a <= 14) $ranges['5-14']++;
                    elseif ($a <= 24) $ranges['15-24']++;
                    elseif ($a <= 34) $ranges['25-34']++;
                    elseif ($a <= 44) $ranges['35-44']++;
                    elseif ($a <= 54) $ranges['45-54']++;
                    elseif ($a <= 64) $ranges['55-64']++;
                    elseif ($a <= 74) $ranges['65-74']++;
                    else $ranges['75+']++;
                }
                foreach ($ageOrder as $r) $edades->push((object)['range'=>$r,'total'=>$ranges[$r] ?? 0]);
            }

            // Transformar a arrays simples
            // Additionally build a compare set: residence vs death per municipality
            // Residence counts per municipality (map muni_id -> total)
            $resCountsQ = DB::table('deaths')
                ->select(DB::raw('residence_municipality_id as muni_id'), DB::raw('COUNT(*) as total'))
                ->groupBy(DB::raw('residence_municipality_id'));
            $applyFilters($resCountsQ);
            $resCounts = $resCountsQ->get()->pluck('total','muni_id')->all();

            // Death counts per municipality (death_municipality_id)
            $deathCountsQ = DB::table('deaths')
                ->select(DB::raw('death_municipality_id as muni_id'), DB::raw('COUNT(*) as total'))
                ->groupBy(DB::raw('death_municipality_id'));
            $applyFilters($deathCountsQ);
            $deathCounts = $deathCountsQ->get()->pluck('total','muni_id')->all();

            $response = [
                'municipios' => [ 'labels' => $municipios->pluck('name')->map(fn($v) => $v ?? 'Sin dato')->values()->all(), 'counts' => $municipios->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                'meses' => [ 'labels' => $meses->pluck('period_label')->values()->all(), 'counts' => $meses->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                'generos' => [ 'labels' => $generos->pluck('sex')->map(fn($v) => $v ?? 'Sin dato')->values()->all(), 'counts' => $generos->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                'causas' => [ 'labels' => $causas->pluck('name')->map(fn($v) => $v ?? 'Sin dato')->values()->all(), 'counts' => $causas->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                'edades' => [ 'labels' => $edades->pluck('range')->values()->all(), 'counts' => $edades->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                // Jurisdictions
                'jurisdictions' => [ 'labels' => $jurisdictions->pluck('name')->map(fn($v) => $v ?? 'Sin dato')->values()->all(), 'counts' => $jurisdictions->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                // Residence vs Death per municipality (aligned to full municipalities list)
                'municipios_compare' => (function() use ($municipios, $resCounts, $deathCounts) {
                    $labels = $municipios->pluck('name')->values()->all();
                    $res = [];
                    $death = [];
                    foreach ($municipios as $m) {
                        $id = $m->id;
                        $res[] = isset($resCounts[$id]) ? (int)$resCounts[$id] : 0;
                        $death[] = isset($deathCounts[$id]) ? (int)$deathCounts[$id] : 0;
                    }
                    return [ 'labels' => $labels, 'residence_counts' => $res, 'death_counts' => $death ];
                })(),
            ];

            return response()->json($response);
        } catch (\Throwable $e) {
            // Log the error and return a clear JSON payload so the frontend debug box shows the cause
            \Log::error('chartsData error: ' . $e->getMessage(), ['exception' => $e]);
            $debug = config('app.debug') ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() : 'Database or configuration error';
            return response()->json([
                'error' => 'Could not fetch charts data',
                'message' => $e->getMessage(),
                'debug' => $debug,
            ], 500);
        }
    }
}
