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
        />

        <div class="users-form-card border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">
            <form action="{{ route('user.update', $user->id) }}" method="POST" id="userEditForm">
                @csrf
                @method('PUT')

                <x-ui.form.section title="Información del usuario" icon="person-outline">
                    <div class="space-y-3">
                        <div>
                            <label for="name" class="block">Nombre(s) <span class="text-red-600">*</span></label>
                            <input id="name" name="name" type="text" required minlength="2" maxlength="191"
                                placeholder="Ej: María Elena" value="{{ old('name', $user->name) }}">
                            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="second_last_name" class="block">Apellido materno</label>
                            <input id="second_last_name" name="second_last_name" type="text" minlength="2" maxlength="191"
                                placeholder="Ej: López" value="{{ old('second_last_name', $user->second_last_name) }}">
                            @error('second_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label for="first_last_name" class="block">Apellido paterno <span class="text-red-600">*</span></label>
                            <input id="first_last_name" name="first_last_name" type="text" required minlength="2" maxlength="191"
                                placeholder="Ej: García" value="{{ old('first_last_name', $user->first_last_name) }}">
                            @error('first_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="email" class="block">Correo electrónico <span class="text-red-600">*</span></label>
                                <input id="email" name="email" type="email" required maxlength="255" autocomplete="email"
                                    placeholder="Ej: usuario@ejemplo.com" value="{{ old('email', $user->email) }}">
                                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="phone" class="block">Teléfono</label>
                                <input id="phone" name="phone" type="tel" maxlength="10" pattern="[0-9]{10}"
                                    inputmode="numeric" autocomplete="tel"
                                    title="Capture exactamente 10 dígitos, sin espacios ni guiones"
                                    placeholder="10 dígitos, sin espacios" value="{{ old('phone', $user->phone) }}">
                                @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </x-ui.form.section>

                <div class="users-form-divider h-px bg-gray-300 my-4 lg:my-6"></div>

                <x-ui.form.section title="Información laboral" icon="business-outline">
                    <div>
                        <label for="position_select" class="block">Cargo <span class="text-red-600">*</span></label>
                        <select id="position_select" class="tomselect-select" name="position_id" required>
                            <option value="">Seleccione un cargo</option>
                            @foreach($positions ?? [] as $position)
                                <option value="{{ $position->id }}" @selected((string) old('position_id', $user->position_id) === (string) $position->id)>
                                    {{ $position->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="district_id" class="block">Distrito <span class="text-red-600">*</span></label>
                        <select id="district_id" class="tomselect-select" name="district_id" required>
                            <option value="">Seleccione un distrito</option>
                            @foreach($districts ?? [] as $district)
                                <option value="{{ $district->id }}" @selected((string) old('district_id', $user->district_id) === (string) $district->id)>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('district_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </x-ui.form.section>

                <div class="users-form-divider h-px bg-gray-300 my-4 lg:my-6"></div>

                <x-ui.form.section title="Configuración de cuenta" icon="settings-outline">
                    <div>
                        <label for="username" class="block">Usuario <span class="text-red-600">*</span></label>
                        <input id="username" name="username" type="text" required minlength="3" maxlength="50"
                            pattern="[a-zA-Z0-9_.-]+" autocomplete="username"
                            placeholder="Ej: mgarcia" value="{{ old('username', $user->username) }}">
                        @error('username') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-[minmax(0,2fr)_minmax(8rem,1fr)] gap-3">
                        <div>
                            <label for="role_id" class="block">Rol <span class="text-red-600">*</span></label>
                            <select id="role_id" class="tomselect-select" name="role_id" required>
                                <option value="">Seleccione un rol</option>
                                @foreach($roles ?? [] as $role)
                                    <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id) === (string) $role->id)>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status_select" class="block">Estado <span class="text-red-600">*</span></label>
                            <select id="status_select" class="tomselect-select" name="is_active" required>
                                <option value="1" @selected((string) old('is_active', (int) $user->is_active) === '1')>Activo</option>
                                <option value="0" @selected((string) old('is_active', (int) $user->is_active) === '0')>Inactivo</option>
                            </select>
                            @error('is_active') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </x-ui.form.section>

                <div class="users-form-divider h-px bg-gray-300 my-4 lg:my-6"></div>

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

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

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
                    allowEmptyOption: true,
                    searchField: ['role_id', 'status_select'].includes(select.id) ? [] : ['text'],
                });
                select.dataset.initialValue = initialValue;
            });

            window.resetUserEditForm = (event) => {
                event?.preventDefault();
                form.reset();
                selects.forEach((select) => select.tomselect.setValue(select.dataset.initialValue, true));
                form.querySelectorAll('[data-server-error]').forEach((error) => error.remove());
            };

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
