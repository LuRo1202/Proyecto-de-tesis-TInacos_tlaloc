@extends('vendedor.layouts.app')

@section('title', 'Mis Ventas')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="header-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-0 d-flex align-items-center flex-wrap">
                    <i class="fas fa-chart-line me-2"></i>Mis Ventas
                    <span class="sucursal-badge">
                        <i class="fas fa-store"></i> {{ $sucursalNombre }}
                    </span>
                    @if($tipo_vista == 'mis_pedidos')
                    <span class="vista-badge">
                        <i class="fas fa-user-check"></i> Mis Pedidos Asignados
                    </span>
                    @else
                    <span class="vista-badge">
                        <i class="fas fa-building"></i> Todos los Pedidos de Sucursal
                    </span>
                    @endif
                </h1>
                <p class="text-muted mb-0 small">Reporte personalizado de ventas</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <!-- Filtros -->
                <select id="filtroMes" class="form-select form-select-sm" style="width: 150px;">
                    <option value="mensual" {{ $tipo_reporte == 'mensual' ? 'selected' : '' }}>Vista Mensual</option>
                    <option value="diario" {{ $tipo_reporte == 'diario' ? 'selected' : '' }}>Vista Diaria</option>
                </select>

            </div>
        </div>
    </div>

    <!-- Información de vista -->
    @if($tipo_vista == 'sucursal')
    <div class="info-alert">
        <i class="fas fa-info-circle"></i>
        <strong>Nota:</strong> Estás viendo todos los pedidos de la sucursal 
        <strong>{{ $sucursalNombre }}</strong> porque no tienes pedidos específicamente asignados.
    </div>
    @endif

    <!-- Estadísticas Principales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ number_format($estadisticas['total_pedidos']) }}</div>
            <div class="stat-label">Pedidos Totales</div>
            <small class="text-muted">
                {{ $tipo_vista == 'mis_pedidos' ? 'Pedidos asignados a ti' : 'Todos los pedidos de sucursal' }}
            </small>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">${{ number_format($estadisticas['ventas_totales'], 0) }}</div>
            <div class="stat-label">Ventas Totales</div>
            <small class="text-muted">Total de ventas confirmadas</small>
        </div>
        
        <div class="stat-card promedio">
            <div class="stat-value">
                {{ $estadisticas['promedio_venta'] > 0 ? 
                '$' . number_format($estadisticas['promedio_venta'], 0) : 'N/A' }}
            </div>
            <div class="stat-label">Promedio por Venta</div>
            <small class="text-muted">Ticket promedio</small>
        </div>
        
        <div class="stat-card clientes">
            <div class="stat-value">{{ number_format($estadisticas['clientes_unicos']) }}</div>
            <div class="stat-label">Clientes Únicos</div>
            <small class="text-muted">Clientes atendidos</small>
        </div>
        
        <div class="stat-card comisiones">
            <div class="stat-value">
                {{ $comisiones_totales > 0 ? 
                '$' . number_format($comisiones_totales, 0) : '$0' }}
            </div>
            <div class="stat-label">Comisiones Totales</div>
            <small class="text-muted">5% sobre ventas confirmadas</small>
        </div>
    </div>

    <!-- Meta de ventas -->
    <div class="meta-ventas">
        <h6><i class="fas fa-bullseye me-2"></i>Meta Mensual de Ventas</h6>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small">Progreso:</span>
            <span class="fw-bold">{{ number_format($porcentaje_meta, 1) }}%</span>
        </div>
        <div class="progress-ventas">
            <div class="progress-bar-ventas" style="width: {{ $porcentaje_meta }}%"></div>
        </div>
        <div class="d-flex justify-content-between mt-2">
            <small class="text-muted">
                <i class="fas fa-check-circle text-success me-1"></i>
                Realizado: ${{ number_format($estadisticas['ventas_totales'], 0) }}
            </small>
            <small class="text-muted">
                <i class="fas fa-flag text-warning me-1"></i>
                Meta: ${{ number_format($meta_mensual, 0) }}
            </small>
        </div>
    </div>

    <!-- Gráfico de ventas -->
    <div class="row">
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2"></i>Historial de Ventas por Mes
                </h5>
                <canvas id="ventasChart"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <!-- Productos más vendidos -->
            <div class="table-top">
                <h5 class="mb-3">
                    <i class="fas fa-fire me-2"></i>Productos más Vendidos
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos_top as $producto)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $producto->producto_nombre }}</div>
                                </td>
                                <td>
                                    <span class="badge-pedido">{{ $producto->cantidad_vendida }} unid.</span>
                                </td>
                                <td class="fw-bold text-success">
                                    ${{ number_format($producto->total_vendido, 0) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">
                                    <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                                    No hay datos de productos vendidos
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas adicionales -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <!-- Clientes más frecuentes -->
            <div class="table-top">
                <h5 class="mb-3">
                    <i class="fas fa-crown me-2"></i>Mejores Clientes
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Pedidos</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientes_top as $cliente)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $cliente->cliente_nombre }}</div>
                                    <small class="text-muted d-block">{{ $cliente->cliente_telefono }}</small>
                                </td>
                                <td>
                                    <span class="badge-pedido">{{ $cliente->pedidos }} pedidos</span>
                                </td>
                                <td class="fw-bold text-success">
                                    ${{ number_format($cliente->total_gastado, 0) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">
                                    <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                    No hay datos de clientes
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <!-- Ventas por mes -->
            <div class="table-top">
                <h5 class="mb-3">
                    <i class="fas fa-calendar-alt me-2"></i>Ventas por Mes
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Pedidos</th>
                                <th>Ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventas_meses as $mes_venta)
                            <tr>
                                <td>
                                    <i class="far fa-calendar text-muted me-1"></i>
                                    {{ $mes_venta->mes_nombre }}
                                </td>
                                <td>
                                    <span class="badge-pedido">{{ $mes_venta->pedidos }}</span>
                                </td>
                                <td class="fw-bold text-success">
                                    ${{ number_format($mes_venta->ventas, 0) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">
                                    <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                                    No hay datos de ventas mensuales
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    :root {
        --primary: #7fad39;
        --primary-dark: #5a8a20;
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
    
    .stat-card.comisiones {
        border-top-color: #fd7e14;
        background: linear-gradient(135deg, #fff8e1, #ffecb3);
    }
    
    .stat-card.clientes {
        border-top-color: #6f42c1;
        background: linear-gradient(135deg, #f3e5f5, #e1bee7);
    }
    
    .stat-card.promedio {
        border-top-color: #20c997;
        background: linear-gradient(135deg, #e0f2f1, #b2dfdb);
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
    
    .chart-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        height: 350px;
    }
    
    .table-top {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .table-top h5 {
        color: var(--dark);
        font-weight: 600;
    }
    
    .progress-ventas {
        height: 10px;
        border-radius: 5px;
        background: #e9ecef;
        overflow: hidden;
        margin-bottom: 10px;
    }
    
    .progress-bar-ventas {
        background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        height: 100%;
        transition: width 0.3s ease;
    }
    
    .meta-ventas {
        background: linear-gradient(135deg, #fff8e1, #ffecb3);
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .meta-ventas h6 {
        color: #856404;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .badge-pedido {
        background: rgba(127, 173, 57, 0.1);
        color: var(--primary);
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
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
    
    .table th {
        font-weight: 600;
        color: #555;
        border-bottom: 2px solid #eee;
        padding: 10px 12px;
        white-space: nowrap;
    }
    
    .table td {
        vertical-align: middle;
        padding: 10px 12px;
        border-bottom: 1px solid #eee;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(127, 173, 57, 0.05);
    }
    
    @media (max-width: 1200px) {
        .container-fluid { padding: 15px; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .chart-container { height: 300px; padding: 15px; }
        .header-bar { flex-direction: column; text-align: center; }
    }
    
    @media (max-width: 576px) {
        .stat-value { font-size: 1.5rem; }
        .chart-container { height: 250px; }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preparar datos para el gráfico
        const meses = @json($ventas_meses->pluck('mes_nombre')->reverse());
        const ventas = @json($ventas_meses->pluck('ventas')->reverse());
        
        // Configurar gráfico solo si hay datos
        if (meses.length > 0 && ventas.length > 0) {
            const ctx = document.getElementById('ventasChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: meses,
                    datasets: [{
                        label: 'Ventas ($)',
                        data: ventas,
                        borderColor: '#7fad39',
                        backgroundColor: 'rgba(127, 173, 57, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#7fad39',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 12,
                                    family: "'Segoe UI', sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#7fad39',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return `Ventas: $${context.raw.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        } else {
            // Mostrar mensaje si no hay datos
            const canvas = document.getElementById('ventasChart');
            canvas.parentNode.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="fas fa-chart-line fa-3x mb-3 d-block"></i>
                    <h5>No hay datos de ventas para mostrar</h5>
                    <p>No se encontraron ventas registradas en el período seleccionado.</p>
                </div>
            `;
        }
        
        // Exportar reporte
        window.exportarReporte = function() {
            Swal.fire({
                title: 'Exportar Reporte',
                text: '¿En qué formato deseas exportar el reporte?',
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'PDF',
                denyButtonText: 'Excel',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                denyButtonColor: '#198754',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '¡Exportado!',
                        text: 'El reporte en PDF se está generando...',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else if (result.isDenied) {
                    Swal.fire({
                        title: '¡Exportado!',
                        text: 'El reporte en Excel se está generando...',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        };
        
        // Cambiar vista
        document.getElementById('filtroMes').addEventListener('change', function() {
            const tipo = this.value;
            if (tipo === 'diario') {
                window.location.href = '{{ route("vendedor.ventas.index") }}?tipo=diario';
            } else {
                window.location.href = '{{ route("vendedor.ventas.index") }}?tipo=mensual';
            }
        });
    });
</script>
@endsection