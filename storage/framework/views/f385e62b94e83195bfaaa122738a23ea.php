
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
            
        </div>

        <!-- CONTENEDOR PRINCIPAL -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl bg-white bg-opacity-95 max-w-full shadow-md overflow-hidden">
            <div class="border-b border-gray-300 bg-gray-50 px-4 lg:px-6 pt-4">
                <nav class="flex flex-wrap gap-1 overflow-x-auto pb-0" aria-label="Tabs">
                    <button type="button" class="chart-tab-btn active" data-chart="municipios" title="Distribución de defunciones por municipio" style="border-bottom-color: #611132;">
                        <i class="fas fa-map-marked-alt text-sm mr-1.5"></i>
                        <span class="font-lora text-sm">Municipios</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="tendencias" title="Tendencia temporal de defunciones" style="border-bottom-color: #4C8CC4;">
                        <i class="fas fa-chart-line text-sm mr-1.5"></i>
                        <span class="font-lora text-sm">Tendencias</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="edades" title="Distribución por rangos etarios" style="border-bottom-color: #75A84E;">
                        <i class="fas fa-chart-bar text-sm mr-1.5"></i>
                        <span class="font-lora text-sm">Edades</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="genero" title="Distribución por género" style="border-bottom-color: #9D2449;">
                        <i class="fas fa-venus-mars text-sm mr-1.5"></i>
                        <span class="font-lora text-sm">Género</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="causas" title="Causas principales de defunción" style="border-bottom-color: #6B4C8A;">
                        <i class="fas fa-heartbeat text-sm mr-1.5"></i>
                        <span class="font-lora text-sm">Causas</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="jurisdicciones" title="Distribución por jurisdicción" style="border-bottom-color: #C08400;">
                        <i class="fas fa-building text-sm mr-1.5"></i>
                        <span class="font-lora text-sm">Jurisdicciones</span>
                    </button>
                    <button type="button" class="chart-tab-btn" data-chart="comparativa" title="Residencia vs Lugar de Defunción" style="border-bottom-color: #4A7C7E;">
                        <i class="fas fa-exchange-alt text-sm mr-1.5"></i>
                        <span class="font-lora text-sm">Comparativa</span>
                    </button>
                </nav>
            </div>

            <div class="p-4 lg:p-6 pt-8 lg:pt-12">
                <!-- Layout: Filtros + Gráfica -->
                <div class="flex flex-col lg:flex-row gap-6 lg:items-stretch">
                    
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
                                            <?php $currentYear = now()->year; ?>
                                            <input type="text" id="year" placeholder="Ej: 2024 o 2024, 2025, 2026" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs" title="Escribe años separados por coma">
                                        </div>

                                        <!-- Se usa un único selector de meses que permite seleccionar 1 o varios -->

                                        <div class="filter-group" id="monthSelector" style="display: none;">
                                            <label class="block text-xs text-gray-600 font-lora mb-1">Meses de defunción:</label>
                                            <div class="grid grid-cols-3 gap-2 mt-2 months-container">
                                                <?php
                                                    $months = [
                                                        '01' => 'Ene','02' => 'Feb','03' => 'Mar','04' => 'Abr','05' => 'May','06' => 'Jun',
                                                        '07' => 'Jul','08' => 'Ago','09' => 'Sep','10' => 'Oct','11' => 'Nov','12' => 'Dic'
                                                    ];
                                                ?>
                                                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mval => $mlabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div>
                                                        <input type="checkbox" id="month-<?php echo e($mval); ?>" name="selectedMonths[]" class="month-checkbox" value="<?php echo e($mval); ?>">
                                                        <label for="month-<?php echo e($mval); ?>" class="month-label block text-center text-xs py-1.5 bg-gray-100 border border-gray-300 rounded cursor-pointer hover:bg-gray-200"><?php echo e($mlabel); ?></label>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                
                                <!-- Tipo de Municipio (Defunción vs Residencia) -->
                                <div id="filterTipoMunicipio" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-map-marker text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Tipo de Municipio</h4>
                                    </div>
                                    <select id="tipoMunicipioFilter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                        <option value="defuncion">Municipio de Defunción</option>
                                        <option value="residencia">Municipio de Residencia</option>
                                    </select>
                                </div>

                                <!-- Filtro de Municipios (contextual) -->
                                <div id="filterMunicipios" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-city text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Municipios</h4>
                                    </div>
                                    <select id="municipiosFilter" class="tomselect-select" multiple data-placeholder="Selecciona municipios">
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
                                    <select id="causasFilter" class="tomselect-select" multiple data-placeholder="Selecciona causas">
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
                                    <select id="jurisdiccionesFilter" class="tomselect-select" multiple data-placeholder="Selecciona jurisdicciones">
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

                                <!-- Selector de Tipo de Comparativa (contextual) -->
                                <div id="filterTipoComparativa" class="filter-section dynamic-filter" style="display: none;">
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-200">
                                        <i class="fas fa-exchange-alt text-[#611132] text-sm"></i>
                                        <h4 class="text-xs font-semibold text-[#404041] font-lora">Tipo de Comparativa</h4>
                                    </div>
                                    <select id="tipoComparativaFilter" class="w-full border border-[#404041] rounded-lg px-3 py-1.5 text-xs">
                                        <option value="residencia-defuncion">Residencia vs Lugar de Defunción</option>
                                        <option value="genero-causa">Género vs Causa</option>
                                        <option value="edad-causa">Rango Etario vs Causa</option>
                                        <option value="lugar-causa">Lugar de Defunción vs Causa</option>
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

                        <div class="mt-4 border border-[#404041] rounded-lg bg-white bg-opacity-95 overflow-visible shadow-sm">
                            <div class="bg-white px-4 py-3 border-b border-[#e5e7eb] flex items-center gap-2">
                                <i class="fas fa-palette text-[#611132] text-sm"></i>
                                <h3 class="text-sm font-lora font-semibold text-[#404041]">Paletas</h3>
                            </div>
                            <div class="px-4 py-4">
                                <div id="colorPalettePicker" class="grid grid-cols-2 gap-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMNA DERECHA - Gráfica -->
                    <div class="lg:flex-1 flex flex-col min-h-0">
                        <!-- Controles de Presentación -->
                        <div class="mb-6">
                            <div class="grid grid-cols-3 gap-4 items-stretch">
                                <!-- Tipo de Gráfica -->
                                <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm min-w-0">
                                    <label class="text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 font-lora">Tipo de Gráfica</label>
                                    <select id="chartTypeSelector" class="hidden">
                                        <option value="bar">Barras</option>
                                        <option value="barHorizontal">Barras Horizontales</option>
                                        <option value="pie">Pastel</option>
                                        <option value="doughnut">Rosquilla</option>
                                        <option value="line">Línea</option>
                                        <option value="area">Área</option>
                                    </select>
                                    <div id="chartTypeButtons" class="grid grid-flow-col auto-cols-fr gap-2 w-full max-w-[26rem]"></div>
                                </div>

                                <!-- Etiquetas -->
                                <div class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm min-w-0">
                                    <label class="text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 font-lora">Etiquetas</label>
                                    <select id="datalabelMode" class="hidden">
                                        <option value="value">Solo Valores</option>
                                        <option value="percent">Solo Porc.</option>
                                        <option value="both">Ambos</option>
                                    </select>
                                    <div id="dataLabelButtons" class="grid grid-flow-col auto-cols-fr gap-2 w-full max-w-[20.5rem]"></div>
                                </div>

                                <!-- Top N -->
                                <div id="filterTop" class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm min-w-0" style="display: none;">
                                    <label class="text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 font-lora">Top</label>
                                    <select id="chartLimit" class="hidden">
                                        <option value="all">Todos</option>
                                        <option value="5">Top 5</option>
                                        <option value="10">Top 10</option>
                                        <option value="15">Top 15</option>
                                    </select>
                                    <div id="chartLimitButtons" class="grid grid-flow-col auto-cols-fr gap-2 w-full max-w-[20.5rem]"></div>
                                </div>

                            </div>

                        
                        </div>

                        <!-- Gráfica Principal -->
                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col flex-1 min-h-0">
                            <!-- Filtros Activos/Aplicados -->
                            <div id="filtrosActivos" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                                <p class="text-xs font-semibold text-[#404041] font-lora mb-2">Filtros aplicados:</p>
                                <div id="filtrosActivosList" class="flex flex-wrap gap-2">
                                    <!-- Los chips se generan dinámicamente con JavaScript -->
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-4 mb-4">
                                <div class="flex items-center gap-3">
                                    <h2 id="chartTitle" class="text-lg font-bold text-[#404041] font-lora">Cargando...</h2>
                                    <div id="chartTotalBadge" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#f8f2f5] border border-[#e7d7de] text-sm font-semibold text-[#611132]">
                                        <span class="text-xs">Total</span>
                                        <span id="chartTotalValue">0</span>
                                    </div>
                                </div>
                                <div class="relative" id="downloadMenuWrapper">
                                    <div class="inline-flex overflow-hidden rounded-lg shadow-sm">
                                        <button type="button" class="bg-[#611132] text-white px-5 py-3 text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap" id="descargarActual">
                                            <i class="fas fa-download text-sm"></i>
                                            Descargar Gráfica
                                        </button>
                                        <button type="button" class="bg-[#4a0e26] text-white px-3 py-3 text-sm font-semibold hover:bg-[#3c0b1f] transition-all duration-300 border-l border-white/10" id="descargarOpciones" aria-label="Abrir opciones de descarga">
                                            <i class="fas fa-chevron-down text-xs opacity-90"></i>
                                        </button>
                                    </div>
                                    <div id="downloadMenu" class="hidden absolute right-0 mt-2 w-64 rounded-lg border border-gray-200 bg-white shadow-lg z-50 overflow-hidden">
                                        <button type="button" class="download-option w-full text-left px-5 py-3 text-sm font-lora text-[#404041] hover:bg-gray-50 flex items-center gap-3" data-export="png-transparent">
                                            <i class="fas fa-image text-[#611132] text-sm"></i>
                                            PNG (transparente)
                                        </button>
                                        <button type="button" class="download-option w-full text-left px-5 py-3 text-sm font-lora text-[#404041] hover:bg-gray-50 flex items-center gap-3" data-export="png-white">
                                            <i class="fas fa-image text-[#611132] text-sm"></i>
                                            PNG (fondo blanco)
                                        </button>
                                        <button type="button" class="download-option w-full text-left px-5 py-3 text-sm font-lora text-[#404041] hover:bg-gray-50 flex items-center gap-3" data-export="pdf">
                                            <i class="fas fa-file-pdf text-[#611132] text-sm"></i>
                                            PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="chart-wrapper relative flex-1 min-h-[520px]" style="position:relative;">
                                <div id="mainChart" style="width: 100%; height: 100%;"></div>
                                <div id="loadingMessage" class="absolute inset-0 z-40 items-center justify-center bg-white/70 backdrop-blur-[1px] rounded-lg" style="display: none;">
                                    <div class="bg-white rounded-full p-3 shadow-sm border border-gray-200">
                                        <i class="fas fa-spinner fa-spin text-2xl text-[#611132]"></i>
                                    </div>
                                </div>
                                <div id="errorMessage" class="absolute inset-0 z-40 items-center justify-center rounded-lg bg-white/70 px-4 text-center" style="display: none;">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                            <i class="fas fa-inbox text-3xl"></i>
                                        </div>
                                        <p class="text-lg font-lora text-gray-600" id="errorText">No hay datos para los filtros seleccionados</p>
                                        <p class="text-sm text-gray-500 font-lora">Intenta ajustar los criterios de búsqueda</p>
                                    </div>
                                </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir ECharts -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
    <!-- Incluir html2canvas para capturar el DOM cuando sea posible (mejor export visual) -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>

    <!-- Include modal para descargas -->
    <?php echo $__env->make('components.modal-descargas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        let currentChartType = 'municipios';
        let currentEchartsInstance = null;
        let chartConfig = {
            type: 'bar',
            dataLabelMode: 'value',
            limit: null,
            colorPalette: 'aqua',
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
            mostrarCausasPrincipales: false,
            tipoComparativa: 'residencia-defuncion',
            tipoMunicipio: 'defuncion'
        };

        // Full municipalities list with jurisdiction info (used to filter municipios when jurisdicción is selected)
        const municipalitiesFull = <?php echo json_encode($municipalities->map(function($m) { return ['id' => $m->id, 'name' => $m->name, 'jurisdiction_id' => $m->jurisdiction_id ?? null]; })->values()) ?>;

        const colorPalettes = {
            // Paleta aqua de 15 colores armoniosos de fuerte a suave (para Top <= 15)
            aqua: [
                '#2B4141', // dark slate
                '#245D66', // deep teal
                '#1D798A', // teal
                '#0EB1D2', // cyan strong
                '#21CBDE', // cyan
                '#34E4EA', // bright cyan
                '#3FEDE7', // aqua
                '#5FCFD0', // muted aqua
                '#8AB9B5', // muted teal
                '#A9BEB2', // ash teal
                '#C8C2AE', // pale oak
                '#D6D4C7', // warm gray
                '#E6E8E0', // light gray
                '#F0F6F4', // very light aqua
                '#F7F3EA'  // pale cream
            ]
            ,
            // Paleta autumn con tonos tierra y calidez progresiva.
            autumn: [
                '#4C061D', '#782D1F', '#8F4020', '#A55320', '#BB6721',
                '#D17A22', '#C39E5A', '#B4C292', '#9AA48A', '#7F6F4F',
                '#736F4E', '#5F593F', '#4B432E', '#3B3923', '#2E2B1E'
            ]
            ,
            // Paleta rose con base intensa y degradado suave.
            rose: [
                '#331832', '#4C061D', '#861B47', '#AF1D51', '#D81E5B',
                '#E43955', '#F0544F', '#F28A6E', '#DB9691', '#D1B7B2',
                '#C6D8D3', '#FDF0D5', '#F7E3C9', '#EECFC2', '#B78A8A'
            ],
            // Paleta spectrum de alto contraste para diferenciar mejor los Top 15.
            spectrum: [
                '#CC4318', '#F6511D', '#FFB400', '#C0B13C', '#80AD77',
                '#40AAB2', '#00A6ED', '#7FB800', '#46722A', '#0D2C54',
                '#6A4C93', '#9C6ADE', '#E76F51', '#2A9D8F', '#264653'
            ],
            // Paleta earth con buen contraste para barras y pastel.
            earth: [
                '#5A3E2B', '#8B5E34', '#B07D4F', '#D9A441', '#C0B13C',
                '#80AD77', '#5C8D89', '#4D6C8A', '#3D405B', '#264653',
                '#6D597A', '#B56576', '#E56B6F', '#EAAC8B', '#F4F1DE'
            ],
            // Paleta golden earth inspirada en la captura actual: dorados, olivo y cierre cálido.
            goldenEarth: [
                '#A56502', '#C77A02', '#AB9003', '#8EA604', '#A8AC03',
                '#C2B102', '#F5BB00', '#F1AD03', '#EC9F05', '#BF3100',
                '#7A5C1E', '#9B6A2F', '#B07A3F', '#D48A2F', '#E0B35B'
            ],
            // Paleta monocromática basada en #611132, de tintes claros a sombras profundas.
            maroon611132: [
                '#611132'
            ]
        };

        const colorPaletteLabels = {
            aqua: 'Aqua',
            autumn: 'Autumn',
            rose: 'Rose',
            spectrum: 'Spectrum',
            earth: 'Earth',
            goldenEarth: 'Golden Earth',
            maroon611132: 'Maroon 611132'
        };

        const chartTypeDefaults = {
            municipios: 'bar',
            tendencias: 'line',
            edades: 'bar',
            genero: 'bar',
            causas: 'bar',
            jurisdicciones: 'bar',
            comparativa: 'bar'
        };

        const chartTypeOptions = {
            municipios: ['bar', 'barHorizontal', 'pie', 'doughnut'],
            tendencias: ['line', 'area'],
            edades: ['bar', 'pie', 'doughnut'],
            genero: ['bar', 'pie', 'doughnut'],
            causas: ['bar', 'barHorizontal', 'pie', 'doughnut'],
            jurisdicciones: ['bar', 'barHorizontal', 'pie', 'doughnut'],
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

        const comparativaLabels = {
            'residencia-defuncion': 'Residencia vs Lugar de Defunción',
            'genero-causa': 'Género vs Causa de Defunción',
            'edad-causa': 'Rango Etario vs Causa de Defunción',
            'lugar-causa': 'Lugar de Defunción vs Causa'
        };

        const filtersForChart = {
            municipios: ['dates', 'tipoMunicipio', 'causas', 'jurisdicciones', 'sexo'],
            tendencias: ['dates', 'municipios', 'causas', 'sexo', 'granularidad'],
            edades: ['dates', 'municipios', 'causas', 'jurisdicciones', 'causasPrincipales'],
            genero: ['dates', 'municipios', 'causas', 'jurisdicciones'],
            causas: ['dates', 'municipios', 'jurisdicciones', 'sexo'],
            jurisdicciones: ['dates', 'causas', 'sexo'],
            comparativa: ['dates', 'tipoComparativa']
        };

        // Gráficas que deben mostrar el selector "Top"
        const chartTypesWithTopSelector = ['municipios', 'jurisdicciones', 'comparativa'];

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

        document.addEventListener('DOMContentLoaded', function() {
            try {
                if (typeof ChartDataLabels !== 'undefined') Chart.register(ChartDataLabels);
            } catch (e) {
                console.warn('ChartDataLabels no disponible');
            }

            // Cargar rangos de fecha default antes de inicializar
            loadDefaultDateRange();
            renderColorPalettePreview(chartConfig.colorPalette);
            renderChartTypeButtons('municipios');
            renderDataLabelButtons('municipios');
            renderChartLimitButtons('municipios');
            
            initializeEventListeners();
            selectChart('municipios');
        });

        // Función para cargar los rangos de fecha default
        async function loadDefaultDateRange() {
            try {
                const response = await fetch('<?php echo e(route("api.default-date-range")); ?>');
                const data = await response.json();
                
                // Actualizar los inputs de fecha
                const startDateInput = document.getElementById('customStartDate');
                const endDateInput = document.getElementById('customEndDate');
                
                if (startDateInput && endDateInput) {
                    startDateInput.value = data.start_date;
                    endDateInput.value = data.end_date;
                    
                    // Actualizar también los valores en activeFilters
                    activeFilters.startDate = data.start_date;
                    activeFilters.endDate = data.end_date;
                }
            } catch (error) {
                console.error('Error loading default date range:', error);
            }
        }

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
                            // Si cambió la jurisdicción mientras se ve 'municipios', actualizar la lista de municipios disponibles
                            if (element.id === 'jurisdiccionesFilter' && currentChartType === 'municipios') {
                                const selected = Array.isArray(value) ? value.map(String) : (value ? [String(value)] : []);
                                updateMunicipiosOptions(selected);
                            }
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

        // Actualiza las opciones del select de municipios según las jurisdicciones seleccionadas
        function updateMunicipiosOptions(selectedJurIds = []) {
            const munEl = document.getElementById('municipiosFilter');
            if (!munEl) return;
            const tom = munEl.tomselect;
            if (!tom) return;

            // Normalizar ids a strings para comparación
            const selStr = (selectedJurIds || []).map(String);

            // Filtrar la lista completa de municipios
            const allowed = municipalitiesFull.filter(m => {
                if (!m.jurisdiction_id) return selStr.length === 0; // si no hay info, mostrar sólo cuando no hay filtro
                if (selStr.length === 0) return true; // sin jurisdicción seleccionada -> todos
                return selStr.includes(String(m.jurisdiction_id));
            });

            // Guardar selecciones actuales y mantener sólo las que siguen permitidas
            const currentVals = Array.isArray(munEl.tomselect.getValue()) ? munEl.tomselect.getValue() : (munEl.tomselect.getValue() ? [munEl.tomselect.getValue()] : []);
            const allowedIds = allowed.map(a => String(a.id));
            const keep = currentVals.filter(v => allowedIds.includes(String(v)));

            // Reconstruir opciones
            tom.clearOptions();
            allowed.forEach(m => tom.addOption({ value: String(m.id), text: m.name }));

            // Restaurar selección válida
            tom.clear();
            if (keep.length) tom.setValue(keep);
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
            document.getElementById('tipoComparativaFilter').addEventListener('change', updateChart);
            document.getElementById('tipoMunicipioFilter').addEventListener('change', updateChart);

            document.getElementById('chartTypeSelector').addEventListener('change', function() {
                chartConfig.type = this.value;
                renderChartTypeButtons(currentChartType);
                updateChart();
            });
            document.getElementById('datalabelMode').addEventListener('change', function() {
                chartConfig.dataLabelMode = this.value;
                renderDataLabelButtons(currentChartType);
                updateChart();
            });
            document.getElementById('chartLimit').addEventListener('change', function() {
                chartConfig.limit = this.value === 'all' ? null : parseInt(this.value);
                renderChartLimitButtons(currentChartType);
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
            if (palettePicker) {
                palettePicker.addEventListener('click', function(event) {
                    const button = event.target.closest('.palette-chip');
                    if (!button) return;
                    const paletteName = button.dataset.palette;
                    if (!paletteName || chartConfig.colorPalette === paletteName) return;
                    chartConfig.colorPalette = paletteName;
                    renderColorPalettePreview(paletteName);
                    updateChart();
                });
            }

            document.getElementById('limpiarFiltros').addEventListener('click', clearFilters);

            const downloadMenuWrapper = document.getElementById('downloadMenuWrapper');
            const downloadMenu = document.getElementById('downloadMenu');
            const downloadButton = document.getElementById('descargarActual');
            const downloadOptionsButton = document.getElementById('descargarOpciones');

            if (downloadButton && downloadMenu) {
                downloadButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    exportCurrentChart('png-white');
                });

                if (downloadOptionsButton) {
                    downloadOptionsButton.addEventListener('click', function(event) {
                        event.stopPropagation();
                        downloadMenu.classList.toggle('hidden');
                    });
                }

                document.querySelectorAll('.download-option').forEach(option => {
                    option.addEventListener('click', async function() {
                        const exportType = this.dataset.export;
                        downloadMenu.classList.add('hidden');
                        await exportCurrentChart(exportType);
                    });
                });

                document.addEventListener('click', function(event) {
                    if (downloadMenuWrapper && !downloadMenuWrapper.contains(event.target)) {
                        downloadMenu.classList.add('hidden');
                    }
                });
            }
        }

        function getCurrentDownloadName(extension) {
            return `grafica-${currentChartType}-${new Date().toISOString().split('T')[0]}.${extension}`;
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

        async function captureChartAsCanvas(transparent = false) {
            if (currentChartType === 'edades' && document.getElementById('mostrarCausasPrincipales').checked && typeof html2canvas !== 'undefined') {
                const element = document.querySelector('[id="mainChart"]').closest('.bg-white');
                return html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: transparent ? null : '#ffffff'
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

            return canvas;
        }

        async function exportCurrentChart(exportType) {
            if (!currentEchartsInstance) return;

            if (exportType === 'pdf') {
                const canvas = await captureChartAsCanvas(false);
                if (!canvas) return;

                const { jsPDF } = window.jspdf || {};
                if (!jsPDF) return;

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
                return;
            }

            const transparent = exportType === 'png-transparent';
            const canvas = await captureChartAsCanvas(transparent);
            if (!canvas) return;

            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = getCurrentDownloadName('png');
            link.click();
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
            chartConfig.type = chartTypeDefaults[chartType];
            loadChart(chartType);
        }

        function updateVisibleFilters(chartType) {
            const allFilters = ['filterTipoMunicipio', 'filterMunicipios', 'filterCausas', 'filterJurisdicciones', 'filterSexo', 'filterGranularidad', 'filterCausasPrincipales', 'filterTipoComparativa'];
            const availableFilters = filtersForChart[chartType] || [];

            allFilters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    let show = false;
                    if (filterId === 'filterTipoMunicipio' && availableFilters.includes('tipoMunicipio')) show = true;
                    if (filterId === 'filterMunicipios' && availableFilters.includes('municipios')) show = true;
                    if (filterId === 'filterCausas' && availableFilters.includes('causas')) show = true;
                    if (filterId === 'filterJurisdicciones' && availableFilters.includes('jurisdicciones')) show = true;
                    if (filterId === 'filterSexo' && availableFilters.includes('sexo')) show = true;
                    if (filterId === 'filterGranularidad' && availableFilters.includes('granularidad')) show = true;
                    if (filterId === 'filterCausasPrincipales' && availableFilters.includes('causasPrincipales')) show = true;
                    if (filterId === 'filterTipoComparativa' && availableFilters.includes('tipoComparativa')) show = true;
                    element.style.display = show ? 'block' : 'none';
                }
            });

            // Mostrar/ocultar selector Top según el tipo de gráfica
            const filterTopElement = document.getElementById('filterTop');
            if (filterTopElement) {
                filterTopElement.style.display = chartTypesWithTopSelector.includes(chartType) ? 'flex' : 'none';
            }

            updateChartTypeOptions(chartType);
            updateDataLabelOptions(chartType);
            renderChartLimitButtons(chartType);
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

        function updateChartTypeOptions(chartType) {
            const selector = document.getElementById('chartTypeSelector');
            if (!selector) return;
            const availableTypes = chartTypeOptions[chartType] || ['bar'];
            const currentValue = selector.value;
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

        function renderColorPalettePreview(paletteName) {
            const picker = document.getElementById('colorPalettePicker');
            if (!picker) return;

            const paletteKeys = Object.keys(colorPalettes);

            picker.innerHTML = paletteKeys.map(key => {
                const palette = colorPalettes[key] || [];
                const label = colorPaletteLabels[key] || key;
                const activeClass = key === paletteName ? 'border-[#611132] bg-[#f8f2f5] ring-1 ring-[#611132]' : 'border-gray-200 bg-white hover:bg-gray-50';
                const swatches = palette.slice(0, 4);

                return `
                    <button type="button" class="palette-chip w-full rounded-lg border px-2 py-2 text-left transition-all duration-200 ${activeClass}" data-palette="${key}" aria-pressed="${key === paletteName}">
                        <div class="flex items-center gap-1 mb-1">
                            ${swatches.map(color => `<span class="h-3.5 w-3.5 rounded-sm border border-white shadow-sm" style="background:${color};" title="${color}"></span>`).join('')}
                        </div>
                        <div class="text-[11px] font-semibold font-lora text-[#404041] truncate">${label}</div>
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
                barHorizontal: 'Horiz.',
                pie: 'Pastel',
                doughnut: 'Rosquilla',
                line: 'Línea',
                area: 'Área'
            };

            container.className = isSingleType
                ? 'flex items-start justify-start gap-2 w-fit max-w-none'
                : 'grid grid-flow-col auto-cols-fr gap-2 w-full max-w-[26rem]';

            container.innerHTML = availableTypes.map(type => {
                const active = type === currentValue;
                return `
                    <button type="button" class="visual-option-btn visual-option-card ${isSingleType ? 'is-compact-single' : ''} ${active ? 'active' : ''}" data-target="chartTypeSelector" data-value="${type}" aria-pressed="${active}">
                        <i class="fas ${chartTypeIcons[type] || 'fa-chart-simple'} text-xl"></i>
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
            const options = [
                { value: 'value', label: 'Valores' },
                { value: 'percent', label: 'Porc.' },
                { value: 'both', label: 'Ambos' }
            ];

            container.innerHTML = options.map(option => {
                const isDisabled = chartType === 'tendencias' && (option.value === 'percent' || option.value === 'both');
                const active = option.value === currentValue;
                return `
                    <button type="button" class="visual-option-btn visual-option-card ${active ? 'active' : ''}" data-target="datalabelMode" data-value="${option.value}" ${isDisabled ? 'disabled' : ''} aria-pressed="${active}">
                        <i class="fas ${dataLabelIcons[option.value] || 'fa-circle'} text-xl"></i>
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
            const options = [
                { value: 'all', label: chartLimitLabels.all },
                { value: '5', label: chartLimitLabels[5] },
                { value: '10', label: chartLimitLabels[10] },
                { value: '15', label: chartLimitLabels[15] }
            ];

            container.innerHTML = options.map(option => {
                const active = option.value === currentValue;
                return `
                    <button type="button" class="visual-option-btn visual-option-card ${active ? 'active' : ''}" data-target="chartLimit" data-value="${option.value}" aria-pressed="${active}">
                        <span class="visual-limit-badge ${option.value === 'all' ? 'is-all' : ''}">${option.value === 'all' ? '∞' : option.value}</span>
                        <span class="visual-option-label">${option.label}</span>
                    </button>
                `;
            }).join('');

            wrapper.style.display = chartTypesWithTopSelector.includes(chartType) ? 'flex' : 'none';
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
            } else if (filterType === 'tipoMunicipio') {
                document.getElementById('tipoMunicipioFilter').value = 'defuncion';
            }
            collectFilters();
            updateActiveFiltersDisplay();
            updateChart();
        }

        function loadChart(chartType) {
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
            fetch(`<?php echo e(route('api.chart.data')); ?>/` + chartType + '?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showErrorMessage(data.message || 'Error al obtener datos');
                    } else {
                        renderChart(data);
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
            const loadingMessage = document.getElementById('loadingMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (loadingMessage) loadingMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
            if (chartContainer) chartContainer.style.visibility = 'visible';
            const axisFontSize = 14;
            const valueLabelFontSize = 13;
            const legendFontSize = 15;
            const verticalBarGrid = {
                left: '3%',
                right: '4%',
                top: '12%',
                bottom: '12%',
                containLabel: true
            };
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
                if (isPieLikeChart) {
                    chartWrapper.style.height = '520px';
                    // Mostrar el chart en modo compacto: solo espacio para la gráfica + leyenda
                    chartWrapper.style.display = 'flex';
                    chartWrapper.style.justifyContent = 'center';
                    chartWrapper.style.alignItems = 'center';
                } else {
                    chartWrapper.style.display = '';
                    chartWrapper.style.justifyContent = '';
                    chartWrapper.style.alignItems = '';
                    chartWrapper.style.height = '';
                    chartWrapper.style.minHeight = '';
                    // Reset chart container width when not using compact pie layout
                    if (chartContainer) chartContainer.style.width = '';
                }
            }

            // Recrear instancia limpia
            currentEchartsInstance = echarts.init(chartContainer);

            let chartTitle = currentChartType === 'comparativa' 
                ? comparativaLabels[activeFilters.tipoComparativa] || chartTitles[currentChartType]
                : (chartTitles[currentChartType] || 'Gráfica');
            
            // Agregar tipo de municipio en el título si aplica
            if (currentChartType === 'municipios' && activeFilters.tipoMunicipio) {
                const tipoLabel = activeFilters.tipoMunicipio === 'residencia' ? 'Residencia' : 'Defunción';
                chartTitle += ` (${tipoLabel})`;
            }
            
            document.getElementById('chartTitle').textContent = chartTitle;
            const totalStr = (data.total || 0).toLocaleString();
            const totalEl = document.getElementById('totalRecords');
            if (totalEl) totalEl.textContent = totalStr;
            const badgeEl = document.getElementById('chartTotalValue');
            if (badgeEl) badgeEl.textContent = totalStr;

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
                            showErrorMessage('No hay datos para los filtros seleccionados');
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
                            showErrorMessage('No hay datos para los filtros seleccionados');
                            return;
                        }
                        labels = fLabels;
                        values = fValues;
                    }
                }
            } catch (e) {
                console.warn('Filter zeros failed', e);
            }

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
                    grid: verticalBarGrid,
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
                // Filtrar slices con valor 0 para evitar mostrar muchos 0 alrededor del pastel
                const filteredPieData = pieData.filter(d => Number(d.value || 0) !== 0);
                if (filteredPieData.length === 0) {
                    showErrorMessage('No hay datos para los filtros seleccionados');
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
                    if (chartWrapper) {
                        containerHeightAdjusted = Math.max(560, pieDiameterPx + 120);
                        chartWrapper.style.height = containerHeightAdjusted + 'px';
                    }
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
                                if (chartConfig.dataLabelMode === 'value') {
                                    return `{value|${formatNumber(params.value)}}`;
                                } else if (chartConfig.dataLabelMode === 'percent') {
                                    return `{percent|${params.percent}%}`;
                                } else if (chartConfig.dataLabelMode === 'both') {
                                    return `{value|${formatNumber(params.value)}} {normal|(${params.percent}%)}`;
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
                let categoryAxisFontSize = ['edades', 'genero'].includes(currentChartType)
                    ? 16
                    : (currentChartType === 'jurisdicciones' ? 12 : axisFontSize);

                if (['municipios', 'jurisdicciones'].includes(currentChartType)) {
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
                            right: '7%',
                            bottom: '3%',
                            top: '10%',
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
                        axisLabel: { 
                            interval: 0, 
                            fontSize: categoryAxisFontSize, 
                            color: '#404041' 
                        },
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
                    requestAnimationFrame(() => {
                        if (currentEchartsInstance && typeof currentEchartsInstance.resize === 'function') {
                            currentEchartsInstance.resize();
                        }
                    });
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
            safeSetValue('chartLimit', 'all');
            chartConfig.limit = null;
            
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

            renderChartTypeButtons(currentChartType);
            renderDataLabelButtons(currentChartType);
            renderChartLimitButtons(currentChartType);
            
            updateActiveFiltersDisplay();
            if (!suppressUpdate) updateChart();
        }

        function showLoadingMessage() {
            const loadingMessage = document.getElementById('loadingMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (loadingMessage) loadingMessage.style.display = 'flex';
            if (errorMessage) errorMessage.style.display = 'none';
            const chartEl = document.getElementById('mainChart');
            if (chartEl) chartEl.style.visibility = 'visible';
        }

        function hideLoadingMessage() {
            const loadingMessage = document.getElementById('loadingMessage');
            if (loadingMessage) loadingMessage.style.display = 'none';
        }

        function showErrorMessage(message) {
            document.getElementById('errorText').textContent = message;
            const errorMessage = document.getElementById('errorMessage');
            const loadingMessage = document.getElementById('loadingMessage');
            if (errorMessage) errorMessage.style.display = 'flex';
            if (loadingMessage) loadingMessage.style.display = 'none';
            const chartEl = document.getElementById('mainChart');
            if (chartEl) chartEl.style.visibility = 'hidden';
        }
    </script>

    <style>
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.principal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Proyectos Laravel\sistema-sec-tam\resources\views/estadisticas/graficas.blade.php ENDPATH**/ ?>