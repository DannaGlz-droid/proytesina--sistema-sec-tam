<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <a href="<?php echo e(route('user.index')); ?>">Back to User List</a>

    <form method="POST" action="<?php echo e(route('user.update', $user->id)); ?>">
        <?php echo method_field('PUT'); ?>
        <?php echo csrf_field(); ?>
        <label for="" name="name">nombre</label>
        <input type="text" name="name" value="<?php echo e($user->name); ?>">

        <label for="" name="first_last_name">apellido paterno</label>
        <input type="text" name="first_last_name" value="<?php echo e($user->first_last_name); ?>">

        <label for="" name="second_last_name">apellido materno</label>
        <input type="text" name="second_last_name" value="<?php echo e($user->second_last_name); ?>">

        <label for="" name="email">email</label>
        <input type="email" name="email" value="<?php echo e($user->email); ?>">

        <label for="" name="phone">phone</label>
        <input type="text" name="phone" value="<?php echo e($user->phone); ?>">

        <label for="" name="username">username</label>
        <input type="text" name="username" value="<?php echo e($user->username); ?>">

        <label for="" name="password">password</label>
        <input type="password" name="password">

        <label for="" name="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation">

    <label for="" name="is_active">Active</label>
    <input type="checkbox" name="is_active" value="1" <?php echo e($user->is_active ? 'checked' : ''); ?>>

        <label for="" name="registration_date">registration_date</label>
        <input type="date" name="registration_date" value="<?php echo e($user->registration_date); ?>">

        <label for="" name="last_session">last session</label>
        <input type="date" name="last_session" value="<?php echo e($user->last_session); ?>">

        <label for="" name="position">Position</label>
        <select name="position_id">
            <option value="">-- Seleccionar --</option>
            <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($position->id); ?>" <?php echo e($user->position_id == $position->id ? 'selected' : ''); ?>><?php echo e($position->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['position_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-red-600"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <label for="" name="jurisdiction">Jurisdiction</label>
        <select name="jurisdiction_id">
            <option value="">-- Seleccionar --</option>
            <?php $__currentLoopData = $jurisdictions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jurisdiction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($jurisdiction->id); ?>" <?php echo e($user->jurisdiction_id == $jurisdiction->id ? 'selected' : ''); ?>><?php echo e($jurisdiction->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['jurisdiction_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-red-600"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <label for="" name="role">Role</label>
        <select name="role_id">
            <option value="">-- Seleccionar --</option>
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($role->id); ?>" <?php echo e($user->role_id == $role->id ? 'selected' : ''); ?>><?php echo e($role->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-red-600"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <input type="submit" value="Update">
    </form>
</body>
</html><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views\usuarios\edit.blade.php ENDPATH**/ ?>