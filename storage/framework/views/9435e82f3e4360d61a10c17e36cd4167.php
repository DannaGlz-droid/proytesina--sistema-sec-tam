<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Iniciar sesión</title>
  </head>
  <body>
    <h1>Iniciar sesión</h1>

    <?php if($errors->any()): ?>
      <div style="color: red;">
        <ul>
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if(session('status')): ?>
      <div style="color: green;"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('login')); ?>">
      <?php echo csrf_field(); ?>

      <div>
        <label for="login">Usuario o correo</label>
        <input id="login" type="text" name="login" value="<?php echo e(old('login')); ?>" required autofocus placeholder="Usuario o correo">
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
      <?php if(Route::has('password.request')): ?>
        <a href="<?php echo e(route('password.request')); ?>">¿Olvidaste tu contraseña?</a>
      <?php endif; ?>
    </p>
  </body>
</html>
<?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/auth/login.blade.php ENDPATH**/ ?>