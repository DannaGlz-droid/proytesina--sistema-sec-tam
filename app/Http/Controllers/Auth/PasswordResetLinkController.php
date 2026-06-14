<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = mb_strtolower(trim((string) $request->input('email')));
        $userIsActive = User::query()
            ->where('email', $email)
            ->where('is_active', true)
            ->exists();

        if ($userIsActive) {
            Password::sendResetLink(['email' => $email]);
        }

        return back()->with(
            'status',
            'Si el correo corresponde a una cuenta activa, recibirás un enlace para restablecer la contraseña.'
        );
    }
}
