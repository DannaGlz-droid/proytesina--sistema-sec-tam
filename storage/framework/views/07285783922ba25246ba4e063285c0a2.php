<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <h1><?php echo e($user->name); ?></h1>

    <p>Apellido Paterno: <?php echo e($user->first_last_name); ?></p>
    <p>Apellido Materno: <?php echo e($user->second_last_name); ?></p>
    <p>Email: <?php echo e($user->email); ?></p>
    <p>Teléfono: <?php echo e($user->phone); ?></p>
    <p>Username: <?php echo e($user->username); ?></p>
    <p>Activo: <?php echo e($user->is_active ? 'Sí' : 'No'); ?></p>
    <p>Fecha de Registro: <?php echo e($user->registration_date); ?></p>
    <p>Última Sesión: <?php echo e($user->last_session); ?></p>
    <p>Posición: <?php echo e($user->position->name ?? 'N/A'); ?></p>
    <p>Jurisdicción: <?php echo e($user->jurisdiction->name ?? 'N/A'); ?></p>
    <p>Rol: <?php echo e($user->role->name ?? 'N/A'); ?></p>
</body>
</html>
<?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views\usuarios\show.blade.php ENDPATH**/ ?>