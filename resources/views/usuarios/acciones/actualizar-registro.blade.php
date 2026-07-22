@extends('layouts.principal')
@section('title', 'Editar usuario')

@section('content')
    @include('components.header-admin')
    @include('components.nav-usuario')

    <div class="users-form-page px-4 sm:px-6 lg:px-10 pt-6 lg:pt-8 pb-8 lg:pb-10">
        <x-ui.page-header
            title="Editar usuario"
            description="Actualice la información y los permisos de acceso de esta cuenta."
            :back-href="route('user.user-gestion')"
            back-label="Volver a Gestión de usuarios"
            :prefer-history-back="true"
        />

        <div class="users-form-card">
            <form action="{{ route('user.update', $user->id) }}" method="POST" id="userEditForm">
                @csrf
                @method('PUT')

                <x-ui.form.section title="Información del usuario" icon="user">
                    <div class="space-y-3">
                        <div>
                            <label for="name" class="block">Nombre(s) <span class="text-red-600">*</span></label>
                            <input id="name" name="name" type="text" required minlength="2" maxlength="191" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                                placeholder="Ej: María Elena" value="{{ old('name', $user->name) }}">
                            @error('name') <p id="name-error" class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="second_last_name" class="block">Apellido materno</label>
                            <input id="second_last_name" name="second_last_name" type="text" minlength="2" maxlength="191" @error('second_last_name') aria-invalid="true" aria-describedby="second-last-name-error" @enderror
                                placeholder="Ej: López" value="{{ old('second_last_name', $user->second_last_name) }}">
                            @error('second_last_name') <p id="second-last-name-error" class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label for="first_last_name" class="block">Apellido paterno <span class="text-red-600">*</span></label>
                            <input id="first_last_name" name="first_last_name" type="text" required minlength="2" maxlength="191" @error('first_last_name') aria-invalid="true" aria-describedby="first-last-name-error" @enderror
                                placeholder="Ej: García" value="{{ old('first_last_name', $user->first_last_name) }}">
                            @error('first_last_name') <p id="first-last-name-error" class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="email" class="block">Correo electrónico <span class="text-red-600">*</span></label>
                                <input id="email" name="email" type="email" required maxlength="255" autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                                    placeholder="Ej: usuario@ejemplo.com" value="{{ old('email', $user->email) }}">
                                @error('email') <p id="email-error" class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="phone" class="block">Teléfono</label>
                                <input id="phone" name="phone" type="tel" maxlength="10" pattern="[0-9]{10}" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror
                                    inputmode="numeric" autocomplete="tel"
                                    title="Capture exactamente 10 dígitos, sin espacios ni guiones"
                                    placeholder="10 dígitos, sin espacios" value="{{ old('phone', $user->phone) }}">
                                @error('phone') <p id="phone-error" class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </x-ui.form.section>

                <div class="users-form-divider"></div>

                <x-ui.form.section title="Información laboral" icon="work">
                    <div>
                        <label for="position_select" class="block">Cargo <span class="text-red-600">*</span></label>
                        <select id="position_select" class="tomselect-select" name="position_id" required @error('position_id') aria-invalid="true" aria-describedby="position-error" @enderror>
                            <option value="">Seleccione un cargo</option>
                            @foreach($positions ?? [] as $position)
                                <option value="{{ $position->id }}" @selected((string) old('position_id', $user->position_id) === (string) $position->id)>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id') <p id="position-error" class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="district_id" class="block">Distrito <span class="text-red-600">*</span></label>
                        <select id="district_id" class="tomselect-select" name="district_id" required @error('district_id') aria-invalid="true" aria-describedby="district-error" @enderror>
                            <option value="">Seleccione un distrito</option>
                            @foreach($districts ?? [] as $district)
                                <option value="{{ $district->id }}" @selected((string) old('district_id', $user->district_id) === (string) $district->id)>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('district_id') <p id="district-error" class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </x-ui.form.section>

                <div class="users-form-divider"></div>

                <x-ui.form.section title="Configuración de cuenta" icon="settings">
                    <div>
                        <label for="username" class="block">Usuario <span class="text-red-600">*</span></label>
                        <input id="username" name="username" type="text" required minlength="3" maxlength="50" @error('username') aria-invalid="true" aria-describedby="username-error" @enderror
                            pattern="[a-zA-Z0-9_.-]+" autocomplete="username"
                            placeholder="Ej: mgarcia" value="{{ old('username', $user->username) }}">
                        @error('username') <p id="username-error" class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-[minmax(0,2fr)_minmax(8rem,1fr)] gap-3">
                        <div>
                            <label for="role_id" class="block">Rol <span class="text-red-600">*</span></label>
                            <select id="role_id" class="tomselect-select" name="role_id" required @error('role_id') aria-invalid="true" aria-describedby="role-error" @enderror>
                                <option value="">Seleccione un rol</option>
                                @foreach($roles ?? [] as $role)
                                    <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id) === (string) $role->id)>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id') <p id="role-error" class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status_select" class="block">Estado <span class="text-red-600">*</span></label>
                            <select id="status_select" class="tomselect-select" name="is_active" required @error('is_active') aria-invalid="true" aria-describedby="status-error" @enderror>
                                <option value="1" @selected((string) old('is_active', (int) $user->is_active) === '1')>Activo</option>
                                <option value="0" @selected((string) old('is_active', (int) $user->is_active) === '0')>Inactivo</option>
                            </select>
                            @error('is_active') <p id="status-error" class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </x-ui.form.section>

                <div class="users-form-divider"></div>

                <x-form-buttons
                    class="users-form-actions"
                    primaryText="Guardar cambios"
                    secondaryText="Restablecer cambios"
                    secondaryOnclick="resetUserEditForm(event)"
                    primaryType="submit"
                    secondaryType="button"
                />
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('userEditForm');
            const selects = ['position_select', 'district_id', 'role_id', 'status_select']
                .map((id) => document.getElementById(id))
                .filter(Boolean);

            selects.forEach((select) => {
                const initialValue = select.value;

                select.tomselect = new TomSelect(select, {
                    create: false,
                    maxItems: 1,
                    maxOptions: 50,
                    allowEmptyOption: false,
                    placeholder: select.querySelector('option[value=""]')?.textContent.trim() || '',
                    searchField: select.id === 'district_id' ? ['text'] : [],
                });
                select.dataset.initialValue = initialValue;
            });

            const navigationState = {
                submitting: false,
                navigating: false,
                confirming: false,
            };

            const getFormSnapshot = () => JSON.stringify(
                Array.from(new FormData(form).entries())
                    .filter(([name]) => !['_token', '_method'].includes(name))
                    .map(([name, value]) => [name, String(value)])
            );
            const initialSnapshot = getFormSnapshot();
            const hasUnsavedChanges = () => getFormSnapshot() !== initialSnapshot;

            const confirmDiscardChanges = async ({ question, description, confirmText }) => {
                if (!hasUnsavedChanges()) {
                    return true;
                }

                if (navigationState.confirming) {
                    return false;
                }

                navigationState.confirming = true;

                try {
                    if (typeof window.confirmDialog !== 'function') {
                        return window.confirm(`${question}\n\n${description}`);
                    }

                    return await window.confirmDialog({
                        title: 'Descartar cambios',
                        question,
                        description,
                        confirmText,
                        cancelText: 'Continuar editando',
                        variant: 'warning',
                    });
                } finally {
                    navigationState.confirming = false;
                }
            };

            window.resetUserEditForm = async (event) => {
                event?.preventDefault();

                const shouldReset = await confirmDiscardChanges({
                    question: '¿Deseas restablecer los cambios?',
                    description: 'Los campos volverán a los valores con los que abriste esta edición.',
                    confirmText: 'Restablecer',
                });

                if (!shouldReset) {
                    return;
                }

                form.reset();
                selects.forEach((select) => select.tomselect.setValue(select.dataset.initialValue, true));
                form.querySelectorAll('[data-server-error]').forEach((error) => error.remove());
            };

            form.addEventListener('submit', () => {
                navigationState.submitting = true;
            });

            document.addEventListener('click', async (event) => {
                const link = event.target.closest('a[href]');

                if (!link
                    || event.defaultPrevented
                    || event.button !== 0
                    || event.metaKey
                    || event.ctrlKey
                    || event.shiftKey
                    || event.altKey
                    || link.target === '_blank'
                    || link.hasAttribute('download')
                    || !hasUnsavedChanges()) {
                    return;
                }

                const href = link.getAttribute('href');

                if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                    return;
                }

                const destination = new URL(link.href, window.location.href);

                if (destination.origin !== window.location.origin) {
                    return;
                }

                event.preventDefault();

                const shouldLeave = await confirmDiscardChanges({
                    question: '¿Deseas salir de la edición?',
                    description: 'Los cambios realizados no se guardarán.',
                    confirmText: 'Salir sin guardar',
                });

                if (!shouldLeave) {
                    return;
                }

                navigationState.navigating = true;
                if (typeof window.navigateBackOrVisit === 'function') {
                    window.navigateBackOrVisit(destination.href);
                    return;
                }

                window.location.assign(destination.href);
            }, true);

            window.addEventListener('beforeunload', (event) => {
                if (navigationState.submitting || navigationState.navigating || !hasUnsavedChanges()) {
                    return;
                }

                event.preventDefault();
                event.returnValue = '';
            });

            @if($errors->any())
                const fieldMap = {
                    name: 'name',
                    first_last_name: 'first_last_name',
                    second_last_name: 'second_last_name',
                    email: 'email',
                    phone: 'phone',
                    username: 'username',
                    position_id: 'position_select',
                    district_id: 'district_id',
                    role_id: 'role_id',
                    is_active: 'status_select',
                };
                const firstError = Object.keys(@json($errors->messages()))[0];
                const target = document.getElementById(fieldMap[firstError]);
                requestAnimationFrame(() => target?.tomselect ? target.tomselect.focus() : target?.focus());
            @endif
        });
    </script>
@endsection
