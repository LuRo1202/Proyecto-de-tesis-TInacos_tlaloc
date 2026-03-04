@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - Sucursal {{ session('sucursal_nombre') }}</title>
    
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
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
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
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--success), #218838);
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
        
        .stat-clientes { border-top-color: var(--primary); }
        .stat-ventas { border-top-color: var(--success); }
        .stat-promedio { border-top-color: var(--info); }
        .stat-pedidos { border-top-color: var(--warning); }
        
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
        
        select.form-control-sm {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236c757d' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 12px 8px;
            padding-right: 2rem;
            cursor: pointer;
            appearance: none;
        }
        
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
        
        .cliente-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        .badge-pedidos {
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            background: rgba(127, 173, 57, 0.1);
            color: var(--primary);
            border: 1px solid rgba(127, 173, 57, 0.2);
        }
        
        .cliente-stats {
            display: flex;
            gap: 10px;
        }
        
        .stat-mini {
            text-align: center;
            padding: 6px;
            background: var(--light);
            border-radius: 5px;
            min-width: 70px;
        }
        
        .stat-mini-value {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        .stat-mini-label {
            font-size: 0.7rem;
            color: var(--gray);
            margin-top: 2px;
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
            cursor: pointer;
        }
        
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
        
        .btn-history {
            background: linear-gradient(135deg, var(--info), #138496);
        }
        
        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
        }
        
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
        
        .pagination-custom .page-link {
            border: none;
            color: var(--gray);
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 5px;
            margin: 0 2px;
        }
        
        .pagination-custom .page-item.active .page-link {
            background: var(--primary);
            color: white;
        }
        
        .pagination-custom .page-link:hover {
            background: var(--light-gray);
            color: var(--dark);
        }
        
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 70px;
                padding: 15px;
            }
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
                gap: 10px;
            }
            .cliente-stats {
                flex-direction: column;
                gap: 5px;
            }
            .stat-mini {
                min-width: 60px;
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
            .card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
            .cliente-stats {
                flex-direction: row;
                flex-wrap: wrap;
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
            .table th,
            .table td {
                padding: 8px 10px;
                font-size: 0.8rem;
            }
            .cliente-avatar {
                width: 35px;
                height: 35px;
                font-size: 0.8rem;
            }
            .btn-action {
                width: 28px;
                height: 28px;
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
            .action-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }
            .cliente-stats {
                flex-direction: column;
            }
        }
        
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
        
        .table tbody tr {
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
        <!-- Header con Sucursal -->
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-users me-2"></i>Clientes de la Sucursal
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
                <button onclick="generarReporteClientes()" class="btn-custom btn-success-custom">
                    <i class="fas fa-file-excel"></i> Reporte Excel
                </button>
            </div>
        </div>

        <!-- Estadísticas de la Sucursal -->
        <div class="stats-grid">
            <div class="stat-card stat-clientes">
                <div class="stat-value">{{ number_format($stats['total_clientes'] ?? 0) }}</div>
                <div class="stat-label">
                    <i class="fas fa-users"></i>
                    Clientes Totales
                </div>
            </div>
            
            <div class="stat-card stat-ventas">
                <div class="stat-value">${{ number_format($stats['total_ventas'] ?? 0, 0) }}</div>
                <div class="stat-label">
                    <i class="fas fa-money-bill-wave"></i>
                    Total Ventas
                </div>
            </div>
            
            <div class="stat-card stat-promedio">
                <div class="stat-value">${{ number_format($stats['promedio_pedido'] ?? 0, 0) }}</div>
                <div class="stat-label">
                    <i class="fas fa-chart-line"></i>
                    Promedio Pedido
                </div>
            </div>
            
            <div class="stat-card stat-pedidos">
                <div class="stat-value">{{ number_format($clientes->count()) }}</div>
                <div class="stat-label">
                    <i class="fas fa-shopping-cart"></i>
                    Mostrando
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-card">
            <h3 class="filter-title">
                <i class="fas fa-filter"></i> Filtros de Clientes
            </h3>
            
            <form method="GET" action="{{ route('gerente.clientes') }}" class="row g-2" id="formFiltros">
                <div class="col-md-4 col-sm-6">
                    <label class="form-label small fw-semibold">Buscar Cliente</label>
                    <input type="text" name="busqueda" class="form-control-sm" 
                           placeholder="Nombre o teléfono..." 
                           value="{{ request('busqueda') }}">
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Estado</label>
                    <select name="estado" class="form-control-sm" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $est)
                        <option value="{{ $est->cliente_estado }}" 
                            {{ request('estado') == $est->cliente_estado ? 'selected' : '' }}>
                            {{ $est->cliente_estado }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Ciudad</label>
                    <select name="ciudad" class="form-control-sm" onchange="this.form.submit()">
                        <option value="">Todas las ciudades</option>
                        @foreach($ciudades as $ciu)
                        <option value="{{ $ciu->cliente_ciudad }}" 
                            {{ request('ciudad') == $ciu->cliente_ciudad ? 'selected' : '' }}>
                            {{ $ciu->cliente_ciudad }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-semibold">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-action" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark));" title="Buscar">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('gerente.clientes') }}" class="btn-action" style="background: linear-gradient(135deg, var(--gray), #6c757d);" title="Limpiar filtros">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-12 mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn-custom btn-primary-custom">
                            <i class="fas fa-filter"></i> Aplicar Filtros
                        </button>
                        
                        <div class="text-muted small">
                            <i class="fas fa-info-circle"></i>
                            Mostrando <strong>{{ $clientes->count() }}</strong> de <strong>{{ $total_clientes }}</strong> clientes
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Lista de Clientes -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-list-ol"></i> Clientes de la Sucursal
                    <span class="badge bg-primary">{{ $clientes->count() }}</span>
                </h5>
                
                @if($clientes->count() > 0)
                <div class="text-muted small">
                    <i class="fas fa-store"></i>
                    Sucursal: <strong>{{ session('sucursal_nombre') }}</strong>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Ubicación</th>
                                <th>Estadísticas</th>
                                <th>Último Pedido</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientes as $cliente)
                                @php
                                    $iniciales = '';
                                    $partes_nombre = explode(' ', $cliente->cliente_nombre);
                                    foreach($partes_nombre as $parte) {
                                        if(trim($parte) != '') {
                                            $iniciales .= strtoupper(substr($parte, 0, 1));
                                            if(strlen($iniciales) >= 2) break;
                                        }
                                    }
                                    
                                    // Color aleatorio pero consistente por teléfono
                                    $colors = ['#7fad39', '#3498db', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c'];
                                    $colorIndex = abs(crc32($cliente->cliente_telefono)) % count($colors);
                                    $avatarColor = $colors[$colorIndex];
                                @endphp
                                <tr onclick="verHistorialCliente('{{ $cliente->cliente_telefono }}')">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="cliente-avatar" style="background: linear-gradient(135deg, {{ $avatarColor }}, {{ $avatarColor }}dd);">
                                                {{ $iniciales ?: 'C' }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $cliente->cliente_nombre }}</div>
                                                <span class="badge-pedidos">
                                                    <i class="fas fa-shopping-cart fa-xs"></i>
                                                    {{ $cliente->total_pedidos }} pedidos
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="fas fa-phone fa-xs me-1 text-muted"></i>
                                            {{ $cliente->cliente_telefono }}
                                        </div>
                                        @if($cliente->email ?? false)
                                        <div>
                                            <i class="fas fa-envelope fa-xs me-1 text-muted"></i>
                                            <small>{{ $cliente->email }}</small>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <i class="fas fa-map-marker-alt fa-xs me-1 text-muted"></i>
                                            {{ $cliente->cliente_ciudad ?? 'N/A' }}, 
                                            {{ $cliente->cliente_estado ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="cliente-stats">
                                            <div class="stat-mini">
                                                <div class="stat-mini-value">${{ number_format($cliente->total_gastado ?? 0, 0) }}</div>
                                                <div class="stat-mini-label">Total</div>
                                            </div>
                                            <div class="stat-mini">
                                                <div class="stat-mini-value">{{ $cliente->total_pedidos }}</div>
                                                <div class="stat-mini-label">Pedidos</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $cliente->ultimo_pedido ? $cliente->ultimo_pedido->format('d/m/Y') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="action-buttons" onclick="event.stopPropagation();">
                                            <button type="button" 
                                                    class="btn-action btn-history" 
                                                    title="Ver historial"
                                                    onclick="verHistorialCliente('{{ $cliente->cliente_telefono }}')">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <a href="https://wa.me/52{{ preg_replace('/[^0-9]/', '', $cliente->cliente_telefono) }}?text=Hola%20{{ urlencode($cliente->cliente_nombre) }}%2C%20te%20contacto%20de%20Tinacos%20Tláloc%20-%20Sucursal%20{{ urlencode(session('sucursal_nombre')) }}"
                                               class="btn-action btn-whatsapp" 
                                               target="_blank"
                                               title="Contactar WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <h5>No hay clientes registrados</h5>
                                        <p>{{ request()->has('busqueda') || request()->has('estado') || request()->has('ciudad') ? 
                                            'No hay clientes que coincidan con los filtros aplicados en tu sucursal.' : 
                                            'Los clientes de tu sucursal aparecerán aquí después de realizar pedidos.' }}</p>
                                        @if(request()->has('busqueda') || request()->has('estado') || request()->has('ciudad'))
                                        <a href="{{ route('gerente.clientes') }}" class="btn-custom btn-primary-custom mt-2">
                                            <i class="fas fa-redo"></i> Ver Todos los Clientes
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Paginación -->
            @if($total_paginas > 1)
            <div class="card-footer">
                <nav aria-label="Paginación">
                    <ul class="pagination pagination-custom justify-content-center mb-0">
                        @if($pagina > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ route('gerente.clientes', array_merge(request()->query(), ['pagina' => $pagina-1])) }}">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        @endif
                        
                        @for($i = max(1, $pagina - 2); $i <= min($total_paginas, $pagina + 2); $i++)
                            @if($i == $pagina)
                            <li class="page-item active">
                                <span class="page-link">{{ $i }}</span>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link" href="{{ route('gerente.clientes', array_merge(request()->query(), ['pagina' => $i])) }}">
                                    {{ $i }}
                                </a>
                            </li>
                            @endif
                        @endfor
                        
                        @if($pagina < $total_paginas)
                        <li class="page-item">
                            <a class="page-link" href="{{ route('gerente.clientes', array_merge(request()->query(), ['pagina' => $pagina+1])) }}">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Ver historial del cliente
        function verHistorialCliente(telefono) {
            window.location.href = '{{ route("gerente.clientes.historial") }}?telefono=' + encodeURIComponent(telefono);
        }
        
        // Generar reporte de clientes
        function generarReporteClientes() {
            Swal.fire({
                title: 'Generando Reporte...',
                text: 'Preparando reporte de clientes de la sucursal',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                didOpen: () => {
                    Swal.showLoading();
                    
                    setTimeout(() => {
                        Swal.fire({
                            title: '¡Reporte Generado!',
                            html: `Reporte de clientes - Sucursal <strong>{{ session('sucursal_nombre') }}</strong><br><br>
                                   <div class="text-start">
                                       <p><i class="fas fa-users text-primary me-2"></i> Total clientes: <strong>{{ number_format($stats['total_clientes'] ?? 0) }}</strong></p>
                                       <p><i class="fas fa-money-bill-wave text-success me-2"></i> Ventas totales: <strong>${{ number_format($stats['total_ventas'] ?? 0, 2) }}</strong></p>
                                       <p><i class="fas fa-chart-line text-info me-2"></i> Promedio por pedido: <strong>${{ number_format($stats['promedio_pedido'] ?? 0, 2) }}</strong></p>
                                   </div>`,
                            icon: 'success',
                            confirmButtonColor: '#7fad39',
                            confirmButtonText: 'Descargar Excel',
                            showCancelButton: true,
                            cancelButtonText: 'Cerrar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Crear URL con los filtros actuales
                                let url = '{{ route("gerente.clientes.reporte") }}?' + new URLSearchParams(window.location.search).toString();
                                window.open(url, '_blank');
                            }
                        });
                    }, 1500);
                }
            });
        }
        
        // SweetAlert2 para filtros aplicados
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('busqueda') || urlParams.has('estado') || urlParams.has('ciudad');
            
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
            
            // Mostrar alerta si no hay clientes
            @if($clientes->count() === 0 && (request()->has('busqueda') || request()->has('estado') || request()->has('ciudad')))
            setTimeout(() => {
                Swal.fire({
                    title: 'Sin Resultados',
                    html: 'No se encontraron clientes con los filtros aplicados en tu sucursal.',
                    icon: 'warning',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Limpiar Filtros'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("gerente.clientes") }}';
                    }
                });
            }, 1000);
            @endif
            
            // Mostrar mensaje inicial
            @if($clientes->count() > 0)
            setTimeout(() => {
                Swal.fire({
                    title: 'Clientes de Sucursal',
                    html: `Estás viendo los clientes de la sucursal <strong>{{ session('sucursal_nombre') }}</strong>`,
                    icon: 'info',
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            }, 800);
            @endif
        });
        
        // Efecto hover para tarjetas de estadísticas
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
            if (!row.querySelector('.empty-state')) {
                row.addEventListener('click', function(e) {
                    if (!e.target.closest('.action-buttons')) {
                        const telefono = this.querySelector('td:nth-child(2)').textContent.trim();
                        verHistorialCliente(telefono);
                    }
                });
            }
        });
        
        // Mejorar experiencia de selects
        document.querySelectorAll('select, input[type="text"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = 'var(--primary)';
                this.style.boxShadow = '0 0 0 2px rgba(127, 173, 57, 0.1)';
            });
            
            input.addEventListener('blur', function() {
                this.style.borderColor = 'var(--light-gray)';
                this.style.boxShadow = 'none';
            });
        });

        @if(session('swal'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '{{ session('swal.title') }}',
                text: '{{ session('swal.message') }}',
                icon: '{{ session('swal.type') }}',
                confirmButtonColor: '#7fad39'
            });
        });
        @endif
    </script>
</body>
</html>