@extends('layouts.principal')
@section('title', 'Estadísticas')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

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
                        <div class="border border-[#404041] rounded-lg bg-white bg-opacity-95 overflow-visible shadow-sm">
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
                                        <div class="filter-group">
                                            <label class="block text-xs text-gray-600 font-lora mb-1">Rango:</label>
                                            <select id="dateRange" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-transparent">
                                                <option value="all">Todas las fechas</option>
                                                <option value="years">Año(s)</option>
                                                <option value="months">Mes(es)</option>
                                                <option value="quarter">Trimestre</option>
                                                <option value="custom">Personalizado</option>
                                            </select>
                                        </div>

                                        <div class="filter-group" id="yearSelector" style="display: none;">
                                            <label class="block text-xs text-gray-600 font-lora mb-1">Año(s) de defunción:</label>
                                            @php $currentYear = now()->year; @endphp
                                            <input type="text" id="year" placeholder="Ej: 2024 o 2024, 2025, 2026" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" title="Escribe años separados por coma">
                                        </div>

                                        <!-- Se usa un único selector de meses que permite seleccionar 1 o varios -->

                                        <div class="filter-group" id="monthSelector" style="display: none;">
                                            <label class="block text-xs text-gray-600 font-lora mb-1">Meses de defunción:</label>
                                            <div class="grid grid-cols-3 gap-2 mt-2 months-container">
                                                @php
                                                    $months = [
                                                        '01' => 'Ene','02' => 'Feb','03' => 'Mar','04' => 'Abr','05' => 'May','06' => 'Jun',
                                                        '07' => 'Jul','08' => 'Ago','09' => 'Sep','10' => 'Oct','11' => 'Nov','12' => 'Dic'
                                                    ];
                                                @endphp
                                                @foreach($months as $mval => $mlabel)
                                                    <div>
                                                        <input type="checkbox" id="month-{{ $mval }}" name="selectedMonths[]" class="month-checkbox" value="{{ $mval }}">
                                                        <label for="month-{{ $mval }}" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200">{{ $mlabel }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="filter-group" id="quarterSelector" style="display: none;">
                                            <label class="block text-xs text-gray-600 font-lora mb-1">Trimestre de defunción:</label>
                                            <select id="quarter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                                <option value="">Seleccionar trimestre</option>
                                                <option value="1">Q1 (Ene-Mar)</option>
                                                <option value="2">Q2 (Abr-Jun)</option>
                                                <option value="3">Q3 (Jul-Sep)</option>
                                                <option value="4">Q4 (Oct-Dic)</option>
                                            </select>
                                        </div>

                                        <div id="customDateSelector" style="display: none;">
                                            <div class="filter-group">
                                                <label class="block text-xs text-gray-600 font-lora mb-1">Desde (fecha de defunción):</label>
                                                <input type="date" id="customStartDate" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                            </div>
                                            <div class="filter-group">
                                                <label class="block text-xs text-gray-600 font-lora mb-1">Hasta (fecha de defunción):</label>
                                                <input type="date" id="customEndDate" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                            </div>
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
                                    <select id="municipiosFilter" class="tomselect-select" multiple data-placeholder="Selecciona municipios">
                                        @foreach($municipalities as $mun)
                                            <option value="{{ $mun->id }}">{{ $mun->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filtro de Causas (contextual) -->
                                <div id="filterCausas" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-heartbeat text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Causas</h4>
                                    </div>
                                    <select id="causasFilter" class="tomselect-select" multiple data-placeholder="Selecciona causas">
                                        @foreach($causes as $cause)
                                            <option value="{{ $cause->id }}">{{ $cause->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filtro de Jurisdicciones (contextual) -->
                                @if($jurisdictions->count() > 0)
                                <div id="filterJurisdicciones" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-building text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Jurisdicciones</h4>
                                    </div>
                                    <select id="jurisdiccionesFilter" class="tomselect-select" multiple data-placeholder="Selecciona jurisdicciones">
                                        @foreach($jurisdictions as $jur)
                                            <option value="{{ $jur->id }}">{{ $jur->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <!-- Filtro de Sexo (contextual) -->
                                <div id="filterSexo" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-venus-mars text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Sexo</h4>
                                    </div>
                                    <select id="sexoFilter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                        <option value="">Todos</option>
                                        @foreach($sexes as $sex)
                                            <option value="{{ $sex->value }}">{{ $sex->label }}</option>
                                        @endforeach
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

                                <!-- Toggle de Causas Principales para Edades (contextual) -->
                                <div id="filterCausasPrincipales" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 pb-2 border-b border-gray-200 mb-3">
                                        <i class="fas fa-star text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Información</h4>
                                    </div>
                                    <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                        <input type="checkbox" id="mostrarCausasPrincipales" class="w-4 h-4 rounded border-[#404041] text-[#611132] cursor-pointer">
                                        <span class="text-xs text-gray-700 font-lora">Mostrar causas principales por edad</span>
                                    </label>
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
                                        <option value="barHorizontal">Barras Horizontales</option>
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
                            
                            <!-- Tabla de Causas Principales (solo para Edades) -->
                            <div id="causasPrincipalesContainer" class="hidden mt-6 pt-4 border-t border-gray-200 w-full">
                                <h3 class="text-base font-bold text-[#404041] mb-4 font-lora">Causas Principales de Muerte por Grupo de Edad</h3>
                                <div class="overflow-x-auto w-full mx-auto causas-table-wrapper">
                                    <table class="w-full text-sm border-collapse table-fixed">
                                        <colgroup>
                                            <col style="width: 16%;">
                                            <col style="width: 8%;">
                                            <col style="width: 76%;">
                                        </colgroup>
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-200">
                                                <th class="px-3 py-2 text-left font-semibold text-[#404041] font-lora">Grupo de Edad</th>
                                                <th id="causasTotalHeader" class="px-3 py-2 text-left font-semibold text-[#404041] font-lora">Total</th>
                                                <th id="causasDetalleHeader" class="px-3 py-2 text-left font-semibold text-[#404041] font-lora">Causas Principales</th>
                                            </tr>
                                        </thead>
                                        <tbody id="causasPrincipalesBody" class="align-top">
                                            <!-- Se llena dinámicamente con JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <style>
                                /* Asegurar que la tabla use todo el ancho y las causas se envuelvan para mejor legibilidad */
                                #causasPrincipalesContainer { width: 100%; }
                                #causasPrincipalesContainer .causas-table-wrapper {
                                    width: min(100%, 1020px);
                                    max-width: 88%;
                                }
                                #causasPrincipalesContainer table { table-layout: fixed; }
                                #causasPrincipalesContainer th, #causasPrincipalesContainer td { word-wrap: break-word; white-space: normal; }
                                #causasPrincipalesContainer td:nth-child(3) { white-space: normal; }
                                /* Un poco más de interlineado para legibilidad */
                                #causasPrincipalesContainer td, #causasPrincipalesContainer th { line-height: 1.4; }
                                /* Información adicional compacta: una sola línea con truncado */
                                #causasPrincipalesContainer td.causas-principales-cell {
                                    white-space: nowrap;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    max-width: 0;
                                    font-size: 0.85rem;
                                }
                                /* Separar visualmente ranking (#1) y cantidad para evitar confusión */
                                #causasPrincipalesContainer .causa-chip {
                                    display: inline-flex;
                                    align-items: center;
                                    gap: 0.25rem;
                                }
                                #causasPrincipalesContainer .top-rank-badge {
                                    display: inline-block;
                                    padding: 0.05rem 0.35rem;
                                    border-radius: 9999px;
                                    background: #f3f4f6;
                                    color: #374151;
                                    font-size: 0.72rem;
                                    font-weight: 700;
                                    line-height: 1.2;
                                }
                                #causasPrincipalesContainer .top-count-pill {
                                    display: inline-block;
                                    padding: 0.05rem 0.35rem;
                                    border-radius: 0.3rem;
                                    background: #fef3c7;
                                    color: #78350f;
                                    font-weight: 800;
                                    line-height: 1.2;
                                    border: 1px solid #f59e0b;
                                }

                                @media (max-width: 1024px) {
                                    #causasPrincipalesContainer .causas-table-wrapper { max-width: 100%; }
                                }
                            </style>
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
    <!-- Incluir html2canvas para capturar el DOM cuando sea posible (mejor export visual) -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <!-- Include modal para descargas -->
    @include('components.modal-descargas')

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
            dateRange: 'all',
            startDate: null,
            endDate: null,
            selectedMonths: [],
            selectedYears: [],
            municipios: [],
            municipiosNames: [],
            causas: [],
            causasNames: [],
            jurisdicciones: [],
            jurisdiccionesNames: [],
            sexo: null,
            granularidad: 'month',
            mostrarCausasPrincipales: false
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

        const chartTypeOptions = {
            municipios: ['auto', 'bar', 'barHorizontal', 'pie', 'doughnut'],
            tendencias: ['auto', 'line', 'area'],
            edades: ['auto', 'bar', 'pie', 'doughnut'],
            genero: ['auto', 'bar', 'pie', 'doughnut'],
            causas: ['auto', 'bar', 'barHorizontal', 'pie', 'doughnut'],
            jurisdicciones: ['auto', 'bar', 'barHorizontal', 'pie', 'doughnut'],
            comparativa: ['bar']
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
            edades: ['dates', 'municipios', 'causas', 'jurisdicciones', 'causasPrincipales'],
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

        function initializeTomSelect() {
            // Inicializar Tom Select para multiselects - permite deseleccionar fácilmente
            const multiSelectIds = ['municipiosFilter', 'causasFilter', 'jurisdiccionesFilter'];
            
            multiSelectIds.forEach(id => {
                const element = document.getElementById(id);
                if (element && !element.tomselect && typeof TomSelect !== 'undefined') {
                    new TomSelect(element, {
                        valueField: 'value',
                        labelField: 'text',
                        searchField: 'text',
                        maxOptions: 100,
                        maxItems: null,
                        create: false,
                        placeholder: element.dataset.placeholder || 'Selecciona opciones',
                        hideSelected: false,
                        closeAfterSelect: false,
                        plugins: {
                            'remove_button': {
                                title: 'Eliminar esta selección'
                            }
                        },
                        onChange: (value) => {
                            // Actualizar los filtros mostrados en "Filtros aplicados:"
                            collectFilters();
                            updateActiveFiltersDisplay();
                            updateChart();
                        },
                        onOptionSelect: () => {
                            // Cerrar después de seleccionar para mejorar UX
                            setTimeout(() => {
                                element.tomselect.close();
                            }, 100);
                        }
                    });
                }
            });
        }

        // Intentar inicializar Tom Select inmediatamente
        if (typeof TomSelect !== 'undefined') {
            initializeTomSelect();
            manageTomSelectDropdowns();
        } else {
            // Si Tom Select no está disponible, esperar a que lo esté
            let attempts = 0;
            const checkTomSelect = setInterval(() => {
                if (typeof TomSelect !== 'undefined') {
                    clearInterval(checkTomSelect);
                    initializeTomSelect();
                    manageTomSelectDropdowns();
                }
                attempts++;
                if (attempts > 50) { // Stop after 5 seconds (50 * 100ms)
                    clearInterval(checkTomSelect);
                    console.warn('TomSelect did not load in time');
                }
            }, 100);
        }

        // Manejar dinámicamente el z-index de los dropdowns de Tom Select
        function manageTomSelectDropdowns() {
            const tomSelectElements = document.querySelectorAll('.tomselect-select');
            
            tomSelectElements.forEach(select => {
                // Buscar la instancia de TomSelect asociada
                if (select.tomselect) {
                    const tomSelectInstance = select.tomselect;
                    
                    // Cuando se abre el dropdown
                    tomSelectInstance.on('dropdown_open', function() {
                        const wrapper = tomSelectInstance.wrapper;
                        if (wrapper) {
                            wrapper.classList.add('ts-dropdown-open');
                        }
                    });
                    
                    // Cuando se cierra el dropdown
                    tomSelectInstance.on('dropdown_close', function() {
                        const wrapper = tomSelectInstance.wrapper;
                        if (wrapper) {
                            wrapper.classList.remove('ts-dropdown-open');
                        }
                    });
                }
            });
        }

        function initializeEventListeners() {
            // Inicializar Tom Select para multiselects
            initializeTomSelect();

            document.querySelectorAll('.chart-tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectChart(this.dataset.chart);
                });
            });

            document.getElementById('dateRange').addEventListener('change', onDateRangeChange);
            
            // Event listeners para campos de fecha
            const yearInput = document.getElementById('year');
            if (yearInput) yearInput.addEventListener('change', updateChart);
            
            // Event listener para mes específico (select simple)
            const monthSelect = document.getElementById('month');
            if (monthSelect && monthSelect.tagName === 'SELECT') {
                monthSelect.addEventListener('change', updateChart);
            }
            
            const quarterSelect = document.getElementById('quarter');
            if (quarterSelect) quarterSelect.addEventListener('change', updateChart);
            
            // Manejar checkboxes de meses
            document.querySelectorAll('.month-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateChart();
                });
            });
            
            // Manejar labels de meses para mejor UX usando event delegation
            const monthsContainer = document.querySelector('.months-container');
            if (monthsContainer) {
                monthsContainer.addEventListener('click', function(e) {
                    // Si se hace clic en un label de mes
                    if (e.target.classList.contains('month-label')) {
                        const label = e.target;
                        const checkbox = label.previousElementSibling;
                        if (checkbox && checkbox.classList.contains('month-checkbox')) {
                            checkbox.checked = !checkbox.checked;
                            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                            checkbox.focus();
                        }
                    }
                });
            }
            
            document.getElementById('customStartDate').addEventListener('change', updateChart);
            document.getElementById('customEndDate').addEventListener('change', updateChart);
            // Nota: Los eventos para municipiosFilter, causasFilter, jurisdiccionesFilter 
            // se manejan dentro de Tom Select (onChange), no aquí
            document.getElementById('sexoFilter').addEventListener('change', updateChart);
            document.getElementById('granularidadFilter').addEventListener('change', updateChart);
            document.getElementById('mostrarCausasPrincipales').addEventListener('change', updateChart);

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

            document.getElementById('descargarActual').addEventListener('click', async function() {
                if (!currentEchartsInstance) return;
                
                // Obtener el tipo de serie para determinar si es pie/doughnut
                const opt = currentEchartsInstance.getOption ? currentEchartsInstance.getOption() : null;
                const series = opt && opt.series && opt.series[0] ? opt.series[0] : null;
                const isChartPie = series && (series.type === 'pie');
                
                // Para gráficas pie/doughnut, usar directamente echarts sin html2canvas para evitar leyendas duplicadas
                if (isChartPie) {
                    const dataURL = currentEchartsInstance.getDataURL({ type: 'png', pixelRatio: 2, backgroundColor: '#ffffff' });
                    const link = document.createElement('a');
                    link.href = dataURL;
                    link.download = `grafica-${currentChartType}-${new Date().toISOString().split('T')[0]}.png`;
                    link.click();
                    return;
                }
                
                // Para Edades con tabla de causas, capturar ambos elementos
                if (currentChartType === 'edades' && document.getElementById('mostrarCausasPrincipales').checked) {
                    // Usar html2canvas si está disponible
                    if (typeof html2canvas !== 'undefined') {
                        const element = document.querySelector('[id="mainChart"]').closest('.bg-white');
                        html2canvas(element, {
                            scale: 2,
                            useCORS: true,
                            backgroundColor: '#ffffff'
                        }).then(canvas => {
                            const link = document.createElement('a');
                            link.href = canvas.toDataURL('image/png');
                            link.download = `grafica-${currentChartType}-${new Date().toISOString().split('T')[0]}.png`;
                            link.click();
                        });
                    } else {
                        // Fallback si html2canvas no está disponible, descargar solo el chart
                        const link = document.createElement('a');
                        link.href = currentEchartsInstance.getDataURL({ type: 'png' });
                        link.download = `grafica-${currentChartType}-${new Date().toISOString().split('T')[0]}.png`;
                        link.click();
                    }
                } else {
                    // Para otros charts (barras, líneas), intentar capturar el DOM con html2canvas (más fiable)
                    if (typeof html2canvas !== 'undefined') {
                        const element = document.querySelector('[id="mainChart"]').closest('.bg-white');
                        if (element) {
                            // Construir un contenedor de export compacto: imagen del chart + leyenda
                            try {
                                // Obtener imagen del chart (solo canvas) en alta resolución
                                const chartImgData = currentEchartsInstance.getDataURL({ type: 'png', pixelRatio: 2, backgroundColor: '#ffffff' });

                                // Crear contenedor export
                                const exportDiv = document.createElement('div');
                                exportDiv.style.display = 'flex';
                                exportDiv.style.alignItems = 'center';
                                exportDiv.style.background = '#ffffff';
                                exportDiv.style.padding = '8px 12px';
                                exportDiv.style.borderRadius = '6px';
                                exportDiv.style.fontFamily = getComputedStyle(document.body).fontFamily || 'sans-serif';
                                exportDiv.style.color = '#404041';

                                // Imagen del chart
                                const img = document.createElement('img');
                                img.src = chartImgData;
                                img.style.display = 'block';
                                img.style.maxWidth = 'none';
                                img.style.height = 'auto';
                                img.style.marginRight = '20px';

                                // Construir leyenda a partir de la opción actual
                                const legendDiv = document.createElement('div');
                                legendDiv.style.display = 'flex';
                                legendDiv.style.flexDirection = 'column';
                                legendDiv.style.gap = '8px';

                                const opt = currentEchartsInstance.getOption ? currentEchartsInstance.getOption() : null;
                                const series = opt && opt.series && opt.series[0] ? opt.series[0] : null;
                                const colors = opt && opt.color ? opt.color : null;

                                if (series && series.data && Array.isArray(series.data)) {
                                    series.data.forEach((d, idx) => {
                                        const item = document.createElement('div');
                                        item.style.display = 'flex';
                                        item.style.alignItems = 'center';
                                        item.style.gap = '10px';
                                        item.style.fontSize = '15px';

                                        const sw = document.createElement('span');
                                        sw.style.display = 'inline-block';
                                        // Ajustar tamaño del recuadro de color para ser proporcional a la fuente
                                        sw.style.width = '18px';
                                        sw.style.height = '16px';
                                        sw.style.borderRadius = '3px';
                                        sw.style.background = (colors && colors[idx]) ? colors[idx] : (d.itemStyle && d.itemStyle.color ? d.itemStyle.color : '#ccc');

                                        const label = document.createElement('span');
                                        label.textContent = d.name;

                                        item.appendChild(sw);
                                        item.appendChild(label);
                                        legendDiv.appendChild(item);
                                    });
                                }

                                exportDiv.appendChild(img);
                                exportDiv.appendChild(legendDiv);

                                // Añadir al DOM fuera de pantalla para que html2canvas pueda renderizarlo
                                exportDiv.style.position = 'fixed';
                                exportDiv.style.left = '-9999px';
                                exportDiv.style.top = '0';
                                document.body.appendChild(exportDiv);

                                // Esperar a que la imagen cargue para capturar correctamente
                                img.onload = function() {
                                    html2canvas(exportDiv, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }).then(canvas => {
                                        const dataURL = canvas.toDataURL('image/png');
                                        const arr = dataURL.split(',');
                                        const mime = arr[0].match(/:(.*?);/)[1];
                                        const bstr = atob(arr[1]);
                                        let n = bstr.length;
                                        const u8arr = new Uint8Array(n);
                                        while(n--) { u8arr[n] = bstr.charCodeAt(n); }
                                        const blob = new Blob([u8arr], { type: mime });
                                        const objUrl = URL.createObjectURL(blob);
                                        const link = document.createElement('a');
                                        link.href = objUrl;
                                        link.download = `grafica-${currentChartType}-${new Date().toISOString().split('T')[0]}.png`;
                                        document.body.appendChild(link);
                                        link.click();
                                        link.remove();
                                        setTimeout(() => URL.revokeObjectURL(objUrl), 1000);
                                        exportDiv.remove();
                                    }).catch(err => {
                                        console.warn('html2canvas exportDiv failed, falling back', err);
                                        exportDiv.remove();
                                    });
                                };
                                // If already cached image, trigger onload manually
                                if (img.complete) img.onload();
                                return;
                            } catch (err) {
                                console.warn('compact export failed, falling back to full DOM capture', err);
                            }
                        }
                    }
                    // Para otros charts, renderizar la opción en un contenedor fuera de pantalla
                    // y exportar solo la imagen resultante (sin el padding/contenedor de la UI)
                    try {
                        // Obtener dimensiones reales del chart en pantalla para mantener proporciones
                        const mainChartEl = document.getElementById('mainChart');
                        const rect = mainChartEl ? mainChartEl.getBoundingClientRect() : null;
                        const exportWidth = rect ? Math.max(600, Math.round(rect.width)) : 900;
                        const exportHeight = rect ? Math.max(400, Math.round(rect.height)) : 600;

                        const tmpDiv = document.createElement('div');
                        tmpDiv.style.width = exportWidth + 'px';
                        tmpDiv.style.height = exportHeight + 'px';
                        tmpDiv.style.position = 'fixed';
                        tmpDiv.style.left = '-9999px';
                        tmpDiv.style.top = '-9999px';
                        // Allow rendering even though offscreen
                        // Make it invisible but renderable: use opacity 0 (visibility:hidden may prevent render)
                        tmpDiv.style.opacity = '0';
                        tmpDiv.style.pointerEvents = 'none';
                        document.body.appendChild(tmpDiv);

                        const tmpChart = echarts.init(tmpDiv, null, { width: exportWidth, height: exportHeight, devicePixelRatio: window.devicePixelRatio || 1 });
                        // Construir una opción de export más robusta basada en la opción actual
                        const currentOption = currentEchartsInstance.getOption ? currentEchartsInstance.getOption() : null;
                        let exportOption = currentOption;

                        if (currentOption && currentOption.series && currentOption.series[0]) {
                            const s = currentOption.series[0];
                            if (s.type === 'pie') {
                                const pieData = s.data || [];
                                const colors = currentOption.color || (window && window.colorPalettes && window.colorPalettes[chartConfig.colorPalette]) || [];

                                // calcular dimensiones desde el contenedor real si está disponible
                                // Use actual export dimensions to compute legend position
                                const visW = exportWidth;
                                const visH = exportHeight;

                                const legendLeftPx = Math.round(visW * 0.62);

                                // Calcular radios en píxeles para asegurar que el círculo quepa
                                const outerRadiusPx = Math.floor(Math.min(visW, visH) * 0.42);
                                const innerRadiusPx = chartConfig.type === 'doughnut' ? Math.floor(outerRadiusPx * 0.56) : 0;
                                const centerX = Math.round(visW * 0.36);
                                const centerY = Math.round(visH / 2);

                                exportOption = {
                                    color: colors,
                                    tooltip: { trigger: 'item' },
                                    legend: { show: false },
                                    series: [{
                                        name: s.name || chartTitles[currentChartType] || '',
                                        type: 'pie',
                                        // usar centro y radios en píxeles para export evitar recortes
                                        center: [centerX, centerY],
                                        radius: chartConfig.type === 'doughnut' ? [innerRadiusPx, outerRadiusPx] : outerRadiusPx,
                                        avoidLabelOverlap: true,
                                        data: pieData,
                                        label: {
                                            show: true,
                                            position: 'outside',
                                            distance: 6,
                                            fontSize: 13,
                                            color: '#404041',
                                            overflow: 'none',
                                            width: 180,
                                            formatter: s.label && s.label.formatter ? s.label.formatter : undefined
                                        },
                                        labelLine: { show: true, length: 12, length2: 8 }
                                    }]
                                };
                            }
                        }

                        tmpChart.setOption(exportOption || currentOption, true);

                        // Esperar un momento para que echarts complete el render fuera de pantalla
                        await new Promise(resolve => setTimeout(resolve, 200));

                        const dataURL = tmpChart.getDataURL({ type: 'png', pixelRatio: 2, backgroundColor: '#ffffff' });
                        tmpChart.dispose();
                        tmpDiv.remove();

                        // Convertir dataURL a Blob para evitar problemas con navegadores
                        function dataURLtoBlob(dataurl) {
                            const arr = dataurl.split(',');
                            const mime = arr[0].match(/:(.*?);/)[1];
                            const bstr = atob(arr[1]);
                            let n = bstr.length;
                            const u8arr = new Uint8Array(n);
                            while(n--) {
                                u8arr[n] = bstr.charCodeAt(n);
                            }
                            return new Blob([u8arr], { type: mime });
                        }

                        const blob = dataURLtoBlob(dataURL);
                        const objUrl = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = objUrl;
                        link.download = `grafica-${currentChartType}-${new Date().toISOString().split('T')[0]}.png`;
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        setTimeout(() => URL.revokeObjectURL(objUrl), 1000);
                    } catch (e) {
                        // Fallback a getDataURL directo si algo falla
                        try {
                            const fallbackData = currentEchartsInstance.getDataURL({ type: 'png', pixelRatio: 2, backgroundColor: '#ffffff' });
                            const blobFb = (function(dataurl){
                                const arr = dataurl.split(',');
                                const mime = arr[0].match(/:(.*?);/)[1];
                                const bstr = atob(arr[1]);
                                let n = bstr.length;
                                const u8arr = new Uint8Array(n);
                                while(n--) { u8arr[n] = bstr.charCodeAt(n); }
                                return new Blob([u8arr], { type: mime });
                            })(fallbackData);
                            const objUrlFb = URL.createObjectURL(blobFb);
                            const linkFb = document.createElement('a');
                            linkFb.href = objUrlFb;
                            linkFb.download = `grafica-${currentChartType}-${new Date().toISOString().split('T')[0]}.png`;
                            document.body.appendChild(linkFb);
                            linkFb.click();
                            linkFb.remove();
                            setTimeout(() => URL.revokeObjectURL(objUrlFb), 1000);
                        } catch (e2) {
                            console.error('Fallback download failed', e2);
                        }
                    }
                }
            });
        }

        function selectChart(chartType) {
            currentChartType = chartType;
            document.querySelectorAll('.chart-tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.chart === chartType);
            });
            updateVisibleFilters(chartType);
            
            // Ocultar tabla de causas si no es Edades
            const causasContainer = document.getElementById('causasPrincipalesContainer');
            if (causasContainer) {
                causasContainer.classList.add('hidden');
            }
            
            // Al cambiar de pestaña, limpiar filtros pero evitar recargar dos veces la gráfica.
            clearFilters(true);
            document.getElementById('chartTypeSelector').value = 'auto';
            chartConfig.type = chartTypeDefaults[chartType];
            loadChart(chartType);
        }

        function updateVisibleFilters(chartType) {
            const allFilters = ['filterMunicipios', 'filterCausas', 'filterJurisdicciones', 'filterSexo', 'filterGranularidad', 'filterCausasPrincipales'];
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
                    if (filterId === 'filterCausasPrincipales' && availableFilters.includes('causasPrincipales')) show = true;
                    element.style.display = show ? 'block' : 'none';
                }
            });
            updateChartTypeOptions(chartType);
        }

        function updateChartTypeOptions(chartType) {
            const selector = document.getElementById('chartTypeSelector');
            if (!selector) return;
            const availableTypes = chartTypeOptions[chartType] || ['auto', 'bar'];
            const currentValue = selector.value;
            const allOptions = {
                'auto': 'Auto',
                'bar': 'Barras',
                'barHorizontal': 'Barras Horizontales',
                'pie': 'Pastel',
                'doughnut': 'Rosquilla',
                'line': 'Línea',
                'area': 'Área'
            };
            selector.innerHTML = '';
            availableTypes.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = allOptions[type] || type;
                selector.appendChild(option);
            });
            if (!availableTypes.includes(currentValue)) {
                selector.value = availableTypes[0];
                chartConfig.type = currentValue === 'auto' ? chartTypeDefaults[chartType] : currentValue;
            } else {
                selector.value = currentValue;
            }
        }

        function onDateRangeChange() {
            const value = document.getElementById('dateRange').value;
            const yearSelector = document.getElementById('yearSelector');
            const monthSimpleSelector = document.getElementById('monthSimpleSelector');
            const monthSelector = document.getElementById('monthSelector');
            const quarterSelector = document.getElementById('quarterSelector');
            const customDateSelector = document.getElementById('customDateSelector');
            
            // Ocultar todo
            [yearSelector, monthSimpleSelector, monthSelector, quarterSelector, customDateSelector].forEach(el => {
                if (el) el.style.display = 'none';
            });
            
            // Mostrar según la opción seleccionada
            switch(value) {
                    case 'years':
                        if (yearSelector) yearSelector.style.display = 'block';
                        break;
                    case 'months':
                        if (yearSelector) yearSelector.style.display = 'block';
                        if (monthSelector) monthSelector.style.display = 'block';
                        // Agregar event listeners a los labels cuando se muestren meses
                        setTimeout(() => {
                            setupMonthLabels();
                        }, 100);
                        break;
                case 'quarter':
                    if (yearSelector) yearSelector.style.display = 'block';
                    if (quarterSelector) quarterSelector.style.display = 'block';
                    break;
                case 'custom':
                    if (customDateSelector) customDateSelector.style.display = 'block';
                    break;
                default:
                    // all - no mostrar nada extra
                    break;
            }
            
            updateChart();
        }

        function setupMonthLabels() {
            const monthsContainer = document.querySelector('.months-container');
            if (!monthsContainer) return;
            
            // Limpiar listeners previos removiendo y recreando el contenedor
            const parent = monthsContainer.parentElement;
            const newContainer = monthsContainer.cloneNode(true);
            parent.replaceChild(newContainer, monthsContainer);
            
            // Obtener el contenedor actualizado
            const updated = document.querySelector('.months-container');
            if (!updated) return;
            
            // Re-agregar listeners a los checkboxes clonados
            updated.querySelectorAll('.month-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateChart();
                });
            });
            
            // Agregar listener con event delegation para los labels
            updated.addEventListener('click', handleMonthLabelClick);
        }
        
        function handleMonthLabelClick(e) {
            if (e.target.classList.contains('month-label')) {
                e.preventDefault();
                const label = e.target;
                const checkbox = label.previousElementSibling;
                if (checkbox && checkbox.classList.contains('month-checkbox')) {
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }

        function collectFilters() {
            const dateRange = document.getElementById('dateRange').value;
            let startDate = null, endDate = null;
            const currentYear = new Date().getFullYear();

            if (dateRange === 'years') {
                const yearVal = document.getElementById('year').value;
                if (yearVal) {
                    // Parsear años separados por coma
                    const yearsRaw = yearVal.split(',').map(y => y.trim()).filter(y => y !== '');
                    const yearsNums = yearsRaw.map(y => Number(y)).filter(y => !isNaN(y) && y > 0);
                    if (yearsNums.length > 0) {
                        yearsNums.sort((a, b) => a - b);
                        const minY = Math.min(...yearsNums);
                        const maxY = Math.max(...yearsNums);
                        startDate = `${minY}-01-01`;
                        endDate = `${maxY}-12-31`;
                        activeFilters.selectedYears = yearsNums;
                    }
                }
            } else if (dateRange === 'months') {
                const yearVal = document.getElementById('year').value;
                const checkedMonths = Array.from(document.querySelectorAll('.month-checkbox:checked')).map(cb => cb.value);
                if (checkedMonths.length > 0 && yearVal) {
                    // Parsear años separados por coma
                    const yearsRaw = yearVal.split(',').map(y => y.trim()).filter(y => y !== '');
                    const yearsNums = yearsRaw.map(y => Number(y)).filter(y => !isNaN(y) && y > 0).sort((a, b) => a - b);
                    const monthsNums = checkedMonths.map(m => parseInt(m));
                    if (yearsNums.length > 0) {
                        // construir periodos y calcular primero/ultimo periodo
                        let periods = [];
                        yearsNums.forEach(y => monthsNums.forEach(m => periods.push(y * 100 + m)));
                        const minPeriod = Math.min(...periods);
                        const maxPeriod = Math.max(...periods);
                        const startYear = Math.floor(minPeriod / 100);
                        const startMonth = minPeriod % 100;
                        const endYear = Math.floor(maxPeriod / 100);
                        const endMonth = maxPeriod % 100;
                        startDate = `${startYear}-${String(startMonth).padStart(2, '0')}-01`;
                        const nextMonth = endMonth === 12 ? `${endYear + 1}-01-01` : `${endYear}-${String(endMonth + 1).padStart(2, '0')}-01`;
                        const endDateObj = new Date(nextMonth);
                        endDateObj.setDate(endDateObj.getDate() - 1);
                        endDate = endDateObj.toISOString().split('T')[0];
                        activeFilters.selectedMonths = monthsNums.sort((a,b) => a - b);
                        activeFilters.selectedYears = yearsNums;
                    }
                }
            } else if (dateRange === 'quarter') {
                const year = document.getElementById('year').value || currentYear;
                const quarter = document.getElementById('quarter').value;
                if (quarter) {
                    const startMonth = (parseInt(quarter) - 1) * 3 + 1;
                    const endMonth = startMonth + 2;
                    startDate = `${year}-${String(startMonth).padStart(2, '0')}-01`;
                    const nextQuarterStart = parseInt(quarter) === 4 ? `${parseInt(year) + 1}-01-01` : `${year}-${String((parseInt(quarter) * 3) + 1).padStart(2, '0')}-01`;
                    const endDateObj = new Date(nextQuarterStart);
                    endDateObj.setDate(endDateObj.getDate() - 1);
                    endDate = endDateObj.toISOString().split('T')[0];
                }
            } else if (dateRange === 'custom') {
                startDate = document.getElementById('customStartDate').value;
                endDate = document.getElementById('customEndDate').value;
            }

            activeFilters.dateRange = dateRange;
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
            activeFilters.mostrarCausasPrincipales = document.getElementById('mostrarCausasPrincipales').checked || false;
            
            updateActiveFiltersDisplay();
        }

        function getDateFilterText() {
            const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            const monthFullNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            
            const dateRange = activeFilters.dateRange;
            const startDate = activeFilters.startDate;
            const endDate = activeFilters.endDate;
            
            if (!startDate || !endDate) return null;
            
            const year = startDate.split('-')[0];
            const startMonth = parseInt(startDate.split('-')[1]);
            const endMonth = parseInt(endDate.split('-')[1]);
            
            switch(dateRange) {
                case 'years':
                    const yrs = activeFilters.selectedYears || [];
                    if (yrs.length === 0) return null;
                    if (yrs.length === 1) return `${yrs[0]}`;
                    // Verificar si los años son consecutivos
                    let consecY = true;
                    for (let i = 1; i < yrs.length; i++) if (yrs[i] !== yrs[i-1] + 1) { consecY = false; break; }
                    if (consecY) return `${yrs[0]}-${yrs[yrs.length - 1]}`;
                    return yrs.join(', ');

                case 'months':
                    // Obtener meses y años seleccionados desde activeFilters
                    const selMonths = activeFilters.selectedMonths || [];
                    const selYears = activeFilters.selectedYears || [];
                    if (selMonths.length === 0) return null;
                    // Si sólo hay un año seleccionado, mostrar meses con ese año
                    if (selYears.length === 1) {
                        const y0 = selYears[0];
                        // Verificar si los meses son consecutivos
                        let isConsecutive = true;
                        for (let i = 1; i < selMonths.length; i++) {
                            if (selMonths[i] !== selMonths[i-1] + 1) { isConsecutive = false; break; }
                        }
                        if (isConsecutive && selMonths.length > 1) {
                            return `${monthNames[selMonths[0] - 1]}-${monthNames[selMonths[selMonths.length - 1] - 1]} ${y0}`;
                        } else {
                            return `${selMonths.map(m => monthNames[m - 1]).join(', ')} ${y0}`;
                        }
                    }
                    // Varios años seleccionados: mostrar meses y rango/lista de años
                    const yrsText = selYears.length > 0 ? (
                        selYears.length === 2 && selYears[1] === selYears[0] + 1 
                            ? `${selYears[0]}-${selYears[1]}` 
                            : selYears.join(', ')
                    ) : '';
                    return `${selMonths.map(m => monthNames[m - 1]).join(', ')} (${yrsText})`;
                    // Obtener meses seleccionados desde activeFilters
                    const selectedMonths = activeFilters.selectedMonths || [];
                    if (selectedMonths.length === 0) return null;
                    
                    // Verificar si los meses son consecutivos
                    let isConsecutive = true;
                    for (let i = 1; i < selectedMonths.length; i++) {
                        if (selectedMonths[i] !== selectedMonths[i-1] + 1) {
                            isConsecutive = false;
                            break;
                        }
                    }
                    
                    if (isConsecutive && selectedMonths.length > 1) {
                        // Meses consecutivos: "Ene-Mar 2026"
                        return `${monthNames[selectedMonths[0] - 1]}-${monthNames[selectedMonths[selectedMonths.length - 1] - 1]} ${year}`;
                    } else {
                        // Meses no consecutivos: "Ene, Abr, Jul, Ago 2026"
                        return `${selectedMonths.map(m => monthNames[m - 1]).join(', ')} ${year}`;
                    }
                case 'quarter':
                    const quarter = Math.ceil(startMonth / 3);
                    return `Q${quarter} ${year}`;
                case 'custom':
                    return `${startDate} a ${endDate}`;
                default:
                    return null;
            }
        }

        function updateActiveFiltersDisplay() {
            const container = document.getElementById('filtrosActivosList');
            const section = document.getElementById('filtrosActivos');
            container.innerHTML = '';
            
            let hasActiveFilters = false;

            // Mostrar rango de fechas
            const dateText = getDateFilterText();
            if (dateText) {
                container.innerHTML += `<span class="inline-flex items-center gap-1 bg-[#611132] text-white text-xs px-2.5 py-1 rounded-full font-lora">
                    <i class="fas fa-calendar-alt"></i>
                    ${dateText}
                    <button onclick="clearDateFilter()" class="ml-1 hover:opacity-70">×</button>
                </span>`;
                hasActiveFilters = true;
            }

            // Mostrar municipios seleccionados
            if (activeFilters.municipios.length > 0) {
                const municipiosText = activeFilters.municipiosNames.join(', ');
                container.innerHTML += `<span class="inline-flex items-center gap-1 bg-[#8B6F47] text-white text-xs px-2.5 py-1 rounded-full font-lora">
                    <i class="fas fa-city"></i>
                    ${municipiosText}
                    <button onclick="clearFilter('municipios')" class="ml-1 hover:opacity-70">×</button>
                </span>`;
                hasActiveFilters = true;
            }

            // Mostrar causas seleccionadas
            if (activeFilters.causas.length > 0) {
                const causasText = activeFilters.causasNames.join(', ');
                container.innerHTML += `<span class="inline-flex items-center gap-1 bg-[#2C5F5D] text-white text-xs px-2.5 py-1 rounded-full font-lora">
                    <i class="fas fa-heartbeat"></i>
                    ${causasText}
                    <button onclick="clearFilter('causas')" class="ml-1 hover:opacity-70">×</button>
                </span>`;
                hasActiveFilters = true;
            }

            // Mostrar jurisdicciones seleccionadas
            if (activeFilters.jurisdicciones.length > 0) {
                const jurisdiccionesText = activeFilters.jurisdiccionesNames.join(', ');
                container.innerHTML += `<span class="inline-flex items-center gap-1 bg-[#9B4D6F] text-white text-xs px-2.5 py-1 rounded-full font-lora">
                    <i class="fas fa-building"></i>
                    ${jurisdiccionesText}
                    <button onclick="clearFilter('jurisdicciones')" class="ml-1 hover:opacity-70">×</button>
                </span>`;
                hasActiveFilters = true;
            }

            // Mostrar sexo seleccionado
            if (activeFilters.sexo) {
                const sexoLabel = activeFilters.sexo === 'M' ? 'Hombre' : (activeFilters.sexo === 'F' ? 'Mujer' : activeFilters.sexo);
                container.innerHTML += `<span class="inline-flex items-center gap-1 bg-[#4A7C7E] text-white text-xs px-2.5 py-1 rounded-full font-lora">
                    <i class="fas fa-venus-mars"></i>
                    ${sexoLabel}
                    <button onclick="clearFilter('sexo')" class="ml-1 hover:opacity-70">×</button>
                </span>`;
                hasActiveFilters = true;
            }

            // Mostrar sección si hay filtros activos
            section.classList.toggle('hidden', !hasActiveFilters);
        }

        function clearDateFilter() {
            document.getElementById('dateRange').value = 'all';
            ['yearSelector','monthSimpleSelector','monthSelector','quarterSelector','customDateSelector'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
            // Limpiar input de año
            document.getElementById('year').value = '';
            document.getElementById('quarter').value = '';
            document.getElementById('customStartDate').value = '';
            document.getElementById('customEndDate').value = '';
            // Limpiar checkboxes de meses
            document.querySelectorAll('.month-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            activeFilters.selectedMonths = [];
            activeFilters.selectedYears = [];
            collectFilters();
            updateChart();
        }

        function clearFilter(filterType) {
            if (filterType === 'municipios') {
                const el = document.getElementById('municipiosFilter');
                el.value = '';
                if (el.tomselect) el.tomselect.clear();
            } else if (filterType === 'causas') {
                const el = document.getElementById('causasFilter');
                el.value = '';
                if (el.tomselect) el.tomselect.clear();
            } else if (filterType === 'jurisdicciones') {
                const el = document.getElementById('jurisdiccionesFilter');
                el.value = '';
                if (el.tomselect) el.tomselect.clear();
            } else if (filterType === 'sexo') {
                document.getElementById('sexoFilter').value = '';
            }
            collectFilters();
            updateActiveFiltersDisplay();
            updateChart();
        }

        function loadChart(chartType) {
            showLoadingMessage();
            collectFilters();

            const filters = {
                ...(activeFilters.startDate && { start_date: activeFilters.startDate }),
                ...(activeFilters.endDate && { end_date: activeFilters.endDate }),
                ...(activeFilters.selectedMonths.length && { months: activeFilters.selectedMonths }),
                ...(activeFilters.selectedYears.length && { years: activeFilters.selectedYears }),
                ...(activeFilters.municipios.length && { municipios: activeFilters.municipios }),
                ...(activeFilters.causas.length && { causas: activeFilters.causas }),
                ...(activeFilters.jurisdicciones.length && { jurisdicciones: activeFilters.jurisdicciones }),
                ...(activeFilters.sexo && { sex: activeFilters.sexo }),
                ...(chartConfig.limit && { limit: chartConfig.limit }),
                ...(chartType === 'tendencias' && { group_by: activeFilters.granularidad })
            };

            const params = new URLSearchParams();
            Object.keys(filters).forEach(k => {
                const v = filters[k];
                if (Array.isArray(v)) {
                    v.forEach(item => params.append(k + '[]', item));
                } else {
                    params.append(k, v);
                }
            });
            fetch(`{{ route('api.chart.data') }}/` + chartType + '?' + params.toString())
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
            const chartWrapper = chartContainer.closest('.chart-wrapper');
            const axisFontSize = 14;
            const valueLabelFontSize = 13;
            const legendFontSize = 15;
            const expandedCircularCharts = ['municipios', 'edades', 'genero', 'causas', 'jurisdicciones'].includes(currentChartType);
            const formatNumber = (num) => Number(num || 0).toLocaleString('es-MX');
            const labelRich = {
                value: { fontWeight: 800, color: '#1f2937', fontSize: expandedCircularCharts ? 18 : valueLabelFontSize + 1 },
                percent: { fontWeight: 800, color: '#1f2937', fontSize: expandedCircularCharts ? 18 : valueLabelFontSize + 1 },
                normal: { fontWeight: 600, color: '#404041', fontSize: expandedCircularCharts ? 16 : valueLabelFontSize }
            };
            
            // Limpiar instancia anterior completamente
            if (currentEchartsInstance) {
                currentEchartsInstance.dispose();
            }

            const isPieLikeChart = chartConfig.type === 'auto'
                ? ['pie', 'doughnut'].includes(getOptimalChartType(currentChartType))
                : ['pie', 'doughnut'].includes(chartConfig.type);

            if (chartWrapper) {
                chartWrapper.style.height = isPieLikeChart ? '520px' : '400px';
                if (isPieLikeChart) {
                    // Mostrar el chart en modo compacto: solo espacio para la gráfica + leyenda
                    chartWrapper.style.display = 'flex';
                    chartWrapper.style.justifyContent = 'center';
                    chartWrapper.style.alignItems = 'center';
                } else {
                    chartWrapper.style.display = '';
                    chartWrapper.style.justifyContent = '';
                    chartWrapper.style.alignItems = '';
                    // Reset chart container width when not using compact pie layout
                    if (chartContainer) chartContainer.style.width = '';
                }
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
            
            // Detectar si es área antes de convertir a line
            const isAreaChart = chartType === 'area';
            
            // Si el usuario selecciona "area", trata como "line" con areaStyle
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
                    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' }, textStyle: { fontSize: axisFontSize } },
                    legend: {
                        data: ['Residencia', 'Lugar de Defunción'],
                        bottom: 10,
                        itemWidth: 18,
                        itemHeight: 16,
                        textStyle: { fontSize: legendFontSize, color: '#404041' }
                    },
                    grid: { left: '3%', right: '4%', bottom: '15%', top: '15%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        data: labels,
                        axisLabel: { rotate: 45, interval: 0, fontSize: axisFontSize, color: '#404041' }
                    },
                    yAxis: {
                        type: 'value',
                        axisLabel: { fontSize: axisFontSize, color: '#404041' }
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
                // Calcular medidas en píxeles para ocupar solo lo necesario
                let pieDiameterPx = 0;
                let legendEstimatePx = 180; // ancho estimado para la leyenda
                let legendRightOffset = 12;
                let containerHeightAdjusted = null;
                
                if (chartWrapper) {
                    const wrapperRect = chartWrapper.getBoundingClientRect();
                    const wrapperH = Math.max(380, wrapperRect.height || 520);
                    const pieScale = expandedCircularCharts ? 1.12 : 0.9;
                    pieDiameterPx = Math.floor(wrapperH * pieScale);
                } else {
                    pieDiameterPx = expandedCircularCharts ? 520 : 420;
                }

                // Para 'edades', 'genero' y 'causas' (gráficas expandidas): ajustar leyenda
                if (expandedCircularCharts) {
                    legendEstimatePx = Math.max(280, pieData.length * 16 + 80);
                    legendRightOffset = 60;
                    if (chartWrapper) {
                        containerHeightAdjusted = Math.max(700, pieDiameterPx + 160);
                        chartWrapper.style.height = containerHeightAdjusted + 'px';
                    }
                }

                // Ajustar ancho del contenedor del canvas para que no ocupe todo el espacio
                let estimatedTotalWidth = pieDiameterPx + legendEstimatePx + 40;
                if (expandedCircularCharts) {
                    // Espacio generoso para que los números de la izquierda no se corten y la leyenda tenga distancia
                    estimatedTotalWidth = pieDiameterPx + legendEstimatePx + 300;
                }
                chartContainer.style.width = estimatedTotalWidth + 'px';
                chartContainer.style.margin = '0 auto';

                // Calcular centro y radios en píxeles para que el pie esté a la izquierda y la leyenda a la derecha
                let centerX = Math.round(pieDiameterPx / 2 + 20) + 'px';
                const centerY = '50%';
                let outerRadius = Math.round(pieDiameterPx / 2) + 'px';
                let innerRadius = chartType === 'doughnut' ? Math.round(pieDiameterPx * 0.35) + 'px' : null;

                // Para 'edades', 'genero' y 'causas' (gráficas expandidas): mover el pie más a la derecha y agrandar el radio
                if (expandedCircularCharts) {
                    centerX = Math.round(pieDiameterPx / 2 + 140) + 'px';
                    outerRadius = Math.round(pieDiameterPx * 0.50) + 'px';
                    if (chartType === 'doughnut') {
                        innerRadius = Math.round(pieDiameterPx * 0.31) + 'px';
                    }
                }

                // Determinar tamaño de fuente de leyenda según el tipo de gráfica
                let legendFontSizeActual = 15;
                if (expandedCircularCharts) {
                    // Para causas, reducir fuente un poco para que quepa mejor
                    legendFontSizeActual = currentChartType === 'causas' ? 13 : 18;
                }

                option = {
                    color: colors,
                    // Activar animaciones explícitamente para pie/doughnut
                    animation: true,
                    animationDuration: 800,
                    animationEasing: 'cubicOut',
                    tooltip: { trigger: 'item', textStyle: { fontSize: axisFontSize } },
                    legend: {
                        orient: 'vertical',
                        left: 'auto',
                        right: legendRightOffset,
                        top: 'middle',
                        itemWidth: expandedCircularCharts ? 36 : 18,
                        itemHeight: expandedCircularCharts ? 32 : 16,
                        itemGap: 12,
                        textStyle: {
                            fontSize: legendFontSizeActual,
                            color: '#404041'
                        }
                    },
                    series: [{
                        name: chartTitles[currentChartType],
                        type: 'pie',
                        center: [centerX, centerY],
                        radius: innerRadius ? [innerRadius, outerRadius] : outerRadius,
                        avoidLabelOverlap: true,
                        data: pieData,
                        label: {
                            show: true,
                            position: 'outside',
                            distance: expandedCircularCharts ? 12 : 6,
                            fontSize: expandedCircularCharts ? 18 : 15,
                            fontWeight: 700,
                            color: '#404041',
                            overflow: 'none',
                            width: expandedCircularCharts ? 500 : 180,
                            rich: labelRich,
                            formatter: (params) => {
                                if (chartConfig.dataLabelMode === 'value') {
                                    return `{value|${formatNumber(params.value)}}`;
                                } else if (chartConfig.dataLabelMode === 'percent') {
                                    return `{percent|${params.percent}%}`;
                                } else if (chartConfig.dataLabelMode === 'both') {
                                    return `{value|${formatNumber(params.value)}} {normal|(${params.percent}%)}`;
                                }
                                return `{normal|${params.name}}`;
                            }
                        },
                        labelLine: {
                            show: true,
                            length: 12,
                            length2: expandedCircularCharts ? 18 : 8
                        }
                    }]
                };
            } else if (chartType === 'line') {
                // Línea o Área para tendencias
                const seriesConfig = {
                    name: chartTitles[currentChartType],
                    type: 'line',
                    data: values,
                    smooth: false,
                    itemStyle: { color: palette[0] },
                    label: {
                        show: chartConfig.dataLabelMode !== 'none',
                        position: 'top',
                        fontSize: valueLabelFontSize,
                        fontWeight: 800,
                        color: '#404041',
                        rich: labelRich,
                        formatter: (params) => {
                            return `{value|${formatNumber(params.value)}}`;
                        }
                    }
                };

                // Solo agregar areaStyle si es gráfica de área
                if (isAreaChart) {
                    seriesConfig.areaStyle = { 
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: palette[0] + 'cc' }, 
                            { offset: 1, color: palette[0] + '00' }
                        ]) 
                    };
                }

                option = {
                    color: colors,
                    title: { text: '' },
                    tooltip: { trigger: 'axis', axisPointer: { type: 'cross' }, textStyle: { fontSize: axisFontSize } },
                    grid: { left: '3%', right: '4%', bottom: '10%', top: '10%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        data: labels,
                        boundaryGap: false,
                        axisLabel: { rotate: 45, interval: 'auto', fontSize: axisFontSize, color: '#404041' }
                    },
                    yAxis: {
                        type: 'value',
                        axisLabel: { fontSize: axisFontSize, color: '#404041' }
                    },
                    series: [seriesConfig]
                };
            } else if (chartType === 'bar' || chartType === 'barHorizontal') {
                // Barras verticales u horizontales
                const isHorizontal = chartType === 'barHorizontal';
                
                // Preparar tooltip especial para Edades con causas principales
                let tooltipFormatter = null;
                if (currentChartType === 'edades' && activeFilters.mostrarCausasPrincipales && data.data_with_causes) {
                    tooltipFormatter = (params) => {
                        if (params.length === 0) return '';
                        const dataIndex = params[0].dataIndex;
                        const item = data.data_with_causes[dataIndex];
                        if (!item || !item.top_causes) return params[0].name + ': ' + params[0].value;
                        
                        let html = `<div style="font-weight:bold;">${item.range}</div>`;
                        html += `<div>Total: ${item.total} defunciones</div>`;
                        if (Object.keys(item.top_causes).length > 0) {
                            html += `<div style="margin-top:5px; font-weight:bold; font-size:0.85em;">Causas principales:</div>`;
                            Object.entries(item.top_causes).forEach(([cause, count], idx) => {
                                const pct = ((count / item.total) * 100).toFixed(1);
                                html += `<div style="font-size:0.85em;">• ${cause}: ${count} (${pct}%)</div>`;
                            });
                        }
                        return html;
                    };
                }
                
                option = {
                    color: colors,
                    title: { text: '' },
                    tooltip: { 
                        trigger: 'axis', 
                        axisPointer: { type: 'shadow' },
                        textStyle: { fontSize: axisFontSize },
                        ...(tooltipFormatter ? { formatter: tooltipFormatter } : {})
                    },
                    grid: { left: isHorizontal ? '20%' : '3%', right: '4%', bottom: isHorizontal ? '3%' : '15%', top: '10%', containLabel: true },
                    [isHorizontal ? 'xAxis' : 'yAxis']: {
                        type: 'value',
                        axisLabel: { fontSize: axisFontSize, color: '#404041' }
                    },
                    [isHorizontal ? 'yAxis' : 'xAxis']: {
                        type: 'category',
                        data: labels,
                        axisLabel: { interval: 0, fontSize: axisFontSize, color: '#404041' },
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
                            fontSize: valueLabelFontSize,
                            fontWeight: 800,
                            color: '#404041',
                            rich: labelRich,
                            formatter: (params) => {
                                const total = values.reduce((a, b) => a + b, 0);
                                if (chartConfig.dataLabelMode === 'value') return `{value|${formatNumber(params.value)}}`;
                                if (chartConfig.dataLabelMode === 'percent') {
                                    return `{percent|${((params.value / total) * 100).toFixed(1)}%}`;
                                }
                                if (chartConfig.dataLabelMode === 'both') {
                                    return `{value|${formatNumber(params.value)}} {normal|(${((params.value / total) * 100).toFixed(1)}%)}`;
                                }
                                return '';
                            }
                        }
                    }]
                };
            }

            // Aplicar opción y preservar animaciones: para pie/rosquilla
            // hacemos resize ANTES de setOption para que echarts inicie
            // con las dimensiones correctas y ejecute la animación inicial.
            function applyOptionAndFinish() {
                try {
                    currentEchartsInstance.setOption(option, true);
                } catch (e) {
                    console.warn('setOption failed', e);
                }
                hideLoadingMessage();
            }

            try {
                if (isPieLikeChart) {
                    // Pequeño timeout para permitir que el DOM aplique estilos antes del resize
                    setTimeout(() => {
                        if (currentEchartsInstance && typeof currentEchartsInstance.resize === 'function') {
                            currentEchartsInstance.resize();
                        }
                        applyOptionAndFinish();
                    }, 60);
                } else {
                    applyOptionAndFinish();
                }
            } catch (e) {
                console.warn('apply option failed', e);
                applyOptionAndFinish();
            }
            
            // Actualizar tabla de causas principales si es Edades
            if (currentChartType === 'edades' && data.data_with_causes) {
                updateCausasTable(data.data_with_causes, data.total || 0);
            }
        }
        
        function updateCausasTable(dataWithCauses, grandTotal = 0) {
            const container = document.getElementById('causasPrincipalesContainer');
            const tbody = document.getElementById('causasPrincipalesBody');
            const totalHeader = document.getElementById('causasTotalHeader');
            const detailHeader = document.getElementById('causasDetalleHeader');
            
            if (!container || !tbody) return;

            const mode = chartConfig.dataLabelMode;
            const safeGrandTotal = Number(grandTotal || 0);

            if (totalHeader && detailHeader) {
                if (mode === 'percent') {
                    totalHeader.textContent = 'Total (%)';
                    detailHeader.textContent = 'Causas Principales (%)';
                } else if (mode === 'both') {
                    totalHeader.textContent = 'Total (Valor y %)';
                    detailHeader.textContent = 'Causas Principales (Valor y %)';
                } else {
                    totalHeader.textContent = 'Total';
                    detailHeader.textContent = 'Causas Principales';
                }
            }

            const formatPct = (value, total) => {
                const num = Number(value || 0);
                const den = Number(total || 0);
                if (!den) return '0.0%';
                return ((num / den) * 100).toFixed(1) + '%';
            };

            const formatTotal = (value) => {
                const num = Number(value || 0);
                if (mode === 'percent') return formatPct(num, safeGrandTotal);
                if (mode === 'both') return `${num.toLocaleString('es-MX')} (${formatPct(num, safeGrandTotal)})`;
                return num.toLocaleString('es-MX');
            };

            const formatCauseValue = (count, rowTotal) => {
                const num = Number(count || 0);
                if (mode === 'percent') return `<span class="font-bold text-[#1f2937]">${formatPct(num, rowTotal)}</span>`;
                if (mode === 'both') return `<span class="font-bold text-[#1f2937]">${num.toLocaleString('es-MX')} (${formatPct(num, rowTotal)})</span>`;
                return `<span class="font-bold text-[#1f2937]">${num.toLocaleString('es-MX')}</span>`;
            };

            const formatCauseValueText = (count, rowTotal) => {
                const num = Number(count || 0);
                if (mode === 'percent') return formatPct(num, rowTotal);
                if (mode === 'both') return `${num.toLocaleString('es-MX')} (${formatPct(num, rowTotal)})`;
                return num.toLocaleString('es-MX');
            };

            const escapeAttr = (text) => String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
            
            tbody.innerHTML = '';
            
            dataWithCauses.forEach(item => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-200 hover:bg-gray-50';

                const sortedCauses = Object.entries(item.top_causes || {})
                    .map(([cause, count]) => ({ cause, count: Number(count || 0) }))
                    .sort((a, b) => b.count - a.count);

                const causasText = sortedCauses
                    .map((entry, idx) => `<span class="causa-chip"><span class="top-rank-badge">#${idx + 1}</span>${entry.cause}<span class="top-count-pill">${formatCauseValue(entry.count, item.total)}</span></span>`)
                    .join(' · ');

                const causasTitle = sortedCauses
                    .map((entry, idx) => `#${idx + 1}: ${entry.cause} = ${formatCauseValueText(entry.count, item.total)}`)
                    .join(' | ');
                
                row.innerHTML = `
                    <td class="px-3 py-2 font-semibold text-[#611132] font-lora">${item.range}</td>
                    <td class="px-3 py-2 text-[#404041] font-extrabold">${formatTotal(item.total)}</td>
                    <td class="px-3 py-2 text-[#404041] causas-principales-cell" title="${escapeAttr(causasTitle)}">${causasText || 'No disponible'}</td>
                `;
                
                tbody.appendChild(row);
            });
            
            // Mostrar u ocultar la tabla SOLO si estamos en Edades Y el checkbox está marcado
            const shouldShow = currentChartType === 'edades' && document.getElementById('mostrarCausasPrincipales').checked;
            container.classList.toggle('hidden', !shouldShow);
        }

        function getOptimalChartType(metric) {
            // Determinar el tipo de gráfica óptimo para cada métrica
            const typeMap = {
                'municipios': 'bar',               // Barras verticales por defecto
                'tendencias': 'line',              // Línea para tendencias
                'edades': 'bar',                   // Barras verticales para edades
                'genero': 'pie',                   // Pastel para género
                'causas': 'bar',                   // Barras verticales por defecto
                'jurisdicciones': 'bar',           // Barras verticales para jurisdicciones
                'comparativa': 'bar'               // Barras agrupadas para comparativa
            };
            return typeMap[metric] || 'bar';
        }

        function updateChart() {
            loadChart(currentChartType);
        }

        function clearFilters(suppressUpdate = false) {
            const safeSetValue = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.value = value;
            };
            
            safeSetValue('dateRange', 'all');
            safeSetValue('year', '');
            safeSetValue('month', '');
            safeSetValue('quarter', '');
            safeSetValue('customStartDate', '');
            safeSetValue('customEndDate', '');
            safeSetValue('sexoFilter', '');
            safeSetValue('granularidadFilter', 'month');
            
            // Limpiar checkboxes de meses
            document.querySelectorAll('.month-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Limpiar multiselects y actualizar Tom Select
            const multiSelectIds = ['municipiosFilter', 'causasFilter', 'jurisdiccionesFilter'];
            multiSelectIds.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = '';
                    // Si Tom Select está inicializado, actualizar su valor
                    if (element.tomselect) {
                        element.tomselect.clear();
                    }
                }
            });
            
            // Ocultar todos los selectores condicionales
            const selectors = [
                document.getElementById('yearSelector'),
                document.getElementById('monthSimpleSelector'),
                document.getElementById('monthSelector'),
                document.getElementById('quarterSelector'),
                document.getElementById('customDateSelector')
            ];
            selectors.forEach(selector => {
                if (selector) selector.style.display = 'none';
            });
            
            updateActiveFiltersDisplay();
            if (!suppressUpdate) updateChart();
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
            transition: all 0.2s ease;
            background: rgba(0, 0, 0, 0.2);
            border: none;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
            line-height: 1;
            flex-shrink: 0;
            min-width: 16px;
        }

        #filtrosActivosList button:hover {
            background: rgba(0, 0, 0, 0.4);
            transform: scale(1.15);
        }

        #filtrosActivosList button {
            font-weight: normal;
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

        /* Estilos para checkboxes de meses */
        .month-checkbox {
            display: none;
        }

        .month-label {
            display: block;
            text-center;
            text-xs;
            padding: 0.375rem 0;
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
        }

        .month-label:hover {
            background-color: #e5e7eb;
            border-color: #9ca3af;
        }

        .month-checkbox:checked + .month-label {
            background-color: #611132;
            color: white;
            border-color: #611132;
            font-weight: 500;
        }

        .month-checkbox:focus + .month-label {
            outline: 2px solid #611132;
            outline-offset: 1px;
        }

        /* === Tom Select Styling === */
        #estadisticas-filtros {
            overflow: visible !important;
            position: relative;
        }

        #estadisticas-filtros > div {
            overflow: visible !important;
        }

        #estadisticas-filtros .px-4 {
            overflow: visible !important;
        }

        select.tomselect-select {
            position: absolute !important;
            left: -9999px !important;
            width: 1px !important;
            height: 1px !important;
            overflow: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
            border: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            display: none !important;
        }

        select.tomselect-select::-ms-expand { display: none !important; }
        select.tomselect-select { 
            background-image: none !important;
            visibility: hidden !important;
        }

        .ts-wrapper { 
            display: block; 
            width: 100%;
            position: relative;
            z-index: 9999 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .ts-control {
            z-index: 9999 !important;
            position: relative;
            border: 1px solid #404041 !important;
            border-radius: 0.5rem !important;
            padding: 8px 12px !important;
            background: #ffffff !important;
            font-family: inherit;
            font-size: 0.75rem;
            line-height: 1.25rem !important;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            box-sizing: border-box;
            margin: 0 !important;
            box-shadow: none !important;
            height: auto !important;
            min-height: 36px !important;
            max-height: 36px !important;
            transition: all 0.2s ease;
        }

        .ts-control:focus-within {
            border-color: #404041 !important;
            outline: none !important;
            box-shadow: 0 0 0 1px #611132 !important;
        }

        .ts-control .item, .ts-control input {
            padding: 0 !important;
            margin: 0 !important;
            height: auto !important;
            line-height: 1.25rem !important;
            font-size: inherit;
            font-family: inherit;
        }

        .ts-control .item {
            display: none !important;
        }

        .ts-control input {
            flex: 1;
            min-width: 150px;
        }

        .ts-control input::placeholder {
            color: #9ca3af;
            font-style: italic;
        }
        }

        .ts-control .dropdown-toggle,
        .ts-control .ts-dropdown-toggle,
        .ts-control .dropdown_toggle,
        .ts-control .ts-clear {
            display: none !important;
        }

        .ts-dropdown {
            border: 1px solid #404041;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-height: 250px;
            overflow-y: auto;
            z-index: 999999 !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            background: white;
            margin-top: 2px;
        }

        .ts-dropdown .ts-option {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .ts-dropdown .ts-option:hover {
            background-color: #f3f4f6;
        }

        .ts-dropdown .ts-option.selected {
            background-color: #e5e7eb;
            color: #404041;
        }

        .ts-control::after {
            content: "";
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 12px 12px;
            pointer-events: none;
            opacity: 0.92;
        }

        .ts-wrapper, .ts-control { vertical-align: middle; }

        /* Asegurar que TomSelect dropdown tenga muy alto z-index */
        .filter-section-content {
            position: relative;
        }

        .ts-wrapper.ts-dropdown-open {
            z-index: 999999 !important;
        }

        .ts-wrapper:not(.ts-dropdown-open) {
            z-index: 1 !important;
        }
    </style>

@endsection
