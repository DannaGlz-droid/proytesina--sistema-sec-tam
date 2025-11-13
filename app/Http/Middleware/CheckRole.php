<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  - Lista de roles permitidos (Administrador, Coordinador, Operador, Invitado)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a esta sección.');
        }

        $user = auth()->user();

        // Verificar que el usuario tenga un rol asignado
        if (!$user->role) {
            return redirect()->route('login')
                ->with('error', 'No tienes un rol asignado. Contacta al administrador.');
        }

        // Verificar si el rol del usuario está en la lista de roles permitidos
        if (!in_array($user->role->name, $roles)) {
            return redirect()->back()
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}

