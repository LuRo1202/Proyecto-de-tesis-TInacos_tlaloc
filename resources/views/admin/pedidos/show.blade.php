@php
    use Carbon\Carbon;
    use App\Models\Sucursal;
    use App\Models\Usuario;
    
    // Estados siguientes permitidos
    $estadoActual = $pedido->estado;
    $estadosTexto = [
        'pendiente' => 'Pendiente',
        'confirmado' => 'Confirmado',
        'enviado' => 'Enviado',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado'
    ];
    
    // Acciones permitidas según estado
    $accionesPorEstado = [
        'pendiente' => ['confirmar', 'cancelar'],
        'confirmado' => ['enviar', 'cancelar'],
        'enviado' => ['entregar', 'cancelar'],
        'entregado' => [],
        'cancelado' => []
    ];
    
    $accionesPermitidas = $accionesPorEstado[$estadoActual] ?? [];
    
    // Mensaje de ayuda
    $mensajeAyuda = '';
    if ($estadoActual == 'pendiente') {
        $mensajeAyuda = 'Solo puedes avanzar a <strong>Confirmado</strong> o <strong>Cancelado</strong>';
    } elseif ($estadoActual == 'confirmado') {
        $mensajeAyuda = 'Solo puedes avanzar a <strong>Enviado</strong> o <strong>Cancelado</strong>';
    } elseif ($estadoActual == 'enviado') {
        $mensajeAyuda = 'Solo puedes avanzar a <strong>Entregado</strong> o <strong>Cancelado</strong>';
    } elseif ($estadoActual == 'entregado') {
        $mensajeAyuda = 'Este pedido ya fue entregado. No se puede modificar el estado.';
    } elseif ($estadoActual == 'cancelado') {
        $mensajeAyuda = 'Este pedido está cancelado. No se puede modificar el estado.';
    }
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido - Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        :root {
            --sidebar-width: 250px;
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
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
        }
        
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 15px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .header-bar {
            background: white;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .header-title {
            margin: 0;
            color: var(--dark);
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .header-title i {
            color: var(--primary);
        }
        
        .header-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn-custom {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        
        .btn-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }
        
        .btn-secondary-custom {
            background: white;
            color: var(--gray);
            border: 1px solid var(--light-gray);
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            margin-bottom: 15px;
            background: white;
        }
        
        .header-pedido {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }
        
        .info-box {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            border: 1px solid var(--light-gray);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .info-label {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 4px;
            font-weight: 500;
        }
        
        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .table {
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #eee;
            padding: 10px 12px;
            white-space: nowrap;
            background: #fafafa;
        }
        
        .table td {
            vertical-align: middle;
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }
        
        .color-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid #ddd;
        }
        
        .badge-estado {
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            min-width: 80px;
            justify-content: center;
        }
        
        .badge-pendiente { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .badge-confirmado { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .badge-enviado { background: #cce5ff; color: #004085; border: 1px solid #b8daff; }
        .badge-entregado { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .badge-cancelado { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .payment-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        
        .payment-confirmed {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .payment-pending {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
            border: 1px solid rgba(255, 193, 7, 0.2);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--light-gray);
            padding: 12px 15px;
            border-radius: 8px 8px 0 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-title i {
            color: var(--primary);
        }
        
        .product-code {
            background: var(--light);
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        .badge-responsable {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .pedido-responsable {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 5px;
        }
        
        .responsable-selector {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid var(--light-gray);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .selector-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .usuario-card {
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .usuario-card:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 6px rgba(127, 173, 57, 0.1);
        }
        
        .usuario-card.selected {
            border-color: var(--primary);
            background-color: rgba(127, 173, 57, 0.05);
        }
        
        .badge-rol {
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 12px;
        }
        
        .badge-vendedor {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .badge-gerente {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
        }
        
        .total-resumen {
            background: var(--light);
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border: 1px solid var(--light-gray);
        }
        
        .timeline {
            position: relative;
            padding-left: 25px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid var(--light-gray);
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -18px;
            top: 12px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--primary);
            z-index: 1;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }
        
        .btn-action {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }
        
        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary-action { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; }
        .btn-success-action { background: linear-gradient(135deg, var(--success), #218838); color: white; }
        .btn-info-action { background: linear-gradient(135deg, var(--info), #138496); color: white; }
        .btn-warning-action { background: linear-gradient(135deg, var(--warning), #e0a800); color: #000; }
        .btn-danger-action { background: linear-gradient(135deg, var(--danger), #c82333); color: white; }
        .btn-outline-action { background: white; color: var(--gray); border: 1px solid var(--light-gray); }
        .btn-outline-action:hover { background: var(--light); color: var(--dark); border-color: var(--gray); }
        
        .alert-info-custom {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 0.85rem;
        }
        
        @media (max-width: 1200px) { .main-content { margin-left: 70px; padding: 12px; } }
        @media (max-width: 992px) { .header-bar { flex-direction: column; align-items: stretch; text-align: center; } .header-actions { justify-content: center; } }
        @media (max-width: 768px) { .main-content { margin-left: 60px; padding: 10px; } .header-title { font-size: 1.1rem; } }
        @media (max-width: 576px) { .main-content { margin-left: 0; padding: 8px; } .btn-action { width: 100%; justify-content: center; text-align: center; } }
        
        @media print {
            body { background: white !important; margin: 0 !important; padding: 20px !important; visibility: hidden; }
            .print-content { visibility: visible !important; position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; padding: 20px !important; margin: 0 !important; display: block !important; }
            .sidebar, .header-bar, .no-print, .btn-action, .btn-custom, .header-actions, .timeline, .actions-grid, .card-header .btn, .main-content > :not(.print-content) { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; min-height: auto !important; }
            table { page-break-inside: avoid; font-size: 10px !important; }
            @page { margin: 1cm; size: A4; }
        }
    </style>
</head>
<body>
    @include('admin.layouts.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        <div class="header-bar no-print">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-shopping-cart me-2"></i>Detalle del Pedido
                </h1>
                <p class="text-muted mb-0 small">Bienvenido, {{ auth()->user()->nombre ?? 'Administrador' }}</p>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('admin.pedidos') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('admin.pedidos.editar', $pedido->id) }}" class="btn-custom btn-primary-custom">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button class="btn-custom btn-secondary-custom" onclick="imprimirPedido()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>

        <!-- Contenido para imprimir -->
        <div class="print-content" style="display: none;">
            <div style="text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #333;">
                <h2 style="color: #333;">Tanques Tláloc</h2>
                <h4>Pedido: {{ $pedido->folio }}</h4>
                <p>Sucursal: {{ $pedido->sucursal->nombre ?? 'N/A' }} | Responsable: {{ $responsable->nombre ?? 'N/A' }}</p>
                <p>Fecha: {{ Carbon::parse($pedido->fecha)->format('d/m/Y H:i') }} | Estado: {{ ucfirst($pedido->estado) }}</p>
            </div>
            
            <div style="margin-bottom: 15px;">
                <h5>Información del Cliente</h5>
                <p><strong>Nombre:</strong> {{ $pedido->cliente_nombre }}<br>
                <strong>Teléfono:</strong> {{ $pedido->cliente_telefono }}<br>
                <strong>Dirección:</strong> {{ $pedido->cliente_direccion }}<br>
                <strong>Ciudad:</strong> {{ $pedido->cliente_ciudad }}, {{ $pedido->cliente_estado }}<br>
                <strong>CP:</strong> {{ $pedido->codigo_postal }}</p>
            </div>
            
            <div style="margin-bottom: 15px;">
                <h5>Productos</h5>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background:#f5f5f5;"><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr><td>{{ $item->producto_nombre }}</td><td style="text-align:center">{{ $item->cantidad }}</td><td style="text-align:right">${{ number_format($item->precio, 2) }}</td><td style="text-align:right">${{ number_format($item->cantidad * $item->precio, 2) }}</td></tr>
                        @endforeach
                        <tr style="font-weight:bold;"><td colspan="3" style="text-align:right">Total:</td><td style="text-align:right">${{ number_format($pedido->total, 2) }}</td></tr>
                    </tbody>
                </table>
            </div>
            
            @if($pedido->notas)
            <div><h5>Notas</h5><p>{{ $pedido->notas }}</p></div>
            @endif
        </div>

        <!-- Mensaje de ayuda sobre estados permitidos -->
        @if($mensajeAyuda)
        <div class="alert-info-custom mb-3 no-print">
            <i class="fas fa-info-circle me-2"></i> {!! $mensajeAyuda !!}
        </div>
        @endif

        <!-- Encabezado del Pedido -->
        <div class="card no-print">
            <div class="header-pedido">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-2">Pedido: {{ $pedido->folio }}</h4>
                        <p class="mb-0"><i class="fas fa-calendar me-1"></i> {{ Carbon::parse($pedido->fecha)->format('d/m/Y H:i') }}</p>
                        @if($pedido->sucursal || $responsable)
                        <div class="pedido-responsable">
                            @if($pedido->sucursal)<i class="fas fa-store"></i> {{ $pedido->sucursal->nombre }}@endif
                            @if($responsable)@if($pedido->sucursal) <span class="mx-1">|</span> @endif<i class="fas fa-user"></i> {{ $responsable->nombre }}@endif
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h3 class="mb-2">${{ number_format($pedido->total, 2) }}</h3>
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <span class="badge-estado badge-{{ $pedido->estado }}"><i class="fas fa-circle fa-xs"></i> {{ ucfirst($pedido->estado) }}</span>
                            @if($pedido->pago_confirmado)
                                <span class="payment-badge payment-confirmed"><i class="fas fa-check-circle"></i> Pago Confirmado</span>
                            @else
                                <span class="payment-badge payment-pending"><i class="fas fa-clock"></i> Pago Pendiente</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="info-box">
                            <h6 class="mb-3"><i class="fas fa-user me-2"></i>Cliente</h6>
                            <div class="mb-3"><div class="info-label">Nombre</div><div class="info-value">{{ $pedido->cliente_nombre }}</div></div>
                            <div class="mb-3"><div class="info-label">Teléfono</div><div class="info-value"><a href="tel:{{ $pedido->cliente_telefono }}" class="text-primary"><i class="fas fa-phone fa-sm me-1"></i>{{ $pedido->cliente_telefono }}</a></div></div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="info-box">
                            <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Dirección</h6>
                            <div class="mb-3"><div class="info-label">Dirección</div><div class="info-value">{{ $pedido->cliente_direccion }}</div></div>
                            <div class="row"><div class="col-6"><div class="info-label">Ciudad</div><div class="info-value">{{ $pedido->cliente_ciudad }}</div></div>
                            <div class="col-6"><div class="info-label">Estado</div><div class="info-value">{{ $pedido->cliente_estado }}</div></div></div>
                            @if($pedido->codigo_postal)<div class="mt-2"><div class="info-label">CP</div><div class="info-value">{{ $pedido->codigo_postal }}</div></div>@endif
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-12 mb-3">
                        <div class="info-box">
                            <h6 class="mb-3"><i class="fas fa-truck me-2"></i>Entrega</h6>
                            <div class="mb-3"><div class="info-label">Método de Pago</div><div class="info-value">{{ $metodos[$pedido->metodo_pago] ?? $pedido->metodo_pago }}</div></div>
                            <div class="mb-3"><div class="info-label">Responsable</div><div class="info-value">@if($responsable)<span class="badge-responsable"><i class="fas fa-user"></i> {{ $responsable->nombre }}</span>@else<span class="badge bg-secondary">Sin asignar</span>@endif</div></div>
                            @if($pedido->distancia_km)<div class="mb-3"><div class="info-label">Distancia</div><div class="info-value">{{ $pedido->distancia_km }} km</div></div>@endif
                            @if($pedido->fecha_entrega)<div><div class="info-label">Entrega Programada</div><div class="info-value">{{ Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') }}</div></div>@endif
                        </div>
                    </div>
                </div>
                
                @if($pedido->notas)
                <div class="info-box mt-3">
                    <h6 class="mb-3"><i class="fas fa-sticky-note me-2"></i>Notas</h6>
                    <p class="mb-0">{{ nl2br(e($pedido->notas)) }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Selector de Responsable -->
        @if($pedido->sucursal)
        <div class="responsable-selector no-print">
            <div class="selector-header">
                <h6 class="mb-0"><i class="fas fa-user-plus me-2"></i>Asignar Responsable</h6>
                <span class="badge bg-info"><i class="fas fa-store me-1"></i>{{ $pedido->sucursal->nombre }}</span>
            </div>
            
            @if(count($usuarios_sucursal) > 0)
            <p class="text-muted small mb-3">Selecciona un vendedor o gerente de la sucursal <strong>{{ $pedido->sucursal->nombre }}</strong> para asignar como responsable.</p>
            
            <div class="row" id="usuariosContainer">
                @foreach($usuarios_sucursal as $usuario)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="usuario-card" data-user-id="{{ $usuario->id }}" data-user-name="{{ $usuario->nombre }}" data-user-rol="{{ $usuario->rol }}" onclick="seleccionarUsuario(this)" id="usuario-{{ $usuario->id }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div><h6 class="mb-1">{{ $usuario->nombre }}</h6><small class="text-muted">{{ $usuario->usuario }}</small></div>
                            <span class="badge-rol badge-{{ $usuario->rol }}">{{ ucfirst($usuario->rol) }}</span>
                        </div>
                        <div class="small text-muted"><i class="fas fa-envelope fa-xs me-1"></i>{{ $usuario->email }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="d-flex gap-2">
                <button class="btn btn-primary flex-grow-1" onclick="asignarResponsable()" id="btnAsignar" disabled><i class="fas fa-user-check me-2"></i>Asignar como Responsable</button>
                @if($responsable)<button class="btn btn-outline-danger" onclick="removerResponsable()" id="btnRemover"><i class="fas fa-user-times me-2"></i>Remover</button>@endif
            </div>
            @else
            <div class="alert alert-warning mb-0">No hay vendedores ni gerentes asignados a esta sucursal.</div>
            @endif
        </div>
        @endif

        <!-- Productos -->
        <div class="card no-print">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-box"></i> Productos <span class="badge bg-primary ms-2">{{ $pedido->total_items }} unidades</span></h5>
                <span class="badge bg-light text-dark">{{ $items->count() }} producto(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>#</th><th>Producto</th><th>Código</th><th>Color</th><th>Capacidad</th><th class="text-center">Cantidad</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr></thead>
                        <tbody>
                            @foreach($items as $index => $item)
                            @php
                                $productoInfo = $item->producto_id ? \App\Models\Producto::with(['color'])->find($item->producto_id) : null;
                                $colorNombre = $productoInfo && $productoInfo->color ? $productoInfo->color->nombre : null;
                                $colorHex = $productoInfo && $productoInfo->color ? $productoInfo->color->codigo_hex : '#ccc';
                                $codigo = $productoInfo ? $productoInfo->codigo : ($item->codigo ?? 'N/A');
                                $litros = $productoInfo ? $productoInfo->litros : ($item->litros ?? null);
                            @endphp
                            <tr>
                                <td class="fw-medium">{{ $index + 1 }}</td>
                                <td><div class="fw-semibold">{{ $item->producto_nombre }}</div></td>
                                <td><span class="product-code">{{ $codigo }}</span></td>
                                <td>@if($colorNombre)<div class="d-flex align-items-center gap-2"><span class="color-dot" style="background-color: {{ $colorHex }};"></span><span>{{ $colorNombre }}</span></div>@else<span class="text-muted">—</span>@endif</td>
                                <td>@if($litros)<span class="badge bg-light text-dark">{{ $litros }} L</span>@else<span class="text-muted">—</span>@endif</td>
                                <td class="text-center"><span class="badge bg-primary">{{ $item->cantidad }}</span></td>
                                <td class="text-end">${{ number_format($item->precio, 2) }}</td>
                                <td class="text-end fw-bold">${{ number_format($item->cantidad * $item->precio, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="total-resumen">
                    <div class="row">
                        <div class="col-md-6"><div><div class="text-muted small">Total de Productos</div><div class="fw-bold fs-5">{{ $pedido->total_items }} unidades</div></div></div>
                        <div class="col-md-6 text-md-end"><div><div class="text-muted small">Total del Pedido</div><div class="fw-bold fs-3 text-primary">${{ number_format($pedido->total, 2) }}</div></div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial y Acciones -->
        <div class="row no-print">
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header"><h5 class="card-title"><i class="fas fa-history"></i> Historial</h5></div>
                    <div class="card-body">
                        @if($historial->count() > 0)
                            @foreach($historial as $registro)
                            <div class="timeline-item mb-3">
                                <div class="d-flex justify-content-between">
                                    <div><strong>{{ $registro->usuario_nombre }}</strong> <small class="text-muted">({{ $registro->usuario_rol }})</small></div>
                                    <small class="text-muted">{{ Carbon::parse($registro->fecha)->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="mt-1"><span class="badge bg-secondary">{{ $registro->accion }}</span> @if($registro->detalles)<span class="ms-2">{{ $registro->detalles }}</span>@endif</div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">No hay historial registrado</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header"><h5 class="card-title"><i class="fas fa-cog"></i> Acciones</h5></div>
                    <div class="card-body">
                        <div class="actions-grid">
                            <a href="{{ route('admin.pedidos.editar', $pedido->id) }}" class="btn-action btn-primary-action"><i class="fas fa-edit"></i> Editar Pedido</a>
                            
                            @if(in_array('confirmar', $accionesPermitidas))
                            <a href="{{ route('admin.pedidos.procesar', ['accion' => 'confirmar', 'id' => $pedido->id]) }}" class="btn-action btn-success-action"><i class="fas fa-check"></i> Confirmar Pedido</a>
                            @endif
                            
                            @if(in_array('enviar', $accionesPermitidas))
                            <a href="{{ route('admin.pedidos.procesar', ['accion' => 'enviar', 'id' => $pedido->id]) }}" class="btn-action btn-info-action"><i class="fas fa-truck"></i> Marcar como Enviado</a>
                            @endif
                            
                            @if(in_array('entregar', $accionesPermitidas))
                            <a href="{{ route('admin.pedidos.procesar', ['accion' => 'entregar', 'id' => $pedido->id]) }}" class="btn-action btn-success-action"><i class="fas fa-box-open"></i> Marcar como Entregado</a>
                            @endif
                            
                            @if(in_array('cancelar', $accionesPermitidas))
                            <button onclick="confirmarCancelacion()" class="btn-action btn-danger-action"><i class="fas fa-times"></i> Cancelar Pedido</button>
                            @endif
                            
                            @if(!$pedido->pago_confirmado && !in_array($estadoActual, ['entregado', 'cancelado']))
                            <a href="{{ route('admin.pedidos.procesar', ['accion' => 'confirmar_pago', 'id' => $pedido->id]) }}" class="btn-action btn-warning-action"><i class="fas fa-money-check"></i> Confirmar Pago</a>
                            @endif
                            
                            @php
                                $whatsapp_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $pedido->cliente_telefono) . "?text=" . urlencode("Hola " . $pedido->cliente_nombre . ", te contacto por tu pedido " . $pedido->folio . " en Tanques Tláloc.");
                            @endphp
                            
                            <a href="{{ $whatsapp_url }}" target="_blank" class="btn-action" style="background: linear-gradient(135deg, #25D366, #128C7E); color: white;"><i class="fab fa-whatsapp"></i> Contactar por WhatsApp</a>
                            
                            <button onclick="imprimirPedido()" class="btn-action btn-outline-action"><i class="fas fa-print"></i> Imprimir</button>
                            <a href="{{ route('admin.pedidos') }}" class="btn-action btn-outline-action"><i class="fas fa-list"></i> Ver Todos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('swal_pedido'))
    <script>
        Swal.fire({
            icon: '{{ session('swal_pedido')['type'] }}',
            title: '{{ session('swal_pedido')['title'] }}',
            text: '{{ session('swal_pedido')['message'] }}',
            confirmButtonColor: '#7fad39'
        });
    </script>
    @endif

    <script>
        let usuarioSeleccionado = null;
        let usuarioSeleccionadoNombre = null;
        let usuarioSeleccionadoRol = null;
        
        function seleccionarUsuario(element) {
            document.querySelectorAll('.usuario-card').forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            usuarioSeleccionado = element.getAttribute('data-user-id');
            usuarioSeleccionadoNombre = element.getAttribute('data-user-name');
            usuarioSeleccionadoRol = element.getAttribute('data-user-rol');
            document.getElementById('btnAsignar').disabled = false;
        }
        
        function asignarResponsable() {
            if (!usuarioSeleccionado) {
                Swal.fire({ title: 'Selecciona un usuario', text: 'Por favor selecciona un usuario para asignar como responsable.', icon: 'warning', confirmButtonColor: '#7fad39' });
                return;
            }
            
            Swal.fire({
                title: '¿Asignar Responsable?',
                html: `¿Deseas asignar a <strong>${usuarioSeleccionadoNombre}</strong> como responsable?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7fad39',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, asignar',
                cancelButtonText: 'Cancelar',
                showLoaderOnConfirm: true,
                preConfirm: () => fetch('{{ route("admin.pedidos.asignar-responsable") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({ pedido_id: {{ $pedido->id }}, usuario_id: usuarioSeleccionado })
                }).then(res => res.json()).then(data => { if (data.error) throw new Error(data.error); return data; })
            }).then(result => { if (result.isConfirmed) { Swal.fire({ title: '¡Asignado!', text: 'Responsable asignado correctamente.', icon: 'success', confirmButtonColor: '#7fad39' }).then(() => location.reload()); } });
        }
        
        function removerResponsable() {
            Swal.fire({
                title: '¿Remover Responsable?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, remover',
                showLoaderOnConfirm: true,
                preConfirm: () => fetch('{{ route("admin.pedidos.remover-responsable") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({ pedido_id: {{ $pedido->id }} })
                }).then(res => res.json())
            }).then(result => { if (result.isConfirmed) { Swal.fire({ title: '¡Removido!', text: 'Responsable removido.', icon: 'success', confirmButtonColor: '#7fad39' }).then(() => location.reload()); } });
        }
        
        function confirmarCancelacion() {
            Swal.fire({
                title: '¿Cancelar Pedido?',
                html: `¿Seguro que deseas cancelar el pedido <strong>{{ $pedido->folio }}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No, mantener'
            }).then(result => { if (result.isConfirmed) window.location.href = '{{ route("admin.pedidos.procesar", ["accion" => "cancelar", "id" => $pedido->id]) }}'; });
        }
        
        let isPrinting = false;
        function imprimirPedido() {
            if (isPrinting) return;
            isPrinting = true;
            const noPrint = document.querySelectorAll('.no-print');
            const originalDisplay = [];
            noPrint.forEach((el, i) => { originalDisplay[i] = el.style.display; el.style.display = 'none'; });
            const printContent = document.querySelector('.print-content');
            const originalPrint = printContent.style.display;
            printContent.style.display = 'block';
            const originalTitle = document.title;
            document.title = "Pedido: {{ $pedido->folio }}";
            const restore = () => { noPrint.forEach((el, i) => { el.style.display = originalDisplay[i]; }); printContent.style.display = originalPrint; document.title = originalTitle; isPrinting = false; };
            window.addEventListener('afterprint', restore, { once: true });
            setTimeout(() => { if (isPrinting) restore(); }, 1000);
            setTimeout(() => window.print(), 50);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            @if($responsable && count($usuarios_sucursal) > 0)
            const responsableCard = document.getElementById('usuario-{{ $responsable->id }}');
            if (responsableCard) { responsableCard.classList.add('selected'); usuarioSeleccionado = '{{ $responsable->id }}'; usuarioSeleccionadoNombre = '{{ $responsable->nombre }}'; usuarioSeleccionadoRol = '{{ $responsable->rol }}'; }
            @endif
        });
    </script>
</body>
</html>