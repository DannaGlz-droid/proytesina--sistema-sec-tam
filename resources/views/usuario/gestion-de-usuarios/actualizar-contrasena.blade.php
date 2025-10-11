@extends('layouts.principal')
@section('title', 'Actualizar Contraseña')
@section('content')

    @include('components.header')
    @include('components.nav-usuario')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-3">Actualizar contraseña de usuario</h1>
        <p class="text-sm lg:text-base text-[#404041] font-lora mb-6">Modifique la contraseña de acceso para el usuario registrado.</p>

        <!-- Información del usuario -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex items-center">
                <ion-icon name="person-circle-outline" class="text-2xl text-blue-500 mr-3"></ion-icon>
                <div>
                    <h3 class="text-sm lg:text-base font-medium text-[#404041]">
                        Usuario: <span class="font-bold">{{ $usuario->nombres ?? 'Nombre' }} {{ $usuario->apellido_paterno ?? 'Apellido' }}</span>
                    </h3>
                    <p class="text-xs text-gray-600 mt-1">
                        Correo: {{ $usuario->email ?? 'usuario@ejemplo.com' }} | 
                        Usuario: {{ $usuario->username ?? 'username' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Cuadro del formulario responsive -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl p-4 lg:p-6 bg-white bg-opacity-95 max-w-7xl shadow-md">
            
            <!-- Sección: Contraseña -->
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center mb-4">
                    <ion-icon name="lock-closed-outline" class="text-xl lg:text-xl text-[#404041] mr-2"></ion-icon>
                    <h2 class="text-lg lg:text-xl font-lora font-bold text-[#404041]">Configuración de contraseña</h2>
                    <div class="flex-1 h-[1px] bg-[#404041] ml-3"></div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Nueva contraseña *</label>
                            <div class="relative">
                                <input type="password" 
                                       id="password"
                                       class="w-full px-3 py-2 pr-10 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                       placeholder="Ingrese la nueva contraseña">
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
                            <label class="block text-xs lg:text-sm font-medium text-[#404041] mb-1">Confirmar contraseña *</label>
                            <div class="relative">
                                <input type="password" 
                                       id="password_confirmation"
                                       class="w-full px-3 py-2 pr-10 text-xs lg:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#404041] focus:border-transparent transition-all duration-200" 
                                       placeholder="Confirme la nueva contraseña">
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
                        
                        <span id="password-strength" class="text-xs font-medium">-</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="password-strength-bar" class="h-2 rounded-full transition-all duration-300"></div>
                    </div>
                </div>

                <!-- Requisitos de seguridad -->
                <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-xs lg:text-sm font-medium text-[#404041] mb-2">Requisitos de seguridad:</h3>
                    <ul class="text-xs text-gray-600 space-y-1">
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
                <div class="mt-4 flex justify-end">
                    <button type="button" 
                            onclick="generatePassword()"
                            class="text-xs px-3 py-1 border border-[#404041] text-[#404041] rounded-lg hover:bg-[#404041] hover:text-white transition-all duration-200 flex items-center">
                        <ion-icon name="refresh-outline" class="mr-1"></ion-icon>
                        Generar contraseña
                    </button>
                </div>
            </div>

            <!-- Línea separadora para botones -->
            <div class="h-[1px] bg-gray-300 my-4 lg:my-6"></div>

            <!-- Botones responsive -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 lg:gap-4">
                <button type="button" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 text-xs lg:text-sm border border-[#404041] text-[#404041] font-medium rounded-lg hover:bg-[#404041] hover:text-white transition-all duration-200">
                    Volver al listado
                </button>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 lg:px-6 py-2 text-xs lg:text-sm bg-[#404041] text-white font-medium rounded-lg hover:bg-[#2a2a2a] transition-all duration-200">
                    Actualizar contraseña
                </button>
            </div>
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
                    color = 'bg-blue-500';
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
            strengthText.className = `text-xs font-medium ${
                strength <= 2 ? 'text-red-500' : 
                strength <= 3 ? 'text-yellow-500' : 
                strength <= 4 ? 'text-blue-500' : 'text-green-500'
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
    </script>

    <!-- Incluir Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@endsection