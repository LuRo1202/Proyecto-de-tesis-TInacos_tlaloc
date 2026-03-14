{{-- resources/views/cliente/pago.blade.php --}}
@php use App\Helpers\CarritoHelper; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pago Seguro | Tanques Tlaloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}">
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <!-- SDK Mercado Pago -->
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <style>
        /* Todos los estilos de tu checkout */
        .cobertura-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 10px; padding: 15px; margin: 15px 0; display: none; }
        .cobertura-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 10px; padding: 15px; margin: 15px 0; display: none; }
        .verificar-cobertura-btn { background: linear-gradient(135deg, #7fad39, #5a8c29); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; transition: all 0.3s; width: 100%; }
        .verificar-cobertura-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(127, 173, 57, 0.4); }
        .verificar-cobertura-btn.loading { background: #6c757d; cursor: not-allowed; }
        .verificar-cobertura-btn.loading i { animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        @media (max-width: 768px) {
            .hero-title { font-size: 1.5rem; text-align: center; }
            .checkout-summary-box { margin-top: 1.5rem; order: -1; }
            .form-control, .form-select { font-size: 16px !important; }
            .btn-lg { padding: 0.75rem !important; font-size: 1rem !important; }
        }
        
        .cobertura-verificada-box { background: linear-gradient(135deg, #d4edda, #c3e6cb); border-left: 5px solid #28a745; border-radius: 12px; padding: 20px; margin: 20px 0; box-shadow: 0 5px 15px rgba(40, 167, 69, 0.2); animation: slideIn 0.5s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Estilos específicos para pago */
        .info-pedido {
            background: #f8f9fa;
            border-left: 4px solid #7fad39;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .info-pedido p { margin-bottom: 8px; font-size: 1.1rem; }
        .info-pedido strong { color: #7fad39; font-size: 1.2rem; }
        .folio-badge { background: rgba(255,255,255,0.2); color: white; padding: 5px 15px; border-radius: 50px; font-size: 1rem; float: right; }
        .total-amount { font-size: 2rem; font-weight: bold; color: #7fad39; }
        .sucursal-info { background: #e8f5e9; padding: 10px 15px; border-radius: 8px; margin-top: 10px; font-size: 0.9rem; }
        #wallet_container { margin: 30px 0; min-height: 200px; }
        .security-badge { text-align: center; color: #666; font-size: 0.9rem; padding: 20px; background: #f8f9fa; border-radius: 10px; }
        .security-badge i { color: #7fad39; margin: 0 5px; }
        .mercadopago-logo { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 10px; }
        .mercadopago-logo i { font-size: 2rem; color: #009ee3; }
    </style>
</head>
<body>
    <!-- HEADER -->
    <nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" style="height: 50px;">
            </a>
            <div class="d-lg-none d-flex align-items-center ms-auto me-3">
                <a href="{{ route('carrito') }}" class="btn btn-primary position-relative btn-sm">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">{{ session('checkout_data.cartCount') ?? 0 }}</span>
                </a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('tienda') }}">Tienda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
                </ul>
                <div class="d-none d-lg-flex align-items-center">
                    @if(auth('web')->check() || auth('cliente')->check())
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                @auth('web') {{ auth('web')->user()->nombre }} @elseauth('cliente') {{ auth('cliente')->user()->nombre }} @endauth
                            </button>
                            <ul class="dropdown-menu">
                                @auth('cliente')
                                    <li><a class="dropdown-item" href="{{ route('cliente.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Mi Cuenta</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endauth
                                <li>
                                    <form method="POST" action="{{ auth('web')->check() ? route('logout') : route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-3"><i class="fas fa-user me-2"></i>Login</a>
                    @endauth
                    <a href="{{ route('carrito') }}" class="btn btn-primary position-relative"><i class="fas fa-shopping-cart"></i><span class="cart-badge">{{ session('checkout_data.cartCount') ?? 0 }}</span></a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="checkout-hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 order-lg-2 mb-4 mb-lg-0">
                    <div class="checkout-summary-box">
                        <h5><i class="fas fa-credit-card me-2"></i>Resumen de Pago</h5>
                        <div class="summary-details">
                            <p><span>Total a pagar:</span> <strong class="text-success">{{ CarritoHelper::formatoPrecio($pedido['total']) }}</strong></p>
                        </div>
                        <a href="{{ route('cliente.checkout') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Checkout
                        </a>
                    </div>
                </div>
                <div class="col-lg-8 order-lg-1">
                    <h1 class="hero-title">Pago Seguro<br><span style="font-size: 1.5rem;">Procesado por Mercado Pago</span></h1>
                    <div class="checkout-steps">
                        <div class="step completed"><div class="step-number">1</div><div class="step-text">Carrito</div></div>
                        <div class="step-line d-none d-md-block"></div>
                        <div class="step completed"><div class="step-number">2</div><div class="step-text">Datos y Cobertura</div></div>
                        <div class="step-line d-none d-md-block"></div>
                        <div class="step active"><div class="step-number">3</div><div class="step-text">Pago</div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN DE PAGO -->
    <section class="checkout-form-section py-4 py-lg-5">
        <div class="container">
            <div class="row g-4">
                <!-- Columna principal -->
                <div class="col-lg-8">
                    <div class="checkout-form-card mb-4">
                        <h4 class="checkout-title"><i class="fas fa-credit-card me-2"></i>Finalizar Pago</h4>
                        
                        @if(isset($pedido) && $pedido)
                        <div class="info-pedido">
                            <h5 class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Detalles del Pedido</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Folio:</strong> <span class="badge bg-secondary">{{ $pedido['folio'] }}</span></p>
                                    <p><strong>Total a pagar:</strong> <span class="text-success fw-bold fs-4">{{ CarritoHelper::formatoPrecio($pedido['total']) }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                                    <p><strong>Estado:</strong> <span class="badge bg-warning text-dark">Pendiente de pago</span></p>
                                </div>
                            </div>
                            @if(isset($pedido['sucursal']))
                            <div class="sucursal-info">
                                <i class="fas fa-store me-2"></i><strong>Sucursal:</strong> {{ $pedido['sucursal']['nombre'] }} 
                                @if(isset($pedido['sucursal']['distancia']))<span class="ms-2">| <i class="fas fa-road me-1"></i>{{ $pedido['sucursal']['distancia'] }} km</span>@endif
                            </div>
                            @endif
                        </div>

                        <!-- 🔥 CONTENEDOR DE CARD PAYMENT BRICK (NO PIDE LOGIN) -->
                        <div id="wallet_container"></div>
                        
                        @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-circle fa-4x text-warning mb-3"></i>
                            <h4>No hay un pedido pendiente</h4>
                            <a href="{{ route('tienda') }}" class="btn btn-primary">Ir a la tienda</a>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Columna lateral -->
                <div class="col-lg-4">
                    <div class="order-summary-card">
                        <h4 class="order-title"><i class="fas fa-shield-alt me-2"></i>Pago Seguro</h4>
                        <div class="security-badge">
                            <div class="mercadopago-logo"><i class="fab fa-mercadopago fa-3x"></i><span class="fw-bold">Mercado Pago</span></div>
                            <p class="mb-2"><i class="fas fa-lock"></i> Pago 100% seguro</p>
                            <hr>
                            <div class="text-start small">
                                <p class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Datos encriptados</p>
                                <p class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Protección al comprador</p>
                                <p class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Sin cargos adicionales</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="main-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand mb-4">
                        <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" style="height: 50px;">
                        <h4 class="mt-3 mb-2">Tanques Tlaloc</h4>
                        <p class="mb-0">Ecatepec, Estado de México</p>
                    </div>
                    <div class="footer-contact">
                        <h6 class="mb-3">Contacto Directo</h6>
                        <p class="mb-2"><i class="fas fa-phone me-2"></i>55 4017 5803</p>
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i><a href="mailto:tanquestlaloc@outlook.com" class="text-white">tanquestlaloc@outlook.com</a></p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer-links">
                        <h6 class="mb-3">Productos</h6>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a></li>
                            <li><a href="{{ route('tienda', ['categoria' => 1]) }}">Tinacos Tradicionales</a></li>
                            <li><a href="{{ route('tienda', ['categoria' => 3]) }}">Cisternas</a></li>
                            <li><a href="{{ route('tienda', ['categoria' => 4]) }}">Accesorios</a></li>
                            <li><a href="{{ route('tienda') }}">Catálogo Completo</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="footer-links">
                        <h6 class="mb-3">Empresa</h6>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('home') }}">Inicio</a></li>
                            <li><a href="{{ route('tienda') }}">Tienda</a></li>
                            <li><a href="{{ route('contacto') }}">Contacto</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row mt-5 pt-4 border-top border-secondary">
                <div class="col-lg-8">
                    <p class="mb-2"><strong>Tanques Tlaloc</strong> - Creadores del Tinaco Bala • Empresa 100% Mexicana</p>
                    <p class="mb-0">Especialistas en ROTOMOLDEO con más de 20 años de experiencia</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <p class="mb-0">&copy; {{ date('Y') }} Tanques Tlaloc. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @if(isset($pedido) && $pedido)
    <script>
        $(document).ready(function() {
            const mp = new MercadoPago('{{ $publicKey }}', {
                locale: 'es-MX'
            });
            
            // 🔥 CARD PAYMENT BRICK - NO PIDE LOGIN
            mp.bricks().create("cardPayment", "wallet_container", {
                initialization: {
                    amount: Number({{ $pedido['total'] }}).toFixed(2),
                    preferenceId: "{{ $preferenceId }}"
                },
                callbacks: {
                    onReady: () => {
                        console.log('Formulario de tarjeta listo');
                    },
                    onSubmit: (formData) => {
                        return new Promise((resolve, reject) => {
                            fetch('/pago/api/process-payment', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(formData)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if(data.success) {
                                    window.location.href = '{{ route("pago.success") }}?payment_id=' + data.payment_id;
                                    resolve();
                                } else {
                                    alert('Error al procesar el pago');
                                    reject();
                                }
                            })
                            .catch(error => {
                                console.error(error);
                                reject();
                            });
                        });
                    },
                    onError: (error) => {
                        console.error('Error:', error);
                        $('#wallet_container').html('<div class="alert alert-danger">Error al cargar el formulario de pago</div>');
                    }
                }
            });
        });
    </script>
    @endif
</body>
</html>