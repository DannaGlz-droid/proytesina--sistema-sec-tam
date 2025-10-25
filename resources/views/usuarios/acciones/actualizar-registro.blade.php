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
                            <label name="name" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s)</label>
                            <input name="name" type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: María Elena"
                                   value="{{ old('name', $user->name) }}">
                        </div>
                        <div>
                            <label name="first_last_name" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno</label>
                            <input name="first_last_name" type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: García"
                                   value="{{ old('first_last_name', $user->first_last_name) }}">
                        </div>
                        <div>
                            <label name="email" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Correo electrónico</label>
                            <input name="email" type="email" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: usuario@ejemplo.com"
                                   value="{{ old('email', $user->email) }}">
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label name="second_last_name" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno</label>
                            <input name="second_last_name" type="text" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: López"
                                   value="{{ old('second_last_name', $user->second_last_name) }}">
                        </div>
                        <div>
                            <label name="phone" class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Teléfono</label>
                            <input name="phone" type="tel" 
                                   class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                   placeholder="Ej: 8123456789"
                                   value="{{ old('phone', $user->phone) }}">
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Cargo *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="position_id">
                                <option value="">Seleccione un cargo</option>
                                @if(isset($positions))
                                    @foreach($positions as $p)
                                        <option value="{{ $p->id }}" {{ old('position_id', $user->position_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Jurisdicción *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="jurisdiction_id">
                                <option value="">Seleccione una jurisdicción</option>
                                @if(isset($jurisdictions))
                                    @foreach($jurisdictions as $j)
                                        <option value="{{ $j->id }}" {{ old('jurisdiction_id', $user->jurisdiction_id) == $j->id ? 'selected' : '' }}>{{ $j->name }}</option>
                                    @endforeach
                                @endif
                            </select>
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Usuario *</label>
                <input name="username" type="text" 
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Rol *</label>
                            <select class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="role_id">
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
    
    <!-- Incluir Font Awesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection