import { expect, test } from '@playwright/test';

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
];

for (const example of forms) {
    test(`${example.url} presenta los errores de envío dentro del formulario`, async ({ page }) => {
        await page.goto(example.url);

        const form = page.locator(example.form);
        await expect(form).toBeAttached();
        await expect(form).toHaveAttribute('novalidate', '');
        await expect(form).toHaveAttribute('data-ui-inline-validation', '');

        await form.locator('button[type="submit"]').click();

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

    await page.locator('#name').fill('Usuario Prueba');
    await page.locator('#first_last_name').fill('Temporal');
    await page.locator('#email').fill('usuario.prueba@example.com');
    await page.locator('#position_select').selectOption({ index: 1 });
    await page.locator('#district_id').selectOption({ index: 1 });
    await page.locator('#username').fill('usuario.prueba');
    await page.locator('#role_id').selectOption({ index: 1 });
    await page.locator('#password').fill('PruebaSegura#2026');
    await page.locator('#password_confirmation').fill('PruebaSegura#2026');

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

