@extends('admin.layouts.app')

@section('title', 'Nueva Oferta - Tanques Tláloc')

@section('content')
<div class="header-bar">
    <div>
        <h1 class="header-title">
            <i class="fas fa-plus-circle me-2"></i>Nueva Oferta
        </h1>
        <p class="text-muted mb-0 small">Crea una nueva promoción o descuento</p>
    </div>
    
    <div class="header-actions">
        <a href="{{ route('admin.ofertas') }}" class="btn-custom btn-secondary-custom">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-tag me-2"></i>Información de la Oferta
        </h5>
    </div>
    
    <div class="card-body">
        <form method="POST" action="{{ route('admin.ofertas.store') }}" id="formOferta">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Oferta *</label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre') }}" required placeholder="Ej: Hot Sale 2026">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                                  rows="3" placeholder="Descripción de la oferta...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-cog me-2"></i>Configuración
                            </h6>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="activa" id="activa" value="1" {{ old('activa', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activa">Oferta activa</label>
                            </div>
                            
                            <p class="small text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Las ofertas inactivas no se muestran en la tienda
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <label class="form-label">Tipo de Descuento *</label>
                    <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" id="tipoDescuento" required>
                        <option value="porcentaje" {{ old('tipo') == 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                        <option value="fijo" {{ old('tipo') == 'fijo' ? 'selected' : '' }}>Monto Fijo ($)</option>
                    </select>
                    @error('tipo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Valor del Descuento *</label>
                    <div class="input-group">
                        <span class="input-group-text" id="valorSimbolo">
                            {{ old('tipo') == 'fijo' ? '$' : '%' }}
                        </span>
                        <input type="number" step="0.01" min="0" name="valor" 
                               class="form-control @error('valor') is-invalid @enderror" 
                               value="{{ old('valor') }}" required>
                    </div>
                    <small class="text-muted">Los valores pueden tener hasta 2 decimales</small>
                    @error('valor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <label class="form-label">Fecha de Inicio *</label>
                    <input type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                           value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" required>
                    @error('fecha_inicio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Fecha de Fin *</label>
                    <input type="date" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" 
                           value="{{ old('fecha_fin', now()->addDays(30)->format('Y-m-d')) }}" required>
                    @error('fecha_fin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <h5 class="mb-3">
                    <i class="fas fa-box me-2"></i>Productos en Oferta
                    <small class="text-muted">(Selecciona los productos que aplican)</small>
                </h5>
                
                <div class="card bg-light border-0">
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @foreach($productos as $categoria => $productosCategoria)
                        <div class="mb-3">
                            <h6 class="fw-bold" style="color: var(--primary);">
                                <i class="fas fa-folder me-2"></i>{{ $categoria }}
                            </h6>
                            <div class="row g-2">
                                @foreach($productosCategoria as $producto)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="productos[]" value="{{ $producto->id }}" 
                                               id="prod{{ $producto->id }}"
                                               {{ is_array(old('productos')) && in_array($producto->id, old('productos')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="prod{{ $producto->id }}">
                                            <strong>{{ $producto->nombre }}</strong>
                                            <small class="text-muted d-block">
                                                Código: {{ $producto->codigo }} | 
                                                Precio: ${{ number_format($producto->precio, 2) }}
                                            </small>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @error('productos')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.ofertas') }}" class="btn-custom btn-secondary-custom">
                    Cancelar
                </a>
                <button type="submit" class="btn-custom btn-success-custom" id="btnGuardar">
                    <i class="fas fa-save me-1"></i> Guardar Oferta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('tipoDescuento').addEventListener('change', function() {
    const simbolo = document.getElementById('valorSimbolo');
    if (this.value === 'porcentaje') {
        simbolo.textContent = '%';
    } else {
        simbolo.textContent = '$';
    }
});

// SweetAlert para el formulario
document.getElementById('formOferta').addEventListener('submit', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: '¿Guardar oferta?',
        html: '<div style="text-align: center;">' +
              '<i class="fas fa-tag" style="font-size: 3rem; color: #7fad39; margin-bottom: 1rem;"></i>' +
              '<p>¿Estás seguro de crear esta oferta?</p>' +
              '</div>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7fad39',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check me-1"></i> Sí, guardar',
        cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Guardando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    this.submit();
                }
            });
        }
    });
});

// Mostrar errores de validación con SweetAlert
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        let errores = '';
        @foreach($errors->all() as $error)
            errores += '• {{ $error }}\n';
        @endforeach
        
        Swal.fire({
            icon: 'error',
            title: 'Error de validación',
            text: errores,
            confirmButtonColor: '#dc3545'
        });
    });
@endif
</script>
@endsection