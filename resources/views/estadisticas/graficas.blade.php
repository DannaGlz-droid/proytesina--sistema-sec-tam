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
                                                    <option value="{{ $mun->id }}">{{ \App\Support\CatalogLabel::municipality($mun->name) }}</option>
                                                @endforeach
                                            </x-filtros.select>
                                        </div>
                                        @if($districts->count() > 0)
                                        <div id="filterdistritoes" class="statistics-filter-control dynamic-filter" style="display: none;">
                                            <label for="distritoesFilter">Distritos</label>
                                            <x-filtros.select id="distritoesFilter" placeholder="Selecciona distritos" multiple>
                                                @foreach($districts as $district)
                                                    <option value="{{ $district->id }}">{{ $district->display_name }}</option>
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
                                                    <option value="{{ $cause->id }}">{{ $cause->display_name }}</option>
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
                                </x-filtros.seccion>
                    </x-filtros.panel>

                    <!-- COLUMNA DERECHA - Gráfica -->
                    <div class="statistics-content">
                        <!-- Controles de Presentación -->
                        <aside class="statistics-display-panel" aria-labelledby="statistics-display-title">
                            <header class="statistics-display-panel__heading">
                                <div class="statistics-display-panel__title-row">
                                    <h2 id="statistics-display-title">Presentación</h2>
                                    <div class="statistics-display-panel__actions">
                                        <button type="button" id="statisticsPresentationReset" class="statistics-presentation-reset">Restablecer</button>
                                        <button type="button" id="statisticsPresentationCollapse" class="statistics-presentation-collapse" aria-expanded="true" aria-controls="statisticsPresentationGroups" aria-label="Contraer opciones de presentación">
                                            <i class="fas fa-chevron-up" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </header>
                            <div id="statisticsPresentationGroups" class="statistics-display-groups">
                                <!-- Tipo de Gráfica -->
                                <fieldset class="statistics-display-group">
                                    <legend id="statisticsChartTypeLegend">Tipo de gráfica</legend>
                                    <select id="chartTypeSelector" class="hidden" aria-hidden="true" tabindex="-1">
                                        <option value="bar">Barras</option>
                                        <option value="barHorizontal">Barras Horizontales</option>
                                        <option value="pie">Pastel</option>
                                        <option value="doughnut">Rosquilla</option>
                                        <option value="line">Línea</option>
                                        <option value="area">Área</option>
                                    </select>
                                    <div id="chartTypeButtons" class="statistics-visual-options" role="group" aria-labelledby="statisticsChartTypeLegend"></div>
                                    <p id="chartTypeAdaptationNote" class="statistics-presentation-note hidden" aria-live="polite"></p>
                                </fieldset>

                                <!-- Etiquetas -->
                                <fieldset class="statistics-display-group">
                                    <legend id="statisticsDataLabelLegend">Etiquetas</legend>
                                    <select id="datalabelMode" class="hidden" aria-hidden="true" tabindex="-1">
                                        <option value="value">Valor</option>
                                        <option value="percent">Porcentaje</option>
                                        <option value="both">Valor y porcentaje</option>
                                        <option value="none">Sin etiquetas</option>
                                    </select>
                                    <div id="dataLabelButtons" class="statistics-visual-options" role="group" aria-labelledby="statisticsDataLabelLegend"></div>
                                    <p id="dataLabelAdaptationNote" class="statistics-presentation-note hidden" aria-live="polite"></p>
                                </fieldset>

                                <!-- Top N -->
                                <fieldset id="filterTop" class="statistics-display-group" style="display: none;">
                                    <legend id="statisticsChartLimitLegend">Cantidad de resultados</legend>
                                    <select id="chartLimit" class="hidden" aria-hidden="true" tabindex="-1">
                                        <option value="all">Todos</option>
                                        <option value="5">Top 5</option>
                                        <option value="10" selected>Top 10</option>
                                        <option value="15">Top 15</option>
                                    </select>
                                    <div id="chartLimitButtons" class="statistics-visual-options" role="group" aria-labelledby="statisticsChartLimitLegend"></div>
                                    <p id="chartLimitAdaptationNote" class="statistics-presentation-note hidden" aria-live="polite"></p>
                                    <p id="chartLimitReadabilityNote" class="statistics-presentation-note statistics-presentation-note--guidance hidden" aria-live="polite"></p>
                                </fieldset>

                                <!-- Paleta -->
                                <fieldset class="statistics-display-group statistics-display-group--palette">
                                    <legend id="statisticsPaletteLegend">Apariencia</legend>
                                    <div class="statistics-palette-popover">
                                        <button type="button" id="statisticsPaletteToggle" class="statistics-palette-toggle" aria-expanded="false" aria-controls="statisticsPaletteMenu" aria-haspopup="true">
                                            <span id="statisticsPaletteSelection" class="statistics-palette-selection" aria-hidden="true"></span>
                                            <span id="statisticsPaletteLabel" class="statistics-palette-label">Granate institucional</span>
                                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                                        </button>
                                        <div id="statisticsPaletteMenu" class="statistics-palette-menu hidden" aria-labelledby="statisticsPaletteLegend">
                                            <div id="colorPalettePicker" class="statistics-palette-grid"></div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset id="statisticsAgeDetailGroup" class="statistics-display-group statistics-display-group--age-detail" style="display: none;">
                                    <legend id="statisticsAgeDetailLegend">Contenido</legend>
                                    <select id="ageDetailMode" class="hidden" aria-hidden="true" tabindex="-1">
                                        <option value="summary" selected>Solo edades</option>
                                        <option value="causes">Edades y causas</option>
                                    </select>
                                    <div id="ageDetailButtons" class="statistics-visual-options" role="group" aria-labelledby="statisticsAgeDetailLegend">
                                        <button type="button" class="visual-option-btn visual-option-card active" data-target="ageDetailMode" data-value="summary" aria-pressed="true">
                                            <i class="fas fa-chart-column" aria-hidden="true"></i>
                                            <span class="visual-option-label">Solo edades</span>
                                        </button>
                                        <button type="button" class="visual-option-btn visual-option-card" data-target="ageDetailMode" data-value="causes" aria-pressed="false">
                                            <i class="fas fa-list-ol" aria-hidden="true"></i>
                                            <span class="visual-option-label">Edades y causas</span>
                                        </button>
                                    </div>
                                </fieldset>

                            </div>
                            <span id="statisticsPresentationStatus" class="sr-only" aria-live="polite"></span>
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
                                <div id="errorMessage" class="statistics-chart-state statistics-chart-state--empty" style="display: none;" role="status" aria-live="polite" aria-atomic="true">
                                    <div id="statisticsChartStateContent" class="statistics-chart-state__content">
                                        <i id="statisticsChartStateIcon" class="statistics-chart-state__icon fas fa-chart-column" aria-hidden="true"></i>
                                        <strong id="errorText">No hay datos para los filtros seleccionados.</strong>
                                        <span id="statisticsChartStateDescription">Prueba con otros criterios o limpia los filtros aplicados.</span>
                                        <button id="statisticsChartStateAction" class="statistics-chart-state__action hidden" type="button"></button>
                                    </div>
                                </div>
                            </div>
                            <section id="causasPrincipalesContainer" class="statistics-causes hidden" aria-labelledby="statistics-causes-title">
                                <header class="statistics-causes__header">
                                    <div>
                                        <h3 id="statistics-causes-title">Causas principales por grupo de edad</h3>
                                        <p>Las tres causas con más registros dentro de cada grupo.</p>
                                    </div>
                                    <span id="statisticsCausesSummary" class="statistics-causes__summary"></span>
                                </header>
                                <div id="causasPrincipalesBody" class="statistics-causes__list"></div>
                            </section>

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
        const defaultPresentationConfig = Object.freeze({
            type: 'bar',
            dataLabelMode: 'value',
            limit: 10,
            ageDetailMode: 'summary',
            colorPalette: 'maroon611132'
        });

        let chartConfig = {
            ...defaultPresentationConfig,
            groupBy: 'month'
        };

        // Preferencias deseadas: una vista puede adaptarlas sin sobrescribirlas.
        let preferredConfig = {
            type: defaultPresentationConfig.type,
            dataLabelMode: defaultPresentationConfig.dataLabelMode,
            limit: defaultPresentationConfig.limit,
            ageDetailMode: defaultPresentationConfig.ageDetailMode
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
            ],
            grayscale: [
                '#1F2937', '#334155', '#475569', '#58677B', '#64748B',
                '#718096', '#7C8998', '#8793A1', '#929DAA', '#9DA7B2',
                '#A8B1BB', '#B2BAC3', '#BCC4CC', '#C6CDD4', '#D0D6DC'
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
            maroon611132: ['#2C0617', '#4a0e26', '#611132', '#8B2A52', '#9C4460', '#B84C3A', '#C97C8A', '#D96969', '#E8A8A8', '#F0CCCC'],
            grayscale: ['#1F2937', '#334155', '#475569', '#58677B', '#64748B', '#718096', '#7C8998', '#8793A1', '#929DAA', '#9DA7B2']
        };

        const colorPaletteLabels = {
            aqua: 'Verde natural',
            autumn: 'Tonos tierra',
            rose: 'Rojos',
            spectrum: 'Alto contraste',
            earth: 'Multicolor',
            goldenEarth: 'Violetas',
            maroon611132: 'Granate institucional',
            institutional: 'Azul institucional',
            grayscale: 'Escala de grises'
        };

        const primaryPaletteKeys = ['maroon611132', 'institutional', 'spectrum', 'grayscale'];
        const additionalPaletteKeys = ['aqua', 'autumn', 'rose', 'earth', 'goldenEarth'];

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
            edades: ['dates', 'municipios', 'causas', 'distritoes'],
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
            both: 'fa-layer-group',
            none: 'fa-eye-slash'
        };

        const chartLimitLabels = {
            all: 'Todos',
            5: 'Top 5',
            10: 'Top 10',
            15: 'Top 15'
        };

        const chartTypeLabels = {
            bar: 'Barras',
            barHorizontal: 'Barras horizontales',
            pie: 'Pastel',
            doughnut: 'Rosquilla',
            line: 'Línea',
            area: 'Área'
        };

        const dataLabelModeLabels = {
            value: 'Valor',
            percent: 'Porcentaje',
            both: 'Valor y porcentaje',
            none: 'Sin etiquetas'
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
            document.getElementById('tipoComparativaFilter').addEventListener('change', markStatisticsFilterDraft);
            document.getElementById('tipoMunicipioFilter').addEventListener('change', markStatisticsFilterDraft);

            document.getElementById('chartTypeSelector').addEventListener('change', function() {
                chartConfig.type = this.value;
                preferredConfig.type = this.value;  // Guardar preferencia global
                // CONFIGURACIONES SON GLOBALES - se mantienen al cambiar de métrica
                renderChartTypeButtons(currentChartType);
                renderChartLimitButtons(currentChartType);
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
            document.getElementById('ageDetailMode').addEventListener('change', function() {
                chartConfig.ageDetailMode = this.value;
                preferredConfig.ageDetailMode = this.value;
                renderAgeDetailButtons();
                rerenderLatestChart();
            });

            document.getElementById('chartTypeButtons')?.addEventListener('click', function(event) {
                const button = event.target.closest('.visual-option-btn[data-target="chartTypeSelector"]');
                if (!button || button.disabled) return;
                setSelectValueAndTrigger('chartTypeSelector', button.dataset.value);
                restorePresentationFocus(this, button.dataset.value);
            });

            document.getElementById('dataLabelButtons')?.addEventListener('click', function(event) {
                const button = event.target.closest('.visual-option-btn[data-target="datalabelMode"]');
                if (!button || button.disabled) return;
                setSelectValueAndTrigger('datalabelMode', button.dataset.value);
                restorePresentationFocus(this, button.dataset.value);
            });

            document.getElementById('chartLimitButtons')?.addEventListener('click', function(event) {
                const button = event.target.closest('.visual-option-btn[data-target="chartLimit"]');
                if (!button || button.disabled) return;
                setSelectValueAndTrigger('chartLimit', button.dataset.value);
                restorePresentationFocus(this, button.dataset.value);
            });

            document.getElementById('ageDetailButtons')?.addEventListener('click', function(event) {
                const button = event.target.closest('.visual-option-btn[data-target="ageDetailMode"]');
                if (!button || button.disabled) return;
                setSelectValueAndTrigger('ageDetailMode', button.dataset.value);
                restorePresentationFocus(this, button.dataset.value);
            });

            const palettePicker = document.getElementById('colorPalettePicker');
            const paletteToggle = document.getElementById('statisticsPaletteToggle');
            const paletteMenu = document.getElementById('statisticsPaletteMenu');
            const palettePopover = paletteToggle?.closest('.statistics-palette-popover');

            const closePaletteMenu = () => {
                if (!paletteMenu || !paletteToggle) return;
                paletteMenu.classList.add('hidden');
                paletteMenu.classList.remove('opens-up');
                paletteMenu.style.removeProperty('max-height');
                paletteToggle.setAttribute('aria-expanded', 'false');
            };

            const positionPaletteMenu = () => {
                if (!paletteMenu || !paletteToggle || paletteMenu.classList.contains('hidden')) return;

                const viewportEdge = 12;
                const menuGap = 6;
                const maximumHeight = 352;
                const triggerRect = paletteToggle.getBoundingClientRect();
                const spaceBelow = Math.max(0, window.innerHeight - triggerRect.bottom - viewportEdge);
                const spaceAbove = Math.max(0, triggerRect.top - viewportEdge);

                paletteMenu.style.removeProperty('max-height');
                const desiredHeight = Math.min(paletteMenu.scrollHeight, maximumHeight);
                const opensUp = spaceBelow < desiredHeight && spaceAbove > spaceBelow;
                const availableHeight = Math.max(96, (opensUp ? spaceAbove : spaceBelow) - menuGap);

                paletteMenu.classList.toggle('opens-up', opensUp);
                paletteMenu.style.maxHeight = `${Math.min(desiredHeight, availableHeight)}px`;
            };

            if (paletteToggle && paletteMenu) {
                paletteToggle.addEventListener('click', function() {
                    const willOpen = paletteMenu.classList.contains('hidden');
                    if (!willOpen) {
                        closePaletteMenu();
                        return;
                    }

                    paletteMenu.classList.remove('hidden');
                    this.setAttribute('aria-expanded', 'true');
                    window.requestAnimationFrame(positionPaletteMenu);
                });

                document.addEventListener('click', function(event) {
                    if (palettePopover?.contains(event.target)) return;
                    closePaletteMenu();
                });

                document.addEventListener('keydown', function(event) {
                    if (event.key !== 'Escape' || paletteMenu.classList.contains('hidden')) return;
                    closePaletteMenu();
                    paletteToggle.focus();
                });

                document.addEventListener('scroll', positionPaletteMenu, true);
                window.addEventListener('resize', positionPaletteMenu);
            }

            if (palettePicker) {
                palettePicker.addEventListener('click', function(event) {
                    const moreButton = event.target.closest('[data-palette-more]');
                    if (moreButton) {
                        const additional = this.querySelector('[data-additional-palettes]');
                        const willOpen = additional?.classList.contains('hidden');
                        additional?.classList.toggle('hidden', !willOpen);
                        moreButton.setAttribute('aria-expanded', String(willOpen));
                        moreButton.querySelector('i')?.classList.toggle('fa-chevron-down', !willOpen);
                        moreButton.querySelector('i')?.classList.toggle('fa-chevron-up', willOpen);
                        window.requestAnimationFrame(positionPaletteMenu);
                        return;
                    }

                    const button = event.target.closest('.palette-chip');
                    if (!button) return;
                    const paletteName = button.dataset.palette;
                    if (!paletteName) return;
                    const changed = chartConfig.colorPalette !== paletteName;
                    chartConfig.colorPalette = paletteName;
                    renderColorPalettePreview(paletteName);
                    closePaletteMenu();
                    paletteToggle?.focus();
                    if (changed) rerenderLatestChart();
                });
            }

            document.getElementById('statisticsPresentationReset')?.addEventListener('click', resetPresentation);
            document.getElementById('statisticsPresentationCollapse')?.addEventListener('click', function() {
                const panel = this.closest('.statistics-display-panel');
                const collapsed = !panel?.classList.contains('is-collapsed');
                panel?.classList.toggle('is-collapsed', collapsed);
                this.setAttribute('aria-expanded', String(!collapsed));
                this.setAttribute('aria-label', collapsed ? 'Expandir opciones de presentación' : 'Contraer opciones de presentación');
                this.querySelector('i')?.classList.toggle('fa-chevron-down', collapsed);
                this.querySelector('i')?.classList.toggle('fa-chevron-up', !collapsed);
            });

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
            document.getElementById('statisticsChartStateAction')?.addEventListener('click', function() {
                if (this.dataset.action === 'clear') {
                    clearFilters();
                    return;
                }
                updateChart();
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
            if (currentChartType === 'edades' && chartConfig.ageDetailMode === 'causes' && typeof html2canvas !== 'undefined') {
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
            const allFilters = ['filterTipoMunicipio', 'filterMunicipios', 'filterCausas', 'filterdistritoes', 'filterSexo', 'filterEdad', 'filterGranularidad', 'filterTipoComparativa'];
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

            const ageDetailGroup = document.getElementById('statisticsAgeDetailGroup');
            if (ageDetailGroup) {
                ageDetailGroup.style.display = chartType === 'edades' ? '' : 'none';
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
            chartConfig.ageDetailMode = preferredConfig.ageDetailMode;

            updateChartTypeOptions(chartType, effectiveType);
            document.getElementById('datalabelMode').value = effectiveDataLabel;
            document.getElementById('chartLimit').value = effectiveLimit === null ? 'all' : String(effectiveLimit);
            document.getElementById('ageDetailMode').value = preferredConfig.ageDetailMode;
            updateDataLabelOptions(chartType);
            renderChartTypeButtons(chartType);
            renderDataLabelButtons(chartType);
            renderChartLimitButtons(chartType);
            renderAgeDetailButtons();

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
            
            // Tendencias admite mostrar el valor u ocultar sus etiquetas.
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

            const renderPaletteButton = key => {
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
            };

            const showAdditional = additionalPaletteKeys.includes(paletteName);
            picker.innerHTML = `
                <div class="statistics-palette-options" role="group" aria-label="Paletas recomendadas">
                    ${primaryPaletteKeys.map(renderPaletteButton).join('')}
                </div>
                <button type="button" class="statistics-palette-more" data-palette-more aria-expanded="${showAdditional}">
                    <span>Más paletas</span>
                    <i class="fas ${showAdditional ? 'fa-chevron-up' : 'fa-chevron-down'}" aria-hidden="true"></i>
                </button>
                <div class="statistics-palette-options statistics-palette-options--additional ${showAdditional ? '' : 'hidden'}" data-additional-palettes role="group" aria-label="Paletas adicionales">
                    ${additionalPaletteKeys.map(renderPaletteButton).join('')}
                </div>
            `;
        }

        function setSelectValueAndTrigger(selectId, value) {
            const select = document.getElementById(selectId);
            if (!select) return;
            select.value = value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function restorePresentationFocus(container, value) {
            window.requestAnimationFrame(() => {
                Array.from(container?.querySelectorAll('.visual-option-btn') || [])
                    .find(button => button.dataset.value === value)
                    ?.focus();
            });
        }

        function resetPresentation() {
            preferredConfig = {
                type: defaultPresentationConfig.type,
                dataLabelMode: defaultPresentationConfig.dataLabelMode,
                limit: defaultPresentationConfig.limit,
                ageDetailMode: defaultPresentationConfig.ageDetailMode
            };
            chartConfig.colorPalette = defaultPresentationConfig.colorPalette;

            applyPreferredPresentation(currentChartType);
            renderColorPalettePreview(defaultPresentationConfig.colorPalette);

            const status = document.getElementById('statisticsPresentationStatus');
            if (status) status.textContent = 'Presentación restablecida a los valores predeterminados.';

            updateChart();
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
            const accessibleLabels = {
                ...labels,
                barHorizontal: 'Barras horizontales'
            };

            container.className = `statistics-visual-options${isSingleType ? ' is-single' : ''}`;

            container.innerHTML = availableTypes.map(type => {
                const active = type === currentValue;
                return `
                    <button type="button" class="visual-option-btn visual-option-card ${isSingleType ? 'is-compact-single' : ''} ${active ? 'active' : ''}" data-target="chartTypeSelector" data-value="${type}" aria-pressed="${active}" aria-label="${accessibleLabels[type] || type}">
                        <i class="fas ${chartTypeIcons[type] || 'fa-chart-simple'}" aria-hidden="true"></i>
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
                { value: 'value', label: 'Valor' },
                { value: 'percent', label: 'Porcentaje' },
                { value: 'both', label: 'Valor y %' },
                { value: 'none', label: 'Sin etiquetas' }
            ];

            // Tendencias no dispone de un denominador categórico para porcentajes.
            if (chartType === 'tendencias') {
                options = options.filter(opt => ['value', 'none'].includes(opt.value));
            }

            const isSingleOption = options.length === 1;

            container.className = `statistics-visual-options${isSingleOption ? ' is-single' : ''}`;

            container.innerHTML = options.map(option => {
                const active = option.value === currentValue;
                return `
                    <button type="button" class="visual-option-btn visual-option-card ${isSingleOption ? 'is-compact-single' : ''} ${active ? 'active' : ''}" data-target="datalabelMode" data-value="${option.value}" aria-pressed="${active}">
                        <i class="fas ${dataLabelIcons[option.value] || 'fa-circle'}" aria-hidden="true"></i>
                        <span class="visual-option-label">${option.label}</span>
                    </button>
                `;
            }).join('');
        }

        function renderAgeDetailButtons() {
            const container = document.getElementById('ageDetailButtons');
            const select = document.getElementById('ageDetailMode');
            if (!container || !select) return;

            const currentValue = select.value || chartConfig.ageDetailMode || 'summary';
            container.querySelectorAll('.visual-option-btn').forEach(button => {
                const active = button.dataset.value === currentValue;
                button.classList.toggle('active', active);
                button.setAttribute('aria-pressed', String(active));
            });
        }

        function renderChartLimitButtons(chartType) {
            const container = document.getElementById('chartLimitButtons');
            const select = document.getElementById('chartLimit');
            const wrapper = document.getElementById('filterTop');
            if (!container || !select || !wrapper) return;

            const currentValue = select.value || (chartConfig.limit ? String(chartConfig.limit) : 'all');
            
            // Obtener los límites disponibles para este tipo de gráfico
            const availableLimits = chartLimitsByType[chartType] || chartLimitsByType.default;
            const availableCategoryCount = latestChartDataType === chartType
                ? Number(latestChartData?.available_categories || 0)
                : 0;

            const options = [
                {
                    value: 'all',
                    label: availableCategoryCount > 0
                        ? `${chartLimitLabels.all} (${availableCategoryCount.toLocaleString('es-MX')})`
                        : chartLimitLabels.all
                },
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

            const readabilityNote = document.getElementById('chartLimitReadabilityNote');
            const needsGuidance = currentValue === 'all' && availableCategoryCount > 15;
            if (readabilityNote) {
                const effectiveChartType = chartConfig.type === 'auto'
                    ? getOptimalChartType(chartType)
                    : chartConfig.type;
                const supportsHorizontal = (chartTypeOptions[chartType] || []).includes('barHorizontal');
                readabilityNote.textContent = !needsGuidance
                    ? ''
                    : ['pie', 'doughnut'].includes(effectiveChartType)
                        ? `${availableCategoryCount.toLocaleString('es-MX')} categorías se resumen en los 10 segmentos principales y un segmento Resto para conservar la legibilidad.`
                    : effectiveChartType === 'barHorizontal'
                        ? `${availableCategoryCount.toLocaleString('es-MX')} categorías amplían la altura de la gráfica. Usa Top 15 para una comparación más rápida.`
                        : `${availableCategoryCount.toLocaleString('es-MX')} categorías pueden saturar la gráfica.${supportsHorizontal ? ' Usa Top 15 o Barras horizontales para facilitar la lectura.' : ' Usa Top 15 para facilitar la lectura.'}`;
                readabilityNote.classList.toggle('hidden', !needsGuidance);
            }

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
                    showErrorMessage('No se pudo cargar la gráfica');
                    return;
                }

                latestChartData = data;
                latestChartDataType = chartType;
                renderChartLimitButtons(chartType);
                renderChart(cloneChartData(data));
            } catch (error) {
                if (error.name === 'AbortError') return;
                console.error('Error:', error);
                if (requestId === chartRequestSequence) {
                    showErrorMessage('No se pudo cargar la gráfica');
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
            setChartOutputActionsEnabled(true);
            if (reviewExcluded) reviewExcluded.href = excludedUrl;
        }

        function showNoChartData(data) {
            const excludedTotal = Number(data?.quality?.excluded_total || 0);
            const filteredResults = hasAppliedChartFilters();
            showEmptyMessage(
                excludedTotal > 0
                    ? 'No hay registros completos para construir esta gráfica.'
                    : 'No hay datos para los filtros seleccionados.',
                excludedTotal > 0,
                Number(data?.filtered_total ?? data?.total ?? 0),
                excludedTotal > 0
                    ? 'Revisa los registros no incluidos o ajusta los filtros aplicados.'
                    : (filteredResults
                        ? 'Prueba con otros criterios o limpia los filtros aplicados.'
                        : 'No hay registros disponibles para esta visualización.')
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
            const valueLabelFontSize = 13;
            const legendFontSize = 12;
            const chartFontFamily = 'Open Sans';
            
            // Grids diferentes según chart type para espaciado consistente
            const verticalBarGrids = {
                municipios: { left: 14, right: 14, top: 28, bottom: 18, containLabel: true },
                edades: { left: 14, right: 14, top: 28, bottom: 18, containLabel: true },
                genero: { left: 14, right: 14, top: 28, bottom: 18, containLabel: true },
                causas: { left: 14, right: 14, top: 28, bottom: 18, containLabel: true },
                distritoes: { left: 14, right: 14, top: 28, bottom: 18, containLabel: true },
                comparativa: { left: 18, right: 18, top: 34, bottom: 54, containLabel: true },
                tendencias: { left: 18, right: 18, top: 34, bottom: 36, containLabel: true }
            };
            
            // Obtener grid para el chart type actual
            const verticalBarGrid = verticalBarGrids[currentChartType] || { left: 14, right: 14, top: 28, bottom: 18, containLabel: true };
            const formatNumber = (num) => Number(num || 0).toLocaleString('es-MX');
            const labelRich = {
                value: { fontFamily: chartFontFamily, fontWeight: 600, color: '#1f2937', fontSize: 13, lineHeight: 16 },
                percent: { fontFamily: chartFontFamily, fontWeight: 600, color: '#1f2937', fontSize: 13, lineHeight: 16 },
                normal: { fontFamily: chartFontFamily, fontWeight: 500, color: '#64748b', fontSize: 10, lineHeight: 13 }
            };

            if (chartConfig.dataLabelMode === 'both') {
                verticalBarGrid.top += 8;
            }
            
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
                    if (chartContainer) {
                        chartContainer.style.width = '';
                        chartContainer.style.margin = '';
                    }
                }
            }

            document.getElementById('chartTitle').textContent = getCurrentChartTitle();
            const filteredTotal = Number(data.filtered_total ?? data.total ?? 0);
            const totalStr = filteredTotal.toLocaleString('es-MX');
            const totalEl = document.getElementById('totalRecords');
            if (totalEl) totalEl.textContent = totalStr;
            const badgeEl = document.getElementById('chartTotalValue');
            if (badgeEl) badgeEl.textContent = totalStr;
            document.getElementById('chartTotalBadge')?.classList.remove('hidden');
            updatePreviousPeriodComparison(data);
            updateChartContext(data);
            updateChartSourceSummary(data.source_summary);
            updateStatisticsAnalysisActions(data);

            let labels = data.labels || [];
            let values = currentChartType === 'comparativa' ? null : (data.counts || []);
            
            // Usar paleta de alto contraste para gráficas circulares, paleta normal para barras/líneas
            const paletteSource = isPieLikeChart ? colorPalettesCircular : colorPalettes;
            const palette = paletteSource[chartConfig.colorPalette];
            const colors = labels.map((_, i) => {
                if (isPieLikeChart && labels.length > 1 && labels.length <= 5) {
                    const distributedIndex = Math.floor(i * palette.length / labels.length);
                    return palette[Math.min(distributedIndex, palette.length - 1)];
                }
                return palette[i % palette.length];
            });

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

                const minimumWholeWordLength = 12;
                const wordChunkSize = Math.max(maxCharsPerLine, minimumWholeWordLength);
                const words = text.split(' ').flatMap(word => {
                    if (word.length <= wordChunkSize) return [word];
                    const parts = [];
                    for (let index = 0; index < word.length; index += wordChunkSize) {
                        parts.push(word.slice(index, index + wordChunkSize));
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

            const verticalAxisLabelStep = labels.length > 20 ? Math.ceil(labels.length / 12) : 1;
            const verticalCategoryAxisLabel = {
                interval: labels.length > 20
                    ? index => index % verticalAxisLabelStep === 0 || index === labels.length - 1
                    : 0,
                rotate: 0,
                align: 'center',
                verticalAlign: 'top',
                lineHeight: 14,
                fontSize: labels.length > 10 ? 11 : axisFontSize,
                fontFamily: chartFontFamily,
                fontWeight: 500,
                color: '#404041',
                hideOverlap: true,
                formatter: formatVerticalCategoryLabel
            };

            const horizontalCategoryAxisLabel = {
                interval: 0,
                align: 'right',
                lineHeight: labels.length > 15 ? 14 : 15,
                fontSize: labels.length > 15 ? 11 : axisFontSize,
                fontFamily: chartFontFamily,
                fontWeight: 500,
                color: '#404041',
                formatter: value => wrapCategoryLabel(value, 28)
            };

            if (chartWrapper) {
                const presentationPanel = document.querySelector('.statistics-display-panel');
                const chartPanel = document.getElementById('statisticsChartPanel');
                const chartHeader = chartPanel?.querySelector('.statistics-chart-header');
                const chartSource = document.getElementById('statisticsChartSource');
                const baseHeight = Math.min(420, Math.max(350, window.innerHeight * 0.4));
                let contentHeight = baseHeight;

                if (resolvedChartType === 'barHorizontal') {
                    contentHeight = Math.max(baseHeight, labels.length * 34 + 82);
                } else if (resolvedChartType === 'bar') {
                    contentHeight = baseHeight;
                } else if (isPieLikeChart) {
                    contentHeight = Math.max(baseHeight, 400);
                }

                const chartChromeHeight = (chartHeader?.offsetHeight || 0)
                    + (chartSource && !chartSource.classList.contains('hidden') ? chartSource.offsetHeight : 0);
                const sidebarMatchedHeight = window.matchMedia('(min-width: 1280px)').matches
                    ? Math.max(0, (presentationPanel?.offsetHeight || 0) - chartChromeHeight)
                    : 0;
                const targetHeight = Math.ceil(
                    resolvedChartType === 'barHorizontal'
                        ? Math.max(contentHeight, sidebarMatchedHeight)
                        : Math.min(680, Math.max(contentHeight, sidebarMatchedHeight))
                );

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
                    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' }, textStyle: { fontFamily: chartFontFamily, fontSize: axisFontSize } },
                    legend: {
                        type: 'scroll',
                        data: comparisonLegend,
                        bottom: 10,
                        itemWidth: 18,
                        itemHeight: 16,
                        textStyle: { fontFamily: chartFontFamily, fontSize: legendFontSize, fontWeight: 500, color: '#404041' }
                    },
                    grid: verticalBarGrid,
                    xAxis: {
                        type: 'category',
                        data: labels,
                        axisLine: { lineStyle: { color: '#94a3b8' } },
                        axisTick: { lineStyle: { color: '#94a3b8' } },
                        axisLabel: verticalCategoryAxisLabel
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: { show: false },
                        axisTick: { show: false },
                        axisLabel: { fontFamily: chartFontFamily, fontSize: axisFontSize, fontWeight: 400, color: '#526278' },
                        splitLine: { lineStyle: { color: '#dbe3ec', width: 1 } }
                    },
                    series: comparisonSeries.map((series, index) => ({
                        name: series.name,
                        type: 'bar',
                        stack: data.stacked ? 'total' : undefined,
                        data: series.data || [],
                        barMaxWidth: data.stacked
                            ? (labels.length <= 5 ? 72 : (labels.length <= 10 ? 58 : 42))
                            : (labels.length <= 5 ? 42 : (labels.length <= 10 ? 36 : 28)),
                        itemStyle: {
                            color: palette[index % palette.length],
                            borderRadius: data.stacked ? 0 : [3, 3, 0, 0]
                        },
                        emphasis: { focus: 'series' },
                        label: {
                            show: showComparisonLabels && chartConfig.dataLabelMode !== 'none',
                            position: data.stacked ? 'inside' : 'top',
                            fontSize: valueLabelFontSize,
                            fontFamily: chartFontFamily,
                            fontWeight: 600,
                            formatter: params => {
                                const value = Number(params.value || 0);
                                if (!value) return '';
                                const percentage = comparisonDenominator > 0
                                    ? `${((value / comparisonDenominator) * 100).toFixed(1)}%`
                                    : '0.0%';
                                if (chartConfig.dataLabelMode === 'percent') return percentage;
                                if (chartConfig.dataLabelMode === 'both') return `${formatNumber(value)}\n${percentage}`;
                                return formatNumber(value);
                            }
                        }
                    }))
                };
            } else if (chartType === 'pie' || chartType === 'doughnut') {
                // Gráficas de pastel
                const sourcePieData = labels.map((label, i) => ({
                    name: label,
                    value: values[i]
                }));
                const omittedTotal = Number(data.omitted_total || 0);
                const omittedCategories = Math.max(
                    0,
                    Number(data.available_categories || 0) - Number(data.displayed_categories || labels.length)
                );

                // Un pastel con decenas de sectores deja de ser comparable. En listados
                // extensos conservamos los diez principales y reunimos la cola en Resto.
                const circularDetailLimit = sourcePieData.length > 15 ? 10 : 12;
                const pieData = sourcePieData.slice(0, circularDetailLimit);
                const groupedPieData = sourcePieData.slice(circularDetailLimit);
                const groupedCategories = omittedCategories + groupedPieData.length;
                const groupedTotal = omittedTotal + groupedPieData.reduce(
                    (sum, item) => sum + Number(item.value || 0),
                    0
                );
                if (groupedTotal > 0) {
                    const remainderLabel = groupedCategories > 0
                        ? `Resto (${groupedCategories})`
                        : 'Resto';
                    pieData.push({ name: remainderLabel, value: groupedTotal, itemStyle: { color: '#cbd5e1' } });
                }
                // Filtrar slices con valor 0 para evitar mostrar muchos 0 alrededor del pastel
                const filteredPieData = pieData.filter(d => Number(d.value || 0) !== 0);
                if (filteredPieData.length === 0) {
                    showNoChartData(data);
                    return;
                }
                chartContainer.style.width = '100%';
                chartContainer.style.margin = '0';

                const circularWidth = chartWrapper?.clientWidth || 960;
                const circularHeight = chartWrapper?.clientHeight || 400;
                const compactCircularLayout = circularWidth < 760;
                const circularSegmentCount = filteredPieData.length;
                const crowdedCircularChart = circularSegmentCount > 6;
                const veryCrowdedCircularChart = circularSegmentCount > 12;
                const longestCircularLabel = Math.max(0, ...filteredPieData.map(item => String(item.name).length));
                const circularLegendNeedsMoreRoom = currentChartType === 'causas' || longestCircularLabel > 22;
                const useCircularCallouts = !compactCircularLayout
                    && chartConfig.dataLabelMode !== 'none'
                    && circularSegmentCount <= 6
                    && !circularLegendNeedsMoreRoom;
                const sparseCircularRadiusCap = circularSegmentCount <= 2
                    ? 174
                    : (circularSegmentCount <= 4 ? 184 : 192);
                // El diámetro se expresa en px y tiene un tope estable. Si se usara un
                // porcentaje, el círculo cambiaría al crecer el panel por notas o controles.
                const desktopOuterRadius = Math.max(
                    148,
                    Math.min(
                        circularLegendNeedsMoreRoom ? Math.min(184, sparseCircularRadiusCap) : sparseCircularRadiusCap,
                        Math.floor((circularHeight - 56) / 2),
                        Math.floor(circularWidth * .22)
                    )
                );
                const desktopInnerRadius = Math.round(desktopOuterRadius * .58);
                const desktopCenterRatio = useCircularCallouts
                    ? .5
                    : circularLegendNeedsMoreRoom
                    ? .32
                    : .35;
                const center = compactCircularLayout
                    ? ['50%', '39%']
                    : [`${Math.round(desktopCenterRatio * 100)}%`, '50%'];
                const radius = chartType === 'doughnut'
                    ? (compactCircularLayout
                        ? ['29%', '50%']
                        : [desktopInnerRadius, desktopOuterRadius])
                    : (compactCircularLayout ? '50%' : desktopOuterRadius);
                const circularTotal = filteredPieData.reduce((sum, item) => sum + Number(item.value || 0), 0);
                const pieValueByName = new Map(filteredPieData.map(item => [String(item.name), Number(item.value || 0)]));
                const formatCircularMetric = (name) => {
                    const value = pieValueByName.get(String(name)) || 0;
                    const percentage = circularTotal > 0 ? `${((value / circularTotal) * 100).toFixed(1)}%` : '0.0%';
                    if (chartConfig.dataLabelMode === 'none') return '';
                    if (chartConfig.dataLabelMode === 'percent') return percentage;
                    if (chartConfig.dataLabelMode === 'both') return `${formatNumber(value)} · ${percentage}`;
                    return formatNumber(value);
                };
                const formatCircularCallout = (params) => {
                    const percentage = filteredTotal > 0
                        ? `${((Number(params.value || 0) / filteredTotal) * 100).toFixed(1)}%`
                        : '0.0%';
                    const metric = chartConfig.dataLabelMode === 'percent'
                        ? percentage
                        : chartConfig.dataLabelMode === 'both'
                            ? `${formatNumber(params.value)} · ${percentage}`
                            : formatNumber(params.value);
                    return `{calloutName|${params.name}}\n{calloutMetric|${metric}}`;
                };

                option = {
                    color: colors,
                    animation: true,
                    animationDuration: 800,
                    animationEasing: 'cubicOut',
                    tooltip: {
                        trigger: 'item',
                        confine: true,
                        backgroundColor: '#ffffff',
                        borderColor: '#cbd5e1',
                        borderWidth: 1,
                        textStyle: { fontFamily: chartFontFamily, fontSize: axisFontSize, color: '#10233f' },
                        formatter: params => `${params.marker}<strong>${params.name}</strong><br>${formatNumber(params.value)} · ${params.percent.toFixed(1)}%`
                    },
                    legend: {
                        show: !useCircularCallouts,
                        type: compactCircularLayout || veryCrowdedCircularChart ? 'scroll' : 'plain',
                        orient: compactCircularLayout ? 'horizontal' : 'vertical',
                        left: compactCircularLayout
                            ? 18
                            : (circularLegendNeedsMoreRoom ? '50%' : (crowdedCircularChart ? '55%' : '57%')),
                        right: compactCircularLayout ? 18 : 16,
                        top: compactCircularLayout ? 'auto' : 'middle',
                        bottom: compactCircularLayout ? 4 : 'auto',
                        itemWidth: 12,
                        itemHeight: 12,
                        itemGap: compactCircularLayout ? 14 : (circularSegmentCount > 10 ? 7 : 10),
                        icon: 'roundRect',
                        formatter: name => {
                            const metric = !useCircularCallouts ? formatCircularMetric(name) : '';
                            return metric ? `{legendName|${name}}  {legendMetric|${metric}}` : `{legendName|${name}}`;
                        },
                        textStyle: {
                            fontFamily: 'Open Sans',
                            fontSize: 12,
                            color: '#334155',
                            rich: {
                                legendName: {
                                    width: compactCircularLayout ? undefined : (circularLegendNeedsMoreRoom ? 250 : 190),
                                    color: '#334155',
                                    lineHeight: 18,
                                    overflow: 'break'
                                },
                                legendMetric: { width: compactCircularLayout ? undefined : 74, align: 'right', color: '#10233f', fontWeight: 600, lineHeight: 18 }
                            }
                        }
                    },
                    series: [{
                        name: chartTitles[currentChartType],
                        type: 'pie',
                        center,
                        radius,
                        avoidLabelOverlap: true,
                        minAngle: 1,
                        selectedOffset: 4,
                        data: filteredPieData,
                        itemStyle: {
                            borderColor: '#ffffff',
                            borderWidth: 2,
                            borderJoin: 'round'
                        },
                        emphasis: {
                            scale: true,
                            scaleSize: 5,
                            itemStyle: { shadowBlur: 10, shadowColor: 'rgba(15, 23, 42, .16)' }
                        },
                        label: {
                            show: useCircularCallouts,
                            position: 'outside',
                            alignTo: 'none',
                            distanceToLabelLine: 7,
                            fontSize: 12,
                            fontFamily: chartFontFamily,
                            fontWeight: 600,
                            color: '#404041',
                            rich: {
                                calloutName: {
                                    fontFamily: chartFontFamily,
                                    fontWeight: 600,
                                    color: '#334155',
                                    fontSize: 12,
                                    lineHeight: 17
                                },
                                calloutMetric: {
                                    fontFamily: chartFontFamily,
                                    fontWeight: 600,
                                    color: '#64748b',
                                    fontSize: 11,
                                    lineHeight: 15
                                }
                            },
                            formatter: formatCircularCallout
                        },
                        labelLine: {
                            show: useCircularCallouts,
                            length: 18,
                            length2: 14,
                            minTurnAngle: 75,
                            lineStyle: { width: 1, color: '#94a3b8' }
                        },
                        labelLayout: {
                            hideOverlap: true
                        }
                    }],
                    graphic: chartType === 'doughnut' ? [{
                        type: 'group',
                        x: Math.round(circularWidth * (compactCircularLayout ? .5 : desktopCenterRatio)),
                        y: Math.round(circularHeight * (compactCircularLayout ? .39 : .5)),
                        children: [
                            {
                                type: 'text',
                                style: {
                                    x: 0,
                                    y: -5,
                                    text: formatNumber(circularTotal),
                                    textAlign: 'center',
                                    textVerticalAlign: 'bottom',
                                    fill: '#10233f',
                                    font: '700 22px Open Sans'
                                }
                            },
                            {
                                type: 'text',
                                style: {
                                    x: 0,
                                    y: 7,
                                    text: 'Total',
                                    textAlign: 'center',
                                    textVerticalAlign: 'top',
                                    fill: '#64748b',
                                    font: '600 11px Open Sans'
                                }
                            }
                        ]
                    }] : []
                };
            } else if (chartType === 'line') {
                // Línea o Área para tendencias
                const seriesConfig = {
                    name: chartTitles[currentChartType],
                    type: 'line',
                    data: values,
                    smooth: false,
                    symbol: 'circle',
                    symbolSize: 6,
                    showSymbol: labels.length <= 24,
                    lineStyle: { width: 2.5, color: palette[0] },
                    itemStyle: { color: palette[0] },
                    emphasis: { focus: 'series', scale: true },
                    labelLayout: { hideOverlap: true },
                    label: {
                        show: chartConfig.dataLabelMode !== 'none',
                        position: 'top',
                        fontSize: valueLabelFontSize,
                        fontFamily: chartFontFamily,
                        fontWeight: 600,
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
                    tooltip: { trigger: 'axis', axisPointer: { type: 'cross' }, textStyle: { fontFamily: chartFontFamily, fontSize: axisFontSize } },
                    grid: verticalBarGrid,
                    xAxis: {
                        type: 'category',
                        data: labels,
                        boundaryGap: false,
                        axisLine: { lineStyle: { color: '#94a3b8' } },
                        axisTick: { lineStyle: { color: '#94a3b8' } },
                        axisLabel: { rotate: 45, interval: 'auto', fontFamily: chartFontFamily, fontSize: axisFontSize, fontWeight: 500, color: '#526278' }
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: { show: false },
                        axisTick: { show: false },
                        axisLabel: { fontFamily: chartFontFamily, fontSize: axisFontSize, fontWeight: 400, color: '#526278' },
                        splitLine: { lineStyle: { color: '#dbe3ec', width: 1 } }
                    },
                    series: [seriesConfig]
                };
            } else if (chartType === 'bar' || chartType === 'barHorizontal') {
                // Barras verticales u horizontales
                const isHorizontal = chartType === 'barHorizontal';
                let categoryAxisFontSize = ['edades', 'genero'].includes(currentChartType)
                    ? 13
                    : (labels.length <= 5 ? 13 : (labels.length <= 10 ? 12 : 11));

                if (['municipios', 'distritoes'].includes(currentChartType)) {
                    categoryAxisFontSize = labels.length <= 5 ? 13 : (labels.length <= 15 ? 12 : 11);
                }

                const denseVerticalBars = !isHorizontal && labels.length > 15;
                const barLabelRich = {
                    value: {
                        ...labelRich.value,
                        fontSize: denseVerticalBars ? 11 : (labels.length > 10 ? 12 : 13),
                        lineHeight: denseVerticalBars ? 13 : (labels.length > 10 ? 15 : 16)
                    },
                    percent: {
                        ...labelRich.percent,
                        fontSize: denseVerticalBars ? 10 : (labels.length > 10 ? 11 : 13),
                        lineHeight: denseVerticalBars ? 12 : (labels.length > 10 ? 14 : 16)
                    },
                    normal: {
                        ...labelRich.normal,
                        fontSize: denseVerticalBars ? 9 : 10,
                        lineHeight: denseVerticalBars ? 11 : 13
                    }
                };
                const sparseVerticalInset = labels.length <= 2
                    ? '26%'
                    : (labels.length === 3 ? '12%' : (labels.length === 4 ? '8%' : null));
                const categoricalGrid = !isHorizontal && sparseVerticalInset
                    ? {
                        ...verticalBarGrid,
                        left: sparseVerticalInset,
                        right: sparseVerticalInset
                    }
                    : verticalBarGrid;
                
                // Preparar tooltip especial para Edades con causas principales
                let tooltipFormatter = null;
                if (currentChartType === 'edades' && chartConfig.ageDetailMode === 'causes' && data.data_with_causes) {
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
                        confine: true,
                        backgroundColor: '#ffffff',
                        borderColor: '#cbd5e1',
                        borderWidth: 1,
                        axisPointer: {
                            type: 'shadow',
                            shadowStyle: { color: 'rgba(71, 85, 105, .08)' }
                        },
                        textStyle: { fontSize: axisFontSize, color: '#10233f' },
                        ...(tooltipFormatter ? { formatter: tooltipFormatter } : {})
                    },
                    grid: isHorizontal
                        ? {
                            left: 18,
                            right: chartConfig.dataLabelMode === 'none' ? 18 : 46,
                            bottom: 22,
                            top: 22,
                            containLabel: true
                        }
                        : categoricalGrid,
                    [isHorizontal ? 'xAxis' : 'yAxis']: {
                        type: 'value',
                        axisLine: { show: false },
                        axisTick: { show: false },
                        axisLabel: { fontFamily: chartFontFamily, fontSize: axisFontSize, fontWeight: 400, color: '#526278' },
                        splitLine: { lineStyle: { color: '#dbe3ec', width: 1 } }
                    },
                    [isHorizontal ? 'yAxis' : 'xAxis']: {
                        type: 'category',
                        data: labels,
                        inverse: isHorizontal,
                        axisLine: { lineStyle: { color: '#94a3b8' } },
                        axisTick: { alignWithLabel: true, lineStyle: { color: '#94a3b8' } },
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
                        barMaxWidth: isHorizontal
                            ? (labels.length <= 5 ? 40 : (labels.length <= 10 ? 34 : (labels.length <= 15 ? 28 : 22)))
                            : (labels.length <= 2 ? 96 : (labels.length <= 4 ? 108 : (labels.length <= 5 ? 112 : (labels.length <= 10 ? 82 : (labels.length <= 15 ? 62 : 42))))),
                        barCategoryGap: isHorizontal
                            ? (labels.length <= 5 ? '38%' : (labels.length <= 10 ? '32%' : (labels.length <= 15 ? '28%' : '24%')))
                            : (labels.length <= 5 ? '45%' : (labels.length <= 10 ? '30%' : (labels.length <= 15 ? '22%' : '12%'))),
                        itemStyle: {
                            color: (params) => colors[params.dataIndex],
                            borderRadius: isHorizontal ? [0, 3, 3, 0] : [3, 3, 0, 0]
                        },
                        emphasis: { focus: 'self', itemStyle: { opacity: .88 } },
                        label: {
                            show: chartConfig.dataLabelMode !== 'none',
                            position: isHorizontal ? 'right' : 'top',
                            distance: isHorizontal ? 7 : 5,
                            fontSize: valueLabelFontSize,
                            fontFamily: chartFontFamily,
                            fontWeight: 600,
                            color: '#404041',
                            rich: barLabelRich,
                            formatter: (params) => {
                                const total = filteredTotal;
                                if (chartConfig.dataLabelMode === 'value') return `{value|${formatNumber(params.value)}}`;
                                if (chartConfig.dataLabelMode === 'percent') {
                                    return `{percent|${total > 0 ? ((params.value / total) * 100).toFixed(1) : '0.0'}%}`;
                                }
                                if (chartConfig.dataLabelMode === 'both') {
                                    return `{value|${formatNumber(params.value)}}\n{normal|${total > 0 ? ((params.value / total) * 100).toFixed(1) : '0.0'}%}`;
                                }
                                return '';
                            }
                        },
                        labelLayout: { hideOverlap: true }
                    }]
                };
            }

            option.textStyle = {
                fontFamily: chartFontFamily,
                fontWeight: 400,
                color: '#334155',
                ...(option.textStyle || {})
            };

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
            
            // Actualizar el detalle de causas principales si es Edades
            if (currentChartType === 'edades' && data.data_with_causes) {
                updateCausasTable(data.data_with_causes, data.total || 0);
            }
        }
        
        function updateCausasTable(dataWithCauses, grandTotal = 0) {
            const container = document.getElementById('causasPrincipalesContainer');
            const list = document.getElementById('causasPrincipalesBody');
            const summary = document.getElementById('statisticsCausesSummary');
            
            if (!container || !list) return;

            const mode = chartConfig.dataLabelMode;
            const safeGrandTotal = Number(grandTotal || 0);

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

            const formatCauseValueText = (count, rowTotal) => {
                const num = Number(count || 0);
                if (mode === 'percent') return formatPct(num, rowTotal);
                if (mode === 'both') return `${num.toLocaleString('es-MX')} (${formatPct(num, rowTotal)})`;
                return num.toLocaleString('es-MX');
            };

            const escapeHtml = (text) => String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
            
            const visibleItems = dataWithCauses.filter(item => Number(item.total || 0) > 0);
            const items = visibleItems.length ? visibleItems : dataWithCauses;

            list.innerHTML = items.map(item => {
                const sortedCauses = Object.entries(item.top_causes || {})
                    .map(([cause, count]) => ({ cause, count: Number(count || 0) }))
                    .sort((a, b) => b.count - a.count);
                const causes = sortedCauses.length
                    ? `<ol class="statistics-causes__ranking">${sortedCauses.map((entry, index) => `
                        <li>
                            <span class="statistics-causes__rank" aria-hidden="true">${index + 1}</span>
                            <span class="statistics-causes__name">${escapeHtml(entry.cause)}</span>
                            <strong class="statistics-causes__value">${formatCauseValueText(entry.count, item.total)}</strong>
                        </li>
                    `).join('')}</ol>`
                    : '<p class="statistics-causes__empty">No hay causas registradas para este grupo.</p>';

                return `
                    <article class="statistics-causes__item">
                        <div class="statistics-causes__group">
                            <span>Grupo de edad</span>
                            <h4>${escapeHtml(item.range)}</h4>
                            <p><strong>${formatTotal(item.total)}</strong> defunciones</p>
                        </div>
                        ${causes}
                    </article>
                `;
            }).join('');

            if (summary) {
                summary.textContent = `${items.length} ${items.length === 1 ? 'grupo' : 'grupos'} · hasta 3 causas por grupo`;
            }
            
            const shouldShow = currentChartType === 'edades' && chartConfig.ageDetailMode === 'causes';
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
            document.getElementById('chartTitle').textContent = getCurrentChartTitle();
            document.getElementById('chartTotalBadge')?.classList.add('hidden');
            document.getElementById('statisticsPreviousComparison')?.classList.add('hidden');
            document.getElementById('statisticsChartContext')?.classList.add('hidden');
            document.getElementById('statisticsChartSource')?.classList.add('hidden');
            setChartOutputActionsEnabled(false);
        }

        function hideLoadingMessage() {
            const loadingMessage = document.getElementById('loadingMessage');
            if (loadingMessage) loadingMessage.style.display = 'none';
        }

        function getCurrentChartTitle() {
            let title = currentChartType === 'comparativa'
                ? comparativaLabels[activeFilters.tipoComparativa] || chartTitles[currentChartType]
                : (chartTitles[currentChartType] || 'Gráfica');

            if (currentChartType === 'municipios' && activeFilters.tipoMunicipio) {
                const typeLabel = activeFilters.tipoMunicipio === 'residencia' ? 'residencia' : 'defunción';
                title += ` (${typeLabel})`;
            }

            return title;
        }

        function hasAppliedChartFilters() {
            return !document.getElementById('filtrosActivos')?.classList.contains('hidden');
        }

        function setChartOutputActionsEnabled(enabled) {
            const viewData = document.getElementById('statisticsViewData');
            const downloadButton = document.getElementById('descargarActual');
            const downloadToggle = document.getElementById('descargarOpciones');
            const downloadMenu = document.getElementById('downloadMenu');

            if (!enabled && viewData) {
                viewData.href = '#';
                viewData.setAttribute('aria-disabled', 'true');
            }
            [downloadButton, downloadToggle].forEach(button => {
                if (button) button.disabled = !enabled;
            });
            if (!enabled) {
                downloadMenu?.classList.add('hidden');
                downloadToggle?.setAttribute('aria-expanded', 'false');
            }
        }

        function configureChartState(kind, title, description, actionLabel = '', action = '') {
            const state = document.getElementById('errorMessage');
            const content = document.getElementById('statisticsChartStateContent');
            const icon = document.getElementById('statisticsChartStateIcon');
            const actionButton = document.getElementById('statisticsChartStateAction');

            document.getElementById('errorText').textContent = title;
            document.getElementById('statisticsChartStateDescription').textContent = description;
            state?.classList.toggle('statistics-chart-state--empty', kind === 'empty');
            state?.classList.toggle('statistics-chart-state--error', kind === 'error');
            state?.setAttribute('role', kind === 'error' ? 'alert' : 'status');
            state?.setAttribute('aria-live', kind === 'error' ? 'assertive' : 'polite');
            icon?.classList.toggle('fa-chart-column', kind === 'empty');
            icon?.classList.toggle('fa-triangle-exclamation', kind === 'error');

            if (actionButton) {
                actionButton.textContent = actionLabel;
                actionButton.dataset.action = action;
                actionButton.classList.toggle('hidden', !actionLabel);
            }
        }

        function showEmptyMessage(message, preserveContext = false, total = 0, description = 'No hay registros disponibles para esta visualización.') {
            configureChartState(
                'empty',
                message,
                description,
                hasAppliedChartFilters() ? 'Limpiar filtros' : '',
                'clear'
            );
            showUnavailableChartState(preserveContext);
            const totalBadge = document.getElementById('chartTotalBadge');
            const totalValue = document.getElementById('chartTotalValue');
            if (totalValue) totalValue.textContent = Number(total).toLocaleString('es-MX');
            totalBadge?.classList.remove('hidden');
        }

        function showErrorMessage(message, preserveContext = false) {
            configureChartState(
                'error',
                message,
                'Revisa tu conexión e inténtalo nuevamente.',
                'Reintentar',
                'retry'
            );
            showUnavailableChartState(preserveContext);
            document.getElementById('chartTotalBadge')?.classList.add('hidden');
        }

        function showUnavailableChartState(preserveContext = false) {
            const errorMessage = document.getElementById('errorMessage');
            const loadingMessage = document.getElementById('loadingMessage');
            if (errorMessage) errorMessage.style.display = 'flex';
            if (loadingMessage) loadingMessage.style.display = 'none';
            const chartEl = document.getElementById('mainChart');
            if (chartEl) chartEl.style.visibility = 'hidden';
            document.getElementById('chartTitle').textContent = getCurrentChartTitle();
            document.getElementById('statisticsPreviousComparison')?.classList.add('hidden');
            if (!preserveContext) document.getElementById('statisticsChartContext')?.classList.add('hidden');
            document.getElementById('statisticsChartSource')?.classList.add('hidden');
            setChartOutputActionsEnabled(false);
        }
    </script>


@endsection
