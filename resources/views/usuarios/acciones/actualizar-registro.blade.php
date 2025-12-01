@extends('layouts.principal')
@section('title', 'Actualizar Usuario')
@section('content')

    @include('components.header-admin')
    @include('components.nav-usuario')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Actualizar datos de usuario</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Modifique los campos necesarios y guarde los cambios.</p>

        <!-- Cuadro del formulario responsive -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">
            
            <form action="{{ route('user.update', $user->id) }}" method="POST">
                @method('PUT')
                @csrf

            <!-- Sección 1: Información del usuario -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="person-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información del usuario</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label for="name" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) <span class="text-red-600">*</span></label>
                            <input id="name" name="name" type="text" required minlength="2" maxlength="191"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: María Elena"
                                value="{{ old('name', $user->name) }}">
                            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="first_last_name" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno <span class="text-red-600">*</span></label>
                            <input id="first_last_name" name="first_last_name" type="text" required minlength="2" maxlength="191"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: García"
                                value="{{ old('first_last_name', $user->first_last_name) }}">
                            @error('first_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label for="second_last_name" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno</label>
                            <input id="second_last_name" name="second_last_name" type="text" minlength="2" maxlength="191"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: López"
                                value="{{ old('second_last_name', $user->second_last_name) }}">
                            @error('second_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                     <label for="email" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Correo electrónico <span class="text-red-600">*</span></label>
                                     <input id="email" name="email" type="email" autocomplete="email" required maxlength="255"
                                         class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                         placeholder="Ej: usuario@ejemplo.com"
                                         value="{{ old('email', $user->email) }}">
                                     @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                     <label for="phone" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Teléfono</label>
                                     <input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" maxlength="20" pattern="[0-9+\-\(\)\s]{8,20}"
                                         class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                         placeholder="Ej: 8123456789"
                                         value="{{ old('phone', $user->phone) }}">
                                     @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 2: Información laboral -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="business-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información laboral</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label for="position_select" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Cargo <span class="text-red-600">*</span></label>
                            <select id="position_select" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select" name="position_id">
                                <option value="">Seleccione un cargo</option>
                                @if(isset($positions))
                                    @foreach($positions as $p)
                                        <option value="{{ $p->id }}" {{ old('position_id', $user->position_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('position_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label for="jurisdiction_id" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Jurisdicción <span class="text-red-600">*</span></label>
                            <select id="jurisdiction_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="jurisdiction_id" required>
                                <option value="">Seleccione una jurisdicción</option>
                                @if(isset($jurisdictions))
                                    @foreach($jurisdictions as $j)
                                        <option value="{{ $j->id }}" {{ old('jurisdiction_id', $user->jurisdiction_id) == $j->id ? 'selected' : '' }}>{{ $j->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('jurisdiction_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Línea separadora -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Sección 3: Configuración de cuenta -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="settings-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Configuración de cuenta</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label for="username" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Usuario <span class="text-red-600">*</span></label>
                <input id="username" name="username" type="text" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_.-]+"
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: mgarcia"
                    value="{{ old('username', $user->username ?? '') }}">
                        </div>
                        
                        <!-- Switch para estado activo/inactivo -->
                        <div class="pt-2">
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-3 font-lora">Estado de la cuenta</label>
                            <div class="flex items-center">
                                <label class="switch">
                                    <input type="checkbox" 
                                           name="is_active"
                                           value="1"
                                           {{ (old('is_active', $user->is_active ?? 1) == 1) ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                                <span class="ml-3 text-sm font-medium text-[#404041] font-lora" id="toggleLabel">
                                    {{ (old('is_active', $user->is_active ?? 1) == 1) ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 font-lora">
                                <i class="fas fa-info-circle text-[#611132] mr-1"></i>
                                Cuando este inactivo, el usuario no podra iniciar sesión en el sistema.
                            </p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label for="role_id" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Rol <span class="text-red-600">*</span></label>
                            <select id="role_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="role_id" required>
                                <option value="">Seleccione un rol</option>
                                @if(isset($roles))
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}" {{ old('role_id', $user->role_id ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Script para convertir errores de Laravel en tooltips HTML5 (igual que en registro) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($errors->any())
                const errors = @json($errors->messages());
                
                // Mapear nombres de campos a sus IDs en el DOM
                const fieldMap = {
                    'name': 'name',
                    'first_last_name': 'first_last_name',
                    'second_last_name': 'second_last_name',
                    'email': 'email',
                    'phone': 'phone',
                    'username': 'username',
                    'password': 'password',
                    'password_confirmation': 'password_confirmation',
                    'role_id': 'role_id',
                    'position_id': 'position_select',
                    'jurisdiction_id': 'jurisdiction_id'
                };

                // Aplicar setCustomValidity a cada campo con error
                Object.keys(errors).forEach(fieldName => {
                    const fieldId = fieldMap[fieldName];
                    if (fieldId) {
                        const field = document.getElementById(fieldId);
                        if (field && errors[fieldName][0]) {
                            field.setCustomValidity(errors[fieldName][0]);
                            // Hacer que el campo muestre el tooltip
                            field.reportValidity();
                        }
                    }
                });

                // Scroll al primer campo con error
                const firstErrorField = Object.keys(errors)[0];
                if (firstErrorField && fieldMap[firstErrorField]) {
                    const field = document.getElementById(fieldMap[firstErrorField]);
                    if (field) {
                        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        field.focus();
                    }
                }
            @endif

            // Limpiar validación personalizada cuando el usuario empieza a escribir
            const allInputs = document.querySelectorAll('input, select');
            allInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.setCustomValidity('');
                });
            });
        });
    </script>

            <!-- Sección de advertencia para usuarios inactivos -->
            <div id="advertenciaInactivo" class="mb-6 p-4 border border-yellow-300 bg-yellow-50 rounded-lg {{ (old('is_active', $user->is_active ?? 1) == 1) ? 'hidden' : '' }}">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-lg mt-0.5 mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800 text-sm font-lora">Usuario inactivo</h3>
                        <p class="text-yellow-700 text-xs mt-1 font-lora">
                            Este usuario está marcado como inactivo. No podrá iniciar sesión en el sistema hasta que sea reactivado.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- USAR COMPONENTE DE BOTONES PERSONALIZADO -->
            <x-form-buttons 
                primaryText="Actualizar registro"
                secondaryText=""
                tertiaryText="Volver al listado"
                tertiaryHref="{{ route('user.user-gestion') }}"
                primaryType="submit"
            />
            </form>
        </div>
    </div>

    <!-- Estilos para el switch -->
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #10B981;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>

    <!-- Script para el switch de estado activo/inactivo -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleCheckbox = document.querySelector('input[name="is_active"]');
            const toggleLabel = document.getElementById('toggleLabel');
            const advertenciaInactivo = document.getElementById('advertenciaInactivo');
            if (!toggleCheckbox) return;
            
            toggleCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    toggleLabel.textContent = 'Activo';
                    advertenciaInactivo.classList.add('hidden');
                } else {
                    toggleLabel.textContent = 'Inactivo';
                    advertenciaInactivo.classList.remove('hidden');
                }
            });
        });
    </script>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Tom Select (only used for position select here) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Make Tom Select control visually match your existing Tailwind input styles (copied from registro) */
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
        #position_select + .ts-control { padding: 8px 12px !important; height: auto !important; min-height: 36px !important; transform: none !important; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pos = document.getElementById('position_select');
            if (pos) {
                try {
                    new TomSelect(pos, {
                        valueField: 'value',
                        labelField: 'text',
                        searchField: ['text'],
                        create: false,
                        maxItems: 1,
                        preload: false,
                        maxOptions: 50
                    });
                } catch (e) {
                    console.warn('TomSelect init failed for #position_select', e);
                }
            }
        });
    </script>

    <!-- Incluir Font Awesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection