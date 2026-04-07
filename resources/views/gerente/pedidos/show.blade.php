@php
    use Carbon\Carbon;
    
    // Estados siguientes permitidos según la regla de negocio (solo avanzar)
    $transicionesEstado = [
        'pendiente' => ['confirmado', 'cancelado'],
        'confirmado' => ['enviado', 'cancelado'],
        'enviado' => ['entregado', 'cancelado'],
        'entregado' => [],
        'cancelado' => []
    ];
    
    $estadoActual = $pedido->estado;
    $estadosSiguientes = $transicionesEstado[$estadoActual] ?? [];
    
    // Verificar si el usuario actual es responsable
    $esResponsableActual = ($responsable && $responsable->id == auth()->id());
    
    // Determinar si puede editar (solo responsable y no entregado/cancelado)
    $puedeEditar = $esResponsableActual && !in_array($estadoActual, ['entregado', 'cancelado']);
    
    // Determinar acciones permitidas según estado
    $accionesPermitidas = [
        'pendiente' => ['confirmar_pago', 'cancelar'],
        'confirmado' => ['confirmar_pago', 'desconfirmar_pago', 'cancelar'],
        'enviado' => ['cancelar'],
        'entregado' => [],
        'cancelado' => []
    ];
    
    $accionesDisponibles = $accionesPermitidas[$estadoActual] ?? [];
    
    // Botones de acción según estado
    $botonesAccion = [];
    if ($puedeEditar) {
        if ($estadoActual == 'pendiente') {
            $botonesAccion[] = ['accion' => 'confirmar', 'texto' => 'Confirmar Pedido', 'icono' => 'fa-check', 'clase' => 'btn-success-action'];
        } elseif ($estadoActual == 'confirmado') {
            $botonesAccion[] = ['accion' => 'enviar', 'texto' => 'Marcar como Enviado', 'icono' => 'fa-truck', 'clase' => 'btn-info-action'];
        } elseif ($estadoActual == 'enviado') {
            $botonesAccion[] = ['accion' => 'entregar', 'texto' => 'Marcar como Entregado', 'icono' => 'fa-box-open', 'clase' => 'btn-success-action'];
        }
        
        if (in_array('confirmar_pago', $accionesDisponibles) && !$pedido->pago_confirmado) {
            $botonesAccion[] = ['accion' => 'confirmar_pago', 'texto' => 'Confirmar Pago', 'icono' => 'fa-money-check', 'clase' => 'btn-warning-action'];
        }
        
        if (in_array('desconfirmar_pago', $accionesDisponibles) && $pedido->pago_confirmado) {
            $botonesAccion[] = ['accion' => 'desconfirmar_pago', 'texto' => 'Desconfirmar Pago', 'icono' => 'fa-times-circle', 'clase' => 'btn-info-action'];
        }
        
        if (in_array('cancelar', $accionesDisponibles)) {
            $botonesAccion[] = ['accion' => 'cancelar', 'texto' => 'Cancelar Pedido', 'icono' => 'fa-times', 'clase' => 'btn-danger-action', 'es_confirmacion' => true];
        }
    }
    
    // Botón para tomar control (solo si no es responsable y no está entregado/cancelado)
    $puedeTomarControl = !$esResponsableActual && !in_array($estadoActual, ['entregado', 'cancelado']);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido - Sucursal {{ session('sucursal_nombre') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
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
        
        .sucursal-badge {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-left: 10px;
        }
        
        .header-subtitle {
            font-size: 0.85rem;
            color: var(--gray);
            margin-top: 4px;
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
        
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, var(--primary-dark), #4a7a18);
            color: white;
        }
        
        .btn-secondary-custom {
            background: white;
            color: var(--gray);
            border: 1px solid var(--light-gray);
        }
        
        .btn-secondary-custom:hover {
            background: var(--light);
            color: var(--dark);
            border-color: var(--gray);
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
        
        .table-hover tbody tr:hover {
            background-color: rgba(127, 173, 57, 0.05);
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
        
        .timeline-item.pendiente { border-left: 3px solid #ffc107; }
        .timeline-item.confirmado { border-left: 3px solid #17a2b8; }
        .timeline-item.enviado { border-left: 3px solid #007bff; }
        .timeline-item.entregado { border-left: 3px solid #28a745; }
        .timeline-item.cancelado { border-left: 3px solid #dc3545; }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -18px;
            top: 12px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: white;
            border: 2px solid;
            z-index: 1;
        }
        
        .timeline-item.pendiente::before { border-color: #ffc107; }
        .timeline-item.confirmado::before { border-color: #17a2b8; }
        .timeline-item.enviado::before { border-color: #007bff; }
        .timeline-item.entregado::before { border-color: #28a745; }
        .timeline-item.cancelado::before { border-color: #dc3545; }
        
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
            justify-content: center;
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
        
        .product-code {
            background: var(--light);
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        .color-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid #ddd;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .btn-store {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-store:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(127, 173, 57, 0.2);
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
        
        .sucursal-info-badge {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .btn-action:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .alert-estado {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.85rem;
        }
        
        @media (max-width: 1200px) { .main-content { margin-left: 70px; padding: 12px; } }
        @media (max-width: 992px) { .header-bar { flex-direction: column; align-items: stretch; text-align: center; } .header-actions { justify-content: center; } }
        @media (max-width: 768px) { .main-content { margin-left: 60px; padding: 10px; } .header-title { font-size: 1.1rem; } }
        @media (max-width: 576px) { .main-content { margin-left: 0; padding: 8px; } .btn-action { width: 100%; justify-content: center; text-align: center; } }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .card, .info-box { animation: fadeIn 0.3s ease-out; }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--light-gray); border-radius: 3px; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 3px; }
        
        @media print {
            body { background: white !important; margin: 0 !important; padding: 20px !important; font-size: 12px !important; visibility: hidden; }
            .print-content { visibility: visible !important; position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; padding: 20px !important; margin: 0 !important; display: block !important; }
            .sidebar, .header-bar, .no-print, .btn-action, .btn-custom, .header-actions, .timeline, .actions-grid, .card-header .btn, .main-content > :not(.print-content) { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; min-height: auto !important; }
            table { page-break-inside: avoid; font-size: 10px !important; }
            h1, h2, h3, h4, h5, h6 { page-break-after: avoid; }
            @page { margin: 1cm; size: A4; }
        }
    </style>
</head>
<body>
    @include('gerente.layouts.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        <div class="header-bar no-print">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-shopping-cart me-2"></i>Detalle del Pedido
                    <span class="sucursal-badge">
                        <i class="fas fa-store"></i> {{ session('sucursal_nombre') }}
                    </span>
                </h1>
                <div class="header-subtitle">
                    Gerente: {{ auth()->user()->nombre ?? 'Gerente' }}
                    @if($esResponsableActual)
                    | <span class="badge-responsable">
                        <i class="fas fa-user-check"></i> Eres responsable
                    </span>
                    @endif
                </div>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('gerente.pedidos') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                @if($puedeEditar)
                <a href="{{ route('gerente.pedidos.editar', $pedido->id) }}" class="btn-custom btn-primary-custom">
                    <i class="fas fa-edit"></i> Editar
                </a>
                @endif
                <a href="{{ route('tienda') }}" target="_blank" class="btn-store">
                    <i class="fas fa-store me-1"></i> Ver Tienda
                </a>
                <button class="btn-custom btn-secondary-custom" onclick="imprimirPedido()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>

        <!-- Contenido para imprimir -->
        <div class="print-content" style="display: none;">
            <div style="text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #333;">
                <h2 style="color: #333; margin-bottom: 5px;">Tanques Tláloc</h2>
                <h4 style="color: #555; margin-bottom: 5px;">Pedido: {{ $pedido->folio }}</h4>
                <p style="font-size: 12px; color: #666; margin-bottom: 5px;">
                    Sucursal: {{ $pedido->sucursal->nombre ?? session('sucursal_nombre') }}
                    @if($responsable ?? false) | Responsable: {{ $responsable->nombre }} @endif
                </p>
                <p style="font-size: 11px; color: #666;">
                    Fecha: {{ Carbon::parse($pedido->fecha)->format('d/m/Y H:i') }} | 
                    Estado: {{ ucfirst($pedido->estado) }}
                    @if($pedido->pago_confirmado) | Pago Confirmado @else | Pago Pendiente @endif
                </p>
            </div>
            
            <div style="margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-radius: 5px;">
                <h5 style="color: #333; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Información del Cliente</h5>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr><td style="padding: 4px 8px; width: 120px; font-weight: bold;">Nombre:</td><td style="padding: 4px 8px;">{{ $pedido->cliente_nombre }}</td></tr>
                    <tr><td style="padding: 4px 8px; font-weight: bold;">Teléfono:</td><td style="padding: 4px 8px;">{{ $pedido->cliente_telefono }}</td></tr>
                </table>
            </div>
            
            <div style="margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-radius: 5px;">
                <h5 style="color: #333; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Dirección de Entrega</h5>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr><td style="padding: 4px 8px; width: 120px; font-weight: bold;">Dirección:</td><td style="padding: 4px 8px;">{{ $pedido->cliente_direccion }}</td></tr>
                    <tr><td style="padding: 4px 8px; font-weight: bold;">Ciudad:</td><td style="padding: 4px 8px;">{{ $pedido->cliente_ciudad }}</td></tr>
                    <tr><td style="padding: 4px 8px; font-weight: bold;">Estado:</td><td style="padding: 4px 8px;">{{ $pedido->cliente_estado }}</td></tr>
                    @if($pedido->codigo_postal)<tr><td style="padding: 4px 8px; font-weight: bold;">C.P.:</td><td style="padding: 4px 8px;">{{ $pedido->codigo_postal }}</td></tr>@endif
                </table>
            </div>
            
            <div style="margin-bottom: 15px;">
                <h5 style="color: #333; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Productos del Pedido</h5>
                <table style="width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 11px;">
                    <thead>
                        <tr style="background-color: #f5f5f5;">
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">#</th>
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">Producto</th>
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">Código</th>
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: center;">Cantidad</th>
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: right;">Precio</th>
                            <th style="border: 1px solid #ddd; padding: 6px; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items ?? $pedido->items as $index => $item)
                        @php
                            $productoInfo = $item->producto_id ? \App\Models\Producto::with(['color'])->find($item->producto_id) : null;
                            $codigo = $productoInfo ? $productoInfo->codigo : 'N/A';
                        @endphp
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 6px;">{{ $index + 1 }}</td>
                            <td style="border: 1px solid #ddd; padding: 6px;">{{ $item->producto_nombre }}</td>
                            <td style="border: 1px solid #ddd; padding: 6px;">{{ $codigo }}</td>
                            <td style="border: 1px solid #ddd; padding: 6px; text-align: center;">{{ $item->cantidad }}</td>
                            <td style="border: 1px solid #ddd; padding: 6px; text-align: right;">${{ number_format($item->precio, 2) }}</td>
                            <td style="border: 1px solid #ddd; padding: 6px; text-align: right;">${{ number_format($item->cantidad * $item->precio, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f9f9f9; font-weight: bold;">
                            <td colspan="3" style="border: 1px solid #ddd; padding: 8px; text-align: right;">Total:</td>
                            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $items ? $items->sum('cantidad') : 0 }}</td>
                            <td colspan="2" style="border: 1px solid #ddd; padding: 8px; text-align: right; font-size: 13px;">${{ number_format($pedido->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            @if($pedido->notas)
            <div style="margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-radius: 5px;">
                <h5 style="color: #333; margin-bottom: 8px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Notas del Pedido</h5>
                <p style="font-size: 12px; line-height: 1.4;">{{ nl2br(e($pedido->notas)) }}</p>
            </div>
            @endif
            
            <div style="text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 10px; color: #666;">
                Documento generado el {{ Carbon::now()->format('d/m/Y H:i:s') }} | 
                Tanques Tláloc - Sistema de Gestión de Pedidos
            </div>
        </div>

        <!-- Información de Sucursal y Responsable -->
        <div class="no-print mb-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <div class="sucursal-info-badge">
                    <i class="fas fa-store"></i>
                    Sucursal: {{ $pedido->sucursal->nombre ?? session('sucursal_nombre') }}
                    @if($pedido->distancia_km) | <i class="fas fa-route"></i> {{ $pedido->distancia_km }} km @endif
                    @if($pedido->cobertura_verificada) | <i class="fas fa-check-circle"></i> Cobertura verificada @endif
                </div>
                
                @if($responsable ?? false)
                <div class="badge-responsable">
                    <i class="fas fa-user"></i>
                    Responsable: {{ $responsable->nombre }}
                    @if(!$puedeEditar && $esResponsableActual && in_array($estadoActual, ['entregado', 'cancelado']))
                    <span class="ms-1"><i class="fas fa-lock"></i> Pedido finalizado</span>
                    @elseif(!$puedeEditar && $responsable->id != auth()->id())
                    <span class="ms-1"><i class="fas fa-lock"></i> No eres responsable</span>
                    @endif
                </div>
                @else
                <div class="badge bg-secondary"><i class="fas fa-user-slash"></i> Sin responsable asignado</div>
                @endif
            </div>
        </div>

        <!-- Encabezado del Pedido -->
        <div class="card no-print">
            <div class="header-pedido">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-2">Pedido: {{ $pedido->folio }}</h4>
                        <p class="mb-1">
                            <i class="fas fa-calendar me-1"></i>
                            {{ Carbon::parse($pedido->fecha)->format('d/m/Y H:i') }}
                            @if($pedido->fecha_confirmacion)
                            <span class="ms-3"><i class="fas fa-check-circle me-1"></i>Confirmado: {{ Carbon::parse($pedido->fecha_confirmacion)->format('d/m/Y H:i') }}</span>
                            @endif
                        </p>
                        <div class="pedido-responsable">
                            <i class="fas fa-store"></i> {{ $pedido->sucursal->nombre ?? session('sucursal_nombre') }}
                            @if($responsable ?? false) | <i class="fas fa-user"></i> {{ $responsable->nombre }} @endif
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end text-start mt-md-0 mt-3">
                        <h3 class="mb-2">${{ number_format($pedido->total, 2) }}</h3>
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <span class="badge-estado badge-{{ $pedido->estado }}">
                                <i class="fas fa-circle fa-xs"></i> {{ ucfirst($pedido->estado) }}
                            </span>
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
                            <div class="mb-3"><div class="info-label">Teléfono</div><div class="info-value"><a href="tel:{{ $pedido->cliente_telefono }}" class="text-decoration-none text-primary"><i class="fas fa-phone fa-sm me-1"></i>{{ $pedido->cliente_telefono }}</a></div></div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="info-box">
                            <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Dirección</h6>
                            <div class="mb-3"><div class="info-label">Dirección</div><div class="info-value">{{ $pedido->cliente_direccion }}</div></div>
                            <div class="row"><div class="col-6 mb-2"><div class="info-label">Ciudad</div><div class="info-value">{{ $pedido->cliente_ciudad }}</div></div>
                            <div class="col-6 mb-2"><div class="info-label">Estado</div><div class="info-value">{{ $pedido->cliente_estado }}</div></div></div>
                            @if($pedido->codigo_postal)<div class="mb-2"><div class="info-label">Código Postal</div><div class="info-value">{{ $pedido->codigo_postal }}</div></div>@endif
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-12 mb-3">
                        <div class="info-box">
                            <h6 class="mb-3"><i class="fas fa-truck me-2"></i>Entrega y Responsable</h6>
                            <div class="mb-3"><div class="info-label">Método de Pago</div><div class="info-value">{{ $metodos[$pedido->metodo_pago] ?? $pedido->metodo_pago }}</div></div>
                            <div class="mb-3"><div class="info-label">Responsable</div><div class="info-value">@if($responsable ?? false)<span class="badge-responsable"><i class="fas fa-user"></i> {{ $responsable->nombre }}</span>@else<span class="badge bg-secondary"><i class="fas fa-question-circle"></i> Sin asignar</span>@endif</div></div>
                            @if($pedido->distancia_km)<div class="mb-3"><div class="info-label">Distancia</div><div class="info-value">{{ $pedido->distancia_km }} km</div></div>@endif
                            @if($pedido->cobertura_verificada)<div class="mb-2"><span class="payment-badge payment-confirmed"><i class="fas fa-check-circle"></i> Cobertura verificada</span></div>@endif
                            @if($pedido->fecha_entrega)<div class="mb-2"><div class="info-label">Entrega Programada</div><div class="info-value">{{ Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') }}</div></div>@endif
                        </div>
                    </div>
                </div>
                
                @if($pedido->notas)
                <div class="info-box mt-3">
                    <h6 class="mb-3"><i class="fas fa-sticky-note me-2"></i>Notas</h6>
                    <p class="mb-0" style="font-size: 0.9rem;">{{ nl2br(e($pedido->notas)) }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Productos del Pedido -->
        <div class="card no-print">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-box"></i> Productos <span class="badge bg-primary ms-2">{{ $items ? $items->sum('cantidad') : 0 }} unidades</span></h5>
                <span class="badge bg-light text-dark">{{ $items ? $items->count() : 0 }} producto(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th width="40">#</th><th>Producto</th><th>Código</th><th class="text-center">Cantidad</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach($items ?? $pedido->items as $index => $item)
                            @php
                                $productoInfo = $item->producto_id ? \App\Models\Producto::find($item->producto_id) : null;
                                $codigo = $productoInfo ? $productoInfo->codigo : 'N/A';
                            @endphp
                            <tr><td class="fw-medium">{{ $index + 1 }}</td><td><div class="fw-semibold" style="font-size: 0.9rem;">{{ $item->producto_nombre }}</div></td><td><span class="product-code">{{ $codigo }}</span></td><td class="text-center"><span class="badge bg-primary">{{ $item->cantidad }}</span></td><td class="text-end">${{ number_format($item->precio, 2) }}</td><td class="text-end fw-bold">${{ number_format($item->cantidad * $item->precio, 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="total-resumen">
                    <div class="row">
                        <div class="col-md-6"><div class="mb-2"><div style="font-size: 0.9rem; color: var(--gray);">Total de Productos</div><div style="font-size: 1.1rem; font-weight: 600;">{{ $items ? $items->sum('cantidad') : 0 }} unidades</div></div></div>
                        <div class="col-md-6 text-md-end"><div class="mb-2"><div style="font-size: 0.9rem; color: var(--gray);">Total del Pedido</div><div style="font-size: 1.4rem; font-weight: 700; color: var(--primary);">${{ number_format($pedido->total, 2) }}</div></div></div>
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
                        <div class="timeline">
                            @foreach($historial as $registro)
                            <div class="timeline-item {{ $registro->accion == 'cancelacion' ? 'cancelado' : ($registro->accion == 'entrega' ? 'entregado' : '') }}">
                                <div class="d-flex justify-content-between flex-wrap">
                                    <div><h6 class="mb-1" style="font-size: 0.95rem;">{{ $registro->usuario->nombre ?? 'Sistema' }} <small class="text-muted ms-2">({{ $registro->usuario->rol ?? 'sistema' }})</small></h6><p class="text-muted mb-0" style="font-size: 0.85rem;"><span class="badge bg-secondary">{{ $registro->accion }}</span>@if($registro->detalles)<span class="ms-2">{{ $registro->detalles }}</span>@endif</p></div>
                                    <div class="text-end"><small class="text-muted">{{ Carbon::parse($registro->fecha)->format('d/m/Y H:i') }}</small></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center text-muted py-4"><i class="fas fa-info-circle me-2"></i>No hay historial registrado para este pedido.</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header"><h5 class="card-title"><i class="fas fa-cog"></i> Acciones</h5></div>
                    <div class="card-body">
                        <div class="actions-grid">
                            @if($puedeEditar)
                                @foreach($botonesAccion as $boton)
                                    @if(isset($boton['es_confirmacion']) && $boton['es_confirmacion'])
                                        <button onclick="confirmarAccion('{{ $boton['accion'] }}')" class="btn-action {{ $boton['clase'] }}">
                                            <i class="fas {{ $boton['icono'] }}"></i> {{ $boton['texto'] }}
                                        </button>
                                    @else
                                        <a href="{{ route('gerente.pedidos.procesar', ['accion' => $boton['accion'], 'id' => $pedido->id]) }}" class="btn-action {{ $boton['clase'] }}">
                                            <i class="fas {{ $boton['icono'] }}"></i> {{ $boton['texto'] }}
                                        </a>
                                    @endif
                                @endforeach
                            @elseif($esResponsableActual && in_array($estadoActual, ['entregado', 'cancelado']))
                                <div class="alert alert-secondary mb-0 text-center py-2">
                                    <i class="fas fa-check-circle me-2"></i> Pedido finalizado - No requiere acciones
                                </div>
                            @elseif($responsable && $responsable->id != auth()->id())
                                <div class="alert alert-warning mb-0 text-center py-2">
                                    <i class="fas fa-lock me-2"></i> No eres responsable de este pedido
                                </div>
                            @endif
                            
                            @if($puedeTomarControl)
                            <button onclick="tomarControl()" class="btn-action btn-warning-action">
                                <i class="fas fa-hand-paper"></i> Tomar Control
                            </button>
                            @endif
                            
                            @php
                            $whatsapp_msg = "Hola " . $pedido->cliente_nombre . ", te contacto por tu pedido " . $pedido->folio . " en Tanques Tláloc - " . session('sucursal_nombre');
                            $telefono_limpio = preg_replace('/[^0-9]/', '', $pedido->cliente_telefono);
                            $whatsapp_url = "https://wa.me/" . $telefono_limpio . "?text=" . urlencode($whatsapp_msg);
                            @endphp
                            
                            <a href="{{ $whatsapp_url }}" class="btn-action" style="background: linear-gradient(135deg, #25D366, #128C7E); color: white;" target="_blank" onclick="trackWhatsAppClick()">
                                <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
                            </a>
                            
                            <button onclick="imprimirPedido()" class="btn-action btn-outline-action"><i class="fas fa-print"></i> Imprimir</button>
                            <a href="{{ route('gerente.pedidos') }}" class="btn-action btn-outline-action"><i class="fas fa-list"></i> Ver Todos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let isPrinting = false;
        
        function imprimirPedido() {
            if (isPrinting) return;
            isPrinting = true;
            
            const originalDisplay = [];
            const noPrintElements = document.querySelectorAll('.no-print');
            noPrintElements.forEach((el, index) => { originalDisplay[index] = el.style.display; el.style.display = 'none'; });
            
            const printContent = document.querySelector('.print-content');
            const originalPrintDisplay = printContent.style.display;
            printContent.style.display = 'block';
            
            const originalTitle = document.title;
            document.title = "Pedido: {{ $pedido->folio }} - Sucursal {{ session('sucursal_nombre') }}";
            
            const restoreView = () => {
                noPrintElements.forEach((el, index) => { el.style.display = originalDisplay[index]; });
                printContent.style.display = originalPrintDisplay;
                document.title = originalTitle;
                isPrinting = false;
            };
            
            window.addEventListener('afterprint', restoreView, { once: true });
            setTimeout(() => { if (isPrinting) restoreView(); }, 1000);
            setTimeout(() => { window.print(); }, 50);
        }
        
        function confirmarAccion(accion) {
            let titulo = '', html = '', url = '';
            
            if (accion === 'cancelar') {
                titulo = '¿Cancelar Pedido?';
                html = `¿Seguro que deseas cancelar el pedido <strong>{{ $pedido->folio }}</strong>?<br><br><small class="text-danger">Esta acción marcará el pedido como cancelado y regresará el stock.</small>`;
                url = '{{ route("gerente.pedidos.procesar", ["accion" => "cancelar", "id" => $pedido->id]) }}';
            }
            
            Swal.fire({
                title: titulo,
                html: html,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
        }
        
        function tomarControl() {
            Swal.fire({
                title: '¿Tomar Control del Pedido?',
                html: `¿Deseas tomar control del pedido <strong>{{ $pedido->folio }}</strong>?<br><br><small>Esta acción te asignará como responsable.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, tomar control',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = '{{ route("gerente.pedidos.procesar", ["accion" => "tomar_control", "id" => $pedido->id]) }}';
            });
        }
        
        function trackWhatsAppClick() {
            Swal.fire({ title: 'Contactando al cliente', text: 'Se abrirá WhatsApp', icon: 'info', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            @if(!$pedido->pago_confirmado && $pedido->estado != 'cancelado')
            setTimeout(() => {
                Swal.fire({
                    title: 'Pago Pendiente',
                    html: `El pedido <strong>{{ $pedido->folio }}</strong> tiene el pago pendiente.`,
                    icon: 'warning',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Confirmar Pago',
                    showCancelButton: true,
                    cancelButtonText: 'Más Tarde'
                }).then((result) => {
                    if (result.isConfirmed) window.location.href = '{{ route("gerente.pedidos.procesar", ["accion" => "confirmar_pago", "id" => $pedido->id]) }}';
                });
            }, 1500);
            @endif
        });
    </script>

    @if(session('swal'))
    <script>
        Swal.fire({ icon: '{{ session('swal.type') }}', title: '{{ session('swal.title') }}', text: '{{ session('swal.message') }}', confirmButtonColor: '#7fad39' });
    </script>
    @endif
</body>
</html>