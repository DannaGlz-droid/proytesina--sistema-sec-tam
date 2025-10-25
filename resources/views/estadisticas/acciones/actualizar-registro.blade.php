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
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) *</label>
                            <input name="name" type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Juan Diego"
                                   value="{{ old('name', $defuncion->name ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno *</label>
                            <input name="first_last_name" type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Nava"
                                   value="{{ old('first_last_name', $defuncion->first_last_name ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Edad *</label>
                            <input name="age" type="number" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: 34"
                                   value="{{ old('age', $defuncion->age ?? '') }}">
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno *</label>
                            <input name="second_last_name" type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Reyes"
                                   value="{{ old('second_last_name', $defuncion->second_last_name ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Sexo *</label>
                            <select name="sex" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione una opción</option>
                                <option value="M" {{ old('sex', $defuncion->sex ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('sex', $defuncion->sex ?? '') == 'F' ? 'selected' : '' }}>Femenino</option>
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
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de residencia *</label>
                            <select id="residence_municipality" name="residence_municipality_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione un municipio</option>
                                @foreach($municipalities as $m)
                                    <option value="{{ $m->id }}" {{ (int)old('residence_municipality_id', $defuncion->residence_municipality_id ?? 0) === $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de defunción *</label>
                            <select id="death_municipality" name="death_municipality_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione un municipio</option>
                                @foreach($municipalities as $m)
                                    <option value="{{ $m->id }}" {{ (int)old('death_municipality_id', $defuncion->death_municipality_id ?? 0) === $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Jurisdicción</label>
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
                            <select name="death_location_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione lugar</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ (int)old('death_location_id', $defuncion->death_location_id ?? 0) === $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            </select>
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
                            <select name="death_cause_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione una causa</option>
                                @foreach($causes as $c)
                                    <option value="{{ $c->id }}" {{ (int)old('death_cause_id', $defuncion->death_cause_id ?? 0) === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Build municipality -> jurisdiction id map. Support keys by id and by lowercased name.
            const muniToJur = @isset($municipalities) @json($municipalities->mapWithKeys(function($m){ return [$m->id => $m->jurisdiction_id, mb_strtolower($m->name) => $m->jurisdiction_id]; })) @else {} @endisset;
            const jurIdToName = @isset($jurisdictions) @json($jurisdictions->mapWithKeys(function($j){ return [$j->id => $j->name]; })) @else {} @endisset;

            const deathMuni = document.getElementById('death_municipality');
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
                const mid = deathMuni?.value || '';
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

            if (deathMuni) {
                deathMuni.addEventListener('change', setJurisdictionBasedOnMunicipality);
                setJurisdictionBasedOnMunicipality();
            }
        });
    </script>
@endsection