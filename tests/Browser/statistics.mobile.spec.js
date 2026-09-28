import { test, expect } from '@playwright/test';
import { openStatistics } from './statistics-fixtures.js';

test('la gráfica y sus acciones siguen utilizables en móvil', async ({ page }) => {
    await openStatistics(page);

    await page.evaluate(() => document.fonts.ready);
    await expect(page.locator('#descargarActual i')).toHaveCSS('font-family', '"Font Awesome 6 Free"');
    await expect.poll(() => page.locator('#descargarActual i').evaluate(element => (
        getComputedStyle(element, '::before').content
    ))).not.toBe('none');

    await expect(page.locator('#statisticsChartPanel')).toBeVisible();
    await expect(page.locator('#statisticsChartPanel')).toHaveScreenshot('municipios-mobile.png');

    await page.locator('#descargarOpciones').click();
    await expect(page.locator('#downloadMenu')).toBeVisible();
    await expect(page.locator('#downloadMenu')).toBeInViewport();
});
