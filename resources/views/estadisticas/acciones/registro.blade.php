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
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                        <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) *</label>
                            <input name="name" type="text" value="{{ old('name') }}"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Juan Diego">
                            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno *</label>
                            <input name="first_last_name" type="text" value="{{ old('first_last_name') }}"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Nava">
                            @error('first_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Edad</label>
                            <input name="age" type="number" value="{{ old('age') }}"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: 34">
                            @error('age') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                        <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno</label>
                            <input name="second_last_name" type="text" value="{{ old('second_last_name') }}"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: Reyes">
                            @error('second_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Sexo *</label>
                            <select name="sex" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione una opción</option>
                                <option value="masculino" {{ old('sex') === 'masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="femenino" {{ old('sex') === 'femenino' ? 'selected' : '' }}>Femenino</option>
                            </select>
                            @error('sex') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de residencia</label>
                            <select id="residence_municipality" name="residence_municipality_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione un municipio</option>
                                @foreach($municipalities as $m)
                                    <option value="{{ $m->id }}" {{ old('residence_municipality_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                            @error('residence_municipality_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Municipio de defunción</label>
                            <select id="death_municipality" name="death_municipality_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora">
                                <option value="">Seleccione un municipio</option>
                                @foreach($municipalities as $m)
                                    <option value="{{ $m->id }}" {{ old('death_municipality_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                            @error('death_municipality_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                        <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Jurisdicción</label>
                            {{-- Hidden input that will be submitted. The visible select is disabled so users can't change it directly. --}}
                            <input type="hidden" id="jurisdiction_input" name="jurisdiction_id" value="{{ old('jurisdiction_id') ?? '' }}">
                            <select id="jurisdiction" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" disabled>
                                <option value="" class="text-gray-300">Pendiente (seleccione municipio)</option>
                                @foreach($jurisdictions as $j)
                                    <option value="{{ $j->id }}" {{ old('jurisdiction_id') == $j->id ? 'selected' : '' }}>{{ $j->name }}</option>
                                @endforeach
                            </select>
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Causa de la defunción *</label>
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Fecha de defunción *</label>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Build a map municipality_id -> jurisdiction_id
            const muniToJur = @json($municipalities->mapWithKeys(function($m){ return [$m->id => $m->jurisdiction_id]; }));

            const deathMuni = document.getElementById('death_municipality');
            const jurisdiction = document.getElementById('jurisdiction');
            const hiddenJur = document.getElementById('jurisdiction_input');
            const form = document.getElementById('death-register-form');

            // Helper classes to show placeholder style when empty
            const placeholderClasses = ['text-gray-500'];

            function applyPlaceholderStyle() {
                if (!jurisdiction) return;
                // When there's no real value, show placeholder style on the visible select
                if (!hiddenJur || !hiddenJur.value) {
                    jurisdiction.classList.add(...placeholderClasses);
                } else {
                    jurisdiction.classList.remove(...placeholderClasses);
                }
            }

            // If there is an old value (validation error), ensure the visible select shows it and the hidden contains it
            if (hiddenJur && hiddenJur.value) {
                try { jurisdiction.value = hiddenJur.value; } catch (err) {}
                if (jurisdiction) jurisdiction.disabled = true;
            } else {
                // keep jurisdiction disabled and empty on initial load
                if (jurisdiction) {
                    jurisdiction.value = '';
                    jurisdiction.disabled = true;
                }
            }
            // Apply placeholder visual state on load
            applyPlaceholderStyle();

            function setJurisdictionBasedOnMunicipality() {
                const mid = deathMuni?.value || '';
                if (mid && muniToJur[mid]) {
                    const jid = muniToJur[mid];
                    if (jurisdiction) jurisdiction.value = jid;
                    if (hiddenJur) hiddenJur.value = jid;
                    if (jurisdiction) jurisdiction.disabled = true; // always locked
                } else {
                    // No mapping: keep jurisdiction locked and clear value (user cannot change it)
                    if (jurisdiction) jurisdiction.value = '';
                    if (hiddenJur) hiddenJur.value = '';
                    if (jurisdiction) jurisdiction.disabled = true;
                }
                // Update visual placeholder state after changing values
                applyPlaceholderStyle();
            }

            if (deathMuni) {
                deathMuni.addEventListener('change', setJurisdictionBasedOnMunicipality);
                setJurisdictionBasedOnMunicipality();
            }

            window.clearDeathForm = function(e) {
                try { e.preventDefault(); } catch (err) {}
                if (!form) return;
                form.reset();
                // Keep jurisdiction locked and clear its hidden value after reset
                if (jurisdiction) {
                    jurisdiction.disabled = true;
                    jurisdiction.value = '';
                }
                if (hiddenJur) {
                    hiddenJur.value = '';
                }
                // Update visual placeholder state after reset
                applyPlaceholderStyle();
            }
        });
    </script>
@endsection