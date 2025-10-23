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
            <button class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto">
                <a href="{{ route('user.create') }}">
                    <i class="fas fa-plus text-xs"></i>
                    Crear Usuario
                </a>
            </button>
        </div>

        <!-- Layout principal: Filtros + Tabla -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Columna Izquierda - Tus filtros personalizados -->
            <div class="lg:w-80">
                <x-filtros.usuarios />
            </div>

            <!-- Columna Derecha - Tabla Flowbite adaptada -->
            <div class="flex-1">
                <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden border border-[#404041]">
                    <!-- BARRA SUPERIOR: Búsqueda y Controles -->
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                        <div class="w-full md:w-1/2">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" id="table-search-users" 
                                       class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-full pl-10 p-2.5" 
                                       placeholder="Buscar por ID, usuario, nombre, correo...">
                            </div>
                        </div>
                        <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700 font-lora">Mostrar</span>
                                <select id="entries-per-page" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-16 p-2">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="text-sm text-gray-700 font-lora">entradas</span>
                            </div>
                        </div>
                    </div>

                    <!-- TABLA -->
                    <div class="overflow-x-auto">
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
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Jurisd.</th>
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Fecha Alta</th>
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
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $user->id }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->username }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->name }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->first_last_name }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->second_last_name }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->email }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->phone }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ optional($user->position)->name ?? '—' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ optional($user->jurisdiction)->name ?? '—' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap">{{ $user->registration_date ? ( $user->registration_date instanceof \DateTimeInterface ? $user->registration_date->format('d/m/Y') : \Carbon\Carbon::parse($user->registration_date)->format('d/m/Y') ) : '—' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap"><span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">{{ optional($user->role)->name ?? '—' }}</span></td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <div class="flex items-center gap-1">
                                                    <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                                    <span class="text-xs">{{ $user->is_active ? 'Activo' : 'Inactivo' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                @php
                                                    $last = $user->last_session;
                                                    if ($last) {
                                                        if ($last instanceof \Carbon\Carbon) {
                                                            echo $last->diffForHumans();
                                                        } elseif ($last instanceof \DateTimeInterface) {
                                                            echo \Carbon\Carbon::instance($last)->diffForHumans();
                                                        } else {
                                                            try { echo \Carbon\Carbon::parse($last)->diffForHumans(); } catch (\Throwable $e) { echo '—'; }
                                                        }
                                                    } else { echo '—'; }
                                                @endphp
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <div class="flex items-center justify-end space-x-1">
                                                    <a href="{{ route('user.edit', $user->id) }}" class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </a>
                                                    <button class="w-7 h-7 flex items-center justify-center rounded border border-[#C08400] text-[#C08400] hover:bg-[#C08400] hover:text-white transition-all duration-200" title="Cambiar Contraseña">
                                                        <a href="{{ route('user.update-password', $user->id) }}">
                                                            <i class="fas fa-key text-xs"></i>
                                                        </a>
                                                    </button>
                                                    <form method="POST" action="{{ route('user.destroy', $user->id) }}" onsubmit="return confirm('¿Eliminar usuario? Esta acción no se puede deshacer.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar">
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
                    </div>

                    <!-- PAGINACIÓN -->
                    <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4 border-t border-[#404041]">
                        <span class="text-sm font-normal text-gray-500 font-lora">
                            Mostrando 
                            <span class="font-semibold text-gray-900">1-4</span>
                            de
                            <span class="font-semibold text-gray-900">24</span>
                            entradas
                        </span>
                        <ul class="inline-flex items-stretch -space-x-px">
                            <li>
                                <a href="#" class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">2</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">3</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">4</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">5</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- AGREGAR FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection