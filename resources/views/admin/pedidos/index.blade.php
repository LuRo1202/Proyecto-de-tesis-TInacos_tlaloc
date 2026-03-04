@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos - Tinacos Tláloc</title>
    
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
            -webkit-appearance: none;
            -moz-appearance: none;
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
        
        .badge-sucursal {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            background: #e3f2fd;
            color: #1976d2;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            white-space: nowrap;
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
        
        .btn-view {
            background: linear-gradient(135deg, var(--info), #138496);
        }
        
        .btn-edit {
            background: linear-gradient(135deg, var(--warning), #e0a800);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, var(--danger), #c82333);
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
        
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.6;
            filter: invert(0.5);
        }
        
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }
        
        select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 2px rgba(127, 173, 57, 0.1) !important;
        }
        
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
    @include('admin.layouts.sidebar')
    
    <div class="main-content">
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-shopping-cart me-2"></i>Gestión de Pedidos
                </h1>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('admin.dashboard') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
               
                @if(request()->has('estado') || request()->has('sucursal_id') || request()->has('fecha'))
                <a href="{{ route('admin.pedidos') }}" class="btn-custom btn-primary-custom">
                    <i class="fas fa-times"></i> Limpiar
                </a>
                @endif
            </div>
        </div>

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
                <div class="stat-value">{{ (int)$pedidos->count() }}</div>
                <div class="stat-label">
                    <i class="fas fa-list"></i>
                    Total Pedidos
                </div>
            </div>
        </div>

        <div class="filter-card">
            <h3 class="filter-title">
                <i class="fas fa-filter"></i> Filtros Rápidos
            </h3>
            
            <form method="GET" action="{{ route('admin.pedidos') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-tag"></i> Estado
                    </label>
                    <select name="estado" class="form-control-sm">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="enviado" {{ request('estado') == 'enviado' ? 'selected' : '' }}>Enviado</option>
                        <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-store"></i> Sucursal
                    </label>
                    <select name="sucursal_id" class="form-control-sm">
                        <option value="">Todas las sucursales</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}" {{ request('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                {{ $sucursal->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        <i class="fas fa-calendar-day"></i> Fecha
                    </label>
                    <input type="date" name="fecha" class="form-control-sm" 
                           value="{{ request('fecha') }}" placeholder="dd/mm/aaaa">
                </div>
                
                <div class="col-12 mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="submit" class="btn-custom btn-primary-custom me-2">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            
                            @if(request()->has('estado') || request()->has('sucursal_id') || request()->has('fecha'))
                            <a href="{{ route('admin.pedidos') }}" class="btn-custom btn-secondary-custom">
                                <i class="fas fa-undo-alt"></i> Limpiar
                            </a>
                            @endif
                        </div>
                        
                        <div class="text-muted small">
                            <i class="fas fa-chart-bar"></i>
                            Mostrando <strong>{{ $pedidos->count() }}</strong> pedidos | 
                            Total: <strong>${{ number_format($total_general, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-list-ol"></i> Lista de Pedidos
                    <span class="badge bg-primary">{{ $pedidos->count() }}</span>
                </h5>
                
                @if($pedidos->count() > 0)
                <div class="text-muted small">
                    <i class="fas fa-money-bill-wave"></i>
                    Total general: <strong>${{ number_format($total_general, 2) }}</strong>
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
                                <th>Sucursal</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Pago</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pedidos as $pedido)
                            <tr onclick="window.location.href='{{ route('admin.pedidos.ver', $pedido->id) }}';">
                                <td>
                                    <strong class="text-primary">#{{ $pedido->folio }}</strong>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-box"></i>
                                        {{ $pedido->items_count }} items
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
                                    <span class="badge-sucursal">
                                        <i class="fas fa-store"></i>
                                        {{ $pedido->sucursal->nombre ?? 'N/A' }}
                                    </span>
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
                                        <a href="{{ route('admin.pedidos.ver', $pedido->id) }}" 
                                           class="btn-action btn-view" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.pedidos.editar', $pedido->id) }}" 
                                           class="btn-action btn-edit" 
                                           title="Editar pedido">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-delete" 
                                                title="Eliminar pedido" 
                                                onclick="eliminarPedido({{ $pedido->id }}, '{{ $pedido->folio }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h5>No se encontraron pedidos</h5>
                                    <p>{{ request()->has('estado') || request()->has('sucursal_id') || request()->has('fecha') ? 
                                        'No hay pedidos que coincidan con los filtros aplicados.' : 
                                        'Aún no hay pedidos registrados en el sistema.' }}</p>
                                    
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // ✅ Inicializar DataTable solo si hay datos
        $(document).ready(function() {
            @if($pedidos->count() > 0)
            $('#tablaPedidos').DataTable({
                language: {
                    "sProcessing":     "Procesando...",
                    "sLengthMenu":     "Mostrar _MENU_ registros",
                    "sZeroRecords":    "No se encontraron resultados",
                    "sEmptyTable":     "Ningún dato disponible en esta tabla",
                    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix":    "",
                    "sSearch":         "Buscar:",
                    "sUrl":            "",
                    "sInfoThousands":  ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst":    "Primero",
                        "sLast":     "Último",
                        "sNext":     "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },
                pageLength: 25,
                order: [[3, 'desc']],
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>tip',
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 7 },
                    { responsivePriority: 3, targets: 4 }
                ]
            });
            @endif
        });
        
        function eliminarPedido(id, folio) {
            event.stopPropagation();
            
            Swal.fire({
                title: '¿Eliminar Pedido?',
                html: `¿Estás seguro de eliminar el pedido <strong>#${folio}</strong>?<br><small>Esta acción no se puede deshacer.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ url("admin/pedidos") }}/' + id + '/eliminar';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('estado') || urlParams.has('sucursal_id') || urlParams.has('fecha');
            
            if (hasFilters) {
                setTimeout(() => {
                    Swal.fire({
                        title: 'Filtros Aplicados',
                        text: 'Mostrando resultados filtrados',
                        icon: 'info',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }, 500);
            }
            
            @if($pedidos->count() === 0 && (request()->has('estado') || request()->has('sucursal_id') || request()->has('fecha')))
            setTimeout(() => {
                Swal.fire({
                    title: 'Sin Resultados',
                    html: 'No se encontraron pedidos con los filtros aplicados.',
                    icon: 'warning',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Limpiar Filtros'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("admin.pedidos") }}';
                    }
                });
            }, 1000);
            @endif
        });
        
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
    </script>

    {{-- SweetAlert para mensajes flash --}}
    @if(session('swal_pedido'))
    <script>
        Swal.fire({
            icon: '{{ session('swal_pedido')['type'] }}',
            title: '{{ session('swal_pedido')['title'] }}',
            text: '{{ session('swal_pedido')['message'] }}',
            confirmButtonColor: '#7fad39',
            confirmButtonText: 'Aceptar'
        });
    </script>
    @endif

    {{-- También mantener swal genérico por si acaso --}}
    @if(session('swal'))
    <script>
        Swal.fire({
            icon: '{{ session('swal')['type'] }}',
            title: '{{ session('swal')['title'] }}',
            text: '{{ session('swal')['message'] }}',
            confirmButtonColor: '#7fad39',
            confirmButtonText: 'Aceptar'
        });
    </script>
    @endif
</body>
</html>