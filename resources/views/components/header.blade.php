<div class="bg-header text-white p-2 lg:p-3 w-full h-14 lg:h-16 font-sans flex items-center justify-between">
    <!-- Logo a la izquierda -->
    <div class="flex items-center space-x-3 ml-3 lg:ml-6">
        <!-- Logo de la Secretaría de Salud: coloque el archivo en public/images/logo-secretaria.png -->
        <img src="{{ asset('images/logo-secretaria.png') }}" alt="Logo Secretaría de Salud" class="h-7 lg:h-9 object-contain">
        <!-- Texto opcional (oculto en pantallas pequeñas) -->
        <span class="hidden lg:inline text-lg lg:text-xl font-semibold">Sistema SEC-TAM</span>
    </div>

    <!-- Botones y usuario a la derecha -->
    <div class="flex items-center">
       
        <!-- Notificaciones con dropdown -->
        <div class="relative flex items-center mr-3 lg:mr-4" x-data="{ openNotifications: false }">
            <!-- Botón de notificaciones -->
            <button @click="openNotifications = !openNotifications" 
                    class="relative p-1 lg:p-1 text-gray-300 hover:text-white transition-colors flex items-center justify-center">
                <ion-icon name="notifications" class="text-xl lg:text-2xl"></ion-icon>
                <!-- Indicador de notificaciones nuevas -->
                <span class="absolute -top-1 -right-1 bg-red-500 text-xs text-white rounded-full h-3 w-3 lg:h-4 lg:w-4 flex items-center justify-center text-[8px] lg:text-[10px]">3</span>
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
                <div class="px-3 lg:px-4 py-2 lg:py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm lg:text-base font-semibold text-gray-800">Notificaciones</h3>
                    <p class="text-xs lg:text-sm text-gray-600 mt-1">Tienes 3 notificaciones nuevas</p>
                </div>

                <!-- Lista de notificaciones -->
                <div class="max-h-60 lg:max-h-80 overflow-y-auto">
                    <!-- Notificación 1 -->
                    <a href="#" class="block px-3 lg:px-4 py-2 lg:py-3 hover:bg-blue-50 border-b border-gray-100 transition-colors">
                        <div class="flex justify-between items-start mb-1">
                            <p class="text-sm lg:text-base font-medium text-gray-900">Nuevo registro pendiente</p>
                            <span class="text-xs lg:text-sm text-gray-500 whitespace-nowrap">Hace 5 min</span>
                        </div>
                        <p class="text-xs lg:text-sm text-gray-700">Juan Pérez ha registrado una nueva defunción</p>
                    </a>

                    <!-- Notificación 2 -->
                    <a href="#" class="block px-3 lg:px-4 py-2 lg:py-3 hover:bg-blue-50 border-b border-gray-100 transition-colors">
                        <div class="flex justify-between items-start mb-1">
                            <p class="text-sm lg:text-base font-medium text-gray-900">Solicitud de certificado</p>
                            <span class="text-xs lg:text-sm text-gray-500 whitespace-nowrap">Hace 1 hora</span>
                        </div>
                        <p class="text-xs lg:text-sm text-gray-700">María García solicitó un certificado de nacimiento</p>
                    </a>

                    <!-- Notificación 3 -->
                    <a href="#" class="block px-3 lg:px-4 py-2 lg:py-3 hover:bg-blue-50 transition-colors">
                        <div class="flex justify-between items-start mb-1">
                            <p class="text-sm lg:text-base font-medium text-gray-900">Actualización del sistema</p>
                            <span class="text-xs lg:text-sm text-gray-500 whitespace-nowrap">Ayer</span>
                        </div>
                        <p class="text-xs lg:text-sm text-gray-700">El sistema se actualizará este sábado</p>
                    </a>
                </div>

                <!-- Pie del menú de notificaciones -->
                <div class="px-3 lg:px-4 py-2 lg:py-3 border-t border-gray-200 bg-gray-50">
                    <a href="/notificaciones" class="text-xs lg:text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        Ver todas las notificaciones →
                    </a>
                </div>
            </div>
        </div>

        <!-- Avatar y nombre del usuario con dropdown responsive -->
        <div class="relative flex items-center" x-data="{ openProfile: false }">
            <!-- Área clickeable SOLO para ir al perfil -->
            <a href="/mi-perfil" class="flex items-center space-x-0 lg:space-x-1">
                <!-- Avatar circular más grande -->
                <div class="w-9 h-9 lg:w-11 lg:h-11 rounded-full overflow-hidden">
                    <ion-icon name="person-circle" class="text-white text-4xl lg:text-[44px]"></ion-icon>
                </div>
                <!-- Nombre y cargo - se oculta en mobile -->
                <div class="text-left hidden lg:block">
                    <p class="text-sm font-semibold">Lic. Carlos Rodríguez</p>
                    <p class="text-xs text-gray-300">Jefe de Jurisdicción</p>
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
                    <p class="text-xs text-gray-500 mt-1">Lic. Carlos Rodríguez</p>
                </div>

                <!-- Opciones del menú - SOLO Mi Perfil y Cerrar Sesión -->
                <a href="/mi-perfil" class="flex items-center px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-gray-700 hover:bg-blue-50 transition-colors group">
                    <ion-icon name="person" class="text-gray-400 group-hover:text-blue-600 mr-2 lg:mr-3 text-sm lg:text-base"></ion-icon>
                    <span class="font-medium">Mi Perfil</span>
                </a>

                <!-- Separador -->
                <div class="border-t border-gray-100 my-1 lg:my-2"></div>

                <!-- Cerrar sesión -->
                <a href="/logout" class="flex items-center px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-red-600 hover:bg-red-50 transition-colors group">
                    <ion-icon name="log-out" class="text-red-400 group-hover:text-red-600 mr-2 lg:mr-3 text-sm lg:text-base"></ion-icon>
                    <span class="font-medium">Cerrar Sesión</span>
                </a>
            </div>
        </div>
    </div>
</div>