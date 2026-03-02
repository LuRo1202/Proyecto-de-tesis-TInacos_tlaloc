@php use App\Helpers\ProductoHelper; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->nombre }} | Tanques Tlaloc - {{ $sucursal->nombre }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/detalles.css') }}">
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tinacos Tlaloc" style="height: 50px;">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('tienda') }}">Tienda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contacto') }}">Contacto</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    @auth
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>{{ auth()->user()->nombre }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
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
                        <span class="cart-badge">{{ $cartCount }}</span> 
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <!-- CONTENIDO PRINCIPAL -->
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <h1 class="hero-title animate__animated animate__fadeInDown">
                        Creadores del Tinaco Bala<br>
                        <span style="font-size: 2rem; display: block; margin-top: 10px;">Espacios Reducidos</span>
                    </h1>
                    
                    <h2 class="hero-subtitle animate__animated animate__fadeInUp">
                        Tanques TlálOC - El mejor Tinaco
                    </h2>
                    
                    <div class="company-info mb-4">
                        <p>
                            <i class="fas fa-flag"></i>
                            <strong>Empresa 100% Mexicana</strong>
                        </p>
                        <p>
                            <i class="fas fa-calendar-alt"></i>
                            <strong>Más de 20 años de experiencia</strong>
                        </p>
                        <p>
                            <i class="fas fa-industry"></i>
                            <strong>Especialistas en ROTOMOLDEO</strong>
                        </p>
                        <p>
                            <i class="fas fa-lightbulb"></i>
                            <strong>Innovadores del Tinaco Bala</strong>
                        </p>
                    </div>
                    
                    <a href="{{ route('tienda', ['categoria' => 2]) }}" class="btn btn-lg hero-btn animate__animated animate__pulse">
                        <i class="fas fa-bolt me-2"></i>Ver Tinaco Bala
                    </a>
                </div>
                
                <!-- COLUMNA DERECHA CON CONTACTO -->
                <div class="col-lg-5">
                    <div class="contact-hero-box">
                        <h5>
                            <i class="fas fa-phone-alt me-2"></i>Contáctanos
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
                            Contactar por WhatsApp
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

    <!-- Product Details Section -->
    <section class="product-details spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="product__details__pic">
                        <div class="product__details__pic__item">
                            <img class="img-zoom" src="{{ ProductoHelper::obtenerImagenProducto($producto->codigo) }}" alt="{{ $producto->nombre }}" id="producto-imagen">
                            
                            <!-- BADGE DE OFERTA - CORREGIDO -->
                            @if($producto->en_oferta)
                            <div class="position-absolute top-0 start-0 m-3" style="z-index: 10;">
                                <span class="badge bg-danger p-2" style="font-size: 1rem;">
                                    <i class="fas fa-tag me-1"></i> -{{ intval($producto->porcentaje_descuento) }}%
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="product__details__text">
                        <h3 id="producto-nombre">{{ $producto->nombre }}</h3>
                        
                        <!-- Selector de variantes -->
                        @if(count($variantes) > 1)
                            @php
                                $familia = preg_replace('/-(C|N|R|AZ|RM|M|B|MD)$/i', '', $producto->codigo);
                            @endphp
                            <div class="variantes-selector mb-4">
                                <h6 class="mb-3">
                                    <i class="fas fa-list-alt text-warning me-2"></i>
                                    <strong>Opciones disponibles:</strong>
                                </h6>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($variantes as $variante)
                                        @php
                                            $activo = ($variante->id == $producto->id);
                                            $sinExistencia = ($variante->pivot->existencias <= 0);
                                            $info = ProductoHelper::obtenerInfoVariantePorCodigo($variante->codigo);
                                            $varianteEnOferta = $variante->en_oferta; // IMPORTANTE: Saber si esta variante tiene oferta
                                        @endphp
                                        <button type="button" 
                                                class="btn-variante-detalle {{ 'tipo-' . $info['tipo'] }} {{ $activo ? 'activo' : '' }} {{ $sinExistencia ? 'disabled' : '' }}"
                                                data-variante-id="{{ $variante->id }}"
                                                data-codigo="{{ $variante->codigo }}"
                                                data-nombre="{{ $variante->nombre }}"
                                                data-precio="{{ $variante->precio }}"
                                                data-precio-final="{{ $variante->precio_final }}"
                                                data-en-oferta="{{ $varianteEnOferta ? 'true' : 'false' }}"
                                                data-porcentaje="{{ $variante->porcentaje_descuento }}"
                                                data-existencias="{{ $variante->pivot->existencias }}"
                                                data-imagen="{{ ProductoHelper::obtenerImagenProducto($variante->codigo) }}"
                                                onclick="cambiarVarianteDetalle(this)"
                                                {{ $sinExistencia ? 'disabled' : '' }}
                                                title="{{ $variante->nombre }}">
                                            
                                            @if($info['tipo'] === 'color')
                                                <span class="variante-color" style="background-color: {{ $info['hex'] }}"></span>
                                            @else
                                                <span class="variante-icono">
                                                    <i class="{{ $info['icono'] }}"></i>
                                                </span>
                                            @endif
                                            
                                            <span class="variante-texto">
                                                {{ $info['nombre'] }}
                                                @if($varianteEnOferta)
                                                    <small class="text-danger d-block">-{{ intval($variante->porcentaje_descuento) }}%</small>
                                                @endif
                                                @if($info['tipo'] === 'mayor-diametro')
                                                    <small class="d-block">+ Diámetro</small>
                                                @endif
                                            </span>
                                            
                                            @if($sinExistencia)
                                                <span class="variante-agotado">✗</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif                      
                        
                        <!-- Información dinámica del precio - MODIFICADO PARA OFERTAS -->
                        <div class="product__details__price" id="producto-precio">
                            @if($producto->en_oferta)
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <span class="text-muted text-decoration-line-through precio-original">
                                        {{ ProductoHelper::formatoPrecio($producto->precio) }}
                                    </span>
                                    <span class="text-danger fw-bold precio-final" style="font-size: 1.5rem;">
                                        {{ ProductoHelper::formatoPrecio($producto->precio_final) }}
                                    </span>
                                    <span class="badge bg-danger porcentaje-descuento">
                                        -{{ intval($producto->porcentaje_descuento) }}%
                                    </span>
                                </div>
                                @php
                                    $ahorro = $producto->precio - $producto->precio_final;
                                @endphp
                                <div class="alert alert-success mt-2 py-2 texto-ahorro" style="background: #d4edda; border: none;">
                                    <i class="fas fa-piggy-bank me-2"></i>
                                    <strong>¡Ahorras {{ ProductoHelper::formatoPrecio($ahorro) }}!</strong>
                                </div>
                            @else
                                <span class="precio-normal">{{ ProductoHelper::formatoPrecio($producto->precio) }}</span>
                            @endif
                        </div>
                        
                        <div id="producto-info">
                            <p><strong>Capacidad:</strong> {{ $producto->litros }} litros</p>
                            <p><strong>Categoría:</strong> {{ $producto->categoria->nombre }}</p>
                            <p><strong>Código:</strong> <span id="producto-codigo">{{ $producto->codigo }}</span></p>
                            <p><strong>Existencias:</strong> <span id="producto-existencias">{{ $producto->pivot->existencias }}</span> unidades</p>
                            @if($producto->color)
                                <p><strong>Color:</strong> <span id="producto-color">{{ $producto->color->nombre }}</span></p>
                            @endif
                        </div>
                        
                        @if($producto->pivot->existencias > 0)
                            <form method="POST" action="{{ route('carrito.agregar') }}" id="form-agregar-carrito">
                                @csrf
                                <input type="hidden" name="producto_id" id="producto-id" value="{{ $producto->id }}">
                                
                                <div class="product__details__quantity">
                                    <div class="quantity">
                                        <div class="pro-qty">
                                            <input type="number" 
                                                name="cantidad" 
                                                id="producto-cantidad" 
                                                value="1" 
                                                min="1" 
                                                max="{{ $producto->pivot->existencias }}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="agregar_carrito" class="primary-btn">
                                    <i class="fas fa-cart-plus me-2"></i>AGREGAR AL CARRITO
                                </button>
                            </form>
                        @else
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Producto agotado temporalmente
                            </div>
                        @endif
                        
                        <ul>
                            <li><b>Entrega:</b> <span>Envíos a toda la localidad</span></li>
                            <li><b>Garantía:</b> <span>1 año contra defectos de fabricación</span></li>
                            <li><b>Material:</b> <span>Polietileno de alta densidad (HDPE)</span></li>
                            <li><b>Accesorios:</b> <span>Incluye tapa, aro y multiconector</span></li>
                        </ul>
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
    
    <!-- Datos de variantes para JS -->
    <script>
        const variantesProducto = @json($variantes);
        const productoActual = {
            id: {{ $producto->id }},
            codigo: '{{ $producto->codigo }}',
            nombre: '{{ $producto->nombre }}',
            precio: {{ $producto->precio }},
            precio_final: {{ $producto->precio_final }},
            en_oferta: {{ $producto->en_oferta ? 'true' : 'false' }},
            porcentaje: {{ $producto->porcentaje_descuento }},
            existencias: {{ $producto->pivot->existencias }},
            imagen: '{{ ProductoHelper::obtenerImagenProducto($producto->codigo) }}'
        };
    </script>
    
    <script>
    // Función para formatear precio
    function formatearPrecio(precio) {
        return '$' + parseFloat(precio).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    // Función para cambiar variante
    function cambiarVarianteDetalle(boton) {
        if (boton.classList.contains('disabled')) return;
        
        // Remover clase activa de todos los botones
        document.querySelectorAll('.btn-variante-detalle').forEach(btn => {
            btn.classList.remove('activo');
        });
        
        // Agregar clase activa al botón clickeado
        boton.classList.add('activo');
        
        // Obtener datos de la variante
        const varianteId = boton.getAttribute('data-variante-id');
        const nombre = boton.getAttribute('data-nombre');
        const precio = parseFloat(boton.getAttribute('data-precio'));
        const precioFinal = parseFloat(boton.getAttribute('data-precio-final') || precio);
        const enOferta = boton.getAttribute('data-en-oferta') === 'true';
        const porcentaje = parseInt(boton.getAttribute('data-porcentaje') || 0);
        const existencias = parseInt(boton.getAttribute('data-existencias'));
        const imagen = boton.getAttribute('data-imagen');
        const codigo = boton.getAttribute('data-codigo');
        
        // Actualizar imagen
        document.getElementById('producto-imagen').src = imagen;
        document.getElementById('producto-imagen').alt = nombre;
        
        // Actualizar nombre
        document.getElementById('producto-nombre').textContent = nombre;
        
        // Actualizar código
        document.getElementById('producto-codigo').textContent = codigo;
        
        // Actualizar existencias
        document.getElementById('producto-existencias').textContent = existencias;
        
        // Actualizar campo oculto
        document.getElementById('producto-id').value = varianteId;
        
        // Actualizar máximo en cantidad
        const cantidadInput = document.getElementById('producto-cantidad');
        cantidadInput.max = existencias;
        if (parseInt(cantidadInput.value) > existencias) {
            cantidadInput.value = existencias;
        }
        
        // Actualizar precio según oferta
        const precioContainer = document.getElementById('producto-precio');
        
        if (enOferta) {
            const ahorro = precio - precioFinal;
            precioContainer.innerHTML = `
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <span class="text-muted text-decoration-line-through precio-original">
                        ${formatearPrecio(precio)}
                    </span>
                    <span class="text-danger fw-bold precio-final" style="font-size: 1.5rem;">
                        ${formatearPrecio(precioFinal)}
                    </span>
                    <span class="badge bg-danger porcentaje-descuento">
                        -${porcentaje}%
                    </span>
                </div>
                <div class="alert alert-success mt-2 py-2 texto-ahorro" style="background: #d4edda; border: none;">
                    <i class="fas fa-piggy-bank me-2"></i>
                    <strong>¡Ahorras ${formatearPrecio(ahorro)}!</strong>
                </div>
            `;
        } else {
            precioContainer.innerHTML = `<span class="precio-normal">${formatearPrecio(precio)}</span>`;
        }
        
        // Actualizar URL (sin recargar)
        const nuevaUrl = new URL(window.location);
        nuevaUrl.pathname = '/producto/' + varianteId;
        window.history.pushState({}, '', nuevaUrl);
    }
    
    // Zoom de imagen
    $(document).ready(function() {
        const container = $('.product__details__pic__item');
        const img = container.find('img');
        let isZooming = false;

        container.on('mousemove', function(e) {
            if (!img[0].complete) return;
            
            const { left, top, width, height } = this.getBoundingClientRect();
            const x = ((e.clientX - left) / width) * 100;
            const y = ((e.clientY - top) / height) * 100;

            img.css({
                'transform-origin': `${x}% ${y}%`,
                'transform': 'scale(2.2)'
            });
            isZooming = true;
            container.addClass('zooming');
        });

        container.on('mouseleave', function() {
            img.css({
                'transform': 'scale(1)',
                'transition': 'transform 0.3s ease'
            });
            setTimeout(() => {
                img.css('transition', 'transform 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)');
            }, 300);
            isZooming = false;
            container.removeClass('zooming');
        });

        container.on('click', function(e) {
            e.preventDefault();
            if (!isZooming) {
                img.css({
                    'transform-origin': 'center center',
                    'transform': 'scale(2.2)'
                });
                isZooming = true;
                container.addClass('zooming');
            } else {
                img.css('transform', 'scale(1)');
                isZooming = false;
                container.removeClass('zooming');
            }
        });

        // Manejo del formulario de carrito
        $('#form-agregar-carrito').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const cantidad = $('#producto-cantidad').val();
            const nombre = $('#producto-nombre').text();
            const imagen = $('#producto-imagen').attr('src');
            const maxStock = parseInt($('#producto-existencias').text());
            
            if (cantidad < 1 || cantidad > maxStock) {
                Swal.fire({
                    icon: 'Stock insuficiente',
                    title: 'Cantidad no válida',
                    text: `Selecciona una cantidad entre 1 y ${maxStock}`,
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            
            // Detectar si tiene oferta
            const tieneOferta = $('#producto-precio .precio-original').length > 0;
            let precioHtml = '';
            
            if (tieneOferta) {
                const precioOriginal = $('.precio-original').text().trim();
                const precioFinal = $('.precio-final').first().text().trim();
                const descuento = $('.porcentaje-descuento').text().trim();
                const ahorro = $('.texto-ahorro strong').text().trim();
                
                precioHtml = `
                    <div style="margin: 10px 0;">
                        <span style="text-decoration: line-through; color: #999;">${precioOriginal}</span>
                        <span style="color: #dc3545; font-weight: bold; margin-left: 10px;">${precioFinal}</span>
                        <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; margin-left: 10px;">${descuento}</span>
                        <div style="color: #28a745; margin-top: 5px;"> ${ahorro}</div>
                    </div>
                `;
            } else {
                const precio = $('.precio-normal').text().trim();
                precioHtml = `<span style="color: #7fad39; font-weight: bold;">${precio}</span>`;
            }
            
            Swal.fire({
                title: '¿Agregar al carrito?',
                html: `
                    <div style="text-align: center;">
                        <img src="${imagen}" style="width: 100px; height: 100px; object-fit: contain; margin: 10px auto; border: 1px solid #ddd; border-radius: 8px; padding: 5px;">
                        <h4 style="margin: 10px 0;">${nombre}</h4>
                        ${precioHtml}
                        <p style="background: #f0f0f0; padding: 8px; border-radius: 5px; margin-top: 10px;">
                            <strong>Cantidad:</strong> ${cantidad}
                        </p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7fad39',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, agregar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: $form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Agregando...',
                                text: 'Por favor espera',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                        },
                        success: function(response) {
                            Swal.close();
                            
                            if (response && response.cartCount !== undefined) {
                                $('.cart-badge').text(response.cartCount);
                            }
                            
                            // ===== ALERTA DE ÉXITO ELEGANTE (LA QUE PEDISTE) =====
                            Swal.fire({
                                icon: 'success',
                                title: '¡Producto Agregado!',
                                html: `
                                    <div style="text-align: center; padding: 10px;">
                                        <div style="background-color: #f0f9f0; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                                            <i class="fas fa-check-circle" style="font-size: 48px; color: #7fad39;"></i>
                                        </div>
                                        <p style="font-size: 18px; font-weight: bold; margin-bottom: 10px; color: #333;">${nombre}</p>
                                        <p style="margin-bottom: 15px; color: #666;">Cantidad: ${cantidad}</p>
                                        <div style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 50px; padding: 12px 25px; display: inline-block; margin: 0 auto;">
                                            <span style="color: #495057; margin-right: 10px; font-size: 14px;">🛒 Carrito:</span>
                                            <span style="background: linear-gradient(135deg, #7fad39, #5d8c2c); color: white; font-weight: bold; padding: 5px 15px; border-radius: 50px; font-size: 20px; box-shadow: 0 4px 10px rgba(127,173,57,0.3);">${response.cartCount || $('.cart-badge').text()}</span>
                                        </div>
                                    </div>
                                `,
                                showCancelButton: true,
                                confirmButtonColor: '#7fad39',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: '<i class="fas fa-shopping-cart me-2"></i> Ver Carrito',
                                cancelButtonText: '<i class="fas fa-store me-2"></i> Seguir Comprando',
                                background: '#fff'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '/carrito';
                                }
                            });
                        },
                        error: function(xhr) {
                            Swal.close();
                            
                            let mensaje = 'No se pudo agregar el producto';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                mensaje = xhr.responseJSON.message;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: mensaje,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        });
    });
    
    // Mensaje de sesión
    @if(session('success'))
    $(document).ready(function() {
        Swal.fire({
            title: '¡Producto Agregado!',
            html: '<div style="text-align: left; padding: 15px;">' +
                  '<p><strong>{{ session('producto_nombre') }}</strong></p>' +
                  '<p>Se ha agregado correctamente al carrito.</p>' +
                  '<p class="text-muted" style="font-size: 0.9em; margin-top: 10px;">' +
                  'Items en carrito: <span class="badge bg-success">{{ $cartCount }}</span></p>' +
                  '</div>',
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#7fad39',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-shopping-cart me-2"></i> Ver Carrito',
            cancelButtonText: '<i class="fas fa-store me-2"></i> Seguir Comprando',
            width: '450px',
            padding: '1.5em',
            backdrop: true,
            allowOutsideClick: false,
            allowEscapeKey: true,
            allowEnterKey: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route('carrito') }}';
            }
        });
        
        $('.cart-badge').text('{{ $cartCount }}');
    });
    @endif
</script>




</body>
</html>