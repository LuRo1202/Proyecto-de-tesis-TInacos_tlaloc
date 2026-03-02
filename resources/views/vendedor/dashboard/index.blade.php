@extends('vendedor.layouts.app')

@section('title', 'Dashboard Vendedor')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="header-bar">
        <div>
            <h1 class="header-title">
                <i class="fas fa-tachometer-alt me-2"></i>Panel de Vendedor
                <span class="badge-disponible">
                    <i class="fas fa-store"></i> {{ $sucursalNombre }}
                </span>
            </h1>
            <p class="text-muted mb-0 small">Bienvenido, {{ auth()->user()->nombre }}</p>
        </div>
        
        <div class="header-info">
            <div class="time-widget" id="relojWidget">
                <div class="current-time" id="currentTime"></div>
                <div class="current-date" id="currentDate"></div>
            </div>
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

    <!-- Alerta de pedidos disponibles -->
    @if($stats['pedidos_disponibles'] > 0)
        <div class="alert-disponibles">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-bell me-2"></i>
                    <strong>¡Hay {{ $stats['pedidos_disponibles'] }} pedidos disponibles!</strong>
                    <span class="text-muted ms-2">Puedes asignártelos para darles seguimiento</span>
                </div>
                <a href="{{ route('vendedor.pedidos.index', ['disponibles' => 1]) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-hand-pointer me-1"></i> Ver Disponibles
                </a>
            </div>
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['pedidos_asignados'] }}</div>
            <div class="stat-label">
                <i class="fas fa-clipboard-check me-1"></i> Mis Pedidos
            </div>
        </div>
        
        <div class="stat-card stat-pendientes">
            <div class="stat-value">{{ $stats['pedidos_pendientes'] }}</div>
            <div class="stat-label">
                <i class="fas fa-clock me-1"></i> Pendientes
            </div>
        </div>
        
        <div class="stat-card stat-disponibles">
            <div class="stat-value">{{ $stats['pedidos_disponibles'] }}</div>
            <div class="stat-label">
                <i class="fas fa-bell me-1"></i> Disponibles
            </div>
        </div>
        
        <div class="stat-card stat-ventas">
            <div class="stat-value">${{ number_format($stats['ventas_mes'], 0) }}</div>
            <div class="stat-label">
                <i class="fas fa-dollar-sign me-1"></i> Ventas Mes
            </div>
        </div>
        
        <div class="stat-card stat-clientes">
            <div class="stat-value">{{ $stats['clientes_atendidos'] }}</div>
            <div class="stat-label">
                <i class="fas fa-user-friends me-1"></i> Clientes Atendidos
            </div>
        </div>
        
        <div class="stat-card stat-comisiones">
            <div class="stat-value">${{ number_format($stats['comisiones_mes'], 0) }}</div>
            <div class="stat-label">
                <i class="fas fa-money-bill-wave me-1"></i> Comisiones Estimadas
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="content-row">
        <!-- Mis Pedidos -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-clipboard-list"></i> Mis Pedidos
                </h5>
                <a href="{{ route('vendedor.pedidos.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list me-1"></i> Ver Todos
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($misPedidos as $pedido)
                                <tr>
                                    <td>
                                        <strong class="text-primary">#{{ $pedido->folio }}</strong>
                                        <small class="text-muted d-block">{{ $pedido->items_count }} items</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $pedido->cliente_nombre }}</div>
                                        <small class="text-muted">{{ $pedido->cliente_telefono }}</small>
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
                                            <a href="{{ route('vendedor.pedidos.show', $pedido->id) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('vendedor.pedidos.seguimiento', $pedido->id) }}" 
                                               class="btn btn-sm btn-success" 
                                               title="Dar seguimiento">
                                                <i class="fas fa-clipboard-check"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-clipboard-list fa-2x mb-2 d-block"></i>
                                        No tienes pedidos asignados
                                        <small class="d-block mt-1">Asigna pedidos disponibles para comenzar</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Derecho -->
        <div>
            <!-- Pedidos Disponibles -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-hand-pointer me-2"></i>Pedidos Disponibles
                    </h5>
                    <span class="badge bg-info">{{ $pedidosDisponibles->count() }}</span>
                </div>
                <div class="card-body">
                    @forelse($pedidosDisponibles as $pedido)
                        @php
                            $yaAsignado = \App\Models\PedidoResponsable::where('pedido_id', $pedido->id)->exists();
                        @endphp
                        <div class="pedido-disponible-item {{ $yaAsignado ? 'disabled' : '' }}">
                            <div class="pedido-info">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="text-primary">#{{ $pedido->folio }}</strong>
                                    <span class="badge-estado badge-{{ strtolower($pedido->estado) }}">
                                        <i class="fas fa-circle fa-xs"></i>
                                        {{ ucfirst($pedido->estado) }}
                                    </span>
                                </div>
                                <p class="mb-1 small">
                                    <i class="fas fa-user me-1"></i>
                                    {{ $pedido->cliente_nombre }}
                                </p>
                                <p class="mb-2 small text-muted">
                                    <i class="fas fa-box me-1"></i>
                                    {{ $pedido->items_count }} producto(s)
                                </p>
                                <p class="mb-1 small text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="pedido-actions">
                                <span class="text-success fw-bold">${{ number_format($pedido->total, 2) }}</span>
                                
                                @if(!$yaAsignado)
                                    <button type="button" 
                                            class="btn-asignar asignar-pedido-btn"
                                            data-pedido-id="{{ $pedido->id }}"
                                            data-pedido-folio="{{ $pedido->folio }}">
                                        <i class="fas fa-user-plus"></i> Asignarme
                                    </button>
                                @else
                                    <span class="assigned-badge">
                                        <i class="fas fa-user-check"></i> Asignado
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                            No hay pedidos disponibles
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Mis productos más vendidos -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-fire me-2"></i>Mis Productos más vendidos
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($productosVendidos as $producto)
                        <div class="product-item">
                            <div class="product-name">{{ $producto->producto_nombre }}</div>
                            <div class="product-sales">{{ $producto->total_vendido }} vendidos</div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-box fa-2x mb-2 d-block"></i>
                            No has vendido productos aún
                        </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Acciones Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('vendedor.pedidos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i> Nuevo Pedido
                        </a>
                        <a href="{{ route('vendedor.pedidos.index') }}" class="btn btn-info">
                            <i class="fas fa-clipboard-list me-2"></i> Mis Pedidos
                        </a>
                        <a href="{{ route('vendedor.pedidos.hoy') }}" class="btn btn-warning">
                            <i class="fas fa-bolt me-2"></i> Pedidos de Hoy
                        </a>
                        <a href="{{ route('vendedor.ventas.index') }}" class="btn btn-success">
                            <i class="fas fa-chart-line me-2"></i> Mis Ventas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-MX', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false
        });
        const dateString = now.toLocaleDateString('es-MX', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        
        document.getElementById('currentTime').textContent = timeString;
        document.getElementById('currentDate').textContent = dateString;
    }
    
    setInterval(updateClock, 1000);
    updateClock();
    
    document.addEventListener('DOMContentLoaded', function() {
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
                            confirmButtonColor: '#7fad39'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });
            });
        });
        
        const pedidosDisponibles = {{ $stats['pedidos_disponibles'] }};
        const userName = "{{ auth()->user()->nombre }}";
        const sucursal = "{{ $sucursalNombre }}";
        
        setTimeout(() => {
            Swal.fire({
                title: `¡Bienvenido, ${userName}!`,
                html: `Panel de vendedor - Sucursal: <strong>${sucursal}</strong>`,
                icon: 'success',
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        }, 500);
        
        if (pedidosDisponibles > 0) {
            setTimeout(() => {
                Swal.fire({
                    title: '¡Pedidos Disponibles!',
                    html: `Hay <strong>${pedidosDisponibles} pedidos</strong> disponibles para asignarte en tu sucursal.`,
                    icon: 'info',
                    confirmButtonColor: '#17a2b8',
                    confirmButtonText: 'Ver Pedidos',
                    showCancelButton: true,
                    cancelButtonText: 'Más Tarde'
                });
            }, 2000);
        }
    });
</script>
@endsection