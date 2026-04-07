{{-- resources/views/vendedor/pedidos/seguimiento.blade.php --}}
@extends('vendedor.layouts.app')

@section('title', 'Seguimiento de Pedido - Vendedor')

@section('styles')
<style>
    :root {
        --sidebar-width: 250px;
        --primary: #7fad39;
    }
    
    .info-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        margin-bottom: 15px;
        border-top: 3px solid var(--primary);
    }
    
    .timeline-item {
        border-left: 3px solid var(--primary);
        padding-left: 15px;
        margin-bottom: 15px;
        padding-bottom: 15px;
    }
    
    .badge-accion {
        font-size: 0.8rem;
        padding: 4px 8px;
        border-radius: 4px;
    }
    
    .badge-observacion { background: #d1ecf1; color: #0c5460; }
    .badge-seguimiento { background: #d4edda; color: #155724; }
    .badge-contacto { background: #cce5ff; color: #004085; }
    .badge-cambio_estado { background: #f8d7da; color: #721c24; }
    .badge-cambio_pago { background: #d4edda; color: #155724; }
    .badge-cambio_fecha { background: #cce5ff; color: #004085; }
    .badge-creado { background: #d1ecf1; color: #0c5460; }
    .badge-responsable_asignado { background: #fff3cd; color: #856404; }
</style>
@endsection

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="header-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>Seguimiento del Pedido
                </h1>
                <p class="text-muted mb-0 small">
                    #{{ $pedido->folio }} - 
                    <span class="badge-estado badge-{{ $pedido->estado }}">
                        {{ ucfirst($pedido->estado) }}
                    </span>
                </p>
            </div>
            <div>
                <a href="{{ route('vendedor.pedidos.show', $pedido->id) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver al Pedido
                </a>
            </div>
        </div>
    </div>

    <!-- Información rápida del pedido -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-card text-center py-2">
                <small class="text-muted">Cliente</small>
                <div class="fw-bold">{{ $pedido->cliente_nombre }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card text-center py-2">
                <small class="text-muted">Teléfono</small>
                <div class="fw-bold">{{ $pedido->cliente_telefono }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card text-center py-2">
                <small class="text-muted">Total</small>
                <div class="fw-bold text-primary">${{ number_format($pedido->total, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card text-center py-2">
                <small class="text-muted">Pago</small>
                <div class="fw-bold {{ $pedido->pago_confirmado ? 'text-success' : 'text-warning' }}">
                    {{ $pedido->pago_confirmado ? 'Confirmado' : 'Pendiente' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes de sesión -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Formulario para nuevo seguimiento -->
        <div class="col-lg-4 mb-3">
            <div class="info-card">
                <h5 class="mb-3"><i class="fas fa-plus-circle me-2"></i>Agregar Seguimiento</h5>
                <form method="POST" action="{{ route('vendedor.pedidos.guardar-seguimiento', $pedido->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de seguimiento</label>
                        <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror">
                            <option value="observacion">📝 Observación</option>
                            <option value="seguimiento">🔄 Seguimiento</option>
                            <option value="contacto">📞 Contacto con cliente</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="comentario" class="form-label">Comentario</label>
                        <textarea name="comentario" id="comentario" class="form-control @error('comentario') is-invalid @enderror" 
                                  rows="5" placeholder="Describe el seguimiento realizado..." required>{{ old('comentario') }}</textarea>
                        @error('comentario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i> Guardar Seguimiento
                    </button>
                </form>
            </div>
            
            <!-- Acción rápida: WhatsApp -->
            @php
                $whatsapp_msg = "Hola " . $pedido->cliente_nombre . ", te contacto por tu pedido " . $pedido->folio . " - Tanques Tláloc";
                $telefono_limpio = preg_replace('/[^0-9]/', '', $pedido->cliente_telefono);
                $whatsapp_url = "https://wa.me/" . $telefono_limpio . "?text=" . urlencode($whatsapp_msg);
            @endphp
            <div class="info-card mt-2">
                <h5 class="mb-3"><i class="fas fa-phone-alt me-2"></i>Acción Rápida</h5>
                <a href="{{ $whatsapp_url }}" target="_blank" class="btn btn-success w-100" style="background: #25D366; border: none;">
                    <i class="fab fa-whatsapp me-1"></i> Contactar por WhatsApp
                </a>
            </div>
        </div>
        
        <!-- Historial de seguimiento -->
        <div class="col-lg-8">
            <div class="info-card">
                <h5 class="mb-3"><i class="fas fa-history me-2"></i>Historial de Seguimiento</h5>
                
                @if($historial->count() > 0)
                    <div style="max-height: 500px; overflow-y: auto;">
                        @foreach($historial as $item)
                            @php
                                $usuarioNombre = $item->usuario->nombre ?? 'Sistema';
                                $usuarioRol = $item->usuario->rol ?? 'sistema';
                                $fechaFormateada = \Carbon\Carbon::parse($item->fecha)->format('d/m/Y H:i');
                            @endphp
                            <div class="timeline-item">
                                <div class="d-flex justify-content-between">
                                    <div class="fw-bold">
                                        {{ $usuarioNombre }}
                                        <span class="badge bg-secondary ms-1">
                                            {{ ucfirst($usuarioRol) }}
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        {{ $fechaFormateada }}
                                    </small>
                                </div>
                                <div class="mt-2">
                                    <span class="badge-accion badge-{{ $item->accion }}">
                                        @switch($item->accion)
                                            @case('observacion') 📝 Observación @break
                                            @case('seguimiento') 🔄 Seguimiento @break
                                            @case('contacto') 📞 Contacto @break
                                            @case('cambio_estado') 📊 Cambio de estado @break
                                            @case('cambio_pago') 💰 Cambio de pago @break
                                            @case('cambio_fecha') 📅 Cambio de fecha @break
                                            @case('creado') 🆕 Creado @break
                                            @case('responsable_asignado') 👤 Responsable asignado @break
                                            @default {{ ucfirst(str_replace('_', ' ', $item->accion)) }}
                                        @endswitch
                                    </span>
                                </div>
                                <p class="mb-0 mt-2">{{ $item->detalles ?? 'Sin detalles' }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-3"></i>
                        <p>No hay seguimiento registrado para este pedido</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-scroll al formulario si hay errores
        @if($errors->any())
            document.querySelector('.info-card form').scrollIntoView({ behavior: 'smooth' });
        @endif
        
        // Confirmar envío del formulario
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const comentario = document.getElementById('comentario').value.trim();
                if (!comentario) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debes escribir un comentario',
                        confirmButtonColor: '#7fad39'
                    });
                }
            });
        }
        
        // Mostrar mensajes con SweetAlert
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
    });
</script>
@endsection