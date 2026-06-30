<div class="bg-header text-white p-2 lg:p-3 w-full h-14 lg:h-16 font-sans flex items-center justify-start gap-4 lg:gap-6">
    <!-- Logos a la izquierda: Tamaulipas | Separador | Secretaría de Salud -->
    <a href="{{ auth()->check() ? (auth()->user()->hasAnyRole(['Administrador','Coordinador']) ? route('statistic.data') : route('reportes.index')) : route('login') }}" class="flex items-center gap-2.5 lg:gap-3 flex-shrink-0">
        <span class="flex h-10 w-28 lg:h-11 lg:w-32 items-center justify-end">
            <img src="{{ asset('images/tam_logo.png') }}" alt="Tamaulipas Gobierno del Estado" class="max-h-9 lg:max-h-10 max-w-full object-contain">
        </span>

        <span class="h-9 lg:h-10 w-px bg-[#bc955c]"></span>

        <span class="flex h-10 w-28 lg:h-11 lg:w-32 items-center justify-start">
            <img src="{{ asset('images/logo-secretaria.png') }}" alt="Secretaría de Salud" class="max-h-9 lg:max-h-10 max-w-full object-contain">
        </span>
        <!-- Texto opcional (oculto en pantallas pequeñas) -->
        <span class="hidden lg:inline text-lg lg:text-xl font-semibold ml-3 whitespace-nowrap">Sistema SEC-TAM</span>
    </a>

    <!-- Centro: Navegación (Reportes, Estadísticas) - oculto en mobile -->
    <div class="hidden md:flex items-center gap-8 lg:gap-12">
        <a href="{{ route('reportes.index') }}" class="text-base lg:text-base font-semibold hover:underline transition-all whitespace-nowrap">Reportes</a>
        <a href="{{ route('statistic.data') }}" class="text-base lg:text-base font-semibold hover:underline transition-all whitespace-nowrap">Estadísticas</a>
    </div>

    <!-- Espaciador flexible para empujar los botones a la derecha -->
    <div class="flex-1 ml-4 lg:ml-6"></div>

    <!-- Botones y usuario a la derecha -->
    <div class="flex items-center gap-3 lg:gap-4 flex-shrink-0">
       
        <!-- Notificaciones con dropdown -->
        <div class="relative flex items-center ml-3 lg:ml-4" x-data="{ openNotifications: false, notifications: [], unreadCount: 0 }" 
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
            <button @click="openNotifications = !openNotifications" 
                    class="relative p-1.5 lg:p-2 text-gray-300 hover:text-white transition-colors flex items-center justify-center hover:bg-white/10 rounded-lg">
                <ion-icon name="notifications" class="text-xl lg:text-2xl"></ion-icon>
                <!-- Indicador de notificaciones nuevas -->
                <span x-show="unreadCount > 0" 
                      x-text="unreadCount > 99 ? '99+' : unreadCount"
                      class="absolute -top-1.5 -right-2 min-w-[1rem] h-4 px-1 rounded-full bg-[#e5484d] text-white text-[10px] font-bold leading-none flex items-center justify-center border border-[#74103a] shadow-sm tabular-nums"></span>
            </button>

            <!-- Menú desplegable de notificaciones responsive -->
            <div x-show="openNotifications" 
                 @click.away="openNotifications = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="absolute right-0 top-full mt-2 w-80 lg:w-96 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                 style="display: none;">
                 
                <!-- Encabezado de notificaciones -->
                <div class="px-3 lg:px-4 py-2 lg:py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm lg:text-base font-semibold text-gray-800">Notificaciones</h3>
                        <p class="text-xs lg:text-sm text-gray-600 mt-1" x-text="unreadCount > 0 ? 'Tienes ' + unreadCount + ' notificación' + (unreadCount !== 1 ? 'es' : '') + ' nueva' + (unreadCount !== 1 ? 's' : '') : 'No hay notificaciones nuevas'"></p>
                    </div>
                    <button @click="
                        fetch('/notificaciones/marcar-todas-leidas', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                            .then(() => {
                                notifications.forEach(n => n.read = true);
                                unreadCount = 0;
                            });
                    " x-show="unreadCount > 0" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        Marcar todas
                    </button>
                </div>

                <!-- Lista de notificaciones -->
                <div class="max-h-60 lg:max-h-80 overflow-y-auto">
                    <template x-if="notifications.length === 0">
                        <div class="px-3 lg:px-4 py-6 text-center text-gray-500 text-sm">
                            No tienes notificaciones
                        </div>
                    </template>
                    
                    <template x-for="notif in notifications" :key="notif.id">
                        <div x-data="{ 
                            expanded: false, 
                            get isLong() { 
                                return ((notif.message && notif.message.length > 120) || (notif.publication_title && notif.publication_title.length > 50)); 
                            } 
                        }" class="block px-3 lg:px-4 py-2 lg:py-3 border-b border-gray-100 transition-colors" :class="!notif.read ? 'bg-blue-50 hover:bg-blue-100' : 'hover:bg-blue-100'">
                            <div class="flex justify-between items-start mb-1">
                                <a :href="'/reportes/publicaciones?publication=' + notif.publication_id" class="text-sm lg:text-sm font-medium text-gray-900 max-w-[18rem] hover:underline"
                                   @click="
                                       if (!notif.read) {
                                           fetch('/notificaciones/' + notif.id + '/marcar-leida', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                               .then(() => {
                                                   notif.read = true;
                                                   unreadCount = Math.max(0, unreadCount - 1);
                                               });
                                       }
                                   " x-text="notif.title"></a>
                                <span class="text-xs lg:text-sm text-gray-500 whitespace-nowrap ml-2" x-text="notif.time_ago"></span>
                            </div>

                            <div class="mb-1">
                                <p style="font-style: italic;" class="text-xs lg:text-sm text-gray-700 font-medium max-w-[18rem]" :class="expanded ? '' : 'truncate'" x-text="'PRUEBA_COMILLAS_' + notif.publication_title + '_PRUEBA'" x-show="notif.publication_title"></p>
                            </div>

                            <div>
                                <p class="text-xs lg:text-sm text-gray-600" :class="expanded ? '' : 'line-clamp-2'" x-text="notif.message"></p>
                            </div>

                            <div class="mt-1">
                                <button x-show="isLong" @click.stop.prevent="expanded = !expanded" class="text-xs text-slate-600 hover:text-slate-800 inline">
                                    <span x-text="expanded ? 'Ver menos' : 'Ver más'"></span>
                                </button>
                                          <a href="#" class="text-xs text-blue-500 hover:text-blue-700 inline"
                                              :class="isLong ? 'ml-3' : ''"
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
                <div class="px-3 lg:px-4 py-2 lg:py-3 border-t border-gray-200 bg-gray-50">
                    <a href="/reportes/publicaciones" class="text-xs lg:text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        Ver todas las publicaciones →
                    </a>
                </div>
            </div>
        </div>

        <!-- Avatar y nombre del usuario con dropdown responsive -->
        <div class="relative flex items-center gap-3 lg:gap-4" x-data="{ openProfile: false }">
            @php
                $headerGivenNames = trim(auth()->user()->name ?? '');
                $headerLastNames = trim(implode(' ', array_filter([auth()->user()->first_last_name, auth()->user()->second_last_name])));
                $headerFirstName = preg_split('/\s+/', $headerGivenNames, -1, PREG_SPLIT_NO_EMPTY)[0] ?? $headerGivenNames;
                $headerShortName = trim(implode(' ', array_filter([$headerFirstName, auth()->user()->first_last_name])));
                $headerFullName = trim(implode(' ', array_filter([auth()->user()->name, auth()->user()->first_last_name, auth()->user()->second_last_name])));
            @endphp
            <!-- Área clickeable para ir al perfil -->
            <a href="{{ route('usuario.miperfil') }}" class="flex items-center gap-2.5 lg:gap-3.5 flex-shrink-0" title="{{ $headerFullName }}">
                <!-- Avatar circular más grande -->
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-full overflow-hidden flex-shrink-0">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Foto de perfil" class="w-full h-full object-cover" data-profile-avatar>
                    @else
                        <img src="{{ asset('images/default_pfp.svg.png') }}" alt="Avatar predeterminado" class="w-full h-full object-cover" data-profile-avatar>
                    @endif
                </div>
                <!-- Nombre y cargo - se oculta en mobile, ancho fijo -->
                <div class="text-left hidden lg:block min-w-0 w-64 h-9 overflow-hidden">
                    <p class="text-base font-semibold truncate leading-tight">{{ $headerShortName ?: 'Usuario' }}</p>
                    <p class="text-sm text-gray-300 truncate leading-tight">{{ auth()->user()->position->name ?? 'Sin cargo' }}</p>
                </div>
            </a>

            <!-- Botón separado SOLO para abrir el menú -->
            <button @click="openProfile = !openProfile" class="ml-1 lg:ml-2 p-1.5 lg:p-2 text-gray-300 hover:text-white transition-colors hover:bg-white/10 rounded-lg flex-shrink-0">
                <ion-icon name="chevron-down" class="text-lg lg:text-xl"></ion-icon>
            </button>

            <!-- Menú desplegable del perfil responsive -->
            <div x-show="openProfile" 
                 @click.away="openProfile = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="absolute right-0 top-full mt-2 w-64 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl ring-1 ring-black/5 z-50"
                 style="display: none;">
                 
                <!-- Encabezado del menú -->
                <div class="border-b border-gray-100 bg-gray-50/90 px-4 py-3">
                    <p class="font-lora text-xs font-semibold uppercase text-[#611132]">Cuenta</p>
                    <div class="mt-1 font-lora leading-snug">
                        <p class="text-sm font-semibold text-[#404041] break-words [overflow-wrap:anywhere]">{{ $headerGivenNames ?: ($headerFullName ?: 'Usuario') }}</p>
                        @if($headerLastNames)
                            <p class="text-xs text-gray-600 break-words [overflow-wrap:anywhere]">{{ $headerLastNames }}</p>
                        @endif
                    </div>
                </div>

                <!-- Opciones del menú - SOLO Mi Perfil y Cerrar Sesión -->
                <div class="py-1.5">
                <a href="{{ route('usuario.miperfil') }}" class="mx-1.5 flex items-center gap-3 rounded-lg px-3 py-2.5 font-lora text-sm text-[#404041] transition-colors hover:bg-[#611132]/5 hover:text-[#611132] focus:bg-[#611132]/5 focus:outline-none group">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-100 text-gray-500 transition-colors group-hover:bg-[#611132]/10 group-hover:text-[#611132]">
                        <ion-icon name="person" class="text-base"></ion-icon>
                    </span>
                    <span class="font-semibold">Mi Perfil</span>
                </a>
                </div>

                <!-- Separador -->
                <div class="border-t border-gray-100"></div>

                <!-- Cerrar sesión (usar POST para evitar GET /logout) -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 font-lora text-sm font-semibold text-red-700 transition-colors hover:bg-red-50 focus:bg-red-50 focus:outline-none group text-left">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-50 text-red-600 transition-colors group-hover:bg-red-100">
                            <ion-icon name="log-out" class="text-base"></ion-icon>
                        </span>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
