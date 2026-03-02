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
    <title>Login - Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Livewire Styles -->
    @livewireStyles
    
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
        
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--light-gray);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
        }
        
        .login-header {
            background: var(--primary);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .login-header::after {
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
        
        .login-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        
        .login-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 40px 30px;
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
        
        .btn-login {
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
        
        .btn-login:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(127, 173, 57, 0.3);
        }
        
        .btn-login:active:not(:disabled) {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .password-toggle {
            background: var(--light-gray);
            border: 1px solid #dee2e6;
            border-left: none;
            color: var(--gray);
            padding: 12px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .password-toggle:hover {
            background: #e2e6ea;
            color: var(--dark);
        }
        
        .back-link {
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
        
        .back-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .back-link i {
            margin-right: 6px;
        }
        
        .form-text {
            color: var(--gray);
            font-size: 0.85rem;
            margin-top: 25px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }
        
        .register-link {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        
        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .register-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        @media (max-width: 576px) {
            body {
                padding: 15px;
                background: var(--light);
            }
            
            .login-container {
                max-width: 100%;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-body {
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
            
            .login-title {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 375px) {
            .login-header {
                padding: 25px 15px;
            }
            
            .login-body {
                padding: 25px 15px;
            }
            
            .form-control,
            .input-group-text,
            .password-toggle {
                padding: 10px 14px;
                font-size: 0.95rem;
            }
            
            .btn-login {
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
        
        .login-card {
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
        
        .form-control:focus,
        .btn-login:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(127, 173, 57, 0.3);
        }
        
        .is-invalid {
            border-color: #dc3545 !important;
        }
        
        .is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.25) !important;
        }
        
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tláloc">
                </div>
                <h1 class="login-title">Tanques Tláloc</h1>
                <p class="login-subtitle">Sistema de Gestión</p>
            </div>
            
            <div class="login-body">
                <livewire:auth.login />
                
                <div class="register-link">
                    ¿Eres cliente y no tienes cuenta? 
                    <a href="{{ route('cliente.registro') }}">Regístrate aquí</a>
                </div>
                
                <a href="{{ route('home') }}" class="back-link">
                    <i class="fas fa-arrow-left me-1"></i>Volver a la tienda
                </a>
                
                <p class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Acceso para personal y clientes registrados
                </p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    <script>
        // Escuchar eventos de Livewire para errores
        document.addEventListener('livewire:init', function () {
            Livewire.on('loginError', (message) => {
                Swal.fire({
                    title: 'Error de Acceso',
                    text: message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Intentar de nuevo'
                });
            });
        });

        // SweetAlert para LOGIN exitoso
        @if(session('login_success'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '¡Bienvenido!',
                text: '{{ session('login_message') }}',
                icon: 'success',
                confirmButtonColor: '#7fad39',
                confirmButtonText: 'Continuar',
                timer: 2000,
                timerProgressBar: true
            });
        });
        @endif

        // SweetAlert para LOGOUT exitoso
        @if(session('logout_success'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '¡Hasta pronto!',
                text: '{{ session('logout_message') }}',
                icon: 'success',
                confirmButtonColor: '#7fad39',
                confirmButtonText: 'Aceptar',
                timer: 3000,
                timerProgressBar: true
            });
        });
        @endif
    </script>
</body>
</html>