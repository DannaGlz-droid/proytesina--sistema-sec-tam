<?php

test('individual user deletion has one confirmation owner', function () {
    $resources = dirname(__DIR__, 2) . '/resources/views/usuarios';
    $actions = file_get_contents($resources . '/partials/table-actions.blade.php');
    $management = file_get_contents($resources . '/gestion-de-usuarios.blade.php');

    expect($actions)
        ->toContain('class="js-delete-user-form"')
        ->not->toContain('data-confirm-delete-form');

    expect($management)
        ->toContain("$(document)\n                .off('submit.usersDelete', '.js-delete-user-form')")
        ->toContain('closeUserActionMenus();')
        ->toContain("form.dataset.deleteDialogOpen = 'true'")
        ->toContain("delete form.dataset.deleteDialogOpen");
});
