{{-- resources/views/cliente/pedidos.blade.php --}}
@php
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mis Pedidos | Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Personalizado (TODOS LOS ESTILOS GLOBALES) -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <style>
        /* ===== SOLO ESTILOS EXCLUSIVOS DE LA PÁGINA DE PEDIDOS ===== */
        /* (NADA DEL HEADER, FOOTER O COMPONENTES GLOBALES) */
        
        :root {
            --verde-principal: #7fad39;
            --verde-oscuro: #5d8c29;
            --verde-suave: #f2f8eb;
            --naranja: #ff6600;
            --naranja-oscuro: #e55a00;
            --gris-fondo: #f5f5f5;
            --gris-borde: #ebebeb;
            --texto-principal: #333;
            --texto-secundario: #666;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .pedidos-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* ===== PAGE HEADER (exclusivo pedidos) ===== */
        .page-header {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border-left: 5px solid var(--verde-principal);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 5px 15px rgba(127, 173, 57, 0.3);
        }

        .header-text h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--texto-principal);
            margin-bottom: 5px;
        }

        .header-text p {
            color: var(--texto-secundario);
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn-custom {
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom {
            background: var(--verde-principal);
            color: white;
            border: 2px solid var(--verde-principal);
        }

        .btn-primary-custom:hover {
            background: var(--verde-oscuro);
            border-color: var(--verde-oscuro);
            transform: translateY(-2px);
            color: white;
        }

        /* ===== FILTROS ===== */
        .filtros-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        .filtros-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filtro-tab {
            padding: 8px 20px;
            border-radius: 30px;
            background: var(--gris-fondo);
            color: var(--texto-principal);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s;
            border: 1px solid var(--gris-borde);
        }

        .filtro-tab:hover {
            background: var(--verde-principal);
            color: white;
            border-color: var(--verde-principal);
        }

        .filtro-tab.active {
            background: var(--verde-principal);
            color: white;
            border-color: var(--verde-principal);
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--texto-secundario);
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid var(--gris-borde);
            border-radius: 30px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--verde-principal);
            box-shadow: 0 0 0 3px rgba(127, 173, 57, 0.1);
        }

        /* ===== LISTA DE PEDIDOS ===== */
        .pedidos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .pedido-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid var(--gris-borde);
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .pedido-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(127, 173, 57, 0.15);
            border-color: var(--verde-principal);
        }

        .pedido-header {
            padding: 15px;
            border-bottom: 1px solid var(--gris-borde);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--gris-fondo);
        }

        .pedido-folio {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--verde-principal);
        }

        .pedido-fecha {
            font-size: 0.85rem;
            color: var(--texto-secundario);
        }

        .pedido-body {
            padding: 20px;
        }

        .pedido-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .pedido-info-label {
            color: var(--texto-secundario);
            font-size: 0.9rem;
        }

        .pedido-info-value {
            font-weight: 600;
            color: var(--texto-principal);
        }

        .pedido-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--verde-principal);
            text-align: right;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed var(--gris-borde);
        }

        .pedido-footer {
            padding: 15px;
            background: var(--gris-fondo);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ===== BADGES DE ESTADO (UNIFICADOS CON DASHBOARD) ===== */
            .badge-estado {
                padding: 8px 16px;
                border-radius: 30px;
                font-size: 0.85rem;
                font-weight: 600;
                display: inline-block;
                text-align: center;
                min-width: 110px;
                letter-spacing: 0.3px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                transition: all 0.2s ease;
            }

            .badge-estado:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            }

            /* Estado Pendiente */
            .badge-pendiente {
                background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
                color: #856404;
                border-left: 3px solid #ffc107;
            }

            /* Estado Confirmado */
            .badge-confirmado {
                background: linear-gradient(135deg, #d1ecf1 0%, #b6e4f0 100%);
                color: #0c5460;
                border-left: 3px solid #17a2b8;
            }

            /* Estado Enviado */
            .badge-enviado {
                background: linear-gradient(135deg, #cce5ff 0%, #b8daff 100%);
                color: #004085;
                border-left: 3px solid #007bff;
            }

            /* Estado Entregado */
            .badge-entregado {
                background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                color: #155724;
                border-left: 3px solid #28a745;
            }

            /* Estado Cancelado */
            .badge-cancelado {
                background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%);
                color: #721c24;
                border-left: 3px solid #dc3545;
            }
        .btn-ver {
            padding: 8px 20px;
            border-radius: 30px;
            background: transparent;
            border: 1px solid var(--verde-principal);
            color: var(--verde-principal);
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-ver:hover {
            background: var(--verde-principal);
            color: white;
        }

        /* ===== PAGINACIÓN ===== */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .page-item {
            list-style: none;
        }

        .page-link {
            display: block;
            padding: 10px 18px;
            border-radius: 10px;
            background: white;
            border: 1px solid var(--gris-borde);
            color: var(--texto-principal);
            text-decoration: none;
            transition: all 0.3s;
        }

        .page-link:hover {
            background: var(--verde-principal);
            color: white;
            border-color: var(--verde-principal);
        }

        .page-item.active .page-link {
            background: var(--verde-principal);
            color: white;
            border-color: var(--verde-principal);
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--gris-borde);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--texto-principal);
        }

        .empty-state p {
            color: var(--texto-secundario);
            margin-bottom: 25px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .pedidos-grid {
                grid-template-columns: 1fr;
            }

            .header-title {
                flex-direction: column;
                text-align: center;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .header-actions {
                justify-content: center;
            }

            .filtros-section {
                flex-direction: column;
                align-items: stretch;
            }

            .filtros-tabs {
                justify-content: center;
            }

            .search-box {
                width: 100%;
            }
        }

                /* ===== BUSCADOR SUPERIOR - MI CUENTA TLÁLOC ===== */
        .top-search-section {
            background: linear-gradient(135deg, #7fad39 0%, #5d8c29 100%);
            padding: 25px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        /* Efecto de brillo animado */
        .top-search-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(45deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.08) 50%, 
                transparent 100%);
            animation: shine 3s infinite linear;
        }

        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Línea decorativa inferior */
        .top-search-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.6) 50%, 
                transparent 100%);
        }

        .top-search-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            color: white;
            position: relative;
            z-index: 1;
            padding: 0 15px;
        }

        /* Título principal */
        .top-search-container h4 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .top-search-container h4 i {
            color: #ffdd40;
            font-size: 2rem;
            filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.3));
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        /* Subtítulo */
        .top-search-container p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.2rem;
            margin-bottom: 0;
            font-weight: 400;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }

        /* Responsive Móvil */
        @media (max-width: 768px) {
            .top-search-section {
                padding: 20px 0;
            }
            
            .top-search-container h4 {
                font-size: 1.6rem;
            }
            
            .top-search-container h4 i {
                font-size: 1.6rem;
            }
            
            .top-search-container p {
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .top-search-section {
                padding: 15px 0;
            }
            
            .top-search-container h4 {
                font-size: 1.4rem;
                flex-direction: column;
                gap: 5px;
            }
            
            .top-search-container h4 i {
                font-size: 1.4rem;
            }
            
            .top-search-container p {
                font-size: 0.95rem;
                padding: 0 10px;
            }
        }

        /* Animación de entrada */
        .top-search-section {
            animation: fadeInDown 0.6s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>


    <!-- ===== HEADER (igual que dashboard) ===== -->
    <nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tlaloc">
            </a>
            
            <div class="d-flex align-items-center">

                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
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
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('cliente.dashboard') }}">Mi Cuenta</a>
                    </li>
                </ul>
                
                <div class="d-none d-lg-flex align-items-center">
                    @if(auth('cliente')->check())
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                {{ auth('cliente')->user()->nombre }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item active" href="{{ route('cliente.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Mi Cuenta
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                        </button>
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
                    @if(auth('cliente')->check())
                        <div class="d-grid gap-2">
                            <span class="btn btn-outline-primary w-100 mb-2 disabled">
                                <i class="fas fa-user me-2"></i>
                                {{ auth('cliente')->user()->nombre }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}">
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


    <!-- ===== BUSCADOR SUPERIOR ===== -->
    <section class="top-search-section">
        <div class="container">
            <div class="top-search-container">
                <h4>
                    <i class="fas fa-search me-2"></i>Mis Pedidos
                </h4>
                <p>Consulta el historial y estado de tus compras</p>
            </div>
        </div>
    </section>

    <!-- ===== CONTENIDO PRINCIPAL ===== -->
    <div class="pedidos-container">

        <!-- Page Header -->
        <div class="page-header">
            <div class="header-title">
                <div class="header-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="header-text">
                    <h1>Mis Pedidos</h1>
                    <p>{{ $pedidos->total() }} pedidos realizados</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('tienda') }}" class="btn-custom btn-primary-custom">
                    <i class="fas fa-store"></i> Seguir comprando
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filtros-section">
            <div class="filtros-tabs">
                <a href="{{ route('cliente.pedidos') }}" class="filtro-tab {{ !request('estado') ? 'active' : '' }}">Todos</a>
                <a href="{{ route('cliente.pedidos', ['estado' => 'pendiente']) }}" class="filtro-tab {{ request('estado') == 'pendiente' ? 'active' : '' }}">Pendientes</a>
                <a href="{{ route('cliente.pedidos', ['estado' => 'enviado']) }}" class="filtro-tab {{ request('estado') == 'enviado' ? 'active' : '' }}">Enviados</a>
                <a href="{{ route('cliente.pedidos', ['estado' => 'entregado']) }}" class="filtro-tab {{ request('estado') == 'entregado' ? 'active' : '' }}">Entregados</a>
                <a href="{{ route('cliente.pedidos', ['estado' => 'cancelado']) }}" class="filtro-tab {{ request('estado') == 'cancelado' ? 'active' : '' }}">Cancelados</a>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchFolio" placeholder="Buscar por folio...">
            </div>
        </div>

        @if($pedidos->count() > 0)
            <!-- Grid de pedidos -->
            <div class="pedidos-grid" id="pedidosGrid">
                @foreach($pedidos as $pedido)
                <a href="{{ route('cliente.pedido.ver', $pedido->id) }}" class="pedido-card">
                    <div class="pedido-header">
                        <span class="pedido-folio">#{{ $pedido->folio }}</span>
                        <span class="pedido-fecha">{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="pedido-body">
                        <div class="pedido-info-row">
                            <span class="pedido-info-label">Total:</span>
                            <span class="pedido-info-value">${{ number_format($pedido->total, 2) }}</span>
                        </div>
                        <div class="pedido-info-row">
                            <span class="pedido-info-label">Productos:</span>
                            <span class="pedido-info-value">{{ $pedido->items->count() }} artículos</span>
                        </div>
                        <div class="pedido-info-row">
                            <span class="pedido-info-label">Pago:</span>
                            <span class="pedido-info-value">
                                @if($pedido->pago_confirmado)
                                    <span class="text-success">✓ Confirmado</span>
                                @else
                                    <span class="text-warning">⏳ Pendiente</span>
                                @endif
                            </span>
                        </div>
                        <div class="pedido-total">
                            ${{ number_format($pedido->total, 2) }}
                        </div>
                    </div>
                    <div class="pedido-footer">
                        @php
                            $badgeClass = match($pedido->estado) {
                                'pendiente' => 'badge-pendiente',
                                'confirmado' => 'badge-confirmado', // ← AGREGAR ESTA LÍNEA
                                'enviado' => 'badge-enviado',
                                'entregado' => 'badge-entregado',
                                'cancelado' => 'badge-cancelado',
                                default => 'badge-pendiente'
                            };
                        @endphp
                        <span class="badge-estado {{ $badgeClass }}">
                            {{ ucfirst($pedido->estado) }}
                        </span>
                        <span class="btn-ver">Ver detalles <i class="fas fa-arrow-right ms-2"></i></span>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="pagination">
                {{ $pedidos->appends(request()->query())->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>No hay pedidos</h3>
                <p>Aún no has realizado ninguna compra. ¡Empieza a explorar nuestros productos!</p>
                <a href="{{ route('tienda') }}" class="btn-primary">
                    <i class="fas fa-store me-2"></i>Ir a la tienda
                </a>
            </div>
        @endif
    </div>

    <!-- ===== FOOTER ===== -->
    <footer class="main-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="footer-brand">
                        <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tlaloc">
                        <h5>Tanques Tlaloc</h5>
                        <p>Especialistas en ROTOMOLDEO</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="footer-links">
                        <h6>Enlaces Rápidos</h6>
                        <ul>
                            <li><a href="{{ route('tienda') }}">Tienda</a></li>
                            <li><a href="{{ route('cliente.pedidos') }}">Mis Pedidos</a></li>
                            <li><a href="{{ route('cliente.completar-perfil') }}">Mi Perfil</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="footer-links">
                        <h6>Ayuda</h6>
                        <ul>
                            <li><a href="{{ route('contacto') }}">Contacto</a></li>
                            <li><a href="#" onclick="contactarWhatsApp(event)">Soporte WhatsApp</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="small text-white-50 mb-0">
                        &copy; {{ date('Y') }} Tanques Tlaloc. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function contactarWhatsApp(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Contactar por WhatsApp',
                text: '¿En qué podemos ayudarte?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#25D366',
                confirmButtonText: '<i class="fab fa-whatsapp me-2"></i>Ir a WhatsApp',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('https://wa.me/5215540175803', '_blank');
                }
            });
        }

        // Búsqueda en tiempo real (simulada)
        document.getElementById('searchFolio')?.addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.pedido-card');
            
            cards.forEach(card => {
                const folio = card.querySelector('.pedido-folio').textContent.toLowerCase();
                if (folio.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>