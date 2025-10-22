<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Position;
use App\Models\Jurisdiction;

class UserController extends Controller
{
    //

    public function index()
    {
        $users = User::all();

        return view('usuarios.index', compact('users'));
    }

    /**
     * Show gestion de usuarios with the three test users.
     */
    public function gestion()
    {
        $usernames = ['decano123', 'sam123', 'castiel123'];
        $users = User::whereIn('username', $usernames)->get();

        return view('usuarios.gestion-de-usuarios', compact('users'));
    }

    public function create()
    {
        // Quick test helper: ensure reference rows exist
        $role = Role::firstOrCreate(['name' => 'Sin definir']);
        $position = Position::firstOrCreate(['name' => 'Sin definir']);
        $jurisdiction = Jurisdiction::firstOrCreate(['name' => 'Sin definir']);

        $user = new User();
        $user->name = 'Decano';
        $user->first_last_name = 'Winchester';
        $user->second_last_name = 'Garcia';
        $user->email = 'decano@example.com';
        $user->phone = '123-456-7890';
        $user->username = 'decano123';
        $user->password = 'securepassword'; // User model casts 'password' => 'hashed'
        $user->is_active = true;
        $user->registration_date = now();
        $user->last_session = now();
        $user->position_id = $position->id;
        $user->jurisdiction_id = $jurisdiction->id;
        $user->role_id = $role->id;
        $user->save();

        User::create([
            'name' => 'Samuel',
            'first_last_name' => 'Winchester',
            'second_last_name' => 'Garcia',
            'email' => 'sam@example.com',
            'phone' => '987-654-3210',
            'username' => 'sam123',
            'password' => Hash::make('securepassword'),
            'is_active' => true,
            'registration_date' => now(),
            'last_session' => now(),
            'position_id' => $position->id,
            'jurisdiction_id' => $jurisdiction->id,
            'role_id' => $role->id,
        ]);

        User::create([
            'name' => 'Castiel',
            'first_last_name' => 'Angel',
            'second_last_name' => 'Celestial',
            'email' => 'castiel@example.com',
            'phone' => '555-555-5555',
            'username' => 'castiel123',
            'password' => Hash::make('securepassword'),
            'is_active' => true,
            'registration_date' => now(),
            'last_session' => now(),
            'position_id' => $position->id,
            'jurisdiction_id' => $jurisdiction->id,
            'role_id' => $role->id,
        ]);

        return redirect()->route('user.index');
    }

}
