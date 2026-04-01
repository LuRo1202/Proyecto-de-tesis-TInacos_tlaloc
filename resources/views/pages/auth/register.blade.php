@php
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro - Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <style>
        :root {
            --primary: #7fad39;
            --primary-dark: #5a8a20;
            --primary-light: #9fc957;
            --light: #f8f9fa;
            --light-gray: #e9ecef;
            --gray: #6c757d;
            --dark: #212529;
        }
        
        body {
            background-color: var(--light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
            background: linear-gradient(135deg, #f0f2f5 0%, #e9ecef 100%);
        }
        
        .register-container {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }
        
        .register-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--light-gray);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .register-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
        }
        
        .register-header {
            background: var(--primary);
            padding: 40px 30px 30px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .register-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        
        .register-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        
        .register-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .register-body {
            padding: 40px 30px 30px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .input-group {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        
        .input-group-text {
            background: var(--light-gray);
            border: 1px solid #dee2e6;
            color: var(--gray);
            padding: 12px 16px;
            border-right: none;
        }
        
        .form-control {
            border: 1px solid #dee2e6;
            border-left: none;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(127, 173, 57, 0.15);
            border-color: var(--primary);
        }
        
        .form-control:focus + .input-group-text {
            border-color: var(--primary);
        }
        
        .btn-register {
            background: var(--primary);
            border: none;
            color: white;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s ease;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-register:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(127, 173, 57, 0.3);
        }
        
        .btn-register:active:not(:disabled) {
            transform: translateY(0);
        }
        
        .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .login-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            padding: 8px 0;
        }
        
        .login-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .login-link i {
            margin-right: 6px;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--gray);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .back-link:hover {
            color: var(--primary);
        }
        
        .back-link i {
            margin-right: 5px;
        }
        
        .form-text {
            color: var(--gray);
            font-size: 0.85rem;
            margin-top: 25px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }
        
        .password-requirements {
            background: var(--light);
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 10px;
            font-size: 0.8rem;
        }
        
        .password-requirements small {
            display: block;
            margin-bottom: 3px;
        }
        
        .password-requirements i {
            width: 16px;
            margin-right: 5px;
        }
        
        .password-requirements .valid {
            color: #28a745;
        }
        
        .password-requirements .invalid {
            color: #6c757d;
        }
        
        @media (max-width: 576px) {
            body {
                padding: 15px;
                background: var(--light);
            }
            
            .register-container {
                max-width: 100%;
            }
            
            .register-header {
                padding: 30px 20px;
            }
            
            .register-body {
                padding: 30px 20px;
            }
            
            .logo-container {
                width: 70px;
                height: 70px;
            }
            
            .logo-container img {
                width: 50px;
                height: 50px;
            }
            
            .register-title {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 375px) {
            .register-header {
                padding: 25px 15px;
            }
            
            .register-body {
                padding: 25px 15px;
            }
            
            .form-control,
            .input-group-text {
                padding: 10px 14px;
                font-size: 0.95rem;
            }
            
            .btn-register {
                padding: 12px;
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .register-card {
            animation: fadeIn 0.5s ease-out;
        }
        
        .btn-loading {
            position: relative;
            color: transparent !important;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
        
        .is-invalid {
            border-color: #dc3545 !important;
        }
        
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }
        /* Barra de fortaleza de contraseña */
        .strength-meter {
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 3px;
        }

        .strength-meter-fill.weak {
            background-color: #dc3545;
            width: 20%;
        }

        .strength-meter-fill.fair {
            background-color: #ffc107;
            width: 40%;
        }

        .strength-meter-fill.good {
            background-color: #17a2b8;
            width: 60%;
        }

        .strength-meter-fill.strong {
            background-color: #28a745;
            width: 80%;
        }

        .strength-meter-fill.very-strong {
            background-color: #20c997;
            width: 100%;
        }

        /* Estilos para requisitos */
        .password-requirements .valid i {
            color: #28a745;
        }

        .password-requirements .invalid i {
            color: #6c757d;
        }

        /* Tooltips de ayuda */
        .requirement-help {
            cursor: help;
            border-bottom: 1px dotted #6c757d;
        }

        /* Animación para sugerencias */
        .suggestion-btn {
            transition: all 0.2s ease;
        }

        .suggestion-btn:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

                /* Estilo para el botón generador */
        .input-group .btn-outline-secondary {
            border-color: #dee2e6;
            background: white;
            padding: 12px 16px;
            z-index: 5;
        }

        .input-group .btn-outline-secondary:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .input-group .btn-outline-secondary i {
            font-size: 1rem;
        }

        /* Animación al hacer clic */
        @keyframes shake {
            0%, 100% { transform: rotate(0); }
            25% { transform: rotate(15deg); }
            75% { transform: rotate(-15deg); }
        }

        .input-group .btn-outline-secondary:active i {
            animation: shake 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tláloc">
                </div>
                <h1 class="register-title">Crear Cuenta</h1>
                <p class="register-subtitle">Compra fácil, rápido y seguro</p>
            </div>
            
            <div class="register-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Por favor corrige los siguientes errores:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('cliente.register.store') }}" id="formRegistro">
                    
                    @csrf
                    <input type="hidden" name="redirect_to" value="{{ request()->get('redirect_to') }}">
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label">Nombre completo *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" 
                                   name="nombre" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   placeholder="Ej: Jose Pérez"
                                   value="{{ old('nombre') }}"
                                   required
                                   autofocus>
                        </div>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" 
                                   name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="correo@ejemplo.com"
                                   value="{{ old('email') }}"
                                   required>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Teléfono -->
                    <div class="mb-3">
                        <label class="form-label">Teléfono *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" 
                                   name="telefono" 
                                   class="form-control @error('telefono') is-invalid @enderror" 
                                   placeholder="55 1234 5678"
                                   value="{{ old('telefono') }}"
                                   required>
                        </div>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Contraseña *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" 
                                name="password" 
                                id="password"
                                class="form-control @error('password') is-invalid @enderror" 
                                placeholder="Crea una contraseña segura"
                                required>
                            <!-- BOTÓN GENERADOR DE CONTRASEÑA (NUEVO) -->
                            <button type="button" 
                                    class="btn btn-outline-secondary" 
                                    onclick="generarContraseña()"
                                    title="Generar contraseña segura"
                                    style="border: 1px solid #dee2e6; border-left: none; border-right: none; border-radius: 0;">
                                <i class="fas fa-key"></i>
                            </button>
                            <span class="input-group-text password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Requisitos de contraseña mejorados -->
                        <div class="password-requirements" id="passwordRequirements">
                            <small class="fw-bold text-muted mb-2 d-block">
                                <i class="fas fa-shield-alt me-1"></i>
                                Tu contraseña debe tener:
                            </small>
                            
                            <!-- Barra de fortaleza -->
                            <div class="strength-meter mb-3">
                                <div class="strength-meter-fill" id="strengthFill"></div>
                            </div>
                            <small class="d-block text-center mb-2" id="strengthText">Ingresa una contraseña</small>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <small id="req-length" class="invalid">
                                        <i class="fas fa-circle"></i> 8+ caracteres
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small id="req-number" class="invalid">
                                        <i class="fas fa-circle"></i> números
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small id="req-uppercase" class="invalid">
                                        <i class="fas fa-circle"></i> mayúsculas
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small id="req-lowercase" class="invalid">
                                        <i class="fas fa-circle"></i> minúsculas
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small id="req-special" class="invalid">
                                        <i class="fas fa-circle"></i> 1 carácter especial
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small id="req-no-sequential" class="invalid">
                                        <i class="fas fa-circle"></i> Sin secuencias (123, abc)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password -->
                    
                    
                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label class="form-label">Confirmar contraseña *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation"
                                   class="form-control" 
                                   placeholder="Repite tu contraseña"
                                   required>
                            <span class="input-group-text password-toggle" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                            </span>
                        </div>
                        <div id="passwordMatch" class="invalid-feedback" style="display: none;">
                            Las contraseñas no coinciden
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-register" id="btnRegistro">
                        <i class="fas fa-user-plus me-2"></i>Crear cuenta
                    </button>
                    
                    <a href="{{ route('login', ['redirect_to' => request()->get('redirect_to')]) }}" class="login-link">
                        <i class="fas fa-arrow-left me-1"></i>¿Ya tienes cuenta? Inicia sesión
                    </a>
                    
                    <a href="{{ route('home') }}" class="back-link">
                        <i class="fas fa-home"></i> Volver a la tienda
                    </a>
                    
                    <p class="form-text">
                        <i class="fas fa-shield-alt me-1"></i>
                        Tus datos están seguros con nosotros
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // Función para mostrar/ocultar contraseña
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = inputId === 'password' ? 
                document.getElementById('togglePasswordIcon') : 
                document.getElementById('toggleConfirmIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Función para actualizar cada requisito
        function updateRequirement(elementId, isValid, validText, invalidText) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            // Limpiar clases
            element.classList.remove('valid', 'invalid');
            
            if (isValid) {
                element.classList.add('valid');
                element.innerHTML = `<i class="fas fa-check-circle"></i> ${validText}`;
            } else {
                element.classList.add('invalid');
                element.innerHTML = `<i class="fas fa-circle"></i> ${invalidText}`;
            }
        }

        // Función para calcular fortaleza
        function calculateStrength(validations) {
            const weights = {
                length: 2,
                number: 1,
                uppercase: 1,
                lowercase: 1,
                special: 2,
                noSequential: 1
            };
            
            let score = 0;
            let maxScore = 0;
            
            for (let [key, value] of Object.entries(validations)) {
                maxScore += weights[key];
                if (value) score += weights[key];
            }
            
            // Normalizar a 0-5
            const normalizedScore = Math.round((score / maxScore) * 5);
            
            let level = '';
            let color = '';
            
            if (normalizedScore <= 1) {
                level = 'Muy débil';
                color = '#dc3545';
            } else if (normalizedScore === 2) {
                level = 'Débil';
                color = '#ff6b6b';
            } else if (normalizedScore === 3) {
                level = 'Regular';
                color = '#ffc107';
            } else if (normalizedScore === 4) {
                level = 'Buena';
                color = '#17a2b8';
            } else if (normalizedScore === 5) {
                level = 'Excelente';
                color = '#28a745';
            }
            
            return { score: normalizedScore, level, color };
        }

        // Función para actualizar medidor de fortaleza
        function updateStrengthMeter(strength) {
            const fill = document.getElementById('strengthFill');
            const text = document.getElementById('strengthText');
            
            if (!fill || !text) return;
            
            // Reset clases
            fill.className = 'strength-meter-fill';
            
            // Aplicar clase según fortaleza
            if (strength.score <= 1) {
                fill.classList.add('weak');
                text.innerHTML = `<span style="color: ${strength.color}">🔴 Contraseña ${strength.level}</span>`;
            } else if (strength.score === 2) {
                fill.classList.add('fair');
                text.innerHTML = `<span style="color: ${strength.color}">🟠 Contraseña ${strength.level}</span>`;
            } else if (strength.score === 3) {
                fill.classList.add('good');
                text.innerHTML = `<span style="color: ${strength.color}">🟡 Contraseña ${strength.level}</span>`;
            } else if (strength.score === 4) {
                fill.classList.add('strong');
                text.innerHTML = `<span style="color: ${strength.color}">🟢 Contraseña ${strength.level}</span>`;
            } else if (strength.score === 5) {
                fill.classList.add('very-strong');
                text.innerHTML = `<span style="color: ${strength.color}">💚 Contraseña ${strength.level}</span>`;
            }
        }

        // Función para usar sugerencia
        function useSuggestion(btn) {
            const suggestion = btn.textContent;
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            
            passwordInput.value = suggestion;
            confirmInput.value = suggestion;
            
            // Disparar eventos
            passwordInput.dispatchEvent(new Event('input'));
            confirmInput.dispatchEvent(new Event('input'));
            
            Swal.fire({
                icon: 'info',
                title: 'Contraseña sugerida',
                text: 'Hemos usado la contraseña sugerida. Puedes modificarla si lo deseas.',
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Validación principal de contraseña
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            
            // Validaciones completas
            const validations = {
                length: password.length >= 8,
                number: /\d/.test(password),
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>_\-+=/\\[\]~`]/.test(password),
                noSequential: !/(?:012|123|234|345|456|567|678|789|890|abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i.test(password)
            };
            
            // Actualizar cada requisito
            updateRequirement('req-length', validations.length, '8+ caracteres ', '8+ caracteres');
            updateRequirement('req-number', validations.number, 'números ', 'números');
            updateRequirement('req-uppercase', validations.uppercase, 'mayúsculas ', 'mayúsculas');
            updateRequirement('req-lowercase', validations.lowercase, 'minúsculas ', 'minúsculas');
            updateRequirement('req-special', validations.special, '1 carácter especial ', '1 carácter especial');
            updateRequirement('req-no-sequential', validations.noSequential, 'Sin secuencias ', 'Sin secuencias (123, abc)');
            
            // Calcular y mostrar fortaleza
            const strength = calculateStrength(validations);
            updateStrengthMeter(strength);
            
            // Mostrar/ocultar sugerencias
            const suggestionsBox = document.getElementById('suggestionsBox');
            if (suggestionsBox) {
                if (password.length > 0 && strength.score < 3) {
                    suggestionsBox.style.display = 'block';
                } else {
                    suggestionsBox.style.display = 'none';
                }
            }
            
            // Validar coincidencia
            const confirm = document.getElementById('password_confirmation').value;
            if (confirm) {
                validarCoincidencia();
            }
        });

        // Validar coincidencia de contraseñas
        document.getElementById('password_confirmation').addEventListener('input', validarCoincidencia);
        
        function validarCoincidencia() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirm && password !== confirm) {
                matchDiv.style.display = 'block';
                return false;
            } else {
                matchDiv.style.display = 'none';
                return true;
            }
        }

        // Validar formulario antes de enviar (VERSIÓN COMPLETA)
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            
            // Validar TODOS los requisitos
            const validations = {
                length: password.length >= 8,
                number: /\d/.test(password),
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>_\-+=/\\[\]~`]/.test(password),
                noSequential: !/(?:012|123|234|345|456|567|678|789|890|abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i.test(password)
            };
            
            const allValid = Object.values(validations).every(v => v === true);
            
            if (!allValid) {
                e.preventDefault();
                
                // Construir lista de lo que falta
                let missingItems = [];
                if (!validations.length) missingItems.push('🔴 8+ caracteres');
                if (!validations.number) missingItems.push('🔴 1 número');
                if (!validations.uppercase) missingItems.push('🔴 1 mayúscula');
                if (!validations.lowercase) missingItems.push('🔴 1 minúscula');
                if (!validations.special) missingItems.push('🔴 1 carácter especial');
                if (!validations.noSequential) missingItems.push('🔴 Sin secuencias (123, abc)');
                
                Swal.fire({
                    title: 'Contraseña no segura',
                    html: `
                        <p>Tu contraseña necesita:</p>
                        <ul style="text-align: left; margin-top: 10px;">
                            ${missingItems.map(item => `<li>${item}</li>`).join('')}
                        </ul>
                    `,
                    icon: 'warning',
                    confirmButtonColor: '#7fad39',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
            
            // Validar coincidencia
            if (password !== confirm) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'Las contraseñas no coinciden',
                    icon: 'error',
                    confirmButtonColor: '#7fad39'
                });
                return false;
            }
        });

                // Generador de contraseñas seguras (AGREGAR ESTO)
        function generarContraseña() {
            // Configuración
            const longitud = 12;
            const mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const minusculas = 'abcdefghijklmnopqrstuvwxyz';
            const numeros = '0123456789';
            const especiales = '!@#$%^&*_+-=';
            
            let contraseña = '';
            
            // Asegurar al menos uno de cada tipo
            contraseña += mayusculas[Math.floor(Math.random() * mayusculas.length)];
            contraseña += minusculas[Math.floor(Math.random() * minusculas.length)];
            contraseña += numeros[Math.floor(Math.random() * numeros.length)];
            contraseña += especiales[Math.floor(Math.random() * especiales.length)];
            
            // Completar el resto aleatoriamente
            const todos = mayusculas + minusculas + numeros + especiales;
            for (let i = contraseña.length; i < longitud; i++) {
                contraseña += todos[Math.floor(Math.random() * todos.length)];
            }
            
            // Mezclar la contraseña
            contraseña = contraseña.split('').sort(() => Math.random() - 0.5).join('');
            
            // Asignar a los campos
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            
            passwordInput.value = contraseña;
            confirmInput.value = contraseña;
            
            // Disparar eventos de validación
            passwordInput.dispatchEvent(new Event('input'));
            confirmInput.dispatchEvent(new Event('input'));
            
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: '¡Contraseña generada!',
                html: `
                    <p>Hemos generado una contraseña segura para ti:</p>
                    <div class="alert alert-info" style="font-family: monospace; font-size: 1.2rem; padding: 10px; background: #e9ecef;">
                        ${contraseña}
                    </div>
                    <p class="text-muted small">La contraseña se ha copiado al portapapeles</p>
                `,
                confirmButtonColor: '#7fad39',
                confirmButtonText: 'Listo'
            });
            
            // Copiar al portapapeles
            navigator.clipboard.writeText(contraseña).catch(() => {
                // Si no se puede copiar, ignorar
            });
        }

        // Mensajes de sesión
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Registro exitoso!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#7fad39'
        });
        @endif
    </script>
</body>
</html>