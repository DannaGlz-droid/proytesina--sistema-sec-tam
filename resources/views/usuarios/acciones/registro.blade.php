@extends('layouts.principal')
@section('title', 'Registrar Usuario')
@section('content')

    @include('components.header-admin')
    @include('components.nav-usuario')

    <div class="users-form-page px-4 sm:px-6 lg:px-10 pt-6 lg:pt-8 pb-8 lg:pb-10">
        <x-ui.page-header
            title="Registro de usuario"
            description="Complete el formulario para registrar un usuario en el sistema."
            :back-href="route('user.user-gestion')"
            back-label="Volver a Gestión de usuarios"
        />

        <!-- Cuadro del formulario responsive -->
        <div class="users-form-card border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">

            <form action="{{ route('user.store') }}" method="POST" id="userRegistroForm">
                    
                @csrf
                    <!-- Sección 1: Información del usuario -->
                    <div class="users-form-section mb-6 lg:mb-8">
                        <div class="users-form-section-header flex items-center mb-4">
                            <ion-icon name="person-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                            <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información del usuario</h2>
                            <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Nombre(s) <span class="text-red-600">*</span></label>
                                    <input id="name" name="name" type="text" required minlength="2" maxlength="191"
                                        class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                        placeholder="Ej: María Elena"
                                        value="{{ old('name') }}">
                                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno</label>
                                    <input id="second_last_name" name="second_last_name" type="text" minlength="2" maxlength="191"
                                        class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                        placeholder="Ej: López"
                                        value="{{ old('second_last_name') }}">
                                    @error('second_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno <span class="text-red-600">*</span></label>
                                    <input id="first_last_name" name="first_last_name" type="text" required minlength="2" maxlength="191"
                                        class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                        placeholder="Ej: García"
                                        value="{{ old('first_last_name') }}">
                                    @error('first_last_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Correo electrónico <span class="text-red-600">*</span></label>
                                            <input id="email" name="email" type="email" required maxlength="255" autocomplete="email"
                                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                                placeholder="Ej: usuario@ejemplo.com"
                                                value="{{ old('email') }}">
                                            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Teléfono</label>
                                            <input id="phone" name="phone" type="tel" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" autocomplete="tel" title="Capture exactamente 10 dígitos, sin espacios ni guiones"
                                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                                placeholder="10 dígitos, sin espacios"
                                                value="{{ old('phone') }}">
                                            @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Línea separadora -->
                    <div class="users-form-divider h-[1px] bg-gray-300 my-4 lg:my-6"></div>

                    <!-- Sección 2: Información laboral -->
                    <div class="users-form-section mb-6 lg:mb-8">
                        <div class="users-form-section-header flex items-center mb-4">
                            <ion-icon name="business-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                            <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Información laboral</h2>
                            <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Cargo <span class="text-red-600">*</span></label>
                                    <select id="position_select" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select" name="position_id" required>
                                        <option value="">Seleccione un cargo</option>
                                        @if(isset($positions))
                                            @foreach($positions as $p)
                                                <option value="{{ $p->id }}" {{ old('position_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('position_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Distrito <span class="text-red-600">*</span></label>
                                    <select id="district_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora tomselect-select" name="district_id" required>
                                        <option value="">Seleccione un distrito</option>
                                        @if(isset($districts))
                                            @foreach($districts as $j)
                                                <option value="{{ $j->id }}" {{ old('district_id') == $j->id ? 'selected' : '' }}>{{ $j->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('district_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Línea separadora -->
                    <div class="users-form-divider h-[1px] bg-gray-300 my-4 lg:my-6"></div>

                    <!-- Sección 3: Configuración de cuenta -->
                    <div class="users-form-section mb-6 lg:mb-8">
                        <div class="users-form-section-header flex items-center mb-4">
                            <ion-icon name="settings-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                            <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Configuración de cuenta</h2>
                            <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Usuario <span class="text-red-600">*</span></label>
                                    <input id="username" name="username" type="text" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_.-]+"
                                        class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                        placeholder="Ej: mgarcia"
                                        value="{{ old('username') }}">
                                    @error('username') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <div class="users-password-label-row">
                                        <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Contraseña <span class="text-red-600">*</span></label>
                                        <span id="password-strength" data-level="empty" aria-live="polite">Sin evaluar</span>
                                    </div>
                                    <div class="relative">
                                        <input name="password" type="password" required minlength="12" maxlength="255" autocomplete="new-password"
                                            id="password"
                                            class="w-full px-3 py-2 pr-10 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                            placeholder="Ingrese su contraseña">
                                        <button type="button" 
                                                onclick="togglePassword('password')"
                                                class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-gray-500 hover:text-[#404041] transition-colors duration-200">
                                            <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                                        </button>
                                    </div>
                                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    <div id="password-strength-track" class="users-password-meter-track" role="progressbar"
                                        aria-label="Fortaleza de la contrase&ntilde;a" aria-valuemin="0" aria-valuemax="5" aria-valuenow="0">
                                        <div id="password-strength-bar" data-level="empty"></div>
                                    </div>
                                    <ul class="users-password-compact-requirements" aria-label="Requisitos de seguridad">
                                        <li data-password-rule="length" data-complete="false">
                                            <ion-icon name="ellipse-outline" aria-hidden="true"></ion-icon>
                                            <span>12 caracteres</span>
                                        </li>
                                        <li data-password-rule="case" data-complete="false">
                                            <ion-icon name="ellipse-outline" aria-hidden="true"></ion-icon>
                                            <span>May&uacute;scula y min&uacute;scula</span>
                                        </li>
                                        <li data-password-rule="number" data-complete="false">
                                            <ion-icon name="ellipse-outline" aria-hidden="true"></ion-icon>
                                            <span>Un n&uacute;mero</span>
                                        </li>
                                        <li data-password-rule="special" data-complete="false">
                                            <ion-icon name="ellipse-outline" aria-hidden="true"></ion-icon>
                                            <span>Un s&iacute;mbolo</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Rol <span class="text-red-600">*</span></label>
                                    <select id="role_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="role_id" required>
                                        <option value="">Seleccione un rol</option>
                                        @if(isset($roles))
                                            @foreach($roles as $r)
                                                <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('role_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Confirmar contraseña <span class="text-red-600">*</span></label>
                                    <div class="relative">
                                        <input name="password_confirmation" type="password" required minlength="12" maxlength="255" autocomplete="new-password"
                                            id="password_confirmation"
                                            class="w-full px-3 py-2 pr-10 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                            placeholder="Confirme su contraseña">
                                        <button type="button" 
                                                onclick="togglePassword('password_confirmation')"
                                                class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-gray-500 hover:text-[#404041] transition-colors duration-200">
                                            <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                                        </button>
                                    </div>
                                    @error('password_confirmation') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                    <div class="users-password-confirm-tools">
                                        <span id="password-match-status" data-status="empty" aria-live="polite">
                                            <ion-icon name="ellipse-outline" aria-hidden="true"></ion-icon>
                                            <span>Confirme la contrase&ntilde;a</span>
                                        </span>
                                        <button type="button" onclick="generatePassword()" class="users-generate-password" title="Generar una contrase&ntilde;a segura">
                                            <i class="fa-solid fa-key users-generate-password-icon" aria-hidden="true"></i>
                                            Generar contrase&ntilde;a
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Línea separadora para botones -->
                    <div class="users-form-divider h-[1px] bg-gray-300 my-4 lg:my-6"></div>

                    <label class="inline-flex items-center space-x-2">
                        <!-- is_active is handled server-side (new users are active by default) -->
                    </label>

                    <!-- USAR COMPONENTE DE BOTONES PERSONALIZADO -->
                    <x-form-buttons 
                        class="users-form-actions"
                        primaryText="Guardar registro"
                        secondaryText="Limpiar formulario"
                        secondaryOnclick="clearRegistroForm(event)"
                        primaryType="submit"
                        secondaryType="button"
                    />
            </form>
        </div>
    </div>

    <!-- Scripts para funcionalidades -->
    <script>
        // Función para mostrar/ocultar contraseña
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.parentElement.querySelector('ion-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.name = 'eye-off-outline';
            } else {
                input.type = 'password';
                icon.name = 'eye-outline';
            }
        }

        // Obtener un índice aleatorio sin sesgo usando Web Crypto.
        function secureRandomIndex(max) {
            const range = 0x100000000;
            const limit = range - (range % max);
            const values = new Uint32Array(1);
            let value;

            do {
                window.crypto.getRandomValues(values);
                value = values[0];
            } while (value >= limit);

            return value % max;
        }

        // Mezclar caracteres con Fisher-Yates y aleatoriedad criptográfica.
        function secureShuffle(value) {
            const characters = value.split('');

            for (let i = characters.length - 1; i > 0; i--) {
                const j = secureRandomIndex(i + 1);
                [characters[i], characters[j]] = [characters[j], characters[i]];
            }

            return characters.join('');
        }

        // Función para generar una contraseña segura.
        function generatePassword() {
            const lowercase = 'abcdefghijklmnopqrstuvwxyz';
            const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const numbers = '0123456789';
            const symbols = '!@#$%^&*';
            
            let password = '';
            
            // Asegurar al menos un carácter de cada tipo
            password += uppercase[secureRandomIndex(uppercase.length)];
            password += lowercase[secureRandomIndex(lowercase.length)];
            password += numbers[secureRandomIndex(numbers.length)];
            password += symbols[secureRandomIndex(symbols.length)];
            
            // Completar hasta 12 caracteres
            const allChars = lowercase + uppercase + numbers + symbols;
            for (let i = password.length; i < 12; i++) {
                password += allChars[secureRandomIndex(allChars.length)];
            }
            
            // Mezclar la contraseña sin depender del orden inicial.
            password = secureShuffle(password);
            
            // Establecer en ambos campos
            document.getElementById('password').value = password;
            document.getElementById('password_confirmation').value = password;
            
            // Actualizar indicador de fortaleza
            checkPasswordStrength(password);
            checkPasswordMatch();
        }

        // Función para verificar fortaleza de contraseña
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength');
            const strengthTrack = document.getElementById('password-strength-track');

            const checks = {
                length: password.length >= 12,
                case: /[a-z]/.test(password) && /[A-Z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*]/.test(password)
            };
            
            let strength = 0;
            let level = 'empty';
            let text = 'Sin evaluar';
            
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[!@#$%^&*]/.test(password)) strength++;
            
            switch(strength) {
                case 0:
                    break;
                case 1:
                case 2:
                    level = 'weak';
                    text = 'Débil';
                    break;
                case 3:
                    level = 'medium';
                    text = 'Media';
                    break;
                case 4:
                    level = 'strong';
                    text = 'Fuerte';
                    break;
                case 5:
                    level = 'very-strong';
                    text = 'Muy fuerte';
                    break;
            }
            
            strengthBar.dataset.level = level;
            strengthBar.style.width = `${(strength / 5) * 100}%`;
            strengthText.textContent = text;
            strengthText.dataset.level = level;
            strengthTrack.setAttribute('aria-valuenow', strength);
            strengthTrack.setAttribute('aria-valuetext', text);

            Object.entries(checks).forEach(([rule, complete]) => {
                const item = document.querySelector(`[data-password-rule="${rule}"]`);
                if (!item) return;

                item.dataset.complete = complete ? 'true' : 'false';
                const icon = item.querySelector('ion-icon');
                if (icon) {
                    icon.name = complete ? 'checkmark-circle' : 'ellipse-outline';
                }
            });
        }

        // Verificar que ambos campos de contraseña coincidan.
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmation = document.getElementById('password_confirmation').value;
            const status = document.getElementById('password-match-status');
            const icon = status.querySelector('ion-icon');
            const text = status.querySelector('span');

            if (!confirmation) {
                status.dataset.status = 'empty';
                icon.name = 'ellipse-outline';
                text.textContent = 'Confirme la contraseña';
                return;
            }

            const matches = password === confirmation;
            status.dataset.status = matches ? 'match' : 'mismatch';
            icon.name = matches ? 'checkmark-circle' : 'alert-circle-outline';
            text.textContent = matches ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden';
        }

        // Event listener para verificar contraseña en tiempo real
        document.getElementById('password').addEventListener('input', function(e) {
            checkPasswordStrength(e.target.value);
            checkPasswordMatch();
        });

        document.getElementById('password_confirmation').addEventListener('input', checkPasswordMatch);

        // Inicializar el indicador de fortaleza
        document.addEventListener('DOMContentLoaded', function() {
            checkPasswordStrength('');
        });

            // Función para limpiar todos los campos del formulario actual (específica para registro de usuarios)
            async function clearRegistroForm(event) {
                event.preventDefault();
                // Buscar el formulario más cercano al botón
                const btn = event.currentTarget || event.target;
                const form = btn.closest('form');
                if (!form) return;

                const canClear = window.confirmFormClear
                    ? await window.confirmFormClear(form, 0)
                    : true;

                if (!canClear) return;

                // Resetear el formulario nativo
                form.reset();

                // Limpiar inputs con valor por defecto de JS (por seguridad)
                const elements = form.querySelectorAll('input, select, textarea');
                elements.forEach(el => {
                    if (el.tagName.toLowerCase() === 'select') {
                        el.selectedIndex = 0;
                        if (el.tomselect) {
                            el.tomselect.clear(true);
                            el.tomselect.setTextboxValue('');
                            el.tomselect.refreshItems();
                        }
                    } else if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = false;
                    } else {
                        el.value = '';
                    }
                });

                if (typeof window.resetUsuarioRegistroTomSelects === 'function') {
                    window.resetUsuarioRegistroTomSelects();
                }

                // Resetear el asistente de seguridad.
                checkPasswordStrength('');
                checkPasswordMatch();

                // Enfocar el primer campo
                const firstInput = form.querySelector('input, select, textarea');
                if (firstInput) firstInput.focus();

                if (typeof window.showToast === 'function') {
                    window.showToast('Formulario limpiado.', 'info', 2400);
                }
            }
    </script>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Tom Select (only used for position select here) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Make Tom Select control visually match your existing Tailwind input styles */
        /* Tom Select control: try to match exact paddings/height of your native selects */
        .ts-wrapper {
            /* remove wrapper border so there's only one visible border (the control itself) */
            border: none !important;
            padding: 0 !important;
            background: transparent !important;
        }

        /* Hide original select elements that are enhanced by Tom Select to avoid native arrow/box
           (these selects have class "tomselect-select" in the template). We prefer an
           accessible hide via the `sr-only` utility so the element remains in the DOM
           for screen readers and form semantics. */
        select.tomselect-select {
            /* keep a minimal visual removal - TomSelect will still use the element */
            position: absolute !important;
            left: -9999px !important;
            width: 1px !important;
            height: 1px !important;
            overflow: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
            border: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
        }

        /* remove the IE dropdown arrow */
        select.tomselect-select::-ms-expand { display: none !important; }
        /* safety: some browsers render a native background image for select arrows */
        select.tomselect-select { background-image: none !important; }

        .ts-wrapper { display: block; width: 100%; }

        .ts-control {
            /* Mirror your Tailwind input: use the same padding/line-height and let height be automatic */
            border: 1px solid #d1d5db !important; /* gray-300 */
            border-radius: 0.5rem !important; /* rounded-lg */
            padding: 8px 12px !important; /* px-3 py-2 equivalent */
            background: #ffffff !important;
            font-family: inherit;
            font-size: 0.875rem; /* 14px */
            line-height: 1.25rem !important;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            position: relative;
            box-sizing: border-box;
            margin: 0 !important;
            /* remove any inner shadow so it matches native selects exactly */
            box-shadow: none !important;
            height: auto !important;
            min-height: 36px !important;
        }
        /* internal input/item inside Tom Select: remove extra padding and align line-height */
        .ts-control .item, .ts-control input {
            padding: 0 !important;
            margin: 0 !important;
            height: auto !important;
            line-height: 1.25rem !important;
            font-size: inherit;
            font-family: inherit;
        }
        /* hide common internal toggle elements Tom Select may use (best-effort) */
        .ts-control .dropdown-toggle,
        .ts-control .ts-dropdown-toggle,
        .ts-control .dropdown_toggle,
        .ts-control .ts-clear {
            display: none !important;
        }
        .ts-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            max-height: 240px;
            overflow: auto;
        }
        .ts-dropdown .ts-option {
            padding: 0.5rem 0.75rem;
        }
        /* caret we will inject (right side) */
        .tomselect-caret {
            display: none !important; /* hide injected caret so native arrow remains */
            position: absolute;
            right: 12px; /* align with native select padding */
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280; /* gray-500 */
            pointer-events: none;
            font-size: 0.9rem;
        }

        /* Draw a native-looking chevron on Tom Select controls so they match other selects.
           This uses an inline SVG encoded as a data URI to mimic the thin outlined chevron. */
        .ts-control::after {
            content: "";
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 12px 12px;
            pointer-events: none;
            opacity: 0.92;
        }

        /* Small tweak for exact vertical alignment: ensure the Tom Select control aligns to baseline
           with other form controls in the grid */
        .ts-wrapper, .ts-control { vertical-align: middle; }

        /* Target the Tom Select control generated next to each original select by ID
           and nudge the death municipality control 1px up if still slightly lower. */
        /* Ensure generated TomSelect controls match their original select container width
           and don't receive manual transforms that break alignment. */
        #residence_municipality_select + .ts-control,
        #death_municipality_select + .ts-control,
        #position_select + .ts-control {
            padding: 8px 12px !important;
            height: auto !important;
            min-height: 36px !important;
            transform: none !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const pos = document.getElementById('position_select');
            if (pos) {
                try {
                    new TomSelect(pos, {
                        valueField: 'value',
                        labelField: 'text',
                        searchField: ['text'],
                        create: false,
                        maxItems: 1,
                        preload: false,
                        maxOptions: 50
                    });
                } catch (e) {
                    console.warn('TomSelect init failed for #position_select', e);
                }
            }

            const dist = document.getElementById('district_id');
            if (dist) {
                try {
                    new TomSelect(dist, {
                        valueField: 'value',
                        labelField: 'text',
                        searchField: ['text'],
                        create: false,
                        maxItems: 1,
                        preload: false,
                        maxOptions: 50
                    });
                } catch (e) {
                    console.warn('TomSelect init failed for #district_id', e);
                }
            }

            const role = document.getElementById('role_id');
            if (role) {
                try {
                    new TomSelect(role, {
                        valueField: 'value',
                        labelField: 'text',
                        searchField: [],
                        create: false,
                        maxItems: 1,
                        preload: false,
                        maxOptions: 10
                    });
                } catch (e) {
                    console.warn('TomSelect init failed for #role_id', e);
                }
            }

            function getTomSelectWrapper(select) {
                return select?.tomselect?.wrapper || select?.nextElementSibling || null;
            }

            function getTomSelectValidityInput(select) {
                return select?.tomselect?.control_input || select?.tomselect?.input || select || null;
            }

            function showTomSelectError(select, message) {
                const validityInput = getTomSelectValidityInput(select);
                if (validityInput?.setCustomValidity) {
                    validityInput.setCustomValidity(message);
                }
            }

            function clearTomSelectError(select) {
                const validityInput = getTomSelectValidityInput(select);
                if (validityInput?.setCustomValidity) {
                    validityInput.setCustomValidity('');
                }
            }

            function reportTomSelectValidity(select) {
                if (select?.tomselect) {
                    select.tomselect.focus();
                }

                const validityInput = getTomSelectValidityInput(select);
                if (validityInput?.reportValidity) {
                    validityInput.reportValidity();
                } else if (select?.reportValidity) {
                    select.reportValidity();
                }
            }

            function validateRequiredTomSelect(select, message) {
                if (!select || !select.hasAttribute('required') || select.value) {
                    if (select) clearTomSelectError(select);
                    return true;
                }

                showTomSelectError(select, message);
                return false;
            }

            function validateUsuarioRegistroTomSelects(focusFirst = false) {
                const requiredSelects = [
                    { select: pos, message: 'Seleccione un cargo.' },
                    { select: dist, message: 'Seleccione un distrito.' },
                    { select: role, message: 'Seleccione un rol.' },
                ];
                let firstInvalid = null;

                requiredSelects.forEach(({ select, message }) => {
                    if (!validateRequiredTomSelect(select, message) && !firstInvalid) {
                        firstInvalid = select;
                    }
                });

                if (firstInvalid && focusFirst) {
                    reportTomSelectValidity(firstInvalid);
                }

                return !firstInvalid;
            }

            window.resetUsuarioRegistroTomSelects = function() {
                [pos, dist, role].forEach(select => {
                    clearTomSelectError(select);
                    if (select?.tomselect) {
                        select.tomselect.clear(true);
                        select.tomselect.setTextboxValue('');
                        select.tomselect.refreshItems();
                    } else if (select) {
                        select.value = '';
                    }
                });
            };

            [pos, dist, role].forEach(select => {
                if (!select) return;
                select.addEventListener('change', () => clearTomSelectError(select));
                select.addEventListener('invalid', function(event) {
                    event.preventDefault();
                    const message = select === pos
                        ? 'Seleccione un cargo.'
                        : select === dist
                            ? 'Seleccione un distrito.'
                            : 'Seleccione un rol.';
                    showTomSelectError(select, message);
                    requestAnimationFrame(() => reportTomSelectValidity(select));
                });
                if (select.tomselect) {
                    select.tomselect.on('change', () => clearTomSelectError(select));
                }
            });

            form?.addEventListener('submit', function(event) {
                if (!validateUsuarioRegistroTomSelects(true)) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        });
    </script>

    <!-- Script para convertir errores de Laravel en tooltips HTML5 -->
    <script>
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
                    'position_id': 'position_select',
                    'district_id': 'district_id'
                };

                // Aplicar setCustomValidity a cada campo con error
                Object.keys(errors).forEach(fieldName => {
                    const fieldId = fieldMap[fieldName];
                    if (fieldId) {
                        const field = document.getElementById(fieldId);
                        if (field && errors[fieldName][0]) {
                            field.setCustomValidity(errors[fieldName][0]);
                            // Hacer que el campo muestre el tooltip
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

            // Validación personalizada: coincidencia de contraseñas
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('password_confirmation');
            
            if (passwordField && confirmPasswordField) {
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
            }
        });
    </script>
@endsection
