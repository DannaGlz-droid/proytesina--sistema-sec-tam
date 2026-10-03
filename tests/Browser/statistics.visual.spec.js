import { test, expect } from '@playwright/test';
import { readFile } from 'node:fs/promises';
import { chartState, openStatistics, pngDimensions } from './statistics-fixtures.js';

test.beforeEach(async ({ page }) => {
    await openStatistics(page);
});

test('Municipios y Distritos cambian su ámbito desde Vista', async ({ page }) => {
    const scopeControl = page.locator('#statisticsGeographicScopeControl');
    const scopeSelector = page.locator('#geographicScopeSelector');

    await page.getByRole('tab', { name: 'Vista' }).click();
    await expect(scopeControl).toBeVisible();
    await expect(page.locator('#statisticsCopyLink')).toBeHidden();
    await expect(page.locator('#statisticsViewData')).toBeVisible();
    await expect(page.locator('#descargarActual')).toBeVisible();
    await expect(page.locator('#statisticsChartQuality')).toBeHidden();
    await expect(page.locator('#chartTitle')).toHaveText('Distribución por municipios de defunción');
    await expect(page.locator('#filterTipoMunicipio')).toHaveCSS('display', 'none');
    await expect(scopeSelector).toHaveValue('defuncion');
    await expect(scopeControl.getByRole('radio', { name: 'Defunción' })).toHaveAttribute('aria-checked', 'true');

    const requestPromise = page.waitForRequest(request => {
        const url = new URL(request.url());
        return url.pathname.endsWith('/api/chart/municipios')
            && url.searchParams.get('municipio_type') === 'residencia';
    });
    await scopeControl.getByRole('radio', { name: 'Residencia' }).click();
    await requestPromise;

    await expect(page.locator('#tipoMunicipioFilter')).toHaveValue('residencia');
    await expect(page.locator('#chartTitle')).toHaveText('Distribución por municipios de residencia');
    await expect(scopeControl.getByRole('radio', { name: 'Residencia' })).toHaveAttribute('aria-checked', 'true');

    await page.getByRole('tab', { name: 'Distritos' }).click();
    await expect(scopeControl).toBeVisible();
    await expect(page.locator('#statisticsGeographicScopeLabel')).toHaveText('Agrupar distritos por');
    await expect(page.locator('#chartTitle')).toHaveText('Distribución por distritos de residencia');
    await expect(scopeSelector).toHaveValue('residencia');
    await expect(page.locator('#filterTipoMunicipio')).toHaveCSS('display', 'none');

    await page.getByRole('tab', { name: 'Edades' }).click();
    await expect(scopeControl).toBeHidden();
    await expect(page.locator('#filterTipoMunicipio')).toHaveCSS('display', 'block');
});

test('Municipios de residencia cambia de cantidades a tasas por población', async ({ page }) => {
    await page.emulateMedia({ reducedMotion: 'no-preference' });
    await page.evaluate(() => {
        window.__municipalityConfigAnimationStarted = false;
        document.getElementById('municipalitySidebarPresentation')?.addEventListener('animationstart', () => {
            window.__municipalityConfigAnimationStarted = true;
        }, { once: true });
    });
    await page.getByRole('tab', { name: 'Vista' }).click();
    await expect(page.locator('#statisticsMeasureGroup')).toBeVisible();
    await expect.poll(() => page.evaluate(() => window.__municipalityConfigAnimationStarted)).toBe(true);

    const residenceRequest = page.waitForRequest(request => {
        const url = new URL(request.url());
        return url.pathname.endsWith('/api/chart/municipios')
            && url.searchParams.get('municipio_type') === 'residencia';
    });
    await page.locator('#statisticsGeographicScopeControl').getByRole('radio', { name: 'Residencia' }).click();
    await residenceRequest;

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

test('Municipios compacta Sexo y Edad con foco neutral', async ({ page }) => {
    await page.getByRole('tab', { name: 'Datos', exact: true }).click();

    const demographicContent = page.locator('#statisticsDemographicFilterGroup > .users-filter-section-content');
    const sexFilter = page.locator('#filterSexo');
    const ageFilter = page.locator('#filterEdad');
    const ageInput = page.locator('#edadFilter');
    const ageComposite = page.locator('.statistics-age-composite');
    const ageUnit = page.locator('#edadUnidadFilter');
    const ageHelpButton = page.getByRole('button', { name: 'Cómo filtrar por edad' });
    const ageTooltip = page.getByRole('tooltip');

    await expect(demographicContent).toBeVisible();
    await expect(sexFilter).toBeVisible();
    await expect(ageFilter).toBeVisible();
    await expect(ageUnit).toHaveValue('years');

    const [sexBox, ageBox, ageInputBox, ageUnitBox] = await Promise.all([
        sexFilter.boundingBox(),
        ageFilter.boundingBox(),
        ageInput.boundingBox(),
        ageUnit.boundingBox(),
    ]);
    expect(Math.abs(sexBox.y - ageBox.y)).toBeLessThanOrEqual(1);
    expect(ageBox.width).toBeGreaterThan(sexBox.width * 2);
    expect(Math.abs(ageInputBox.y - ageUnitBox.y)).toBeLessThanOrEqual(1);

    const renderedSexControl = page.locator('#filterSexo .ts-control');
    const sexControlBox = await renderedSexControl.count()
        ? await renderedSexControl.boundingBox()
        : await page.locator('#sexoFilter').boundingBox();
    const ageCompositeBox = await ageComposite.boundingBox();
    expect(Math.abs(sexControlBox.y - ageCompositeBox.y)).toBeLessThanOrEqual(1);
    expect(Math.abs(sexControlBox.height - ageCompositeBox.height)).toBeLessThanOrEqual(1);

    await expect(page.locator('#sexoFilter option[value="F"]')).toHaveText('Femenino');
    await expect(page.locator('#sexoFilter option[value="M"]')).toHaveText('Masculino');
    await page.locator('#sexoFilter').evaluate(select => {
        if (select.tomselect) select.tomselect.setValue('F', true);
        else {
            select.value = 'F';
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
    await expect(page.locator('#sexoFilter')).toHaveValue('F');
    expect(await page.locator('#sexoFilter').evaluate(select => select.selectedOptions[0]?.textContent?.trim()))
        .toBe('Femenino');
    const compactSexLabel = page.locator('#filterSexo .app-filter-select__item-label');
    if (await compactSexLabel.count()) await expect(compactSexLabel).toHaveText('F');

    await ageInput.focus();
    await expect(ageComposite).toHaveCSS('border-color', 'rgb(71, 85, 105)');
    await ageInput.fill('12abc-20');
    await expect(ageInput).toHaveValue('12-20');
    await ageInput.fill('10,20,30,40,50,60,70,90');
    await expect(page.locator('#edadFilterValuePreview')).toBeVisible();
    await expect(page.locator('#edadFilterValuePreview')).toHaveText('10,20,30,40,50,60,70,90');
    const ageHeightBeforeUnitChange = (await ageComposite.boundingBox()).height;
    await ageUnit.selectOption('months');
    expect((await ageComposite.boundingBox()).height).toBeCloseTo(ageHeightBeforeUnitChange, 1);
    await ageUnit.selectOption('days');
    await expect(ageUnit).toHaveValue('days');
    expect((await ageComposite.boundingBox()).height).toBeCloseTo(ageHeightBeforeUnitChange, 1);

    await ageHelpButton.focus();
    await expect(ageTooltip).toHaveCSS('visibility', 'visible');
    await expect(ageTooltip).toContainText('varias separadas por comas');

    const [panelBox, tooltipBox] = await Promise.all([
        page.locator('#estadisticas-filtros').boundingBox(),
        ageTooltip.boundingBox(),
    ]);
    expect(tooltipBox.x).toBeGreaterThanOrEqual(panelBox.x);
});

test('Municipios equilibra graficas compactas sin comprimir barras extensas', async ({ page }) => {
    const configuration = page.locator('#estadisticas-filtros');
    const chartPanel = page.locator('#statisticsChartPanel');
    const chartHeight = async () => (await chartPanel.boundingBox()).height;

    await page.getByRole('tab', { name: 'Datos', exact: true }).click();
    const dataViewHeight = await chartHeight();

    await page.getByRole('tab', { name: 'Vista' }).click();
    await page.locator('#municipalityChartTypeButtons')
        .getByRole('button', { name: 'Dona' })
        .click();
    await expect.poll(chartHeight).toBeCloseTo(dataViewHeight, 0);

    await page.locator('#municipalityChartTypeButtons [data-value="barHorizontal"]').click();
    await page.locator('#chartLimitButtons [data-value="5"]').click();
    await expect.poll(chartHeight).toBeCloseTo(dataViewHeight, 0);

    await page.locator('#municipalityChartTypeButtons [data-value="bar"]').click();
    await expect.poll(chartHeight).toBeCloseTo(dataViewHeight, 0);

    await page.locator('#municipalityChartTypeButtons [data-value="barHorizontal"]').click();
    await page.locator('#chartLimitButtons [data-value="all"]').click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(44);

    const [configurationBox, chartBox] = await Promise.all([
        configuration.boundingBox(),
        chartPanel.boundingBox(),
    ]);
    expect(chartBox.height).toBeGreaterThan(configurationBox.height);
});

test('Municipios conserva la misma altura entre Datos y Vista para cada grafica', async ({ page }) => {
    await page.setViewportSize({ width: 1536, height: 900 });

    const chartPanel = page.locator('#statisticsChartPanel');
    const chartCanvas = page.locator('.statistics-chart-canvas');
    const panelHeight = async () => Math.round((await chartPanel.boundingBox()).height);
    const canvasHeight = async () => Math.round((await chartCanvas.boundingBox()).height);
    const renderedCategoryCount = async () => page.evaluate(() => {
        const chart = window.echarts.getInstanceByDom(document.getElementById('mainChart'));
        return chart?.getModel()?.getSeriesByIndex(0)?.getData()?.count?.() || 0;
    });
    const dataTab = page.getByRole('tab', { name: 'Datos', exact: true });
    const viewTab = page.getByRole('tab', { name: 'Vista' });

    const cases = [
        { type: 'bar', limit: '5', expectedLabels: 5 },
        { type: 'bar', limit: '10', expectedLabels: 10 },
        { type: 'bar', limit: '15', expectedLabels: 15 },
        { type: 'barHorizontal', limit: '5', expectedLabels: 5 },
        { type: 'barHorizontal', limit: '10', expectedLabels: 10 },
        { type: 'barHorizontal', limit: '15', expectedLabels: 15 },
        { type: 'pie', expectedLabels: 10 },
        { type: 'doughnut', expectedLabels: 10 },
    ];

    for (const chartCase of cases) {
        await viewTab.click();
        await page.locator(`#municipalityChartTypeButtons [data-value="${chartCase.type}"]`).click();
        if (chartCase.limit) {
            await page.locator(`#chartLimitButtons [data-value="${chartCase.limit}"]`).click();
        }
        await expect.poll(renderedCategoryCount).toBe(chartCase.expectedLabels);

        const viewPanelHeight = await panelHeight();
        const viewCanvasHeight = await canvasHeight();
        await dataTab.click();

        await expect.poll(panelHeight).toBe(viewPanelHeight);
        await expect.poll(canvasHeight).toBe(viewCanvasHeight);
    }
});

test('las graficas circulares conservan espacio antes de la leyenda', async ({ page }) => {
    await page.setViewportSize({ width: 1536, height: 900 });
    await page.getByRole('tab', { name: 'Vista' }).click();

    const circularGeometry = async () => page.evaluate(() => {
        const chart = window.echarts.getInstanceByDom(document.getElementById('mainChart'));
        const seriesModel = chart?.getModel()?.getSeriesByIndex(0);
        const layout = seriesModel?.getData()?.getItemLayout(0);
        const legendModel = chart?.getModel()?.getComponent('legend');
        const legendView = legendModel ? chart?.getViewOfComponentModel?.(legendModel) : null;
        const legendBounds = legendView?.group?.getBoundingRect?.();
        const legendPoint = legendBounds
            ? legendView.group.transformCoordToGlobal(legendBounds.x, legendBounds.y)
            : null;
        const legendStart = Number(legendPoint?.[0]);

        return {
            radius: Number(layout?.r || 0),
            gap: !Number.isFinite(legendStart)
                ? null
                : legendStart - (Number(layout?.cx || 0) + Number(layout?.r || 0)),
        };
    });

    for (const type of ['pie', 'doughnut']) {
        await page.locator(`#municipalityChartTypeButtons [data-value="${type}"]`).click();
        await expect.poll(async () => (await circularGeometry()).radius).toBeGreaterThan(0);
        const geometry = await circularGeometry();
        expect(geometry.gap).toBeGreaterThanOrEqual(32);
    }
});

test('los filtros aplicados permanecen junto a la gráfica sin saltos ni borradores accidentales', async ({ page }) => {
    const strip = page.locator('#filtrosActivos');
    const chartHeader = page.locator('#statisticsChartPanel .statistics-chart-header');
    const chartCanvas = page.locator('.statistics-chart-canvas');

    await expect(chartHeader.locator('#filtrosActivos')).toHaveCount(1);
    await expect(page.locator('.statistics-workbench-toolbar #filtrosActivos')).toHaveCount(0);
    await expect(strip).toContainText('Sin filtros adicionales');
    await expect(strip).toHaveAttribute('data-filter-count', '0');
    const emptyCanvasBox = await chartCanvas.boundingBox();
    const contextBox = await page.locator('#statisticsChartContextRow').boundingBox();
    const emptyStripBox = await strip.boundingBox();
    expect(emptyStripBox.y).toBeGreaterThanOrEqual(contextBox.y + contextBox.height - 1);
    expect(Math.abs(emptyStripBox.x - contextBox.x)).toBeLessThanOrEqual(1);

    await page.evaluate(() => {
        const setControlValue = (id, value) => {
            const control = document.getElementById(id);
            if (control?.tomselect) control.tomselect.setValue(value, true);
            else if (control) control.value = value;
        };

        Object.assign(activeFilters, {
            dateRange: 'years',
            selectedYears: [2025],
            selectedMonths: [],
            residenceDistricts: ['9'],
            residenceDistrictNames: ['IX · Miguel Alemán'],
            deathDistricts: ['3', '10'],
            deathDistrictNames: ['III · Matamoros', 'X · Valle Hermoso'],
            deathLocations: ['1', '2'],
            deathLocationNames: ['IMSS-Bienestar', 'Unidad médica privada'],
            causas: ['1', '2', '3'],
            causasNames: ['Caídas accidentales', 'Exposición a fuego y humo', 'Otros accidentes'],
            sexo: 'M',
            edad: '10,20,30,40,50,60,70,90',
            edadUnidad: 'years',
        });
        setControlValue('dateRange', 'years');
        document.getElementById('year').value = '2025';
        setControlValue('residenceDistrictsFilter', ['9']);
        setControlValue('deathDistrictsFilter', ['3', '10']);
        setControlValue('deathLocationsFilter', ['1', '2']);
        setControlValue('causasFilter', ['1', '2', '3']);
        setControlValue('sexoFilter', 'M');
        document.getElementById('edadFilter').value = '10,20,30,40,50,60,70,90';
        filterDraftSnapshot = captureStatisticsFilterState();
        updateActiveFiltersDisplay();
    });

    await expect(strip).toHaveAttribute('data-filter-count', '7');
    const visibleFilterChips = page.locator('#filtrosActivosList .statistics-filter-chip:visible');
    await expect.poll(() => visibleFilterChips.count()).toBeGreaterThan(0);
    const filterOverflowText = (await page.locator('#statisticsActiveFiltersMore').textContent())?.trim() || '';
    expect(filterOverflowText).toContain('Ver todos (7)');
    await expect(page.locator('#filtrosActivosList .statistics-filter-chip:has([data-clear-filter="causas"])'))
        .not.toContainText('seleccionadas');
    await expect(page.locator('#filtrosActivosList .statistics-filter-chip:has([data-clear-filter="causas"])'))
        .toContainText(/ y \d+ más/);
    await expect(page.locator('#filtrosActivosList .statistics-filter-chip:has([data-clear-filter="deathDistricts"])'))
        .not.toContainText('seleccionados');
    await expect(page.locator('#filtrosActivosList .statistics-filter-chip:has([data-clear-filter="edad"])'))
        .toContainText('10, 20, 30, 40, 50, 60, 70, 90 años');
    await expect(page.locator('#filtrosActivosList .statistics-filter-chip[title]')).toHaveCount(0);
    expect(Math.abs((await chartCanvas.boundingBox()).y - emptyCanvasBox.y)).toBeLessThanOrEqual(1);
    const appliedStripBox = await strip.boundingBox();
    expect(Math.abs(appliedStripBox.y - emptyStripBox.y)).toBeLessThanOrEqual(1);
    expect(Math.abs(appliedStripBox.height - emptyStripBox.height)).toBeLessThanOrEqual(1);
    await expect(chartHeader).toHaveScreenshot('filtros-aplicados.png');

    for (const width of [1536, 1280]) {
        await page.setViewportSize({ width, height: 900 });
        await expect(page.locator('#statisticsActiveFiltersMore')).toContainText('Ver todos (7)');
        const visibleChips = page.locator('#filtrosActivosList .statistics-filter-chip:visible');
        await expect.poll(() => visibleChips.count()).toBeGreaterThan(0);
        const chipBoxes = await visibleChips.evaluateAll(chips => chips.map(chip => {
            const box = chip.getBoundingClientRect();
            return { top: box.top, bottom: box.bottom, right: box.right };
        }));
        expect(Math.max(...chipBoxes.map(box => box.top)) - Math.min(...chipBoxes.map(box => box.top))).toBeLessThanOrEqual(1);
        const moreBox = await page.locator('#statisticsActiveFiltersMore').boundingBox();
        expect(chipBoxes.at(-1).right).toBeLessThanOrEqual(moreBox.x + 1);
    }
    await page.setViewportSize({ width: 1920, height: 1080 });

    await page.locator('#statisticsActiveFiltersMore').click();
    const popover = page.locator('#statisticsActiveFiltersPopover');
    await expect(popover).toBeVisible();
    await expect(page.locator('#statisticsActiveFiltersAll .statistics-filter-chip')).toHaveCount(7);
    await expect(popover).toContainText('Exposición a fuego y humo');
    await expect(popover).toHaveScreenshot('filtros-aplicados-panel.png');
    await page.keyboard.press('Escape');
    await expect(popover).toBeHidden();

    await page.locator('#edadFilter').fill('99');
    await page.evaluate(() => markStatisticsFilterDraft());
    await page.locator('#statisticsActiveFiltersMore').click();
    await page.locator('#statisticsActiveFiltersAll [data-clear-filter="sexo"]').click();
    await expect(page.locator('#edadFilter')).toHaveValue('10,20,30,40,50,60,70,90');
    expect(await page.evaluate(() => activeFilters.edad)).toBe('10,20,30,40,50,60,70,90');

    await page.keyboard.press('Escape');
    await page.locator('#filtrosActivosList .statistics-filter-chip').evaluateAll(chips => {
        chips.slice(1).forEach(chip => chip.remove());
        layoutActiveFilterChips();
    });
    await page.locator('#statisticsActiveFiltersMore').click();
    const singleFilterMoreBox = await page.locator('#statisticsActiveFiltersMore').boundingBox();
    const singleFilterPopoverBox = await popover.boundingBox();
    expect(Math.abs(singleFilterPopoverBox.x - singleFilterMoreBox.x)).toBeLessThanOrEqual(2);

    await page.setViewportSize({ width: 600, height: 900 });
    await expect(page.locator('.statistics-active-filters__desktop')).toBeHidden();
    await expect(page.locator('#statisticsActiveFiltersMobile')).toBeVisible();
    await expect(page.locator('#statisticsActiveFiltersMobileText')).toHaveText('2 filtros aplicados');
});

test('Fecha muestra contexto y mantiene compactos meses y trimestres', async ({ page }) => {
    const setDateMode = mode => page.locator('#dateRange').evaluate((select, nextMode) => {
        if (select.tomselect) select.tomselect.setValue(nextMode);
        else {
            select.value = nextMode;
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }, mode);

    await expect(page.locator('#dateFilterDetail')).toBeHidden();
    await expect(page.locator('#dateRange option')).toHaveCount(6);

    await setDateMode('months');
    await expect(page.locator('#yearSelector')).toBeVisible();
    await expect(page.locator('#monthSelector')).toBeVisible();
    const yearHelpButton = page.locator('#yearFilterHelpPopover .statistics-filter-help-trigger');
    const yearTooltip = page.locator('#yearFilterHelp');
    await expect(yearHelpButton).toBeVisible();
    await yearHelpButton.focus();
    await expect(yearTooltip).toBeVisible();
    await expect(yearTooltip).toContainText('Los años disponibles van de 2024 a 2025');
    await expect(page.locator('#year')).toHaveAttribute('placeholder', 'Ej. 2024-2025');

    const [filterPanelBox, yearTooltipBox] = await Promise.all([
        page.locator('#estadisticas-filtros').boundingBox(),
        yearTooltip.boundingBox(),
    ]);
    expect(yearTooltipBox.x).toBeGreaterThanOrEqual(filterPanelBox.x);
    expect(yearTooltipBox.x + yearTooltipBox.width)
        .toBeLessThanOrEqual(filterPanelBox.x + filterPanelBox.width + 1);

    const [yearBox, monthBox] = await Promise.all([
        page.locator('#yearSelector').boundingBox(),
        page.locator('#monthSelector').boundingBox(),
    ]);
    expect(Math.abs(yearBox.y - monthBox.y)).toBeLessThanOrEqual(1);

    await page.locator('#monthPickerToggle').click();
    await expect(page.locator('#monthPickerPopover')).toBeVisible();
    const [dateDetailBox, monthPopoverBox] = await Promise.all([
        page.locator('#dateFilterDetail').boundingBox(),
        page.locator('#monthPickerPopover').boundingBox(),
    ]);
    expect(Math.abs(dateDetailBox.x - monthPopoverBox.x)).toBeLessThanOrEqual(1);
    expect(Math.abs(dateDetailBox.width - monthPopoverBox.width)).toBeLessThanOrEqual(1);
    await page.locator('label[for="month-01"]').click();
    await page.locator('label[for="month-02"]').click();
    await page.locator('label[for="month-03"]').click();
    await expect(page.locator('#monthPickerSummary')).toHaveText('Ene, Feb +1');
    await page.locator('#closeMonthPicker').click();
    await expect(page.locator('#monthPickerPopover')).toBeHidden();

    await page.locator('#year').fill('2026');
    expect(await page.evaluate(() => validateStatisticsFilterDraft())).toBe(false);
    await expect(page.locator('#yearFilterError')).toContainText('Los años disponibles van de 2024 a 2025');
    await page.locator('#year').fill('2024-2025');

    await setDateMode('quarter');
    await expect(page.locator('#quarterSelector')).toBeVisible();
    await expect(yearHelpButton).toBeHidden();
    await expect(page.locator('#year')).toHaveValue('2025');
    await expect(page.locator('#year')).toHaveAttribute('placeholder', 'Ej. 2025');
    await expect(page.locator('#quarter option[value=""]')).toHaveText('Elige');
    const [quarterYearBox, quarterBox] = await Promise.all([
        page.locator('#yearSelector').boundingBox(),
        page.locator('#quarterSelector').boundingBox(),
    ]);
    expect(Math.abs(quarterYearBox.y - quarterBox.y)).toBeLessThanOrEqual(1);

    await setDateMode('years');
    await expect(page.locator('#year')).toHaveValue('');
    await setDateMode('months');
    await expect(page.locator('#year')).toHaveValue('2024-2025');
    await setDateMode('quarter');
    await expect(page.locator('#year')).toHaveValue('2025');

    await setDateMode('custom');
    await expect(page.locator('#customDateSelector')).toBeVisible();
    await expect(page.locator('#customStartDate')).toHaveAttribute('min', '2024-12-29');
    await expect(page.locator('#customEndDate')).toHaveAttribute('min', '2024-12-30');
    await expect(page.locator('#customStartDate')).toHaveAttribute('max', '2025-12-29');
    await expect(page.locator('#customEndDate')).toHaveAttribute('max', '2025-12-29');
    const acceptsFutureDate = await page.evaluate(() => {
        document.getElementById('customStartDate').value = '2025-12-01';
        document.getElementById('customEndDate').value = '2026-01-01';
        return validateStatisticsFilterDraft();
    });
    expect(acceptsFutureDate).toBe(false);
    await expect(page.locator('#customDateFilterError')).toContainText('La fecha no puede ser posterior');

    const outsideCoverage = await page.evaluate(() => {
        activeFilters.dateRange = 'months';
        activeFilters.selectedYears = [2024];
        activeFilters.selectedMonths = [2];
        activeFilters.startDate = null;
        activeFilters.endDate = null;
        showNoChartData({ filtered_total: 0, quality: { excluded_total: 0 } });
        return isSelectedDateFilterOutsideCoverage();
    });
    expect(outsideCoverage).toBe(true);
    await expect(page.locator('#errorText')).toHaveText('No hay datos disponibles para este periodo.');
    await expect(page.locator('#statisticsChartStateDescription')).toContainText('29 dic 2024');

    await page.evaluate(() => {
        activeFilters.selectedYears = [2025];
        activeFilters.selectedMonths = [2];
        showNoChartData({ filtered_total: 0, quality: { excluded_total: 0 } });
    });
    await expect(page.locator('#errorText')).toHaveText('No se encontraron registros con los filtros seleccionados.');

    await setDateMode('full');
    await expect(page.locator('#dateFilterDetail')).toBeHidden();
});

test('Fecha informa cuando no puede comprobar la cobertura y permite reintentar', async ({ page }) => {
    await page.unroute('**/api/default-date-range');
    let attempts = 0;
    await page.route('**/api/default-date-range', route => {
        attempts += 1;
        if (attempts === 1) {
            return route.fulfill({ status: 503, contentType: 'application/json', body: '{}' });
        }

        return route.fulfill({
            status: 200,
            contentType: 'application/json',
            body: JSON.stringify({
                start_date: '2024-12-30',
                end_date: '2025-12-29',
                data_start: '2024-12-29',
                data_end: '2025-12-29',
            }),
        });
    });

    await page.reload();
    const notice = page.locator('#statisticsDateCoverageNotice');
    await expect(notice).toBeVisible();
    await expect(notice).toContainText('No se pudieron comprobar las fechas disponibles');

    await page.locator('#statisticsDateCoverageRetry').click();
    await expect(notice).toBeHidden();
    await expect(page.locator('#customStartDate')).toHaveValue('2024-12-30');
    await expect(page.locator('#customEndDate')).toHaveValue('2025-12-29');
});

test('Municipios muestra los filtros de ubicación de forma clara y compacta', async ({ page }) => {
    await page.getByRole('tab', { name: 'Datos', exact: true }).click();

    const locationFields = page.locator('#filterMunicipalityLocations');
    await expect(locationFields).toBeVisible();
    await expect(page.locator('#filterdistritoes')).toHaveCSS('display', 'none');
    await expect(page.locator('label[for="residenceDistrictsFilter"]')).toHaveText('Distritos de residencia');
    await expect(page.locator('label[for="deathDistrictsFilter"]')).toHaveText('Distritos de defunción');
    await expect(page.locator('label[for="edadFilter"]')).toHaveText('Edad');

    const residenceEditor = page.locator('#residenceDistrictEditor');
    const deathEditor = page.locator('#deathDistrictEditor');
    await expect(residenceEditor).toBeVisible();
    await expect(deathEditor).toBeVisible();

    const residenceBox = await residenceEditor.boundingBox();
    const deathBox = await deathEditor.boundingBox();
    expect(residenceBox.width).toBeGreaterThan(250);
    expect(deathBox.width).toBeGreaterThan(250);

    const compactMultiselects = [
        'residenceDistrictsFilter',
        'deathDistrictsFilter',
        'deathLocationsFilter',
        'causasFilter',
    ];
    for (const id of compactMultiselects) {
        const select = page.locator(`#${id}`);
        await expect(select).toHaveAttribute('data-collapsed-limit', '1');
        const firstTwoValues = await select.locator('option').evaluateAll(options => (
            options.slice(0, 2).map(option => option.value)
        ));
        const usesTomSelect = await select.evaluate(element => Boolean(element.tomselect));
        if (usesTomSelect) {
            await select.evaluate((element, values) => element.tomselect.setValue(values), firstTwoValues);
        } else {
            await select.selectOption(firstTwoValues);
        }
        await expect(select).toHaveValues(firstTwoValues);
        if (id === 'causasFilter') {
            await expect(page.locator('#statisticsFiltersApply')).toBeEnabled();
        }
        if (usesTomSelect) {
            await select.evaluate(element => element.tomselect.clear());
        } else {
            await select.selectOption([]);
        }
    }

    const selected = { residence: '2', death: '2', location: '2' };
    await page.locator('#residenceDistrictsFilter').selectOption([selected.residence]);
    await page.locator('#deathDistrictsFilter').selectOption([selected.death]);
    await page.locator('#deathLocationsFilter').selectOption([selected.location]);

    const requestPromise = page.waitForRequest(request => {
        const url = new URL(request.url());
        return url.pathname.endsWith('/api/chart/municipios')
            && url.searchParams.get('district_ids[]') === selected.residence
            && url.searchParams.get('death_district_ids[]') === selected.death
            && url.searchParams.get('death_location_ids[]') === selected.location;
    });
    await page.locator('#statisticsFiltersApply').click();
    await requestPromise;

    await page.locator('#limpiarFiltros').click();
    await expect(page.locator('#residenceDistrictsFilter')).toHaveValues([]);
    await expect(page.locator('#deathDistrictsFilter')).toHaveValues([]);
    await expect(page.locator('#deathLocationsFilter')).toHaveValues([]);
});

test('Tendencias cambia su agrupación desde Vista', async ({ page }) => {
    await page.getByRole('tab', { name: 'Tendencias' }).click();

    const granularityControl = page.locator('#statisticsTrendGranularityControl');
    const granularitySelector = page.locator('#trendGranularitySelector');
    await expect(granularityControl).toBeVisible();
    await expect(page.locator('#filterGranularidad')).toHaveCSS('display', 'none');
    await expect(granularitySelector).toHaveValue('month');
    await expect(page.locator('#chartTitle')).toHaveText('Tendencia mensual');
    await expect(granularityControl.getByRole('radio', { name: 'Mes' })).toHaveAttribute('aria-checked', 'true');

    const requestPromise = page.waitForRequest(request => {
        const url = new URL(request.url());
        return url.pathname.endsWith('/api/chart/tendencias')
            && url.searchParams.get('group_by') === 'year';
    });
    await granularityControl.getByRole('radio', { name: 'Año' }).click();
    await requestPromise;

    await expect(page.locator('#granularidadFilter')).toHaveValue('year');
    await expect(page.locator('#chartTitle')).toHaveText('Tendencia anual');
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
    await expect(page.locator('#chartTitle')).toHaveText('Municipio de residencia vs. municipio de defunción');
    await expect(page.locator('#statisticsComparisonControl')).toBeVisible();
    await expect(page.locator('#statisticsComparisonControl')).toContainText('Municipio de residencia vs. municipio de defunción');
    await page.locator('#statisticsComparisonTrigger').click();
    await expect(page.locator('#statisticsComparisonMenu')).toBeVisible();
    await expect(page.locator('#statisticsComparisonMenu .statistics-heading-menu__group')).toHaveText([
        'Residencia vs. defunción',
        'Cruces de variables',
    ]);
    await page.keyboard.press('Escape');
    await expect(page.locator('#statisticsComparisonMenu')).toBeHidden();
    await page.locator('#statisticsComparisonTrigger').evaluate(element => element.blur());
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
    await expect.poll(async () => {
        const current = await chartState(page);
        return {
            labels: current.labels,
            rotation: current.rotation,
            width: current.width,
            series: current.series,
        };
    }).toEqual({
        labels: chartBefore.labels,
        rotation: chartBefore.rotation,
        width: chartBefore.width,
        series: chartBefore.series,
    });
});

test('pagina las barras horizontales extensas y conserva columnas en una hoja', async ({ page }) => {
    await page.getByRole('tab', { name: 'Vista' }).click();
    const limits = page.locator('#chartLimitButtons');
    await limits.getByRole('button', { name: /Mostrar todas las categorías/ }).click();
    await expect.poll(async () => (await chartState(page)).labels.length).toBe(44);
    expect(await page.evaluate(() => getHorizontalPdfPagination(currentEchartsInstance))).toBeNull();

    await page.locator('#municipalityChartTypeButtons')
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
