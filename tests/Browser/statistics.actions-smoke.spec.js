import { test, expect } from '@playwright/test';
import { openStatistics } from './statistics-fixtures.js';

test('expone compartir y el ranking municipal sin saturar el encabezado', async ({ page, context }) => {
    await openStatistics(page);
    await context.grantPermissions(['clipboard-read', 'clipboard-write'], { origin: new URL(page.url()).origin });

    await expect(page.locator('#statisticsCopyLink')).not.toBeVisible();
    await page.locator('#descargarOpciones').click();
    await expect(page.locator('#statisticsCopyLink')).toBeVisible();
    await expect(page.locator('#statisticsCopyLinkLabel')).toHaveText('Copiar enlace de esta vista');
    await page.locator('#statisticsCopyLink').click();
    await expect(page.locator('#statisticsCopyLinkLabel')).toHaveText('Enlace copiado');

    await page.keyboard.press('Escape');
    const point = await page.locator('#mainChart').evaluate(element => {
        const chart = window.echarts.getInstanceByDom(element);
        const option = chart.getOption();
        const label = option.xAxis[0].data[0];
        const rendered = option.series[0].data[0];
        const value = Number(typeof rendered === 'object' ? rendered.value : rendered);
        return {
            x: chart.convertToPixel({ xAxisIndex: 0 }, label),
            y: chart.convertToPixel({ yAxisIndex: 0 }, value / 2),
        };
    });
    await page.locator('#mainChart').click({ position: point });
    await expect(page.locator('#statisticsDrilldownMeta')).toContainText('posición 1 de 44');
});
