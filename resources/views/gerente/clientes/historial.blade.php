@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial del Cliente - Sucursal {{ session('sucursal_nombre') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --primary: #7fad39;
            --primary-dark: #5a8a20;
            --primary-light: #9fc957;
            --light: #f8f9fa;
            --light-gray: #e9ecef;
            --gray: #6c757d;
            --dark: #212529;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
        }
        
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .header-bar {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .header-title {
            margin: 0;
            color: var(--dark);
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .header-title i {
            color: var(--primary);
        }
        
        .sucursal-badge {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-left: 10px;
        }
        
        .header-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn-custom {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        
        .btn-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }
        
        .btn-secondary-custom {
            background: white;
            color: var(--gray);
            border: 1px solid var(--light-gray);
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--success), #218838);
            color: white;
        }
        
        .cliente-info-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            border-left: 5px solid var(--primary);
        }
        
        .cliente-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(127, 173, 57, 0.3);
        }
        
        .cliente-nombre {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }
        
        .cliente-detalle-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: var(--light);
            border-radius: 8px;
            margin-bottom: 8px;
        }
        
        .cliente-detalle-item i {
            width: 20px;
            color: var(--primary);
            font-size: 1rem;
        }
        
        .cliente-email-badge {
            background: rgba(127, 173, 57, 0.1);
            color: var(--primary-dark);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid rgba(127, 173, 57, 0.2);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 18px 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            text-align: center;
            border-bottom: 3px solid var(--primary);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
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
            font-size: 0.8rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-icon {
            font-size: 2rem;
            color: var(--primary);
            opacity: 0.2;
            position: absolute;
            top: 10px;
            right: 10px;
        }
        
        .badge-estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            min-width: 90px;
            justify-content: center;
        }
        
        .badge-pendiente { 
            background: #fff3cd; 
            color: #856404; 
            border: 1px solid #ffeaa7;
        }
        .badge-confirmado { 
            background: #d1ecf1; 
            color: #0c5460; 
            border: 1px solid #bee5eb;
        }
        .badge-enviado { 
            background: #cce5ff; 
            color: #004085; 
            border: 1px solid #b8daff;
        }
        .badge-entregado { 
            background: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb;
        }
        .badge-cancelado { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--light-gray);
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .card-header h5 {
            margin: 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-header h5 i {
            color: var(--primary);
        }
        
        .table {
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #eee;
            padding: 12px 15px;
            white-space: nowrap;
            background: #fafafa;
        }
        
        .table td {
            vertical-align: middle;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .table-hover tbody tr {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(127, 173, 57, 0.05);
        }
        

        
        .pago-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .pago-confirmado {
            background: #d4edda;
            color: #155724;
        }
        
        .pago-pendiente {
            background: #fff3cd;
            color: #856404;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .empty-state h5 {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .badge-producto {
            background: #e9ecef;
            color: #495057;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            display: inline-block;
            margin: 2px 0;
        }
        
        .producto-detalle-mini {
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
            }
            .cliente-nombre {
                font-size: 1.4rem;
            }
            .cliente-avatar-large {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                margin-left: 0;
            }
            .header-actions {
                flex-direction: column;
                width: 100%;
            }
            .btn-custom {
                width: 100%;
                justify-content: center;
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    @include('gerente.layouts.sidebar')
    
    <div class="main-content animate-fade-in">
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-user-clock me-2"></i>Historial del Cliente
                    <span class="sucursal-badge">
                        <i class="fas fa-store"></i> {{ session('sucursal_nombre') }}
                    </span>
                </h1>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('gerente.clientes') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver a Clientes
                </a>
                <a href="https://wa.me/52{{ preg_replace('/[^0-9]/', '', $cliente->telefono ?? $cliente->cliente_telefono) }}?text=Hola%20{{ urlencode($cliente->nombre ?? $cliente->cliente_nombre) }}%2C%20te%20contacto%20de%20Tinacos%20Tláloc%20-%20Sucursal%20{{ urlencode(session('sucursal_nombre')) }}"
                   class="btn-custom btn-success-custom" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>

        <!-- Tarjeta de información del cliente mejorada -->
        <div class="cliente-info-card">
            <div class="row align-items-center">
                <div class="col-auto">
                    @php
                        $iniciales = '';
                        $nombreCompleto = $cliente->nombre ?? $cliente->cliente_nombre;
                        $partes_nombre = explode(' ', $nombreCompleto);
                        foreach($partes_nombre as $parte) {
                            if(trim($parte) != '') {
                                $iniciales .= strtoupper(substr($parte, 0, 1));
                                if(strlen($iniciales) >= 2) break;
                            }
                        }
                        $telefono = $cliente->telefono ?? $cliente->cliente_telefono;
                        $direccion = $cliente->direccion ?? $cliente->cliente_direccion ?? '';
                        $ciudad = $cliente->ciudad ?? $cliente->cliente_ciudad ?? '';
                        $estado = $cliente->estado ?? $cliente->cliente_estado ?? '';
                        $email = $cliente->email ?? null;
                    @endphp
                    <div class="cliente-avatar-large">
                        {{ $iniciales ?: 'C' }}
                    </div>
                </div>
                <div class="col">
                    <div class="cliente-nombre">{{ $nombreCompleto }}</div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="cliente-detalle-item">
                                <i class="fas fa-phone-alt"></i>
                                <div>
                                    <small class="text-muted d-block">Teléfono</small>
                                    <strong>{{ $telefono }}</strong>
                                </div>
                            </div>
                            @if($email)
                            <div class="cliente-detalle-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <small class="text-muted d-block">Email</small>
                                    <strong>{{ $email }}</strong>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="cliente-detalle-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <small class="text-muted d-block">Dirección</small>
                                    <strong>{{ $direccion ?: 'No especificada' }}</strong>
                                </div>
                            </div>
                            <div class="cliente-detalle-item">
                                <i class="fas fa-city"></i>
                                <div>
                                    <small class="text-muted d-block">Ubicación</small>
                                    <strong>{{ $ciudad ?: 'N/A' }}, {{ $estado ?: 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas mejoradas -->
        <div class="stats-grid">
            <div class="stat-card position-relative">
                <i class="fas fa-shopping-cart stat-icon"></i>
                <div class="stat-value">{{ $stats_cliente['total_pedidos'] }}</div>
                <div class="stat-label">Total Pedidos</div>
            </div>
            <div class="stat-card position-relative">
                <i class="fas fa-dollar-sign stat-icon"></i>
                <div class="stat-value">${{ number_format($stats_cliente['total_gastado'], 2) }}</div>
                <div class="stat-label">Total Gastado</div>
            </div>
            <div class="stat-card position-relative">
                <i class="fas fa-check-circle stat-icon"></i>
                <div class="stat-value">{{ $stats_cliente['pedidos_entregados'] }}</div>
                <div class="stat-label">Entregados</div>
            </div>
            <div class="stat-card position-relative">
                <i class="fas fa-clock stat-icon"></i>
                <div class="stat-value">{{ $stats_cliente['pedidos_pendientes'] }}</div>
                <div class="stat-label">Pendientes</div>
            </div>
        </div>

        <!-- Tabla de pedidos mejorada -->
        <div class="card">
            <div class="card-header">
                <h5>
                    <i class="fas fa-history"></i> Historial de Pedidos
                </h5>
                <span class="badge bg-primary">{{ $pedidos->count() }} pedidos</span>
            </div>
            <div class="card-body p-0">
                @if($pedidos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Productos</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Pago</th>
                                <th>Entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidos as $pedido)
                            @php
                                $totalProductos = $pedido->items ? $pedido->items->sum('cantidad') : 0;
                                $primerProducto = $pedido->items && $pedido->items->first() ? $pedido->items->first()->producto_nombre : '';
                                $masProductos = $pedido->items && $pedido->items->count() > 1 ? ($pedido->items->count() - 1) : 0;
                            @endphp
                            <tr onclick="window.location.href='{{ route('gerente.pedidos.ver', $pedido->id) }}'">
                                <td>
                                    <span class="folio-badge">#{{ $pedido->folio }}</span>
                                </td>
                                <td>
                                    <div>{{ $pedido->fecha->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $pedido->fecha->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $primerProducto ?: 'Sin productos' }}</div>
                                    <small class="text-muted">
                                        {{ $totalProductos }} productos
                                        @if($masProductos > 0)
                                            <span class="badge-producto ms-1">+{{ $masProductos }} más</span>
                                        @endif
                                    </small>
                                </td>
                                <td class="text-end fw-bold">${{ number_format($pedido->total, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge-estado badge-{{ $pedido->estado }}">
                                        <i class="fas fa-circle fa-xs me-1"></i>
                                        {{ ucfirst($pedido->estado) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($pedido->pago_confirmado)
                                        <span class="pago-badge pago-confirmado">
                                            <i class="fas fa-check-circle me-1"></i>Confirmado
                                        </span>
                                    @else
                                        <span class="pago-badge pago-pendiente">
                                            <i class="fas fa-clock me-1"></i>Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($pedido->fecha_entrega)
                                        {{ $pedido->fecha_entrega->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h5>No hay pedidos registrados</h5>
                    <p class="text-muted">Este cliente no tiene pedidos en tu sucursal.</p>
                    <a href="{{ route('gerente.clientes') }}" class="btn-custom btn-primary-custom mt-3">
                        <i class="fas fa-arrow-left me-1"></i> Volver a Clientes
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animación para las tarjetas de estadísticas
            document.querySelectorAll('.stat-card').forEach((card, index) => {
                card.style.animation = `fadeIn 0.5s ease-out ${index * 0.1}s both`;
            });
            
            // Efecto hover para filas de la tabla
            document.querySelectorAll('.table tbody tr').forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(127, 173, 57, 0.05)';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
            
            // Mostrar mensaje de bienvenida
            @if($pedidos->count() > 0)
            setTimeout(() => {
                Swal.fire({
                    title: 'Historial de Cliente',
                    html: `Mostrando <strong>{{ $pedidos->count() }} pedidos</strong> de {{ $cliente->nombre ?? $cliente->cliente_nombre }}`,
                    icon: 'info',
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            }, 500);
            @endif
        });
    </script>
    
    @if(session('swal'))
    <script>
        Swal.fire({
            title: '{{ session('swal.title') }}',
            text: '{{ session('swal.message') }}',
            icon: '{{ session('swal.type') }}',
            confirmButtonColor: '#7fad39'
        });
    </script>
    @endif
</body>
</html>