@extends('layouts.principal')
@section('title', 'Mi perfil')
@section('content')

    @include('components.header-admin')
    @include('components.nav-usuario')

    @php
        $profileUser = auth()->user();
        $profileName = $fullName ?? $profileUser->name;
        $profileUsername = $profileUser->username ?? explode('@', $profileUser->email)[0];
        $profilePosition = $profileUser->position->name ?? 'Sin cargo';
        $profileDistrictRaw = $profileUser->district->name ?? 'Sin distrito';
        $profileDistrictParts = preg_split('/\s*-\s*/u', $profileDistrictRaw, 2);
        $profileDistrict = count($profileDistrictParts) === 2
            ? mb_strtoupper(trim($profileDistrictParts[0]), 'UTF-8') . ' · ' . \Illuminate\Support\Str::title(\Illuminate\Support\Str::lower(trim($profileDistrictParts[1])))
            : $profileDistrictRaw;
        $profilePhone = $profileUser->formattedPhone() ?? 'Sin teléfono registrado';
        $profilePhoneHref = $profileUser->phoneHref();
        $profileRegistrationDate = $profileUser->formatted_registration_date
            ?? optional($profileUser->created_at)->format('d/m/Y')
            ?? 'Sin registro';
        $profileIsActive = (bool) ($profileUser->is_active ?? true);
        $profileRole = strtolower($profileUser->role->name ?? 'Usuario');
        $profileRoleLabel = match ($profileRole) {
            'admin', 'administrador' => 'Administrador',
            'coordinator', 'coordinador' => 'Coordinador',
            'operator', 'operador' => 'Operador',
            'invitado' => 'Invitado',
            default => ucfirst($profileRole),
        };
        $profileRoleClass = match ($profileRole) {
            'admin', 'administrador' => 'is-admin',
            'coordinator', 'coordinador' => 'is-coordinator',
            'operator', 'operador' => 'is-operator',
            'invitado' => 'is-guest',
            default => 'is-neutral',
        };
        $showProfileContactHelp = in_array($profileRole, [
            'coordinator',
            'coordinador',
            'operator',
            'operador',
            'guest',
            'invitado',
        ], true);
    @endphp

    <div class="users-form-page profile-page px-4 sm:px-6 lg:px-10 pt-6 lg:pt-8 pb-8 lg:pb-10">
        <x-ui.page-header
            title="Mi perfil"
            description="Consulta tu información personal y los datos de tu cuenta."
        />

        <article class="users-form-card profile-account-card">
            <header class="profile-overview">
                <div class="profile-overview-identity">
                    <div class="profile-avatar-control">
                        <div class="profile-avatar-frame">
                            @if($profileUser->profile_photo_path)
                                <img src="{{ asset('storage/' . $profileUser->profile_photo_path) }}" alt="Foto de perfil de {{ $profileName }}" class="profile-avatar" data-profile-avatar>
                            @else
                                <img src="{{ asset('images/default_pfp.svg.png') }}" alt="Avatar predeterminado de {{ $profileName }}" class="profile-avatar profile-avatar-placeholder" data-profile-avatar>
                            @endif
                        </div>

                        <button type="button" id="photoMenuBtn" class="profile-photo-button" title="Opciones de fotografía" aria-label="Opciones de fotografía" aria-haspopup="menu" aria-expanded="false" aria-controls="photoMenu">
                            <i class="fas fa-camera" aria-hidden="true"></i>
                        </button>

                        <div id="photoMenu" class="profile-photo-menu hidden" role="menu" aria-label="Acciones de fotografía">
                            <button type="button" id="uploadPhotoBtn" class="profile-photo-menu-item" role="menuitem">
                                <i class="fas fa-camera" aria-hidden="true"></i>
                                <span>Cambiar fotografía</span>
                            </button>
                            <button type="button" id="deletePhotoBtn" class="profile-photo-menu-item profile-photo-menu-item-danger {{ $profileUser->profile_photo_path ? '' : 'hidden' }}" role="menuitem">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                                <span>Eliminar fotografía</span>
                            </button>
                        </div>

                        <input type="file" id="photoInput" accept="image/jpeg,image/png,image/jpg" class="hidden" aria-label="Seleccionar fotografía de perfil">
                    </div>

                    <div class="profile-overview-copy">
                        <span>Perfil de usuario</span>
                        <h2>{{ $profileName }}</h2>
                        <p class="profile-overview-username">{{ '@' . $profileUsername }}</p>
                        <p class="profile-overview-date">Fecha de alta: <span class="profile-tabular">{{ $profileRegistrationDate }}</span></p>
                    </div>
                </div>

                <div class="profile-overview-account" aria-label="Resumen de la cuenta">
                    <div>
                        <span class="profile-overview-label">Estado de la cuenta</span>
                        <span class="profile-status-text">
                            <span class="profile-status-dot {{ $profileIsActive ? 'is-active' : 'is-inactive' }}" aria-hidden="true"></span>
                            {{ $profileIsActive ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div>
                        <span class="profile-overview-label">Rol en el sistema</span>
                        <span class="profile-role-badge {{ $profileRoleClass }}">{{ $profileRoleLabel }}</span>
                    </div>
                </div>
            </header>

            <div class="profile-details-layout">
                <section class="profile-detail-group" aria-labelledby="profile-personal-title">
                    <h2 id="profile-personal-title" class="profile-detail-title">
                        <i class="far fa-user" aria-hidden="true"></i>
                        Información personal
                    </h2>
                    <dl class="profile-data-list">
                        <div class="profile-data-item">
                            <dt>Correo electrónico</dt>
                            <dd><a href="mailto:{{ $profileUser->email }}">{{ $profileUser->email }}</a></dd>
                        </div>
                        <div class="profile-data-item">
                            <dt>Teléfono</dt>
                            <dd class="profile-tabular">
                                @if($profilePhoneHref)
                                    <a href="tel:{{ $profilePhoneHref }}">{{ $profilePhone }}</a>
                                @else
                                    {{ $profilePhone }}
                                @endif
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="profile-detail-group" aria-labelledby="profile-work-title">
                    <h2 id="profile-work-title" class="profile-detail-title">
                        <i class="far fa-building" aria-hidden="true"></i>
                        Información laboral
                    </h2>
                    <dl class="profile-data-list">
                        <div class="profile-data-item">
                            <dt>Cargo</dt>
                            <dd>{{ $profilePosition }}</dd>
                        </div>
                        <div class="profile-data-item">
                            <dt>Distrito</dt>
                            <dd>{{ $profileDistrict }}</dd>
                        </div>
                    </dl>
                </section>

            </div>

            @if($showProfileContactHelp)
                <div class="profile-help-note">
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                    <div>
                        <strong>¿Necesitas actualizar tus datos?</strong>
                        @if($systemContact)
                            <p>
                                Solicita la modificación al responsable del sistema:
                                <span class="profile-help-contact-name">{{ $systemContact->contactDisplayName() }}</span>
                                @if($systemContact->email)
                                    <span aria-hidden="true"> · </span><a href="mailto:{{ $systemContact->email }}">{{ $systemContact->email }}</a>
                                @endif
                                @if($systemContact->phoneHref())
                                    <span aria-hidden="true"> · </span><a class="profile-tabular" href="tel:{{ $systemContact->phoneHref() }}">{{ $systemContact->formattedPhone(true) }}</a>
                                @endif
                            </p>
                        @else
                            <p>Comunícate con el área responsable del sistema para solicitar la modificación.</p>
                        @endif
                    </div>
                </div>
            @endif

            <footer class="profile-account-footer">
                @if(in_array($profileRole, ['admin', 'administrador'], true))
                    <a href="{{ route('user.edit', ['user' => $profileUser, 'from' => 'profile']) }}" class="profile-manage-account-button">
                        <i class="fas fa-user-cog" aria-hidden="true"></i>
                        Administrar esta cuenta
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="profile-logout-button">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        Cerrar sesión
                    </button>
                </form>
            </footer>
        </article>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const photoMenuBtn = document.getElementById('photoMenuBtn');
            const photoMenu = document.getElementById('photoMenu');
            const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
            const deletePhotoBtn = document.getElementById('deletePhotoBtn');
            const photoInput = document.getElementById('photoInput');
            const defaultAvatarUrl = '{{ asset('images/default_pfp.svg.png') }}';

            function closePhotoMenu(returnFocus = false) {
                if (!photoMenu || !photoMenuBtn) return;
                photoMenu.classList.add('hidden');
                photoMenuBtn.setAttribute('aria-expanded', 'false');
                if (returnFocus) photoMenuBtn.focus();
            }

            function togglePhotoMenu() {
                if (!photoMenu || !photoMenuBtn) return;
                const isOpen = !photoMenu.classList.contains('hidden');
                photoMenu.classList.toggle('hidden', isOpen);
                photoMenuBtn.setAttribute('aria-expanded', String(!isOpen));
                if (!isOpen) uploadPhotoBtn?.focus();
            }

            function notify(message, type = 'success', duration = 3000) {
                if (typeof window.showToast === 'function') {
                    window.showToast(message, type, duration);
                    return;
                }
                console[type === 'error' ? 'error' : 'log'](message);
            }

            function updateProfileAvatars(src, alt, isPlaceholder = false) {
                document.querySelectorAll('[data-profile-avatar]').forEach((avatar) => {
                    avatar.src = src;
                    avatar.alt = alt;
                    avatar.classList.toggle(
                        'profile-avatar-placeholder',
                        isPlaceholder && Boolean(avatar.closest('.profile-avatar-frame'))
                    );
                    avatar.classList.toggle(
                        'app-header-avatar-placeholder',
                        isPlaceholder && Boolean(avatar.closest('.app-header-profile-summary'))
                    );
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

            uploadPhotoBtn?.addEventListener('click', function() {
                closePhotoMenu();
                photoInput?.click();
            });

            photoMenuBtn?.addEventListener('click', function(event) {
                event.stopPropagation();
                togglePhotoMenu();
            });

            document.addEventListener('click', function(event) {
                if (!photoMenu || photoMenu.classList.contains('hidden')) return;
                if (event.target.closest('#photoMenu') || event.target.closest('#photoMenuBtn')) return;
                closePhotoMenu();
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !photoMenu?.classList.contains('hidden')) closePhotoMenu(true);
            });

            photoMenu?.addEventListener('keydown', function(event) {
                if (!['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) return;

                const items = [...photoMenu.querySelectorAll('[role="menuitem"]')]
                    .filter((item) => !item.classList.contains('hidden') && !item.disabled);
                if (!items.length) return;

                event.preventDefault();
                const currentIndex = items.indexOf(document.activeElement);
                let nextIndex = currentIndex;

                if (event.key === 'Home') nextIndex = 0;
                if (event.key === 'End') nextIndex = items.length - 1;
                if (event.key === 'ArrowDown') nextIndex = (currentIndex + 1 + items.length) % items.length;
                if (event.key === 'ArrowUp') nextIndex = (currentIndex - 1 + items.length) % items.length;

                items[nextIndex].focus();
            });

            photoInput?.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (!file) return;
                if (file.size > 5 * 1024 * 1024) {
                    notify('El archivo es demasiado grande. El tamaño máximo es 5 MB.', 'warning', 3200);
                    photoInput.value = '';
                    return;
                }

                const formData = new FormData();
                formData.append('profile_photo', file);
                formData.append('_token', '{{ csrf_token() }}');
                const uploadBtnHtml = uploadPhotoBtn.innerHTML;
                uploadPhotoBtn.disabled = true;
                uploadPhotoBtn.setAttribute('aria-busy', 'true');
                uploadPhotoBtn.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i><span>Subiendo...</span>';

                fetch('{{ route("usuario.upload-photo") }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData
                })
                    .then(parseJsonResponse)
                    .then((data) => {
                        if (!data.success) throw new Error(data.message || 'No se pudo subir la fotografía.');
                        updateProfileAvatars(data.photo_url, 'Foto de perfil de {{ $profileName }}');
                        deletePhotoBtn?.classList.remove('hidden');
                        notify(data.message || 'La foto de perfil se actualizó correctamente.', 'success', 2800);
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        notify(error.message || 'No se pudo subir la fotografía. Inténtalo nuevamente.', 'error', 3200);
                    })
                    .finally(() => {
                        uploadPhotoBtn.disabled = false;
                        uploadPhotoBtn.removeAttribute('aria-busy');
                        uploadPhotoBtn.innerHTML = uploadBtnHtml;
                        photoInput.value = '';
                    });
            });

            deletePhotoBtn?.addEventListener('click', async function() {
                closePhotoMenu();
                const confirmed = typeof window.confirmDeleteDialog === 'function'
                    ? await window.confirmDeleteDialog({
                        title: 'Eliminar foto de perfil',
                        subject: 'tu foto de perfil',
                        description: 'Se mostrará el avatar predeterminado y la foto actual no podrá recuperarse.'
                    })
                    : false;
                if (!confirmed) return;

                deletePhotoBtn.disabled = true;
                deletePhotoBtn.setAttribute('aria-busy', 'true');
                fetch('{{ route("usuario.delete-photo") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                    .then(parseJsonResponse)
                    .then((data) => {
                        if (!data.success) throw new Error(data.message || 'No se pudo eliminar la fotografía.');
                        updateProfileAvatars(defaultAvatarUrl, 'Avatar predeterminado de {{ $profileName }}', true);
                        deletePhotoBtn.classList.add('hidden');
                        notify(data.message || 'La foto de perfil se eliminó correctamente.', 'success', 2800);
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        notify(error.message || 'No se pudo eliminar la fotografía. Inténtalo nuevamente.', 'error', 3200);
                    })
                    .finally(() => {
                        if (!deletePhotoBtn.isConnected) return;
                        deletePhotoBtn.disabled = false;
                        deletePhotoBtn.removeAttribute('aria-busy');
                    });
            });
        });
    </script>
@endsection
