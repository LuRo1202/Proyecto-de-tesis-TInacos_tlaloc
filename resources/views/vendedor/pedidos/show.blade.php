@extends('vendedor.layouts.app')

@section('title', 'Pedido #' . $pedido->folio)

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="header-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-file-invoice me-2"></i>Pedido #{{ $pedido->folio }}
                </h1>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y H:i') }} |
                    <i class="fas fa-store ms-2 me-1"></i> {{ $sucursal->nombre }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('vendedor.pedidos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
                <a href="{{ route('vendedor.pedidos.seguimiento', $pedido->id) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-clipboard-check me-1"></i> Seguimiento
                </a>
            </div>
        </div>
    </div>

    <!-- Alerta informativa sobre estados permitidos -->
    @php
        $mensajeAyuda = '';
        if ($pedido->estado == 'pendiente') {
            $mensajeAyuda = 'Solo puedes avanzar a <strong>Confirmado</strong> o <strong>Cancelado</strong>';
        } elseif ($pedido->estado == 'confirmado') {
            $mensajeAyuda = 'Solo puedes avanzar a <strong>Enviado</strong> o <strong>Cancelado</strong>';
        } elseif ($pedido->estado == 'enviado') {
            $mensajeAyuda = 'Solo puedes avanzar a <strong>Entregado</strong> o <strong>Cancelado</strong>';
        } elseif ($pedido->estado == 'entregado') {
            $mensajeAyuda = 'Este pedido ya fue entregado. No se puede modificar.';
        } elseif ($pedido->estado == 'cancelado') {
            $mensajeAyuda = 'Este pedido está cancelado. No se puede modificar.';
        }
    @endphp
    
    @if($mensajeAyuda && !in_array($pedido->estado, ['entregado', 'cancelado']))
    <div class="alert alert-info mb-3">
        <i class="fas fa-info-circle me-2"></i> {!! $mensajeAyuda !!}
    </div>
    @elseif(in_array($pedido->estado, ['entregado', 'cancelado']))
    <div class="alert alert-secondary mb-3">
        <i class="fas fa-lock me-2"></i> {!! $mensajeAyuda !!}
    </div>
    @endif

    <div class="row">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <!-- Resumen del pedido -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Resumen del Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge-estado badge-{{ $pedido->estado }} fs-6 p-2">
                                <i class="fas fa-circle fa-xs me-1"></i>{{ ucfirst($pedido->estado) }}
                            </span>
                            <span class="badge-pago ms-2 {{ $pedido->pago_confirmado ? 'bg-success' : 'bg-warning' }} p-2">
                                <i class="fas fa-{{ $pedido->pago_confirmado ? 'check' : 'clock' }} me-1"></i>
                                Pago {{ $pedido->pago_confirmado ? 'Confirmado' : 'Pendiente' }}
                            </span>
                        </div>
                        <div class="fw-bold fs-4" style="color: var(--primary);">
                            ${{ number_format($pedido->total, 2) }}
                        </div>
                    </div>

                    <!-- Productos con información detallada -->
                    <h6 class="border-bottom pb-2 mb-3">Productos</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->items as $item)
                                @php
                                    $productoInfo = $item->producto_id ? \App\Models\Producto::with(['color', 'categoria'])->find($item->producto_id) : null;
                                    $colorNombre = $productoInfo && $productoInfo->color ? $productoInfo->color->nombre : null;
                                    $colorHex = $productoInfo && $productoInfo->color ? $productoInfo->color->codigo_hex : '#ccc';
                                    $capacidad = $productoInfo ? $productoInfo->litros : null;
                                    $codigo = $productoInfo ? $productoInfo->codigo : null;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($colorNombre)
                                            <span class="color-dot" style="background-color: {{ $colorHex }};"></span>
                                            @endif
                                            <div>
                                                <strong>{{ $item->producto_nombre }}</strong>
                                                <div class="small text-muted">
                                                    @if($codigo)
                                                        <span class="badge bg-light text-dark me-1">Código: {{ $codigo }}</span>
                                                    @endif
                                                    @if($colorNombre)
                                                        <span class="badge bg-light text-dark me-1">Color: {{ $colorNombre }}</span>
                                                    @endif
                                                    @if($capacidad)
                                                        <span class="badge bg-light text-dark me-1">{{ $capacidad }} litros</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-primary">{{ $item->cantidad }}</span></td>
                                    <td class="text-end">${{ number_format($item->precio, 2) }}</td>
                                    <td class="text-end fw-bold">${{ number_format($item->cantidad * $item->precio, 2) }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-light">
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold fs-5" style="color: var(--primary);">
                                        ${{ number_format($pedido->total, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Formulario de actualización (solo si no está entregado o cancelado) -->
            @if(!in_array($pedido->estado, ['entregado', 'cancelado']))
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Actualizar Estado del Pedido</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vendedor.pedidos.update', $pedido->id) }}" id="updateForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">Selecciona el nuevo estado:</label>
                            <div class="btn-estado-group">
                                @php
                                    $botones = [
                                        'pendiente' => ['icono' => 'fa-clock', 'texto' => 'Pendiente', 'clase' => 'btn-pendiente'],
                                        'confirmado' => ['icono' => 'fa-check-circle', 'texto' => 'Confirmado', 'clase' => 'btn-confirmado'],
                                        'enviado' => ['icono' => 'fa-truck', 'texto' => 'Enviado', 'clase' => 'btn-enviado'],
                                        'entregado' => ['icono' => 'fa-box-open', 'texto' => 'Entregado', 'clase' => 'btn-entregado'],
                                        'cancelado' => ['icono' => 'fa-times-circle', 'texto' => 'Cancelado', 'clase' => 'btn-cancelado']
                                    ];
                                @endphp
                                
                                @foreach($botones as $estado => $info)
                                    @php
                                        $puedeSeleccionar = ($estado == $pedido->estado) || in_array($estado, $estadosSiguientes);
                                        $esActivo = ($pedido->estado == $estado);
                                    @endphp
                                    
                                    <button type="button" 
                                            class="btn-estado {{ $info['clase'] }} {{ $esActivo ? 'active' : '' }}"
                                            onclick="seleccionarEstado('{{ $estado }}', this)"
                                            {{ !$puedeSeleccionar ? 'disabled' : '' }}>
                                        <i class="fas {{ $info['icono'] }} fa-lg"></i>
                                        <span>{{ $info['texto'] }}</span>
                                        @if(!$puedeSeleccionar && $estado != $pedido->estado)
                                            <small class="d-block" style="font-size: 0.6rem;">❌ No permitido</small>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="estado" id="inputEstado" value="{{ $pedido->estado }}">
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pago</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" 
                                           name="pago_confirmado" value="1" 
                                           id="pagoConfirmado" {{ $pedido->pago_confirmado ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pagoConfirmado">Confirmado</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fecha Entrega</label>
                                <input type="date" name="fecha_entrega" class="form-control" 
                                       value="{{ $pedido->fecha_entrega ? \Carbon\Carbon::parse($pedido->fecha_entrega)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Comentario</label>
                                <textarea name="comentario" class="form-control" rows="2" 
                                          placeholder="Motivo del cambio o notas adicionales..."></textarea>
                                <small class="text-muted">Este comentario se guardará en el historial</small>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna lateral -->
        <div class="col-lg-4">
            <!-- Información del cliente -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Cliente</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $pedido->cliente_nombre }}</strong></p>
                    <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $pedido->cliente_telefono }}</p>
                    <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ $pedido->cliente_direccion }}</p>
                    <p class="mb-1"><i class="fas fa-city me-2"></i>{{ $pedido->cliente_ciudad }}, {{ $pedido->cliente_estado }}</p>
                    <p class="mb-0"><i class="fas fa-mail-bulk me-2"></i>CP: {{ $pedido->codigo_postal }}</p>
                    @if($pedido->notas)
                    <div class="mt-2 p-2 bg-light rounded">
                        <small><i class="fas fa-sticky-note me-1"></i>{{ $pedido->notas }}</small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Información de entrega -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Entrega</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><i class="fas fa-store me-2"></i>Sucursal: {{ $pedido->sucursal->nombre }}</p>
                    @if($pedido->distancia_km)
                    <p class="mb-1"><i class="fas fa-road me-2"></i>Distancia: {{ $pedido->distancia_km }} km</p>
                    @endif
                    @if($pedido->fecha_entrega)
                    <p class="mb-0"><i class="fas fa-calendar-check me-2"></i>Entrega: {{ \Carbon\Carbon::parse($pedido->fecha_entrega)->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>

            <!-- Responsables -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Responsables</h5>
                </div>
                <div class="card-body">
                    @forelse($pedido->responsables as $responsable)
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                             style="width: 32px; height: 32px; font-size: 0.8rem;">
                            {{ substr($responsable->usuario->nombre, 0, 2) }}
                        </div>
                        <div>
                            <strong>{{ $responsable->usuario->nombre }}</strong>
                            <br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($responsable->fecha_asignacion)->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">Sin responsables asignados</p>
                    @endforelse
                </div>
            </div>

            <!-- Historial -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial</h5>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @forelse($pedido->historial as $cambio)
                    <div class="mb-3 pb-2 border-bottom">
                        <small class="text-muted">{{ \Carbon\Carbon::parse($cambio->fecha)->format('d/m/Y H:i') }}</small>
                        <div><strong>{{ $cambio->usuario->nombre ?? 'Sistema' }}</strong></div>
                        <small>{{ $cambio->detalles }}</small>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">Sin historial</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    :root {
        --primary: #7fad39;
        --primary-dark: #5a8a20;
    }
    
    .badge-estado {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .badge-pendiente { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
    .badge-confirmado { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    .badge-enviado { background: #cce5ff; color: #004085; border: 1px solid #b8daff; }
    .badge-entregado { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .badge-cancelado { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    
    .badge-pago {
        padding: 6px 12px;
        border-radius: 20px;
        color: white;
        font-size: 0.9rem;
    }
    
    .btn-estado-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 8px;
        margin-bottom: 20px;
    }
    
    .btn-estado {
        padding: 12px 8px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        width: 100%;
    }
    
    .btn-estado:hover:not(:disabled) {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
    
    .btn-estado:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-estado.active {
        border-color: #333;
        box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
        transform: scale(1.02);
    }
    
    .btn-estado i { font-size: 1.3rem; }
    
    .btn-pendiente { background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); color: #856404; }
    .btn-confirmado { background: linear-gradient(135deg, #d1ecf1 0%, #b6e4f0 100%); color: #0c5460; }
    .btn-enviado { background: linear-gradient(135deg, #cce5ff 0%, #b8d9ff 100%); color: #004085; }
    .btn-entregado { background: linear-gradient(135deg, #d4edda 0%, #c1e6cf 100%); color: #155724; }
    .btn-cancelado { background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%); color: #721c24; }
    
    .color-dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid #ddd;
    }
    
    @media (max-width: 768px) {
        .btn-estado-group { grid-template-columns: repeat(2, 1fr); }
        .btn-estado { padding: 10px 5px; font-size: 0.75rem; }
        .btn-estado i { font-size: 1.1rem; }
    }
    @media (max-width: 480px) { .btn-estado-group { grid-template-columns: 1fr; } }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // $estadosSiguientes es un array directo desde el controlador
    // Ejemplo: si estadoActual = 'pendiente', entonces estadosSiguientes = ['confirmado', 'cancelado']
    const estadosSiguientes = @json($estadosSiguientes);
    const estadoActual = '{{ $pedido->estado }}';
    
    function seleccionarEstado(estado, elemento) {
        // Validar si el estado está permitido
        if (estado !== estadoActual && !estadosSiguientes.includes(estado)) {
            const estadosTexto = {
                'pendiente': 'Pendiente', 'confirmado': 'Confirmado',
                'enviado': 'Enviado', 'entregado': 'Entregado', 'cancelado': 'Cancelado'
            };
            Swal.fire({
                title: 'Cambio no permitido',
                html: `No puedes cambiar el estado de <strong>${estadosTexto[estadoActual]}</strong> a <strong>${estadosTexto[estado]}</strong>.<br><br><small>Solo puedes avanzar al siguiente estado del flujo del pedido.</small>`,
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
        
        let mensaje = '';
        if (estado === 'cancelado') {
            mensaje = 'Al cancelar, los productos regresarán al inventario';
        } else if (estado === 'entregado') {
            mensaje = 'El pedido se marcará como entregado';
        } else {
            mensaje = 'Estado cambiado a: ' + estado.charAt(0).toUpperCase() + estado.slice(1);
        }
        
        Swal.fire({
            title: 'Estado seleccionado',
            text: mensaje,
            icon: 'info',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }

    document.getElementById('updateForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const estadoSelect = document.getElementById('inputEstado').value;
        const esCancelacion = estadoSelect === 'cancelado';
        const esEntrega = estadoSelect === 'entregado';
        
        let titulo = '¿Actualizar pedido?';
        let mensaje = 'Los cambios se guardarán permanentemente';
        let icono = 'question';
        let color = '#7fad39';
        
        if (esCancelacion) {
            titulo = '¿Cancelar pedido?';
            mensaje = 'Al cancelar el pedido, los productos regresarán al inventario. ¿Continuar?';
            icono = 'warning';
            color = '#dc3545';
        } else if (esEntrega) {
            titulo = '¿Marcar como entregado?';
            mensaje = 'Confirma que el pedido ha sido entregado al cliente.';
            icono = 'success';
            color = '#28a745';
        }
        
        Swal.fire({
            title: titulo,
            text: mensaje,
            icon: icono,
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#6c757d',
            confirmButtonText: esCancelacion ? 'Sí, cancelar' : 'Sí, actualizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        this.submit();
                    }
                });
            }
        });
    });
    
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
    @endif
</script>
@endsection