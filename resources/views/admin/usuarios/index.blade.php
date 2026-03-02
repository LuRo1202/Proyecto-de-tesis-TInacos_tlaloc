<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
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
            --sidebar-bg: #1a1d28;
            --sidebar-text: #e9ecef;
            --sidebar-hover: #2d3343;
            --sidebar-active: #3498db;
            --sidebar-border: #2d3343;
            --badge-danger: #e74c3c;
            --badge-warning: #f39c12;
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
        
        /* Tabla */
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
        
        /* Badges - VERSIÓN MÁS VIBRANTE */
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* ADMIN - Rojo vibrante */
        .badge-admin { 
            background: linear-gradient(135deg, #dc3545, #c82333); 
            color: white !important; 
            border: none;
            box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
        }

        /* GERENTE - Naranja/ámbar vibrante */
        .badge-gerente { 
            background: linear-gradient(135deg, #fd7e14, #e06b0c); 
            color: white !important; 
            border: none;
            box-shadow: 0 2px 6px rgba(253, 126, 20, 0.3);
        }

        /* VENDEDOR - Azul vibrante */
        .badge-vendedor { 
            background: linear-gradient(135deg, #007bff, #0062cc); 
            color: white !important; 
            border: none;
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
        }

        /* ACTIVO - Verde vibrante */
        .badge-activo { 
            background: linear-gradient(135deg, #28a745, #218838); 
            color: white !important; 
            border: none;
            box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
        }

        /* INACTIVO - Gris suave pero más visible */
        .badge-inactivo { 
            background: linear-gradient(135deg, #6c757d, #5a6268); 
            color: white !important; 
            border: none;
            box-shadow: 0 2px 6px rgba(108, 117, 125, 0.2);
        }

        /* SUCURSAL - Verde de la marca vibrante */
        .badge-sucursal { 
            background: linear-gradient(135deg, #7fad39, #5a8a20); 
            color: white !important; 
            border: none;
            box-shadow: 0 2px 6px rgba(127, 173, 57, 0.3);
        }
        
        /* Avatar */
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
            flex-shrink: 0;
        }
        
        /* Acciones - BOTONES CON COLORES ESPECÍFICOS */
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

        /* EDITAR - AMARILLO */
        .btn-edit {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        /* ELIMINAR - ROJO */
        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        /* DESACTIVAR (usuario activo) - NARANJA */
        .btn-warning {
            background: linear-gradient(135deg, #fd7e14, #e06b0c);
        }

        /* ACTIVAR (usuario inactivo) - VERDE */
        .btn-success {
            background: linear-gradient(135deg, #28a745, #218838);
        }

        /* Badge para "Tu cuenta" */
        .badge-self {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        /* Modal */
        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 8px 8px 0 0;
        }
        
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        /* Form */
        .form-label {
            font-weight: 500;
            color: var(--dark);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            border: 1px solid var(--light-gray);
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(127, 173, 57, 0.1);
            outline: none;
        }
        
        #sucursalContainer, #editSucursalContainer {
            display: none;
        }
        
        /* Paginación */
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
        
        /* Estado Vacío */
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
        .card {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Responsive */
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
            
            .user-avatar {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
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
                flex-direction: column;
                width: 100%;
            }
            
            .btn-action {
                width: 100%;
            }
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
                    <i class="fas fa-user-cog me-2"></i>Gestión de Usuarios
                </h1>
                <p class="text-muted mb-0 small">Bienvenido, {{ auth()->user()->nombre ?? 'Administrador' }}</p>
            </div>
            
            <div class="header-actions">
                <button class="btn-custom btn-primary-custom" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $total_usuarios }}</div>
                <div class="stat-label">Total Usuarios</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value">{{ $total_activos }}</div>
                <div class="stat-label">Usuarios Activos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value">{{ $total_admins }}</div>
                <div class="stat-label">Administradores</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value">{{ $total_gerentes + $total_vendedores }}</div>
                <div class="stat-label">Vendedores/Gerentes</div>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-users me-2"></i>Lista de Usuarios
                </h5>
                <span class="badge" style="background: var(--primary); color: white;">
                    Página {{ $pagina_actual }} de {{ $total_paginas }}
                </span>
            </div>
            
            <div class="card-body p-0">
                @if($usuarios->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h5>No hay usuarios registrados</h5>
                    <p class="text-muted">Crea el primer usuario para comenzar</p>
                    <button class="btn-custom btn-primary-custom" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                        <i class="fas fa-plus"></i> Crear Primer Usuario
                    </button>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Nombre</th>
                                <th>Rol</th>
                                <th>Sucursal</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                            @php
                                $iniciales = strtoupper(substr($usuario->nombre, 0, 1));
                            @endphp
                            <tr>
                                <td data-label="Usuario">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar">{{ $iniciales }}</div>
                                        <div>
                                            <div>{{ $usuario->usuario }}</div>
                                            <small class="text-muted">{{ $usuario->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Nombre">{{ $usuario->nombre }}</td>
                                <td data-label="Rol">
                                    @if($usuario->rol === 'admin')
                                        <span class="badge badge-admin">Administrador</span>
                                    @elseif($usuario->rol === 'gerente')
                                        <span class="badge badge-gerente">Gerente</span>
                                    @else
                                        <span class="badge badge-vendedor">Vendedor</span>
                                    @endif
                                </td>
                                <td data-label="Sucursal">
                                    @if($usuario->sucursal_nombre)
                                        <span class="badge badge-sucursal">{{ $usuario->sucursal_nombre }}</span>
                                    @else
                                        <span class="text-muted small">- Sin sucursal -</span>
                                    @endif
                                </td>
                                <td data-label="Estado">
                                    @if($usuario->activo)
                                        <span class="badge badge-activo">Activo</span>
                                    @else
                                        <span class="badge badge-inactivo">Inactivo</span>
                                    @endif
                                </td>
                                <td data-label="Acciones">
                                    <div class="action-buttons">
                                        <!-- EDITAR - SIEMPRE VISIBLE -->
                                        <button class="btn-action btn-edit btn-editar-usuario" 
                                                data-id="{{ $usuario->id }}"
                                                data-usuario="{{ $usuario->usuario }}"
                                                data-nombre="{{ $usuario->nombre }}"
                                                data-email="{{ $usuario->email ?? '' }}"
                                                data-rol="{{ $usuario->rol }}"
                                                data-sucursal-id="{{ $usuario->sucursal_id }}"
                                                data-activo="{{ $usuario->activo }}"
                                                title="Editar usuario">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        @if($usuario->id != auth()->id())
                                            @if($usuario->activo)
                                                <!-- DESACTIVAR -->
                                                <button class="btn-action btn-warning btn-toggle-estado"
                                                        data-id="{{ $usuario->id }}"
                                                        data-nombre="{{ $usuario->nombre }}"
                                                        data-activo="1"
                                                        title="Desactivar usuario">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <!-- ACTIVAR -->
                                                <button class="btn-action btn-success btn-toggle-estado"
                                                        data-id="{{ $usuario->id }}"
                                                        data-nombre="{{ $usuario->nombre }}"
                                                        data-activo="0"
                                                        title="Activar usuario">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        
                                            <!-- ELIMINAR -->
                                            <button class="btn-action btn-delete btn-eliminar-usuario"
                                                    data-id="{{ $usuario->id }}"
                                                    data-nombre="{{ $usuario->nombre }}"
                                                    data-usuario="{{ $usuario->usuario }}"
                                                    title="Eliminar usuario">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <!-- PARA EL PROPIO USUARIO - SOLO ESTO -->
                                            <span class="badge-self">
                                                <i class="fas fa-user"></i> Tu cuenta
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            
            <!-- Paginación -->
            @if($total_paginas > 1)
            <div class="card-footer">
                <nav aria-label="Paginación">
                    <ul class="pagination pagination-custom justify-content-center mb-0">
                        @if($pagina_actual > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ route('admin.usuarios', ['pagina' => $pagina_actual - 1]) }}">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        @endif
                        
                        @for($i = 1; $i <= $total_paginas; $i++)
                        <li class="page-item {{ $i == $pagina_actual ? 'active' : '' }}">
                            <a class="page-link" href="{{ route('admin.usuarios', ['pagina' => $i]) }}">{{ $i }}</a>
                        </li>
                        @endfor
                        
                        @if($pagina_actual < $total_paginas)
                        <li class="page-item">
                            <a class="page-link" href="{{ route('admin.usuarios', ['pagina' => $pagina_actual + 1]) }}">
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

    <!-- Modal Nuevo Usuario -->
    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.usuarios.store') }}" id="formNuevoUsuario">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo *</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Juan Pérez">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Usuario *</label>
                            <input type="text" name="usuario" class="form-control" required placeholder="Ej: jperez">
                            <small class="form-text text-muted">Sin espacios, solo letras y números</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña *</label>
                            <input type="password" name="contrasena" class="form-control" required minlength="6">
                            <small class="form-text text-muted">Mínimo 6 caracteres</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol *</label>
                            <select name="rol" class="form-select" required id="rolUsuario">
                                <option value="vendedor">Vendedor</option>
                                <option value="gerente">Gerente</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3" id="sucursalContainer">
                            <label class="form-label">Sucursal Asignada *</label>
                            <select name="sucursal_id" class="form-select" id="sucursalSelect">
                                <option value="">Seleccionar sucursal...</option>
                                @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Los vendedores y gerentes necesitan sucursal</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-custom btn-secondary-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-custom btn-success-custom">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.usuarios.update') }}" id="formEditarUsuario">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editUsuarioId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo *</label>
                            <input type="text" name="nombre" id="editUsuarioNombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" id="editUsuarioUsuario" class="form-control" disabled>
                            <small class="form-text text-muted">El usuario no se puede cambiar</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="editUsuarioEmail" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="contrasena" class="form-control" id="editUsuarioContrasena" placeholder="Dejar vacío para mantener actual">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol *</label>
                            <select name="rol" id="editUsuarioRol" class="form-select" required>
                                <option value="vendedor">Vendedor</option>
                                <option value="gerente">Gerente</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3" id="editSucursalContainer">
                            <label class="form-label">Sucursal Asignada *</label>
                            <select name="sucursal_id" class="form-select" id="editSucursalSelect">
                                <option value="">Seleccionar sucursal...</option>
                                @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activa" id="editUsuarioActiva" value="1">
                            <label class="form-check-label" for="editUsuarioActiva">Usuario Activo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-custom btn-secondary-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-custom btn-success-custom">Guardar Cambios</button>
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
            // Mensajes SweetAlert
            @if(session('swal'))
                Swal.fire({
                    icon: '{{ session('swal')['type'] }}',
                    title: '{{ session('swal')['title'] }}',
                    text: '{{ session('swal')['message'] }}',
                    confirmButtonColor: '#7fad39'
                });
            @endif

            // Función para mostrar/ocultar sucursal
            function toggleSucursal(rol, containerId, selectId) {
                const container = document.getElementById(containerId);
                const select = document.getElementById(selectId);
                
                if (rol === 'vendedor' || rol === 'gerente') {
                    container.style.display = 'block';
                    select.required = true;
                } else {
                    container.style.display = 'none';
                    select.required = false;
                    select.value = '';
                }
            }

            // Inicializar - Nuevo usuario
            const rolSelect = document.getElementById('rolUsuario');
            if (rolSelect) {
                toggleSucursal(rolSelect.value, 'sucursalContainer', 'sucursalSelect');
                rolSelect.addEventListener('change', function() {
                    toggleSucursal(this.value, 'sucursalContainer', 'sucursalSelect');
                });
            }

            // Botones editar
            document.querySelectorAll('.btn-editar-usuario').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('editUsuarioId').value = this.dataset.id;
                    document.getElementById('editUsuarioUsuario').value = this.dataset.usuario;
                    document.getElementById('editUsuarioNombre').value = this.dataset.nombre;
                    document.getElementById('editUsuarioEmail').value = this.dataset.email;
                    document.getElementById('editUsuarioRol').value = this.dataset.rol;
                    document.getElementById('editUsuarioActiva').checked = this.dataset.activo === '1';
                    document.getElementById('editUsuarioContrasena').value = '';
                    
                    const sucursalSelect = document.getElementById('editSucursalSelect');
                    if (this.dataset.sucursalId) {
                        sucursalSelect.value = this.dataset.sucursalId;
                    } else {
                        sucursalSelect.value = '';
                    }
                    
                    toggleSucursal(this.dataset.rol, 'editSucursalContainer', 'editSucursalSelect');
                    
                    // Cambio de rol en edición
                    document.getElementById('editUsuarioRol').onchange = function() {
                        toggleSucursal(this.value, 'editSucursalContainer', 'editSucursalSelect');
                    };
                    
                    new bootstrap.Modal(document.getElementById('editarUsuarioModal')).show();
                });
            });

            // Botones cambiar estado
            document.querySelectorAll('.btn-toggle-estado').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nombre = this.dataset.nombre;
                    const activo = this.dataset.activo === '1';
                    
                    Swal.fire({
                        title: `${activo ? 'Desactivar' : 'Activar'} usuario`,
                        html: `¿Estás seguro de ${activo ? 'desactivar' : 'activar'} a <strong>${nombre}</strong>?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: activo ? '#fd7e14' : '#28a745',
                        confirmButtonText: `Sí, ${activo ? 'desactivar' : 'activar'}`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `{{ route('admin.usuarios.toggle') }}?id=${id}`;
                        }
                    });
                });
            });

            // Botones eliminar usuario
            document.querySelectorAll('.btn-eliminar-usuario').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nombre = this.dataset.nombre;
                    const usuario = this.dataset.usuario;
                    
                    Swal.fire({
                        title: '¿Eliminar usuario?',
                        html: `¿Estás seguro de eliminar al usuario <strong>${nombre}</strong> (${usuario})?<br><br>
                               <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("admin.usuarios.destroy") }}';
                            
                            const csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            
                            const method = document.createElement('input');
                            method.type = 'hidden';
                            method.name = '_method';
                            method.value = 'DELETE';
                            
                            const idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.name = 'id';
                            idInput.value = id;
                            
                            form.appendChild(csrf);
                            form.appendChild(method);
                            form.appendChild(idInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
            
            // Efecto hover para cards
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Auto-generar usuario
            document.querySelector('input[name="nombre"]')?.addEventListener('input', function() {
                const nombre = this.value.trim();
                const usuarioInput = document.querySelector('input[name="usuario"]');
                
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
        });
    </script>
</body>
</html>