@extends('layouts.principal')
@section('title', 'Registros Fallidos de Importación')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <main class="users-management-page statistics-failed-imports-page px-4 sm:px-6 lg:px-10 pt-5 lg:pt-7 pb-8 lg:pb-10">
        <x-ui.page-header class="users-management-header statistics-failed-imports-header" title="Importaciones fallidas" description="Revise, corrija o descarte los registros que no pudieron guardarse desde {{ $importFileName }}.">
            <x-slot:actions><a href="{{ route('statistic.import-history-view') }}" class="users-page-create-btn"><i class="fas fa-arrow-left" aria-hidden="true"></i>Volver al historial</a></x-slot:actions>
        </x-ui.page-header>

        <section class="app-table-card users-table-card failed-imports-card" aria-label="Registros fallidos de la importación">
            <div class="app-table-toolbar flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                <section class="users-filter-card failed-imports-controls" aria-label="Controles de registros fallidos">
                    <div class="users-filter-form">
                        <div class="users-filter-topbar">
                            <div class="failed-imports-file-context"><span class="failed-imports-file-icon"><i class="far fa-file-excel" aria-hidden="true"></i></span><div><strong>{{ $importFileName }}</strong><small>Registros pendientes de revisión</small></div></div>
                            <div class="users-filter-search"><i class="fas fa-search" aria-hidden="true"></i><input id="search-failed-imports" type="search" placeholder="Buscar registros fallidos..." aria-label="Buscar registros fallidos" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" enterkeyhint="search" aria-busy="false"><span class="users-search-progress" aria-hidden="true"></span><button id="clear-failed-imports-search" type="button" class="hidden" title="Limpiar búsqueda" aria-label="Limpiar búsqueda"><i class="fas fa-times" aria-hidden="true"></i></button></div>
                            <div class="app-table-page-size users-filter-page-size"><select id="per-page-failed-imports" class="users-native-page-size" aria-hidden="true" tabindex="-1">@foreach([10,25,50,100] as $size)<option value="{{ $size }}" @selected($size === 10)>{{ $size }}</option>@endforeach</select><div class="users-page-size-dropdown"><button id="per-page-failed-imports-button" type="button" class="users-page-size-button" aria-haspopup="listbox" aria-expanded="false"><span>Mostrar</span><strong id="per-page-failed-imports-label">10</strong><i class="fas fa-list-ul users-page-size-icon" aria-hidden="true"></i></button><div id="per-page-failed-imports-menu" class="users-page-size-menu hidden" role="listbox" aria-labelledby="per-page-failed-imports-button">@foreach([10,25,50,100] as $size)<button type="button" role="option" class="users-page-size-option {{ $size === 10 ? 'is-active' : '' }}" data-value="{{ $size }}">{{ $size }}</button>@endforeach</div></div></div>
                        </div>
                    </div>
                </section>
            </div>
        <!-- HEADER CON TÍTULO Y BOTONES -->
        <div class="failed-imports-legacy-header flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Registros Fallidos - "{{ $importFileName }}"</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    Revise y corrija los registros que fallaron durante la importación para reintentar su guardado.
                </p>
            </div>

            <a href="{{ route('statistic.import-history-view') }}" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto">
                <i class="fas fa-arrow-left text-xs"></i>
                Volver
            </a>
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="failed-imports-state" role="status" aria-label="Cargando registros fallidos">
            <span class="sr-only">Cargando registros fallidos</span>
            <div class="failed-imports-loading-grid" aria-hidden="true">
                @for ($card = 0; $card < 2; $card++)
                    <article class="failed-imports-loading-card">
                        <div class="failed-imports-skeleton-alert">
                            <span class="failed-imports-skeleton-circle"></span>
                            <div><span class="failed-imports-skeleton-bar is-short"></span><span class="failed-imports-skeleton-bar is-message"></span></div>
                        </div>
                        <section class="failed-imports-skeleton-section">
                            <span class="failed-imports-skeleton-heading"></span>
                            <div class="failed-imports-skeleton-fields is-three-columns">
                                @for ($field = 0; $field < 6; $field++)
                                    <div><span class="failed-imports-skeleton-label"></span><span class="failed-imports-skeleton-bar"></span></div>
                                @endfor
                            </div>
                        </section>
                        <section class="failed-imports-skeleton-section">
                            <span class="failed-imports-skeleton-heading"></span>
                            <div class="failed-imports-skeleton-fields is-two-columns">
                                @for ($field = 0; $field < 4; $field++)
                                    <div><span class="failed-imports-skeleton-label"></span><span class="failed-imports-skeleton-bar"></span></div>
                                @endfor
                            </div>
                        </section>
                        <section class="failed-imports-skeleton-section">
                            <span class="failed-imports-skeleton-heading"></span>
                            <div class="failed-imports-skeleton-fields is-two-columns">
                                @for ($field = 0; $field < 2; $field++)
                                    <div><span class="failed-imports-skeleton-label"></span><span class="failed-imports-skeleton-bar"></span></div>
                                @endfor
                            </div>
                        </section>
                        <div class="failed-imports-skeleton-actions"><span></span><span></span></div>
                    </article>
                @endfor
            </div>
        </div>

        <!-- Records Container -->
        <div id="records-container" class="hidden space-y-6">
            <!-- Records List (with empty state) -->
            <div id="records-list" class="failed-imports-list">
                <!-- Empty State (shown when no records) -->
                <div id="empty-state" class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4"><i class="fas fa-check" aria-hidden="true"></i></div>
                    <p class="text-lg font-lora text-gray-600">No quedan registros por revisar</p>
                    <p class="text-sm text-gray-500 font-lora mt-2">Todos los registros fallidos de esta importación ya fueron atendidos.</p>
                </div>
            </div>

        </div>
        <nav class="users-table-footer failed-imports-footer flex flex-row flex-wrap items-center justify-between gap-3 p-4" aria-label="Paginación de registros fallidos"><span id="dt-info" class="text-sm font-normal text-gray-500 flex-1 min-w-0 is-loading">Preparando tabla</span><div id="dt-pagination" class="flex-none"></div></nav>
        </section>
    </main>

@endsection

<!-- Template for failed record card -->
<template id="record-template">
    <article class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 shadow-md record-card" data-record-id="">
        
        <!-- Error Alert -->
        <div class="failed-record-error bg-red-50 border-l-4 border-red-600 p-4 mb-6 rounded-r-lg" role="alert">
            <span class="failed-record-error-icon"><i class="fas fa-exclamation" aria-hidden="true"></i></span>
            <div><p class="text-red-700 font-semibold text-sm">Requiere corrección</p><p class="text-red-600 text-sm mt-1 error-message"></p></div>
        </div>

        <!-- Validation Errors (hidden by default) -->
        <div class="validation-errors hidden bg-red-50 border border-red-300 rounded-lg p-4 mb-6">
            <p class="text-red-700 font-semibold text-sm mb-2">Errores de validación:</p>
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1"></ul>
        </div>

        <!-- VIEW MODE -->
        <div class="view-mode">
            <form class="correction-form">
                <!-- Sección 1: Información del fallecido -->
                <div class="mb-6 lg:mb-8">
                    <div class="flex items-center mb-4">
                        <ion-icon name="person-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información del fallecido</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-4 items-start">
                        <!-- Row 1: Folio | Nombre | Ap. paterno -->
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Folio <span class="text-red-600">*</span></label>
                            <input name="folio" type="text" value="" disabled
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora" 
                                placeholder="Ej: 230787888">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) <span class="text-red-600">*</span></label>
                            <input name="nombre" type="text" value="" disabled
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora" 
                                   placeholder="Ej: Juan Diego">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno <span class="text-red-600">*</span></label>
                            <input name="primerapellido" type="text" value="" disabled
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora" 
                                   placeholder="Ej: Nava">
                        </div>

                        <!-- Row 2: Apellido materno | Sexo | Edad -->
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno</label>
                            <input name="segundoapellido" type="text" value="" disabled
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora" 
                                   placeholder="Ej: Reyes">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Sexo <span class="text-red-600">*</span></label>
                            <select name="sexod" disabled class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora">
                                <option value="">Seleccione una opción</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Edad <span class="text-red-600">*</span></label>
                            <div class="flex gap-2">
                                <input name="edad_valor" type="number" min="0" max="150" value="" disabled
                                       class="w-1/2 px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora"
                                       placeholder="Ej: 34">
                                <select name="edad_unidad" disabled class="w-1/2 px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora">
                                    <option value="">Unidad</option>
                                    <option value="anos">Años</option>
                                    <option value="meses">Meses</option>
                                    <option value="dias">Días</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Línea separadora -->
                <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

                <!-- Sección 2: Ubicación -->
                <div class="mb-6 lg:mb-8">
                    <div class="flex items-center mb-4">
                        <ion-icon name="location-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Ubicación</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="failed-location-grid grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                        <!-- Left column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de residencia <span class="text-red-600">*</span></label>
                                <select name="municipioresidenciad" disabled
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de defunción <span class="text-red-600">*</span></label>
                                <select name="municipiodefunciond" disabled
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Distrito de residencia</label>
                                <input name="distrito" type="text" value="" readonly
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-lora" 
                                       placeholder="Distrito">
                            </div>

                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Distrito de defunción</label>
                                <select name="jurisdicciondefunciond" disabled
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                    <option value="">Seleccione un distrito</option>
                                </select>
                            </div>
                        </div>

                        <div class="failed-location-place">
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar específico <span class="text-red-600">*</span></label>
                            <select name="sitiodefunciond" required disabled
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                <option value="">Seleccione lugar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Línea separadora -->
                <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

                <!-- Sección 3: Información de la defunción -->
                <div class="mb-6 lg:mb-8">
                    <div class="flex items-center mb-4">
                        <ion-icon name="medical-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información de la defunción</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                        <!-- Left column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Causa de la defunción <span class="text-red-600">*</span></label>
                                <select name="sheet" disabled
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-700 font-lora tomselect-select">
                                    <option value="">Seleccione una causa</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de defunción <span class="text-red-600">*</span></label>
                                <input name="fechadefuncion" type="date" value="" disabled
                                       class="fecha-input w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg text-gray-700 font-lora" 
                                       placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Línea separadora para botones -->
                <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

                <!-- Action Buttons - View Mode -->
                <div class="action-buttons-view flex flex-col sm:flex-row justify-end gap-3 lg:gap-4">
                    <button type="button" class="btn-discard border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center justify-center gap-2 whitespace-nowrap">
                        <i class="fas fa-trash text-xs"></i> Descartar
                    </button>
                    <button type="button" class="btn-toggle-edit bg-[#611132] text-white px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2 whitespace-nowrap">
                        <i class="fas fa-edit text-xs"></i> Editar
                    </button>
                </div>

                <!-- Action Buttons - Edit Mode -->
                <div class="action-buttons-edit hidden flex flex-col sm:flex-row justify-end gap-3 lg:gap-4">
                    <button type="button" class="btn-cancel-edit border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center justify-center gap-2 whitespace-nowrap">
                        <i class="fas fa-times text-xs"></i> Cancelar
                    </button>
                    <button type="button" class="btn-save-correction border border-[#404041] text-[#404041] px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-gray-50 transition-all duration-300 font-lora flex items-center justify-center gap-2 whitespace-nowrap">
                        <i class="fas fa-save text-xs"></i> Guardar
                    </button>
                    <button type="button" class="btn-retry bg-[#611132] text-white px-4 lg:px-6 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2 whitespace-nowrap">
                        <i class="fas fa-check text-xs"></i> Importar
                    </button>
                </div>
            </form>
        </div>

        <!-- EDIT MODE -->
        <div class="edit-mode hidden">
            <form class="correction-form">
                <!-- Sección 1: Información del fallecido -->
                <div class="mb-6 lg:mb-8">
                    <div class="flex items-center mb-4">
                        <ion-icon name="person-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información del fallecido</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-4 items-start">
                        <!-- Row 1: Folio | Nombre | Ap. paterno -->
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Folio <span class="text-red-600">*</span></label>
                            <input name="folio" type="text" value="" required
                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                placeholder="Ej: 230787888">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) <span class="text-red-600">*</span></label>
                            <input name="nombre" type="text" value="" required minlength="2" maxlength="191"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Juan Diego">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno <span class="text-red-600">*</span></label>
                            <input name="primerapellido" type="text" value="" required minlength="2" maxlength="191"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Nava">
                        </div>

                        <!-- Row 2: Apellido materno | Sexo | Edad (valor + unidad) -->
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno</label>
                            <input name="segundoapellido" type="text" value="" minlength="2" maxlength="191"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Reyes">
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Sexo <span class="text-red-600">*</span></label>
                            <select name="sexod" required class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione una opción</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Edad <span class="text-red-600">*</span></label>
                            <div class="flex gap-2">
                                <input name="edad_valor" type="number" min="0" max="150" value="" required
                                       class="w-1/2 px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                       placeholder="Ej: 34">
                                <select name="edad_unidad" required class="w-1/2 px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                    <option value="">Unidad</option>
                                    <option value="anos">Años</option>
                                    <option value="meses">Meses</option>
                                    <option value="dias">Días</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Línea separadora -->
                <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

                <!-- Sección 2: Ubicación -->
                <div class="mb-6 lg:mb-8">
                    <div class="flex items-center mb-4">
                        <ion-icon name="location-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Ubicación</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="failed-location-grid grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                        <!-- Left column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de residencia <span class="text-red-600">*</span></label>
                                <select name="municipioresidenciad" required
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select">
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de defunción <span class="text-red-600">*</span></label>
                                <select name="municipiodefunciond" required
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select">
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Distrito de residencia</label>
                                <input name="distrito" type="text" value=""
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                       placeholder="Distrito">
                            </div>

                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Distrito de defunción <span class="text-red-600">*</span></label>
                                <select name="jurisdicciondefunciond" required
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select">
                                    <option value="">Seleccione un distrito</option>
                                </select>
                            </div>
                        </div>

                        <div class="failed-location-place">
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar específico <span class="text-red-600">*</span></label>
                            <select name="sitiodefunciond" required
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select">
                                <option value="">Seleccione lugar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Línea separadora -->
                <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

                <!-- Sección 3: Información de la defunción -->
                <div class="mb-6 lg:mb-8">
                    <div class="flex items-center mb-4">
                        <ion-icon name="medical-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                        <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información de la defunción</h2>
                        <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                        <!-- Left column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Causa de la defunción <span class="text-red-600">*</span></label>
                                <select name="sheet" required
                                       class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select">
                                    <option value="">Seleccione una causa</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right column -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de defunción <span class="text-red-600">*</span></label>
                                <input name="fechadefuncion" type="date" value="" required
                                       class="fecha-input w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Línea separadora para botones -->
                <div class="h-px bg-gray-300 my-4 lg:my-6"></div>


            </form>
        </div>
    </article>
</template>

<!-- Incluir Ionicons -->

<!-- Font Awesome -->

<!-- Tom Select CDN (single-select, styled to match inputs) -->

@push('scripts')
<style>
    /* Tom Select styling - match input styles */
    .ts-wrapper {
        border: none !important;
        padding: 0 !important;
        background: transparent !important;
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
    }

    select.tomselect-select::-ms-expand { display: none !important; }
    select.tomselect-select { background-image: none !important; }

    .ts-wrapper { display: block; width: 100%; }

    .ts-control {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 8px 12px !important;
        background: #ffffff !important;
        font-family: inherit;
        font-size: 0.875rem;
        line-height: 1.25rem !important;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        position: relative;
        box-sizing: border-box;
        margin: 0 !important;
        box-shadow: none !important;
        height: auto !important;
        min-height: 36px !important;
        transition: all 0.2s ease;
    }

    .ts-control:focus-within {
        border-color: #404041 !important;
        box-shadow: 0 0 0 2px rgba(64, 64, 65, 0.1) !important;
        outline: 2px solid transparent !important;
        outline-offset: 2px !important;
    }

    .ts-control .item, .ts-control input {
        padding: 0 !important;
        margin: 0 !important;
        height: auto !important;
        line-height: 1.25rem !important;
        font-size: inherit;
        font-family: inherit;
    }

    .ts-control .dropdown-toggle,
    .ts-control .ts-dropdown-toggle,
    .ts-control .dropdown_toggle,
    .ts-control .ts-clear {
        display: none !important;
    }

    .ts-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        max-height: 240px;
        overflow: auto;
    }

    .ts-dropdown .ts-option {
        padding: 0.5rem 0.75rem;
    }

    .tomselect-caret {
        display: none !important;
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        pointer-events: none;
        font-size: 0.9rem;
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

    /* Disabled Tom Select styling - VERY STRONG */
    select.tomselect-select:disabled ~ .ts-wrapper,
    select.tomselect-select:disabled ~ .ts-wrapper .ts-control,
    .ts-wrapper.disabled,
    .ts-wrapper.disabled .ts-control {
        background-color: #f3f4f6 !important;
        color: #6b7280 !important;
        opacity: 1 !important;
        cursor: not-allowed !important;
    }

    .view-mode select.tomselect-select:disabled {
        background-color: #f3f4f6 !important;
    }

    .view-mode .ts-wrapper .ts-control {
        background-color: #f3f4f6 !important;
        color: #6b7280 !important;
    }

    .view-mode { display: block; }
    .edit-mode { display: none; }
    
    .record-card.in-edit .view-mode { display: none; }
    .record-card.in-edit .edit-mode { display: block; }
    
    .record-card {
        transition: all 0.3s ease;
    }
    
    .record-card.in-edit {
        border-color: #611132 !important;
        background-color: #fef2f6 !important;
    }

    /* Make native date input visually match other inputs/selects in the form */
    input[type="date"] {
        padding: 8px 12px !important; /* match px-3 py-2 */
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        background: #ffffff !important;
        font-family: inherit;
        font-size: 0.875rem;
        line-height: 1.25rem;
        box-shadow: none !important;
        height: auto !important;
        min-height: 36px !important;
    }
    /* Disabled date input - show gray background */
    input[type="date"]:disabled,
    input.fecha-input:disabled {
        background: #f3f4f6 !important; /* gray-100 */
        color: #6b7280 !important; /* gray-500 - match other disabled inputs */
        border-color: #d1d5db !important; /* Keep gray border */
        cursor: not-allowed !important;
        opacity: 1 !important;
    }
    /* Enabled date input - white background */
    input[type="date"]:not(:disabled),
    input.fecha-input:not(:disabled) {
        background: #ffffff !important;
        color: #000000 !important;
    }
    /* Make the calendar icon gray when disabled */
    input.fecha-input:disabled::-webkit-calendar-picker-indicator {
        filter: opacity(0.5) grayscale(100%);
    }
    /* Slightly tone down the calendar icon so it blends with your selects */
    input[type="date"]::-webkit-calendar-picker-indicator {
        opacity: 0.7;
        transform: scale(0.95);
    }
    input[type="date"]::-webkit-inner-spin-button,
    input[type="date"]::-webkit-clear-button {
        display: none;
    }

    /* Super aggressive date input styling for view mode */
    .view-mode input[type="date"],
    .view-mode input[type="date"]:disabled {
        background-color: #f3f4f6 !important;
        color: #6b7280 !important;
        border: 1px solid #d1d5db !important;
        cursor: not-allowed !important;
    }

    /* Readonly input styling (e.g., jurisdiction field) */
    input[readonly] {
        background: #f3f4f6 !important; /* gray-100 */
        color: #6b7280 !important; /* gray-500 */
        border: 1px solid #d1d5db !important;
        cursor: not-allowed !important;
        opacity: 1 !important;
    }

    input:disabled,
    select:disabled {
        background: #f3f4f6 !important;
        color: #6b7280 !important;
        border: 1px solid #d1d5db !important;
        cursor: not-allowed !important;
        opacity: 1 !important;
    }

    /* Override to ensure readonly/disabled fields are always gray */
    .correction-form input:disabled,
    .correction-form input[readonly],
    .correction-form select:disabled {
        background-color: #f3f4f6 !important;
        color: #6b7280 !important;
    }
</style>

<script>
// Helper function to parse dates in various formats and convert to YYYY-MM-DD for HTML date input
function parseDateForInput(dateStr) {
    if (!dateStr) return '';
    
    let date = null;
    
    // Try DD/MM/YYYY format (common in imports)
    if (dateStr.includes('/')) {
        const parts = dateStr.split('/');
        if (parts.length === 3) {
            // Try MM/DD/YYYY first
            if (parseInt(parts[0]) <= 12) {
                date = new Date(parseInt(parts[2]), parseInt(parts[0]) - 1, parseInt(parts[1]));
            } else {
                // Must be DD/MM/YYYY
                date = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
            }
        }
    } else if (dateStr.includes('-')) {
        // Try YYYY-MM-DD or DD-MM-YYYY
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            if (parseInt(parts[0]) > 31) {
                // YYYY-MM-DD
                date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            } else {
                // DD-MM-YYYY
                date = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
            }
        }
    }
    
    // If parsing failed, try generic Date constructor
    if (!date || isNaN(date.getTime())) {
        date = new Date(dateStr);
    }
    
    // If still invalid, return empty
    if (!date || isNaN(date.getTime())) {
        return '';
    }
    
    // Format as YYYY-MM-DD
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Build a map municipality name -> jurisdiction name (for lookup)
const muniToJurName = @json(
    $municipalities->mapWithKeys(function($m) use ($jurisdictions) {
        $jur = $jurisdictions->find($m->district_id);
        return [$m->name => $jur ? $jur->name : ''];
    })
);

function districtNameForMunicipality(municipalityName) {
    const target = String(municipalityName || '').trim().toUpperCase();
    if (!target) return '';

    for (const [municipality, district] of Object.entries(muniToJurName)) {
        const candidate = municipality.trim().toUpperCase();
        if (candidate === target || candidate.includes(target) || target.includes(candidate)) {
            return district;
        }
    }

    return '';
}

// Build data for Tom Select
const municipalitiesData = @json($municipalities);
const districtsData = @json($districts);
const locationsData = @json($locations);
const causesData = @json($causes);

// Function to initialize Tom Select for a specific select element
function initializeTomSelect(selector, data, labelField = 'name', valueField = 'id') {
    const elements = document.querySelectorAll(selector);
    elements.forEach(el => {
        if (el.tomselect) return; // Already initialized
        
        new TomSelect(el, {
            options: data.map(item => ({
                [valueField]: item[valueField],
                [labelField]: item[labelField],
                text: item[labelField]
            })),
            items: el.value ? [el.value] : [],
            valueField: valueField,
            labelField: labelField,
            searchField: labelField,
            create: false,
            placeholder: el.getAttribute('data-placeholder') || 'Seleccione una opción',
            maxItems: 1,
            closeAfterSelect: true
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const importId = {{ $importId }};
    let currentPage = 1;
    let isInitialLoad = true;
    let failedImportsPerPage = 10;
    let failedImportsSearch = '';

    function notifyFailedImport(message, type = 'success', duration = 3000) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type, duration);
            return;
        }

        const logger = type === 'error' ? console.error : console.log;
        logger(message);
    }

    const failedSearchInput = document.getElementById('search-failed-imports');
    const failedSearchClear = document.getElementById('clear-failed-imports-search');
    const failedSearchControl = failedSearchInput?.closest('.users-filter-search');
    const failedPerPageSelect = document.getElementById('per-page-failed-imports');
    const failedPerPageButton = document.getElementById('per-page-failed-imports-button');
    const failedPerPageMenu = document.getElementById('per-page-failed-imports-menu');

    failedSearchInput?.setAttribute('aria-busy', 'false');
    const updateFailedSearchClear = () => failedSearchClear?.classList.toggle('hidden', !failedSearchInput?.value);
    const applyFailedSearch = () => {
        failedImportsSearch = failedSearchInput?.value.trim() || '';
        failedSearchControl?.classList.add('is-searching');
        failedSearchInput?.setAttribute('aria-busy', 'true');
        isInitialLoad = false;
        loadFailedRecords(1);
    };

    failedSearchInput?.addEventListener('input', updateFailedSearchClear);
    failedSearchInput?.addEventListener('keydown', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            applyFailedSearch();
        }
    });
    failedSearchClear?.addEventListener('click', () => {
        failedSearchInput.value = '';
        updateFailedSearchClear();
        applyFailedSearch();
    });

    failedPerPageButton?.addEventListener('click', () => {
        const open = failedPerPageMenu?.classList.contains('hidden');
        failedPerPageMenu?.classList.toggle('hidden', !open);
        failedPerPageButton.setAttribute('aria-expanded', String(open));
    });
    failedPerPageMenu?.querySelectorAll('[data-value]').forEach(option => option.addEventListener('click', () => {
        failedPerPageSelect.value = option.dataset.value;
        document.getElementById('per-page-failed-imports-label').textContent = option.dataset.value;
        failedPerPageMenu.querySelectorAll('[data-value]').forEach(item => item.classList.toggle('is-active', item === option));
        failedPerPageMenu.classList.add('hidden');
        failedPerPageButton.setAttribute('aria-expanded', 'false');
        failedPerPageSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }));
    failedPerPageSelect?.addEventListener('change', event => {
        failedImportsPerPage = Number(event.target.value) || 10;
        isInitialLoad = false;
        loadFailedRecords(1);
    });
    document.addEventListener('click', event => {
        if (!event.target.closest('.users-page-size-dropdown')) {
            failedPerPageMenu?.classList.add('hidden');
            failedPerPageButton?.setAttribute('aria-expanded', 'false');
        }
    });

    function loadFailedRecords(page = 1) {
        const loadingState = document.getElementById('loading-state');
        const recordsContainer = document.getElementById('records-container');
        const recordsList = document.getElementById('records-list');
        
        // Only show loading state on initial load
        if (isInitialLoad) {
            loadingState.classList.remove('hidden');
            recordsContainer.classList.add('hidden');
        } else {
            // On pagination, add fade out transition
            recordsList.style.opacity = '0';
            recordsList.style.transition = 'opacity 0.2s ease-out';
        }

        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const query = new URLSearchParams({ page, per_page: failedImportsPerPage });
        if (failedImportsSearch) query.set('search', failedImportsSearch);
        fetch(`/api/estadisticas/importaciones/${importId}/registros-fallidos?${query.toString()}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            credentials: 'same-origin' // Include cookies for authentication
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                loadingState.classList.add('hidden');
                recordsContainer.classList.remove('hidden');
                
                // On initial load, ensure opacity is 1 immediately
                if (isInitialLoad) {
                    recordsList.style.transition = 'none';
                    recordsList.style.opacity = '1';
                }
                isInitialLoad = false;

                // Check if response structure is valid
                if (!data) {
                    throw new Error('Empty response from API');
                }
                
                if (!data.ok) {
                    throw new Error(data.message || 'API returned error');
                }
                
                // Safely access nested data
                if (!data.data) {
                    document.getElementById('empty-state').classList.remove('hidden');
                    return;
                }
                // Check if this is a paginated response (Laravel pagination object)
                const records = Array.isArray(data.data) ? data.data : (data.data.data || []);
                
                if (records.length > 0) {
                    recordsList.innerHTML = '';
                    
                    renderRecords(records);
                    
                    // Fade in the records list on pagination
                    if (!isInitialLoad) {
                        setTimeout(() => {
                            recordsList.style.opacity = '1';
                        }, 10);
                    }
                    
                    // If it's a paginated response, use the pagination object; otherwise use the raw data
                    const paginationData = Array.isArray(data.data) ? null : data.data;
                    if (paginationData) {
                        renderFailedPaginationPilot(paginationData);
                    }
                    currentPage = page;
                } else {
                    // Show only the empty state
                    updateFailedPaginationInfo({ from: 0, to: 0, total: 0 });
                    document.getElementById('dt-pagination').replaceChildren();
                    recordsList.innerHTML = `
                        <div id="empty-state" class="col-span-full text-center py-12">
                            <div class="text-gray-400 mb-4"><i class="fas fa-check" aria-hidden="true"></i></div>
                            <p class="text-lg font-lora text-gray-600">No quedan registros por revisar</p>
                            <p class="text-sm text-gray-500 font-lora mt-2">Todos los registros fallidos de esta importación ya fueron atendidos.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading failed records:', error);
                notifyFailedImport('No se pudieron cargar los registros fallidos. Intenta nuevamente.', 'error');
                loadingState.classList.add('hidden');
                recordsContainer.classList.remove('hidden');
                isInitialLoad = false;
                recordsList.style.opacity = '1';
                updateFailedPaginationInfo({ from: 0, to: 0, total: 0 });
                document.getElementById('dt-pagination').replaceChildren();
                recordsList.innerHTML = `
                    <div class="failed-imports-load-error" role="alert">
                        <span><i class="fas fa-exclamation" aria-hidden="true"></i></span>
                        <div><p>No se pudieron cargar los registros</p><p>Intente nuevamente en unos momentos.</p></div>
                    </div>
                `;
            })
            .finally(() => {
                failedSearchControl?.classList.remove('is-searching');
                failedSearchInput?.setAttribute('aria-busy', 'false');
                updateFailedSearchClear();
            });
    }

    function findMatchingItemId(textValue, data) {
        if (!textValue || !data || data.length === 0) return '';
        
        const trimmed = String(textValue).trim();
        
        // Exact match first
        let item = data.find(d => String(d.name).trim() === trimmed);
        if (item) {
            console.log('✓ Exact match: "' + textValue + '" -> ID:', item.id);
            return String(item.id);
        }
        
        // Case-insensitive match
        const upper = trimmed.toUpperCase();
        item = data.find(d => String(d.name).trim().toUpperCase() === upper);
        if (item) {
            console.log('✓ Case-insensitive match: "' + textValue + '" -> ID:', item.id);
            return String(item.id);
        }
        
        // Partial match (for longer names with potential variations)
        item = data.find(d => {
            const dName = String(d.name).trim().toUpperCase();
            return dName.includes(upper) || upper.includes(dName);
        });
        if (item) {
            console.log('✓ Partial match: "' + textValue + '" -> ID:', item.id);
            return String(item.id);
        }
        
        // Debug: show what we have
        console.warn('✗ NO MATCH for "' + textValue + '". Available:', data.slice(0, 3).map(d => d.name).join(' | ') + '...');
        return '';
    }

    function initializeTomSelectField(el, data) {
        const name = el.getAttribute('name');
        // Use originalValue if available (the original text from server), otherwise originalText
        const textValue = el.dataset.originalValue || el.dataset.originalText || el.value || '';
        
        if (!data || data.length === 0) {
            console.log('No data available for', name);
            return;
        }
        
        console.log('Initializing Tom Select:', name, '- text value:', textValue);
        
        // Find matching ID
        let selectedId = findMatchingItemId(textValue, data);
        
        // Create Tom Select options
        const options = data.map(item => ({
            id: String(item.id),
            name: item.name,
            text: item.name
        }));

        // In review mode, preserve the source value even when it is precisely
        // the invalid option that caused the import to fail.
        if (!selectedId && el.disabled && textValue) {
            selectedId = '__source_value__';
            options.unshift({
                id: selectedId,
                name: textValue,
                text: textValue
            });
        }
        
        const config = {
            options: options,
            items: selectedId ? [selectedId] : [],
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            create: false,
            placeholder: 'Seleccione una opción',
            maxItems: 1,
            closeAfterSelect: true,
            disable: el.disabled
        };
        
        const ts = new TomSelect(el, config);
        
        // Ensure value is set after initialization
        if (selectedId) {
            ts.setValue(selectedId);
            // Update the data attribute with the mapped name for consistency
            const item = data.find(d => String(d.id) === selectedId);
            if (item) {
                el.dataset.originalText = item.name;
                console.log('Set originalText to:', item.name);
            }
        }
        
        // Function to apply/reapply styling
        const applyDisabledStyle = () => {
            const wrapper = el.closest('.ts-wrapper');
            if (wrapper) {
                const control = wrapper.querySelector('.ts-control');
                if (control) {
                    if (el.disabled) {
                        wrapper.classList.add('disabled');
                        control.classList.add('disabled');
                        control.style.setProperty('background-color', 'transparent', 'important');
                        control.style.setProperty('color', '#10233f', 'important');
                        control.style.setProperty('cursor', 'default', 'important');
                        control.style.setProperty('opacity', '1', 'important');
                        
                        // Also style the input inside
                        const input = control.querySelector('input');
                        if (input) {
                            input.style.setProperty('background-color', 'transparent', 'important');
                            input.style.setProperty('cursor', 'default', 'important');
                        }
                    } else {
                        wrapper.classList.remove('disabled');
                        control.classList.remove('disabled');
                        control.style.setProperty('background-color', '#ffffff', 'important');
                        control.style.setProperty('color', '#000000', 'important');
                        control.style.setProperty('cursor', 'pointer', 'important');
                        control.style.setProperty('opacity', '1', 'important');
                        
                        // Also style the input inside
                        const input = control.querySelector('input');
                        if (input) {
                            input.style.setProperty('background-color', 'transparent', 'important');
                            input.style.setProperty('cursor', 'pointer', 'important');
                        }
                    }
                }
            }
        };
        
        // Apply styling immediately after Tom Select initialization
        applyDisabledStyle();
        
        // Reapply styling after a longer delay to ensure Tom Select is fully rendered
        setTimeout(applyDisabledStyle, 100);
        
        // Keep reapplying every time disabled status changes
        const originalDisabled = el.disabled;
        const observer = new MutationObserver(() => {
            if (el.disabled !== originalDisabled) {
                applyDisabledStyle();
            }
        });
        observer.observe(el, { attributes: true, attributeFilter: ['disabled'] });
        
        return ts;
    }

    function renderRecords(records) {
        const container = document.getElementById('records-list');
        container.innerHTML = '';

        records.forEach(record => {
            const template = document.getElementById('record-template');
            const clone = template.content.cloneNode(true);
            
            const card = clone.querySelector('.record-card');
            card.dataset.recordId = record.id;

            clone.querySelector('.error-message').textContent = record.error_message;

            const originalData = record.original_row_data || {};
            const formData = record.corrected_data || originalData;
            
            // Debug: log what fields we have
            console.log('Record #' + record.id + ' fields:', Object.keys(originalData));
            
            // Populate all form fields
            const form = clone.querySelector('.correction-form');
            form.querySelectorAll('[name]').forEach(field => {
                const fieldName = field.getAttribute('name');
                let value = formData[fieldName];
                
                // Map alternate field names
                if (!value && fieldName === 'folio') {
                    value = formData.folio_gob || originalData.folio_gob;
                }
                if (!value && fieldName === 'primerapellido') {
                    value = formData.primerapellid || originalData.primerapellid;
                }
                if (!value && fieldName === 'segundoapellido') {
                    value = formData.segundoapellid || originalData.segundoapellid;
                }
                
                // Special handling for sexo field - convert HOMBRE/MUJER to M/F
                if (fieldName === 'sexod' && value) {
                    if (value === 'HOMBRE') value = 'M';
                    else if (value === 'MUJER') value = 'F';
                }
                
                // Special handling for fechadefuncion - convert to YYYY-MM-DD format for date input
                if (fieldName === 'fechadefuncion' && value) {
                    value = parseDateForInput(value);
                }
                
                // Special handling for edad - split valor and unidad
                if (fieldName === 'edad_valor' || fieldName === 'edad_unidad') {
                    const edadStr = originalData.edad || formData.edad || '';
                    if (edadStr) {
                        // Try to extract number and unit
                        const match = edadStr.match(/^(\d+)\s*(?:años|anos|years|a)?$/i);
                        if (match && fieldName === 'edad_valor') {
                            value = match[1];
                        } else if (fieldName === 'edad_unidad' && !value) {
                            value = 'anos'; // default to years
                        }
                    }
                }
                
                // Distrito: CALCULAR con base en el municipio de residencia
                if (fieldName === 'distrito') {
                    // Primero buscar si existe en los datos
                    value = formData.distrito || 
                            originalData.distrito ||
                            formData.jurisdiction ||
                            originalData.jurisdiction ||
                            formData.distrito_id ||
                            originalData.distrito_id ||
                            originalData.jurisdicccion ||
                            '';
                    
                    // Si no existe, calcularla basada en el municipio de residencia
                    if (!value) {
                        const muniResidencia = (formData.municipioresidenciad || originalData.municipioresidenciad || '').trim();
                        console.log('DEBUG: Buscando distrito para municipio:', JSON.stringify(muniResidencia));
                        
                        if (muniResidencia) {
                            // Helper para buscar el distrito
                            const findJurisdiction = (muni) => {
                                const muniUpper = muni.toUpperCase().trim();
                                
                                // 1. Búsqueda exacta
                                for (const [key, jur] of Object.entries(muniToJurName)) {
                                    if (key.trim() === muni) {
                                        console.log('✓ EXACT MATCH: "' + muni + '" -> "' + jur + '"');
                                        return jur;
                                    }
                                }
                                
                                // 2. Búsqueda case-insensitive
                                for (const [key, jur] of Object.entries(muniToJurName)) {
                                    if (key.toUpperCase().trim() === muniUpper) {
                                        console.log('✓ CASE-INSENSITIVE: "' + muni + '" -> "' + jur + '"');
                                        return jur;
                                    }
                                }
                                
                                // 3. Búsqueda parcial (si muni contiene key o key contiene muni)
                                for (const [key, jur] of Object.entries(muniToJurName)) {
                                    const keyUpper = key.toUpperCase().trim();
                                    if (muniUpper.includes(keyUpper) || keyUpper.includes(muniUpper)) {
                                        console.log('✓ PARTIAL MATCH: "' + muni + '" vs "' + key + '" -> "' + jur + '"');
                                        return jur;
                                    }
                                }
                                
                                console.warn('✗ NO MATCH for "' + muni + '". Need to search all ' + Object.keys(muniToJurName).length + ' municipalities');
                                console.warn('   Sample municipalities:', Object.keys(muniToJurName).slice(0, 10).join(', '));
                                return '';
                            };
                            
                            value = findJurisdiction(muniResidencia);
                            console.log('DEBUG: Resultado distrito:', JSON.stringify(value));
                        } else {
                            console.log('DEBUG: Municipio residencia vacío');
                        }
                    }
                }

                if (fieldName === 'jurisdicciondefunciond') {
                    const deathMunicipality = (formData.municipiodefunciond || originalData.municipiodefunciond || '').trim();
                    value = districtNameForMunicipality(deathMunicipality)
                        || formData.jurisdicciondefunciond
                        || originalData.jurisdicciondefunciond
                        || formData.distritodefunciond
                        || originalData.distritodefunciond
                        || '';
                }
                
                if (value !== undefined && value !== null && value !== '') {
                    field.value = value;
                    
                    // Para Tom Select fields, guardar el valor original ANTES de que Tom Select lo modifique
                    if (field.classList.contains('tomselect-select')) {
                        const fieldName = field.getAttribute('name');
                        let assignedData = [];
                        
                        // Determine which data to use based on field name
                        if (fieldName === 'municipioresidenciad' || fieldName === 'municipiodefunciond') {
                            assignedData = municipalitiesData;
                        } else if (fieldName === 'jurisdicciondefunciond') {
                            assignedData = districtsData;
                        } else if (fieldName === 'sitiodefunciond') {
                            assignedData = locationsData;
                        } else if (fieldName === 'sheet') {
                            assignedData = causesData;
                        }
                        
                        // Check if this value exists in the available data
                        let valueExists = false;
                        if (assignedData.length > 0) {
                            const valUpper = String(value).toUpperCase().trim();
                            valueExists = assignedData.some(item => 
                                String(item.name).toUpperCase().trim() === valUpper
                            );
                        }
                        
                        // If value doesn't exist in data, set to "OTRO" for municipios
                        if (!valueExists && (fieldName === 'municipioresidenciad' || fieldName === 'municipiodefunciond')) {
                            value = 'OTRO';
                            field.value = value;
                            console.log('Value "' + field.dataset.originalValue + '" not found in data, setting to OTRO');
                        }
                        
                        // Guardar en dataset para recuperar más tarde
                        field.dataset.originalText = value;
                        field.dataset.originalValue = value;
                    }
                }
            });

            // Set the maximum date for fechadefuncion input to today (client timezone)
            const deathDateInput = form.querySelector('input[name="fechadefuncion"]');
            if (deathDateInput) {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const todayString = `${year}-${month}-${day}`;
                deathDateInput.max = todayString;
            }

            // Don't save originalFormData yet - we'll do it after Tom Select initialization
            // This prevents saving IDs instead of the text values we need for restoration

            attachRecordListeners(clone, record.id);
            container.appendChild(clone);
        });

        // Initialize Tom Select for all select elements in the new records
        setTimeout(() => {
            const allSelects = container.querySelectorAll('select.tomselect-select');
            console.log('Tom Select: Found', allSelects.length, 'selects to initialize');
            allSelects.forEach((el) => {
                if (!el.tomselect) {
                    const name = el.getAttribute('name');
                    let data = [];
                    
                    // Determine which data to use based on the field name
                    if (name === 'municipioresidenciad' || name === 'municipiodefunciond') {
                        data = municipalitiesData;
                    } else if (name === 'jurisdicciondefunciond') {
                        data = districtsData;
                    } else if (name === 'sitiodefunciond') {
                        data = locationsData;
                    } else if (name === 'sheet') {
                        data = causesData;
                    }
                    
                    initializeTomSelectField(el, data);
                }
            });
            
            // NOW save originalFormData after Tom Select is initialized
            // Use originalValue (the original text from server) for Tom Select fields instead of the ID
            container.querySelectorAll('.record-card').forEach(card => {
                const form = card.querySelector('.correction-form');
                const originalFormData = {};
                form.querySelectorAll('[name]').forEach(field => {
                    if (field.classList.contains('tomselect-select')) {
                        // For Tom Select fields, use the ORIGINAL value from server (before any mapping)
                        originalFormData[field.getAttribute('name')] = field.dataset.originalValue || '';
                    } else {
                        originalFormData[field.getAttribute('name')] = field.value;
                    }
                });
                card.dataset.originalFormData = JSON.stringify(originalFormData);
                
                // Debug log important fields
                console.group('Record #' + card.dataset.recordId + ' - Saved Data');
                console.log('Municipio Residencia:', originalFormData.municipioresidenciad);
                console.log('Distrito:', originalFormData.distrito);
                console.log('Municipio Defunción:', originalFormData.municipiodefunciond);
                console.log('Lugar Específico:', originalFormData.sitiodefunciond);
                console.log('Causa Defunción:', originalFormData.sheet);
                console.groupEnd();
            });
        }, 50);
    }

    function attachRecordListeners(cardElement, recordId) {
        const card = cardElement.querySelector('.record-card');
        const form = cardElement.querySelector('.correction-form');
        const actionButtonsView = cardElement.querySelector('.action-buttons-view');
        const actionButtonsEdit = cardElement.querySelector('.action-buttons-edit');
        
        const btnToggleEdit = cardElement.querySelector('.btn-toggle-edit');
        const btnCancelEdit = cardElement.querySelector('.btn-cancel-edit');
        const btnSaveCorrection = cardElement.querySelector('.btn-save-correction');
        const btnRetry = cardElement.querySelector('.btn-retry');
        const btnDiscard = cardElement.querySelector('.btn-discard');

        // Store original form state for cancel
        const originalFormData = {};
        form.querySelectorAll('[name]').forEach(field => {
            originalFormData[field.getAttribute('name')] = field.value;
        });
        card.dataset.originalFormData = JSON.stringify(originalFormData);

        // Toggle Edit: Enable inputs and show edit buttons
        if (btnToggleEdit) {
            btnToggleEdit.addEventListener('click', (e) => {
                e.preventDefault();

                card.classList.add('is-editing-fields');
                form.querySelectorAll('[name]').forEach(field => {
                    field.disabled = false;
                });
                
                // Destroy existing Tom Select instances and recreate with edit mode
                setTimeout(() => {
                    const editSelects = form.querySelectorAll('select.tomselect-select');
                    editSelects.forEach(el => {
                        // Destroy existing Tom Select if it exists
                        if (el.tomselect) {
                            el.tomselect.destroy();
                        }
                        
                        let data = [];
                        const name = el.getAttribute('name');
                        
                        if (name === 'municipioresidenciad' || name === 'municipiodefunciond') {
                            data = municipalitiesData;
                        } else if (name === 'jurisdicciondefunciond') {
                            data = districtsData;
                        } else if (name === 'sitiodefunciond') {
                            data = locationsData;
                        } else if (name === 'sheet') {
                            data = causesData;
                        }
                        
                        if (data.length > 0) {
                            el.disabled = false;
                            initializeTomSelectField(el, data);
                            
                            // Add change listener to update originalText when user changes the value
                            el.addEventListener('change', function() {
                                // Get the selected item's name from the data array
                                const selectedId = this.value;
                                if (selectedId && data.length > 0) {
                                    const item = data.find(d => String(d.id) === String(selectedId));
                                    if (item) {
                                        this.dataset.originalText = item.name;
                                        console.log('Updated originalText to:', item.name);
                                    }
                                }
                            });
                        }
                    });

                    const deathMunicipalitySelect = form.querySelector('[name="municipiodefunciond"]');
                    const deathDistrictSelect = form.querySelector('[name="jurisdicciondefunciond"]');
                    const syncDeathDistrict = () => {
                        const municipality = municipalitiesData.find(item => String(item.id) === String(deathMunicipalitySelect?.value));
                        const districtName = districtNameForMunicipality(municipality?.name || deathMunicipalitySelect?.dataset.originalText);
                        const district = districtsData.find(item => String(item.name).trim().toUpperCase() === String(districtName).trim().toUpperCase());

                        if (district && deathDistrictSelect?.tomselect) {
                            deathDistrictSelect.tomselect.setValue(String(district.id), true);
                            deathDistrictSelect.dataset.originalText = district.name;
                        }
                    };
                    deathMunicipalitySelect?.tomselect?.on('change', syncDeathDistrict);
                    syncDeathDistrict();
                }, 50);
                
                actionButtonsView.classList.add('hidden');
                actionButtonsEdit.classList.remove('hidden');
            });
        }

        // Cancel Edit: Disable inputs, restore original values, destroy Tom Select, show view buttons
        if (btnCancelEdit) {
            btnCancelEdit.addEventListener('click', (e) => {
                e.preventDefault();
                card.classList.remove('is-editing-fields');
                
                // First, restore original form data
                const originalData = JSON.parse(card.dataset.originalFormData || '{}');
                form.querySelectorAll('[name]').forEach(field => {
                    const fieldName = field.getAttribute('name');
                    const originalValue = originalData[fieldName] || '';
                    
                    // Restore the original value
                    field.value = originalValue;
                    
                    // For Tom Select fields, also restore the originalValue and originalText for later lookup
                    if (field.classList.contains('tomselect-select')) {
                        field.dataset.originalValue = originalValue;
                        field.dataset.originalText = originalValue;
                    }
                    
                    field.disabled = true;
                });
                
                // Then destroy and recreate Tom Select instances with restored values
                setTimeout(() => {
                    const editSelects = form.querySelectorAll('select.tomselect-select');
                    editSelects.forEach(el => {
                        // Destroy existing Tom Select if it exists
                        if (el.tomselect) {
                            el.tomselect.destroy();
                        }
                        
                        let data = [];
                        const name = el.getAttribute('name');
                        
                        if (name === 'municipioresidenciad' || name === 'municipiodefunciond') {
                            data = municipalitiesData;
                        } else if (name === 'jurisdicciondefunciond') {
                            data = districtsData;
                        } else if (name === 'sitiodefunciond') {
                            data = locationsData;
                        } else if (name === 'sheet') {
                            data = causesData;
                        }
                        
                        if (data.length > 0) {
                            el.disabled = true;
                            initializeTomSelectField(el, data);
                        }
                    });
                }, 50);
                
                actionButtonsEdit.classList.add('hidden');
                actionButtonsView.classList.remove('hidden');
            });
        }

        // Save Correction
        if (btnSaveCorrection) {
            btnSaveCorrection.addEventListener('click', (e) => {
                e.preventDefault();
                if (validateAgeFields(form)) {
                    saveCorrection(recordId, form, card, false);
                }
            });
        }

        // Retry Import
        if (btnRetry) {
            btnRetry.addEventListener('click', (e) => {
                e.preventDefault();
                if (validateAgeFields(form)) {
                    saveCorrection(recordId, form, card, true);
                }
            });
        }

        // Discard Record
        if (btnDiscard) {
            btnDiscard.addEventListener('click', async (e) => {
                e.preventDefault();
                const confirmed = await window.confirmDeleteDialog({
                    title: 'Descartar registro',
                    subject: 'este registro fallido',
                    description: 'Se quitará de la lista y no podrá recuperarse.',
                    confirmText: 'Descartar'
                });

                if (!confirmed) {
                    return;
                }

                discardRecord(recordId, card);
            });
        }
    }

    function validateAgeFields(form) {
        const edadValor = form.querySelector('[name="edad_valor"]');
        const edadUnidad = form.querySelector('[name="edad_unidad"]');
        
        // If no age fields, validation passes
        if (!edadValor || !edadUnidad) return true;
        
        const valor = edadValor.value.trim();
        const unidad = edadUnidad.value;
        
        // If valor is provided, unidad is required
        if (valor !== '' && !unidad) {
            edadUnidad.setCustomValidity('Debe seleccionar la unidad (años, meses o días)');
            edadUnidad.reportValidity();
            return false;
        }
        
        // If unidad is 'meses', valor must be < 12
        if (unidad === 'meses' && valor !== '') {
            const valorNum = parseInt(valor);
            if (valorNum >= 12) {
                edadValor.setCustomValidity('Si la unidad es "meses", el valor debe ser menor a 12. Para 12 o más use años.');
                edadValor.reportValidity();
                return false;
            }
        }

        // If unidad is 'dias', valor must be between 0 and 30 (inclusive)
        if (unidad === 'dias' && valor !== '') {
            const valorNum = parseInt(valor);
            if (valorNum < 0) {
                edadValor.setCustomValidity('Si la unidad es "días", el valor debe ser mayor o igual a 0.');
                edadValor.reportValidity();
                return false;
            }
            if (valorNum > 30) {
                edadValor.setCustomValidity('Si la unidad es "días", el valor debe ser menor o igual a 30.');
                edadValor.reportValidity();
                return false;
            }
        }
        
        // Clear custom validity if all checks pass
        edadValor.setCustomValidity('');
        edadUnidad.setCustomValidity('');
        return true;
    }

    function saveCorrection(recordId, form, card, shouldRetry = false) {
        const correctedData = {};
        form.querySelectorAll('[name]').forEach(field => {
            const fieldName = field.getAttribute('name');
            let value = '';
            
            // For Tom Select fields, use originalText (the name) instead of value (the ID)
            if (field.classList.contains('tomselect-select')) {
                value = field.dataset.originalText ? field.dataset.originalText.trim() : '';
            } else {
                value = field.value ? field.value.trim() : '';
            }
            
            if (value !== '') {
                correctedData[fieldName] = value;
            }
        });

        const endpoint = shouldRetry 
            ? `/api/estadisticas/registros-fallidos/${recordId}/reintentar`
            : `/api/estadisticas/registros-fallidos/${recordId}/corregir`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ corrected_data: correctedData }),
        })
        .then(response => response.json())
        .then(data => {
            const validationErrors = card.querySelector('.validation-errors');
            
            if (data.ok) {
                if (shouldRetry) {
                    // Animate removal without reloading page
                    card.style.transition = 'opacity 0.3s ease-out';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        // Check if there are any cards left on this page
                        const recordsList = document.getElementById('records-list');
                        if (recordsList.children.length === 0) {
                            // Reload current page or go to previous page if this was last item
                            const currentPageNum = currentPage;
                            loadFailedRecords(currentPageNum > 1 ? currentPageNum - 1 : currentPageNum);
                        }
                    }, 300);
                    notifyFailedImport('Registro importado.', 'success');
                } else {
                    card.classList.remove('in-edit');
                    validationErrors.classList.add('hidden');
                    form.querySelectorAll('[name]').forEach(field => {
                        const fieldName = field.getAttribute('name');
                        const displayElement = card.querySelector(`.original-${fieldName}`);
                        if (displayElement) {
                            let displayValue = field.value || '-';
                            
                            // For Tom Select fields, convert ID back to name for display
                            if (field.classList.contains('tomselect-select')) {
                                let data = [];
                                if (fieldName === 'municipioresidenciad' || fieldName === 'municipiodefunciond') {
                                    data = municipalitiesData;
                                } else if (fieldName === 'jurisdicciondefunciond') {
                                    data = districtsData;
                                } else if (fieldName === 'sitiodefunciond') {
                                    data = locationsData;
                                } else if (fieldName === 'sheet') {
                                    data = causesData;
                                }
                                
                                if (data && data.length > 0 && field.value) {
                                    const item = data.find(i => String(i.id) === String(field.value));
                                    if (item) {
                                        displayValue = item.name;
                                        field.dataset.originalText = item.name;
                                    }
                                }
                            }
                            
                            displayElement.textContent = displayValue;
                        }
                    });
                    notifyFailedImport('Cambios guardados.', 'success');
                }
            } else {
                if (data.errors && Array.isArray(data.errors)) {
                    const errorsList = validationErrors.querySelector('ul');
                    errorsList.innerHTML = '';
                    data.errors.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        errorsList.appendChild(li);
                    });
                    validationErrors.classList.remove('hidden');
                } else {
                    notifyFailedImport(data.message || 'No se pudieron guardar los cambios. Intenta nuevamente.', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            notifyFailedImport('No se pudieron guardar los cambios. Inténtalo nuevamente.', 'error');
        });
    }

    function discardRecord(recordId, card) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(`/api/estadisticas/registros-fallidos/${recordId}/descartar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                notifyFailedImport('Registro descartado.', 'success');
                // Animate removal
                card.style.transition = 'opacity 0.3s ease-out';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    // Check if there are any cards left on this page
                    const recordsList = document.getElementById('records-list');
                    if (recordsList.children.length === 0) {
                        // Reload current page or go to previous page if this was last item
                        const currentPageNum = currentPage;
                        loadFailedRecords(currentPageNum > 1 ? currentPageNum - 1 : currentPageNum);
                    }
                }, 300);
            } else {
                notifyFailedImport(data.message || 'No se pudo descartar el registro. Inténtalo nuevamente.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            notifyFailedImport('No se pudo descartar el registro. Inténtalo nuevamente.', 'error');
        });
    }

    function renderFailedPaginationPilot(data) {
        const container = document.getElementById('dt-pagination');
        updateFailedPaginationInfo(data);
        if (!container) return;
        const activePage = Number(data.current_page) || 1;
        const totalPages = Number(data.last_page) || 0;
        const pageButton = page => page === activePage
            ? `<span class="fb-page-btn fb-page-num fb-page-active" aria-current="page">${page}</span>`
            : `<a href="#" data-page="${page}" class="pagination-link-failed-imports fb-page-btn fb-page-num">${page}</a>`;
        const ellipsis = () => '<span class="fb-page-btn fb-page-num fb-page-ellipsis">...</span>';
        let html = '<div class="fb-pagination" role="navigation" aria-label="Paginación de registros fallidos">';

        html += activePage === 1 || totalPages === 0
            ? '<span class="fb-page-btn fb-page-first fb-page-disabled">Anterior</span>'
            : `<a href="#" data-page="${activePage - 1}" class="pagination-link-failed-imports fb-page-btn fb-page-first">Anterior</a>`;

        if (totalPages <= 5) {
            for (let page = 1; page <= totalPages; page++) html += pageButton(page);
        } else if (activePage <= 3) {
            for (let page = 1; page <= 5; page++) html += pageButton(page);
            html += ellipsis() + pageButton(totalPages);
        } else if (activePage >= totalPages - 2) {
            html += pageButton(1) + ellipsis();
            for (let page = totalPages - 4; page <= totalPages; page++) html += pageButton(page);
        } else {
            html += pageButton(1) + ellipsis();
            for (let page = activePage - 1; page <= activePage + 1; page++) html += pageButton(page);
            html += ellipsis() + pageButton(totalPages);
        }

        html += activePage === totalPages || totalPages === 0
            ? '<span class="fb-page-btn fb-page-last fb-page-disabled">Siguiente</span>'
            : `<a href="#" data-page="${activePage + 1}" class="pagination-link-failed-imports fb-page-btn fb-page-last">Siguiente</a>`;
        html += '</div>';
        container.innerHTML = html;

        container.querySelectorAll('.pagination-link-failed-imports').forEach(link => {
            link.addEventListener('click', event => {
                event.preventDefault();
                loadFailedRecords(Number(link.dataset.page));
                document.querySelector('.failed-imports-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function updateFailedPaginationInfo(data) {
        const footerInfo = document.getElementById('dt-info');
        if (!footerInfo) return;
        const from = Number(data.from) || 0;
        const to = Number(data.to) || 0;
        const total = Number(data.total) || 0;
        footerInfo.classList.remove('is-loading');
        footerInfo.innerHTML = `<span class="users-table-info-main">Mostrando <span class="font-semibold text-gray-900">${from}-${to}</span> de <span class="font-semibold text-gray-900">${total}</span></span>`;
    }

    loadFailedRecords(1);
});
</script>
@endpush
