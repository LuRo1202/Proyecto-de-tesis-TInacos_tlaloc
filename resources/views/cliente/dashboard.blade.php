{{-- resources/views/cliente/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta | {{ $cliente->nombre }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .welcome-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .avatar-circle {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 5px solid rgba(255,255,255,0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .avatar-circle i {
            font-size: 60px;
            color: #667eea;
        }
        .info-panel {
            padding: 40px;
        }
        .info-item {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
            transition: transform 0.3s;
        }
        .info-item:hover {
            transform: translateX(10px);
        }
        .info-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
        }
        .btn-custom {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            margin: 5px;
        }
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .btn-logout {
            background: #f56565;
            color: white;
            border: none;
        }
        .btn-logout:hover {
            background: #c53030;
            color: white;
        }
        .btn-store {
            background: #667eea;
            color: white;
            border: none;
        }
        .btn-store:hover {
            background: #5a67d8;
            color: white;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-box {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: 1px solid #edf2f7;
        }
        .stat-icon {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 15px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1.2;
        }
        .stat-label {
            color: #718096;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="dashboard-card">
            <!-- Header con avatar -->
            <div class="welcome-header">
                <div class="avatar-circle">
                    <i class="fas fa-user"></i>
                </div>
                <h2 class="fw-bold mb-2">{{ $cliente->nombre }}</h2>
                <p class="mb-0 opacity-75">Cliente registrado</p>
                
                <!-- Botón logout en el header -->
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-light px-4 py-2 rounded-pill">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </button>
                </form>
            </div>
            
            <!-- Panel de información -->
            <div class="info-panel">
                <h3 class="text-center mb-4 fw-bold">
                    <i class="fas fa-user-circle me-2 text-primary"></i>
                    Datos de tu cuenta
                </h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-user me-2"></i>Nombre completo
                            </div>
                            <div class="info-value">{{ $cliente->nombre }}</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-envelope me-2"></i>Correo electrónico
                            </div>
                            <div class="info-value">{{ $cliente->email }}</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-phone me-2"></i>Teléfono
                            </div>
                            <div class="info-value">{{ $cliente->telefono ?? 'No registrado' }}</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-map-marker-alt me-2"></i>Dirección
                            </div>
                            <div class="info-value">{{ $cliente->direccion ?? 'No registrada' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Mensaje de bienvenida -->
                <div class="alert alert-primary text-center mt-4" role="alert">
                    <i class="fas fa-smile me-2"></i>
                    ¡Bienvenido a tu panel de control! Aquí podrás ver tus pedidos y gestionar tu cuenta.
                </div>
                
                <!-- Botones de acción -->
                <div class="text-center mt-4">
                    <a href="{{ route('tienda') }}" class="btn btn-store btn-custom">
                        <i class="fas fa-store me-2"></i>Ir a la Tienda
                    </a>
                    
                    <a href="{{ route('carrito') }}" class="btn btn-success btn-custom">
                        <i class="fas fa-shopping-cart me-2"></i>Ver Carrito
                    </a>
                </div>
                
                <!-- Stats con datos reales del controlador -->
                <div class="stats-grid mt-5">
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-number">{{ $pedidos->count() }}</div>
                        <div class="stat-label">Pedidos totales</div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number">{{ $pedidos->where('estado', 'pendiente')->count() }}</div>
                        <div class="stat-label">Pedidos pendientes</div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <div class="stat-number">{{ $pedidos->where('estado', 'entregado')->count() }}</div>
                        <div class="stat-label">Entregados</div>
                    </div>
                </div>
                
                <!-- Total gastado -->
                <div class="text-center mt-4 p-3 bg-light rounded">
                    <h5>Total gastado: <span class="text-success fw-bold">${{ number_format($totalGastado, 2) }}</span></h5>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>