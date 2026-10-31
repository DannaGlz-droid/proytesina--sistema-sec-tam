import { expect, test } from '@playwright/test';

test('keeps the remember checkbox visibly focused after inline validation', async ({ context, page }) => {
    await context.clearCookies();
    await page.goto('/login');

    await page.getByRole('button', { name: 'Iniciar sesión' }).click();
    await expect(page.locator('#login')).toBeFocused();
    await expect(page.getByText('Capture su usuario o correo.')).toBeVisible();
    await expect(page.getByText('Capture su contraseña.')).toBeVisible();

    await page.keyboard.press('Tab');
    await expect(page.locator('#password')).toBeFocused();

    await page.keyboard.press('Tab');
    await expect(page.locator('[data-login-password-toggle]')).toBeFocused();

    await page.keyboard.press('Tab');
    const remember = page.locator('input[name="remember"]');
    await expect(remember).toBeFocused();

    const focusRing = await page.locator('.login-remember-control').evaluate((control) =>
        getComputedStyle(control).boxShadow
    );
    expect(focusRing).not.toBe('none');

    await page.keyboard.press('Space');
    await expect(remember).toBeChecked();
});
