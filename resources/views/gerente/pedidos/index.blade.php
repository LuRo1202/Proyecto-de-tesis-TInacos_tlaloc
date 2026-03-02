@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos - Sucursal {{ session('sucursal_nombre') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
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
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Header Compacto con Sucursal */
        .header-bar {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .header-title {
            margin: 0;
            color: var(--dark);
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .header-title i {
            color: var(--primary);
        }
        
        .sucursal-badge {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 10px;
        }
        
        .header-subtitle {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 5px;
        }
        
        .header-actions {
            display: flex;
            gap: 8px;
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
        
        .btn-danger-custom {
            background: linear-gradient(135deg, var(--danger), #c82333);
            color: white;
        }
        
        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning), #e0a800);
            color: #000;
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
        .stat-confirmado { border-top-color: #17a2b8; }
        .stat-enviado { border-top-color: #007bff; }
        .stat-entregado { border-top-color: #28a745; }
        .stat-cancelado { border-top-color: #dc3545; }
        
        /* Info Sucursal */
        .sucursal-info {
            background: white;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #3498db;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .sucursal-details {
            flex: 1;
        }
        
        .sucursal-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .sucursal-data {
            font-size: 0.85rem;
            color: var(--gray);
        }
        
        .sucursal-data span {
            margin-right: 15px;
        }
        
        .sucursal-data i {
            color: var(--primary);
            margin-right: 5px;
        }
        
        .vendedores-count {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Filtros Compactos */
        .filter-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--light-gray);
        }
        
        .filter-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .filter-title i {
            color: var(--primary);
        }
        
        .form-control-sm {
            padding: 6px 10px;
            font-size: 0.85rem;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            width: 100%;
            transition: all 0.2s ease;
        }
        
        .form-control-sm:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(127, 173, 57, 0.1);
            outline: none;
        }
        
        /* Selects mejorados */
        select.form-control-sm {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236c757d' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 12px 8px;
            padding-right: 2rem;
            cursor: pointer;
            appearance: none;
        }
        
        /* Tabla Compacta */
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
        
        .payment-badge {
            padding: 3px 6px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.7rem;
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
        
        /* Acciones Compactas */
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
        
        .btn-edit {
            background: linear-gradient(135deg, var(--warning), #e0a800);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, var(--danger), #c82333);
        }
        
        /* Estado Vacío Compacto */
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
        
        /* Botón Tienda */
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
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 70px;
                padding: 15px;
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
            
            .header-actions {
                justify-content: center;
            }
            
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .stat-value {
                font-size: 1.3rem;
            }
            
            .sucursal-info {
                flex-direction: column;
                text-align: center;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
                padding: 12px;
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
        }
        
        @media (max-width: 576px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .header-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-custom {
                width: 100%;
                justify-content: center;
            }
            
            .filter-card {
                padding: 12px;
            }
            
            .filter-title {
                font-size: 0.9rem;
            }
            
            .action-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .btn-action {
                width: 28px;
                height: 28px;
            }
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
        .filter-card,
        .card {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Scrollbar Personalizado */
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
        
        /* Mejora para inputs de fecha */
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.6;
            filter: invert(0.5);
        }
        
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }
        
        /* Filas clickeables */
        .table tbody tr {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(127, 173, 57, 0.08);
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
                    <i class="fas fa-shopping-cart me-2"></i>Pedidos de Sucursal
                    <span class="sucursal-badge">
                        <i class="fas fa-store"></i> {{ session('sucursal_nombre') }}
                    </span>
                </h1>
                <div class="header-subtitle">
                    Gerente: {{ auth()->user()->nombre ?? 'Gerente' }}
                </div>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('gerente.dashboard') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('gerente.pedidos.nuevo') }}" class="btn-custom btn-success-custom">
                    <i class="fas fa-plus"></i> Nuevo Pedido
                </a>
                <a href="{{ route('tienda') }}" target="_blank" class="btn-store">
                    <i class="fas fa-store me-1"></i> Ver Tienda
                </a>
                @if(request()->has('estado') || request()->has('fecha_inicio') || request()->has('fecha_fin') || request()->has('busqueda'))
                <a href="{{ route('gerente.pedidos') }}" class="btn-custom btn-primary-custom">
                    <i class="fas fa-times"></i> Limpiar
                </a>
                @endif
            </div>
        </div>

        <!-- Información de la Sucursal -->
        <div class="sucursal-info">
            <div class="sucursal-details">
                <div class="sucursal-name">
                    <i class="fas fa-store"></i>
                    {{ session('sucursal_nombre') }}
                </div>
                <div class="sucursal-data">
                    <span>
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $sucursal->direccion ?? 'Dirección no disponible' }}
                    </span>
                    <span>
                        <i class="fas fa-phone"></i>
                        {{ $sucursal->telefono ?? 'Teléfono no disponible' }}
                    </span>
                </div>
            </div>
            <div class="vendedores-count">
                <i class="fas fa-users"></i>
                {{ $vendedores->count() }} vendedores activos
            </div>
        </div>

        <!-- Estadísticas de la Sucursal -->
        <div class="stats-grid">
            @php
            $estados_map = [
                'pendiente' => ['label' => 'Pendientes', 'icon' => 'fa-clock'],
                'confirmado' => ['label' => 'Confirmados', 'icon' => 'fa-check-circle'],
                'enviado' => ['label' => 'Enviados', 'icon' => 'fa-shipping-fast'],
                'entregado' => ['label' => 'Entregados', 'icon' => 'fa-box-open'],
                'cancelado' => ['label' => 'Cancelados', 'icon' => 'fa-ban']
            ];
            @endphp
            
            @foreach($estados_map as $key => $info)
                @php
                $count = 0;
                foreach($estados_count as $estado_item) {
                    if ($estado_item->estado == $key) {
                        $count = (int)$estado_item->total;
                        break;
                    }
                }
                @endphp
                <div class="stat-card stat-{{ $key }}">
                    <div class="stat-value">{{ $count }}</div>
                    <div class="stat-label">
                        <i class="fas {{ $info['icon'] }}"></i>
                        {{ $info['label'] }}
                    </div>
                </div>
            @endforeach
            
            <div class="stat-card">
                <div class="stat-value">{{ $pedidos->count() }}</div>
                <div class="stat-label">
                    <i class="fas fa-list"></i>
                    Total Pedidos
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-card">
            <h3 class="filter-title">
                <i class="fas fa-filter"></i> Filtros de Búsqueda
            </h3>
            
            <form method="GET" action="{{ route('gerente.pedidos') }}" class="row g-2">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Estado</label>
                    <select name="estado" class="form-control-sm">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="enviado" {{ request('estado') == 'enviado' ? 'selected' : '' }}>Enviado</option>
                        <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control-sm" value="{{ request('fecha_inicio') }}">
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control-sm" value="{{ request('fecha_fin') }}">
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Buscar</label>
                    <div class="input-group">
                        <input type="text" name="busqueda" class="form-control-sm" 
                               placeholder="Folio, cliente..." 
                               value="{{ request('busqueda') }}">
                        <button class="btn-action btn-view" type="submit" style="border-radius: 0 5px 5px 0;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <div class="col-12 mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn-custom btn-primary-custom">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        
                        <div class="text-muted small">
                            <i class="fas fa-info-circle"></i>
                            Mostrando <strong>{{ $pedidos->count() }}</strong> pedidos de sucursal | 
                            Total: <strong>${{ number_format($total_general, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Lista de Pedidos -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-list-ol"></i> Pedidos de {{ session('sucursal_nombre') }}
                    <span class="badge bg-primary">{{ $pedidos->count() }}</span>
                </h5>
                
                @if($pedidos->count() > 0)
                <div class="text-muted small">
                    <i class="fas fa-money-bill-wave"></i>
                    Total sucursal: <strong>${{ number_format($total_general, 2) }}</strong>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablaPedidos">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Pago</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pedidos as $pedido)
                            <tr onclick="window.location.href='{{ route('gerente.pedidos.ver', $pedido->id) }}';">
                                <td>
                                    <strong class="text-primary">#{{ $pedido->folio }}</strong>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-box"></i>
                                        {{ $pedido->items_count ?? 0 }} items
                                    </small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $pedido->cliente_nombre }}</div>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone"></i>
                                        {{ $pedido->cliente_telefono }}
                                    </small>
                                </td>
                                <td>
                                    <div>{{ Carbon::parse($pedido->fecha)->format('d/m/Y') }}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        {{ Carbon::parse($pedido->fecha)->format('H:i') }}
                                    </small>
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
                                    @if($pedido->pago_confirmado)
                                        <span class="payment-badge payment-confirmed">
                                            <i class="fas fa-check-circle"></i> Confirmado
                                        </span>
                                    @else
                                        <span class="payment-badge payment-pending">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons" onclick="event.stopPropagation();">
                                        <a href="{{ route('gerente.pedidos.ver', $pedido->id) }}" 
                                           class="btn-action btn-view" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('gerente.pedidos.editar', $pedido->id) }}" 
                                           class="btn-action btn-edit" 
                                           title="Editar pedido">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-delete" 
                                                title="Eliminar pedido" 
                                                onclick="confirmarEliminar({{ $pedido->id }}, '{{ $pedido->folio }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h5>No hay pedidos en esta sucursal</h5>
                                    <p>{{ request()->has('estado') || request()->has('busqueda') ? 
                                        'No hay pedidos que coincidan con los filtros aplicados.' : 
                                        'Esta sucursal aún no tiene pedidos registrados.' }}</p>
                                    <a href="{{ route('gerente.pedidos') }}" class="btn-custom btn-primary-custom mt-2">
                                        <i class="fas fa-redo"></i> Ver Todos
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ✅ CORREGIDO: Inicializar DataTable solo si hay datos
        $(document).ready(function() {
            if ($('#tablaPedidos tbody tr').length > 1 || !$('#tablaPedidos tbody tr td.empty-state').length) {
                $('#tablaPedidos').DataTable({
                    language: {
                        "sProcessing":     "Procesando...",
                        "sLengthMenu":     "Mostrar _MENU_ registros",
                        "sZeroRecords":    "No se encontraron resultados",
                        "sEmptyTable":     "Ningún dato disponible",
                        "sInfo":           "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        "sInfoEmpty":      "Mostrando 0 a 0 de 0 registros",
                        "sInfoFiltered":   "(filtrado de _MAX_ registros totales)",
                        "sSearch":         "Buscar:",
                        "oPaginate": {
                            "sFirst":    "Primero",
                            "sLast":     "Último",
                            "sNext":     "Siguiente",
                            "sPrevious": "Anterior"
                        }
                    },
                    pageLength: 25,
                    order: [[2, 'desc']],
                    responsive: true,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>tip',
                    columnDefs: [
                        { responsivePriority: 1, targets: 0 },
                        { responsivePriority: 2, targets: 6 },
                        { responsivePriority: 3, targets: 3 }
                    ]
                });
            }
        });
        
        // ✅ FUNCIÓN CORREGIDA PARA ELIMINAR CON FETCH
        function confirmarEliminar(id, folio) {
            event.stopPropagation();
            
            Swal.fire({
                title: '¿Eliminar Pedido?',
                html: `¿Estás seguro de eliminar el pedido <strong>#${folio}</strong> de esta sucursal?<br><small>Esta acción no se puede deshacer y regresará el stock al inventario.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Eliminando pedido...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            
                            // Hacer petición DELETE con fetch
                            fetch(`/gerente/pedidos/eliminar/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(err => { 
                                        throw new Error(err.message || 'Error en la respuesta del servidor');
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: '¡Eliminado!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#7fad39',
                                        timer: 2000,
                                        timerProgressBar: true
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: data.message || 'No se pudo eliminar el pedido',
                                        icon: 'error',
                                        confirmButtonColor: '#7fad39'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Error',
                                    text: error.message || 'Error de conexión al eliminar el pedido',
                                    icon: 'error',
                                    confirmButtonColor: '#7fad39'
                                });
                            });
                        }
                    });
                }
            });
        }
        
        // SweetAlert2 para filtros aplicados
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('estado') || urlParams.has('fecha_inicio') || 
                              urlParams.has('fecha_fin') || urlParams.has('busqueda');
            
            if (hasFilters) {
                setTimeout(() => {
                    Swal.fire({
                        title: 'Filtros Aplicados',
                        text: 'Mostrando resultados filtrados de tu sucursal',
                        icon: 'info',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }, 500);
            }
            
            // Mostrar alerta si no hay pedidos
            @if($pedidos->count() === 0 && (request()->has('estado') || request()->has('busqueda')))
            setTimeout(() => {
                Swal.fire({
                    title: 'Sin Resultados',
                    html: 'No se encontraron pedidos con los filtros aplicados en tu sucursal.',
                    icon: 'warning',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Limpiar Filtros',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("gerente.pedidos") }}';
                    }
                });
            }, 1000);
            @endif
            
            // Notificación de bienvenida
            setTimeout(() => {
                Swal.fire({
                    title: 'Pedidos de Sucursal',
                    html: `Viendo pedidos de: <strong>{{ session('sucursal_nombre') }}</strong>`,
                    icon: 'info',
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            }, 800);
        });
        
        // Efecto hover para tarjetas
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Hacer filas clickeables
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('.action-buttons') && !e.target.closest('.btn')) {
                    const viewLink = this.querySelector('.btn-view');
                    if (viewLink) {
                        window.location.href = viewLink.href;
                    }
                }
            });
        });
        
        // Mejorar experiencia de inputs
        document.querySelectorAll('select, input[type="date"], input[type="text"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = 'var(--primary)';
                this.style.boxShadow = '0 0 0 2px rgba(127, 173, 57, 0.1)';
            });
            
            input.addEventListener('blur', function() {
                this.style.borderColor = 'var(--light-gray)';
                this.style.boxShadow = 'none';
            });
        });
        
        // Prevenir conflicto de SweetAlert con DataTables
        $(document).ready(function() {
            // Desactivar alertas de DataTables
            $.fn.dataTable.ext.errMode = 'none';
        });
    </script>

    @if(session('swal'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '{{ session('swal.type') }}',
                title: '{{ session('swal.title') }}',
                text: '{{ session('swal.message') }}',
                confirmButtonColor: '#7fad39',
                timer: {{ session('swal.type') == 'success' ? 3000 : '' }},
                timerProgressBar: {{ session('swal.type') == 'success' ? 'true' : 'false' }},
                showConfirmButton: {{ session('swal.type') == 'error' ? 'true' : 'false' }}
            });
        });
    </script>
    @endif
</body>
</html>