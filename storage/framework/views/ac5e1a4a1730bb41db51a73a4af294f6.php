<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <h1>Hola desde la vista de usuarios</h1>

    <h2>Lista de usuarios geniales ♥</h2>

    <a href="<?php echo e(route('user.create')); ?>">Create new user</a>
   
    <?php if($users->isEmpty()): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">No hay usuarios disponibles.</div>
        <?php else: ?>
            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Usuario</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nombre completo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Teléfono</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Rol</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Cargo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Jurisdicción</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Activo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Alta</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Última sesión</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo e($user->id); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo e($user->username ?? '—'); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo e($user->name ?? '—'); ?> <?php echo e($user->first_last_name ?? ''); ?> <?php echo e($user->second_last_name ?? ''); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo e($user->email ?? '—'); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo e($user->phone ?? '—'); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo e(optional($user->role)->name ?? '—'); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo e(optional($user->position)->name ?? '—'); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700"><?php echo e(optional($user->jurisdiction)->name ?? '—'); ?></td>
                                <td class="px-4 py-2 text-sm"><?php if($user->is_active): ?> <span class="text-green-600 font-semibold">Sí</span> <?php else: ?> <span class="text-red-600">No</span> <?php endif; ?></td>
                                <td class="px-4 py-2 text-sm text-gray-700">
                                    <?php
                                        $reg = $user->registration_date;
                                        if ($reg instanceof \DateTimeInterface) {
                                            echo $reg->format('d/m/Y');
                                        } elseif (!empty($reg)) {
                                            try { echo \Carbon\Carbon::parse($reg)->format('d/m/Y'); } catch (\Throwable $e) { echo '—'; }
                                        } else { echo '—'; }
                                    ?>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700">
                                    <?php
                                        $last = $user->last_session;
                                        if ($last instanceof \DateTimeInterface) {
                                            echo $last->format('d/m/Y H:i');
                                        } elseif (!empty($last)) {
                                            try { echo \Carbon\Carbon::parse($last)->format('d/m/Y H:i'); } catch (\Throwable $e) { echo '—'; }
                                        } else { echo '—'; }
                                    ?>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
  <div class="flex items-center justify-end space-x-1">
    <a href="<?php echo e(route('user.edit', $user->id)); ?>"
       class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200"
       title="Editar">
      <i class="fas fa-edit text-xs"></i>
    </a>

    <a href="<?php echo e(route('user.show', $user->id)); ?>"
       class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200"
       title="Ver">
      <i class="fas fa-eye text-xs"></i>
    </a>

    <form action="<?php echo e(route('user.destroy', $user->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <input type="submit" value="DELETE"
               class="w-7 h-7 flex items-center justify-center rounded border border-red-600 text-red-600 hover:bg-red-600 hover:text-white transition-all duration-200"
               title="Eliminar"
               onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
    </form>
  </div>
</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <p class="mt-4 text-sm text-gray-600">Total usuarios: <?php echo e($users->count()); ?></p>

    <p>¡Gracias por visitar nuestra página de usuarios!</p>
</body>
</html><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views\usuarios\index.blade.php ENDPATH**/ ?>