import { expect, test } from '@playwright/test';

test('does not reveal an authenticated page after logout and browser back', async ({ page }) => {
    for (const historyEntry of [1, 2, 3]) {
        await page.goto(`/__e2e/estadisticas/graficas?history=${historyEntry}`);
        await expect(page).toHaveURL(new RegExp(`/__e2e/estadisticas/graficas\\?history=${historyEntry}$`));
    }

    await page.locator('form[action$="/logout"]').first().evaluate((form) => form.requestSubmit());
    await expect(page).toHaveURL(/\/login$/);

    for (let attempt = 0; attempt < 3; attempt += 1) {
        await page.goBack({ waitUntil: 'domcontentloaded' });
        await expect(page).toHaveURL(/\/login$/);
    }
    await expect(page.getByRole('heading', { name: 'Iniciar sesión' })).toBeVisible();
});
