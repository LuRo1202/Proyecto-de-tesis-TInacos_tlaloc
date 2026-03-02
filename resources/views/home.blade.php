@php use App\Helpers\ProductoHelper; use App\Helpers\CarritoHelper; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Creadores del Tinaco Bala | Tanques Tlaloc - {{ $sucursal->nombre ?? 'Ecatepec' }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Estilos adicionales para ofertas */
        .bg-gradient-oferta {
            background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);
            border-top: 3px solid #dc3545;
            border-bottom: 3px solid #dc3545;
        }

        .oferta-badge .badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .precio-original.tachado {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9rem;
        }

        .precio-oferta {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .color-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
            border: 1px solid #ddd;
        }

        @media (max-width: 768px) {
            .bg-gradient-oferta .badge.fs-6 {
                font-size: 0.9rem !important;
                padding: 5px 15px !important;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" class="img-fluid" style="max-height: 45px;">
            </a>
            
            <!-- Botón carrito móvil -->
            <div class="d-lg-none d-flex align-items-center ms-auto me-3">
                <a href="{{ route('carrito') }}" class="btn btn-primary position-relative btn-sm">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">{{ $cartCount ?? 0 }}</span> 
                </a>
            </div>
            
            <!-- Botón hamburguesa -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('home') }}">Inicio</a>
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
                
                <!-- Botones móvil -->
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

    <!-- BUSCADOR SUPERIOR -->
    <section class="top-search-section">
        <div class="container">
            <div class="top-search-container">
                <div class="text-center mb-3">
                    <h4 class="text-white mb-2 fw-600">
                        <i class="fas fa-search me-2 d-none d-md-inline"></i>¿Qué necesitas hoy?
                    </h4>
                    <p class="text-white mb-3 d-none d-md-block opacity-75">
                        Encuentra tinacos, cisternas y accesorios en {{ $sucursal->nombre ?? 'Ecatepec' }}
                    </p>
                </div>
                
                <form action="{{ route('tienda') }}" method="GET" class="top-search-form">
                    <div class="input-group flex-nowrap">
                        <input type="text" 
                            name="q" 
                            class="form-control top-search-input" 
                            placeholder="Buscar productos..."
                            value="{{ request('q') }}"
                            required>
                        <button type="submit" class="btn top-search-btn">
                            <i class="fas fa-search"></i>
                            <span class="d-none d-md-inline ms-2">Buscar</span>
                        </button>
                    </div>
                </form>
                
                <div class="search-suggestions mt-3">
                    <div class="row g-1 justify-content-center">
                        @foreach($categorias ?? [] as $categoria)
                        <div class="col-auto">
                            <a href="{{ route('tienda', ['categoria' => $categoria->id]) }}" 
                               class="btn btn-sm btn-outline-light">
                                {{ $categoria->nombre }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <h1 class="hero-title animate__animated animate__fadeInDown mb-3">
                        Creadores del Tinaco Bala
                        <span class="d-block mt-2 fs-4 fs-md-3">Espacios Reducidos</span>
                    </h1>
                    
                    <h2 class="hero-subtitle animate__animated animate__fadeInUp mb-4">
                        Tanques TlálOC - El mejor Tinaco
                    </h2>
                    
                    <div class="company-info mb-4">
                        <div class="row g-2">
                            <div class="col-6 col-md-12">
                                <p class="mb-1">
                                    <i class="fas fa-flag text-primary"></i>
                                    <small><strong>Empresa 100% Mexicana</strong></small>
                                </p>
                            </div>
                            <div class="col-6 col-md-12">
                                <p class="mb-1">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                    <small><strong>Más de 20 años</strong></small>
                                </p>
                            </div>
                            <div class="col-6 col-md-12">
                                <p class="mb-1">
                                    <i class="fas fa-industry text-primary"></i>
                                    <small><strong>Especialistas ROTOMOLDEO</strong></small>
                                </p>
                            </div>
                            <div class="col-6 col-md-12">
                                <p class="mb-1">
                                    <i class="fas fa-lightbulb text-primary"></i>
                                    <small><strong>Innovadores Tinaco Bala</strong></small>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('tienda', ['categoria' => 2]) }}" 
                           class="btn btn-lg hero-btn animate__animated animate__pulse">
                            <i class="fas fa-bolt me-2"></i>Ver Tinaco Bala
                        </a>
                        <a href="https://wa.me/5215540175803" target="_blank" class="btn btn-success btn-lg">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="contact-hero-box">
                        <h5 class="text-center mb-3">
                            <i class="fas fa-phone-alt me-2"></i>{{ $sucursal->nombre ?? 'Sucursal Ecatepec' }}
                        </h5>
                        
                        <div class="phone-list mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                <strong class="small">{{ $sucursal->telefono ?? '55 4017 5803' }}</strong>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                <small>{{ $sucursal->direccion ?? 'Av Morelos Oriente 186 a, Ecatepec' }}</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock me-2 text-primary"></i>
                                <small>{{ $sucursal->horario ?? 'Lun-Vie 9:00-18:00, Sáb 9:00-14:00' }}</small>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <small class="text-muted d-block mb-2">
                                <i class="fas fa-envelope me-1"></i>
                                Email: 
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

    <!-- PRODUCTOS POR CATEGORÍA -->
    <section class="py-4 py-md-5">
        <div class="container">
            <div class="section-header text-center mb-4 mb-md-5">
                <h2 class="section-title mb-2">Nuestros Productos</h2>
                <p class="section-subtitle d-none d-md-block">Especialistas en ROTOMOLDEO desde hace más de 20 años</p>
            </div>
            
            <div class="row g-3 g-md-4">
                @forelse($categorias ?? [] as $categoria)
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="category-card h-100">
                        <a href="{{ route('tienda', ['categoria' => $categoria->id]) }}" class="text-decoration-none">
                            @php
                            $imagenCategoria = "assets/img/categorias/categoria-{$categoria->id}.jpg";
                            @endphp
                            <div class="ratio ratio-1x1">
                                <img src="{{ asset($imagenCategoria) }}" 
                                     class="category-img" 
                                     alt="{{ $categoria->nombre }}"
                                     style="object-fit: cover;">
                            </div>
                            <div class="category-overlay p-2 p-md-3">
                                <h6 class="category-title mb-1">{{ $categoria->nombre }}</h6>
                                <small class="category-description">Ver productos →</small>
                            </div>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No hay categorías disponibles.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- SECCIÓN DE OFERTAS -->
    @if(isset($productosEnOferta) && $productosEnOferta->isNotEmpty())
    <section class="py-4 py-md-5 bg-gradient-oferta">
        <div class="container">
            <div class="section-header text-center mb-4 mb-md-5">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <span class="badge bg-danger fs-6 py-2 px-4 rounded-pill">
                        <i class="fas fa-tag me-2"></i>¡OFERTAS ESPECIALES!
                    </span>
                </div>
                <h2 class="section-title mb-2">Productos en Descuento</h2>
                <p class="section-subtitle">Aprovecha estas promociones por tiempo limitado</p>
                
                @if(isset($ofertasActivas) && $ofertasActivas->isNotEmpty())
                    @foreach($ofertasActivas as $oferta)
                    <div class="alert alert-warning mt-3 mb-0 mx-auto" style="max-width: 600px;">
                        <i class="fas fa-fire me-2"></i>
                        <strong>{{ $oferta->nombre }}:</strong> 
                        @if($oferta->tipo == 'porcentaje')
                            {{ $oferta->valor }}% de descuento
                        @else
                            ${{ number_format($oferta->valor, 2) }} de descuento
                        @endif
                        - Válido hasta {{ \Carbon\Carbon::parse($oferta->fecha_fin)->format('d/m/Y') }}
                    </div>
                    @endforeach
                @endif
            </div>
            
            <div class="row g-3 g-md-4">
                @foreach($productosEnOferta as $producto)
                @php
                    $variante = ProductoHelper::obtenerInfoVariante($producto);
                    $imagen = ProductoHelper::obtenerImagenProducto($producto->codigo);
                @endphp
                <div class="col-6 col-md-4 col-lg-3 d-flex">
                    <div class="product-card w-100 d-flex flex-column position-relative">
                        
                        <!-- OFERTA BADGE -->
                        <div class="oferta-badge position-absolute" style="top: 10px; left: 10px; z-index: 10;">
                            <span class="badge bg-danger shadow-lg" style="font-size: 0.9rem; padding: 8px 15px; border-radius: 25px;">
                                <i class="fas fa-bolt me-1"></i>-{{ ProductoHelper::formatoPorcentaje($producto->porcentaje_descuento) }}%
                            </span>

                        </div>
                        
                        <!-- Badge de litros -->
                        <div class="position-absolute" style="top: 10px; right: 10px; z-index: 2;">
                            <span class="badge bg-info shadow-sm" style="font-size: 0.8rem; padding: 5px 10px;">{{ $producto->litros }} L</span>
                        </div>
                        
                        <!-- Imagen -->
                        <a href="{{ route('producto', $producto->id) }}" class="text-decoration-none">
                            <div style="height: 180px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; border-radius: 12px 12px 0 0;">
                                <img src="{{ $imagen }}" 
                                     class="product-img" 
                                     alt="{{ $producto->nombre }}"
                                     style="max-height: 160px; max-width: 90%; object-fit: contain;">
                            </div>
                        </a>
                        
                        <!-- Contenido -->
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h6 class="product-name mb-2" title="{{ $producto->nombre }}">
                                {{ $producto->nombre }}
                                @if($producto->color)
                                <small class="d-block text-muted">
                                    <span class="color-dot" style="background-color: {{ $variante['hex'] }}; display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 5px;"></span>
                                    {{ $variante['nombre'] }}
                                </small>
                                @endif
                            </h6>
                            
                            <div class="product-specs mb-3">
                                <p class="mb-1 small">
                                    <i class="fas fa-expand-arrows-alt me-2"></i>
                                    <span>Capacidad: {{ $producto->litros }} litros</span>
                                </p>
                                <p class="mb-0 small">
                                    <i class="fas fa-tag me-2"></i>
                                    <span>{{ $producto->categoria->nombre }}</span>
                                </p>
                            </div>
                            
                            <!-- PRECIO CON OFERTA -->
                            <div class="d-flex flex-column mt-auto pt-2">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted text-decoration-line-through small me-2">
                                        ${{ number_format($producto->precio, 2) }}
                                    </span>
                                    <span class="badge bg-success">    -{{ ProductoHelper::formatoPorcentaje($producto->porcentaje_descuento) }}%</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="product-price text-danger fw-bold fs-5">
                                        ${{ number_format($producto->precio_final, 2) }}
                                    </span>
                                    <div class="product-actions d-flex gap-2">
                                        <a href="{{ route('producto', $producto->id) }}" 
                                           class="btn-action view-btn" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <form action="{{ route('carrito.agregar') }}" method="POST" class="d-inline add-to-cart-form">
                                            @csrf
                                            <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                            <input type="hidden" name="cantidad" value="1">
                                            <button type="button" class="btn-action cart-btn border-0 btn-add-cart" title="Agregar al carrito">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('tienda', ['en_oferta' => true]) }}" class="btn btn-danger">
                    <i class="fas fa-bolt me-2"></i>Ver Todas las Ofertas
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- PRODUCTOS DESTACADOS -->
    @if($productosDestacados->isNotEmpty())
    <section class="py-4 py-md-5 bg-light">
        <div class="container">
            <div class="section-header d-flex justify-content-between align-items-center mb-4 mb-md-5">
                <div>
                    <h2 class="section-title mb-1 mb-md-2">Productos Destacados</h2>
                    <p class="section-subtitle mb-0 d-none d-md-block">Desde 225 litros hasta 10,000 litros</p>
                </div>
                <a href="{{ route('tienda') }}" class="btn btn-outline-primary d-none d-md-inline-flex">
                    Ver Catálogo <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            
            <div class="row g-3 g-md-4">
                @foreach($productosDestacados as $producto)
                @php
                    $variante = ProductoHelper::obtenerInfoVariante($producto);
                    $imagen = ProductoHelper::obtenerImagenProducto($producto->codigo);
                    $enOferta = $producto->en_oferta;
                @endphp
                <div class="col-6 col-md-4 col-lg-3 d-flex">
                    <div class="product-card w-100 d-flex flex-column">
                        
                        <!-- Badges -->
                        <div class="product-badge position-absolute" style="top: 10px; left: 10px; z-index: 2;">
                            <span class="badge bg-danger mb-1 d-block" style="font-size: 0.7rem; padding: 3px 8px;"><i class="fas fa-star"></i>Destacado</span>
                            @if($enOferta)
                            <span class="badge bg-warning text-dark mb-1 d-block" style="font-size: 0.7rem; padding: 3px 8px;">
                                <i class="fas fa-tag me-1"></i>Oferta
                            </span>
                            @endif
                            <span class="badge bg-info" style="font-size: 0.7rem; padding: 3px 8px;">{{ $producto->litros }} L</span>
                        </div>
                        
                        <!-- Imagen -->
                        <a href="{{ route('producto', $producto->id) }}" class="text-decoration-none">
                            <div style="height: 180px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; border-radius: 12px 12px 0 0;">
                                <img src="{{ $imagen }}" 
                                     class="product-img" 
                                     alt="{{ $producto->nombre }}"
                                     style="max-height: 160px; max-width: 90%; object-fit: contain;">
                            </div>
                        </a>
                        
                        <!-- Contenido -->
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <h6 class="product-name mb-2" title="{{ $producto->nombre }}">
                                {{ $producto->nombre }}
                                @if($producto->color)
                                <small class="d-block text-muted">
                                    <span class="color-dot" style="background-color: {{ $variante['hex'] }}; display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 3px;"></span>
                                    {{ $variante['nombre'] }}
                                </small>
                                @endif
                            </h6>
                            
                            <div class="product-specs mb-3">
                                <p class="mb-1 small">
                                    <i class="fas fa-expand-arrows-alt me-2"></i>
                                    <span>Capacidad: {{ $producto->litros }} litros</span>
                                </p>
                                <p class="mb-0 small">
                                    <i class="fas fa-tag me-2"></i>
                                    <span>{{ $producto->categoria->nombre }}</span>
                                </p>
                            </div>
                            
                            <!-- Precio y botones -->
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                                @if($enOferta)
                                <div class="d-flex flex-column">
                                    <span class="text-muted text-decoration-line-through small">${{ number_format($producto->precio, 2) }}</span>
                                    <span class="product-price text-danger fw-bold">${{ number_format($producto->precio_final, 2) }}</span>
                                </div>
                                @else
                                <span class="product-price fw-bold">${{ number_format($producto->precio, 2) }}</span>
                                @endif
                                
                                <div class="product-actions d-flex gap-2">
                                    <a href="{{ route('producto', $producto->id) }}" 
                                       class="btn-action view-btn" 
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <form action="{{ route('carrito.agregar') }}" method="POST" class="d-inline add-to-cart-form">
                                        @csrf
                                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                        <input type="hidden" name="cantidad" value="1">
                                        <button type="button" class="btn-action cart-btn border-0 btn-add-cart" title="Agregar al carrito">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-4 d-md-none">
                <a href="{{ route('tienda') }}" class="btn btn-outline-primary">
                    Ver Catálogo Completo <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- CALIDAD Y MATERIALES -->
    <section class="py-4 py-md-5 bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="section-title mb-3 mb-md-4">Calidad y Materiales</h2>
                    <div class="quality-list">
                        @php
                        $calidades = [
                            ['icon' => 'fas fa-certificate', 'title' => 'Material de Primera Calidad', 'desc' => 'Polietileno de máxima calidad: HDPE y LMDPE.'],
                            ['icon' => 'fas fa-leaf', 'title' => 'Materia Prima 100% Virgen', 'desc' => 'Garantizamos impermeabilidad absoluta y grado alimenticio.'],
                            ['icon' => 'fas fa-file-contract', 'title' => 'Normativa Mexicana', 'desc' => 'Cumplimos con la Norma NMX-C-374-ONNCCE-CNCP-2012.']
                        ];
                        @endphp
                        @foreach($calidades as $item)
                        <div class="quality-item d-flex align-items-start mb-3 mb-md-4">
                            <div class="quality-icon-circle flex-shrink-0">
                                <i class="{{ $item['icon'] }}"></i>
                            </div>
                            <div class="quality-content ms-3">
                                <h6 class="fw-bold mb-1">{{ $item['title'] }}</h6>
                                <p class="text-muted mb-0 small">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="applications-card p-3 p-md-5">
                        <h3 class="text-white mb-3 mb-md-4 fw-bold">Aplicaciones Industriales</h3>
                        <div class="row g-2 g-md-4">
                            @php
                            $aplicaciones = [
                                ['icon' => 'fas fa-hard-hat', 'text' => 'Construcción'],
                                ['icon' => 'fas fa-tractor', 'text' => 'Agrícola'],
                                ['icon' => 'fas fa-flask', 'text' => 'Química'],
                                ['icon' => 'fas fa-mountain', 'text' => 'Minera'],
                                ['icon' => 'fas fa-fish', 'text' => 'Pesquera'],
                                ['icon' => 'fas fa-gas-pump', 'text' => 'Petrolera']
                            ];
                            @endphp
                            @foreach($aplicaciones as $app)
                            <div class="col-4 col-sm-4 col-md-4 col-lg-4 text-center app-item">
                                <div class="app-icon-box mb-2">
                                    <i class="{{ $app['icon'] }}"></i>
                                </div>
                                <p class="small fw-bold mb-0">{{ $app['text'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DISTRIBUCIÓN Y CONTACTO -->
    <section class="py-4 py-md-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <h2 class="section-title mb-3 mb-md-4">Distribución Local y Ubicación</h2>
                    
                    <div class="row mb-3 mb-md-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="info-card-box h-100">
                                <div class="d-flex align-items-center">
                                    <div class="info-icon-circle me-3">
                                        <i class="fas fa-truck-moving"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="mb-1">Envíos Locales</h6>
                                        <p class="text-muted small mb-0">Cobertura local con logística especializada.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card-box h-100">
                                <div class="d-flex align-items-center">
                                    <div class="info-icon-circle me-3">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="mb-1">Precios a Distribuidores</h6>
                                        <p class="text-muted small mb-0">Condiciones especiales para ventas al mayoreo.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="map-wrapper-inline shadow-sm mb-3 mb-md-4">
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15034.895173145875!2d-99.0390942!3d19.5963305!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1f1add0886f97%3A0xa1f81173c822f26c!2sTanques%20Tl%C3%A1loc!5e0!3m2!1ses!2smx!4v1712167669350!5m2!1ses!2smx" 
                                style="border:0;" allowfullscreen="" loading="lazy">
                            </iframe>
                        </div>
                    </div>

                    <div class="address-card-inline p-3 p-md-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-map-marker-alt text-danger fs-5 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">{{ $sucursal->nombre ?? 'Sucursal Ecatepec' }}</h6>
                                <p class="mb-0 text-muted small">
                                    {{ $sucursal->direccion ?? 'Av Morelos Oriente 186 a, Colonia San Cristobal Centro, Ecatepec de Morelos, Estado de México. C.P. 55000' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="contact-form-card p-3 p-md-4 shadow-sm border-0">
                        <h4 class="text-center mb-3 mb-md-4 fw-bold">¿Necesitas Asesoría?</h4>
                        
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif
                        
                        <form action="{{ route('home.contacto.enviar') }}" method="POST" id="formContacto">
                            @csrf
                            <div class="mb-3">
                                <label class="small fw-bold">Nombre *</label>
                                <input type="text" name="nombre" class="form-control form-control-sm" placeholder="Tu nombre" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold">Teléfono *</label>
                                <input type="tel" name="telefono" class="form-control form-control-sm" placeholder="55 1234 5678" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold">Correo Electrónico *</label>
                                <input type="email" name="email" class="form-control form-control-sm" placeholder="correo@ejemplo.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold">Mensaje *</label>
                                <textarea name="mensaje" class="form-control form-control-sm" rows="3" placeholder="¿En qué podemos ayudarte?" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-green-contact w-100 py-2" id="btnEnviar">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="footer-brand mb-3">
                        <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" class="img-fluid mb-2" style="max-height: 40px;">
                        <h5 class="mb-1">Tanques Tlaloc - {{ $sucursal->nombre ?? 'Ecatepec' }}</h5>
                        <p class="small mb-0">{{ $sucursal->direccion ?? 'Ecatepec, Estado de México' }}</p>
                    </div>
                    
                    <div class="footer-contact">
                        <h6 class="mb-2">Contacto Directo</h6>
                        <p class="mb-1 small">
                            <i class="fas fa-phone me-2"></i>{{ $sucursal->telefono ?? '55 4017 5803' }}
                        </p>
                        <p class="mb-1 small">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:{{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}" class="text-white">
                                {{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}
                            </a>
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="footer-links">
                        <h6 class="mb-2">Productos</h6>
                        <div class="row">
                            <div class="col-6">
                                <ul class="list-unstyled">
                                    <li><a href="{{ route('tienda', ['categoria' => 2]) }}" class="small">Tinaco Bala</a></li>
                                    <li><a href="{{ route('tienda', ['categoria' => 1]) }}" class="small">Tinacos</a></li>
                                    <li><a href="{{ route('tienda', ['categoria' => 3]) }}" class="small">Cisternas</a></li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <ul class="list-unstyled">
                                    <li><a href="{{ route('tienda', ['categoria' => 4]) }}" class="small">Accesorios</a></li>
                                    <li><a href="{{ route('tienda') }}" class="small">Catálogo</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="footer-links">
                        <h6 class="mb-2">Empresa</h6>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('home') }}" class="small">Inicio</a></li>
                            <li><a href="{{ route('tienda') }}" class="small">Tienda</a></li>
                            <li><a href="{{ route('contacto') }}" class="small">Contacto</a></li>
                        </ul>
                        
                        <h6 class="mt-3 mb-2">Síguenos</h6>
                        <div class="social-icons">
                            <a href="#" class="social-icon facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-icon whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="#" class="social-icon phone">
                                <i class="fas fa-phone"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4 pt-3 border-top border-secondary">
                <div class="col-12">
                    <p class="mb-1 text-center small">
                        <strong>Tanques Tlaloc</strong> - Creadores del Tinaco Bala • Empresa 100% Mexicana
                    </p>
                    <p class="mb-0 text-center small">
                        Especialistas en ROTOMOLDEO con más de 20 años de experiencia
                    </p>
                    <p class="mt-2 mb-0 text-center small">
                        &copy; {{ date('Y') }} Tanques Tlaloc. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Script para manejar el carrito con AJAX -->
    <script>
    $(document).ready(function() {
        // Función para actualizar badge del carrito
        function actualizarBadgeCarrito(nuevoContador) {
            $('.cart-badge').text(nuevoContador).fadeIn(100);
            
            // Animación
            $('.cart-badge').addClass('animate__animated animate__pulse');
            setTimeout(() => {
                $('.cart-badge').removeClass('animate__animated animate__pulse');
            }, 500);
        }
        
        // ===== MANEJO DEL CARRITO CON AJAX =====
        $(document).on('click', '.btn-add-cart', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const $form = $btn.closest('form');
            const productCard = $btn.closest('.product-card');
            const productName = productCard.find('.product-name').text().trim();
            
            // SweetAlert de confirmación
            Swal.fire({
                title: '¿Añadir al carrito?',
                html: `¿Deseas agregar <strong>"${productName}"</strong> a tu compra?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7fad39',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, agregar',
                cancelButtonText: 'Cancelar',
                background: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: $form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Agregando...',
                                text: 'Por favor espera',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading(),
                                background: '#fff'
                            });
                        },
                        success: function(response) {
                            // Cerrar loading
                            Swal.close();
                            
                            // Actualizar badge con el nuevo contador
                            if (response && response.cartCount !== undefined) {
                                actualizarBadgeCarrito(response.cartCount);
                            }
                            
                            // SweetAlert de éxito
                            Swal.fire({
                                icon: 'success',
                                title: '¡Producto Agregado!',
                                html: `
                                    <div style="text-align: center; padding: 10px;">
                                        <div style="background-color: #f0f9f0; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                                            <i class="fas fa-check-circle" style="font-size: 48px; color: #7fad39;"></i>
                                        </div>
                                        <p style="font-size: 18px; font-weight: bold; margin-bottom: 10px; color: #333;">${productName}</p>
                                        <p style="margin-bottom: 15px; color: #666;">Cantidad: 1</p>
                                        <div style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 50px; padding: 12px 25px; display: inline-block; margin: 0 auto;">
                                            <span style="color: #495057; margin-right: 10px; font-size: 14px;">🛒 Carrito:</span>
                                            <span style="background: linear-gradient(135deg, #7fad39, #5d8c2c); color: white; font-weight: bold; padding: 5px 15px; border-radius: 50px; font-size: 20px; box-shadow: 0 4px 10px rgba(127,173,57,0.3);">${response.cartCount || $('.cart-badge').text()}</span>
                                        </div>
                                    </div>
                                `,
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#7fad39',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: '<i class="fas fa-shopping-cart me-2"></i> Ver Carrito',
                                cancelButtonText: '<i class="fas fa-store me-2"></i> Seguir Comprando',
                                background: '#fff'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '/carrito';
                                }
                            });
                        },
                        error: function(xhr) {
                            // Cerrar loading
                            Swal.close();
                            
                            let mensaje = 'No se pudo agregar el producto';
                            let titulo = 'Stock insuficiente';
                            
                            // Si el servidor devolvió un mensaje
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                mensaje = xhr.responseJSON.message;
                                
                                // Personalizar título si es error de stock
                                if (mensaje.toLowerCase().includes('stock') || 
                                    mensaje.toLowerCase().includes('unidades') ||
                                    mensaje.toLowerCase().includes('disponibles')) {
                                    titulo = 'Stock insuficiente';
                                }
                            } else if (xhr.status === 400) {
                                mensaje = 'No hay suficiente stock disponible';
                                titulo = 'Stock insuficiente';
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: titulo,
                                text: mensaje,
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'Entendido',
                                background: '#fff'
                            });
                        }
                    });
                }
            });
        });

        // ===== CERRAR ALERTAS AUTOMÁTICAMENTE =====
        setTimeout(function() {
            $('.alert').each(function() {
                var $alert = $(this);
                
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    var bsAlert = new bootstrap.Alert($alert[0]);
                    setTimeout(function() {
                        bsAlert.close();
                    }, 5000);
                } 
                else if ($alert.find('.btn-close').length) {
                    setTimeout(function() {
                        $alert.find('.btn-close').click();
                    }, 5000);
                }
                else {
                    setTimeout(function() {
                        $alert.fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 5000);
                }
            });
        }, 100);

        // ===== MANEJO DEL FORMULARIO DE CONTACTO =====
        const $formContacto = $('#formContacto');
        const $btnEnviar = $('#btnEnviar');
        
        if ($formContacto.length > 0) {
            // Validación en tiempo real (cuando el usuario escribe)
            $formContacto.find('input, textarea').on('input', function() {
                validarCampoEnTiempoReal($(this));
            });

            // Validación cuando el campo pierde el foco
            $formContacto.find('input, textarea').on('blur', function() {
                validarCampoCompleto($(this));
            });

            // Validación al enviar
            $formContacto.on('submit', function(e) {
                let isValid = true;
                
                // Validar todos los campos
                if (!validarCampoCompleto($('[name="nombre"]'))) isValid = false;
                if (!validarCampoCompleto($('[name="telefono"]'))) isValid = false;
                if (!validarCampoCompleto($('[name="email"]'))) isValid = false;
                if (!validarCampoCompleto($('[name="mensaje"]'))) isValid = false;
                
                if (!isValid) {
                    e.preventDefault();
                    mostrarAlerta('Por favor, corrige los errores en el formulario', 'error');
                    return false;
                }
                
                // Deshabilitar botón
                if ($btnEnviar.length > 0) {
                    $btnEnviar.prop('disabled', true);
                    $btnEnviar.html('<i class="fas fa-spinner fa-spin me-2"></i>Enviando...');
                }
            });
        }
        
        // ===== FUNCIÓN PARA VALIDACIÓN EN TIEMPO REAL (solo mientras escribe) =====
        function validarCampoEnTiempoReal($campo) {
            const valor = $campo.val();
            const nombreCampo = $campo.attr('name');
            
            // Solo validar mientras escribe (sin mostrar errores todavía)
            if (nombreCampo === 'nombre') {
                // Permitir solo letras mientras escribe
                const soloLetras = valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                if (valor !== soloLetras) {
                    $campo.val(soloLetras); // Eliminar caracteres no permitidos
                }
            }
            
            if (nombreCampo === 'telefono') {
                // Permitir solo números mientras escribe
                const soloNumeros = valor.replace(/[^0-9]/g, '');
                if (valor !== soloNumeros) {
                    $campo.val(soloNumeros); // Eliminar caracteres no permitidos
                }
            }
        }
        
        // ===== FUNCIÓN PARA VALIDACIÓN COMPLETA (al perder foco o enviar) =====
        function validarCampoCompleto($campo) {
            const valor = $campo.val().trim();
            const nombreCampo = $campo.attr('name');
            
            $campo.removeClass('is-valid is-invalid');
            
            if (!valor) {
                $campo.addClass('is-invalid');
                return false;
            }
            
            // Validación para NOMBRE (solo letras)
            if (nombreCampo === 'nombre') {
                const soloLetrasRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
                if (!soloLetrasRegex.test(valor)) {
                    $campo.addClass('is-invalid');
                    mostrarAlerta('El nombre solo puede contener letras y espacios', 'error');
                    return false;
                }
                if (valor.length < 3) {
                    $campo.addClass('is-invalid');
                    mostrarAlerta('El nombre debe tener al menos 3 caracteres', 'error');
                    return false;
                }
                $campo.addClass('is-valid');
                return true;
            }
            
            // Validación para TELÉFONO (solo números)
            if (nombreCampo === 'telefono') {
                const soloNumerosRegex = /^\d+$/;
                if (!soloNumerosRegex.test(valor)) {
                    $campo.addClass('is-invalid');
                    mostrarAlerta('El teléfono solo puede contener números', 'error');
                    return false;
                }
                if (valor.length !== 10) {
                    $campo.addClass('is-invalid');
                    mostrarAlerta('El teléfono debe tener 10 dígitos', 'error');
                    return false;
                }
                $campo.addClass('is-valid');
                return true;
            }
            
            // Validación para EMAIL
            if (nombreCampo === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(valor)) {
                    $campo.addClass('is-invalid');
                    mostrarAlerta('Ingresa un correo electrónico válido', 'error');
                    return false;
                }
                $campo.addClass('is-valid');
                return true;
            }
            
            // Validación para MENSAJE
            if (nombreCampo === 'mensaje') {
                if (valor.length < 10) {
                    $campo.addClass('is-invalid');
                    mostrarAlerta('El mensaje debe tener al menos 10 caracteres', 'error');
                    return false;
                }
                $campo.addClass('is-valid');
                return true;
            }
            
            return true;
        }
        
        // ===== FUNCIÓN PARA MOSTRAR ALERTAS =====
        function mostrarAlerta(mensaje, tipo = 'info') {
            Swal.fire({
                icon: tipo,
                title: tipo === 'success' ? 'Éxito' : 
                       tipo === 'error' ? 'Error' : 
                       tipo === 'warning' ? 'Advertencia' : 'Información',
                text: mensaje,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        }
    });
    </script>
    
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    @endif
</body>
</html>