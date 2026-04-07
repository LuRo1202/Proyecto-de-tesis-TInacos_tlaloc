{{-- resources/views/cliente/pedido-detalle.blade.php --}}
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
    <title>Pedido #{{ $pedido->folio }} | Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
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
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --gris-fondo: #f5f5f5;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--gris-fondo);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            padding: 30px 0;
        }

        .detalle-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border-left: 5px solid var(--primary);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
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
            color: var(--dark);
            margin-bottom: 5px;
        }

        .header-text p {
            color: var(--gray);
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn-custom {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, var(--primary-dark), #4a7a18);
        }

        .btn-secondary-custom {
            background: white;
            color: var(--dark);
            border: 1px solid var(--light-gray);
        }

        .btn-secondary-custom:hover {
            background: var(--light);
        }

        .btn-danger-custom {
            background: linear-gradient(135deg, var(--danger), #c82333);
            color: white;
        }

        .estado-principal {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .estado-pendiente { 
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            color: #856404; 
            border-left: 3px solid #ffc107;
        }
        .estado-confirmado { 
            background: linear-gradient(135deg, #d1ecf1 0%, #b6e4f0 100%);
            color: #0c5460; 
            border-left: 3px solid #17a2b8;
        }
        .estado-enviado { 
            background: linear-gradient(135deg, #cce5ff 0%, #b8daff 100%);
            color: #004085; 
            border-left: 3px solid #007bff;
        }
        .estado-entregado { 
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724; 
            border-left: 3px solid #28a745;
        }
        .estado-cancelado { 
            background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%);
            color: #721c24; 
            border-left: 3px solid #dc3545;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid var(--light-gray);
        }

        .info-card-title {
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-card-title i {
            color: var(--primary);
        }

        .info-card-content {
            font-weight: 600;
            color: var(--dark);
        }

        .info-card-content small {
            font-weight: normal;
            color: var(--gray);
            font-size: 0.85rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
            margin-bottom: 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 24px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, var(--primary-light), var(--primary));
        }

        .timeline-item {
            position: relative;
            padding-left: 70px;
            margin-bottom: 25px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-icon {
            position: absolute;
            left: 15px;
            top: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: white;
            border: 2px solid;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            font-size: 0.8rem;
        }

        .timeline-icon.pendiente { border-color: #ffc107; color: #856404; }
        .timeline-icon.confirmado { border-color: #17a2b8; color: #138496; }
        .timeline-icon.enviado { border-color: var(--primary); color: var(--primary); }
        .timeline-icon.entregado { border-color: var(--success); color: var(--success); }
        .timeline-icon.cancelado { border-color: var(--danger); color: var(--danger); }
        .timeline-icon.completed { background: currentColor; color: white; }

        .timeline-content {
            background: var(--light);
            border-radius: 12px;
            padding: 15px;
        }

        .timeline-date {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 5px;
        }

        .timeline-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .timeline-desc {
            font-size: 0.9rem;
            color: var(--gray);
        }

        .table-responsive {
            border-radius: 12px;
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .table {
            font-size: 0.95rem;
            margin-bottom: 0;
            min-width: 700px;
        }

        .table th {
            font-weight: 600;
            color: var(--gray);
            border-bottom: 2px solid var(--light-gray);
            padding: 15px;
            background: var(--light);
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid var(--light-gray);
        }

        .producto-imagen {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .producto-badge {
            background: var(--light);
            padding: 2px 8px;
            border-radius: 20px;
            border: 1px solid var(--light-gray);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .color-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid #ddd;
        }

        .totales-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--light-gray);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed var(--light-gray);
        }

        .total-row:last-child {
            border-bottom: none;
        }

        .total-row.final {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            padding-top: 15px;
        }

        .acciones-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 20px 0 30px;
        }

        .top-search-section {
            background: linear-gradient(135deg, #7fad39 0%, #5d8c29 100%);
            padding: 25px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
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
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .top-search-container p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.2rem;
            margin-bottom: 0;
            font-weight: 400;
        }

        @media (max-width: 768px) {
            .detalle-container { padding: 0 15px; }
            .page-header { flex-direction: column; align-items: stretch; }
            .header-actions { justify-content: center; }
            .header-title { flex-direction: column; text-align: center; }
            .timeline::before { left: 20px; }
            .timeline-item { padding-left: 55px; }
            .timeline-icon { left: 12px; width: 24px; height: 24px; font-size: 0.7rem; }
            .acciones-container { flex-direction: column; }
            .btn-custom { width: 100%; justify-content: center; }
            .top-search-container h4 { font-size: 1.6rem; }
            .top-search-container h4 i { font-size: 1.6rem; }
            .top-search-container p { font-size: 1rem; }
        }
    </style>
</head>
<body>

    <!-- ===== HEADER ===== -->
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('tienda') }}">Tienda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('cliente.dashboard') }}">Mi Cuenta</a></li>
                </ul>
                
                <div class="d-none d-lg-flex align-items-center">
                    @if(auth('cliente')->check())
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                {{ auth('cliente')->user()->nombre }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item active" href="{{ route('cliente.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Mi Cuenta</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-3"><i class="fas fa-user me-2"></i>Login</a>
                    @endauth
                    
                    <a href="{{ route('carrito') }}" class="btn btn-primary position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge">{{ $cartCount ?? 0 }}</span>
                    </a>
                </div>
                
                <div class="d-lg-none mt-3">
                    @if(auth('cliente')->check())
                        <div class="d-grid gap-2">
                            <span class="btn btn-outline-primary w-100 mb-2 disabled"><i class="fas fa-user me-2"></i>{{ auth('cliente')->user()->nombre }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-2"><i class="fas fa-user me-2"></i>Iniciar Sesión</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== BUSCADOR SUPERIOR ===== -->
    <section class="top-search-section">
        <div class="container">
            <div class="top-search-container">
                <h4><i class="fas fa-box me-2"></i>Detalle del Pedido</h4>
                <p>Información completa y seguimiento</p>
            </div>
        </div>
    </section>

    <!-- ===== CONTENIDO PRINCIPAL ===== -->
    <main class="main-content">
        <div class="detalle-container">

            <!-- Header -->
            <div class="page-header">
                <div class="header-title">
                    <div class="header-icon"><i class="fas fa-box"></i></div>
                    <div class="header-text">
                        <h1>Pedido #{{ $pedido->folio }}</h1>
                        <p>{{ \Carbon\Carbon::parse($pedido->fecha ?? $pedido->created_at)->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('cliente.pedidos') }}" class="btn-custom btn-secondary-custom"><i class="fas fa-arrow-left"></i> Volver</a>
                    <a href="{{ route('tienda') }}" class="btn-custom btn-primary-custom"><i class="fas fa-store"></i> Tienda</a>
                </div>
            </div>

            <!-- Estado principal -->
            @php
                $estado = $pedido->estado;
                $estadoClass = match($estado) {
                    'pendiente' => 'estado-pendiente',
                    'confirmado' => 'estado-confirmado',
                    'enviado' => 'estado-enviado',
                    'entregado' => 'estado-entregado',
                    'cancelado' => 'estado-cancelado',
                    default => 'estado-pendiente'
                };
                $icono = match($estado) {
                    'pendiente' => 'fa-clock',
                    'confirmado' => 'fa-check-circle',
                    'enviado' => 'fa-truck',
                    'entregado' => 'fa-check-double',
                    'cancelado' => 'fa-times-circle',
                    default => 'fa-clock'
                };
            @endphp

            <div class="estado-principal {{ $estadoClass }}">
                <i class="fas {{ $icono }}"></i>
                <span>Estado: <strong>{{ ucfirst($estado) }}</strong></span>
            </div>

            <!-- Información del pedido -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-card-title"><i class="fas fa-map-marker-alt"></i> Dirección de envío</div>
                    <div class="info-card-content">
                        {{ $pedido->cliente_direccion }}<br>
                        <small>{{ $pedido->cliente_ciudad }}, {{ $pedido->cliente_estado }} - C.P. {{ $pedido->codigo_postal }}</small>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-title"><i class="fas fa-store"></i> Sucursal</div>
                    <div class="info-card-content">
                        {{ $pedido->sucursal->nombre ?? 'No asignada' }}<br>
                        <small>{{ $pedido->sucursal->direccion ?? '' }}</small>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-title"><i class="fas fa-credit-card"></i> Método de pago</div>
                    <div class="info-card-content">
                        {{ ucfirst(str_replace('_', ' ', $pedido->metodo_pago ?? 'manual')) }}
                        @if($pedido->pago_confirmado)
                            <br><small class="text-success">✓ Pago confirmado</small>
                        @else
                            <br><small class="text-warning">⏳ Pendiente de pago</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline de seguimiento -->
            @if(isset($historial) && $historial->count() > 0)
            <div class="info-card">
                <div class="info-card-title"><i class="fas fa-history"></i> Seguimiento del pedido</div>
                <div class="timeline">
                    @foreach($historial as $evento)
                        @php
                            $timelineClass = match($evento->accion) {
                                'pendiente', 'creado' => 'pendiente',
                                'confirmado' => 'confirmado',
                                'enviado' => 'enviado',
                                'entregado' => 'entregado',
                                'cancelado' => 'cancelado',
                                default => 'pendiente'
                            };
                            $timelineIcono = match($evento->accion) {
                                'pendiente', 'creado' => 'fa-clock',
                                'confirmado' => 'fa-check-circle',
                                'enviado' => 'fa-truck',
                                'entregado' => 'fa-check-double',
                                'cancelado' => 'fa-times-circle',
                                default => 'fa-clock'
                            };
                        @endphp
                        <div class="timeline-item">
                            <div class="timeline-icon {{ $timelineClass }}">
                                <i class="fas {{ $timelineIcono }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-date">{{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y H:i') }}</div>
                                <div class="timeline-title">{{ ucfirst($evento->accion) }}</div>
                                @if($evento->detalles)<div class="timeline-desc">{{ $evento->detalles }}</div>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Productos -->
            <div class="info-card">
                <div class="info-card-title"><i class="fas fa-boxes"></i> Productos</div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>Producto</th><th>Código</th><th class="text-center">Cantidad</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach($pedido->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="producto-imagen"><i class="fas fa-box"></i></div>
                                        <div class="fw-bold">{{ $item->producto_nombre }}</div>
                                    </div>
                                </td>
                                <td><span class="producto-badge"><i class="fas fa-barcode fa-xs me-1"></i>{{ $item->producto_id ? \App\Models\Producto::find($item->producto_id)?->codigo ?? 'N/A' : 'N/A' }}</span></td>
                                <td class="text-center"><strong>{{ $item->cantidad }}</strong></td>
                                <td class="text-end">${{ number_format($item->precio, 2) }}</td>
                                <td class="text-end fw-bold">${{ number_format($item->cantidad * $item->precio, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="totales-card">
                    <div class="total-row"><span>Subtotal:</span><span class="fw-bold">${{ number_format($pedido->total, 2) }}</span></div>
                    <div class="total-row"><span>Envío:</span><span class="fw-bold text-success">Gratis</span></div>
                    <div class="total-row final"><span>Total:</span><span>${{ number_format($pedido->total, 2) }}</span></div>
                </div>
            </div>

            <!-- Notas -->
            @if($pedido->notas)
            <div class="info-card">
                <div class="info-card-title"><i class="fas fa-sticky-note"></i> Notas del pedido</div>
                <div class="info-card-content">{{ $pedido->notas }}</div>
            </div>
            @endif

            <!-- Acciones -->
            <div class="acciones-container">
                @if($pedido->estado === 'pendiente')
                    <button onclick="cancelarPedido({{ $pedido->id }}, '{{ $pedido->folio }}')" class="btn-custom btn-danger-custom">
                        <i class="fas fa-times"></i> Cancelar pedido
                    </button>
                @endif
                <button onclick="window.print()" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </main>

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
                    <p class="small text-white-50 mb-0">&copy; {{ date('Y') }} Tanques Tlaloc. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">@csrf</form>

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

        function cancelarPedido(id, folio) {
            Swal.fire({
                title: '¿Cancelar pedido?',
                text: `Estás a punto de cancelar el pedido #${folio}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No, mantener',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Cancelando pedido',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    fetch('/cliente/pedido/' + id + '/cancelar', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Cancelado!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => { location.reload(); });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'No se pudo cancelar el pedido'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al cancelar el pedido. Intenta de nuevo.'
                        });
                    });
                }
            });
        }

        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}'
        });
        @endif
    </script>
</body>
</html>