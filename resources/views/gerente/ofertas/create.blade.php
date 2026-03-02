@php
    use Illuminate\Support\Facades\Route;
    
    $currentRoute = Route::currentRouteName();
    
    // Obtener contadores desde la sesión
    $pedidos_pendientes_count = session('pedidos_pendientes_count', 0);
    $productos_bajos_count = session('productos_bajos_count', 0);
    
    // Obtener sucursal del gerente (desde sesión)
    $sucursalNombre = session('sucursal_nombre', 'Sin sucursal asignada');
    $sucursalId = session('sucursal_id');
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nueva Oferta - Gerente</title>
    
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
            --sidebar-bg: #1a1d28;
            --sidebar-text: #e9ecef;
            --sidebar-hover: #2d3343;
            --sidebar-active: #3498db;
            --sidebar-border: #2d3343;
            --badge-danger: #e74c3c;
            --badge-warning: #f39c12;
            --primary: #7fad39;
            --primary-dark: #5a8a20;
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
            overflow-x: hidden;
        }
        
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Sidebar styles (copiados de tu sidebar) */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid var(--sidebar-border);
        }
        
        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid var(--sidebar-border);
            background: rgba(255,255,255,0.02);
        }
        
        .brand {
            font-size: 1.4rem;
            margin: 0 0 8px 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: white;
            letter-spacing: 0.5px;
        }
        
        .brand i {
            font-size: 1.6rem;
            color: var(--sidebar-active);
        }
        
        .sidebar-header small {
            opacity: 0.7;
            font-size: 0.75rem;
            display: block;
            color: var(--sidebar-text);
            font-weight: 400;
        }
        
        .sucursal-badge {
            display: inline-block;
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            margin-top: 8px;
            font-weight: 600;
            border: 1px solid rgba(52, 152, 219, 0.3);
        }
        
        .sucursal-badge i {
            margin-right: 4px;
            color: #3498db;
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }
        
        .sidebar-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-nav li {
            margin: 4px 15px;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        
        .sidebar-nav li:hover:not(.active) {
            background: var(--sidebar-hover);
            transform: translateX(4px);
        }
        
        .sidebar-nav a {
            color: rgba(233, 236, 239, 0.8);
            text-decoration: none;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .sidebar-nav .active {
            background: var(--sidebar-hover);
            position: relative;
            border-left: 3px solid var(--sidebar-active);
        }
        
        .sidebar-nav .active a {
            color: white;
            font-weight: 600;
        }
        
        .sidebar-nav a i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
            opacity: 0.8;
            transition: all 0.2s ease;
        }
        
        .sidebar-nav .active a i,
        .sidebar-nav li:hover a i {
            opacity: 1;
            color: var(--sidebar-active);
        }
        
        .sidebar-nav .badge {
            margin-left: auto;
            background: rgba(255,255,255,0.1);
            font-size: 0.7rem;
            padding: 3px 8px;
            min-width: 22px;
            text-align: center;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        
        .sidebar-nav .badge.bg-danger {
            background: var(--badge-danger) !important;
            color: white;
            box-shadow: 0 2px 4px rgba(231, 76, 60, 0.2);
        }
        
        .sidebar-nav .badge.bg-warning {
            background: var(--badge-warning) !important;
            color: white;
            box-shadow: 0 2px 4px rgba(243, 156, 18, 0.2);
        }
        
        .sidebar-nav .badge.bg-success {
            background: var(--primary) !important;
            color: white;
        }
        
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--sidebar-border);
            background: rgba(255,255,255,0.02);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sidebar-active), #2980b9);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            flex-shrink: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .user-details {
            flex: 1;
            overflow: hidden;
        }
        
        .user-details .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: white;
        }
        
        .user-details .user-role {
            font-size: 0.75rem;
            opacity: 0.7;
            margin: 2px 0 0 0;
            color: var(--sidebar-text);
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            background: rgba(255,255,255,0.05);
            color: var(--sidebar-text);
            border: 1px solid var(--sidebar-border);
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            outline: none;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-color: rgba(255,255,255,0.2);
        }
        
        /* SCROLLBAR PERSONALIZADO VERDE */
        .sidebar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #2d3343;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #7fad39;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #5a8a20;
        }

        /* Scroll para la navegación */
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.03);
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 2px;
        }
        
        /* BOTÓN HAMBURGUESA - SOLO MÓVIL */
        .sidebar-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            border: 1px solid var(--sidebar-border);
            border-radius: 8px;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            font-size: 1.3rem;
            display: none;
        }
        
        .sidebar-toggle:hover {
            background: var(--sidebar-hover);
        }
        
        /* OVERLAY MÓVIL */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Header Bar */
        .header-bar {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
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
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-custom {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
        }
        
        .btn-secondary-custom {
            background: white;
            color: var(--gray);
            border: 1px solid var(--light-gray);
        }
        
        .btn-secondary-custom:hover {
            background: var(--light);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 20px;
            background: white;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--light-gray);
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }
        
        .card-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-header i {
            color: var(--primary);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .form-control, .form-select {
            padding: 10px 12px;
            font-size: 0.95rem;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            width: 100%;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(127, 173, 57, 0.1);
            outline: none;
        }
        
        .input-group-text {
            background: var(--light);
            border: 1px solid var(--light-gray);
            color: var(--gray);
            padding: 10px 12px;
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .form-check-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(127, 173, 57, 0.1);
        }
        
        .productos-container {
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
            background: var(--light);
            border-radius: 8px;
            border: 1px solid var(--light-gray);
        }
        
        .productos-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .productos-container::-webkit-scrollbar-track {
            background: var(--light-gray);
            border-radius: 3px;
        }
        
        .productos-container::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }
        
        .categoria-titulo {
            color: var(--primary);
            font-weight: 600;
            margin: 15px 0 10px;
            font-size: 1rem;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .categoria-titulo:first-of-type {
            margin-top: 0;
        }
        
        .producto-check {
            padding: 10px;
            border-radius: 6px;
            transition: background 0.2s;
            border: 1px solid transparent;
        }
        
        .producto-check:hover {
            background: white;
            border-color: var(--primary);
            cursor: pointer;
        }
        
        .producto-info small {
            color: var(--gray);
            font-size: 0.75rem;
        }
        
        .text-muted {
            color: var(--gray) !important;
        }
        
        .text-success {
            color: var(--success) !important;
        }
        
        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 70px;
            }
            
            .sidebar {
                width: 70px;
                align-items: center;
            }
            
            .sidebar-header {
                padding: 20px 10px;
            }
            
            .brand span,
            .sidebar-header small,
            .sucursal-badge,
            .sidebar-nav a span:not(.badge),
            .user-details,
            .logout-btn span {
                display: none;
            }
            
            .brand i {
                font-size: 1.5rem;
                margin: 0;
            }
            
            .sidebar-nav {
                width: 100%;
            }
            
            .sidebar-nav li {
                margin: 4px 10px;
                position: relative;
            }
            
            .sidebar-nav a {
                justify-content: center;
                padding: 14px 10px;
            }
            
            .sidebar-nav a i {
                margin: 0;
                font-size: 1.2rem;
            }
            
            .sidebar-nav .badge {
                position: absolute;
                top: 6px;
                right: 6px;
                font-size: 0.65rem;
                padding: 2px 5px;
                min-width: 18px;
            }
            
            .user-info {
                justify-content: center;
                margin-bottom: 10px;
            }
            
            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 0.9rem;
            }
            
            .logout-btn {
                padding: 10px;
                justify-content: center;
            }
            
            .logout-btn i {
                margin: 0;
                font-size: 1.1rem;
            }
            
            .sidebar-toggle {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .sidebar {
                width: 250px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: 5px 0 20px rgba(0,0,0,0.2);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .brand span,
            .sidebar-header small,
            .sucursal-badge,
            .sidebar-nav a span:not(.badge),
            .user-details,
            .logout-btn span {
                display: block !important;
            }
            
            .sidebar-nav a {
                justify-content: flex-start;
                padding: 12px 15px;
            }
            
            .sidebar-nav .badge {
                position: static;
                margin-left: auto;
            }
            
            .user-info {
                justify-content: flex-start;
            }
            
            .logout-btn {
                justify-content: flex-start;
            }
            
            .sidebar-toggle {
                display: flex;
                left: 15px;
                top: 15px;
            }
            
            .header-bar {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            
            .header-actions {
                justify-content: center;
            }
            
            .productos-container {
                max-height: 300px;
            }
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header-bar,
        .card {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* SweetAlert2 */
        .swal2-popup {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif !important;
            border-radius: 15px !important;
            padding: 2rem !important;
        }
        
        .swal2-title {
            color: #1a1d28 !important;
            font-size: 1.4rem !important;
            font-weight: 600 !important;
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
    </style>
</head>
<body>
    <!-- Botón hamburguesa SOLO MÓVIL -->
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menú">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Overlay móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand">
                <i class="fas fa-user-tie"></i>
                <span>Tláloc</span>
            </div>
            <small>Panel Gerente</small>
            <div class="sucursal-badge">
                <i class="fas fa-store"></i> {{ $sucursalNombre }}
            </div>
        </div>
        
        <nav class="sidebar-nav" aria-label="Navegación principal">
            <ul>
                <li class="{{ request()->routeIs('gerente.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('gerente.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('gerente.pedidos*') ? 'active' : '' }}">
                    <a href="{{ route('gerente.pedidos') }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Pedidos</span>
                        @if($pedidos_pendientes_count > 0)
                        <span class="badge bg-danger">{{ $pedidos_pendientes_count }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ request()->routeIs('gerente.productos*') ? 'active' : '' }}">
                    <a href="{{ route('gerente.productos') }}">
                        <i class="fas fa-box"></i>
                        <span>Inventario</span>
                        @if($productos_bajos_count > 0)
                        <span class="badge bg-warning">{{ $productos_bajos_count }}</span>
                        @endif
                    </a>
                </li>

                <li class="{{ request()->routeIs('gerente.ofertas*') ? 'active' : '' }}">
                    <a href="{{ route('gerente.ofertas') }}">
                        <i class="fas fa-tags"></i>
                        <span>Ofertas</span>
                        @php
                            use App\Models\Oferta;
                            $sucursal_id = session('sucursal_id');
                            $ofertas_activas = 0;
                            if($sucursal_id) {
                                $ofertas_activas = Oferta::where('activa', true)
                                    ->whereHas('productos.sucursales', function($q) use ($sucursal_id) {
                                        $q->where('sucursal_id', $sucursal_id);
                                    })->count();
                            }
                        @endphp
                        @if($ofertas_activas > 0)
                        <span class="badge bg-success">{{ $ofertas_activas }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ request()->routeIs('gerente.vendedores*') ? 'active' : '' }}">
                    <a href="{{ route('gerente.vendedores') }}">
                        <i class="fas fa-users"></i>
                        <span>Vendedores</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('gerente.reportes*') ? 'active' : '' }}">
                    <a href="{{ route('gerente.reportes') }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reportes</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('gerente.clientes*') ? 'active' : '' }}">
                    <a href="{{ route('gerente.clientes') }}">
                        <i class="fas fa-user-friends"></i>
                        <span>Clientes</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    @php
                        $nombre = auth()->user()->nombre ?? 'Gerente';
                        $iniciales = '';
                        $partes = explode(' ', $nombre);
                        foreach($partes as $parte) {
                            if(!empty(trim($parte))) {
                                $iniciales .= strtoupper(substr(trim($parte), 0, 1));
                                if(strlen($iniciales) >= 2) break;
                            }
                        }
                        echo empty($iniciales) ? 'G' : $iniciales;
                    @endphp
                </div>
                <div class="user-details">
                    <p class="user-name">{{ auth()->user()->nombre ?? 'Gerente' }}</p>
                    <p class="user-role">Gerente de Sucursal</p>
                </div>
            </div>
            
            <!-- Formulario de logout oculto -->
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            
            <button class="logout-btn" id="logoutBtn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </button>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- Header -->
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-plus-circle me-2"></i>Nueva Oferta
                </h1>
                <p class="text-muted mb-0 small">Crea una nueva promoción para tu sucursal</p>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('gerente.ofertas') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Formulario -->
        <div class="card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-tag me-2"></i>Información de la Oferta
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('gerente.ofertas.store') }}" id="formOferta">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Nombre de la Oferta *</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre') }}" required placeholder="Ej: Hot Sale 2026">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                                          rows="3" placeholder="Descripción de la oferta...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-cog me-2"></i>Configuración
                                    </h6>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="activa" id="activa" value="1" {{ old('activa', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="activa">Oferta activa</label>
                                    </div>
                                    
                                    <p class="small text-muted mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Las ofertas inactivas no se muestran en la tienda
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-4 mt-2">
                        <div class="col-md-4">
                            <label class="form-label">Tipo de Descuento *</label>
                            <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" id="tipoDescuento" required>
                                <option value="porcentaje" {{ old('tipo') == 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                                <option value="fijo" {{ old('tipo') == 'fijo' ? 'selected' : '' }}>Monto Fijo ($)</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Valor del Descuento *</label>
                            <div class="input-group">
                                <span class="input-group-text" id="valorSimbolo">
                                    {{ old('tipo') == 'fijo' ? '$' : '%' }}
                                </span>
                                <input type="number" step="0.01" min="0" name="valor" 
                                       class="form-control @error('valor') is-invalid @enderror" 
                                       value="{{ old('valor') }}" required>
                            </div>
                            <small class="text-muted">Hasta 2 decimales</small>
                            @error('valor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Inicio *</label>
                            <input type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                   value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Fin *</label>
                            <input type="date" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                   value="{{ old('fecha_fin', now()->addDays(30)->format('Y-m-d')) }}" required>
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="form-label mb-3">
                            <i class="fas fa-box me-2"></i>Productos en Oferta
                            <small class="text-muted">(Selecciona los productos de tu sucursal)</small>
                        </label>
                        
                        <div class="productos-container">
                            @forelse($productos as $categoria => $productosCategoria)
                                <h6 class="categoria-titulo">
                                    <i class="fas fa-folder me-2"></i>{{ $categoria }}
                                </h6>
                                <div class="row g-3 mb-4">
                                    @foreach($productosCategoria as $producto)
                                    <div class="col-md-6">
                                        <div class="producto-check" onclick="document.getElementById('prod{{ $producto->id }}').click()">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="productos[]" value="{{ $producto->id }}" 
                                                       id="prod{{ $producto->id }}"
                                                       {{ is_array(old('productos')) && in_array($producto->id, old('productos')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="prod{{ $producto->id }}">
                                                    <strong>{{ $producto->nombre }}</strong>
                                                    <div class="producto-info">
                                                        <small>Código: {{ $producto->codigo }}</small><br>
                                                        <small>Precio: ${{ number_format($producto->precio, 2) }}</small>
                                                        @php
                                                            $stock = DB::table('producto_sucursal')
                                                                ->where('producto_id', $producto->id)
                                                                ->where('sucursal_id', $sucursalId)
                                                                ->value('existencias') ?? 0;
                                                        @endphp
                                                        <br>
                                                        <small class="text-success">
                                                            <i class="fas fa-check-circle"></i> Stock: {{ $stock }}
                                                        </small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @empty
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No hay productos disponibles en tu sucursal.
                                </div>
                            @endforelse
                        </div>
                        @error('productos')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('gerente.ofertas') }}" class="btn-custom btn-secondary-custom">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-custom btn-success-custom" id="btnGuardar">
                            <i class="fas fa-save me-1"></i> Guardar Oferta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const logoutBtn = document.getElementById('logoutBtn');
            const logoutForm = document.getElementById('logout-form');
            
            let isSidebarOpen = false;
            
            function openSidebar() {
                sidebar.classList.add('mobile-open');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                isSidebarOpen = true;
            }
            
            function closeSidebar() {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = 'auto';
                isSidebarOpen = false;
            }
            
            function toggleSidebar() {
                if (isSidebarOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }
            
            sidebarToggle.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', closeSidebar);
            
            document.querySelectorAll('.sidebar-nav a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768 && isSidebarOpen) {
                        closeSidebar();
                    }
                });
            });
            
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: '¿Cerrar Sesión?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-sign-out-alt me-1"></i> Sí, cerrar sesión',
                    cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutForm.submit();
                    }
                });
            });
            
            // Cambiar símbolo según tipo
            document.getElementById('tipoDescuento').addEventListener('change', function() {
                document.getElementById('valorSimbolo').textContent = this.value === 'porcentaje' ? '%' : '$';
            });
            
            // Confirmación antes de guardar
            document.getElementById('formOferta').addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: '¿Guardar oferta?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#7fad39',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check me-1"></i> Sí, guardar',
                    cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
        
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                let errores = '';
                @foreach($errors->all() as $error)
                    errores += '• {{ $error }}\n';
                @endforeach
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: errores,
                    confirmButtonColor: '#dc3545'
                });
            });
        @endif
        
        @if(session('swal'))
            Swal.fire({
                icon: '{{ session('swal')['type'] }}',
                title: '{{ session('swal')['title'] }}',
                text: '{{ session('swal')['message'] }}',
                confirmButtonColor: '#7fad39'
            });
        @endif
    </script>
</body>
</html>