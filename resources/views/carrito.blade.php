@php use App\Helpers\CarritoHelper; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Carrito de Compras | Tanques Tlaloc - {{ $sucursal->nombre }}</title>
    
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
    <link rel="stylesheet" href="{{ asset('assets/css/carrito.css') }}">
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <style>
        .quantity-input {
            -moz-appearance: textfield;
            appearance: textfield;
        }
        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .cart-table tbody tr {
            transition: all 0.3s ease;
        }
        input:disabled {
            background-color: #f5f5f5 !important;
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" style="height: 50px;">
            </a>

            <div class="d-lg-none d-flex align-items-center ms-auto me-3">
                <a href="{{ route('carrito') }}" class="btn btn-primary position-relative btn-sm">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">{{ $cartCount }}</span> 
                </a>
            </div>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tienda') }}">Tienda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contacto') }}">Contacto</a>
                    </li>
                </ul>
                
                <div class="d-none d-lg-flex align-items-center">
                    {{-- Verificar si hay alguien logueado (admin o cliente) --}}
                    @if(auth('web')->check() || auth('cliente')->check())
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                {{-- Mostrar nombre según quien esté logueado --}}
                                @auth('web')
                                    {{ auth('web')->user()->nombre }}
                                @elseauth('cliente')
                                    {{ auth('cliente')->user()->nombre }}
                                @endauth
                            </button>
                            <ul class="dropdown-menu">
                                @auth('cliente')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('cliente.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>Mi Cuenta
                                        </a>
                                    </li>
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
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-3">
                            <i class="fas fa-user me-2"></i>Login
                        </a>
                    @endauth
                    
                    <a href="{{ route('carrito') }}" class="btn btn-primary position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge">{{ $cartCount ?? 0 }}</span>
                    </a>
                </div>

                <div class="d-lg-none mt-3">
                    @if(auth('web')->check() || auth('cliente')->check())
                        <div class="d-grid gap-2">
                            <span class="btn btn-outline-primary w-100 mb-2 disabled">
                                <i class="fas fa-user me-2"></i>
                                @auth('web')
                                    {{ auth('web')->user()->nombre }}
                                @elseauth('cliente')
                                    {{ auth('cliente')->user()->nombre }}
                                @endauth
                            </span>
                            <form method="POST" action="{{ auth('web')->check() ? route('logout') : route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-user me-2"></i>Iniciar Sesión
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <h1 class="hero-title animate__animated animate__fadeInDown">
                        Creadores del Tinaco Bala<br>
                        <span style="font-size: 2rem; display: block; margin-top: 10px;">Espacios Reducidos</span>
                    </h1>
                    
                    <h2 class="hero-subtitle animate__animated animate__fadeInUp">
                        Tanques TlálOC - El mejor Tinaco
                    </h2>
                    
                    <div class="company-info mb-4">
                        <p><i class="fas fa-flag"></i><strong>Empresa 100% Mexicana</strong></p>
                        <p><i class="fas fa-calendar-alt"></i><strong>Más de 20 años de experiencia</strong></p>
                        <p><i class="fas fa-industry"></i><strong>Especialistas en ROTOMOLDEO</strong></p>
                        <p><i class="fas fa-lightbulb"></i><strong>Innovadores del Tinaco Bala</strong></p>
                    </div>
                    
                    <a href="{{ route('tienda', ['categoria' => 2]) }}" class="btn btn-lg hero-btn animate__animated animate__pulse">
                        <i class="fas fa-bolt me-2"></i>Ver Tinaco Bala
                    </a>
                </div>
                
                <div class="col-lg-5">
                    <div class="contact-hero-box">
                        <h5><i class="fas fa-phone-alt me-2"></i>Contáctanos</h5>
                        <div class="phone-list">
                            <p><i class="fas fa-phone"></i><strong>{{ $sucursal->telefono ?? '55 4017 5803' }}</strong></p>
                            <p><i class="fas fa-phone"></i><strong>444 184 4270</strong></p>
                            <p><i class="fas fa-phone"></i><strong>81 8654 0464</strong></p>
                        </div>
                        
                        <a href="https://wa.me/5215540175803" target="_blank" class="whatsapp-btn">
                            <i class="fab fa-whatsapp"></i>Contactar por WhatsApp
                        </a>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-envelope me-1"></i>Email: 
                                <a href="mailto:{{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}" class="text-primary">
                                    {{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CARRITO DE COMPRAS ===== -->
    <section class="py-5">
        <div class="container">
            <!-- Header del carrito -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center bg-white p-4 rounded shadow-sm">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <div class="bg-primary rounded-circle p-3 me-3">
                                <i class="fas fa-shopping-cart text-white fa-lg"></i>
                            </div>
                            <div>
                                <h1 class="h3 fw-bold mb-1 text-dark">Mi Carrito de Compras</h1>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill px-3 py-2 me-2">
                                        <i class="fas fa-box me-1"></i>
                                        {{ $cartCount }} producto{{ $cartCount != 1 ? 's' : '' }}
                                    </span>
                                    <span class="text-muted">
                                        <i class="fas fa-calculator me-1"></i>
                                        Total: {{ CarritoHelper::formatoPrecio($total) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        @if(!$productosCarrito->isEmpty())
                        <a href="{{ route('tienda') }}" class="btn btn-outline-primary px-4">
                            <i class="fas fa-arrow-left me-2"></i>Seguir Comprando
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($productosCarrito->isEmpty())
            <!-- Carrito vacío -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-shopping-cart fa-5x text-muted opacity-25"></i>
                            </div>
                            <h3 class="mb-3 text-dark">Tu carrito está vacío</h3>
                            <p class="text-muted mb-4">Agrega productos para comenzar tu compra</p>
                            <a href="{{ route('tienda') }}" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-store me-2"></i>Explorar Tienda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            @else
            
            <div class="row">
                <!-- Lista de productos -->
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <form method="POST" action="{{ route('carrito.actualizar') }}" id="formActualizarCarrito">
                        @csrf
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-boxes me-2"></i>Productos en el Carrito
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <!-- VERSIÓN DESKTOP -->
                                <div class="table-responsive d-none d-lg-block">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" class="ps-4" style="width: 40%;">Producto</th>
                                                <th scope="col" class="text-center">Precio</th>
                                                <th scope="col" class="text-center" style="width: 150px;">Cantidad</th>
                                                <th scope="col" class="text-center">Subtotal</th>
                                                <th scope="col" class="text-center pe-4" style="width: 80px;">Eliminar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($productosCarrito as $item)
                                            <tr>
                                                <td class="ps-4 py-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <img src="{{ CarritoHelper::obtenerImagenProducto($item['codigo']) }}" 
                                                                 alt="{{ $item['nombre'] }}" 
                                                                 class="rounded border" 
                                                                 style="width: 80px; height: 80px; object-fit: contain;">
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1 fw-semibold">{{ $item['nombre'] }}</h6>
                                                            <p class="text-muted small mb-1">
                                                                <i class="fas fa-barcode me-1"></i>Código: {{ $item['codigo'] }}
                                                            </p>
                                                            <p class="text-muted small mb-0">
                                                                <i class="fas fa-tint me-1"></i>{{ $item['litros'] }} litros
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center py-4">
                                                    <div class="h5 mb-0 fw-bold text-primary">{{ CarritoHelper::formatoPrecio($item['precio']) }}</div>
                                                </td>
                                                <td class="text-center py-4">
                                                    <div style="max-width: 120px; margin: 0 auto;">
                                                        <input type="number" 
                                                               name="cantidad[{{ $item['id'] }}]" 
                                                               id="cantidad-{{ $item['id'] }}"
                                                               value="{{ $item['cantidad'] }}" 
                                                               min="1" 
                                                               max="{{ $item['existencias'] }}"
                                                               class="form-control text-center quantity-input desktop-input"
                                                               style="height: 45px; font-size: 16px; font-weight: bold;">
                                                        <small class="text-muted d-block mt-1">
                                                            <i class="fas fa-cubes me-1"></i>Máx: {{ $item['existencias'] }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="text-center py-4">
                                                    <div class="h5 mb-0 fw-bold text-success">{{ CarritoHelper::formatoPrecio($item['subtotal']) }}</div>
                                                </td>
                                                <td class="text-center py-4">
                                                    <a href="{{ route('carrito.eliminar', $item['id']) }}" 
                                                       class="btn btn-sm btn-outline-danger btn-remove"
                                                       title="Eliminar producto"
                                                       data-producto="{{ $item['nombre'] }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- VERSIÓN MÓVIL -->
                                <div class="d-block d-lg-none">
                                    @foreach($productosCarrito as $item)
                                    <div class="border-bottom p-3">
                                        <div class="row g-3">
                                            <div class="col-4">
                                                <img src="{{ CarritoHelper::obtenerImagenProducto($item['codigo']) }}" 
                                                     alt="{{ $item['nombre'] }}" 
                                                     class="img-fluid rounded border">
                                            </div>
                                            <div class="col-8">
                                                <h6 class="fw-bold mb-1">{{ $item['nombre'] }}</h6>
                                                <p class="text-muted small mb-2">
                                                    <i class="fas fa-barcode me-1"></i>{{ $item['codigo'] }}
                                                </p>
                                                
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div class="h5 mb-0 fw-bold text-primary">{{ CarritoHelper::formatoPrecio($item['precio']) }}</div>
                                                    <a href="{{ route('carrito.eliminar', $item['id']) }}" 
                                                       class="btn btn-sm btn-outline-danger btn-remove-mobile"
                                                       data-producto="{{ $item['nombre'] }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                                
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div style="width: 140px;">
                                                        <input type="number" 
                                                               name="cantidad[{{ $item['id'] }}]" 
                                                               id="cantidad-mobile-{{ $item['id'] }}"
                                                               value="{{ $item['cantidad'] }}" 
                                                               min="1" 
                                                               max="{{ $item['existencias'] }}"
                                                               class="form-control text-center quantity-input mobile-input"
                                                               style="height: 40px; font-weight: bold;">
                                                        <small class="text-muted d-block mt-1 text-center">Máx: {{ $item['existencias'] }}</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="h5 mb-0 fw-bold text-success">{{ CarritoHelper::formatoPrecio($item['subtotal']) }}</div>
                                                        <small class="text-muted">Subtotal</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="card-footer bg-white border-0 py-3">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                    <button type="submit" class="btn btn-warning px-4 py-2 w-100 w-md-auto">
                                        <i class="fas fa-sync-alt me-2"></i>Actualizar Cantidades
                                    </button>
                                    
                                    <div class="d-flex gap-2 w-100 w-md-auto">
                                        <a href="{{ route('carrito.vaciar') }}" 
                                           class="btn btn-outline-danger btn-vaciar-carrito w-100"
                                           id="btnVaciarCarrito">
                                            <i class="fas fa-trash me-2"></i>Vaciar Carrito
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Resumen del pedido -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                        <div class="card-header bg-primary text-white border-0 py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-receipt me-2"></i>Resumen de Compra
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4">
                                @foreach($productosCarrito as $item)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="text-truncate" style="max-width: 70%;">
                                        <small class="text-muted">{{ $item['nombre'] }}</small>
                                    </div>
                                    <div>
                                        <small class="text-muted">{{ $item['cantidad'] }} × {{ CarritoHelper::formatoPrecio($item['precio']) }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span class="fw-semibold">{{ CarritoHelper::formatoPrecio($total) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">Envío</span>
                                    <span class="fw-semibold text-success">Gratis</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <span class="h5 fw-bold">Total</span>
                                    <span class="h4 fw-bold text-primary">{{ CarritoHelper::formatoPrecio($total) }}</span>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-3">
                                @auth('cliente')
                                    <!-- DEBUG: Cliente logueado: {{ auth('cliente')->user()->email }} -->
                                    <a href="{{ route('cliente.checkout') }}" class="btn btn-primary btn-lg py-3 fw-bold">
                                        <i class="fas fa-credit-card me-2"></i>Continuar al Pago
                                    </a>
                                @else
                                    <!-- DEBUG: NO hay cliente logueado -->
                                    {{-- Si no es cliente, va al login --}}
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg py-3 fw-bold">
                                        <i class="fas fa-sign-in-alt me-2"></i>Inicia sesión para comprar
                                    </a>
                                @endauth
                                <div class="text-center">
                                    <small class="text-muted"><i class="fas fa-lock me-1"></i>Pago 100% seguro</small>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold mb-3"><i class="fas fa-shipping-fast me-2"></i>Envío Gratis</h6>
                                <ul class="list-unstyled text-muted small">
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Entrega en 3-5 días</li>
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Sin costo adicional</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Seguimiento incluido</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand mb-4">
                        <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" style="height: 50px;">
                        <h4 class="mt-3 mb-2">Tanques Tlaloc - {{ $sucursal->nombre }}</h4>
                        <p class="mb-0">{{ $sucursal->direccion ?? 'Ecatepec, Estado de México' }}</p>
                    </div>
                    <div class="footer-contact">
                        <h6 class="mb-3">Contacto Directo</h6>
                        <p class="mb-2"><i class="fas fa-phone me-2"></i>{{ $sucursal->telefono ?? '55 4017 5803' }}</p>
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i><a href="mailto:{{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}" class="text-white">{{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}</a></p>
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
                        
                        <h6 class="mt-4 mb-3">Síguenos</h6>
                        <div class="social-icons">
                            <a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon whatsapp"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" class="social-icon phone"><i class="fas fa-phone"></i></a>
                        </div>
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
    <script src="{{ asset('assets/js/carrito.js') }}"></script>
    
    @if(session('swal'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '{{ session('swal')['type'] }}',
                title: '{{ session('swal')['title'] }}',
                text: '{{ session('swal')['message'] }}',
                confirmButtonColor: '#7fad39',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: true
            });
        });
    </script>
    @endif
</body>
</html>