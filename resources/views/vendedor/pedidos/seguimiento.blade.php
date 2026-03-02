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
    .badge-asignacion { background: #fff3cd; color: #856404; }
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
                <p class="text-muted mb-0 small">#{{ $pedido->folio }} - {{ ucfirst($pedido->estado) }}</p>
            </div>
            <a href="{{ route('vendedor.pedidos.show', $pedido->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver al Pedido
            </a>
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
                            <option value="observacion">Observación</option>
                            <option value="seguimiento">Seguimiento</option>
                            <option value="contacto">Contacto con cliente</option>
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
        </div>
        
        <!-- Historial de seguimiento -->
        <div class="col-lg-8">
            <div class="info-card">
                <h5 class="mb-3"><i class="fas fa-history me-2"></i>Historial de Seguimiento</h5>
                
                @if($historial->count() > 0)
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
                                        {{ $usuarioRol }}
                                    </span>
                                </div>
                                <small class="text-muted">
                                    {{ $fechaFormateada }}
                                </small>
                            </div>
                            <div class="mt-2">
                                <span class="badge-accion badge-{{ $item->accion }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->accion)) }}
                                </span>
                            </div>
                            <p class="mb-0 mt-2">{{ $item->detalles ?? 'Sin detalles' }}</p>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-3"></i>
                        <p>No hay seguimiento registrado</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
    });
</script>
@endsection