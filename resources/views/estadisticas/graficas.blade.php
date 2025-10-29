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
                                <canvas id="municipioChart"></canvas>
                            </x-graficos.tarjeta>
                        </x-graficos.categoria>

                        <!-- Categoría: Tendencia Temporal -->
                        <x-graficos.categoria 
                            titulo="Tendencia Temporal" 
                            icono="calendar-alt"
                        >
                            <x-graficos.tarjeta 
                                titulo="Tendencia Mensual" 
                                :tipos="['line', 'bar']" 
                                tipoInicial="line"
                                graficoId="tendenciaChart"
                            >
                                <canvas id="tendenciaChart"></canvas>
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
                                <canvas id="edadChart"></canvas>
                            </x-graficos.tarjeta>
                            
                            <x-graficos.tarjeta 
                                titulo="Distribución por Género" 
                                :tipos="['doughnut', 'pie', 'bar']" 
                                tipoInicial="doughnut"
                                graficoId="generoChart"
                            >
                                <canvas id="generoChart"></canvas>
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
                                <canvas id="causaChart"></canvas>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar gráficos
            const municipioCtx = document.getElementById('municipioChart').getContext('2d');
            const tendenciaCtx = document.getElementById('tendenciaChart').getContext('2d');
            const edadCtx = document.getElementById('edadChart').getContext('2d');
            const generoCtx = document.getElementById('generoChart').getContext('2d');
            const causaCtx = document.getElementById('causaChart').getContext('2d');
            
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
                        
                        // Cambiar tipo de gráfico
                        chart.config.type = chartType;
                        chart.update();
                    }
                });
            });

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

            function updateChartsFromResponse(resp) {
                // Municipios
                const m = normalizeDataset(resp.municipios.labels, resp.municipios.counts);
                charts.municipioChart.data.labels = m.labels;
                charts.municipioChart.data.datasets[0].data = m.counts;
                charts.municipioChart.update();

                // Tendencia
                const t = normalizeDataset(resp.meses.labels, resp.meses.counts);
                charts.tendenciaChart.data.labels = t.labels;
                charts.tendenciaChart.data.datasets[0].data = t.counts;
                charts.tendenciaChart.update();

                // Edades
                const e = normalizeDataset(resp.edades.labels, resp.edades.counts);
                charts.edadChart.data.labels = e.labels;
                charts.edadChart.data.datasets[0].data = e.counts;
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
                } else if (dateRange === 'month' && year && month) {
                    const d1 = new Date(Number(year), Number(month) - 1, 1);
                    const d2 = new Date(Number(year), Number(month), 0);
                    params.start_date = d1.toISOString().slice(0,10);
                    params.end_date = d2.toISOString().slice(0,10);
                } else if (dateRange === 'year' && year) {
                    params.start_date = `${year}-01-01`;
                    params.end_date = `${year}-12-31`;
                } else if (dateRange === 'multiple-months' && year && selectedMonths.length) {
                    const months = selectedMonths.map(m => Number(m));
                    const min = Math.min(...months);
                    const max = Math.max(...months);
                    const d1 = new Date(Number(year), min - 1, 1);
                    const d2 = new Date(Number(year), max, 0);
                    params.start_date = d1.toISOString().slice(0,10);
                    params.end_date = d2.toISOString().slice(0,10);
                } else if (dateRange === 'quarter' && year && quarter) {
                    const q = Number(quarter);
                    const startMonth = (q - 1) * 3;
                    const d1 = new Date(Number(year), startMonth, 1);
                    const d2 = new Date(Number(year), startMonth + 3, 0);
                    params.start_date = d1.toISOString().slice(0,10);
                    params.end_date = d2.toISOString().slice(0,10);
                }

                // Municipality of death (use the select id municipioDefuncion)
                const muniDef = document.getElementById('municipioDefuncion')?.value;
                if (muniDef) {
                    if (/^\d+$/.test(muniDef)) params.municipality_id = muniDef; // send id
                    else params.municipioDefuncion = muniDef; // send name fallback
                }

                // Cause (id if present)
                const causa = document.getElementById('causa')?.value;
                if (causa) {
                    if (/^\d+$/.test(causa)) params.cause_id = causa;
                    else params.causa = causa;
                }

                // Sex mapping: 'hombre'->M, 'mujer'->F
                const sexo = document.getElementById('sexo')?.value;
                if (sexo) {
                    const s = sexo.toLowerCase();
                    if (s === 'hombre') params.sex = 'M';
                    else if (s === 'mujer') params.sex = 'F';
                    else params.sex = sexo;
                }

                // limit for top-N
                const limit = document.getElementById('chartLimit')?.value;
                if (limit && limit !== 'all') params.limit = limit;

                return params;
            }

            let fetchTimeout = null;
            function loadChartsDebounced() {
                if (fetchTimeout) clearTimeout(fetchTimeout);
                fetchTimeout = setTimeout(loadCharts, 250);
            }

            function loadCharts() {
                const params = buildParamsFromFilterControls();
                const qs = new URLSearchParams(params).toString();
                const url = chartsEndpoint + (qs ? ('?' + qs) : '');
                fetch(url, { credentials: 'same-origin' })
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(json => {
                        updateChartsFromResponse(json);
                    })
                    .catch(err => {
                        console.error('Error cargando datos de gráficos:', err);
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