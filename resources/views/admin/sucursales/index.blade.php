<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Sucursales - Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
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
            background: #f8f9fa;
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
        
        /* Header */
        .header-bar {
            background: white;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    
        
        /* Botones */
        .btn-custom {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }
        
        .btn-primary-custom {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary-custom:hover {
            background: var(--primary-dark);
            color: white;
        }
        
        .btn-success-custom {
            background: var(--success);
            color: white;
        }
        
        .btn-success-custom:hover {
            background: #218838;
            color: white;
        }
        
        .btn-danger-custom {
            background: var(--danger);
            color: white;
        }
        
        .btn-danger-custom:hover {
            background: #c82333;
            color: white;
        }
        
        .btn-info-custom {
            background: var(--info);
            color: white;
        }
        
        .btn-info-custom:hover {
            background: #138496;
            color: white;
        }
        
        /* Card */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--light-gray);
            padding: 15px;
            border-radius: 8px 8px 0 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        
        /* Mapa */
        #map {
            height: 400px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid var(--light-gray);
            z-index: 1;
        }
        
        .leaflet-container {
            font-family: inherit;
            border-radius: 8px;
        }
        
        /* Sucursal Card */
        .sucursal-card {
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .sucursal-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--success);
        }
        
        .sucursal-card.inactiva::before {
            background: var(--danger);
        }
        
        .sucursal-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .sucursal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .sucursal-nombre {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.1rem;
            margin: 0;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .badge-success {
            background: var(--success);
            color: white;
        }
        
        .badge-danger {
            background: var(--danger);
            color: white;
        }
        
        .badge-info {
            background: var(--info);
            color: white;
        }
        
        .cobertura-badge {
            background: var(--info);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .sucursal-info {
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        
        .info-icon {
            color: #6c757d;
            width: 20px;
            margin-right: 8px;
            font-size: 0.9rem;
            text-align: center;
        }
        
        .info-text {
            color: #495057;
            font-size: 0.9rem;
            flex: 1;
        }
        
        .coordenadas {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--light-gray);
        }
        
        .sucursal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            padding-top: 15px;
            border-top: 1px solid var(--light-gray);
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid var(--light-gray);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .stat-success { color: var(--success); }
        .stat-primary { color: var(--primary); }
        .stat-info { color: var(--info); }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .empty-state-icon {
            font-size: 3rem;
            color: var(--light-gray);
            margin-bottom: 20px;
        }
        
        .empty-state-title {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }
        
        .empty-state-text {
            color: var(--gray);
            font-size: 0.95rem;
            margin-bottom: 20px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Modal */
        .modal-header {
            background: var(--primary);
            color: white;
            border-radius: 8px 8px 0 0;
            padding: 15px;
        }
        
        .modal-title {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        /* Form */
        .form-label {
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .form-control, .form-select, textarea.form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus, textarea.form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(127, 173, 57, 0.25);
        }
        
        /* Info Alert */
        .info-alert {
            background: rgba(23, 162, 184, 0.1);
            border: 1px solid rgba(23, 162, 184, 0.3);
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            color: #0c5460;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 70px;
                padding: 15px;
            }
        }
        
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }
            
            .stat-value {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
                padding: 10px;
            }
            
            .header-bar {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .header-actions {
                width: 100%;
                justify-content: center;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            #map {
                height: 300px;
            }
            
            .sucursal-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
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
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .sucursal-actions {
                flex-direction: column;
            }
            
            .sucursal-actions .btn-custom {
                width: 100%;
                justify-content: center;
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
                    <i class="fas fa-store me-2"></i>Gestión de Sucursales
                </h1>
                <p class="text-muted mb-0">Bienvenido, {{ auth()->user()->nombre ?? 'Administrador' }}</p>
            </div>
            
            <div class="header-actions">
                <button class="btn-custom btn-primary-custom" data-bs-toggle="modal" data-bs-target="#nuevaSucursalModal">
                    <i class="fas fa-plus"></i> Nueva Sucursal
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value stat-primary">{{ $estadisticas['sucursales_activas'] }}</div>
                <div class="stat-label">Sucursales Activas</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value stat-success">{{ $estadisticas['pedidos_asignados'] }}</div>
                <div class="stat-label">Pedidos Asignados</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value stat-info">{{ $estadisticas['total_pedidos'] }}</div>
                <div class="stat-label">Total Pedidos</div>
            </div>
        </div>

        <!-- Mapa de Sucursales -->
        @if(count($sucursales) > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-map"></i> Mapa de Cobertura
                </h5>
                <span class="badge badge-info">
                    <i class="fas fa-crosshairs"></i> {{ count($sucursales) }} sucursales
                </span>
            </div>
            <div class="card-body">
                <div id="map"></div>
                <div class="mt-3 small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Los círculos representan el radio de cobertura de cada sucursal
                </div>
            </div>
        </div>
        @endif

        <!-- Lista de Sucursales -->
        @if(count($sucursales) > 0)
        <div class="row">
            @foreach($sucursales as $sucursal)
            <div class="col-lg-6 mb-3">
                <div class="sucursal-card {{ !$sucursal->activa ? 'inactiva' : '' }}">
                    <div class="sucursal-header">
                        <h5 class="sucursal-nombre">{{ $sucursal->nombre }}</h5>
                        <div class="d-flex gap-2">
                            <span class="cobertura-badge">
                                <i class="fas fa-circle-radiation"></i> {{ $sucursal->radio_cobertura_km }} km
                            </span>
                            @if($sucursal->activa)
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Activa
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-ban"></i> Inactiva
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="sucursal-info">
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="info-text">{{ $sucursal->direccion }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-phone"></i></div>
                            <div class="info-text">{{ $sucursal->telefono }}</div>
                        </div>
                        
                        @if($sucursal->email)
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-envelope"></i></div>
                            <div class="info-text">{{ $sucursal->email }}</div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="coordenadas">
                        <i class="fas fa-crosshairs me-1"></i>
                        {{ $sucursal->latitud }}, {{ $sucursal->longitud }}
                    </div>
                    
                    <div class="sucursal-actions">
                        <button class="btn-custom btn-info-custom btn-editar" 
                                data-id="{{ $sucursal->id }}"
                                data-nombre="{{ $sucursal->nombre }}"
                                data-direccion="{{ $sucursal->direccion }}"
                                data-telefono="{{ $sucursal->telefono }}"
                                data-email="{{ $sucursal->email }}"
                                data-latitud="{{ $sucursal->latitud }}"
                                data-longitud="{{ $sucursal->longitud }}"
                                data-radio="{{ $sucursal->radio_cobertura_km }}"
                                data-activa="{{ $sucursal->activa ? '1' : '0' }}">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn-custom btn-danger-custom btn-eliminar"
                                data-id="{{ $sucursal->id }}"
                                data-nombre="{{ $sucursal->nombre }}">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Estado vacío -->
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-store"></i></div>
            <h5 class="empty-state-title">No hay sucursales registradas</h5>
            <p class="empty-state-text">
                Configura las sucursales para gestionar la cobertura de entregas y asignar pedidos.
            </p>
            <button class="btn-custom btn-primary-custom" data-bs-toggle="modal" data-bs-target="#nuevaSucursalModal">
                <i class="fas fa-plus"></i> Crear Primera Sucursal
            </button>
        </div>
        @endif
    </div>

    <!-- Modal Nueva Sucursal -->
    <div class="modal fade" id="nuevaSucursalModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Nueva Sucursal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.sucursales.store') }}" id="formNuevaSucursal">
                    @csrf
                    <div class="modal-body">
                        <div class="info-alert">
                            <i class="fas fa-info-circle me-2"></i>
                            Puedes obtener las coordenadas en <a href="https://www.google.com/maps" target="_blank">Google Maps</a> haciendo clic derecho en la ubicación.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre de la Sucursal *</label>
                                <input type="text" name="nombre" class="form-control" required
                                       placeholder="Ej: Sucursal Centro">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono *</label>
                                <input type="text" name="telefono" class="form-control" required
                                       placeholder="Ej: 55 1234 5678">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Dirección Completa *</label>
                            <textarea name="direccion" class="form-control" rows="2" required
                                      placeholder="Calle, número, colonia, ciudad, estado"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                       placeholder="sucursal@ejemplo.com">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Radio de Cobertura (km) *</label>
                                <input type="number" name="radio_cobertura" class="form-control" 
                                       value="8" min="1" max="50" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitud *</label>
                                <input type="number" step="any" name="latitud" class="form-control" required 
                                       placeholder="Ej: 19.4326">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitud *</label>
                                <input type="number" step="any" name="longitud" class="form-control" required 
                                       placeholder="Ej: -99.1332">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn-custom btn-success-custom">
                            <i class="fas fa-save"></i> Crear Sucursal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Sucursal -->
    <div class="modal fade" id="editarSucursalModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Editar Sucursal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.sucursales.update') }}" id="formEditarSucursal">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editSucursalId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre de la Sucursal *</label>
                                <input type="text" name="nombre" id="editSucursalNombre" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono *</label>
                                <input type="text" name="telefono" id="editSucursalTelefono" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Dirección Completa *</label>
                            <textarea name="direccion" id="editSucursalDireccion" class="form-control" rows="2" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="editSucursalEmail" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Radio de Cobertura (km) *</label>
                                <input type="number" name="radio_cobertura" id="editSucursalRadio" class="form-control" min="1" max="50" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitud *</label>
                                <input type="number" step="any" name="latitud" id="editSucursalLatitud" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitud *</label>
                                <input type="number" step="any" name="longitud" id="editSucursalLongitud" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="activa" id="editSucursalActiva" value="1">
                                <label class="form-check-label" for="editSucursalActiva">
                                    Sucursal Activa
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn-custom btn-success-custom">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
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

            // Botones de editar
            document.querySelectorAll('.btn-editar').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('editSucursalId').value = this.dataset.id;
                    document.getElementById('editSucursalNombre').value = this.dataset.nombre;
                    document.getElementById('editSucursalDireccion').value = this.dataset.direccion;
                    document.getElementById('editSucursalTelefono').value = this.dataset.telefono;
                    document.getElementById('editSucursalEmail').value = this.dataset.email || '';
                    document.getElementById('editSucursalLatitud').value = this.dataset.latitud;
                    document.getElementById('editSucursalLongitud').value = this.dataset.longitud;
                    document.getElementById('editSucursalRadio').value = this.dataset.radio;
                    document.getElementById('editSucursalActiva').checked = this.dataset.activa === '1';
                    
                    new bootstrap.Modal(document.getElementById('editarSucursalModal')).show();
                });
            });

            // Botones de eliminar
            document.querySelectorAll('.btn-eliminar').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nombre = this.dataset.nombre;
                    
                    Swal.fire({
                        title: '¿Eliminar sucursal?',
                        text: `¿Estás seguro de eliminar la sucursal "${nombre}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("admin.sucursales.destroy") }}';
                            
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

            // Mapa
            @if(count($sucursales) > 0)
                const map = L.map('map').setView([{{ $sucursales[0]->latitud }}, {{ $sucursales[0]->longitud }}], 10);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                
                @foreach($sucursales as $sucursal)
                    L.marker([{{ $sucursal->latitud }}, {{ $sucursal->longitud }}]).addTo(map)
                        .bindPopup(`
                            <strong>{{ $sucursal->nombre }}</strong><br>
                            <small>{{ $sucursal->direccion }}</small><br>
                            <small><i class="fas fa-phone"></i> {{ $sucursal->telefono }}</small><br>
                            <small><i class="fas fa-circle-radiation"></i> Cobertura: {{ $sucursal->radio_cobertura_km }} km</small>
                        `);
                    
                    L.circle([{{ $sucursal->latitud }}, {{ $sucursal->longitud }}], {
                        color: '{{ $sucursal->activa ? "#28a745" : "#dc3545" }}',
                        fillColor: '{{ $sucursal->activa ? "#28a745" : "#dc3545" }}',
                        fillOpacity: 0.1,
                        radius: {{ $sucursal->radio_cobertura_km * 1000 }}
                    }).addTo(map);
                @endforeach
            @endif
        });
    </script>
</body>
</html>