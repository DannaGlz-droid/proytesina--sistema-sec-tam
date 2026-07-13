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
            <div class="lg:w-80 lg:flex-none min-w-0">
                <div class="border border-[#404041] rounded-lg p-6 bg-white overflow-hidden">
                    <!-- FOTO DE PERFIL -->
                    <div class="flex flex-col items-center mb-6">
                        <div class="relative group">
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Foto de perfil" class="w-24 h-24 rounded-full object-cover border-4 border-[#611132]" data-profile-avatar>
                            @else
                                <img src="{{ asset('images/default_pfp.svg.png') }}" alt="Avatar predeterminado" class="w-24 h-24 rounded-full object-cover border-4 border-[#611132]" data-profile-avatar>
                            @endif
                            
                            <button type="button" id="photoMenuBtn" class="absolute -bottom-1 -right-1 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-[#611132] text-white shadow-sm transition-all duration-200 hover:bg-[#4a0e26] hover:shadow-md active:scale-95 focus:outline-none focus:ring-2 focus:ring-[#611132]/30" title="Opciones de foto" aria-label="Opciones de foto" aria-expanded="false" aria-controls="photoMenu">
                                <i class="fas fa-camera text-xs"></i>
                            </button>

                            <div id="photoMenu" class="hidden absolute left-1/2 top-full z-20 mt-3 w-44 -translate-x-1/2 overflow-hidden rounded-lg border border-gray-200 bg-white text-left shadow-xl ring-1 ring-black/5">
                                <button type="button" id="uploadPhotoBtn" class="flex w-full items-center gap-2 px-3 py-2 text-xs font-lora font-semibold text-[#404041] transition-colors hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
                                    <i class="fas fa-camera w-4 text-[#611132]"></i>
                                    Cambiar foto
                                </button>
                                <button type="button" id="deletePhotoBtn" class="{{ auth()->user()->profile_photo_path ? '' : 'hidden' }} flex w-full items-center gap-2 border-t border-gray-100 px-3 py-2 text-xs font-lora font-semibold text-red-700 transition-colors hover:bg-red-50 focus:bg-red-50 focus:outline-none">
                                    <i class="fas fa-trash w-4"></i>
                                    Eliminar foto
                                </button>
                            </div>
                        </div>
                        
                        <!-- Input file hidden -->
                        <input type="file" id="photoInput" accept="image/*" style="display: none;">
                        
                        <h2 class="text-lg font-lora font-bold text-[#404041] text-center mt-4 leading-snug max-w-full break-words [overflow-wrap:anywhere]">{{ $fullName ?? auth()->user()->name }}</h2>
                        <p class="text-sm text-gray-600 font-lora text-center leading-snug max-w-full break-words [overflow-wrap:anywhere]">{{ auth()->user()->position->name ?? 'Sin cargo' }}</p>
                    </div>

                    <!-- INFORMACIÓN RÁPIDA -->
                    <div class="space-y-3 border-t border-gray-200 pt-4">
                        <div class="flex items-start gap-3 min-w-0">
                            <i class="fas fa-envelope text-[#611132] text-sm mt-1 flex-none"></i>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 font-lora">Correo</p>
                                <p class="text-sm text-[#404041] font-lora break-words [overflow-wrap:anywhere]">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3 min-w-0">
                            <i class="fas fa-briefcase text-[#611132] text-sm mt-1 flex-none"></i>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 font-lora">Cargo</p>
                                <p class="text-sm text-[#404041] font-lora break-words [overflow-wrap:anywhere]">{{ auth()->user()->position->name ?? 'Sin cargo' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3 min-w-0">
                            <i class="fas fa-map-marker-alt text-[#611132] text-sm mt-1 flex-none"></i>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 font-lora">Distrito</p>
                                <p class="text-sm text-[#404041] font-lora break-words [overflow-wrap:anywhere]">{{ auth()->user()->district->name ?? 'Sin distrito' }}</p>
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
            <div class="flex-1 min-w-0">
                <div class="border border-[#404041] rounded-lg bg-white overflow-hidden">
                    
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
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border break-words [overflow-wrap:anywhere]">{{ auth()->user()->username ?? explode('@', auth()->user()->email)[0] }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Nombre Completo</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border break-words [overflow-wrap:anywhere]">{{ $fullName ?? auth()->user()->name }}</p>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información de Contacto</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Correo Electrónico</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border break-words [overflow-wrap:anywhere]">{{ auth()->user()->email }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Teléfono</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border break-words [overflow-wrap:anywhere]">{{ auth()->user()->phone ?? 'No especificado' }}</p>
                                </div>
                            </div>

                            <!-- Información Laboral -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información Laboral</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Cargo</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border break-words [overflow-wrap:anywhere]">{{ auth()->user()->position->name ?? 'Sin cargo' }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Distrito</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border break-words [overflow-wrap:anywhere]">{{ auth()->user()->district->name ?? 'Sin distrito' }}</p>
                                </div>
                            </div>

                            <!-- Información de Cuenta -->
                            <div class="space-y-4">
                                <h4 class="font-lora font-semibold text-[#404041] text-sm border-b border-gray-200 pb-2">Información de Cuenta</h4>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 font-lora mb-1">Fecha de Alta</label>
                                    <p class="text-sm text-[#404041] font-lora bg-gray-50 px-3 py-2 rounded border break-words [overflow-wrap:anywhere]">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                                        @php
                                            $roleName = auth()->user()->role->name ?? 'Usuario';
                                            $roleLower = strtolower($roleName);
                                            if (in_array($roleLower, ['administrador', 'admin'])) {
                                                $roleClasses = 'bg-[#e0e7ff] text-[#3730a3]';
                                            } elseif (in_array($roleLower, ['coordinador'])) {
                                                $roleClasses = 'bg-[#dcfce7] text-[#166534]';
                                            } elseif (in_array($roleLower, ['operador'])) {
                                                $roleClasses = 'bg-[#fef3c7] text-[#92400e]';
                                            } elseif ($roleLower === 'invitado') {
                                                $roleClasses = 'bg-[#fee2e2] text-[#991b1b]';
                                            } elseif (in_array($roleLower, ['usuario', 'user'])) {
                                                $roleClasses = 'bg-[#f8f1f4] text-[#611132]';
                                            } else {
                                                $roleClasses = 'bg-slate-100 text-slate-700';
                                            }
                                        @endphp
                                        <span class="inline-block max-w-full {{ $roleClasses }} text-xs font-semibold px-2.5 py-1 rounded-full whitespace-normal break-words [overflow-wrap:anywhere]">{{ $roleName }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- NOTA INFORMATIVA ACTUALIZADA -->
                        <div class="mt-6 p-4 bg-gray-100 rounded-lg border border-gray-300">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-gray-600 mt-0.5"></i>
                                <div class="min-w-0">
                                    <p class="text-sm text-[#404041] font-lora font-semibold">¿Necesitas ayuda?</p>
                                    <p class="text-xs text-gray-600 font-lora mt-1 break-words [overflow-wrap:anywhere]">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const photoMenuBtn = document.getElementById('photoMenuBtn');
            const photoMenu = document.getElementById('photoMenu');
            const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
            const deletePhotoBtn = document.getElementById('deletePhotoBtn');
            const photoInput = document.getElementById('photoInput');
            const defaultAvatarUrl = '{{ asset('images/default_pfp.svg.png') }}';

            function closePhotoMenu() {
                if (!photoMenu || !photoMenuBtn) return;

                photoMenu.classList.add('hidden');
                photoMenuBtn.setAttribute('aria-expanded', 'false');
            }

            function togglePhotoMenu() {
                if (!photoMenu || !photoMenuBtn) return;

                const isOpen = !photoMenu.classList.contains('hidden');
                photoMenu.classList.toggle('hidden', isOpen);
                photoMenuBtn.setAttribute('aria-expanded', String(!isOpen));
            }

            function notify(message, type = 'success', duration = 3000) {
                if (typeof window.showToast === 'function') {
                    window.showToast(message, type, duration);
                    return;
                }

                console[type === 'error' ? 'error' : 'log'](message);
            }

            function updateProfileAvatars(src, alt) {
                document.querySelectorAll('[data-profile-avatar]').forEach((avatar) => {
                    avatar.src = src;
                    avatar.alt = alt;
                });
            }

            async function parseJsonResponse(response) {
                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const message = data.message || Object.values(data.errors || {})?.flat()?.[0] || 'No se pudo completar la acción.';
                    throw new Error(message);
                }

                return data;
            }

            if (uploadPhotoBtn) {
                uploadPhotoBtn.addEventListener('click', function() {
                    closePhotoMenu();
                    photoInput.click();
                });
            }

            if (photoMenuBtn) {
                photoMenuBtn.addEventListener('click', function(event) {
                    event.stopPropagation();
                    togglePhotoMenu();
                });
            }

            document.addEventListener('click', function(event) {
                if (!photoMenu || photoMenu.classList.contains('hidden')) return;
                if (event.target.closest('#photoMenu') || event.target.closest('#photoMenuBtn')) return;

                closePhotoMenu();
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closePhotoMenu();
                }
            });

            if (photoInput) {
                photoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    if (file.size > 5 * 1024 * 1024) {
                        notify('El archivo es demasiado grande. Máximo 5 MB.', 'warning', 3200);
                        photoInput.value = '';
                        return;
                    }

                    const formData = new FormData();
                    formData.append('profile_photo', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    const uploadBtn = uploadPhotoBtn;
                    const uploadBtnHtml = uploadBtn.innerHTML;
                    uploadBtn.disabled = true;
                    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin w-4 text-[#611132]"></i> Subiendo...';

                    fetch('{{ route("usuario.upload-photo") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(parseJsonResponse)
                    .then(data => {
                        if (data.success) {
                            updateProfileAvatars(data.photo_url, 'Foto de perfil');
                            if (deletePhotoBtn) {
                                deletePhotoBtn.classList.remove('hidden');
                            }
                            notify(data.message || 'Foto de perfil actualizada.', 'success', 2800);
                        } else {
                            notify(data.message || 'No se pudo subir la foto.', 'error', 3200);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        notify(error.message || 'No se pudo subir la foto. Intenta nuevamente.', 'error', 3200);
                    })
                    .finally(() => {
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = uploadBtnHtml;
                        photoInput.value = '';
                    });
                });
            }

            if (deletePhotoBtn) {
                deletePhotoBtn.addEventListener('click', async function() {
                    closePhotoMenu();

                    const confirmed = typeof window.confirmDialog === 'function'
                        ? await window.confirmDialog({
                            title: 'Eliminar foto de perfil',
                            message: 'Se quitará tu foto actual y se mostrará el avatar predeterminado.',
                            confirmText: 'Eliminar',
                            cancelText: 'Cancelar',
                            variant: 'danger'
                        })
                        : false;

                    if (!confirmed) return;

                    const deleteBtn = deletePhotoBtn;
                    deleteBtn.disabled = true;

                    fetch('{{ route("usuario.delete-photo") }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(parseJsonResponse)
                    .then(data => {
                        if (data.success) {
                            updateProfileAvatars(defaultAvatarUrl, 'Avatar predeterminado');
                            deleteBtn.classList.add('hidden');
                            notify(data.message || 'Foto de perfil eliminada.', 'success', 2800);
                        } else {
                            notify(data.message || 'No se pudo eliminar la foto.', 'error', 3200);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        notify(error.message || 'No se pudo eliminar la foto. Intenta nuevamente.', 'error', 3200);
                    })
                    .finally(() => {
                        if (deleteBtn.isConnected) {
                            deleteBtn.disabled = false;
                        }
                    });
                });
            }
        });
    </script>
@endsection
