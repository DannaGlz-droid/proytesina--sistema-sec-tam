<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Death;
use App\Models\DeathCause;
use App\Models\Municipality;
use App\Models\Jurisdiction;
use App\Models\DeathLocation;
use Illuminate\Support\Facades\Redirect;

class DeathController extends Controller
{
    //
    public function index(Request $request)
    {
        // Validate and normalize incoming filter/pagination params
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'in:25,50,100,250'],
            'sort' => ['nullable', 'string'],
            'dateRange' => ['nullable', 'string'],
            'year' => ['nullable', 'integer'],
            'month' => ['nullable', 'string'],
            'selectedMonths' => ['nullable', 'array'],
            'selectedMonths.*' => ['nullable', 'string'],
            'quarter' => ['nullable', 'in:1,2,3,4'],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date'],
            'jurisdiccion' => ['nullable', 'string'],
            'municipio' => ['nullable', 'string'],
            'municipioDefuncion' => ['nullable', 'string'],
            'sexo' => ['nullable', 'string'],
            'edad' => ['nullable', 'string'],
            'causa' => ['nullable', 'string'],
        ]);

    $perPage = isset($validated['per_page']) ? (int) $validated['per_page'] : 25;
    $q = $validated['q'] ?? null;
    // Default sort: newest records first by insertion (id desc)
    $sort = $validated['sort'] ?? $request->input('sort', 'id_desc');

        $query = Death::query();

        // Simple text search (name or parts of the name or age if numeric)
        if ($q) {
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('first_last_name', 'like', "%{$q}%")
                    ->orWhere('second_last_name', 'like', "%{$q}%");

                if (is_numeric($q)) {
                    $qry->orWhere('age', (int) $q);
                }
            });
        }

        // Date filters (based on dateRange / year / month / selectedMonths / quarter / custom range)
        $dateRange = $request->input('dateRange');
        if ($dateRange && $dateRange !== 'custom') {
            // If dateRange is numeric treat as days
            if (is_numeric($dateRange)) {
                $days = (int) $dateRange;
                $query->whereDate('death_date', '>=', now()->subDays($days));
            } elseif ($dateRange === 'year') {
                $year = $request->input('year') ? (int) $request->input('year') : now()->year;
                $query->whereYear('death_date', $year);
            } elseif ($dateRange === 'month') {
                $year = $request->input('year') ? (int) $request->input('year') : now()->year;
                $month = $request->input('month');
                if ($month) {
                    $query->whereYear('death_date', $year)->whereMonth('death_date', $month);
                }
            } elseif ($dateRange === 'multiple-months') {
                $year = $request->input('year') ? (int) $request->input('year') : now()->year;
                $months = $request->input('selectedMonths', []);
                if (!empty($months)) {
                    $query->whereYear('death_date', $year)
                          ->whereIn(
                              \DB::raw('MONTH(death_date)'),
                              array_map('intval', $months)
                          );
                }
            } elseif ($dateRange === 'quarter') {
                $year = $request->input('year') ? (int) $request->input('year') : now()->year;
                $quarter = (int) $request->input('quarter');
                if ($quarter >= 1 && $quarter <= 4) {
                    $startMonth = ($quarter - 1) * 3 + 1;
                    $endMonth = $startMonth + 2;
                    $query->whereYear('death_date', $year)
                          ->whereBetween(\DB::raw('MONTH(death_date)'), [$startMonth, $endMonth]);
                }
            }
        } elseif ($dateRange === 'custom') {
            if ($request->filled('startDate')) {
                $query->whereDate('death_date', '>=', $request->input('startDate'));
            }
            if ($request->filled('endDate')) {
                $query->whereDate('death_date', '<=', $request->input('endDate'));
            }
        }

        // Jurisdiction / Municipality (residence) / death municipality filters
        if ($request->filled('jurisdiccion')) {
            $val = $request->input('jurisdiccion');
            $query->whereHas('jurisdiction', function ($qj) use ($val) {
                $qj->whereRaw('LOWER(name) = ?', [strtolower($val)])
                   ->orWhere('name', 'like', "%{$val}%");
            });
        }

        if ($request->filled('municipio')) {
            $val = $request->input('municipio');
            $query->whereHas('residenceMunicipality', function ($qm) use ($val) {
                $qm->whereRaw('LOWER(name) = ?', [strtolower($val)])
                   ->orWhere('name', 'like', "%{$val}%");
            });
        }

        if ($request->filled('municipioDefuncion')) {
            $val = $request->input('municipioDefuncion');
            $query->whereHas('deathMunicipality', function ($qm) use ($val) {
                $qm->whereRaw('LOWER(name) = ?', [strtolower($val)])
                   ->orWhere('name', 'like', "%{$val}%");
            });
        }

        // Sex filter (case-insensitive)
        if ($request->filled('sexo')) {
            $val = $request->input('sexo');
            $query->whereRaw('LOWER(sex) = ?', [strtolower($val)]);
        }

        // Age filter parsing: single, range (min-max) or csv list
        if ($request->filled('edad')) {
            $edad = trim($request->input('edad'));
            if (strpos($edad, '-') !== false) {
                [$min, $max] = array_map('intval', explode('-', $edad, 2));
                $query->whereBetween('age', [$min, $max]);
            } elseif (strpos($edad, ',') !== false) {
                $vals = array_map('intval', array_filter(array_map('trim', explode(',', $edad))));
                $query->whereIn('age', $vals);
            } elseif (is_numeric($edad)) {
                $query->where('age', (int) $edad);
            }
        }

        // Cause filter: try numeric id or match by name
        if ($request->filled('causa')) {
            $val = $request->input('causa');
            if (is_numeric($val)) {
                $query->where('death_cause_id', (int) $val);
            } else {
                $query->whereHas('deathCause', function ($qc) use ($val) {
                    $qc->whereRaw('LOWER(name) = ?', [strtolower($val)])
                       ->orWhere('name', 'like', "%{$val}%");
                });
            }
        }

        // Allowed sorts
        $allowedSorts = [
            'death_date_desc' => ['death_date', 'desc'],
            'death_date_asc' => ['death_date', 'asc'],
            'age_asc' => ['age', 'asc'],
            'age_desc' => ['age', 'desc'],
            'name_asc' => ['name', 'asc'],
            'name_desc' => ['name', 'desc'],
            'id_desc' => ['id', 'desc'],
            'id_asc' => ['id', 'asc'],
        ];

        $orderBy = $allowedSorts[$sort] ?? $allowedSorts['death_date_desc'];

        $deaths = $query->with(['deathCause', 'deathMunicipality', 'jurisdiction', 'deathLocation']);

        // Apply primary order
        $deaths = $deaths->orderBy($orderBy[0], $orderBy[1]);

        // Add deterministic tiebreaker: if primary order is not by id, also order by id desc
        if ($orderBy[0] !== 'id') {
            $deaths = $deaths->orderBy('id', 'desc');
        }

        $deaths = $deaths->paginate($perPage)->withQueryString();

        // Lookup data used by filters (small lists)
        $causes = DeathCause::all();
        $jurisdictions = Jurisdiction::all();
        $municipalities = Municipality::all();

        return view('estadisticas.datos', compact('deaths', 'causes', 'jurisdictions', 'municipalities'));
    }

    /**
     * Alias used by some routes: forward to index
     */
    public function datos(Request $request)
    {
        return $this->index($request);
    }

    public function create()
    {
        // Show the form for creating a new death record
        $causes = DeathCause::all();
        $jurisdictions = Jurisdiction::all();
        $municipalities = Municipality::all();
        $locations = DeathLocation::all();

        return view('estadisticas.acciones.registro', compact('causes', 'jurisdictions', 'municipalities', 'locations'));
    }

    /**
     * Store a newly created death record.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'gov_folio' => ['required','string','max:191','unique:deaths,gov_folio'],
            'name' => ['required','string','max:191'],
            'first_last_name' => ['required','string','max:191'],
            'second_last_name' => ['nullable','string','max:191'],
            // Accept either the legacy 'age' or the new composite fields
            'age' => ['nullable','integer','min:0','max:150'],
            'edad_valor' => ['nullable','integer','min:0','max:150'],
            'edad_unidad' => ['nullable','string','in:anos,meses'],
            'sex' => ['required','in:masculino,femenino,hombre,mujer,M,F,male,female'],
            'residence_municipality_id' => ['nullable','integer','exists:municipalities,id'],
            'death_municipality_id' => ['nullable','integer','exists:municipalities,id'],
            'jurisdiction_id' => ['nullable','integer','exists:jurisdictions,id'],
            'death_location_id' => ['nullable','integer','exists:death_locations,id'],
            'death_cause_id' => ['required','integer','exists:death_causes,id'],
            'death_date' => ['required','date'],
        ]);

        // Normalize sex values to short form if possible
        $sex = strtolower($data['sex']);
        if (in_array($sex, ['masculino','hombre','m'])) $data['sex'] = 'M';
        elseif (in_array($sex, ['femenino','mujer','f'])) $data['sex'] = 'F';
        else $data['sex'] = strtoupper(substr($sex,0,1));

        // Determine age_years/age_months from composite inputs if provided
        $ageYears = null;
        $ageMonths = null;
        $ageForLegacy = $data['age'] ?? null;

        if (!empty($data['edad_valor']) && !empty($data['edad_unidad'])) {
            $valor = (int) $data['edad_valor'];
            $unidad = $data['edad_unidad'];
            if ($unidad === 'meses') {
                $ageYears = 0;
                $ageMonths = $valor;
                $ageForLegacy = 0;
            } else {
                // 'anos'
                $ageYears = $valor;
                $ageMonths = null;
                $ageForLegacy = $valor;
            }
        } else {
            // Fallback: use legacy 'age' field if present
            if (!is_null($ageForLegacy)) {
                $ageYears = (int) $ageForLegacy;
                $ageMonths = null;
            }
        }

        // For manual registration: do not accept 12 or more months — ask user to use years
        if (!is_null($ageMonths) && $ageMonths >= 12) {
            return Redirect::back()->withInput()->withErrors(['edad_valor' => 'Si la unidad es "meses", el valor debe ser menor a 12; para 12 o más use años.']);
        }
        // Determine jurisdiction: derive from residence municipality when possible
        // If not found, use explicit `jurisdiction_id` from the form; otherwise assign a default 'NO ENCONTRADA'
        $jurisdictionId = null;
        if (!empty($data['residence_municipality_id'])) {
            $resMun = Municipality::find($data['residence_municipality_id']);
            if ($resMun && $resMun->jurisdiction_id) $jurisdictionId = $resMun->jurisdiction_id;
        }
        if (is_null($jurisdictionId) && !empty($data['jurisdiction_id'])) {
            $jurisdictionId = $data['jurisdiction_id'];
        }
        if (is_null($jurisdictionId)) {
            $defaultJur = Jurisdiction::firstOrCreate(['name' => 'NO ENCONTRADA']);
            $jurisdictionId = $defaultJur->id;
        }

        // Create record
        $death = Death::create([
            'gov_folio' => $data['gov_folio'],
            'name' => $data['name'],
            'first_last_name' => $data['first_last_name'],
            'second_last_name' => $data['second_last_name'] ?? null,
            'age' => $ageForLegacy ?? null,
            'age_years' => $ageYears,
            'age_months' => $ageMonths,
            'sex' => $data['sex'],
            'death_date' => $data['death_date'],
            'residence_municipality_id' => $data['residence_municipality_id'] ?? null,
            'death_municipality_id' => $data['death_municipality_id'] ?? null,
            'jurisdiction_id' => $jurisdictionId,
            'death_location_id' => $data['death_location_id'] ?? null,
            'death_cause_id' => $data['death_cause_id'],
        ]);

        return Redirect::route('statistic.data')->with('success', 'Registro de defunción creado correctamente.');
    }

    /**
     * Update the specified death record.
     */
    public function update(Request $request, Death $death)
    {
        $data = $request->validate([
            'gov_folio' => ['required','string','max:191','unique:deaths,gov_folio,' . $death->id],
            'name' => ['required','string','max:191'],
            'first_last_name' => ['required','string','max:191'],
            'second_last_name' => ['nullable','string','max:191'],
            // Accept either the legacy 'age' or the new composite fields
            'age' => ['nullable','integer','min:0','max:150'],
            'edad_valor' => ['nullable','integer','min:0','max:150'],
            'edad_unidad' => ['nullable','string','in:anos,meses'],
            'sex' => ['required','in:M,F,masculino,femenino,hombre,mujer,m,f'],
            'residence_municipality_id' => ['nullable','integer','exists:municipalities,id'],
            'death_municipality_id' => ['nullable','integer','exists:municipalities,id'],
            'jurisdiction_id' => ['nullable','integer','exists:jurisdictions,id'],
            'death_location_id' => ['nullable','integer','exists:death_locations,id'],
            'death_cause_id' => ['required','integer','exists:death_causes,id'],
            'death_date' => ['required','date'],
        ]);

        // Normalize sex values to short form if possible
        $sex = strtolower($data['sex']);
        if (in_array($sex, ['masculino','hombre','m'])) $data['sex'] = 'M';
        elseif (in_array($sex, ['femenino','mujer','f'])) $data['sex'] = 'F';
        else $data['sex'] = strtoupper(substr($sex,0,1));

        // Determine age_years/age_months from composite inputs if provided
        $ageYears = null;
        $ageMonths = null;
        $ageForLegacy = $data['age'] ?? null;

        if (!empty($data['edad_valor']) && !empty($data['edad_unidad'])) {
            $valor = (int) $data['edad_valor'];
            $unidad = $data['edad_unidad'];
            if ($unidad === 'meses') {
                $ageYears = 0;
                $ageMonths = $valor;
                $ageForLegacy = 0;
            } else {
                $ageYears = $valor;
                $ageMonths = null;
                $ageForLegacy = $valor;
            }
        } else {
            if (!is_null($ageForLegacy)) {
                $ageYears = (int) $ageForLegacy;
                $ageMonths = null;
            }
        }

        // For manual update: do not accept 12 or more months — ask user to use years
        if (!is_null($ageMonths) && $ageMonths >= 12) {
            return Redirect::back()->withInput()->withErrors(['edad_valor' => 'Si la unidad es "meses", el valor debe ser menor a 12; para 12 o más use años.']);
        }
        // Determine jurisdiction: derive from residence municipality when possible
        // If not found, use explicit `jurisdiction_id` from the form; otherwise assign a default 'NO ENCONTRADA'
        $jurisdictionId = null;
        if (!empty($data['residence_municipality_id'])) {
            $resMun = Municipality::find($data['residence_municipality_id']);
            if ($resMun && $resMun->jurisdiction_id) $jurisdictionId = $resMun->jurisdiction_id;
        }
        if (is_null($jurisdictionId) && !empty($data['jurisdiction_id'])) {
            $jurisdictionId = $data['jurisdiction_id'];
        }
        if (is_null($jurisdictionId)) {
            $defaultJur = Jurisdiction::firstOrCreate(['name' => 'NO ENCONTRADA']);
            $jurisdictionId = $defaultJur->id;
        }

        // Update record
        $death->update([
            'gov_folio' => $data['gov_folio'],
            'name' => $data['name'],
            'first_last_name' => $data['first_last_name'],
            'second_last_name' => $data['second_last_name'] ?? null,
            'age' => $ageForLegacy ?? null,
            'age_years' => $ageYears,
            'age_months' => $ageMonths,
            'sex' => $data['sex'],
            'death_date' => $data['death_date'],
            'residence_municipality_id' => $data['residence_municipality_id'] ?? null,
            'death_municipality_id' => $data['death_municipality_id'] ?? null,
            'jurisdiction_id' => $jurisdictionId,
            'death_location_id' => $data['death_location_id'] ?? null,
            'death_cause_id' => $data['death_cause_id'],
        ]);

        return Redirect::route('statistic.data')->with('success', 'Registro de defunción actualizado correctamente.');
    }

    public function edit(Death $death)
    {
        // Show the form for editing an existing death record
        $causes = DeathCause::all();
        $jurisdictions = Jurisdiction::all();
        $municipalities = Municipality::all();
        $locations = DeathLocation::all();

        // The update view expects a variable named $defuncion (existing views use Spanish variable)
        $defuncion = $death;
        return view('estadisticas.acciones.actualizar-registro', compact('defuncion', 'causes', 'jurisdictions', 'municipalities', 'locations'));
    }

    public function show(Death $death)
    {
        // Show details of a specific death record
        return view('estadisticas.show', compact('death'));
    }

    public function destroy(Request $request, Death $death)
    {
        // Delete the specified death record
        $death->delete();

        return Redirect::route('statistic.data')->with('success', 'Registro de defunción eliminado correctamente.');
    }

}
