import { readFileSync } from 'node:fs';
import path from 'node:path';

const PERIOD = {
    start_date: '2024-12-29',
    end_date: '2025-12-29',
    years: [],
    months: [],
    data_start: '2024-12-29',
    data_end: '2025-12-29',
    is_default: true,
    is_all_time: false,
};

const SOURCE_SUMMARY = {
    imports_count: 2,
    manual_records: 11,
    total_records: 798,
    imports: [
        { id: 1, name: 'defunciones-2025.xlsx', records: 512, imported_at: '2025-09-20T12:00:00Z' },
        { id: 2, name: 'actualizacion-semanal.csv', records: 275, imported_at: '2025-09-25T12:00:00Z' },
    ],
};

const MUNICIPALITIES = [
    ['Reynosa', 169], ['Victoria', 94], ['Otro', 74], ['Nuevo Laredo', 66],
    ['Tampico', 62], ['Matamoros', 55], ['Mante', 38], ['Madero', 34],
    ['Altamira', 24], ['Río Bravo', 22], ['San Fernando', 17], ['Soto la Marina', 15],
    ['Güémez', 13], ['Hidalgo', 13], ['Padilla', 13], ['Tula', 11],
    ['González', 11], ['Aldama', 7], ['Llera', 7], ['Valle Hermoso', 6],
    ['Xicoténcatl', 6], ['Abasolo', 5], ['Villagrán', 5], ['Jaumave', 4],
    ['Ocampo', 3], ['Casas', 2], ['Gómez Farías', 2], ['Mainero', 2],
    ['Antiguo Morelos', 1], ['Burgos', 1], ['Bustamante', 1], ['Camargo', 1],
    ['Cruillas', 1], ['Guerrero', 1], ['Gustavo Díaz Ordaz', 1], ['Jiménez', 1],
    ['Méndez', 1], ['Mier', 1], ['Miguel Alemán', 1], ['Miquihuana', 1],
    ['Nuevo Morelos', 1], ['Palmillas', 1], ['San Carlos', 1], ['San Nicolás', 1],
];

const DISTRICTS = [
    ['IV · Reynosa', 179], ['I · Victoria', 137], ['II · Tampico', 86],
    ['V · Nuevo Laredo', 76], ['XII · Altamira', 72], ['III · Matamoros', 65],
    ['VI · Mante', 59], ['X · Valle Hermoso', 40], ['XI · Padilla', 29],
    ['VIII · Jaumave', 24], ['VII · San Fernando', 18], ['IX · Miguel Alemán', 13],
];

const PLACES = [
    ['Vía pública', 289], ['Servicios IMSS-Bienestar', 140], ['Otro lugar', 119],
    ['IMSS', 103], ['Hogar', 68], ['Unidad médica privada', 29], ['ISSSTE', 18],
    ['Secretaría de Salud', 8], ['No especificado', 6], ['Pemex', 6],
    ['IMSS-Bienestar', 3], ['Otra unidad pública', 3], ['Se ignora', 2],
    ['SEDENA', 2], ['Semar', 2],
];

const AGE_DATA = [
    { range: '<5 años', total: 30, top_causes: { 'Otros accidentes': 22, 'Ahogamiento residencia': 6, 'Vehículo de motor residencia': 2 } },
    { range: '5-19 años', total: 52, top_causes: { 'Vehículo de motor residencia': 19, 'Otros accidentes': 19, 'Peatón residencia': 6 } },
    { range: '20-64 años', total: 528, top_causes: { 'Otros accidentes': 255, 'Vehículo de motor residencia': 147, 'Peatón residencia': 43 } },
    { range: '65+ años', total: 188, top_causes: { 'Otros accidentes': 122, 'Peatón residencia': 20, 'Vehículo de motor residencia': 17 } },
];

function basePayload(total = 798) {
    return {
        filtered_total: total,
        matched_total: total,
        excluded_total: 0,
        total,
        period: PERIOD,
        quality: { excluded_total: 0, required_fields: [] },
        source_summary: SOURCE_SUMMARY,
    };
}

function rankedPayload(type, entries, availableCategories, rankingLabel, limit, requiredField) {
    const visible = limit ? entries.slice(0, limit) : entries;
    const displayedTotal = visible.reduce((sum, entry) => sum + entry[1], 0);

    return {
        ...basePayload(),
        type,
        labels: visible.map(entry => entry[0]),
        counts: visible.map(entry => entry[1]),
        displayed_total: displayedTotal,
        displayed_categories: visible.length,
        available_categories: availableCategories,
        coverage_percentage: Number(((displayedTotal / 798) * 100).toFixed(1)),
        omitted_total: Math.max(0, 798 - displayedTotal),
        ranking_label: rankingLabel,
        quality: { excluded_total: 0, required_fields: [requiredField] },
        drilldown: visible.map((entry, index) => ({
            label: entry[0],
            filter_key: `${type}_ids`,
            filter_value: index + 1,
        })),
    };
}

function chartPayload(type, searchParams) {
    const parsedLimit = Number(searchParams.get('limit'));
    const limit = Number.isFinite(parsedLimit) && parsedLimit > 0 ? parsedLimit : null;

    if (type === 'municipios') {
        if (searchParams.get('measure') === 'rate' && searchParams.get('municipio_type') === 'residencia') {
            const visible = limit ? MUNICIPALITIES.slice(0, limit) : MUNICIPALITIES;
            const populations = visible.map((_, index) => 50000 + (index * 2500));
            return {
                ...rankedPayload('municipios', visible, 44, 'municipios', null, 'Municipio de residencia'),
                measure: 'rate',
                rates: visible.map((entry, index) => Number(((entry[1] / populations[index]) * 100000).toFixed(1))),
                populations,
                denominator_year: 2025,
                population_source: { name: 'CONAPO', version: 'Proyecciones municipales 1990-2040' },
                period: { ...PERIOD, start_date: '2025-01-01', end_date: '2025-12-31', is_default: false },
            };
        }
        return rankedPayload('municipios', MUNICIPALITIES, 44, 'municipios', limit, 'Municipio de defunción');
    }
    if (type === 'distritoes' || type === 'jurisdicciones') {
        return rankedPayload('distritoes', DISTRICTS, 12, 'distritos', limit, 'Distrito de defunción');
    }
    if (type === 'lugares') {
        return rankedPayload('lugares', PLACES, 15, 'lugares', limit, 'Lugar de defunción');
    }
    if (type === 'edades') {
        return {
            ...basePayload(),
            type: 'edades',
            labels: AGE_DATA.map(item => item.range),
            counts: AGE_DATA.map(item => item.total),
            data_with_causes: AGE_DATA,
            displayed_total: 798,
            displayed_categories: 4,
            available_categories: 4,
            coverage_percentage: 100,
            omitted_total: 0,
            ranking_label: 'grupos de edad',
            quality: { excluded_total: 0, required_fields: ['Edad'] },
        };
    }
    if (type === 'genero') {
        return {
            ...basePayload(), type: 'genero', labels: ['Masculino', 'Femenino'], counts: [607, 191],
            displayed_total: 798, displayed_categories: 2, available_categories: 2,
            coverage_percentage: 100, omitted_total: 0, ranking_label: 'categorías',
        };
    }
    if (type === 'tendencias') {
        return {
            ...basePayload(), type: 'tendencias',
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            counts: [68, 62, 70, 66, 67, 57, 64, 60, 67, 69, 67, 81],
            displayed_total: 798, displayed_categories: 12, available_categories: 12,
            coverage_percentage: 100, omitted_total: 0, group_by: 'month', ranking_label: 'periodos',
        };
    }
    if (type === 'causas') {
        const causes = [
            ['Otros accidentes', 418], ['Vehículo motor residencia', 185], ['Peatón residencia', 69],
            ['Ahogamiento residencia', 57], ['Caídas accidentales', 33],
            ['Exposición a fuego y humo', 28], ['Envenenamiento residencia', 8],
        ];
        return rankedPayload('causas', causes, 7, 'causas', limit, 'Causa de defunción');
    }

    const labels = MUNICIPALITIES.slice(0, limit || 10).map(entry => entry[0]);
    const residence = [179, 103, 76, 65, 57, 50, 0, 35, 29, 31].slice(0, labels.length);
    const death = [169, 94, 66, 55, 62, 24, 74, 38, 34, 22].slice(0, labels.length);
    return {
        ...basePayload(),
        type: 'comparativa',
        labels,
        series: [
            { name: 'Municipio de residencia', data: residence },
            { name: 'Municipio de defunción', data: death },
        ],
        displayed_total: death.reduce((sum, value) => sum + value, 0),
        displayed_categories: labels.length,
        available_categories: 44,
        coverage_percentage: 85.2,
        omitted_total: 118,
        ranking_label: 'municipios',
        quality: { excluded_total: 0, required_fields: ['Municipio de residencia', 'Municipio de defunción'] },
    };
}

export async function installStatisticsMocks(page) {
    const localBrowserLibraries = new Map([
        ['echarts@5.5.1/dist/echarts.min.js', path.resolve('node_modules/echarts/dist/echarts.min.js')],
        ['html2canvas@1.4.1/dist/html2canvas.min.js', path.resolve('node_modules/html2canvas/dist/html2canvas.min.js')],
        ['jspdf@2.5.1/dist/jspdf.umd.min.js', path.resolve('node_modules/jspdf/dist/jspdf.umd.min.js')],
    ]);

    const fontAwesomeAssets = new Map([
        ['/font-awesome/6.4.0/css/all.min.css', {
            path: path.resolve('node_modules/@fortawesome/fontawesome-free/css/all.min.css'),
            contentType: 'text/css',
        }],
        ['/font-awesome/6.4.0/webfonts/fa-solid-900.woff2', {
            path: path.resolve('node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.woff2'),
            contentType: 'font/woff2',
        }],
        ['/font-awesome/6.4.0/webfonts/fa-regular-400.woff2', {
            path: path.resolve('node_modules/@fortawesome/fontawesome-free/webfonts/fa-regular-400.woff2'),
            contentType: 'font/woff2',
        }],
        ['/font-awesome/6.4.0/webfonts/fa-brands-400.woff2', {
            path: path.resolve('node_modules/@fortawesome/fontawesome-free/webfonts/fa-brands-400.woff2'),
            contentType: 'font/woff2',
        }],
    ]);

    await page.route(/^https:\/\//, route => {
        const requestUrl = route.request().url();
        const library = [...localBrowserLibraries.entries()]
            .find(([urlFragment]) => requestUrl.includes(urlFragment));

        if (library) {
            return route.fulfill({
                status: 200,
                contentType: 'application/javascript',
                body: readFileSync(library[1]),
            });
        }

        const fontAwesomeAsset = [...fontAwesomeAssets.entries()]
            .find(([urlFragment]) => requestUrl.includes(urlFragment));

        if (fontAwesomeAsset) {
            return route.fulfill({
                status: 200,
                contentType: fontAwesomeAsset[1].contentType,
                body: readFileSync(fontAwesomeAsset[1].path),
            });
        }

        return route.abort('blockedbyclient');
    });

    await page.route('**/api/default-date-range', route => route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ start_date: PERIOD.start_date, end_date: PERIOD.end_date }),
    }));

    await page.route('**/api/chart/**', route => {
        const url = new URL(route.request().url());
        const type = url.pathname.split('/').filter(Boolean).at(-1);
        return route.fulfill({
            status: 200,
            contentType: 'application/json',
            body: JSON.stringify(chartPayload(type, url.searchParams)),
        });
    });
}

export async function openStatistics(page) {
    const browserErrors = [];
    page.on('pageerror', error => browserErrors.push(error.message));
    page.on('console', message => {
        if (message.type() === 'error' && !message.text().includes('ERR_BLOCKED_BY_CLIENT')) {
            browserErrors.push(message.text());
        }
    });
    await installStatisticsMocks(page);
    await page.goto('/__e2e/estadisticas/graficas');
    try {
        await page.locator('#mainChart canvas').waitFor({ timeout: 10_000 });
    } catch (error) {
        const state = await page.evaluate(() => ({
            title: document.getElementById('chartTitle')?.textContent,
            echarts: typeof window.echarts,
            loadChart: typeof window.loadChart,
            chartHtml: document.getElementById('mainChart')?.innerHTML,
        }));
        throw new Error(`La gráfica no se inicializó: ${JSON.stringify({ state, browserErrors })}`);
    }
    await page.waitForFunction(() => {
        const chart = document.getElementById('mainChart');
        return Boolean(window.echarts?.getInstanceByDom(chart));
    });
}

export async function chartState(page) {
    return page.evaluate(() => {
        const chart = document.getElementById('mainChart');
        const instance = window.echarts.getInstanceByDom(chart);
        const option = instance?.getOption?.();
        if (!option) {
            return {
                labels: [],
                rotation: null,
                width: Math.round(chart.getBoundingClientRect().width),
                height: Math.round(chart.getBoundingClientRect().height),
                series: [],
            };
        }
        const xAxis = Array.isArray(option.xAxis) ? option.xAxis[0] : option.xAxis;
        const yAxis = Array.isArray(option.yAxis) ? option.yAxis[0] : option.yAxis;
        const categoryAxis = xAxis?.type === 'category' ? xAxis : yAxis;
        return {
            labels: categoryAxis?.data || [],
            rotation: Number(categoryAxis?.axisLabel?.rotate || 0),
            width: Math.round(chart.getBoundingClientRect().width),
            height: Math.round(chart.getBoundingClientRect().height),
            series: (option.series || []).map(item => item.name).filter(Boolean),
        };
    });
}

export function pngDimensions(buffer) {
    if (buffer.length < 24 || buffer.toString('ascii', 1, 4) !== 'PNG') {
        throw new Error('El archivo descargado no es un PNG válido.');
    }
    return { width: buffer.readUInt32BE(16), height: buffer.readUInt32BE(20) };
}

