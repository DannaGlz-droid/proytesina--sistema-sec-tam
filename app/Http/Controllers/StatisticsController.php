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

            // 5) Edades -> intentar usar birth_date si existe, si no usar columna age si existe
            $edades = collect();
            if (Schema::hasColumn('deaths', 'birth_date')) {
                $edades = DB::table('deaths')
                    ->select(DB::raw("CASE
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= 18 THEN '0-18'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 19 AND 35 THEN '19-35'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 36 AND 50 THEN '36-50'
                        WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 51 AND 65 THEN '51-65'
                        ELSE '65+' END as range"),
                        DB::raw('COUNT(*) as total'))
                    ->groupBy('range')
                    ->get();
            } elseif (Schema::hasColumn('deaths', 'age')) {
                // Agrupar en PHP si sólo hay columna age
                $byAge = DB::table('deaths')
                    ->select('age')
                    ->whereNotNull('age')
                    ->get()
                    ->pluck('age')
                    ->toArray();

                $ranges = [
                    '0-18' => 0,
                    '19-35' => 0,
                    '36-50' => 0,
                    '51-65' => 0,
                    '65+' => 0,
                ];

                foreach ($byAge as $a) {
                    if ($a <= 18) $ranges['0-18']++;
                    elseif ($a <= 35) $ranges['19-35']++;
                    elseif ($a <= 50) $ranges['36-50']++;
                    elseif ($a <= 65) $ranges['51-65']++;
                    else $ranges['65+']++;
                }

                $edades = collect();
                foreach ($ranges as $r => $t) {
                    $edades->push((object)['range' => $r, 'total' => $t]);
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
    // Accept flexible filter keys: start_date,end_date, municipio (name), municipality_id (id), causa (name), cause_id (id), sex, limit
    $filters = $request->all();

        // resolver columna de fecha
        $dateColumn = Schema::hasColumn('deaths', 'death_date') ? 'death_date' : 'created_at';

        // helper para aplicar filtros a un query builder
        $applyFilters = function ($query) use ($filters, $dateColumn) {
            // Fecha
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $query->whereBetween($dateColumn, [$filters['start_date'], $filters['end_date']]);
            }

            // Municipio: accept numeric id (municipality_id) or name (municipio)
            if (!empty($filters['municipality_id']) && is_numeric($filters['municipality_id'])) {
                $query->where('death_municipality_id', (int)$filters['municipality_id']);
            } elseif (!empty($filters['municipio'])) {
                // filter by municipality name: find matching ids
                $names = (array) $filters['municipio'];
                $query->whereIn('death_municipality_id', DB::table('municipalities')->select('id')->whereIn('name', $names));
            } elseif (!empty($filters['municipioDefuncion'])) {
                $names = (array) $filters['municipioDefuncion'];
                $query->whereIn('death_municipality_id', DB::table('municipalities')->select('id')->whereIn('name', $names));
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

        // Municipios
        $municipiosQ = DB::table('deaths')
            ->leftJoin('municipalities', 'municipalities.id', '=', 'deaths.death_municipality_id')
            ->select('municipalities.name as name', DB::raw('COUNT(deaths.id) as total'))
            ->groupBy('municipalities.name')
            ->orderByDesc('total');
        $applyFilters($municipiosQ);
        if (!empty($filters['limit']) && is_numeric($filters['limit'])) {
            $municipiosQ->limit((int)$filters['limit']);
        }
        $municipios = $municipiosQ->get();

        // Meses
        $mesesQ = DB::table('deaths')
            ->select(DB::raw("MONTH(deaths.{$dateColumn}) as month_number"),
                     DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%b') as month_name"),
                     DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw("MONTH(deaths.{$dateColumn})"), DB::raw("DATE_FORMAT(deaths.{$dateColumn}, '%b')"))
            ->orderBy(DB::raw("MONTH(deaths.{$dateColumn})"));
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
        if (Schema::hasColumn('deaths', 'birth_date')) {
            $edadesQ = DB::table('deaths')
                ->select(DB::raw("CASE
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= 18 THEN '0-18'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 19 AND 35 THEN '19-35'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 36 AND 50 THEN '36-50'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 51 AND 65 THEN '51-65'
                    ELSE '65+' END as range"), DB::raw('COUNT(*) as total'))
                ->groupBy('range');
            $applyFilters($edadesQ);
            $edades = $edadesQ->get();
        } elseif (Schema::hasColumn('deaths', 'age')) {
            $byAgeQ = DB::table('deaths')->select('age')->whereNotNull('age');
            $applyFilters($byAgeQ);
            $byAge = $byAgeQ->get()->pluck('age')->toArray();

            $ranges = ['0-18'=>0,'19-35'=>0,'36-50'=>0,'51-65'=>0,'65+'=>0];
            foreach ($byAge as $a) {
                if ($a <= 18) $ranges['0-18']++;
                elseif ($a <= 35) $ranges['19-35']++;
                elseif ($a <= 50) $ranges['36-50']++;
                elseif ($a <= 65) $ranges['51-65']++;
                else $ranges['65+']++;
            }
            foreach ($ranges as $r => $t) $edades->push((object)['range'=>$r,'total'=>$t]);
        }

        // Transformar a arrays simples
        $response = [
            'municipios' => [ 'labels' => $municipios->pluck('name')->map(fn($v) => $v ?? 'Sin dato')->values()->all(), 'counts' => $municipios->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
            'meses' => [ 'labels' => $meses->pluck('month_name')->values()->all(), 'counts' => $meses->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
            'generos' => [ 'labels' => $generos->pluck('sex')->map(fn($v) => $v ?? 'Sin dato')->values()->all(), 'counts' => $generos->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
            'causas' => [ 'labels' => $causas->pluck('name')->map(fn($v) => $v ?? 'Sin dato')->values()->all(), 'counts' => $causas->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
            'edades' => [ 'labels' => $edades->pluck('range')->values()->all(), 'counts' => $edades->pluck('total')->map(fn($v) => (int)$v)->values()->all() ],
        ];

        return response()->json($response);
    }
}
