<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vendedor') - Tláloc</title>
    
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
            --sidebar-border: #2d3343;
            --badge-danger: #e74c3c;
            --badge-warning: #f39c12;
            --badge-success: #2ecc71;
            --badge-info: #3498db;
            --primary: #7fad39;
            --primary-dark: #5a8a20;
            --light: #f8f9fa;
            --light-gray: #e9ecef;
            --gray: #6c757d;
            --dark: #212529;
            --info: #17a2b8;
        }
        
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        
        /* Sidebar */
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
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid var(--sidebar-border);
        }
        
        .content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
            padding: 20px;
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
            color: var(--primary);
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
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-top: 5px;
            font-weight: 600;
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
            border-left: 3px solid var(--primary);
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
            color: var(--primary);
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
        }
        
        .sidebar-nav .badge.bg-warning {
            background: var(--badge-warning) !important;
            color: white;
        }
        
        .sidebar-nav .badge.bg-success {
            background: var(--badge-success) !important;
            color: white;
        }
        
        .sidebar-nav .badge.bg-info {
            background: var(--badge-info) !important;
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
            background: linear-gradient(135deg, var(--primary), #5a8a20);
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
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            border: 1px solid var(--sidebar-border);
            border-radius: 8px;
            width: 42px;
            height: 42px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }
        
        .sidebar-toggle:hover {
            background: var(--sidebar-hover);
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(3px);
            z-index: 1049;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Estilos del Dashboard */
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
        
        .time-widget {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-align: center;
            min-width: 130px;
            box-shadow: 0 2px 6px rgba(127, 173, 57, 0.2);
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
        
        .stat-pendientes { border-top-color: #ffc107; }
        .stat-disponibles { border-top-color: var(--info); }
        .stat-ventas { border-top-color: #28a745; }
        .stat-clientes { border-top-color: #6f42c1; }
        .stat-comisiones { border-top-color: #fd7e14; }
        
        .content-row {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        @media (max-width: 992px) {
            .content-row {
                grid-template-columns: 1fr;
            }
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
        
        .btn-asignar {
            background: linear-gradient(135deg, var(--info), #138496);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }
        
        .btn-asignar:hover {
            background: linear-gradient(135deg, #138496, #117a8b);
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }
        
        .badge-disponible {
            background: linear-gradient(135deg, var(--info), #138496);
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 8px;
        }
        
        .alert-disponibles {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-left: 4px solid var(--info);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .product-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            border-radius: 6px;
            margin-bottom: 6px;
            background: var(--light);
            transition: all 0.2s ease;
        }
        
        .product-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
            flex: 1;
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
        
        .pedido-disponible-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            background: #f8f9fa;
            transition: all 0.2s ease;
        }
        
        .pedido-disponible-item:hover {
            background: #e9ecef;
            border-color: #ced4da;
        }
        
        .pedido-disponible-item .pedido-info {
            margin-bottom: 10px;
        }
        
        .pedido-disponible-item .pedido-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .assigned-badge {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
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
        
        .action-buttons {
            display: flex;
            gap: 5px;
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
        
        /* Responsive */
        @media (max-width: 1200px) {
            .sidebar {
                width: 70px;
            }
            
            .content-wrapper {
                margin-left: 70px;
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
                display: none !important;
            }
            
            .brand i {
                font-size: 1.5rem;
                margin: 0;
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
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .content-wrapper {
                margin-left: 0;
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
            }
            
            .sidebar.mobile-open ~ .sidebar-toggle {
                left: 270px;
            }
            
            .sidebar.mobile-open ~ .sidebar-toggle i {
                transform: rotate(90deg);
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    @php
        $currentRoute = Route::currentRouteName();
        $usuario = Auth::user();
        
        // Obtener sucursal del vendedor
        $sucursal = $usuario->sucursales()->first();
        $sucursalNombre = $sucursal ? $sucursal->nombre : 'Sin sucursal';
        
        // Iniciales para el avatar
        $iniciales = '';
        $partes = explode(' ', $usuario->nombre);
        foreach($partes as $parte) {
            if(!empty(trim($parte))) {
                $iniciales .= strtoupper(substr(trim($parte), 0, 1));
                if(strlen($iniciales) >= 2) break;
            }
        }
        $iniciales = empty($iniciales) ? 'V' : $iniciales;
    @endphp

    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="brand">
                    <i class="fas fa-user-tie"></i>
                    <span>Tláloc</span>
                </div>
                <small>Panel Vendedor</small>
                <div class="sucursal-badge">
                    <i class="fas fa-store"></i> {{ $sucursalNombre }}
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <!-- Dashboard -->
                    <li class="{{ $currentRoute == 'vendedor.dashboard' ? 'active' : '' }}">
                        <a href="{{ route('vendedor.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <!-- Mis Pedidos -->
                    <li class="{{ $currentRoute == 'vendedor.pedidos.index' ? 'active' : '' }}">                    
                        <a href="{{ route('vendedor.pedidos.index') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Mis Pedidos</span>
                            @if($sidebar_pedidos_asignados > 0)
                                <span class="badge bg-info">{{ $sidebar_pedidos_asignados }}</span>
                            @endif
                        </a>
                    </li>
                    
                    <!-- Nuevo Pedido -->
                    <li class="{{ $currentRoute == 'vendedor.pedidos.create' ? 'active' : '' }}">
                        <a href="{{ route('vendedor.pedidos.create') }}">
                            <i class="fas fa-plus-circle"></i>
                            <span>Nuevo Pedido</span>
                        </a>
                    </li>
                    
                    <!-- Pedidos de Hoy (CORREGIDO) -->
                    <li class="{{ $currentRoute == 'vendedor.pedidos.hoy' ? 'active' : '' }}">
                        <a href="{{ route('vendedor.pedidos.hoy') }}">
                            <i class="fas fa-bolt"></i>
                            <span>Pedidos de Hoy</span>
                            @if($sidebar_pedidos_hoy > 0)
                                <span class="badge bg-danger">{{ $sidebar_pedidos_hoy }}</span>
                            @endif
                        </a>
                    </li>
                    
                    <!-- Mis Ventas -->
                    <li class="{{ str_starts_with($currentRoute, 'vendedor.ventas') ? 'active' : '' }}">
                        <a href="{{ route('vendedor.ventas.index') }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Mis Ventas</span>
                            @if($sidebar_ventas_mes > 0)
                                <span class="badge bg-success">{{ $sidebar_ventas_mes }}</span>
                            @endif
                        </a>
                    </li>
                    
                    <!-- Mis Clientes -->
                    <li class="{{ str_starts_with($currentRoute, 'vendedor.clientes') ? 'active' : '' }}">
                        <a href="{{ route('vendedor.clientes.index') }}">
                            <i class="fas fa-user-friends"></i>
                            <span>Mis Clientes</span>
                        </a>
                    </li>
                    
                    <!-- Catálogo -->
                    <li class="{{ str_starts_with($currentRoute, 'vendedor.catalogo') ? 'active' : '' }}">
                        <a href="{{ route('vendedor.catalogo.index') }}">
                            <i class="fas fa-box"></i>
                            <span>Catálogo</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        {{ $iniciales }}
                    </div>
                    <div class="user-details">
                        <p class="user-name">{{ $usuario->nombre }}</p>
                        <p class="user-role">Vendedor</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <button type="button" class="logout-btn" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Toggle button para móvil -->
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Mostrar/Ocultar menú">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Overlay para móvil -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Contenido principal -->
        <main class="content-wrapper">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
        <!-- Script para actualización en tiempo real -->
    <script>
        // Actualizar contadores cada 30 segundos
        setInterval(function() {
            fetch('{{ route("vendedor.contadores.actualizar") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Contadores actualizados:', data);
                
                // Actualizar Mis Pedidos (badge azul)
                const misPedidosBadge = document.querySelector('a[href="{{ route("vendedor.pedidos.index") }}"] .badge');
                if (misPedidosBadge) {
                    if (data.pedidos_asignados > 0) {
                        misPedidosBadge.textContent = data.pedidos_asignados;
                        misPedidosBadge.style.display = 'inline-block';
                    } else {
                        misPedidosBadge.style.display = 'none';
                    }
                }
                
                // Actualizar Pedidos de Hoy (badge rojo)
                const hoyBadge = document.querySelector('a[href="{{ route("vendedor.pedidos.hoy") }}"] .badge');
                if (hoyBadge) {
                    if (data.pedidos_hoy > 0) {
                        hoyBadge.textContent = data.pedidos_hoy;
                        hoyBadge.style.display = 'inline-block';
                    } else {
                        hoyBadge.style.display = 'none';
                    }
                }
                
                // Actualizar Mis Ventas (badge verde)
                const ventasBadge = document.querySelector('a[href="{{ route("vendedor.ventas.index") }}"] .badge');
                if (ventasBadge) {
                    if (data.ventas_mes > 0) {
                        ventasBadge.textContent = data.ventas_mes;
                        ventasBadge.style.display = 'inline-block';
                    } else {
                        ventasBadge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error actualizando contadores:', error));
        }, 30000);
    </script>

    <!-- Script para cerrar sesión -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logoutBtn');
            const logoutForm = document.getElementById('logout-form');
            
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: '¿Cerrar Sesión?',
                        html: '<div style="text-align: center;">' +
                              '<i class="fas fa-sign-out-alt" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>' +
                              '<p>¿Estás seguro de que deseas salir del sistema?</p>' +
                              '</div>',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Sí, cerrar sesión',
                        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            logoutForm.submit();
                        }
                    });
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>