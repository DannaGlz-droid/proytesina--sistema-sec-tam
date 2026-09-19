<?php

namespace App\Http\Controllers;

use App\Models\DeathCause;
use App\Models\District;
use App\Services\DeathFilterService;
use App\Services\StatisticsAnalysisService;
use App\Support\CatalogLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class StatisticsController extends Controller
{
    public function __construct(
        private readonly DeathFilterService $deathFilters,
        private readonly StatisticsAnalysisService $statisticsAnalysis,
    ) {}

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

            // 3) Sexo
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
        $municipiosLabels = collect($data['municipios'])->pluck('name')->map(fn ($v) => $v ? CatalogLabel::municipality($v) : 'Sin dato')->toArray();
        $municipiosCounts = collect($data['municipios'])->pluck('total')->map(function ($v) { return (int)$v; })->toArray();

        $mesLabels = collect($data['meses'])->pluck('month_name')->toArray();
        $mesCounts = collect($data['meses'])->pluck('total')->map(function ($v) { return (int)$v; })->toArray();

    $generoLabels = collect($data['generos'])->pluck('sex')->map(function ($v) { return $v ?? 'Sin dato'; })->toArray();
        $generoCounts = collect($data['generos'])->pluck('total')->map(function ($v) { return (int)$v; })->toArray();

        $causaLabels = collect($data['causas'])->pluck('name')->map(fn ($v) => $v ? CatalogLabel::cause($v) : 'Sin dato')->toArray();
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
        $municipalities = DB::table('municipalities')
            ->select('id', 'name', 'district_id')
            ->orderBy('name')
            ->get();
        $causes = DeathCause::allowedCatalog();
        $districts = Schema::hasTable('districts') ? District::statisticsCatalog() : collect();
        $sexes = collect([
            (object) ['value' => 'F', 'label' => 'Femenino'],
            (object) ['value' => 'M', 'label' => 'Masculino'],
        ]);

        return compact('municipalities','causes','districts','sexes');
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

            $canonicalFilters = $this->deathFilters->normalize($filters, [
                'municipality_column' => $munCol,
            ]);
            $applyFilters = fn ($query) => $this->deathFilters->apply($query, $canonicalFilters);

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
            $municipalitiesQ = DB::table('municipalities')->select('id','name','district_id')->orderBy('name');
            // If districts table looks like the 12 Tamaulipas districts, restrict to them
            if (Schema::hasTable('districts') && DB::table('districts')->count() === 12) {
                $distIds = DB::table('districts')->pluck('id')->all();
                $municipalitiesQ->whereIn('district_id', $distIds);
            }
            $municipalitiesFull = $municipalitiesQ->get();

            // Map counts onto the full municipalities list and sort by total desc for display
            $municipios = $municipalitiesFull->map(function($m) use ($munCountsRaw) {
                $total = isset($munCountsRaw[$m->id]) ? (int)$munCountsRaw[$m->id] : 0;
                return (object)['name' => $m->name ?? 'Sin dato', 'total' => $total, 'id' => $m->id];
            })->sortByDesc('total')->values();

            // Jurisdicciones (por el campo district_id en deaths, si existe la tabla)
            $districts = collect();
            if (Schema::hasTable('districts')) {
                $distQ = DB::table('deaths')
                    ->leftJoin('districts', 'districts.id', '=', 'deaths.district_id')
                    ->select('districts.name as name', DB::raw('COUNT(deaths.id) as total'))
                    ->groupBy('districts.name')
                    ->orderByDesc('total');
                $applyFilters($distQ);
                if (!empty($filters['limit']) && is_numeric($filters['limit'])) {
                    $distQ->limit((int)$filters['limit']);
                }
                $districts = $distQ->get();
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
                'municipios' => [ 'labels' => $municipios->pluck('name')->map(fn($v) => $v ? CatalogLabel::municipality($v) : 'Sin dato')->values()->all(), 'counts' => $municipios->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                'meses' => [ 'labels' => $meses->pluck('period_label')->values()->all(), 'counts' => $meses->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                'generos' => [ 'labels' => $generos->pluck('sex')->map(fn($v) => $v ?? 'Sin dato')->values()->all(), 'counts' => $generos->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                'causas' => [ 'labels' => $causas->pluck('name')->map(fn($v) => $v ? CatalogLabel::cause($v) : 'Sin dato')->values()->all(), 'counts' => $causas->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                'edades' => [ 'labels' => $edades->pluck('range')->values()->all(), 'counts' => $edades->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                // districts
                'districts' => [ 'labels' => $districts->pluck('name')->map(fn($v) => $v ? CatalogLabel::district($v) : 'Sin dato')->values()->all(), 'counts' => $districts->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
                // Residence vs Death per municipality (aligned to full municipalities list)
                'municipios_compare' => (function() use ($municipios, $resCounts, $deathCounts) {
                    $labels = $municipios->pluck('name')->map(fn ($v) => $v ? CatalogLabel::municipality($v) : 'Sin dato')->values()->all();
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
            $debug = config('app.debug') ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() : null;
            return response()->json([
                'error' => 'No se pudieron obtener los datos de las gráficas.',
                'message' => 'No se pudieron cargar los datos. Inténtalo nuevamente.',
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
            $usesAllTime = !empty($filters['all_time']);
            $usesDefaultPeriod = !$usesAllTime && empty($filters['start_date']) && empty($filters['end_date']) &&
                empty($filters['months']) && empty($filters['years']);
            
            // Si no hay filtros de fecha especificados, aplicar rango default (últimos 12 meses)
            if ($usesDefaultPeriod) {
                $defaultRange = $this->calculateDefaultDateRange();
                $filters['start_date'] = $defaultRange['start_date'];
                $filters['end_date'] = $defaultRange['end_date'];
            }
            
            // Resolver columna de fecha
            $dateColumn = Schema::hasColumn('deaths', 'death_date') ? 'death_date' : 'created_at';

            // Unificar los dos nombres internos del ámbito para que filtros,
            // agrupación, tabla y exportación nunca interpreten alcances distintos.
            $requestedMunicipalityType = $filters['municipio_type'] ?? null;
            $usesResidenceScope = $requestedMunicipalityType === 'residencia'
                || ($requestedMunicipalityType === null && ($filters['municipio_kind'] ?? null) === 'residence');
            $filters['municipio_type'] = $usesResidenceScope ? 'residencia' : 'defuncion';
            $filters['municipio_kind'] = $usesResidenceScope ? 'residence' : 'death';
            $munCol = $usesResidenceScope ? 'residence_municipality_id' : 'death_municipality_id';
            
            $canonicalFilters = $this->deathFilters->normalize($filters, [
                'municipality_column' => $munCol,
            ]);
            $analysisContext = $this->statisticsAnalysis->context([
                'analysis_type' => $chartType,
                'comparativa_type' => $filters['comparativa_type'] ?? null,
                'municipio_type' => $filters['municipio_type'] ?? null,
            ]);
            $applyDataFilters = fn ($query) => $this->deathFilters->apply($query, $canonicalFilters);
            $applyFilters = function ($query) use ($applyDataFilters, $analysisContext) {
                $applyDataFilters($query);
                if ($analysisContext) {
                    $this->statisticsAnalysis->applyEligible($query, $analysisContext);
                }

                return $query;
            };
            
            $limit = !empty($filters['limit']) && is_numeric($filters['limit']) ? (int)$filters['limit'] : null;
            
            // Según el tipo de gráfica, ejecutar consultas específicas
            switch ($chartType) {
                case 'municipios':
                    $response = $this->getChartMunicipios($filters, $dateColumn, $munCol, $applyFilters, $limit);
                    break;
                    
                case 'tendencias':
                    $response = $this->getChartTendencias($filters, $dateColumn, $applyFilters);
                    break;
                    
                case 'edades':
                    $response = $this->getChartEdades($filters, $dateColumn, $applyFilters, $limit);
                    break;
                    
                case 'genero':
                    $response = $this->getChartGenero($filters, $dateColumn, $applyFilters);
                    break;
                    
                case 'causas':
                    $response = $this->getChartCausas($filters, $dateColumn, $applyFilters, $limit);
                    break;
                    
                case 'jurisdicciones':
                case 'distritoes':
                    $response = $this->getChartJurisdicciones($filters, $dateColumn, $applyFilters, $limit);
                    break;

                case 'lugares':
                    $response = $this->getChartLugares($filters, $dateColumn, $applyFilters, $limit);
                    break;
                    
                case 'comparativa':
                    $response = $this->getChartComparativa($filters, $dateColumn, $munCol, $applyFilters, $limit);
                    break;
                    
                default:
                    return response()->json(['error' => 'El tipo de gráfica seleccionado no es válido.'], 400);
            }

            if ($response->getStatusCode() >= 400) {
                return $response;
            }

            $payload = $response->getData(true);
            $matchedQuery = DB::table('deaths');
            $applyDataFilters($matchedQuery);
            $matchedTotal = $matchedQuery->count();

            $contextQuery = DB::table('deaths')
                ->selectRaw("COUNT(*) as filtered_total, MIN({$dateColumn}) as data_start, MAX({$dateColumn}) as data_end");
            $applyFilters($contextQuery);
            $context = $contextQuery->first();
            $filteredTotal = (int) ($context->filtered_total ?? 0);
            $displayedTotal = (int) ($payload['displayed_total'] ?? $payload['total'] ?? 0);
            $coverageNumerator = (int) ($payload['coverage_numerator'] ?? $displayedTotal);

            $payload['filtered_total'] = $filteredTotal;
            $payload['matched_total'] = $matchedTotal;
            $payload['excluded_total'] = max(0, $matchedTotal - $filteredTotal);
            $payload['quality'] = [
                'excluded_total' => max(0, $matchedTotal - $filteredTotal),
                'required_fields' => $analysisContext['required_fields'] ?? [],
            ];
            $payload['displayed_total'] = $displayedTotal;
            $payload['total'] = $filteredTotal;
            $payload['displayed_categories'] = (int) ($payload['displayed_categories'] ?? count($payload['labels'] ?? []));
            $payload['available_categories'] = (int) ($payload['available_categories'] ?? $payload['displayed_categories']);
            $payload['coverage_percentage'] = $filteredTotal > 0
                ? round(min(100, ($coverageNumerator / $filteredTotal) * 100), 1)
                : 0.0;
            $payload['omitted_total'] = max(0, $filteredTotal - $displayedTotal);
            $payload['period'] = [
                'start_date' => $filters['start_date'] ?? null,
                'end_date' => $filters['end_date'] ?? null,
                'years' => array_values($filters['years'] ?? []),
                'months' => array_values($filters['months'] ?? []),
                'data_start' => $context->data_start ?? null,
                'data_end' => $context->data_end ?? null,
                'is_default' => $usesDefaultPeriod,
                'is_all_time' => $usesAllTime,
            ];
            $payload['source_summary'] = $this->getChartSourceSummary($applyFilters);

            unset($payload['coverage_numerator']);

            return response()->json($payload);
        } catch (\Throwable $e) {
            \Log::error('getChartData error: ' . $e->getMessage(), ['exception' => $e]);
            $debug = config('app.debug') ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() : null;
            return response()->json([
                'error' => 'No se pudieron obtener los datos de la gráfica.',
                'message' => 'No se pudieron cargar los datos. Inténtalo nuevamente.',
                'debug' => $debug,
            ], 500);
        }
    }

    /**
     * Resume el origen de todos los registros que cumplen los filtros de datos.
     * El límite Top N no interviene porque es una opción de presentación.
     */
    private function getChartSourceSummary(callable $applyFilters): array
    {
        $sourceQuery = DB::table('deaths')
            ->select('import_id', DB::raw('COUNT(*) as records'))
            ->groupBy('import_id');

        $applyFilters($sourceQuery);
        $sourceGroups = $sourceQuery->get();
        $importIds = $sourceGroups
            ->pluck('import_id')
            ->filter(fn ($id) => $id !== null)
            ->map(fn ($id) => (int) $id)
            ->values();
        $importsById = $importIds->isEmpty()
            ? collect()
            : DB::table('imports')
                ->whereIn('id', $importIds->all())
                ->get(['id', 'original_name', 'created_at'])
                ->keyBy('id');

        $manualRecords = 0;
        $imports = [];

        foreach ($sourceGroups as $group) {
            $records = (int) $group->records;

            if ($group->import_id === null) {
                $manualRecords += $records;
                continue;
            }

            $importId = (int) $group->import_id;
            $import = $importsById->get($importId);
            $imports[] = [
                'id' => $importId,
                'name' => $import?->original_name ?: "Importación #{$importId}",
                'records' => $records,
                'imported_at' => $import?->created_at,
            ];
        }

        usort($imports, fn (array $left, array $right) => $right['records'] <=> $left['records']);

        return [
            'imports_count' => count($imports),
            'manual_records' => $manualRecords,
            'total_records' => $sourceGroups->sum(fn ($group) => (int) $group->records),
            'imports' => $imports,
        ];
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

        $applyFilters($munCountsQ);
        $munCountsRaw = $munCountsQ->get()->pluck('total', 'muni_id')->all();

        $municipalitiesQ = DB::table('municipalities')->select('id','name','district_id')->orderBy('name');
        if (Schema::hasTable('districts') && DB::table('districts')->count() === 12) {
            $distIds = DB::table('districts')->pluck('id')->all();
            $municipalitiesQ->whereIn('district_id', $distIds);
        }
        $municipalitiesFull = $municipalitiesQ->get();
        $municipios = $municipalitiesFull->map(function($m) use ($munCountsRaw) {
            $total = isset($munCountsRaw[$m->id]) ? (int)$munCountsRaw[$m->id] : 0;
            return ['id' => (int) $m->id, 'name' => $m->name ?? 'Sin dato', 'total' => $total];
        })->filter(fn ($municipality) => $municipality['total'] > 0)->sort(function ($left, $right) {
            $byTotal = $right['total'] <=> $left['total'];

            return $byTotal !== 0
                ? $byTotal
                : strnatcasecmp((string) $left['name'], (string) $right['name']);
        })->values();

        $availableCategories = $municipios->count();
        
        if ($limit) {
            $municipios = $municipios->take($limit);
        }

        $displayedTotal = (int) $municipios->sum('total');

        return response()->json([
            'type' => 'municipios',
            'labels' => $municipios->pluck('name')->map(fn ($v) => $v ? CatalogLabel::municipality($v) : 'Sin dato')->values()->all(),
            'counts' => $municipios->pluck('total')->values()->all(),
            'displayed_total' => $displayedTotal,
            'available_categories' => $availableCategories,
            'ranking_label' => 'municipios',
        ]);
    }

    private function getChartTendencias($filters, $dateColumn, $applyFilters)
    {
        $groupBy = $filters['group_by'] ?? 'month';
        $driver = DB::connection()->getDriverName();
        $column = "deaths.{$dateColumn}";
        $periodExpression = match ($groupBy) {
            'day' => "DATE({$column})",
            'year' => $driver === 'sqlite'
                ? "strftime('%Y', {$column})"
                : "DATE_FORMAT({$column}, '%Y')",
            default => $driver === 'sqlite'
                ? "strftime('%Y-%m', {$column})"
                : "DATE_FORMAT({$column}, '%Y-%m')",
        };

        $query = DB::table('deaths')
            ->selectRaw("{$periodExpression} as period, COUNT(*) as total")
            ->groupByRaw($periodExpression)
            ->orderByRaw($periodExpression);
        
        $applyFilters($query);
        $data = $query->get();
        
        $monthsSpanish = [1 => 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $data->each(function ($item) use ($groupBy, $monthsSpanish) {
            if ($groupBy === 'day') {
                [$year, $month, $day] = array_map('intval', explode('-', $item->period));
                $item->period_label = sprintf('%02d %s %d', $day, $monthsSpanish[$month], $year);
            } elseif ($groupBy === 'year') {
                $item->period_label = (string) $item->period;
            } else {
                [$year, $month] = array_map('intval', explode('-', $item->period));
                $item->period_label = $monthsSpanish[$month].' '.$year;
            }
        });

        return response()->json([
            'type' => 'tendencias',
            'group_by' => $groupBy,
            'labels' => $data->pluck('period_label')->values()->all(),
            'counts' => $data->pluck('total')->map(fn($v) => (int)$v)->values()->all(),
            'displayed_total' => array_sum($data->pluck('total')->all()),
        ]);
    }

    private function getChartEdades($filters, $dateColumn, $applyFilters, $limit)
    {
        $ageOrder = ['<5 años','5-19 años','20-64 años','65+ años','Sin dato'];
        $edades = collect();
        $causasPorEdad = [];

        // Build subquery first for age with causes
        $subquery = DB::table('deaths')
            ->leftJoin('death_causes', 'death_causes.id', '=', 'deaths.death_cause_id')
            ->select(
                DB::raw("CASE
                    WHEN age IS NULL OR age < 0 THEN 'Sin dato'
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
            
            $causasPorEdad[$ageLabel] = $causesInAge
                ->mapWithKeys(fn ($total, $cause) => [
                    $cause ? CatalogLabel::cause($cause) : 'Sin dato' => $total,
                ])
                ->toArray();
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
            'displayed_total' => array_sum($edades->pluck('total')->all()),
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
            if (in_array(strtolower($label), ['m', 'masculino', 'hombre'])) $label = 'Masculino';
            elseif (in_array(strtolower($label), ['f', 'femenino', 'mujer'])) $label = 'Femenino';
            return ['label' => $label, 'total' => (int)$g->total];
        })->sortBy(fn ($item) => array_search($item['label'], ['Masculino', 'Femenino', 'Sin dato'], true))->values();

        return response()->json([
            'type' => 'genero',
            'labels' => $generos->pluck('label')->values()->all(),
            'counts' => $generos->pluck('total')->values()->all(),
            'displayed_total' => array_sum($generos->pluck('total')->all()),
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
        $causas = $query->get();
        $availableCategories = $causas->count();
        if ($limit) $causas = $causas->take($limit);

        return response()->json([
            'type' => 'causas',
            'labels' => $causas->pluck('name')->map(fn($v) => $v ? CatalogLabel::cause($v) : 'Sin dato')->values()->all(),
            'counts' => $causas->pluck('total')->map(fn($v) => (int)$v)->values()->all(),
            'displayed_total' => array_sum($causas->pluck('total')->all()),
            'available_categories' => $availableCategories,
            'ranking_label' => 'causas',
        ]);
    }

    private function getChartJurisdicciones($filters, $dateColumn, $applyFilters, $limit)
    {
        if (!Schema::hasTable('districts')) {
            return response()->json(['error' => 'No se pudo cargar la información de los distritos.'], 404);
        }

        $districtColumn = ($filters['municipio_type'] ?? 'defuncion') === 'residencia'
            ? 'district_id'
            : 'death_district_id';

        $query = DB::table('deaths')
            ->leftJoin('districts', 'districts.id', '=', "deaths.{$districtColumn}")
            ->select('districts.name as name', DB::raw('COUNT(deaths.id) as total'))
            ->groupBy('districts.name')
            ->orderByDesc('total');
        $applyFilters($query);
        $districts = $query->get();
        $availableCategories = $districts->count();
        if ($limit) $districts = $districts->take($limit);

        return response()->json([
            'type' => 'jurisdicciones',
            'labels' => $districts->pluck('name')->map(fn($v) => $v ? CatalogLabel::district($v) : 'Sin dato')->values()->all(),
            'counts' => $districts->pluck('total')->map(fn($v) => (int)$v)->values()->all(),
            'displayed_total' => array_sum($districts->pluck('total')->all()),
            'available_categories' => $availableCategories,
            'ranking_label' => 'distritos',
        ]);
    }

    private function getChartLugares($filters, $dateColumn, $applyFilters, $limit)
    {
        $query = DB::table('deaths')
            ->join('death_locations', 'death_locations.id', '=', 'deaths.death_location_id')
            ->select(
                'death_locations.id as id',
                'death_locations.name as name',
                DB::raw('COUNT(deaths.id) as total')
            )
            ->groupBy('death_locations.id', 'death_locations.name')
            ->orderByDesc('total')
            ->orderBy('death_locations.name');
        $applyFilters($query);
        $locations = $query->get();
        $availableCategories = $locations->count();
        if ($limit) $locations = $locations->take($limit);

        return response()->json([
            'type' => 'lugares',
            'labels' => $locations->pluck('name')->map(fn ($value) => CatalogLabel::location($value))->values()->all(),
            'counts' => $locations->pluck('total')->map(fn ($value) => (int) $value)->values()->all(),
            'displayed_total' => array_sum($locations->pluck('total')->all()),
            'available_categories' => $availableCategories,
            'ranking_label' => 'lugares',
        ]);
    }

    private function getChartComparativa($filters, $dateColumn, $munCol, $applyFilters, $limit)
    {
        $comparativaType = $filters['comparativa_type'] ?? 'residencia-defuncion';
        
        switch ($comparativaType) {
            case 'distrito-residencia-defuncion':
                return $this->getChartComparativaDistritos($filters, $dateColumn, $applyFilters, $limit);
            case 'genero-causa':
                return $this->getChartComparativaGeneroCausa($filters, $dateColumn, $applyFilters, $limit);
            case 'edad-causa':
                return $this->getChartComparativaEdadCausa($filters, $dateColumn, $applyFilters, $limit);
            case 'lugar-causa':
                return $this->getChartComparativaLugarCausa($filters, $dateColumn, $applyFilters, $limit);
            case 'lugar-municipio':
                return $this->getChartComparativaLugarMunicipio($filters, $dateColumn, $applyFilters, $limit);
            case 'residencia-defuncion':
            default:
                return $this->getChartComparativaResidenciaDefuncion($filters, $dateColumn, $munCol, $applyFilters, $limit);
        }
    }

    private function getChartComparativaDistritos($filters, $dateColumn, $applyFilters, $limit)
    {
        $dataQuery = DB::table('deaths')
            ->join('districts as residence_districts', 'residence_districts.id', '=', 'deaths.district_id')
            ->join('districts as death_districts', 'death_districts.id', '=', 'deaths.death_district_id')
            ->select(
                'residence_districts.id as residence_id',
                'residence_districts.name as residence_name',
                'death_districts.id as death_id',
                'death_districts.name as death_name',
                DB::raw('COUNT(deaths.id) as total')
            )
            ->groupBy(
                'residence_districts.id',
                'residence_districts.name',
                'death_districts.id',
                'death_districts.name'
            );
        $applyFilters($dataQuery);
        $rows = $dataQuery->get();

        $deathTotals = [];
        $residenceTotals = [];
        $matrix = [];
        foreach ($rows as $row) {
            $deathId = (int) $row->death_id;
            $residenceId = (int) $row->residence_id;
            $total = (int) $row->total;
            $deathTotals[$deathId] = [
                'name' => $row->death_name,
                'total' => ($deathTotals[$deathId]['total'] ?? 0) + $total,
            ];
            $residenceTotals[$residenceId] = [
                'name' => $row->residence_name,
                'total' => ($residenceTotals[$residenceId]['total'] ?? 0) + $total,
            ];
            $matrix[$residenceId][$deathId] = $total;
        }

        uasort($deathTotals, fn ($left, $right) => $right['total'] <=> $left['total']);
        uasort($residenceTotals, fn ($left, $right) => $right['total'] <=> $left['total']);
        $availableCategories = count($deathTotals);
        if ($limit) {
            $deathTotals = array_slice($deathTotals, 0, $limit, true);
        }

        $deathIds = array_keys($deathTotals);
        $labels = array_map(
            fn ($district) => CatalogLabel::district($district['name']),
            array_values($deathTotals)
        );
        $series = [];
        foreach ($residenceTotals as $residenceId => $district) {
            $series[] = [
                'name' => CatalogLabel::district($district['name']),
                'data' => array_map(
                    fn ($deathId) => (int) ($matrix[$residenceId][$deathId] ?? 0),
                    $deathIds
                ),
            ];
        }

        $displayedTotal = array_sum(array_map(
            fn ($seriesItem) => array_sum($seriesItem['data']),
            $series
        ));

        return response()->json([
            'type' => 'comparativa',
            'labels' => $labels,
            'series' => $series,
            'matrix' => true,
            'x_axis_label' => 'Distrito de defunción',
            'y_axis_label' => 'Distrito de residencia',
            'displayed_total' => $displayedTotal,
            'coverage_numerator' => $displayedTotal,
            'available_categories' => $availableCategories,
            'ranking_label' => 'distritos de defunción',
        ]);
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

        $municipalitiesQ = DB::table('municipalities')->select('id','name','district_id')->orderBy('name');
        if (Schema::hasTable('districts') && DB::table('districts')->count() === 12) {
            $distIds = DB::table('districts')->pluck('id')->all();
            $municipalitiesQ->whereIn('district_id', $distIds);
        }
        $municipalitiesFull = $municipalitiesQ->get();

        $municipios = $municipalitiesFull->map(function($m) use ($resCounts, $deathCounts) {
            $resCount = isset($resCounts[$m->id]) ? (int)$resCounts[$m->id] : 0;
            $deathCount = isset($deathCounts[$m->id]) ? (int)$deathCounts[$m->id] : 0;
            return [
                'id' => (int) $m->id,
                'name' => $m->name ?? 'Sin dato',
                'residence' => $resCount,
                'death' => $deathCount
            ];
        })->filter(fn ($m) => $m['residence'] > 0 || $m['death'] > 0)
            ->sortByDesc(function($m) { return $m['residence'] + $m['death']; })
            ->values();

        $availableCategories = $municipios->count();

        if ($limit) {
            $municipios = $municipios->take($limit);
        }

        $labels = $municipios->pluck('name')->map(fn ($v) => $v ? CatalogLabel::municipality($v) : 'Sin dato')->values()->all();
        $residence = $municipios->pluck('residence')->values()->all();
        $death = $municipios->pluck('death')->values()->all();
        $selectedMunicipalityIds = $municipios->pluck('id')->all();

        $coverageQuery = DB::table('deaths');
        $applyFilters($coverageQuery);
        if (!empty($selectedMunicipalityIds)) {
            $coverageQuery->where(function ($query) use ($selectedMunicipalityIds) {
                $query->whereIn('residence_municipality_id', $selectedMunicipalityIds)
                    ->orWhereIn('death_municipality_id', $selectedMunicipalityIds);
            });
        }
        $representedRecords = empty($selectedMunicipalityIds) ? 0 : $coverageQuery->count();

        return response()->json([
            'type' => 'comparativa',
            'labels' => $labels,
            'residence_counts' => $residence,
            'death_counts' => $death,
            'series' => [
                ['name' => 'Municipio de residencia', 'data' => $residence],
                ['name' => 'Municipio de defunción', 'data' => $death],
            ],
            'displayed_total' => $representedRecords,
            'coverage_numerator' => $representedRecords,
            'available_categories' => $availableCategories,
            'ranking_label' => 'municipios',
        ]);
    }

    private function getChartComparativaGeneroCausa($filters, $dateColumn, $applyFilters, $limit)
    {
        // Obtener causas agrupadas por sexo
        $dataQ = DB::table('deaths')
            ->leftJoin('death_causes', 'death_causes.id', '=', 'deaths.death_cause_id')
            ->select('deaths.sex', 'death_causes.id as cause_id', 'death_causes.name as cause', DB::raw('COUNT(deaths.id) as total'))
            ->groupBy('deaths.sex', 'death_causes.id', 'death_causes.name');
        $applyFilters($dataQ);
        $data = $dataQ->get();

        // Agrupar por causa y normalizar todos los valores de sexo presentes.
        $causes = [];
        $sexLabels = [];
        foreach ($data as $row) {
            $cause = $row->cause ?? 'Sin dato';
            $sex = match (strtolower((string) ($row->sex ?? ''))) {
                'm', 'masculino', 'hombre' => 'Masculino',
                'f', 'femenino', 'mujer' => 'Femenino',
                default => 'Sin dato',
            };
            if (!isset($causes[$cause])) {
                $causes[$cause] = ['cause_id' => $row->cause_id, 'series' => []];
            }
            $causes[$cause]['series'][$sex] = ($causes[$cause]['series'][$sex] ?? 0) + (int) $row->total;
            $sexLabels[$sex] = true;
        }

        // Ordenar por total descendente y tomar top N si aplica
        uasort($causes, function($a, $b) {
            $totalA = array_sum($a['series']);
            $totalB = array_sum($b['series']);
            return $totalB <=> $totalA;
        });

        $availableCategories = count($causes);
        if ($limit) {
            $causes = array_slice($causes, 0, $limit, true);
        }

        $labels = array_map(fn ($cause) => CatalogLabel::cause($cause), array_keys($causes));
        $orderedSexLabels = array_values(array_filter(['Masculino', 'Femenino', 'Sin dato'], fn ($label) => isset($sexLabels[$label])));
        $series = array_map(function ($sex) use ($causes) {
            return [
                'name' => $sex,
                'data' => array_values(array_map(fn ($cause) => (int) ($cause['series'][$sex] ?? 0), $causes)),
            ];
        }, $orderedSexLabels);
        $displayedTotal = array_sum(array_map(fn ($cause) => array_sum($cause['series']), $causes));

        return response()->json([
            'type' => 'comparativa',
            'labels' => $labels,
            'series' => $series,
            'displayed_total' => $displayedTotal,
            'coverage_numerator' => $displayedTotal,
            'available_categories' => $availableCategories,
            'ranking_label' => 'causas',
        ]);
    }

    private function getChartComparativaEdadCausa($filters, $dateColumn, $applyFilters, $limit)
    {
        // Rango de edades
        $ageOrder = ['0-4','5-14','15-24','25-34','35-44','45-54','55-64','65-74','75+','Sin dato'];
        
        $dataQ = DB::table('deaths')
            ->leftJoin('death_causes', 'death_causes.id', '=', 'deaths.death_cause_id')
            ->select(
                DB::raw("CASE
                    WHEN deaths.age IS NULL OR deaths.age < 0 THEN 'Sin dato'
                    WHEN deaths.age BETWEEN 0 AND 4 THEN '0-4'
                    WHEN deaths.age BETWEEN 5 AND 14 THEN '5-14'
                    WHEN deaths.age BETWEEN 15 AND 24 THEN '15-24'
                    WHEN deaths.age BETWEEN 25 AND 34 THEN '25-34'
                    WHEN deaths.age BETWEEN 35 AND 44 THEN '35-44'
                    WHEN deaths.age BETWEEN 45 AND 54 THEN '45-54'
                    WHEN deaths.age BETWEEN 55 AND 64 THEN '55-64'
                    WHEN deaths.age BETWEEN 65 AND 74 THEN '65-74'
                    ELSE '75+' END as age_range"),
                'death_causes.name as cause',
                DB::raw('COUNT(deaths.id) as total')
            )
            ->groupBy('age_range', 'death_causes.name');
        $applyFilters($dataQ);
        $data = $dataQ->get();

        $ageGroups = array_fill_keys($ageOrder, []);
        $causeTotals = [];
        foreach ($data as $row) {
            $age = $row->age_range ?? 'Sin dato';
            $cause = $row->cause ?? 'Sin dato';
            $ageGroups[$age][$cause] = (int) $row->total;
            $causeTotals[$cause] = ($causeTotals[$cause] ?? 0) + (int) $row->total;
        }

        arsort($causeTotals);
        $availableCategories = count($causeTotals);
        $selectedCauses = array_keys($limit ? array_slice($causeTotals, 0, $limit, true) : $causeTotals);
        $labels = array_values(array_filter($ageOrder, fn ($age) => array_sum($ageGroups[$age] ?? []) > 0));
        $series = [];
        foreach ($selectedCauses as $cause) {
            $series[] = [
                'name' => CatalogLabel::cause($cause),
                'data' => array_map(fn ($age) => (int) ($ageGroups[$age][$cause] ?? 0), $labels),
            ];
        }

        $selectedTotal = array_sum(array_intersect_key($causeTotals, array_flip($selectedCauses)));
        $otherData = array_map(function ($age) use ($ageGroups, $selectedCauses) {
            return array_sum(array_diff_key($ageGroups[$age] ?? [], array_flip($selectedCauses)));
        }, $labels);
        if (array_sum($otherData) > 0) $series[] = ['name' => 'Otras causas', 'data' => $otherData];

        return response()->json([
            'type' => 'comparativa',
            'labels' => $labels,
            'series' => $series,
            'stacked' => true,
            'displayed_total' => (int) $data->sum('total'),
            'coverage_numerator' => $selectedTotal,
            'available_categories' => $availableCategories,
            'displayed_categories' => count($selectedCauses),
            'ranking_label' => 'causas',
        ]);
    }

    private function getChartComparativaLugarMunicipio($filters, $dateColumn, $applyFilters, $limit)
    {
        $dataQuery = DB::table('deaths')
            ->join('death_locations', 'death_locations.id', '=', 'deaths.death_location_id')
            ->join('municipalities', 'municipalities.id', '=', 'deaths.death_municipality_id')
            ->select(
                'death_locations.id as location_id',
                'death_locations.name as location_name',
                'municipalities.id as municipality_id',
                'municipalities.name as municipality_name',
                DB::raw('COUNT(deaths.id) as total')
            )
            ->groupBy(
                'death_locations.id',
                'death_locations.name',
                'municipalities.id',
                'municipalities.name'
            );
        $applyFilters($dataQuery);
        $rows = $dataQuery->get();

        $municipalities = [];
        $locations = [];
        $matrix = [];
        foreach ($rows as $row) {
            $municipalityId = (int) $row->municipality_id;
            $locationId = (int) $row->location_id;
            $total = (int) $row->total;
            $municipalities[$municipalityId] = [
                'name' => $row->municipality_name,
                'total' => ($municipalities[$municipalityId]['total'] ?? 0) + $total,
            ];
            $locations[$locationId] = [
                'name' => $row->location_name,
                'total' => ($locations[$locationId]['total'] ?? 0) + $total,
            ];
            $matrix[$locationId][$municipalityId] = $total;
        }

        $sortByTotalAndName = fn ($left, $right) => ($right['total'] <=> $left['total'])
            ?: strcasecmp((string) $left['name'], (string) $right['name']);
        uasort($municipalities, $sortByTotalAndName);
        uasort($locations, $sortByTotalAndName);

        $availableCategories = count($municipalities);
        if ($limit) {
            $municipalities = array_slice($municipalities, 0, $limit, true);
        }

        $municipalityIds = array_keys($municipalities);
        $labels = array_map(
            fn ($municipality) => CatalogLabel::municipality($municipality['name']),
            array_values($municipalities)
        );
        $series = [];
        foreach ($locations as $locationId => $location) {
            $series[] = [
                'name' => CatalogLabel::location($location['name']),
                'data' => array_map(
                    fn ($municipalityId) => (int) ($matrix[$locationId][$municipalityId] ?? 0),
                    $municipalityIds
                ),
            ];
        }

        $displayedTotal = array_sum(array_map(
            fn ($seriesItem) => array_sum($seriesItem['data']),
            $series
        ));

        return response()->json([
            'type' => 'comparativa',
            'labels' => $labels,
            'series' => $series,
            'matrix' => true,
            'stacked' => true,
            'x_axis_label' => 'Municipio de defunción',
            'y_axis_label' => 'Lugar de defunción',
            'displayed_total' => $displayedTotal,
            'coverage_numerator' => $displayedTotal,
            'available_categories' => $availableCategories,
            'ranking_label' => 'municipios de defunción',
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

        $locations = [];
        $causeTotals = [];
        foreach ($data as $row) {
            $location = $row->location ?? 'Sin dato';
            $cause = $row->cause ?? 'Sin dato';
            if (!isset($locations[$location])) {
                $locations[$location] = [];
            }
            $locations[$location][$cause] = (int)$row->total;
            $causeTotals[$cause] = ($causeTotals[$cause] ?? 0) + (int) $row->total;
        }

        // Ordenar por total descendente y tomar top N si aplica
        uasort($locations, function($a, $b) {
            return array_sum($b) <=> array_sum($a);
        });

        $availableCategories = count($locations);
        if ($limit) $locations = array_slice($locations, 0, $limit, true);

        $labels = array_map(fn ($location) => CatalogLabel::location($location), array_keys($locations));
        arsort($causeTotals);
        $selectedCauses = array_keys(array_slice($causeTotals, 0, 4, true));
        $series = [];
        foreach ($selectedCauses as $cause) {
            $series[] = [
                'name' => CatalogLabel::cause($cause),
                'data' => array_map(fn ($location) => (int) ($locations[$location][$cause] ?? 0), $labels),
            ];
        }

        $otherData = array_map(function ($location) use ($locations, $selectedCauses) {
            return array_sum(array_diff_key($locations[$location], array_flip($selectedCauses)));
        }, $labels);
        if (array_sum($otherData) > 0) $series[] = ['name' => 'Otras causas', 'data' => $otherData];
        $displayedTotal = array_sum(array_map('array_sum', $locations));

        return response()->json([
            'type' => 'comparativa',
            'labels' => $labels,
            'series' => $series,
            'stacked' => true,
            'displayed_total' => $displayedTotal,
            'coverage_numerator' => $displayedTotal,
            'available_categories' => $availableCategories,
            'ranking_label' => 'lugares',
        ]);
    }
}
