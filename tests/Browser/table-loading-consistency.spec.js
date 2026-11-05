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
        fixture.className = 'users-management-page ui-data-table';
        fixture.innerHTML = `
            <div class="users-table-card">
                <table id="users-table" class="ui-data-table__table app-data-table">
                    <tbody>
                        <tr data-unselected><td>Sin seleccionar</td></tr>
                        <tr aria-selected="true" data-selected><td><input type="checkbox" checked> Seleccionada</td></tr>
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

    await expect(selectedCell).toHaveCSS('background-color', 'rgb(253, 242, 245)');
    await expect(selectedCell).toHaveCSS('box-shadow', /rgb\(117, 11, 53\)/);
    expect(await background(selectedCell)).not.toBe(await background(unselectedCell));
    await selectedRow.hover();
    await expect(selectedCell).toHaveCSS('background-color', 'rgb(250, 232, 238)');
    expect(await background(selectedCell)).not.toBe(await background(unselectedCell));
    await expect(selectedRow).toHaveAttribute('aria-selected', 'true');
});

test('filtros activos y seleccion conservan separador y ritmo vertical del estado normal', async ({ page }) => {
    await page.goto('/usuario/gestion-de-usuarios');

    await page.evaluate(() => {
        const fixture = document.createElement('section');
        fixture.id = 'selection-spacing-fixture';
        fixture.className = 'users-management-page';
        fixture.innerHTML = `
            <div data-state="normal" class="ui-data-table users-table-card">
                <div class="ui-data-table__toolbar app-table-toolbar"></div>
                <div class="ui-data-table__bulk-actions app-table-bulk-inline flex items-center">
                    <span class="app-table-selection-count">1 seleccionado</span>
                </div>
                <div class="ui-data-table__viewport app-table-shell">
                    <table id="users-table" class="ui-data-table__table app-data-table"><thead><tr><th>Usuario</th></tr></thead></table>
                </div>
            </div>
            <div data-state="filtered" class="ui-data-table users-table-card">
                <div class="ui-data-table__toolbar app-table-toolbar">
                    <div class="users-filter-card">
                        <form class="users-filter-form">
                            <div class="ui-data-table__filter-chips users-filter-chips"><span class="users-filter-chip">Rol: Coordinador</span></div>
                        </form>
                    </div>
                </div>
                <div class="ui-data-table__bulk-actions app-table-bulk-inline flex items-center">
                    <span class="app-table-selection-count">1 seleccionado</span>
                </div>
                <div class="ui-data-table__viewport app-table-shell">
                    <table id="users-table" class="ui-data-table__table app-data-table"><thead><tr><th>Usuario</th></tr></thead></table>
                </div>
            </div>
        `;
        document.body.appendChild(fixture);
    });

    const normal = page.locator('#selection-spacing-fixture [data-state="normal"]');
    const filtered = page.locator('#selection-spacing-fixture [data-state="filtered"]');
    const toolbar = filtered.locator('.ui-data-table__toolbar');
    const chips = filtered.locator('.ui-data-table__filter-chips');
    const selection = filtered.locator('.ui-data-table__bulk-actions');
    const headerCell = filtered.locator('.ui-data-table__table thead th');

    await expect(toolbar).toHaveCSS('border-bottom-width', '1px');
    await expect(toolbar).toHaveCSS('border-bottom-style', 'solid');
    await expect(toolbar).toHaveCSS('border-bottom-color', 'rgb(229, 231, 235)');
    await expect(chips).toHaveCSS('border-bottom-width', '1px');
    await expect(chips).toHaveCSS('border-bottom-style', 'solid');
    await expect(chips).toHaveCSS('border-bottom-color', 'rgb(229, 231, 235)');
    await expect(selection).toHaveCSS('border-top-width', '0px');
    await expect(selection).toHaveCSS('border-bottom-width', '0px');
    await expect(headerCell).toHaveCSS('border-top-width', '1px');
    await expect(headerCell).toHaveCSS('border-top-style', 'solid');

    const spacing = async (container) => container.evaluate((element) => {
        const bar = element.querySelector('.ui-data-table__bulk-actions').getBoundingClientRect();
        const count = element.querySelector('.app-table-selection-count').getBoundingClientRect();
        return {
            height: bar.height,
            top: count.top - bar.top,
            bottom: bar.bottom - count.bottom,
        };
    });

    const normalSpacing = await spacing(normal);
    const filteredSpacing = await spacing(filtered);

    expect(filteredSpacing.height - normalSpacing.height).toBeCloseTo(3.2, 1);
    expect(filteredSpacing.top - normalSpacing.top).toBeCloseTo(3.2, 1);
    expect(filteredSpacing.bottom).toBeCloseTo(normalSpacing.bottom, 5);
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

test('el selector compartido de filas conserva su estado y navegación por teclado', async ({ page }) => {
    await page.goto('/usuario/gestion-de-usuarios');

    const filterToggle = page.locator('#usersFilterToggle');
    const filterPanel = page.locator('#usersFilterPanel');
    const button = page.locator('#dt-per-page-button');
    const menu = page.locator('#dt-per-page-menu');
    const label = page.locator('#dt-per-page-label');

    await filterToggle.click();
    const filterActiveStyle = await filterToggle.evaluate(element => ({
        borderColor: getComputedStyle(element).borderColor,
        boxShadow: getComputedStyle(element).boxShadow,
    }));

    await button.click();
    const pageSizeActiveStyle = await button.evaluate(element => ({
        borderColor: getComputedStyle(element).borderColor,
        boxShadow: getComputedStyle(element).boxShadow,
    }));

    expect(pageSizeActiveStyle).toEqual(filterActiveStyle);
    await expect(filterPanel).toBeHidden();
    await expect(button).toHaveAttribute('aria-expanded', 'true');
    await expect(menu).toBeVisible();

    await page.locator('#dt-per-page-menu [data-value="10"]').click();
    await expect(label).toHaveText('10');
    await expect(menu).toBeHidden();

    await button.click();
    await page.keyboard.press('Escape');
    await expect(button).toBeFocused();
    await expect(button).toHaveAttribute('aria-expanded', 'false');

    await filterToggle.click();
    await expect(menu).toBeHidden();
});

test('el menu compartido de la ultima fila evita cubrir el pie de tabla', async ({ page }) => {
    await page.goto('/usuario/gestion-de-usuarios');

    await page.evaluate(() => {
        const fixture = document.createElement('section');
        fixture.id = 'row-actions-boundary-fixture';
        fixture.className = 'ui-data-table users-table-card';
        fixture.innerHTML = `
            <div class="ui-data-table__viewport">
                <table id="row-actions-boundary-table" class="ui-data-table__table app-data-table">
                    <tbody>
                        <tr>
                            <td>Última fila</td>
                            <td class="dt-actions-cell">
                                <div class="users-row-actions">
                                    <button type="button" class="users-row-menu-button" aria-expanded="false">Acciones</button>
                                    <div class="users-row-menu hidden">
                                        <button type="button" class="users-row-menu-item">Editar</button>
                                        <button type="button" class="users-row-menu-item">Eliminar</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <footer class="ui-data-table__footer">Pie de tabla</footer>
        `;
        document.body.appendChild(fixture);
        window.AppDataTable.initRowActions({
            table: fixture.querySelector('#row-actions-boundary-table'),
        });

        // Gestión de usuarios suppresses button bubbling so row expansion is
        // not triggered. The shared menu must still receive the click.
        fixture.querySelector('tbody').addEventListener('click', (event) => {
            if (event.target.closest('button')) event.stopPropagation();
        });
    });

    const trigger = page.locator('#row-actions-boundary-fixture .users-row-menu-button');
    await expect(page.locator('#row-actions-boundary-fixture .dt-actions-cell')).toHaveCSS('width', '64px');
    await expect(page.locator('#row-actions-boundary-fixture .users-row-actions')).toHaveCSS('justify-content', 'center');
    await trigger.scrollIntoViewIfNeeded();
    await trigger.click();

    const menu = page.locator('body > .users-row-menu:not(.hidden)');
    await expect(menu).toBeVisible();
    await expect(menu).toHaveClass(/opens-upward/);

    const triggerBox = await trigger.boundingBox();
    const menuBox = await menu.boundingBox();
    const footerBox = await page.locator('#row-actions-boundary-fixture .ui-data-table__footer').boundingBox();

    expect(triggerBox).not.toBeNull();
    expect(menuBox).not.toBeNull();
    expect(footerBox).not.toBeNull();
    expect(menuBox.y + menuBox.height).toBeLessThanOrEqual(triggerBox.y);
    expect(menuBox.y + menuBox.height).toBeLessThan(footerBox.y);

    await page.keyboard.press('Escape');
    await expect(trigger).toBeFocused();
    await expect(menu).toBeHidden();
});

test('la tabla real conserva la geometria compartida de la columna de acciones', async ({ page }) => {
    await page.goto('/usuario/gestion-de-usuarios');

    const actionsHeader = page.locator('.ui-data-table__table thead th.dt-actions-cell');
    await expect(actionsHeader).toHaveCSS('width', '64px');
    await expect(actionsHeader).toHaveCSS('min-width', '64px');
    await expect(actionsHeader).toHaveCSS('max-width', '64px');
    await expect(actionsHeader).toHaveCSS('text-align', 'center');
});

test('un nombre de usuario largo muestra un recorte con puntos suspensivos', async ({ page }) => {
    await page.goto('/usuario/gestion-de-usuarios');

    await page.evaluate(() => {
        const fixture = document.createElement('span');
        fixture.id = 'long-username-fixture';
        fixture.className = 'users-username-badge';
        fixture.style.maxWidth = '7.5rem';
        fixture.innerHTML = '<span class="users-username-text">@mfernanda_hernandez_prueba</span>';
        document.body.appendChild(fixture);
    });

    const username = page.locator('#long-username-fixture .users-username-text');
    await expect(username).toHaveCSS('text-overflow', 'ellipsis');
    await expect(username).toHaveCSS('overflow', 'hidden');
    expect(await username.evaluate(element => element.scrollWidth > element.clientWidth)).toBe(true);
});

