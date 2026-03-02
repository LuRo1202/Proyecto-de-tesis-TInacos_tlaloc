@extends('vendedor.layouts.app')

@section('title', 'Mis Clientes')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="header-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-0 d-flex align-items-center flex-wrap">
                    <i class="fas fa-user-friends me-2"></i>Mis Clientes
                    <span class="sucursal-badge">
                        <i class="fas fa-store"></i> {{ $sucursalNombre }}
                    </span>
                    @if($tipo_vista == 'mis_clientes')
                    <span class="vista-badge">
                        <i class="fas fa-user-check"></i> Clientes Asignados
                    </span>
                    @else
                    <span class="vista-badge">
                        <i class="fas fa-building"></i> Clientes de Sucursal
                    </span>
                    @endif
                </h1>
                <p class="text-muted mb-0 small">Gestión de clientes y contactos</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('vendedor.pedidos.create') }}" class="btn btn-success">
                    <i class="fas fa-plus-circle me-1"></i> Nuevo Pedido
                </a>
            </div>
        </div>
    </div>

    <!-- Información de vista -->
    @if($tipo_vista == 'sucursal_clientes')
    <div class="info-alert">
        <i class="fas fa-info-circle"></i>
        <strong>Nota:</strong> Estás viendo todos los clientes de la sucursal 
        <strong>{{ $sucursalNombre }}</strong> porque no tienes clientes específicamente asignados.
    </div>
    @endif

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['total_clientes']) }}</div>
            <div class="stat-label">Clientes Totales</div>
            <small class="text-muted">
                {{ $tipo_vista == 'mis_clientes' ? 'Clientes asignados' : 'Clientes de sucursal' }}
            </small>
        </div>
        
        <div class="stat-card clientes-activos">
            <div class="stat-value">{{ number_format($stats['clientes_30dias']) }}</div>
            <div class="stat-label">Clientes Activos</div>
            <small class="text-muted">Últimos 30 días</small>
        </div>
        
        <div class="stat-card ticket">
            <div class="stat-value">
                {{ $stats['ticket_promedio'] > 0 ? 
                '$' . number_format($stats['ticket_promedio'], 0) : 'N/A' }}
            </div>
            <div class="stat-label">Ticket Promedio</div>
            <small class="text-muted">Por cliente</small>
        </div>
    </div>

    <!-- Buscador -->
    <div class="search-box">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="busqueda" class="form-control border-0 bg-light" 
                           placeholder="Buscar cliente por nombre, teléfono o ciudad..." 
                           value="{{ $busqueda }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                    @if($busqueda)
                    <a href="{{ route('vendedor.clientes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Limpiar
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de clientes -->
    @if(count($clientes) > 0)
    <div class="row g-4">
        @foreach($clientes as $cliente)
            @php
                $inicial = strtoupper(substr($cliente->cliente_nombre, 0, 1));
                $es_frecuente = $cliente->total_pedidos >= 3;
                $es_premium = $cliente->total_comprado >= 10000;
                $es_nuevo = $cliente->total_pedidos == 1 && $cliente->total_comprado < 5000;
                $dias_ultima_compra = $cliente->ultima_compra ? 
                    \Carbon\Carbon::parse($cliente->ultima_compra)->diffInDays(now()) : 999;
                $telefono_limpio = preg_replace('/[^0-9]/', '', $cliente->cliente_telefono);
                
                // Colores para el avatar según inicial
                $colores_avatar = [
                    'A' => '#FF6B6B', 'B' => '#4ECDC4', 'C' => '#45B7D1', 'D' => '#96CEB4',
                    'E' => '#FFEAA7', 'F' => '#DDA0DD', 'G' => '#98D8C8', 'H' => '#F7DC6F',
                    'I' => '#BB8FCE', 'J' => '#85C1E2', 'K' => '#F1948A', 'L' => '#82E0AA',
                    'M' => '#F5B7B1', 'N' => '#AED6F1', 'O' => '#F9E79F', 'P' => '#D7BDE2',
                    'Q' => '#A9DFBF', 'R' => '#FAD7A0', 'S' => '#F0B27A', 'T' => '#7FB3D5',
                    'U' => '#F1948A', 'V' => '#7DCEA0', 'W' => '#E59866', 'X' => '#B2BABB',
                    'Y' => '#F7DC6F', 'Z' => '#AED6F1'
                ];
                $color_avatar = $colores_avatar[$inicial] ?? $colores_avatar['A'];
            @endphp
        <div class="col-xxl-3 col-xl-4 col-lg-6">
            <div class="cliente-card">
                <!-- Badge de estado superior -->
                <div class="card-status-bar" style="background: {{ $color_avatar }};"></div>
                
                <div class="card-header-cliente">
                    <div class="cliente-avatar" style="background: linear-gradient(135deg, {{ $color_avatar }}, {{ $color_avatar }}dd);">
                        {{ $inicial }}
                    </div>
                    <div class="cliente-info-header">
                        <h5 class="cliente-nombre">{{ $cliente->cliente_nombre }}</h5>
                        <div class="cliente-telefono">
                            <i class="fas fa-phone-alt me-1"></i>
                            {{ $cliente->cliente_telefono }}
                        </div>
                        <div class="cliente-badges">
                            @if($es_nuevo)
                            <span class="badge-nuevo">
                                <i class="fas fa-seedling me-1"></i>Nuevo
                            </span>
                            @endif
                            @if($es_frecuente)
                            <span class="badge-frecuente">
                                <i class="fas fa-star me-1"></i>Frecuente
                            </span>
                            @endif
                            @if($es_premium)
                            <span class="badge-premium">
                                <i class="fas fa-crown me-1"></i>Premium
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body-cliente">
                    <!-- Ubicación -->
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Ubicación</span>
                            <span class="info-value">{{ $cliente->cliente_ciudad }}, {{ $cliente->cliente_estado }}</span>
                        </div>
                    </div>

                    <!-- Dirección (si existe) -->
                    @if($cliente->cliente_direccion)
                    <div class="info-row">
                        <div class="info-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Dirección</span>
                            <span class="info-value">{{ substr($cliente->cliente_direccion, 0, 40) }}{{ strlen($cliente->cliente_direccion) > 40 ? '...' : '' }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Estadísticas -->
                    <div class="stats-container">
                        <div class="stat-item">
                            <span class="stat-number">{{ $cliente->total_pedidos }}</span>
                            <span class="stat-text">Pedidos</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">${{ number_format($cliente->total_comprado, 0) }}</span>
                            <span class="stat-text">Gastado</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">
                                @if($cliente->ultima_compra)
                                    {{ \Carbon\Carbon::parse($cliente->ultima_compra)->format('d/m/y') }}
                                @else
                                    -
                                @endif
                            </span>
                            <span class="stat-text">Última</span>
                        </div>
                    </div>

                    <!-- Alerta de inactividad -->
                    @if($dias_ultima_compra > 90 && $cliente->total_comprado > 0)
                    <div class="inactive-alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ $dias_ultima_compra }} días sin comprar</span>
                    </div>
                    @endif
                </div>

                <div class="card-footer-cliente">
                    @if(!empty($telefono_limpio))
                    <a href="https://wa.me/52{{ $telefono_limpio }}?text=Hola%20{{ urlencode($cliente->cliente_nombre) }}%2C%20soy%20tu%20vendedor%20de%20Tanques%20Tláloc.%20¿Cómo%20estás?" 
                       target="_blank" class="btn-whatsapp">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                    @endif
                    <a href="{{ route('vendedor.pedidos.create', ['cliente' => $cliente->cliente_telefono]) }}" 
                       class="btn-pedido">
                        <i class="fas fa-cart-plus"></i>
                        <span>Nuevo Pedido</span>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Estado vacío -->
    <div class="empty-state">
        <i class="fas fa-user-friends"></i>
        <h5 class="text-muted mb-3">No hay clientes registrados</h5>
        <p class="text-muted mb-4">
            {{ $busqueda ? 
                'No se encontraron clientes con la búsqueda: "' . $busqueda . '"' : 
                'Aún no tienes clientes ' . ($tipo_vista == 'mis_clientes' ? 'asignados' : 'en esta sucursal') }}
        </p>
        @if(!$busqueda)
        <a href="{{ route('vendedor.pedidos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Crear Primer Cliente
        </a>
        @endif
    </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    :root {
        --primary: #7fad39;
        --primary-dark: #5a8a20;
        --primary-light: #9fc957;
        --light: #f8f9fa;
        --gray: #6c757d;
        --dark: #212529;
    }
    
    .header-bar {
        background: white;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 15px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        border-left: 4px solid var(--primary);
    }
    
    .sucursal-badge {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-left: 10px;
    }
    
    .vista-badge {
        background: linear-gradient(135deg, #fd7e14, #e8590c);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-left: 5px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        text-align: center;
        border-top: 4px solid var(--primary);
    }
    
    .stat-card.clientes-activos {
        border-top-color: #2ecc71;
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
    }
    
    .stat-card.ticket {
        border-top-color: #9b59b6;
        background: linear-gradient(135deg, #f3e5f5, #e1bee7);
    }
    
    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
        margin-bottom: 8px;
    }
    
    .stat-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--gray);
        margin-bottom: 5px;
    }
    
    .search-box {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .search-box .input-group {
        max-width: 500px;
    }
    
    /* TARJETAS MEJORADAS */
    .cliente-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid #eee;
    }
    
    .cliente-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(127, 173, 57, 0.15);
        border-color: var(--primary);
    }
    
    .card-status-bar {
        height: 4px;
        width: 100%;
        background: var(--primary);
    }
    
    .card-header-cliente {
        padding: 20px 20px 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .cliente-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
        color: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }
    
    .cliente-info-header {
        flex: 1;
    }
    
    .cliente-nombre {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0 0 5px 0;
        color: var(--dark);
    }
    
    .cliente-telefono {
        font-size: 0.9rem;
        color: var(--gray);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .cliente-telefono i {
        color: var(--primary);
        font-size: 0.9rem;
    }
    
    .cliente-badges {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    .badge-nuevo {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    
    .badge-frecuente {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    
    .badge-premium {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    
    .card-body-cliente {
        padding: 15px 20px;
        flex: 1;
    }
    
    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }
    
    .info-icon {
        width: 24px;
        color: var(--primary);
        font-size: 1rem;
        text-align: center;
        margin-top: 2px;
    }
    
    .info-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .info-label {
        font-size: 0.7rem;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    
    .info-value {
        font-size: 0.9rem;
        color: var(--dark);
        line-height: 1.3;
    }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin: 15px 0;
        background: #f8f9fa;
        border-radius: 10px;
        padding: 12px;
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-number {
        display: block;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 3px;
    }
    
    .stat-text {
        font-size: 0.7rem;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .inactive-alert {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        border-radius: 6px;
        padding: 8px 12px;
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #856404;
    }
    
    .inactive-alert i {
        color: #ffc107;
        font-size: 1rem;
    }
    
    .card-footer-cliente {
        padding: 15px 20px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
    }
    
    .btn-whatsapp {
        background: #25D366;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        flex: 1;
    }
    
    .btn-whatsapp:hover {
        background: #128C7E;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);
    }
    
    .btn-pedido {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        flex: 1;
    }
    
    .btn-pedido:hover {
        background: linear-gradient(135deg, var(--primary-dark), #4a7a18);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(127, 173, 57, 0.3);
    }
    
    .info-alert {
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        border-left: 4px solid #2196f3;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        color: #1565c0;
    }
    
    .info-alert i {
        font-size: 1.2rem;
        margin-right: 10px;
    }
    
    .empty-state {
        background: white;
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }
    
    /* Responsive */
    @media (max-width: 1400px) {
        .col-xxl-3 { width: 33.333%; }
    }
    
    @media (max-width: 1200px) {
        .col-xl-4 { width: 50%; }
    }
    
    @media (max-width: 768px) {
        .col-lg-6 { width: 100%; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .cliente-avatar { width: 60px; height: 60px; font-size: 1.5rem; }
        .cliente-nombre { font-size: 1.1rem; }
    }
    
    @media (max-width: 576px) {
        .stats-grid { grid-template-columns: 1fr; }
        .card-footer-cliente { flex-direction: column; }
        .btn-whatsapp, .btn-pedido { width: 100%; }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Contar clientes inactivos
        const clientesInactivos = document.querySelectorAll('.inactive-alert');
        if (clientesInactivos.length > 0) {
            setTimeout(() => {
                Swal.fire({
                    title: '¡Clientes Inactivos!',
                    html: `Tienes <strong>${clientesInactivos.length} cliente(s)</strong> inactivos (más de 90 días sin comprar).<br><br>¿Deseas contactarlos?`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ver Clientes',
                    cancelButtonText: 'Después',
                    confirmButtonColor: '#7fad39',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        clientesInactivos[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }, 2000);
        }
        
        // Animación para tarjetas de clientes
        document.querySelectorAll('.cliente-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection