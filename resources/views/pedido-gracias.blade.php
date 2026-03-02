<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pedido Confirmado! | Tanques Tlaloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('{{ asset("assets/img/hero/hero-foreground.png") }}') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .gracias-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            margin: 20px auto;
            animation: slideUp 0.8s ease-out;
            overflow: hidden;
        }
        
        .header-gradient {
            background: linear-gradient(135deg, #7fad39, #5a8c29);
            padding: 40px 30px;
            color: white;
            text-align: center;
            border-radius: 20px 20px 0 0;
            position: relative;
        }
        
        .header-gradient::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background: linear-gradient(135deg, transparent 50%, white 50%);
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
            color: #7fad39;
            font-size: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .info-box {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.03);
        }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #dee2e6;
            transition: all 0.3s;
        }
        
        .info-item:hover {
            transform: translateX(5px);
            background: rgba(127, 173, 57, 0.02);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #7fad39, #5a8c29);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            box-shadow: 0 5px 10px rgba(127, 173, 57, 0.2);
        }
        
        /* ===== BOTONES MEJORADOS ===== */
        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        .btn-moderno {
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            cursor: pointer;
            min-width: 180px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .btn-moderno i {
            font-size: 1.2rem;
            transition: transform 0.3s;
        }
        
        /* Botón Verde - Seguir Comprando */
        .btn-verde-moderno {
            background: linear-gradient(145deg, #7fad39, #5a8c29);
            color: white;
            box-shadow: 0 8px 15px rgba(127, 173, 57, 0.3);
        }
        
        .btn-verde-moderno:hover {
            background: linear-gradient(145deg, #5a8c29, #7fad39);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 25px rgba(127, 173, 57, 0.4);
        }
        
        .btn-verde-moderno:hover i {
            transform: rotate(360deg);
        }
        
        /* Botón Outline - Ir al Inicio */
        .btn-outline-moderno {
            background: transparent;
            color: #6c757d;
            border: 2px solid #6c757d;
            box-shadow: none;
        }
        
        .btn-outline-moderno:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
        }
        
        .btn-outline-moderno:hover i {
            transform: translateX(-3px);
        }
        
        /* Botón WhatsApp */
        .btn-whatsapp-moderno {
            background: linear-gradient(145deg, #25D366, #128C7E);
            color: white;
            padding: 16px 32px;
            border-radius: 60px;
            font-size: 1.1rem;
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.3);
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }
        
        .btn-whatsapp-moderno:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 30px rgba(37, 211, 102, 0.4);
        }
        
        .btn-whatsapp-moderno:hover i {
            animation: shake 0.5s ease infinite;
        }
        
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
        
        /* Barra de progreso */
        .progress-container {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            margin: 25px 0 10px;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #7fad39, #5a8c29);
            width: 0%;
            border-radius: 4px;
            transition: width 1s linear;
        }
        
        .redirect-text {
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 15px;
            text-align: center;
            background: rgba(127, 173, 57, 0.05);
            padding: 10px;
            border-radius: 50px;
        }
        
        .redirect-text i {
            color: #7fad39;
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Estilos responsivos */
        @media (max-width: 768px) {
            .gracias-card {
                margin: 10px;
            }
            
            .header-gradient {
                padding: 30px 20px;
            }
            
            .check-wrapper {
                width: 70px;
                height: 70px;
                font-size: 35px;
            }
            
            .btn-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-moderno {
                width: 100%;
                min-width: auto;
            }
            
            .btn-whatsapp-moderno {
                max-width: 100%;
            }
            
            .info-item:hover {
                transform: none;
            }
        }
        
        /* Efecto de brillo en botones */
        .btn-moderno {
            position: relative;
            overflow: hidden;
        }
        
        .btn-moderno::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 200%;
            height: 200%;
            background: rgba(255,255,255,0.2);
            transform: rotate(30deg);
            transition: transform 0.5s;
            opacity: 0;
        }
        
        .btn-moderno:hover::after {
            transform: rotate(30deg) translate(50%, 50%);
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="gracias-card">
            <div class="header-gradient">
                <div class="check-wrapper">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="h2 fw-bold mb-2">¡Pedido Confirmado!</h1>
                <p class="opacity-75 mb-0">Tu compra se ha realizado exitosamente</p>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4 class="fw-bold">¡Gracias por tu compra!</h4>
                    <p class="text-muted">Hemos recibido tu pedido correctamente</p>
                </div>

                <div class="info-box">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Folio</small>
                            <strong class="fs-5">{{ $folio ?? 'PED-' . date('YmdHis') }}</strong>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Fecha y hora</small>
                            <strong>{{ date('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Contacto</small>
                            <strong>55 4017 5803</strong>
                        </div>
                    </div>
                </div>

                <!-- Botón WhatsApp Moderno -->
                <div class="text-center mb-4">
                    <a href="https://wa.me/5215540175803?text=Hola!%20Quiero%20información%20sobre%20mi%20pedido%20{{ $folio ?? '' }}" 
                       target="_blank" 
                       class="btn-moderno btn-whatsapp-moderno">
                        <i class="fab fa-whatsapp"></i>
                        Contactar por WhatsApp
                    </a>
                </div>

                <!-- Barra de progreso -->
                <div class="progress-container">
                    <div class="progress-bar-fill" id="progressBar"></div>
                </div>
                
                <div class="redirect-text">
                    <i class="fas fa-clock"></i>
                    Redirigiendo al inicio en <span id="timer" class="fw-bold text-success">15</span> segundos
                </div>

                <!-- Botones Mejorados -->
                <div class="btn-container">
                    <a href="{{ route('tienda') }}" class="btn-moderno btn-verde-moderno">
                        <i class="fas fa-store"></i>
                        Seguir Comprando
                    </a>
                    <a href="{{ route('home') }}" class="btn-moderno btn-outline-moderno">
                        <i class="fas fa-home"></i>
                        Ir al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar contador y barra de progreso
            let timeLeft = 15;
            const totalTime = 15;
            const timerElement = document.getElementById('timer');
            const progressBar = document.getElementById('progressBar');
            
            // Inicializar barra de progreso
            progressBar.style.width = '0%';

            const countdown = setInterval(function() {
                timeLeft--;
                timerElement.textContent = timeLeft;
                
                // Calcular porcentaje de progreso
                let percentage = ((totalTime - timeLeft) / totalTime) * 100;
                progressBar.style.width = percentage + '%';

                // Cuando llegue a 0, redirigir a home
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    window.location.href = '{{ route("home") }}';
                }
            }, 1000);
            
            // Efectos táctiles para móviles
            const buttons = document.querySelectorAll('.btn-moderno');
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