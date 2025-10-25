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
            'name' => ['required','string','max:191'],
            'first_last_name' => ['required','string','max:191'],
            'second_last_name' => ['nullable','string','max:191'],
            'age' => ['nullable','integer','min:0','max:150'],
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

        // Create record
        $death = Death::create([
            'name' => $data['name'],
            'first_last_name' => $data['first_last_name'],
            'second_last_name' => $data['second_last_name'] ?? null,
            'age' => $data['age'] ?? null,
            'sex' => $data['sex'],
            'death_date' => $data['death_date'],
            'residence_municipality_id' => $data['residence_municipality_id'] ?? null,
            'death_municipality_id' => $data['death_municipality_id'] ?? null,
            'jurisdiction_id' => $data['jurisdiction_id'] ?? null,
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
            'name' => ['required','string','max:191'],
            'first_last_name' => ['required','string','max:191'],
            'second_last_name' => ['nullable','string','max:191'],
            'age' => ['nullable','integer','min:0','max:150'],
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

        // Update record
        $death->update([
            'name' => $data['name'],
            'first_last_name' => $data['first_last_name'],
            'second_last_name' => $data['second_last_name'] ?? null,
            'age' => $data['age'] ?? null,
            'sex' => $data['sex'],
            'death_date' => $data['death_date'],
            'residence_municipality_id' => $data['residence_municipality_id'] ?? null,
            'death_municipality_id' => $data['death_municipality_id'] ?? null,
            'jurisdiction_id' => $data['jurisdiction_id'] ?? null,
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
