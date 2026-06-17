<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recuperar contraseña | Sistema SEC-TAM</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
      html,
      body {
        margin: 0;
        min-height: 100%;
      }

      .auth-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 16px;
        background:
          radial-gradient(circle at top left, rgba(188, 149, 92, 0.34) 0, transparent 34%),
          radial-gradient(circle at bottom right, rgba(255, 255, 255, 0.16) 0, transparent 28%),
          #611132;
        box-sizing: border-box;
      }

      .auth-card {
        width: min(480px, calc(100vw - 32px));
        background: #ffffff;
        border-radius: 22px;
        box-shadow: 0 26px 60px rgba(0, 0, 0, 0.24);
        padding: 38px 38px 34px;
        box-sizing: border-box;
      }

      .auth-card > section {
        padding: 0;
      }

      .auth-logos {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        margin-bottom: 34px;
      }

      .auth-logo-slot {
        width: 136px;
        height: 46px;
        display: flex;
        align-items: center;
      }

      .auth-logo-slot:first-child {
        justify-content: flex-end;
      }

      .auth-logo-slot:last-child {
        justify-content: flex-start;
      }

      .auth-logo-slot img {
        display: block;
        max-width: 100%;
        max-height: 38px;
        object-fit: contain;
      }

      .auth-logo-divider {
        width: 1px;
        height: 38px;
        background: #bc955c;
        flex: 0 0 auto;
      }

      .auth-header {
        margin-bottom: 30px;
      }

      .auth-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      .auth-field label {
        margin-bottom: 8px;
      }

      .auth-input {
        width: 100%;
        min-height: 46px;
        box-sizing: border-box;
      }

      .auth-submit {
        min-height: 46px;
        margin-top: 2px;
      }

      .privacy-notice {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 10px;
        line-height: 1.5;
      }

      .privacy-notice strong {
        display: block;
        margin-bottom: 4px;
        color: #611132;
        font-size: 10px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
      }

      .privacy-notice a {
        color: #611132;
        font-weight: 700;
        text-decoration: underline;
      }

      @media (max-width: 420px) {
        .auth-card {
          padding: 30px 24px 28px;
        }

        .auth-logo-slot {
          width: 118px;
        }
      }
    </style>
  </head>
  <body class="min-h-screen bg-[#611132] text-[#404041] font-sans">
    <main class="auth-page relative min-h-screen flex items-center justify-center overflow-hidden px-4 py-8 lg:px-8">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(188,149,92,0.34)_0,_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(255,255,255,0.16)_0,_transparent_28%)]"></div>
      <div class="absolute inset-x-0 top-0 h-1 bg-[#bc955c]"></div>

      <div class="auth-card relative w-full max-w-md overflow-hidden rounded-2xl border border-white/20 bg-white shadow-2xl">
        <section class="bg-white p-6 sm:p-8">
          <div class="auth-logos mb-8 flex items-center justify-center gap-4">
            <div class="auth-logo-slot flex h-14 w-36 items-center justify-end">
              <img src="{{ asset('images/tam_logo.png') }}" alt="Tamaulipas Gobierno del Estado" class="max-h-12 max-w-full object-contain">
            </div>
            <div class="auth-logo-divider h-12 w-px bg-[#bc955c]"></div>
            <div class="auth-logo-slot flex h-14 w-36 items-center justify-start">
              <img src="{{ asset('images/logo-secretaria.png') }}" alt="Secretaría de Salud" class="max-h-12 max-w-full object-contain">
            </div>
          </div>

          <div class="mx-auto max-w-md">
            <div class="auth-header mb-8">
              <p class="mb-3 text-xs font-bold uppercase tracking-[0.28em] text-[#611132]">Recuperación de acceso</p>
              <h2 class="font-lora text-3xl font-bold text-[#404041]">Restablecer contraseña</h2>
              <p class="mt-2 text-sm text-gray-600">
                Ingresa tu correo institucional. Si corresponde a una cuenta activa, recibirás un enlace para crear una nueva contraseña.
              </p>
            </div>

            @if (session('status'))
              <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
              </div>
            @endif

            @if ($errors->any())
              <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="mb-1 font-semibold">No fue posible enviar el enlace.</p>
                <ul class="list-inside list-disc space-y-1">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="auth-form space-y-5">
              @csrf

              <div class="auth-field">
                <label for="email" class="mb-2 block text-sm font-semibold text-[#404041]">Correo institucional</label>
                <input
                  id="email"
                  type="email"
                  name="email"
                  value="{{ old('email') }}"
                  required
                  autofocus
                  autocomplete="email"
                  maxlength="320"
                  placeholder="usuario@institucion.gob.mx"
                  class="auth-input w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition focus:border-[#611132] focus:bg-white focus:ring-[#611132]"
                >
              </div>

              <button
                type="submit"
                class="auth-submit w-full rounded-xl bg-[#611132] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-[#611132]/20 transition hover:bg-[#4a0e26] focus:outline-none focus:ring-2 focus:ring-[#611132] focus:ring-offset-2"
              >
                Enviar enlace de recuperación
              </button>
            </form>

            <div class="mt-5 text-center">
              <a href="{{ route('login') }}" class="text-xs font-semibold text-[#611132] hover:underline">
                Volver a iniciar sesión
              </a>
            </div>

            <div class="mt-8 rounded-lg border border-[#bc955c]/40 bg-[#fffaf2] px-4 py-3">
              <p class="text-xs leading-5 text-gray-700">
                Por seguridad, el sistema no confirma si el correo existe o no.
              </p>
            </div>

            <div class="privacy-notice">
              <strong>Aviso de privacidad</strong>
              El uso de este sistema es de carácter institucional y confidencial. El aviso de privacidad del correo electrónico institucional del Gobierno del Estado de Tamaulipas está disponible en el siguiente
              <a href="https://www.tamaulipas.gob.mx/aviso-de-privacidad/" target="_blank" rel="noopener noreferrer">enlace</a>.
            </div>
          </div>
        </section>
      </div>
    </main>
  </body>
</html>
