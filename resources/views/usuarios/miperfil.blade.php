@extends('layouts.principal')
@section('title', 'Mi Perfil')
@section('content')

    @include('components.header-admin')
    @include('components.nav-usuario')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Mi Perfil</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Consulta tu información personal y datos de cuenta.</p>

        <div class="flex flex-col lg:flex-row gap-6">
            
            <!-- COLUMNA IZQUIERDA - FICHA DE USUARIO -->
            <div class="lg:w-80">
                <div class="border border-[#404041] rounded-lg p-6 bg-white">
                    <!-- FOTO DE PERFIL -->
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-24 h-24 bg-[#611132] rounded-full flex items-center justify-center mb-4">
                            <span class="text-white text-2xl font-lora font-bold">
                                @php
                                    $givenName = trim(auth()->user()->name ?? '');
                                    $firstLast = trim(auth()->user()->first_last_name ?? '');
                                    $secondLast = trim(auth()->user()->second_last_name ?? '');
                                    $fullName = trim(implode(' ', array_filter([$givenName, $firstLast, $secondLast])));
                                    $initials = '';
                                    if ($fullName !== '') {
                                        $parts = preg_split('/\s+/', $fullName);
                                        if (count($parts) >= 2) {
                                            $initials = mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1);
                                        } else {
                                            $initials = mb_substr($parts[0], 0, 2);
                                        }
                                    }
                                @endphp
                                {{ strtoupper($initials) }}
                            </span>
                        </div>
                        <h2 class="text-lg font-lora font-bold text-[#404041] text-center">{{ $fullName ?: auth()->user()->name }}</h2>
                        <p class="text-sm text-gray-600 font-lora text-center">{{ auth()->user()->position->name ?? 'Sin cargo' }}</p>
                       
                    </div>

                    <!-- INFORMACIÓN RÁPIDA -->
                    <div class="space-y-3 border-t border-gray-200 pt-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-envelope text-[#611132] text-sm"></i>
                            <div>
                                <p class="text-xs text-gray-500 font-lora">Correo</p>
                                <p class="text-sm text-[#404041] font-lora">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <i class="fas fa-briefcase text-[#611132] text-sm"></i>
                            <div>
                                <p class="text-xs text-gray-500 font-lora">Cargo</p>
                                <p class="text-sm text-[#404041] font-lora">{{ auth()->user()->position->name ?? 'Sin cargo' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <i class="fas fa-map-marker-alt text-[#611132] text-sm"></i>
                            <div>
                                <p class="text-xs text-gray-500 font-lora">Jurisdicción</p>
                                <p class="text-sm text-[#404041] font-lora">{{ auth()->user()->jurisdiction->name ?? 'Sin jurisdicción' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- BOTÓN CERRAR SESIÓN -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center justify-center gap-2">
                                <i class="fas fa-sign-out-alt text-xs"></i>
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA - INFORMACIÓN DETALLADA -->
            <div class="flex-1">
                <div class="border border-[#404041] rounded-lg bg-white">
                    
                    <!-- ENCABEZADO -->
                    <div class="border-b border-[#404041] p-4">
                        <h3 class="text-lg font-lora font-bold text-[#404041]">Información Personal</h3>
                        <p class="text-sm text-gray-600 font-lora mt-1">Datos personales y información de tu cuenta</p>
                    </div>

                    <!-- CONTENIDO -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información Básica -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información Básica</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Usuario</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border">{{ auth()->user()->username ?? explode('@', auth()->user()->email)[0] }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Nombre Completo</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border">{{ $fullName ?? auth()->user()->name }}</p>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información de Contacto</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Correo Electrónico</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border">{{ auth()->user()->email }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Teléfono</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border">{{ auth()->user()->phone ?? 'No especificado' }}</p>
                                </div>
                            </div>

                            <!-- Información Laboral -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información Laboral</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Cargo</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border">{{ auth()->user()->position->name ?? 'Sin cargo' }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Jurisdicción</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border">{{ auth()->user()->jurisdiction->name ?? 'Sin jurisdicción' }}</p>
                                </div>
                            </div>

                            <!-- Información de Cuenta -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información de Cuenta</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Fecha de Alta</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs text-gray-600 font-lora mb-1">Estado de la Cuenta</label>
                                        <div class="flex items-center gap-2">
                                            @if(auth()->user()->status ?? true)
                                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                <span class="text-sm text-green-600 font-lora">Activo</span>
                                            @else
                                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                                <span class="text-sm text-red-600 font-lora">Inactivo</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs text-gray-600 font-lora mb-1">Rol en el Sistema</label>
                                        <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">{{ auth()->user()->role->name ?? 'Usuario' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- NOTA INFORMATIVA ACTUALIZADA -->
                        <div class="mt-6 p-4 bg-gray-100 rounded-lg border border-gray-300">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-gray-600 mt-0.5"></i>
                                <div>
                                    <p class="text-sm text-[#404041] font-lora font-semibold">¿Necesitas ayuda?</p>
                                    <p class="text-xs text-gray-600 font-lora mt-1">
                                        Para cualquier modificación en tu información, contacta al administrador del sistema:<br>
                                        <strong>carlos.rodriguez@tamaulipas.gob.mx</strong> | <strong>+52 834 318 6300</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AGREGAR FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection