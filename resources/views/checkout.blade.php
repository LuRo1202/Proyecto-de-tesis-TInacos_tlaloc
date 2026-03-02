@php
    use App\Helpers\ProductoHelper;
    use App\Helpers\CarritoHelper;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <title>Finalizar Compra | Tanques Tlaloc - {{ $sucursal->nombre }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}">
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <style>
        .cobertura-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            display: none;
        }
        
        .cobertura-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            display: none;
        }
        
        .verificar-cobertura-btn {
            background: linear-gradient(135deg, #7fad39, #5a8c29);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }
        
        .verificar-cobertura-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(127, 173, 57, 0.4);
        }
        
        .verificar-cobertura-btn.loading {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        .verificar-cobertura-btn.loading i {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.5rem;
                text-align: center;
            }
            
            .checkout-summary-box {
                margin-top: 1.5rem;
                order: -1;
            }
            
            .form-control, .form-select {
                font-size: 16px !important;
            }
            
            .btn-lg {
                padding: 0.75rem !important;
                font-size: 1rem !important;
            }
            
            .order-item-img {
                min-width: 50px;
                width: 50px;
                height: 50px;
            }
            
            .order-item-info h6 {
                font-size: 0.85rem;
                margin-bottom: 2px;
            }
            
            .order-item-info small {
                font-size: 0.75rem;
            }
            
            .order-item-price {
                font-size: 0.9rem;
                min-width: 70px;
                text-align: right;
            }
        }
        
        .cobertura-verificada-box {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-left: 5px solid #28a745;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.2);
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .cobertura-verificada-box h5 {
            color: #155724;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .cobertura-verificada-box h5 i {
            background: #28a745;
            color: white;
            padding: 8px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .cobertura-detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            margin-bottom: 8px;
        }
        
        .cobertura-detail-item i {
            font-size: 1.2rem;
            color: #28a745;
            width: 24px;
        }
        
        .cobertura-detail-item strong {
            color: #155724;
            margin-right: 5px;
        }
        
        .cobertura-detail-item span {
            color: #155724;
        }
        
        .sucursal-info-badge {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            border: 1px solid #c3e6cb;
        }
        
        .sucursal-info-badge p {
            margin-bottom: 5px;
            color: #155724;
        }
        
        .sucursal-info-badge i {
            color: #28a745;
            width: 20px;
        }
    </style>
</head>

<body>
    <!-- Header (TU DISEÑO ORIGINAL) -->
<nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" style="height: 50px;">
            </a>

            <div class="d-lg-none d-flex align-items-center ms-auto me-3">
                <a href="{{ route('carrito') }}" class="btn btn-primary position-relative btn-sm">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">{{ $cartCount }}</span> 
                </a>
            </div>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tienda') }}">Tienda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contacto') }}">Contacto</a>
                    </li>
                </ul>
                
                <div class="d-none d-lg-flex align-items-center">
                    {{-- Verificar si hay alguien logueado (admin o cliente) --}}
                    @if(auth('web')->check() || auth('cliente')->check())
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                {{-- Mostrar nombre según quien esté logueado --}}
                                @auth('web')
                                    {{ auth('web')->user()->nombre }}
                                @elseauth('cliente')
                                    {{ auth('cliente')->user()->nombre }}
                                @endauth
                            </button>
                            <ul class="dropdown-menu">
                                @auth('cliente')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('cliente.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>Mi Cuenta
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endauth
                                <li>
                                    <form method="POST" action="{{ auth('web')->check() ? route('logout') : route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-3">
                            <i class="fas fa-user me-2"></i>Login
                        </a>
                    @endauth
                    
                    <a href="{{ route('carrito') }}" class="btn btn-primary position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge">{{ $cartCount ?? 0 }}</span>
                    </a>
                </div>

                <div class="d-lg-none mt-3">
                    @if(auth('web')->check() || auth('cliente')->check())
                        <div class="d-grid gap-2">
                            <span class="btn btn-outline-primary w-100 mb-2 disabled">
                                <i class="fas fa-user me-2"></i>
                                @auth('web')
                                    {{ auth('web')->user()->nombre }}
                                @elseauth('cliente')
                                    {{ auth('cliente')->user()->nombre }}
                                @endauth
                            </span>
                            <form method="POST" action="{{ auth('web')->check() ? route('logout') : route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-user me-2"></i>Iniciar Sesión
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="checkout-hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 order-lg-2 mb-4 mb-lg-0">
                    <div class="checkout-summary-box">
                        <h5><i class="fas fa-shopping-bag me-2"></i>Resumen de Compra</h5>
                        <div class="summary-details">
                            <p><span>Productos:</span> <strong>{{ $productosCarrito->count() }}</strong></p>
                            <p><span>Total:</span> <strong class="text-success">{{ CarritoHelper::formatoPrecio($total) }}</strong></p>
                        </div>
                        <a href="{{ route('carrito') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Carrito
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-8 order-lg-1">
                    <h1 class="hero-title">
                        Finalizar tu Compra<br>
                        <span style="font-size: 1.5rem; display: block; margin-top: 10px;">Verificamos cobertura antes del pago</span>
                    </h1>
                    
                    <div class="checkout-steps">
                        <div class="step completed">
                            <div class="step-number">1</div>
                            <div class="step-text">Carrito</div>
                        </div>
                        <div class="step-line d-none d-md-block"></div>
                        <div class="step active">
                            <div class="step-number">2</div>
                            <div class="step-text">Datos y Cobertura</div>
                        </div>
                        <div class="step-line d-none d-md-block"></div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-text">Pago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Form Section -->
    <section class="checkout-form-section py-4 py-lg-5">
        <div class="container">
            <div class="row g-4">
                <!-- Formulario principal -->
                <div class="col-lg-8">
                    <div class="checkout-form-card mb-4">
                        <h4 class="checkout-title">
                            <i class="fas fa-truck me-2"></i>Información de Envío y Cobertura
                        </h4>
                        
                     
                        
                        <!-- ✅ MENSAJES DE COBERTURA -->
                        <div id="cobertura-success" class="cobertura-success" style="display: none;">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle fs-1 me-3" style="color: #28a745;"></i>
                                <div>
                                    <h5 class="mb-1">✅ ¡Cobertura confirmada!</h5>
                                    <p class="mb-0">Tu dirección está dentro de nuestra zona de entrega</p>
                                </div>
                            </div>
                            <div class="sucursal-info-badge">
                                <p><i class="fas fa-store me-2"></i><strong>Sucursal:</strong> <span id="success-sucursal-nombre"></span></p>
                                <p><i class="fas fa-location-dot me-2"></i><strong>Dirección:</strong> <span id="success-sucursal-direccion"></span></p>
                                <p><i class="fas fa-road me-2"></i><strong>Distancia:</strong> <span id="success-distancia"></span></p>
                            </div>
                        </div>
                        
                        <div id="cobertura-error" class="cobertura-error" style="display: none;">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-times-circle fs-1 me-3" style="color: #dc3545;"></i>
                                <div>
                                    <h5 class="mb-1">❌ Sin cobertura</h5>
                                    <p class="mb-0"><span id="error-message"></span></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ✅ Mostrar cobertura guardada en sesión -->
                        @if(session('cobertura_verificada') && session('cobertura_verificada.valido'))
                        <div class="cobertura-verificada-box" id="cobertura-session-box">
                            <h5>
                                <i class="fas fa-check-circle"></i>
                                Cobertura verificada
                            </h5>
                            
                            <div class="cobertura-detail-item">
                                <i class="fas fa-store"></i>
                                <div>
                                    <strong>Sucursal:</strong> 
                                    <span>{{ session('cobertura_verificada.sucursal_nombre') }}</span>
                                </div>
                            </div>
                            
                            <div class="cobertura-detail-item">
                                <i class="fas fa-location-dot"></i>
                                <div>
                                    <strong>Dirección sucursal:</strong> 
                                    <span>{{ session('cobertura_verificada.sucursal_direccion') }}</span>
                                </div>
                            </div>
                            
                            <div class="cobertura-detail-item">
                                <i class="fas fa-road"></i>
                                <div>
                                    <strong>Distancia:</strong> 
                                    <span>{{ session('cobertura_verificada.distancia') }} km</span>
                                </div>
                            </div>
                            
                            <div class="cobertura-detail-item">
                                <i class="fas fa-map-pin"></i>
                                <div>
                                    <strong>Tu dirección:</strong> 
                                    <span>{{ session('cobertura_verificada.direccion_cliente') }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-sm btn-outline-success" id="cambiar-direccion">
                                    <i class="fas fa-pen me-1"></i>Cambiar dirección
                                </button>
                            </div>
                        </div>
                        @endif
                        
                        <form method="POST" action="{{ route('cliente.checkout.procesar') }}" id="form-checkout">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre" class="form-label">
                                            <i class="fas fa-user me-2"></i>Nombre completo *
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('nombre') is-invalid @enderror" 
                                               id="nombre" 
                                               name="nombre" 
                                               required 
                                               placeholder="Ej: Jose Pérez"
                                               value="{{ old('nombre', $cliente->nombre) }}">
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono" class="form-label">
                                            <i class="fas fa-phone me-2"></i>Teléfono *
                                        </label>
                                        <input type="tel" 
                                               class="form-control @error('telefono') is-invalid @enderror" 
                                               id="telefono" 
                                               name="telefono" 
                                               required 
                                               placeholder="55 1234 5678"
                                               pattern="[0-9\s]{10,15}"
                                               inputmode="tel"
                                               value="{{ old('telefono', $cliente->telefono) }}">
                                        @error('telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="direccion" class="form-label">
                                            <i class="fas fa-map-marker-alt me-2"></i>Dirección completa *
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('direccion') is-invalid @enderror" 
                                               id="direccion" 
                                               name="direccion" 
                                               required 
                                               placeholder="Calle, número, colonia"
                                               value="{{ old('direccion', session('cobertura_verificada.direccion_cliente') ?? $cliente->direccion) }}">
                                        @error('direccion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ciudad" class="form-label">
                                            <i class="fas fa-city me-2"></i>Ciudad *
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('ciudad') is-invalid @enderror" 
                                               id="ciudad" 
                                               name="ciudad" 
                                               required 
                                               placeholder="Ej: Ecatepec de Morelos"
                                               value="{{ old('ciudad', session('cobertura_verificada.ciudad') ?? $cliente->ciudad) }}">
                                        @error('ciudad')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado" class="form-label">
                                            <i class="fas fa-flag me-2"></i>Estado *
                                        </label>
                                        <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                            <option value="Estado de México" {{ old('estado', session('cobertura_verificada.estado') ?? $cliente->estado) == 'Estado de México' ? 'selected' : '' }}>Estado de México</option>
                                            <option value="Ciudad de México" {{ old('estado', session('cobertura_verificada.estado') ?? $cliente->estado) == 'Ciudad de México' ? 'selected' : '' }}>Ciudad de México</option>
                                            <option value="CDMX" {{ old('estado', session('cobertura_verificada.estado') ?? $cliente->estado) == 'CDMX' ? 'selected' : '' }}>Ciudad de México</option>
                                            <option value="San Luis Potosí" {{ old('estado', session('cobertura_verificada.estado') ?? $cliente->estado) == 'San Luis Potosí' ? 'selected' : '' }}>San Luis Potosí</option>
                                            <option value="Nuevo León" {{ old('estado', session('cobertura_verificada.estado') ?? $cliente->estado) == 'Nuevo León' ? 'selected' : '' }}>Nuevo León</option>
                                        
                                        </select>
                                        @error('estado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="codigo_postal" class="form-label">
                                            <i class="fas fa-mail-bulk me-2"></i>Código Postal *
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('codigo_postal') is-invalid @enderror" 
                                               id="codigo_postal" 
                                               name="codigo_postal" 
                                               required 
                                               placeholder="Ej: 55000"
                                               pattern="[0-9]{5}"
                                               maxlength="5"
                                               inputmode="numeric"
                                               value="{{ old('codigo_postal', session('cobertura_verificada.codigo_postal') ?? $cliente->codigo_postal) }}">
                                        @error('codigo_postal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="d-grid mt-2">
                                        <button type="button" id="verificar-cobertura" class="verificar-cobertura-btn">
                                            <i class="fas fa-search-location me-2"></i>Verificar Cobertura de Envío
                                        </button>
                                        <small class="text-muted mt-2 text-center">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Verificamos si entregamos en tu zona (radio de {{ $sucursal->radio_cobertura_km ?? 8 }}km de nuestras sucursales)
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-3">
                                    <div class="form-group">
                                        <label for="notas" class="form-label">
                                            <i class="fas fa-sticky-note me-2"></i>Notas adicionales (opcional)
                                        </label>
                                        <textarea class="form-control" 
                                                  id="notas" 
                                                  name="notas" 
                                                  rows="3" 
                                                  placeholder="Instrucciones especiales para la entrega, referencias, etc.">{{ old('notas') }}</textarea>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <div class="checkout-terms-card">
                        <h6><i class="fas fa-shield-alt me-2"></i>Términos y Condiciones</h6>
                        <div class="terms-content">
                            <p><strong>Cobertura de envío:</strong></p>
                            <ul>
                                <li>Entregamos dentro de un radio de {{ $sucursal->radio_cobertura_km ?? 8 }}km de nuestras sucursales</li>
                                <li>Horario de entregas: Lunes a Viernes 9:00 - 18:00 hrs</li>
                                <li>Tiempo de entrega: 3-5 días hábiles después de confirmación de pago</li>
                            </ul>
                            <p><strong>Política de pagos:</strong></p>
                            <ul>
                                <li>Pago 100% seguro en línea</li>
                                <li>Facturación electrónica incluida</li>
                                <li>En caso de no cobertura, se reembolsará el pago</li>
                            </ul>
                            <div class="form-check mt-3">
                                <input class="form-check-input @error('aceptoTerminos') is-invalid @enderror" 
                                       type="checkbox" 
                                       id="aceptoTerminos" 
                                       name="aceptoTerminos" 
                                       required 
                                       {{ old('aceptoTerminos') || session('cobertura_verificada') ? 'checked' : '' }}>
                                <label class="form-check-label" for="aceptoTerminos">
                                    He leído y acepto los términos y condiciones *
                                </label>
                                @error('aceptoTerminos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
                
                <!-- Resumen del pedido -->
                <div class="col-lg-4">
                    <div class="order-summary-card">
                        <h4 class="order-title">
                            <i class="fas fa-clipboard-list me-2"></i>Detalles del Pedido
                        </h4>
                        
                        <div class="order-items">
                            @foreach($productosCarrito as $item)
                            <div class="order-item">
                                <div class="order-item-img">
                                    <img src="{{ ProductoHelper::obtenerImagenProducto($item['codigo']) }}" 
                                         alt="{{ $item['nombre'] }}"
                                         loading="lazy">
                                </div>
                                <div class="order-item-info">
                                    <h6>{{ $item['nombre'] }}</h6>
                                    <small>Código: {{ $item['codigo'] }} | {{ $item['litros'] }}L</small>
                                    <p class="mb-0">Cantidad: {{ $item['cantidad'] }}</p>
                                </div>
                                <div class="order-item-price">
                                    {{ CarritoHelper::formatoPrecio($item['subtotal']) }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="order-totals">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span>{{ CarritoHelper::formatoPrecio($total) }}</span>
                            </div>
                            <div class="total-row">
                                <span>Envío:</span>
                                <span class="text-success">Gratis</span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Total a pagar:</span>
                                <span class="text-success fw-bold">{{ CarritoHelper::formatoPrecio($total) }}</span>
                            </div>
                        </div>
                        
                        <div class="order-actions mt-4">
                            <button type="submit" form="form-checkout" class="btn btn-primary btn-lg w-100" id="btn-finalizar" {{ !session('cobertura_verificada') ? 'disabled' : '' }}>
                                <i class="fas fa-credit-card me-2"></i>Continuar al Pago
                            </button>
                            <p class="text-center mt-3 small text-muted mb-0">
                                <i class="fas fa-lock me-1"></i>
                                @if(session('cobertura_verificada'))
                                    Cobertura verificada - Puedes continuar
                                @else
                                    Verifica cobertura para continuar
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand mb-4">
                        <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" style="height: 50px;">
                        <h4 class="mt-3 mb-2">Tanques Tlaloc - {{ $sucursal->nombre }}</h4>
                        <p class="mb-0">{{ $sucursal->direccion ?? 'Ecatepec, Estado de México' }}</p>
                    </div>
                    
                    <div class="footer-contact">
                        <h6 class="mb-3">Contacto Directo</h6>
                        <p class="mb-2">
                            <i class="fas fa-phone me-2"></i>{{ $sucursal->telefono ?? '55 4017 5803' }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:{{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}" class="text-white">
                                {{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}
                            </a>
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="footer-links">
                        <h6 class="mb-3">Productos</h6>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a></li>
                            <li><a href="{{ route('tienda', ['categoria' => 1]) }}">Tinacos Tradicionales</a></li>
                            <li><a href="{{ route('tienda', ['categoria' => 3]) }}">Cisternas</a></li>
                            <li><a href="{{ route('tienda', ['categoria' => 4]) }}">Accesorios</a></li>
                            <li><a href="{{ route('tienda') }}">Catálogo Completo</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="footer-links">
                        <h6 class="mb-3">Empresa</h6>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('home') }}">Inicio</a></li>
                            <li><a href="{{ route('tienda') }}">Tienda</a></li>
                            <li><a href="{{ route('contacto') }}">Contacto</a></li>
                        </ul>
                        
                        <h6 class="mt-4 mb-3">Síguenos</h6>
                        <div class="social-icons">
                            <a href="#" class="social-icon facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-icon whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="#" class="social-icon phone">
                                <i class="fas fa-phone"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5 pt-4 border-top border-secondary">
                <div class="col-lg-8">
                    <p class="mb-2">
                        <strong>Tanques Tlaloc</strong> - Creadores del Tinaco Bala • Empresa 100% Mexicana
                    </p>
                    <p class="mb-0">
                        Especialistas en ROTOMOLDEO con más de 20 años de experiencia
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <p class="mb-0">&copy; {{ date('Y') }} Tanques Tlaloc. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.coberturaData = @json(session('cobertura_verificada'));
    </script>

    <script src="{{ asset('assets/js/checkout_maps.js') }}"></script>
        <!-- Mensaje de bienvenida con SweetAlert (se ejecutará al cargar la página) -->
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'success',
                title: '¡Hola {{ $cliente->nombre }}!',
                text: 'Estás comprando como: {{ $cliente->email }}',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: '#7fad39',
                color: 'white'
            });
        });
    </script>
    
    @if(session('swal'))
    <script>
        Swal.fire({
            icon: '{{ session('swal')['type'] }}',
            title: '{{ session('swal')['title'] }}',
            text: '{{ session('swal')['message'] }}',
            confirmButtonColor: '#7fad39',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: true
        });
    </script>
    @endif
</body>
</html>