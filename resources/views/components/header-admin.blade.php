<div class="bg-header text-white p-2 lg:p-3 w-full h-14 lg:h-16 font-sans flex items-center justify-between">
    <!-- Logo del admin a la izquierda (reemplaza el punto) -->
    <div class="flex items-center space-x-3 ml-3 lg:ml-6">
        <!-- Logo admin: coloque el archivo en public/images/logo-admin.png -->
        <img src="{{ asset('images/logo-secretaria.png') }}" alt="Logo Administración" class="h-7 lg:h-9 object-contain">
    </div>

    <!-- Botones y usuario a la derecha -->
    <div class="flex items-center">
        <!-- Botones del header: mostrarlos siempre para evitar que se oculten por breakpoints -->
        <div class="flex items-center space-x-2">
            {{-- Reportes: Todos menos Invitado --}}
            @if(!auth()->user()->isGuest())
                <a href="{{ route('reportes.index') }}" class="text-sm lg:text-base bg-header hover:bg-[#9B4D6E] hover:text-[#E8CA8B] text-white py-1 lg:py-1 px-3 lg:px-4 rounded-md transition duration-200 ease-in-out mr-3 lg:mr-4 inline-flex items-center justify-center">
                    Reportes
                </a>
            @endif
            
            {{-- Estadísticas: Solo Administrador y Coordinador --}}
            @if(auth()->user()->hasAnyRole(['Administrador', 'Coordinador']))
                <a href="{{ route('statistic.data') }}" class="text-sm lg:text-base bg-header hover:bg-[#9B4D6E] hover:text-[#E8CA8B] text-white py-1 lg:py-1 px-3 lg:px-4 rounded-md transition duration-200 ease-in-out mr-3 lg:mr-4 inline-flex items-center justify-center">
                    Estadísticas
                </a>
            @endif
        </div>
        
        <!-- Notificaciones con dropdown -->
        <div class="relative flex items-center mr-3 lg:mr-4" x-data="{ openNotifications: false, notifications: [], unreadCount: 0 }" 
             x-init="
                 // Cargar notificaciones al iniciar
                 fetch('/notificaciones')
                     .then(res => res.json())
                     .then(data => {
                         notifications = data.notifications;
                         unreadCount = data.unread_count;
                     });
                 
                 // Recargar cada 30 segundos
                 setInterval(() => {
                     fetch('/notificaciones')
                         .then(res => res.json())
                         .then(data => {
                             notifications = data.notifications;
                             unreadCount = data.unread_count;
                         });
                 }, 30000);
             ">
            <!-- Botón de notificaciones -->
            <button @click="openNotifications = !openNotifications" 
                    class="relative p-1 lg:p-1 text-gray-300 hover:text-white transition-colors flex items-center justify-center">
                <ion-icon name="notifications" class="text-xl lg:text-2xl"></ion-icon>
                <!-- Indicador de notificaciones nuevas -->
                <span x-show="unreadCount > 0" 
                      x-text="unreadCount > 9 ? '9+' : unreadCount"
                      class="absolute -top-1 -right-1 bg-red-500 text-xs text-white rounded-full h-3 w-3 lg:h-4 lg:w-4 flex items-center justify-center text-[8px] lg:text-[10px]"></span>
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
                                <p class="text-xs lg:text-sm text-gray-700 font-medium max-w-[18rem]" :class="expanded ? '' : 'truncate'" x-text="notif.publication_title"></p>
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
        <div class="relative flex items-center" x-data="{ openProfile: false }">
            <!-- Área clickeable SOLO para ir al perfil -->
            <a href="{{ route('usuario.miperfil') }}" class="flex items-center space-x-0 lg:space-x-1">
                <!-- Avatar circular más grande -->
                <div class="w-9 h-9 lg:w-11 lg:h-11 rounded-full overflow-hidden">
                    <ion-icon name="person-circle" class="text-white text-4xl lg:text-[44px]"></ion-icon>
                </div>
                <!-- Nombre y cargo - se oculta en mobile -->
                <div class="text-left hidden lg:block">
                    <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-300">{{ auth()->user()->position->name ?? 'Sin cargo' }}</p>
                </div>
            </a>

            <!-- Botón separado SOLO para abrir el menú -->
            <button @click="openProfile = !openProfile" class="p-1 text-gray-300 hover:text-white transition-colors ml-1">
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
                 class="absolute right-0 top-full mt-2 w-56 lg:w-64 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-200"
                 style="display: none;">
                 
                <!-- Encabezado del menú -->
                <div class="px-3 lg:px-4 py-2 lg:py-3 border-b border-gray-100 bg-gray-50">
                    <p class="text-xs lg:text-sm font-medium text-gray-600">Cuenta</p>
                    <p class="text-xs text-gray-500 mt-1">{{ auth()->user()->name }}</p>
                </div>

                <!-- Opciones del menú -->
                <a href="{{ route('usuario.miperfil') }}" class="flex items-center px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-700 hover:bg-blue-50 transition-colors group">
                    <ion-icon name="person" class="text-gray-400 group-hover:text-blue-600 mr-2 lg:mr-3 text-sm lg:text-base"></ion-icon>
                    <span class="font-medium">Mi Perfil</span>
                </a>

                {{-- Gestión de Usuarios: Solo Administrador --}}
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('user.user-gestion') }}" class="flex items-center px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-700 hover:bg-blue-50 transition-colors group">
                        <ion-icon name="people" class="text-gray-400 group-hover:text-blue-600 mr-2 lg:mr-3 text-sm lg:text-base"></ion-icon>
                        <span class="font-medium">Gestión de Usuarios</span>
                    </a>
                @endif

                <!-- Separador -->
                <div class="border-t border-gray-100 my-1 lg:my-2"></div>

                <!-- Cerrar sesión -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-red-600 hover:bg-red-50 transition-colors group text-left">
                        <ion-icon name="log-out" class="text-red-400 group-hover:text-red-600 mr-2 lg:mr-3 text-sm lg:text-base"></ion-icon>
                        <span class="font-medium">Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>