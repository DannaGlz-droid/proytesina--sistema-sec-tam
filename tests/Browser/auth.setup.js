import { test as setup, expect } from '@playwright/test';
import { mkdirSync } from 'node:fs';

const authFile = 'test-results/.auth/statistics-user.json';

setup('autentica al usuario de las pruebas visuales', async ({ page }) => {
    mkdirSync('test-results/.auth', { recursive: true });

    await page.goto('/login');
    await page.locator('#login').fill('visual_admin');
    await page.locator('#password').fill('visual-tests');
    await page.getByRole('button', { name: 'Iniciar sesión' }).click();

    await expect(page).toHaveURL(/\/estadisticas\/datos/);
    await page.context().storageState({ path: authFile });
});
