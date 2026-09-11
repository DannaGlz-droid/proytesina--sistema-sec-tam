@extends('layouts.principal')
@section('title', 'Estadísticas')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <main class="statistics-page">
        <div class="statistics-page__inner">
        <x-ui.page-header
            class="statistics-page-header"
            title="Estadísticas interactivas"
            description="Seleccione una métrica y explore los datos de defunciones con filtros personalizados."
        />

        <!-- CONTENEDOR PRINCIPAL -->
        <section class="statistics-shell" aria-label="Panel de estadísticas">
            <div class="statistics-metric-nav">
                <nav class="statistics-metric-tabs" aria-label="Métricas estadísticas" role="tablist">
                    <button type="button" class="chart-tab-btn active" data-chart="municipios" title="Distribución de defunciones por municipio" role="tab" aria-selected="true" aria-controls="statisticsChartPanel" tabindex="0">
                        <span>Municipios</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="tendencias" title="Tendencia temporal de defunciones" role="tab" aria-selected="false" aria-controls="statisticsChartPanel" tabindex="-1">
                        <span>Tendencias</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="edades" title="Distribución por rangos etarios" role="tab" aria-selected="false" aria-controls="statisticsChartPanel" tabindex="-1">
                        <span>Edades</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="genero" title="Distribución por género" role="tab" aria-selected="false" aria-controls="statisticsChartPanel" tabindex="-1">
                        <span>Género</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="causas" title="Causas principales de defunción" role="tab" aria-selected="false" aria-controls="statisticsChartPanel" tabindex="-1">
                        <span>Causas</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="distritoes" title="Distribución por distrito" role="tab" aria-selected="false" aria-controls="statisticsChartPanel" tabindex="-1">
                        <span>Distritos</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="comparativa" title="Residencia frente a lugar de defunción" role="tab" aria-selected="false" aria-controls="statisticsChartPanel" tabindex="-1">
                        <span>Comparativa</span>
                    </button>
                </nav>
            </div>

            <div class="statistics-shell__body">
                <div class="statistics-workbench-toolbar">
                    <x-filtros.boton
                        id="statisticsFiltersToggle"
                        controls="estadisticas-filtros"
                        count-id="statisticsFilterCount"
                        class="statistics-filter-toggle"
                    />
                    <div id="filtrosActivos" class="statistics-active-filters hidden">
                        <div id="filtrosActivosList" class="statistics-active-filters__list users-filter-chips" aria-live="polite">
                            <!-- Los chips se generan dinámicamente con JavaScript -->
                        </div>
                    </div>
                </div>
                <!-- Layout: Filtros + Gráfica -->
                <div class="statistics-layout">
                    
                    <!-- COLUMNA IZQUIERDA - Filtros (DINÁMICOS según gráfica) -->
                    <x-filtros.panel
                        id="estadisticas-filtros"
                        title-id="statistics-filters-title"
                        clear-id="limpiarFiltros"
                        cancel-id="statisticsFiltersCancel"
                        apply-id="statisticsFiltersApply"
                        :open="true"
                        class="statistics-sidebar statistics-filter-popover"
                        body-class="statistics-panel__body statistics-filter-list"
                        footer-class="statistics-filter-actions"
                        cancel-class="statistics-filter-action statistics-filter-action--secondary"
                        apply-class="statistics-filter-action statistics-filter-action--primary"
                    >
                                <x-filtros.seccion titulo="Fecha de defunción" :abierto="true" data-statistics-filter-section>
                                        <div class="statistics-filter-control">
                                            <label for="dateRange">Periodo</label>
                                            <x-filtros.select id="dateRange" placeholder="Todas las fechas">
                                                <option value="all">Todas las fechas</option>
                                                <option value="years">Por año</option>
                                                <option value="months">Por meses</option>
                                                <option value="quarter">Por trimestre</option>
                                                <option value="custom">Rango personalizado</option>
                                            </x-filtros.select>
                                        </div>

                                        <div class="statistics-filter-control" id="yearSelector" style="display: none;">
                                            <label for="year">Año o periodo</label>
                                            @php $currentYear = now()->year; @endphp
                                            <input type="text" id="year" placeholder="Ej. 2026 o 2024-2026" title="Escribe años separados por coma o un periodo">
                                        </div>

                                        <div class="statistics-filter-control" id="monthSelector" style="display: none;">
                                            <span class="statistics-filter-control__label">Meses</span>
                                            <div class="months-container">
                                                @php
                                                    $months = [
                                                        '01' => 'Ene','02' => 'Feb','03' => 'Mar','04' => 'Abr','05' => 'May','06' => 'Jun',
                                                        '07' => 'Jul','08' => 'Ago','09' => 'Sep','10' => 'Oct','11' => 'Nov','12' => 'Dic'
                                                    ];
                                                @endphp
                                                @foreach($months as $mval => $mlabel)
                                                    <div>
                                                        <input type="checkbox" id="month-{{ $mval }}" name="selectedMonths[]" class="month-checkbox" value="{{ $mval }}">
                                                        <label for="month-{{ $mval }}" class="month-label">{{ $mlabel }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="statistics-filter-control" id="quarterSelector" style="display: none;">
                                            <label for="quarter">Trimestre</label>
                                            <x-filtros.select id="quarter" placeholder="Seleccionar trimestre">
                                                <option value="">Seleccionar trimestre</option>
                                                <option value="1">Q1 (Ene–Mar)</option>
                                                <option value="2">Q2 (Abr–Jun)</option>
                                                <option value="3">Q3 (Jul–Sep)</option>
                                                <option value="4">Q4 (Oct–Dic)</option>
                                            </x-filtros.select>
                                        </div>

                                        <div id="customDateSelector" class="statistics-filter-date-grid" style="display: none;">
                                            <label>Desde<input type="date" id="customStartDate"></label>
                                            <label>Hasta<input type="date" id="customEndDate"></label>
                                        </div>
                                </x-filtros.seccion>

                                <x-filtros.seccion id="statisticsLocationFilterGroup" titulo="Ubicación" data-statistics-filter-section data-statistics-filter-group hidden>
                                        <div id="filterTipoMunicipio" class="statistics-filter-control dynamic-filter" style="display: none;">
                                            <label for="tipoMunicipioFilter">Tipo de municipio</label>
                                            <x-filtros.select id="tipoMunicipioFilter" placeholder="Municipio de defunción">
                                                <option value="defuncion">Municipio de defunción</option>
                                                <option value="residencia">Municipio de residencia</option>
                                            </x-filtros.select>
                                        </div>
                                        <div id="filterMunicipios" class="statistics-filter-control dynamic-filter" style="display: none;">
                                            <label for="municipiosFilter">Municipios</label>
                                            <x-filtros.select id="municipiosFilter" placeholder="Selecciona municipios" multiple>
                                                @foreach($municipalities as $mun)
                                                    <option value="{{ $mun->id }}">{{ $mun->name }}</option>
                                                @endforeach
                                            </x-filtros.select>
                                        </div>
                                        @if($districts->count() > 0)
                                        <div id="filterdistritoes" class="statistics-filter-control dynamic-filter" style="display: none;">
                                            <label for="distritoesFilter">Distritos</label>
                                            <x-filtros.select id="distritoesFilter" placeholder="Selecciona distritos" multiple>
                                                @foreach($districts as $district)
                                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                                @endforeach
                                            </x-filtros.select>
                                        </div>
                                        @endif
                                </x-filtros.seccion>

                                <x-filtros.seccion id="statisticsDemographicFilterGroup" titulo="Datos demográficos" data-statistics-filter-section data-statistics-filter-group hidden>
                                        <div id="filterSexo" class="statistics-filter-control dynamic-filter" style="display: none;">
                                            <label for="sexoFilter">Sexo</label>
                                            <x-filtros.select id="sexoFilter" placeholder="Todos">
                                                <option value="">Todos</option>
                                                @foreach($sexes as $sex)
                                                    <option value="{{ $sex->value }}">{{ $sex->label }}</option>
                                                @endforeach
                                            </x-filtros.select>
                                        </div>
                                        <div id="filterEdad" class="statistics-filter-control dynamic-filter" style="display: none;">
                                            <label for="edadFilter">Edad</label>
                                            <input
                                                type="text"
                                                id="edadFilter"
                                                inputmode="numeric"
                                                placeholder="Ej. 25, 20-30 o 5,10,15"
                                                aria-describedby="edadFilterHelp"
                                            >
                                            <small id="edadFilterHelp" class="statistics-filter-help">Edad exacta, rango o valores separados por coma.</small>
                                        </div>
                                </x-filtros.seccion>

                                <x-filtros.seccion id="statisticsCauseFilterGroup" titulo="Causa de defunción" data-statistics-filter-section data-statistics-filter-group hidden>
                                        <div id="filterCausas" class="statistics-filter-control dynamic-filter" style="display: none;">
                                            <label for="causasFilter">Causas</label>
                                            <x-filtros.select id="causasFilter" placeholder="Selecciona causas" multiple>
                                                @foreach($causes as $cause)
                                                    <option value="{{ $cause->id }}">{{ $cause->name }}</option>
                                                @endforeach
                                            </x-filtros.select>
                                        </div>
                                </x-filtros.seccion>

                                <x-filtros.seccion id="statisticsMetricFilterGroup" titulo="Opciones de la métrica" data-statistics-filter-section data-statistics-filter-group hidden>
                                        <div id="filterGranularidad" class="statistics-filter-control dynamic-filter" style="display: none;">
                                            <label for="granularidadFilter">Granularidad</label>
                                            <x-filtros.select id="granularidadFilter" placeholder="Mensual">
                                                <option value="day">Diaria</option>
                                                <option value="month" selected>Mensual</option>
                                                <option value="year">Anual</option>
                                            </x-filtros.select>
                                        </div>
                                        <div id="filterTipoComparativa" class="statistics-filter-control dynamic-filter" style="display: none;">
                                            <label for="tipoComparativaFilter">Tipo de comparativa</label>
                                            <x-filtros.select id="tipoComparativaFilter" placeholder="Residencia frente a lugar de defunción">
                                                <option value="residencia-defuncion">Residencia frente a lugar de defunción</option>
                                                <option value="genero-causa">Género por causa</option>
                                                <option value="edad-causa">Rango etario por causa</option>
                                                <option value="lugar-causa">Lugar de defunción por causa</option>
                                            </x-filtros.select>
                                        </div>
                                        <div id="filterCausasPrincipales" class="statistics-filter-control dynamic-filter" style="display: none;">
                                            <label class="statistics-filter-check-row">
                                                <input type="checkbox" id="mostrarCausasPrincipales">
                                                <span>Mostrar causas principales por edad</span>
                                            </label>
                                        </div>
                                </x-filtros.seccion>
                    </x-filtros.panel>

                    <!-- COLUMNA DERECHA - Gráfica -->
                    <div class="statistics-content">
                        <!-- Controles de Presentación -->
                        <aside class="statistics-display-panel" aria-labelledby="statistics-display-title">
                            <header class="statistics-display-panel__heading">
                                <h2 id="statistics-display-title">Presentación</h2>
                                <p>Ajuste la visualización.</p>
                            </header>
                            <div class="statistics-display-groups">
                                <!-- Tipo de Gráfica -->
                                <div class="statistics-display-group">
                                    <label>Tipo de gráfica</label>
                                    <select id="chartTypeSelector" class="hidden">
                                        <option value="bar">Barras</option>
                                        <option value="barHorizontal">Barras Horizontales</option>
                                        <option value="pie">Pastel</option>
                                        <option value="doughnut">Rosquilla</option>
                                        <option value="line">Línea</option>
                                        <option value="area">Área</option>
                                    </select>
                                    <div id="chartTypeButtons" class="statistics-visual-options"></div>
                                    <p id="chartTypeAdaptationNote" class="statistics-presentation-note hidden" aria-live="polite"></p>
                                </div>

                                <!-- Etiquetas -->
                                <div class="statistics-display-group">
                                    <label>Etiquetas</label>
                                    <select id="datalabelMode" class="hidden">
                                        <option value="value">Solo valores</option>
                                        <option value="percent">Solo porcentaje</option>
                                        <option value="both">Ambos</option>
                                    </select>
                                    <div id="dataLabelButtons" class="statistics-visual-options"></div>
                                    <p id="dataLabelAdaptationNote" class="statistics-presentation-note hidden" aria-live="polite"></p>
                                </div>

                                <!-- Top N -->
                                <div id="filterTop" class="statistics-display-group" style="display: none;">
                                    <label>Cantidad de resultados</label>
                                    <select id="chartLimit" class="hidden">
                                        <option value="all">Todos</option>
                                        <option value="5">Top 5</option>
                                        <option value="10" selected>Top 10</option>
                                        <option value="15">Top 15</option>
                                    </select>
                                    <div id="chartLimitButtons" class="statistics-visual-options"></div>
                                    <p id="chartLimitAdaptationNote" class="statistics-presentation-note hidden" aria-live="polite"></p>
                                </div>

                                <!-- Paleta -->
                                <div class="statistics-display-group statistics-display-group--palette">
                                    <label>Apariencia</label>
                                    <div class="statistics-palette-popover">
                                        <button type="button" id="statisticsPaletteToggle" class="statistics-palette-toggle" aria-expanded="false" aria-controls="statisticsPaletteMenu" aria-haspopup="true">
                                            <span id="statisticsPaletteSelection" class="statistics-palette-selection" aria-hidden="true"></span>
                                            <span id="statisticsPaletteLabel" class="statistics-palette-label">Granate institucional</span>
                                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                        </button>
                                        <div id="statisticsPaletteMenu" class="statistics-palette-menu hidden" aria-label="Paleta de colores">
                                            <div id="colorPalettePicker" class="statistics-palette-grid"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </aside>

                        <!-- Gráfica Principal -->
                        <section id="statisticsChartPanel" class="statistics-chart-panel" aria-labelledby="chartTitle" role="tabpanel">
                            <header class="statistics-chart-header">
                                <div class="statistics-chart-heading">
                                    <div class="statistics-chart-heading__main">
                                        <h2 id="chartTitle">Cargando...</h2>
                                        <div id="chartTotalBadge" class="statistics-chart-total">
                                            <span>Total analizado</span>
                                            <span id="chartTotalValue">0</span>
                                        </div>
                                        <span id="statisticsPreviousComparison" class="statistics-previous-comparison hidden" aria-live="polite">
                                            <i id="statisticsPreviousComparisonIcon" class="fas fa-minus" aria-hidden="true"></i>
                                            <span id="statisticsPreviousComparisonText"></span>
                                        </span>
                                    </div>
                                    <div id="statisticsChartContext" class="statistics-chart-context hidden" aria-live="polite">
                                        <span id="statisticsChartPeriod"></span>
                                        <span id="statisticsChartCoverage" class="hidden"></span>
                                        <span id="statisticsChartQuality" class="statistics-chart-quality hidden">
                                            <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                                            <span id="statisticsChartQualityText"></span>
                                            <a id="statisticsReviewExcluded" href="#">Revisar</a>
                                        </span>
                                    </div>
                                </div>
                                <div class="statistics-download" id="downloadMenuWrapper">
                                    <a id="statisticsViewData" class="statistics-view-data" href="#" aria-disabled="true">
                                        <i class="fas fa-table-list" aria-hidden="true"></i>
                                        Ver datos
                                    </a>
                                    <div class="statistics-download__group">
                                        <button type="button" class="statistics-download__primary" id="descargarActual">
                                            <i class="fas fa-download" aria-hidden="true"></i>
                                            Descargar gráfica
                                        </button>
                                        <button type="button" class="statistics-download__toggle" id="descargarOpciones" aria-label="Abrir opciones de descarga" aria-haspopup="menu" aria-expanded="false" aria-controls="downloadMenu">
                                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div id="downloadMenu" class="statistics-download-menu hidden" role="menu">
                                        <label class="statistics-download-context-option">
                                            <input type="checkbox" id="includeChartContext" checked>
                                            <span>
                                                <strong>Incluir contexto</strong>
                                                <small>Título, periodo, total y comparación</small>
                                            </span>
                                        </label>
                                        <button type="button" class="download-option" data-export="png-transparent" role="menuitem">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                            PNG (transparente)
                                        </button>
                                        <button type="button" class="download-option" data-export="png-white" role="menuitem">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                            PNG (fondo blanco)
                                        </button>
                                        <button type="button" class="download-option" data-export="pdf" role="menuitem">
                                            <i class="fas fa-file-pdf" aria-hidden="true"></i>
                                            PDF
                                        </button>
                                        <button type="button" class="download-option" data-export="csv" role="menuitem">
                                            <i class="fas fa-file-csv" aria-hidden="true"></i>
                                            Datos en CSV
                                        </button>
                                    </div>
                                </div>
                            </header>
                            <div class="chart-wrapper statistics-chart-canvas">
                                <div id="mainChart" style="width: 100%; height: 100%;"></div>
                                <div id="loadingMessage" class="statistics-chart-state statistics-chart-state--loading" style="display: none;" role="status" aria-live="polite">
                                    <div class="statistics-chart-skeleton" aria-hidden="true">
                                        <span></span><span></span><span></span><span></span><span></span>
                                    </div>
                                    <p>Cargando datos...</p>
                                </div>
                                <div id="errorMessage" class="statistics-chart-state statistics-chart-state--empty" style="display: none;" role="status" aria-live="polite">
                                    <div>
                                        <div class="statistics-chart-state__icon">
                                            <i class="fas fa-chart-column" aria-hidden="true"></i>
                                        </div>
                                        <p id="errorText">No hay datos para los filtros seleccionados.</p>
                                        <span>Pruebe con otros criterios o limpie los filtros aplicados.</span>
                                    </div>
                                </div>
                            </div>
                            <footer id="statisticsChartSource" class="statistics-chart-source hidden">
                                <details id="statisticsChartSourceDetails" class="statistics-chart-source__details">
                                    <summary>
                                        <span class="statistics-chart-source__summary">
                                            <i class="fas fa-file-import" aria-hidden="true"></i>
                                            <span id="statisticsChartSourceSummary">Origen de los registros</span>
                                        </span>
                                        <span class="statistics-chart-source__action">
                                            Ver detalle
                                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                        </span>
                                    </summary>
                                    <div class="statistics-chart-source__popover">
                                        <h3>Origen de los registros</h3>
                                        <p>Registros que cumplen los filtros de datos aplicados.</p>
                                        <ul id="statisticsChartSourceList"></ul>
                                        <div id="statisticsChartSourceTotal" class="statistics-chart-source__total"></div>
                                    </div>
                                </details>
                            </footer>
                            
                            <!-- Tabla de Causas Principales (solo para Edades) -->
                            <section id="causasPrincipalesContainer" class="statistics-causes hidden" aria-labelledby="statistics-causes-title">
                                <h3 id="statistics-causes-title">Causas principales de defunción por grupo de edad</h3>
                                <div class="causas-table-wrapper">
                                    <table>
                                        <colgroup>
                                            <col style="width: 16%;">
                                            <col style="width: 8%;">
                                            <col style="width: 76%;">
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th>Grupo de edad</th>
                                                <th id="causasTotalHeader">Total</th>
                                                <th id="causasDetalleHeader">Causas principales</th>
                                            </tr>
                                        </thead>
                                        <tbody id="causasPrincipalesBody">
                                            <!-- Se llena dinámicamente con JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </section>

                            <style media="not all" data-legacy-statistics-table-styles>
                                /* Asegurar que la tabla use todo el ancho disponible y las causas puedan mostrarse completas */
                                #causasPrincipalesContainer { width: 100%; }
                                #causasPrincipalesContainer .causas-table-wrapper {
                                    width: 100%;
                                    max-width: 100%;
                                }
                                /* Permitir que la tabla ajuste sus columnas según contenido para dar más espacio a la columna de causas */
                                #causasPrincipalesContainer table { table-layout: auto; width: 100%; }
                                #causasPrincipalesContainer th, #causasPrincipalesContainer td { word-wrap: break-word; white-space: normal; }
                                #causasPrincipalesContainer td:nth-child(3) { white-space: normal; }
                                /* Un poco más de interlineado para legibilidad */
                                #causasPrincipalesContainer td, #causasPrincipalesContainer th { line-height: 1.45; }
                                /* Permitir que las causas se envuelvan en varias líneas en lugar de truncar */
                                #causasPrincipalesContainer td.causas-principales-cell {
                                    white-space: normal;
                                    overflow: visible;
                                    text-overflow: unset;
                                    font-size: 0.95rem;
                                }
                                #causasPrincipalesContainer .causa-chip { margin-right: 0.5rem; display: inline-flex; align-items: center; gap: 0.35rem; }
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
                        </section>
                    </div>
                </div>
            </div>
        </section>
        </div>
    </main>

    <!-- Incluir ECharts -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
    <!-- Incluir html2canvas para capturar el DOM cuando sea posible (mejor export visual) -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>

    <!-- Include modal para descargas -->
    @include('components.modal-descargas')

    <script>
        let currentChartType = 'municipios';
        let currentEchartsInstance = null;
        let latestChartData = null;
        let latestChartDataType = null;
        let chartRequestController = null;
        let chartRequestSequence = 0;
        let defaultDateRange = { startDate: '', endDate: '' };
        let filterDraftSnapshot = null;
        let restoringFilterDraft = false;
        let chartConfig = {
            type: 'bar',
            dataLabelMode: 'value',
            limit: 10,
            colorPalette: 'maroon611132',
            groupBy: 'month'
        };

        // Preferencias deseadas: una vista puede adaptarlas sin sobrescribirlas.
        let preferredConfig = {
            type: 'bar',
            dataLabelMode: 'value',
            limit: 10
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
            distritoes: [],
            distritoesNames: [],
            sexo: null,
            edad: null,
            granularidad: 'month',
            mostrarCausasPrincipales: false,
            tipoComparativa: 'residencia-defuncion',
            tipoMunicipio: 'defuncion'
        };

        // Lista de municipios con su distrito (se usa para filtrar por distrito).
        const municipalitiesFull = @json($municipalities->map(function($m) { return ['id' => $m->id, 'name' => $m->name, 'district_id' => $m->district_id ?? null]; })->values());

        const colorPalettes = {
            // Paleta aqua de 15 colores armoniosos oscuro a medio (para Top <= 15)
            aqua: [
                '#2F3D14', // verde casi negro extremo
                '#3D4E1B', // verde muy oscuro sombra
                '#485C24', // verde casi negro
                '#5A6B2D', // verde oscuro profundo
                '#6C7836', // verde oscuro muy saturado
                '#7C8A43', // verde oliva oscuro
                '#8B9852', // verde oliva medio oscuro (NUEVO)
                '#909E54', // verde oliva intermedio
                '#9FB063', // verde oliva claro
                '#A8C363', // verde amarillo saturado
                '#B8CC76', // verde amarillo vibrante
                '#C2D67E', // verde amarillo brillante
                '#CADE90', // verde amarillo medio (NUEVO)
                '#D6E4A5', // verde amarillo claro suave (NUEVO)
                '#DFE9B8'  // verde claro suave (NUEVO)
            ]
            ,
            // Paleta autumn tierra: progresión de marrón oscuro a beige claro con 5 colores adicionales.
            autumn: [
                '#1A140D', // marrón casi negro extremo (NUEVO)
                '#2C2111', // marrón muy oscuro
                '#3D3120', // marrón oscuro intermedio (NUEVO)
                '#44331A', // marrón oscuro
                '#5C4624', // marrón medio oscuro
                '#6F5630', // marrón medio intermedio (NUEVO)
                '#83673F', // marrón medio
                '#93754A', // marrón medio claro (NUEVO)
                '#A9875A', // marrón claro
                '#C4976C', // marrón claro intermedio (NUEVO)
                '#CD9F6E', // tan/durazno
                '#DCAF85', // tan claro
                '#EAC39C', // tan muy claro
                '#F8D6B3', // beige claro
                '#F7E1BE'  // beige muy claro
            ],
            // Paleta rose: progresión de rojo oscuro a rosa claro con 5 colores adicionales estratégicos.
            rose: [
                '#6B1114', // rojo muy oscuro sombra extrema (NUEVO)
                '#9C191B', // rojo oscuro muy saturado
                '#8F1518', // rojo oscuro intermedio (NUEVO)
                '#AC1C1E', // rojo oscuro saturado
                '#BD1F21', // rojo profundo
                '#CB2A2E', // rojo claro intermedio (NUEVO)
                '#D02224', // rojo medio
                '#DD2C2F', // rojo claro
                '#E35053', // rojo claro saturado
                '#E95D60', // rojo-rosa oscuro intermedio (NUEVO)
                '#E66063', // rojo-rosa intermedio
                '#EC8385', // rojo-rosa claro
                '#F1A7A9', // rosa claro
                '#F4B5B8', // rosa claro suave (NUEVO)
                '#F6CACC'  // rosa muy claro
            ],
            // Paleta Tropical Harmonic: degradado profesional azul → verde → amarillo → naranja → rojo con contraste máximo.
            spectrum: [
                '#264653', // azul oscuro profundo
                '#287271', // verde azulado profundo
                '#2A9D8F', // verde azulado vibrante
                '#5CAE9F', // verde agua brillante
                '#8AB17D', // verde oliva
                '#BABB74', // verde amarillento
                '#E9C46A', // amarillo dorado
                '#EFB366', // naranja dorado suave
                '#F4A261', // naranja
                '#EE8959', // naranja rojo
                '#E76F51', // rojo naranja
                '#D1623E', // rojo profundo
                '#A84A2A', // terracota
                '#6B3E2E', // marrón chocolate
                '#3A3A3A'  // gris oscuro carbón
            ],
            // Paleta earth naranja-amarillo: progresión de naranja oscuro a amarillo claro con 5 colores adicionales.
            earth: [
                '#CC5200', // naranja muy oscuro sombra (NUEVO)
                '#FF7B00', // naranja oscuro
                '#FF8514', // naranja profundo (NUEVO)
                '#FF8800', // naranja
                '#FF9500', // naranja claro
                '#FFA200', // naranja amarillento
                '#FFAA00', // naranja amarillo intermedio (NUEVO)
                '#FFB700', // amarillo naranja
                '#FFC300', // amarillo dorado
                '#FFD000', // amarillo
                '#FFD615', // amarillo brillante (NUEVO)
                '#FFD933', // amarillo claro
                '#FFDE4D', // amarillo claro cálido (NUEVO)
                '#FFE15C', // amarillo claro pálido
                '#FFFACD'  // amarillo muy claro suave
            ],
            // Paleta goldenEarth púrpura monocromática: progresión de púrpura oscuro a púrpura claro con 5 colores adicionales.
            goldenEarth: [
                '#2A0E47', // púrpura casi negro extremo (NUEVO)
                '#431259', // púrpura casi negro
                '#511F73', // púrpura muy oscuro intermedio (NUEVO)
                '#60308C', // púrpura muy oscuro
                '#6A3FA0', // púrpura oscuro intermedio (NUEVO)
                '#6F46A6', // púrpura oscuro
                '#805EBF', // púrpura medio
                '#8F6FC9', // púrpura medio claro intermedio (NUEVO)
                '#8B79D9', // púrpura claro
                '#9A99F2', // púrpura claro vibrante
                '#A8B3F0', // púrpura claro suave intermedio (NUEVO)
                '#B3BEFF', // púrpura claro
                '#CCDCFF', // púrpura muy claro
                '#D9E6FF', // púrpura pálido claro (NUEVO)
                '#E6F2FF'  // púrpura muy claro pálido
            ],
            // Paleta monocromática basada en #611132, de tintes claros a sombras profundas.
            maroon611132: [
                '#611132', '#8B2A52', '#9D2449', '#B84C3A', '#7A2946',
                '#4A0E26', '#C25C6F', '#9C4460', '#6F344C', '#A74F62'
            ],
            // Paleta Monocromática Azul Corporativa: progresión de azul oscuro a azul claro con 5 colores adicionales.
            institutional: [
                '#001A3A', // azul casi negro (sombra extrema - NUEVO)
                '#003A70', // azul marino profundo
                '#1965A0', // azul institucional
                '#1A4D7A', // azul muy oscuro intermedio (NUEVO)
                '#0F558F', // azul corporativo oscuro
                '#2D82BD', // azul claro corporativo
                '#2476B1', // azul profesional
                '#5CA5D0', // azul luminoso intermedio (NUEVO)
                '#4893C6', // azul brillante
                '#64A4CE', // azul claro profesional
                '#8FC5E0', // azul pastel intermedio (NUEVO)
                '#8DBEDC', // azul pastel
                '#B7D7EA', // azul muy claro
                '#D8E8F5', // azul pálido (NUEVO)
                '#E2EFF6'  // casi blanco azulado
            ]
        };

        // Paletas de ALTO CONTRASTE para gráficas circulares (pie/doughnut) - 10 colores más contrastantes seleccionados de los 15 originales
        const colorPalettesCircular = {
            aqua: ['#2F3D14', '#3D4E1B', '#6C7836', '#8B9852', '#A8C363', '#B8CC76', '#C2D67E', '#D6E4A5', '#DFE9B8', '#485C24'],
            autumn: ['#1A140D', '#3D3120', '#5C4624', '#83673F', '#A9875A', '#C4976C', '#DCAF85', '#EAC39C', '#F8D6B3', '#F7E1BE'],
            rose: ['#6B1114', '#9C191B', '#BD1F21', '#DD2C2F', '#E35053', '#E95D60', '#EC8385', '#F1A7A9', '#F4B5B8', '#F6CACC'],
            spectrum: ['#264653', '#2A9D8F', '#8AB17D', '#E9C46A', '#F4A261', '#E76F51', '#D1623E', '#A84A2A', '#6B3E2E', '#3A3A3A'],
            earth: ['#4D7300', '#99CC33', '#CCEE66', '#33CCAA', '#006699', '#990066', '#E066CC', '#FF6600', '#FF9900', '#FFCC00'],
            goldenEarth: ['#2A0E47', '#431259', '#6A3FA0', '#805EBF', '#8F6FC9', '#9A99F2', '#B3BEFF', '#CCDCFF', '#D9E6FF', '#E6F2FF'],
            institutional: ['#001A3A', '#003A70', '#1965A0', '#0F558F', '#2D82BD', '#5CA5D0', '#8FC5E0', '#B7D7EA', '#D8E8F5', '#E2EFF6'],
            maroon611132: ['#2C0617', '#4a0e26', '#611132', '#8B2A52', '#9C4460', '#B84C3A', '#C97C8A', '#D96969', '#E8A8A8', '#F0CCCC']
        };

        const colorPaletteLabels = {
            aqua: 'Verde natural',
            autumn: 'Tonos tierra',
            rose: 'Rojos',
            spectrum: 'Espectro',
            earth: 'Multicolor',
            goldenEarth: 'Violetas',
            maroon611132: 'Granate institucional',
            institutional: 'Azul institucional'
        };

        const chartTypeDefaults = {
            municipios: 'bar',
            tendencias: 'line',
            edades: 'bar',
            genero: 'bar',
            causas: 'bar',
            distritoes: 'bar',
            comparativa: 'bar'
        };

        const chartTypeOptions = {
            municipios: ['bar', 'barHorizontal', 'pie', 'doughnut'],
            tendencias: ['line', 'area'],
            edades: ['bar', 'pie', 'doughnut'],
            genero: ['bar', 'pie', 'doughnut'],
            causas: ['bar', 'barHorizontal', 'pie', 'doughnut'],
            distritoes: ['bar', 'barHorizontal', 'pie', 'doughnut'],
            comparativa: ['bar']
        };

        const chartTitles = {
            municipios: 'Distribución por municipios',
            tendencias: 'Tendencia temporal',
            edades: 'Distribución por edades',
            genero: 'Distribución por género',
            causas: 'Causas de defunción',
            distritoes: 'Distribución por distritos',
            comparativa: 'Comparación entre residencia y defunción'
        };

        const comparativaLabels = {
            'residencia-defuncion': 'Residencia frente a lugar de defunción',
            'genero-causa': 'Género por causa de defunción',
            'edad-causa': 'Rango etario por causa de defunción',
            'lugar-causa': 'Lugar de defunción por causa'
        };

        const filtersForChart = {
            municipios: ['dates', 'tipoMunicipio', 'causas', 'distritoes', 'sexo', 'edad'],
            tendencias: ['dates', 'municipios', 'causas', 'sexo', 'edad', 'granularidad'],
            edades: ['dates', 'municipios', 'causas', 'distritoes', 'causasPrincipales'],
            genero: ['dates', 'municipios', 'causas', 'distritoes', 'edad'],
            causas: ['dates', 'municipios', 'distritoes', 'sexo', 'edad'],
            distritoes: ['dates', 'causas', 'sexo', 'edad'],
            comparativa: ['dates', 'tipoComparativa']
        };

        // Gráficas que deben mostrar el selector "Top"
        const chartTypesWithTopSelector = ['municipios', 'causas', 'distritoes', 'comparativa'];

        const chartTypeIcons = {
            bar: 'fa-chart-column',
            barHorizontal: 'fa-chart-bar',
            pie: 'fa-chart-pie',
            doughnut: 'fa-circle-notch',
            line: 'fa-chart-line',
            area: 'fa-chart-area'
        };

        const dataLabelIcons = {
            value: 'fa-hashtag',
            percent: 'fa-percent',
            both: 'fa-layer-group'
        };

        const chartLimitLabels = {
            all: 'Todos',
            5: 'Top 5',
            10: 'Top 10',
            15: 'Top 15'
        };

        const chartTypeLabels = {
            bar: 'Barras',
            barHorizontal: 'Horizontal',
            pie: 'Pastel',
            doughnut: 'Rosquilla',
            line: 'Línea',
            area: 'Área'
        };

        const dataLabelModeLabels = {
            value: 'Valores',
            percent: 'Porcentaje',
            both: 'Ambos'
        };

        // Definir límites disponibles por tipo de gráfico
        const chartLimitsByType = {
            municipios: [5, 10, 15],
            distritoes: [5, 10],  // Solo hasta 10 porque hay 12 distritoes en total
            comparativa: [5, 10, 15],
            default: [5, 10, 15]
        };

        document.addEventListener('DOMContentLoaded', async function() {
            try {
                if (typeof ChartDataLabels !== 'undefined') Chart.register(ChartDataLabels);
            } catch (e) {
                console.warn('ChartDataLabels no disponible');
            }

            // Cargar rangos de fecha default antes de inicializar
            await loadDefaultDateRange();
            renderColorPalettePreview(chartConfig.colorPalette);
            renderChartTypeButtons('municipios');
            renderDataLabelButtons('municipios');
            renderChartLimitButtons('municipios');
            
            initializeEventListeners();
            selectChart('municipios');

            const sourceDetails = document.getElementById('statisticsChartSourceDetails');
            document.addEventListener('click', function(event) {
                if (sourceDetails?.open && !sourceDetails.contains(event.target)) {
                    sourceDetails.open = false;
                }
            });
            document.addEventListener('keydown', function(event) {
                if (event.key !== 'Escape' || !sourceDetails?.open) return;
                sourceDetails.open = false;
                sourceDetails.querySelector('summary')?.focus();
            });
        });

        // Función para cargar los rangos de fecha default
        async function loadDefaultDateRange() {
            try {
                const response = await fetch('{{ route("api.default-date-range") }}');
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const data = await response.json();
                defaultDateRange = {
                    startDate: data.start_date || '',
                    endDate: data.end_date || ''
                };
                
                // Actualizar los inputs de fecha
                const startDateInput = document.getElementById('customStartDate');
                const endDateInput = document.getElementById('customEndDate');
                
                if (startDateInput && endDateInput) {
                    startDateInput.value = defaultDateRange.startDate;
                    endDateInput.value = defaultDateRange.endDate;
                }
            } catch (error) {
                console.error('Error loading default date range:', error);
            }
        }

        function initializeTomSelect() {
            const filterSelectIds = [
                'dateRange',
                'quarter',
                'tipoMunicipioFilter',
                'municipiosFilter',
                'causasFilter',
                'distritoesFilter',
                'sexoFilter',
                'granularidadFilter',
                'tipoComparativaFilter'
            ];

            filterSelectIds.forEach(id => {
                const element = document.getElementById(id);
                if (element && !element.tomselect && window.AppFilterSelect) {
                    const isMultiple = element.multiple;
                    const isSearchable = isMultiple || element.options.length > 8;
                    window.AppFilterSelect.init(element, {
                        searchable: isSearchable,
                        onChange: (value) => {
                            // Si cambió el distrito mientras se ve 'municipios', actualizar la lista disponible.
                            if (element.id === 'distritoesFilter' && metricUsesMunicipalityAndDistrict()) {
                                const selected = Array.isArray(value) ? value.map(String) : (value ? [String(value)] : []);
                                updateMunicipiosOptions(selected);
                            }
                            markStatisticsFilterDraft();
                        }
                    });
                }
            });
        }

        // Intentar inicializar Tom Select inmediatamente
        if (window.AppFilterSelect) {
            initializeTomSelect();
        } else {
            // Si Tom Select no está disponible, esperar a que lo esté
            let attempts = 0;
            const checkTomSelect = setInterval(() => {
                if (window.AppFilterSelect) {
                    clearInterval(checkTomSelect);
                    initializeTomSelect();
                }
                attempts++;
                if (attempts > 50) { // Stop after 5 seconds (50 * 100ms)
                    clearInterval(checkTomSelect);
                    console.warn('TomSelect did not load in time');
                }
            }, 100);
        }

        function captureStatisticsFilterState() {
            const panel = document.getElementById('estadisticas-filtros');
            if (!panel) return {};

            return Array.from(panel.querySelectorAll('input[id], select[id]')).reduce((state, control) => {
                if (control.type === 'checkbox') {
                    state[control.id] = control.checked;
                } else if (control.multiple) {
                    state[control.id] = control.tomselect
                        ? [].concat(control.tomselect.getValue() || [])
                        : Array.from(control.selectedOptions).map(option => option.value);
                } else {
                    state[control.id] = control.value;
                }
                return state;
            }, {});
        }

        function restoreStatisticsFilterState(state) {
            if (!state) return;
            restoringFilterDraft = true;

            const restoreControl = (id, value) => {
                const control = document.getElementById(id);
                if (!control) return;

                if (control.type === 'checkbox') {
                    control.checked = Boolean(value);
                } else if (control.tomselect) {
                    control.tomselect.setValue(value, true);
                } else if (control.multiple) {
                    const selectedValues = new Set([].concat(value || []).map(String));
                    Array.from(control.options).forEach(option => {
                        option.selected = selectedValues.has(String(option.value));
                    });
                } else {
                    control.value = value ?? '';
                }
            };

            Object.entries(state).forEach(([id, value]) => {
                if (id === 'distritoesFilter' || id === 'municipiosFilter') return;
                restoreControl(id, value);
            });

            restoreControl('distritoesFilter', state.distritoesFilter || []);
            if (metricUsesMunicipalityAndDistrict()) updateMunicipiosOptions([].concat(state.distritoesFilter || []));
            restoreControl('municipiosFilter', state.municipiosFilter || []);

            onDateRangeChange();
            restoringFilterDraft = false;
        }

        function markStatisticsFilterDraft() {
            if (restoringFilterDraft) return;
            const panel = document.getElementById('estadisticas-filtros');
            if (panel?.classList.contains('is-open')) panel.classList.add('has-draft-changes');
        }

        function openStatisticsFilters() {
            const panel = document.getElementById('estadisticas-filtros');
            const toggle = document.getElementById('statisticsFiltersToggle');
            if (!panel || !toggle) return;

            filterDraftSnapshot = captureStatisticsFilterState();
            panel.classList.remove('has-draft-changes');
            panel.classList.add('is-open');
            toggle.setAttribute('aria-expanded', 'true');
        }

        function closeStatisticsFilters({ restore = false } = {}) {
            const panel = document.getElementById('estadisticas-filtros');
            const toggle = document.getElementById('statisticsFiltersToggle');
            if (!panel || !toggle) return;

            if (restore && filterDraftSnapshot) restoreStatisticsFilterState(filterDraftSnapshot);
            filterDraftSnapshot = null;
            panel.classList.remove('is-open', 'has-draft-changes');
            toggle.setAttribute('aria-expanded', 'false');
        }

        // Actualiza las opciones del select de municipios según las distritoes seleccionadas
        function updateMunicipiosOptions(selectedJurIds = []) {
            const munEl = document.getElementById('municipiosFilter');
            if (!munEl) return;
            const tom = munEl.tomselect;
            if (!tom) return;

            // Normalizar ids a strings para comparación
            const selStr = (selectedJurIds || []).map(String);

            // Filtrar la lista completa de municipios
            const allowed = municipalitiesFull.filter(m => {
                if (!m.district_id) return selStr.length === 0; // si no hay info, mostrar sólo cuando no hay filtro
                if (selStr.length === 0) return true; // Sin distrito seleccionado: mostrar todos.
                return selStr.includes(String(m.district_id));
            });

            // Guardar selecciones actuales y mantener sólo las que siguen permitidas
            const currentVals = Array.isArray(munEl.tomselect.getValue()) ? munEl.tomselect.getValue() : (munEl.tomselect.getValue() ? [munEl.tomselect.getValue()] : []);
            const allowedIds = allowed.map(a => String(a.id));
            const keep = currentVals.filter(v => allowedIds.includes(String(v)));

            // Reconstruir opciones
            tom.clearOptions();
            allowed.forEach(m => tom.addOption({ value: String(m.id), text: m.name }));

            // Restaurar selección válida
            tom.clear(true);
            if (keep.length) tom.setValue(keep, true);
        }

        function metricSupportsFilter(chartType, filterName) {
            return (filtersForChart[chartType] || []).includes(filterName);
        }

        function metricUsesMunicipalityAndDistrict() {
            return metricSupportsFilter(currentChartType, 'municipios')
                && metricSupportsFilter(currentChartType, 'distritoes');
        }

        function initializeEventListeners() {
            // Inicializar Tom Select para multiselects
            initializeTomSelect();

            const chartTabs = Array.from(document.querySelectorAll('.chart-tab-btn'));
            chartTabs.forEach((btn, index) => {
                btn.addEventListener('click', function() {
                    selectChart(this.dataset.chart);
                });

                btn.addEventListener('keydown', function(event) {
                    let targetIndex = null;
                    if (event.key === 'ArrowRight') targetIndex = (index + 1) % chartTabs.length;
                    if (event.key === 'ArrowLeft') targetIndex = (index - 1 + chartTabs.length) % chartTabs.length;
                    if (event.key === 'Home') targetIndex = 0;
                    if (event.key === 'End') targetIndex = chartTabs.length - 1;
                    if (targetIndex === null) return;

                    event.preventDefault();
                    chartTabs[targetIndex].focus();
                    chartTabs[targetIndex].click();
                });
            });

            const filtersToggle = document.getElementById('statisticsFiltersToggle');
            const filtersSidebar = document.getElementById('estadisticas-filtros');
            const filtersCancel = document.getElementById('statisticsFiltersCancel');
            const filtersApply = document.getElementById('statisticsFiltersApply');
            if (filtersToggle && filtersSidebar) {
                filtersToggle.addEventListener('click', function() {
                    const willOpen = this.getAttribute('aria-expanded') !== 'true';
                    if (willOpen) {
                        openStatisticsFilters();
                    } else {
                        closeStatisticsFilters({ restore: true });
                    }
                });

                document.addEventListener('click', function(event) {
                    if (filtersToggle.contains(event.target) || filtersSidebar.contains(event.target)) return;
                    if (filtersSidebar.classList.contains('is-open')) closeStatisticsFilters({ restore: true });
                }, true);

                document.addEventListener('keydown', function(event) {
                    if (event.key !== 'Escape' || !filtersSidebar.classList.contains('is-open')) return;
                    closeStatisticsFilters({ restore: true });
                    filtersToggle.focus();
                });

                filtersCancel?.addEventListener('click', function() {
                    closeStatisticsFilters({ restore: true });
                    filtersToggle.focus();
                });

                filtersApply?.addEventListener('click', function() {
                    collectFilters();
                    filterDraftSnapshot = captureStatisticsFilterState();
                    closeStatisticsFilters();
                    updateChart();
                    filtersToggle.focus();
                });

                filtersSidebar.querySelectorAll('[data-statistics-filter-section]').forEach(section => {
                    const sectionToggle = section.querySelector('[data-filter-section-toggle]');
                    sectionToggle?.addEventListener('click', function() {
                        const willOpen = !section.classList.contains('is-open');
                        section.classList.toggle('is-open', willOpen);
                        this.setAttribute('aria-expanded', String(willOpen));
                        const icon = this.querySelector('i');
                        icon?.classList.toggle('fa-chevron-down', willOpen);
                        icon?.classList.toggle('fa-chevron-right', !willOpen);
                    });
                });
            }

            const chartCanvas = document.querySelector('.statistics-chart-canvas');
            if (chartCanvas && typeof ResizeObserver !== 'undefined') {
                let resizeFrame = null;
                const chartResizeObserver = new ResizeObserver(() => {
                    if (resizeFrame) window.cancelAnimationFrame(resizeFrame);
                    resizeFrame = window.requestAnimationFrame(() => currentEchartsInstance?.resize());
                });
                chartResizeObserver.observe(chartCanvas);
            } else {
                window.addEventListener('resize', () => currentEchartsInstance?.resize());
            }

            document.getElementById('dateRange').addEventListener('change', function() {
                onDateRangeChange();
                markStatisticsFilterDraft();
            });
            
            // Event listeners para campos de fecha
            const yearInput = document.getElementById('year');
            if (yearInput) yearInput.addEventListener('change', markStatisticsFilterDraft);
            
            // Event listener para mes específico (select simple)
            const monthSelect = document.getElementById('month');
            if (monthSelect && monthSelect.tagName === 'SELECT') {
                monthSelect.addEventListener('change', markStatisticsFilterDraft);
            }
            
            const quarterSelect = document.getElementById('quarter');
            if (quarterSelect) quarterSelect.addEventListener('change', markStatisticsFilterDraft);
            
            // Manejar checkboxes de meses
            document.querySelectorAll('.month-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    markStatisticsFilterDraft();
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
            
            document.getElementById('customStartDate').addEventListener('change', markStatisticsFilterDraft);
            document.getElementById('customEndDate').addEventListener('change', markStatisticsFilterDraft);
            // Nota: Los eventos para municipiosFilter, causasFilter, distritoesFilter 
            // se manejan dentro de Tom Select (onChange), no aquí
            document.getElementById('sexoFilter').addEventListener('change', markStatisticsFilterDraft);
            document.getElementById('edadFilter').addEventListener('input', markStatisticsFilterDraft);
            document.getElementById('granularidadFilter').addEventListener('change', markStatisticsFilterDraft);
            document.getElementById('mostrarCausasPrincipales').addEventListener('change', markStatisticsFilterDraft);
            document.getElementById('tipoComparativaFilter').addEventListener('change', markStatisticsFilterDraft);
            document.getElementById('tipoMunicipioFilter').addEventListener('change', markStatisticsFilterDraft);

            document.getElementById('chartTypeSelector').addEventListener('change', function() {
                chartConfig.type = this.value;
                preferredConfig.type = this.value;  // Guardar preferencia global
                // CONFIGURACIONES SON GLOBALES - se mantienen al cambiar de métrica
                renderChartTypeButtons(currentChartType);
                setPresentationAdaptationNote('chartTypeAdaptationNote');
                rerenderLatestChart();
            });
            document.getElementById('datalabelMode').addEventListener('change', function() {
                chartConfig.dataLabelMode = this.value;
                preferredConfig.dataLabelMode = this.value;  // Guardar preferencia global
                // CONFIGURACIONES SON GLOBALES - se mantienen al cambiar de métrica
                renderDataLabelButtons(currentChartType);
                setPresentationAdaptationNote('dataLabelAdaptationNote');
                rerenderLatestChart();
            });
            document.getElementById('chartLimit').addEventListener('change', function() {
                chartConfig.limit = this.value === 'all' ? null : parseInt(this.value);
                preferredConfig.limit = chartConfig.limit;  // Guardar preferencia global
                // CONFIGURACIONES SON GLOBALES - se mantienen al cambiar de métrica
                renderChartLimitButtons(currentChartType);
                setPresentationAdaptationNote('chartLimitAdaptationNote');
                updateChart();
            });

            document.getElementById('chartTypeButtons')?.addEventListener('click', function(event) {
                const button = event.target.closest('.visual-option-btn[data-target="chartTypeSelector"]');
                if (!button || button.disabled) return;
                setSelectValueAndTrigger('chartTypeSelector', button.dataset.value);
            });

            document.getElementById('dataLabelButtons')?.addEventListener('click', function(event) {
                const button = event.target.closest('.visual-option-btn[data-target="datalabelMode"]');
                if (!button || button.disabled) return;
                setSelectValueAndTrigger('datalabelMode', button.dataset.value);
            });

            document.getElementById('chartLimitButtons')?.addEventListener('click', function(event) {
                const button = event.target.closest('.visual-option-btn[data-target="chartLimit"]');
                if (!button || button.disabled) return;
                setSelectValueAndTrigger('chartLimit', button.dataset.value);
            });

            const palettePicker = document.getElementById('colorPalettePicker');
            const paletteToggle = document.getElementById('statisticsPaletteToggle');
            const paletteMenu = document.getElementById('statisticsPaletteMenu');
            const palettePopover = paletteToggle?.closest('.statistics-palette-popover');

            if (paletteToggle && paletteMenu) {
                paletteToggle.addEventListener('click', function() {
                    const willOpen = paletteMenu.classList.contains('hidden');
                    paletteMenu.classList.toggle('hidden', !willOpen);
                    this.setAttribute('aria-expanded', String(willOpen));
                });

                document.addEventListener('click', function(event) {
                    if (palettePopover?.contains(event.target)) return;
                    paletteMenu.classList.add('hidden');
                    paletteToggle.setAttribute('aria-expanded', 'false');
                });

                document.addEventListener('keydown', function(event) {
                    if (event.key !== 'Escape' || paletteMenu.classList.contains('hidden')) return;
                    paletteMenu.classList.add('hidden');
                    paletteToggle.setAttribute('aria-expanded', 'false');
                    paletteToggle.focus();
                });
            }

            if (palettePicker) {
                palettePicker.addEventListener('click', function(event) {
                    const button = event.target.closest('.palette-chip');
                    if (!button) return;
                    const paletteName = button.dataset.palette;
                    if (!paletteName) return;
                    const changed = chartConfig.colorPalette !== paletteName;
                    chartConfig.colorPalette = paletteName;
                    renderColorPalettePreview(paletteName);
                    paletteMenu?.classList.add('hidden');
                    paletteToggle?.setAttribute('aria-expanded', 'false');
                    if (changed) rerenderLatestChart();
                });
            }

            document.getElementById('limpiarFiltros').addEventListener('click', function() {
                clearFilters(true);
                markStatisticsFilterDraft();
            });

            const downloadMenuWrapper = document.getElementById('downloadMenuWrapper');
            const downloadMenu = document.getElementById('downloadMenu');
            const downloadButton = document.getElementById('descargarActual');
            const downloadOptionsButton = document.getElementById('descargarOpciones');
            document.getElementById('statisticsViewData')?.addEventListener('click', function(event) {
                if (this.getAttribute('aria-disabled') === 'true') event.preventDefault();
            });

            if (downloadButton && downloadMenu) {
                downloadButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    exportCurrentChart('png-white');
                });

                if (downloadOptionsButton) {
                    downloadOptionsButton.addEventListener('click', function(event) {
                        event.stopPropagation();
                        const willOpen = downloadMenu.classList.contains('hidden');
                        downloadMenu.classList.toggle('hidden', !willOpen);
                        this.setAttribute('aria-expanded', String(willOpen));
                    });
                }

                document.querySelectorAll('.download-option').forEach(option => {
                    option.addEventListener('click', async function() {
                        const exportType = this.dataset.export;
                        downloadMenu.classList.add('hidden');
                        downloadOptionsButton?.setAttribute('aria-expanded', 'false');
                        if (exportType === 'csv') {
                            downloadAnalysisCsv();
                            return;
                        }
                        await exportCurrentChart(exportType);
                    });
                });

                document.addEventListener('click', function(event) {
                    if (downloadMenuWrapper && !downloadMenuWrapper.contains(event.target)) {
                        downloadMenu.classList.add('hidden');
                        downloadOptionsButton?.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('keydown', function(event) {
                    if (event.key !== 'Escape' || downloadMenu.classList.contains('hidden')) return;
                    downloadMenu.classList.add('hidden');
                    downloadOptionsButton?.setAttribute('aria-expanded', 'false');
                    downloadOptionsButton?.focus();
                });
            }
        }

        function getCurrentDownloadName(extension) {
            return `estadisticas-${currentChartType}-${new Date().toISOString().split('T')[0]}.${extension}`;
        }

        function getChartBackgroundOption(transparent) {
            return transparent ? {} : { backgroundColor: '#ffffff' };
        }

        function getChartDataUrl(transparent = false) {
            if (!currentEchartsInstance) return null;

            const backgroundOptions = getChartBackgroundOption(transparent);
            const opt = currentEchartsInstance.getOption ? currentEchartsInstance.getOption() : null;
            const series = opt && opt.series && opt.series[0] ? opt.series[0] : null;
            const isChartPie = series && (series.type === 'pie');

            if (isChartPie) {
                return currentEchartsInstance.getDataURL({ type: 'png', pixelRatio: 2, ...backgroundOptions });
            }

            return currentEchartsInstance.getDataURL({ type: 'png', pixelRatio: 2, ...backgroundOptions });
        }

        function shouldIncludeChartContext() {
            return document.getElementById('includeChartContext')?.checked !== false;
        }

        function formatExportDate(value) {
            if (!value) return '';
            const date = new Date(`${String(value).slice(0, 10)}T12:00:00Z`);
            if (Number.isNaN(date.getTime())) return '';
            return new Intl.DateTimeFormat('es-MX', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                timeZone: 'UTC'
            }).format(date);
        }

        function getExportComparisonText(comparison) {
            if (!comparison?.available) return '';

            const period = comparison.period || {};
            let periodText = '';
            if (period.start_date && period.end_date) {
                periodText = `${formatExportDate(period.start_date)} – ${formatExportDate(period.end_date)}`;
            } else if (Array.isArray(period.years) && period.years.length) {
                periodText = period.years.join(', ');
                if (Array.isArray(period.months) && period.months.length) {
                    periodText += ' (mismos meses seleccionados)';
                }
            }

            if (comparison.direction === 'no_baseline') {
                return `Sin registros en el periodo anterior${periodText ? ` (${periodText})` : ''}`;
            }

            const percentage = Math.abs(Number(comparison.percentage_change || 0)).toLocaleString('es-MX', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 1
            });
            const marker = comparison.direction === 'increase'
                ? '↑'
                : (comparison.direction === 'decrease' ? '↓' : '—');
            const difference = Number(comparison.difference || 0);
            const signedDifference = `${difference > 0 ? '+' : ''}${difference.toLocaleString('es-MX')}`;

            return `${marker} ${percentage}% frente a ${periodText || 'periodo anterior'} · Diferencia: ${signedDifference}`;
        }

        function wrapCanvasText(context, value, maxWidth) {
            const words = String(value || '').trim().split(/\s+/).filter(Boolean);
            if (!words.length) return [];
            const lines = [];
            let line = words.shift();

            words.forEach(word => {
                const candidate = `${line} ${word}`;
                if (context.measureText(candidate).width <= maxWidth) {
                    line = candidate;
                } else {
                    lines.push(line);
                    line = word;
                }
            });
            lines.push(line);
            return lines;
        }

        function composeChartWithContext(chartCanvas, transparent = false) {
            const chartElement = document.getElementById('mainChart');
            const displayWidth = chartElement?.clientWidth || (chartCanvas.width / 2);
            const scale = Math.max(1, chartCanvas.width / Math.max(1, displayWidth));
            const padding = 22 * scale;
            const accentOffset = 11 * scale;
            const maxTextWidth = chartCanvas.width - (padding * 2) - accentOffset;
            const title = document.getElementById('chartTitle')?.textContent?.trim() || 'Estadísticas';
            const total = Number(latestChartData?.filtered_total ?? latestChartData?.total ?? 0).toLocaleString('es-MX');
            const periodElement = document.getElementById('statisticsChartPeriod');
            const coverageElement = document.getElementById('statisticsChartCoverage');
            const qualityElement = document.getElementById('statisticsChartQualityText');
            const contextLines = [];
            const periodText = periodElement && !periodElement.classList.contains('hidden')
                ? periodElement.textContent.trim()
                : '';

            contextLines.push(`${periodText ? `${periodText} · ` : ''}Total analizado: ${total}`);
            const comparisonText = getExportComparisonText(latestChartData?.previous_period_comparison);
            if (comparisonText) contextLines.push(comparisonText);
            if (coverageElement && !coverageElement.classList.contains('hidden') && coverageElement.textContent.trim()) {
                contextLines.push(coverageElement.textContent.trim());
            }
            if (qualityElement?.textContent?.trim()) {
                contextLines.push(`Nota: ${qualityElement.textContent.trim()}`);
            }

            const measureCanvas = document.createElement('canvas');
            const measureContext = measureCanvas.getContext('2d');
            if (!measureContext) return chartCanvas;
            measureContext.font = `600 ${18 * scale}px Lora, Georgia, serif`;
            const titleLines = wrapCanvasText(measureContext, title, maxTextWidth);
            measureContext.font = `500 ${11.5 * scale}px "Open Sans", Arial, sans-serif`;
            const bodyLines = contextLines.flatMap(line => wrapCanvasText(measureContext, line, maxTextWidth));
            const titleLineHeight = 24 * scale;
            const bodyLineHeight = 17 * scale;
            const headerHeight = Math.ceil(
                padding + (titleLines.length * titleLineHeight) + (7 * scale) +
                (bodyLines.length * bodyLineHeight) + padding
            );

            const canvas = document.createElement('canvas');
            canvas.width = chartCanvas.width;
            canvas.height = headerHeight + chartCanvas.height;
            const context = canvas.getContext('2d');
            if (!context) return chartCanvas;

            if (!transparent) {
                context.fillStyle = '#ffffff';
                context.fillRect(0, 0, canvas.width, canvas.height);
            }

            context.fillStyle = '#6f0f37';
            context.fillRect(padding, padding + (2 * scale), 3 * scale, Math.max(titleLineHeight, titleLines.length * titleLineHeight - (4 * scale)));
            let y = padding + (19 * scale);
            context.fillStyle = '#10233f';
            context.font = `600 ${18 * scale}px Lora, Georgia, serif`;
            titleLines.forEach(line => {
                context.fillText(line, padding + accentOffset, y);
                y += titleLineHeight;
            });

            y += 2 * scale;
            context.fillStyle = '#526278';
            context.font = `500 ${11.5 * scale}px "Open Sans", Arial, sans-serif`;
            bodyLines.forEach(line => {
                context.fillText(line, padding + accentOffset, y);
                y += bodyLineHeight;
            });

            context.strokeStyle = '#dbe3ec';
            context.lineWidth = Math.max(1, scale / 2);
            context.beginPath();
            context.moveTo(padding, headerHeight - (8 * scale));
            context.lineTo(canvas.width - padding, headerHeight - (8 * scale));
            context.stroke();
            context.drawImage(chartCanvas, 0, headerHeight);

            return canvas;
        }

        async function captureChartAsCanvas(transparent = false, includeContext = shouldIncludeChartContext()) {
            if (currentChartType === 'edades' && document.getElementById('mostrarCausasPrincipales').checked && typeof html2canvas !== 'undefined') {
                const element = document.getElementById('mainChart')?.closest('.statistics-chart-panel');
                if (!element) return null;
                return html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: transparent ? null : '#ffffff',
                    onclone: clonedDocument => {
                        const panel = clonedDocument.getElementById('statisticsChartPanel');
                        const header = panel?.querySelector('.statistics-chart-header');
                        const source = panel?.querySelector('.statistics-chart-source');
                        const actions = panel?.querySelector('.statistics-download');
                        if (actions) actions.style.display = 'none';
                        if (source) source.style.display = 'none';
                        if (header && !includeContext) header.style.display = 'none';
                    }
                });
            }

            const dataUrl = getChartDataUrl(transparent);
            if (!dataUrl) return null;

            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.src = dataUrl;
            await img.decode();

            const canvas = document.createElement('canvas');
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            const ctx = canvas.getContext('2d');
            if (ctx) {
                ctx.drawImage(img, 0, 0);
            }

            return includeContext ? composeChartWithContext(canvas, transparent) : canvas;
        }

        async function exportCurrentChart(exportType) {
            if (!currentEchartsInstance) return false;

            if (exportType === 'pdf') {
                const canvas = await captureChartAsCanvas(false);
                if (!canvas) return false;

                const { jsPDF } = window.jspdf || {};
                if (!jsPDF) return false;

                const pdf = new jsPDF({ orientation: canvas.width >= canvas.height ? 'landscape' : 'portrait', unit: 'pt', format: 'a4' });
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                const ratio = Math.min((pageWidth - 40) / canvas.width, (pageHeight - 40) / canvas.height);
                const width = canvas.width * ratio;
                const height = canvas.height * ratio;
                const x = (pageWidth - width) / 2;
                const y = (pageHeight - height) / 2;

                pdf.addImage(canvas.toDataURL('image/png'), 'PNG', x, y, width, height);
                pdf.save(getCurrentDownloadName('pdf'));
                return true;
            }

            const transparent = exportType === 'png-transparent';
            const canvas = await captureChartAsCanvas(transparent);
            if (!canvas) return false;

            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = getCurrentDownloadName('png');
            link.click();
            return true;
        }

        window.exportStatisticsChart = exportCurrentChart;

        function selectChart(chartType) {
            const filtersPanel = document.getElementById('estadisticas-filtros');
            if (filtersPanel?.classList.contains('is-open')) {
                closeStatisticsFilters({ restore: true });
            }

            currentChartType = chartType;
            latestChartData = null;
            latestChartDataType = null;
            
            // Actualizar botones de tab
            document.querySelectorAll('.chart-tab-btn').forEach(btn => {
                const isSelected = btn.dataset.chart === chartType;
                btn.classList.toggle('active', isSelected);
                btn.setAttribute('aria-selected', String(isSelected));
                btn.tabIndex = isSelected ? 0 : -1;
            });
            
            // Actualizar filtros contextuales disponibles para esta métrica
            updateVisibleFilters(chartType);
            
            // Ocultar tabla de causas si no es Edades
            const causasContainer = document.getElementById('causasPrincipalesContainer');
            if (causasContainer) {
                causasContainer.classList.add('hidden');
            }

            applyPreferredPresentation(chartType);
            updateActiveFiltersDisplay();
            
            // Cargar datos de la nueva métrica
            loadChart(chartType);
        }

        function updateVisibleFilters(chartType) {
            const allFilters = ['filterTipoMunicipio', 'filterMunicipios', 'filterCausas', 'filterdistritoes', 'filterSexo', 'filterEdad', 'filterGranularidad', 'filterCausasPrincipales', 'filterTipoComparativa'];
            const availableFilters = filtersForChart[chartType] || [];

            allFilters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    let show = false;
                    if (filterId === 'filterTipoMunicipio' && availableFilters.includes('tipoMunicipio')) show = true;
                    if (filterId === 'filterMunicipios' && availableFilters.includes('municipios')) show = true;
                    if (filterId === 'filterCausas' && availableFilters.includes('causas')) show = true;
                    if (filterId === 'filterdistritoes' && availableFilters.includes('distritoes')) show = true;
                    if (filterId === 'filterSexo' && availableFilters.includes('sexo')) show = true;
                    if (filterId === 'filterEdad' && availableFilters.includes('edad')) show = true;
                    if (filterId === 'filterGranularidad' && availableFilters.includes('granularidad')) show = true;
                    if (filterId === 'filterCausasPrincipales' && availableFilters.includes('causasPrincipales')) show = true;
                    if (filterId === 'filterTipoComparativa' && availableFilters.includes('tipoComparativa')) show = true;
                    element.style.display = show ? 'block' : 'none';
                }
            });

            document.querySelectorAll('[data-statistics-filter-group]').forEach(group => {
                const hasVisibleControl = Array.from(group.querySelectorAll('.dynamic-filter'))
                    .some(control => control.style.display !== 'none');
                group.hidden = !hasVisibleControl;
            });

            // Mostrar/ocultar selector Top según el tipo de gráfica
            const filterTopElement = document.getElementById('filterTop');
            if (filterTopElement) {
                const showTopSelector = chartTypesWithTopSelector.includes(chartType);
                filterTopElement.style.display = showTopSelector ? '' : 'none';
                filterTopElement.parentElement?.classList.toggle('has-top-filter', showTopSelector);
            }

        }

        function setPresentationAdaptationNote(id, text = '') {
            const note = document.getElementById(id);
            if (!note) return;
            note.textContent = text;
            note.classList.toggle('hidden', !text);
        }

        function getEffectiveLimit(chartType) {
            if (!chartTypesWithTopSelector.includes(chartType)) return null;
            if (preferredConfig.limit === null) return null;

            const available = chartLimitsByType[chartType] || chartLimitsByType.default;
            if (available.includes(preferredConfig.limit)) return preferredConfig.limit;

            const lowerOrEqual = available.filter(limit => limit <= preferredConfig.limit);
            return lowerOrEqual.length ? Math.max(...lowerOrEqual) : Math.min(...available);
        }

        function applyPreferredPresentation(chartType) {
            const validTypes = chartTypeOptions[chartType] || ['bar'];
            const effectiveType = validTypes.includes(preferredConfig.type)
                ? preferredConfig.type
                : chartTypeDefaults[chartType];
            const effectiveDataLabel = chartType === 'tendencias' && ['percent', 'both'].includes(preferredConfig.dataLabelMode)
                ? 'value'
                : preferredConfig.dataLabelMode;
            const effectiveLimit = getEffectiveLimit(chartType);

            chartConfig.type = effectiveType;
            chartConfig.dataLabelMode = effectiveDataLabel;
            chartConfig.limit = effectiveLimit;

            updateChartTypeOptions(chartType, effectiveType);
            document.getElementById('datalabelMode').value = effectiveDataLabel;
            document.getElementById('chartLimit').value = effectiveLimit === null ? 'all' : String(effectiveLimit);
            updateDataLabelOptions(chartType);
            renderChartTypeButtons(chartType);
            renderDataLabelButtons(chartType);
            renderChartLimitButtons(chartType);

            setPresentationAdaptationNote(
                'chartTypeAdaptationNote',
                effectiveType !== preferredConfig.type
                    ? `Esta vista usa ${chartTypeLabels[effectiveType]}; se conserva ${chartTypeLabels[preferredConfig.type]}.`
                    : ''
            );
            setPresentationAdaptationNote(
                'dataLabelAdaptationNote',
                effectiveDataLabel !== preferredConfig.dataLabelMode
                    ? `Esta vista usa ${dataLabelModeLabels[effectiveDataLabel]}; se conserva ${dataLabelModeLabels[preferredConfig.dataLabelMode]}.`
                    : ''
            );
            setPresentationAdaptationNote(
                'chartLimitAdaptationNote',
                chartTypesWithTopSelector.includes(chartType) && effectiveLimit !== preferredConfig.limit
                    ? `${chartLimitLabels[effectiveLimit]} aplicado; se conserva ${chartLimitLabels[preferredConfig.limit]}.`
                    : ''
            );
        }

        function updateDataLabelOptions(chartType) {
            const datalabelModeSelect = document.getElementById('datalabelMode');
            if (!datalabelModeSelect) return;

            const options = datalabelModeSelect.querySelectorAll('option');
            
            // Para Tendencias, deshabilitar "Solo %" y "Ambos"
            if (chartType === 'tendencias') {
                options.forEach(option => {
                    if (option.value === 'percent' || option.value === 'both') {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
                // Si está seleccionado un opción deshabilitada, cambiar a "value"
                if (datalabelModeSelect.value === 'percent' || datalabelModeSelect.value === 'both') {
                    datalabelModeSelect.value = 'value';
                    chartConfig.dataLabelMode = 'value';
                }
            } else {
                // Para otros gráficos, habilitar todas las opciones
                options.forEach(option => {
                    option.disabled = false;
                });
            }

            renderDataLabelButtons(chartType);
        }

        function updateChartTypeOptions(chartType, selectedValue = chartConfig.type) {
            const selector = document.getElementById('chartTypeSelector');
            if (!selector) return;
            const availableTypes = chartTypeOptions[chartType] || ['bar'];
            const currentValue = selectedValue;
            const allOptions = {
                'bar': 'Barras',
                'barHorizontal': 'Barras Horizontales',
                'pie': 'Pastel',
                'doughnut': 'Rosquilla',
                'line': 'Línea',
                'area': 'Área'
            };
            selector.innerHTML = '';
            availableTypes.forEach((type, index) => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = allOptions[type] || type;
                // Marcar como selected si es la primera opción o si coincide con el valor actual
                if (index === 0 || type === currentValue) {
                    option.selected = true;
                }
                selector.appendChild(option);
            });
            
            // Asegurarse de que siempre hay un valor seleccionado
            if (!selector.value) {
                selector.value = availableTypes[0];
            }
            chartConfig.type = selector.value;
            renderChartTypeButtons(chartType);
            renderChartLimitButtons(chartType);
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
                    if (customDateSelector) customDateSelector.style.display = 'grid';
                    break;
                default:
                    // all - no mostrar nada extra
                    break;
            }
            
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
                    markStatisticsFilterDraft();
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

        function renderColorPalettePreview(paletteName) {
            const picker = document.getElementById('colorPalettePicker');
            if (!picker) return;

            const paletteKeys = Object.keys(colorPalettes);
            const selectedPalette = colorPalettes[paletteName] || [];
            const selectedLabel = colorPaletteLabels[paletteName] || paletteName;
            const selection = document.getElementById('statisticsPaletteSelection');
            const label = document.getElementById('statisticsPaletteLabel');

            if (selection) {
                selection.innerHTML = selectedPalette.slice(0, 4)
                    .map(color => `<span style="background:${color};"></span>`)
                    .join('');
            }
            if (label) label.textContent = selectedLabel;

            picker.innerHTML = paletteKeys.map(key => {
                const palette = colorPalettes[key] || [];
                const label = colorPaletteLabels[key] || key;
                const swatches = palette.slice(0, 4);

                return `
                    <button type="button" class="palette-chip" data-palette="${key}" aria-pressed="${key === paletteName}" aria-label="Usar paleta ${label}">
                        <div class="palette-chip__swatches">
                            ${swatches.map(color => `<span style="background:${color};" title="${color}"></span>`).join('')}
                        </div>
                        <div class="palette-chip__label">${label}</div>
                    </button>
                `;
            }).join('');
        }

        function setSelectValueAndTrigger(selectId, value) {
            const select = document.getElementById(selectId);
            if (!select) return;
            select.value = value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function renderChartTypeButtons(chartType) {
            const container = document.getElementById('chartTypeButtons');
            const selector = document.getElementById('chartTypeSelector');
            if (!container || !selector) return;

            const availableTypes = chartTypeOptions[chartType] || ['bar'];
            const currentValue = selector.value || chartConfig.type || availableTypes[0];
            const isSingleType = availableTypes.length === 1;
            const labels = {
                bar: 'Barras',
                barHorizontal: 'Horizontal',
                pie: 'Pastel',
                doughnut: 'Rosquilla',
                line: 'Línea',
                area: 'Área'
            };

            container.className = `statistics-visual-options${isSingleType ? ' is-single' : ''}`;

            container.innerHTML = availableTypes.map(type => {
                const active = type === currentValue;
                return `
                    <button type="button" class="visual-option-btn visual-option-card ${isSingleType ? 'is-compact-single' : ''} ${active ? 'active' : ''}" data-target="chartTypeSelector" data-value="${type}" aria-pressed="${active}">
                        <i class="fas ${chartTypeIcons[type] || 'fa-chart-simple'}"></i>
                        <span class="visual-option-label">${labels[type] || type}</span>
                    </button>
                `;
            }).join('');
        }

        function renderDataLabelButtons(chartType) {
            const container = document.getElementById('dataLabelButtons');
            const select = document.getElementById('datalabelMode');
            if (!container || !select) return;

            const currentValue = select.value || chartConfig.dataLabelMode || 'value';
            let options = [
                { value: 'value', label: 'Valores' },
                { value: 'percent', label: 'Porcentaje' },
                { value: 'both', label: 'Ambos' }
            ];

            // Para Tendencias, mostrar solo Valores
            if (chartType === 'tendencias') {
                options = options.filter(opt => opt.value === 'value');
            }

            const isSingleOption = options.length === 1;

            container.className = `statistics-visual-options${isSingleOption ? ' is-single' : ''}`;

            container.innerHTML = options.map(option => {
                const active = option.value === currentValue;
                return `
                    <button type="button" class="visual-option-btn visual-option-card ${isSingleOption ? 'is-compact-single' : ''} ${active ? 'active' : ''}" data-target="datalabelMode" data-value="${option.value}" aria-pressed="${active}">
                        <i class="fas ${dataLabelIcons[option.value] || 'fa-circle'}"></i>
                        <span class="visual-option-label">${option.label}</span>
                    </button>
                `;
            }).join('');
        }

        function renderChartLimitButtons(chartType) {
            const container = document.getElementById('chartLimitButtons');
            const select = document.getElementById('chartLimit');
            const wrapper = document.getElementById('filterTop');
            if (!container || !select || !wrapper) return;

            const currentValue = select.value || (chartConfig.limit ? String(chartConfig.limit) : 'all');
            
            // Obtener los límites disponibles para este tipo de gráfico
            const availableLimits = chartLimitsByType[chartType] || chartLimitsByType.default;
            
            const options = [
                { value: 'all', label: chartLimitLabels.all },
                ...availableLimits.map(limit => ({ 
                    value: String(limit), 
                    label: chartLimitLabels[limit] 
                }))
            ];

            const isSingleOption = options.length === 1;

            container.className = `statistics-visual-options${isSingleOption ? ' is-single' : ''}`;

            container.innerHTML = options.map(option => {
                const active = option.value === currentValue;
                return `
                    <button type="button" class="visual-option-btn visual-option-card ${isSingleOption ? 'is-compact-single' : ''} ${active ? 'active' : ''}" data-target="chartLimit" data-value="${option.value}" aria-pressed="${active}">
                        <span class="visual-limit-badge ${option.value === 'all' ? 'is-all' : ''}">${option.value === 'all' ? '∞' : option.value}</span>
                        <span class="visual-option-label">${option.label}</span>
                    </button>
                `;
            }).join('');

            const showTopSelector = chartTypesWithTopSelector.includes(chartType);
            wrapper.style.display = showTopSelector ? '' : 'none';
            wrapper.parentElement?.classList.toggle('has-top-filter', showTopSelector);
        }

        function collectFilters() {
            const dateRange = document.getElementById('dateRange').value;
            let startDate = null, endDate = null;
            const currentYear = new Date().getFullYear();

            // Estos valores dependen del modo de fecha actual. Reiniciarlos evita
            // enviar meses/años de una selección anterior al cambiar de modo.
            activeFilters.selectedMonths = [];
            activeFilters.selectedYears = [];

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
            activeFilters.distritoes = Array.from(document.getElementById('distritoesFilter').selectedOptions || []).map(o => o.value);
            activeFilters.distritoesNames = Array.from(document.getElementById('distritoesFilter').selectedOptions || []).map(o => o.text);
            activeFilters.sexo = document.getElementById('sexoFilter').value || null;
            activeFilters.edad = document.getElementById('edadFilter').value.trim() || null;
            activeFilters.granularidad = document.getElementById('granularidadFilter').value || 'month';
            activeFilters.mostrarCausasPrincipales = document.getElementById('mostrarCausasPrincipales').checked || false;
            activeFilters.tipoComparativa = document.getElementById('tipoComparativaFilter').value || 'residencia-defuncion';
            activeFilters.tipoMunicipio = document.getElementById('tipoMunicipioFilter').value || 'defuncion';
            
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
            const countBadge = document.getElementById('statisticsFilterCount');
            container.innerHTML = '';
            const escapeFilterLabel = value => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
            
            let hasActiveFilters = false;
            let activeFilterCount = 0;

            // Mostrar rango de fechas
            const dateText = getDateFilterText();
            if (dateText) {
                container.innerHTML += `<span class="users-filter-chip statistics-filter-chip">
                    Fecha: ${escapeFilterLabel(dateText)}
                    <button type="button" onclick="clearDateFilter()" aria-label="Quitar filtro de fecha">&times;</button>
                </span>`;
                hasActiveFilters = true;
                activeFilterCount++;
            }

            // Mostrar municipios seleccionados
            if (metricSupportsFilter(currentChartType, 'municipios') && activeFilters.municipios.length > 0) {
                const municipiosText = activeFilters.municipiosNames.join(', ');
                const municipiosLabel = activeFilters.municipios.length === 1 ? 'Municipio' : 'Municipios';
                container.innerHTML += `<span class="users-filter-chip statistics-filter-chip">
                    ${municipiosLabel}: ${escapeFilterLabel(municipiosText)}
                    <button type="button" onclick="clearFilter('municipios')" aria-label="Quitar filtro de municipios">&times;</button>
                </span>`;
                hasActiveFilters = true;
                activeFilterCount++;
            }

            // Mostrar causas seleccionadas
            if (metricSupportsFilter(currentChartType, 'causas') && activeFilters.causas.length > 0) {
                const causasText = activeFilters.causasNames.join(', ');
                const causasLabel = activeFilters.causas.length === 1 ? 'Causa' : 'Causas';
                container.innerHTML += `<span class="users-filter-chip statistics-filter-chip">
                    ${causasLabel}: ${escapeFilterLabel(causasText)}
                    <button type="button" onclick="clearFilter('causas')" aria-label="Quitar filtro de causas">&times;</button>
                </span>`;
                hasActiveFilters = true;
                activeFilterCount++;
            }

            // Mostrar distritoes seleccionadas
            if (metricSupportsFilter(currentChartType, 'distritoes') && activeFilters.distritoes.length > 0) {
                const distritoesText = activeFilters.distritoesNames.join(', ');
                const distritosLabel = activeFilters.distritoes.length === 1 ? 'Distrito' : 'Distritos';
                container.innerHTML += `<span class="users-filter-chip statistics-filter-chip">
                    ${distritosLabel}: ${escapeFilterLabel(distritoesText)}
                    <button type="button" onclick="clearFilter('distritoes')" aria-label="Quitar filtro de distritos">&times;</button>
                </span>`;
                hasActiveFilters = true;
                activeFilterCount++;
            }

            // Mostrar sexo seleccionado
            if (metricSupportsFilter(currentChartType, 'sexo') && activeFilters.sexo) {
                const sexoLabel = activeFilters.sexo === 'M' ? 'Masculino' : (activeFilters.sexo === 'F' ? 'Femenino' : activeFilters.sexo);
                container.innerHTML += `<span class="users-filter-chip statistics-filter-chip">
                    Sexo: ${escapeFilterLabel(sexoLabel)}
                    <button type="button" onclick="clearFilter('sexo')" aria-label="Quitar filtro de sexo">&times;</button>
                </span>`;
                hasActiveFilters = true;
                activeFilterCount++;
            }

            if (metricSupportsFilter(currentChartType, 'edad') && activeFilters.edad) {
                container.innerHTML += `<span class="users-filter-chip statistics-filter-chip">
                    Edad: ${escapeFilterLabel(activeFilters.edad)}
                    <button type="button" onclick="clearFilter('edad')" aria-label="Quitar filtro de edad">&times;</button>
                </span>`;
                hasActiveFilters = true;
                activeFilterCount++;
            }

            // Mostrar sección si hay filtros activos
            section.classList.toggle('hidden', !hasActiveFilters);
            if (countBadge) {
                countBadge.textContent = String(activeFilterCount);
                countBadge.classList.toggle('hidden', activeFilterCount === 0);
            }
        }

        function clearDateFilter() {
            const dateRangeControl = document.getElementById('dateRange');
            if (dateRangeControl.tomselect) dateRangeControl.tomselect.setValue('all', true);
            else dateRangeControl.value = 'all';
            ['yearSelector','monthSimpleSelector','monthSelector','quarterSelector','customDateSelector'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
            // Limpiar input de año
            document.getElementById('year').value = '';
            const quarterControl = document.getElementById('quarter');
            if (quarterControl.tomselect) quarterControl.tomselect.clear(true);
            else quarterControl.value = '';
            document.getElementById('customStartDate').value = defaultDateRange.startDate;
            document.getElementById('customEndDate').value = defaultDateRange.endDate;
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
            } else if (filterType === 'distritoes') {
                const el = document.getElementById('distritoesFilter');
                el.value = '';
                if (el.tomselect) el.tomselect.clear();
            } else if (filterType === 'sexo') {
                const el = document.getElementById('sexoFilter');
                if (el.tomselect) el.tomselect.clear(true);
                else el.value = '';
            } else if (filterType === 'edad') {
                document.getElementById('edadFilter').value = '';
            } else if (filterType === 'tipoMunicipio') {
                const el = document.getElementById('tipoMunicipioFilter');
                if (el.tomselect) el.tomselect.setValue('defuncion', true);
                else el.value = 'defuncion';
            }
            collectFilters();
            updateActiveFiltersDisplay();
            updateChart();
        }

        async function loadChart(chartType) {
            collectFilters();

            const filters = {
                ...(activeFilters.startDate && { start_date: activeFilters.startDate }),
                ...(activeFilters.endDate && { end_date: activeFilters.endDate }),
                ...(activeFilters.selectedMonths.length && { months: activeFilters.selectedMonths }),
                ...(activeFilters.selectedYears.length && { years: activeFilters.selectedYears }),
                ...(metricSupportsFilter(chartType, 'municipios') && activeFilters.municipios.length && { municipios: activeFilters.municipios }),
                ...(metricSupportsFilter(chartType, 'causas') && activeFilters.causas.length && { causas: activeFilters.causas }),
                ...(metricSupportsFilter(chartType, 'distritoes') && activeFilters.distritoes.length && { distritoes: activeFilters.distritoes }),
                ...(metricSupportsFilter(chartType, 'sexo') && activeFilters.sexo && { sex: activeFilters.sexo }),
                ...(metricSupportsFilter(chartType, 'edad') && activeFilters.edad && { age: activeFilters.edad }),
                ...(chartTypesWithTopSelector.includes(chartType) && chartConfig.limit && { limit: chartConfig.limit }),
                ...(chartType === 'tendencias' && { group_by: activeFilters.granularidad }),
                ...(chartType === 'comparativa' && { comparativa_type: activeFilters.tipoComparativa }),
                ...(chartType === 'municipios' && { municipio_type: activeFilters.tipoMunicipio })
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
            chartRequestController?.abort();
            chartRequestController = new AbortController();
            const requestController = chartRequestController;
            const requestId = ++chartRequestSequence;
            showLoadingMessage();

            try {
                const response = await fetch(
                    `{{ route('api.chart.data') }}/` + chartType + '?' + params.toString(),
                    { signal: requestController.signal }
                );
                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();
                if (requestId !== chartRequestSequence || chartType !== currentChartType) return;

                if (data.error) {
                    showErrorMessage(data.message || 'No se pudieron obtener los datos.');
                    return;
                }

                latestChartData = data;
                latestChartDataType = chartType;
                renderChart(cloneChartData(data));
            } catch (error) {
                if (error.name === 'AbortError') return;
                console.error('Error:', error);
                if (requestId === chartRequestSequence) {
                    showErrorMessage('No se pudieron cargar los datos. Intenta nuevamente.');
                }
            } finally {
                if (chartRequestController === requestController) chartRequestController = null;
            }
        }

        function cloneChartData(data) {
            if (typeof structuredClone === 'function') return structuredClone(data);
            return JSON.parse(JSON.stringify(data));
        }

        function rerenderLatestChart() {
            if (latestChartData && latestChartDataType === currentChartType) {
                renderChart(cloneChartData(latestChartData));
                return;
            }
            updateChart();
        }

        function buildStatisticsAnalysisParams(data, { excluded = false, csv = false } = {}) {
            const params = new URLSearchParams();
            const appendMany = (key, values) => {
                [].concat(values || []).filter(value => value !== null && value !== '').forEach(value => params.append(`${key}[]`, value));
            };
            const period = data?.period || {};

            if (period.start_date) params.set('start_date', period.start_date);
            if (period.end_date) params.set('end_date', period.end_date);
            if (!period.start_date && !period.end_date) {
                appendMany('years', period.years);
                appendMany('months', period.months);
            }

            if (metricSupportsFilter(currentChartType, 'municipios')) {
                appendMany('death_municipality_ids', activeFilters.municipios);
            }
            if (metricSupportsFilter(currentChartType, 'causas')) appendMany('cause_ids', activeFilters.causas);
            if (metricSupportsFilter(currentChartType, 'distritoes')) appendMany('district_ids', activeFilters.distritoes);
            if (metricSupportsFilter(currentChartType, 'sexo') && activeFilters.sexo) params.set('sex', activeFilters.sexo);
            if (metricSupportsFilter(currentChartType, 'edad') && activeFilters.edad) params.set('age', activeFilters.edad);

            params.set('analysis_type', currentChartType);
            if (currentChartType === 'comparativa') params.set('comparativa_type', activeFilters.tipoComparativa);
            if (currentChartType === 'municipios') params.set('municipio_type', activeFilters.tipoMunicipio);
            if (excluded) params.set('analysis_excluded', '1');
            if (csv) params.set('format', 'csv');

            return params;
        }

        function updateStatisticsAnalysisActions(data) {
            const viewData = document.getElementById('statisticsViewData');
            const reviewExcluded = document.getElementById('statisticsReviewExcluded');
            const dataUrl = `{{ route('statistic.data') }}?${buildStatisticsAnalysisParams(data).toString()}`;
            const excludedUrl = `{{ route('statistic.data') }}?${buildStatisticsAnalysisParams(data, { excluded: true }).toString()}`;

            if (viewData) {
                viewData.href = dataUrl;
                viewData.removeAttribute('aria-disabled');
            }
            if (reviewExcluded) reviewExcluded.href = excludedUrl;
        }

        function showNoChartData(data) {
            const excludedTotal = Number(data?.quality?.excluded_total || 0);
            showErrorMessage(
                excludedTotal > 0
                    ? 'No hay registros completos para construir esta gráfica.'
                    : 'No hay datos para los filtros seleccionados.',
                excludedTotal > 0
            );
        }

        function downloadAnalysisCsv() {
            if (!latestChartData || latestChartDataType !== currentChartType) return;
            const params = buildStatisticsAnalysisParams(latestChartData, { csv: true });
            window.location.assign(`{{ route('statistic.export') }}?${params.toString()}`);
        }

        function updateChartSourceSummary(sourceSummary) {
            const footer = document.getElementById('statisticsChartSource');
            const details = document.getElementById('statisticsChartSourceDetails');
            const summary = document.getElementById('statisticsChartSourceSummary');
            const list = document.getElementById('statisticsChartSourceList');
            const total = document.getElementById('statisticsChartSourceTotal');
            if (!footer || !details || !summary || !list || !total) return;

            const imports = Array.isArray(sourceSummary?.imports) ? sourceSummary.imports : [];
            const manualRecords = Number(sourceSummary?.manual_records || 0);
            const totalRecords = Number(sourceSummary?.total_records || 0);

            details.open = false;
            list.replaceChildren();

            if (totalRecords <= 0) {
                footer.classList.add('hidden');
                return;
            }

            const importText = imports.length === 1 ? '1 importación' : `${imports.length} importaciones`;
            const manualText = manualRecords === 1 ? '1 registro manual' : `${manualRecords.toLocaleString('es-MX')} registros manuales`;
            summary.textContent = imports.length > 0
                ? `Origen: ${importText}${manualRecords > 0 ? ` · ${manualText}` : ''}`
                : 'Origen: captura manual';

            const dateFormatter = new Intl.DateTimeFormat('es-MX', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            const appendSource = (name, records, description) => {
                const item = document.createElement('li');
                const copy = document.createElement('span');
                const title = document.createElement('strong');
                const meta = document.createElement('small');
                const count = document.createElement('strong');

                title.textContent = name;
                meta.textContent = description;
                count.textContent = `${Number(records).toLocaleString('es-MX')} ${Number(records) === 1 ? 'registro' : 'registros'}`;
                copy.append(title, meta);
                item.append(copy, count);
                list.append(item);
            };

            imports.forEach(source => {
                let importedAt = 'Importación registrada';
                if (source.imported_at) {
                    const date = new Date(source.imported_at);
                    if (!Number.isNaN(date.getTime())) importedAt = `Importado el ${dateFormatter.format(date)}`;
                }
                appendSource(source.name || `Importación #${source.id}`, source.records, importedAt);
            });

            if (manualRecords > 0) {
                appendSource('Captura manual', manualRecords, 'Registros agregados directamente en el sistema');
            }

            total.textContent = `${totalRecords.toLocaleString('es-MX')} ${totalRecords === 1 ? 'registro considerado' : 'registros considerados'}`;
            footer.classList.remove('hidden');
        }

        function updatePreviousPeriodComparison(data) {
            const element = document.getElementById('statisticsPreviousComparison');
            const icon = document.getElementById('statisticsPreviousComparisonIcon');
            const text = document.getElementById('statisticsPreviousComparisonText');
            if (!element || !icon || !text) return;

            const comparison = data?.previous_period_comparison;
            if (!comparison?.available) {
                element.className = 'statistics-previous-comparison hidden';
                element.removeAttribute('title');
                text.textContent = '';
                return;
            }

            const direction = comparison.direction || 'unchanged';
            const percentage = Number(comparison.percentage_change);
            const percentageText = Number.isFinite(percentage)
                ? Math.abs(percentage).toLocaleString('es-MX', { minimumFractionDigits: 0, maximumFractionDigits: 1 })
                : null;
            const settings = {
                increase: {
                    icon: 'fas fa-arrow-up',
                    label: `${percentageText}% vs. periodo anterior`
                },
                decrease: {
                    icon: 'fas fa-arrow-down',
                    label: `${percentageText}% vs. periodo anterior`
                },
                unchanged: {
                    icon: 'fas fa-minus',
                    label: 'Sin cambio vs. periodo anterior'
                },
                no_baseline: {
                    icon: 'fas fa-minus',
                    label: 'Sin registros en el periodo anterior'
                }
            };
            const selected = settings[direction] || settings.unchanged;

            const dateFormatter = new Intl.DateTimeFormat('es-MX', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                timeZone: 'UTC'
            });
            const formatDate = value => {
                const date = new Date(`${String(value).slice(0, 10)}T12:00:00Z`);
                return Number.isNaN(date.getTime()) ? '' : dateFormatter.format(date);
            };
            const period = comparison.period || {};
            let previousPeriodLabel = '';
            if (period.start_date && period.end_date) {
                previousPeriodLabel = `${formatDate(period.start_date)} – ${formatDate(period.end_date)}`;
            } else if (Array.isArray(period.years) && period.years.length) {
                previousPeriodLabel = period.years.join(', ');
                if (Array.isArray(period.months) && period.months.length) {
                    previousPeriodLabel += ' · mismos meses seleccionados';
                }
            }

            const difference = Number(comparison.difference || 0);
            const signedDifference = `${difference > 0 ? '+' : ''}${difference.toLocaleString('es-MX')}`;
            const previousTotal = Number(comparison.previous_total || 0).toLocaleString('es-MX');

            element.className = `statistics-previous-comparison is-${direction}`;
            icon.className = selected.icon;
            text.textContent = selected.label;
            element.title = [
                previousPeriodLabel ? `Periodo anterior: ${previousPeriodLabel}` : '',
                `${previousTotal} registros analizados`,
                `Diferencia: ${signedDifference}`
            ].filter(Boolean).join(' · ');
        }

        function updateChartContext(data) {
            const context = document.getElementById('statisticsChartContext');
            const periodElement = document.getElementById('statisticsChartPeriod');
            const coverageElement = document.getElementById('statisticsChartCoverage');
            const qualityElement = document.getElementById('statisticsChartQuality');
            const qualityText = document.getElementById('statisticsChartQualityText');
            if (!context || !periodElement || !coverageElement || !qualityElement || !qualityText) return;

            const period = data.period || {};
            const dateFormatter = new Intl.DateTimeFormat('es-MX', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                timeZone: 'UTC'
            });
            const formatDate = value => {
                if (!value) return '';
                const date = new Date(`${String(value).slice(0, 10)}T12:00:00Z`);
                return Number.isNaN(date.getTime()) ? '' : dateFormatter.format(date);
            };

            let periodText = '';
            if (period.start_date && period.end_date) {
                periodText = `${period.is_default ? 'Periodo predeterminado' : 'Periodo'}: ${formatDate(period.start_date)} – ${formatDate(period.end_date)}`;
            } else if (Array.isArray(period.years) && period.years.length) {
                const years = period.years.map(Number).filter(Number.isFinite).sort((a, b) => a - b);
                const months = Array.isArray(period.months) ? period.months.filter(Boolean) : [];
                periodText = months.length
                    ? `Periodo: ${months.length * years.length} ${months.length * years.length === 1 ? 'mes seleccionado' : 'meses seleccionados'}`
                    : `Periodo: ${years.join(', ')}`;
            } else if (period.data_start && period.data_end) {
                periodText = `Datos: ${formatDate(period.data_start)} – ${formatDate(period.data_end)}`;
            }

            const displayedCategories = Number(data.displayed_categories || 0);
            const availableCategories = Number(data.available_categories || displayedCategories);
            const coverage = Number(data.coverage_percentage || 0);
            const hasLimitedCoverage = availableCategories > displayedCategories || coverage < 99.95;

            periodElement.textContent = periodText;
            periodElement.classList.toggle('hidden', !periodText);
            if (hasLimitedCoverage && Number(data.filtered_total || 0) > 0) {
                const dimension = data.ranking_label || 'categorías';
                coverageElement.textContent = `Top ${displayedCategories} de ${availableCategories} ${dimension} · cobertura ${coverage.toFixed(1)}%`;
                coverageElement.classList.remove('hidden');
            } else {
                coverageElement.textContent = '';
                coverageElement.classList.add('hidden');
            }

            const excludedTotal = Number(data.quality?.excluded_total || 0);
            const requiredFields = Array.isArray(data.quality?.required_fields) ? data.quality.required_fields : [];
            if (excludedTotal > 0) {
                qualityText.textContent = `${excludedTotal.toLocaleString('es-MX')} ${excludedTotal === 1 ? 'registro no incluido' : 'registros no incluidos'} por información incompleta`;
                qualityElement.title = requiredFields.length ? `Campos necesarios: ${requiredFields.join(', ')}` : '';
                qualityElement.classList.remove('hidden');
            } else {
                qualityText.textContent = '';
                qualityElement.removeAttribute('title');
                qualityElement.classList.add('hidden');
            }

            context.classList.toggle('hidden', !periodText && coverageElement.classList.contains('hidden') && qualityElement.classList.contains('hidden'));
        }

        function renderChart(data) {
            const chartContainer = document.getElementById('mainChart');
            const chartWrapper = chartContainer.closest('.chart-wrapper');
            const loadingMessage = document.getElementById('loadingMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (loadingMessage) loadingMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
            if (chartContainer) chartContainer.style.visibility = 'visible';
            const axisFontSize = 12;
            const valueLabelFontSize = 12;
            const legendFontSize = 12;
            
            // Grids diferentes según chart type para espaciado consistente
            const verticalBarGrids = {
                municipios: { left: '3%', right: '4%', top: 42, bottom: 52, containLabel: true },
                edades: { left: '3%', right: '4%', top: 42, bottom: 48, containLabel: true },
                genero: { left: '3%', right: '4%', top: 42, bottom: 54, containLabel: true },
                causas: { left: '3%', right: '4%', top: 42, bottom: 54, containLabel: true },
                distritoes: { left: '3%', right: '4%', top: 42, bottom: 54, containLabel: true },
                comparativa: { left: '3%', right: '4%', top: 38, bottom: 68, containLabel: true },
                tendencias: { left: '3%', right: '4%', top: 40, bottom: 56, containLabel: true }
            };
            
            // Obtener grid para el chart type actual
            const verticalBarGrid = verticalBarGrids[currentChartType] || { left: '3%', right: '4%', top: 42, bottom: 54, containLabel: true };
            const expandedCircularCharts = ['municipios', 'edades', 'genero', 'causas', 'distritoes'].includes(currentChartType);
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

            const resolvedChartType = chartConfig.type === 'auto'
                ? getOptimalChartType(currentChartType)
                : chartConfig.type;
            const isPieLikeChart = ['pie', 'doughnut'].includes(resolvedChartType);

            if (chartWrapper) {
                chartWrapper.style.height = '';
                chartWrapper.style.minHeight = '';
                chartWrapper.style.flexBasis = '';
                if (isPieLikeChart) {
                    // Mostrar el chart en modo compacto: solo espacio para la gráfica + leyenda
                    chartWrapper.style.display = 'flex';
                    chartWrapper.style.justifyContent = 'center';
                    chartWrapper.style.alignItems = 'center';
                } else {
                    chartWrapper.style.display = '';
                    chartWrapper.style.justifyContent = '';
                    chartWrapper.style.alignItems = '';
                    
                    // Altura uniforme para todas las gráficas no circulares
                    // Reset chart container width when not using compact pie layout
                    if (chartContainer) chartContainer.style.width = '';
                }
            }

            let chartTitle = currentChartType === 'comparativa' 
                ? comparativaLabels[activeFilters.tipoComparativa] || chartTitles[currentChartType]
                : (chartTitles[currentChartType] || 'Gráfica');
            
            // Agregar tipo de municipio en el título si aplica
            if (currentChartType === 'municipios' && activeFilters.tipoMunicipio) {
                const tipoLabel = activeFilters.tipoMunicipio === 'residencia' ? 'residencia' : 'defunción';
                chartTitle += ` (${tipoLabel})`;
            }
            
            document.getElementById('chartTitle').textContent = chartTitle;
            const filteredTotal = Number(data.filtered_total ?? data.total ?? 0);
            const totalStr = filteredTotal.toLocaleString('es-MX');
            const totalEl = document.getElementById('totalRecords');
            if (totalEl) totalEl.textContent = totalStr;
            const badgeEl = document.getElementById('chartTotalValue');
            if (badgeEl) badgeEl.textContent = totalStr;
            updatePreviousPeriodComparison(data);
            updateChartContext(data);
            updateChartSourceSummary(data.source_summary);
            updateStatisticsAnalysisActions(data);

            let labels = data.labels || [];
            let values = currentChartType === 'comparativa' ? null : (data.counts || []);
            
            // Usar paleta de alto contraste para gráficas circulares, paleta normal para barras/líneas
            const paletteSource = isPieLikeChart ? colorPalettesCircular : colorPalettes;
            const palette = paletteSource[chartConfig.colorPalette];
            const colors = labels.map((_, i) => palette[i % palette.length]);

            // Determinar tipo de gráfica: usar la selección del usuario o el óptimo si es "auto"
            let chartType = resolvedChartType;
            
            // Detectar si es área antes de convertir a line
            const isAreaChart = chartType === 'area';
            
            // Si el usuario selecciona "area", trata como "line" con areaStyle
            if (chartType === 'area') {
                chartType = 'line';
            }
            
            // Construir opciones según el tipo de gráfica
            let option = {};

            // Filtrar entradas con valor 0 en gráficas de barras para evitar mostrar categorías sin datos
            try {
                if ((chartType === 'bar' || chartType === 'barHorizontal')) {
                    if (currentChartType === 'comparativa' && data.residence_counts && data.death_counts) {
                        const fLabels = [];
                        const fRes = [];
                        const fDeath = [];
                        for (let i = 0; i < labels.length; i++) {
                            const r = Number(data.residence_counts[i] || 0);
                            const d = Number(data.death_counts[i] || 0);
                            if (r !== 0 || d !== 0) {
                                fLabels.push(labels[i]);
                                fRes.push(r);
                                fDeath.push(d);
                            }
                        }
                        if (fLabels.length === 0) {
                            showNoChartData(data);
                            return;
                        }
                        labels = fLabels;
                        data.residence_counts = fRes;
                        data.death_counts = fDeath;
                    } else if (values && values.length) {
                        const fLabels = [];
                        const fValues = [];
                        for (let i = 0; i < labels.length; i++) {
                            const v = Number(values[i] || 0);
                            if (v !== 0) {
                                fLabels.push(labels[i]);
                                fValues.push(values[i]);
                            }
                        }
                        if (fLabels.length === 0) {
                            showNoChartData(data);
                            return;
                        }
                        labels = fLabels;
                        values = fValues;
                    }
                }
            } catch (e) {
                console.warn('Filter zeros failed', e);
            }

            const normalizeCategoryLabel = (value) => String(value ?? '').replace(/\s+/g, ' ').trim();
            const plotWidth = Math.max(280, (chartWrapper?.clientWidth || 960) * 0.88);
            const categorySlotWidth = plotWidth / Math.max(labels.length, 1);
            const charsPerAxisLine = Math.max(7, Math.min(22, Math.floor(categorySlotWidth / 8.5)));
            const wrapCategoryLabel = (value, maxCharsPerLine) => {
                const text = normalizeCategoryLabel(value);
                if (text.length <= maxCharsPerLine) return text;

                const words = text.split(' ').flatMap(word => {
                    if (word.length <= maxCharsPerLine) return [word];
                    const parts = [];
                    for (let index = 0; index < word.length; index += maxCharsPerLine) {
                        parts.push(word.slice(index, index + maxCharsPerLine));
                    }
                    return parts;
                });
                const lines = [];
                let currentLine = '';

                for (const word of words) {
                    const candidate = currentLine ? `${currentLine} ${word}` : word;
                    if (candidate.length <= maxCharsPerLine) {
                        currentLine = candidate;
                        continue;
                    }

                    if (currentLine) lines.push(currentLine);
                    currentLine = word;
                }

                if (currentLine) lines.push(currentLine);
                return lines.join('\n');
            };

            const formatVerticalCategoryLabel = value => wrapCategoryLabel(value, charsPerAxisLine);
            const verticalLabelLineCount = Math.max(
                1,
                ...labels.map(label => formatVerticalCategoryLabel(label).split('\n').length)
            );

            const verticalCategoryAxisLabel = {
                interval: 0,
                rotate: 0,
                align: 'center',
                verticalAlign: 'top',
                lineHeight: 14,
                fontSize: labels.length > 10 ? 11 : axisFontSize,
                color: '#404041',
                formatter: formatVerticalCategoryLabel
            };

            const horizontalCategoryAxisLabel = {
                interval: 0,
                align: 'right',
                lineHeight: 14,
                fontSize: labels.length > 12 ? 11 : axisFontSize,
                color: '#404041',
                formatter: value => wrapCategoryLabel(value, 28)
            };

            if (verticalLabelLineCount > 1) {
                verticalBarGrid.bottom += (verticalLabelLineCount - 1) * 14;
            }

            if (chartWrapper) {
                const presentationPanel = document.querySelector('.statistics-display-panel');
                const chartPanel = document.getElementById('statisticsChartPanel');
                const chartHeader = chartPanel?.querySelector('.statistics-chart-header');
                const chartSource = document.getElementById('statisticsChartSource');
                const baseHeight = Math.min(448, Math.max(368, window.innerHeight * 0.44));
                let contentHeight = baseHeight;

                if (resolvedChartType === 'barHorizontal') {
                    contentHeight = Math.min(680, Math.max(baseHeight, labels.length * 34 + 108));
                } else if (resolvedChartType === 'bar') {
                    contentHeight = Math.min(620, baseHeight + Math.max(0, verticalLabelLineCount - 1) * 18);
                } else if (isPieLikeChart) {
                    contentHeight = Math.max(baseHeight, 420);
                }

                const chartChromeHeight = (chartHeader?.offsetHeight || 0)
                    + (chartSource && !chartSource.classList.contains('hidden') ? chartSource.offsetHeight : 0);
                const sidebarMatchedHeight = window.matchMedia('(min-width: 1280px)').matches
                    ? Math.max(0, (presentationPanel?.offsetHeight || 0) - chartChromeHeight)
                    : 0;
                const targetHeight = Math.ceil(Math.min(680, Math.max(contentHeight, sidebarMatchedHeight)));

                chartWrapper.style.height = `${targetHeight}px`;
                chartWrapper.style.minHeight = `${targetHeight}px`;
                chartWrapper.style.flexBasis = `${targetHeight}px`;
            }

            // Inicializar ECharts cuando el lienzo ya tiene su altura definitiva.
            // Así su canvas interno ocupa toda la tarjeta desde el primer render.
            currentEchartsInstance = echarts.init(chartContainer);

            if (currentChartType === 'comparativa') {
                const comparisonSeries = Array.isArray(data.series) && data.series.length
                    ? data.series
                    : [
                        { name: 'Municipio de residencia', data: data.residence_counts || [] },
                        { name: 'Municipio de defunción', data: data.death_counts || [] }
                    ];
                const comparisonLegend = comparisonSeries.map(series => series.name);
                const showComparisonLabels = comparisonSeries.length <= 3;
                const comparisonDenominator = Number(data.filtered_total || 0);

                option = {
                    color: palette,
                    animation: true,
                    animationDuration: 800,
                    animationEasing: 'cubicOut',
                    title: { text: '' },
                    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' }, textStyle: { fontSize: axisFontSize } },
                    legend: {
                        type: 'scroll',
                        data: comparisonLegend,
                        bottom: 10,
                        itemWidth: 18,
                        itemHeight: 16,
                        textStyle: { fontSize: legendFontSize, color: '#404041' }
                    },
                    grid: verticalBarGrid,
                    xAxis: {
                        type: 'category',
                        data: labels,
                        axisLabel: verticalCategoryAxisLabel
                    },
                    yAxis: {
                        type: 'value',
                        axisLabel: { fontSize: axisFontSize, color: '#404041' }
                    },
                    series: comparisonSeries.map((series, index) => ({
                        name: series.name,
                        type: 'bar',
                        stack: data.stacked ? 'total' : undefined,
                        data: series.data || [],
                        itemStyle: { color: palette[index % palette.length] },
                        emphasis: { focus: 'series' },
                        label: {
                            show: showComparisonLabels && chartConfig.dataLabelMode !== 'none',
                            position: data.stacked ? 'inside' : 'top',
                            fontSize: valueLabelFontSize,
                            fontWeight: 700,
                            formatter: params => {
                                const value = Number(params.value || 0);
                                if (!value) return '';
                                const percentage = comparisonDenominator > 0
                                    ? `${((value / comparisonDenominator) * 100).toFixed(1)}%`
                                    : '0.0%';
                                if (chartConfig.dataLabelMode === 'percent') return percentage;
                                if (chartConfig.dataLabelMode === 'both') return `${formatNumber(value)} (${percentage})`;
                                return formatNumber(value);
                            }
                        }
                    }))
                };
            } else if (chartType === 'pie' || chartType === 'doughnut') {
                // Gráficas de pastel
                const pieData = labels.map((label, i) => ({
                    name: label,
                    value: values[i]
                }));
                const omittedTotal = Number(data.omitted_total || 0);
                if (omittedTotal > 0) {
                    pieData.push({ name: 'Otros', value: omittedTotal, itemStyle: { color: '#cbd5e1' } });
                }
                // Filtrar slices con valor 0 para evitar mostrar muchos 0 alrededor del pastel
                const filteredPieData = pieData.filter(d => Number(d.value || 0) !== 0);
                if (filteredPieData.length === 0) {
                    showNoChartData(data);
                    return;
                }
                // Calcular medidas en píxeles para ocupar solo lo necesario
                let pieDiameterPx = 0;
                let legendEstimatePx = 180; // ancho estimado para la leyenda
                let legendRightOffset = 12;
                let containerHeightAdjusted = null;
                
                if (chartWrapper) {
                    const wrapperRect = chartWrapper.getBoundingClientRect();
                    const wrapperH = Math.max(380, wrapperRect.height || 520);
                    const pieScale = expandedCircularCharts ? 0.84 : 0.9;
                    pieDiameterPx = Math.floor(wrapperH * pieScale);
                } else {
                    pieDiameterPx = expandedCircularCharts ? 420 : 420;
                }

                // Para 'edades', 'genero' y 'causas' (gráficas expandidas): ajustar leyenda
                if (expandedCircularCharts) {
                    legendEstimatePx = Math.max(230, filteredPieData.length * 13 + 70);
                    // Espaciado especial para Causas: mucho más espacio
                    legendRightOffset = currentChartType === 'causas' ? 130 : 90;
                }

                // Ajustar ancho del contenedor del canvas para que no ocupe todo el espacio
                let estimatedTotalWidth = pieDiameterPx + legendEstimatePx + 40;
                if (expandedCircularCharts) {
                    // Espacio generoso para que los números de la izquierda no se corten y la leyenda tenga distancia
                    let additionalSpacing = currentChartType === 'causas' ? 320 : 240;
                    estimatedTotalWidth = pieDiameterPx + legendEstimatePx + additionalSpacing;
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
                    centerX = Math.round(pieDiameterPx / 2 + 90) + 'px';
                    outerRadius = Math.round(pieDiameterPx * 0.42) + 'px';
                    if (chartType === 'doughnut') {
                        innerRadius = Math.round(pieDiameterPx * 0.26) + 'px';
                    }
                }

                // Determinar tamaño de fuente de leyenda según el tipo de gráfica
                let legendFontSizeActual = 15;
                if (expandedCircularCharts) {
                    // Para causas, reducir fuente un poco para que quepa mejor
                    legendFontSizeActual = currentChartType === 'causas' ? 12 : 14;
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
                        itemHeight: expandedCircularCharts ? 22 : 16,
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
                        data: filteredPieData,
                        label: {
                            show: true,
                            position: 'outside',
                            distance: expandedCircularCharts ? 8 : 6,
                            fontSize: expandedCircularCharts ? 14 : 15,
                            fontWeight: 700,
                            color: '#404041',
                            overflow: 'none',
                            width: expandedCircularCharts ? 260 : 180,
                            rich: labelRich,
                            formatter: (params) => {
                                const percentage = filteredTotal > 0
                                    ? `${((Number(params.value || 0) / filteredTotal) * 100).toFixed(1)}%`
                                    : '0.0%';
                                if (chartConfig.dataLabelMode === 'value') {
                                    return `{value|${formatNumber(params.value)}}`;
                                } else if (chartConfig.dataLabelMode === 'percent') {
                                    return `{percent|${percentage}}`;
                                } else if (chartConfig.dataLabelMode === 'both') {
                                    return `{value|${formatNumber(params.value)}} {normal|(${percentage})}`;
                                }
                                // Por defecto: mostrar valor en lugar del nombre
                                return `{value|${formatNumber(params.value)}}`;
                            }
                        },
                        labelLine: {
                            show: true,
                            length: 12,
                            length2: expandedCircularCharts ? 12 : 8
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
                    animation: true,
                    animationDuration: 800,
                    animationEasing: 'cubicOut',
                    title: { text: '' },
                    tooltip: { trigger: 'axis', axisPointer: { type: 'cross' }, textStyle: { fontSize: axisFontSize } },
                    grid: verticalBarGrid,
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
                let categoryAxisFontSize = ['edades', 'genero'].includes(currentChartType)
                    ? 16
                    : (currentChartType === 'distritoes' ? 12 : axisFontSize);

                if (['municipios', 'distritoes'].includes(currentChartType)) {
                    if (chartConfig.limit === 10) {
                        categoryAxisFontSize = 15;
                    } else if (chartConfig.limit === 5) {
                        categoryAxisFontSize = 16;
                    }
                }
                
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
                    animation: true,
                    animationDuration: 800,
                    animationEasing: 'cubicOut',
                    title: { text: '' },
                    tooltip: { 
                        trigger: 'axis', 
                        axisPointer: { type: 'shadow' },
                        textStyle: { fontSize: axisFontSize },
                        ...(tooltipFormatter ? { formatter: tooltipFormatter } : {})
                    },
                    grid: isHorizontal
                        ? {
                            left: '3%',
                            right: '4%',
                            bottom: 40,
                            top: 36,
                            containLabel: true
                        }
                        : verticalBarGrid,
                    [isHorizontal ? 'xAxis' : 'yAxis']: {
                        type: 'value',
                        axisLabel: { fontSize: axisFontSize, color: '#404041' }
                    },
                    [isHorizontal ? 'yAxis' : 'xAxis']: {
                        type: 'category',
                        data: labels,
                        axisLabel: isHorizontal
                            ? { ...horizontalCategoryAxisLabel, fontSize: Math.min(horizontalCategoryAxisLabel.fontSize, categoryAxisFontSize) }
                            : {
                                ...verticalCategoryAxisLabel,
                                fontSize: verticalLabelLineCount > 1
                                    ? Math.min(verticalCategoryAxisLabel.fontSize, categoryAxisFontSize)
                                    : categoryAxisFontSize
                            }
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
                                const total = filteredTotal;
                                if (chartConfig.dataLabelMode === 'value') return `{value|${formatNumber(params.value)}}`;
                                if (chartConfig.dataLabelMode === 'percent') {
                                    return `{percent|${total > 0 ? ((params.value / total) * 100).toFixed(1) : '0.0'}%}`;
                                }
                                if (chartConfig.dataLabelMode === 'both') {
                                    return `{value|${formatNumber(params.value)}} {normal|(${total > 0 ? ((params.value / total) * 100).toFixed(1) : '0.0'}%)}`;
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
            const chartInstance = currentEchartsInstance;
            function applyOptionAndFinish() {
                if (!chartInstance || currentEchartsInstance !== chartInstance) return;
                try {
                    chartInstance.setOption(option, false);
                } catch (e) {
                    console.warn('setOption failed', e);
                }
            }

            try {
                if (isPieLikeChart) {
                    // Pequeño timeout para permitir que el DOM aplique estilos antes del resize
                    setTimeout(() => {
                        if (currentEchartsInstance !== chartInstance) return;
                        if (typeof chartInstance.resize === 'function') {
                            chartInstance.resize();
                        }
                        applyOptionAndFinish();
                    }, 60);
                } else {
                    // Para gráficas no circulares: también hacer resize antes de setOption para permitir animaciones
                    if (typeof chartInstance.resize === 'function') {
                        chartInstance.resize();
                    }
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
                row.className = 'statistics-causes__row';

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
                    <td class="statistics-causes__range">${item.range}</td>
                    <td class="statistics-causes__total">${formatTotal(item.total)}</td>
                    <td class="causas-principales-cell" title="${escapeAttr(causasTitle)}">${causasText || 'No disponible'}</td>
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
                'distritoes': 'bar',           // Barras verticales para distritoes
                'comparativa': 'bar'               // Barras agrupadas para comparativa
            };
            return typeMap[metric] || 'bar';
        }

        function updateChart() {
            loadChart(currentChartType);
        }

        function resetActiveDataFilters() {
            Object.assign(activeFilters, {
                dateRange: 'all',
                startDate: null,
                endDate: null,
                selectedMonths: [],
                selectedYears: [],
                municipios: [],
                municipiosNames: [],
                causas: [],
                causasNames: [],
                distritoes: [],
                distritoesNames: [],
                sexo: null,
                edad: null,
                granularidad: 'month',
                mostrarCausasPrincipales: false,
                tipoComparativa: 'residencia-defuncion',
                tipoMunicipio: 'defuncion'
            });
        }

        function clearFilters(suppressUpdate = false) {
            const safeSetValue = (id, value) => {
                const el = document.getElementById(id);
                if (!el) return;
                if (el.tomselect) el.tomselect.setValue(value, true);
                else el.value = value;
            };
            
            safeSetValue('dateRange', 'all');
            safeSetValue('year', '');
            safeSetValue('month', '');
            safeSetValue('quarter', '');
            safeSetValue('customStartDate', defaultDateRange.startDate);
            safeSetValue('customEndDate', defaultDateRange.endDate);
            safeSetValue('sexoFilter', '');
            safeSetValue('edadFilter', '');
            safeSetValue('granularidadFilter', 'month');
            safeSetValue('tipoComparativaFilter', 'residencia-defuncion');
            safeSetValue('tipoMunicipioFilter', 'defuncion');
            const causesToggle = document.getElementById('mostrarCausasPrincipales');
            if (causesToggle) causesToggle.checked = false;
            // NO resetear chartLimit aquí - preservar configuración de visualización por métrica
            // safeSetValue('chartLimit', 'all');
            // chartConfig.limit = null;
            
            // Limpiar checkboxes de meses
            document.querySelectorAll('.month-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Limpiar multiselects y actualizar Tom Select
            const multiSelectIds = ['municipiosFilter', 'causasFilter', 'distritoesFilter'];
            multiSelectIds.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = '';
                    // Si Tom Select está inicializado, actualizar su valor
                    if (element.tomselect) {
                        element.tomselect.clear(true);
                    }
                }
            });
            if (metricUsesMunicipalityAndDistrict()) updateMunicipiosOptions([]);
            
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

            renderChartTypeButtons(currentChartType);
            renderDataLabelButtons(currentChartType);
            renderChartLimitButtons(currentChartType);
            
            resetActiveDataFilters();
            updateActiveFiltersDisplay();
            if (!suppressUpdate) updateChart();
        }

        function showLoadingMessage() {
            const loadingMessage = document.getElementById('loadingMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (loadingMessage) loadingMessage.style.display = 'flex';
            if (errorMessage) errorMessage.style.display = 'none';
            const chartEl = document.getElementById('mainChart');
            if (chartEl) chartEl.style.visibility = 'hidden';
            document.getElementById('statisticsPreviousComparison')?.classList.add('hidden');
            document.getElementById('statisticsChartContext')?.classList.add('hidden');
            document.getElementById('statisticsChartSource')?.classList.add('hidden');
            const viewData = document.getElementById('statisticsViewData');
            if (viewData) {
                viewData.href = '#';
                viewData.setAttribute('aria-disabled', 'true');
            }
        }

        function hideLoadingMessage() {
            const loadingMessage = document.getElementById('loadingMessage');
            if (loadingMessage) loadingMessage.style.display = 'none';
        }

        function showErrorMessage(message, preserveContext = false) {
            document.getElementById('errorText').textContent = message;
            const errorMessage = document.getElementById('errorMessage');
            const loadingMessage = document.getElementById('loadingMessage');
            if (errorMessage) errorMessage.style.display = 'flex';
            if (loadingMessage) loadingMessage.style.display = 'none';
            const chartEl = document.getElementById('mainChart');
            if (chartEl) chartEl.style.visibility = 'hidden';
            document.getElementById('statisticsPreviousComparison')?.classList.add('hidden');
            if (!preserveContext) document.getElementById('statisticsChartContext')?.classList.add('hidden');
            document.getElementById('statisticsChartSource')?.classList.add('hidden');
            const viewData = document.getElementById('statisticsViewData');
            if (viewData) {
                viewData.href = '#';
                viewData.setAttribute('aria-disabled', 'true');
            }
        }
    </script>

    <style media="not all" data-legacy-statistics-page-styles>
        /* Estilos para botones de pestaña */
        .chart-tab-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1rem;
            border: 1px solid #d1d5db;
            border-bottom: 0;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #404041;
            font-weight: 500;
            white-space: nowrap;
            position: relative;
        }

        .chart-tab-btn:hover:not(.active) {
            background-color: #f3f4f6;
        }

        .chart-tab-btn.active {
            color: white;
            background-color: #404041;
            border-color: #d1d5db;
        }

        .chart-tab-btn i {
            color: currentColor;
        }

        .visual-option-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            justify-content: center;
            width: 100%;
            min-height: 4.9rem;
            padding: 0.65rem 0.5rem;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #404041;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            text-align: center;
            border-radius: 0.75rem;
        }

        .visual-option-btn.is-compact-single {
            width: auto;
            min-width: 9.5rem;
            padding-left: 0.9rem;
            padding-right: 0.9rem;
        }

        .visual-option-btn span {
            line-height: 1.05;
        }

        .visual-option-label {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.18rem 0.48rem;
            border-radius: 9999px;
            background: #f8f2f5;
            color: #611132;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            width: fit-content;
        }

        .visual-option-btn:hover:not(:disabled) {
            border-color: #c7b0bb;
            background: #fdf8fa;
            transform: translateY(-1px);
        }

        .visual-option-btn.active {
            border-color: #611132;
            background: #f8f2f5;
            color: #611132;
            box-shadow: 0 0 0 1px rgba(97, 17, 50, 0.08);
        }

        .visual-option-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .visual-option-card {
            min-height: 4.9rem;
        }

        .visual-option-pill {
            min-height: 4.9rem;
        }

        .visual-limit-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.7rem;
            height: 1.7rem;
            border-radius: 9999px;
            background: #f3f4f6;
            color: #611132;
            font-size: 0.88rem;
            font-weight: 800;
            line-height: 1;
        }

        .visual-limit-badge.is-all {
            width: 1.7rem;
            height: 1.7rem;
            font-size: 1.35rem;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            width: 0;
            height: 0;
            display: none;
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

    </style>

@endsection
