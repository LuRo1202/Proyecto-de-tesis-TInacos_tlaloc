@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Sucursal - {{ session('sucursal_nombre') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
        
        /* Header con Sucursal */
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
        
        .btn-export {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .btn-export:hover {
            background: linear-gradient(135deg, #2980b9, #1f618d);
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
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
        
        .stat-pedidos { border-top-color: var(--primary); }
        .stat-ventas { border-top-color: var(--success); }
        .stat-promedio { border-top-color: var(--info); }
        .stat-clientes { border-top-color: var(--warning); }
        
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
        
        select.form-control-sm {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236c757d' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 12px 8px;
            padding-right: 2rem;
            cursor: pointer;
            appearance: none;
        }
        
        /* Card Styles */
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
        
        /* Tabs Personalizados */
        .nav-tabs-custom {
            border: none;
            gap: 5px;
        }
        
        .nav-tabs-custom .nav-link {
            border: none;
            color: var(--gray);
            font-weight: 500;
            font-size: 0.85rem;
            padding: 8px 15px;
            border-radius: 6px;
            transition: all 0.2s ease;
            background: white;
            border: 1px solid var(--light-gray);
        }
        
        .nav-tabs-custom .nav-link:hover {
            background: var(--light);
            color: var(--dark);
        }
        
        .nav-tabs-custom .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-color: var(--primary);
        }
        
        /* Chart Containers */
        .chart-container {
            position: relative;
            height: 250px;
            margin-bottom: 20px;
        }
        
        .tab-content {
            padding: 15px 0;
        }
        
        /* Tablas Compactas */
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
        
        /* Progress Bars */
        .progress-custom {
            height: 20px;
            border-radius: 10px;
            background: var(--light-gray);
            overflow: hidden;
        }
        
        .progress-bar-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 10px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
        }
        
        /* Método de Pago Badges */
        .badge-metodo {
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        
        .badge-en-linea { 
            background: rgba(40, 167, 69, 0.1); 
            color: var(--success); 
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        .badge-efectivo { 
            background: rgba(23, 162, 184, 0.1); 
            color: #0c5460; 
            border: 1px solid rgba(23, 162, 184, 0.2);
        }
        .badge-transferencia { 
            background: rgba(255, 193, 7, 0.1); 
            color: #856404; 
            border: 1px solid rgba(255, 193, 7, 0.2);
        }
        
        /* Vendedor Avatar */
        .vendedor-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--info), #138496);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.8rem;
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
            
            .chart-container {
                height: 220px;
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
            
            .chart-container {
                height: 200px;
            }
            
            .nav-tabs-custom {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-tabs-custom .nav-link {
                padding: 6px 12px;
                font-size: 0.8rem;
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
            
            .chart-container {
                height: 180px;
            }
            
            .nav-tabs-custom {
                gap: 3px;
            }
            
            .nav-tabs-custom .nav-link {
                padding: 5px 10px;
                font-size: 0.75rem;
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
            
            .chart-container {
                height: 160px;
            }
            
            .nav-tabs-custom {
                flex-direction: column;
            }
            
            .nav-tabs-custom .nav-link {
                text-align: center;
            }
        }
        
        /* Animaciones Suaves */
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
    </style>
</head>
<body>
    @include('gerente.layouts.sidebar')
    
    <div class="main-content">
        <!-- Header con Sucursal -->
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-chart-line me-2"></i>Reportes de la Sucursal
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
    
                
            </div>
        </div>

        <!-- Estadísticas de la Sucursal -->
        <div class="stats-grid">
            <div class="stat-card stat-pedidos">
                <div class="stat-value">{{ $estadisticas['total_pedidos'] ?? 0 }}</div>
                <div class="stat-label">
                    <i class="fas fa-shopping-cart"></i>
                    Pedidos
                </div>
            </div>
            
            <div class="stat-card stat-ventas">
                <div class="stat-value">${{ number_format($estadisticas['total_ventas'] ?? 0, 0) }}</div>
                <div class="stat-label">
                    <i class="fas fa-money-bill-wave"></i>
                    Ventas Totales
                </div>
            </div>
            
            <div class="stat-card stat-promedio">
                <div class="stat-value">${{ number_format($estadisticas['promedio_venta'] ?? 0, 0) }}</div>
                <div class="stat-label">
                    <i class="fas fa-chart-line"></i>
                    Venta Promedio
                </div>
            </div>
            
            <div class="stat-card stat-clientes">
                <div class="stat-value">{{ $estadisticas['clientes_unicos'] ?? 0 }}</div>
                <div class="stat-label">
                    <i class="fas fa-users"></i>
                    Clientes Únicos
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-card">
            <h3 class="filter-title">
                <i class="fas fa-filter"></i> Filtros del Reporte
            </h3>
            
            <form method="GET" action="{{ route('gerente.reportes') }}" class="row g-2">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control-sm" value="{{ $fecha_inicio }}">
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control-sm" value="{{ $fecha_fin }}">
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Tipo de Reporte</label>
                    <select name="tipo_reporte" class="form-control-sm">
                        <option value="ventas" {{ $tipo_reporte == 'ventas' ? 'selected' : '' }}>Ventas</option>
                        <option value="productos" {{ $tipo_reporte == 'productos' ? 'selected' : '' }}>Productos</option>
                        <option value="vendedores" {{ $tipo_reporte == 'vendedores' ? 'selected' : '' }}>Vendedores</option>
                        <option value="pagos" {{ $tipo_reporte == 'pagos' ? 'selected' : '' }}>Métodos de Pago</option>
                    </select>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div style="display: flex; gap: 5px; margin-top: 22px;">
                        <button type="submit" class="btn-custom btn-primary-custom" style="flex: 1;">
                            <i class="fas fa-filter"></i> Generar Reporte
                        </button>
                        <a href="{{ route('gerente.reportes') }}" class="btn-custom btn-secondary-custom" title="Limpiar Filtros">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-12 mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="fas fa-calendar"></i>
                            {{ Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ Carbon::parse($fecha_fin)->format('d/m/Y') }}
                            <span class="ms-2">
                                <i class="fas fa-store"></i> {{ session('sucursal_nombre') }}
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Pestañas de Reportes -->
        <ul class="nav nav-tabs nav-tabs-custom mb-3" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $tipo_reporte == 'ventas' ? 'active' : '' }}" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button">
                    <i class="fas fa-chart-bar me-1"></i> Ventas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $tipo_reporte == 'productos' ? 'active' : '' }}" id="productos-tab" data-bs-toggle="tab" data-bs-target="#productos" type="button">
                    <i class="fas fa-box me-1"></i> Productos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $tipo_reporte == 'vendedores' ? 'active' : '' }}" id="vendedores-tab" data-bs-toggle="tab" data-bs-target="#vendedores" type="button">
                    <i class="fas fa-users me-1"></i> Vendedores
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $tipo_reporte == 'pagos' ? 'active' : '' }}" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#pagos" type="button">
                    <i class="fas fa-credit-card me-1"></i> Pagos
                </button>
            </li>
        </ul>

        <div class="tab-content" id="reportTabsContent">
            <!-- Pestaña Ventas -->
            <div class="tab-pane fade {{ $tipo_reporte == 'ventas' ? 'show active' : '' }}" id="ventas" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-chart-bar"></i> Evolución de Ventas
                        </h5>
                        <div class="text-muted small">
                            <i class="fas fa-store"></i> {{ session('sucursal_nombre') }}
                        </div>
                    </div>
                    <div class="card-body">
                        @if($datos_ventas->count() > 0)
                        <div class="chart-container">
                            <canvas id="ventasChart"></canvas>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Pedidos</th>
                                        <th>Ventas Totales</th>
                                        <th>Venta Promedio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($datos_ventas as $venta)
                                    <tr>
                                        <td>
                                            <i class="far fa-calendar text-muted me-1"></i>
                                            {{ $venta->fecha_dia->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $venta->total_pedidos }}</span>
                                        </td>
                                        <td class="fw-bold" style="color: var(--primary);">
                                            ${{ number_format($venta->total_ventas, 2) }}
                                        </td>
                                        <td>
                                            ${{ number_format($venta->promedio_venta, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="empty-state">
                            <i class="fas fa-chart-bar"></i>
                            <h5>No hay ventas registradas</h5>
                            <p>No se encontraron ventas en el período seleccionado para tu sucursal.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pestaña Productos -->
            <div class="tab-pane fade {{ $tipo_reporte == 'productos' ? 'show active' : '' }}" id="productos" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-box"></i> Productos Más Vendidos
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($top_productos->count() > 0)
                        <div class="chart-container">
                            <canvas id="productosChart"></canvas>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Código</th>
                                        <th>Unidades Vendidas</th>
                                        <th>Ingresos Totales</th>
                                        <th>Precio Unitario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($top_productos as $producto)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $producto->nombre }}</div>
                                            <small class="text-muted">{{ $producto->litros }} litros</small>
                                        </td>
                                        <td>
                                            <code>{{ $producto->codigo }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $producto->total_vendido }} unid.</span>
                                        </td>
                                        <td class="fw-bold" style="color: var(--primary);">
                                            ${{ number_format($producto->total_ingresos, 2) }}
                                        </td>
                                        <td>
                                            ${{ number_format($producto->precio, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="empty-state">
                            <i class="fas fa-box"></i>
                            <h5>No hay productos vendidos</h5>
                            <p>No se encontraron productos vendidos en el período seleccionado en tu sucursal.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pestaña Vendedores -->
            <div class="tab-pane fade {{ $tipo_reporte == 'vendedores' ? 'show active' : '' }}" id="vendedores" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-users"></i> Desempeño de Vendedores
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($ventas_por_vendedor->count() > 0)
                        <div class="chart-container">
                            <canvas id="vendedoresChart"></canvas>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Vendedor</th>
                                        <th>Pedidos</th>
                                        <th>Ventas Totales</th>
                                        <th>Venta Promedio</th>
                                        <th>% del Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_ventas_vendedores = $ventas_por_vendedor->sum('total_ventas');
                                    @endphp
                                    @foreach($ventas_por_vendedor as $vendedor)
                                        @php
                                            $iniciales = strtoupper(substr($vendedor->vendedor_nombre, 0, 2));
                                            $porcentaje = $total_ventas_vendedores > 0 ? ($vendedor->total_ventas / $total_ventas_vendedores * 100) : 0;
                                        @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="vendedor-avatar">
                                                    {{ $iniciales }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $vendedor->vendedor_nombre }}</div>
                                                    <small class="text-muted">{{ $vendedor->usuario }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $vendedor->total_pedidos }}</span>
                                        </td>
                                        <td class="fw-bold" style="color: var(--primary);">
                                            ${{ number_format($vendedor->total_ventas, 2) }}
                                        </td>
                                        <td>
                                            ${{ number_format($vendedor->promedio_venta, 2) }}
                                        </td>
                                        <td>
                                            <div class="progress-custom">
                                                <div class="progress-bar-custom" style="width: {{ $porcentaje }}%">
                                                    {{ number_format($porcentaje, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h5>No hay datos de vendedores</h5>
                            <p>No se encontraron ventas por vendedores en el período seleccionado en tu sucursal.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pestaña Métodos de Pago -->
            <div class="tab-pane fade {{ $tipo_reporte == 'pagos' ? 'show active' : '' }}" id="pagos" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-credit-card"></i> Métodos de Pago
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($ventas_por_metodo->count() > 0)
                        <div class="chart-container">
                            <canvas id="pagosChart"></canvas>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Método de Pago</th>
                                        <th>Pedidos</th>
                                        <th>Ventas Totales</th>
                                        <th>% del Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_ventas_metodos = $ventas_por_metodo->sum('total_ventas');
                                        $metodos_nombre = [
                                            'en_linea' => 'En línea',
                                            'efectivo' => 'Efectivo',
                                            'transferencia' => 'Transferencia'
                                        ];
                                    @endphp
                                    @foreach($ventas_por_metodo as $metodo)
                                        @php
                                            $porcentaje = $total_ventas_metodos > 0 ? ($metodo->total_ventas / $total_ventas_metodos * 100) : 0;
                                            $badge_class = [
                                                'en_linea' => 'badge-en-linea',
                                                'efectivo' => 'badge-efectivo',
                                                'transferencia' => 'badge-transferencia'
                                            ][$metodo->metodo_pago] ?? '';
                                        @endphp
                                    <tr>
                                        <td>
                                            <span class="badge-metodo {{ $badge_class }}">
                                                {{ $metodos_nombre[$metodo->metodo_pago] ?? $metodo->metodo_pago }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $metodo->total_pedidos }}</span>
                                        </td>
                                        <td class="fw-bold" style="color: var(--primary);">
                                            ${{ number_format($metodo->total_ventas, 2) }}
                                        </td>
                                        <td>
                                            <div class="progress-custom">
                                                <div class="progress-bar-custom" style="width: {{ $porcentaje }}%">
                                                    {{ number_format($porcentaje, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="empty-state">
                            <i class="fas fa-credit-card"></i>
                            <h5>No hay datos de pagos</h5>
                            <p>No se encontraron ventas por método de pago en el período seleccionado en tu sucursal.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Configuración de colores
        const chartColors = {
            primary: '#7fad39',
            success: '#28a745',
            info: '#17a2b8',
            warning: '#ffc107',
            danger: '#dc3545',
            secondary: '#6c757d',
            light: '#f8f9fa',
            dark: '#212529'
        };

        // Gráfico de Ventas
        @if($datos_ventas->count() > 0)
        document.addEventListener('DOMContentLoaded', function() {
            const ventasCtx = document.getElementById('ventasChart')?.getContext('2d');
            if (ventasCtx) {
                const ventasChart = new Chart(ventasCtx, {
                    type: 'line',
                    data: {
                        labels: [@foreach($datos_ventas->reverse() as $v) '{{ $v->fecha_dia->format('d/m') }}', @endforeach],
                        datasets: [{
                            label: 'Ventas Diarias ($)',
                            data: [@foreach($datos_ventas->reverse() as $v) {{ $v->total_ventas }}, @endforeach],
                            borderColor: chartColors.primary,
                            backgroundColor: chartColors.primary + '20',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: chartColors.primary,
                            pointBorderColor: 'white',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { color: chartColors.dark, font: { size: 12 } }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: chartColors.primary,
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        return `Ventas: $${context.raw.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: chartColors.light },
                                ticks: { color: chartColors.gray, font: { size: 11 } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: chartColors.light },
                                ticks: {
                                    color: chartColors.gray,
                                    font: { size: 11 },
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
        @endif

        // Gráfico de Productos
        @if($top_productos->count() > 0)
        document.addEventListener('DOMContentLoaded', function() {
            const productosCtx = document.getElementById('productosChart')?.getContext('2d');
            if (productosCtx) {
                const productosChart = new Chart(productosCtx, {
                    type: 'bar',
                    data: {
                        labels: [@foreach($top_productos as $p) '{{ $p->codigo }}', @endforeach],
                        datasets: [{
                            label: 'Unidades Vendidas',
                            data: [@foreach($top_productos as $p) {{ $p->total_vendido }}, @endforeach],
                            backgroundColor: [
                                chartColors.primary + '80',
                                chartColors.success + '80',
                                chartColors.info + '80',
                                chartColors.warning + '80',
                                chartColors.danger + '80',
                                chartColors.secondary + '80'
                            ],
                            borderColor: [
                                chartColors.primary,
                                chartColors.success,
                                chartColors.info,
                                chartColors.warning,
                                chartColors.danger,
                                chartColors.secondary
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { color: chartColors.dark, font: { size: 12 } }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: chartColors.gray, font: { size: 11 } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: chartColors.light },
                                ticks: { color: chartColors.gray, font: { size: 11 } }
                            }
                        }
                    }
                });
            }
        });
        @endif

        // Gráfico de Vendedores
        @if($ventas_por_vendedor->count() > 0)
        document.addEventListener('DOMContentLoaded', function() {
            const vendedoresCtx = document.getElementById('vendedoresChart')?.getContext('2d');
            if (vendedoresCtx) {
                const vendedoresChart = new Chart(vendedoresCtx, {
                    type: 'bar',
                    data: {
                        labels: [@foreach($ventas_por_vendedor as $v) '{{ substr($v->vendedor_nombre, 0, 15) }}', @endforeach],
                        datasets: [{
                            label: 'Ventas por Vendedor ($)',
                            data: [@foreach($ventas_por_vendedor as $v) {{ $v->total_ventas }}, @endforeach],
                            backgroundColor: chartColors.info + '80',
                            borderColor: chartColors.info,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { color: chartColors.dark, font: { size: 12 } }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: chartColors.gray, font: { size: 11 } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: chartColors.light },
                                ticks: {
                                    color: chartColors.gray,
                                    font: { size: 11 },
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
        @endif

        // Gráfico de Métodos de Pago
        @if($ventas_por_metodo->count() > 0)
        document.addEventListener('DOMContentLoaded', function() {
            const pagosCtx = document.getElementById('pagosChart')?.getContext('2d');
            if (pagosCtx) {
                const metodos_nombre = {
                    'en_linea': 'En línea',
                    'efectivo': 'Efectivo',
                    'transferencia': 'Transferencia'
                };
                const pagosChart = new Chart(pagosCtx, {
                    type: 'pie',
                    data: {
                        labels: [@foreach($ventas_por_metodo as $m) '{{ $metodos_nombre[$m->metodo_pago] ?? $m->metodo_pago }}', @endforeach],
                        datasets: [{
                            data: [@foreach($ventas_por_metodo as $m) {{ $m->total_ventas }}, @endforeach],
                            backgroundColor: [
                                chartColors.success + '80',
                                chartColors.info + '80',
                                chartColors.warning + '80'
                            ],
                            borderColor: [
                                chartColors.success,
                                chartColors.info,
                                chartColors.warning
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: { color: chartColors.dark, font: { size: 11 } }
                            }
                        }
                    }
                });
            }
        });
        @endif

        // Exportar reporte
        function exportarReporte() {
            Swal.fire({
                title: 'Exportar Reporte',
                html: `Exportar reporte de la sucursal <strong>{{ session('sucursal_nombre') }}</strong><br>
                       <small class="text-muted">Período: {{ Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ Carbon::parse($fecha_fin)->format('d/m/Y') }}</small>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: chartColors.primary,
                cancelButtonColor: chartColors.secondary,
                confirmButtonText: 'Excel',
                cancelButtonText: 'PDF',
                showDenyButton: true,
                denyButtonText: 'Imprimir',
                denyButtonColor: chartColors.info,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Exportar a Excel',
                        text: 'La funcionalidad de exportar a Excel estará disponible próximamente.',
                        icon: 'info',
                        confirmButtonColor: chartColors.primary
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: 'Exportar a PDF',
                        text: 'La funcionalidad de exportar a PDF estará disponible próximamente.',
                        icon: 'info',
                        confirmButtonColor: chartColors.primary
                    });
                } else if (result.isDenied) {
                    window.print();
                }
            });
        }
        
        // Efecto hover para tarjetas
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Mejorar experiencia de inputs
        document.querySelectorAll('input[type="date"], select').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = chartColors.primary;
                this.style.boxShadow = `0 0 0 2px ${chartColors.primary}20`;
            });
            
            input.addEventListener('blur', function() {
                this.style.borderColor = '';
                this.style.boxShadow = 'none';
            });
        });
        
        // SweetAlert2 para información
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('fecha_inicio') || urlParams.has('fecha_fin') || 
                              (urlParams.has('tipo_reporte') && urlParams.get('tipo_reporte') !== 'ventas');
            
            if (hasFilters) {
                setTimeout(() => {
                    Swal.fire({
                        title: 'Reporte Filtrado',
                        text: 'Mostrando resultados del período seleccionado',
                        icon: 'info',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }, 500);
            }
            
            // Mostrar alerta si no hay datos
            @if(empty($datos_ventas) && empty($top_productos) && empty($ventas_por_vendedor) && empty($ventas_por_metodo) && 
                ($fecha_inicio != date('Y-m-01') || $fecha_fin != date('Y-m-d')))
            setTimeout(() => {
                Swal.fire({
                    title: 'Sin Datos',
                    html: 'No se encontraron datos para el período seleccionado en tu sucursal.',
                    icon: 'warning',
                    confirmButtonColor: chartColors.warning,
                    confirmButtonText: 'Cambiar Fechas'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const today = new Date();
                        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                        const defaultUrl = '{{ route("gerente.reportes") }}?fecha_inicio=' + 
                                         firstDay.toISOString().split('T')[0] + 
                                         '&fecha_fin=' + today.toISOString().split('T')[0];
                        window.location.href = defaultUrl;
                    }
                });
            }, 1000);
            @endif
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