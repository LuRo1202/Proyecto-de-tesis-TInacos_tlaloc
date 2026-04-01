<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pago Exitoso! - Tanques Tlaloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <style>
    :root {
        --verde: #7fad39;
        --verde-dark: #5a8a20;
        --color-principal: #7fad39;
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
        padding: 14px 20px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        flex: 1;
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

    .btn-outline-custom {
        background: white;
        color: var(--color-principal);
        border: 2px solid var(--color-principal);
    }

    .btn-outline-custom:hover {
        background: var(--color-principal);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(127, 173, 57, 0.3);
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

    .folio-badge {
        background: #f0f9f0;
        border: 1px solid var(--verde);
        border-radius: 12px;
        padding: 12px 20px;
        display: inline-block;
        margin: 15px 0;
    }

    .folio-badge strong {
        color: var(--verde-dark);
        font-size: 1.2rem;
        font-family: monospace;
    }

    .button-group {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 20px 0;
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
            font-size: 0.95rem;
            width: 100%;
        }
        
        .button-group {
            flex-direction: column;
            gap: 10px;
        }
        
        .step-icon {
            width: 40px;
            height: 40px;
            line-height: 40px;
            font-size: 1.2rem;
        }
        
        .folio-badge strong {
            font-size: 1rem;
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
        
        .card-body {
            padding: 20px 15px !important;
        }
    }
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
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <h1 class="h2 fw-bold mb-2">¡Pago Exitoso!</h1>
                        <p class="opacity-75 mb-0">Tu compra ha sido confirmada</p>
                    </div>

                    <div class="card-body p-4 p-md-5 text-center">
                        
                        <!-- FOLIO DEL PEDIDO -->
                        <div class="folio-badge">
                            <i class="fas fa-receipt me-2" style="color: var(--verde);"></i>
                            <strong>Folio: {{ $pedido->folio ?? 'PENDIENTE' }}</strong>
                        </div>

                        <p class="fs-5 text-secondary mb-4">
                            ¡Gracias por tu compra! Tu pedido <strong>#{{ $pedido->folio ?? '' }}</strong> ha sido confirmado.
                        </p>

                        <div class="row g-3 g-md-4 mb-4 mb-md-5">
                            <div class="col-4">
                                <div class="step-icon"><i class="bi bi-credit-card"></i></div>
                                <h6 class="small fw-bold mb-0">Pago Confirmado</h6>
                            </div>
                            <div class="col-4">
                                <div class="step-icon"><i class="bi bi-box-seam"></i></div>
                                <h6 class="small fw-bold mb-0">Preparando Pedido</h6>
                            </div>
                            <div class="col-4">
                                <div class="step-icon"><i class="bi bi-truck"></i></div>
                                <h6 class="small fw-bold mb-0">Envío en Proceso</h6>
                            </div>
                        </div>

                        <div class="next-steps-section mt-4 mt-md-5 text-start">
                            <h4 class="h5 fw-bold mb-3 mb-md-4 text-center" style="color: var(--verde-dark);">
                                <i class="bi bi-arrow-right-circle me-2"></i>¿Qué sigue ahora?
                            </h4>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="info-box">
                                        <i class="bi bi-envelope-check text-primary"></i>
                                        <h6>Confirma tu correo</h6>
                                        <p class="mb-0">Revisa tu bandeja para el comprobante.</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="info-box">
                                        <i class="bi bi-person-check text-success"></i>
                                        <h6>Seguimiento</h6>
                                        <p class="mb-0">Da seguimiento desde tu panel de cliente.</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="info-box">
                                        <i class="bi bi-shield-check text-warning"></i>
                                        <h6>Garantía</h6>
                                        <p class="mb-0">Todos los productos tienen garantía.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SOLO 2 BOTONES -->
                        <div class="button-group">
                            <a href="https://wa.me/5215540175803" class="btn-custom btn-whatsapp" target="_blank">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                            <a href="{{ route('cliente.dashboard') }}" class="btn-custom btn-outline-custom">
                                <i class="bi bi-box-seam"></i> Mis Pedidos
                            </a>
                        </div>

                        <div class="mt-4 mt-md-5 redirect-text">
                            <span class="spinner-grow spinner-grow-sm text-success me-2"></span>
                            Redirigiendo al inicio en <span id="timer" class="fw-bold">10</span> segundos...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let timeLeft = 10;
            const totalTime = 10;
            const timerElement = document.getElementById('timer');
            const progressBar = document.getElementById('progressBar');

            const countdown = setInterval(function() {
                timeLeft--;
                timerElement.textContent = timeLeft;
                
                let percentage = ((totalTime - timeLeft) / totalTime) * 100;
                progressBar.style.width = percentage + '%';

                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    window.location.href = '{{ route("home") }}';
                }
            }, 1000);
        });
    </script>
</body>
</html>