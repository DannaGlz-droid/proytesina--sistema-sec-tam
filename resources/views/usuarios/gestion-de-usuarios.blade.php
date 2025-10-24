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

        <!-- Layout principal: Filtros + Tabla -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Columna Izquierda - Tus filtros personalizados -->
            <div class="lg:w-80">
                <x-filtros.usuarios :positions="$positions" :jurisdictions="$jurisdictions" :roles="$roles" />
            </div>

            <!-- Columna Derecha - Tabla Flowbite adaptada -->
            <div class="flex-1">
                <div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden border border-[#404041]">
                    <!-- BARRA SUPERIOR: Búsqueda y Controles MEJORADA -->
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 p-4">
                        <form method="GET" action="{{ route('user.user-gestion') }}" class="w-full flex items-center gap-3">
                        <!-- BÚSQUEDA MÁS ANCHA (responsive: md=1/2, lg=2/3) -->
                        <div class="w-full md:w-1/2 lg:w-2/3">
                                <div class="relative flex-1 w-full">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fas fa-search text-gray-400 text-sm"></i>
                                    </div>
                                    <input type="text" name="q" value="{{ request('q') }}" id="table-search-users"
                                        aria-label="Buscar usuarios"
                                        class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-full pl-10 pr-24 p-2.5 max-w-full"
                                        placeholder="Buscar por ID, usuario, nombre, correo...">

                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
                                        <!-- Submit button -->
                                        <button type="submit" class="h-8 px-3 bg-[#611132] text-white rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-150" title="Buscar">
                                            <i class="fas fa-search text-xs"></i>
                                        </button>

                                        <!-- Clear button: mostrar solo si hay búsqueda activa -->
                                            @if(request('q'))
                                            <button type="button" onclick="clearSearch(this)" class="h-8 px-2 bg-white border border-[#404041] text-gray-700 rounded-lg text-xs hover:bg-gray-100" title="Limpiar búsqueda" aria-label="Limpiar búsqueda">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                        </div>

                        <!-- CONTROLES DERECHA - SEPARADOS -->
                        <!-- CONTROLES DERECHA - SEPARADOS -->
                        <div class="flex items-center space-x-3">
    <!-- Mostrar -->
    <div class="flex items-center space-x-2">
        <span class="text-sm text-gray-700 font-lora">Mostrar</span>
        <select name="per_page" onchange="this.form.submit()" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-20 p-2">
            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
        </select>
    </div>
    
    <!-- Ordenar -->
    <div class="flex items-center space-x-2 md:ml-3 lg:ml-6">
        <span class="text-sm text-gray-700 font-lora">Ordenar</span>
        <select name="sort" onchange="this.form.submit()" class="bg-white border border-[#404041] rounded-lg text-sm p-2 min-w-[180px]">
            <option value="registration_date_desc" {{ request('sort', 'registration_date_desc') === 'registration_date_desc' ? 'selected' : '' }}>Fecha alta: Recientes</option>
            <option value="registration_date_asc" {{ request('sort') === 'registration_date_asc' ? 'selected' : '' }}>Fecha alta: Antiguos</option>
            <option value="username_asc" {{ request('sort') === 'username_asc' ? 'selected' : '' }}>Usuario: A–Z</option>
            <option value="username_desc" {{ request('sort') === 'username_desc' ? 'selected' : '' }}>Usuario: Z–A</option>
            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nombre: A–Z</option>
            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nombre: Z–A</option>
        </select>
    </div>
                        </div>
                        </form>
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
                                    <th scope="col" class="px-3 py-2 font-lora whitespace-nowrap text-xs">Jurisdicción</th>
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
                                        {{-- Zebra striping: alternate white / gray rows for readability --}}
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
                                                        // default / unknown roles
                                                        $roleClasses = 'bg-yellow-100 text-yellow-800';
                                                    }
                                                @endphp
                                                <span class="{{ $roleClasses }} text-xs font-medium px-2 py-0.5 rounded-full">{{ $roleName }}</span>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                @php $isActive = (bool) $user->is_active; $statusText = $isActive ? 'Activo' : 'Inactivo'; @endphp
                                                <div class="flex items-center gap-1" role="status" aria-label="Estado: {{ $statusText }}" title="Estado: {{ $statusText }}">
                                                    {{-- color indicators: keep green/red semantics but use slightly different shades for contrast --}}
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
                    </div>

                    <!-- PAGINACIÓN -->
                    <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4 border-t border-[#404041]">
                        <span class="text-sm font-normal text-gray-500 font-lora">
                            Mostrando 
                            <span class="font-semibold text-gray-900">{{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }}</span>
                            de
                            <span class="font-semibold text-gray-900">{{ $users->total() }}</span>
                            entradas
                        </span>

                        <div>
                            {{-- Paginación personalizada con el mismo estilo previo; enlaces dinámicos --}}
                            @php
                                $lastPage = $users->lastPage();
                                $current = $users->currentPage();
                                $query = request()->except('page');
                            @endphp

                            <ul class="inline-flex items-stretch -space-x-px">
                                {{-- Previous --}}
                                <li>
                                    @if($users->onFirstPage())
                                        <span aria-disabled="true" class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 cursor-default">
                                            <i class="fas fa-chevron-left text-xs" aria-hidden="true"></i>
                                        </span>
                                    @else
                                        <a href="{{ $users->previousPageUrl() }}" aria-label="Página anterior" class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                            <i class="fas fa-chevron-left text-xs" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </li>

                                {{-- Pages (ventana máxima de 5 botones) --}}
                                @php
                                    $maxButtons = 5;
                                @endphp

                                @if($lastPage <= $maxButtons)
                                    @for ($i = 1; $i <= $lastPage; $i++)
                                        @php $isActive = $i === $current; @endphp
                                        <li>
                                            @if($isActive)
                                                <a href="{{ $users->url($i) }}" aria-current="page" aria-label="Página {{ $i }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">{{ $i }}</a>
                                            @else
                                                <a href="{{ $users->url($i) }}" aria-label="Ir a la página {{ $i }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $i }}</a>
                                            @endif
                                        </li>
                                    @endfor
                                @else
                                    {{-- Caso inicio: current cerca del inicio -> mostrar 1..5 --}}
                                    @if($current <= 3)
                                        @for($i = 1; $i <= 5; $i++)
                                            @php $isActive = $i === $current; @endphp
                                            <li>
                                                @if($isActive)
                                                    <a href="{{ $users->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">{{ $i }}</a>
                                                @else
                                                    <a href="{{ $users->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $i }}</a>
                                                @endif
                                            </li>
                                        @endfor
                                        <li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>
                                        <li><a href="{{ $users->url($lastPage) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $lastPage }}</a></li>

                                    {{-- Caso final: current cerca del final -> mostrar last-4 .. last --}}
                                    @elseif($current >= $lastPage - 2)
                                        <li><a href="{{ $users->url(1) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>
                                        <li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>
                                        @for($i = $lastPage - 4; $i <= $lastPage; $i++)
                                            @php $isActive = $i === $current; @endphp
                                            <li>
                                                @if($isActive)
                                                    <a href="{{ $users->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">{{ $i }}</a>
                                                @else
                                                    <a href="{{ $users->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $i }}</a>
                                                @endif
                                            </li>
                                        @endfor

                                    {{-- Caso medio: mostrar 1, ..., current-2..current+2, ..., last --}}
                                    @else
                                        <li><a href="{{ $users->url(1) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>
                                        <li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>

                                        @for($i = $current - 2; $i <= $current + 2; $i++)
                                            @php $isActive = $i === $current; @endphp
                                            <li>
                                                @if($isActive)
                                                    <a href="{{ $users->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">{{ $i }}</a>
                                                @else
                                                    <a href="{{ $users->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $i }}</a>
                                                @endif
                                            </li>
                                        @endfor

                                        <li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>
                                        <li><a href="{{ $users->url($lastPage) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $lastPage }}</a></li>
                                    @endif
                                @endif

                                {{-- Next --}}
                                <li>
                                    @if($users->hasMorePages())
                                        <a href="{{ $users->nextPageUrl() }}" aria-label="Página siguiente" class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                            <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
                                        </a>
                                    @else
                                        <span aria-disabled="true" class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 cursor-default">
                                            <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </nav>
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