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
                // Always include jurisdiction_id to allow frontend dependent filtering
                $municipalities = DB::table('municipalities')
                    ->select('id','name','jurisdiction_id')
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
                // Fecha: soportar start_date/end_date, o arrays de years/months
                if (!empty($filters['months']) && is_array($filters['months']) && !empty($filters['years']) && is_array($filters['years'])) {
                    $periods = [];
                    foreach ($filters['years'] as $y) {
                        foreach ($filters['months'] as $m) {
                            $yy = (int)$y;
                            $mm = sprintf('%02d', (int)$m);
                            $periods[] = "{$yy}-{$mm}";
                        }
                    }
                    if (!empty($periods)) {
                        $query->whereIn(DB::raw("DATE_FORMAT({$dateColumn}, '%Y-%m')"), $periods);
                    }
                } elseif (!empty($filters['years']) && is_array($filters['years'])) {
                    $years = array_map('strval', $filters['years']);
                    $query->whereIn(DB::raw("DATE_FORMAT({$dateColumn}, '%Y')"), $years);
                } elseif (!empty($filters['start_date']) && !empty($filters['end_date'])) {
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

    /**
     * Obtiene el rango de fechas default (últimos 12 meses desde el último registro)
     */
    private function calculateDefaultDateRange()
    {
        $dateColumn = Schema::hasColumn('deaths', 'death_date') ? 'death_date' : 'created_at';
        
        // Obtener la fecha máxima de registro
        $maxDate = DB::table('deaths')->max($dateColumn);
        
        if (!$maxDate) {
            // Si no hay registros, usar hoy y 1 año atrás
            $endDate = now();
            $startDate = now()->subYear();
        } else {
            $endDate = \Carbon\Carbon::parse($maxDate);
            $startDate = $endDate->copy()->subYear();
        }
        
        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }

    /**
     * Endpoint para obtener rangos de fecha default
     * GET /api/default-date-range
     */
    public function getDefaultDateRangeApi()
    {
        return response()->json($this->calculateDefaultDateRange());
    }

    /**
     * Obtiene datos de un tipo de gráfica específica con filtros aplicados.
     * GET /api/chart/{chartType}?filters...
     * 
     * Tipos soportados: municipios, tendencias, edades, genero, causas, jurisdicciones, comparativa
     */
    public function getChartData(Request $request, $chartType = 'municipios')
    {
        try {
            $filters = $request->all();
            
            // Si no hay filtros de fecha especificados, aplicar rango default (últimos 12 meses)
            if (empty($filters['start_date']) && empty($filters['end_date']) && 
                empty($filters['months']) && empty($filters['years'])) {
                $defaultRange = $this->calculateDefaultDateRange();
                $filters['start_date'] = $defaultRange['start_date'];
                $filters['end_date'] = $defaultRange['end_date'];
            }
            
            // Resolver columna de fecha
            $dateColumn = Schema::hasColumn('deaths', 'death_date') ? 'death_date' : 'created_at';
            
            // Determinar columna de municipio según parámetro
            $munCol = !empty($filters['municipio_kind']) && $filters['municipio_kind'] === 'residence' 
                ? 'residence_municipality_id' 
                : 'death_municipality_id';
            
            // Helper para aplicar filtros comunes
            $applyFilters = function ($query) use ($filters, $dateColumn, $munCol) {
                // Rango de fechas: soportar arrays `months` y `years` o start/end
                if (!empty($filters['months']) && is_array($filters['months']) && !empty($filters['years']) && is_array($filters['years'])) {
                    $periods = [];
                    foreach ($filters['years'] as $y) {
                        foreach ($filters['months'] as $m) {
                            $yy = (int)$y;
                            $mm = sprintf('%02d', (int)$m);
                            $periods[] = "{$yy}-{$mm}";
                        }
                    }
                    if (!empty($periods)) {
                        $query->whereIn(DB::raw("DATE_FORMAT({$dateColumn}, '%Y-%m')"), $periods);
                    }
                } elseif (!empty($filters['years']) && is_array($filters['years'])) {
                    $years = array_map('strval', $filters['years']);
                    $query->whereIn(DB::raw("DATE_FORMAT({$dateColumn}, '%Y')"), $years);
                } elseif (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                    $query->whereBetween($dateColumn, [$filters['start_date'], $filters['end_date']]);
                }
                
                // Municipio
                if (!empty($filters['municipality_id']) && is_numeric($filters['municipality_id'])) {
                    $query->where($munCol, (int)$filters['municipality_id']);
                } elseif (!empty($filters['municipios']) && is_array($filters['municipios'])) {
                    $query->whereIn($munCol, $filters['municipios']);
                }
                
                // Causa
                if (!empty($filters['cause_id']) && is_numeric($filters['cause_id'])) {
                    $query->where('death_cause_id', (int)$filters['cause_id']);
                } elseif (!empty($filters['causas']) && is_array($filters['causas'])) {
                    $query->whereIn('death_cause_id', $filters['causas']);
                }
                
                // Sexo
                if (!empty($filters['sex'])) {
                    $query->where('sex', $filters['sex']);
                }
                
                // Jurisdicción: aceptar un id numérico o un array de ids (`jurisdicciones[]`)
                if (!empty($filters['jurisdiction_id']) && is_numeric($filters['jurisdiction_id'])) {
                    $query->where('jurisdiction_id', (int)$filters['jurisdiction_id']);
                } elseif (!empty($filters['jurisdicciones']) && is_array($filters['jurisdicciones'])) {
                    // Normalizar valores numéricos
                    $ids = array_values(array_filter($filters['jurisdicciones'], fn($v) => is_numeric($v)));
                    if (!empty($ids)) {
                        $query->whereIn('jurisdiction_id', $ids);
                    }
                }
            };
            
            $limit = !empty($filters['limit']) && is_numeric($filters['limit']) ? (int)$filters['limit'] : null;
            
            // Según el tipo de gráfica, ejecutar consultas específicas
            switch ($chartType) {
                case 'municipios':
                    return $this->getChartMunicipios($filters, $dateColumn, $munCol, $applyFilters, $limit);
                    
                case 'tendencias':
                    return $this->getChartTendencias($filters, $dateColumn, $applyFilters);
                    
                case 'edades':
                    return $this->getChartEdades($filters, $dateColumn, $applyFilters, $limit);
                    
                case 'genero':
                    return $this->getChartGenero($filters, $dateColumn, $applyFilters);
                    
                case 'causas':
                    return $this->getChartCausas($filters, $dateColumn, $applyFilters, $limit);
                    
                case 'jurisdicciones':
                    return $this->getChartJurisdicciones($filters, $dateColumn, $applyFilters, $limit);
                    
                case 'comparativa':
                    return $this->getChartComparativa($filters, $dateColumn, $munCol, $applyFilters, $limit);
                    
                default:
                    return response()->json(['error' => 'Tipo de gráfica no válido'], 400);
            }
        } catch (\Throwable $e) {
            \Log::error('getChartData error: ' . $e->getMessage(), ['exception' => $e]);
            $debug = config('app.debug') ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() : 'Error';
            return response()->json([
                'error' => 'Error al obtener datos de la gráfica',
                'message' => $e->getMessage(),
                'debug' => $debug,
            ], 500);
        }
    }

    private function getChartMunicipios($filters, $dateColumn, $munCol, $applyFilters, $limit)
    {
        // Determinar el tipo de municipio (defunción o residencia)
        $municipioType = $filters['municipio_type'] ?? 'defuncion';
        $finalMunCol = $municipioType === 'residencia' 
            ? 'residence_municipality_id' 
            : 'death_municipality_id';

        $munCountsQ = DB::table('deaths')
            ->select(DB::raw("{$finalMunCol} as muni_id"), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw("{$finalMunCol}"));

        // If jurisdictions filter is present, compute municipality ids belonging to those
        // jurisdictions and restrict the counts query by those municipality ids. This
        // ensures the distribution by municipalities shows only municipios that belong
        // to the selected jurisdicción(es), regardless of deaths.jurisdiction_id values.
        $jurisdictionMunIds = null;
        if (!empty($filters['jurisdicciones']) && is_array($filters['jurisdicciones'])) {
            $jurIdsFilter = array_values(array_filter($filters['jurisdicciones'], fn($v) => is_numeric($v)));
            if (!empty($jurIdsFilter)) {
                $jurisdictionMunIds = DB::table('municipalities')->whereIn('jurisdiction_id', $jurIdsFilter)->pluck('id')->all();
                if (!empty($jurisdictionMunIds)) {
                    $munCountsQ->whereIn($finalMunCol, $jurisdictionMunIds);
                }
            }
        }

        $applyFilters($munCountsQ);
        $munCountsRaw = $munCountsQ->get()->pluck('total', 'muni_id')->all();

        $municipalitiesQ = DB::table('municipalities')->select('id','name','jurisdiction_id')->orderBy('name');
        if (Schema::hasTable('jurisdictions') && DB::table('jurisdictions')->count() === 12) {
            $jurIds = DB::table('jurisdictions')->pluck('id')->all();
            $municipalitiesQ->whereIn('jurisdiction_id', $jurIds);
        }
        $municipalitiesFull = $municipalitiesQ->get();
        // If frontend requested specific jurisdictions, restrict the municipalities list
        if (!empty($filters['jurisdicciones']) && is_array($filters['jurisdicciones'])) {
            $jurIdsFilter = array_values(array_filter($filters['jurisdicciones'], fn($v) => is_numeric($v)));
            if (!empty($jurIdsFilter)) {
                $municipalitiesFull = $municipalitiesFull->filter(function($m) use ($jurIdsFilter) {
                    return in_array($m->jurisdiction_id, $jurIdsFilter);
                })->values();
            }
        }

        $municipios = $municipalitiesFull->map(function($m) use ($munCountsRaw) {
            $total = isset($munCountsRaw[$m->id]) ? (int)$munCountsRaw[$m->id] : 0;
            return ['name' => $m->name ?? 'Sin dato', 'total' => $total];
        })->sortByDesc('total');
        
        if ($limit) {
            $municipios = $municipios->take($limit);
        }

        return response()->json([
            'type' => 'municipios',
            'labels' => $municipios->pluck('name')->values()->all(),
            'counts' => $municipios->pluck('total')->values()->all(),
            'total' => array_sum($municipios->pluck('total')->all()),
        ]);
    }

    private function getChartTendencias($filters, $dateColumn, $applyFilters)
    {
        $groupBy = $filters['group_by'] ?? 'month';
        
        // Meses en español para traducción
        $monthsSpanish = [
            'Jan' => 'Ene', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Abr',
            'May' => 'May', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Ago',
            'Sep' => 'Sep', 'Oct' => 'Oct', 'Nov' => 'Nov', 'Dec' => 'Dic'
        ];
        
        if ($groupBy === 'day') {
            $query = DB::table('deaths')
                ->select(DB::raw("DATE(deaths.{$dateColumn}) as period"),
                         DB::raw("MIN(DATE_FORMAT(deaths.{$dateColumn}, '%d %b')) as period_label"),
                         DB::raw('COUNT(*) as total'))
                ->groupBy(DB::raw("DATE(deaths.{$dateColumn})"))->orderBy(DB::raw("DATE(deaths.{$dateColumn})"));
        } elseif ($groupBy === 'year') {
            $query = DB::table('deaths')
                ->select(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y') as period"),
                         DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y') as period_label"),
                         DB::raw('COUNT(*) as total'))
                ->groupBy(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y')"))
                ->orderBy(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y')"));
        } else {
            // month (default)
            $query = DB::table('deaths')
                ->select(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y-%m') as period"),
                         DB::raw("MIN(DATE_FORMAT(deaths.{$dateColumn}, '%b %Y')) as period_label"),
                         DB::raw('COUNT(*) as total'))
                ->groupBy(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y-%m')"))
                ->orderBy(DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%Y-%m')"));
        }
        
        $applyFilters($query);
        $data = $query->get();
        
        // Traducir meses al español si es agrupación por mes
        if ($groupBy === 'month') {
            $data = $data->map(function($item) use ($monthsSpanish) {
                // Cambiar formato "Jan 2026" a "Ene 2026"
                foreach ($monthsSpanish as $en => $es) {
                    $item->period_label = str_replace($en, $es, $item->period_label);
                }
                return $item;
            });
        } elseif ($groupBy === 'day') {
            // Traducir meses también para días
            $data = $data->map(function($item) use ($monthsSpanish) {
                foreach ($monthsSpanish as $en => $es) {
                    $item->period_label = str_replace($en, $es, $item->period_label);
                }
                return $item;
            });
        }

        return response()->json([
            'type' => 'tendencias',
            'group_by' => $groupBy,
            'labels' => $data->pluck('period_label')->values()->all(),
            'counts' => $data->pluck('total')->map(fn($v) => (int)$v)->values()->all(),
            'total' => array_sum($data->pluck('total')->all()),
        ]);
    }

    private function getChartEdades($filters, $dateColumn, $applyFilters, $limit)
    {
        $ageOrder = ['<5 años','5-19 años','20-64 años','65+ años'];
        $edades = collect();
        $causasPorEdad = [];

        // Build subquery first for age with causes
        $subquery = DB::table('deaths')
            ->leftJoin('death_causes', 'death_causes.id', '=', 'deaths.death_cause_id')
            ->select(
                DB::raw("CASE
                    WHEN age < 5 THEN '<5 años'
                    WHEN age >= 5 AND age < 20 THEN '5-19 años'
                    WHEN age >= 20 AND age < 65 THEN '20-64 años'
                    ELSE '65+ años' END as age_range"),
                'death_causes.name as cause_name'
            );
        
        $applyFilters($subquery);
        
        // Get raw data with causes for detailed analysis
        $edadesRaw = $subquery->get();
        
        // Count by age range
        $edadesCounts = $edadesRaw->groupBy('age_range')
            ->map(function($items) { return $items->count(); });

        // Get top causes per age group (top 3)
        foreach ($ageOrder as $ageLabel) {
            $causesInAge = $edadesRaw->where('age_range', $ageLabel)
                ->groupBy('cause_name')
                ->map(function($items) { return $items->count(); })
                ->sortDesc()
                ->take(3);
            
            $causasPorEdad[$ageLabel] = $causesInAge->toArray();
        }

        // Build ordered result
        $ordered = collect();
        foreach ($ageOrder as $lbl) {
            $count = $edadesCounts->get($lbl, 0);
            $ordered->push([
                'range' => $lbl, 
                'total' => (int)$count,
                'top_causes' => $causasPorEdad[$lbl] ?? []
            ]);
        }
        $edades = $ordered;

        return response()->json([
            'type' => 'edades',
            'labels' => $edades->pluck('range')->values()->all(),
            'counts' => $edades->pluck('total')->values()->all(),
            'total' => array_sum($edades->pluck('total')->all()),
            'data_with_causes' => $edades->toArray(),
        ]);
    }

    private function getChartGenero($filters, $dateColumn, $applyFilters)
    {
        $query = DB::table('deaths')
            ->select('sex', DB::raw('COUNT(*) as total'))
            ->groupBy('sex');
        $applyFilters($query);
        $generos = $query->get();

        // Normalizar etiquetas
        $generos = $generos->map(function($g) {
            $label = $g->sex ?? 'Sin dato';
            if (in_array(strtolower($label), ['m', 'masculino'])) $label = 'Hombre';
            elseif (in_array(strtolower($label), ['f', 'femenino'])) $label = 'Mujer';
            return ['label' => $label, 'total' => (int)$g->total];
        });

        return response()->json([
            'type' => 'genero',
            'labels' => $generos->pluck('label')->values()->all(),
            'counts' => $generos->pluck('total')->values()->all(),
            'total' => array_sum($generos->pluck('total')->all()),
        ]);
    }

    private function getChartCausas($filters, $dateColumn, $applyFilters, $limit)
    {
        $query = DB::table('deaths')
            ->leftJoin('death_causes', 'death_causes.id', '=', 'deaths.death_cause_id')
            ->select('death_causes.name as name', DB::raw('COUNT(deaths.id) as total'))
            ->groupBy('death_causes.name')
            ->orderByDesc('total');
        $applyFilters($query);
        if ($limit) {
            $query->limit($limit);
        }
        $causas = $query->get();

        return response()->json([
            'type' => 'causas',
            'labels' => $causas->pluck('name')->map(fn($v) => $v ?? 'Sin dato')->values()->all(),
            'counts' => $causas->pluck('total')->map(fn($v) => (int)$v)->values()->all(),
            'total' => array_sum($causas->pluck('total')->all()),
        ]);
    }

    private function getChartJurisdicciones($filters, $dateColumn, $applyFilters, $limit)
    {
        if (!Schema::hasTable('jurisdictions')) {
            return response()->json(['error' => 'Tabla de jurisdicciones no existe'], 404);
        }

        $query = DB::table('deaths')
            ->leftJoin('jurisdictions', 'jurisdictions.id', '=', 'deaths.jurisdiction_id')
            ->select('jurisdictions.name as name', DB::raw('COUNT(deaths.id) as total'))
            ->groupBy('jurisdictions.name')
            ->orderByDesc('total');
        $applyFilters($query);
        if ($limit) {
            $query->limit($limit);
        }
        $jurisdictions = $query->get();

        return response()->json([
            'type' => 'jurisdicciones',
            'labels' => $jurisdictions->pluck('name')->map(fn($v) => $v ?? 'Sin dato')->values()->all(),
            'counts' => $jurisdictions->pluck('total')->map(fn($v) => (int)$v)->values()->all(),
            'total' => array_sum($jurisdictions->pluck('total')->all()),
        ]);
    }

    private function getChartComparativa($filters, $dateColumn, $munCol, $applyFilters, $limit)
    {
        $comparativaType = $filters['comparativa_type'] ?? 'residencia-defuncion';
        
        switch ($comparativaType) {
            case 'genero-causa':
                return $this->getChartComparativaGeneroCausa($filters, $dateColumn, $applyFilters, $limit);
            case 'edad-causa':
                return $this->getChartComparativaEdadCausa($filters, $dateColumn, $applyFilters, $limit);
            case 'lugar-causa':
                return $this->getChartComparativaLugarCausa($filters, $dateColumn, $applyFilters, $limit);
            case 'residencia-defuncion':
            default:
                return $this->getChartComparativaResidenciaDefuncion($filters, $dateColumn, $munCol, $applyFilters, $limit);
        }
    }

    private function getChartComparativaResidenciaDefuncion($filters, $dateColumn, $munCol, $applyFilters, $limit)
    {
        // Obtener municipios
        $munCountsQ = DB::table('deaths')
            ->select(DB::raw("death_municipality_id as muni_id"), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw("death_municipality_id"));
        $applyFilters($munCountsQ);
        $deathCounts = $munCountsQ->get()->pluck('total', 'muni_id')->all();

        $resCountsQ = DB::table('deaths')
            ->select(DB::raw('residence_municipality_id as muni_id'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('residence_municipality_id'));
        $applyFilters($resCountsQ);
        $resCounts = $resCountsQ->get()->pluck('total', 'muni_id')->all();

        $municipalitiesQ = DB::table('municipalities')->select('id','name','jurisdiction_id')->orderBy('name');
        if (Schema::hasTable('jurisdictions') && DB::table('jurisdictions')->count() === 12) {
            $jurIds = DB::table('jurisdictions')->pluck('id')->all();
            $municipalitiesQ->whereIn('jurisdiction_id', $jurIds);
        }
        $municipalitiesFull = $municipalitiesQ->get();

        $municipios = $municipalitiesFull->map(function($m) use ($resCounts, $deathCounts) {
            $resCount = isset($resCounts[$m->id]) ? (int)$resCounts[$m->id] : 0;
            $deathCount = isset($deathCounts[$m->id]) ? (int)$deathCounts[$m->id] : 0;
            return [
                'name' => $m->name ?? 'Sin dato',
                'residence' => $resCount,
                'death' => $deathCount
            ];
        })->sortByDesc(function($m) { return $m['residence'] + $m['death']; });

        if ($limit) {
            $municipios = $municipios->take($limit);
        }

        $labels = $municipios->pluck('name')->values()->all();
        $residence = $municipios->pluck('residence')->values()->all();
        $death = $municipios->pluck('death')->values()->all();

        return response()->json([
            'type' => 'comparativa',
            'labels' => $labels,
            'residence_counts' => $residence,
            'death_counts' => $death,
            'total' => array_sum($residence) + array_sum($death),
        ]);
    }

    private function getChartComparativaGeneroCausa($filters, $dateColumn, $applyFilters, $limit)
    {
        // Obtener causas agrupadas por género
        $dataQ = DB::table('deaths')
            ->leftJoin('death_causes', 'death_causes.id', '=', 'deaths.death_cause_id')
            ->select('deaths.sex', 'death_causes.name as cause', DB::raw('COUNT(deaths.id) as total'))
            ->groupBy('deaths.sex', 'death_causes.name');
        $applyFilters($dataQ);
        $data = $dataQ->get();

        // Agrupar por causa
        $causes = [];
        foreach ($data as $row) {
            $cause = $row->cause ?? 'Sin dato';
            if (!isset($causes[$cause])) {
                $causes[$cause] = ['M' => 0, 'F' => 0, 'Otro' => 0];
            }
            $causes[$cause][$row->sex] = (int)$row->total;
        }

        // Ordenar por total descendente y tomar top N si aplica
        uasort($causes, function($a, $b) {
            $totalA = array_sum($a);
            $totalB = array_sum($b);
            return $totalB <=> $totalA;
        });

        if ($limit) {
            $causes = array_slice($causes, 0, $limit, true);
        }

        $labels = array_keys($causes);
        $male_counts = array_map(function($c) { return $c['M'] ?? 0; }, $causes);
        $female_counts = array_map(function($c) { return $c['F'] ?? 0; }, $causes);

        return response()->json([
            'type' => 'comparativa',
            'labels' => $labels,
            'residence_counts' => $male_counts,
            'death_counts' => $female_counts,
            'total' => array_sum($male_counts) + array_sum($female_counts),
        ]);
    }

    private function getChartComparativaEdadCausa($filters, $dateColumn, $applyFilters, $limit)
    {
        // Rango de edades
        $ageOrder = ['0-4','5-14','15-24','25-34','35-44','45-54','55-64','65-74','75+'];
        
        $dataQ = DB::table('deaths')
            ->leftJoin('death_causes', 'death_causes.id', '=', 'deaths.death_cause_id')
            ->select(
                DB::raw("CASE
                    WHEN deaths.age_years IS NULL OR deaths.age_years < 0 THEN 'Desconocido'
                    WHEN deaths.age_years BETWEEN 0 AND 4 THEN '0-4'
                    WHEN deaths.age_years BETWEEN 5 AND 14 THEN '5-14'
                    WHEN deaths.age_years BETWEEN 15 AND 24 THEN '15-24'
                    WHEN deaths.age_years BETWEEN 25 AND 34 THEN '25-34'
                    WHEN deaths.age_years BETWEEN 35 AND 44 THEN '35-44'
                    WHEN deaths.age_years BETWEEN 45 AND 54 THEN '45-54'
                    WHEN deaths.age_years BETWEEN 55 AND 64 THEN '55-64'
                    WHEN deaths.age_years BETWEEN 65 AND 74 THEN '65-74'
                    ELSE '75+' END as age_range"),
                'death_causes.name as cause',
                DB::raw('COUNT(deaths.id) as total')
            )
            ->groupBy('age_range', 'death_causes.name');
        $applyFilters($dataQ);
        $data = $dataQ->get();

        // Agrupar por rango de edad
        $ageGroups = [];
        foreach ($ageOrder as $age) {
            $ageGroups[$age] = ['causes' => []];
        }
        
        foreach ($data as $row) {
            $age = $row->age_range ?? 'Desconocido';
            $cause = $row->cause ?? 'Sin dato';
            if (!isset($ageGroups[$age])) {
                $ageGroups[$age] = ['causes' => []];
            }
            $ageGroups[$age]['causes'][$cause] = (int)$row->total;
        }

        // Para cada rango de edad, obtener causa principal
        $labels = [];
        $primary_cause_counts = [];
        
        foreach ($ageGroups as $age => $group) {
            if (empty($group['causes'])) continue;
            
            arsort($group['causes']);
            $primaryCause = key($group['causes']);
            $count = current($group['causes']);
            
            $labels[] = $age;
            $primary_cause_counts[] = $count;
        }

        return response()->json([
            'type' => 'comparativa',
            'labels' => $labels,
            'residence_counts' => array_fill(0, count($labels), 0), // Placeholder
            'death_counts' => $primary_cause_counts,
            'total' => array_sum($primary_cause_counts),
        ]);
    }

    private function getChartComparativaLugarCausa($filters, $dateColumn, $applyFilters, $limit)
    {
        // Obtener causas agrupadas por lugar de defunción
        $dataQ = DB::table('deaths')
            ->leftJoin('death_locations', 'death_locations.id', '=', 'deaths.death_location_id')
            ->leftJoin('death_causes', 'death_causes.id', '=', 'deaths.death_cause_id')
            ->select('death_locations.name as location', 'death_causes.name as cause', DB::raw('COUNT(deaths.id) as total'))
            ->groupBy('death_locations.name', 'death_causes.name');
        $applyFilters($dataQ);
        $data = $dataQ->get();

        // Agrupar por lugar
        $locations = [];
        foreach ($data as $row) {
            $location = $row->location ?? 'Sin dato';
            $cause = $row->cause ?? 'Sin dato';
            if (!isset($locations[$location])) {
                $locations[$location] = [];
            }
            $locations[$location][$cause] = (int)$row->total;
        }

        // Ordenar por total descendente y tomar top N si aplica
        uasort($locations, function($a, $b) {
            return array_sum($b) <=> array_sum($a);
        });

        if ($limit) {
            $locations = array_slice($locations, 0, $limit, true);
        }

        $labels = array_keys($locations);
        
        // Para cada lugar, obtener causa principal
        $primary_cause_counts = [];
        foreach ($locations as $location => $causes) {
            arsort($causes);
            $primary_cause_counts[] = current($causes);
        }

        return response()->json([
            'type' => 'comparativa',
            'labels' => $labels,
            'residence_counts' => array_fill(0, count($labels), 0), // Placeholder
            'death_counts' => $primary_cause_counts,
            'total' => array_sum($primary_cause_counts),
        ]);
    }
}
