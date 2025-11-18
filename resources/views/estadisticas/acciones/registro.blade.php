@extends('layouts.principal')
@section('title', 'Nuevo Registro')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Nuevo registro de defunción</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Complete el formulario para registrar una defunción en el sistema.</p>

        <!-- Cuadro del formulario responsive -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">
            <form id="death-register-form" method="POST" action="{{ route('statistic.store') }}">
                @csrf
            
            <!-- Sección 1: Información del fallecido -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="person-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información del fallecido</h2>
                    <div class="flex-1 h-px bg-[#404041] ml-3"></div>
                </div>
                
                <!-- Mantener solo una versión del grid principal; la versión final aparece más abajo. -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-4 items-start">
                        <!-- Row 1: Folio | Nombre | Ap. paterno -->
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Folio</label>
                            <input name="gov_folio" type="text" value="{{ old('gov_folio') }}" required
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: 230787888">
                            @error('gov_folio') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s)</label>
                            <input name="name" type="text" value="{{ old('name') }}"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Juan Diego">
                            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno</label>
                            <input name="first_last_name" type="text" value="{{ old('first_last_name') }}"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Nava">
                            @error('first_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Row 2: Apellido materno | Sexo | Edad (valor + unidad) -->
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno</label>
                            <input name="second_last_name" type="text" value="{{ old('second_last_name') }}"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Reyes">
                            @error('second_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Sexo</label>
                            <select name="sex" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione una opción</option>
                                <option value="masculino" {{ old('sex') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="femenino" {{ old('sex') === 'femenino' ? 'selected' : '' }}>Femenino</option>
                            </select>
                            @error('sex') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Edad</label>
                            <div class="flex gap-2">
                                <input name="edad_valor" type="number" min="0" max="150" value="{{ old('edad_valor') }}"
                                       class="w-1/2 px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                       placeholder="Ej: 34">
                                <select name="edad_unidad" required aria-required="true" class="w-1/2 px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                    <option value="">Unidad</option>
                                    <option value="anos" {{ old('edad_unidad') === 'anos' ? 'selected' : '' }}>Años</option>
                                    <option value="meses" {{ old('edad_unidad') === 'meses' ? 'selected' : '' }}>Meses</option>
                                </select>
                            </div>
                            @error('edad_valor') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            @error('edad_unidad') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
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
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                        <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de residencia</label>
                            <select id="residence_municipality_select" name="residence_municipality_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select">
                                <option value="">Seleccione un municipio</option>
                                @if(old('residence_municipality_id'))
                                    @php $m = $municipalities->firstWhere('id', old('residence_municipality_id')) @endphp
                                    @if($m)
                                        <option value="{{ $m->id }}" selected>{{ $m->name }}</option>
                                    @endif
                                @endif
                            </select>
                            @error('residence_municipality_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de defunción</label>
                            <select id="death_municipality_select" name="death_municipality_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select">
                                <option value="">Seleccione un municipio</option>
                                @if(old('death_municipality_id'))
                                    @php $m2 = $municipalities->firstWhere('id', old('death_municipality_id')) @endphp
                                    @if($m2)
                                        <option value="{{ $m2->id }}" selected>{{ $m2->name }}</option>
                                    @endif
                                @endif
                            </select>
                            @error('death_municipality_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                    </div>
                    
                        <div class="space-y-3">
                        
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Jurisdicción de residencia</label>
                            {{-- Hidden input that will be submitted. The visible select is disabled so users can't change it directly. --}}
                            <input type="hidden" id="jurisdiction_input" name="jurisdiction_id" value="{{ old('jurisdiction_id') ?? '' }}">
                            <!-- Visible, readable jurisdiction display (readonly) to improve accessibility. The hidden input still submits the id. -->
                            <input id="jurisdiction_display" type="text" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" value="Pendiente (seleccione municipio)" readonly aria-describedby="jurisdiction-help">
                            <span id="jurisdiction-help" class="sr-only">La jurisdicción se determina automáticamente según el municipio seleccionado</span>
                            @error('jurisdiction_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar específico</label>
                            <select id="death_municipality_location" name="death_location_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione lugar</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('death_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                            @error('death_location_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
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
                        <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Causa de la defunción</label>
                            <select id="death_cause" name="death_cause_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione una causa</option>
                                @foreach($causes as $c)
                                    <option value="{{ $c->id }}" {{ old('death_cause_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('death_cause_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de defunción</label>
                            <input name="death_date" type="date" value="{{ old('death_date') }}"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                            @error('death_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

            <!-- USAR COMPONENTE DE BOTONES: Limpiar / Volver / Guardar -->
            <x-form-buttons 
                secondaryOnclick="clearDeathForm(event)"
                tertiaryText="Volver al listado"
                tertiaryHref="{{ route('statistic.data') }}"
                primaryText="Guardar registro"
            />
            </form>
        </div>
    </div>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Tom Select CDN (single-select, styled to match inputs) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Make Tom Select control visually match your existing Tailwind input styles */
        /* Tom Select control: try to match exact paddings/height of your native selects */
        .ts-wrapper {
            /* remove wrapper border so there's only one visible border (the control itself) */
            border: none !important;
            padding: 0 !important;
            background: transparent !important;
        }

        /* Hide original select elements that are enhanced by Tom Select to avoid native arrow/box
           (these selects have class "tomselect-select" in the template). We prefer an
           accessible hide via the `sr-only` utility so the element remains in the DOM
           for screen readers and form semantics. */
        select.tomselect-select {
            /* keep a minimal visual removal - TomSelect will still use the element */
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

        /* remove the IE dropdown arrow */
        select.tomselect-select::-ms-expand { display: none !important; }
        /* safety: some browsers render a native background image for select arrows */
        select.tomselect-select { background-image: none !important; }

        .ts-wrapper { display: block; width: 100%; }

        .ts-control {
            /* Mirror your Tailwind input: use the same padding/line-height and let height be automatic */
            border: 1px solid #d1d5db !important; /* gray-300 */
            border-radius: 0.5rem !important; /* rounded-lg */
            padding: 8px 12px !important; /* px-3 py-2 equivalent */
            background: #ffffff !important;
            font-family: inherit;
            font-size: 0.875rem; /* 14px */
            line-height: 1.25rem !important;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            position: relative;
            box-sizing: border-box;
            margin: 0 !important;
            /* remove any inner shadow so it matches native selects exactly */
            box-shadow: none !important;
            height: auto !important;
            min-height: 36px !important;
        }
        /* internal input/item inside Tom Select: remove extra padding and align line-height */
        .ts-control .item, .ts-control input {
            padding: 0 !important;
            margin: 0 !important;
            height: auto !important;
            line-height: 1.25rem !important;
            font-size: inherit;
            font-family: inherit;
        }
        /* hide common internal toggle elements Tom Select may use (best-effort) */
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
        /* caret we will inject (right side) */
        .tomselect-caret {
            display: none !important; /* hide injected caret so native arrow remains */
            position: absolute;
            right: 12px; /* align with native select padding */
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280; /* gray-500 */
            pointer-events: none;
            font-size: 0.9rem;
        }

        /* Draw a native-looking chevron on Tom Select controls so they match other selects.
           This uses an inline SVG encoded as a data URI to mimic the thin outlined chevron. */
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

        /* Small tweak for exact vertical alignment: ensure the Tom Select control aligns to baseline
           with other form controls in the grid */
        .ts-wrapper, .ts-control { vertical-align: middle; }

        /* Target the Tom Select control generated next to each original select by ID
           and nudge the death municipality control 1px up if still slightly lower. */
        /* Ensure generated TomSelect controls match their original select container width
           and don't receive manual transforms that break alignment. */
        #residence_municipality_select + .ts-control,
        #death_municipality_select + .ts-control {
            padding: 8px 12px !important;
            height: auto !important;
            min-height: 36px !important;
            transform: none !important;
        }
        /* (No chevron unification) keep native select appearance for non-TomSelect selects */
    </style>

    <style>
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
        /* Slightly tone down the calendar icon so it blends with your selects */
        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.7;
            transform: scale(0.95);
        }
        input[type="date"]::-webkit-inner-spin-button,
        input[type="date"]::-webkit-clear-button {
            display: none;
        }
    </style>

    <script>
            document.addEventListener('DOMContentLoaded', function() {
            // Build a map municipality_id -> jurisdiction_id
            const muniToJur = @json($municipalities->mapWithKeys(function($m){ return [$m->id => $m->jurisdiction_id]; }));
            // Map jurisdiction_id -> jurisdiction name (for display)
            const jurisNames = @json($jurisdictions->mapWithKeys(function($j){ return [$j->id => $j->name]; }));

            const residenceMuni = document.getElementById('residence_municipality_select');
            const deathMuni = document.getElementById('death_municipality_select');
            const jurisdictionDisplay = document.getElementById('jurisdiction_display');
            const hiddenJur = document.getElementById('jurisdiction_input');
            const form = document.getElementById('death-register-form');

            // Helper classes to show placeholder style when empty
            const placeholderClasses = ['text-gray-500'];

            function applyPlaceholderStyle() {
                if (!jurisdictionDisplay) return;
                // When there's no real value, show placeholder style on the visible read-only input
                if (!hiddenJur || !hiddenJur.value) {
                    jurisdictionDisplay.classList.add(...placeholderClasses);
                } else {
                    jurisdictionDisplay.classList.remove(...placeholderClasses);
                }
            }

            // If there is an old value (validation error), ensure the visible display shows it and the hidden contains it
            if (hiddenJur && hiddenJur.value) {
                try { jurisdictionDisplay.value = jurisNames[hiddenJur.value] || ''; } catch (err) {}
            } else {
                // keep display showing placeholder on initial load
                if (jurisdictionDisplay) {
                    jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                }
            }
            // Apply placeholder visual state on load
            applyPlaceholderStyle();

            function setJurisdictionBasedOnMunicipality() {
                // Prefer the residence municipality to determine jurisdiction
                const mid = (residenceMuni?.value) ? residenceMuni.value : (deathMuni?.value || '');
                if (mid && muniToJur[mid]) {
                    const jid = muniToJur[mid];
                    if (hiddenJur) hiddenJur.value = jid;
                    if (jurisdictionDisplay) jurisdictionDisplay.value = jurisNames[jid] || '';
                } else {
                    // No mapping: set to pendiente and clear hidden (we'll let server assign 'NO ENCONTRADA' if needed)
                    if (hiddenJur) hiddenJur.value = '';
                    if (jurisdictionDisplay) jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                }
                // Update visual placeholder state after changing values
                applyPlaceholderStyle();
            }

            if (residenceMuni) {
                residenceMuni.addEventListener('change', setJurisdictionBasedOnMunicipality);
            }
            // also initialize on load using residence (or death as fallback)
            setJurisdictionBasedOnMunicipality();

            window.clearDeathForm = function(e) {
                try { e.preventDefault(); } catch (err) {}
                if (!form) return;
                form.reset();
                // Keep jurisdiction locked and clear its hidden value after reset
                if (jurisdictionDisplay) {
                    jurisdictionDisplay.value = 'Pendiente (seleccione municipio)';
                }
                if (hiddenJur) {
                    hiddenJur.value = '';
                }
                // Update visual placeholder state after reset
                applyPlaceholderStyle();
            }
        });
        // Initialize Tom Select (single selection) for the municipality fields
        (function() {
            // helper to fetch suggestions
            function fetchMunicipalities(q) {
                return fetch('/api/municipalities/search?q=' + encodeURIComponent(q)).then(r => r.json());
            }

            // residence
            const residenceSelect = document.getElementById('residence_municipality_select');
            if (residenceSelect) {
                const ts1 = new TomSelect(residenceSelect, {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    maxOptions: 20,
                    maxItems: 1,
                    create: false,
                    preload: true,
                    load: function(query, callback) {
                        // Allow empty query so TomSelect can preload initial items (popular/top)
                        fetchMunicipalities(query).then(items => callback(items)).catch(() => callback());
                    },
                    onChange: function(value) {
                        // when TomSelect changes, the underlying select value is already updated
                        // no extra hidden field is needed
                    }
                });
                // hide the original select element (prevents native browser arrow/outer box)
                try { residenceSelect.style.display = 'none'; } catch (e) {}
                // (no injected caret) keep the native browser arrow to match other selects
            }

            // death
            const deathSelect = document.getElementById('death_municipality_select');
            if (deathSelect) {
                const ts2 = new TomSelect(deathSelect, {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    maxOptions: 20,
                    maxItems: 1,
                    create: false,
                    preload: true,
                    load: function(query, callback) {
                        // Allow empty query so TomSelect can preload initial items (popular/top)
                        fetchMunicipalities(query).then(items => callback(items)).catch(() => callback());
                    },
                    onChange: function(value) {
                        // trigger jurisdiction mapping on the underlying select
                        const evt = new Event('change');
                        deathSelect.dispatchEvent(evt);
                    }
                });
                // hide the original select element (prevents native browser arrow/outer box)
                try { deathSelect.style.display = 'none'; } catch (e) {}
                // (no injected caret) keep the native browser arrow to match other selects
            }
        })();

        /* Initialize TomSelect for causes and specific places (remote search) */
        (function() {
            function fetchCauses(q) {
                return fetch('/api/causes/search?q=' + encodeURIComponent(q)).then(r => r.json());
            }

            const causeSelect = document.getElementById('death_cause');
            if (causeSelect) {
                new TomSelect(causeSelect, {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    maxOptions: 30,
                    maxItems: 1,
                    create: false,
                    load: function(query, callback) {
                        if (!query.length) return callback();
                        fetchCauses(query).then(items => callback(items)).catch(() => callback());
                    }
                });
                try { causeSelect.style.display = 'none'; } catch (e) {}
            }

            function fetchLocations(q) {
                return fetch('/api/locations/search?q=' + encodeURIComponent(q)).then(r => r.json());
            }

            const locationSelect = document.getElementById('death_municipality_location');
            if (locationSelect) {
                const locTs = new TomSelect(locationSelect, {
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    maxOptions: 40,
                    maxItems: 1,
                    create: false,
                    load: function(query, callback) {
                        // If empty query, TomSelect will typically call with empty string; server returns popular ones
                        fetchLocations(query).then(items => callback(items)).catch(() => callback());
                    }
                });
                try { locationSelect.style.display = 'none'; } catch (e) {}
            }
        })();
    </script>
@endsection