@extends('layouts.principal')
@section('title', 'Gestión de Usuarios')
@section('content')

    @include('components.header-admin')
    @include('components.nav-usuario')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO Y BOTÓN -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Gestión de Usuarios</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    Administre y gestione todos los usuarios del sistema con permisos y roles específicos.
                </p>
            </div>
            
            <!-- BOTÓN CREAR USUARIO -->
                <a href="{{ route('user.create') }}" class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto" title="Crear Usuario">
                <i class="fas fa-plus text-xs"></i>
                Crear Usuario
                </a>
            </div>
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="lg:w-80">
                        <x-filtros.usuarios :positions="$positions" :jurisdictions="$jurisdictions" :roles="$roles" />
                    </div>
                    <div class="flex-1">
                        <x-table-controls :items="$users">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-[#404041]">
                                <tr>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">ID</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Usuario</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Nombre</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">A. Paterno</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">A. Materno</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Correo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Teléfono</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Cargo</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Jurisdicción</th>
                                    <th scope="col" class="px-3 py-3 font-lora whitespace-nowrap text-xs">Fecha Alta</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Rol</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Estado</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Últ. Sesión</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">
                                        <span class="sr-only">Acciones</span>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @if(isset($users) && $users->isNotEmpty())
                                    @foreach($users as $user)
                                        <tr class="border-b hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                            <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $user->id }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->username }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->name }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->first_last_name }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->second_last_name }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->email }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->phone }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ optional($user->position)->name ?? '—' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ optional($user->jurisdiction)->name ?? '—' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->formatted_registration_date ?? '—' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                @php
                                                    $roleName = optional($user->role)->name ?? '—';
                                                    $roleLower = strtolower($roleName);
                                                    if (in_array($roleLower, ['administrador', 'admin'])) {
                                                        $roleClasses = 'bg-red-100 text-red-800';
                                                    } elseif (in_array($roleLower, ['usuario', 'user'])) {
                                                        $roleClasses = 'bg-green-100 text-green-800';
                                                    } elseif ($roleLower === 'invitado') {
                                                        $roleClasses = 'bg-gray-100 text-gray-800';
                                                    } elseif ($roleLower === 'operador') {
                                                        $roleClasses = 'bg-blue-100 text-blue-800';
                                                    } else {
                                                        $roleClasses = 'bg-yellow-100 text-yellow-800';
                                                    }
                                                @endphp
                                                <span class="{{ $roleClasses }} text-xs font-medium px-2 py-0.5 rounded-full">{{ $roleName }}</span>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                @php $isActive = (bool) $user->is_active; $statusText = $isActive ? 'Activo' : 'Inactivo'; @endphp
                                                <div class="flex items-center gap-1" role="status" aria-label="Estado: {{ $statusText }}" title="Estado: {{ $statusText }}">
                                                    <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-rose-500' }}" aria-hidden="true"></span>
                                                    <span class="text-xs">{{ $statusText }}</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->last_session_diff ?? '—' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <div class="flex items-center justify-end space-x-1">
                                                    <a href="{{ route('user.edit', $user->id) }}" class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar" aria-label="Editar usuario {{ $user->id }}">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </a>
                                                    <a href="{{ route('user.update-password', $user->id) }}" class="w-7 h-7 flex items-center justify-center rounded border border-[#C08400] text-[#C08400] hover:bg-[#C08400] hover:text-white transition-all duration-200" title="Cambiar Contraseña" aria-label="Cambiar contraseña usuario {{ $user->id }}">
                                                        <i class="fas fa-key text-xs"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('user.destroy', $user->id) }}" onsubmit="return confirm('¿Eliminar usuario? Esta acción no se puede deshacer.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar" aria-label="Eliminar usuario {{ $user->id }}">
                                                            <i class="fas fa-trash text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="14" class="px-3 py-4 text-center text-sm text-gray-500">No se encontraron usuarios.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </x-table-controls>
                </div>
            </div>
        </div>
    </div>

    <!-- AGREGAR FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        function clearSearch(btn) {
            try {
                const form = btn.closest('form');
                if (!form) return;
                const q = form.querySelector('input[name="q"]');
                if (q) q.value = '';
                form.submit();
            } catch (e) {
                console.error('clearSearch error', e);
            }
        }
    </script>
@endsection