@extends('vendedor.layouts.app')

@section('title', $verDisponibles ? 'Pedidos Disponibles' : 'Mis Pedidos')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="header-bar">
        <div>
            <h1 class="header-title">
                <i class="fas fa-shopping-cart me-2"></i>
                {{ $verDisponibles ? 'Pedidos Disponibles' : 'Mis Pedidos' }}
                <span class="badge-disponible">
                    <i class="fas fa-store"></i> {{ $sucursalNombre }}
                </span>
            </h1>
            <p class="text-muted mb-0 small">Bienvenido, {{ auth()->user()->nombre }}</p>
        </div>
        
        <div class="d-flex gap-2">
            @if(!$verDisponibles)
                <a href="{{ route('vendedor.pedidos.index', ['disponibles' => 1]) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-hand-pointer me-1"></i> Ver Disponibles 
                    @if($totalDisponibles > 0)
                        <span class="badge bg-light text-dark ms-1">{{ $totalDisponibles }}</span>
                    @endif
                </a>
            @else
                <a href="{{ route('vendedor.pedidos.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-clipboard-list me-1"></i> Mis Pedidos
                </a>
            @endif
            <a href="{{ route('vendedor.pedidos.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Nuevo Pedido
            </a>
            <a href="{{ route('vendedor.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Mensajes de sesión -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!$verDisponibles)
        <!-- Estadísticas de mis pedidos -->
        <div class="stats-grid">
            <div class="stat-card {{ $estado == 'todos' ? 'active' : '' }}" 
                 onclick="window.location.href='{{ route('vendedor.pedidos.index') }}'">
                <div class="stat-value">{{ $contadores['todos'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-boxes me-1"></i>
                    Todos
                </div>
            </div>
            
            <div class="stat-card stat-pendientes {{ $estado == 'pendiente' ? 'active' : '' }}" 
                 onclick="window.location.href='{{ route('vendedor.pedidos.index', ['estado' => 'pendiente']) }}'">
                <div class="stat-value">{{ $contadores['pendiente'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-clock me-1"></i>
                    Pendientes
                </div>
            </div>
            
            <div class="stat-card stat-confirmados {{ $estado == 'confirmado' ? 'active' : '' }}" 
                 onclick="window.location.href='{{ route('vendedor.pedidos.index', ['estado' => 'confirmado']) }}'">
                <div class="stat-value">{{ $contadores['confirmado'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-check-circle me-1"></i>
                    Confirmados
                </div>
            </div>
            
            <div class="stat-card stat-enviados {{ $estado == 'enviado' ? 'active' : '' }}" 
                 onclick="window.location.href='{{ route('vendedor.pedidos.index', ['estado' => 'enviado']) }}'">
                <div class="stat-value">{{ $contadores['enviado'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-shipping-fast me-1"></i>
                    Enviados
                </div>
            </div>
            
            <div class="stat-card stat-entregados {{ $estado == 'entregado' ? 'active' : '' }}" 
                 onclick="window.location.href='{{ route('vendedor.pedidos.index', ['estado' => 'entregado']) }}'">
                <div class="stat-value">{{ $contadores['entregado'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-home me-1"></i>
                    Entregados
                </div>
            </div>
            
            <div class="stat-card stat-cancelados {{ $estado == 'cancelado' ? 'active' : '' }}" 
                 onclick="window.location.href='{{ route('vendedor.pedidos.index', ['estado' => 'cancelado']) }}'">
                <div class="stat-value">{{ $contadores['cancelado'] }}</div>
                <div class="stat-label">
                    <i class="fas fa-times-circle me-1"></i>
                    Cancelados
                </div>
            </div>
            
            <div class="stat-card disponibles" 
                 onclick="window.location.href='{{ route('vendedor.pedidos.index', ['disponibles' => 1]) }}'">
                <div class="stat-value">{{ $totalDisponibles }}</div>
                <div class="stat-label">
                    <i class="fas fa-bell me-1"></i>
                    Disponibles
                </div>
            </div>
        </div>

        <!-- Filtros avanzados -->
        <div class="filter-card">
            <form method="GET" class="row g-3">
                <input type="hidden" name="estado" value="{{ $estado }}">
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Desde</label>
                    <input type="date" name="desde" value="{{ $desde }}" 
                           class="form-control form-control-sm">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Hasta</label>
                    <input type="date" name="hasta" value="{{ $hasta }}" 
                           class="form-control form-control-sm">
                </div>
                
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2">
                        <i class="fas fa-filter me-1"></i> Aplicar Filtros
                    </button>
                    <a href="{{ route('vendedor.pedidos.index', $estado != 'todos' ? ['estado' => $estado] : []) }}" 
                       class="btn btn-secondary btn-sm">
                        <i class="fas fa-times me-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    @endif

    <!-- Tabla de pedidos -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 d-flex align-items-center gap-2">
                @if($verDisponibles)
                    <i class="fas fa-hand-pointer"></i> Pedidos Disponibles para Asignar
                @else
                    <i class="fas fa-clipboard-list"></i> 
                    {{ $estado == 'todos' ? 'Todos mis pedidos' : ucfirst($estado) . ' (' . $contadores[$estado] . ')' }}
                @endif
            </h5>
            
            @if(count($pedidos) > 0)
                <span class="badge bg-primary">
                    {{ count($pedidos) }} pedido{{ count($pedidos) !== 1 ? 's' : '' }}
                </span>
            @endif
        </div>
        
        <div class="card-body p-0">
            @if(count($pedidos) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidos as $pedido)
                                @php
                                    $yaAsignado = false;
                                    $responsableNombre = '';
                                    if ($verDisponibles) {
                                        $responsable = $pedido->responsables->first();
                                        $yaAsignado = !is_null($responsable);
                                        $responsableNombre = $responsable ? $responsable->usuario->nombre : '';
                                    }
                                @endphp
                                <tr class="{{ $yaAsignado ? 'table-secondary' : '' }}">
                                    <td>
                                        <strong class="text-primary">#{{ $pedido->folio }}</strong>
                                        <small class="text-muted d-block">{{ $pedido->items_count }} items</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $pedido->cliente_nombre }}</div>
                                        <small class="text-muted">{{ $pedido->cliente_telefono }}</small>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y') }}
                                        <small class="text-muted d-block">{{ \Carbon\Carbon::parse($pedido->fecha)->format('H:i') }}</small>
                                    </td>
                                    <td class="fw-bold" style="color: var(--primary);">
                                        ${{ number_format($pedido->total, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge-estado badge-{{ strtolower($pedido->estado) }}">
                                            <i class="fas fa-circle fa-xs"></i>
                                            {{ ucfirst($pedido->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons d-flex gap-2">
                                            @if($verDisponibles && !$yaAsignado)
                                                <button type="button" 
                                                        class="btn-asignar asignar-pedido-btn"
                                                        data-pedido-id="{{ $pedido->id }}"
                                                        data-pedido-folio="{{ $pedido->folio }}">
                                                    <i class="fas fa-user-plus"></i> Asignarme
                                                </button>
                                            @elseif($verDisponibles && $yaAsignado)
                                                <span class="asignado-badge" title="Asignado a: {{ $responsableNombre }}">
                                                    <i class="fas fa-user-check"></i> Ya asignado
                                                </span>
                                            @endif
                                            
                                            <a href="{{ route('vendedor.pedidos.show', $pedido->id) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if(!$verDisponibles)
                                                <a href="{{ route('vendedor.pedidos.seguimiento', $pedido->id) }}" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Dar seguimiento">
                                                    <i class="fas fa-clipboard-check"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h5 class="text-muted mb-3">
                        {{ $verDisponibles ? 'No hay pedidos disponibles' : 'No tienes pedidos' }}
                    </h5>
                    <p class="text-muted small mb-4">
                        {{ $verDisponibles ? 
                            'Todos los pedidos han sido asignados o no hay pedidos pendientes en tu sucursal.' : 
                            ($estado == 'todos' ? 
                                'No tienes pedidos asignados. Asigna pedidos disponibles para comenzar.' : 
                                'No tienes pedidos en estado ' . $estado) }}
                    </p>
                    @if(!$verDisponibles && $estado == 'todos')
                        <a href="{{ route('vendedor.pedidos.index', ['disponibles' => 1]) }}" class="btn btn-info">
                            <i class="fas fa-hand-pointer me-2"></i> Ver Pedidos Disponibles
                        </a>
                    @endif
                    <a href="{{ route('vendedor.pedidos.create') }}" class="btn btn-primary ms-2">
                        <i class="fas fa-plus-circle me-2"></i> Crear Nuevo Pedido
                    </a>
                </div>
            @endif
        </div>
        
        @if(count($pedidos) > 0)
            <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ count($pedidos) }} pedido{{ count($pedidos) !== 1 ? 's' : '' }}
                </small>
                
                @if($verDisponibles)
                    <small class="text-info">
                        <i class="fas fa-info-circle me-1"></i>
                        Los pedidos disponibles pueden ser asignados por cualquier vendedor de la sucursal
                    </small>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Formulario oculto para CSRF -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Asignar pedido con AJAX
        const asignarButtons = document.querySelectorAll('.asignar-pedido-btn');
        
        asignarButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const pedidoId = this.dataset.pedidoId;
                const folio = this.dataset.pedidoFolio;
                const buttonElement = this;
                const originalText = this.innerHTML;
                
                Swal.fire({
                    title: '¿Asignar este pedido?',
                    html: `<strong>Pedido #${folio}</strong><br><br>¿Deseas asignarte este pedido para darle seguimiento?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#17a2b8',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, asignarme',
                    cancelButtonText: 'Cancelar',
                    showLoaderOnConfirm: true,
                    preConfirm: async () => {
                        try {
                            buttonElement.disabled = true;
                            buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Asignando...';
                            
                            const response = await fetch('{{ route("vendedor.pedidos.asignar") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    pedido_id: pedidoId
                                })
                            });
                            
                            const data = await response.json();
                            
                            if (!response.ok) {
                                throw new Error(data.message || 'Error al asignar el pedido');
                            }
                            
                            return data;
                            
                        } catch (error) {
                            buttonElement.disabled = false;
                            buttonElement.innerHTML = originalText;
                            Swal.showValidationMessage(`Error: ${error.message}`);
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: '¡Asignado!',
                            text: result.value.message,
                            icon: 'success',
                            confirmButtonColor: '#7fad39',
                            timer: 3000,
                            timerProgressBar: true,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else if (result.isDismissed) {
                        buttonElement.disabled = false;
                        buttonElement.innerHTML = originalText;
                    }
                });
            });
        });
        
        // Mostrar mensaje si se asignó un pedido
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('assigned')) {
            Swal.fire({
                title: '¡Asignación exitosa!',
                text: 'El pedido ha sido asignado a tu cuenta',
                icon: 'success',
                confirmButtonColor: '#7fad39',
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
            
            // Limpiar parámetro de URL
            const newUrl = window.location.pathname + '?disponibles=1';
            window.history.replaceState({}, document.title, newUrl);
        }
        
        // Auto-refresh cada 60 segundos para pedidos disponibles
        if (urlParams.has('disponibles')) {
            setTimeout(() => {
                location.reload();
            }, 60000);
        }
        
        // Animaciones para las tarjetas de estadísticas
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Inicializar tooltips de Bootstrap
        const tooltipTriggerList = document.querySelectorAll('[title]');
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection

@push('styles')
<style>
    /* Estilos adicionales específicos para pedidos */
    .filter-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }
    
    .asignado-badge {
        background: linear-gradient(135deg, #28a745, #218838);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .stat-card.disponibles {
        border-top-color: var(--info);
    }
    
    .stat-card.disponibles.active {
        background: linear-gradient(135deg, var(--info), #138496);
        color: white;
    }
    
    .stat-card.disponibles.active .stat-value,
    .stat-card.disponibles.active .stat-label {
        color: white !important;
    }
    
    .table-secondary {
        background-color: #f8f9fa !important;
    }
    
    .table-secondary td {
        opacity: 0.8;
    }
</style>
@endpush