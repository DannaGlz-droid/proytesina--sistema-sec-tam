<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión</title>
  </head>
  <body>
    <h1>Iniciar sesión</h1>

    @if ($errors->any())
      <div style="color: red;">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if (session('status'))
      <div style="color: green;">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div>
        <label for="email">Correo electrónico</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
      </div>

      <div>
        <label for="password">Contraseña</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">
      </div>

      <div>
        <label>
          <input type="checkbox" name="remember"> Recordarme
        </label>
      </div>

      <div>
        <button type="submit">Iniciar sesión</button>
      </div>
    </form>

    <p>
      @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
      @endif
    </p>
  </body>
</html>
