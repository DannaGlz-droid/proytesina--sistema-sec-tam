import { expect, test } from '@playwright/test';

const visualProperties = [
    'backgroundColor',
    'borderBottomColor',
    'borderBottomLeftRadius',
    'borderBottomWidth',
    'borderLeftColor',
    'borderLeftWidth',
    'borderRightColor',
    'borderRightWidth',
    'borderTopColor',
    'borderTopLeftRadius',
    'borderTopWidth',
    'boxShadow',
    'color',
    'fontFamily',
    'fontSize',
    'fontWeight',
    'height',
    'letterSpacing',
    'lineHeight',
    'marginBottom',
    'marginTop',
    'maxHeight',
    'overflowX',
    'overflowY',
    'paddingBottom',
    'paddingLeft',
    'paddingRight',
    'paddingTop',
    'scrollbarColor',
    'scrollbarWidth',
    'textTransform',
    'transitionDuration',
    'transitionProperty',
];

async function selectVisualContract(page, url) {
    await page.goto(url);
    const form = page.locator('.users-form-page .users-form-card form');
    await expect(form).toBeAttached();
    await form.evaluate((element) => {
        element.insertAdjacentHTML('beforeend', `
            <div data-visual-contract-target
                 class="ts-wrapper app-filter-select app-filter-select--form single has-items input-active focus dropdown-active"
                 style="position:fixed;left:-10000px;top:0;width:600px">
                <div class="ts-control">
                    <div class="item">Opción seleccionada</div>
                    <input placeholder="Seleccionar">
                </div>
                <div class="ts-dropdown single" style="display:block">
                    <div class="ts-dropdown-content">
                        <div class="option">Opción normal</div>
                        <div class="option selected">Opción seleccionada</div>
                        <div class="option active">Opción activa</div>
                    </div>
                </div>
            </div>
        `);
    });

    const wrapper = page.locator('[data-visual-contract-target]');

    return wrapper.evaluate((root, properties) => {
        const control = root.querySelector('.ts-control');
        const selectedItem = control.querySelector('.item');
        const input = control.querySelector('input');
        const dropdown = root.querySelector('.ts-dropdown');
        const content = root.querySelector('.ts-dropdown-content');
        const selectedOption = dropdown.querySelector('.option.selected');
        const activeOption = dropdown.querySelector('.option.active');
        const normalOption = dropdown.querySelector('.option:not(.selected):not(.active)');

        const read = (element, pseudo = null) => {
            const styles = getComputedStyle(element, pseudo);
            return Object.fromEntries(properties.map(property => [property, styles[property]]));
        };

        return {
            wrapper: read(root),
            control: read(control),
            arrow: read(control, '::after'),
            selectedItem: read(selectedItem),
            input: read(input),
            dropdown: read(dropdown),
            content: read(content),
            scrollbar: read(content, '::-webkit-scrollbar'),
            scrollbarTrack: read(content, '::-webkit-scrollbar-track'),
            scrollbarThumb: read(content, '::-webkit-scrollbar-thumb'),
            normalOption: read(normalOption),
            selectedOption: read(selectedOption),
            activeOption: read(activeOption),
        };
    }, visualProperties);
}

test('los selectores de ambos formularios comparten el mismo contrato visual', async ({ page }) => {
    for (const viewport of [
        { width: 1920, height: 1080 },
        { width: 390, height: 844 },
    ]) {
        await page.setViewportSize(viewport);

        const userSelect = await selectVisualContract(
            page,
            '/usuario/gestion-de-usuarios/registro',
        );
        const deathSelect = await selectVisualContract(
            page,
            '/estadisticas/registro',
        );
        const alcoholSelect = await selectVisualContract(
            page,
            '/reportes/registro/alcoholimetria',
        );

        expect(deathSelect).toEqual(userSelect);
        expect(alcoholSelect).toEqual(userSelect);
    }
});

test('los selectores de formulario usan placeholders contextuales', async ({ page }) => {
    const cases = [
        {
            url: '/usuario/gestion-de-usuarios/registro',
            placeholders: {
                position_select: 'Seleccione un cargo',
                district_id: 'Seleccione un distrito',
                role_id: 'Seleccione un rol',
            },
        },
        {
            url: '/estadisticas/registro',
            placeholders: {
                sex: 'Seleccione el sexo',
                residence_municipality_select: 'Seleccione un municipio',
                death_municipality_select: 'Seleccione un municipio',
                death_municipality_location: 'Seleccione un lugar',
                death_cause: 'Seleccione una causa',
            },
        },
        {
            url: '/reportes/registro/alcoholimetria',
            placeholders: {
                alcohol_municipality_select: 'Seleccione un municipio',
            },
        },
    ];

    for (const example of cases) {
        await page.goto(example.url);

        for (const [id, placeholder] of Object.entries(example.placeholders)) {
            const select = page.locator(`#${id}`);
            await expect(select).toHaveAttribute('data-placeholder', placeholder);
        }
    }

    await page.goto('/reportes/registro/alcoholimetria');
    const selectableDistrict = page.locator('#jurisdiction_select_alcohol');
    const assignedDistrict = page.locator('#jurisdiction_display_alcohol');
    expect(await selectableDistrict.count() + await assignedDistrict.count()).toBe(1);
    if (await selectableDistrict.count()) {
        await expect(selectableDistrict).toHaveAttribute('data-placeholder', 'Seleccione un distrito');
    } else {
        await expect(assignedDistrict).toBeDisabled();
        await expect(assignedDistrict).not.toHaveValue('');
    }
});

