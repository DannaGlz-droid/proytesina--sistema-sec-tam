@extends('layouts.principal')
@section('title', 'Estadísticas')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO Y BOTÓN EXTERNO -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Estadísticas</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    Visualice datos a través de gráficos interactivos. Utilice los filtros para personalizar su análisis.
                </p>
            </div>
            
            <!-- BOTÓN CENTRADO VERTICALMENTE -->
            <button class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto" id="descargarTodo">
                <i class="fas fa-download text-xs"></i>
                Descargar Todo
            </button>
        </div>

        <!-- Contenedor principal -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl bg-white bg-opacity-95 max-w-full shadow-md overflow-hidden">
            
            <div class="p-4 lg:p-6 pt-8 lg:pt-12">
                <!-- Layout principal: Filtros + Gráficos -->
                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <!-- Columna Izquierda - Filtros (ANCHO FIJO) -->
                    <div id="estadisticas-filtros" class="lg:w-80">
                        {{-- Pasar las listas necesarias al componente de filtros para que use los datos reales de BD --}}
                        <x-filtros.estadisticas :municipalities="$municipalities ?? collect()" :causes="$causes ?? collect()" :jurisdictions="$jurisdictions ?? collect()" :sexes="$sexes ?? collect()" />
                    </div>

                    <!-- Columna Derecha - Gráficos (ESPACIO RESTANTE) -->
                    <div class="lg:flex-1">
                        
                        <!-- Categoría: Distribución Geográfica -->
                        <x-graficos.categoria 
                            titulo="Distribución Geográfica" 
                            icono="map-marked-alt"
                        >
                            <x-graficos.tarjeta 
                                titulo="Distribución por Municipios" 
                                :tipos="['bar', 'pie', 'doughnut']" 
                                tipoInicial="bar"
                                graficoId="municipioChart"
                            >
                                <!-- label moved to header controls to keep layout compact -->
                                <div class="chart-wrapper" style="height:320px; overflow:hidden; position:relative;">
                                    <canvas id="municipioChart" class="chart-canvas" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>
                            
                            <x-graficos.tarjeta 
                                titulo="Distribución por Jurisdicción" 
                                :tipos="['bar', 'doughnut']" 
                                tipoInicial="bar"
                                graficoId="jurisdiccionChart"
                            >
                                <div class="chart-wrapper" style="height:320px; overflow:hidden; position:relative;">
                                    <canvas id="jurisdiccionChart" class="chart-canvas" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>

                            <x-graficos.tarjeta 
                                titulo="Comparativa: Residencia vs Defunción" 
                                :tipos="['bar']" 
                                tipoInicial="bar"
                                graficoId="compareChart"
                            >
                                <div class="chart-wrapper" style="height:320px; overflow:hidden; position:relative;">
                                    <canvas id="compareChart" class="chart-canvas" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>
                        </x-graficos.categoria>

                        <!-- Categoría: Tendencia Temporal -->
                        <x-graficos.categoria 
                            titulo="Tendencia Temporal" 
                            icono="calendar-alt"
                        >
                            <x-graficos.tarjeta 
                                    titulo="Tendencia" 
                                    :tipos="['line', 'bar']" 
                                    tipoInicial="line"
                                    graficoId="tendenciaChart"
                                >
                                    <!-- Subtítulo dinámico: se actualizará según la granularidad seleccionada -->
                                    <div id="tendenciaSubtitle" class="text-sm text-gray-600 mb-2">Mensual</div>
                                    <div class="chart-wrapper" style="height:260px; overflow:hidden; position:relative;">
                                        <canvas id="tendenciaChart" class="chart-canvas" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                    </div>
                                </x-graficos.tarjeta>
                        </x-graficos.categoria>

                        <!-- Categoría: Distribución Demográfica -->
                        <x-graficos.categoria 
                            titulo="Distribución Demográfica" 
                            icono="users"
                            grid-cols="grid-cols-1 lg:grid-cols-2 gap-6"
                        >
                            <x-graficos.tarjeta 
                                titulo="Distribución por Edades" 
                                :tipos="['bar', 'pie']" 
                                tipoInicial="bar"
                                graficoId="edadChart"
                            >
                                <div class="chart-wrapper" style="height:260px; overflow:hidden; position:relative;">
                                    <canvas id="edadChart" class="chart-canvas" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>
                            
                            <x-graficos.tarjeta 
                                titulo="Distribución por Género" 
                                :tipos="['doughnut', 'pie', 'bar']" 
                                tipoInicial="doughnut"
                                graficoId="generoChart"
                            >
                                <div class="chart-wrapper" style="height:260px; overflow:hidden; position:relative;">
                                    <canvas id="generoChart" class="chart-canvas" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>
                        </x-graficos.categoria>

                        <!-- Categoría: Causas -->
                        <x-graficos.categoria 
                            titulo="Causas" 
                            icono="heartbeat"
                        >
                            <x-graficos.tarjeta 
                                titulo="Causas de Defunción" 
                                :tipos="['doughnut', 'pie', 'bar']" 
                                tipoInicial="doughnut"
                                graficoId="causaChart"
                            >
                                <div class="chart-wrapper" style="height:260px; overflow:hidden; position:relative;">
                                    <canvas id="causaChart" class="chart-canvas" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>
                        </x-graficos.categoria>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Data labels plugin para mostrar cifras directamente en los gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <!-- INCLUIR EL MODAL DE DESCARGAS -->
    @include('components.modal-descargas')

    <!-- Script para gráficos (ACTUALIZADO) -->
    <script>
    // Mapa de gráficos global para acceso desde el modal
    let charts = {};
    // Municipio kind selected: 'death' (defunción) or 'residence' (residencia)
    let selectedMunKind = 'residence';

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar gráficos
            const municipioCtx = document.getElementById('municipioChart').getContext('2d');
            const tendenciaCtx = document.getElementById('tendenciaChart').getContext('2d');
            const edadCtx = document.getElementById('edadChart').getContext('2d');
            const generoCtx = document.getElementById('generoChart').getContext('2d');
            const causaCtx = document.getElementById('causaChart').getContext('2d');
            const jurisdiccionCtx = document.getElementById('jurisdiccionChart').getContext('2d');
            const compareCtx = document.getElementById('compareChart').getContext('2d');
            
            // Inicialmente no hay datos (se cargarán vía fetch según filtros)
            const sampleData = {
                municipios: [], municipioCounts: [],
                meses: [], mesCounts: [],
                edades: [], edadCounts: [],
                generos: [], generoCounts: [],
                causas: [], causaCounts: []
            };
            
            // Registrar el plugin de datalabels (muestra los números dentro/debajo de los elementos)
            try {
                if (typeof ChartDataLabels !== 'undefined') Chart.register(ChartDataLabels);
            } catch (e) {
                console.warn('chartjs-plugin-datalabels no disponible:', e);
            }

            // Crear gráficos (inicialmente vacíos)
            charts.municipioChart = new Chart(municipioCtx, {
                type: 'bar',
                data: {
                    labels: sampleData.municipios,
                    datasets: [{
                        label: 'Defunciones',
                        data: sampleData.municipioCounts,
                        backgroundColor: [
                            'rgba(97, 17, 50, 0.7)',
                            'rgba(64, 64, 65, 0.7)',
                            'rgba(139, 69, 19, 0.7)',
                            'rgba(101, 67, 33, 0.7)',
                            'rgba(119, 119, 119, 0.7)',
                            'rgba(150, 150, 150, 0.7)',
                            'rgba(180, 180, 180, 0.7)'
                        ],
                        borderColor: [
                            'rgba(97, 17, 50, 1)',
                            'rgba(64, 64, 65, 1)',
                            'rgba(139, 69, 19, 1)',
                            'rgba(101, 67, 33, 1)',
                            'rgba(119, 119, 119, 1)',
                            'rgba(150, 150, 150, 1)',
                            'rgba(180, 180, 180, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    // Top padding reduced so the legend doesn't create excessive empty space above the bars
                    layout: { padding: { top: 12, right: 8, left: 8, bottom: 8 } },
                    plugins: {
                        datalabels: {
                            color: '#fff',
                            anchor: 'center',
                            align: 'center',
                            clamp: true,
                            formatter: function(value) { return value; },
                            font: { weight: '600', size: 11 }
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            charts.tendenciaChart = new Chart(tendenciaCtx, {
                type: 'line',
                data: {
                    labels: sampleData.meses,
                    datasets: [{
                        label: 'Defunciones',
                        data: sampleData.mesCounts,
                        backgroundColor: 'rgba(97, 17, 50, 0.2)',
                        borderColor: 'rgba(97, 17, 50, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(97, 17, 50, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(97, 17, 50, 1)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            display: false // normalmente no mostramos etiquetas en líneas para no saturar
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            charts.edadChart = new Chart(edadCtx, {
                type: 'bar',
                data: {
                    labels: sampleData.edades,
                    datasets: [{
                        label: 'Personas',
                        data: sampleData.edadCounts,
                        backgroundColor: 'rgba(64, 64, 65, 0.7)',
                        borderColor: 'rgba(64, 64, 65, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            color: '#222',
                            anchor: 'end',
                            align: 'end',
                            formatter: function(value) { return value; },
                            font: { weight: '600', size: 11 }
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            charts.generoChart = new Chart(generoCtx, {
                type: 'doughnut',
                data: {
                    labels: sampleData.generos,
                    datasets: [{
                        data: sampleData.generoCounts,
                        backgroundColor: [
                            'rgba(97, 17, 50, 0.7)',
                            'rgba(64, 64, 65, 0.7)'
                        ],
                        borderColor: [
                            'rgba(97, 17, 50, 1)',
                            'rgba(64, 64, 65, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        datalabels: {
                            color: '#fff',
                            formatter: function(value, ctx) {
                                // Mostrar valor y porcentaje
                                const data = ctx.chart.data.datasets[0].data;
                                const sum = data.reduce((a, b) => a + b, 0) || 0;
                                const pct = sum ? Math.round((value / sum) * 100) : 0;
                                return value + ' (' + pct + '%)';
                            },
                            font: { weight: '700', size: 11 }
                        }
                    }
                }
            });
            
            charts.causaChart = new Chart(causaCtx, {
                type: 'doughnut',
                data: {
                    labels: sampleData.causas,
                    datasets: [{
                        data: sampleData.causaCounts,
                        backgroundColor: [
                            'rgba(97, 17, 50, 0.7)',
                            'rgba(64, 64, 65, 0.7)',
                            'rgba(139, 69, 19, 0.7)',
                            'rgba(101, 67, 33, 0.7)',
                            'rgba(119, 119, 119, 0.7)',
                            'rgba(150, 150, 150, 0.7)'
                        ],
                        borderColor: [
                            'rgba(97, 17, 50, 1)',
                            'rgba(64, 64, 65, 1)',
                            'rgba(139, 69, 19, 1)',
                            'rgba(101, 67, 33, 1)',
                            'rgba(119, 119, 119, 1)',
                            'rgba(150, 150, 150, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        datalabels: {
                            color: '#fff',
                            formatter: function(value, ctx) {
                                const data = ctx.chart.data.datasets[0].data;
                                const sum = data.reduce((a, b) => a + b, 0) || 0;
                                const pct = sum ? Math.round((value / sum) * 100) : 0;
                                return value + ' (' + pct + '%)';
                            },
                            font: { weight: '700', size: 11 }
                        }
                    }
                }
            });

            // Jurisdicción chart
            charts.jurisdiccionChart = new Chart(jurisdiccionCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Defunciones',
                        data: [],
                        backgroundColor: 'rgba(97, 17, 50, 0.7)',
                        borderColor: 'rgba(97, 17, 50, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: { color: '#222', anchor: 'end', align: 'end', formatter: v=>v, font: { weight: '600', size: 11 } },
                        legend: { position: 'top' }
                    },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Compare Residence vs Death chart (stacked)
            charts.compareChart = new Chart(compareCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [
                        { label: 'Residencia', data: [], backgroundColor: 'rgba(64, 64, 65, 0.7)' },
                        { label: 'Defunción', data: [], backgroundColor: 'rgba(97, 17, 50, 0.7)' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { datalabels: { color: '#222', font:{weight:'600'} }, legend:{position:'top'} },
                    scales: { x: { stacked: false }, y: { beginAtZero: true, stacked: false } }
                }
            });

            // Municipio kind toggle (Defunción / Residencia)
            const muniKindToggle = document.getElementById('municipioKindToggle');
            if (muniKindToggle) {
                muniKindToggle.querySelectorAll('.mun-kind-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        // reset visual state
                        muniKindToggle.querySelectorAll('.mun-kind-btn').forEach(b => {
                            // clear all possible background/text classes so we can set the desired inactive style
                            b.classList.remove('bg-[#611132]', 'text-white', 'bg-transparent', 'text-gray-600', 'bg-white');
                            // set inactive appearance
                            b.classList.add('bg-white', 'text-gray-600');
                        });

                                // set active appearance on clicked button
                                this.classList.remove('bg-white', 'text-gray-600');
                                this.classList.add('bg-[#611132]', 'text-white');

                        // save selected kind and reload charts
                        selectedMunKind = this.getAttribute('data-value') || 'death';

                        // Update legend label immediately so the UI reflects the choice
                        try {
                            if (charts && charts.municipioChart && charts.municipioChart.data && charts.municipioChart.data.datasets && charts.municipioChart.data.datasets[0]) {
                                charts.municipioChart.data.datasets[0].label = (selectedMunKind === 'residence') ? 'Defunciones (residencia)' : 'Defunciones (defunción)';
                                charts.municipioChart.update();
                            }
                        } catch (e) {
                            console.warn('No se pudo actualizar la etiqueta del chart inmediatamente:', e);
                        }

                        loadChartsDebounced();
                    });
                });

                // Initialize default active button (residence)
                const initBtn = muniKindToggle.querySelector('.mun-kind-btn[data-value="residence"]');
                if (initBtn) {
                    // ensure any conflicting classes removed
                    initBtn.classList.remove('bg-white','bg-transparent','text-gray-600');
                    initBtn.classList.add('bg-[#611132]', 'text-white');
                    selectedMunKind = 'residence';
                }
            }

            // Cambiar tipo de gráfico
            document.querySelectorAll('.chart-type-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chartType = this.getAttribute('data-chart-type');
                    const chartId = this.closest('.chart-card').querySelector('canvas').id;
                    const chart = charts[chartId];

                    if (chart) {
                        // Remover clase active de todos los botones del mismo grupo
                        this.parentElement.querySelectorAll('.chart-type-btn').forEach(b => {
                            b.classList.remove('active', 'bg-[#611132]', 'text-white');
                            b.classList.add('bg-transparent', 'text-gray-600');
                        });

                        // Agregar clase active al botón clickeado
                        this.classList.add('active', 'bg-[#611132]', 'text-white');
                        this.classList.remove('bg-transparent', 'text-gray-600');

                        // Recreate chart with appropriate options for the new type to avoid leftover gridlines
                        recreateChart(chartId, chartType);
                    }
                });
            });

            // Helper: return sensible default options per chart type to avoid leaving scale/grid artifacts
            function getDefaultOptionsForType(type) {
                const base = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        datalabels: { display: true, color: '#fff', font: { weight: '600' } }
                    }
                };

                if (type === 'pie' || type === 'doughnut') {
                    base.plugins.legend.position = 'right';
                    base.plugins.datalabels.formatter = function(value, ctx) {
                        const data = ctx.chart.data.datasets[0].data;
                        const sum = data.reduce((a,b)=>a+(Number(b)||0),0) || 0;
                        const pct = sum ? Math.round((value / sum) * 100) : 0;
                        return value.toLocaleString() + ' (' + pct + '%)';
                    };
                    // pie/doughnut do not use scales
                } else {
                    // bar/line/scatter: include scales and integer ticks
                    base.plugins.datalabels.formatter = function(value) { return Number(value).toLocaleString(); };
                    base.scales = {
                        x: { ticks: { autoSkip: true }, grid: { display: false } },
                        y: { beginAtZero: true, ticks: { callback: v => Math.round(v).toLocaleString() } }
                    };
                }

                return base;
            }

            // Recreate a chart instance with the chosen type; this avoids leftover gridlines when switching
            function recreateChart(chartId, newType) {
                try {
                    const old = charts[chartId];
                    if (!old) return;
                    const canvas = document.getElementById(chartId);
                    const ctx = canvas.getContext('2d');

                    // Deep clone data (labels + datasets) but not functions
                    const data = JSON.parse(JSON.stringify(old.data || { labels: [], datasets: [] }));

                    // Destroy old chart instance
                    try { old.destroy(); } catch (e) { /* ignore */ }

                    // Build default options for the new type
                    const options = getDefaultOptionsForType(newType);

                    // Register datalabels plugin if available
                    try { if (typeof ChartDataLabels !== 'undefined') Chart.register(ChartDataLabels); } catch (e) {}

                    // Create new chart
                    const cfg = { type: newType, data: data, options: options };
                    charts[chartId] = new Chart(ctx, cfg);

                    // Apply nice scaling for cartesian charts
                    if (newType === 'bar' || newType === 'line') {
                        try {
                            const max = Math.max(...(data.datasets[0].data.map(n => Number(n) || 0)));
                            applyNiceScaling(charts[chartId], max);
                        } catch (e) {}
                    }
                } catch (e) {
                    console.error('Error recreating chart', e);
                }
            }

            // Descargar gráficas individuales - AHORA ABRE EL MODAL
            document.querySelectorAll('.chart-download-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chartId = this.closest('.chart-card').querySelector('canvas').id;
                    window.mostrarModalDescargas('individual', chartId);
                });
            });

            // Descargar todo - AHORA ABRE EL MODAL
            document.getElementById('descargarTodo').addEventListener('click', function() {
                window.mostrarModalDescargas('todo');
            });

            // --- FETCH Y ACTUALIZACIÓN DINÁMICA SEGÚN FILTROS ---
            const chartsEndpoint = '{{ route('estadisticas.charts-data') }}';

            function mapSexLabel(s) {
                if (!s) return s;
                if (s === 'M' || s.toLowerCase() === 'm') return 'Hombre';
                if (s === 'F' || s.toLowerCase() === 'f') return 'Mujer';
                return s;
            }

            function normalizeDataset(labels, counts) {
                if (!Array.isArray(labels) || labels.length === 0) {
                    return { labels: ['Sin datos'], counts: [0] };
                }
                return { labels: labels, counts: counts };
            }

            // Calcular un stepSize "agradable" para el eje Y (1,2,5 * 10^n) para ~5-8 ticks
            function niceStep(maxValue, targetTicks = 6) {
                if (!isFinite(maxValue) || maxValue <= 0) return 1;
                const raw = maxValue / targetTicks;
                const pow = Math.pow(10, Math.floor(Math.log10(raw)));
                const mant = raw / pow;
                let niceMant;
                if (mant <= 1) niceMant = 1;
                else if (mant <= 2) niceMant = 2;
                else if (mant <= 5) niceMant = 5;
                else niceMant = 10;
                return niceMant * pow;
            }

            // Aplicar escala agradable a un Chart.js chart instance (asegura ticks enteros, formateados)
            function applyNiceScaling(chart, suggestedMax) {
                try {
                    if (!chart || !chart.options || !chart.options.scales || !chart.options.scales.y) return;
                    const y = chart.options.scales.y;
                    y.beginAtZero = true;
                    // compute step from suggestedMax
                    const step = niceStep(suggestedMax || (y.suggestedMax || 0));
                    y.ticks = y.ticks || {};
                    y.ticks.stepSize = step;
                    // force integer ticks and format with thousands separator
                    y.ticks.callback = function(value) {
                        // only show integers on axis
                        const v = Number(value);
                        if (!Number.isFinite(v)) return value;
                        // Show integer tick; avoid decimals
                        return Math.round(v).toLocaleString();
                    };
                    // Optionally set suggestedMax to a multiple of step so top tick is round
                    if (!y.suggestedMax || y.suggestedMax < suggestedMax) {
                        const top = Math.ceil((suggestedMax || 0) / step) * step;
                        y.suggestedMax = top;
                    }
                } catch (e) {
                    console.warn('applyNiceScaling error', e);
                }
            }

            // Helpers para formateo de fechas y periodos legibles
            function monthShortNameESP(m) {
                const names = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                return names[m-1] || '';
            }

            function formatDateDDMMYYYY(dstr) {
                if (!dstr) return '';
                const d = new Date(dstr);
                if (isNaN(d)) return dstr;
                const dd = String(d.getDate()).padStart(2,'0');
                const mm = String(d.getMonth()+1).padStart(2,'0');
                const yy = d.getFullYear();
                return `${dd}/${mm}/${yy}`;
            }

            function formatMonthYear(dstr) {
                // dstr may be '2025-03-01' or '2025-03' or '03' if only month number passed
                try {
                    const d = new Date(dstr);
                    if (!isNaN(d)) return `${monthShortNameESP(d.getMonth()+1)} ${d.getFullYear()}`;
                } catch(e) {}
                // fallback: try split
                if (/^\d{4}-\d{2}/.test(dstr)) {
                    const parts = dstr.split('-');
                    return `${monthShortNameESP(Number(parts[1]))} ${parts[0]}`;
                }
                return dstr;
            }

            function getReadablePeriodLabel(params) {
                // Inspect the UI controls to present a friendly label (handles quarter, month, year, multiple-months, custom)
                const dateRange = document.getElementById('dateRange')?.value;
                const year = document.getElementById('year')?.value;
                const month = document.getElementById('month')?.value;
                const selectedMonths = Array.from(document.querySelectorAll('.month-checkbox:checked')).map(i => Number(i.value));
                const quarter = document.getElementById('quarter')?.value;
                const startDate = document.getElementById('startDate')?.value;
                const endDate = document.getElementById('endDate')?.value;

                if (dateRange === 'quarter' && year && quarter) {
                    const q = Number(quarter);
                    const startMonth = (q - 1) * 3 + 1;
                    const endMonth = startMonth + 2;
                    const startLabel = `${monthShortNameESP(startMonth)} ${year}`;
                    const endLabel = `${monthShortNameESP(endMonth)} ${year}`;
                    return `Q${q} ${year} (${startLabel}–${endLabel})`;
                }

                if (dateRange === 'month' && year && month) {
                    // specific month
                    const mNum = Number(month);
                    return `${monthShortNameESP(mNum)} ${year}`;
                }

                if (dateRange === 'year' && year) {
                    return `${year}`;
                }

                if (dateRange === 'multiple-months' && year && selectedMonths.length) {
                    selectedMonths.sort((a,b)=>a-b);
                    const min = selectedMonths[0];
                    const max = selectedMonths[selectedMonths.length-1];
                    if (min === max) return `${monthShortNameESP(min)} ${year}`;
                    return `${monthShortNameESP(min)}–${monthShortNameESP(max)} ${year}`;
                }

                if (dateRange === 'custom' && startDate && endDate) {
                    // Decide how to present based on group_by (day/month/year)
                    const gb = params.group_by || 'month';
                    if (gb === 'day') return `${formatDateDDMMYYYY(startDate)} – ${formatDateDDMMYYYY(endDate)}`;
                    if (gb === 'month') {
                        // present like 'Ene 2024 – Jun 2024' using month-year
                        const s = new Date(startDate); const e = new Date(endDate);
                        return `${monthShortNameESP(s.getMonth()+1)} ${s.getFullYear()} – ${monthShortNameESP(e.getMonth()+1)} ${e.getFullYear()}`;
                    }
                    return `${new Date(startDate).getFullYear()} – ${new Date(endDate).getFullYear()}`;
                }

                // Fallback: if we have explicit start/end in params
                if (params.start_date && params.end_date) {
                    const s = new Date(params.start_date); const e = new Date(params.end_date);
                    // if same month/year, show month year
                    if (s.getFullYear() === e.getFullYear() && s.getMonth() === e.getMonth()) return `${monthShortNameESP(s.getMonth()+1)} ${s.getFullYear()}`;
                    // if span <= 365 show month-year range
                    const spanDays = Math.ceil((e - s) / (1000*60*60*24));
                    if (spanDays <= 365) return `${monthShortNameESP(s.getMonth()+1)} ${s.getFullYear()} – ${monthShortNameESP(e.getMonth()+1)} ${e.getFullYear()}`;
                    return `${s.getFullYear()} – ${e.getFullYear()}`;
                }

                return 'Todos los datos';
            }

            function updateChartsFromResponse(resp) {
                console.log('updateChartsFromResponse received:', resp);
                // Municipios
                const m = normalizeDataset(resp.municipios.labels, resp.municipios.counts);

                // Ajustar altura del wrapper dinámicamente según número de municipios (más etiquetas = más altura)
                try {
                    const muniWrapper = document.getElementById('municipioChart')?.closest('.chart-wrapper');
                    if (muniWrapper) {
                        const labelCount = Array.isArray(m.labels) ? m.labels.length : 0;
                        const base = 360; // altura por defecto (reducida para evitar exceso de espacio)
                        const extraPer = 16; // px extra por etiqueta adicional
                        const threshold = 10; // hasta 10 etiquetas no aumenta
                        const extra = labelCount > threshold ? (labelCount - threshold) * extraPer : 0;
                        const newH = Math.max(base + extra, base);
                        muniWrapper.style.height = newH + 'px';
                    }
                } catch (e) {
                    console.warn('No se pudo ajustar altura dinámica de municipios:', e);
                }

                charts.municipioChart.data.labels = m.labels;
                charts.municipioChart.data.datasets[0].data = m.counts;
                // Update dataset label to reflect whether we're grouping by residence or death
                charts.municipioChart.data.datasets[0].label = (selectedMunKind === 'residence') ? 'Defunciones (residencia)' : 'Defunciones (defunción)';
                // aplicar escala agradable según el máximo del dataset
                try { const maxM = Math.max(...(m.counts.map(c=>Number(c)||0))); applyNiceScaling(charts.municipioChart, maxM); } catch(e){}
                charts.municipioChart.update();

                // Tendencia
                const t = normalizeDataset(resp.meses.labels, resp.meses.counts);
                charts.tendenciaChart.data.labels = t.labels;
                charts.tendenciaChart.data.datasets[0].data = t.counts;
                try { const maxT = Math.max(...(t.counts.map(c=>Number(c)||0))); applyNiceScaling(charts.tendenciaChart, maxT); } catch(e){}
                charts.tendenciaChart.update();

                // Añadir total al subtítulo (suma de la serie de tendencia si existe)
                try {
                    const sub = document.getElementById('tendenciaSubtitle');
                    if (sub) {
                        let total = 0;
                        if (resp.meses && Array.isArray(resp.meses.counts)) total = resp.meses.counts.reduce((a,b)=>a+(Number(b)||0),0);
                        // fallback: sum municipios
                        if (!total && resp.municipios && Array.isArray(resp.municipios.counts)) total = resp.municipios.counts.reduce((a,b)=>a+(Number(b)||0),0);
                        if (total) sub.textContent = sub.textContent + ` · Total: ${total}`;
                    }
                } catch(e){ console.warn('No se pudo añadir total al subtítulo:', e); }

                // Edades
                const e = normalizeDataset(resp.edades.labels, resp.edades.counts);
                charts.edadChart.data.labels = e.labels;
                charts.edadChart.data.datasets[0].data = e.counts;
                try { const maxE = Math.max(...(e.counts.map(c=>Number(c)||0))); applyNiceScaling(charts.edadChart, maxE); } catch(e){}
                charts.edadChart.update();

                // Genero (mapear etiquetas)
                let gLabels = (resp.generos.labels || []).map(mapSexLabel);
                const g = normalizeDataset(gLabels, resp.generos.counts);
                charts.generoChart.data.labels = g.labels;
                charts.generoChart.data.datasets[0].data = g.counts;
                charts.generoChart.update();

                // Causas
                const c = normalizeDataset(resp.causas.labels, resp.causas.counts);
                charts.causaChart.data.labels = c.labels;
                charts.causaChart.data.datasets[0].data = c.counts;
                charts.causaChart.update();

                // Jurisdicciones (si vienen)
                if (resp.jurisdictions) {
                    const j = normalizeDataset(resp.jurisdictions.labels, resp.jurisdictions.counts);
                    charts.jurisdiccionChart.data.labels = j.labels;
                    charts.jurisdiccionChart.data.datasets[0].data = j.counts;
                    try { const maxJ = Math.max(...(j.counts.map(c=>Number(c)||0))); applyNiceScaling(charts.jurisdiccionChart, maxJ); } catch(e){}
                    charts.jurisdiccionChart.update();
                }

                // Comparativa residencia vs defuncion
                if (resp.municipios_compare) {
                    const labels = resp.municipios_compare.labels || [];
                    const resCounts = resp.municipios_compare.residence_counts || [];
                    const deathCounts = resp.municipios_compare.death_counts || [];
                    // normalize to at least show something
                    const cmp = normalizeDataset(labels, resCounts);
                    charts.compareChart.data.labels = cmp.labels;
                    charts.compareChart.data.datasets[0].data = resCounts;
                    charts.compareChart.data.datasets[1].data = deathCounts;
                    try { const both = resCounts.concat(deathCounts).map(c=>Number(c)||0); const maxC = both.length ? Math.max(...both) : 0; applyNiceScaling(charts.compareChart, maxC); } catch(e){}
                    charts.compareChart.update();
                }
            }

            // Build request params from the filters UI (maps ids to the endpoint query params)
            function buildParamsFromFilterControls() {
                const params = {};

                const dateRange = document.getElementById('dateRange')?.value;
                const year = document.getElementById('year')?.value;
                const month = document.getElementById('month')?.value;
                const selectedMonths = Array.from(document.querySelectorAll('.month-checkbox:checked')).map(i => i.value);
                const quarter = document.getElementById('quarter')?.value;
                const startDate = document.getElementById('startDate')?.value;
                const endDate = document.getElementById('endDate')?.value;

                // compute start_date/end_date according to the selected mode
                if (dateRange === 'custom' && startDate && endDate) {
                    params.start_date = startDate;
                    params.end_date = endDate;
                    // compute span in days to decide grouping granularity
                    try {
                        const d1 = new Date(startDate);
                        const d2 = new Date(endDate);
                        const spanDays = Math.max(1, Math.ceil((d2 - d1) / (1000*60*60*24)));
                        if (spanDays <= 31) params.group_by = 'day';
                        else if (spanDays <= 365) params.group_by = 'month';
                        else params.group_by = 'year';
                    } catch (e) {
                        // ignore
                    }
                } else if (dateRange === 'month' && year && month) {
                    const d1 = new Date(Number(year), Number(month) - 1, 1);
                    const d2 = new Date(Number(year), Number(month), 0);
                    params.start_date = d1.toISOString().slice(0,10);
                    params.end_date = d2.toISOString().slice(0,10);
                    // specific month -> show daily trend
                    params.group_by = 'day';
                } else if (dateRange === 'year' && year) {
                    params.start_date = `${year}-01-01`;
                    params.end_date = `${year}-12-31`;
                    // whole year -> monthly trend
                    params.group_by = 'month';
                } else if (dateRange === 'multiple-months' && year && selectedMonths.length) {
                    const months = selectedMonths.map(m => Number(m));
                    const min = Math.min(...months);
                    const max = Math.max(...months);
                    const d1 = new Date(Number(year), min - 1, 1);
                    const d2 = new Date(Number(year), max, 0);
                    params.start_date = d1.toISOString().slice(0,10);
                    params.end_date = d2.toISOString().slice(0,10);
                    // multiple months -> monthly trend
                    params.group_by = 'month';
                } else if (dateRange === 'quarter' && year && quarter) {
                    const q = Number(quarter);
                    const startMonth = (q - 1) * 3;
                    const d1 = new Date(Number(year), startMonth, 1);
                    const d2 = new Date(Number(year), startMonth + 3, 0);
                    params.start_date = d1.toISOString().slice(0,10);
                    params.end_date = d2.toISOString().slice(0,10);
                    // quarter -> monthly trend
                    params.group_by = 'month';
                }

                // Municipality: choose the select depending on the municipio_kind toggle
                const muniSelect = (selectedMunKind === 'residence') ? document.getElementById('municipio') : document.getElementById('municipioDefuncion');
                const muniVal = (muniSelect?.value || '').trim();
                // ignore generic 'Todos'/'All' placeholder values that some selects use
                if (muniVal && !/^(todos|all)$/i.test(muniVal)) {
                    if (/^\d+$/.test(muniVal)) params.municipality_id = muniVal; // send id
                    else {
                        // send as name fallback depending on which select
                        if (selectedMunKind === 'residence') params.municipio = muniVal;
                        else params.municipioDefuncion = muniVal;
                    }
                }

                // Cause (id if present)
                const causa = (document.getElementById('causa')?.value || '').trim();
                if (causa && !/^(todos|all)$/i.test(causa)) {
                    if (/^\d+$/.test(causa)) params.cause_id = causa;
                    else params.causa = causa;
                }

                // Sex mapping: 'hombre'->M, 'mujer'->F
                const sexo = (document.getElementById('sexo')?.value || '').trim();
                if (sexo && !/^(todos|all)$/i.test(sexo)) {
                    const s = sexo.toLowerCase();
                    if (s === 'hombre') params.sex = 'M';
                    else if (s === 'mujer') params.sex = 'F';
                    else params.sex = sexo;
                }

                // limit for top-N
                const limit = document.getElementById('chartLimit')?.value;
                if (limit && limit !== 'all') params.limit = limit;

                // Include municipio_kind so backend knows which municipality column to aggregate
                if (typeof selectedMunKind !== 'undefined' && selectedMunKind) {
                    params.municipio_kind = selectedMunKind;
                }

                // If group_by not set by the above logic, default to 'month' (this is what the backend expects by default)
                if (!params.group_by) params.group_by = 'month';

                return params;
            }

            let fetchTimeout = null;
            function loadChartsDebounced() {
                if (fetchTimeout) clearTimeout(fetchTimeout);
                fetchTimeout = setTimeout(loadCharts, 250);
            }

            function loadCharts() {
                const params = buildParamsFromFilterControls();
                // Actualizar subtítulo de la tarjeta de tendencia según la granularidad seleccionada y el rango legible
                try {
                    const sub = document.getElementById('tendenciaSubtitle');
                    if (sub) {
                        const gb = params.group_by || 'month';
                        const map = { day: 'Diaria', month: 'Mensual', year: 'Anual' };
                        const gran = map[gb] ? map[gb] : gb;
                        const periodLabel = getReadablePeriodLabel(params);
                        sub.textContent = `${gran} · ${periodLabel}`;
                    }
                } catch (e) {
                    console.warn('No se pudo actualizar subtítulo de tendencia:', e);
                }
                const qs = new URLSearchParams(params).toString();
                const url = chartsEndpoint + (qs ? ('?' + qs) : '');
                console.log('Fetching charts data from:', url);
                fetch(url, { credentials: 'same-origin' })
                    .then(async r => {
                        // Try to parse JSON even when HTTP status is not OK so we can display server-side debug info
                        let jsonBody = null;
                        try {
                            jsonBody = await r.json();
                        } catch (e) {
                            // fallback to raw text if JSON parsing fails
                            try { jsonBody = { __raw: await r.text() }; } catch (e2) { jsonBody = { __raw: null }; }
                        }

                        console.log('Charts data response (status ' + r.status + '):', jsonBody);

                        // show the JSON (or text) in a visible debug box on the page to help debugging
                        try {
                            let dbg = document.getElementById('chartsDebug');
                            if (!dbg) {
                                dbg = document.createElement('pre');
                                dbg.id = 'chartsDebug';
                                dbg.style.cssText = 'background:#f7fafc;border:1px solid #e2e8f0;padding:10px;margin:12px;white-space:pre-wrap;max-height:220px;overflow:auto;font-size:12px;';
                                const container = document.querySelector('.lg\:flex-1') || document.body;
                                container.insertBefore(dbg, container.firstChild);
                            }
                            dbg.textContent = JSON.stringify({ status: r.status, body: jsonBody }, null, 2);
                        } catch (e) {
                            console.warn('No se pudo mostrar debug box:', e);
                        }

                        if (!r.ok) {
                            console.error('Server returned HTTP', r.status, jsonBody);
                            // do not try to update charts on error
                            return;
                        }

                        updateChartsFromResponse(jsonBody);
                    })
                    .catch(err => {
                        console.error('Error cargando datos de gráficos (network/parsing):', err);
                    });
            }

            // Exponer función para ser invocada desde el panel de filtros
            window.loadCharts = loadCharts;

            // Escuchar cambios en los filtros
            const filtrosContainer = document.getElementById('estadisticas-filtros');
            if (filtrosContainer) {
                filtrosContainer.addEventListener('change', loadChartsDebounced);
                filtrosContainer.addEventListener('input', loadChartsDebounced);
            }

            // Cargar inicialmente (sin filtros)
            loadCharts();

            // Lógica para selectores de fecha condicionales
            const dateRangeSelect = document.querySelector('select');
            if (dateRangeSelect) {
                dateRangeSelect.addEventListener('change', function() {
                    // Lógica para mostrar/ocultar selectores condicionales
                    console.log('Rango seleccionado:', this.value);
                });
            }
        });
    </script>

    <!-- AGREGAR FONT AWESOME PARA LOS ÍCONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@push('styles')
<style>
/* Ensure chart canvases don't overflow their card containers */
.chart-canvas { 
    display: block;
    max-width: 100%;
    box-sizing: border-box;
}

/* For very narrow screens reduce height a bit */
@media (max-width: 640px) {
    .chart-canvas { height: 220px !important; }
}

/* Wrapper that constrains the chart area and hides overflow */
.chart-wrapper { position: relative; overflow: hidden; }
.chart-wrapper { position: relative; overflow: hidden; margin-bottom: 18px; z-index: 0; }
.chart-wrapper canvas { width: 100% !important; height: 100% !important; display: block; }
</style>
@endpush