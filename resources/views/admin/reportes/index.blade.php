<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes y Estadísticas - Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    @stack('styles')
    
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
        
        /* Header Compacto */
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
        
        /* Selects mejorados */
        select.form-control-sm {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236c757d' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 12px 8px;
            padding-right: 2rem;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
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
        .badge-manual { 
            background: rgba(23, 162, 184, 0.1); 
            color: #0c5460; 
            border: 1px solid rgba(23, 162, 184, 0.2);
        }
        .badge-otro { 
            background: rgba(255, 193, 7, 0.1); 
            color: #856404; 
            border: 1px solid rgba(255, 193, 7, 0.2);
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
        
        /* Mejora para inputs */
        input[type="date"]:focus,
        select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 2px rgba(127, 173, 57, 0.1) !important;
        }

        /* Botones de filtros compactos */
        .btn-filter {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.2s ease;
            color: white;
            font-size: 0.85rem;
            cursor: pointer;
        }

        .btn-filter:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        }

        .btn-filter-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        .btn-filter-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
        }
    </style>
</head>
<body>
    @include('admin.layouts.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-chart-line me-2"></i>Reportes y Estadísticas
                </h1>
                <p class="text-muted mb-0 small">
                    @if($sucursal && auth()->user()->rol !== 'admin')
                        Sucursal: <strong>{{ $sucursal->nombre }}</strong>
                    @elseif($sucursal_id !== 'todas' && is_numeric($sucursal_id))
                        @php $sucursalSeleccionada = \App\Models\Sucursal::find($sucursal_id); @endphp
                        @if($sucursalSeleccionada)
                            Sucursal: <strong>{{ $sucursalSeleccionada->nombre }}</strong>
                        @endif
                    @else
                        <span class="text-info">Administrador - Ver todas las sucursales</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card stat-pedidos">
                <div class="stat-value">{{ $estadisticas['total_pedidos'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-shopping-cart"></i>
                    Total Pedidos
                </div>
            </div>
            
            <div class="stat-card stat-ventas">
                <div class="stat-value">${{ number_format($estadisticas['total_ventas'], 0) }}</div>
                <div class="stat-label">
                    <i class="fas fa-money-bill-wave"></i>
                    Ventas Totales
                </div>
            </div>
            
            <div class="stat-card stat-promedio">
                <div class="stat-value">${{ number_format($estadisticas['promedio_venta'], 0) }}</div>
                <div class="stat-label">
                    <i class="fas fa-chart-line"></i>
                    Venta Promedio
                </div>
            </div>
            
            <div class="stat-card stat-clientes">
                <div class="stat-value">{{ $estadisticas['clientes_unicos'] }}</div>
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
            
            <form method="GET" class="row g-2">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control-sm" value="{{ $fecha_inicio }}">
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control-sm" value="{{ $fecha_fin }}">
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Sucursal</label>
                    <select name="sucursal_id" class="form-control-sm">
                        @if(auth()->user()->rol === 'admin')
                            <option value="todas" {{ $sucursal_id == 'todas' ? 'selected' : '' }}>Todas las sucursales</option>
                        @endif
                        @foreach($sucursales as $s)
                            <option value="{{ $s->id }}" {{ $sucursal_id == $s->id ? 'selected' : '' }}>
                                {{ $s->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Tipo de Reporte</label>
                    <select name="tipo_reporte" class="form-control-sm">
                        <option value="ventas" {{ $tipo_reporte == 'ventas' ? 'selected' : '' }}>Ventas</option>
                        <option value="productos" {{ $tipo_reporte == 'productos' ? 'selected' : '' }}>Productos</option>
                        <option value="pagos" {{ $tipo_reporte == 'pagos' ? 'selected' : '' }}>Métodos de Pago</option>
                        <option value="sucursales" {{ $tipo_reporte == 'sucursales' ? 'selected' : '' }}>Sucursales</option>
                        <option value="vendedores" {{ $tipo_reporte == 'vendedores' ? 'selected' : '' }}>Vendedores</option>
                    </select>
                </div>
                
                <div class="col-12 mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn-custom btn-primary-custom">
                            <i class="fas fa-filter"></i> Generar Reporte
                        </button>
                        
                        <div class="text-muted small">
                            <i class="fas fa-calendar"></i>
                            {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
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
                <button class="nav-link {{ $tipo_reporte == 'pagos' ? 'active' : '' }}" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#pagos" type="button">
                    <i class="fas fa-credit-card me-1"></i> Pagos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $tipo_reporte == 'sucursales' ? 'active' : '' }}" id="sucursales-tab" data-bs-toggle="tab" data-bs-target="#sucursales" type="button">
                    <i class="fas fa-store me-1"></i> Sucursales
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $tipo_reporte == 'vendedores' ? 'active' : '' }}" id="vendedores-tab" data-bs-toggle="tab" data-bs-target="#vendedores" type="button">
                    <i class="fas fa-user-tie me-1"></i> Vendedores
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
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="ventasChart"></canvas>
                        </div>
                        
                        @if($datos_ventas->isNotEmpty())
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
                                            {{ \Carbon\Carbon::parse($venta->fecha_dia)->format('d/m/Y') }}
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
                            <h5>No hay datos de ventas</h5>
                            <p>No se encontraron ventas confirmadas en el período seleccionado</p>
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
                        <div class="chart-container">
                            <canvas id="productosChart"></canvas>
                        </div>
                        
                        @if($top_productos->isNotEmpty() && $top_productos[0]->total_vendido > 0)
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
                                        @if($producto->total_vendido == 0) @continue @endif
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $producto->nombre ?? 'Producto no encontrado' }}</div>
                                            <small class="text-muted">{{ $producto->litros }} litros</small>
                                        </td>
                                        <td>
                                            <code>{{ $producto->codigo ?? 'N/A' }}</code>
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
                            <h5>No hay datos de productos</h5>
                            <p>No se encontraron productos vendidos en el período seleccionado</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pestaña Geografía -->
            <div class="tab-pane fade {{ $tipo_reporte == 'geografia' ? 'show active' : '' }}" id="geografia" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-map"></i> Ventas por Estado
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="geografiaChart"></canvas>
                        </div>
                        
                        @if($ventas_por_estado->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>Pedidos</th>
                                        <th>Ventas Totales</th>
                                        <th>% del Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total_ventas_estados = $ventas_por_estado->sum('total_ventas'); @endphp
                                    @foreach($ventas_por_estado as $estado)
                                    @php $porcentaje = $total_ventas_estados > 0 ? ($estado->total_ventas / $total_ventas_estados * 100) : 0; @endphp
                                    <tr>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                            {{ $estado->cliente_estado }}
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $estado->total_pedidos }}</span>
                                        </td>
                                        <td class="fw-bold" style="color: var(--primary);">
                                            ${{ number_format($estado->total_ventas, 2) }}
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
                            <i class="fas fa-map"></i>
                            <h5>No hay datos geográficos</h5>
                            <p>No se encontraron ventas por estado en el período seleccionado</p>
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
                        <div class="chart-container">
                            <canvas id="pagosChart"></canvas>
                        </div>
                        
                        @if($ventas_por_metodo->isNotEmpty())
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
                                    @php $total_ventas_metodos = $ventas_por_metodo->sum('total_ventas'); @endphp
                                    @foreach($ventas_por_metodo as $metodo)
                                    @php 
                                        $porcentaje = $total_ventas_metodos > 0 ? ($metodo->total_ventas / $total_ventas_metodos * 100) : 0;
                                        $badge_class = [
                                            'en_linea' => 'badge-en-linea',
                                            'manual' => 'badge-manual',
                                            'otro' => 'badge-otro'
                                        ][$metodo->metodo_pago] ?? 'badge-otro';
                                        $metodos_nombre = [
                                            'en_linea' => 'En línea',
                                            'manual' => 'Manual',
                                            'otro' => 'Otro'
                                        ];
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="badge-metodo {{ $badge_class }}">
                                                {{ $metodos_nombre[$metodo->metodo_pago] ?? 'Otro' }}
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
                            <p>No se encontraron ventas por método de pago en el período seleccionado</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pestaña Sucursales -->
            <div class="tab-pane fade {{ $tipo_reporte == 'sucursales' ? 'show active' : '' }}" id="sucursales" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-store"></i> Ventas por Sucursal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="sucursalesChart"></canvas>
                        </div>
                        
                        @if($ventas_por_sucursal->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Sucursal</th>
                                        <th>Pedidos</th>
                                        <th>Ventas Totales</th>
                                        <th>% del Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total_ventas_sucursales = $ventas_por_sucursal->sum('total_ventas'); @endphp
                                    @foreach($ventas_por_sucursal as $sucursal_item)
                                    @php $porcentaje = $total_ventas_sucursales > 0 ? ($sucursal_item->total_ventas / $total_ventas_sucursales * 100) : 0; @endphp
                                    <tr>
                                        <td>
                                            <i class="fas fa-store text-muted me-1"></i>
                                            {{ $sucursal_item->sucursal }}
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $sucursal_item->total_pedidos }}</span>
                                        </td>
                                        <td class="fw-bold" style="color: var(--primary);">
                                            ${{ number_format($sucursal_item->total_ventas, 2) }}
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
                            <i class="fas fa-store"></i>
                            <h5>No hay datos de sucursales</h5>
                            <p>No se encontraron ventas por sucursal en el período seleccionado</p>
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
                            <i class="fas fa-user-tie"></i> Vendedores Más Activos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="vendedoresChart"></canvas>
                        </div>
                        
                        @if($top_vendedores->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Vendedor</th>
                                        <th>Pedidos Gestionados</th>
                                        <th>Ventas Gestionadas</th>
                                        <th>% del Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total_ventas_vendedores = $top_vendedores->sum('total_ventas_gestionadas'); @endphp
                                    @foreach($top_vendedores as $vendedor)
                                    @php $porcentaje = $total_ventas_vendedores > 0 ? ($vendedor->total_ventas_gestionadas / $total_ventas_vendedores * 100) : 0; @endphp
                                    <tr>
                                        <td>
                                            <i class="fas fa-user-tie text-muted me-1"></i>
                                            {{ $vendedor->vendedor ?? 'Sin asignar' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $vendedor->total_pedidos_asignados }}</span>
                                        </td>
                                        <td class="fw-bold" style="color: var(--primary);">
                                            ${{ number_format($vendedor->total_ventas_gestionadas, 2) }}
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
                            <i class="fas fa-user-tie"></i>
                            <h5>No hay datos de vendedores</h5>
                            <p>No se encontraron vendedores activos en el período seleccionado</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
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

        @if($datos_ventas->isNotEmpty())
        // Gráfico de Ventas
        const ventasCtx = document.getElementById('ventasChart')?.getContext('2d');
        if (ventasCtx) {
            const ventasChart = new Chart(ventasCtx, {
                type: 'line',
                data: {
                    labels: [@foreach($datos_ventas->sortBy('fecha_dia') as $venta) '{{ \Carbon\Carbon::parse($venta->fecha_dia)->format('d/m') }}', @endforeach],
                    datasets: [{
                        label: 'Ventas Diarias ($)',
                        data: [@foreach($datos_ventas->sortBy('fecha_dia') as $venta) {{ $venta->total_ventas }}, @endforeach],
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
                            labels: {
                                color: chartColors.dark,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: chartColors.primary,
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.raw.toLocaleString();
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
        @endif

        @if($top_productos->isNotEmpty() && $top_productos[0]->total_vendido > 0)
        // Gráfico de Productos
        const productosCtx = document.getElementById('productosChart')?.getContext('2d');
        if (productosCtx) {
            const productosChart = new Chart(productosCtx, {
                type: 'bar',
                data: {
                    labels: [@foreach($top_productos as $p) @if($p->total_vendido > 0) '{{ $p->codigo ?? 'N/A' }}', @endif @endforeach],
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: [@foreach($top_productos as $p) @if($p->total_vendido > 0) {{ $p->total_vendido }}, @endif @endforeach],
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
                            labels: {
                                color: chartColors.dark,
                                font: { size: 12 }
                            }
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
        @endif

        @if($ventas_por_estado->isNotEmpty())
        // Gráfico de Geografía
        const geografiaCtx = document.getElementById('geografiaChart')?.getContext('2d');
        if (geografiaCtx) {
            const geografiaChart = new Chart(geografiaCtx, {
                type: 'doughnut',
                data: {
                    labels: [@foreach($ventas_por_estado as $e) '{{ $e->cliente_estado }}', @endforeach],
                    datasets: [{
                        data: [@foreach($ventas_por_estado as $e) {{ $e->total_ventas }}, @endforeach],
                        backgroundColor: [
                            chartColors.primary,
                            chartColors.success,
                            chartColors.info,
                            chartColors.warning,
                            chartColors.danger,
                            chartColors.secondary,
                            '#6f42c1',
                            '#e83e8c',
                            '#fd7e14',
                            '#20c997'
                        ],
                        borderWidth: 2,
                        borderColor: 'white'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                color: chartColors.dark,
                                font: { size: 11 },
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        @endif

        @if($ventas_por_metodo->isNotEmpty())
        // Gráfico de Métodos de Pago
        const pagosCtx = document.getElementById('pagosChart')?.getContext('2d');
        if (pagosCtx) {
            const pagosChart = new Chart(pagosCtx, {
                type: 'pie',
                data: {
                    labels: [@foreach($ventas_por_metodo as $m) 
                        @php
                            $metodos_nombre = [
                                'en_linea' => 'En línea',
                                'manual' => 'Manual',
                                'otro' => 'Otro'
                            ];
                        @endphp
                        '{{ $metodos_nombre[$m->metodo_pago] ?? 'Otro' }}',
                    @endforeach],
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
                            labels: {
                                color: chartColors.dark,
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });
        }
        @endif

        @if($ventas_por_sucursal->isNotEmpty())
        // Gráfico de Sucursales
        const sucursalesCtx = document.getElementById('sucursalesChart')?.getContext('2d');
        if (sucursalesCtx) {
            const sucursalesChart = new Chart(sucursalesCtx, {
                type: 'doughnut',
                data: {
                    labels: [@foreach($ventas_por_sucursal as $s) '{{ $s->sucursal }}', @endforeach],
                    datasets: [{
                        data: [@foreach($ventas_por_sucursal as $s) {{ $s->total_ventas }}, @endforeach],
                        backgroundColor: [
                            chartColors.primary,
                            chartColors.success,
                            chartColors.info,
                            chartColors.warning,
                            chartColors.danger
                        ],
                        borderWidth: 2,
                        borderColor: 'white'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                color: chartColors.dark,
                                font: { size: 11 },
                                padding: 15
                            }
                        }
                    }
                }
            });
        }
        @endif

        @if($top_vendedores->isNotEmpty())
        // Gráfico de Vendedores
        const vendedoresCtx = document.getElementById('vendedoresChart')?.getContext('2d');
        if (vendedoresCtx) {
            const vendedoresChart = new Chart(vendedoresCtx, {
                type: 'bar',
                data: {
                    labels: [@foreach($top_vendedores as $v) '{{ $v->vendedor ?? 'Sin asignar' }}', @endforeach],
                    datasets: [{
                        label: 'Ventas Gestionadas ($)',
                        data: [@foreach($top_vendedores as $v) {{ $v->total_ventas_gestionadas }}, @endforeach],
                        backgroundColor: chartColors.primary + '80',
                        borderColor: chartColors.primary,
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
                            labels: {
                                color: chartColors.dark,
                                font: { size: 12 }
                            }
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
        @endif

        // Efecto hover para tarjetas de estadísticas
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
        
        // SweetAlert2 para filtros aplicados
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('fecha_inicio') || urlParams.has('fecha_fin') || 
                              urlParams.has('tipo_reporte') && urlParams.get('tipo_reporte') !== 'ventas';
            
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
            
            @if($datos_ventas->isEmpty() && $top_productos->isEmpty() && $ventas_por_estado->isEmpty() && 
                $ventas_por_metodo->isEmpty() && ($fecha_inicio != date('Y-m-01') || $fecha_fin != date('Y-m-d')))
            setTimeout(() => {
                Swal.fire({
                    title: 'Sin Datos',
                    html: 'No se encontraron datos para el período seleccionado.',
                    icon: 'warning',
                    confirmButtonColor: chartColors.warning,
                    confirmButtonText: 'Cambiar Fechas'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("admin.reportes") }}';
                    }
                });
            }, 1000);
            @endif
        });
    </script>
</body>
</html>