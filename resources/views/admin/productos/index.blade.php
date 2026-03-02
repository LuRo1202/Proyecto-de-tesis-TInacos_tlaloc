@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Tanques Tláloc</title>
    
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
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--success), #218838);
            color: white;
        }
        
        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning), #e0a800);
            color: #000;
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
        
        .stat-total { border-top-color: var(--primary); }
        .stat-bajos { border-top-color: var(--warning); }
        .stat-valor { border-top-color: var(--success); }
        .stat-sin-stock { border-top-color: var(--danger); }
        
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
        
        .alert-stock {
            background: white;
            border-left: 4px solid var(--warning);
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-stock i {
            color: var(--warning);
            font-size: 1.2rem;
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
        
        .badge-stock {
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        
        .badge-stock-alto { 
            background: rgba(40, 167, 69, 0.1); 
            color: var(--success); 
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        .badge-stock-medio { 
            background: rgba(255, 193, 7, 0.1); 
            color: #856404; 
            border: 1px solid rgba(255, 193, 7, 0.2);
        }
        .badge-stock-bajo { 
            background: rgba(220, 53, 69, 0.1); 
            color: var(--danger); 
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .badge-estado {
            padding: 3px 6px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        
        .badge-activo {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .badge-destacado {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
            border: 1px solid rgba(255, 193, 7, 0.2);
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
        
        .producto-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid var(--light-gray);
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .producto-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .producto-img i {
            color: var(--gray);
            font-size: 1.2rem;
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
        
        .sucursal-actual {
            background: rgba(127, 173, 57, 0.1);
            border: 1px solid rgba(127, 173, 57, 0.2);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.8rem;
            color: var(--primary-dark);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-left: 10px;
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
            
            .card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
            
            .producto-img {
                width: 50px;
                height: 50px;
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
            
            .producto-img {
                width: 40px;
                height: 40px;
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
            
            .producto-img {
                width: 35px;
                height: 35px;
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
        
        input[type="text"]:focus,
        select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 2px rgba(127, 173, 57, 0.1) !important;
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
    <!-- Incluimos el sidebar -->
    @include('admin.layouts.sidebar')
    
    <div class="main-content">
        <div class="header-bar">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <h1 class="header-title mb-0">
                    <i class="fas fa-box me-2"></i>Gestión de Productos
                </h1>
                @php
                    $sucursal_nombre = \App\Models\Sucursal::find($sucursal_id)->nombre ?? 'Todas';
                @endphp
                @if($sucursal_id)
                <span class="sucursal-actual">
                    <i class="fas fa-store"></i> {{ $sucursal_nombre }}
                </span>
                @endif
            </div>
            
            <div class="header-actions">
                <a href="{{ route('admin.productos.nuevo', ['sucursal_id' => $sucursal_id]) }}" class="btn-custom btn-success-custom">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </a>
                @if(request()->has('sucursal_id') || request()->has('categoria') || request()->has('busqueda') || request()->has('stock_bajo'))
                <a href="{{ route('admin.productos') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-times"></i> Limpiar
                </a>
                @endif
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card stat-total">
                <div class="stat-value">{{ $total_productos }}</div>
                <div class="stat-label">
                    <i class="fas fa-box"></i>
                    Total Productos
                </div>
            </div>
            
            <div class="stat-card stat-bajos">
                <div class="stat-value">{{ $productos_bajos }}</div>
                <div class="stat-label">
                    <i class="fas fa-exclamation-triangle"></i>
                    Stock Bajo
                </div>
            </div>
            
            <div class="stat-card stat-valor">
                <div class="stat-value">${{ number_format($valor_inventario, 0) }}</div>
                <div class="stat-label">
                    <i class="fas fa-money-bill-wave"></i>
                    Valor Inventario
                </div>
            </div>
            
            <div class="stat-card stat-sin-stock">
                <div class="stat-value">{{ $sin_stock }}</div>
                <div class="stat-label">
                    <i class="fas fa-times-circle"></i>
                    Sin Stock
                </div>
            </div>
        </div>

        @if($productos_bajos > 0)
        <div class="alert-stock">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="flex-grow-1">
                <div class="fw-semibold">¡Atención!</div>
                <div class="small">
                    Tienes <strong>{{ $productos_bajos }} producto(s)</strong> con inventario bajo.
                    <a href="{{ route('admin.productos', array_merge(request()->all(), ['stock_bajo' => 1])) }}" class="text-primary text-decoration-none ms-1">
                        <i class="fas fa-filter fa-xs"></i> Ver stock bajo
                    </a>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="filter-card">
            <h3 class="filter-title">
                <i class="fas fa-filter"></i> Filtros de Búsqueda
            </h3>
            
            <form method="GET" action="{{ route('admin.productos') }}" class="row g-2">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Sucursal</label>
                    <select name="sucursal_id" class="form-control-sm" id="selectSucursal">
                        <option value="">Todas las sucursales</option>
                        @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" {{ request('sucursal_id', $sucursal_id) == $sucursal->id ? 'selected' : '' }}>
                            {{ $sucursal->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold">Categoría</label>
                    <select name="categoria" class="form-control-sm">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4 col-sm-6">
                    <label class="form-label small fw-semibold">Buscar Producto</label>
                    <input type="text" name="busqueda" class="form-control-sm" 
                           placeholder="Código o nombre..." 
                           value="{{ request('busqueda') }}">
                </div>
                
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-semibold">Stock</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="stock_bajo" id="stockBajo" 
                               {{ request('stock_bajo') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="stockBajo">
                            Solo stock bajo
                        </label>
                    </div>
                </div>
                
                <div class="col-12 mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn-custom btn-primary-custom">
                            <i class="fas fa-filter"></i> Aplicar Filtros
                        </button>
                        
                        <div class="text-muted small">
                            <i class="fas fa-info-circle"></i>
                            Mostrando <strong>{{ $total_productos }}</strong> productos
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-list-ol"></i> Lista de Productos
                    <span class="badge bg-primary">{{ $total_productos }}</span>
                </h5>
                
                @if($total_productos > 0)
                <div class="text-muted small">
                    <i class="fas fa-money-bill-wave"></i>
                    Valor total: <strong>${{ number_format($valor_inventario, 2) }}</strong>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="60"></th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos_paginados as $producto)
                            @php
                                $stock_class = 'badge-stock-alto';
                                if ($producto->existencias <= 5) {
                                    $stock_class = $producto->existencias == 0 ? 'badge-stock-bajo' : 'badge-stock-medio';
                                }
                            @endphp
                            <tr onclick="window.location.href='{{ route('admin.productos.editar', array_merge(request()->all(), ['id' => $producto->id])) }}';">
                                <td>
                                    <div class="producto-img">
                                        @php
                                            $imagen_path = public_path('assets/img/productos/' . $producto->codigo . '.jpg');
                                        @endphp
                                        @if(file_exists($imagen_path))
                                            <img src="{{ asset('assets/img/productos/' . $producto->codigo . '.jpg') }}?v={{ time() }}" alt="{{ $producto->nombre }}">
                                        @else
                                            <i class="fas fa-box"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong class="text-primary">{{ $producto->codigo }}</strong>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $producto->nombre }}</div>
                                    <small class="text-muted">
                                        {{ $producto->litros }} litros
                                    </small>
                                </td>
                                <td>
                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </td>
                                <td class="fw-bold" style="color: var(--primary);">
                                    ${{ number_format($producto->precio, 2) }}
                                </td>
                                <td>
                                    <span class="badge-stock {{ $stock_class }}">
                                        <i class="fas fa-box fa-xs"></i>
                                        {{ $producto->existencias }} unidades
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($producto->activo)
                                            <span class="badge-estado badge-activo">
                                                <i class="fas fa-check-circle fa-xs"></i> Activo
                                            </span>
                                        @else
                                            <span class="badge-estado" style="background: rgba(108, 117, 125, 0.1); color: var(--gray);">
                                                <i class="fas fa-times-circle fa-xs"></i> Inactivo
                                            </span>
                                        @endif
                                        
                                        @if($producto->destacado)
                                            <span class="badge-estado badge-destacado">
                                                <i class="fas fa-star fa-xs"></i> Destacado
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons" onclick="event.stopPropagation();">
                                        <a href="{{ route('admin.productos.editar', array_merge(request()->all(), ['id' => $producto->id])) }}" 
                                           class="btn-action btn-edit" 
                                           title="Editar producto">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-delete" 
                                                title="Eliminar producto" 
                                                onclick="confirmarEliminar({{ $producto->id }}, '{{ $producto->nombre }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <i class="fas fa-box"></i>
                                    <h5>No se encontraron productos</h5>
                                    <p>{{ request()->has('sucursal_id') || request()->has('categoria') || request()->has('busqueda') || request()->has('stock_bajo') ? 
                                        'No hay productos que coincidan con los filtros aplicados.' : 
                                        'Aún no hay productos registrados en el sistema.' }}</p>
                                    <a href="{{ route('admin.productos.nuevo', ['sucursal_id' => $sucursal_id]) }}" class="btn-custom btn-primary-custom mt-2">
                                        <i class="fas fa-plus"></i> Agregar Producto
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($total_paginas > 1)
            <div class="card-footer">
                <nav aria-label="Paginación">
                    <ul class="pagination pagination-custom justify-content-center mb-0">
                        @if($pagina > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ route('admin.productos', array_merge(request()->all(), ['pagina' => $pagina-1])) }}">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        @endif
                        
                        @php
                            $inicio = max(1, $pagina - 2);
                            $fin = min($total_paginas, $pagina + 2);
                        @endphp
                        
                        @for($i = $inicio; $i <= $fin; $i++)
                            @if($i == $pagina)
                            <li class="page-item active">
                                <span class="page-link">{{ $i }}</span>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link" href="{{ route('admin.productos', array_merge(request()->all(), ['pagina' => $i])) }}">
                                    {{ $i }}
                                </a>
                            </li>
                            @endif
                        @endfor
                        
                        @if($pagina < $total_paginas)
                        <li class="page-item">
                            <a class="page-link" href="{{ route('admin.productos', array_merge(request()->all(), ['pagina' => $pagina+1])) }}">
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

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function confirmarEliminar(id, nombre) {
            event.stopPropagation();
            
            Swal.fire({
                title: '¿Eliminar Producto Permanentemente?',
                html: `¿Estás seguro de eliminar permanentemente el producto <strong>"${nombre}"</strong>?<br><small class="text-danger">Esta acción NO se puede deshacer.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    var url = '{{ route("admin.productos.destroy", ":id") }}';
                    url = url.replace(':id', id);
                    
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: data.message || 'Producto eliminado permanentemente.',
                                icon: 'success',
                                confirmButtonColor: '#7fad39',
                                confirmButtonText: 'Aceptar',
                                timer: 3000,
                                timerProgressBar: true
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.error || 'No se pudo eliminar el producto.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error de Conexión',
                            text: 'No se pudo conectar con el servidor.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('sucursal_id') || urlParams.has('categoria') || urlParams.has('busqueda') || urlParams.has('stock_bajo');
            
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
            
            @if($total_productos === 0 && (request()->has('sucursal_id') || request()->has('categoria') || request()->has('busqueda') || request()->has('stock_bajo')))
            setTimeout(() => {
                Swal.fire({
                    title: 'Sin Resultados',
                    html: 'No se encontraron productos con los filtros aplicados.',
                    icon: 'warning',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Limpiar Filtros'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("admin.productos") }}';
                    }
                });
            }, 1000);
            @endif
            
            // Script para mantener el scroll después de recargar
            if (sessionStorage.getItem('scrollPosition')) {
                window.scrollTo(0, sessionStorage.getItem('scrollPosition'));
                sessionStorage.removeItem('scrollPosition');
            }
            
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function() {
                    sessionStorage.setItem('scrollPosition', window.scrollY);
                });
            });
        });
        
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('.action-buttons')) {
                    const editLink = this.querySelector('.btn-edit');
                    if (editLink) {
                        window.location.href = editLink.href;
                    }
                }
            });
        });
        
        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('focus', function() {
                this.style.borderColor = 'var(--primary)';
                this.style.boxShadow = '0 0 0 2px rgba(127, 173, 57, 0.1)';
            });
            
            select.addEventListener('blur', function() {
                this.style.borderColor = 'var(--light-gray)';
                this.style.boxShadow = 'none';
            });
        });
        
        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = 'var(--primary)';
                this.style.boxShadow = '0 0 0 2px rgba(127, 173, 57, 0.1)';
            });
            
            input.addEventListener('blur', function() {
                this.style.borderColor = 'var(--light-gray)';
                this.style.boxShadow = 'none';
            });
        });

        // Logout con SweetAlert
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: '¿Cerrar Sesión?',
                    html: '<div style="text-align: center;">' +
                          '<i class="fas fa-sign-out-alt" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>' +
                          '<p style="margin-bottom: 0.5rem;">¿Estás seguro de que deseas salir del sistema?</p>' +
                          '<small style="color: #6c757d;">Tu sesión actual se cerrará y serás redirigido al inicio de sesión.</small>' +
                          '</div>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Sí, cerrar sesión',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    reverseButtons: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Cerrando sesión...',
                            text: 'Por favor espera un momento',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        setTimeout(() => {
                            document.getElementById('logout-form').submit();
                        }, 500);
                    }
                });
            });
        }
    </script>
</body>
</html>