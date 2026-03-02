@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vendedores - Sucursal {{ session('sucursal_nombre') }}</title>
    
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
        
        /* Header Compacto con Sucursal */
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
        
        .btn-info-custom {
            background: linear-gradient(135deg, var(--info), #138496);
            color: white;
        }
        
        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning), #e0a800);
            color: #000;
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, var(--danger), #c82333);
            color: white;
        }
        
        /* Cards de Estadísticas */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .stat-icon.vendedores { background: rgba(52, 152, 219, 0.1); color: #3498db; }
        .stat-icon.activos { background: rgba(40, 167, 69, 0.1); color: #28a745; }
        .stat-icon.pedidos { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
        .stat-icon.ventas { background: rgba(127, 173, 57, 0.1); color: var(--primary); }
        
        .stat-label {
            font-size: 0.75rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
        }
        
        .stat-value {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .stat-change {
            font-size: 0.7rem;
            color: var(--gray);
        }
        
        /* Card */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            margin-bottom: 15px;
            background: white;
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
        
        /* Tabla Compacta */
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
        
        /* Badges Compactos */
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
        
        .badge-activo { 
            background: rgba(40, 167, 69, 0.1); 
            color: var(--success); 
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .badge-inactivo { 
            background: rgba(108, 117, 125, 0.1); 
            color: var(--gray); 
            border: 1px solid rgba(108, 117, 125, 0.2);
        }
        
        .badge-pedidos { 
            background: rgba(255, 193, 7, 0.1); 
            color: #856404; 
            border: 1px solid rgba(255, 193, 7, 0.2);
        }
        
        .badge-ventas { 
            background: rgba(127, 173, 57, 0.1); 
            color: var(--primary); 
            border: 1px solid rgba(127, 173, 57, 0.2);
        }
        
        /* Formulario Agregar Vendedor */
        .form-container {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        
        .form-label-sm {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray);
            margin-bottom: 3px;
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
        
        /* Ranking Vendedores */
        .ranking-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .ranking-item {
            background: white;
            border-radius: 6px;
            padding: 10px;
            border: 1px solid var(--light-gray);
            transition: all 0.2s ease;
        }
        
        .ranking-item:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 4px rgba(127, 173, 57, 0.1);
        }
        
        .ranking-pos {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.75rem;
        }
        
        .ranking-pos.top1 { background: #ffd700; }
        .ranking-pos.top2 { background: #c0c0c0; }
        .ranking-pos.top3 { background: #cd7f32; }
        
        .ranking-info {
            flex: 1;
        }
        
        .ranking-nombre {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--dark);
            margin-bottom: 2px;
        }
        
        .ranking-metricas {
            display: flex;
            gap: 10px;
            font-size: 0.75rem;
        }
        
        .ranking-ventas {
            color: var(--primary);
            font-weight: 600;
        }
        
        .ranking-pedidos {
            color: var(--info);
        }
        
        /* Acciones Mejoradas */
        .acciones-container {
            display: flex;
            gap: 6px;
        }
        
        .btn-accion {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.8rem;
            position: relative;
        }
        
        .btn-accion:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        
        .btn-accion::after {
            content: attr(title);
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--dark);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 100;
        }
        
        .btn-accion:hover::after {
            opacity: 1;
            visibility: visible;
            bottom: -35px;
        }
        
        .btn-accion.editar {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }
        
        .btn-accion.editar:hover {
            background: linear-gradient(135deg, #138496, #117a8b);
        }
        
        .btn-accion.activar {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
        }
        
        .btn-accion.activar:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
        }
        
        .btn-accion.desactivar {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #000;
        }
        
        .btn-accion.desactivar:hover {
            background: linear-gradient(135deg, #e0a800, #d39e00);
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 70px;
                padding: 12px;
            }
            
            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
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
            
            .card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
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
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .table-responsive {
                font-size: 0.8rem;
            }
            
            .table th,
            .table td {
                padding: 8px 10px;
            }
            
            .acciones-container {
                flex-direction: column;
            }
            
            .btn-accion {
                width: 28px;
                height: 28px;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                margin-left: 0;
                padding: 8px;
            }
            
            .header-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-custom {
                width: 100%;
                justify-content: center;
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
        
        .card,
        .stat-card,
        .form-container {
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
        
        /* Avatar del vendedor */
        .vendedor-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            margin-right: 10px;
        }
        
        /* Hover para filas de tabla */
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(127, 173, 57, 0.08) !important;
            transform: translateX(2px);
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
                    <i class="fas fa-users me-2"></i>Gestión de Vendedores
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
                <button class="btn-custom btn-success-custom" data-bs-toggle="modal" data-bs-target="#modalAgregarVendedor">
                    <i class="fas fa-user-plus me-1"></i> Agregar Vendedor
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon vendedores">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-label">Total Vendedores</div>
                <div class="stat-value">{{ $estadisticas['total_vendedores'] ?? 0 }}</div>
                <div class="stat-change">
                    <i class="fas fa-chart-line me-1"></i> En tu sucursal
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon activos">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-label">Vendedores Activos</div>
                <div class="stat-value">{{ $estadisticas['vendedores_activos'] ?? 0 }}</div>
                <div class="stat-change">
                    <i class="fas fa-bullseye me-1"></i> 
                    @php
                        $total = $estadisticas['total_vendedores'] ?? 1;
                        $activos = $estadisticas['vendedores_activos'] ?? 0;
                        $porcentaje = $total > 0 ? round(($activos / $total) * 100) : 0;
                    @endphp
                    {{ $porcentaje }}% activos
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pedidos">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-label">Pedidos de Sucursal</div>
                <div class="stat-value">{{ $estadisticas['total_pedidos_sucursal'] ?? 0 }}</div>
                <div class="stat-change">
                    <i class="fas fa-store me-1"></i> Todos los pedidos
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon ventas">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-label">Ventas de Sucursal</div>
                <div class="stat-value">${{ number_format($estadisticas['ventas_totales_sucursal'] ?? 0, 2) }}</div>
                <div class="stat-change">
                    <i class="fas fa-chart-line me-1"></i> Total entregados
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Lista de Vendedores -->
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-list me-2"></i> Vendedores de la Sucursal
                            <span class="badge bg-primary">{{ $vendedores->count() }} registrados</span>
                        </h5>
                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vendedor</th>
                                        <th>Usuario</th>
                                        <th>Estado</th>
                                        <th>Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vendedores as $vendedor)
                                        @php
                                            $iniciales = strtoupper(substr($vendedor->nombre, 0, 2));
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="vendedor-avatar">
                                                        {{ $iniciales }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold" style="font-size: 0.9rem;">
                                                            {{ $vendedor->nombre }}
                                                        </div>
                                                        <small class="text-muted d-block">
                                                            {{ $vendedor->email }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ $vendedor->usuario }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($vendedor->activo)
                                                    <span class="badge-estado badge-activo">
                                                        <i class="fas fa-check-circle"></i> Activo
                                                    </span>
                                                @else
                                                    <span class="badge-estado badge-inactivo">
                                                        <i class="fas fa-times-circle"></i> Inactivo
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ Carbon::parse($vendedor->fecha_creacion)->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="acciones-container">
                                                    <!-- Botón Editar -->
                                                    <button class="btn-accion editar" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalEditarVendedor"
                                                            data-id="{{ $vendedor->id }}"
                                                            data-nombre="{{ $vendedor->nombre }}"
                                                            data-usuario="{{ $vendedor->usuario }}"
                                                            data-email="{{ $vendedor->email }}"
                                                            title="Editar Vendedor">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    
                                                    <!-- Botón Activar/Desactivar -->
                                                    @if($vendedor->activo)
                                                    <form method="POST" action="{{ route('gerente.vendedores.toggle') }}" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="usuario_id" value="{{ $vendedor->id }}">
                                                        <input type="hidden" name="nuevo_estado" value="0">
                                                        <button type="submit" class="btn-accion desactivar" 
                                                                onclick="return confirmarCambioEstado('{{ $vendedor->nombre }}', 'desactivar')"
                                                                title="Desactivar Vendedor">
                                                            <i class="fas fa-user-slash"></i>
                                                        </button>
                                                    </form>
                                                    @else
                                                    <form method="POST" action="{{ route('gerente.vendedores.toggle') }}" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="usuario_id" value="{{ $vendedor->id }}">
                                                        <input type="hidden" name="nuevo_estado" value="1">
                                                        <button type="submit" class="btn-accion activar" 
                                                                onclick="return confirmarCambioEstado('{{ $vendedor->nombre }}', 'activar')"
                                                                title="Activar Vendedor">
                                                            <i class="fas fa-user-check"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-users fa-2x mb-3"></i>
                                                    <h6>No hay vendedores registrados</h6>
                                                    <p class="small">Agrega vendedores a tu sucursal para comenzar.</p>
                                                    <button class="btn-custom btn-primary-custom mt-2" data-bs-toggle="modal" data-bs-target="#modalAgregarVendedor">
                                                        <i class="fas fa-user-plus me-1"></i> Agregar primer vendedor
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ranking y Acciones -->
            <div class="col-lg-4 mb-3">
                <!-- Ranking de Vendedores -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-trophy me-2"></i> Ranking de Vendedores
                        </h5>
                        <small class="text-muted">Últimos 30 días</small>
                    </div>
                    <div class="card-body">
                        @if($estadisticas_vendedores->count() > 0)
                        <div class="ranking-list">
                            @php $posicion = 1; @endphp
                            @foreach($estadisticas_vendedores as $ranking)
                                @if($ranking->total_pedidos_asignados > 0 || $ranking->ventas_totales > 0)
                                <div class="ranking-item d-flex align-items-center gap-3">
                                    <div class="ranking-pos top{{ $posicion <= 3 ? $posicion : '' }}">
                                        {{ $posicion++ }}
                                    </div>
                                    <div class="ranking-info">
                                        <div class="ranking-nombre">{{ $ranking->nombre }}</div>
                                        <div class="ranking-metricas">
                                            @if($ranking->ventas_totales > 0)
                                            <span class="ranking-ventas">
                                                <i class="fas fa-money-bill-wave fa-xs"></i> 
                                                ${{ number_format($ranking->ventas_totales, 2) }}
                                            </span>
                                            @endif
                                            @if($ranking->total_pedidos_asignados > 0)
                                            <span class="ranking-pedidos">
                                                <i class="fas fa-shopping-cart fa-xs"></i> 
                                                {{ $ranking->total_pedidos_asignados }} pedidos
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                            
                            @if($posicion === 1)
                            <div class="text-center py-3 text-muted">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <p class="small mb-0">No hay pedidos asignados a vendedores aún</p>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <p class="small mb-0">No hay vendedores activos con pedidos asignados</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Información de Sucursal -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle me-2"></i> Información de Sucursal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Sucursal:</div>
                            <div class="fw-semibold">{{ session('sucursal_nombre') }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Vendedores activos:</div>
                            <div class="fw-semibold text-success">
                                {{ $estadisticas['vendedores_activos'] ?? 0 }} de {{ $estadisticas['total_vendedores'] ?? 0 }}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Pedidos pendientes:</div>
                            <div class="fw-semibold text-warning">
                                <i class="fas fa-clock fa-sm me-1"></i>
                                {{ $pedidos_pendientes_count }} pendientes
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Ventas totales:</div>
                            <div class="fw-semibold text-success">
                                <i class="fas fa-dollar-sign fa-sm me-1"></i>
                                ${{ number_format($estadisticas['ventas_totales_sucursal'] ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar Vendedor -->
    <div class="modal fade" id="modalAgregarVendedor" tabindex="-1" aria-labelledby="modalAgregarVendedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('gerente.vendedores.store') }}" id="formAgregarVendedor">
                    @csrf
                    
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalAgregarVendedorLabel">
                            <i class="fas fa-user-plus me-2"></i> Agregar Nuevo Vendedor
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label-sm">Nombre completo *</label>
                            <input type="text" name="nombre" class="form-control-sm" required 
                                   placeholder="Ej: Juan Pérez García" id="nombreVendedor" value="{{ old('nombre') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-sm">Nombre de usuario *</label>
                            <input type="text" name="usuario" class="form-control-sm" required 
                                   placeholder="Ej: jperez" pattern="[a-zA-Z0-9_]+" title="Solo letras, números y guión bajo" id="usuarioVendedor" value="{{ old('usuario') }}">
                            <small class="text-muted">Solo letras, números y guión bajo. Sin espacios.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-sm">Correo electrónico *</label>
                            <input type="email" name="email" class="form-control-sm" required 
                                   placeholder="ejemplo@tinacosonline.com" value="{{ old('email') }}">
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label-sm">Contraseña *</label>
                                <input type="password" name="contrasena" class="form-control-sm" required 
                                       id="contrasena" minlength="6">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label-sm">Confirmar contraseña *</label>
                                <input type="password" name="confirmar_contrasena" class="form-control-sm" required 
                                       id="confirmar_contrasena" minlength="6">
                            </div>
                        </div>
                        
                        <div class="alert alert-info small mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            El vendedor será asignado automáticamente a tu sucursal: <strong>{{ session('sucursal_nombre') }}</strong>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn-custom btn-secondary-custom" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-custom btn-success-custom">
                            <i class="fas fa-save me-1"></i> Guardar Vendedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Vendedor -->
    <div class="modal fade" id="modalEditarVendedor" tabindex="-1" aria-labelledby="modalEditarVendedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('gerente.vendedores.update') }}" id="formEditarVendedor">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="usuario_id" id="editUsuarioId">
                    
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="modalEditarVendedorLabel">
                            <i class="fas fa-edit me-2"></i> Editar Vendedor
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label-sm">Nombre completo *</label>
                            <input type="text" name="nombre" class="form-control-sm" id="editNombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-sm">Nombre de usuario</label>
                            <input type="text" class="form-control-sm" id="editUsuario" disabled>
                            <small class="text-muted">El nombre de usuario no se puede cambiar</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-sm">Correo electrónico *</label>
                            <input type="email" name="email" class="form-control-sm" id="editEmail" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label-sm">Nueva Contraseña</label>
                            <input type="password" name="contrasena" class="form-control-sm" 
                                   placeholder="Dejar vacío para mantener la actual">
                            <small class="text-muted">Mínimo 6 caracteres. Solo llena si deseas cambiar la contraseña.</small>
                        </div>
                        
                        <div class="alert alert-warning small mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Solo puedes editar vendedores de tu sucursal: <strong>{{ session('sucursal_nombre') }}</strong>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn-custom btn-secondary-custom" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-custom btn-success-custom">
                            <i class="fas fa-save me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Función para confirmar cambio de estado
        function confirmarCambioEstado(nombre, accion) {
            event.preventDefault();
            const form = event.target.closest('form');
            
            Swal.fire({
                title: `¿${accion === 'activar' ? 'Activar' : 'Desactivar'} Vendedor?`,
                html: `¿Estás seguro de ${accion} al vendedor <strong>${nombre}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: accion === 'activar' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            
            return false;
        }

        // Función para generar reporte de vendedores
        function generarReporteVendedores() {
            Swal.fire({
                title: 'Generando Reporte...',
                text: 'Preparando reporte de vendedores de la sucursal',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                showLoaderOnConfirm: true,
                didOpen: () => {
                    Swal.showLoading();
                    
                    setTimeout(() => {
                        Swal.fire({
                            title: '¡Reporte Generado!',
                            html: `Reporte de vendedores - Sucursal <strong>{{ session('sucursal_nombre') }}</strong><br><br>
                                   <div class="text-start">
                                       <p><i class="fas fa-users text-primary me-2"></i> Total vendedores: <strong>{{ $estadisticas['total_vendedores'] ?? 0 }}</strong></p>
                                       <p><i class="fas fa-user-check text-success me-2"></i> Vendedores activos: <strong>{{ $estadisticas['vendedores_activos'] ?? 0 }}</strong></p>
                                       <p><i class="fas fa-shopping-cart text-warning me-2"></i> Pedidos pendientes: <strong>{{ $pedidos_pendientes_count }}</strong></p>
                                       <p><i class="fas fa-money-bill-wave text-success me-2"></i> Ventas totales: <strong>${{ number_format($estadisticas['ventas_totales_sucursal'] ?? 0, 2) }}</strong></p>
                                   </div>`,
                            icon: 'success',
                            confirmButtonColor: '#7fad39',
                            confirmButtonText: 'Descargar Excel',
                            showCancelButton: true,
                            cancelButtonText: 'Cerrar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.open('reporte_vendedores_excel.php?sucursal={{ session('sucursal_id') }}', '_blank');
                            }
                        });
                    }, 1500);
                }
            });
        }
        
        // Configurar modal de editar vendedor
        document.addEventListener('DOMContentLoaded', function() {
            const modalEditar = document.getElementById('modalEditarVendedor');
            if (modalEditar) {
                modalEditar.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const nombre = button.getAttribute('data-nombre');
                    const usuario = button.getAttribute('data-usuario');
                    const email = button.getAttribute('data-email');
                    
                    document.getElementById('editUsuarioId').value = id;
                    document.getElementById('editNombre').value = nombre;
                    document.getElementById('editUsuario').value = usuario;
                    document.getElementById('editEmail').value = email;
                });
            }
        });
        
        // Auto-generar usuario basado en el nombre
        document.getElementById('nombreVendedor')?.addEventListener('input', function() {
            const nombre = this.value.trim();
            const usuarioInput = document.getElementById('usuarioVendedor');
            
            if (usuarioInput && usuarioInput.value === '' && nombre !== '') {
                const partes = nombre.toLowerCase().split(' ');
                if (partes.length >= 2) {
                    let usuario = partes[0].charAt(0) + partes[1];
                    usuario = usuario.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]/g, '');
                    if (usuario.length >= 3) {
                        usuarioInput.value = usuario;
                    }
                }
            }
        });
        
        // Validación del formulario de agregar vendedor
        document.getElementById('formAgregarVendedor').addEventListener('submit', function(e) {
            const contrasena = document.getElementById('contrasena').value;
            const confirmarContrasena = document.getElementById('confirmar_contrasena').value;
            
            if (contrasena !== confirmarContrasena) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'Las contraseñas no coinciden',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
            
            if (contrasena.length < 6) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'La contraseña debe tener al menos 6 caracteres',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
        });
        
        // Validación del formulario de editar vendedor
        document.getElementById('formEditarVendedor').addEventListener('submit', function(e) {
            const contrasena = this.querySelector('[name="contrasena"]').value;
            
            if (contrasena && contrasena.length < 6) {
                e.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'La nueva contraseña debe tener al menos 6 caracteres',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
        });
        
        // Mostrar SweetAlert2 si hay mensaje en sesión
        @if(session('swal'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '{{ session('swal.title') }}',
                text: '{{ session('swal.message') }}',
                icon: '{{ session('swal.type') }}',
                confirmButtonColor: '#7fad39',
                confirmButtonText: 'Aceptar'
            });
        });
        @endif
        
        // Mostrar errores de validación
        @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            let mensajeError = '';
            @foreach($errors->all() as $error)
                mensajeError += `• {{ $error }}<br>`;
            @endforeach
            
            Swal.fire({
                title: 'Error en el formulario',
                html: mensajeError,
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Corregir'
            });
        });
        @endif
        
        // Mejorar experiencia de inputs
        document.querySelectorAll('.form-control-sm').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = 'var(--primary)';
                this.style.boxShadow = '0 0 0 2px rgba(127, 173, 57, 0.1)';
            });
            
            input.addEventListener('blur', function() {
                this.style.borderColor = 'var(--light-gray)';
                this.style.boxShadow = 'none';
            });
        });
        
        // Mostrar información al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                @if($vendedores->count() > 0)
                Swal.fire({
                    title: 'Gestión de Vendedores',
                    html: `Estás gestionando <strong>{{ $vendedores->count() }} vendedores</strong> de la sucursal <strong>{{ session('sucursal_nombre') }}</strong>`,
                    icon: 'info',
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
                @endif
            }, 800);
        });
    </script>
</body>
</html>