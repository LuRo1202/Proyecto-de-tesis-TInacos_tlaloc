@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido - Sucursal {{ session('sucursal_nombre') }}</title>
    
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
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 10px;
        }
        
        .header-subtitle {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 5px;
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
        
        .btn-secondary-custom {
            background: white;
            color: var(--gray);
            border: 1px solid var(--light-gray);
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--success), #218838);
            color: white;
        }
        
        .resumen-pedido {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary);
        }
        
        .info-resumen {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            padding: 8px;
        }
        
        .info-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
        }
        
        .info-value {
            font-weight: 600;
            color: var(--dark);
            font-size: 1rem;
        }
        
        .sucursal-info {
            background: rgba(52, 152, 219, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.2);
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .sucursal-icon {
            color: #3498db;
            font-size: 1.1rem;
        }
        
        .sucursal-text {
            font-weight: 500;
            color: #2c3e50;
            font-size: 0.85rem;
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
        }
        
        .card-header h5 {
            margin: 0;
            color: var(--dark);
            font-size: 1rem;
            display: flex;
            align-items: center;
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
        
        .badge-pendiente { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .badge-confirmado { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .badge-enviado { background: #cce5ff; color: #004085; border: 1px solid #b8daff; }
        .badge-entregado { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .badge-cancelado { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .form-label {
            font-weight: 500;
            color: #555;
            font-size: 0.85rem;
            margin-bottom: 5px;
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
        }
        
        .btn-estado-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .btn-estado {
            padding: 8px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            border: 2px solid transparent;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }
        
        .btn-estado:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-estado.active {
            border-color: var(--dark);
            box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
        }
        
        .btn-estado i { font-size: 0.9rem; }
        .btn-pendiente { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); color: #856404; }
        .btn-confirmado { background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); color: #0c5460; }
        .btn-enviado { background: linear-gradient(135deg, #cce5ff 0%, #b8daff 100%); color: #004085; }
        .btn-entregado { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; }
        .btn-cancelado { background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); color: #721c24; }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .info-cliente {
            background: white;
            border-radius: 6px;
            padding: 15px;
            border: 1px solid var(--light-gray);
        }
        
        .cliente-item {
            margin-bottom: 10px;
        }
        
        .cliente-label {
            font-weight: 600;
            color: var(--gray);
            font-size: 0.75rem;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .cliente-value {
            font-weight: 500;
            color: var(--dark);
            font-size: 0.85rem;
        }
        
        .sucursal-field {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            color: #495057;
        }
        
        .acciones-rapidas {
            display: grid;
            gap: 8px;
        }
        
        .btn-accion {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }
        
        .btn-accion:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-store {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-store:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(127, 173, 57, 0.2);
        }
        
        @media (max-width: 1200px) {
            .main-content { margin-left: 70px; }
        }
        @media (max-width: 768px) {
            .main-content { margin-left: 60px; }
        }
        @media (max-width: 576px) {
            .main-content { margin-left: 0; }
        }
        
        .timeline {
            position: relative;
            padding-left: 20px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e9ecef;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 10px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--primary);
            border: 2px solid white;
            box-shadow: 0 0 0 2px var(--primary-light);
        }
        
        .badge-responsable {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
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
                    <i class="fas fa-edit me-2"></i>Editar Pedido
                    <span class="sucursal-badge">
                        <i class="fas fa-store"></i> {{ session('sucursal_nombre') }}
                    </span>
                </h1>
                <div class="header-subtitle">
                    Gerente: {{ auth()->user()->nombre ?? 'Gerente' }}
                </div>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('gerente.pedidos.ver', $pedido->id) }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('gerente.pedidos') }}" class="btn-custom btn-primary-custom">
                    <i class="fas fa-list"></i> Ver Pedidos
                </a>
                <a href="{{ route('tienda') }}" target="_blank" class="btn-store">
                    <i class="fas fa-store me-1"></i> Ver Tienda
                </a>
            </div>
        </div>

        <!-- Resumen del Pedido -->
        <div class="resumen-pedido">
            <div class="info-resumen">
                <div class="info-item">
                    <div class="info-label">Pedido</div>
                    <div class="info-value">{{ $pedido->folio }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Cliente</div>
                    <div class="info-value">{{ $pedido->cliente_nombre }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total</div>
                    <div class="info-value text-primary">${{ number_format($pedido->total, 2) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Estado Actual</div>
                    <div>
                        <span class="badge-estado badge-{{ $pedido->estado }}">
                            <i class="fas fa-circle fa-xs"></i> {{ ucfirst($pedido->estado) }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Responsable Actual</div>
                    <div class="info-value">
                        @if($responsable_actual)
                            <span class="badge-responsable">
                                <i class="fas fa-user"></i> {{ $responsable_actual->nombre }}
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fas fa-question-circle"></i> Sin asignar
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="sucursal-info">
                <i class="fas fa-store sucursal-icon"></i>
                <div class="sucursal-text">
                    Este pedido pertenece a tu sucursal: <strong>{{ session('sucursal_nombre') }}</strong>
                    @if($pedido->distancia_km) | Distancia: <strong>{{ $pedido->distancia_km }} km</strong> @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Formulario de Edición -->
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-edit me-2"></i>Editar Información del Pedido</h5>
                        <small class="text-muted">Sucursal: {{ session('sucursal_nombre') }}</small>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('gerente.pedidos.update', $pedido->id) }}" id="formEditarPedido">
                            @csrf
                            @method('PUT')
                            
                            <!-- Estado del Pedido -->
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Estado del Pedido</label>
                                <div class="btn-estado-group">
                                    <button type="button" class="btn-estado btn-pendiente {{ $pedido->estado == 'pendiente' ? 'active' : '' }}" 
                                            onclick="seleccionarEstado('pendiente', this)">
                                        <i class="fas fa-clock"></i>
                                        <span>Pendiente</span>
                                    </button>
                                    
                                    <button type="button" class="btn-estado btn-confirmado {{ $pedido->estado == 'confirmado' ? 'active' : '' }}" 
                                            onclick="seleccionarEstado('confirmado', this)">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Confirmado</span>
                                    </button>
                                    
                                    <button type="button" class="btn-estado btn-enviado {{ $pedido->estado == 'enviado' ? 'active' : '' }}" 
                                            onclick="seleccionarEstado('enviado', this)">
                                        <i class="fas fa-truck"></i>
                                        <span>Enviado</span>
                                    </button>
                                    
                                    <button type="button" class="btn-estado btn-entregado {{ $pedido->estado == 'entregado' ? 'active' : '' }}" 
                                            onclick="seleccionarEstado('entregado', this)">
                                        <i class="fas fa-box-open"></i>
                                        <span>Entregado</span>
                                    </button>
                                    
                                    <button type="button" class="btn-estado btn-cancelado {{ $pedido->estado == 'cancelado' ? 'active' : '' }}" 
                                            onclick="seleccionarEstado('cancelado', this)">
                                        <i class="fas fa-times"></i>
                                        <span>Cancelado</span>
                                    </button>
                                </div>
                                <input type="hidden" name="estado" id="inputEstado" value="{{ $pedido->estado }}">
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sucursal Asignada</label>
                                    <div class="sucursal-field">{{ session('sucursal_nombre') }}</div>
                                    <input type="hidden" name="sucursal_asignada" value="{{ session('sucursal_nombre') }}">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha de Entrega</label>
                                    <input type="date" name="fecha_entrega" class="form-control-sm w-100" 
                                           value="{{ $pedido->fecha_entrega ? Carbon::parse($pedido->fecha_entrega)->format('Y-m-d') : '' }}"
                                           {{ $pedido->estado == 'entregado' ? 'required' : '' }}>
                                </div>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Distancia (km)</label>
                                    <input type="number" name="distancia_km" class="form-control-sm w-100" step="0.01" min="0"
                                           value="{{ $pedido->distancia_km }}" placeholder="Ej: 3.5">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Opciones</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pago_confirmado" id="pagoConfirmado" value="1"
                                                {{ $pedido->pago_confirmado ? 'checked' : '' }}
                                                {{ $pedido->estado == 'cancelado' ? 'disabled' : '' }}>
                                            <label class="form-check-label small" for="pagoConfirmado">Pago Confirmado</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="cobertura_verificada" id="coberturaVerificada" value="1"
                                                {{ $pedido->cobertura_verificada ? 'checked' : '' }}
                                                {{ $pedido->estado == 'cancelado' ? 'disabled' : '' }}>
                                            <label class="form-check-label small" for="coberturaVerificada">Cobertura verificada</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Reasignación de Vendedor -->
                            <div class="row g-2 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Reasignar Vendedor Responsable</label>
                                    <select name="vendedor_responsable" class="form-control-sm w-100">
                                        <option value="">-- Mantener actual --</option>
                                        @foreach($vendedores as $vendedor)
                                            <option value="{{ $vendedor->id }}" 
                                                {{ $vendedor_actual_id == $vendedor->id ? 'selected' : '' }}>
                                                {{ $vendedor->nombre }} ({{ $vendedor->usuario }})
                                            </option>
                                        @endforeach
                                        <option value="{{ auth()->id() }}">-- Tomar control (Yo mismo) --</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Notas Internas</label>
                                <textarea name="notas" class="form-control-sm w-100" rows="4" 
                                          placeholder="Notas adicionales...">{{ $pedido->notas }}</textarea>
                            </div>
                            
                            <div class="d-flex justify-content-between gap-2">
                                <a href="{{ route('gerente.pedidos.ver', $pedido->id) }}" class="btn-custom btn-secondary-custom">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn-custom btn-success-custom">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Historial del Pedido -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-history me-2"></i>Historial de Cambios</h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @if($historial->count() > 0)
                            <div class="timeline">
                                @foreach($historial as $registro)
                                    <div class="timeline-item mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="fw-bold">
                                                {{ $registro->usuario_nombre }}
                                                <small class="text-muted ms-2">({{ $registro->usuario_rol }})</small>
                                            </div>
                                            <small class="text-muted">{{ Carbon::parse($registro->fecha)->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <div class="small mt-1">
                                            <span class="badge bg-secondary">{{ $registro->accion }}</span>
                                            @if($registro->detalles) <span class="ms-2">{{ $registro->detalles }}</span> @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-info-circle me-2"></i> No hay historial registrado.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Información del Cliente y Acciones -->
            <div class="col-lg-4 mb-3">
                <!-- Información del Cliente -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5><i class="fas fa-user me-2"></i>Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-cliente">
                            <div class="cliente-item">
                                <div class="cliente-label">Nombre</div>
                                <div class="cliente-value">{{ $pedido->cliente_nombre }}</div>
                            </div>
                            <div class="cliente-item">
                                <div class="cliente-label">Teléfono</div>
                                <div class="cliente-value">
                                    <a href="tel:{{ $pedido->cliente_telefono }}" class="text-decoration-none text-primary">
                                        <i class="fas fa-phone fa-sm me-1"></i> {{ $pedido->cliente_telefono }}
                                    </a>
                                </div>
                            </div>
                            <div class="cliente-item">
                                <div class="cliente-label">Dirección</div>
                                <div class="cliente-value">{{ $pedido->cliente_direccion }}</div>
                            </div>
                            <div class="cliente-item">
                                <div class="cliente-label">Ciudad/Estado</div>
                                <div class="cliente-value">{{ $pedido->cliente_ciudad }}, {{ $pedido->cliente_estado }}</div>
                            </div>
                            <div class="cliente-item">
                                <div class="cliente-label">Método de Pago</div>
                                <div class="cliente-value">
                                    @php $metodos = ['en_linea' => 'En línea', 'efectivo' => 'Efectivo', 'transferencia' => 'Transferencia', 'manual' => 'Manual']; @endphp
                                    {{ $metodos[$pedido->metodo_pago] ?? $pedido->metodo_pago }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Acciones Rápidas -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <div class="acciones-rapidas">
                            @php
                                $whatsapp_msg = "Hola " . $pedido->cliente_nombre . ", te contacto por tu pedido " . $pedido->folio . " - " . session('sucursal_nombre');
                                $telefono_limpio = preg_replace('/[^0-9]/', '', $pedido->cliente_telefono);
                                $whatsapp_url = "https://wa.me/" . $telefono_limpio . "?text=" . urlencode($whatsapp_msg);
                            @endphp
                            
                            <a href="{{ $whatsapp_url }}" class="btn-accion" style="background: linear-gradient(135deg, #25D366, #128C7E); color: white;" target="_blank">
                                <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
                            </a>
                            
                            @if($pedido->estado != 'cancelado' && $pedido->estado != 'entregado')
                            <a href="{{ route('gerente.pedidos.procesar', ['accion' => 'cancelar', 'id' => $pedido->id]) }}" 
                               class="btn-accion" style="background: linear-gradient(135deg, var(--danger), #c82333); color: white;">
                                <i class="fas fa-times"></i> Cancelar Pedido
                            </a>
                            @endif
                            
                            @if(!$pedido->pago_confirmado && $pedido->estado != 'cancelado')
                            <a href="{{ route('gerente.pedidos.procesar', ['accion' => 'confirmar_pago', 'id' => $pedido->id]) }}" 
                               class="btn-accion" style="background: linear-gradient(135deg, var(--warning), #e0a800); color: #000;">
                                <i class="fas fa-money-check"></i> Confirmar Pago
                            </a>
                            @endif
                            
                            @if($pedido->pago_confirmado && $pedido->estado != 'cancelado')
                            <a href="{{ route('gerente.pedidos.procesar', ['accion' => 'desconfirmar_pago', 'id' => $pedido->id]) }}" 
                               class="btn-accion" style="background: linear-gradient(135deg, var(--info), #138496); color: white;">
                                <i class="fas fa-times-circle"></i> Desconfirmar Pago
                            </a>
                            @endif
                            
                            @if($vendedor_actual_id != auth()->id() && auth()->id())
                            <a href="{{ route('gerente.pedidos.procesar', ['accion' => 'tomar_control', 'id' => $pedido->id]) }}" 
                               class="btn-accion" style="background: linear-gradient(135deg, var(--warning), #e0a800); color: #000;">
                                <i class="fas fa-hand-paper"></i> Tomar Control
                            </a>
                            @endif

                            <!-- Botón de eliminar -->
                            <button type="button" class="btn-accion" style="background: linear-gradient(135deg, #6c757d, #495057); color: white;" onclick="confirmarEliminacion()">
                                <i class="fas fa-trash-alt"></i> Eliminar Pedido
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Información de Sucursal -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-store me-2"></i>Información de Sucursal</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-cliente">
                            <div class="cliente-item">
                                <div class="cliente-label">Sucursal</div>
                                <div class="cliente-value">{{ session('sucursal_nombre') }}</div>
                            </div>
                            <div class="cliente-item">
                                <div class="cliente-label">Pedido</div>
                                <div class="cliente-value"><span class="badge bg-light text-dark">#{{ $pedido->folio }}</span></div>
                            </div>
                            <div class="cliente-item">
                                <div class="cliente-label">Fecha de Creación</div>
                                <div class="cliente-value">{{ Carbon::parse($pedido->fecha)->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($pedido->fecha_confirmacion)
                            <div class="cliente-item">
                                <div class="cliente-label">Confirmado el</div>
                                <div class="cliente-value">{{ Carbon::parse($pedido->fecha_confirmacion)->format('d/m/Y H:i') }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if(session('swal'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '{{ session('swal.title') }}',
                text: '{{ session('swal.message') }}',
                icon: '{{ session('swal.type') }}',
                confirmButtonColor: '#7fad39'
            });
        });
        @endif

        // Función para seleccionar estado
        function seleccionarEstado(estado, elemento) {
            document.getElementById('inputEstado').value = estado;
            
            // Quitar clase active de todos los botones
            document.querySelectorAll('.btn-estado').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Agregar clase active al botón seleccionado
            elemento.classList.add('active');
            
            // Si se selecciona "entregado", establecer fecha actual
            if (estado === 'entregado') {
                const fechaInput = document.querySelector('input[name="fecha_entrega"]');
                if (!fechaInput.value) {
                    const today = new Date().toISOString().split('T')[0];
                    fechaInput.value = today;
                    fechaInput.required = true;
                }
            }
            
            // Si se selecciona "cancelado", deshabilitar checkboxes
            if (estado === 'cancelado') {
                document.getElementById('pagoConfirmado').disabled = true;
                document.getElementById('coberturaVerificada').disabled = true;
                document.getElementById('pagoConfirmado').checked = false;
            } else {
                document.getElementById('pagoConfirmado').disabled = false;
                document.getElementById('coberturaVerificada').disabled = false;
            }
        }

        function confirmarEliminacion() {
            Swal.fire({
                title: '¿Eliminar Pedido?',
                html: `¿Seguro de eliminar <strong>#{{ $pedido->folio }}</strong>?<br><small>Esta acción es irreversible y regresará el stock.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            
                            fetch(`/gerente/pedidos/eliminar/{{ $pedido->id }}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: '¡Eliminado!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#7fad39'
                                    }).then(() => {
                                        window.location.href = '{{ route("gerente.pedidos") }}';
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: data.message || 'No se pudo eliminar',
                                        icon: 'error',
                                        confirmButtonColor: '#7fad39'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error de conexión',
                                    icon: 'error',
                                    confirmButtonColor: '#7fad39'
                                });
                            });
                        }
                    });
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            @if($pedido->estado == 'cancelado')
            document.getElementById('pagoConfirmado').disabled = true;
            document.getElementById('coberturaVerificada').disabled = true;
            @endif
        });
    </script>
</body>
</html>