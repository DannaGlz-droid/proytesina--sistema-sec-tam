import { expect, test } from '@playwright/test';

async function styleSnapshot(locator, properties) {
    return locator.evaluate((element, names) => {
        const styles = window.getComputedStyle(element);
        return Object.fromEntries(names.map(name => [name, styles.getPropertyValue(name)]));
    }, properties);
}

const forms = [
    {
        url: '/usuario/gestion-de-usuarios/registro',
        form: '#userRegistroForm',
        expectedError: 'Capture nombre(s).',
        expectedSelectErrors: ['Seleccione un cargo.', 'Seleccione un distrito.', 'Seleccione un rol.'],
    },
    {
        url: '/estadisticas/registro',
        form: '#death-register-form',
        expectedError: 'Capture folio.',
        expectedSelectErrors: [
            'Seleccione el sexo.',
            'Seleccione el municipio de residencia.',
            'Seleccione el municipio de defunción.',
            'Seleccione el lugar de defunción.',
            'Seleccione la causa de la defunción.',
        ],
    },
    {
        url: '/reportes/registro/alcoholimetria',
        form: '#alcoholimetriaForm',
        expectedError: 'Capture el tema.',
        expectedSelectErrors: ['Seleccione un municipio.'],
    },
];

for (const example of forms) {
    test(`${example.url} presenta los errores de envío dentro del formulario`, async ({ page }) => {
        await page.goto(example.url);

        const form = page.locator(example.form);
        await expect(form).toBeAttached();
        await expect(form).toHaveAttribute('novalidate', '');
        await expect(form).toHaveAttribute('data-ui-inline-validation', '');

        if (example.form === '#userRegistroForm') {
            await expect(form.locator('#password-match-status')).toBeVisible();
            await expect(form.locator('#password-match-status')).toContainText('Confirme la contraseña');
        }

        await form.locator('button[type="submit"]').click();

        if (example.form === '#userRegistroForm') {
            await expect(form.locator('#password-match-status')).toBeHidden();
        }

        await expect(form.locator('.ui-field-error:not(.hidden), .statistics-field-error:not(.hidden)').first())
            .toContainText(example.expectedError);
        await expect(form.locator('.ts-control .ui-field-error, .ts-control .statistics-field-error')).toHaveCount(0);
        const messages = await form.locator('.ui-field-error:not(.hidden), .statistics-field-error:not(.hidden)').allTextContents();
        expect(messages.every((message) => !message.includes('_'))).toBe(true);
        for (const message of example.expectedSelectErrors) {
            await expect(form.getByText(message, { exact: true })).toHaveCount(1);
        }
        await expect(page).toHaveURL(new RegExp(`${example.url.replaceAll('/', '\\/')}$`));
        await expect(form.locator('[data-ui-submit-button]')).toBeEnabled();
    });
}

test('los selects cortos permiten elegir por escritura y Enter', async ({ page }) => {
    await page.goto('/estadisticas/registro');

    await page.locator('#sex').evaluate((select) => {
        if (select.tomselect) {
            select.tomselect.focus();
            select.tomselect.open();
        } else {
            select.focus();
        }
    });
    await page.keyboard.type('fem');
    await page.keyboard.press('Enter');
    await expect(page.locator('#sex')).toHaveValue('femenino');

    await page.locator('#edad_unidad').evaluate((select) => {
        if (select.tomselect) {
            select.tomselect.focus();
            select.tomselect.open();
        } else {
            select.focus();
        }
    });
    await page.keyboard.type('mes');
    await page.keyboard.press('Enter');
    await expect(page.locator('#edad_unidad')).toHaveValue('meses');
});

test('un envío válido de usuario muestra el spinner y el estado compartido Guardando', async ({ page }) => {
    await page.goto('/usuario/gestion-de-usuarios/registro');

    const form = page.locator('#userRegistroForm');
    await form.evaluate(element => element.addEventListener('submit', event => event.preventDefault()));

    for (const field of [page.locator('#password'), page.locator('#password_confirmation')]) {
        await expect(field).toHaveAttribute('autocapitalize', 'none');
        await expect(field).toHaveAttribute('autocorrect', 'off');
        await expect(field).toHaveAttribute('spellcheck', 'false');
    }

    await page.locator('#name').fill('Usuario Prueba');
    await page.locator('#first_last_name').fill('Temporal');
    await page.locator('#email').fill('usuario.prueba@example.com');
    await page.locator('#position_select').selectOption({ index: 1 });
    await page.locator('#district_id').selectOption({ index: 1 });
    await page.locator('#username').fill('usuario.prueba');
    await page.locator('#role_id').selectOption({ index: 1 });
    await page.locator('#password').fill('PruebaSegura#2026');
    const confirmation = page.locator('#password_confirmation');
    await confirmation.fill('OtraSegura#2026');
    await expect(page.getByText('Las contraseñas no coinciden.', { exact: true })).toBeVisible();
    await expect(confirmation).toHaveAttribute('aria-invalid', 'true');
    await confirmation.fill('PruebaSegura#2026');
    await expect(page.locator('#password_confirmation-client-error')).toBeHidden();
    await expect(confirmation).not.toHaveAttribute('aria-invalid', 'true');
    await expect(page.locator('#password-match-status')).toContainText('Las contraseñas coinciden');

    const submit = form.locator('[data-ui-submit-button]');
    await submit.click();

    await expect(form).toHaveAttribute('aria-busy', 'true');
    await expect(submit).toBeDisabled();
    await expect(submit).toHaveAttribute('aria-busy', 'true');
    await expect(submit).toHaveCSS('cursor', 'wait');
    const spinner = submit.locator('[data-ui-submit-spinner]');
    await expect(spinner).not.toHaveAttribute('hidden', '');
    await expect(spinner).toHaveClass(/fa-spin/);
    await expect(submit.locator('[data-ui-submit-label]')).toHaveText('Guardando…');
});

test('el cambio de contraseña usa validación inline y bloquea envíos repetidos', async ({ page }) => {
    await page.goto('/usuario/gestion-de-usuarios');

    // El componente se monta aun cuando la base aislada no contiene filas de usuario.
    // Un disparador equivalente permite probar el diálogo sin depender del DataTable.
    await page.evaluate(() => {
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'js-change-password';
        trigger.dataset.passwordUpdateUrl = '/usuario/gestion-de-usuarios/actualizar-contrasena/1';
        trigger.dataset.userName = 'Usuario de prueba';
        trigger.dataset.username = 'usuario.prueba';
        document.body.appendChild(trigger);
        trigger.click();
    });

    const dialog = page.locator('#user-password-dialog');
    const password = dialog.locator('#user-password-new');
    const confirmation = dialog.locator('#user-password-confirm');
    const submit = dialog.locator('.user-password-dialog-save');

    for (const field of [password, confirmation]) {
        await expect(field).toHaveAttribute('autocapitalize', 'none');
        await expect(field).toHaveAttribute('autocorrect', 'off');
        await expect(field).toHaveAttribute('spellcheck', 'false');
    }

    await expect(dialog).toHaveAttribute('aria-hidden', 'false');
    await expect(dialog.locator('#user-password-dialog-match')).toBeVisible();
    await expect(dialog.locator('#user-password-dialog-match')).toContainText('Confirme la contraseña');

    await password.fill('PruebaSegura#2026');
    await confirmation.fill('OtraSegura#2026');
    await expect(dialog.getByText('Las contraseñas no coinciden.', { exact: true })).toBeVisible();
    await expect(confirmation).toHaveAttribute('aria-invalid', 'true');
    await confirmation.fill('PruebaSegura#2026');
    await expect(dialog.locator('#user-password-confirm-error')).toBeHidden();
    await expect(dialog.locator('#user-password-dialog-match')).toContainText('Las contraseñas coinciden');
    await password.fill('');
    await confirmation.fill('');
    await expect(dialog.locator('#user-password-dialog-match')).toContainText('Confirme la contraseña');

    await submit.click();

    await expect(dialog.getByText('Capture la nueva contraseña.', { exact: true })).toBeVisible();
    await expect(dialog.getByText('Confirme la nueva contraseña.', { exact: true })).toBeVisible();
    const errorIcon = await dialog.locator('#user-password-confirm-error').evaluate((element) => {
        const styles = window.getComputedStyle(element, '::before');
        return { content: styles.content, display: styles.display };
    });
    expect(errorIcon).toEqual({ content: '"!"', display: 'flex' });
    await expect(dialog.locator('#user-password-dialog-match')).toBeHidden();
    await expect(password).toBeFocused();
    await expect(password).toHaveAttribute('aria-invalid', 'true');

    await password.fill('Corta#1');
    await expect(dialog.getByText('La nueva contraseña debe tener al menos 12 caracteres.', { exact: true })).toBeVisible();

    await password.fill('PruebaSegura#2026');
    await confirmation.fill('OtraSegura#2026');
    await expect(dialog.getByText('Las contraseñas no coinciden.', { exact: true })).toHaveCount(1);
    await expect(dialog.locator('#user-password-dialog-match')).toHaveAttribute('data-status', 'mismatch');
    await expect(dialog.locator('#user-password-dialog-match')).toBeHidden();

    await confirmation.fill('PruebaSegura#2026');
    await expect(dialog.locator('#user-password-dialog-match')).toHaveAttribute('data-status', 'match');
    await expect(dialog.locator('#user-password-dialog-match')).toBeVisible();
    await expect(dialog.locator('#user-password-dialog-match')).toContainText('Las contraseñas coinciden');
    await expect(dialog.locator('#user-password-new-error')).toBeHidden();
    await expect(dialog.locator('#user-password-confirm-error')).toBeHidden();
    await expect(password).not.toHaveAttribute('aria-invalid', 'true');
    await expect(confirmation).not.toHaveAttribute('aria-invalid', 'true');

    await page.route('**/usuario/gestion-de-usuarios/actualizar-contrasena/*', async route => {
        await new Promise(resolve => setTimeout(resolve, 1500));
        await route.fulfill({
            status: 200,
            contentType: 'application/json',
            body: JSON.stringify({ ok: true, message: 'La contraseña se actualizó correctamente.' }),
        });
    });

    await submit.click();
    await Promise.all([
        expect(dialog.locator('form')).toHaveAttribute('aria-busy', 'true'),
        expect(submit).toBeDisabled(),
        expect(submit.locator('[data-password-save-spinner]')).not.toHaveAttribute('hidden', ''),
        expect(submit.locator('[data-password-save-label]')).toHaveText('Guardando…'),
    ]);
    await expect(dialog).toHaveAttribute('aria-hidden', 'true');
});

test('el cambio de contraseña conserva el contrato visual del formulario de usuarios', async ({ page }) => {
    const labelProperties = ['font-family', 'font-size', 'font-weight', 'line-height'];
    const controlProperties = ['font-family', 'font-size', 'font-weight', 'height', 'border-radius', 'border-width', 'background-color'];
    const hoverProperties = ['border-color', 'background-color'];
    const focusProperties = ['border-color', 'background-color', 'box-shadow'];
    const buttonProperties = ['font-family', 'font-size', 'font-weight', 'height', 'border-radius', 'background-color', 'color'];
    const compactButtonProperties = ['font-family', 'font-size', 'font-weight', 'height', 'border-radius'];

    await page.goto('/usuario/gestion-de-usuarios/registro');
    const referencePassword = page.locator('#password');
    const referenceControl = await styleSnapshot(referencePassword, controlProperties);
    await referencePassword.hover();
    await page.waitForTimeout(200);
    const referenceHover = await styleSnapshot(referencePassword, hoverProperties);
    await referencePassword.focus();
    await page.waitForTimeout(200);
    const referenceFocus = await styleSnapshot(referencePassword, focusProperties);
    const reference = {
        label: await styleSnapshot(page.locator('label[for="password"]'), labelProperties),
        control: referenceControl,
        hover: referenceHover,
        focus: referenceFocus,
        primary: await styleSnapshot(page.locator('.users-form-actions .ui-button--primary'), buttonProperties),
        secondary: await styleSnapshot(page.locator('.users-form-actions .ui-button--secondary'), buttonProperties),
        generate: await styleSnapshot(page.locator('.users-password-confirm-tools .users-generate-password'), compactButtonProperties),
    };

    await page.goto('/usuario/gestion-de-usuarios');
    await page.evaluate(() => {
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'js-change-password';
        trigger.dataset.passwordUpdateUrl = '/usuario/gestion-de-usuarios/actualizar-contrasena/1';
        trigger.dataset.userName = 'Usuario de prueba';
        trigger.dataset.username = 'usuario.prueba';
        document.body.appendChild(trigger);
        trigger.click();
    });

    const dialog = page.locator('#user-password-dialog');
    await expect(dialog).toHaveAttribute('aria-hidden', 'false');

    expect(await styleSnapshot(dialog.locator('label[for="user-password-new"]'), labelProperties)).toEqual(reference.label);
    const modalPassword = dialog.locator('#user-password-new');
    expect(await styleSnapshot(modalPassword, controlProperties)).toEqual(reference.control);
    await modalPassword.evaluate(element => element.blur());
    await modalPassword.hover();
    await page.waitForTimeout(200);
    expect(await styleSnapshot(modalPassword, hoverProperties)).toEqual(reference.hover);
    await modalPassword.focus();
    await page.waitForTimeout(200);
    expect(await styleSnapshot(modalPassword, focusProperties)).toEqual(reference.focus);
    expect(await styleSnapshot(dialog.locator('.user-password-dialog-save'), buttonProperties)).toEqual(reference.primary);
    expect(await styleSnapshot(dialog.locator('.user-password-dialog-cancel'), buttonProperties)).toEqual(reference.secondary);
    expect(await styleSnapshot(dialog.locator('#user-password-dialog-generate'), compactButtonProperties)).toEqual(reference.generate);
});

test('los campos del modal evitan el zoom automatico de iOS en movil', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/usuario/gestion-de-usuarios');
    await page.evaluate(() => {
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'js-change-password';
        trigger.dataset.passwordUpdateUrl = '/usuario/gestion-de-usuarios/actualizar-contrasena/1';
        trigger.dataset.userName = 'Usuario de prueba';
        trigger.dataset.username = 'usuario.prueba';
        document.body.appendChild(trigger);
        trigger.click();
    });

    const dialog = page.locator('#user-password-dialog');
    await expect(dialog).toHaveAttribute('aria-hidden', 'false');
    await expect(dialog.locator('#user-password-new')).toHaveCSS('font-size', '16px');
    await expect(dialog.locator('#user-password-confirm')).toHaveCSS('font-size', '16px');
});

