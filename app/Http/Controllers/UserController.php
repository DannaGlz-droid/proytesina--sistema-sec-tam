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

    public function index()
    {
        $users = User::all();

        // Use the management view as the canonical listing for users
        return view('usuarios.gestion-de-usuarios', compact('users'));
    }


    /**
     * Show the gestion de usuarios page.
     */
    public function gestion()
    {
        // return all users (most recent first). For large datasets consider paginate().
        $users = User::orderBy('id', 'desc')->get();

        return view('usuarios.gestion-de-usuarios', compact('users'));
    }


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
