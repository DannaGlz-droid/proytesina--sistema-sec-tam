@extends('layouts.principal')
@section('title', 'Actualizar Registro')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Actualizar registro de defunción</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Modifique los campos necesarios y guarde los cambios.</p>

        <!-- Cuadro del formulario responsive -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">
            <form id="death-update-form" method="POST" action="{{ route('statistic.update', $defuncion->id ?? 0) }}">
                @csrf
                @method('PUT')
            
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
                           <input id="gov_folio" name="gov_folio" type="text" 
                               class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                               placeholder="Ej: 230787888"
                               value="{{ old('gov_folio', $defuncion->gov_folio ?? '') }}" required minlength="9" maxlength="9" pattern="[0-9]{9}" inputmode="numeric" title="Ingrese 9 dígitos">
                        @error('gov_folio') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) <span class="text-red-600">*</span></label>
                        <input id="name" name="name" type="text" required minlength="2" maxlength="191"
                               class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                               placeholder="Ej: Juan Diego"
                               value="{{ old('name', $defuncion->name ?? '') }}">
                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno <span class="text-red-600">*</span></label>
                        <input id="first_last_name" name="first_last_name" type="text" required minlength="2" maxlength="191"
                               class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                               placeholder="Ej: Nava"
                               value="{{ old('first_last_name', $defuncion->first_last_name ?? '') }}">
                        @error('first_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Row 2: Apellido materno | Sexo | Edad (valor + unidad) -->
                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno</label>
                        <input id="second_last_name" name="second_last_name" type="text" minlength="2" maxlength="191"
                               class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                               placeholder="Ej: Reyes"
                               value="{{ old('second_last_name', $defuncion->second_last_name ?? '') }}">
                        @error('second_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Sexo *</label>
                        <select name="sex" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                            <option value="">Seleccione una opción</option>
                            <option value="M" {{ old('sex', $defuncion->sex ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sex', $defuncion->sex ?? '') == 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                        @error('sex') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Edad *</label>
                        @php
                            $defaultValor = old('edad_valor');
                            $defaultUnidad = old('edad_unidad');
                            if (is_null($defaultValor)) {
                                if (isset($defuncion->age_years) && $defuncion->age_years !== null) {
                                    if ($defuncion->age_years >= 1) {
                                        $defaultValor = $defuncion->age_years;
                                        $defaultUnidad = $defaultUnidad ?? 'anos';
                                    } else {
                                        if (isset($defuncion->age_months) && $defuncion->age_months !== null) {
                                            $defaultValor = $defuncion->age_months;
                                            $defaultUnidad = $defaultUnidad ?? 'meses';
                                        } else {
                                            $defaultValor = $defuncion->age ?? '';
                                            $defaultUnidad = $defaultUnidad ?? 'anos';
                                        }
                                    }
                                } else {
                                    $defaultValor = $defuncion->age ?? '';
                                    $defaultUnidad = $defaultUnidad ?? 'anos';
                                }
                            }
                        @endphp
                           <div class="flex gap-2">
                            <input id="edad_valor" name="edad_valor" type="number" min="0" max="150" value="{{ $defaultValor }}" required
                                class="w-1/2 px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                placeholder="Ej: 34">
                            <select id="edad_unidad" name="edad_unidad" required aria-required="true" class="w-1/2 px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Unidad</option>
                                <option value="anos" {{ $defaultUnidad === 'anos' ? 'selected' : '' }}>Años</option>
                                <option value="meses" {{ $defaultUnidad === 'meses' ? 'selected' : '' }}>Meses</option>
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de residencia <span class="text-red-600">*</span></label>
                            <select id="residence_municipality_select" name="residence_municipality_id" required class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select">
                                <option value="">Seleccione un municipio</option>
                                @foreach($municipalities as $m)
                                    <option value="{{ $m->id }}" {{ (int)old('residence_municipality_id', $defuncion->residence_municipality_id ?? 0) === $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                            <p id="residence_municipality_error" class="text-xs text-red-600 mt-1 hidden">Este campo es requerido</p>
                            @error('residence_municipality_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de defunción <span class="text-red-600">*</span></label>
                            <select id="death_municipality_select" name="death_municipality_id" required class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select">
                                <option value="">Seleccione un municipio</option>
                                @foreach($municipalities as $m)
                                    <option value="{{ $m->id }}" {{ (int)old('death_municipality_id', $defuncion->death_municipality_id ?? 0) === $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                            <p id="death_municipality_error" class="text-xs text-red-600 mt-1 hidden">Este campo es requerido</p>
                            @error('death_municipality_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Jurisdicción de residencia</label>
                {{-- Hidden input to submit jurisdiction id; visible input is readonly/display only --}}
                <input type="hidden" id="jurisdiction_input" name="jurisdiction_id" value="{{ old('jurisdiction_id', $defuncion->jurisdiction_id ?? '') }}">
                <input type="text" id="jurisdiction" 
                    class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                    placeholder="Pendiente (seleccione municipio)"
                    readonly
                    value="{{ old('jurisdiction_id') ? ($jurisdictions->firstWhere('id', old('jurisdiction_id'))->name ?? '') : (optional($defuncion->jurisdiction)->name ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Lugar específico *</label>
                            <select id="death_municipality_location" name="death_location_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione lugar</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ (int)old('death_location_id', $defuncion->death_location_id ?? 0) === $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Causa de la defunción *</label>
                            <select id="death_cause" name="death_cause_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione una causa</option>
                                @foreach($causes as $c)
                                    <option value="{{ $c->id }}" {{ (int)old('death_cause_id', $defuncion->death_cause_id ?? 0) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('death_cause_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de defunción *</label>
                            <input name="death_date" type="date" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                   value="{{ old('death_date', optional($defuncion)->death_date ? \Carbon\Carbon::parse(optional($defuncion)->death_date)->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="h-px bg-gray-300 my-4 lg:my-6"></div>

            <!-- USAR COMPONENTE DE BOTONES ESTANDARIZADO -->
            <x-form-buttons 
                primaryText="Actualizar registro"
                secondaryText=""
                tertiaryText="Volver al listado"
                tertiaryHref="{{ route('statistic.data') }}"
                primaryType="submit"
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
        /* Tom Select visual parity with native inputs (copied from registro) */
        .ts-wrapper { border: none !important; padding: 0 !important; background: transparent !important; }
        select.tomselect-select { position: absolute !important; left: -9999px !important; width: 1px !important; height: 1px !important; overflow: hidden !important; opacity: 0 !important; pointer-events: none !important; border: 0 !important; margin: 0 !important; padding: 0 !important; background: transparent !important; -webkit-appearance: none !important; -moz-appearance: none !important; appearance: none !important; }
        select.tomselect-select::-ms-expand { display: none !important; }
        select.tomselect-select { background-image: none !important; }
        .ts-wrapper { display: block; width: 100%; }
        .ts-control { border: 1px solid #d1d5db !important; border-radius: 0.5rem !important; padding: 8px 12px !important; background: #ffffff !important; font-family: inherit; font-size: 0.875rem; line-height: 1.25rem !important; display: flex; align-items: center; justify-content: flex-start; position: relative; box-sizing: border-box; margin: 0 !important; box-shadow: none !important; height: auto !important; min-height: 36px !important; }
        .ts-control .item, .ts-control input { padding: 0 !important; margin: 0 !important; height: auto !important; line-height: 1.25rem !important; font-size: inherit; font-family: inherit; }
        .ts-control .dropdown-toggle, .ts-control .ts-dropdown-toggle, .ts-control .dropdown_toggle, .ts-control .ts-clear { display: none !important; }
        .ts-dropdown { border: 1px solid #d1d5db; border-radius: 0.5rem; box-shadow: 0 2px 6px rgba(0,0,0,0.08); max-height: 240px; overflow: auto; }
        .ts-dropdown .ts-option { padding: 0.5rem 0.75rem; }
        .tomselect-caret { display: none !important; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #6b7280; pointer-events: none; font-size: 0.9rem; }
        .ts-control::after { content: ""; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>"); background-repeat: no-repeat; background-position: center; background-size: 12px 12px; pointer-events: none; opacity: 0.92; }
        .ts-wrapper, .ts-control { vertical-align: middle; }
        #residence_municipality_select + .ts-control, #death_municipality_select + .ts-control { padding: 8px 12px !important; height: auto !important; min-height: 36px !important; transform: none !important; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Build municipality -> jurisdiction id map. Support keys by id and by lowercased name.
            const muniToJur = @isset($municipalities) @json($municipalities->mapWithKeys(function($m){ return [$m->id => $m->jurisdiction_id, mb_strtolower($m->name) => $m->jurisdiction_id]; })) @else {} @endisset;
            const jurIdToName = @isset($jurisdictions) @json($jurisdictions->mapWithKeys(function($j){ return [$j->id => $j->name]; })) @else {} @endisset;

            const residenceMuni = document.getElementById('residence_municipality_select');
            const deathMuni = document.getElementById('death_municipality_select');
            const jurisdictionVisible = document.getElementById('jurisdiction');
            const hiddenJur = document.getElementById('jurisdiction_input');

            const placeholderClasses = ['text-gray-400','italic'];

            function applyPlaceholderStyle() {
                if (!jurisdictionVisible) return;
                if (!hiddenJur || !hiddenJur.value) {
                    jurisdictionVisible.classList.add(...placeholderClasses);
                } else {
                    jurisdictionVisible.classList.remove(...placeholderClasses);
                }
            }

            // Initialize visible jurisdiction from hidden (old value) if present
            if (hiddenJur && hiddenJur.value) {
                // try to show friendly name if available
                const name = jurIdToName[hiddenJur.value] ?? hiddenJur.value;
                jurisdictionVisible.value = name;
            } else {
                jurisdictionVisible.value = '';
            }
            // keep readonly locked
            if (jurisdictionVisible) jurisdictionVisible.readOnly = true;
            applyPlaceholderStyle();

            function setJurisdictionBasedOnMunicipality() {
                // Prefer residence municipality to determine jurisdiction; fallback to death municipality
                const mid = (residenceMuni?.value) ? residenceMuni.value : (deathMuni?.value || '');
                let jid = null;
                if (mid && muniToJur[mid]) jid = muniToJur[mid];
                else if (mid && muniToJur[mid.toLowerCase?.()]) jid = muniToJur[mid.toLowerCase?.()];

                if (jid) {
                    if (hiddenJur) hiddenJur.value = jid;
                    if (jurisdictionVisible) jurisdictionVisible.value = jurIdToName[jid] ?? jid;
                } else {
                    if (hiddenJur) hiddenJur.value = '';
                    if (jurisdictionVisible) jurisdictionVisible.value = '';
                }
                applyPlaceholderStyle();
            }

            if (residenceMuni) {
                residenceMuni.addEventListener('change', setJurisdictionBasedOnMunicipality);
            }
            // initialize on load
            setJurisdictionBasedOnMunicipality();
        });
    </script>
    <script>
        // Initialize Tom Select for municipality selects and remote lookups for causes/locations
        (function() {
            // Set max date for death_date to today
            document.addEventListener('DOMContentLoaded', function() {
                const deathDateInput = document.querySelector('input[name="death_date"]');
                if (deathDateInput) {
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    const day = String(today.getDate()).padStart(2, '0');
                    deathDateInput.max = `${year}-${month}-${day}`;
                }
            });

            function fetchMunicipalities(q) {
                return fetch('/api/municipalities/search?q=' + encodeURIComponent(q)).then(r => r.json());
            }

            // Helper to add validation styling to a TomSelect instance
            function addValidationStyles(selectElement, tomSelectInstance, errorElementId) {
                const checkValidity = () => {
                    const value = selectElement.value;
                    const wrapper = selectElement.closest('.ts-wrapper') || selectElement.parentElement;
                    const tsControl = wrapper?.querySelector('.ts-control');
                    const errorElement = errorElementId ? document.getElementById(errorElementId) : null;

                    if (!value || value === '') {
                        if (tsControl) {
                            tsControl.style.borderColor = '#dc2626';
                            tsControl.style.borderWidth = '2px';
                            tsControl.style.boxShadow = '0 0 0 3px rgba(220, 38, 38, 0.1)';
                        }
                        if (errorElement) errorElement.classList.remove('hidden');
                        return false;
                    } else {
                        if (tsControl) {
                            tsControl.style.borderColor = '';
                            tsControl.style.borderWidth = '';
                            tsControl.style.boxShadow = '';
                        }
                        if (errorElement) errorElement.classList.add('hidden');
                        return true;
                    }
                };

                if (tomSelectInstance) tomSelectInstance.on('change', checkValidity);
                // also check on native select change (TomSelect may not fire in some edge-cases)
                selectElement.addEventListener('change', checkValidity);
                return checkValidity;
            }

            // Age validation (same logic as registro)
            function validateAge() {
                const edadValor = document.getElementById('edad_valor');
                const edadUnidad = document.getElementById('edad_unidad');
                if (!edadValor || !edadUnidad) return true;

                const valor = String(edadValor.value || '').trim();
                const unidad = edadUnidad.value;

                if (valor !== '' && !unidad) {
                    edadUnidad.setCustomValidity('Debe seleccionar la unidad (años o meses)');
                    return false;
                }
                if (unidad === 'meses' && valor !== '') {
                    const valorNum = parseInt(valor);
                    if (valorNum >= 12) {
                        edadValor.setCustomValidity('Si la unidad es "meses", el valor debe ser menor a 12. Para 12 o más use años.');
                        return false;
                    }
                }
                edadValor.setCustomValidity('');
                edadUnidad.setCustomValidity('');
                return true;
            }

            // Form submit validation
            const form = document.getElementById('death-update-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!validateAge()) {
                        e.preventDefault();
                        const edadValor = document.getElementById('edad_valor');
                        const edadUnidad = document.getElementById('edad_unidad');
                        if (edadValor && !edadValor.checkValidity()) edadValor.reportValidity();
                        else if (edadUnidad && !edadUnidad.checkValidity()) edadUnidad.reportValidity();
                        return false;
                    }

                    const requiredSelects = [
                        'residence_municipality_select',
                        'death_municipality_select',
                        'death_municipality_location',
                        'death_cause'
                    ];
                    let hasErrors = false;
                    for (const id of requiredSelects) {
                        const sel = document.getElementById(id);
                        if (sel && (!sel.value || sel.value === '')) {
                            hasErrors = true;
                            sel.dispatchEvent(new Event('change'));
                        }
                    }
                    if (hasErrors) {
                        e.preventDefault();
                        return false;
                    }
                });
            }

            // Initialize TomSelect instances and attach validation styling
            const residenceSelect = document.getElementById('residence_municipality_select');
            let tsResidence = null;
            if (residenceSelect) {
                try {
                    tsResidence = new TomSelect(residenceSelect, {
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        maxOptions: 20,
                        maxItems: 1,
                        create: false,
                        preload: true,
                        load: function(query, callback) { fetchMunicipalities(query).then(items => callback(items)).catch(() => callback()); },
                        onChange: function(value) { residenceSelect.dispatchEvent(new Event('change')); }
                    });
                    addValidationStyles(residenceSelect, tsResidence, 'residence_municipality_error');
                    residenceSelect.style.display = 'none';
                } catch (err) { console.warn('TomSelect init failed (residence)', err); }
            }

            const deathSelect = document.getElementById('death_municipality_select');
            let tsDeath = null;
            if (deathSelect) {
                try {
                    tsDeath = new TomSelect(deathSelect, {
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        maxOptions: 20,
                        maxItems: 1,
                        create: false,
                        preload: true,
                        load: function(query, callback) { fetchMunicipalities(query).then(items => callback(items)).catch(() => callback()); },
                        onChange: function(value) { deathSelect.dispatchEvent(new Event('change')); }
                    });
                    addValidationStyles(deathSelect, tsDeath, 'death_municipality_error');
                    deathSelect.style.display = 'none';
                } catch (err) { console.warn('TomSelect init failed (death)', err); }
            }

            // Causes remote
            function fetchCauses(q) { return fetch('/api/causes/search?q=' + encodeURIComponent(q)).then(r => r.json()); }
            const causeSelect = document.getElementById('death_cause');
            let tsCause = null;
            if (causeSelect) {
                try {
                    tsCause = new TomSelect(causeSelect, {
                        valueField: 'id', labelField: 'name', searchField: 'name', maxOptions: 30, maxItems: 1, create: false,
                        load: function(query, callback) { if (!query.length) return callback(); fetchCauses(query).then(items => callback(items)).catch(() => callback()); }
                    });
                    addValidationStyles(causeSelect, tsCause, null);
                    causeSelect.style.display = 'none';
                } catch (err) { console.warn('TomSelect init failed (cause)', err); }
            }

            // Locations remote
            function fetchLocations(q) { return fetch('/api/locations/search?q=' + encodeURIComponent(q)).then(r => r.json()); }
            const locationSelect = document.getElementById('death_municipality_location');
            let tsLocation = null;
            if (locationSelect) {
                try {
                    tsLocation = new TomSelect(locationSelect, { valueField: 'id', labelField: 'name', searchField: 'name', maxOptions: 40, maxItems: 1, create: false,
                        load: function(query, callback) { fetchLocations(query).then(items => callback(items)).catch(() => callback()); }
                    });
                    addValidationStyles(locationSelect, tsLocation, null);
                    locationSelect.style.display = 'none';
                } catch (err) { console.warn('TomSelect init failed (location)', err); }
            }
        })();
    </script>
@endsection