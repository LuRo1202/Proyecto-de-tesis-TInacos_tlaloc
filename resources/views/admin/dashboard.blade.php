@extends('admin.layouts.app')

@section('title', 'Dashboard - Tanques Tláloc')

@section('content')
    <!-- Header -->
    <div class="header-bar">
        <div>
            <h1 class="header-title">
                <i class="fas fa-tachometer-alt me-2"></i>Panel de Control
            </h1>
            <p class="text-muted mb-0 small">Bienvenido, {{ $usuario_nombre }}</p>
        </div>
        
        <div class="header-info">
            <div class="time-widget">
                <div class="current-time" id="currentTime"></div>
                <div class="current-date" id="currentDate"></div>
            </div>
            
            <a href="{{ route('tienda') }}" target="_blank" class="btn-store">
                <i class="fas fa-store me-1"></i> Ver Tienda
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_pedidos'] }}</div>
            <div class="stat-label">
                <i class="fas fa-shopping-cart me-1"></i>
                Total Pedidos
            </div>
        </div>
        
        <div class="stat-card stat-pendiente">
            <div class="stat-value">{{ $stats['pedidos_pendientes'] }}</div>
            <div class="stat-label">
                <i class="fas fa-clock me-1"></i>
                Pendientes
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_productos'] }}</div>
            <div class="stat-label">
                <i class="fas fa-box me-1"></i>
                Productos
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">${{ number_format($stats['ventas_mes'], 0) }}</div>
            <div class="stat-label">
                <i class="fas fa-dollar-sign me-1"></i>
                Ventas Mes
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">${{ number_format($stats['ventas_hoy'], 0) }}</div>
            <div class="stat-label">
                <i class="fas fa-calendar-day me-1"></i>
                Ventas Hoy
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_clientes'] }}</div>
            <div class="stat-label">
                <i class="fas fa-users me-1"></i>
                Clientes
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="content-row">
        <!-- Últimos Pedidos -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-history"></i> Últimos Pedidos
                </h5>
                <a href="{{ route('admin.pedidos') }}" class="btn-store">
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
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosPedidos as $pedido)
                            <tr onclick="window.location.href='{{ route('admin.pedidos.ver', $pedido->id) }}';">
                                <td>
                                    <strong class="text-primary">#{{ $pedido->folio }}</strong>
                                    <small class="text-muted">{{ $pedido->sucursal?->nombre ?? 'Sin sucursal' }}</small>
                                    <small class="text-muted d-block">{{ $pedido->items_count }} items</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $pedido->cliente_nombre }}</div>
                                    <small class="text-muted">{{ $pedido->cliente_telefono }}</small>
                                </td>
                                <td>
                                    <div>{{ $pedido->fecha->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $pedido->fecha->format('H:i') }}</small>
                                </td>
                                <td class="fw-bold" style="color: var(--primary);">
                                    ${{ number_format($pedido->total, 2) }}
                                </td>
                                <td>
                                    <span class="badge-estado badge-{{ $pedido->estado }}">
                                        <i class="fas fa-circle fa-xs"></i>
                                        {{ ucfirst($pedido->estado) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons" onclick="event.stopPropagation();">
                                        <a href="{{ route('admin.pedidos.ver', $pedido->id) }}" 
                                           class="btn-action btn-view" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.pedidos.editar', $pedido->id) }}" 
                                           class="btn-action btn-edit" 
                                           title="Editar pedido">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h5>No hay pedidos registrados</h5>
                                    <p>Aún no hay pedidos registrados en el sistema.</p>
                                    
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
            <!-- Productos más vendidos -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-fire me-2"></i>Productos más vendidos
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($productosMasVendidos as $producto)
                    <div class="product-item">
                        <div class="product-name">{{ $producto->producto_nombre }}</div>
                        <div class="product-sales">{{ $producto->total_vendido }}</div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-box"></i>
                        <h5>No hay datos</h5>
                        <p>No hay productos vendidos aún</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Alerta de productos bajos -->
            @if($stats['productos_bajos'] > 0)
            <div class="inventory-alert">
                <div class="alert-content">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="alert-text">
                        <h6>¡Atención!</h6>
                        <p>Tienes <strong>{{ $stats['productos_bajos'] }} producto(s)</strong> con inventario bajo.</p>
                        <a href="{{ route('admin.productos', ['filter' => 'bajos']) }}" class="btn-custom" style="background: #ff9800; border: none; color: white;">
                            <i class="fas fa-box me-1"></i> Revisar
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="quick-actions">
        <h3 class="actions-title">
            <i class="fas fa-bolt"></i> Acciones Rápidas
        </h3>
        
        <div class="actions-grid">
                       
            <a href="{{ route('admin.productos.nuevo') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-box"></i>
                </div>
                <span class="action-text">Agregar Producto</span>
            </a>
            
            <a href="{{ route('admin.reportes') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <span class="action-text">Ver Reportes</span>
            </a>
            
            <a href="{{ route('admin.sucursales') }}" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <span class="action-text">Gestionar Sucursales</span>
            </a>
        </div>
    </div>

    <script>
        // Reloj en tiempo real
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
        
        // SweetAlert2 - Notificación de bienvenida
        document.addEventListener('DOMContentLoaded', function() {
            const userName = "{{ $usuario_nombre }}";
            
            setTimeout(() => {
                Swal.fire({
                    title: `¡Bienvenido, ${userName}!`,
                    text: 'Panel de administración Tanques Tláloc',
                    icon: 'success',
                    confirmButtonColor: '#7fad39',
                    confirmButtonText: 'Comenzar',
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            }, 500);
            
            // Mostrar alerta si hay muchos pedidos pendientes
            @if($stats['pedidos_pendientes'] > 5)
            setTimeout(() => {
                Swal.fire({
                    title: 'Pedidos Pendientes',
                    html: `Tienes <strong>{{ $stats['pedidos_pendientes'] }} pedidos</strong> pendientes por atender.`,
                    icon: 'warning',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Revisar Ahora',
                    showCancelButton: true,
                    cancelButtonText: 'Más Tarde'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("admin.pedidos", ["estado" => "pendiente"]) }}';
                    }
                });
            }, 2000);
            @endif
            
            // Mostrar alerta si hay productos bajos en inventario
            @if($stats['productos_bajos'] > 0)
            setTimeout(() => {
                Swal.fire({
                    title: 'Inventario Bajo',
                    html: `<p>Tienes <strong>{{ $stats['productos_bajos'] }} productos</strong> con inventario bajo.</p>`,
                    icon: 'info',
                    confirmButtonColor: '#17a2b8',
                    confirmButtonText: 'Ver Inventario',
                    showCancelButton: true,
                    cancelButtonText: 'Después'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("admin.productos", ["filter" => "bajos"]) }}';
                    }
                });
            }, 3000);
            @endif
        });
        
        // Auto-refresh del dashboard cada 2 minutos
        setTimeout(function() {
            Swal.fire({
                title: 'Actualizando datos...',
                text: 'Obteniendo información más reciente',
                icon: 'info',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            }).then(() => {
                window.location.reload();
            });
        }, 120000);
        
        // Hacer filas clickeables
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('.action-buttons')) {
                    const viewLink = this.querySelector('.btn-view');
                    if (viewLink) {
                        window.location.href = viewLink.href;
                    }
                }
            });
        });
    </script>
@endsection