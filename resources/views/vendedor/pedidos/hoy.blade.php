@extends('vendedor.layouts.app')

@section('title', 'Pedidos de Hoy')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="header-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-bolt me-2"></i>Pedidos de Hoy
                </h1>
                <p class="text-muted mb-0 small">
                    {{ now()->format('d/m/Y') }} | Sucursal: <strong>{{ $sucursalNombre }}</strong>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('vendedor.pedidos.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list me-1"></i> Todos los Pedidos
                </a>
                <a href="{{ route('vendedor.pedidos.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Nuevo Pedido
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas del día -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-value">{{ $contadores['total'] }}</div>
            <div class="stat-label">
                <i class="fas fa-shopping-cart me-1"></i>
                Total Hoy
            </div>
        </div>
        
        @if($pedidos_urgentes > 0)
        <div class="stat-card urgente">
            @if($pedidos_urgentes > 0)
            <span class="urgente-badge" title="¡Urgentes!">
                <i class="fas fa-exclamation"></i>
            </span>
            @endif
            <div class="stat-value" style="color: var(--danger);">{{ $pedidos_urgentes }}</div>
            <div class="stat-label">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Urgentes
            </div>
        </div>
        @endif
        
        <div class="stat-card pendiente">
            <div class="stat-value">{{ $contadores['pendiente'] }}</div>
            <div class="stat-label">
                <i class="fas fa-clock me-1"></i>
                Pendientes
            </div>
        </div>
        
        <div class="stat-card confirmado">
            <div class="stat-value">{{ $contadores['confirmado'] }}</div>
            <div class="stat-label">
                <i class="fas fa-check-circle me-1"></i>
                Confirmados
            </div>
        </div>
        
        <div class="stat-card enviado">
            <div class="stat-value">{{ $contadores['enviado'] }}</div>
            <div class="stat-label">
                <i class="fas fa-truck me-1"></i>
                Enviados
            </div>
        </div>
        
        <div class="stat-card entregado">
            <div class="stat-value">{{ $contadores['entregado'] }}</div>
            <div class="stat-label">
                <i class="fas fa-box-check me-1"></i>
                Entregados
            </div>
        </div>
        
        <div class="stat-card ventas">
            <div class="stat-value">${{ number_format($ventas_hoy, 0) }}</div>
            <div class="stat-label">
                <i class="fas fa-dollar-sign me-1"></i>
                Ventas Hoy
            </div>
        </div>
    </div>

    <!-- Lista de pedidos -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-shopping-cart me-2"></i>Pedidos del Día
            </h5>
            <span class="badge bg-primary">{{ count($pedidos_hoy) }}</span>
        </div>
        <div class="card-body p-0">
            @if(count($pedidos_hoy) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Hora</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos_hoy as $pedido)
                            @php
                                // Determinar si es urgente (pendiente con más de 1 hora)
                                $es_urgente = $pedido->estado == 'pendiente' && 
                                            \Carbon\Carbon::parse($pedido->fecha)->diffInHours(now()) >= 1;
                                
                                // Formatear hora
                                $hora_pedido = \Carbon\Carbon::parse($pedido->fecha)->format('H:i');
                                $fecha_pedido = \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y');
                                
                                // Limpiar teléfono para WhatsApp
                                $telefono_limpio = preg_replace('/[^0-9]/', '', $pedido->cliente_telefono);
                            @endphp
                            <tr class="{{ $es_urgente ? 'table-warning' : '' }}">
                                <td>
                                    <div class="position-relative">
                                        <strong class="text-primary">#{{ $pedido->folio }}</strong>
                                        @if($es_urgente)
                                        <span class="urgente-badge" title="¡Urgente! Más de 1 hora pendiente">
                                            <i class="fas fa-exclamation"></i>
                                        </span>
                                        @endif
                                        <small class="text-muted d-block">{{ $pedido->items_count }} items</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $pedido->cliente_nombre }}</div>
                                    <small class="text-muted">{{ $pedido->cliente_telefono }}</small>
                                </td>
                                <td>
                                    {{ $hora_pedido }}
                                    <br>
                                    <small class="text-muted">{{ $fecha_pedido }}</small>
                                </td>
                                <td class="fw-bold text-success">
                                    ${{ number_format($pedido->total, 2) }}
                                </td>
                                <td>
                                    <span class="badge-estado badge-{{ strtolower($pedido->estado) }}">
                                        <i class="fas fa-circle fa-xs"></i>
                                        {{ ucfirst($pedido->estado) }}
                                    </span>
                                </td>
                                <td>
                                    @if($pedido->pago_confirmado)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Pagado
                                    </span>
                                    @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>Pendiente
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if($es_urgente)
                                        <a href="{{ route('vendedor.pedidos.show', $pedido->id) }}" 
                                           class="btn-urgente btn-sm">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Urgente
                                        </a>
                                        @else
                                        <a href="{{ route('vendedor.pedidos.show', $pedido->id) }}" 
                                           class="btn btn-sm btn-info" title="Ver pedido">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                        <a href="{{ route('vendedor.pedidos.seguimiento', $pedido->id) }}" 
                                           class="btn btn-sm btn-success" title="Seguimiento">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                        @if(!empty($telefono_limpio))
                                        <a href="javascript:void(0)" 
                                           onclick="contactarCliente('{{ $telefono_limpio }}', '{{ addslashes($pedido->cliente_nombre) }}')" 
                                           class="btn btn-sm btn-whatsapp" 
                                           style="background-color: #25D366; border-color: #25D366; color: white;" 
                                           title="Contactar por WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
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
            <div class="text-center py-5">
                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay pedidos hoy</h5>
                <p class="text-muted small">No se han registrado pedidos en esta sucursal para el día de hoy</p>
                <a href="{{ route('vendedor.pedidos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Crear Primer Pedido
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Recordatorios -->
    @if($pedidos_pendientes > 0)
    <div class="alert alert-warning mt-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <h6 class="mb-1">¡Atención!</h6>
                <p class="mb-0">
                    Hay <strong>{{ $pedidos_pendientes }} pedido(s) pendientes</strong> de hoy en esta sucursal. 
                    @if($pedidos_urgentes > 0)
                    <strong>{{ $pedidos_urgentes }} de ellos son urgentes</strong> (más de 1 hora sin atender).
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif
    
    @if($ventas_hoy > 0)
    <div class="alert alert-success mt-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-trophy fa-2x me-3"></i>
            <div>
                <h6 class="mb-1">¡Excelente día de ventas!</h6>
                <p class="mb-0">
                    Ventas totales del día: <strong>${{ number_format($ventas_hoy, 2) }}</strong>
                    @if($contadores['entregado'] > 0)
                    de <strong>{{ $contadores['entregado'] }} pedido(s) entregado(s)</strong>.
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    :root {
        --primary: #7fad39;
        --primary-dark: #5a8a20;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --info: #17a2b8;
        --light-gray: #e9ecef;
    }
    
    .header-bar {
        background: white;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        border-left: 4px solid var(--primary);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
        margin-bottom: 15px;
    }
    
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        text-align: center;
        border-top: 3px solid var(--primary);
        position: relative;
        transition: transform 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    .stat-card.total { border-top-color: var(--primary); }
    .stat-card.pendiente { border-top-color: var(--warning); }
    .stat-card.confirmado { border-top-color: var(--info); }
    .stat-card.enviado { border-top-color: #6f42c1; }
    .stat-card.entregado { border-top-color: var(--success); }
    .stat-card.ventas { border-top-color: var(--success); }
    .stat-card.urgente { 
        border-top-color: var(--danger);
        animation: pulse-border 2s infinite;
    }
    
    @keyframes pulse-border {
        0% { border-top-color: var(--danger); }
        50% { border-top-color: #ff6b6b; }
        100% { border-top-color: var(--danger); }
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 4px;
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
    
    .urgente-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--danger);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
        animation: pulse 2s infinite;
        z-index: 1;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .badge-estado {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid transparent;
    }
    
    .badge-pendiente { 
        background: #fff3cd; 
        color: #856404; 
        border-color: #ffeaa7;
    }
    .badge-confirmado { 
        background: #d1ecf1; 
        color: #0c5460; 
        border-color: #bee5eb;
    }
    .badge-enviado { 
        background: #cce5ff; 
        color: #004085; 
        border-color: #b8daff;
    }
    .badge-entregado { 
        background: #d4edda; 
        color: #155724; 
        border-color: #c3e6cb;
    }
    .badge-cancelado { 
        background: #f8d7da; 
        color: #721c24; 
        border-color: #f5c6cb;
    }
    
    .btn-urgente {
        background: linear-gradient(135deg, var(--danger), #c82333);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .btn-urgente:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        color: white;
    }
    
    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }
    
    .table th {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        border-bottom: 2px solid var(--light-gray);
    }
    
    .action-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    
    .card-footer {
        background: white;
        border-top: 1px solid var(--light-gray);
        padding: 12px 15px;
    }
    
    @media (max-width: 1200px) {
        .container-fluid { padding: 12px; }
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .stats-grid { grid-template-columns: 1fr; }
        .action-buttons { flex-direction: column; }
        .action-buttons a { width: 100%; justify-content: center; }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== MOSTRAR MENSAJES DE SESIÓN CON SWEETALERT =====
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

        // Función para contactar por WhatsApp
        window.contactarCliente = function(telefono, nombre) {
            if (telefono) {
                const mensaje = `Hola ${nombre}, soy tu vendedor de Tanques Tláloc. Te contacto sobre tu pedido de hoy.`;
                const url = `https://wa.me/52${telefono}?text=${encodeURIComponent(mensaje)}`;
                window.open(url, '_blank');
            } else {
                Swal.fire('Error', 'No se pudo obtener el número de teléfono', 'error');
            }
        };
        
        // Notificación de pedidos pendientes
        const pedidosPendientes = {{ $pedidos_pendientes }};
        const pedidosUrgentes = {{ $pedidos_urgentes }};
        
        if (pedidosUrgentes > 0) {
            setTimeout(() => {
                Swal.fire({
                    title: '¡Pedidos Urgentes!',
                    html: `Hay <strong>${pedidosUrgentes} pedido(s) urgente(s)</strong> en la sucursal que requieren atención inmediata.`,
                    icon: 'warning',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ver Ahora',
                    showCancelButton: true,
                    cancelButtonText: 'Después'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar solo los urgentes
                        document.querySelectorAll('tbody tr').forEach(row => {
                            if (!row.classList.contains('table-warning')) {
                                row.style.display = 'none';
                            }
                        });
                        
                        // Actualizar contador
                        const visibleCount = document.querySelectorAll('tbody tr.table-warning').length;
                        document.querySelector('.card-header .badge').textContent = visibleCount;
                        
                        Swal.fire({
                            title: 'Mostrando solo urgentes',
                            text: 'Se están mostrando solo los pedidos urgentes',
                            icon: 'info',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }, 1000);
        } else if (pedidosPendientes > 0) {
            setTimeout(() => {
                Swal.fire({
                    title: 'Pedidos Pendientes',
                    html: `Hay <strong>${pedidosPendientes} pedido(s) pendientes</strong> en la sucursal.`,
                    icon: 'info',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Ver Pendientes',
                    showCancelButton: true,
                    cancelButtonText: 'Después'
                });
            }, 1500);
        }
        
        // Filtros de estado
        const filtroContainer = document.createElement('div');
        filtroContainer.className = 'card-footer d-flex gap-2 flex-wrap';
        filtroContainer.innerHTML = `
            <button class="btn btn-sm btn-outline-primary" onclick="filtrarTabla('all')">
                <i class="fas fa-filter me-1"></i>Todos
            </button>
            <button class="btn btn-sm btn-outline-warning" onclick="filtrarTabla('pendiente')">
                Pendientes
            </button>
            <button class="btn btn-sm btn-outline-info" onclick="filtrarTabla('confirmado')">
                Confirmados
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="filtrarTabla('enviado')">
                Enviados
            </button>
            <button class="btn btn-sm btn-outline-success" onclick="filtrarTabla('entregado')">
                Entregados
            </button>
        `;
        
        // Insertar filtro después de la tabla
        const card = document.querySelector('.card');
        if (card) {
            card.appendChild(filtroContainer);
        }
        
        window.filtrarTabla = function(estado) {
            const rows = document.querySelectorAll('tbody tr');
            let contador = 0;
            
            rows.forEach(row => {
                const badge = row.querySelector('.badge-estado');
                if (badge) {
                    const estadoRow = badge.classList.contains('badge-pendiente') ? 'pendiente' :
                                    badge.classList.contains('badge-confirmado') ? 'confirmado' :
                                    badge.classList.contains('badge-enviado') ? 'enviado' :
                                    badge.classList.contains('badge-entregado') ? 'entregado' : '';
                    
                    if (estado === 'all' || estadoRow === estado) {
                        row.style.display = '';
                        contador++;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
            
            // Actualizar contador en el header
            const headerBadge = document.querySelector('.card-header .badge');
            if (headerBadge) {
                headerBadge.textContent = contador;
            }
            
            // Mostrar mensaje si no hay resultados
            if (contador === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin resultados',
                    text: `No hay pedidos en estado "${estado}"`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        };
    });
</script>
@endsection