@php
    use Carbon\Carbon;
    
    // Estados siguientes permitidos
    $estadoActual = $pedido->estado;
    $estadosTexto = [
        'pendiente' => 'Pendiente',
        'confirmado' => 'Confirmado',
        'enviado' => 'Enviado',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado'
    ];
    
    // Transiciones permitidas
    $transicionesEstado = [
        'pendiente' => ['confirmado', 'cancelado'],
        'confirmado' => ['enviado', 'cancelado'],
        'enviado' => ['entregado', 'cancelado'],
        'entregado' => [],
        'cancelado' => []
    ];
    
    $estadosSiguientes = $transicionesEstado[$estadoActual] ?? [];
    
    // Mensaje de ayuda
    $mensajeAyuda = '';
    if ($estadoActual == 'pendiente') {
        $mensajeAyuda = 'Solo puedes avanzar a <strong>Confirmado</strong> o <strong>Cancelado</strong>';
    } elseif ($estadoActual == 'confirmado') {
        $mensajeAyuda = 'Solo puedes avanzar a <strong>Enviado</strong> o <strong>Cancelado</strong>';
    } elseif ($estadoActual == 'enviado') {
        $mensajeAyuda = 'Solo puedes avanzar a <strong>Entregado</strong> o <strong>Cancelado</strong>';
    } elseif ($estadoActual == 'entregado') {
        $mensajeAyuda = '⚠️ Este pedido ya fue entregado. No se puede modificar el estado.';
    } elseif ($estadoActual == 'cancelado') {
        $mensajeAyuda = '⚠️ Este pedido está cancelado. No se puede modificar el estado.';
    }
    
    // Si el pedido está entregado o cancelado, deshabilitar campos de estado
    $pedidoFinalizado = in_array($estadoActual, ['entregado', 'cancelado']);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido - Tinacos Tláloc</title>
    
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
        
        .btn-estado:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-estado:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
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
            justify-content: center;
        }
        
        .btn-accion:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .responsable-selector {
            margin-top: 10px;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border: 1px solid var(--light-gray);
        }
        
        .usuario-option {
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .usuario-option:hover {
            border-color: var(--primary);
            background-color: rgba(127, 173, 57, 0.05);
        }
        
        .usuario-option.selected {
            border-color: var(--primary);
            background-color: rgba(127, 173, 57, 0.1);
        }
        
        .badge-rol {
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 12px;
        }
        
        .badge-vendedor {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        
        .badge-gerente {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
        }
        
        .responsable-info {
            background: rgba(155, 89, 182, 0.1);
            border: 1px solid rgba(155, 89, 182, 0.2);
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .responsable-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #8e44ad;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .responsable-name {
            font-weight: 600;
            color: #9b59b6;
            font-size: 0.9rem;
        }
        
        .badge-sucursal {
            background: rgba(127, 173, 57, 0.1);
            color: var(--primary-dark);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
        }
        
        .alert-info-custom {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }
        
        @media (max-width: 1200px) { .main-content { margin-left: 70px; padding: 15px; } }
        @media (max-width: 992px) { .header-bar { flex-direction: column; align-items: stretch; text-align: center; } .header-actions { justify-content: center; } }
        @media (max-width: 768px) { .main-content { margin-left: 60px; padding: 12px; } .btn-estado-group { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 576px) { .main-content { margin-left: 0; padding: 10px; } .btn-estado-group { grid-template-columns: 1fr; } }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .card, .resumen-pedido { animation: fadeIn 0.3s ease-out; }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--light-gray); border-radius: 3px; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 3px; }
    </style>
</head>
<body>
    @include('admin.layouts.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-edit me-2"></i>Editar Pedido
                </h1>
                @if($pedido->sucursal)
                <div class="badge-sucursal">
                    <i class="fas fa-store"></i> {{ $pedido->sucursal->nombre }}
                </div>
                @endif
            </div>
            
            <div class="header-actions">
                <a href="{{ route('admin.pedidos.ver', $pedido->id) }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('admin.pedidos') }}" class="btn-custom btn-primary-custom">
                    <i class="fas fa-list"></i> Ver Todos
                </a>
            </div>
        </div>

        <!-- Mensaje de ayuda sobre estados permitidos -->
        @if($mensajeAyuda)
        <div class="alert-info-custom">
            <i class="fas fa-info-circle me-2"></i> {!! $mensajeAyuda !!}
        </div>
        @endif

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
            </div>
        </div>

        <div class="row">
            <!-- Formulario de Edición -->
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-edit me-2"></i>Editar Información del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.pedidos.update', $pedido->id) }}" id="formEditarPedido">
                            @csrf
                            @method('PUT')
                            
                            <!-- Estado del Pedido -->
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Estado del Pedido</label>
                                <div class="btn-estado-group">
                                    @php
                                        $botones = [
                                            'pendiente' => ['icono' => 'fa-clock', 'texto' => 'Pendiente', 'clase' => 'btn-pendiente'],
                                            'confirmado' => ['icono' => 'fa-check-circle', 'texto' => 'Confirmado', 'clase' => 'btn-confirmado'],
                                            'enviado' => ['icono' => 'fa-truck', 'texto' => 'Enviado', 'clase' => 'btn-enviado'],
                                            'entregado' => ['icono' => 'fa-box-open', 'texto' => 'Entregado', 'clase' => 'btn-entregado'],
                                            'cancelado' => ['icono' => 'fa-times', 'texto' => 'Cancelado', 'clase' => 'btn-cancelado']
                                        ];
                                    @endphp
                                    
                                    @foreach($botones as $estado => $info)
                                        @php
                                            $puedeSeleccionar = ($estado == $estadoActual) || in_array($estado, $estadosSiguientes);
                                            $esActivo = ($estadoActual == $estado);
                                        @endphp
                                        
                                        <button type="button" 
                                                class="btn-estado {{ $info['clase'] }} {{ $esActivo ? 'active' : '' }}"
                                                onclick="seleccionarEstado('{{ $estado }}', this)"
                                                {{ !$puedeSeleccionar ? 'disabled' : '' }}>
                                            <i class="fas {{ $info['icono'] }}"></i>
                                            <span>{{ $info['texto'] }}</span>
                                            @if(!$puedeSeleccionar && $estado != $estadoActual)
                                                <small class="d-block" style="font-size: 0.6rem;">❌ No permitido</small>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="estado" id="inputEstado" value="{{ $pedido->estado }}">
                                
                                @if($pedidoFinalizado)
                                <div class="alert alert-secondary mt-2 small">
                                    <i class="fas fa-lock me-1"></i> 
                                    @if($estadoActual == 'entregado')
                                        El pedido ya fue entregado. No se puede modificar el estado.
                                    @else
                                        El pedido está cancelado. No se puede modificar el estado.
                                    @endif
                                </div>
                                @endif
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sucursal</label>
                                    <select name="sucursal_id" class="form-control-sm" id="selectSucursal" onchange="cambiarSucursal()" {{ $pedidoFinalizado ? 'disabled' : '' }}>
                                        <option value="">Seleccionar sucursal...</option>
                                        @foreach($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}" {{ $pedido->sucursal_id == $sucursal->id ? 'selected' : '' }}>
                                            {{ $sucursal->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha de Entrega</label>
                                    <input type="date" name="fecha_entrega" class="form-control-sm" 
                                           value="{{ $pedido->fecha_entrega ? Carbon::parse($pedido->fecha_entrega)->format('Y-m-d') : '' }}"
                                           {{ $pedidoFinalizado ? 'disabled' : '' }}>
                                </div>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Distancia (km)</label>
                                    <input type="number" name="distancia_km" class="form-control-sm" step="0.01" min="0"
                                           value="{{ $pedido->distancia_km }}"
                                           {{ $pedidoFinalizado ? 'disabled' : '' }}>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Opciones</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pago_confirmado" id="pagoConfirmado"
                                                {{ $pedido->pago_confirmado ? 'checked' : '' }}
                                                {{ $pedidoFinalizado ? 'disabled' : '' }}>
                                            <label class="form-check-label small" for="pagoConfirmado">
                                                Pago Confirmado
                                            </label>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="cobertura_verificada" id="coberturaVerificada"
                                                {{ $pedido->cobertura_verificada ? 'checked' : '' }}
                                                {{ $pedidoFinalizado ? 'disabled' : '' }}>
                                            <label class="form-check-label small" for="coberturaVerificada">
                                                Cobertura Verificada
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Asignación de Responsable (solo si no está finalizado) -->
                            @if(!$pedidoFinalizado)
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">Asignar Responsable</label>
                                
                                @if($responsable_actual)
                                <div class="responsable-info mb-3">
                                    <div class="responsable-title">
                                        <i class="fas fa-user"></i> Responsable Actual
                                    </div>
                                    <div class="responsable-name">
                                        {{ $responsable_actual->nombre }}
                                    </div>
                                    <div class="responsable-rol">
                                        {{ ucfirst($responsable_actual->rol) }} - {{ $responsable_actual->usuario }}
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="eliminarResponsable()">
                                        <i class="fas fa-user-times me-1"></i> Remover Responsable
                                    </button>
                                </div>
                                @endif
                                
                                <div id="selectorResponsable" style="{{ empty($usuarios_sucursal) ? 'display: none;' : '' }}">
                                    <div class="responsable-selector">
                                        <p class="text-muted small mb-3">
                                            Selecciona un vendedor o gerente de la sucursal para asignar como responsable:
                                        </p>
                                        
                                        <div id="usuariosContainer">
                                            @if(count($usuarios_sucursal) > 0)
                                                @foreach($usuarios_sucursal as $usuario)
                                                <div class="usuario-option" 
                                                     onclick="seleccionarUsuario(this)"
                                                     data-user-id="{{ $usuario->id }}"
                                                     data-user-name="{{ $usuario->nombre }}"
                                                     data-user-rol="{{ $usuario->rol }}"
                                                     id="option-{{ $usuario->id }}">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <div class="fw-medium">{{ $usuario->nombre }}</div>
                                                            <small class="text-muted">{{ $usuario->usuario }}</small>
                                                        </div>
                                                        <span class="badge-rol badge-{{ $usuario->rol }}">
                                                            {{ ucfirst($usuario->rol) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @else
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    No hay usuarios disponibles en esta sucursal.
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <input type="hidden" name="responsable_id" id="inputResponsable" value="{{ $responsable_actual ? $responsable_actual->id : '' }}">
                                    </div>
                                </div>
                                
                                <div id="sinSucursal" style="{{ $pedido->sucursal_id ? 'display: none;' : '' }}">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Selecciona una sucursal para poder asignar un responsable.
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="mb-4">
                                <label class="form-label">Notas Internas</label>
                                <textarea name="notas" class="form-control-sm" rows="4" 
                                          placeholder="Notas adicionales sobre el pedido..."
                                          {{ $pedidoFinalizado ? 'disabled' : '' }}>{{ $pedido->notas }}</textarea>
                                <div class="form-text small">Estas notas son solo para uso interno.</div>
                            </div>
                            
                            <div class="d-flex justify-content-between gap-2">
                                <a href="{{ route('admin.pedidos.ver', $pedido->id) }}" class="btn-custom btn-secondary-custom">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                @if(!$pedidoFinalizado)
                                <button type="submit" class="btn-custom btn-success-custom">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                @else
                                <div class="alert alert-secondary mb-0 py-2 px-3 small">
                                    <i class="fas fa-lock me-1"></i> Este pedido no puede ser editado
                                </div>
                                @endif
                            </div>
                        </form>
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
                                    @php
                                    $metodos = [
                                        'en_linea' => 'En línea (Tarjeta/PayPal)',
                                        'efectivo' => 'Efectivo contra entrega',
                                        'transferencia' => 'Transferencia bancaria',
                                        'manual' => 'Manual'
                                    ];
                                    echo $metodos[$pedido->metodo_pago] ?? $pedido->metodo_pago;
                                    @endphp
                                </div>
                            </div>
                            
                            @if($pedido->codigo_postal)
                            <div class="cliente-item">
                                <div class="cliente-label">Código Postal</div>
                                <div class="cliente-value">{{ $pedido->codigo_postal }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Acciones Rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <div class="acciones-rapidas">
                            @php
                                $whatsapp_msg = "Hola " . $pedido->cliente_nombre . ", te contacto por tu pedido " . $pedido->folio . " en Tanques Tláloc.";
                                $whatsapp_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $pedido->cliente_telefono) . "?text=" . urlencode($whatsapp_msg);
                            @endphp
                            
                            <a href="{{ $whatsapp_url }}"
                               class="btn-accion" 
                               style="background: linear-gradient(135deg, #25D366, #128C7E); color: white;"
                               target="_blank">
                                <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
                            </a>
                            
                            @if(!$pedido->pago_confirmado && !$pedidoFinalizado)
                            <a href="{{ route('admin.pedidos.procesar', ['accion' => 'confirmar_pago', 'id' => $pedido->id]) }}"
                               class="btn-accion" 
                               style="background: linear-gradient(135deg, var(--warning), #e0a800); color: #000;">
                                <i class="fas fa-money-check"></i> Confirmar Pago
                            </a>
                            @endif
                            
                            @if(!$pedidoFinalizado)
                            <button type="button" 
                                    class="btn-accion" 
                                    style="background: linear-gradient(135deg, var(--danger), #c82333); color: white;"
                                    onclick="confirmarEliminacion({{ $pedido->id }}, '{{ $pedido->folio }}')">
                                <i class="fas fa-trash"></i> Eliminar Pedido
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('swal_pedido'))
    <script>
        Swal.fire({
            icon: '{{ session('swal_pedido')['type'] }}',
            title: '{{ session('swal_pedido')['title'] }}',
            text: '{{ session('swal_pedido')['message'] }}',
            confirmButtonColor: '#7fad39'
        });
    </script>
    @endif

    <script>
        let responsableSeleccionado = '{{ $responsable_actual ? $responsable_actual->id : '' }}';
        let responsableSeleccionadoNombre = '{{ $responsable_actual ? $responsable_actual->nombre : '' }}';
        
        const estadosSiguientes = @json($estadosSiguientes);
        const estadoActual = '{{ $estadoActual }}';
        
        function seleccionarEstado(estado, elemento) {
            // Validar si el estado está permitido
            if (estado !== estadoActual && !estadosSiguientes.includes(estado)) {
                const estadosTexto = {
                    'pendiente': 'Pendiente', 'confirmado': 'Confirmado',
                    'enviado': 'Enviado', 'entregado': 'Entregado', 'cancelado': 'Cancelado'
                };
                Swal.fire({
                    title: 'Cambio no permitido',
                    html: `No puedes cambiar el estado de <strong>${estadosTexto[estadoActual]}</strong> a <strong>${estadosTexto[estado]}</strong>.<br><br><small>Solo puedes avanzar en el flujo del pedido.</small>`,
                    icon: 'warning',
                    confirmButtonColor: '#7fad39'
                });
                return;
            }
            
            document.getElementById('inputEstado').value = estado;
            
            document.querySelectorAll('.btn-estado').forEach(btn => {
                btn.classList.remove('active');
            });
            
            elemento.classList.add('active');
            
            Swal.fire({
                title: 'Estado seleccionado',
                text: 'Estado cambiado a: ' + estado.charAt(0).toUpperCase() + estado.slice(1),
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500
            });
        }
        
        function seleccionarUsuario(element) {
            const userId = element.getAttribute('data-user-id');
            const userName = element.getAttribute('data-user-name');
            const userRol = element.getAttribute('data-user-rol');
            
            document.querySelectorAll('.usuario-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            element.classList.add('selected');
            
            responsableSeleccionado = userId;
            responsableSeleccionadoNombre = userName;
            document.getElementById('inputResponsable').value = userId;
            
            Swal.fire({
                title: 'Responsable Seleccionado',
                html: `<strong>${userName}</strong> (${userRol}) será asignado como responsable.`,
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        }
        
        function eliminarResponsable() {
            Swal.fire({
                title: '¿Remover Responsable?',
                html: `¿Deseas remover a <strong>${responsableSeleccionadoNombre}</strong> como responsable?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, remover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelectorAll('.usuario-option').forEach(option => {
                        option.classList.remove('selected');
                    });
                    
                    responsableSeleccionado = '';
                    responsableSeleccionadoNombre = '';
                    document.getElementById('inputResponsable').value = '';
                    
                    const responsableInfo = document.querySelector('.responsable-info');
                    if (responsableInfo) {
                        responsableInfo.style.display = 'none';
                    }
                    
                    Swal.fire({
                        title: 'Responsable Removido',
                        text: 'El responsable ha sido removido del pedido.',
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        }
        
        function cambiarSucursal() {
            const sucursalId = document.getElementById('selectSucursal').value;
            const selector = document.getElementById('selectorResponsable');
            const sinSucursal = document.getElementById('sinSucursal');
            
            if (sucursalId) {
                window.location.href = '{{ route("admin.pedidos.editar", $pedido->id) }}?sucursal_id=' + sucursalId;
            } else {
                selector.style.display = 'none';
                sinSucursal.style.display = 'block';
                document.getElementById('inputResponsable').value = '';
            }
        }
        
        function confirmarEliminacion(id, folio) {
            Swal.fire({
                title: '¿Eliminar Pedido?',
                html: `¿Estás seguro de eliminar el pedido <strong>#${folio}</strong>?<br><br><small class="text-danger">Esta acción no se puede deshacer y regresará el stock.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor, espera un momento.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            window.location.href = '{{ url("admin/pedidos") }}/' + id + '/eliminar';
                        }
                    });
                }
            });
        }
        
        document.getElementById('formEditarPedido')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const sucursalSeleccionada = document.getElementById('selectSucursal').value;
            const nuevoResponsable = document.getElementById('inputResponsable').value;
            const tieneResponsable = '{{ $responsable_actual ? 'true' : 'false' }}';
            
            if (sucursalSeleccionada && !nuevoResponsable && tieneResponsable === 'false') {
                Swal.fire({
                    title: '¿Continuar sin Responsable?',
                    text: 'Has seleccionado una sucursal pero no has asignado un responsable. ¿Deseas continuar?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#7fad39',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, continuar',
                    cancelButtonText: 'Asignar responsable'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Guardando...',
                            text: 'Por favor espera',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                                e.target.submit();
                            }
                        });
                    }
                });
                return;
            }
            
            Swal.fire({
                title: 'Guardando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    e.target.submit();
                }
            });
        });
        
        @if($responsable_actual)
        document.addEventListener('DOMContentLoaded', function() {
            const responsableOption = document.getElementById('option-{{ $responsable_actual->id }}');
            if (responsableOption) {
                setTimeout(() => {
                    responsableOption.classList.add('selected');
                }, 300);
            }
        });
        @endif
    </script>
</body>
</html>