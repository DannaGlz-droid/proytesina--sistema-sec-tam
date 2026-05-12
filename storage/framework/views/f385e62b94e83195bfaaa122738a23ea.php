
<?php $__env->startSection('title', 'Estadísticas'); ?>
<?php $__env->startSection('content'); ?>

    <?php echo $__env->make('components.header-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.nav-estadisticas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Estadísticas Interactivas</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    Seleccione una métrica para analizar y explore los datos con filtros personalizados.
                </p>
            </div>
            <button class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto" id="descargarActual">
                <i class="fas fa-download text-xs"></i>
                Descargar Gráfica
            </button>
        </div>

        <!-- SELECTOR DE GRÁFICAS (Pestañas) -->
        <div class="mb-8 border-b border-[#e5e7eb]">
            <div class="flex flex-wrap gap-0 overflow-x-auto pb-0">
                <button class="chart-tab-btn active" data-chart="municipios" title="Distribución de defunciones por municipio">
                    <i class="fas fa-map-marked-alt text-sm mr-1.5"></i>
                    <span class="font-lora text-sm">Municipios</span>
                </button>
                <button class="chart-tab-btn" data-chart="tendencias" title="Tendencia temporal de defunciones">
                    <i class="fas fa-chart-line text-sm mr-1.5"></i>
                    <span class="font-lora text-sm">Tendencias</span>
                </button>
                <button class="chart-tab-btn" data-chart="edades" title="Distribución por rangos etarios">
                    <i class="fas fa-chart-bar text-sm mr-1.5"></i>
                    <span class="font-lora text-sm">Edades</span>
                </button>
                <button class="chart-tab-btn" data-chart="genero" title="Distribución por género">
                    <i class="fas fa-venus-mars text-sm mr-1.5"></i>
                    <span class="font-lora text-sm">Género</span>
                </button>
                <button class="chart-tab-btn" data-chart="causas" title="Causas principales de defunción">
                    <i class="fas fa-heartbeat text-sm mr-1.5"></i>
                    <span class="font-lora text-sm">Causas</span>
                </button>
                <button class="chart-tab-btn" data-chart="jurisdicciones" title="Distribución por jurisdicción">
                    <i class="fas fa-building text-sm mr-1.5"></i>
                    <span class="font-lora text-sm">Jurisdicciones</span>
                </button>
                <button class="chart-tab-btn" data-chart="comparativa" title="Residencia vs Lugar de Defunción">
                    <i class="fas fa-exchange-alt text-sm mr-1.5"></i>
                    <span class="font-lora text-sm">Comparativa</span>
                </button>
            </div>
        </div>

        <!-- CONTENEDOR PRINCIPAL -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl bg-white bg-opacity-95 max-w-full shadow-md overflow-hidden">
            
            <div class="p-4 lg:p-6 pt-8 lg:pt-12">
                <!-- Layout: Filtros + Gráfica -->
                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <!-- COLUMNA IZQUIERDA - Filtros (DINÁMICOS según gráfica) -->
                    <div id="estadisticas-filtros" class="lg:w-80 flex-shrink-0">
                        <div class="border border-[#404041] rounded-lg bg-white bg-opacity-95 overflow-hidden shadow-sm">
                            <!-- Header de Filtros -->
                            <div class="bg-white px-4 py-3 border-b border-[#e5e7eb] flex justify-between items-center">
                                <h3 class="text-sm font-lora font-semibold text-[#404041]">Filtros</h3>
                                <button type="button" class="text-[#611132] text-xs font-semibold hover:text-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-1" id="limpiarFiltros">
                                    <i class="fas fa-redo text-xs"></i>
                                    Limpiar
                                </button>
                            </div>

                            <!-- Filtros Activos/Aplicados -->
                            <div id="filtrosActivos" class="px-4 py-3 bg-blue-50 border-b border-blue-200 hidden">
                                <p class="text-xs font-semibold text-[#404041] font-lora mb-2">Filtros aplicados:</p>
                                <div id="filtrosActivosList" class="flex flex-wrap gap-2">
                                    <!-- Los chips se generan dinámicamente con JavaScript -->
                                </div>
                            </div>

                            <!-- Contenido de Filtros -->
                            <div class="px-4 py-4 space-y-4">
                                <!-- Filtro de Fechas (siempre visible) -->
                                <div class="filter-section">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-calendar-alt text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Fechas</h4>
                                    </div>
                                    <div class="space-y-2">
                                        <div>
                                            <label class="block text-xs text-gray-600 font-lora mb-1">Rango:</label>
                                            <select id="dateRange" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                                                <option value="all">Todas las fechas</option>
                                                <option value="year">Año específico</option>
                                                <option value="month">Mes específico</option>
                                                <option value="custom">Personalizado</option>
                                            </select>
                                        </div>

                                        <div id="yearSelector" style="display: none;">
                                            <label class="block text-xs text-gray-600 font-lora mb-1">Año:</label>
                                            <select id="year" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                                <option value="">Seleccionar año</option>
                                                <option value="2024">2024</option>
                                                <option value="2023">2023</option>
                                                <option value="2022">2022</option>
                                                <option value="2021">2021</option>
                                                <option value="2020">2020</option>
                                            </select>
                                        </div>

                                        <div id="monthSelector" style="display: none;">
                                            <label class="block text-xs text-gray-600 font-lora mb-1">Mes:</label>
                                            <select id="month" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                                <option value="">Seleccionar mes</option>
                                                <option value="01">Enero</option>
                                                <option value="02">Febrero</option>
                                                <option value="03">Marzo</option>
                                                <option value="04">Abril</option>
                                                <option value="05">Mayo</option>
                                                <option value="06">Junio</option>
                                                <option value="07">Julio</option>
                                                <option value="08">Agosto</option>
                                                <option value="09">Septiembre</option>
                                                <option value="10">Octubre</option>
                                                <option value="11">Noviembre</option>
                                                <option value="12">Diciembre</option>
                                            </select>
                                        </div>

                                        <div id="customDateSelector" style="display: none;">
                                            <label class="block text-xs text-gray-600 font-lora mb-1">Desde:</label>
                                            <input type="date" id="customStartDate" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                            <label class="block text-xs text-gray-600 font-lora mb-1 mt-2">Hasta:</label>
                                            <input type="date" id="customEndDate" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                        </div>
                                    </div>
                                </div>

                                <!-- FILTROS CONTEXTUALES DINÁMICOS -->
                                
                                <!-- Filtro de Municipios (contextual) -->
                                <div id="filterMunicipios" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-city text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Municipios</h4>
                                    </div>
                                    <select id="municipiosFilter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" multiple data-placeholder="Selecciona municipios">
                                        <?php $__currentLoopData = $municipalities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($mun->id); ?>"><?php echo e($mun->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <!-- Filtro de Causas (contextual) -->
                                <div id="filterCausas" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-heartbeat text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Causas</h4>
                                    </div>
                                    <select id="causasFilter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" multiple data-placeholder="Selecciona causas">
                                        <?php $__currentLoopData = $causes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cause): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cause->id); ?>"><?php echo e($cause->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <!-- Filtro de Jurisdicciones (contextual) -->
                                <?php if($jurisdictions->count() > 0): ?>
                                <div id="filterJurisdicciones" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-building text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Jurisdicciones</h4>
                                    </div>
                                    <select id="jurisdiccionesFilter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" multiple data-placeholder="Selecciona jurisdicciones">
                                        <?php $__currentLoopData = $jurisdictions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($jur->id); ?>"><?php echo e($jur->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <?php endif; ?>

                                <!-- Filtro de Sexo (contextual) -->
                                <div id="filterSexo" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-venus-mars text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Sexo</h4>
                                    </div>
                                    <select id="sexoFilter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                        <option value="">Todos</option>
                                        <?php $__currentLoopData = $sexes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($sex->value); ?>"><?php echo e($sex->label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <!-- Selector de Granularidad para Tendencias (contextual) -->
                                <div id="filterGranularidad" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-hourglass-half text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Granularidad</h4>
                                    </div>
                                    <select id="granularidadFilter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                        <option value="day">Diaria</option>
                                        <option value="month" selected>Mensual</option>
                                        <option value="year">Anual</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- COLUMNA DERECHA - Gráfica -->
                    <div class="lg:flex-1">
                        <!-- Controles de Presentación -->
                        <div class="flex flex-col gap-4 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex flex-col sm:flex-row gap-4 flex-wrap">
                                <!-- Tipo de Gráfica -->
                                <div class="flex flex-col gap-1 flex-1 min-w-40">
                                    <label class="text-xs font-semibold text-gray-700 font-lora">Tipo de Gráfica</label>
                                    <select id="chartTypeSelector" class="text-sm border border-gray-200 rounded px-3 py-1.5 font-lora bg-white">
                                        <option value="auto">Auto</option>
                                        <option value="bar">Barras</option>
                                        <option value="pie">Pastel</option>
                                        <option value="doughnut">Rosquilla</option>
                                        <option value="line">Línea</option>
                                        <option value="area">Área</option>
                                    </select>
                                </div>

                                <!-- Etiquetas -->
                                <div class="flex flex-col gap-1 flex-1 min-w-40">
                                    <label class="text-xs font-semibold text-gray-700 font-lora">Etiquetas</label>
                                    <select id="datalabelMode" class="text-sm border border-gray-200 rounded px-3 py-1.5 font-lora bg-white">
                                        <option value="auto">Auto</option>
                                        <option value="value">Solo Valores</option>
                                        <option value="percent">Solo %</option>
                                        <option value="both">Ambos</option>
                                        <option value="none">Ocultar</option>
                                    </select>
                                </div>

                                <!-- Top N -->
                                <div class="flex flex-col gap-1 flex-1 min-w-40">
                                    <label class="text-xs font-semibold text-gray-700 font-lora">Top</label>
                                    <select id="chartLimit" class="text-sm border border-gray-200 rounded px-3 py-1.5 font-lora bg-white">
                                        <option value="all">Todos</option>
                                        <option value="5">Top 5</option>
                                        <option value="10">Top 10</option>
                                        <option value="15">Top 15</option>
                                        <option value="20">Top 20</option>
                                    </select>
                                </div>

                                <!-- Paleta de Colores -->
                                <div class="flex flex-col gap-1 flex-1 min-w-40">
                                    <label class="text-xs font-semibold text-gray-700 font-lora">Paleta</label>
                                    <select id="colorPalette" class="text-sm border border-gray-200 rounded px-3 py-1.5 font-lora bg-white">
                                        <option value="principal">Principal</option>
                                        <option value="modern">Moderna</option>
                                        <option value="nature">Natural</option>
                                        <option value="warm">Cálida</option>
                                        <option value="cool">Fría</option>
                                        <option value="sunset">Sunset</option>
                                        <option value="ocean">Ocean</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Indicador de Total -->
                            <div class="text-sm text-gray-700 font-lora">
                                <span class="font-semibold">Total de registros:</span>
                                <span id="totalRecords" class="text-[#611132] font-bold">0</span>
                            </div>
                        </div>

                        <!-- Gráfica Principal -->
                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <h2 id="chartTitle" class="text-lg font-bold text-[#404041] mb-4 font-lora">Cargando...</h2>
                            <div class="chart-wrapper" style="height:400px; position:relative;">
                                <div id="mainChart" style="width: 100%; height: 100%;"></div>
                            </div>
                        </div>

                        <!-- Mensaje de Carga/Error -->
                        <div id="loadingMessage" class="hidden text-center py-8 text-gray-600">
                            <i class="fas fa-spinner fa-spin text-2xl text-[#611132] mb-3"></i>
                            <p class="font-lora">Cargando datos...</p>
                        </div>
                        <div id="errorMessage" class="hidden text-center py-8 text-red-600 bg-red-50 rounded-lg">
                            <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                            <p class="font-lora" id="errorText">Error al cargar los datos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir ECharts -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>

    <!-- Include modal para descargas -->
    <?php echo $__env->make('components.modal-descargas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        let currentChartType = 'municipios';
        let currentEchartsInstance = null;
        let chartConfig = {
            type: 'bar',
            dataLabelMode: 'auto',
            limit: null,
            colorPalette: 'principal',
            groupBy: 'month'
        };

        let activeFilters = {
            startDate: null,
            endDate: null,
            municipios: [],
            municipiosNames: [],
            causas: [],
            causasNames: [],
            jurisdicciones: [],
            jurisdiccionesNames: [],
            sexo: null,
            granularidad: 'month'
        };

        const colorPalettes = {
            principal: ['#611132', '#8B6F47', '#2C5F5D', '#9B4D6F', '#4A7C7E', '#A67C52', '#3D4F5C', '#7A5060'],
            modern: ['#4A90A4', '#7BA05B', '#D88559', '#B565A7', '#5DADA5', '#F4D35E', '#EE964B', '#F95738'],
            nature: ['#264653', '#2A9D8F', '#E9C46A', '#F4A261', '#E76F51', '#D4A574', '#B8956A', '#9A8C98'],
            warm: ['#FF6B6B', '#FFA62B', '#FFD93D', '#FF6B9D', '#C44569', '#F8B500', '#D61355', '#FF3864'],
            cool: ['#1A535C', '#4ECDC4', '#44A08D', '#95E1D3', '#38B6FF', '#118AB2', '#073B4C', '#06D6A0'],
            sunset: ['#FF6B35', '#F7931E', '#FDB833', '#F37335', '#C1272D', '#FF4E50', '#F34C3A', '#D1291E'],
            ocean: ['#264653', '#2A9D8F', '#E76F51', '#F4A261', '#E9C46A', '#457B9D', '#1D3557', '#A8DADC']
        };

        const chartTypeDefaults = {
            municipios: 'bar',
            tendencias: 'line',
            edades: 'bar',
            genero: 'doughnut',
            causas: 'doughnut',
            jurisdicciones: 'bar',
            comparativa: 'bar'
        };

        const chartTitles = {
            municipios: 'Distribución por Municipios',
            tendencias: 'Tendencia Temporal',
            edades: 'Distribución por Edades',
            genero: 'Distribución por Género',
            causas: 'Causas de Defunción',
            jurisdicciones: 'Distribución por Jurisdicción',
            comparativa: 'Comparativa: Residencia vs Defunción'
        };

        const filtersForChart = {
            municipios: ['dates', 'causas', 'jurisdicciones', 'sexo'],
            tendencias: ['dates', 'municipios', 'causas', 'sexo', 'granularidad'],
            edades: ['dates', 'municipios', 'causas', 'jurisdicciones'],
            genero: ['dates', 'municipios', 'causas', 'jurisdicciones'],
            causas: ['dates', 'municipios', 'jurisdicciones', 'sexo'],
            jurisdicciones: ['dates', 'causas', 'sexo'],
            comparativa: ['dates', 'causas', 'sexo']
        };

        document.addEventListener('DOMContentLoaded', function() {
            try {
                if (typeof ChartDataLabels !== 'undefined') Chart.register(ChartDataLabels);
            } catch (e) {
                console.warn('ChartDataLabels no disponible');
            }

            initializeEventListeners();
            loadChart('municipios');
        });

        function initializeEventListeners() {
            document.querySelectorAll('.chart-tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectChart(this.dataset.chart);
                });
            });

            document.getElementById('dateRange').addEventListener('change', onDateRangeChange);
            document.getElementById('year').addEventListener('change', updateChart);
            document.getElementById('month').addEventListener('change', updateChart);
            document.getElementById('customStartDate').addEventListener('change', updateChart);
            document.getElementById('customEndDate').addEventListener('change', updateChart);
            document.getElementById('municipiosFilter').addEventListener('change', updateChart);
            document.getElementById('causasFilter').addEventListener('change', updateChart);
            document.getElementById('jurisdiccionesFilter').addEventListener('change', updateChart);
            document.getElementById('sexoFilter').addEventListener('change', updateChart);
            document.getElementById('granularidadFilter').addEventListener('change', updateChart);

            document.getElementById('chartTypeSelector').addEventListener('change', function() {
                chartConfig.type = this.value === 'auto' ? chartTypeDefaults[currentChartType] : this.value;
                updateChart();
            });
            document.getElementById('datalabelMode').addEventListener('change', function() {
                chartConfig.dataLabelMode = this.value;
                updateChart();
            });
            document.getElementById('chartLimit').addEventListener('change', function() {
                chartConfig.limit = this.value === 'all' ? null : parseInt(this.value);
                updateChart();
            });
            document.getElementById('colorPalette').addEventListener('change', function() {
                chartConfig.colorPalette = this.value;
                updateChart();
            });

            document.getElementById('limpiarFiltros').addEventListener('click', clearFilters);

            document.getElementById('descargarActual').addEventListener('click', function() {
                if (currentEchartsInstance) {
                    const link = document.createElement('a');
                    link.href = currentEchartsInstance.getDataURL({ type: 'png' });
                    link.download = `grafica-${currentChartType}-${new Date().toISOString().split('T')[0]}.png`;
                    link.click();
                }
            });
        }

        function selectChart(chartType) {
            currentChartType = chartType;
            document.querySelectorAll('.chart-tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.chart === chartType);
            });
            updateVisibleFilters(chartType);
            document.getElementById('chartTypeSelector').value = 'auto';
            chartConfig.type = chartTypeDefaults[chartType];
            loadChart(chartType);
        }

        function updateVisibleFilters(chartType) {
            const allFilters = ['filterMunicipios', 'filterCausas', 'filterJurisdicciones', 'filterSexo', 'filterGranularidad'];
            const availableFilters = filtersForChart[chartType] || [];

            allFilters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    let show = false;
                    if (filterId === 'filterMunicipios' && availableFilters.includes('municipios')) show = true;
                    if (filterId === 'filterCausas' && availableFilters.includes('causas')) show = true;
                    if (filterId === 'filterJurisdicciones' && availableFilters.includes('jurisdicciones')) show = true;
                    if (filterId === 'filterSexo' && availableFilters.includes('sexo')) show = true;
                    if (filterId === 'filterGranularidad' && availableFilters.includes('granularidad')) show = true;
                    element.style.display = show ? 'block' : 'none';
                }
            });
        }

        function onDateRangeChange() {
            const value = document.getElementById('dateRange').value;
            document.getElementById('yearSelector').style.display = value === 'year' ? 'block' : 'none';
            document.getElementById('monthSelector').style.display = value === 'month' ? 'block' : 'none';
            document.getElementById('customDateSelector').style.display = value === 'custom' ? 'block' : 'none';
            updateChart();
        }

        function collectFilters() {
            const dateRange = document.getElementById('dateRange').value;
            let startDate = null, endDate = null;

            if (dateRange === 'year') {
                const year = document.getElementById('year').value;
                if (year) {
                    startDate = `${year}-01-01`;
                    endDate = `${year}-12-31`;
                }
            } else if (dateRange === 'month') {
                const year = new Date().getFullYear();
                const month = document.getElementById('month').value;
                if (month) {
                    startDate = `${year}-${month}-01`;
                    const nextMonth = parseInt(month) === 12 ? `${year + 1}-01-01` : `${year}-${String(parseInt(month) + 1).padStart(2, '0')}-01`;
                    endDate = new Date(nextMonth);
                    endDate.setDate(endDate.getDate() - 1);
                    endDate = endDate.toISOString().split('T')[0];
                }
            } else if (dateRange === 'custom') {
                startDate = document.getElementById('customStartDate').value;
                endDate = document.getElementById('customEndDate').value;
            }

            activeFilters.startDate = startDate;
            activeFilters.endDate = endDate;
            activeFilters.municipios = Array.from(document.getElementById('municipiosFilter').selectedOptions || []).map(o => o.value);
            activeFilters.municipiosNames = Array.from(document.getElementById('municipiosFilter').selectedOptions || []).map(o => o.text);
            activeFilters.causas = Array.from(document.getElementById('causasFilter').selectedOptions || []).map(o => o.value);
            activeFilters.causasNames = Array.from(document.getElementById('causasFilter').selectedOptions || []).map(o => o.text);
            activeFilters.jurisdicciones = Array.from(document.getElementById('jurisdiccionesFilter').selectedOptions || []).map(o => o.value);
            activeFilters.jurisdiccionesNames = Array.from(document.getElementById('jurisdiccionesFilter').selectedOptions || []).map(o => o.text);
            activeFilters.sexo = document.getElementById('sexoFilter').value || null;
            activeFilters.granularidad = document.getElementById('granularidadFilter').value || 'month';
            
            updateActiveFiltersDisplay();
        }

        function updateActiveFiltersDisplay() {
            const container = document.getElementById('filtrosActivosList');
            const section = document.getElementById('filtrosActivos');
            container.innerHTML = '';
            
            let hasActiveFilters = false;

            // Mostrar rango de fechas
            if (activeFilters.startDate || activeFilters.endDate) {
                const dateText = activeFilters.startDate && activeFilters.endDate 
                    ? `${activeFilters.startDate.split('-')[0]}`
                    : 'Personalizado';
                container.innerHTML += `<span class="inline-flex items-center gap-1 bg-[#611132] text-white text-xs px-2.5 py-1 rounded-full font-lora">
                    <i class="fas fa-calendar-alt"></i>
                    ${dateText}
                    <button onclick="clearDateFilter()" class="ml-1 hover:opacity-70"><i class="fas fa-times"></i></button>
                </span>`;
                hasActiveFilters = true;
            }

            // Mostrar municipios seleccionados
            if (activeFilters.municipios.length > 0) {
                const municipiosText = activeFilters.municipiosNames.join(', ');
                container.innerHTML += `<span class="inline-flex items-center gap-1 bg-[#8B6F47] text-white text-xs px-2.5 py-1 rounded-full font-lora">
                    <i class="fas fa-city"></i>
                    ${municipiosText}
                    <button onclick="clearFilter('municipios')" class="ml-1 hover:opacity-70"><i class="fas fa-times"></i></button>
                </span>`;
                hasActiveFilters = true;
            }

            // Mostrar causas seleccionadas
            if (activeFilters.causas.length > 0) {
                const causasText = activeFilters.causasNames.join(', ');
                container.innerHTML += `<span class="inline-flex items-center gap-1 bg-[#2C5F5D] text-white text-xs px-2.5 py-1 rounded-full font-lora">
                    <i class="fas fa-heartbeat"></i>
                    ${causasText}
                    <button onclick="clearFilter('causas')" class="ml-1 hover:opacity-70"><i class="fas fa-times"></i></button>
                </span>`;
                hasActiveFilters = true;
            }

            // Mostrar jurisdicciones seleccionadas
            if (activeFilters.jurisdicciones.length > 0) {
                const jurisdiccionesText = activeFilters.jurisdiccionesNames.join(', ');
                container.innerHTML += `<span class="inline-flex items-center gap-1 bg-[#9B4D6F] text-white text-xs px-2.5 py-1 rounded-full font-lora">
                    <i class="fas fa-building"></i>
                    ${jurisdiccionesText}
                    <button onclick="clearFilter('jurisdicciones')" class="ml-1 hover:opacity-70"><i class="fas fa-times"></i></button>
                </span>`;
                hasActiveFilters = true;
            }

            // Mostrar sexo seleccionado
            if (activeFilters.sexo) {
                const sexoLabel = activeFilters.sexo === 'M' ? 'Hombre' : (activeFilters.sexo === 'F' ? 'Mujer' : activeFilters.sexo);
                container.innerHTML += `<span class="inline-flex items-center gap-1 bg-[#4A7C7E] text-white text-xs px-2.5 py-1 rounded-full font-lora">
                    <i class="fas fa-venus-mars"></i>
                    ${sexoLabel}
                    <button onclick="clearFilter('sexo')" class="ml-1 hover:opacity-70"><i class="fas fa-times"></i></button>
                </span>`;
                hasActiveFilters = true;
            }

            // Mostrar sección si hay filtros activos
            section.classList.toggle('hidden', !hasActiveFilters);
        }

        function clearDateFilter() {
            document.getElementById('dateRange').value = 'all';
            document.getElementById('yearSelector').style.display = 'none';
            document.getElementById('monthSelector').style.display = 'none';
            document.getElementById('customDateSelector').style.display = 'none';
            document.getElementById('year').value = '';
            document.getElementById('month').value = '';
            document.getElementById('customStartDate').value = '';
            document.getElementById('customEndDate').value = '';
            updateChart();
        }

        function clearFilter(filterType) {
            if (filterType === 'municipios') {
                document.getElementById('municipiosFilter').value = '';
            } else if (filterType === 'causas') {
                document.getElementById('causasFilter').value = '';
            } else if (filterType === 'jurisdicciones') {
                document.getElementById('jurisdiccionesFilter').value = '';
            } else if (filterType === 'sexo') {
                document.getElementById('sexoFilter').value = '';
            }
            updateChart();
        }

        function loadChart(chartType) {
            showLoadingMessage();
            collectFilters();

            const filters = {
                ...(activeFilters.startDate && { start_date: activeFilters.startDate }),
                ...(activeFilters.endDate && { end_date: activeFilters.endDate }),
                ...(activeFilters.municipios.length && { municipios: activeFilters.municipios }),
                ...(activeFilters.causas.length && { causas: activeFilters.causas }),
                ...(activeFilters.jurisdicciones.length && { jurisdicciones: activeFilters.jurisdicciones }),
                ...(activeFilters.sexo && { sex: activeFilters.sexo }),
                ...(chartConfig.limit && { limit: chartConfig.limit }),
                ...(chartType === 'tendencias' && { group_by: activeFilters.granularidad })
            };

            const params = new URLSearchParams(filters);
            fetch(`<?php echo e(route('api.chart.data')); ?>/` + chartType + '?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showErrorMessage(data.message || 'Error al obtener datos');
                    } else {
                        renderChart(data);
                        hideLoadingMessage();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorMessage('Error al cargar los datos');
                });
        }

        function renderChart(data) {
            const chartContainer = document.getElementById('mainChart');
            
            // Limpiar instancia anterior completamente
            if (currentEchartsInstance) {
                currentEchartsInstance.dispose();
            }
            // Recrear instancia limpia
            currentEchartsInstance = echarts.init(chartContainer);

            document.getElementById('chartTitle').textContent = chartTitles[currentChartType] || 'Gráfica';
            document.getElementById('totalRecords').textContent = (data.total || 0).toLocaleString();

            let labels = data.labels || [];
            let values = currentChartType === 'comparativa' ? null : (data.counts || []);
            
            const palette = colorPalettes[chartConfig.colorPalette];
            const colors = labels.map((_, i) => palette[i % palette.length]);

            // Determinar tipo de gráfica: usar la selección del usuario o el óptimo si es "auto"
            let chartType = chartConfig.type === 'auto' ? getOptimalChartType(currentChartType) : chartConfig.type;
            
            // Si el usuario selecciona "area", trata como "line" con área
            if (chartType === 'area') {
                chartType = 'line';
            }
            
            // Construir opciones según el tipo de gráfica
            let option = {};

            if (currentChartType === 'comparativa') {
                // Gráfica de barras agrupadas para comparativa
                option = {
                    color: palette,
                    title: { text: '' },
                    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
                    legend: { data: ['Residencia', 'Lugar de Defunción'], bottom: 10 },
                    grid: { left: '3%', right: '4%', bottom: '15%', top: '15%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        data: labels,
                        axisLabel: { rotate: 45, interval: 0 }
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            name: 'Residencia',
                            type: 'bar',
                            data: data.residence_counts || [],
                            itemStyle: { color: palette[0] }
                        },
                        {
                            name: 'Lugar de Defunción',
                            type: 'bar',
                            data: data.death_counts || [],
                            itemStyle: { color: palette[1] }
                        }
                    ]
                };
            } else if (chartType === 'pie' || chartType === 'doughnut') {
                // Gráficas de pastel
                const pieData = labels.map((label, i) => ({
                    name: label,
                    value: values[i]
                }));
                
                option = {
                    color: colors,
                    tooltip: { trigger: 'item' },
                    legend: { orient: 'vertical', left: 'left', bottom: 10 },
                    series: [{
                        name: chartTitles[currentChartType],
                        type: chartType === 'doughnut' ? 'pie' : 'pie',
                        radius: chartType === 'doughnut' ? ['40%', '70%'] : '75%',
                        data: pieData,
                        label: {
                            show: true,
                            formatter: (params) => {
                                if (chartConfig.dataLabelMode === 'value') {
                                    return params.value;
                                } else if (chartConfig.dataLabelMode === 'percent') {
                                    return params.percent + '%';
                                } else if (chartConfig.dataLabelMode === 'both') {
                                    return params.value + ' (' + params.percent + '%)';
                                }
                                return params.name;
                            }
                        }
                    }]
                };
            } else if (chartType === 'line') {
                // Línea con área para tendencias
                option = {
                    color: colors,
                    title: { text: '' },
                    tooltip: { trigger: 'axis', axisPointer: { type: 'cross' } },
                    grid: { left: '3%', right: '4%', bottom: '10%', top: '10%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        data: labels,
                        boundaryGap: false,
                        axisLabel: { rotate: 45, interval: 'auto' }
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [{
                        name: chartTitles[currentChartType],
                        type: 'line',
                        data: values,
                        smooth: true,
                        areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{ offset: 0, color: palette[0] + 'cc' }, { offset: 1, color: palette[0] + '00' }]) },
                        itemStyle: { color: palette[0] },
                        label: {
                            show: chartConfig.dataLabelMode !== 'none',
                            position: 'top',
                            formatter: (params) => {
                                if (chartConfig.dataLabelMode === 'value') return params.value;
                                return params.value;
                            }
                        }
                    }]
                };
            } else if (chartType === 'bar' || chartType === 'barHorizontal') {
                // Barras verticales u horizontales
                const isHorizontal = chartType === 'barHorizontal';
                option = {
                    color: colors,
                    title: { text: '' },
                    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
                    grid: { left: isHorizontal ? '20%' : '3%', right: '4%', bottom: isHorizontal ? '3%' : '15%', top: '10%', containLabel: true },
                    [isHorizontal ? 'xAxis' : 'yAxis']: { type: 'value' },
                    [isHorizontal ? 'yAxis' : 'xAxis']: {
                        type: 'category',
                        data: labels,
                        axisLabel: { interval: 0 },
                        ...(isHorizontal ? {} : { rotate: 45 })
                    },
                    series: [{
                        name: chartTitles[currentChartType],
                        type: 'bar',
                        data: values,
                        itemStyle: {
                            color: (params) => colors[params.dataIndex]
                        },
                        label: {
                            show: chartConfig.dataLabelMode !== 'none',
                            position: isHorizontal ? 'right' : 'top',
                            formatter: (params) => {
                                if (chartConfig.dataLabelMode === 'value') return params.value;
                                if (chartConfig.dataLabelMode === 'percent') {
                                    const total = values.reduce((a, b) => a + b, 0);
                                    return ((params.value / total) * 100).toFixed(1) + '%';
                                }
                                if (chartConfig.dataLabelMode === 'both') {
                                    const total = values.reduce((a, b) => a + b, 0);
                                    return params.value + ' (' + ((params.value / total) * 100).toFixed(1) + '%)';
                                }
                                return '';
                            }
                        }
                    }]
                };
            }

            currentEchartsInstance.setOption(option, true);
            hideLoadingMessage();
        }

        function getOptimalChartType(metric) {
            // Determinar el tipo de gráfica óptimo para cada métrica
            const typeMap = {
                'municipios': 'barHorizontal',    // Barras horizontales para municipios
                'tendencias': 'line',              // Línea con área para tendencias
                'edades': 'bar',                   // Barras verticales para edades
                'genero': 'pie',                   // Pastel para género
                'causas': 'barHorizontal',         // Barras horizontales para causas
                'jurisdicciones': 'bar',           // Barras verticales para jurisdicciones
                'comparativa': 'bar'               // Barras agrupadas para comparativa
            };
            return typeMap[metric] || 'bar';
        }

        function updateChart() {
            loadChart(currentChartType);
        }

        function clearFilters() {
            document.getElementById('dateRange').value = 'all';
            document.getElementById('year').value = '';
            document.getElementById('month').value = '';
            document.getElementById('customStartDate').value = '';
            document.getElementById('customEndDate').value = '';
            document.getElementById('municipiosFilter').value = '';
            document.getElementById('causasFilter').value = '';
            document.getElementById('jurisdiccionesFilter').value = '';
            document.getElementById('sexoFilter').value = '';
            document.getElementById('granularidadFilter').value = 'month';
            
            document.getElementById('yearSelector').style.display = 'none';
            document.getElementById('monthSelector').style.display = 'none';
            document.getElementById('customDateSelector').style.display = 'none';
            
            updateActiveFiltersDisplay();
            updateChart();
        }

        function showLoadingMessage() {
            document.getElementById('loadingMessage').classList.remove('hidden');
            document.getElementById('errorMessage').classList.add('hidden');
        }

        function hideLoadingMessage() {
            document.getElementById('loadingMessage').classList.add('hidden');
        }

        function showErrorMessage(message) {
            document.getElementById('errorText').textContent = message;
            document.getElementById('errorMessage').classList.remove('hidden');
            document.getElementById('loadingMessage').classList.add('hidden');
        }
    </script>

    <style>
        /* Estilos para botones de pestaña */
        .chart-tab-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.875rem 1rem;
            border: none;
            border-bottom: 3px solid transparent;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #666;
            font-weight: 500;
            white-space: nowrap;
            position: relative;
        }

        .chart-tab-btn:hover {
            color: #611132;
            background-color: #f8f4f6;
        }

        .chart-tab-btn.active {
            color: #611132;
            border-bottom-color: #611132;
            background-color: transparent;
        }

        .chart-tab-btn i {
            color: currentColor;
        }

        /* Estilos para el selector de gráficas */
        .chart-selector-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.25rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #404041;
            font-weight: 500;
        }

        .chart-selector-btn:hover {
            border-color: #611132;
            background-color: #f8f4f6;
            transform: translateY(-2px);
        }

        .chart-selector-btn.active {
            border-color: #611132;
            background-color: #611132;
            color: white;
            box-shadow: 0 4px 12px rgba(97, 17, 50, 0.3);
        }

        .chart-selector-btn i {
            color: currentColor;
        }

        /* Estilos para los filtros */
        .filter-section {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                max-height: 0;
            }
            to {
                opacity: 1;
                max-height: 500px;
            }
        }

        .dynamic-filter {
            border: none;
            padding: 0;
        }

        .dynamic-filter select {
            max-height: 150px;
            overflow-y: auto;
        }

        #estadisticas-filtros select[multiple] {
            padding: 0.375rem 0;
        }

        #estadisticas-filtros select[multiple] option {
            padding: 0.375rem 0.75rem;
        }

        /* Estilos para filtros activos */
        #filtrosActivosList span {
            animation: fadeInSlide 0.3s ease-out;
        }

        #filtrosActivosList button {
            cursor: pointer;
            transition: opacity 0.2s;
        }

        #filtrosActivosList button:hover {
            opacity: 0.7;
        }

        @keyframes fadeInSlide {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        }
    </style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.principal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/estadisticas/graficas.blade.php ENDPATH**/ ?>