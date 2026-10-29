<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Acceso institucional al sistema de la Secretaría de Salud.">
    <link rel="icon" type="image/png" href="{{ asset('images/tam_pag_logoF.png') }}">
    <title>Iniciar sesión | Secretaría de Salud</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  </head>
  <body class="login-body">
    <main class="login-page">
      <div class="login-page-accent" aria-hidden="true"></div>

      <article class="login-card" aria-labelledby="login-title">
        <div class="login-logos" aria-label="Gobierno del Estado de Tamaulipas y Secretaría de Salud">
          <div class="login-logo-slot">
            <img src="{{ asset('images/tam_logo.png') }}" alt="Tamaulipas Gobierno del Estado">
          </div>
          <div class="login-logo-divider" aria-hidden="true"></div>
          <div class="login-logo-slot">
            <img src="{{ asset('images/logo-secretaria.png') }}" alt="Secretaría de Salud">
          </div>
        </div>

        <header class="login-header">
          <p class="login-eyebrow">Acceso seguro</p>
          <h1 id="login-title">Iniciar sesión</h1>
          <p>Ingresa con tu usuario o correo electrónico.</p>
        </header>

        @if (session('status'))
          <div class="login-notice login-notice--success" role="status">
            <i class="fas fa-circle-check" aria-hidden="true"></i>
            <span>{{ session('status') }}</span>
          </div>
        @endif

        <form
          method="POST"
          action="{{ route('login') }}"
          class="login-form"
          novalidate
          data-ui-inline-validation
        >
          @csrf

          @error('credentials')
            <div class="login-notice login-notice--error" role="alert" data-login-auth-error data-server-error>
              <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 7.75v5.5"></path>
                <path d="M12 16.75h.01"></path>
              </svg>
              <span>{{ $message }}</span>
            </div>
          @enderror

          <div class="login-field">
            <label for="login">Usuario o correo</label>
            <input
              id="login"
              type="text"
              name="login"
              value="{{ old('login') }}"
              required
              autofocus
              autocomplete="username"
              maxlength="320"
              autocapitalize="none"
              autocorrect="off"
              spellcheck="false"
              enterkeyhint="next"
              placeholder="usuario@institucion.gob.mx"
              data-validation-required-message="Capture su usuario o correo."
              @error('login') aria-invalid="true" aria-describedby="login-server-error" @enderror
            >
            @error('login')
              <p id="login-server-error" class="login-field-error" role="alert" data-server-error>{{ $message }}</p>
            @enderror
          </div>

          <div class="login-field">
            <div class="login-password-heading">
              <label for="password">Contraseña</label>
              @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
              @endif
            </div>

            <div class="login-password-input app-password-input-wrap">
              <input
                id="password"
                type="password"
                name="password"
                required
                maxlength="255"
                autocomplete="current-password"
                enterkeyhint="go"
                placeholder="Ingrese su contraseña"
                data-validation-required-message="Capture su contraseña."
                @error('password') aria-invalid="true" aria-describedby="password-server-error" @enderror
              >
              <button
                type="button"
                class="app-password-visibility login-password-visibility"
                data-login-password-toggle
                aria-label="Mostrar contraseña"
                title="Mostrar contraseña"
              >
                <svg data-login-eye-open viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M2.75 12s3.5-6 9.25-6 9.25 6 9.25 6-3.5 6-9.25 6-9.25-6-9.25-6Z"></path>
                  <circle cx="12" cy="12" r="2.75"></circle>
                </svg>
                <svg data-login-eye-closed viewBox="0 0 24 24" fill="none" aria-hidden="true" hidden>
                  <path d="M2.75 12s3.5-6 9.25-6 9.25 6 9.25 6-3.5 6-9.25 6-9.25-6-9.25-6Z"></path>
                  <circle cx="12" cy="12" r="2.75"></circle>
                  <path d="M4 4 20 20"></path>
                </svg>
              </button>
            </div>
            @error('password')
              <p id="password-server-error" class="login-field-error" role="alert" data-server-error>{{ $message }}</p>
            @enderror
          </div>

          <div class="login-options">
            <label class="login-remember">
              <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
              <span class="login-remember-control" aria-hidden="true">
                <svg viewBox="0 0 16 16" fill="none">
                  <path d="m3.25 8.25 3 3 6.5-6.5"></path>
                </svg>
              </span>
              <span>Recordarme</span>
            </label>
          </div>

          <button
            type="submit"
            class="login-submit"
            data-ui-submit-button
            data-loading-text="Iniciando sesión…"
          >
            <span class="login-submit-spinner" data-ui-submit-spinner hidden aria-hidden="true"></span>
            <span data-ui-submit-label>Iniciar sesión</span>
          </button>
        </form>

        <footer class="privacy-notice">
          <strong>Uso y confidencialidad</strong>
          <p>
            El acceso y la información de este sistema son de uso institucional y confidencial. Su utilización y difusión se limitan a fines oficiales.
          </p>
        </footer>
      </article>
    </main>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('.login-form');
        const toggle = document.querySelector('[data-login-password-toggle]');
        const password = document.getElementById('password');
        let authenticationError = document.querySelector('[data-login-auth-error]');

        const clearAuthenticationError = function () {
          if (!authenticationError) return;
          authenticationError.remove();
          authenticationError = null;
        };

        form?.addEventListener('submit', clearAuthenticationError);
        form?.querySelectorAll('#login, #password').forEach(function (field) {
          field.addEventListener('input', clearAuthenticationError);
        });

        toggle?.addEventListener('click', function () {
          const willShow = password.type === 'password';
          password.type = willShow ? 'text' : 'password';

          const label = willShow ? 'Ocultar contraseña' : 'Mostrar contraseña';
          toggle.setAttribute('aria-label', label);
          toggle.setAttribute('title', label);
          toggle.querySelector('[data-login-eye-open]').hidden = willShow;
          toggle.querySelector('[data-login-eye-closed]').hidden = !willShow;
          password.focus({ preventScroll: true });
        });
      });
    </script>
  </body>
</html>
