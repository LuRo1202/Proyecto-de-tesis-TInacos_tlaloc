<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error en el Pago - Tanques Tlaloc</title>
    
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
        --color-principal: #dc3545;
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

    .card-body {
        padding: 30px 25px !important;
    }

    .alert-error-box {
        background: #fff5f5;
        border-left: 4px solid #dc3545;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
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
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <h1 class="h2 fw-bold mb-2">Error en el Pago</h1>
                        <p class="opacity-75 mb-0">No pudimos procesar tu transacción</p>
                    </div>

                    <div class="card-body p-4 p-md-5 text-center">
                        
                        <div class="alert-error-box">
                            <i class="bi bi-credit-card fs-1 d-block mb-3" style="color: #dc3545;"></i>
                            <h4 class="fw-bold mb-3">¡Ups! Algo salió mal</h4>
                            <p class="text-secondary mb-0">{{ $message ?? 'Hubo un problema al procesar tu pago. Por favor, intenta nuevamente.' }}</p>
                        </div>

                        <!-- SOLO 2 BOTONES -->
                        <div class="button-group">
                            <a href="https://wa.me/5215540175803" class="btn-custom btn-whatsapp" target="_blank">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                            <a href="{{ route('carrito') }}" class="btn-custom btn-outline-custom">
                                <i class="bi bi-arrow-repeat"></i> Volver al Carrito
                            </a>
                        </div>

                        <div class="mt-4 mt-md-5 redirect-text">
                            <span class="spinner-grow spinner-grow-sm text-danger me-2"></span>
                            Redirigiendo al carrito en <span id="timer" class="fw-bold">10</span> segundos...
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
                    window.location.href = '{{ route("carrito") }}';
                }
            }, 1000);
        });
    </script>
</body>
</html>