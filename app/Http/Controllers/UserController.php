<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Position;
use App\Models\District;
use App\Models\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UserRequest;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private function allowedPositions()
    {
        return Position::whereNotIn(DB::raw('LOWER(name)'), ['administrador', 'admin', 'no definido'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Search users by name (for AJAX autocomplete in filters)
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $query = User::query();

        if ($request->boolean('importers_only')) {
            $query->whereHas('role', function ($roleQuery) {
                $roleQuery->whereIn('name', ['Administrador', 'Coordinador']);
            });
        }

        if ($q) {
            $query->where(function ($searchQuery) use ($q) {
                $searchQuery->where('name', 'like', "%{$q}%")
                    ->orWhere('first_last_name', 'like', "%{$q}%")
                    ->orWhere('second_last_name', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $items = $query->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'first_last_name', 'second_last_name', 'username']);

        $items = $items->map(function ($user) {
            $fullName = trim($user->name . ' ' . $user->first_last_name . ' ' . ($user->second_last_name ?? ''));
            return [
                'id' => $user->id,
                'name' => $user->name,
                'full_name' => $fullName,
                'username' => $user->username,
                'display_name' => $user->username ? $fullName . ' (@' . $user->username . ')' : $fullName,
            ];
        });

        // Return results as array
        return response()->json($items->toArray());
    }

    

    /**
     * Mass delete users by ids (AJAX)
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
            // Delete related notifications first to avoid FK issues
            Notification::whereIn('recipient_user_id', $ids)->orWhereIn('sender_user_id', $ids)->delete();
            $deleted = User::whereIn('id', $ids)->delete();
            DB::commit();

            return response()->json(['ok' => true, 'deleted' => $deleted]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('massDelete users error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Error al eliminar usuarios'], 500);
        }
    }

    public function index(Request $request)
    {
        // Validar y normalizar parámetros de entrada: búsqueda, paginación y filtros
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'in:10,25,50,100'],
            'sort' => ['nullable', 'string'],
            'is_active' => ['nullable', 'in:0,1','numeric'],
            'last_session' => ['nullable', 'string'],
            'date_range' => ['nullable', 'string'],
            'date_from' => ['nullable','date'],
            'date_to' => ['nullable','date'],
            'position_id' => ['nullable','integer','exists:positions,id'],
            'district_id' => ['nullable','integer','exists:districts,id'],
            'role_id' => ['nullable','integer','exists:roles,id'],
        ]);

    $perPage = isset($validated['per_page']) ? (int) $validated['per_page'] : 25;
        $q = $validated['q'] ?? null;
    $sort = $validated['sort'] ?? $request->input('sort', 'registration_date_desc');

        // Construir la consulta base y aplicar filtros de búsqueda si existen
        $query = User::query();

        if ($q) {
            $query->where(function ($qry) use ($q) {
                $qry->where('id', $q)
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // is_active filter
        if ($request->filled('is_active')) {
            // allow values '0' or '1'
            $query->where('is_active', (int) $request->input('is_active'));
        }

        // role/position/jurisdiction filters (IDs)
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->input('position_id'));
        }
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->input('district_id'));
        }

        // last_session filter shortcuts: today, 7, 30, 90, never
        if ($request->filled('last_session')) {
            $ls = $request->input('last_session');
            if ($ls === 'today') {
                $query->whereDate('last_session', now()->toDateString());
            } elseif (is_numeric($ls)) {
                $days = (int) $ls;
                $query->where('last_session', '>=', now()->subDays($days));
            } elseif ($ls === 'never') {
                $query->whereNull('last_session')
                      ->whereNotExists(function ($subQuery) {
                          $subQuery->select(DB::raw(1))
                              ->from('sessions')
                              ->whereColumn('sessions.user_id', 'users.id')
                              ->where('last_activity', '>=', now()->subHours(24)->timestamp);
                      });
            }
        }

        // last_session filter shortcuts: today, 7, 30, 90, never
        if ($request->filled('last_session')) {
            $ls = $request->input('last_session');
            if ($ls === 'today') {
                $query->whereDate('last_session', now()->toDateString());
            } elseif (is_numeric($ls)) {
                $days = (int) $ls;
                $query->where('last_session', '>=', now()->subDays($days));
            } elseif ($ls === 'never') {
                $query->whereNull('last_session');
            }
        }

        // last_session filter shortcuts: today, 7, 30, 90, never
        if ($request->filled('last_session')) {
            $ls = $request->input('last_session');
            if ($ls === 'today') {
                $query->whereDate('last_session', now()->toDateString());
            } elseif (is_numeric($ls)) {
                $days = (int) $ls;
                $query->where('last_session', '>=', now()->subDays($days));
            } elseif ($ls === 'never') {
                $query->whereNull('last_session');
            }
        }

        // registration date range
        if ($request->filled('date_range') && $request->input('date_range') !== 'custom' && $request->input('date_range') !== 'all') {
            $dr = $request->input('date_range');
            $daysMap = [
                '7days' => 7,
                '30days' => 30,
                '90days' => 90,
                '6months' => 180,
                '1year' => 365,
            ];
            if (isset($daysMap[$dr])) {
                $days = $daysMap[$dr];
                $query->whereDate('registration_date', '>=', now()->subDays($days));
            }
        } elseif ($request->input('date_range') === 'custom') {
            if ($request->filled('date_from')) {
                $query->whereDate('registration_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('registration_date', '<=', $request->input('date_to'));
            }
        }

        // Aplica orden según parámetro sort (columna + dirección)
        $allowedSorts = [
            'registration_date_desc' => ['registration_date','desc'],
            'registration_date_asc' => ['registration_date','asc'],
            'username_asc' => ['username','asc'],
            'username_desc' => ['username','desc'],
            'name_asc' => ['name','asc'],
            'name_desc' => ['name','desc'],
            'id_desc' => ['id','desc'],
            'id_asc' => ['id','asc'],
        ];

        $orderBy = $allowedSorts[$sort] ?? $allowedSorts['registration_date_desc'];

        // Paginación: mantiene los parámetros de consulta en los enlaces
        $users = $query->with(['role','position','district'])
                       ->orderBy($orderBy[0], $orderBy[1])
                       ->paginate($perPage)
                       ->withQueryString();

        // lookup data for filters
        $positions = $this->allowedPositions();
        $districts = District::userAssignmentCatalog();
        $roles = Role::all();

        // Renderizar la vista con el paginador (helpers firstItem/lastItem/total disponibles)
        return view('usuarios.gestion-de-usuarios', compact('users','positions','districts','roles'));
    }

    /**
     * DataTables server-side endpoint for AJAX requests.
     */
    public function dataTable(Request $request)
    {
        // Debug: log incoming filter params to help diagnose client-side issues
        try {
            Log::debug('User datatable request inputs', $request->only(['last_session', 'search', 'q']));
        } catch (\Throwable $e) {
            // ignore logging errors
        }
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 25);

        $searchValue = $request->input('search')['value'] ?? $request->input('q') ?? null;

        $order = $request->input('order', []);
        $orderColIdx = isset($order[0]['column']) ? (int) $order[0]['column'] : 1;
        $orderDir = isset($order[0]['dir']) && in_array(strtolower($order[0]['dir']), ['asc','desc']) ? $order[0]['dir'] : 'desc';

        // Map DataTables column index to DB column name (details + checkbox columns first)
        $columnsMap = [
            0 => 'id', // details toggle
            1 => 'id', // checkbox
            2 => 'id',
            3 => 'username',
            4 => 'name',
            5 => 'first_last_name',
            6 => 'second_last_name',
            7 => 'email',
            8 => null,
            9 => 'position_id',
            10 => 'district_name',
            11 => 'registration_date',
            12 => 'role_id',
            13 => 'is_active',
            14 => 'last_session',
        ];

        $orderColumn = $columnsMap[$orderColIdx] ?? 'id';
        $orderColumn = $orderColumn ?: 'id';

        $query = User::query();

        // Apply search across several columns
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('id', 'like', "%{$searchValue}%")
                  ->orWhere('username', 'like', "%{$searchValue}%")
                  ->orWhere('name', 'like', "%{$searchValue}%")
                  ->orWhere('first_last_name', 'like', "%{$searchValue}%")
                  ->orWhere('second_last_name', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('phone', 'like', "%{$searchValue}%");
            });
        }

        // Apply basic filters if present
        if ($request->filled('is_active')) {
            $query->where('is_active', (int) $request->input('is_active'));
        }
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->input('position_id'));
        }
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->input('district_id'));
        }

        // last_session filter shortcuts: today, 7, 30, 90, never
        if ($request->filled('last_session')) {
            $ls = $request->input('last_session');
            if ($ls === 'today') {
                $query->whereDate('last_session', now()->toDateString());
            } elseif (is_numeric($ls)) {
                $days = (int) $ls;
                $query->whereNotNull('last_session')
                      ->whereDate('last_session', '>=', now()->subDays($days)->toDateString());
            } elseif ($ls === 'never') {
                $query->whereNull('last_session')
                      ->whereNotExists(function ($subQuery) {
                          $subQuery->select(DB::raw(1))
                              ->from('sessions')
                              ->whereColumn('sessions.user_id', 'users.id')
                              ->where('last_activity', '>=', now()->subHours(24)->timestamp);
                      });
            }
        }

        // registration date range
        if ($request->filled('date_range') && $request->input('date_range') !== 'custom' && $request->input('date_range') !== 'all') {
            $dr = $request->input('date_range');
            $daysMap = [
                '7days' => 7,
                '30days' => 30,
                '90days' => 90,
                '6months' => 180,
                '1year' => 365,
            ];
            if (isset($daysMap[$dr])) {
                $days = $daysMap[$dr];
                $query->whereDate('registration_date', '>=', now()->subDays($days)->toDateString());
            }
        } elseif ($request->input('date_range') === 'custom') {
            if ($request->filled('date_from')) {
                $query->whereDate('registration_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('registration_date', '<=', $request->input('date_to'));
            }
        }

        $recordsTotal = User::count();
        $recordsFiltered = $query->count();

        $query->with(['role', 'position', 'district']);

        if ($orderColumn === 'district_name') {
            $districtOrderCase = "
                CASE
                    WHEN order_districts.name LIKE 'I -%' OR order_districts.name LIKE 'I-%' THEN 1
                    WHEN order_districts.name LIKE 'II -%' OR order_districts.name LIKE 'II-%' THEN 2
                    WHEN order_districts.name LIKE 'III -%' OR order_districts.name LIKE 'III-%' THEN 3
                    WHEN order_districts.name LIKE 'IV -%' OR order_districts.name LIKE 'IV-%' THEN 4
                    WHEN order_districts.name LIKE 'V -%' OR order_districts.name LIKE 'V-%' THEN 5
                    WHEN order_districts.name LIKE 'VI -%' OR order_districts.name LIKE 'VI-%' THEN 6
                    WHEN order_districts.name LIKE 'VII -%' OR order_districts.name LIKE 'VII-%' THEN 7
                    WHEN order_districts.name LIKE 'VIII -%' OR order_districts.name LIKE 'VIII-%' THEN 8
                    WHEN order_districts.name LIKE 'IX -%' OR order_districts.name LIKE 'IX-%' THEN 9
                    WHEN order_districts.name LIKE 'X -%' OR order_districts.name LIKE 'X-%' THEN 10
                    WHEN order_districts.name LIKE 'XI -%' OR order_districts.name LIKE 'XI-%' THEN 11
                    WHEN order_districts.name LIKE 'XII -%' OR order_districts.name LIKE 'XII-%' THEN 12
                    ELSE 999
                END
            ";

            $query->leftJoin('districts as order_districts', 'order_districts.id', '=', 'users.district_id')
                ->select('users.*')
                ->orderByRaw("CASE WHEN ({$districtOrderCase}) = 999 THEN 1 ELSE 0 END ASC")
                ->orderByRaw("({$districtOrderCase}) " . strtoupper($orderDir))
                ->orderBy('order_districts.name')
                ->orderBy('users.username');
        } else {
            $query->orderBy($orderColumn, $orderDir);
        }

        $users = $query->skip($start)
                       ->take($length)
                       ->get();

        $data = $users->map(function ($user) {
            $roleName = optional($user->role)->name ?? '—';
            $roleLower = strtolower($roleName);
            if (in_array($roleLower, ['administrador', 'admin'])) {
                $roleClasses = 'bg-[#e0e7ff] text-[#3730a3]';
            } elseif (in_array($roleLower, ['coordinador'])) {
                $roleClasses = 'bg-[#dcfce7] text-[#166534]';
            } elseif (in_array($roleLower, ['operador'])) {
                $roleClasses = 'bg-[#fef3c7] text-[#92400e]';
            } elseif ($roleLower === 'invitado') {
                $roleClasses = 'bg-[#fee2e2] text-[#991b1b]';
            } elseif (in_array($roleLower, ['usuario', 'user'])) {
                $roleClasses = 'bg-[#f8f1f4] text-[#611132]';
            } else {
                $roleClasses = 'bg-slate-100 text-slate-700';
            }

            $isActive = (bool) $user->is_active;
            $statusText = $isActive ? 'Activo' : 'Inactivo';
            $statusDot = $isActive ? 'bg-emerald-500' : 'bg-rose-500';
            $hasActiveSession = DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('last_activity', '>=', now()->subHours(24)->timestamp)
                ->exists();

            if ($hasActiveSession) {
                $lastSession = '<span class="dt-session-badge dt-session-online" title="Sesión activa">En línea</span>';
            } elseif ($user->last_session) {
                try {
                    $lastSessionDate = $user->last_session instanceof \DateTimeInterface
                        ? \Carbon\Carbon::instance($user->last_session)
                        : \Carbon\Carbon::parse($user->last_session);
                    $diff = $lastSessionDate->diff(now());

                    if ($diff->y > 0) {
                        $shortDiff = $diff->y . ' a';
                    } elseif ($diff->m > 0) {
                        $shortDiff = $diff->m . ' m';
                    } elseif ($diff->d > 0) {
                        $shortDiff = $diff->d . ' d';
                    } elseif ($diff->h > 0) {
                        $shortDiff = $diff->h . ' h';
                    } elseif ($diff->i > 0) {
                        $shortDiff = $diff->i . ' min';
                    } else {
                        $shortDiff = 'ahora';
                    }

                    $lastSessionTitle = e('Última sesión: ' . $lastSessionDate->format('d/m/Y H:i'));
                    $lastSession = '<span class="dt-session-badge dt-session-away" title="' . $lastSessionTitle . '">Hace ' . $shortDiff . '</span>';
                } catch (\Throwable $e) {
                    $lastSession = '<span class="dt-session-badge dt-session-empty">—</span>';
                }
            } else {
                $lastSession = '<span class="dt-session-badge dt-session-empty" title="Sin sesiones registradas">Nunca</span>';
            }

            return [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'first_last_name' => $user->first_last_name,
                'second_last_name' => $user->second_last_name ?: '—',
                'email' => $user->email,
                'phone' => $user->phone ?: '—',
                'position' => optional($user->position)->name ?? '—',
                'district' => optional($user->district)->name ?? '—',
                'registration_date' => $user->formatted_registration_date ?? $user->registration_date,
                'role' => "<span class='inline-flex items-center justify-center px-3 py-1 rounded-full text-xs leading-none font-bold whitespace-nowrap {$roleClasses}'>{$roleName}</span>",
                'status' => "<div class='flex items-center gap-1 whitespace-nowrap'><span class='w-2 h-2 rounded-full {$statusDot}'></span><span class='text-xs'>{$statusText}</span></div>",
                'last_session' => $lastSession,
                'actions' => view('usuarios.partials.table-actions', compact('user'))->render(),
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }


    /**
     * Show the gestion de usuarios page.
     */
    /*
    public function gestion()
    {
        // Deprecated: previously returned all users (most recent first) without pagination.
        // The `index()` method now handles filtering, ordering and pagination and is used
        // by the active routes. Keep this here commented as a reference in case an
        // export endpoint or a different behaviour is required in the future.
        //
        // $users = User::orderBy('id', 'desc')->get();
        // return view('usuarios.gestion-de-usuarios', compact('users'));
    }
    */


    public function create()
    {

        $positions = $this->allowedPositions();
        $districts = District::userAssignmentCatalog();
        
        // Solo mostrar: Administrador, Operador, Coordinador (usar nombres canónicos en español)
        $roles = Role::whereIn('name', ['Administrador', 'Operador', 'Coordinador', 'administrador', 'operador', 'coordinador'])->get();

        // Render the registration view (controller provides lookup data)
        return view('usuarios.acciones.registro', compact('positions', 'districts', 'roles'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

         // Default new users to active unless explicitly unchecked
         $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

         // hash password (even if User has 'password' => 'hashed' cast)
         $data['password'] = Hash::make($data['password']);

         // Ensure registration_date is set: prefer submitted value, otherwise default to today
         if (empty($data['registration_date'])) {
             $data['registration_date'] = now()->toDateString();
         }

         User::create($data);

         // Redirect to the user index (management) route
         return redirect()->route('user.user-gestion')->with('success', 'Usuario creado.');
    }

    public function edit(User $user)
    {
        $positions = $this->allowedPositions();
        $districts = District::userAssignmentCatalog();
        
        // Solo mostrar: Administrador, Operador, Coordinador (usar nombres canónicos en español)
        $roles = Role::whereIn('name', ['Administrador', 'Operador', 'Coordinador', 'administrador', 'operador', 'coordinador'])->get();

        return view('usuarios.acciones.actualizar-registro', compact('user', 'positions', 'districts', 'roles'));
      }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        $passwordChanged = ! empty($data['password']);

        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if (! $user->is_active || $passwordChanged) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
            $user->forceFill(['remember_token' => null])->save();
        }

        return redirect()->route('user.user-gestion')->with('success', 'Usuario actualizado.');

    }

    public function show(User $user)
    {
        return view('usuarios.show', compact('user'));
    }

    /**
     * Show the password update form for a given user.
     * If no user is provided, use the currently authenticated user.
     */
    public function password(?User $user = null)
    {
        if (is_null($user)) {
            $user = auth()->user();
        }

        return view('usuarios.acciones.actualizar-contrasena', compact('user'));
    }

    /**
     * Update the password for a given user.
     */
    public function updatePassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($data['password']);
        $user->remember_token = null;
        $user->save();
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return redirect()->route('user.user-gestion')->with('success', 'Contraseña actualizada.');
    }

    public function destroy(Request $request, User $user)
    {
        // Remove related notifications that reference this user to avoid FK constraint errors.
        // We delete both notifications where the user is recipient or sender.
        Notification::where('recipient_user_id', $user->id)->orWhere('sender_user_id', $user->id)->delete();

        // Optionally: if you want to remove other dependent data (comments, sessions, etc.),
        // handle them here before deleting the user.

        $user->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Usuario eliminado.',
            ]);
        }

        return redirect()->route('user.user-gestion')->with('success', 'Usuario eliminado.');
    }
}
