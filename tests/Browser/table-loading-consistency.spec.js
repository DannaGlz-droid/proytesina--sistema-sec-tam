import { expect, test } from '@playwright/test';

test('el skeleton de una tabla no hereda el hover de las filas de datos', async ({ page }) => {
    await page.goto('/usuario/gestion-de-usuarios');

    await page.evaluate(() => {
        const fixture = document.createElement('table');
        fixture.id = 'table-loading-hover-fixture';
        fixture.className = 'app-data-table';
        fixture.innerHTML = `
            <tbody>
                <tr data-real-row><td>Usuario real</td></tr>
                <tr data-loading-row>
                    <td class="dt-empty">
                        <div class="users-table-skeleton" role="status" aria-label="Cargando usuarios">
                            <div></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        `;
        document.body.appendChild(fixture);
    });

    const realCell = page.locator('#table-loading-hover-fixture [data-real-row] td');
    const loadingCell = page.locator('#table-loading-hover-fixture [data-loading-row] td');
    const background = locator => locator.evaluate(element => getComputedStyle(element).backgroundColor);

    const realRestingBackground = await background(realCell);
    await realCell.hover();
    await page.waitForTimeout(200);
    expect(await background(realCell)).not.toBe(realRestingBackground);

    const loadingRestingBackground = await background(loadingCell);
    await loadingCell.hover();
    await page.waitForTimeout(200);
    expect(await background(loadingCell)).toBe(loadingRestingBackground);
    await expect(page.locator('#table-loading-hover-fixture [data-loading-row]')).toHaveCSS('cursor', 'default');
});

test('una fila seleccionada conserva un resaltado distinto incluso con hover', async ({ page }) => {
    await page.goto('/usuario/gestion-de-usuarios');

    await page.evaluate(() => {
        const fixture = document.createElement('div');
        fixture.className = 'users-management-page';
        fixture.innerHTML = `
            <div class="users-table-card">
                <table id="users-table" class="app-data-table">
                    <tbody>
                        <tr data-unselected><td>Sin seleccionar</td></tr>
                        <tr class="is-selected" aria-selected="true" data-selected><td>Seleccionada</td></tr>
                    </tbody>
                </table>
            </div>
        `;
        document.body.appendChild(fixture);
    });

    const unselectedCell = page.locator('[data-unselected] td');
    const selectedRow = page.locator('[data-selected]');
    const selectedCell = selectedRow.locator('td');
    const background = locator => locator.evaluate(element => getComputedStyle(element).backgroundColor);

    expect(await background(selectedCell)).not.toBe(await background(unselectedCell));
    await selectedRow.hover();
    expect(await background(selectedCell)).not.toBe(await background(unselectedCell));
    await expect(selectedRow).toHaveAttribute('aria-selected', 'true');
});

test('el panel de filtros de usuarios se integra al flujo de la tabla en móvil', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/usuario/gestion-de-usuarios');

    const toggle = page.locator('#usersFilterToggle');
    const panel = page.locator('#usersFilterPanel');

    await toggle.click();

    await expect(toggle).toHaveAttribute('aria-expanded', 'true');
    await expect(panel).toBeVisible();
    await expect(panel).toHaveCSS('position', 'static');

    const box = await panel.boundingBox();
    const searchBox = await page.locator('.users-filter-search').boundingBox();
    expect(box).not.toBeNull();
    expect(searchBox).not.toBeNull();
    expect(box.x).toBeGreaterThanOrEqual(0);
    expect(box.x + box.width).toBeLessThanOrEqual(390);
    expect(box.y).toBeGreaterThanOrEqual(searchBox.y + searchBox.height);

    await expect(
        page.locator('#usersFilterPanel select#distrito'),
    ).toHaveCSS('font-size', '16px');
});

