@php
    $headerReportsActive = request()->routeIs('reportes.*');
    $headerStatisticsActive = request()->routeIs('statistic.*', 'estadisticas.*');
@endphp

<header class="app-top-header bg-header text-white font-sans">
    <!-- Logos a la izquierda: Tamaulipas | Separador | Secretaría de Salud -->
    <a href="{{ auth()->check() ? (auth()->user()->hasAnyRole(['Administrador','Coordinador']) ? route('statistic.data') : route('reportes.index')) : route('login') }}" class="app-header-brand flex items-center gap-2.5 lg:gap-3 ml-3 lg:ml-6">
        <span class="app-header-logo app-header-logo-state flex h-10 w-28 lg:h-11 lg:w-32 items-center justify-end">
            <img src="{{ asset('images/tam_logo.png') }}" alt="Tamaulipas Gobierno del Estado" class="max-h-9 lg:max-h-10 max-w-full object-contain">
        </span>

        <span class="app-header-logo-separator h-9 lg:h-10 w-px bg-[#bc955c]"></span>

        <span class="app-header-logo app-header-logo-health flex h-10 w-28 lg:h-11 lg:w-32 items-center justify-start">
            <img src="{{ asset('images/logo-secretaria.png') }}" alt="Secretaría de Salud" class="max-h-9 lg:max-h-10 max-w-full object-contain">
        </span>
    </a>

    <!-- Botones y usuario a la derecha -->
    <div class="app-header-actions">
        <!-- Botones del header: mostrarlos siempre para evitar que se oculten por breakpoints -->
        <nav class="app-header-primary-nav" aria-label="Navegación principal">
            {{-- Reportes: Todos menos Invitado --}}
            @if(!auth()->user()->isGuest())
                <a href="{{ route('reportes.index') }}"
                   @class(['app-header-nav-link', 'is-active' => $headerReportsActive])
                   @if($headerReportsActive) aria-current="page" @endif>
                    Reportes
                </a>
            @endif
            
            {{-- Estadísticas: Solo Administrador y Coordinador --}}
            @if(auth()->user()->hasAnyRole(['Administrador', 'Coordinador']))
                <a href="{{ route('statistic.data') }}"
                   @class(['app-header-nav-link', 'is-active' => $headerStatisticsActive])
                   @if($headerStatisticsActive) aria-current="page" @endif>
                    Estadísticas
                </a>
            @endif
        </nav>
        
        <!-- Notificaciones con dropdown -->
        <div class="app-header-notifications relative" x-data="{ openNotifications: false, notifications: [], unreadCount: 0 }"
             @keydown.escape.window="openNotifications = false"
             x-init="
                 // Cargar notificaciones al iniciar
                 fetch('/notificaciones', {
                     headers: {
                         'Accept': 'application/json',
                         'X-Requested-With': 'XMLHttpRequest'
                     }
                 })
                     .then(res => res.json())
                     .then(data => {
                         notifications = data.notifications;
                         unreadCount = data.unread_count;
                     });
                 
                 // Recargar cada 30 segundos
                 setInterval(() => {
                     fetch('/notificaciones', {
                         headers: {
                             'Accept': 'application/json',
                             'X-Requested-With': 'XMLHttpRequest'
                         }
                     })
                         .then(res => res.json())
                         .then(data => {
                             notifications = data.notifications;
                             unreadCount = data.unread_count;
                         });
                 }, 30000);
             ">
            <!-- Botón de notificaciones -->
            <button type="button"
                    @click="openNotifications = !openNotifications"
                    :aria-expanded="openNotifications.toString()"
                    aria-haspopup="true"
                    aria-label="Abrir notificaciones"
                    class="app-header-icon-button">
                <i class="fa-solid fa-bell" aria-hidden="true"></i>
                <!-- Indicador de notificaciones nuevas -->
                <span x-show="unreadCount > 0" 
                      x-text="unreadCount > 99 ? '99+' : unreadCount"
                      class="absolute -top-1.5 -right-2 min-w-[1rem] h-4 px-1 rounded-full bg-[#e5484d] text-white text-[10px] font-bold leading-none flex items-center justify-center border border-[#74103a] shadow-sm tabular-nums"></span>
            </button>

            <!-- Menú desplegable de notificaciones responsive -->
            <div x-show="openNotifications" 
                 @click.away="openNotifications = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="app-notifications-panel absolute right-0 top-full mt-2 z-50"
                 style="display: none;">
                 
                <!-- Encabezado de notificaciones -->
                <div class="app-notifications-header">
                    <div>
                        <h3>Notificaciones</h3>
                        <p x-text="unreadCount > 0 ? 'Tienes ' + unreadCount + ' notificación' + (unreadCount !== 1 ? 'es' : '') + ' nueva' + (unreadCount !== 1 ? 's' : '') : 'No hay notificaciones nuevas'"></p>
                    </div>
                    <button @click="
                        fetch('/notificaciones/marcar-todas-leidas', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                            .then(() => {
                                notifications.forEach(n => n.read = true);
                                unreadCount = 0;
                            });
                    " x-show="unreadCount > 0" class="app-notifications-mark-all">
                        Marcar todas
                    </button>
                </div>

                <!-- Lista de notificaciones -->
                <div class="app-notifications-list">
                    <template x-if="notifications.length === 0">
                        <div class="app-notifications-empty">
                            No tienes notificaciones
                        </div>
                    </template>
                    
                    <template x-for="notif in notifications" :key="notif.id">
                        <div x-data="{ 
                            expanded: false, 
                            get isLong() { 
                                return ((notif.message && notif.message.length > 120) || (notif.publication_title && notif.publication_title.length > 50)); 
                            } 
                        }" class="app-notification-item" :class="{ 'is-unread': !notif.read }">
                            <!-- Encabezado: Tipo de acción y tiempo -->
                            <div class="app-notification-heading">
                                <div class="app-notification-title-wrap">
                                    <span x-show="!notif.read" class="app-notification-unread-dot" aria-hidden="true"></span>
                                    <a :href="'/reportes/publicaciones?publication=' + notif.publication_id" class="app-notification-title"
                                       @click="
                                           if (!notif.read) {
                                               fetch('/notificaciones/' + notif.id + '/marcar-leida', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                                   .then(() => {
                                                       notif.read = true;
                                                       unreadCount = Math.max(0, unreadCount - 1);
                                                   });
                                           }
                                       " x-text="notif.title"></a>
                                </div>
                                <span class="app-notification-time" x-text="notif.time_ago"></span>
                            </div>

                            <!-- Título del reporte en itálica y entre comillas francesas -->
                            <div class="app-notification-subject-wrap">
                                <p class="app-notification-subject" :class="expanded ? '' : 'truncate'">
                                    « <span x-text="notif.publication_title"></span> »
                                </p>
                            </div>

                            <!-- Mensaje/Descripción -->
                            <div>
                                <p class="app-notification-message" :class="expanded ? '' : 'line-clamp-2'" x-text="notif.message"></p>
                            </div>

                            <div class="app-notification-actions">
                                <button x-show="isLong" @click.stop.prevent="expanded = !expanded">
                                    <span x-text="expanded ? 'Ver menos' : 'Ver más'"></span>
                                </button>
                                <a href="#"
                                   @click.prevent="
                                       if (!notif.read) {
                                           fetch('/notificaciones/' + notif.id + '/marcar-leida', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                               .then(() => {
                                                   notif.read = true;
                                                   unreadCount = Math.max(0, unreadCount - 1);
                                               });
                                       }
                                       if (typeof window.openPublicationFromNotification === 'function') {
                                           window.openPublicationFromNotification(notif.publication_id, notif.comment_id);
                                       } else {
                                           window.location = '/reportes/publicaciones?publication=' + notif.publication_id + '&comment=' + (notif.comment_id || '');
                                       }
                                    ">Ir a reporte</a>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Pie del menú de notificaciones -->
                <div class="app-notifications-footer">
                    <a href="/reportes/publicaciones">
                        Ver todas las notificaciones <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Avatar y nombre del usuario con dropdown responsive -->
        <div class="app-header-profile relative" x-data="{ openProfile: false }" @keydown.escape.window="openProfile = false">
            @php
                $headerGivenNames = trim(auth()->user()->name ?? '');
                $headerLastNames = trim(implode(' ', array_filter([auth()->user()->first_last_name, auth()->user()->second_last_name])));
                $headerFirstName = preg_split('/\s+/', $headerGivenNames, -1, PREG_SPLIT_NO_EMPTY)[0] ?? $headerGivenNames;
                $headerShortName = trim(implode(' ', array_filter([$headerFirstName, auth()->user()->first_last_name])));
                $headerFullName = trim(implode(' ', array_filter([auth()->user()->name, auth()->user()->first_last_name, auth()->user()->second_last_name])));
            @endphp
            <!-- Área clickeable SOLO para ir al perfil -->
            <a href="{{ route('usuario.miperfil') }}" class="app-header-profile-summary" title="{{ $headerFullName }}">
                <!-- Avatar circular más grande -->
                <div class="w-9 h-9 lg:w-11 lg:h-11 rounded-full overflow-hidden flex-shrink-0 bg-[#611132] flex items-center justify-center">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Foto de perfil" class="w-full h-full object-cover" data-profile-avatar>
                    @else
                        <img src="{{ asset('images/default_pfp.svg.png') }}" alt="Avatar predeterminado" class="w-full h-full object-cover" data-profile-avatar>
                    @endif
                </div>
                <!-- Nombre y cargo - se oculta en mobile -->
                <div class="text-left hidden lg:block min-w-0 w-44 h-8 overflow-hidden">
                    <p class="text-sm font-semibold leading-tight truncate">{{ $headerShortName ?: 'Usuario' }}</p>
                    <p class="text-xs text-gray-300 leading-tight truncate">{{ auth()->user()->position->name ?? 'Sin cargo' }}</p>
                </div>
            </a>

            <!-- Botón separado SOLO para abrir el menú -->
            <button type="button"
                    @click="openProfile = !openProfile"
                    :aria-expanded="openProfile.toString()"
                    aria-haspopup="true"
                    aria-label="Abrir menú de la cuenta"
                    class="app-header-profile-trigger">
                <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>

            <!-- Menú desplegable del perfil responsive -->
            <div x-show="openProfile" 
                 @click.away="openProfile = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="app-profile-panel absolute right-0 top-full mt-2 z-50"
                 style="display: none;">
                 
                <!-- Encabezado del menú -->
                <div class="app-profile-panel-header">
                    <div class="app-profile-panel-avatar">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/default_pfp.svg.png') }}" alt="" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="app-profile-panel-identity">
                        <p>{{ $headerFullName ?: 'Usuario' }}</p>
                        <span>{{ auth()->user()->position->name ?? 'Sin cargo' }}</span>
                    </div>
                </div>

                <!-- Opciones del menú -->
                <div class="app-profile-menu">
                <a href="{{ route('usuario.miperfil') }}" class="app-profile-menu-item">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                    <span>Mi perfil</span>
                </a>

                {{-- Gestión de Usuarios: Solo Administrador --}}
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('user.user-gestion') }}" class="app-profile-menu-item">
                        <i class="fa-solid fa-users" aria-hidden="true"></i>
                        <span>Gestión de usuarios</span>
                    </a>
                @endif
                </div>

                <!-- Separador -->
                <div class="app-profile-menu-separator"></div>

                <!-- Cerrar sesión -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="app-profile-menu-item is-danger">
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                        <span>Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
