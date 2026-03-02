<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ request('error') ? 'Error - ' : '¡Gracias! - ' }}Tanques Tlaloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    @php
        // Obtener parámetros de la URL
        $error = request('error');
        $nombre = request('nombre', '');
        $tipo = request('tipo', 'contacto');
        
        // Configurar variables según el tipo de respuesta
        if ($error) {
            if ($error == 'error_envio') {
                $mensaje_error = request('mensaje', 'Error al enviar el mensaje.');
                $titulo_formulario = 'Error en el Envío';
                $titulo_principal = '¡Lo sentimos!';
                $mensaje_principal = 'Hubo un problema al procesar tu solicitud';
                $mensaje_adicional = 'Por favor, intenta nuevamente o contáctanos directamente.';
                $icono_formulario = '❌';
                $colorIcono = '#dc3545';
            } elseif ($error == 'error_sistema') {
                $mensaje_error = 'Error del sistema. Por favor, contacta directamente por teléfono.';
                $titulo_formulario = 'Error del Sistema';
                $titulo_principal = '¡Ups! Algo salió mal';
                $mensaje_principal = 'Hubo un error en nuestro sistema';
                $mensaje_adicional = 'Nuestro equipo técnico ha sido notificado.';
                $icono_formulario = '⚠️';
                $colorIcono = '#ffc107';
            } elseif (strpos($error, '|') !== false) {
                $errores = explode('|', $error);
                $mensaje_error = 'Por favor corrige los siguientes errores: ' . implode(', ', $errores);
                $titulo_formulario = 'Error en el Formulario';
                $titulo_principal = '¡Atención!';
                $mensaje_principal = 'Hay errores en los datos enviados';
                $mensaje_adicional = 'Por favor, revisa la información e intenta nuevamente.';
                $icono_formulario = '📝';
                $colorIcono = '#fd7e14';
            } else {
                $mensaje_error = 'Hubo un problema al procesar tu solicitud.';
                $titulo_formulario = 'Error';
                $titulo_principal = '¡Algo salió mal!';
                $mensaje_principal = 'No pudimos procesar tu solicitud';
                $mensaje_adicional = 'Por favor, intenta nuevamente.';
                $icono_formulario = '❌';
                $colorIcono = '#6c757d';
            }
        } else {
            $nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
            
            if ($tipo === 'reserva') {
                $titulo_formulario = 'Solicitud de Cotización';
                $icono_formulario = '🚚';
                $titulo_principal = '¡Gracias, ' . $nombreSeguro . '!';
                $mensaje_principal = 'Hemos recibido tu solicitud de cotización';
                $mensaje_adicional = 'Te enviaremos la cotización en las próximas 24 horas.';
                $colorIcono = '#7fad39';
            } elseif ($tipo === 'proyecto') {  
                $titulo_formulario = 'Solicitud de Proyecto';
                $icono_formulario = '🚀';
                $titulo_principal = '¡Proyecto Enviado, ' . $nombreSeguro . '!';
                $mensaje_principal = 'Hemos recibido tu proyecto y archivos adjuntos';
                $mensaje_adicional = 'Nuestro equipo especializado lo revisará y te contactará en 24-48 horas.';
                $colorIcono = '#7fad39';
            } else {
                $titulo_formulario = 'Mensaje de Contacto';
                $icono_formulario = '📧';
                $titulo_principal = '¡Gracias, ' . $nombreSeguro . '!';
                $mensaje_principal = 'Hemos recibido tu mensaje de contacto';
                $mensaje_adicional = 'Te responderemos en las próximas 24 horas.';
                $colorIcono = '#7fad39';
            }
        }
    @endphp
    
    <style>
    :root {
        --verde: #7fad39;
        --verde-dark: #5a8a20;
        --color-principal: {{ $error ? $colorIcono : '#7fad39' }};
    }
    
    body {
        background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                    url('{{ asset("assets/img/hero/hero-foreground.png") }}') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        margin: 0;
        padding: 15px;
    }

    .main-card {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        border: none;
        overflow: hidden;
        width: 100%;
        max-width: 550px;
        margin: 20px auto;
        animation: slideUp 0.8s ease-out;
    }

    .header-gradient {
        background: var(--color-principal);
        padding: 40px 30px;
        color: white;
        text-align: center;
        position: relative;
    }

    .check-wrapper {
        width: 80px;
        height: 80px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: var(--color-principal);
        font-size: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .step-icon {
        font-size: 1.5rem;
        color: var(--color-principal);
        background: #f1f8e9;
        width: 50px;
        height: 50px;
        line-height: 50px;
        border-radius: 50%;
        margin-bottom: 10px;
        display: inline-block;
    }

    .progress-container {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.2);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--color-principal);
        width: 0%;
        box-shadow: 0 0 10px var(--color-principal);
        transition: width 1s linear;
    }

    .btn-custom {
        padding: 14px 30px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 180px;
    }

    .btn-whatsapp {
        background-color: #25D366;
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
    }

    .btn-whatsapp:hover {
        background-color: #128C7E;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(37, 211, 102, 0.4);
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .redirect-text {
        color: #666;
        font-size: 0.85rem;
        margin-top: 25px;
    }

    .info-box {
        transition: all 0.3s ease;
        border: 1px solid #e8e8e8 !important;
        height: 100%;
        border-radius: 12px;
        padding: 20px 15px;
        text-align: center;
    }

    .info-box:hover {
        transform: translateY(-5px);
        border-color: var(--color-principal) !important;
        background-color: #fff !important;
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
    }

    .info-box i {
        font-size: 2rem;
        margin-bottom: 15px;
        display: block;
    }

    .info-box h6 {
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: #333;
    }

    .info-box p {
        font-size: 0.85rem;
        line-height: 1.4;
        color: #666;
    }

    .tracking-wider {
        letter-spacing: 1px;
        color: var(--verde-dark);
        font-size: 0.8rem;
    }

    .card-body {
        padding: 30px 25px !important;
    }

    /* Estilos para mensajes de error */
    .alert-error {
        border-left: 5px solid var(--color-principal);
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
    }

    @media (max-width: 768px) {
        body {
            padding: 10px;
            align-items: flex-start;
            padding-top: 20px;
        }
        
        .main-card {
            border-radius: 15px;
            margin: 10px auto;
            max-width: 100%;
        }
        
        .header-gradient {
            padding: 30px 20px;
        }
        
        .check-wrapper {
            width: 70px;
            height: 70px;
            font-size: 35px;
            margin-bottom: 15px;
        }
        
        h1.h2 {
            font-size: 1.5rem !important;
        }
        
        .card-body {
            padding: 25px 20px !important;
        }
        
        .btn-custom {
            padding: 12px 20px;
            min-width: 160px;
            font-size: 0.95rem;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .d-md-flex.gap-3 {
            flex-direction: column;
            gap: 10px !important;
        }
        
        .step-icon {
            width: 40px;
            height: 40px;
            line-height: 40px;
            font-size: 1.2rem;
        }
        
        .row.g-4.mb-5 .col-4 h6 {
            font-size: 0.75rem;
        }
        
        .info-box {
            padding: 15px 10px;
            margin-bottom: 15px;
        }
        
        .info-box i {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .info-box h6 {
            font-size: 0.85rem;
        }
        
        .info-box p {
            font-size: 0.75rem;
        }
        
        .redirect-text {
            font-size: 0.8rem;
        }
        
        .mt-5.pt-4.border-top {
            margin-top: 20px !important;
            padding-top: 20px !important;
        }
        
        .mt-5.redirect-text {
            margin-top: 20px !important;
        }
    }

    @media (max-width: 576px) {
        .header-gradient {
            padding: 25px 15px;
        }
        
        .check-wrapper {
            width: 60px;
            height: 60px;
            font-size: 30px;
        }
        
        h1.h2 {
            font-size: 1.3rem !important;
        }
        
        p.fs-5 {
            font-size: 1rem !important;
        }
        
        .card-body {
            padding: 20px 15px !important;
        }
        
        .progress-container {
            height: 4px;
        }
        
        .row.g-3 .col-md-4 {
            margin-bottom: 10px;
        }
        
        .mt-5.pt-4.border-top .row .col-md-6 {
            margin-bottom: 10px;
            text-align: center !important;
        }
        
        .tracking-wider {
            font-size: 0.7rem;
        }
        
        .redirect-text {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 375px) {
        .check-wrapper {
            width: 50px;
            height: 50px;
            font-size: 25px;
        }
        
        h1.h2 {
            font-size: 1.2rem !important;
        }
        
        .step-icon {
            width: 35px;
            height: 35px;
            line-height: 35px;
            font-size: 1rem;
        }
        
        .row.g-4.mb-5 .col-4 h6 {
            font-size: 0.7rem;
        }
        
        .info-box {
            padding: 12px 8px;
        }
    }

    .btn:focus, .btn-custom:focus {
        outline: 3px solid rgba(127, 173, 57, 0.5);
        outline-offset: 2px;
    }

    .text-secondary {
        color: #555 !important;
    }

    .opacity-75 {
        opacity: 0.85;
    }

    @if($error)
    .next-steps-section,
    .step-icon-container {
        display: none !important;
    }
    @endif
    </style>    
</head>
<body>

    <div class="progress-container">
        <div class="progress-bar-fill" id="progressBar"></div>
    </div>

    <div class="container pb-4 pb-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-7">
                
                <div class="main-card">
                    <div class="header-gradient">
                        <div class="check-wrapper">
                            @if($error)
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            @else
                                <i class="bi bi-check-lg"></i>
                            @endif
                        </div>
                        <h1 class="h2 fw-bold mb-2">{{ $titulo_principal }}</h1>
                        <p class="opacity-75 mb-0">{{ $titulo_formulario }}</p>
                    </div>

                    <div class="card-body p-4 p-md-5 text-center">
                        @if($error)
                            <!-- MOSTRAR ERROR -->
                            <div class="alert alert-error p-4 mb-4">
                                <i class="bi bi-exclamation-octagon-fill fs-1 d-block mb-3" style="color: var(--color-principal);"></i>
                                <h4 class="fw-bold mb-3">{{ $mensaje_principal }}</h4>
                                <p class="text-secondary mb-0">{{ $mensaje_adicional }}</p>
                                
                                @if(!empty($mensaje_error))
                                <div class="mt-3 p-3 bg-light border-start border-4 border-danger rounded">
                                    <p class="mb-2"><strong>Detalles del error:</strong></p>
                                    <p class="mb-0 text-danger">{{ $mensaje_error }}</p>
                                </div>
                                @endif
                            </div>
                        @else
                            <!-- MOSTRAR ÉXITO -->
                            <p class="fs-5 text-secondary mb-4">
                                {{ $mensaje_principal }}. {{ $mensaje_adicional }}
                            </p>

                            <div class="row g-3 g-md-4 mb-4 mb-md-5 step-icon-container">
                                <div class="col-4">
                                    <div class="step-icon"><i class="bi bi-envelope-check"></i></div>
                                    <h6 class="small fw-bold mb-0">Email Recibido</h6>
                                </div>
                                <div class="col-4">
                                    <div class="step-icon"><i class="bi bi-person-gear"></i></div>
                                    <h6 class="small fw-bold mb-0">Asignando Asesor</h6>
                                </div>
                                <div class="col-4">
                                    <div class="step-icon"><i class="bi bi-phone-vibrate"></i></div>
                                    <h6 class="small fw-bold mb-0">Contacto en breve</h6>
                                </div>
                            </div>

                            <div class="next-steps-section mt-4 mt-md-5 text-start">
                                <h4 class="h5 fw-bold mb-3 mb-md-4 text-center" style="color: var(--verde-dark);">
                                    <i class="bi bi-arrow-right-circle me-2"></i>¿Qué sigue ahora?
                                </h4>
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <div class="info-box">
                                            <i class="bi bi-mailbox2 text-primary"></i>
                                            <h6>Confirma tu correo</h6>
                                            <p class="mb-0">Revisa tu bandeja (y spam) para asegurar que recibiste nuestro folio.</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="info-box">
                                            <i class="bi bi-truck text-success"></i>
                                            <h6>Logística Tlaloc</h6>
                                            <p class="mb-0">Un especialista revisará la zona de entrega para tu presupuesto.</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="info-box">
                                            <i class="bi bi-shield-check text-warning"></i>
                                            <h6>Garantía Lista</h6>
                                            <p class="mb-0">Tenemos stock disponible para envío inmediato si confirmas hoy.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(defined('EMPRESA_TELEFONO'))
                        <div class="mt-4 mt-md-5 pt-3 pt-md-4 border-top">
                            <div class="row align-items-center opacity-75">
                                <div class="col-12 mb-2">
                                    <small class="text-uppercase fw-bold tracking-wider">Atención Directa Tanques Tlaloc</small>
                                </div>
                                <div class="col-md-6 small mb-2 mb-md-0">
                                    <i class="bi bi-geo-alt-fill me-1"></i> {{ defined('EMPRESA_DIRECCION') ? EMPRESA_DIRECCION : 'México' }}
                                </div>
                                <div class="col-md-6 small text-md-end">
                                    <i class="bi bi-telephone-fill me-1"></i> {{ EMPRESA_TELEFONO }} 
                                    <span class="mx-2 d-none d-md-inline">|</span>
                                    <br class="d-md-none">
                                    <i class="bi bi-envelope-fill me-1"></i> {{ defined('MI_CORREO_PRINCIPAL') ? MI_CORREO_PRINCIPAL : '' }}
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <br><br>
                        
                        <div class="d-grid d-md-flex justify-content-center gap-3">
                            @if(defined('EMPRESA_WHATSAPP'))
                            <a href="https://wa.me/{{ EMPRESA_WHATSAPP }}" class="btn btn-custom btn-whatsapp" target="_blank">
                                <i class="bi bi-whatsapp me-2"></i> WhatsApp Directo
                            </a>
                            @endif
                            
                            @if($error)
                            <a href="{{ route('home') }}#contacto" class="btn btn-custom btn-outline-danger">
                                <i class="bi bi-arrow-clockwise me-2"></i> Reintentar
                            </a>
                            @endif
                            
                            <a href="{{ route('home') }}" class="btn btn-custom btn-outline-secondary">
                                <i class="bi bi-house me-2"></i> Volver al Inicio
                            </a>
                        </div>

                        <div class="mt-4 mt-md-5 redirect-text">
                            <span class="spinner-grow spinner-grow-sm {{ $error ? 'text-danger' : 'text-success' }} me-2"></span>
                            Redirigiendo al inicio en <span id="timer" class="fw-bold">15</span> segundos...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let timeLeft = 15;
            const totalTime = 15;
            const timerElement = document.getElementById('timer');
            const progressBar = document.getElementById('progressBar');

            const countdown = setInterval(function() {
                timeLeft--;
                timerElement.textContent = timeLeft;
                
                let percentage = ((totalTime - timeLeft) / totalTime) * 100;
                progressBar.style.width = percentage + '%';

                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    window.location.href = '{{ $error ? route("home")."#contacto" : route("home") }}';
                }
            }, 1000);
            
            const buttons = document.querySelectorAll('.btn-custom');
            buttons.forEach(button => {
                button.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });
                button.addEventListener('touchend', function() {
                    this.style.transform = '';
                });
            });
        });
    </script>
</body>
</html>