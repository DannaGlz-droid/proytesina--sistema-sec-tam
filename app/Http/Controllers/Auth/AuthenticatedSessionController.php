<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Reset last_session to NULL when user logs in (to indicate they're online)
        $user = Auth::user();
        $user->update(['last_session' => null]);
        
        if ($user->hasAnyRole(['Administrador', 'Coordinador'])) {
            return redirect()->intended(route('statistic.data', absolute: false));
        }
        
        // Operador e Invitado van a reportes
        return redirect()->intended(route('reportes.index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Actualizar last_session ANTES de hacer logout
        $user = Auth::user();
        if ($user) {
            $user->update(['last_session' => now()]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // After logout, redirect to the login page
        return redirect()->route('login');
    }
}
