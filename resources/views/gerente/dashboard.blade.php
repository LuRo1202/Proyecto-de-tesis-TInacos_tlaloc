@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gerente - Tanques Tláloc</title>
    
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
        
        /* Header Compacto */
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
        
        .header-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        /* Botones Compactos */
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
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--success), #218838);
            color: white;
        }
        
        /* Estadísticas Compactas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
            text-align: center;
            border-top: 3px solid var(--primary);
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            line-height: 1;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .stat-pendiente { border-top-color: #ffc107; }
        
        /* Contenido Principal */
        .content-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            margin-bottom: 15px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--light-gray);
            font-weight: 600;
            padding: 12px 15px;
            border-radius: 8px 8px 0 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        /* Badges */
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
        
        .badge-pendiente { 
            background: #fff3cd; 
            color: #856404; 
            border: 1px solid #ffeaa7;
        }
        .badge-confirmado { 
            background: #d1ecf1; 
            color: #0c5460; 
            border: 1px solid #bee5eb;
        }
        .badge-enviado { 
            background: #cce5ff; 
            color: #004085; 
            border: 1px solid #b8daff;
        }
        .badge-entregado { 
            background: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb;
        }
        .badge-cancelado { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
        }
        
        /* Tabla */
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
        }
        
        .table td {
            vertical-align: middle;
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(127, 173, 57, 0.05);
        }
        
        /* Acciones */
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-action {
            width: 30px;
            height: 30px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.2s ease;
            color: white;
            font-size: 0.75rem;
            cursor: pointer;
        }
        
        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }
        
        .btn-view {
            background: linear-gradient(135deg, var(--info), #138496);
        }
        
        /* Productos Top */
        .product-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            border-radius: 6px;
            margin-bottom: 6px;
            background: var(--light);
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        
        .product-item:hover {
            background: white;
            border-color: var(--primary-light);
            transform: translateX(2px);
        }
        
        .product-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        .product-sales {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.8rem;
            min-width: 40px;
            text-align: center;
        }
        
        /* Vendedores */
        .vendedor-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 6px;
            background: var(--light);
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }
        
        .vendedor-item:hover {
            background: white;
            transform: translateX(2px);
            border: 1px solid var(--primary-light);
        }
        
        .vendedor-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .vendedor-info {
            flex: 1;
        }
        
        .vendedor-nombre {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        .vendedor-email {
            font-size: 0.75rem;
            color: var(--gray);
        }
        
        /* Alerta de Inventario */
        .inventory-alert {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ff9800;
            border-radius: 8px;
            padding: 12px;
            margin-top: 12px;
        }
        
        .alert-content {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        
        .alert-icon {
            font-size: 1.2rem;
            color: #ff9800;
            flex-shrink: 0;
            margin-top: 2px;
        }
        
        .alert-text h6 {
            font-weight: 600;
            color: #856404;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }
        
        .alert-text p {
            color: #856404;
            margin-bottom: 8px;
            font-size: 0.8rem;
        }
        
        /* Acciones Rápidas */
        .quick-actions {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            margin-top: 15px;
        }
        
        .actions-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        
        .action-card {
            background: var(--light);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            color: var(--dark);
            transition: all 0.2s ease;
            border: 1px solid transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        
        .action-card:hover {
            background: white;
            transform: translateY(-2px);
            border-color: var(--primary);
            box-shadow: 0 3px 8px rgba(127, 173, 57, 0.15);
        }
        
        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }
        
        .action-text {
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        /* Estado Vacío */
        .empty-state {
            text-align: center;
            padding: 30px 15px;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.3;
        }
        
        .empty-state h5 {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 1rem;
        }
        
        .empty-state p {
            font-size: 0.85rem;
            margin: 0 auto 12px;
        }
        
        /* Time Widget */
        .time-widget {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-align: center;
            min-width: 130px;
            box-shadow: 0 2px 6px rgba(127, 173, 57, 0.2);
        }
        
        .current-time {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .current-date {
            font-size: 0.75rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        /* Sucursal Badge */
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
        
        /* Botón Ver Tienda */
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
        
        /* Responsive */
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 70px;
                padding: 12px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
                gap: 10px;
            }
        }
        
        @media (max-width: 992px) {
            .header-bar {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            
            .header-info {
                justify-content: center;
            }
            
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .stat-value {
                font-size: 1.3rem;
            }
            
            .content-row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
                padding: 10px;
            }
            
            .header-title {
                font-size: 1.1rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
            
            .table th,
            .table td {
                padding: 8px 10px;
                font-size: 0.8rem;
            }
            
            .badge-estado {
                padding: 3px 6px;
                font-size: 0.7rem;
                min-width: 70px;
            }
            
            .actions-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                margin-left: 0;
                padding: 8px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .header-info {
                flex-direction: column;
                width: 100%;
            }
            
            .time-widget {
                width: 100%;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--light-gray);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }
        
        /* Filas clickeables */
        .table tbody tr {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(127, 173, 57, 0.08);
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-card,
        .card,
        .quick-actions {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body>
    @include('gerente.layouts.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-tachometer-alt me-2"></i>Panel de Control
                    <span class="sucursal-badge">
                        <i class="fas fa-store"></i> {{ $sucursal->nombre ?? session('sucursal_nombre', 'Sin sucursal') }}
                    </span>
                </h1>
                <p class="text-muted mb-0 small">Bienvenido, {{ auth()->user()->nombre ?? 'Gerente' }} (Gerente)</p>
            </div>
            
            <div class="header-info">
                <div class="time-widget">
                    <div class="current-time" id="currentTime"></div>
                    <div class="current-date" id="currentDate"></div>
                </div>
                
                <a href="{{ route('tienda') }}" target="_blank" class="btn-store">
                    <i class="fas fa-store me-1"></i> Ver Tienda
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $stats['total_pedidos'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-shopping-cart me-1"></i>
                    Pedidos Sucursal
                </div>
            </div>
            
            <div class="stat-card stat-pendiente">
                <div class="stat-value">{{ $stats['pedidos_pendientes'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-clock me-1"></i>
                    Pendientes
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value">{{ $stats['total_vendedores'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-users me-1"></i>
                    Vendedores
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value">${{ number_format($stats['ventas_mes'], 0) }}</div>
                <div class="stat-label">
                    <i class="fas fa-dollar-sign me-1"></i>
                    Ventas Mes
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value">${{ number_format($stats['ventas_hoy'], 0) }}</div>
                <div class="stat-label">
                    <i class="fas fa-calendar-day me-1"></i>
                    Ventas Hoy
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value">{{ $stats['total_clientes'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-user-friends me-1"></i>
                    Clientes
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="content-row">
            <!-- Últimos Pedidos -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-history"></i> Últimos Pedidos
                    </h5>
                    <a href="{{ route('gerente.pedidos') }}" class="btn-store">
                        <i class="fas fa-list me-1"></i> Ver Todos
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimosPedidos as $pedido)
                                <tr onclick="window.location.href='{{ route('gerente.pedidos.ver', $pedido->id) }}';">
                                    <td>
                                        <strong class="text-primary">#{{ $pedido->folio }}</strong>
                                        <small class="text-muted d-block">{{ $pedido->items_count }} items</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $pedido->cliente_nombre }}</div>
                                        <small class="text-muted">{{ $pedido->cliente_telefono }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $pedido->fecha->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $pedido->fecha->format('H:i') }}</small>
                                    </td>
                                    <td class="fw-bold" style="color: var(--primary);">
                                        ${{ number_format($pedido->total, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge-estado badge-{{ $pedido->estado }}">
                                            <i class="fas fa-circle fa-xs"></i>
                                            {{ ucfirst($pedido->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons" onclick="event.stopPropagation();">
                                            <a href="{{ route('gerente.pedidos.ver', $pedido->id) }}" 
                                               class="btn-action btn-view" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <i class="fas fa-shopping-cart"></i>
                                        <h5>No hay pedidos en esta sucursal</h5>
                                        <p>Esta sucursal aún no tiene pedidos registrados.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Derecho -->
            <div>
                <!-- Productos más vendidos -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-fire me-2"></i>Productos más vendidos
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($productosMasVendidos as $producto)
                        <div class="product-item">
                            <div class="product-name">{{ $producto->producto_nombre }}</div>
                            <div class="product-sales">{{ $producto->total_vendido }}</div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-box"></i>
                            <h5>No hay ventas</h5>
                            <p>No hay productos vendidos aún en esta sucursal</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Vendedores de la sucursal -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Vendedores
                        </h5>
                        <span class="badge bg-primary">{{ $vendedores->count() }} activos</span>
                    </div>
                    <div class="card-body">
                        @forelse($vendedores as $vendedor)
                        @php
                            $nombre = $vendedor->nombre;
                            $iniciales = '';
                            $partes = explode(' ', $nombre);
                            foreach($partes as $parte) {
                                if(!empty(trim($parte))) {
                                    $iniciales .= strtoupper(substr(trim($parte), 0, 1));
                                    if(strlen($iniciales) >= 2) break;
                                }
                            }
                        @endphp
                        <div class="vendedor-item">
                            <div class="vendedor-avatar">
                                {{ empty($iniciales) ? 'V' : $iniciales }}
                            </div>
                            <div class="vendedor-info">
                                <div class="vendedor-nombre">{{ $vendedor->nombre }}</div>
                                <div class="vendedor-email">{{ $vendedor->email }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            <h5>Sin vendedores</h5>
                            <p>No hay vendedores asignados a esta sucursal</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Alerta de productos bajos -->
                @if($stats['productos_bajos'] > 0)
                <div class="inventory-alert">
                    <div class="alert-content">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="alert-text">
                            <h6>¡Atención!</h6>
                            <p>Tienes <strong>{{ $stats['productos_bajos'] }} producto(s)</strong> con inventario bajo en tu sucursal.</p>
                            <a href="{{ route('gerente.productos', ['filter' => 'bajos']) }}" class="btn-custom btn-primary-custom" style="background: #ff9800; border: none;">
                                <i class="fas fa-box me-1"></i> Revisar Inventario
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="quick-actions">
            <h3 class="actions-title">
                <i class="fas fa-bolt"></i> Acciones Rápidas
            </h3>
            
            <div class="actions-grid">
                <a href="{{ route('gerente.pedidos') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <span class="action-text">Ver Pedidos</span>
                </a>
                
                <a href="{{ route('gerente.productos') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <span class="action-text">Inventario</span>
                </a>
                
                <a href="{{ route('gerente.vendedores') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="action-text">Vendedores</span>
                </a>
                
                <a href="{{ route('gerente.reportes') }}" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <span class="action-text">Reportes</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Reloj en tiempo real
        function updateClock() {
            const now = new Date();
            
            const timeString = now.toLocaleTimeString('es-MX', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false
            });
            
            const dateString = now.toLocaleDateString('es-MX', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
            
            document.getElementById('currentTime').textContent = timeString;
            document.getElementById('currentDate').textContent = dateString;
        }
        
        setInterval(updateClock, 1000);
        updateClock();
        
        // SweetAlert - Bienvenida
        document.addEventListener('DOMContentLoaded', function() {
            const userName = "{{ auth()->user()->nombre ?? 'Gerente' }}";
            const sucursal = "{{ $sucursal->nombre ?? session('sucursal_nombre', 'Sin sucursal') }}";
            
            setTimeout(() => {
                Swal.fire({
                    title: `¡Bienvenido, ${userName}!`,
                    html: `Panel de gerencia - Sucursal: <strong>${sucursal}</strong>`,
                    icon: 'success',
                    confirmButtonColor: '#7fad39',
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            }, 500);
            
            // Alerta de pedidos pendientes
            @if($stats['pedidos_pendientes'] > 3)
            setTimeout(() => {
                Swal.fire({
                    title: 'Pedidos Pendientes',
                    html: `Tienes <strong>{{ $stats['pedidos_pendientes'] }} pedidos</strong> pendientes por atender en tu sucursal.`,
                    icon: 'warning',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Revisar Ahora',
                    showCancelButton: true,
                    cancelButtonText: 'Más Tarde'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("gerente.pedidos", ["estado" => "pendiente"]) }}';
                    }
                });
            }, 2000);
            @endif
            
            // Alerta de inventario bajo
            @if($stats['productos_bajos'] > 0)
            setTimeout(() => {
                Swal.fire({
                    title: 'Inventario Bajo',
                    html: `Tienes <strong>{{ $stats['productos_bajos'] }} productos</strong> con inventario bajo en tu sucursal.`,
                    icon: 'info',
                    confirmButtonColor: '#17a2b8',
                    confirmButtonText: 'Ver Inventario',
                    showCancelButton: true,
                    cancelButtonText: 'Después'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("gerente.productos", ["filter" => "bajos"]) }}';
                    }
                });
            }, 3000);
            @endif
        });
        
        // Auto-refresh cada 2 minutos
        setTimeout(function() {
            Swal.fire({
                title: 'Actualizando datos...',
                text: 'Obteniendo información más reciente de tu sucursal',
                icon: 'info',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            }).then(() => {
                window.location.reload();
            });
        }, 120000);
        
        // Animaciones hover
        document.querySelectorAll('.stat-card, .action-card, .product-item, .vendedor-item').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Filas clickeables
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('.action-buttons')) {
                    const viewLink = this.querySelector('.btn-view');
                    if (viewLink) {
                        window.location.href = viewLink.href;
                    }
                }
            });
        });
        
        // Notificaciones de conexión
        window.addEventListener('online', function() {
            Swal.fire({
                title: 'Conexión Restablecida',
                text: 'Estás conectado nuevamente',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
        
        window.addEventListener('offline', function() {
            Swal.fire({
                title: 'Sin Conexión',
                text: 'Comprueba tu conexión a internet',
                icon: 'warning',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });
        });
    </script>
</body>
</html>