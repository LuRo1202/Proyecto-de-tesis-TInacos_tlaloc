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
    <title>Nueva Contraseña - Tanques Tláloc</title>
    
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
        
        .reset-container {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }
        
        .reset-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--light-gray);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .reset-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
        }
        
        .reset-header {
            background: var(--primary);
            padding: 40px 30px 30px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .reset-header::after {
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
        
        .reset-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        
        .reset-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .reset-body {
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
        
        .btn-reset {
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
        
        .btn-reset:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(127, 173, 57, 0.3);
        }
        
        .btn-reset:active:not(:disabled) {
            transform: translateY(0);
        }
        
        .btn-reset:disabled {
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
        
        .info-box {
            background: var(--light);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
        }
        
        .info-box i {
            color: var(--primary);
            margin-right: 8px;
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
        
        .strength-meter {
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 3px;
        }
        
        .strength-meter-fill.weak { background-color: #dc3545; width: 20%; }
        .strength-meter-fill.fair { background-color: #ffc107; width: 40%; }
        .strength-meter-fill.good { background-color: #17a2b8; width: 60%; }
        .strength-meter-fill.strong { background-color: #28a745; width: 80%; }
        .strength-meter-fill.very-strong { background-color: #20c997; width: 100%; }
        
        @media (max-width: 576px) {
            body { padding: 15px; background: var(--light); }
            .reset-container { max-width: 100%; }
            .reset-header { padding: 30px 20px; }
            .reset-body { padding: 30px 20px; }
            .logo-container { width: 70px; height: 70px; }
            .logo-container img { width: 50px; height: 50px; }
            .reset-title { font-size: 1.5rem; }
        }

        /* Estilo para mensajes de éxito */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success-message i {
            color: #28a745;
            margin-right: 8px;
        }

        /* Estilo para el botón generador (agregar al final del <style>) */
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
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tláloc">
                </div>
                <h1 class="reset-title">Nueva Contraseña</h1>
                <p class="reset-subtitle">Ingresa tu nueva contraseña</p>
            </div>
            
            <div class="reset-body">
                
                @if(session('status'))
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        {{ session('status') }}
                    </div>
                @endif
                
                <div class="info-box">
                    <i class="fas fa-lock"></i>
                    <small>Crea una contraseña segura para tu cuenta</small>
                </div>
                
                <form method="POST" action="{{ request()->url() }}" id="formReset">
                    @csrf
                    
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    
                    <!-- Nueva Contraseña -->
                    
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" 
                                name="password" 
                                id="password"
                                class="form-control" 
                                placeholder="Mínimo 8 caracteres"
                                required>
                            <!-- 👇 BOTÓN GENERADOR (NUEVO) -->
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
                        
                        <!-- Barra de fortaleza -->
                        <div class="strength-meter mt-2">
                            <div class="strength-meter-fill" id="strengthFill"></div>
                        </div>
                        
                        <!-- Requisitos -->
                        <div class="password-requirements" id="passwordRequirements">
                            <small class="fw-bold text-muted">La contraseña debe tener:</small>
                            <div class="row g-2 mt-1">
                                <div class="col-6">
                                    <small id="req-length" class="invalid"><i class="fas fa-circle"></i> 8+ caracteres</small>
                                </div>
                                <div class="col-6">
                                    <small id="req-number" class="invalid"><i class="fas fa-circle"></i> números</small>
                                </div>
                                <div class="col-6">
                                    <small id="req-uppercase" class="invalid"><i class="fas fa-circle"></i> mayúsculas</small>
                                </div>
                                <div class="col-6">
                                    <small id="req-lowercase" class="invalid"><i class="fas fa-circle"></i> minúsculas</small>
                                </div>
                                <div class="col-6">
                                    <small id="req-special" class="invalid"><i class="fas fa-circle"></i> 1 carácter especial</small>
                                </div>
                                <div class="col-6">
                                    <small id="req-no-sequential" class="invalid"><i class="fas fa-circle"></i> Sin secuencias</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Confirmar Contraseña -->
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
                        <div id="passwordMatch" class="invalid-feedback" style="display: none; color: #dc3545; font-size: 0.875rem; margin-top: 5px;">
                            Las contraseñas no coinciden
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-reset" id="btnReset">
                        <i class="fas fa-save me-2"></i>Restablecer contraseña
                    </button>
                    
                    <a href="{{ route('login') }}" class="login-link">
                        <i class="fas fa-arrow-left me-1"></i>Volver al inicio de sesión
                    </a>
                    
                    <p class="form-text">
                        <i class="fas fa-shield-alt me-1"></i>
                        Tu seguridad es importante para nosotros
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
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

        function updateRequirement(elementId, isValid, validText, invalidText) {
            const element = document.getElementById(elementId);
            if (!element) return;
            element.classList.remove('valid', 'invalid');
            if (isValid) {
                element.classList.add('valid');
                element.innerHTML = `<i class="fas fa-check-circle"></i> ${validText}`;
            } else {
                element.classList.add('invalid');
                element.innerHTML = `<i class="fas fa-circle"></i> ${invalidText}`;
            }
        }

        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            
            const validations = {
                length: password.length >= 8,
                number: /\d/.test(password),
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>_\-+=/\\[\]~`]/.test(password),
                noSequential: !/(?:012|123|234|345|456|567|678|789|890|abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i.test(password)
            };
            
            updateRequirement('req-length', validations.length, '8+ caracteres ', '8+ caracteres');
            updateRequirement('req-number', validations.number, 'números ', 'números');
            updateRequirement('req-uppercase', validations.uppercase, 'mayúsculas ', 'mayúsculas');
            updateRequirement('req-lowercase', validations.lowercase, 'minúsculas ', 'minúsculas');
            updateRequirement('req-special', validations.special, '1 carácter especial ', '1 carácter especial');
            updateRequirement('req-no-sequential', validations.noSequential, 'Sin secuencias ', 'Sin secuencias');
            
            // Calcular fortaleza
            const score = Object.values(validations).filter(v => v).length;
            const fill = document.getElementById('strengthFill');
            fill.className = 'strength-meter-fill';
            
            if (score <= 2) fill.classList.add('weak');
            else if (score === 3) fill.classList.add('fair');
            else if (score === 4) fill.classList.add('good');
            else if (score === 5) fill.classList.add('strong');
            else if (score === 6) fill.classList.add('very-strong');
            
            // Validar coincidencia
            const confirm = document.getElementById('password_confirmation').value;
            if (confirm) validarCoincidencia();
        });

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

        document.getElementById('formReset').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            
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
                Swal.fire({
                    title: 'Contraseña no segura',
                    text: 'La contraseña debe cumplir con todos los requisitos de seguridad',
                    icon: 'warning',
                    confirmButtonColor: '#7fad39'
                });
                return false;
            }
            
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
            
            document.getElementById('btnReset').classList.add('btn-loading');
            document.getElementById('btnReset').disabled = true;
        });

        // Generador de contraseñas seguras
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

        // Mostrar errores con SweetAlert
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ $errors->first() }}',
                confirmButtonColor: '#7fad39'
            });
        @endif
    </script>
</body>
</html>