<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado
     */
    public function show()
    {
        $user = auth()->user();
        
        // Construir nombre completo
        $givenName = trim($user->name ?? '');
        $firstLast = trim($user->first_last_name ?? '');
        $secondLast = trim($user->second_last_name ?? '');
        $fullName = trim(implode(' ', array_filter([$givenName, $firstLast, $secondLast])));
        
        return view('usuarios.miperfil', [
            'fullName' => $fullName
        ]);
    }

    /**
     * Subir o actualizar la foto de perfil
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120' // máx 5MB
        ]);

        $user = auth()->user();

        // Eliminar la foto antigua si existe
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Guardar la nueva foto
        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        // Actualizar el usuario
        $user->update(['profile_photo_path' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'La foto de perfil se actualizó correctamente.',
            'photo_url' => asset('storage/' . $path)
        ]);
    }

    /**
     * Eliminar la foto de perfil
     */
    public function deletePhoto()
    {
        $user = auth()->user();

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);

            return response()->json([
                'success' => true,
                'message' => 'La foto de perfil se eliminó correctamente.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No hay una foto de perfil para eliminar.'
        ], 404);
    }
}
