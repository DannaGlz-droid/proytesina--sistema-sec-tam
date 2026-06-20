<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Iniciar sesión | Sistema SEC-TAM</title>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
      html,
      body {
        margin: 0;
        min-height: 100%;
      }

      .login-page {
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

      .login-card {
        width: min(480px, calc(100vw - 32px));
        background: #ffffff;
        border-radius: 22px;
        box-shadow: 0 26px 60px rgba(0, 0, 0, 0.24);
        padding: 38px 38px 34px;
        box-sizing: border-box;
      }

      .login-card > section {
        padding: 0;
      }

      .login-header {
        margin-bottom: 30px;
      }

      .login-logos {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        margin-bottom: 34px;
      }

      .login-logo-slot {
        width: 136px;
        height: 46px;
        display: flex;
        align-items: center;
      }

      .login-logo-slot:first-child {
        justify-content: flex-end;
      }

      .login-logo-slot:last-child {
        justify-content: flex-start;
      }

      .login-logo-slot img {
        display: block;
        max-width: 100%;
        max-height: 38px;
        object-fit: contain;
      }

      .login-logo-divider {
        width: 1px;
        height: 38px;
        background: #bc955c;
        flex: 0 0 auto;
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

      .login-input {
        width: 100%;
        box-sizing: border-box;
        min-height: 46px;
      }

      .login-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      .login-field label {
        margin-bottom: 8px;
      }

      .login-password-heading {
        margin-bottom: 8px;
      }

      .login-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: 2px;
        margin-bottom: 2px;
      }

      .login-submit {
        min-height: 46px;
        margin-top: 2px;
      }

      @media (max-width: 420px) {
        .login-card {
          padding: 30px 24px 28px;
        }

        .login-logo-slot {
          width: 118px;
        }
      }
    </style>
  </head>
  <body class="min-h-screen bg-[#611132] text-[#404041] font-sans">
    <main class="login-page relative min-h-screen flex items-center justify-center overflow-hidden px-4 py-8 lg:px-8">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(188,149,92,0.34)_0,_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(255,255,255,0.16)_0,_transparent_28%)]"></div>
      <div class="absolute inset-x-0 top-0 h-1 bg-[#bc955c]"></div>

      <div class="login-card relative w-full max-w-md overflow-hidden rounded-2xl border border-white/20 bg-white shadow-2xl">
        <section class="bg-white p-6 sm:p-8">
          <div class="login-logos mb-8 flex items-center justify-center gap-4">
            <div class="login-logo-slot flex h-14 w-36 items-center justify-end">
              <img src="<?php echo e(asset('images/tam_logo.png')); ?>" alt="Tamaulipas Gobierno del Estado" class="max-h-12 max-w-full object-contain">
            </div>
            <div class="login-logo-divider h-12 w-px bg-[#bc955c]"></div>
            <div class="login-logo-slot flex h-14 w-36 items-center justify-start">
              <img src="<?php echo e(asset('images/logo-secretaria.png')); ?>" alt="Secretaría de Salud" class="max-h-12 max-w-full object-contain">
            </div>
          </div>

          <div class="mx-auto max-w-md">
            <div class="login-header mb-8">
              <p class="mb-3 text-xs font-bold uppercase tracking-[0.28em] text-[#611132]">Acceso seguro</p>
              <h2 class="font-lora text-3xl font-bold text-[#404041]">Iniciar sesión</h2>
              <p class="mt-2 text-sm text-gray-600">
                Ingresa con tu usuario o correo institucional.
              </p>
            </div>

            <?php if(session('status')): ?>
              <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <?php echo e(session('status')); ?>

              </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
              <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="mb-1 font-semibold">No fue posible iniciar sesión.</p>
                <ul class="list-inside list-disc space-y-1">
                  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
              </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="login-form space-y-5">
              <?php echo csrf_field(); ?>

              <div class="login-field">
                <label for="login" class="mb-2 block text-sm font-semibold text-[#404041]">Usuario o correo</label>
                <input
                  id="login"
                  type="text"
                  name="login"
                  value="<?php echo e(old('login')); ?>"
                  required
                  autofocus
                  autocomplete="username"
                  maxlength="320"
                  autocapitalize="none"
                  spellcheck="false"
                  placeholder="usuario@institucion.gob.mx"
                  class="login-input w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition focus:border-[#611132] focus:bg-white focus:ring-[#611132]"
                >
              </div>

              <div class="login-field">
                <div class="login-password-heading mb-2 flex items-center justify-between">
                  <label for="password" class="block text-sm font-semibold text-[#404041]">Contraseña</label>
                  <?php if(Route::has('password.request')): ?>
                    <a href="<?php echo e(route('password.request')); ?>" class="text-xs font-semibold text-[#611132] hover:underline">
                      ¿Olvidaste tu contraseña?
                    </a>
                  <?php endif; ?>
                </div>

                <div class="relative">
                  <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••••••"
                    class="login-input w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm shadow-sm transition focus:border-[#611132] focus:bg-white focus:ring-[#611132]"
                  >
                  <button
                    type="button"
                    onclick="togglePasswordVisibility()"
                    class="absolute inset-y-0 right-0 px-4 text-gray-500 hover:text-[#611132]"
                    aria-label="Mostrar u ocultar contraseña"
                  >
                    <svg id="password-eye" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                  </button>
                </div>
              </div>

              <div class="login-options flex items-center justify-between gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                  <input type="checkbox" name="remember" value="1" <?php echo e(old('remember') ? 'checked' : ''); ?> class="rounded border-gray-300 text-[#611132] focus:ring-[#611132]">
                  Recordarme
                </label>

                <span class="text-xs text-gray-500">Sesión protegida</span>
              </div>

              <button
                type="submit"
                class="login-submit w-full rounded-xl bg-[#611132] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-[#611132]/20 transition hover:bg-[#4a0e26] focus:outline-none focus:ring-2 focus:ring-[#611132] focus:ring-offset-2"
              >
                Iniciar sesión
              </button>
            </form>

            <div class="privacy-notice">
              <strong>Aviso de privacidad</strong>
              El uso de este sistema es de carácter institucional y confidencial. El aviso de privacidad del correo electrónico institucional del Gobierno del Estado de Tamaulipas está disponible en el siguiente
              <a href="https://www.tamaulipas.gob.mx/aviso-de-privacidad/" target="_blank" rel="noopener noreferrer">enlace</a>.
            </div>
          </div>
        </section>
      </div>
    </main>

    <script>
      function togglePasswordVisibility() {
        const password = document.getElementById('password');
        password.type = password.type === 'password' ? 'text' : 'password';
      }
    </script>
  </body>
</html>
<?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/auth/login.blade.php ENDPATH**/ ?>