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
];

async function openImportHistory(page, viewport) {
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
                data: imports,
                current_page: 1,
                per_page: 5000,
                total: imports.length,
            },
        }),
    }));
    await page.goto('/estadisticas/historial-importaciones');
    try {
        await expect(page.locator('#imports-tbody .imports-table-row')).toHaveCount(imports.length);
    } catch (error) {
        throw new Error(`${error.message}\nErrores del navegador: ${JSON.stringify(browserErrors)}`);
    }
}

test('el historial conserva sus controles y resume los resultados reales', async ({ page }) => {
    await openImportHistory(page, { width: 1440, height: 900 });

    await expect(page.locator('#imports-overview-corrections')).toHaveText('3');
    await expect(page.locator('#imports-overview-differences')).toHaveText('4');
    await expect(page.locator('#imports-overview-latest')).toHaveText('ACCIDENTES 2024 AL 16 DE DIC 2024.xlsx');
    await expect(page.getByRole('button', { name: /Filtros/ })).toBeVisible();
    await expect(page.getByRole('searchbox', { name: 'Buscar importaciones' })).toBeVisible();
    await expect(page.locator('#per-page-imports-button')).toContainText('Mostrar');
    await expect(page.getByRole('columnheader', { name: 'Fecha / cargado por' })).toBeVisible();
    await expect(page.locator('.imports-origin-date').first()).toContainText('6/11/2026');
    await expect(page.locator('.imports-origin-user').first()).toContainText('Carlos Enrique');
    await expect(page.locator('.imports-origin-user').first()).toContainText('@admin_local_test');
    await expect(page.locator('#dt-info')).toContainText('Mostrando 1-4 de 4');
    await expect(page.locator('.imports-result-list')).toHaveCount(4);
    await expect(page.locator('.imports-result-badge')).toHaveCount(4);
    await expect(page.locator('.imports-result-text')).toHaveCount(5);
    await expect(page.locator('.imports-result-track')).toHaveCount(0);
    await expect(page.locator('.imports-file-icon')).toHaveCount(0);
    await expect(page.locator('.imports-result-list').nth(1)).toHaveAttribute(
        'aria-label',
        '3 por corregir, 4 con diferencias, 2 agregados, 1 ya existentes',
    );
    await expect(page.locator('.imports-status-note')).toHaveCount(3);
    await expect(page.locator('#imports-tbody .imports-table-row').nth(1)).toHaveClass(/has-corrections/);
    await expect(page.locator('#imports-tbody .imports-status').nth(1)).toContainText('Completado');
    await expect(page.locator('#imports-tbody .imports-status').nth(1)).toHaveClass(/status-completed/);
    await expect(page.locator('#imports-tbody .imports-status-note').first()).toHaveText('Requiere corrección');
    await expect(page.locator('.imports-result-list').nth(2)).toHaveClass(/is-reverted/);
    await expect(page.locator('.imports-result-list').nth(2)).toHaveAttribute('aria-label', '6 por corregir, 4 con diferencias, 558 agregados originalmente');
    await expect(page.locator('.imports-result-list').nth(2).locator('.imports-result-badge')).toHaveCount(0);
    const reversedRow = page.locator('#imports-tbody .imports-table-row').nth(2);
    await expect(reversedRow).toHaveClass(/is-reversed/);
    await expect(reversedRow).not.toHaveClass(/has-corrections|has-differences/);
    await expect(reversedRow.locator('.row-check-import')).toBeEnabled();
    await expect(reversedRow.locator('[data-reverse-import]')).toHaveCount(0);
    await expect(reversedRow.getByText('Revisar y corregir')).toHaveCount(0);
    await expect(reversedRow.locator('[data-delete-import]')).toBeEnabled();
    await expect(reversedRow.locator('.imports-status-note')).toHaveText('Datos retirados');
    await expect(page.locator('#imports-tbody .imports-table-row').nth(3).locator('.imports-status-note')).toHaveText('Sin cambios que retirar');
    const rowHeight = await page.locator('#imports-tbody .imports-table-row').first().evaluate(row => row.getBoundingClientRect().height);
    expect(rowHeight).toBeLessThanOrEqual(70);
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

    const overflow = await page.locator('.imports-table-scroll').evaluate(element => ({
        clientWidth: element.clientWidth,
        scrollWidth: element.scrollWidth,
    }));
    expect(overflow.scrollWidth).toBeLessThanOrEqual(overflow.clientWidth + 1);
});
