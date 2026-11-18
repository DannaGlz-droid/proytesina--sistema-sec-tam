import { test, expect } from '@playwright/test';

const imports = [
    {
        id: 1,
        original_name: 'ACCIDENTES 2024 AL 16 DE DIC 2024.xlsx',
        status: 'completed',
        rows_total: 724,
        rows_imported: 724,
        rows_failed: 0,
        rows_skipped_duplicates: 0,
        rows_changed_existing: 0,
        is_reversed: false,
        created_at: '2026-11-06T16:31:00-06:00',
        user_id: 1,
        created_by: 'Carlos Enrique',
        created_by_first_last_name: 'Rodríguez',
        created_by_second_last_name: 'Nava',
        created_by_username: 'admin_local_test',
    },
    {
        id: 2,
        original_name: 'meses_invalidos.xlsx',
        status: 'completed',
        rows_total: 10,
        rows_imported: 2,
        rows_failed: 3,
        rows_skipped_duplicates: 1,
        rows_changed_existing: 4,
        is_reversed: false,
        created_at: '2026-10-26T18:39:00-06:00',
        user_id: 1,
        created_by: 'Carlos Enrique',
        created_by_first_last_name: 'Rodríguez',
        created_by_second_last_name: 'Nava',
        created_by_username: 'admin_local_test',
    },
    {
        id: 3,
        original_name: 'archivo_revertido_con_nombre_extenso_para_validar_el_ajuste.xlsx',
        status: 'completed',
        rows_total: 568,
        rows_imported: 558,
        rows_failed: 6,
        rows_skipped_duplicates: 0,
        rows_changed_existing: 4,
        is_reversed: true,
        created_at: '2026-08-24T18:14:00-06:00',
        user_id: 1,
        created_by: 'Carlos Enrique',
        created_by_first_last_name: 'Rodríguez',
        created_by_second_last_name: 'Nava',
        created_by_username: 'admin_local_test',
    },
    {
        id: 4,
        original_name: 'archivo_revertido_sin_cambios.xlsx',
        status: 'completed',
        rows_total: 724,
        rows_imported: 0,
        rows_failed: 0,
        rows_skipped_duplicates: 724,
        rows_changed_existing: 0,
        is_reversed: true,
        created_at: '2026-07-01T10:00:00-06:00',
        user_id: 1,
        created_by: 'Carlos Enrique',
        created_by_first_last_name: 'Rodríguez',
        created_by_second_last_name: 'Nava',
        created_by_username: 'admin_local_test',
    },
    {
        id: 5,
        original_name: 'archivo_con_columnas_invalidas.xlsx',
        status: 'failed',
        rows_total: 0,
        rows_imported: 0,
        rows_failed: 0,
        rows_skipped_duplicates: 0,
        rows_changed_existing: 0,
        failure_reason: 'Faltan columnas requeridas',
        is_reversed: false,
        created_at: '2026-06-30T09:00:00-06:00',
        user_id: 1,
        created_by: 'Carlos Enrique',
        created_by_first_last_name: 'Rodríguez',
        created_by_second_last_name: 'Nava',
        created_by_username: 'admin_local_test',
    },
];

async function openImportHistory(page, viewport, dataset = imports) {
    const browserErrors = [];
    page.on('pageerror', error => browserErrors.push(error.message));
    page.on('console', message => {
        if (message.type() === 'error') browserErrors.push(message.text());
    });
    await page.setViewportSize(viewport);
    await page.route('**/api/estadisticas/historial-importaciones**', route => route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
            ok: true,
            data: {
                data: dataset,
                current_page: 1,
                per_page: 5000,
                total: dataset.length,
            },
        }),
    }));
    await page.route('**/api/users/search**', route => {
        const importers = Array.from(new Map(dataset.map(item => {
            const fullName = [item.created_by, item.created_by_first_last_name, item.created_by_second_last_name].filter(Boolean).join(' ');
            return [String(item.user_id), {
                id: item.user_id,
                name: item.created_by,
                full_name: fullName,
                username: item.created_by_username,
                display_name: item.created_by_username ? `${fullName} (@${item.created_by_username})` : fullName,
            }];
        })).values());
        return route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify(importers) });
    });
    await page.goto('/estadisticas/historial-importaciones');
    const activeCount = dataset.filter(item => !item.is_reversed && item.status !== 'failed').length;
    try {
        await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(activeCount);
    } catch (error) {
        throw new Error(`${error.message}\nErrores del navegador: ${JSON.stringify(browserErrors)}`);
    }
}

test('el historial conserva sus controles y resume los resultados reales', async ({ page }) => {
    await openImportHistory(page, { width: 1440, height: 900 });

    await expect(page.locator('#imports-overview-corrections')).toHaveText('3');
    await expect(page.locator('#imports-overview-differences')).toHaveText('4');
    await expect(page.locator('#imports-overview-latest')).toHaveText('ACCIDENTES 2024 AL 16 DE DIC 2024.xlsx');
    await expect(page.getByRole('tab', { name: /Activas 2/ })).toHaveAttribute('aria-selected', 'true');
    await expect(page.getByRole('tab', { name: /Revertidas 2/ })).toBeVisible();
    await expect(page.getByRole('tab', { name: /Fallidas 1/ })).toBeVisible();
    await expect(page.getByRole('button', { name: /Filtros/ })).toBeVisible();
    await expect(page.getByRole('searchbox', { name: 'Buscar importaciones' })).toBeVisible();
    await expect(page.locator('#per-page-imports-button')).toContainText('Mostrar');
    const fileHeader = page.getByRole('columnheader', { name: 'Archivo' });
    const dateHeader = page.getByRole('columnheader', { name: 'Fecha de carga / responsable' });
    await expect(dateHeader).toBeVisible();
    await expect(dateHeader).toHaveAttribute('aria-sort', 'descending');
    const sortIndicators = await fileHeader.evaluate(element => ({
        ascending: getComputedStyle(element, '::before').content,
        descending: getComputedStyle(element, '::after').content,
    }));
    expect(sortIndicators.ascending).not.toBe('none');
    expect(sortIndicators.descending).not.toBe('none');
    await expect(page.locator('.imports-origin-date').first()).toContainText('06/11/2026');
    await expect(page.locator('.imports-origin-user').first()).toContainText('Carlos Enrique');
    await expect(page.locator('.imports-origin-user').first()).not.toContainText('@admin_local_test');
    await expect(page.locator('.imports-origin-user').first()).toHaveAttribute('aria-label', /@admin_local_test/);
    await expect(page.locator('#dt-info')).toContainText('Mostrando 1-2 de 2');
    await expect(page.locator('#dt-pagination')).toBeVisible();
    await expect(page.locator('#dt-pagination')).toContainText('Anterior1Siguiente');
    await expect(page.locator('#imports-tbody .row-check-import')).toHaveCount(0);
    await expect(page.locator('.imports-result-list')).toHaveCount(2);
    await expect(page.locator('.imports-result-badge')).toHaveCount(4);
    await expect(page.locator('.imports-result-text')).toHaveCount(1);
    await expect(page.locator('.imports-result-track')).toHaveCount(0);
    await expect(page.locator('.imports-file-icon')).toHaveCount(0);
    await expect(page.locator('.imports-result-list').nth(1)).toHaveAttribute(
        'aria-label',
        '3 por corregir, 4 con diferencias, 2 agregados, 1 ya existente',
    );
    await expect(page.getByRole('columnheader', { name: 'Resultado', exact: true })).toBeVisible();
    await expect(page.locator('#imports-tbody .imports-table-row').nth(1)).toHaveClass(/has-corrections/);
    await expect(page.locator('#imports-tbody .imports-status').nth(1)).toContainText('Completado');
    await expect(page.locator('#imports-tbody .imports-status').nth(1)).toHaveClass(/status-completed/);
    await expect(page.locator('#imports-tbody .imports-status-note').first()).toHaveText('Requiere corrección');
    await expect(page.locator('.imports-status-note')).toHaveCount(1);

    const correctionsFilter = page.locator('[data-import-overview-filter="corrections"]');
    const differencesFilter = page.locator('[data-import-overview-filter="differences"]');
    await correctionsFilter.click();
    await expect(correctionsFilter).toHaveAttribute('aria-pressed', 'true');
    await expect(correctionsFilter).toHaveClass(/is-filter-active/);
    await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(1);
    await expect(page.locator('#imports-tbody .imports-file-name')).toHaveText('meses_invalidos.xlsx');
    await expect(page.locator('#dt-info')).toContainText('Mostrando 1-1 de 1');
    await correctionsFilter.click();
    await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(2);

    await differencesFilter.click();
    await expect(differencesFilter).toHaveAttribute('aria-pressed', 'true');
    await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(1);
    await differencesFilter.click();
    await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(2);

    const activeFileLeft = (await page.locator('#imports-tbody .imports-file-column').first().boundingBox())?.x;
    const activeFileColor = await page.locator('#imports-tbody .imports-file-name').first().evaluate(element => getComputedStyle(element).color);

    await page.getByRole('tab', { name: /Revertidas 2/ }).click();
    await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(2);
    await expect(page.getByRole('columnheader', { name: 'Resultado original', exact: true })).toBeVisible();
    await expect(page.locator('#dt-info')).toContainText('Mostrando 1-2 de 2');
    const reversedFileLeft = (await page.locator('#imports-tbody .imports-file-column').first().boundingBox())?.x;
    expect(Math.abs((activeFileLeft || 0) - (reversedFileLeft || 0))).toBeLessThanOrEqual(1);
    await expect(page.locator('#imports-tbody .imports-file-name').first()).toHaveCSS('color', activeFileColor);
    await expect(page.locator('.imports-result-list').first()).toHaveClass(/is-reverted/);
    await expect(page.locator('.imports-result-list').first()).toHaveAttribute('aria-label', '6 pendientes descartados, 4 diferencias, 558 agregados');
    await expect(page.locator('.imports-result-list').first().locator('.imports-result-badge')).toHaveCount(0);
    const reversedRow = page.locator('#imports-tbody .imports-table-row').first();
    await expect(reversedRow).toHaveClass(/is-reversed/);
    await expect(reversedRow).not.toHaveClass(/has-corrections|has-differences/);
    await expect(reversedRow.locator('.row-check-import')).toBeEnabled();
    await expect(reversedRow.locator('[data-reverse-import]')).toHaveCount(0);
    await expect(reversedRow.getByText('Revisar y corregir')).toHaveCount(0);
    await expect(reversedRow.locator('[data-delete-import]')).toBeEnabled();
    await expect(reversedRow.locator('.imports-status-note')).toHaveText('Datos retirados');
    await expect(page.locator('#imports-tbody .imports-table-row').nth(1).locator('.imports-status-note')).toHaveText('No agregó datos');
    await page.locator('#select-all-imports').check();
    await expect(page.locator('#imports-tbody .row-check-import:checked')).toHaveCount(2);
    await expect(page.locator('#bulk-selected-count-imports')).toHaveText('2 seleccionadas');
    await expect(page.locator('#bulk-delete-imports')).toContainText('Eliminar del historial');

    await page.getByRole('tab', { name: /Fallidas 1/ }).click();
    await expect(page.locator('#bulk-selection-bar-imports')).toHaveClass(/hidden/);
    await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(1);
    await expect(page.getByRole('columnheader', { name: 'Motivo de falla', exact: true })).toBeVisible();
    const failedRow = page.locator('#imports-tbody .imports-table-row').first();
    await expect(failedRow).toHaveClass(/is-failed/);
    await expect(failedRow.locator('.imports-file-name')).toHaveCSS('color', activeFileColor);
    await expect(failedRow.locator('.row-check-import')).toBeEnabled();
    await expect(failedRow.locator('.imports-file-cell small')).toHaveCount(0);
    await expect(failedRow.locator('.imports-result-empty--failed')).toHaveText('Faltan columnas requeridas');
    await expect(failedRow.locator('.imports-result-empty--failed strong, .imports-result-empty--failed small')).toHaveCount(0);
    await expect(failedRow.locator('.imports-status')).toContainText('Fallido');
    await expect(failedRow.locator('.imports-status-note')).toHaveCount(0);
    const rowHeight = await page.locator('#imports-tbody .imports-table-row').first().evaluate(row => row.getBoundingClientRect().height);
    expect(rowHeight).toBeLessThanOrEqual(70);

    await page.getByRole('tab', { name: /Activas 2/ }).click();
    await fileHeader.press('Enter');
    await expect(fileHeader).toHaveAttribute('aria-sort', 'ascending');
    await expect(dateHeader).toHaveAttribute('aria-sort', 'none');
});

test('el resumen prioriza pendientes sobre una cifra de cero agregados', async ({ page }) => {
    const dataset = imports.map(item => item.id === 2
        ? { ...item, created_at: '2026-11-16T18:11:00-06:00', rows_imported: 0 }
        : item);

    await openImportHistory(page, { width: 1440, height: 900 }, dataset);

    await expect(page.locator('#imports-overview-latest')).toHaveText('meses_invalidos.xlsx');
    await expect(page.locator('#imports-overview-latest-detail')).toContainText('3 registros por corregir');
    await expect(page.locator('#imports-overview-latest-detail')).not.toContainText('0 registros agregados');
});

test('el resumen muestra el último intento aunque haya fallado', async ({ page }) => {
    const dataset = imports.map(item => item.id === 5
        ? { ...item, created_at: '2026-11-16T19:15:00-06:00' }
        : item);

    await openImportHistory(page, { width: 1440, height: 900 }, dataset);

    await expect(page.locator('.imports-overview-item--latest .imports-overview-label')).toHaveText('Última importación');
    await expect(page.locator('#imports-overview-latest')).toHaveText('archivo_con_columnas_invalidas.xlsx');
    await expect(page.locator('#imports-overview-latest-detail')).toContainText('Fallida · Faltan columnas requeridas');
});

test('los filtros cambian su contenido y su regla con cada pestaña', async ({ page }) => {
    await openImportHistory(page, { width: 1440, height: 900 });

    await page.locator('#importsFilterToggle').click();
    await expect(page.locator('[data-import-filter-view="active"]')).toBeVisible();
    await expect(page.locator('[data-import-filter-view="reversed"]')).toBeHidden();
    await expect(page.locator('[data-import-filter-view="failed"]')).toBeHidden();
    const userFilterSection = page.locator('[data-filter-section]').filter({ has: page.getByRole('button', { name: 'Usuario', exact: true }) });
    await userFilterSection.getByRole('button', { name: 'Usuario', exact: true }).click();
    await expect(page.locator('#usuarioImports option:checked')).toHaveText('Todos los usuarios');
    await expect(page.locator('#usuarioImports option[value="1"]')).toContainText('Carlos Enrique Rodríguez Nava');

    await page.getByRole('button', { name: 'Resultado', exact: true }).click();
    await page.locator('#importsFilterPanel').getByText('Requiere corrección', { exact: true }).click();
    await page.locator('#aplicarFiltrosImportaciones').click();
    await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(1);
    await expect(page.locator('#importsFilterChips')).toContainText('Resultado: requiere corrección');

    await page.getByRole('tab', { name: /Revertidas 2/ }).click();
    await expect(page.locator('#importsFilterChips')).not.toContainText('Resultado: requiere corrección');
    await page.locator('#importsFilterToggle').click();
    await expect(page.locator('[data-import-filter-view="active"]')).toBeHidden();
    await expect(page.locator('[data-import-filter-view="reversed"]')).toBeVisible();
    await page.getByRole('button', { name: 'Efecto de la reversión', exact: true }).click();
    await page.locator('#importsFilterPanel').getByText('No había datos aplicados', { exact: true }).click();
    await page.locator('#aplicarFiltrosImportaciones').click();
    await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(1);
    await expect(page.locator('#imports-tbody .imports-file-name')).toHaveText('archivo_revertido_sin_cambios.xlsx');

    await page.getByRole('tab', { name: /Fallidas 1/ }).click();
    await page.locator('#importsFilterToggle').click();
    await expect(page.locator('[data-import-filter-view="reversed"]')).toBeHidden();
    await expect(page.locator('[data-import-filter-view="failed"]')).toBeVisible();
    await page.getByRole('button', { name: 'Motivo de falla', exact: true }).click();
    await page.locator('[data-import-filter-view="failed"] .ts-control').click();
    await page.locator('[data-import-filter-view="failed"] .ts-dropdown .option', { hasText: 'Columnas requeridas faltantes' }).click();
    await page.locator('#aplicarFiltrosImportaciones').click();
    await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(1);
    await expect(page.locator('#importsFilterChips')).toContainText('Motivo: Columnas requeridas faltantes');
});

test('la reversión explica el alcance y bloquea importaciones anteriores', async ({ page }) => {
    await openImportHistory(page, { width: 1440, height: 900 });

    const rows = page.locator('#imports-tbody .imports-table-row');
    await expect(page.getByText('Revertir primero')).toHaveCount(0);

    await rows.nth(1).locator('[data-import-menu-toggle]').click();
    const blockedReverseAction = page.getByRole('menuitem', { name: /Revertir, bloqueado/ });
    await expect(blockedReverseAction).toBeVisible();
    await expect(blockedReverseAction.locator('i')).toHaveClass(/fa-lock/);
    await blockedReverseAction.click();
    await expect(page.locator('#report-confirm-title')).toHaveText('No se puede revertir todavía');
    await expect(page.locator('#report-confirm-description')).toContainText('ACCIDENTES 2024 AL 16 DE DIC 2024.xlsx');
    await expect(page.locator('#report-confirm-description')).toContainText('consistencia del historial');
    await expect(page.locator('#report-confirm-description')).toContainText('cortes acumulativos');
    await expect(page.locator('#report-confirm-cancel')).toBeHidden();
    await page.locator('#report-confirm-accept').click();

    await rows.first().locator('[data-import-menu-toggle]').click();
    await page.getByRole('menuitem', { name: 'Revertir' }).click();
    await expect(page.locator('#report-confirm-title')).toHaveText('Revertir importación');
    await expect(page.locator('#report-confirm-question')).toContainText('ACCIDENTES 2024 AL 16 DE DIC 2024.xlsx');
    await expect(page.locator('#report-confirm-description')).toContainText('724 registros agregados');
    await expect(page.locator('#report-confirm-description')).toContainText('modificaciones posteriores');
    await expect(page.locator('#report-confirm-cancel')).toBeVisible();
    await page.locator('#report-confirm-cancel').click();
});

test('el historial convierte las filas en tarjetas sin desbordamiento horizontal en móvil', async ({ page }) => {
    await openImportHistory(page, { width: 390, height: 844 });

    const firstRow = page.locator('#imports-tbody .imports-table-row').first();
    await expect(firstRow).toHaveCSS('display', 'grid');
    await expect(firstRow.locator('.imports-file-column')).toBeVisible();
    await expect(firstRow.locator('.imports-origin-column')).toBeVisible();
    await expect(firstRow.locator('.imports-result-column')).toBeVisible();
    await expect(firstRow.locator('.imports-status-cell')).toBeVisible();
    await expect(firstRow.locator('[data-import-menu-toggle]')).toBeVisible();
    await expect(firstRow.locator('.row-check-import')).toHaveCount(0);

    const overflow = await page.locator('.imports-table-scroll').evaluate(element => ({
        clientWidth: element.clientWidth,
        scrollWidth: element.scrollWidth,
    }));
    expect(overflow.scrollWidth).toBeLessThanOrEqual(overflow.clientWidth + 1);
});
