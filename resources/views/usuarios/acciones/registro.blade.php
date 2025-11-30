@extends('layouts.principal')
@section('title', 'Registrar Usuario')
@section('content')

    @include('components.header-admin')
    @include('components.nav-usuario')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Registro de usuario</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Complete el formulario para registrar un usuario en el sistema.</p>

        <!-- Cuadro del formulario responsive -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">

            <form action="{{ route('user.store') }}" method="POST" id="userRegistroForm">
                    
                @csrf
                    <!-- Sección 1: Información del usuario -->
                    <div class="mb-6 lg:mb-8">
                        <div class="flex items-center mb-4">
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
                                </div>
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido materno</label>
                                    <input id="second_last_name" name="second_last_name" type="text" minlength="2" maxlength="191"
                                        class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                        placeholder="Ej: López"
                                        value="{{ old('second_last_name') }}">
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Apellido paterno <span class="text-red-600">*</span></label>
                                    <input id="first_last_name" name="first_last_name" type="text" required minlength="2" maxlength="191"
                                        class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                        placeholder="Ej: García"
                                        value="{{ old('first_last_name') }}">
                                </div>
                                <div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Correo electrónico <span class="text-red-600">*</span></label>
                                            <input id="email" name="email" type="email" required maxlength="255" autocomplete="email"
                                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                                placeholder="Ej: usuario@ejemplo.com"
                                                value="{{ old('email') }}">
                                        </div>
                                        <div>
                                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Teléfono</label>
                                            <input id="phone" name="phone" type="tel" maxlength="20" pattern="[0-9+\-\(\)\s]{8,20}" inputmode="tel" autocomplete="tel"
                                                class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora"
                                                placeholder="Ej: 8123456789"
                                                value="{{ old('phone') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Línea separadora -->
                    <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

                    <!-- Sección 2: Información laboral -->
                    <div class="mb-6 lg:mb-8">
                        <div class="flex items-center mb-4">
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
                                        <option value="0">No definido</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Jurisdicción <span class="text-red-600">*</span></label>
                                    <select id="jurisdiction_id" class="w-full px-3 py-2 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" name="jurisdiction_id" required>
                                        <option value="">Seleccione una jurisdicción</option>
                                        @if(isset($jurisdictions))
                                            @foreach($jurisdictions as $j)
                                                <option value="{{ $j->id }}" {{ old('jurisdiction_id') == $j->id ? 'selected' : '' }}>{{ $j->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Línea separadora -->
                    <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

                    <!-- Sección 3: Configuración de cuenta -->
                    <div class="mb-6 lg:mb-8">
                        <div class="flex items-center mb-4">
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
                                </div>
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Contraseña <span class="text-red-600">*</span></label>
                                    <div class="relative">
                                        <input name="password" type="password" required minlength="6"
                                            id="password"
                                            class="w-full px-3 py-2 pr-10 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                            placeholder="Ingrese su contraseña">
                                        <button type="button" 
                                                onclick="togglePassword('password')"
                                                class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-gray-500 hover:text-[#404041] transition-colors duration-200">
                                            <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                                        </button>
                                    </div>
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
                                </div>
                                <div>
                                    <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1 font-lora">Confirmar contraseña <span class="text-red-600">*</span></label>
                                    <div class="relative">
                                        <input name="password_confirmation" type="password" required minlength="6"
                                            id="password_confirmation"
                                            class="w-full px-3 py-2 pr-10 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200 font-lora" 
                                            placeholder="Confirme su contraseña">
                                        <button type="button" 
                                                onclick="togglePassword('password_confirmation')"
                                                class="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-gray-500 hover:text-[#404041] transition-colors duration-200">
                                            <ion-icon name="eye-outline" class="text-lg"></ion-icon>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Indicador de fortaleza de contraseña -->
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-1">
                                <span id="password-strength" class="text-xs font-medium font-lora">-</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="password-strength-bar" class="h-2 rounded-full transition-all duration-300"></div>
                            </div>
                        </div>

                        <!-- Requisitos de seguridad -->
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <h3 class="text-xs lg:text-sm font-medium text-[#404041] mb-2 font-lora">Requisitos de seguridad:</h3>
                            <ul class="text-xs text-gray-600 space-y-1 font-lora">
                                <li class="flex items-center">
                                    <ion-icon name="checkmark-circle-outline" class="text-green-500 mr-2"></ion-icon>
                                    Mínimo 8 caracteres
                                </li>
                                <li class="flex items-center">
                                    <ion-icon name="checkmark-circle-outline" class="text-green-500 mr-2"></ion-icon>
                                    Al menos una letra mayúscula y una minúscula
                                </li>
                                <li class="flex items-center">
                                    <ion-icon name="checkmark-circle-outline" class="text-green-500 mr-2"></ion-icon>
                                    Incluir al menos un número
                                </li>
                                <li class="flex items-center">
                                    <ion-icon name="checkmark-circle-outline" class="text-green-500 mr-2"></ion-icon>
                                    Incluir al menos un carácter especial (!@#$%^&*)
                                </li>
                            </ul>
                        </div>

                        <!-- Botón para generar contraseña -->
                                <div class="mt-4 flex justify-start">
                                    <button type="button"
                                            onclick="generatePassword()"
                                            class="text-xs px-3 py-1 border border-[#404041] text-[#404041] rounded-lg hover:bg-[#404041] hover:text-white transition-all duration-200 flex items-center mr-3 font-lora">
                                        <ion-icon name="refresh-outline" class="mr-1"></ion-icon>
                                        Generar contraseña
                                    </button>
                                </div>
                    </div>

                    <!-- Línea separadora para botones -->
                    <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

                    <label class="inline-flex items-center space-x-2">
                        <!-- is_active is handled server-side (new users are active by default) -->
                    </label>

                    <!-- USAR COMPONENTE DE BOTONES PERSONALIZADO -->
                    <x-form-buttons 
                        primaryText="Guardar registro"
                        secondaryText="Limpiar formulario"
                        secondaryOnclick="clearRegistroForm(event)"
                        tertiaryText="Volver al listado"
                        tertiaryHref="{{ route('user.user-gestion') }}"
                        primaryType="submit"
                        secondaryType="button"
                    />
                </div>

            </form>
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

        // Función para generar contraseña segura
        function generatePassword() {
            const lowercase = 'abcdefghijklmnopqrstuvwxyz';
            const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const numbers = '0123456789';
            const symbols = '!@#$%^&*';
            
            let password = '';
            
            // Asegurar al menos un carácter de cada tipo
            password += uppercase[Math.floor(Math.random() * uppercase.length)];
            password += lowercase[Math.floor(Math.random() * lowercase.length)];
            password += numbers[Math.floor(Math.random() * numbers.length)];
            password += symbols[Math.floor(Math.random() * symbols.length)];
            
            // Completar hasta 12 caracteres
            const allChars = lowercase + uppercase + numbers + symbols;
            for (let i = password.length; i < 12; i++) {
                password += allChars[Math.floor(Math.random() * allChars.length)];
            }
            
            // Mezclar la contraseña
            password = password.split('').sort(() => Math.random() - 0.5).join('');
            
            // Establecer en ambos campos
            document.getElementById('password').value = password;
            document.getElementById('password_confirmation').value = password;
            
            // Actualizar indicador de fortaleza
            checkPasswordStrength(password);
        }

        // Función para verificar fortaleza de contraseña
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength');
            
            let strength = 0;
            let color = '';
            let text = '';
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[!@#$%^&*]/.test(password)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    color = 'bg-red-500';
                    text = 'Débil';
                    break;
                case 2:
                case 3:
                    color = 'bg-yellow-500';
                    text = 'Media';
                    break;
                case 4:
                    color = 'bg-gray-600';
                    text = 'Fuerte';
                    break;
                case 5:
                    color = 'bg-green-500';
                    text = 'Muy Fuerte';
                    break;
            }
            
            strengthBar.className = `h-2 rounded-full transition-all duration-300 ${color}`;
            strengthBar.style.width = `${(strength / 5) * 100}%`;
            strengthText.textContent = text;
            strengthText.className = `text-xs font-medium font-lora ${
                strength <= 2 ? 'text-red-500' : 
                strength <= 3 ? 'text-yellow-500' : 
                strength <= 4 ? 'text-gray-600' : 'text-green-500'
            }`;
        }

        // Event listener para verificar contraseña en tiempo real
        document.getElementById('password').addEventListener('input', function(e) {
            checkPasswordStrength(e.target.value);
        });

        // Inicializar el indicador de fortaleza
        document.addEventListener('DOMContentLoaded', function() {
            checkPasswordStrength('');
        });

            // Función para limpiar todos los campos del formulario actual (específica para registro de usuarios)
            function clearRegistroForm(event) {
                event.preventDefault();
                // Buscar el formulario más cercano al botón
                const btn = event.currentTarget || event.target;
                const form = btn.closest('form');
                if (!form) return;

                // Resetear el formulario nativo
                form.reset();

                // Limpiar inputs con valor por defecto de JS (por seguridad)
                const elements = form.querySelectorAll('input, select, textarea');
                elements.forEach(el => {
                    if (el.tagName.toLowerCase() === 'select') {
                        el.selectedIndex = 0;
                    } else if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = false;
                    } else {
                        el.value = '';
                    }
                });

                // Resetear indicadores de fortaleza y texto
                const strengthBar = document.getElementById('password-strength-bar');
                const strengthText = document.getElementById('password-strength');
                if (strengthBar) {
                    strengthBar.className = 'h-2 rounded-full transition-all duration-300';
                    strengthBar.style.width = '0%';
                }
                if (strengthText) {
                    strengthText.textContent = '-';
                    strengthText.className = 'text-xs font-medium font-lora';
                }

                // Enfocar el primer campo
                const firstInput = form.querySelector('input, select, textarea');
                if (firstInput) firstInput.focus();
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
        /* (No chevron unification) keep native select appearance for non-TomSelect selects */
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    'jurisdiction_id': 'jurisdiction_id'
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