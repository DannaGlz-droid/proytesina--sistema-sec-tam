<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registrar Usuario</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50">

    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-6">
                    <a href="{{ route('user.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                        <span>←</span> Volver a la lista de usuarios
                    </a>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-6">Registrar Nuevo Usuario</h1>

                <form method="POST" action="{{ route('user.store') }}" id="userForm">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Nombre -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre <span class="text-red-600">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name"
                                name="name" 
                                value="{{ old('name') }}"
                                required
                                minlength="2"
                                maxlength="191"
                                placeholder="Ej: Juan"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Apellido Paterno -->
                        <div>
                            <label for="first_last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Apellido Paterno
                            </label>
                            <input 
                                type="text" 
                                id="first_last_name"
                                name="first_last_name" 
                                value="{{ old('first_last_name') }}"
                                minlength="2"
                                maxlength="191"
                                placeholder="Ej: García"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Apellido Materno -->
                        <div>
                            <label for="second_last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Apellido Materno
                            </label>
                            <input 
                                type="text" 
                                id="second_last_name"
                                name="second_last_name" 
                                value="{{ old('second_last_name') }}"
                                minlength="2"
                                maxlength="191"
                                placeholder="Ej: López"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Correo Electrónico <span class="text-red-600">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="email"
                                name="email" 
                                value="{{ old('email') }}"
                                required
                                maxlength="255"
                                inputmode="email"
                                placeholder="usuario@ejemplo.com"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono
                            </label>
                            <input 
                                type="tel" 
                                id="phone"
                                name="phone" 
                                value="{{ old('phone') }}"
                                maxlength="20"
                                pattern="[0-9+\-\(\) ]{8,20}"
                                placeholder="+52 (777) 123-4567"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Usuario -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de Usuario <span class="text-red-600">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="username"
                                name="username" 
                                value="{{ old('username') }}"
                                required
                                minlength="3"
                                maxlength="50"
                                pattern="[a-zA-Z0-9_.-]+"
                                placeholder="usuario_123"
                                inputmode="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="text-gray-500 text-sm mt-1">Solo letras, números, guiones, puntos y guiones bajos</p>
                        </div>

                        <!-- Contraseña -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña <span class="text-red-600">*</span>
                            </label>
                            <input 
                                type="password" 
                                id="password"
                                name="password" 
                                required
                                minlength="6"
                                maxlength="255"
                                placeholder="Mínimo 6 caracteres"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="text-gray-500 text-sm mt-1">Mínimo 6 caracteres</p>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Contraseña <span class="text-red-600">*</span>
                            </label>
                            <input 
                                type="password" 
                                id="password_confirmation"
                                name="password_confirmation" 
                                required
                                minlength="6"
                                maxlength="255"
                                placeholder="Repite tu contraseña"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                        </div>

                        <!-- Posición -->
                        <div>
                            <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Posición
                            </label>
                            <select 
                                id="position_id"
                                name="position_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">-- Seleccionar --</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jurisdicción -->
                        <div>
                            <label for="jurisdiction_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Jurisdicción
                            </label>
                            <select 
                                id="jurisdiction_id"
                                name="jurisdiction_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">-- Seleccionar --</option>
                                @foreach ($jurisdictions as $jurisdiction)
                                    <option value="{{ $jurisdiction->id }}" {{ old('jurisdiction_id') == $jurisdiction->id ? 'selected' : '' }}>
                                        {{ $jurisdiction->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rol -->
                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Rol <span class="text-red-600">*</span>
                            </label>
                            <select 
                                id="role_id"
                                name="role_id"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">-- Seleccionar --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estado Activo -->
                        <div class="flex items-center gap-3">
                            <input 
                                type="checkbox" 
                                id="active"
                                name="is_active" 
                                value="1"
                                checked
                                class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                            >
                            <label for="active" class="text-sm font-medium text-gray-700">
                                Usuario Activo
                            </label>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4 mt-8">
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                        >
                            Crear Usuario
                        </button>
                        <a 
                            href="{{ route('user.index') }}" 
                            class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-medium"
                        >
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('password_confirmation');

        // Convertir errores de Laravel a validación HTML5
        document.addEventListener('DOMContentLoaded', function() {
            @if($errors->any())
                const errors = @json($errors->messages());
                
                // Mapear nombres de campos a sus IDs en el DOM
                const fieldMap = {
                    'name': 'name',
                    'first_last_name': 'first_last_name',
                    'second_last_name': 'second_last_name',
                    'email': 'email',
                    'phone': 'phone',
                    'username': 'username',
                    'password': 'password',
                    'password_confirmation': 'password_confirmation',
                    'role_id': 'role_id',
                    'position_id': 'position_id',
                    'jurisdiction_id': 'jurisdiction_id'
                };

                // Aplicar setCustomValidity a cada campo con error
                Object.keys(errors).forEach(fieldName => {
                    const fieldId = fieldMap[fieldName];
                    if (fieldId) {
                        const field = document.getElementById(fieldId);
                        if (field && errors[fieldName][0]) {
                            field.setCustomValidity(errors[fieldName][0]);
                            // Hacer que el campo sea inválido visualmente
                            field.reportValidity();
                        }
                    }
                });

                // Scroll al primer campo con error
                const firstErrorField = Object.keys(errors)[0];
                if (firstErrorField && fieldMap[firstErrorField]) {
                    const field = document.getElementById(fieldMap[firstErrorField]);
                    if (field) {
                        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        field.focus();
                    }
                }
            @endif

            // Limpiar validación personalizada cuando el usuario empieza a escribir
            const allInputs = document.querySelectorAll('input, select');
            allInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.setCustomValidity('');
                });
            });
        });

        // Validación personalizada: coincidencia de contraseñas
        confirmPasswordField.addEventListener('input', function() {
            if (this.value !== passwordField.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });

        passwordField.addEventListener('input', function() {
            // Re-validar confirmación cuando se cambia la contraseña
            if (confirmPasswordField.value) {
                confirmPasswordField.dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>