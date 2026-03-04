@php
    use App\Models\Sucursal;
    $sucursalActual = session('sucursal_nombre');
    $sucursalId = session('sucursal_id');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nuevo Pedido - Sucursal {{ session('sucursal_nombre') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
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
        
        .vendedor-selector {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid var(--light-gray);
            border-left: 4px solid var(--info);
        }
        
        .vendedor-selector label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .vendedor-selector label i {
            color: var(--info);
        }
        
        .vendedor-selector .form-select {
            border: 2px solid var(--light-gray);
            transition: all 0.2s ease;
        }
        
        .vendedor-selector .form-select:focus {
            border-color: var(--info);
            box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.1);
        }
        
        .verificar-cobertura-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .verificar-cobertura-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .verificar-cobertura-btn.loading {
            background: var(--gray);
        }
        
        .verificar-cobertura-btn.loading i {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .cobertura-permanente {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--success);
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .cobertura-permanente h6 {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .cobertura-permanente p {
            margin: 5px 0;
            font-size: 0.85rem;
        }
        
        .cobertura-detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            margin-bottom: 8px;
        }
        
        .cobertura-detail-item i {
            font-size: 1.2rem;
            color: #28a745;
            width: 24px;
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
        
        .card-header h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-header h5 i {
            color: var(--primary);
        }
        
        .card-body {
            padding: 15px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark);
            font-size: 0.85rem;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .form-label i {
            color: var(--primary);
            width: 16px;
        }
        
        .form-control, .form-select {
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            padding: 8px 12px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(127, 173, 57, 0.1);
            outline: none;
        }
        
        .badge-sucursal {
            background: rgba(127, 173, 57, 0.1);
            color: var(--primary-dark);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .producto-item {
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            background: white;
            position: relative;
            transition: all 0.2s ease;
        }
        
        .producto-item:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(127, 173, 57, 0.1);
        }
        
        .btn-remove-producto {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.7rem;
            transition: all 0.2s ease;
        }
        
        .btn-remove-producto:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        
        .existencias-info {
            font-size: 0.75rem;
            margin-top: 4px;
        }
        
        .existencias-baja {
            color: var(--danger);
            font-weight: 500;
        }
        
        .existencias-normal {
            color: var(--success);
        }
        
        .oferta-badge {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
            margin-left: 5px;
        }
        
        .resumen-pedido {
            background: linear-gradient(135deg, var(--light) 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            border: 2px solid var(--primary);
        }
        
        .resumen-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-align: right;
        }
        
        .input-group-text {
            background-color: var(--light);
            border: 1px solid var(--light-gray);
            color: var(--gray);
            font-size: 0.85rem;
        }

        .list-group-item {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid var(--light-gray);
            margin-bottom: 5px;
            border-radius: 5px !important;
        }
        
        .list-group-item:hover {
            background-color: rgba(127, 173, 57, 0.1);
            border-color: var(--primary);
            transform: translateX(5px);
        }
        
        #resultados-busqueda {
            max-height: 300px;
            overflow-y: auto;
            border-radius: 5px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 5px;
        }
        
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 70px;
                padding: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
                padding: 12px;
            }
            
            .header-bar {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
                gap: 8px;
            }
            
            .header-actions {
                justify-content: center;
            }
            
            .card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
            
            .producto-item {
                padding: 12px;
            }
            
            .producto-item .btn-remove-producto {
                top: 5px;
                right: 5px;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
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
        
        .card, .producto-item {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body>
    @include('gerente.layouts.sidebar')
    
    <div class="main-content">
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-cart-plus me-2"></i>Nuevo Pedido
                    <span class="sucursal-badge">
                        <i class="fas fa-store"></i> {{ session('sucursal_nombre') }}
                    </span>
                </h1>
                <div class="header-subtitle">
                    Gerente: {{ auth()->user()->nombre ?? 'Gerente' }}
                </div>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('gerente.pedidos') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('gerente.pedidos.store') }}" id="form-pedido" novalidate>
            @csrf
            <input type="hidden" name="sucursal_id" id="sucursal_id" value="{{ session('sucursal_id') }}">
            <input type="hidden" name="sucursal_nombre" id="sucursal_nombre" value="{{ session('sucursal_nombre') }}">
            
            @if(isset($vendedores) && $vendedores->count() > 0)
            <div class="vendedor-selector">
                <label for="vendedor_responsable">
                    <i class="fas fa-user-tie"></i> Asignar pedido a vendedor
                </label>
                <select class="form-select" name="vendedor_responsable" id="vendedor_responsable" required>
                    <option value="">-- Seleccionar vendedor --</option>
                    @foreach($vendedores as $vendedor)
                        <option value="{{ $vendedor->id }}" {{ old('vendedor_responsable') == $vendedor->id ? 'selected' : '' }}>
                            {{ $vendedor->nombre }} ({{ $vendedor->usuario }})
                        </option>
                    @endforeach
                </select>
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle"></i> El pedido será asignado al vendedor seleccionado
                </small>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No hay vendedores disponibles en esta sucursal. El pedido quedará sin asignar.
                <input type="hidden" name="vendedor_responsable" value="">
            </div>
            @endif

            <div class="card mb-3">
                <div class="card-header">
                    <h5><i class="fas fa-search"></i> Buscar Cliente Registrado</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" id="buscador-cliente" class="form-control" 
                                       placeholder="Buscar por teléfono, nombre o email...">
                                <button class="btn btn-primary" type="button" id="btn-buscar-cliente">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                            <div id="resultados-busqueda" class="mt-2" style="display: none;"></div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle"></i> Busca por teléfono, nombre o email
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-truck"></i> Información de Envío y Cobertura</h5>
                    <span class="badge-sucursal" id="badge-sucursal">
                        <i class="fas fa-store"></i> {{ session('sucursal_nombre') }}
                    </span>
                </div>
                <div class="card-body">
                    <div id="cobertura-verificada-box" class="cobertura-permanente" style="display: none;">
                        <h6><i class="fas fa-check-circle me-2"></i>Cobertura verificada</h6>
                        <div class="cobertura-detail-item">
                            <i class="fas fa-store"></i>
                            <div><strong>Sucursal:</strong> <span id="sucursal-nombre"></span></div>
                        </div>
                        <div class="cobertura-detail-item">
                            <i class="fas fa-location-dot"></i>
                            <div><strong>Dirección sucursal:</strong> <span id="sucursal-direccion"></span></div>
                        </div>
                        <div class="cobertura-detail-item">
                            <i class="fas fa-road"></i>
                            <div><strong>Distancia:</strong> <span id="distancia"></span> km</div>
                        </div>
                        <div class="mt-2 text-end">
                            <button type="button" class="btn-custom btn-secondary-custom" id="cambiar-direccion">
                                <i class="fas fa-sync-alt me-1"></i>Cambiar dirección
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_nombre" class="form-label">
                                    <i class="fas fa-user"></i>Nombre completo *
                                </label>
                                <input type="text" 
                                       class="form-control @error('cliente_nombre') is-invalid @enderror" 
                                       id="cliente_nombre" 
                                       name="cliente_nombre" 
                                       required 
                                       placeholder="Ej: Juan Pérez"
                                       value="{{ old('cliente_nombre', $cliente_datos['nombre'] ?? '') }}">
                                @error('cliente_nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_telefono" class="form-label">
                                    <i class="fas fa-phone"></i>Teléfono *
                                </label>
                                <input type="tel" 
                                       class="form-control @error('cliente_telefono') is-invalid @enderror" 
                                       id="cliente_telefono" 
                                       name="cliente_telefono" 
                                       required 
                                       placeholder="55 1234 5678"
                                       value="{{ old('cliente_telefono', $cliente_datos['telefono'] ?? '') }}">
                                @error('cliente_telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_email" class="form-label">
                                    <i class="fas fa-envelope"></i>Correo electrónico
                                </label>
                                <input type="email" 
                                       class="form-control @error('cliente_email') is-invalid @enderror" 
                                       id="cliente_email" 
                                       name="cliente_email" 
                                       placeholder="ejemplo@correo.com"
                                       value="{{ old('cliente_email') }}">
                                @error('cliente_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo_postal" class="form-label">
                                    <i class="fas fa-mail-bulk"></i>Código Postal *
                                </label>
                                <input type="text" 
                                       class="form-control @error('codigo_postal') is-invalid @enderror" 
                                       id="codigo_postal" 
                                       name="codigo_postal" 
                                       required 
                                       placeholder="Ej: 55000"
                                       maxlength="5"
                                       value="{{ old('codigo_postal', $cliente_datos['codigo_postal'] ?? '') }}">
                                @error('codigo_postal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label for="cliente_direccion" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>Dirección completa *
                                </label>
                                <input type="text" 
                                       class="form-control @error('cliente_direccion') is-invalid @enderror" 
                                       id="cliente_direccion" 
                                       name="cliente_direccion" 
                                       required 
                                       placeholder="Calle, número, colonia"
                                       value="{{ old('cliente_direccion', $cliente_datos['direccion'] ?? '') }}">
                                @error('cliente_direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cliente_ciudad" class="form-label">
                                    <i class="fas fa-city"></i>Ciudad *
                                </label>
                                <input type="text" 
                                       class="form-control @error('cliente_ciudad') is-invalid @enderror" 
                                       id="cliente_ciudad" 
                                       name="cliente_ciudad" 
                                       required 
                                       placeholder="Ej: Ecatepec"
                                       value="{{ old('cliente_ciudad', $cliente_datos['ciudad'] ?? '') }}">
                                @error('cliente_ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cliente_estado" class="form-label">
                                    <i class="fas fa-flag"></i>Estado *
                                </label>
                                <select class="form-select @error('cliente_estado') is-invalid @enderror" 
                                        id="cliente_estado" name="cliente_estado" required>
                                    <option value="">Seleccionar estado</option>
                                    <option value="Estado de México" {{ old('cliente_estado', $cliente_datos['estado'] ?? '') == 'Estado de México' ? 'selected' : '' }}>Estado de México</option>
                                    <option value="Ciudad de México" {{ old('cliente_estado', $cliente_datos['estado'] ?? '') == 'Ciudad de México' ? 'selected' : '' }}>Ciudad de México</option>
                                    <option value="San Luis Potosí" {{ old('cliente_estado', $cliente_datos['estado'] ?? '') == 'San Luis Potosí' ? 'selected' : '' }}>San Luis Potosí</option>
                                    <option value="Nuevo León" {{ old('cliente_estado', $cliente_datos['estado'] ?? '') == 'Nuevo León' ? 'selected' : '' }}>Nuevo León</option>
                                </select>
                                @error('cliente_estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4"></div>
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label for="notas" class="form-label">
                                    <i class="fas fa-sticky-note"></i>Notas adicionales
                                </label>
                                <textarea class="form-control" 
                                          id="notas" 
                                          name="notas" 
                                          rows="3" 
                                          placeholder="Instrucciones especiales para la entrega, referencias, etc.">{{ old('notas', $cliente_datos['notas'] ?? '') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-grid">
                                <button type="button" id="verificar-cobertura" class="verificar-cobertura-btn">
                                    <i class="fas fa-search-location"></i>Verificar Cobertura de Envío
                                </button>
                                <small class="text-muted mt-2 text-center d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Verificamos si entregamos en tu zona (radio de 5-8km de nuestras sucursales)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-boxes"></i> Productos del Pedido</h5>
                    <button type="button" class="btn-custom btn-primary-custom" onclick="agregarProducto()">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                </div>
                <div class="card-body">
                    <div id="productos-container"></div>
                    
                    <div class="resumen-pedido">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Total del Pedido</h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="resumen-total" id="total-pedido">$0.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between gap-2">
                <a href="{{ route('gerente.pedidos') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" 
                        name="crear_pedido" 
                        id="btn-crear-pedido" 
                        class="btn-custom btn-success-custom"
                        disabled>
                    <i class="fas fa-save"></i> Crear Pedido
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let productoCount = 0;
        let coberturaVerificada = false;
        const productos = @json($productos);
        const csrfToken = '{{ csrf_token() }}';
        const sucursalId = '{{ session('sucursal_id') }}';
        const sucursalNombre = '{{ session('sucursal_nombre') }}';

        @if(isset($producto_precargado) && $producto_precargado)
        const productoPrecargado = {
            id: {{ $producto_precargado->id }},
            nombre: '{{ $producto_precargado->nombre }}',
            precio: {{ $producto_precargado->precio }},
            precio_final: {{ $precio_con_oferta ?? $producto_precargado->precio }},
            en_oferta: {{ ($producto_precargado->ofertas->isNotEmpty() ?? false) ? 'true' : 'false' }},
            descuento: {{ ($producto_precargado->ofertas->first()->valor ?? 0) }},
            existencias: {{ $producto_precargado->existencias ?? 0 }}
        };
        @else
        const productoPrecargado = null;
        @endif

        function initAutocomplete() {
            const direccionInput = document.getElementById('cliente_direccion');
            if (direccionInput) {
                const autocomplete = new google.maps.places.Autocomplete(direccionInput, {
                    types: ['address'],
                    componentRestrictions: {country: 'mx'},
                    fields: ['address_components', 'formatted_address']
                });
                
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place.formatted_address) {
                        direccionInput.value = place.formatted_address;
                        
                        if (place.address_components) {
                            place.address_components.forEach(component => {
                                if (component.types.includes('locality')) {
                                    document.getElementById('cliente_ciudad').value = component.long_name;
                                }
                                if (component.types.includes('administrative_area_level_1')) {
                                    const estadoSelect = document.getElementById('cliente_estado');
                                    for (let i = 0; i < estadoSelect.options.length; i++) {
                                        if (estadoSelect.options[i].text === component.long_name) {
                                            estadoSelect.value = estadoSelect.options[i].value;
                                            break;
                                        }
                                    }
                                }
                                if (component.types.includes('postal_code')) {
                                    document.getElementById('codigo_postal').value = component.long_name;
                                }
                            });
                        }
                    }
                });
            }
        }

        $('#btn-buscar-cliente').click(function() {
            const busqueda = $('#buscador-cliente').val().trim();
            
            if (busqueda.length < 3) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Búsqueda muy corta',
                    text: 'Ingrese al menos 3 caracteres para buscar',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            
            const btn = $(this);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Buscando...');
            btn.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("gerente.clientes.buscar") }}',
                method: 'POST',
                data: {
                    _token: csrfToken,
                    busqueda: busqueda
                },
                success: function(response) {
                    if (response.length > 0) {
                        let html = '<div class="list-group">';
                        response.forEach(cliente => {
                            html += `
                                <a href="#" class="list-group-item list-group-item-action" 
                                   onclick="seleccionarCliente('${cliente.nombre.replace(/'/g, "\\'")}', '${cliente.telefono}', '${cliente.email || ''}', '${cliente.direccion || ''}', '${cliente.ciudad || ''}', '${cliente.estado || ''}', '${cliente.codigo_postal || ''}')">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>${cliente.nombre}</strong><br>
                                            <small class="text-muted">
                                                <i class="fas fa-phone"></i> ${cliente.telefono}
                                                ${cliente.email ? ` | <i class="fas fa-envelope"></i> ${cliente.email}` : ''}
                                            </small>
                                        </div>
                                        <i class="fas fa-chevron-right text-primary"></i>
                                    </div>
                                </a>
                            `;
                        });
                        html += '</div>';
                        $('#resultados-busqueda').html(html).show();
                    } else {
                        $('#resultados-busqueda').html(`
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> No se encontraron clientes con "${busqueda}"
                            </div>
                        `).show();
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo completar la búsqueda',
                        confirmButtonColor: '#7fad39'
                    });
                },
                complete: function() {
                    btn.html('<i class="fas fa-search"></i> Buscar');
                    btn.prop('disabled', false);
                }
            });
        });

        window.seleccionarCliente = function(nombre, telefono, email, direccion, ciudad, estado, cp) {
            $('#cliente_nombre').val(nombre);
            $('#cliente_telefono').val(telefono);
            $('#cliente_email').val(email);
            $('#cliente_direccion').val(direccion);
            $('#cliente_ciudad').val(ciudad);
            $('#cliente_estado').val(estado);
            $('#codigo_postal').val(cp);
            
            $('#resultados-busqueda').hide();
            $('#buscador-cliente').val('');
            
            Swal.fire({
                icon: 'success',
                title: 'Cliente cargado',
                html: `Datos de <strong>${nombre}</strong> cargados correctamente`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        };

        $('#buscador-cliente').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $('#btn-buscar-cliente').click();
            }
        });

        $('#verificar-cobertura').click(function() {
            const direccion = $('#cliente_direccion').val().trim();
            const ciudad = $('#cliente_ciudad').val().trim();
            const estado = $('#cliente_estado').val().trim();
            
            if (!direccion || !ciudad || !estado) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos requeridos',
                    text: 'Complete dirección, ciudad y estado antes de verificar cobertura',
                    confirmButtonColor: '#7fad39'
                });
                return;
            }
            
            const btn = $(this);
            btn.addClass('loading').html('<i class="fas fa-spinner fa-spin"></i> Verificando...');
            
            $.ajax({
                url: '{{ route("gerente.cobertura.verificar") }}',
                type: 'POST',
                data: {
                    _token: csrfToken,
                    direccion: direccion,
                    ciudad: ciudad,
                    estado: estado,
                    sucursal_id: sucursalId
                },
                success: function(response) {
                    if (response.valido) {
                        coberturaVerificada = true;
                        $('#btn-crear-pedido').prop('disabled', false);
                        
                        $('#sucursal-nombre').text(response.sucursal_nombre);
                        $('#sucursal-direccion').text(response.sucursal_direccion);
                        $('#distancia').text(response.distancia);
                        $('#cobertura-verificada-box').show();
                        
                        $('input[name="distancia_km"]').remove();
                        $('#form-pedido').append(`<input type="hidden" name="distancia_km" value="${response.distancia}">`);
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Cobertura verificada!',
                            html: `
                                <p><strong>Sucursal asignada:</strong> ${response.sucursal_nombre}</p>
                                <p><strong>Distancia:</strong> ${response.distancia} km</p>
                                <p><strong>Dirección sucursal:</strong> ${response.sucursal_direccion}</p>
                            `,
                            confirmButtonColor: '#7fad39'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sin cobertura',
                            text: response.message,
                            confirmButtonColor: '#7fad39'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo verificar la cobertura',
                        confirmButtonColor: '#7fad39'
                    });
                },
                complete: function() {
                    btn.removeClass('loading').html('<i class="fas fa-search-location"></i> Verificar Cobertura de Envío');
                }
            });
        });

        $('#cambiar-direccion').click(function() {
            coberturaVerificada = false;
            $('#btn-crear-pedido').prop('disabled', true);
            $('#cobertura-verificada-box').hide();
            $('input[name="distancia_km"]').remove();
        });

        function agregarProducto() {
            const container = document.getElementById('productos-container');
            const index = productoCount++;
            
            let options = '<option value="">Seleccionar producto</option>';
            productos.forEach(p => {
                options += `<option value="${p.id}" 
                                data-precio="${p.precio}" 
                                data-existencias="${p.existencias}" 
                                data-nombre="${p.nombre}"
                                data-codigo="${p.codigo}">
                            ${p.codigo} - ${p.nombre} (${p.litros > 0 ? p.litros + ' lts' : 'Accesorio'})
                        </option>`;
            });
            
            const productoDiv = document.createElement('div');
            productoDiv.className = 'producto-item';
            productoDiv.id = `producto-${index}`;
            productoDiv.innerHTML = `
                <button type="button" class="btn-remove-producto" onclick="eliminarProducto(${index})" title="Eliminar producto">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="row g-3 align-items-end">
                    <div class="col-lg-6 col-md-12">
                        <label class="form-label">Producto *</label>
                        <select name="productos[${index}]" 
                                class="form-select select-producto" 
                                required
                                onchange="actualizarProducto(${index})" 
                                data-index="${index}">
                            ${options}
                        </select>
                        <small class="existencias-info" id="existencias-info-${index}"></small>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <label class="form-label">Cantidad *</label>
                        <input type="number" 
                               name="cantidades[${index}]" 
                               class="form-control cantidad" 
                               id="cantidad-${index}"
                               value="1" 
                               min="1" 
                               onchange="calcularSubtotal(${index})" 
                               oninput="validarCantidad(${index})" 
                               required>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <label class="form-label">Precio Unitario</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" 
                                   class="form-control precio-unitario" 
                                   id="precio-${index}" 
                                   readonly 
                                   value="0.00">
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <label class="form-label">Subtotal</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" 
                                   class="form-control subtotal-display" 
                                   id="subtotal-${index}" 
                                   readonly 
                                   value="0.00">
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(productoDiv);
            calcularTotal();
        }

        function actualizarProducto(index) {
            const select = document.querySelector(`#producto-${index} .select-producto`);
            const precioInput = document.getElementById(`precio-${index}`);
            const existenciasInfo = document.getElementById(`existencias-info-${index}`);
            const cantidadInput = document.getElementById(`cantidad-${index}`);
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                const productoId = option.value;
                const precioOriginal = parseFloat(option.dataset.precio);
                const existencias = parseInt(option.dataset.existencias);
                
                precioInput.value = precioOriginal.toFixed(2);
                existenciasInfo.innerHTML = '<span class="text-info">Verificando oferta...</span>';
                
                $.ajax({
                    url: '{{ route("gerente.productos.verificar-oferta") }}',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        producto_id: productoId
                    },
                    success: function(response) {
                        if (response.en_oferta) {
                            precioInput.value = response.precio_final.toFixed(2);
                            existenciasInfo.innerHTML = `
                                <span class="badge bg-danger" style="font-size: 0.75rem;">
                                    <i class="fas fa-tag"></i> -${Math.round(response.porcentaje)}% OFERTA
                                </span>
                                <span class="ms-2 ${existencias <= 5 ? 'existencias-baja' : 'existencias-normal'}">
                                    ${existencias} disponibles
                                </span>
                            `;
                        } else {
                            precioInput.value = precioOriginal.toFixed(2);
                            if (existencias <= 5) {
                                existenciasInfo.innerHTML = `<span class="existencias-baja">⚠️ Solo ${existencias} disponibles</span>`;
                            } else {
                                existenciasInfo.innerHTML = `<span class="existencias-normal">${existencias} disponibles</span>`;
                            }
                        }
                        validarCantidad(index);
                        calcularSubtotal(index);
                    },
                    error: function() {
                        precioInput.value = precioOriginal.toFixed(2);
                        if (existencias <= 5) {
                            existenciasInfo.innerHTML = `<span class="existencias-baja">⚠️ Solo ${existencias} disponibles</span>`;
                        } else {
                            existenciasInfo.innerHTML = `<span class="existencias-normal">${existencias} disponibles</span>`;
                        }
                        calcularSubtotal(index);
                    }
                });
            } else {
                precioInput.value = '0.00';
                existenciasInfo.textContent = '';
                document.getElementById(`subtotal-${index}`).value = '0.00';
                calcularTotal();
            }
        }

        function validarCantidad(index) {
            const select = document.querySelector(`#producto-${index} .select-producto`);
            const cantidadInput = document.getElementById(`cantidad-${index}`);
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                const existencias = parseInt(option.dataset.existencias);
                const cantidad = parseInt(cantidadInput.value) || 0;
                
                if (cantidad < 1) {
                    cantidadInput.value = 1;
                } else if (cantidad > existencias && existencias > 0) {
                    cantidadInput.value = existencias;
                }
            }
            calcularSubtotal(index);
        }

        function calcularSubtotal(index) {
            const precio = parseFloat(document.getElementById(`precio-${index}`).value) || 0;
            const cantidad = parseInt(document.getElementById(`cantidad-${index}`).value) || 0;
            const subtotal = precio * cantidad;
            document.getElementById(`subtotal-${index}`).value = subtotal.toFixed(2);
            calcularTotal();
        }

        function calcularTotal() {
            let total = 0;
            for (let i = 0; i < productoCount; i++) {
                const subtotal = parseFloat(document.getElementById(`subtotal-${i}`)?.value) || 0;
                total += subtotal;
            }
            document.getElementById('total-pedido').textContent = '$' + total.toFixed(2);
        }

        function eliminarProducto(index) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: '¿Está seguro de eliminar este producto del pedido?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const productoDiv = document.getElementById(`producto-${index}`);
                    if (productoDiv) {
                        productoDiv.remove();
                        calcularTotal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'El producto ha sido eliminado del pedido',
                            confirmButtonColor: '#7fad39',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                }
            });
        }

        $('#form-pedido').on('submit', function(e) {
            if (!coberturaVerificada) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Cobertura no verificada',
                    text: 'Debe verificar la cobertura antes de crear el pedido',
                    confirmButtonColor: '#7fad39'
                });
                return false;
            }
            
            const productosSeleccionados = $('.select-producto').filter(function() {
                return $(this).val() !== '';
            }).length;
            
            if (productosSeleccionados === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Sin productos',
                    text: 'Debe agregar al menos un producto al pedido',
                    confirmButtonColor: '#7fad39'
                });
                return false;
            }
            
            const vendedorSelect = $('#vendedor_responsable');
            if (vendedorSelect.length > 0 && vendedorSelect.val() === '') {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Vendedor no seleccionado',
                    text: 'Debe asignar el pedido a un vendedor',
                    confirmButtonColor: '#7fad39'
                });
                return false;
            }
            
            return true;
        });

        $(document).ready(function() {
            if (typeof google !== 'undefined') {
                initAutocomplete();
            }
            
            // 🚨 IMPORTANTE: NO agregar productos automáticamente
            // El usuario debe hacer clic en "Agregar Producto"
            
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    html: '{!! implode('<br>', $errors->all()) !!}',
                    confirmButtonColor: '#7fad39'
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
        });
    </script>
</body>
</html>