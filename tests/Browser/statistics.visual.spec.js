import { test, expect } from '@playwright/test';
import { readFile } from 'node:fs/promises';
import { chartState, openStatistics, pngDimensions } from './statistics-fixtures.js';

test.beforeEach(async ({ page }) => {
    await openStatistics(page);
});

test('Municipios y Distritos cambian su ámbito desde el encabezado', async ({ page }) => {
    const scopeControl = page.locator('#statisticsGeographicScopeControl');
    const scopeSelector = page.locator('#geographicScopeSelector');

    await expect(scopeControl).toBeVisible();
    await expect(page.locator('#statisticsCopyLink')).toBeHidden();
    await expect(page.locator('#statisticsViewData')).toBeVisible();
    await expect(page.locator('#descargarActual')).toBeVisible();
    await expect(page.locator('.statistics-chart-heading')).toContainText('Distribución por municipios');
    await expect(page.locator('.statistics-chart-heading')).toContainText('Lugar de defunción');
    await expect(page.locator('#filterTipoMunicipio')).toHaveCSS('display', 'none');
    await expect(scopeSelector).toHaveValue('defuncion');

    const requestPromise = page.waitForRequest(request => {
        const url = new URL(request.url());
        return url.pathname.endsWith('/api/chart/municipios')
            && url.searchParams.get('municipio_type') === 'residencia';
    });
    await scopeSelector.selectOption('residencia');
    await requestPromise;

    await expect(page.locator('#tipoMunicipioFilter')).toHaveValue('residencia');
    await expect(page.locator('#chartTitle')).toHaveText('Distribución por municipios');

    await page.getByRole('tab', { name: 'Distritos' }).click();
    await expect(scopeControl).toBeVisible();
    await expect(page.locator('#statisticsGeographicScopeLabel')).toHaveText('Analizar distritos por');
    await expect(scopeSelector).toHaveValue('residencia');
    await expect(page.locator('#filterTipoMunicipio')).toHaveCSS('display', 'none');

    await page.getByRole('tab', { name: 'Edades' }).click();
    await expect(scopeControl).toBeHidden();
    await expect(page.locator('#filterTipoMunicipio')).toHaveCSS('display', 'block');
});

test('Municipios de residencia cambia de cantidades a tasas por población', async ({ page }) => {
    await page.locator('#geographicScopeSelector').selectOption('residencia');
    await page.getByRole('tab', { name: 'Vista' }).click();
    await expect(page.locator('#statisticsMeasureGroup')).toBeVisible();

    const requestPromise = page.waitForRequest(request => {
        const url = new URL(request.url());
        return url.pathname.endsWith('/api/chart/municipios')
            && url.searchParams.get('measure') === 'rate';
    });
    await page.locator('#statisticsMeasureButtons').getByRole('button', { name: 'Tasa', exact: true }).click();
    await requestPromise;

    await expect(page.locator('#chartTitle')).toHaveText('Tasa de defunciones por municipio');
    await expect(page.locator('#chartTypeButtons').getByRole('button', { name: 'Pastel' })).toHaveCount(0);
    await expect(page.locator('#chartTypeButtons').getByRole('button', { name: 'Dona' })).toHaveCount(0);
    await expect(page.locator('#statisticsChartSourceSummary')).toContainText('Población: CONAPO (2025)');
    await expect.poll(async () => page.evaluate(() => {
        const chart = window.echarts.getInstanceByDom(document.getElementById('mainChart'));
        return Number(chart?.getOption()?.series?.[0]?.data?.[0]);
    })).toBeCloseTo(338, 1);
});

test('Tendencias cambia su agrupación desde el encabezado', async ({ page }) => {
    await page.getByRole('tab', { name: 'Tendencias' }).click();

    const granularityControl = page.locator('#statisticsTrendGranularityControl');
    const granularitySelector = page.locator('#trendGranularitySelector');
    await expect(granularityControl).toBeVisible();
    await expect(page.locator('#filterGranularidad')).toHaveCSS('display', 'none');
    await expect(granularitySelector).toHaveValue('month');

    const requestPromise = page.waitForRequest(request => {
        const url = new URL(request.url());
        return url.pathname.endsWith('/api/chart/tendencias')
            && url.searchParams.get('group_by') === 'year';
    });
    await granularitySelector.selectOption('year');
    await requestPromise;

    await expect(page.locator('#granularidadFilter')).toHaveValue('year');
});

test('Top 5, 10 y 15 conservan etiquetas legibles', async ({ page }) => {
    await page.getByRole('tab', { name: 'Vista' }).click();
    const limits = page.locator('#chartLimitButtons');

    await limits.locator('[data-value="15"]').click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(15);
    expect((await chartState(page)).rotation).toBe(38);
    await expect(page.locator('#statisticsChartPanel')).toHaveScreenshot('municipios-top-15.png');

    await limits.locator('[data-value="5"]').click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(5);
    expect((await chartState(page)).rotation).toBe(0);

    await limits.locator('[data-value="10"]').click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(10);
    expect((await chartState(page)).rotation).toBe(0);
});

test('distritos y lugares acomodan catálogos largos', async ({ page }) => {
    await page.getByRole('tab', { name: 'Distritos' }).click();
    await page.locator('#chartLimitButtons [data-value="all"]').click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(12);
    expect((await chartState(page)).rotation).toBe(38);
    await expect(page.locator('#statisticsChartPanel')).toHaveScreenshot('distritos-todos.png');

    await page.getByRole('tab', { name: 'Lugares' }).click();
    await page.locator('#chartLimitButtons [data-value="all"]').click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(15);
    expect((await chartState(page)).rotation).toBe(48);
});

test('edades muestra y exporta la tabla de causas por separado', async ({ page }) => {
    await page.getByRole('tab', { name: 'Edades' }).click();
    await page.getByLabel('Mostrar principales causas por edad').check();

    const detail = page.locator('#causasPrincipalesContainer');
    await expect(detail).toBeVisible();
    await expect(detail.locator('.statistics-causes__item')).toHaveCount(4);
    await expect(page.locator('#statisticsChartPanel')).toHaveScreenshot('edades-con-tabla.png');

    const downloadPromise = page.waitForEvent('download');
    await page.locator('#downloadAgeCausesTable').click();
    const download = await downloadPromise;
    const path = await download.path();
    const dimensions = pngDimensions(await readFile(path));
    expect(dimensions.width).toBeGreaterThan(1_000);
    expect(dimensions.height).toBeGreaterThan(300);

    await page.locator('#downloadAgeCausesOptions').click();
    const pdfDownloadPromise = page.waitForEvent('download');
    await page.locator('[data-table-export="pdf"]').click();
    const pdfDownload = await pdfDownloadPromise;
    const pdfFile = await readFile(await pdfDownload.path());
    expect(pdfFile.subarray(0, 4).toString()).toBe('%PDF');

    await page.locator('#chartTypeButtons').getByRole('button', { name: 'Dona' }).click();
    await expect.poll(async () => page.evaluate(() => (
        currentEchartsInstance?.getOption?.()?.series?.[0]?.type || null
    ))).toBe('pie');
    const exportRadii = await page.evaluate(() => {
        const option = currentEchartsInstance.getOption();
        const before = [...option.series[0].radius];
        const polished = polishAgeDetailExportOption(option);
        return { before, after: polished.series[0].radius };
    });
    expect(Number(exportRadii.after[1])).toBeGreaterThan(Number(exportRadii.before[1]));
});

test('la preferencia de contexto persiste y cambia únicamente el archivo exportado', async ({ page }) => {
    const chartBefore = await chartState(page);
    await page.locator('#descargarOpciones').click();
    await page.locator('#includeChartContext').uncheck();

    const withoutContextPromise = page.waitForEvent('download');
    await page.locator('[data-export="png-white"]').click();
    const withoutContext = await withoutContextPromise;
    const withoutDimensions = pngDimensions(await readFile(await withoutContext.path()));

    await expect.poll(async () => chartState(page)).toEqual(chartBefore);
    await page.reload();
    await page.locator('#mainChart canvas').waitFor();
    await page.locator('#descargarOpciones').click();
    await expect(page.locator('#includeChartContext')).not.toBeChecked();
    await page.locator('#includeChartContext').check();

    const withContextPromise = page.waitForEvent('download');
    await page.locator('[data-export="png-white"]').click();
    const withContext = await withContextPromise;
    const withDimensions = pngDimensions(await readFile(await withContext.path()));

    expect(withDimensions.width).toBe(withoutDimensions.width);
    expect(withDimensions.height).toBeGreaterThan(withoutDimensions.height);
    await expect.poll(async () => chartState(page)).toEqual(chartBefore);
});

test('comparativa conserva sus dos series y una presentación legible', async ({ page }) => {
    await page.getByRole('tab', { name: 'Comparativa' }).click();
    await expect(page.locator('#chartTitle')).toHaveText('Comparativa');
    await expect(page.locator('#statisticsComparisonControl')).toBeVisible();
    await expect(page.locator('#statisticsComparisonControl')).toContainText('Municipio de residencia vs. municipio de defunción');
    await expect.poll(async () => (await chartState(page)).series).toEqual([
        'Municipio de residencia',
        'Municipio de defunción',
    ]);
    await expect(page.locator('#statisticsChartPanel')).toHaveScreenshot('comparativa-municipios.png');
});

test('el PDF amplía gráficas con pocas categorías sin alterar la vista', async ({ page }) => {
    const chartBefore = await chartState(page);

    await page.getByRole('tab', { name: 'Sexo' }).click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(2);
    const genderProfile = await page.evaluate(() => (
        getChartExportProfile(currentEchartsInstance, 460, 'pdf')
    ));
    expect(genderProfile).toEqual({ height: 640, sparseGridInset: '17%' });

    await page.getByRole('tab', { name: 'Distritos' }).click();
    await page.locator('#chartLimitButtons').getByRole('button', { name: 'Top 5', exact: true }).click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(5);
    const districtProfile = await page.evaluate(() => (
        getChartExportProfile(currentEchartsInstance, 460, 'pdf')
    ));
    expect(districtProfile).toEqual({ height: 590, sparseGridInset: '7%' });

    await page.getByRole('tab', { name: 'Municipios' }).click();
    await expect.poll(async () => chartState(page)).toEqual(chartBefore);
});

test('pagina las barras horizontales extensas y conserva columnas en una hoja', async ({ page }) => {
    const limits = page.locator('#chartLimitButtons');
    await limits.getByRole('button', { name: /Mostrar todas las categorías/ }).click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(44);
    expect(await page.evaluate(() => getHorizontalPdfPagination(currentEchartsInstance))).toBeNull();

    await page.locator('#chartTypeButtons')
        .getByRole('button', { name: 'Gráfica de barras horizontales' })
        .click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(44);

    const pagination = await page.evaluate(() => getHorizontalPdfPagination(currentEchartsInstance));
    expect(pagination.pages.map(item => item.categoryCount)).toEqual([15, 15, 14]);
    expect(pagination.pages.map(item => [item.positionStart, item.positionEnd])).toEqual([
        [1, 15],
        [16, 30],
        [31, 44],
    ]);
    expect(pagination.pages.map(item => item.axisMaximum)).toEqual([180, 180, 180]);

    const secondPageOption = await page.evaluate(() => {
        const option = currentEchartsInstance.getOption();
        const { pages } = getHorizontalPdfPagination(currentEchartsInstance);
        const sliced = sliceHorizontalChartOption(option, pages[1]);

        return {
            labels: sliced.yAxis[0].data.length,
            axisMaximum: sliced.xAxis[0].max,
        };
    });
    expect(secondPageOption).toEqual({ labels: 15, axisMaximum: 180 });

    await page.locator('#descargarOpciones').click();
    const downloadPromise = page.waitForEvent('download');
    await page.locator('[data-export="pdf"]').click();
    const download = await downloadPromise;
    const file = await readFile(await download.path());
    const pageObjects = file.toString('latin1').match(/\/Type \/Page\b/g) || [];

    expect(file.subarray(0, 4).toString()).toBe('%PDF');
    expect(pageObjects).toHaveLength(3);
});

test('genera un PDF válido y restablece el estado Preparando', async ({ page }) => {
    await page.locator('#descargarOpciones').click();
    const downloadPromise = page.waitForEvent('download');
    await page.locator('[data-export="pdf"]').click();
    const download = await downloadPromise;
    const file = await readFile(await download.path());

    expect(file.subarray(0, 4).toString()).toBe('%PDF');
    await expect(page.locator('#downloadMenuWrapper')).not.toHaveAttribute('aria-busy', 'true');
    await expect(page.locator('#descargarActual')).toContainText('Descargar gráfica');
});
