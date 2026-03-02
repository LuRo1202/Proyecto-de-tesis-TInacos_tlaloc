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
    <title>Gestión de Ofertas - Gerente</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- DataTables CSS (opcional) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
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
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }
        
        .filter-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--light-gray);
        }
        
        .filter-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--dark);
        }
        
        .filter-title i {
            color: var(--primary);
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .form-control-sm {
            padding: 8px 12px;
            font-size: 0.9rem;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            width: 100%;
            transition: all 0.2s ease;
        }
        
        .form-control-sm:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(127, 173, 57, 0.1);
            outline: none;
        }
        
        select.form-control-sm {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236c757d' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 12px 8px;
            padding-right: 2.5rem;
            cursor: pointer;
            appearance: none;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
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
        
        .table {
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #eee;
            padding: 12px 15px;
            white-space: nowrap;
        }
        
        .table td {
            vertical-align: middle;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(127, 173, 57, 0.05);
            cursor: pointer;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn-action {
            width: 34px;
            height: 34px;
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
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #fd7e14, #e06b0c);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745, #218838);
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .badge-tipo {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
        }
        
        .badge-vigente {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-proxima {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #856404;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-expirada {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-inactiva {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.3;
        }
        
        .empty-state h5 {
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.2rem;
            color: var(--dark);
        }
        
        .empty-state p {
            font-size: 0.95rem;
            margin: 0 auto 20px;
        }
        
        /* Paginación */
        .pagination {
            margin: 0;
        }
        
        .page-link {
            border: none;
            color: var(--gray);
            padding: 8px 12px;
            font-size: 0.9rem;
            border-radius: 6px;
            margin: 0 2px;
        }
        
        .page-item.active .page-link {
            background: var(--primary);
            color: white;
        }
        
        .page-link:hover {
            background: var(--light-gray);
            color: var(--dark);
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
            
            .sidebar.mobile-open + .sidebar-toggle {
                left: 270px;
            }
            
            .header-bar {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            
            .header-actions {
                justify-content: center;
            }
            
            .filter-card .row > div {
                margin-bottom: 10px;
            }
            
            .table thead {
                display: none;
            }
            
            .table tbody tr {
                display: block;
                border: 1px solid var(--light-gray);
                margin-bottom: 15px;
                border-radius: 8px;
            }
            
            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border: none;
                border-bottom: 1px solid var(--light-gray);
                padding: 12px;
            }
            
            .table tbody td:before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--dark);
                margin-right: 10px;
            }
            
            .table tbody td:last-child {
                border-bottom: none;
            }
            
            .action-buttons {
                flex-direction: row;
                width: 100%;
                justify-content: flex-end;
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
        .filter-card,
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
                    <i class="fas fa-tags me-2"></i>Gestión de Ofertas
                </h1>
                <p class="text-muted mb-0 small">Administra las promociones de tu sucursal</p>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('gerente.ofertas.nuevo') }}" class="btn-custom btn-success-custom">
                    <i class="fas fa-plus"></i> Nueva Oferta
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-card">
            <h3 class="filter-title">
                <i class="fas fa-filter"></i> Filtros
            </h3>
            
            <form method="GET" action="{{ route('gerente.ofertas') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-control-sm">
                        <option value="">Todas las ofertas</option>
                        <option value="activas" {{ request('estado') == 'activas' ? 'selected' : '' }}>Activas</option>
                        <option value="inactivas" {{ request('estado') == 'inactivas' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="buscar" class="form-control-sm" 
                           placeholder="Nombre o descripción..." value="{{ request('buscar') }}">
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn-custom btn-primary-custom w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
                @if(request()->has('estado') || request()->has('buscar'))
                <div class="col-12 text-end">
                    <a href="{{ route('gerente.ofertas') }}" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-times"></i> Limpiar filtros
                    </a>
                </div>
                @endif
            </form>
        </div>

        <!-- Tabla de Ofertas -->
        <div class="card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-list me-2"></i>Lista de Ofertas
                </h5>
                <span class="badge bg-primary">Total: {{ $ofertas->total() }}</span>
            </div>
            
            <div class="card-body p-0">
                @if($ofertas->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-tags"></i>
                    <h5>No hay ofertas registradas</h5>
                    <p>Crea tu primera oferta para comenzar a promocionar productos</p>
                    <a href="{{ route('gerente.ofertas.nuevo') }}" class="btn-custom btn-success-custom mt-2">
                        <i class="fas fa-plus"></i> Crear Primera Oferta
                    </a>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Productos</th>
                                <th>Vigencia</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ofertas as $oferta)
                            @php
                                $vigente = $oferta->activa && 
                                          now()->between($oferta->fecha_inicio, $oferta->fecha_fin);
                                $proxima = $oferta->activa && now()->lt($oferta->fecha_inicio);
                                $expirada = $oferta->activa && now()->gt($oferta->fecha_fin);
                            @endphp
                            <tr onclick="window.location.href='{{ route('gerente.ofertas.editar', $oferta->id) }}'">
                                <td data-label="Nombre">
                                    <strong>{{ $oferta->nombre }}</strong>
                                    @if($oferta->descripcion)
                                    <small class="text-muted d-block">{{ Str::limit($oferta->descripcion, 50) }}</small>
                                    @endif
                                </td>
                                <td data-label="Tipo">
                                    @if($oferta->tipo === 'porcentaje')
                                        <span class="badge-tipo" style="background: #17a2b8;">
                                            <i class="fas fa-percent"></i> %
                                        </span>
                                    @else
                                        <span class="badge-tipo" style="background: #fd7e14;">
                                            <i class="fas fa-dollar-sign"></i> $
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Valor" class="fw-bold" style="color: #7fad39;">
                                    @if($oferta->tipo === 'porcentaje')
                                        {{ number_format($oferta->valor, 2) }}%
                                    @else
                                        ${{ number_format($oferta->valor, 2) }}
                                    @endif
                                </td>
                                <td data-label="Productos">
                                    <span class="badge bg-primary">
                                        {{ $oferta->productos_count }} productos
                                    </span>
                                </td>
                                <td data-label="Vigencia">
                                    <div><i class="fas fa-calendar-alt text-muted me-1"></i>{{ $oferta->fecha_inicio->format('d/m/Y') }}</div>
                                    <div><i class="fas fa-calendar-check text-muted me-1"></i>{{ $oferta->fecha_fin->format('d/m/Y') }}</div>
                                </td>
                                <td data-label="Estado">
                                    @if($vigente)
                                        <span class="badge-vigente">
                                            <i class="fas fa-check-circle"></i> Vigente
                                        </span>
                                    @elseif($proxima)
                                        <span class="badge-proxima">
                                            <i class="fas fa-clock"></i> Próxima
                                        </span>
                                    @elseif($expirada)
                                        <span class="badge-expirada">
                                            <i class="fas fa-hourglass-end"></i> Expirada
                                        </span>
                                    @elseif(!$oferta->activa)
                                        <span class="badge-inactiva">
                                            <i class="fas fa-ban"></i> Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Acciones" onclick="event.stopPropagation()">
                                    <div class="action-buttons">
                                        <a href="{{ route('gerente.ofertas.editar', $oferta->id) }}" 
                                           class="btn-action btn-edit" title="Editar oferta">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if($oferta->activa)
                                            <button type="button" 
                                                    class="btn-action btn-warning" 
                                                    title="Desactivar oferta"
                                                    onclick="toggleOferta({{ $oferta->id }}, '{{ $oferta->nombre }}', true)">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="btn-action btn-success" 
                                                    title="Activar oferta"
                                                    onclick="toggleOferta({{ $oferta->id }}, '{{ $oferta->nombre }}', false)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                @if($ofertas->hasPages())
                <div class="card-footer">
                    <nav aria-label="Paginación">
                        {{ $ofertas->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
                @endif
                @endif
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
        });
        
        function toggleOferta(id, nombre, activa) {
            event.stopPropagation();
            const accion = activa ? 'desactivar' : 'activar';
            
            Swal.fire({
                title: `¿${accion} oferta?`,
                html: `<p>¿Estás seguro de <strong>${accion}</strong> la oferta <strong>${nombre}</strong>?</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: activa ? '#fd7e14' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `<i class="fas fa-${activa ? 'ban' : 'check'} me-1"></i> Sí, ${accion}`,
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/gerente/ofertas/${id}/toggle`;
                }
            });
        }
        
        @if(session('swal'))
            Swal.fire({
                icon: '{{ session('swal')['type'] }}',
                title: '{{ session('swal')['title'] }}',
                text: '{{ session('swal')['message'] }}',
                confirmButtonColor: '#7fad39',
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    </script>
</body>
</html>