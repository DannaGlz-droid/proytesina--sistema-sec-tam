<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Position;
use App\Models\Jurisdiction;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    //

    public function index(Request $request)
    {
        // Validar y normalizar parámetros de entrada: búsqueda, paginación y filtros
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'in:10,20,50,100'],
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

    $perPage = isset($validated['per_page']) ? (int) $validated['per_page'] : 10;
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
        $jurisdictions = Jurisdiction::all();
        $roles = Role::all();

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
        $jurisdictions = Jurisdiction::all();
        $roles = Role::all();

        return view('usuarios.acciones.actualizar-registro', compact('user', 'positions', 'jurisdictions', 'roles'));
      }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : false;

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
        $user->delete();

        return redirect()->route('user.user-gestion')->with('success', 'User deleted successfully.');
    }
}
