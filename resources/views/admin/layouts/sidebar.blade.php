@php
    use Illuminate\Support\Facades\Route;
    
    // Obtener la ruta actual para resaltar el menú activo
    $currentRoute = Route::currentRouteName();
    
    // Obtener contadores desde la sesión o base de datos
    $pedidos_pendientes_count = session('pedidos_pendientes_count', 0);
    $productos_bajos_count = session('productos_bajos_count', 0);
    
    // Determinar la página actual para PHP puro (compatibilidad)
    $current_page = request()->path();
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
        }
        
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
        
        /* Scrollbar personalizado */

        .sidebar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #2d3343;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #7fad39; /* Color verde de tu marca */
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #5a8a20;
        }

        /* También mantener el scroll para la navegación */
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
        
        /* BOTÓN HAMBURGUESA */
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
        
        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .sidebar {
                width: 70px;
                align-items: center;
            }
            
            .sidebar-header {
                padding: 20px 10px;
            }
            
            .brand span,
            .sidebar-header small,
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
            
            .sidebar.mobile-open + .sidebar-toggle i {
                transform: rotate(90deg);
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
        
        .sidebar-nav li {
            animation: fadeIn 0.3s ease-out forwards;
            opacity: 0;
        }
        
        .sidebar-nav li:nth-child(1) { animation-delay: 0.1s; }
        .sidebar-nav li:nth-child(2) { animation-delay: 0.15s; }
        .sidebar-nav li:nth-child(3) { animation-delay: 0.2s; }
        .sidebar-nav li:nth-child(4) { animation-delay: 0.25s; }
        .sidebar-nav li:nth-child(5) { animation-delay: 0.3s; }
        .sidebar-nav li:nth-child(6) { animation-delay: 0.35s; }
        .sidebar-nav li:nth-child(7) { animation-delay: 0.4s; }
        .sidebar-nav li:nth-child(8) { animation-delay: 0.45s; }
        
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
            margin-bottom: 1rem !important;
        }
        
        .swal2-html-container {
            color: #6c757d !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
        }
        
        .swal2-confirm {
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 0.7rem 2rem !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            transition: all 0.2s ease !important;
        }
        
        .swal2-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3) !important;
        }
        
        .swal2-cancel {
            background: white !important;
            color: #6c757d !important;
            border: 1px solid #e9ecef !important;
            border-radius: 8px !important;
            padding: 0.7rem 2rem !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            transition: all 0.2s ease !important;
        }
        
        .swal2-cancel:hover {
            background: #f8f9fa !important;
            border-color: #dee2e6 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
        }
        
        .swal2-icon {
            margin-bottom: 1rem !important;
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
                <i class="fas fa-water"></i>
                <span>Tláloc</span>
            </div>
            <small>Panel Administrativo</small>
        </div>
        
        <nav class="sidebar-nav" aria-label="Navegación principal">
            <ul>
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.pedidos*') ? 'active' : '' }}">
                    <a href="{{ route('admin.pedidos') }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Pedidos</span>
                        @if(isset($pedidos_pendientes_count) && $pedidos_pendientes_count > 0)
                        <span class="badge bg-danger">{{ $pedidos_pendientes_count }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.productos*') ? 'active' : '' }}">
                    <a href="{{ route('admin.productos') }}">
                        <i class="fas fa-box"></i>
                        <span>Productos</span>
                        @if(isset($productos_bajos_count) && $productos_bajos_count > 0)
                        <span class="badge bg-warning">{{ $productos_bajos_count }}</span>
                        @endif
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.ofertas*') ? 'active' : '' }}">
                    <a href="{{ route('admin.ofertas') }}">
                        <i class="fas fa-tags"></i>
                        <span>Ofertas</span>
                        @php
                            $ofertas_activas = \App\Models\Oferta::where('activa', true)->count();
                        @endphp
                        @if($ofertas_activas > 0)
                        <span class="badge bg-success">{{ $ofertas_activas }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.categorias*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categorias') }}">
                        <i class="fas fa-tags"></i>
                        <span>Categorías</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.sucursales*') ? 'active' : '' }}">
                    <a href="{{ route('admin.sucursales') }}">
                        <i class="fas fa-store"></i>
                        <span>Sucursales</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">
                    <a href="{{ route('admin.usuarios') }}">
                        <i class="fas fa-user-cog"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.reportes*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reportes') }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reportes</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    @php
                        $nombre = auth()->user()->nombre ?? 'Admin';
                        $iniciales = '';
                        $partes = explode(' ', $nombre);
                        foreach($partes as $parte) {
                            if(!empty(trim($parte))) {
                                $iniciales .= strtoupper(substr(trim($parte), 0, 1));
                                if(strlen($iniciales) >= 2) break;
                            }
                        }
                        echo empty($iniciales) ? 'A' : $iniciales;
                    @endphp
                </div>
                <div class="user-details">
                    <p class="user-name">{{ auth()->user()->nombre ?? 'Administrador' }}</p>
                    <p class="user-role">{{ ucfirst(auth()->user()->rol ?? 'admin') }}</p>
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

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const logoutBtn = document.getElementById('logoutBtn');
            const logoutForm = document.getElementById('logout-form');
            
            // Estado sidebar móvil
            let isSidebarOpen = false;
            
            // Funciones sidebar móvil
            function openSidebar() {
                sidebar.classList.add('mobile-open');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                sidebarToggle.setAttribute('aria-label', 'Cerrar menú');
                isSidebarOpen = true;
            }
            
            function closeSidebar() {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = 'auto';
                sidebarToggle.setAttribute('aria-label', 'Abrir menú');
                isSidebarOpen = false;
            }
            
            function toggleSidebar() {
                if (isSidebarOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }
            
            // Inicializar botón según tamaño
            function initSidebarToggle() {
                if (window.innerWidth <= 768) {
                    sidebarToggle.style.display = 'flex';
                    sidebarToggle.setAttribute('aria-label', 'Abrir menú');
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    isSidebarOpen = false;
                } else {
                    sidebarToggle.style.display = 'none';
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    isSidebarOpen = false;
                }
            }
            
            // Eventos
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });
            
            sidebarOverlay.addEventListener('click', closeSidebar);
            
            const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768 && isSidebarOpen) {
                        closeSidebar();
                    }
                });
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isSidebarOpen) {
                    closeSidebar();
                }
            });
            
            window.addEventListener('resize', initSidebarToggle);
            initSidebarToggle();
            
            // Tooltips para tablet
            if (window.innerWidth <= 1200 && window.innerWidth > 768) {
                const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
                sidebarLinks.forEach(link => {
                    const text = link.querySelector('span:not(.badge)');
                    if (text) {
                        const titleText = text.textContent;
                        link.setAttribute('title', titleText);
                        
                        link.addEventListener('mouseenter', function(e) {
                            const tooltip = document.createElement('div');
                            tooltip.className = 'tablet-tooltip';
                            tooltip.textContent = titleText;
                            tooltip.style.cssText = `
                                position: absolute;
                                background: var(--sidebar-hover);
                                color: white;
                                padding: 8px 12px;
                                border-radius: 6px;
                                font-size: 0.85rem;
                                z-index: 1100;
                                left: 70px;
                                white-space: nowrap;
                                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                                pointer-events: none;
                            `;
                            document.body.appendChild(tooltip);
                            
                            const rect = this.getBoundingClientRect();
                            tooltip.style.top = (rect.top + rect.height/2 - tooltip.offsetHeight/2) + 'px';
                            
                            this.addEventListener('mouseleave', function() {
                                if (tooltip.parentNode) {
                                    tooltip.parentNode.removeChild(tooltip);
                                }
                            }, { once: true });
                        });
                    }
                });
                
                const userAvatar = document.querySelector('.user-avatar');
                const userName = document.querySelector('.user-name');
                if (userAvatar && userName) {
                    userAvatar.setAttribute('title', userName.textContent);
                }
            }
            
            // Logout con SweetAlert 
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
                        allowOutsideClick: false,
                        backdrop: 'rgba(0,0,0,0.4)'
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
                            
                            // Enviar formulario por POST directamente
                            logoutForm.submit();
                        }
                    });
                });
            }
            
            // Efecto hover para desktop
            if (window.innerWidth > 768) {
                const sidebarItems = document.querySelectorAll('.sidebar-nav li');
                sidebarItems.forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateX(4px)';
                    });
                    
                    item.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateX(0)';
                    });
                });
            }
        });
    </script>
</body>
</html>