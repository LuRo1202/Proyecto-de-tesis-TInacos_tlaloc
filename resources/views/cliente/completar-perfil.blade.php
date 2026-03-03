{{-- resources/views/cliente/completar-perfil.blade.php --}}
@php
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mi Perfil | Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
    
    <!-- CSS Personalizado (TODOS LOS ESTILOS GLOBALES) -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <style>
        /* ===== SOLO ESTILOS EXCLUSIVOS DE LA PÁGINA DE PERFIL ===== */
        
        :root {
            --verde-principal: #7fad39;
            --verde-oscuro: #5d8c29;
            --verde-suave: #f2f8eb;
            --naranja: #ff6600;
            --naranja-oscuro: #e55a00;
            --rojo: #ff0000;
            --gris-fondo: #f5f5f5;
            --gris-borde: #ebebeb;
            --texto-principal: #333;
            --texto-secundario: #666;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--gris-fondo);
            color: var(--texto-principal);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== CONTENIDO PRINCIPAL ===== */
        .main-content {
            flex: 1;
            padding: 30px 0;
        }

        .perfil-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ===== PAGE HEADER ===== */
        .page-header {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border-left: 5px solid var(--verde-principal);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 5px 15px rgba(127, 173, 57, 0.3);
        }

        .header-text h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--texto-principal);
            margin-bottom: 5px;
        }

        .header-text p {
            color: var(--texto-secundario);
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        /* ===== BOTONES ===== */
        .btn-custom {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--verde-principal), var(--verde-oscuro));
            color: white;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, var(--verde-oscuro), #4a7a18);
        }

        .btn-secondary-custom {
            background: white;
            color: var(--texto-principal);
            border: 1px solid var(--gris-borde);
        }

        .btn-secondary-custom:hover {
            background: var(--gris-fondo);
        }

        /* ===== FORMULARIO ===== */
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gris-borde);
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--texto-principal);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--verde-principal);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: var(--texto-principal);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-label i {
            color: var(--verde-principal);
            width: 20px;
        }

        .form-control {
            border: 1px solid var(--gris-borde);
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s;
            width: 100%;
        }

        .form-control:focus {
            border-color: var(--verde-principal);
            box-shadow: 0 0 0 3px rgba(127, 173, 57, 0.1);
            outline: none;
        }

        .form-control.is-valid {
            border-color: var(--verde-principal);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%237fad39' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-control.is-invalid {
            border-color: var(--rojo);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        }

        .invalid-feedback {
            color: var(--rojo);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        /* ===== INFO BOX ===== */
        .info-box {
            background: var(--verde-suave);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid var(--verde-principal);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .info-box i {
            font-size: 1.5rem;
            color: var(--verde-principal);
        }

        .info-box span {
            color: var(--texto-principal);
            line-height: 1.5;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .header-title {
                flex-direction: column;
                text-align: center;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .header-actions {
                justify-content: center;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-custom {
                width: 100%;
                justify-content: center;
            }

            .info-box {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .perfil-container {
                padding: 0 15px;
            }

            .form-card {
                padding: 20px;
            }
        }

        /* ===== BUSCADOR SUPERIOR - MI CUENTA TLÁLOC ===== */
        .top-search-section {
            background: linear-gradient(135deg, #7fad39 0%, #5d8c29 100%);
            padding: 25px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .top-search-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(45deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.08) 50%, 
                transparent 100%);
            animation: shine 3s infinite linear;
        }

        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .top-search-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.6) 50%, 
                transparent 100%);
        }

        .top-search-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            color: white;
            position: relative;
            z-index: 1;
            padding: 0 15px;
        }

        .top-search-container h4 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .top-search-container h4 i {
            color: #ffdd40;
            font-size: 2rem;
            filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.3));
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .top-search-container p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.2rem;
            margin-bottom: 0;
            font-weight: 400;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .top-search-section {
                padding: 20px 0;
            }
            
            .top-search-container h4 {
                font-size: 1.6rem;
            }
            
            .top-search-container h4 i {
                font-size: 1.6rem;
            }
            
            .top-search-container p {
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .top-search-section {
                padding: 15px 0;
            }
            
            .top-search-container h4 {
                font-size: 1.4rem;
                flex-direction: column;
                gap: 5px;
            }
            
            .top-search-container h4 i {
                font-size: 1.4rem;
            }
            
            .top-search-container p {
                font-size: 0.95rem;
                padding: 0 10px;
            }
        }

        .top-search-section {
            animation: fadeInDown 0.6s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <!-- ===== HEADER ===== -->
    <nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tlaloc">
            </a>
            
            <div class="d-flex align-items-center">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
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
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('cliente.dashboard') }}">Mi Cuenta</a>
                    </li>
                </ul>
                
                <div class="d-none d-lg-flex align-items-center">
                    @if(auth('cliente')->check())
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                {{ auth('cliente')->user()->nombre }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item active" href="{{ route('cliente.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Mi Cuenta
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                        </button>
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
                    @if(auth('cliente')->check())
                        <div class="d-grid gap-2">
                            <span class="btn btn-outline-primary w-100 mb-2 disabled">
                                <i class="fas fa-user me-2"></i>
                                {{ auth('cliente')->user()->nombre }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}">
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

    <!-- ===== BUSCADOR SUPERIOR ===== -->
    <section class="top-search-section">
        <div class="container">
            <div class="top-search-container">
                <h4>
                    <i class="fas fa-user-circle me-2"></i>Mi Perfil
                </h4>
                <p>Actualiza tus datos personales y dirección</p>
            </div>
        </div>
    </section>

    <!-- ===== CONTENIDO PRINCIPAL ===== -->
    <main class="main-content">
        <div class="perfil-container">

            <!-- Page Header -->
            <div class="page-header">
                <div class="header-title">
                    <div class="header-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="header-text">
                        <h1>Mi Perfil</h1>
                        <p>Completa y actualiza tu información</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('cliente.dashboard') }}" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <div class="form-card">
                <form method="POST" action="{{ url('/cliente/actualizar-direccion') }}" id="formPerfil">
                    @csrf
                    
                    <!-- Información Personal -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-user"></i>
                            Información Personal
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i>
                                Nombre completo
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $cliente->nombre }}" 
                                   readonly
                                   disabled>
                            <small class="text-muted">El nombre no se puede modificar</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i>
                                Correo electrónico
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   value="{{ $cliente->email }}" 
                                   readonly
                                   disabled>
                            <small class="text-muted">El correo no se puede modificar</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone"></i>
                                Teléfono
                            </label>
                            <input type="tel" 
                                   name="telefono" 
                                   class="form-control @error('telefono') is-invalid @enderror" 
                                   value="{{ old('telefono', $cliente->telefono) }}"
                                   placeholder="55 1234 5678"
                                   maxlength="10"
                                   pattern="[0-9]{10}">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">10 dígitos, solo números</small>
                        </div>
                    </div>

                    <!-- Dirección de Envío -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-map-marker-alt"></i>
                            Dirección de Envío
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-road"></i>
                                Calle y número
                            </label>
                            <input type="text" 
                                   name="direccion" 
                                   id="direccion"
                                   class="form-control @error('direccion') is-invalid @enderror" 
                                   value="{{ old('direccion', $cliente->direccion) }}"
                                   placeholder="Av. Morelos #123, Col. Centro"
                                   required
                                   autocomplete="off">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Escribe tu dirección y selecciona una opción</small>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-city"></i>
                                    Ciudad
                                </label>
                                <input type="text" 
                                       name="ciudad" 
                                       id="ciudad"
                                       class="form-control @error('ciudad') is-invalid @enderror" 
                                       value="{{ old('ciudad', $cliente->ciudad) }}"
                                       placeholder="Ecatepec"
                                       required
                                       readonly>
                                @error('ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-pin"></i>
                                    Estado
                                </label>
                                <input type="text" 
                                       name="estado" 
                                       id="estado"
                                       class="form-control @error('estado') is-invalid @enderror" 
                                       value="{{ old('estado', $cliente->estado) }}"
                                       placeholder="Estado de México"
                                       required
                                       readonly>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-mail-bulk"></i>
                                Código Postal
                            </label>
                            <input type="text" 
                                   name="codigo_postal" 
                                   id="codigo_postal"
                                   class="form-control @error('codigo_postal') is-invalid @enderror" 
                                   value="{{ old('codigo_postal', $cliente->codigo_postal) }}"
                                   placeholder="55000"
                                   maxlength="5"
                                   pattern="[0-9]{5}"
                                   required
                                   readonly>
                            @error('codigo_postal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">5 dígitos</small>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <span>Los datos de contacto se usarán para confirmar tus pedidos y enviar información importante.</span>
                    </div>

                    <!-- Botones -->
                    <div class="form-actions">
                        <a href="{{ route('cliente.dashboard') }}" class="btn-custom btn-secondary-custom">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-custom btn-primary-custom" id="btnGuardar">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="main-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="footer-brand">
                        <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tlaloc">
                        <h5>Tanques Tlaloc</h5>
                        <p>Especialistas en ROTOMOLDEO con más de 20 años de experiencia. Creadores del Tinaco Bala.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h6>Enlaces Rápidos</h6>
                        <ul>
                            <li><a href="{{ route('tienda') }}"><i class="fas fa-chevron-right"></i> Tienda</a></li>
                            <li><a href="{{ route('cliente.pedidos') }}"><i class="fas fa-chevron-right"></i> Mis Pedidos</a></li>
                            <li><a href="{{ route('cliente.dashboard') }}"><i class="fas fa-chevron-right"></i> Dashboard</a></li>
                            <li><a href="{{ route('contacto') }}"><i class="fas fa-chevron-right"></i> Contacto</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="footer-links">
                        <h6>Ayuda y Soporte</h6>
                        <ul>
                            <li><a href="{{ route('contacto') }}"><i class="fas fa-chevron-right"></i> Centro de Ayuda</a></li>
                            <li><a href="#" onclick="contactarWhatsApp(event)"><i class="fab fa-whatsapp"></i> WhatsApp</a></li>
                            <li><a href="mailto:soporte@tlaloc.com"><i class="fas fa-envelope"></i> soporte@tlaloc.com</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Tanques Tlaloc. Todos los derechos reservados. Empresa 100% Mexicana.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            console.log('✅ Perfil con autocompletado cargado');

            // ===== VARIABLES =====
            const $telefono = $('input[name="telefono"]');
            const $codigoPostal = $('#codigo_postal');
            const $direccion = $('#direccion');
            const $ciudad = $('#ciudad');
            const $estado = $('#estado');
            const $form = $('#formPerfil');

            // ===== AUTOCOMPLETADO DE GOOGLE MAPS =====
            function initGoogleAutocomplete() {
                const input = document.getElementById('direccion');
                if (!input) return;

                if (typeof google === 'undefined' || !google.maps) {
                    console.warn('⏳ Google Maps no está listo, reintentando en 1s...');
                    setTimeout(initGoogleAutocomplete, 1000);
                    return;
                }

                try {
                    const autocomplete = new google.maps.places.Autocomplete(input, {
                        types: ['address'],
                        componentRestrictions: { country: 'mx' },
                        fields: ['address_components', 'formatted_address']
                    });

                    autocomplete.addListener('place_changed', function() {
                        const place = autocomplete.getPlace();
                        
                        if (!place.address_components) return;

                        let ciudad = '';
                        let estado = '';
                        let cp = '';

                        place.address_components.forEach(component => {
                            const types = component.types;
                            
                            if (types.includes('locality') || types.includes('sublocality')) {
                                ciudad = component.long_name;
                            }
                            if (types.includes('administrative_area_level_1')) {
                                estado = component.long_name;
                            }
                            if (types.includes('postal_code')) {
                                cp = component.long_name;
                            }
                        });

                        if (ciudad) {
                            $ciudad.val(ciudad);
                            $ciudad.removeClass('is-invalid');
                        }
                        if (estado) {
                            $estado.val(estado);
                            $estado.removeClass('is-invalid');
                        }
                        if (cp) {
                            $codigoPostal.val(cp);
                            $codigoPostal.removeClass('is-invalid');
                        }

                        Swal.fire({
                            icon: 'success',
                            title: '¡Dirección completada!',
                            text: 'Los datos de ciudad, estado y código postal se han llenado automáticamente.',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    });

                    console.log('✅ Autocompletado de Google inicializado correctamente');
                } catch (error) {
                    console.error('❌ Error al inicializar autocomplete:', error);
                }
            }

            // ===== VALIDACIONES EN TIEMPO REAL =====
            $telefono.on('input', function() {
                let valor = $(this).val();
                let soloNumeros = valor.replace(/\D/g, '');
                if (soloNumeros.length > 10) {
                    soloNumeros = soloNumeros.substring(0, 10);
                }
                $(this).val(soloNumeros);
            });

            $codigoPostal.on('input', function() {
                let valor = $(this).val();
                let soloNumeros = valor.replace(/\D/g, '');
                if (soloNumeros.length > 5) {
                    soloNumeros = soloNumeros.substring(0, 5);
                }
                $(this).val(soloNumeros);
            });

            $direccion.on('input', function() {
                let valor = $(this).val();
                let direccionValida = valor.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,#\-]/g, '');
                $(this).val(direccionValida);
                
                // Cuando el usuario empieza a escribir, habilitar los campos de nuevo
                $ciudad.prop('readonly', false);
                $estado.prop('readonly', false);
                $codigoPostal.prop('readonly', false);
            });

            $ciudad.on('input', function() {
                let valor = $(this).val();
                let soloLetras = valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                $(this).val(soloLetras);
            });

            $estado.on('input', function() {
                let valor = $(this).val();
                let soloLetras = valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                $(this).val(soloLetras);
            });

            // ===== VALIDACIÓN AL ENVIAR =====
            $form.on('submit', function(e) {
                const telefono = $telefono.val();
                const cp = $codigoPostal.val();
                const direccion = $direccion.val();
                const ciudad = $ciudad.val();
                const estado = $estado.val();
                
                let isValid = true;

                // Validar teléfono
                if (telefono && !/^\d{10}$/.test(telefono)) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El teléfono debe tener 10 dígitos'
                    });
                    isValid = false;
                    return false;
                }
                
                // Validar código postal
                if (!/^\d{5}$/.test(cp)) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El código postal debe tener 5 dígitos'
                    });
                    isValid = false;
                    return false;
                }

                // Validar campos requeridos
                if (!direccion || !ciudad || !estado) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Campos incompletos',
                        text: 'Por favor completa todos los campos de dirección'
                    });
                    isValid = false;
                    return false;
                }

                if (isValid) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardando cambios!',
                        text: 'Tus datos se están actualizando...',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });

            // ===== FUNCIONES ADICIONALES =====
            function contactarWhatsApp(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Contactar por WhatsApp',
                    text: '¿En qué podemos ayudarte?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#25D366',
                    confirmButtonText: '<i class="fab fa-whatsapp me-2"></i>Ir a WhatsApp',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open('https://wa.me/5215540175803', '_blank');
                    }
                });
            }

            // Exponer función para el onclick
            window.contactarWhatsApp = contactarWhatsApp;

            // ===== INICIALIZAR =====
            initGoogleAutocomplete();

            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Excelente!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
            @endif
        });
    </script>
</body>
</html>