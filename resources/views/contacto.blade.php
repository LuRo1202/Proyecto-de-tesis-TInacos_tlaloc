<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | Tanques Tlaloc - {{ $sucursal->nombre }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/contacto.css') }}">
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <!-- ===== HEADER ===== -->
    <nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" class="img-fluid" style="max-height: 45px;">
            </a>
            
            <!-- Botón carrito móvil -->
            <div class="d-lg-none d-flex align-items-center ms-auto me-3">
                <a href="{{ route('carrito') }}" class="btn btn-primary position-relative btn-sm">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">{{ $cartCount ?? 0 }}</span> 
                </a>
            </div>
            
            <!-- Botón hamburguesa -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tienda') }}">Tienda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('contacto') }}">Contacto</a>
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
                
                <!-- Botones móvil -->
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


    <!-- ===== HERO SECTION ===== -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <h1 class="hero-title animate__animated animate__fadeInDown">
                        ¿Necesitas asesoría especializada?<br>
                        <span style="font-size: 2rem; display: block; margin-top: 10px;">Estamos para ayudarte</span>
                    </h1>
                    
                    <h2 class="hero-subtitle animate__animated animate__fadeInUp">
                        Contáctanos - Tanques Tláloc
                    </h2>
                    
                    <div class="company-info mb-4">
                        <p>
                            <i class="fas fa-flag"></i>
                            <strong>Empresa 100% Mexicana</strong>
                        </p>
                        <p>
                            <i class="fas fa-headset"></i>
                            <strong>Atención personalizada</strong>
                        </p>
                        <p>
                            <i class="fas fa-truck"></i>
                            <strong>Asesoría en instalación</strong>
                        </p>
                        <p>
                            <i class="fas fa-users"></i>
                            <strong>Servicio a distribuidores</strong>
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="contact-hero-box">
                        <h5>
                            <i class="fas fa-phone-alt me-2"></i>Contacto Directo
                        </h5>
                        
                        <div class="phone-list">
                            <p>
                                <i class="fas fa-phone"></i>
                                <strong>{{ $sucursal->telefono ?? '55 4017 5803' }}</strong>
                            </p>
                            <p>
                                <i class="fas fa-phone"></i>
                                <strong>444 184 4270</strong>
                            </p>
                            <p>
                                <i class="fas fa-phone"></i>
                                <strong>81 8654 0464</strong>
                            </p>
                        </div>
                        
                        <a href="https://wa.me/5215540175803" 
                           target="_blank" 
                           class="whatsapp-btn">
                            <i class="fab fa-whatsapp"></i>
                            WhatsApp inmediato
                        </a>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-envelope me-1"></i>
                                Email: 
                                <a href="mailto:{{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}" class="text-primary">
                                    {{ $sucursal->email ?? 'tanquestlaloc@outlook.com' }}
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
    <div class="container mt-4">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endif

    <!-- SECCIÓN DE SUCURSALES CON MAPAS -->
    <section class="contact-sucursales py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-12 text-center">
                    <h2 class="section-title">Nuestras Sucursales</h2>
                    <p class="section-subtitle">Visítanos en cualquiera de nuestras ubicaciones</p>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Matriz Estado de México -->
                <div class="col-lg-4 col-md-6">
                    <div class="sucursal-card">
                        <div class="sucursal-header">
                            <div class="sucursal-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h4>Matriz Estado de México</h4>
                            <p class="sucursal-badge matriz">MATRIZ</p>
                        </div>
                        
                        <div class="sucursal-info">
                            <p><i class="fas fa-phone me-2"></i><strong>Teléfono:</strong> 55 4017 5803</p>
                            <p><i class="fas fa-map-marker-alt me-2"></i><strong>Ubicación:</strong> Av Morelos 186a, San Cristóbal Centro, 55000 Ecatepec de Morelos, Méx.</p>
                            <p><i class="fas fa-clock me-2"></i><strong>Horario:</strong> Lunes a Viernes 9:00 - 18:00</p>
                        </div>
                        
                        <div class="sucursal-mapa">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15034.895173145875!2d-99.0390942!3d19.5963305!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1f1add0886f97%3A0xa1f81173c822f26c!2sTanques%20Tl%C3%A1loc!5e0!3m2!1ses!2smx!4v1712167669350!5m2!1ses!2smx" 
                                width="100%" 
                                height="250" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                        
                        <div class="sucursal-actions">
                            <a href="https://goo.gl/maps/example1" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-directions me-1"></i> Cómo llegar
                            </a>
                            <a href="tel:5540175803" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-phone me-1"></i> Llamar
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Sucursal San Luis Potosí -->
                <div class="col-lg-4 col-md-6">
                    <div class="sucursal-card">
                        <div class="sucursal-header">
                            <div class="sucursal-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <h4>Sucursal San Luis Potosí</h4>
                            <p class="sucursal-badge sucursal">SUCURSAL</p>
                        </div>
                        
                        <div class="sucursal-info">
                            <p><i class="fas fa-phone me-2"></i><strong>Teléfono:</strong> 444 184 4270</p>
                            <p><i class="fas fa-map-marker-alt me-2"></i><strong>Ubicación:</strong> Francisco I. Madero #492 A Soledad de Graciano Sánchez, 78437 San Luis Potosí, S.L.P.</p>
                            <p><i class="fas fa-clock me-2"></i><strong>Horario:</strong> Lunes a Viernes 9:00 - 18:00</p>
                        </div>
                        
                        <div class="sucursal-mapa">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3695.269305789966!2d-100.92841419999999!3d22.153818599999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x842aa14fa6b7df67%3A0x254b7c880c11a2dd!2sTanques%20Tl%C3%A1loc!5e0!3m2!1ses!2smx!4v1712167944439!5m2!1ses!2smx" 
                                width="100%" 
                                height="250" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                        
                        <div class="sucursal-actions">
                            <a href="https://goo.gl/maps/example2" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-directions me-1"></i> Cómo llegar
                            </a>
                            <a href="tel:4441844270" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-phone me-1"></i> Llamar
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Sucursal Monterrey -->
                <div class="col-lg-4 col-md-6">
                    <div class="sucursal-card">
                        <div class="sucursal-header">
                            <div class="sucursal-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <h4>Sucursal Monterrey</h4>
                            <p class="sucursal-badge sucursal">SUCURSAL</p>
                        </div>
                        
                        <div class="sucursal-info">
                            <p><i class="fas fa-phone me-2"></i><strong>Teléfono:</strong> 81 8654 0464</p>
                            <p><i class="fas fa-map-marker-alt me-2"></i><strong>Ubicación:</strong> Av. Lic. Arturo B. de La Garza, poniente, 67267 Nuevo León, N.L.</p>
                            <p><i class="fas fa-clock me-2"></i><strong>Horario:</strong> Lunes a Viernes 9:00 - 18:00</p>
                        </div>
                        
                        <div class="sucursal-mapa">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3596.6499633668363!2d-100.10144939999999!3d25.6497405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8662c36b44aa15c9%3A0x6afb3826bd8b65ef!2sTinacos%20Tlaloc!5e0!3m2!1ses!2smx!4v1712167901629!5m2!1ses!2smx" 
                                width="100%" 
                                height="250" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                        
                        <div class="sucursal-actions">
                            <a href="https://goo.gl/maps/example3" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-directions me-1"></i> Cómo llegar
                            </a>
                            <a href="tel:8186540464" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-phone me-1"></i> Llamar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FORMULARIO DE PROYECTO -->
    <section class="contact-form-section py-5 bg-light" id="contactform">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="section-title">Tienes un proyecto<br><span class="text-primary">Nosotros lo hacemos realidad</span></h2>
                    <p class="section-subtitle">Cuéntanos tu idea y nos pondremos en contacto contigo</p>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="project-form-card">
                        <form method="POST" action="{{ route('proyecto.enviar')  }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre" class="form-label">
                                            <i class="fas fa-user me-2"></i>Nombre completo *
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('nombre') is-invalid @enderror" 
                                               id="nombre" 
                                               name="nombre" 
                                               value="{{ old('nombre') }}"
                                               required 
                                               placeholder="Ej: Jose Pérez">
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Correo electrónico *
                                        </label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}"
                                               required 
                                               placeholder="correo@ejemplo.com">
                                        @error('email')
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
                                               value="{{ old('telefono') }}"
                                               required 
                                               placeholder="55 1234 5678">
                                        @error('telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="archivo" class="form-label">
                                            <i class="fas fa-file-upload me-2"></i>Subir archivo (opcional)
                                        </label>
                                        <input type="file" 
                                               class="form-control @error('archivo') is-invalid @enderror" 
                                               id="archivo" 
                                               name="archivo"
                                               accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Máximo 5 MB, formatos: PDF, JPG, PNG
                                        </small>
                                        @error('archivo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="comentarios" class="form-label">
                                            <i class="fas fa-comment-dots me-2"></i>Cuéntanos tu idea o proyecto *
                                        </label>
                                        <textarea class="form-control @error('comentarios') is-invalid @enderror" 
                                                  id="comentarios" 
                                                  name="comentarios" 
                                                  rows="6" 
                                                  required 
                                                  placeholder="Describe tu proyecto, necesidades específicas, medidas requeridas, etc...">{{ old('comentarios') }}</textarea>
                                        @error('comentarios')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-check mb-4">
                                        <input class="form-check-input @error('privacidad') is-invalid @enderror" 
                                               type="checkbox" 
                                               id="privacidad" 
                                               name="privacidad" 
                                               value="1"
                                               {{ old('privacidad') ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label" for="privacidad">
                                            He leído y acepto la <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#terminosModal">política de privacidad</a> *
                                        </label>
                                        @error('privacidad')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar Proyecto
                                    </button>
                                    <p class="text-muted mt-3 small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Los campos marcados con * son obligatorios
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== MODAL DE TÉRMINOS Y CONDICIONES ===== -->
    <div class="modal fade" id="terminosModal" tabindex="-1" aria-labelledby="terminosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="terminosModalLabel">
                        <i class="fas fa-file-contract me-2"></i>
                        Términos, Condiciones y Política de Privacidad
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-primary">Última actualización: {{ date('d/m/Y') }}</h6>
                    
                    <h5 class="mt-4">1. Aceptación de los Términos</h5>
                    <p>Al utilizar nuestro sitio web y servicios, usted acepta cumplir con estos términos y condiciones. Si no está de acuerdo con alguna parte de estos términos, no utilice nuestro sitio web.</p>
                    
                    <h5>2. Protección de Datos Personales</h5>
                    <p>En Tanques Tláloc respetamos su privacidad. Los datos personales que recabamos a través de formularios son utilizados exclusivamente para:</p>
                    <ul>
                        <li>Contactarle respecto a su consulta o proyecto</li>
                        <li>Procesar pedidos y cotizaciones</li>
                        <li>Enviar información sobre productos y promociones</li>
                        <li>Mejorar nuestros servicios</li>
                    </ul>
                    
                    <h5>3. Confidencialidad</h5>
                    <p>Sus datos personales no serán compartidos, vendidos o transferidos a terceros sin su consentimiento expreso, excepto cuando sea requerido por ley.</p>
                    
                    <h5>4. Información del Producto</h5>
                    <p>Nos esforzamos por mantener la información de productos precisa y actualizada. Sin embargo, no garantizamos que las descripciones, precios u otra información sean completamente exactas o estén libres de errores.</p>
                    
                    <h5>5. Derechos ARCO</h5>
                    <p>Usted tiene derecho a Acceder, Rectificar, Cancelar u Oponerse al tratamiento de sus datos personales. Para ejercer estos derechos, contáctenos en:</p>
                    <p><strong>Email:</strong> tanquestlaloc@outlook.com</p>
                    <p><strong>Teléfono:</strong> 55 4017 5803</p>
                    
                    <h5>6. Uso de Archivos Adjuntos</h5>
                    <p>Los archivos que suba a través de nuestro formulario serán utilizados exclusivamente para evaluar su proyecto y no serán compartidos con terceros.</p>
                    
                    <h5>7. Contacto</h5>
                    <p>Para preguntas sobre estos términos o nuestra política de privacidad, contáctenos en:</p>
                    <p><i class="fas fa-envelope me-2"></i>tanquestlaloc@outlook.com</p>
                    <p><i class="fas fa-phone me-2"></i>55 4017 5803</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="aceptarTerminos" data-bs-dismiss="modal">Acepto los términos</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
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

    <!-- ===== SCRIPTS ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/contacto.js') }}"></script>
    
    @if(session('success'))
    <script>
    $(document).ready(function() {
        Swal.fire({
            title: '¡Mensaje Enviado!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#7fad39',
            confirmButtonText: 'Aceptar'
        });
    });
    </script>
    @endif

    <script>
        $(document).ready(function() {
            $('#aceptarTerminos').click(function() {
                $('#privacidad').prop('checked', true);
                
                Swal.fire({
                    title: '¡Términos aceptados!',
                    text: 'Has aceptado los términos y condiciones',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
            
            $('#terminosModal').on('hidden.bs.modal', function () {
                $('#privacidad').prop('checked', true);
            });
        });
    </script>
</body>
</html>