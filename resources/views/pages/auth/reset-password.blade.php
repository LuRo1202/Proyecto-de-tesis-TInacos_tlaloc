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
    <title>Recuperar Contraseña - Tanques Tláloc</title>
    
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
        
        .form-control:focus + .input-group-text {
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
        
        .login-link, .register-link {
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
        
        .login-link:hover, .register-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .login-link i, .register-link i {
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
        
        @media (max-width: 576px) {
            body {
                padding: 15px;
                background: var(--light);
            }
            
            .reset-container {
                max-width: 100%;
            }
            
            .reset-header {
                padding: 30px 20px;
            }
            
            .reset-body {
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
            
            .reset-title {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 375px) {
            .reset-header {
                padding: 25px 15px;
            }
            
            .reset-body {
                padding: 25px 15px;
            }
            
            .form-control,
            .input-group-text {
                padding: 10px 14px;
                font-size: 0.95rem;
            }
            
            .btn-reset {
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
        
        .reset-card {
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
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="reset-header">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tláloc">
                </div>
                <h1 class="reset-title">Recuperar Contraseña</h1>
                <p class="reset-subtitle">Te enviaremos instrucciones a tu correo</p>
            </div>
            
            <div class="reset-body">
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <small>Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</small>
                </div>
                
                <form method="POST" action="{{ route('cliente.reset.send') }}" id="formReset">
                    @csrf
                    
                    <!-- Email -->
                    <div class="mb-4">
                        <label class="form-label">Correo electrónico *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" 
                                   name="email" 
                                   id="email"
                                   class="form-control" 
                                   placeholder="correo@ejemplo.com"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-reset" id="btnReset">
                        <i class="fas fa-paper-plane me-2"></i>Enviar instrucciones
                    </button>
                    
                    <!-- ENLACE PARA INICIAR SESIÓN -->
                    <a href="{{ route('login') }}" class="login-link">
                        <i class="fas fa-arrow-left me-1"></i>Volver al inicio de sesión
                    </a>
                    
                    <!-- ENLACE PARA REGISTRO -->
                    <div class="register-link">
                        ¿Eres cliente y no tienes cuenta? 
                        <a href="{{ route('cliente.registro') }}">Regístrate aquí</a>
                    </div>
                    
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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formReset');
            const btn = document.getElementById('btnReset');
            
            // Mostrar SweetAlert para éxito
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Correo enviado!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    willClose: () => {
                        // Limpiar el campo de email después del éxito
                        document.getElementById('email').value = '';
                    }
                });
            @endif

            // Mostrar SweetAlert para errores
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: `
                        <ul style="text-align: left;">
                            @foreach($errors->all() as $error)
                                <li>❌ {{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    confirmButtonColor: '#7fad39',
                    confirmButtonText: 'Entendido'
                });
            @endif

            // Validar formulario antes de enviar
            form.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value.trim();
                
                if (!email) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campo vacío',
                        text: 'Por favor ingresa tu correo electrónico',
                        confirmButtonColor: '#7fad39'
                    });
                    return false;
                }
                
                if (!email.includes('@') || !email.includes('.')) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Correo inválido',
                        text: 'Por favor ingresa un correo electrónico válido',
                        confirmButtonColor: '#7fad39'
                    });
                    return false;
                }
                
                // Mostrar loading
                btn.classList.add('btn-loading');
                btn.disabled = true;
            });
        });
    </script>
</body>
</html>