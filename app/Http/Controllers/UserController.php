<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Position;
use App\Models\Jurisdiction;
use App\Models\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    //

    

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
            'jurisdiction_id' => ['nullable','integer','exists:jurisdictions,id'],
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
        if ($request->filled('jurisdiction_id')) {
            $query->where('jurisdiction_id', $request->input('jurisdiction_id'));
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
        if ($request->filled('date_range') && $request->input('date_range') !== 'custom') {
            $dr = $request->input('date_range');
            if (is_numeric($dr)) {
                $days = (int) $dr;
                $query->whereDate('registration_date', '>=', now()->subDays($days));
            } elseif ($dr === 'year') {
                $query->whereYear('registration_date', now()->year);
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
        $users = $query->with(['role','position','jurisdiction'])
                       ->orderBy($orderBy[0], $orderBy[1])
                       ->paginate($perPage)
                       ->withQueryString();

        // lookup data for filters
        $positions = Position::all();
        $jurisdictions = Jurisdiction::all();
        $roles = Role::all();

        // Renderizar la vista con el paginador (helpers firstItem/lastItem/total disponibles)
        return view('usuarios.gestion-de-usuarios', compact('users','positions','jurisdictions','roles'));
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

        // Map DataTables column index to DB column name (account for checkbox column at index 0)
        $columnsMap = [
            0 => 'id', // checkbox
            1 => 'id',
            2 => 'username',
            3 => 'name',
            4 => 'first_last_name',
            5 => 'second_last_name',
            6 => 'email',
            7 => 'phone',
            8 => 'position_id',
            9 => 'jurisdiction_id',
            10 => 'registration_date',
            11 => 'role_id',
            12 => 'is_active',
            13 => 'last_session',
        ];

        $orderColumn = $columnsMap[$orderColIdx] ?? 'id';

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
        if ($request->filled('jurisdiction_id')) {
            $query->where('jurisdiction_id', $request->input('jurisdiction_id'));
        }

        $recordsTotal = User::count();
        $recordsFiltered = $query->count();

        $users = $query->with(['role', 'position', 'jurisdiction'])
                       ->orderBy($orderColumn, $orderDir)
                       ->skip($start)
                       ->take($length)
                       ->get();

        $data = $users->map(function ($user) {
            $roleName = optional($user->role)->name ?? '—';
            $roleLower = strtolower($roleName);
            if (in_array($roleLower, ['administrador', 'admin'])) {
                $roleClasses = 'bg-red-100 text-red-800';
            } elseif (in_array($roleLower, ['usuario', 'user'])) {
                $roleClasses = 'bg-green-100 text-green-800';
            } elseif ($roleLower === 'invitado') {
                $roleClasses = 'bg-gray-100 text-gray-800';
            } elseif ($roleLower === 'operador') {
                $roleClasses = 'bg-blue-100 text-blue-800';
            } else {
                $roleClasses = 'bg-yellow-100 text-yellow-800';
            }

            $isActive = (bool) $user->is_active;
            $statusText = $isActive ? 'Activo' : 'Inactivo';
            $statusDot = $isActive ? 'bg-emerald-500' : 'bg-rose-500';

            return [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'first_last_name' => $user->first_last_name,
                'second_last_name' => $user->second_last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'position' => optional($user->position)->name ?? '—',
                'jurisdiction' => optional($user->jurisdiction)->name ?? '—',
                'registration_date' => $user->formatted_registration_date ?? $user->registration_date,
                'role' => "<span class='{$roleClasses} text-xs font-medium px-2 py-0.5 rounded-full'>{$roleName}</span>",
                'status' => "<div class='flex items-center gap-1'><span class='w-2 h-2 rounded-full {$statusDot}'></span><span class='text-xs'>{$statusText}</span></div>",
                'last_session' => $user->last_session_diff ?? $user->last_session,
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

        $positions = Position::all();
        // Solo mostrar jurisdicciones numeradas (I - XII), excluir opciones genéricas
        $jurisdictions = Jurisdiction::whereNotIn('name', ['OTRO', 'Sin jurisdicción', 'NO ENCONTRADA', 'No encontrada', 'Otra', 'otra'])
                                      ->orderBy('name')
                                      ->get();
        // Solo mostrar: Administrador, Operador, Coordinador (usar nombres canónicos en español)
        $roles = Role::whereIn('name', ['Administrador', 'Operador', 'Coordinador', 'administrador', 'operador', 'coordinador'])->get();

        // Render the registration view (controller provides lookup data)
        return view('usuarios.acciones.registro', compact('positions', 'jurisdictions', 'roles'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

         // Default new users to active unless explicitly unchecked
         $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

         // hash password (even if User has 'password' => 'hashed' cast)
         $data['password'] = Hash::make($data['password']);

         // Convert position_id = 0 to NULL (No definido)
         if (isset($data['position_id']) && $data['position_id'] == 0) {
             $data['position_id'] = null;
         }

         // Ensure registration_date is set: prefer submitted value, otherwise default to today
         if (empty($data['registration_date'])) {
             $data['registration_date'] = now()->toDateString();
         }

         User::create($data);

         // Redirect to the user index (management) route
         return redirect()->route('user.user-gestion')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $positions = Position::all();
        // Solo mostrar jurisdicciones numeradas (I - XII), excluir opciones genéricas
        $jurisdictions = Jurisdiction::whereNotIn('name', ['OTRO', 'Sin jurisdicción', 'NO ENCONTRADA', 'No encontrada', 'Otra', 'otra'])
                                      ->orderBy('name')
                                      ->get();
        // Solo mostrar: Administrador, Operador, Coordinador (usar nombres canónicos en español)
        $roles = Role::whereIn('name', ['Administrador', 'Operador', 'Coordinador', 'administrador', 'operador', 'coordinador'])->get();

        return view('usuarios.acciones.actualizar-registro', compact('user', 'positions', 'jurisdictions', 'roles'));
      }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        // Convert position_id = 0 to NULL (No definido)
        if (isset($data['position_id']) && $data['position_id'] == 0) {
            $data['position_id'] = null;
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('user.user-gestion')->with('success', 'User updated successfully.');

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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect()->route('user.user-gestion')->with('success', 'Password updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        // Remove related notifications that reference this user to avoid FK constraint errors.
        // We delete both notifications where the user is recipient or sender.
        Notification::where('recipient_user_id', $user->id)->orWhere('sender_user_id', $user->id)->delete();

        // Optionally: if you want to remove other dependent data (comments, sessions, etc.),
        // handle them here before deleting the user.

        $user->delete();

        return redirect()->route('user.user-gestion')->with('success', 'User deleted successfully.');
    }
}
