<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Death;
use App\Models\DeathCause;
use App\Models\Municipality;
use App\Models\District;
use App\Models\DeathLocation;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeathController extends Controller
{
    /**
     * DataTables server-side endpoint for AJAX requests.
     */
    public function dataTable(Request $request)
    {
        // DataTables parameters
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 25);
        $searchValue = $request->input('search.value', '');
        
        // Order parameters
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        // Column mapping (same order as table columns shown to server-side, NOT including the client-side checkbox column)
        // Note: DataTables on the client will include a leading checkbox column (index 0). When DataTables sends an order column index,
        // we need to map it to our $columns array. If the requested column is 0 (the checkbox), default to 'id'. Otherwise subtract 1.
        $columns = ['gov_folio', 'name', 'first_last_name', 'second_last_name', 'age', 'sex', 'death_date', 'residence_municipality_id', 'death_municipality_id', 'district_id', 'death_location_id', 'death_cause_id'];
        if ($orderColumnIndex == 0) {
            $orderColumn = 'id';
        } else {
            $mappedIndex = $orderColumnIndex - 1;
            $orderColumn = $columns[$mappedIndex] ?? 'id';
        }
        
        // Build query
        $query = Death::query();
        
        // Apply search
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('gov_folio', 'like', "%{$searchValue}%")
                  ->orWhere('name', 'like', "%{$searchValue}%")
                  ->orWhere('first_last_name', 'like', "%{$searchValue}%")
                  ->orWhere('second_last_name', 'like', "%{$searchValue}%");
                if (is_numeric($searchValue)) {
                    $q->orWhere('age', (int) $searchValue);
                }
            });
        }
        
        // Apply existing filters from form (same as index method)
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('jurisdiccion')) {
            $val = $request->input('jurisdiccion');
            $query->whereHas('district', function ($qj) use ($val) {
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
        
        if ($request->filled('sexo')) {
            $val = $request->input('sexo');
            $query->whereRaw('LOWER(sex) = ?', [strtolower($val)]);
        }
        
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
        
        // Get total count
        $recordsTotal = Death::count();
        $recordsFiltered = $query->count();
        
        // Apply ordering and pagination
        $deaths = $query->with(['deathCause', 'deathMunicipality', 'residenceMunicipality', 'district', 'deathLocation']);
        
        // Special handling for age ordering: calculate total days
        if ($orderColumn === 'age') {
            $deaths->orderByRaw("(COALESCE(age_years, 0) * 365 + COALESCE(age_months, 0) * 30 + COALESCE(age_days, 0)) {$orderDir}");
        } else {
            $deaths->orderBy($orderColumn, $orderDir);
        }
        
        $deaths = $deaths
                        ->skip($start)
                        ->take($length)
                        ->get();
        
        // Format data for DataTables. Include `id` so the client can build checkboxes for bulk operations.
        $data = $deaths->map(function ($death) {
            // Calcular edad total en días para ordenamiento numérico
            $ageDaysTotal = 0;
            if ($death->age_years) {
                $ageDaysTotal += $death->age_years * 365;
            }
            if ($death->age_months) {
                $ageDaysTotal += $death->age_months * 30;
            }
            if ($death->age_days) {
                $ageDaysTotal += $death->age_days;
            }
            
            return [
                'id' => $death->id,
                'gov_folio' => $this->displayValue($death->gov_folio),
                'name' => $this->displayValue($death->name_formatted),
                'first_last_name' => $this->displayValue($death->first_last_name_formatted),
                'second_last_name' => $this->displayValue($death->second_last_name_formatted),
                'age' => $ageDaysTotal,  // Valor numérico en días para ordenamiento
                'age_display' => $death->pretty_age ?? '—',  // Valor formateado para mostrar
                'sex' => $this->displaySex($death->sex),
                'death_date' => $death->death_date ? $death->death_date->format('d/m/Y') : '—',
                'residence_municipality' => $this->displayTitleText(optional($death->residenceMunicipality)->name),
                'death_municipality' => $this->displayTitleText(optional($death->deathMunicipality)->name),
                'district' => $this->displayDistrict(optional($death->district)->name),
                'death_location' => $this->displayTitleText(optional($death->deathLocation)->name),
                'death_cause' => $this->displayCause(optional($death->deathCause)->name),
                'actions' => view('estadisticas.partials.table-actions', compact('death'))->render(),
            ];
        });

        // End format
        
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    //
    public function index(Request $request)
    {
        // Validate and normalize incoming filter/pagination params
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'in:25,50,100,250'],
            'sort' => ['nullable', 'string'],
            'dateRange' => ['nullable', 'string'],
            'year' => ['nullable', 'string', 'max:255'],
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

        $this->applyDateFilters($query, $request);

        // Jurisdiction / Municipality (residence) / death municipality filters
        if ($request->filled('jurisdiccion')) {
            $val = $request->input('jurisdiccion');
            $query->whereHas('district', function ($qj) use ($val) {
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

        $deaths = $query->with(['deathCause', 'deathMunicipality', 'district', 'deathLocation']);

        // Apply primary order
        $deaths = $deaths->orderBy($orderBy[0], $orderBy[1]);

        // Add deterministic tiebreaker: if primary order is not by id, also order by id desc
        if ($orderBy[0] !== 'id') {
            $deaths = $deaths->orderBy('id', 'desc');
        }

        $deaths = $deaths->paginate($perPage)->withQueryString();

        // Lookup data used by filters (small lists)
        $causes = DeathCause::allowedCatalog();
        $districts = District::statisticsCatalog();
        $municipalities = Municipality::all();

        // Get count of unresolved import failures
        $unresolvedFailures = DB::table('import_failures')
            ->where('is_resolved', false)
            ->count();

        return view('estadisticas.datos', compact('deaths', 'causes', 'districts', 'municipalities', 'unresolvedFailures'));
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
        $causes = DeathCause::allowedCatalog();
        $districts = District::statisticsCatalog();
        $municipalities = Municipality::all();
        $locations = DeathLocation::all();

        return view('estadisticas.acciones.registro', compact('causes', 'districts', 'municipalities', 'locations'));
    }

    /**
     * Store a newly created death record.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // gov_folio: accept 9-digit folios and the alphanumeric defunction format.
            'gov_folio' => ['required','string','regex:/^(?:[0-9]{9}|[0-9]{2}[A-Za-z][0-9]{5}[A-Za-z][0-9]{8})$/','unique:deaths,gov_folio'],
            'name' => ['required','string','max:191'],
            'first_last_name' => ['required','string','max:191'],
            'second_last_name' => ['nullable','string','max:191'],
            // Accept either the legacy 'age' or the new composite fields
            'age' => ['nullable','integer','min:0','max:150'],
            'edad_valor' => ['required','integer','min:0','max:150'],
            'edad_unidad' => ['required','string','in:anos,meses,dias'],
            'sex' => ['required','in:masculino,femenino,hombre,mujer,M,F,male,female'],
            'residence_municipality_id' => ['required','integer',function($attribute, $value, $fail) {
                if ($value != 0 && !\DB::table('municipalities')->where('id', $value)->exists()) {
                    $fail('El municipio seleccionado no es válido.');
                }
            }],
            'death_municipality_id' => ['required','integer',function($attribute, $value, $fail) {
                if ($value != 0 && !\DB::table('municipalities')->where('id', $value)->exists()) {
                    $fail('El municipio seleccionado no es válido.');
                }
            }],
            'district_id' => $this->allowedStatisticsDistrictValidationRules(),
            'death_location_id' => ['required','integer','exists:death_locations,id'],
            'death_cause_id' => $this->allowedDeathCauseValidationRules(),
            'death_date' => ['required','date','before_or_equal:today'],
        ]);

        // Normalize sex values to short form if possible
        $sex = strtolower($data['sex']);
        if (in_array($sex, ['masculino','hombre','m'])) $data['sex'] = 'M';
        elseif (in_array($sex, ['femenino','mujer','f'])) $data['sex'] = 'F';
        else $data['sex'] = strtoupper(substr($sex,0,1));

        // Convert "No encontrado" (id=0) to NULL for foreign keys
        if ($data['residence_municipality_id'] == 0) {
            $data['residence_municipality_id'] = null;
        }
        if ($data['death_municipality_id'] == 0) {
            $data['death_municipality_id'] = null;
        }

        // Determine age_years/age_months from composite inputs if provided
        $ageYears = null;
        $ageMonths = null;
        $ageDays = null;
        $ageForLegacy = $data['age'] ?? null;

        if (!empty($data['edad_valor']) && !empty($data['edad_unidad'])) {
            $valor = (int) $data['edad_valor'];
            $unidad = $data['edad_unidad'];
            if ($unidad === 'meses') {
                $ageYears = 0;
                $ageMonths = $valor;
                $ageForLegacy = 0;
            } elseif ($unidad === 'dias') {
                $ageYears = 0;
                $ageMonths = 0;
                $ageDays = $valor;
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

        // For manual registration: days must be between 0 and 30
        if (!is_null($ageDays)) {
            if ($ageDays < 0) {
                return Redirect::back()->withInput()->withErrors(['edad_valor' => 'Si la unidad es "días", el valor debe ser mayor o igual a 0.']);
            }
            if ($ageDays > 30) {
                return Redirect::back()->withInput()->withErrors(['edad_valor' => 'Si la unidad es "días", el valor debe ser menor o igual a 30.']);
            }
        }
        // Determine jurisdiction: derive from residence municipality when possible
        // If not found, use explicit `district_id` from the form; otherwise assign OTRO.
        $districtId = null;
        if (!empty($data['residence_municipality_id'])) {
            $resMun = Municipality::find($data['residence_municipality_id']);
            if ($resMun && $resMun->district_id) $districtId = $resMun->district_id;
        }
        if (is_null($districtId) && !empty($data['district_id'])) {
            $districtId = $data['district_id'];
        }
        if (is_null($districtId)) {
            $defaultJur = District::firstOrCreate(['name' => District::OTHER_NAME]);
            $districtId = $defaultJur->id;
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
            'age_days' => $ageDays ?? null,
            'sex' => $data['sex'],
            'death_date' => $data['death_date'],
            'residence_municipality_id' => $data['residence_municipality_id'] ?? null,
            'death_municipality_id' => $data['death_municipality_id'] ?? null,
            'district_id' => $districtId,
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
            // gov_folio: accept 9-digit folios and the alphanumeric defunction format.
            'gov_folio' => ['required','string','regex:/^(?:[0-9]{9}|[0-9]{2}[A-Za-z][0-9]{5}[A-Za-z][0-9]{8})$/','unique:deaths,gov_folio,' . $death->id],
            'name' => ['required','string','max:191'],
            'first_last_name' => ['required','string','max:191'],
            'second_last_name' => ['nullable','string','max:191'],
            // Accept either the legacy 'age' or the new composite fields
            'age' => ['nullable','integer','min:0','max:150'],
            'edad_valor' => ['required','integer','min:0','max:150'],
            'edad_unidad' => ['required','string','in:anos,meses,dias'],
            'sex' => ['required','in:M,F,masculino,femenino,hombre,mujer,m,f'],
            'residence_municipality_id' => ['required','integer',function($attribute, $value, $fail) {
                if ($value != 0 && !\DB::table('municipalities')->where('id', $value)->exists()) {
                    $fail('El municipio seleccionado no es válido.');
                }
            }],
            'death_municipality_id' => ['required','integer',function($attribute, $value, $fail) {
                if ($value != 0 && !\DB::table('municipalities')->where('id', $value)->exists()) {
                    $fail('El municipio seleccionado no es válido.');
                }
            }],
            'district_id' => $this->allowedStatisticsDistrictValidationRules(),
            'death_location_id' => ['required','integer','exists:death_locations,id'],
            'death_cause_id' => $this->allowedDeathCauseValidationRules(),
            'death_date' => ['required','date','before_or_equal:today'],
        ]);

        // Normalize sex values to short form if possible
        $sex = strtolower($data['sex']);
        if (in_array($sex, ['masculino','hombre','m'])) $data['sex'] = 'M';
        elseif (in_array($sex, ['femenino','mujer','f'])) $data['sex'] = 'F';
        else $data['sex'] = strtoupper(substr($sex,0,1));

        // Convert "No encontrado" (id=0) to NULL for foreign keys
        if ($data['residence_municipality_id'] == 0) {
            $data['residence_municipality_id'] = null;
        }
        if ($data['death_municipality_id'] == 0) {
            $data['death_municipality_id'] = null;
        }

        // Determine age_years/age_months from composite inputs if provided
        $ageYears = null;
        $ageMonths = null;
        $ageDays = null;
        $ageForLegacy = $data['age'] ?? null;

        if (!empty($data['edad_valor']) && !empty($data['edad_unidad'])) {
            $valor = (int) $data['edad_valor'];
            $unidad = $data['edad_unidad'];
            if ($unidad === 'meses') {
                $ageYears = 0;
                $ageMonths = $valor;
                $ageForLegacy = 0;
            } elseif ($unidad === 'dias') {
                $ageYears = 0;
                $ageMonths = 0;
                $ageDays = $valor;
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

        // For manual update: days must be between 0 and 30
        if (!is_null($ageDays)) {
            if ($ageDays < 0) {
                return Redirect::back()->withInput()->withErrors(['edad_valor' => 'Si la unidad es "días", el valor debe ser mayor o igual a 0.']);
            }
            if ($ageDays > 30) {
                return Redirect::back()->withInput()->withErrors(['edad_valor' => 'Si la unidad es "días", el valor debe ser menor o igual a 30.']);
            }
        }
        // Determine jurisdiction: derive from residence municipality when possible
        // If not found, use explicit `district_id` from the form; otherwise assign OTRO.
        $districtId = null;
        if (!empty($data['residence_municipality_id'])) {
            $resMun = Municipality::find($data['residence_municipality_id']);
            if ($resMun && $resMun->district_id) $districtId = $resMun->district_id;
        }
        if (is_null($districtId) && !empty($data['district_id'])) {
            $districtId = $data['district_id'];
        }
        if (is_null($districtId)) {
            $defaultJur = District::firstOrCreate(['name' => District::OTHER_NAME]);
            $districtId = $defaultJur->id;
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
            'age_days' => $ageDays ?? null,
            'sex' => $data['sex'],
            'death_date' => $data['death_date'],
            'residence_municipality_id' => $data['residence_municipality_id'] ?? null,
            'death_municipality_id' => $data['death_municipality_id'] ?? null,
            'district_id' => $districtId,
            'death_location_id' => $data['death_location_id'] ?? null,
            'death_cause_id' => $data['death_cause_id'],
        ]);

        return Redirect::route('statistic.data')->with('success', 'Registro de defunción actualizado correctamente.');
    }

    public function edit(Death $death)
    {
        // Show the form for editing an existing death record
        $causes = DeathCause::allowedCatalog();
        $districts = District::statisticsCatalog();
        $municipalities = Municipality::all();
        $locations = DeathLocation::all();

        // The update view expects a variable named $defuncion (existing views use Spanish variable)
        $defuncion = $death;
        return view('estadisticas.acciones.actualizar-registro', compact('defuncion', 'causes', 'districts', 'municipalities', 'locations'));
    }

    private function allowedDeathCauseValidationRules(): array
    {
        return [
            'required',
            'integer',
            function ($attribute, $value, $fail) {
                $isAllowed = DeathCause::query()
                    ->whereKey($value)
                    ->whereIn('name', DeathCause::allowedNames())
                    ->exists();

                if (!$isAllowed) {
                    $fail('La causa seleccionada no es válida.');
                }
            },
        ];
    }

    private function allowedStatisticsDistrictValidationRules(): array
    {
        return [
            'nullable',
            'integer',
            function ($attribute, $value, $fail) {
                if ($value === null || $value === '') {
                    return;
                }

                $allowedIds = District::statisticsCatalog()->pluck('id')->map(fn ($id) => (int) $id)->all();

                if (!in_array((int) $value, $allowedIds, true)) {
                    $fail('El distrito seleccionado no es válido.');
                }
            },
        ];
    }

    private function applyDateFilters($query, Request $request): void
    {
        $dateRange = $request->input('dateRange');

        if (!$dateRange || $dateRange === 'all') {
            return;
        }

        if (is_numeric($dateRange)) {
            $query->whereDate('death_date', '>=', now()->subDays((int) $dateRange));
            return;
        }

        if ($dateRange === 'custom') {
            if ($request->filled('startDate')) {
                $query->whereDate('death_date', '>=', $request->input('startDate'));
            }

            if ($request->filled('endDate')) {
                $query->whereDate('death_date', '<=', $request->input('endDate'));
            }

            return;
        }

        $years = $this->parseYears($request->input('year'));
        if (empty($years)) {
            $years = [now()->year];
        }

        if (in_array($dateRange, ['year', 'years'], true)) {
            $this->applyYearFilter($query, $years);
            return;
        }

        if (in_array($dateRange, ['month', 'months', 'multiple-months'], true)) {
            $months = $this->parseMonths($request->input('selectedMonths', []), $request->input('month'));
            if (!empty($months)) {
                $this->applyYearFilter($query, $years);
                $query->whereIn(DB::raw('MONTH(death_date)'), $months);
            }

            return;
        }

        if ($dateRange === 'quarter') {
            $quarter = (int) $request->input('quarter');
            if ($quarter >= 1 && $quarter <= 4) {
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $startMonth + 2;
                $this->applyYearFilter($query, $years);
                $query->whereBetween(DB::raw('MONTH(death_date)'), [$startMonth, $endMonth]);
            }
        }
    }

    private function parseYears($value): array
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $currentYear = now()->year;
        $years = [];
        $parts = preg_split('/[,\s]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part) {
            $part = trim($part);

            if (preg_match('/^(\d{4})-(\d{4})$/', $part, $matches)) {
                $start = (int) $matches[1];
                $end = (int) $matches[2];

                if ($start > $end) {
                    [$start, $end] = [$end, $start];
                }

                $years = array_merge($years, range($start, $end));
                continue;
            }

            if (preg_match('/^\d{4}$/', $part)) {
                $years[] = (int) $part;
            }
        }

        return collect($years)
            ->filter(fn ($year) => $year >= 1950 && $year <= $currentYear)
            ->unique()
            ->values()
            ->all();
    }

    private function parseMonths($selectedMonths, $singleMonth = null): array
    {
        $months = is_array($selectedMonths) ? $selectedMonths : [$selectedMonths];

        if (empty(array_filter($months)) && $singleMonth) {
            $months = [$singleMonth];
        }

        return collect($months)
            ->map(fn ($month) => (int) $month)
            ->filter(fn ($month) => $month >= 1 && $month <= 12)
            ->unique()
            ->values()
            ->all();
    }

    private function applyYearFilter($query, array $years): void
    {
        if (count($years) === 1) {
            $query->whereYear('death_date', $years[0]);
            return;
        }

        $query->whereIn(DB::raw('YEAR(death_date)'), $years);
    }

    private function displayValue($value): string
    {
        $value = is_string($value) ? trim($value) : $value;

        return ($value === null || $value === '') ? '—' : (string) $value;
    }

    private function displayTitleText($value): string
    {
        $value = $this->displayValue($value);

        return $value === '—' ? $value : Death::formatPersonName($value);
    }

    private function displayDistrict($value): string
    {
        $value = $this->displayValue($value);

        if ($value === '—') {
            return $value;
        }

        $normalized = mb_strtoupper($value, 'UTF-8');

        if (preg_match('/^([IVXLCDM]+)\s*-\s*(.+)$/u', $normalized, $matches)) {
            return $matches[1] . ' - ' . $matches[2];
        }

        return Death::formatPersonName($value);
    }

    private function displayCause($value): string
    {
        $formatted = $this->displayTitleText($value);

        if ($formatted === '—') {
            return $formatted;
        }

        foreach (['Imss', 'Issste', 'Sedena', 'Insabi'] as $acronym) {
            $formatted = preg_replace('/\b' . preg_quote($acronym, '/') . '\b/u', mb_strtoupper($acronym, 'UTF-8'), $formatted);
        }

        return $formatted;
    }

    private function displaySex($value): string
    {
        $value = $this->displayValue($value);

        if ($value === '—') {
            return $value;
        }

        return match (mb_strtoupper($value, 'UTF-8')) {
            'M', 'MASCULINO', 'HOMBRE' => 'M',
            'F', 'FEMENINO', 'MUJER' => 'F',
            default => Death::formatPersonName($value),
        };
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

    /**
     * Mass delete deaths by ids (AJAX)
     */
    public function massDelete(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer']
        ]);

        $ids = $data['ids'];

        try {
            DB::beginTransaction();
            $deleted = Death::whereIn('id', $ids)->delete();
            DB::commit();

            return response()->json(['ok' => true, 'deleted' => $deleted]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('massDelete deaths error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Error al eliminar registros de defunción'], 500);
        }
    }

}
