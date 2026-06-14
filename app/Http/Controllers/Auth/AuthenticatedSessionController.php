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

        $fallback = $user->hasAnyRole(['Administrador', 'Coordinador'])
            ? route('statistic.data', absolute: false)
            : route('reportes.index', absolute: false);

        $intended = $request->session()->pull('url.intended');

        return $this->isNavigableIntendedUrl($request, $intended)
            ? redirect()->to($intended)
            : redirect()->to($fallback);
    }

    /**
     * Prevent AJAX/JSON endpoints from becoming the post-login destination.
     */
    private function isNavigableIntendedUrl(Request $request, mixed $intended): bool
    {
        if (! is_string($intended) || $intended === '') {
            return false;
        }

        $parts = parse_url($intended);

        if ($parts === false) {
            return false;
        }

        if (isset($parts['host']) && $parts['host'] !== $request->getHost()) {
            return false;
        }

        $path = '/'.ltrim($parts['path'] ?? '/', '/');

        foreach (['/api/', '/notificaciones', '/login', '/logout'] as $blockedPath) {
            if ($path === rtrim($blockedPath, '/') || str_starts_with($path, $blockedPath)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $origin = $request->headers->get('Origin');

        if ($origin && ! $this->isSameOrigin($request, $origin)) {
            abort(403);
        }

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

    private function isSameOrigin(Request $request, string $origin): bool
    {
        $parts = parse_url($origin);

        if ($parts === false || ! isset($parts['host'])) {
            return false;
        }

        $originPort = $parts['port'] ?? (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80);
        $requestPort = $request->getPort();

        return $parts['host'] === $request->getHost()
            && $originPort === $requestPort;
    }
}
