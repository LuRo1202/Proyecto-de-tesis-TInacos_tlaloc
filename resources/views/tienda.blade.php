<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tituloCategoria }} | Tanques Tlaloc - {{ $sucursal->nombre }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/tienda.css') }}">
    
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" class="img-fluid" style="max-height: 50px;">
            </a>
            
            <!-- Botón carrito móvil -->
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
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" 
                        href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ (request()->routeIs('tienda') && request()->get('categoria') != 2) ? 'active' : (request()->routeIs('tienda') && !request()->has('categoria') ? 'active' : '') }}" 
                        href="{{ route('tienda') }}">Tienda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tienda') && request()->get('categoria') == 2 ? 'active' : '' }}" 
                        href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contacto') ? 'active' : '' }}" 
                        href="{{ route('contacto') }}">Contacto</a>
                    </li>
                </ul>
                
                <!-- Botones desktop -->
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
                    <h4 class="text-white mb-2" style="font-weight: 600;">
                        <i class="fas fa-search me-2"></i>¿Qué necesitas hoy?
                    </h4>
                    <p class="text-white mb-3 d-none d-md-block" style="opacity: 0.9; font-size: 1.05rem;">
                        Encuentra tinacos, cisternas y accesorios en {{ $sucursal->nombre }}
                    </p>
                </div>
                
                <form action="{{ route('tienda') }}" method="GET" class="top-search-form">
                    <div class="input-group">
                        <input type="text" 
                            name="q" 
                            class="form-control top-search-input" 
                            placeholder="Ej: TIN-225, 225, tinaco, bala, accesorio..."
                            value="{{ $busqueda }}"
                            autocomplete="off">
                        @if($precio_min > 0 || $precio_max > 0)
                        <input type="hidden" name="precio_min" value="{{ $precio_min }}">
                        <input type="hidden" name="precio_max" value="{{ $precio_max }}">
                        @endif
                        @if($categoria_id > 0)
                        <input type="hidden" name="categoria" value="{{ $categoria_id }}">
                        @endif
                        <button type="submit" class="btn top-search-btn">
                            <i class="fas fa-search me-2"></i>
                            <span class="d-none d-md-inline">Buscar</span>
                        </button>
                    </div>
                </form>
                
                <div class="search-suggestions mt-3">
                    <div class="row g-1 justify-content-center">
                        @foreach($categorias as $cat)
                        <div class="col-auto">
                            <a href="{{ route('tienda', ['categoria' => $cat->id]) }}" 
                               class="btn btn-sm btn-outline-light">
                                {{ $cat->nombre }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4 py-md-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar de categorías y filtros -->
                <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
                    <!-- Botón para móvil -->
                    <button class="btn btn-outline-primary w-100 mb-3 d-md-none" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#sidebarFilters">
                        <i class="fas fa-filter me-2"></i> Mostrar/Ocultar Filtros
                    </button>
                    
                    <div class="collapse d-md-block" id="sidebarFilters">
                        <!-- Info sucursal -->
                        <div class="card filter-card hover-lift mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-store text-primary me-2"></i>{{ $sucursal->nombre }}
                                </h6>
                                <div class="small">
                                    <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $sucursal->telefono ?? '55 4017 5803' }}</p>
                                    <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ $sucursal->direccion ?? 'Ecatepec, Estado de México' }}</p>
                                    <p class="mb-0"><i class="fas fa-clock me-2"></i>{{ $sucursal->horario ?? 'Lun-Vie 9:00-18:00, Sáb 9:00-14:00' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Categorías -->
                        <div class="card filter-card hover-lift mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-list text-primary me-2"></i>Categorías
                                </h6>
                                <div class="list-group list-group-flush categories-list">
                                    <a href="{{ route('tienda') }}" 
                                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ ($categoria_id == 0 && empty($busqueda)) ? 'active' : '' }}">
                                        Todos los productos
                                        <span class="badge bg-primary rounded-pill">{{ count($productosAgrupados) }}</span>
                                    </a>
                                    @foreach($categorias as $cat)
                                    <a href="{{ route('tienda', ['categoria' => $cat->id]) }}" 
                                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ ($categoria_id == $cat->id) ? 'active' : '' }}">
                                        <small>{{ $cat->nombre }}</small>
                                        <span class="badge bg-secondary rounded-pill">
                                            {{ $sucursal->productos()->where('categoria_id', $cat->id)->wherePivot('existencias', '>', 0)->count() }}
                                        </span>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filtro por precio -->
                        <div class="card filter-card hover-lift mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-filter me-2"></i> Filtrar por Precio
                                </h6>
                                
                                @if($precio_min > 0 || $precio_max > 0)
                                <div class="alert filter-alert mb-3">
                                    <small>
                                        <i class="fas fa-filter me-1"></i>
                                        <strong>Filtro aplicado:</strong> 
                                        ${{ number_format($precio_min) }} - ${{ number_format($precio_max) }}
                                        <a href="{{ route('tienda', ['categoria' => $categoria_id, 'q' => $busqueda]) }}" 
                                           class="ms-2 text-danger">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </small>
                                </div>
                                @endif
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-2">Rangos sugeridos:</small>
                                    <div class="list-group list-group-flush small price-ranges">
                                        @php
                                        $rangos = [
                                            ['min' => 0, 'max' => 1000, 'label' => 'Hasta $1,000'],
                                            ['min' => 1001, 'max' => 2000, 'label' => '$1,001 - $2,000'],
                                            ['min' => 2001, 'max' => 3000, 'label' => '$2,001 - $3,000'],
                                            ['min' => 3001, 'max' => 5000, 'label' => '$3,001 - $5,000'],
                                            ['min' => 5001, 'max' => 10000, 'label' => '$5,001 - $10,000'],
                                            ['min' => 10001, 'max' => 20000, 'label' => '$10,001 - $20,000'],
                                            ['min' => 20001, 'max' => 0, 'label' => 'Más de $20,000'],
                                        ];
                                        @endphp
                                        @foreach($rangos as $rango)
                                        <a href="{{ route('tienda', array_merge(request()->except(['precio_min', 'precio_max']), ['precio_min' => $rango['min'], 'precio_max' => $rango['max']])) }}" 
                                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">
                                            <span>{{ $rango['label'] }}</span>
                                            <i class="fas fa-chevron-right small"></i>
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <small class="text-muted d-block mb-2">Rango personalizado:</small>
                                    <form action="{{ route('tienda') }}" method="GET" class="row g-2">
                                        @if($categoria_id > 0)
                                        <input type="hidden" name="categoria" value="{{ $categoria_id }}">
                                        @endif
                                        @if(!empty($busqueda))
                                        <input type="hidden" name="q" value="{{ $busqueda }}">
                                        @endif
                                        
                                        <div class="col-6">
                                            <input type="number" 
                                                   name="precio_min" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Mín"
                                                   value="{{ $precio_min > 0 ? $precio_min : '' }}"
                                                   min="0">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" 
                                                   name="precio_max" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Máx"
                                                   value="{{ $precio_max > 0 ? $precio_max : '' }}"
                                                   min="0">
                                        </div>
                                        <div class="col-12 mt-2">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-search me-1"></i> Aplicar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Consejos de búsqueda -->
                        <div class="card tips-card hover-lift mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-lightbulb text-warning me-2"></i> Consejos de búsqueda
                                </h6>
                                <ul class="list-unstyled small mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Código exacto:</strong> TIN-225
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Por capacidad:</strong> 225
                                    </li>
                                    <li>
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Por tipo:</strong> tinaco, bala
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                        
                <!-- Lista de productos -->
                <div class="col-lg-9 col-md-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h4 mb-0">{{ $tituloCategoria }}</h1>
                        <span class="badge bg-info">{{ count($productosAgrupados) }} productos</span>
                    </div>
                    
                    @if(!empty($busqueda) || $precio_min > 0 || $precio_max > 0)
                    <div class="alert alert-info mb-4">
                        <p class="mb-0 small">
                            Mostrando <strong>{{ count($productosAgrupados) }} producto(s)</strong>
                            @if(!empty($busqueda))
                            para: <strong>"{{ $busqueda }}"</strong>
                            @endif
                        </p>
                    </div>
                    @endif
                    
                    <!-- Grid de productos -->
                    @php use App\Helpers\ProductoHelper; @endphp
                    
                    <div class="row g-3">
                        @forelse($productosAgrupados as $familia => $datos)
                            @php
                                $productoPrincipal = $datos['principal'];
                                $variantes = $datos['variantes'];
                                $imagenPrincipal = ProductoHelper::obtenerImagenProducto($productoPrincipal->codigo);
                                $esAccesorio = (strpos($productoPrincipal->codigo, 'ACC-') === 0);
                                $esDispensador = ($familia === 'DISP-20');
                                
                                // OBTENER PROPIEDADES DE OFERTA DEL PRODUCTO PRINCIPAL
                                $enOferta = $productoPrincipal->en_oferta ?? false;
                                $precioOriginal = $productoPrincipal->precio_original ?? $productoPrincipal->precio;
                                $precioFinal = $productoPrincipal->precio_final ?? $productoPrincipal->precio;
                                $porcentajeDescuento = $productoPrincipal->porcentaje_descuento ?? 0;
                                $descuentoFormateado = ProductoHelper::formatoPorcentaje($porcentajeDescuento);
                            @endphp
                            
                            <div class="col-xxl-3 col-lg-4 col-md-4 col-sm-2 col-6">
                                <div class="card product-card h-100 shadow-sm border-0">
                                    <!-- Imagen del producto -->
                                    <div class="position-relative overflow-hidden rounded-top" 
                                         style="height: 200px; background: #f8f9fa;"
                                         id="imagen-{{ $familia }}">
                                        <img src="{{ $imagenPrincipal }}" 
                                             alt="{{ $productoPrincipal->nombre }}" 
                                             class="img-fluid h-100 w-100 object-fit-contain p-3"
                                             id="img-{{ $familia }}">
                                        
                                        <!-- Badges -->
                                        <div class="position-absolute top-0 start-0 m-2">
                                            @if($enOferta)
                                            <span class="badge bg-danger small">
                                                <i class="fas fa-tag"></i> -{{ $descuentoFormateado }}%
                                            </span>
                                            @elseif($productoPrincipal->destacado)
                                            <span class="badge bg-danger small">
                                                <i class="fas fa-star"></i> Destacado
                                            </span>
                                            @endif
                                        </div>
                                        
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-info small">
                                                {{ $productoPrincipal->litros }} L
                                            </span>
                                        </div>
                                        
                                        <div class="position-absolute bottom-0 end-0 m-2">
                                            <form action="{{ route('carrito.agregar') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="producto_id" value="{{ $productoPrincipal->id }}" id="input-{{ $familia }}">
                                                <input type="hidden" name="cantidad" value="1">
                                                <button type="button" class="btn btn-success btn-sm btn-add-cart rounded-circle shadow"
                                                        title="Agregar al carrito">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <!-- Información del producto -->
                                    <div class="card-body d-flex flex-column p-3">
                                        <!-- Nombre -->
                                        <h6 class="card-title mb-2" style="font-size: 0.95rem; line-height: 1.3; min-height: 2.8rem;">
                                            <a href="{{ route('producto', $productoPrincipal->id) }}" 
                                               id="link-{{ $familia }}"
                                               class="text-decoration-none text-dark fw-bold">
                                                {{ $productoPrincipal->nombre }}
                                            </a>
                                            @if($productoPrincipal->color)
                                            <small class="d-block text-muted" id="color-text-{{ $familia }}">
                                                <span class="color-dot" style="background-color: {{ $productoPrincipal->color->codigo_hex ?? '#ccc' }}; display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 3px;"></span>
                                                Color: {{ $productoPrincipal->color->nombre }}
                                            </small>
                                            @endif
                                        </h6>
                                        
                                        <!-- Capacidad y código -->
                                        <div class="mb-2">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-tint text-primary me-2 small"></i> 
                                                <small><strong>{{ $productoPrincipal->litros }} litros</strong></small>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-barcode text-secondary me-2 small"></i> 
                                                <small class="text-muted" id="codigo-{{ $familia }}">{{ $productoPrincipal->codigo }}</small>
                                            </div>
                                        </div>
                                                                        
                                        <!-- Selector de variantes -->
                                        @if(count($variantes) > 1 && !$esAccesorio)
                                        <div class="selector-variantes mb-3">
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-list-alt text-warning me-1"></i> 
                                                <strong>Opciones:</strong>
                                            </small>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($variantes as $index => $variante)
                                                    @php
                                                        $sinExistencia = ($variante->pivot->existencias <= 0);
                                                        $info = ProductoHelper::obtenerInfoVariante($variante);
                                                        $esPrincipal = ($variante->id == $productoPrincipal->id);
                                                        $claseTipo = 'tipo-' . $info['tipo'];
                                                        $claseActivo = $esPrincipal ? 'activo' : '';
                                                        $claseDisabled = $sinExistencia ? 'disabled' : '';
                                                        
                                                        // DATOS DE OFERTA PARA LA VARIANTE
                                                        $varianteEnOferta = $variante->en_oferta ?? false;
                                                        $varianteDescuento = $variante->porcentaje_descuento ?? 0;
                                                        $varianteDescuentoFormateado = ProductoHelper::formatoPorcentaje($varianteDescuento);
                                                    @endphp
                                                    <button type="button" 
                                                            class="btn-variante btn-sm {{ $claseTipo }} {{ $claseActivo }} {{ $claseDisabled }}"
                                                            data-variante-id="{{ $variante->id }}"
                                                            data-familia="{{ $familia }}"
                                                            data-imagen="{{ ProductoHelper::obtenerImagenProducto($variante->codigo) }}"
                                                            data-codigo="{{ $variante->codigo }}"
                                                            data-precio="{{ $variante->precio }}"
                                                            data-precio-final="{{ $variante->precio_final ?? $variante->precio }}"
                                                            data-en-oferta="{{ $varianteEnOferta ? 'true' : 'false' }}"
                                                            data-descuento="{{ $varianteDescuento }}"
                                                            data-descuento-formateado="{{ $varianteDescuentoFormateado }}"
                                                            data-stock="{{ $variante->pivot->existencias }}"
                                                            data-color="{{ $info['nombre'] }}"
                                                            data-color-hex="{{ $info['hex'] }}"
                                                            onclick="cambiarVariante(this, '{{ $familia }}')"
                                                            {{ $sinExistencia ? 'disabled' : '' }}
                                                            title="{{ $variante->nombre }} - {{ $info['nombre'] }}">
                                                        
                                                        @if($info['tipo'] === 'color')
                                                        <span class="variante-color" style="background-color: {{ $info['hex'] }}"></span>
                                                        @else
                                                        <i class="{{ $info['icono'] }} small"></i>
                                                        @endif
                                                        
                                                        
                                                        @if($sinExistencia)
                                                        <span class="variante-agotado">✗</span>
                                                        @endif
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Precio y stock -->
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div id="price-container-{{ $familia }}">
                                                    @if($enOferta)
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted text-decoration-line-through small">
                                                            {{ ProductoHelper::formatoPrecio($precioOriginal) }}
                                                        </span>
                                                        <h5 class="text-danger fw-bold mb-0" id="precio-{{ $familia }}">
                                                            {{ ProductoHelper::formatoPrecio($precioFinal) }}
                                                        </h5>
                                                    </div>
                                                    @else
                                                    <h5 class="text-success fw-bold mb-0" id="precio-{{ $familia }}">
                                                        {{ ProductoHelper::formatoPrecio($precioOriginal) }}
                                                    </h5>
                                                    @endif
                                                </div>
                                                
                                                <div class="stock-info" id="stock-{{ $familia }}">
                                                    @if($productoPrincipal->pivot->existencias <= 0)
                                                    <span class="badge bg-danger p-1 small">
                                                        <i class="fas fa-times-circle"></i> Agotado
                                                    </span>
                                                    @elseif($productoPrincipal->pivot->existencias < 5)
                                                    <span class="badge bg-warning text-dark p-1 small">
                                                        <i class="fas fa-exclamation-triangle"></i> Últimas {{ $productoPrincipal->pivot->existencias }}
                                                    </span>
                                                    @else
                                                    <span class="badge bg-success p-1 small">
                                                        <i class="fas fa-check-circle"></i> {{ $productoPrincipal->pivot->existencias }} disp.
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Botón ver detalles -->
                                            <div class="d-grid">
                                                <a href="{{ route('producto', $productoPrincipal->id) }}" 
                                                   id="link-{{ $familia }}"
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i> Ver detalles
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning shadow-sm">
                                    <p class="mb-0">No se encontraron productos.</p>
                                </div>
                            </div>
                        @endforelse
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
                    <div class="footer-brand mb-4">
                        <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" class="img-fluid mb-2" style="max-height: 50px;">
                        <h5 class="mt-2 mb-1">Tanques Tlaloc - {{ $sucursal->nombre }}</h5>
                        <p class="mb-0 small">{{ $sucursal->direccion ?? 'Ecatepec, Estado de México' }}</p>
                    </div>
                    
                    <div class="footer-contact">
                        <h6 class="mb-2">Contacto Directo</h6>
                        <p class="mb-2">
                            <i class="fas fa-phone me-2"></i>{{ $sucursal->telefono ?? '55 4017 5803' }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:{{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}" class="text-white small">
                                {{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}
                            </a>
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="footer-links">
                        <h6 class="mb-2">Productos</h6>
                        <ul class="list-unstyled">
                            @foreach($categorias as $cat)
                            <li><a href="{{ route('tienda', ['categoria' => $cat->id]) }}" class="small">{{ $cat->nombre }}</a></li>
                            @endforeach
                            <li><a href="{{ route('tienda') }}" class="small">Catálogo Completo</a></li>
                        </ul>
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
    
    <!-- Datos de variantes -->
    <script>
        const variantesData = @json($productosAgrupados);
    </script>

    <!-- JS de tienda (incluido directamente) -->
    <script>
        // Función para formatear porcentaje
        function formatoPorcentajeJS(valor) {
            return Math.round(parseFloat(valor));
        }

        // Función para formatear precio
        function formatoPrecioJS(precio) {
            return '$' + parseFloat(precio).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        // Función para cambiar variante
        window.cambiarVariante = function(boton, familia) {
            const $boton = $(boton);
            const varianteId = $boton.data('variante-id');
            
            // Buscar en variantesData
            let varianteEncontrada = null;
            if (variantesData[familia] && variantesData[familia].variantes) {
                varianteEncontrada = variantesData[familia].variantes.find(v => v.id == varianteId);
            }
            
            if (varianteEncontrada) {
                // 1. Actualizar botones
                $boton.parent().find('.btn-variante').removeClass('activo');
                $boton.addClass('activo');
                
                // 2. Cambiar imagen
                const $img = $(`#img-${familia}`);
                const nuevaImagen = $boton.data('imagen') || varianteEncontrada.imagen || '/assets/img/productos/' + varianteEncontrada.codigo + '.jpg';
                $img.fadeOut(150, function() {
                    $(this).attr('src', nuevaImagen).fadeIn(150);
                });
                
                // 3. Actualizar código
                $(`#codigo-${familia}`).text($boton.data('codigo') || varianteEncontrada.codigo);
                
                // 4. Actualizar precio (considerando oferta)
                const precioContainer = $(`#price-container-${familia}`);
                const precioOriginal = parseFloat($boton.data('precio') || varianteEncontrada.precio);
                const precioFinal = parseFloat($boton.data('precio-final') || varianteEncontrada.precio_final || varianteEncontrada.precio);
                const enOferta = $boton.data('en-oferta') === 'true' || varianteEncontrada.en_oferta;
                
                if (enOferta) {
                    precioContainer.html(`
                        <div class="d-flex flex-column">
                            <span class="text-muted text-decoration-line-through small">${formatoPrecioJS(precioOriginal)}</span>
                            <h5 class="text-danger fw-bold mb-0" id="precio-${familia}">${formatoPrecioJS(precioFinal)}</h5>
                        </div>
                    `);
                } else {
                    precioContainer.html(`<h5 class="text-success fw-bold mb-0" id="precio-${familia}">${formatoPrecioJS(precioOriginal)}</h5>`);
                }
                
                // 5. Actualizar color
                const colorText = $(`#color-text-${familia}`);
                if (colorText.length) {
                    const colorNombre = $boton.data('color') || varianteEncontrada.color_nombre;
                    const colorHex = $boton.data('color-hex') || '#ccc';
                    colorText.html('<span class="color-dot" style="background-color: ' + colorHex + '; display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 3px;"></span> Color: ' + colorNombre);
                }
                
                // 6. Actualizar stock
                const stock = parseInt($boton.data('stock') || varianteEncontrada.pivot?.existencias || 0);
                const $stock = $(`#stock-${familia}`);
                if (stock <= 0) {
                    $stock.html('<span class="badge bg-danger p-1 small"><i class="fas fa-times-circle"></i> Agotado</span>');
                } else if (stock < 5) {
                    $stock.html('<span class="badge bg-warning text-dark p-1 small"><i class="fas fa-exclamation-triangle"></i> Últimas ' + stock + '</span>');
                } else {
                    $stock.html('<span class="badge bg-success p-1 small"><i class="fas fa-check-circle"></i> ' + stock + ' disp.</span>');
                }
                
                // 7. Actualizar input y link
                $(`#input-${familia}`).val(varianteEncontrada.id);
                $(`#link-${familia}`).attr('href', '/producto/' + varianteEncontrada.id);
                
                // 8. Actualizar badge de oferta
                const badgeOferta = $(`#imagen-${familia}`).find('.badge.bg-danger.small:first');
                if (badgeOferta.length) {
                    if (enOferta) {
                        const descuento = $boton.data('descuento-formateado') || formatoPorcentajeJS($boton.data('descuento') || 0);
                        badgeOferta.html(`<i class="fas fa-tag"></i> -${descuento}%`);
                        badgeOferta.show();
                    } else {
                        badgeOferta.hide();
                    }
                }
            }
        };

        // Actualizar badge del carrito
        function actualizarBadgeCarrito(nuevoContador) {
            $('.cart-badge').text(nuevoContador).fadeIn(100);
            
            // Animación
            $('.cart-badge').addClass('animate__animated animate__pulse');
            setTimeout(() => {
                $('.cart-badge').removeClass('animate__animated animate__pulse');
            }, 500);
        }

        // Manejador del carrito
        $(document).ready(function() {
            
            // Delegación de eventos para botones de agregar al carrito
            $(document).on('click', '.btn-add-cart', function(e) {
                e.preventDefault();
                
                const $btn = $(this);
                const $form = $btn.closest('form');
                const $card = $btn.closest('.card');
                
                // Obtener nombre del producto
                let nombre = $card.find('.card-title a').text().trim() || 
                            $card.find('.card-title').text().trim();
                
                // SweetAlert de confirmación
                Swal.fire({
                    title: '¿Añadir al carrito?',
                    html: `¿Deseas agregar <strong>"${nombre}"</strong> a tu compra?`,
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
                                
                                // ALERTA DE ÉXITO
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Producto Agregado!',
                                    html: `
                                        <div style="text-align: center; padding: 10px;">
                                            <div style="background-color: #f0f9f0; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                                                <i class="fas fa-check-circle" style="font-size: 48px; color: #7fad39;"></i>
                                            </div>
                                            <p style="font-size: 18px; font-weight: bold; margin-bottom: 10px; color: #333;">${nombre}</p>
                                            <p style="margin-bottom: 15px; color: #666;">Cantidad: 1</p>
                                            <div style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 50px; padding: 12px 25px; display: inline-block; margin: 0 auto;">
                                                <span style="color: #495057; margin-right: 10px; font-size: 14px;">🛒 Carrito:</span>
                                                <span style="background: linear-gradient(135deg, #7fad39, #5d8c2c); color: white; font-weight: bold; padding: 5px 15px; border-radius: 50px; font-size: 20px; box-shadow: 0 4px 10px rgba(127,173,57,0.3);">${response.cartCount || $('.cart-badge').text()}</span>
                                            </div>
                                        </div>
                                    `,
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
                                let titulo = 'Error';
                                
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    mensaje = xhr.responseJSON.message;
                                    
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
            
            // Validar filtros de precio
            $('form[action="{{ route('tienda') }}"]').submit(function() {
                let precioMin = $(this).find('input[name="precio_min"]').val();
                let precioMax = $(this).find('input[name="precio_max"]').val();
                
                if (precioMin && precioMax && parseFloat(precioMin) > parseFloat(precioMax)) {
                    alert('El precio mínimo no puede ser mayor que el precio máximo');
                    return false;
                }
                
                return true;
            });
        });
    </script>

    <!-- SweetAlert para mensajes de sesión -->
    @if(session('swal'))
    <script>
        Swal.fire({
            icon: '{{ session('swal')['type'] }}',
            title: '{{ session('swal')['title'] }}',
            text: '{{ session('swal')['message'] }}',
            timer: 2500,
            showConfirmButton: false
        });
    </script>
    @endif
</body>
</html>