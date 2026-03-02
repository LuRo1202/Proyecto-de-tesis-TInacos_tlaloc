@extends('admin.layouts.app')

@section('title', 'Gestión de Ofertas - Tanques Tláloc')

@section('content')

<style>
    /* Acciones - BOTONES CON COLORES CORRECTOS */
.action-buttons {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
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
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* EDITAR - AMARILLO */
.btn-edit {
    background: linear-gradient(135deg, #ffc107, #e0a800);
}

.btn-edit:hover {
    background: linear-gradient(135deg, #e0a800, #c69500);
}

/* ELIMINAR - ROJO */
.btn-delete {
    background: linear-gradient(135deg, #dc3545, #c82333);
}

.btn-delete:hover {
    background: linear-gradient(135deg, #c82333, #a71d2a);
}

/* DESACTIVAR - NARANJA */
.btn-warning {
    background: linear-gradient(135deg, #fd7e14, #e06b0c);
}

.btn-warning:hover {
    background: linear-gradient(135deg, #e06b0c, #c05a0a);
}

/* ACTIVAR - VERDE */
.btn-success {
    background: linear-gradient(135deg, #28a745, #218838);
}

.btn-success:hover {
    background: linear-gradient(135deg, #218838, #1e7e34);
}


</style>

<div class="header-bar">
    <div>
        <h1 class="header-title">
            <i class="fas fa-tags me-2"></i>Gestión de Ofertas
        </h1>
        <p class="text-muted mb-0 small">Administra las promociones y descuentos</p>
    </div>
    
    <div class="header-actions">
        <a href="{{ route('admin.ofertas.nuevo') }}" class="btn-custom btn-success-custom">
            <i class="fas fa-plus"></i> Nueva Oferta
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="filter-card">
    <h3 class="filter-title">
        <i class="fas fa-filter"></i> Filtros
    </h3>
    
    <form method="GET" action="{{ route('admin.ofertas') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Estado</label>
            <select name="estado" class="form-control-sm">
                <option value="">Todas las ofertas</option>
                <option value="activas" {{ request('estado') == 'activas' ? 'selected' : '' }}>Activas</option>
                <option value="inactivas" {{ request('estado') == 'inactivas' ? 'selected' : '' }}>Inactivas</option>
            </select>
        </div>
        
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Buscar</label>
            <input type="text" name="buscar" class="form-control-sm" 
                   placeholder="Nombre o descripción..." value="{{ request('buscar') }}">
        </div>
        
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn-custom btn-primary-custom me-2">
                <i class="fas fa-search"></i> Filtrar
            </button>
            @if(request()->has('estado') || request()->has('buscar'))
            <a href="{{ route('admin.ofertas') }}" class="btn-custom btn-secondary-custom">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Tabla de Ofertas -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 d-flex align-items-center gap-2">
            <i class="fas fa-list"></i> Lista de Ofertas
            <span class="badge bg-primary">{{ $ofertas->total() }}</span>
        </h5>
    </div>
    
    <div class="card-body p-0">
        @if($ofertas->isEmpty())
        <div class="empty-state">
            <i class="fas fa-tags"></i>
            <h5>No hay ofertas registradas</h5>
            <p>Crea tu primera oferta para comenzar a promocionar productos</p>
            <a href="{{ route('admin.ofertas.nuevo') }}" class="btn-custom btn-success-custom mt-2">
                <i class="fas fa-plus"></i> Crear Primera Oferta
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Productos</th>
                        <th>Vigencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ofertas as $oferta)
                    @php
                        $vigente = $oferta->activa && 
                                  now()->between($oferta->fecha_inicio, $oferta->fecha_fin);
                        $proxima = $oferta->activa && now()->lt($oferta->fecha_inicio);
                        $expirada = $oferta->activa && now()->gt($oferta->fecha_fin);
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $oferta->nombre }}</strong>
                            @if($oferta->descripcion)
                            <small class="text-muted d-block">{{ Str::limit($oferta->descripcion, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($oferta->tipo === 'porcentaje')
                                <span class="badge" style="background: #17a2b8; color: white;">
                                    <i class="fas fa-percent"></i> Porcentaje
                                </span>
                            @else
                                <span class="badge" style="background: #fd7e14; color: white;">
                                    <i class="fas fa-dollar-sign"></i> Fijo
                                </span>
                            @endif
                        </td>
                        <td class="fw-bold" style="color: var(--primary);">
                            @if($oferta->tipo === 'porcentaje')
                                {{ number_format($oferta->valor, 2) }}%
                            @else
                                ${{ number_format($oferta->valor, 2) }}
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background: var(--primary); color: white;">
                                {{ $oferta->productos_count }} productos
                            </span>
                        </td>
                        <td>
                            <div>
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                {{ $oferta->fecha_inicio->format('d/m/Y') }}
                            </div>
                            <div>
                                <i class="fas fa-calendar-check text-muted me-1"></i>
                                {{ $oferta->fecha_fin->format('d/m/Y') }}
                            </div>
                        </td>
                        <td>
                            @if($vigente)
                                <span class="badge" style="background: #28a745; color: white;">
                                    <i class="fas fa-check-circle"></i> Vigente
                                </span>
                            @elseif($proxima)
                                <span class="badge" style="background: #ffc107; color: #856404;">
                                    <i class="fas fa-clock"></i> Próxima
                                </span>
                            @elseif($expirada)
                                <span class="badge" style="background: #6c757d; color: white;">
                                    <i class="fas fa-hourglass-end"></i> Expirada
                                </span>
                            @elseif(!$oferta->activa)
                                <span class="badge" style="background: #dc3545; color: white;">
                                    <i class="fas fa-ban"></i> Inactiva
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- EDITAR - AMARILLO -->
                                <a href="{{ route('admin.ofertas.editar', $oferta->id) }}" 
                                   class="btn-action btn-edit" title="Editar oferta">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                @if($oferta->activa)
                                    <!-- DESACTIVAR - NARANJA -->
                                    <button type="button" 
                                            class="btn-action btn-warning" 
                                            title="Desactivar oferta"
                                            onclick="toggleOferta({{ $oferta->id }}, '{{ $oferta->nombre }}', true)">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <!-- ACTIVAR - VERDE -->
                                    <button type="button" 
                                            class="btn-action btn-success" 
                                            title="Activar oferta"
                                            onclick="toggleOferta({{ $oferta->id }}, '{{ $oferta->nombre }}', false)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                
                                <!-- ELIMINAR - ROJO -->
                                <button type="button" 
                                        class="btn-action btn-delete" 
                                        title="Eliminar oferta"
                                        onclick="eliminarOferta({{ $oferta->id }}, '{{ $oferta->nombre }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="card-footer">
            {{ $ofertas->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Formulario oculto para toggle -->
<form id="toggle-form" method="GET" style="display: none;"></form>

<!-- Formulario oculto para eliminar -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function toggleOferta(id, nombre, activa) {
    const accion = activa ? 'desactivar' : 'activar';
    
    Swal.fire({
        title: `¿${accion} oferta?`,
        html: `<div style="text-align: center;">
                <i class="fas fa-${activa ? 'ban' : 'check-circle'}" style="font-size: 3rem; color: ${activa ? '#fd7e14' : '#28a745'}; margin-bottom: 1rem;"></i>
                <p>¿Estás seguro de <strong>${accion}</strong> la oferta <strong>${nombre}</strong>?</p>
               </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: activa ? '#fd7e14' : '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `<i class="fas fa-${activa ? 'ban' : 'check'} me-1"></i> Sí, ${accion}`,
        cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar',
        reverseButtons: true,
        background: '#ffffff',
        backdrop: 'rgba(0,0,0,0.4)'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    window.location.href = `/admin/ofertas/${id}/toggle`;
                }
            });
        }
    });
}

function eliminarOferta(id, nombre) {
    Swal.fire({
        title: '¿Eliminar oferta?',
        html: `<div style="text-align: center;">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                <p>¿Estás seguro de eliminar la oferta <strong>${nombre}</strong>?</p>
                <p class="text-danger small"><i class="fas fa-exclamation-circle me-1"></i> Esta acción no se puede deshacer</p>
               </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Sí, eliminar',
        cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar',
        reverseButtons: true,
        background: '#ffffff',
        backdrop: 'rgba(0,0,0,0.4)'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    
                    const form = document.getElementById('delete-form');
                    form.action = `/admin/ofertas/${id}`;
                    form.submit();
                }
            });
        }
    });
}

// Mostrar mensajes de sesión con SweetAlert2
@if(session('swal'))
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '{{ session('swal')['type'] }}',
            title: '{{ session('swal')['title'] }}',
            text: '{{ session('swal')['message'] }}',
            confirmButtonColor: '#7fad39',
            timer: 3000,
            timerProgressBar: true
        });
    });
@endif
</script>
@endsection