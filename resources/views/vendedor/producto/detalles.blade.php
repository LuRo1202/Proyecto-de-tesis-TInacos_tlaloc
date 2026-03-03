@extends('vendedor.layouts.app')

@section('title', $producto->nombre . ' - Detalles')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="header-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-box me-2"></i>Detalles del Producto
                </h1>
                <span class="badge-categoria">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</span>
                <span class="sucursal-badge ms-2">
                    <i class="fas fa-store"></i> {{ $sucursal->nombre }}
                </span>
            </div>
            
            <a href="{{ route('vendedor.catalogo.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver al Catálogo
            </a>
        </div>
    </div>

    <div class="row g-3">
        <!-- Columna de imagen con zoom -->
        <div class="col-lg-6">
            <div class="product-container">
                <div class="product-image-container">
                    <div class="product-image-zoom-container" id="zoomContainer">
                        
                        <img class="product-zoom-image" 
                             src="{{ App\Helpers\ProductoHelper::obtenerImagenProducto($producto->codigo) }}" 
                             alt="{{ $producto->nombre }}" 
                             id="producto-imagen"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22 viewBox=%220 0 400 300%22%3E%3Crect width=%22400%22 height=%22300%22 fill=%22%23f8f9fa%22/%3E%3Ctext x=%22200%22 y=%22150%22 font-family=%22Arial%22 font-size=%2216%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22 fill=%22%236c757d%22%3E{{ substr($producto->codigo, 0, 8) }}%3C/text%3E%3C/svg%3E';">
                        <div class="zoom-indicator">
                            <i class="fas fa-search-plus me-1"></i> Zoom activo
                        </div>
                    </div>
                    
                    @if(strpos(App\Helpers\ProductoHelper::obtenerImagenProducto($producto->codigo), 'data:image/svg') === 0)
                    <div class="alert alert-warning alert-sm mt-3 mb-0 py-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Imagen no disponible
                    </div>
                    @endif
                </div>
                
                <!-- Información de inventario compacta -->
                <div class="p-3">
                    <div class="compact-card-header">
                        <i class="fas fa-warehouse me-2"></i>Información de Inventario
                    </div>
                    
                    <div class="inventory-grid">
                        <div class="inventory-item">
                            <div class="inventory-label">Existencia Actual</div>
                            <div class="inventory-value">
                                @php
                                    $badge_class = 'badge-stock-alto';
                                    $stock_text = $existencias . ' unidades';
                                    
                                    if ($existencias <= 0) {
                                        $badge_class = 'badge-stock-agotado';
                                        $stock_text = 'Agotado';
                                    } elseif ($existencias <= 5) {
                                        $badge_class = 'badge-stock-bajo';
                                    } elseif ($existencias <= 15) {
                                        $badge_class = 'badge-stock-medio';
                                    }
                                @endphp
                                <span class="badge-stock {{ $badge_class }}" id="badge-stock">
                                    <i class="fas fa-boxes me-1"></i>
                                    {{ $stock_text }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="inventory-item">
                            <div class="inventory-label">Estado</div>
                            <div class="inventory-value">
                                @if($producto->activo)
                                <span class="badge bg-success">Activo</span>
                                @else
                                <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="inventory-item">
                            <div class="inventory-label">Destacado</div>
                            <div class="inventory-value">
                                @if($producto->destacado)
                                <span class="badge bg-warning">Destacado</span>
                                @else
                                <span class="badge bg-secondary">Normal</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="inventory-item">
                            <div class="inventory-label">Capacidad</div>
                            <div class="inventory-value" id="producto-litros">
                                <i class="fas fa-tint me-1"></i>
                                {{ number_format($producto->litros) }} litros
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Columna de información -->
        <div class="col-lg-6">
            <div class="product-container p-3">
                <!-- Título y precio -->
                <h1 class="product-title" id="producto-nombre">
                    {{ $producto->nombre }}
                </h1>
                
                <div class="product-price-container">
                    @if($en_oferta)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="precio-original" id="producto-precio-original">
                            {{ App\Helpers\ProductoHelper::formatoPrecio($precio_original) }}
                        </span>
                        <span class="badge bg-danger" id="producto-descuento">
                            -{{ App\Helpers\ProductoHelper::formatoPorcentaje($producto->porcentaje_descuento) }}%
                        </span>
                    </div>
                    <div class="product-price" id="producto-precio-final">
                        {{ App\Helpers\ProductoHelper::formatoPrecio($precio_final) }}
                    </div>
                    <small class="text-success">
                        <i class="fas fa-tag me-1"></i>Precio con oferta
                    </small>
                    @else
                    <div class="product-price" id="producto-precio">
                        {{ App\Helpers\ProductoHelper::formatoPrecio($precio_original) }}
                    </div>
                    @endif
                </div>
                
                <!-- Selector de variantes -->
                @if($variantes->count() > 1)
                <div class="variantes-selector">
                    <h6 class="variantes-title">
                        <i class="fas fa-list-alt me-2"></i>
                        Opciones disponibles:
                    </h6>
                    <div class="d-flex flex-wrap gap-2" id="variantes-container">
                        @foreach($variantes as $variante)
                            @php
                                $activo = ($variante->id == $producto->id);
                                $existenciasVariante = $sucursal->productos()
                                    ->where('producto_id', $variante->id)
                                    ->first()
                                    ->pivot
                                    ->existencias ?? 0;
                                $sinExistencia = ($existenciasVariante <= 0);
                                $info = App\Helpers\ProductoHelper::obtenerInfoVariantePorCodigo($variante->codigo);
                                $imagenVariante = App\Helpers\ProductoHelper::obtenerImagenProducto($variante->codigo);
                                
                                // Verificar si esta variante tiene oferta
                                $varianteEnOferta = false;
                                $variantePrecioOriginal = $variante->precio;
                                $variantePrecioFinal = $variante->precio;
                                $varianteDescuento = 0;
                                
                                if ($variante->ofertas->isNotEmpty()) {
                                    $varianteEnOferta = true;
                                    $ofertaVar = $variante->ofertas->first();
                                    if ($ofertaVar->tipo == 'porcentaje') {
                                        $variantePrecioFinal = $variante->precio * (1 - $ofertaVar->valor / 100);
                                        $varianteDescuento = $ofertaVar->valor;
                                    } else {
                                        $variantePrecioFinal = $variante->precio - $ofertaVar->valor;
                                        $varianteDescuento = round(($ofertaVar->valor / $variante->precio) * 100);
                                    }
                                }
                            @endphp
                            <button type="button" 
                                    class="btn-variante-detalle tipo-{{ $info['tipo'] }} {{ $activo ? 'activo' : '' }} {{ $sinExistencia ? 'disabled' : '' }}"
                                    data-variante-id="{{ $variante->id }}"
                                    data-codigo="{{ $variante->codigo }}"
                                    data-nombre="{{ $variante->nombre }}"
                                    data-precio-original="{{ $variante->precio }}"
                                    data-precio-final="{{ $variantePrecioFinal }}"
                                    data-en-oferta="{{ $varianteEnOferta ? 'true' : 'false' }}"
                                    data-descuento="{{ $varianteDescuento }}"
                                    data-existencias="{{ $existenciasVariante }}"
                                    data-litros="{{ $variante->litros }}"
                                    data-imagen="{{ $imagenVariante }}"
                                    onclick="cambiarVarianteDetalle(this)"
                                    {{ $sinExistencia ? 'disabled' : '' }}
                                    title="{{ $variante->nombre }}">
                                
                                @if($info['tipo'] === 'color')
                                <span class="variante-color" style="background-color: {{ $info['hex'] }}"></span>
                                @endif
                                
                                <span class="variante-texto">
                                    {{ $info['nombre'] }}
                                    @if($info['tipo'] === 'mayor-diametro')
                                    <small class="d-block text-muted">+ Diámetro</small>
                                    @endif
                                </span>
                                
                                @if($varianteEnOferta)
                                <span class="variante-oferta">🔥 -{{ App\Helpers\ProductoHelper::formatoPorcentaje($varianteDescuento) }}%</span>
                                @endif
                                
                                @if($sinExistencia)
                                <span class="variante-agotado">✗</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Información del producto -->
                <div class="product-meta" id="producto-info">
                    <div class="meta-item">
                        <strong>Capacidad:</strong>
                        <span id="info-litros">{{ $producto->litros }} litros</span>
                    </div>
                    <div class="meta-item">
                        <strong>Categoría:</strong>
                        <span>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</span>
                    </div>
                    <div class="meta-item">
                        <strong>Código:</strong>
                        <span id="producto-codigo">{{ $producto->codigo }}</span>
                    </div>
                    @if($producto->color)
                    <div class="meta-item">
                        <strong>Color:</strong>
                        <span id="producto-color">{{ $producto->color->nombre }}</span>
                    </div>
                    @endif
                    <div class="meta-item">
                        <strong>Existencias:</strong>
                        <span id="info-existencias">
                            @if($existencias > 0)
                            <span class="badge-stock {{ $badge_class }}" id="texto-existencias">
                                {{ $existencias }} unidades
                            </span>
                            @else
                            <span class="badge-stock badge-stock-agotado" id="texto-existencias">
                                Agotado
                            </span>
                            @endif
                        </span>
                    </div>
                </div>
                
                <!-- Formulario para crear pedido -->
                @if($existencias > 0)
                <div class="product-form">
                    <div class="quantity-selector">
                        <label for="producto-cantidad" style="font-weight: 600;">Cantidad:</label>
                        <input type="number" 
                               id="producto-cantidad" 
                               value="1" 
                               min="1" 
                               max="{{ $existencias }}"
                               class="quantity-input">
                    </div>
                    
                    <div class="d-grid">
                        <a href="#" class="btn-crear-pedido" id="btn-crear-pedido">
                            <i class="fas fa-file-invoice me-2"></i>CREAR PEDIDO
                        </a>
                    </div>
                </div>
                @else
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Producto agotado temporalmente
                </div>
                @endif
                
                <!-- Características del producto -->
                <div class="product-features">
                    <h6 style="font-weight: 600; margin-bottom: 12px; font-size: 0.95rem;">
                        <i class="fas fa-list-check me-2"></i>Características:
                    </h6>
                    <ul class="features-list">
                        <li>
                            <i class="fas fa-truck"></i>
                            <b>Entrega:</b>
                            <span>Envíos a toda la localidad</span>
                        </li>
                        <li>
                            <i class="fas fa-shield-alt"></i>
                            <b>Garantía:</b>
                            <span>1 año contra defectos de fabricación</span>
                        </li>
                        <li>
                            <i class="fas fa-cube"></i>
                            <b>Material:</b>
                            <span>Polietileno de alta densidad (HDPE)</span>
                        </li>
                        <li>
                            <i class="fas fa-tools"></i>
                            <b>Accesorios:</b>
                            <span>Incluye tapa, aro y multiconector</span>
                        </li>
                    </ul>
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
        --primary-light: #9fc957;
        --light: #f8f9fa;
        --light-gray: #e9ecef;
        --gray: #6c757d;
        --dark: #212529;
    }
    
    .header-bar {
        background: white;
        border-radius: 8px;
        padding: 12px 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-left: 4px solid var(--primary);
    }
    
    .header-title {
        margin: 0;
        color: var(--dark);
        font-weight: 600;
        font-size: 1rem;
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
    
    .badge-categoria {
        background: var(--primary);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .product-container {
        background: white;
        border-radius: 10px;
        padding: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        position: relative;
    }
    
    .product-image-container {
        padding: 15px;
        border-bottom: 1px solid var(--light-gray);
        position: relative;
    }
    
    .product-image-zoom-container {
        position: relative;
        overflow: hidden;
        cursor: zoom-in;
        background: #f8f9fa;
        border-radius: 6px;
    }
    
    .oferta-badge-absolute {
        position: absolute;
        top: 25px;
        left: 25px;
        z-index: 10;
    }
    
    .product-zoom-image {
        width: 100%;
        height: 280px;
        object-fit: contain;
        transition: transform 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        will-change: transform;
        display: block;
    }
    
    .zoom-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        z-index: 2;
    }
    
    .product-image-zoom-container:hover .zoom-indicator {
        opacity: 1;
    }
    
    .compact-card-header {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--light-gray);
    }
    
    .inventory-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .inventory-item {
        background: var(--light);
        border-radius: 6px;
        padding: 10px;
        border: 1px solid var(--light-gray);
    }
    
    .inventory-label {
        font-size: 0.8rem;
        color: var(--gray);
        margin-bottom: 4px;
    }
    
    .inventory-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--dark);
    }
    
    .product-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 5px;
        line-height: 1.3;
    }
    
    .product-price-container {
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
    }
    
    .precio-original {
        font-size: 1rem;
        color: var(--gray);
        text-decoration: line-through;
    }
    
    .product-price {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--primary);
    }
    
    .variantes-selector {
        margin: 20px 0;
        padding: 15px;
        background: var(--light);
        border-radius: 6px;
        border: 1px solid var(--light-gray);
    }
    
    .variantes-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 12px;
    }
    
    .btn-variante-detalle {
        padding: 8px 12px;
        border: 2px solid #ddd;
        border-radius: 6px;
        background: white;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        font-weight: 500;
        cursor: pointer;
        font-size: 0.8rem;
        position: relative;
    }
    
    .btn-variante-detalle:hover {
        border-color: var(--primary);
        background: #f8f9fa;
    }
    
    .btn-variante-detalle.activo {
        border-color: var(--primary);
        background: var(--primary);
        color: white;
    }
    
    .btn-variante-detalle.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .variante-color {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid #ddd;
    }
    
    .variante-oferta {
        background: #dc3545;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.7rem;
        margin-left: 5px;
    }
    
    .variante-agotado {
        color: #dc3545;
        font-weight: bold;
        margin-left: 4px;
    }
    
    .product-meta {
        margin-bottom: 20px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    
    .meta-item strong {
        min-width: 100px;
        color: var(--dark);
        font-weight: 500;
    }
    
    .badge-stock {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .badge-stock-alto { background: #d4edda; color: #155724; }
    .badge-stock-medio { background: #fff3cd; color: #856404; }
    .badge-stock-bajo { background: #f8d7da; color: #721c24; }
    .badge-stock-agotado { background: #e9ecef; color: #6c757d; }
    
    .product-form {
        margin: 20px 0;
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .quantity-input {
        width: 80px;
        padding: 8px 12px;
        border: 1px solid var(--light-gray);
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        text-align: center;
    }
    
    .btn-crear-pedido {
        background: var(--primary);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
    }
    
    .btn-crear-pedido:hover {
        background: var(--primary-dark);
        color: white;
    }
    
    .product-features {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid var(--light-gray);
    }
    
    .features-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .features-list li {
        padding: 8px 0;
        display: flex;
        align-items: flex-start;
        font-size: 0.9rem;
    }
    
    .features-list li i {
        color: var(--primary);
        margin-right: 10px;
        width: 16px;
        margin-top: 2px;
    }
    
    .features-list li b {
        min-width: 100px;
        color: var(--dark);
        font-weight: 500;
    }
    
    @media (max-width: 992px) {
        .product-zoom-image {
            height: 240px;
        }
        .inventory-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .product-zoom-image {
            height: 200px;
        }
        .product-title {
            font-size: 1.3rem;
        }
        .product-price {
            font-size: 1.4rem;
        }
        .meta-item strong {
            min-width: 80px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    let productoIdActual = {{ $producto->id }};
    let isZooming = false;

    // Inicializar zoom
    function inicializarZoom() {
        const container = document.getElementById('zoomContainer');
        const img = container.querySelector('.product-zoom-image');
        
        if (!img.complete) {
            img.onload = inicializarZoom;
            return;
        }
        
        container.addEventListener('mousemove', function(e) {
            const { left, top, width, height } = this.getBoundingClientRect();
            const x = ((e.clientX - left) / width) * 100;
            const y = ((e.clientY - top) / height) * 100;
            
            img.style.transformOrigin = `${x}% ${y}%`;
            img.style.transform = 'scale(2.2)';
            isZooming = true;
            container.classList.add('zooming');
        });

        container.addEventListener('mouseleave', function() {
            img.style.transform = 'scale(1)';
            img.style.transition = 'transform 0.3s ease';
            setTimeout(() => {
                img.style.transition = 'transform 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            }, 300);
            isZooming = false;
            container.classList.remove('zooming');
        });

        container.addEventListener('click', function(e) {
            e.preventDefault();
            if (!isZooming) {
                img.style.transformOrigin = 'center center';
                img.style.transform = 'scale(2.2)';
                isZooming = true;
                container.classList.add('zooming');
            } else {
                img.style.transform = 'scale(1)';
                isZooming = false;
                container.classList.remove('zooming');
            }
        });
    }

    // Cambiar variante
    function cambiarVarianteDetalle(element) {
        if (element.classList.contains('disabled')) return;
        
        const varianteId = element.dataset.varianteId;
        const codigo = element.dataset.codigo;
        const nombre = element.dataset.nombre;
        const precioOriginal = element.dataset.precioOriginal;
        const precioFinal = element.dataset.precioFinal;
        const enOferta = element.dataset.enOferta === 'true';
        const descuento = element.dataset.descuento;
        const existencias = element.dataset.existencias;
        const litros = element.dataset.litros;
        const imagen = element.dataset.imagen;
        
        productoIdActual = varianteId;
        
        // Actualizar clases activas
        document.querySelectorAll('.btn-variante-detalle').forEach(btn => {
            btn.classList.remove('activo');
        });
        element.classList.add('activo');
        
        // Actualizar nombre
        document.getElementById('producto-nombre').textContent = nombre;
        document.getElementById('producto-codigo').textContent = codigo;
        document.getElementById('info-litros').textContent = litros + ' litros';
        document.getElementById('producto-litros').innerHTML = '<i class="fas fa-tint me-1"></i>' + litros + ' litros';
        
        // Actualizar precios según oferta
        const priceContainer = document.querySelector('.product-price-container');
        if (enOferta) {
            priceContainer.innerHTML = `
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="precio-original" id="producto-precio-original">
                        $${parseFloat(precioOriginal).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </span>
                    <span class="badge bg-danger" id="producto-descuento">
                        -${descuento}%
                    </span>
                </div>
                <div class="product-price" id="producto-precio-final">
                    $${parseFloat(precioFinal).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                </div>
                <small class="text-success">
                    <i class="fas fa-tag me-1"></i>Precio con oferta
                </small>
            `;
        } else {
            priceContainer.innerHTML = `
                <div class="product-price" id="producto-precio">
                    $${parseFloat(precioOriginal).toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                </div>
            `;
        }
        
        // Actualizar stock
        let stockClass = '';
        let stockText = '';
        
        if (parseInt(existencias) <= 0) {
            stockClass = 'badge-stock-agotado';
            stockText = 'Agotado';
            document.getElementById('btn-crear-pedido').disabled = true;
            document.getElementById('btn-crear-pedido').classList.add('disabled');
            document.getElementById('producto-cantidad').disabled = true;
        } else {
            if (parseInt(existencias) <= 5) {
                stockClass = 'badge-stock-bajo';
                stockText = existencias + ' unidades';
            } else if (parseInt(existencias) <= 15) {
                stockClass = 'badge-stock-medio';
                stockText = existencias + ' unidades';
            } else {
                stockClass = 'badge-stock-alto';
                stockText = existencias + ' unidades';
            }
            document.getElementById('btn-crear-pedido').disabled = false;
            document.getElementById('btn-crear-pedido').classList.remove('disabled');
            document.getElementById('producto-cantidad').disabled = false;
        }
        
        const badgeStock = document.getElementById('badge-stock');
        badgeStock.className = 'badge-stock ' + stockClass;
        badgeStock.innerHTML = '<i class="fas fa-boxes me-1"></i>' + stockText;
        
        const textoExistencias = document.getElementById('texto-existencias');
        if (textoExistencias) {
            textoExistencias.className = 'badge-stock ' + stockClass;
            textoExistencias.textContent = stockText;
        }
        
        // Actualizar imagen
        const imgElement = document.getElementById('producto-imagen');
        imgElement.src = imagen;
        imgElement.alt = nombre;
        
        imgElement.onload = function() {
            inicializarZoom();
        };
        
        // Actualizar cantidad máxima
        const cantidadInput = document.getElementById('producto-cantidad');
        cantidadInput.max = existencias;
        if (parseInt(existencias) > 0) {
            if (parseInt(cantidadInput.value) > parseInt(existencias)) {
                cantidadInput.value = existencias;
            }
        }
        
        actualizarEnlacePedido();
        
        Swal.fire({
            title: 'Variante cambiada',
            text: nombre,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    // Actualizar enlace de pedido
    function actualizarEnlacePedido() {
        const cantidad = document.getElementById('producto-cantidad').value;
        document.getElementById('btn-crear-pedido').href = `{{ route('vendedor.pedidos.create') }}?producto_id=${productoIdActual}&cantidad=${cantidad}`;
    }

    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
        inicializarZoom();
        actualizarEnlacePedido();
        
        const cantidadInput = document.getElementById('producto-cantidad');
        if (cantidadInput) {
            cantidadInput.addEventListener('change', function() {
                let cantidad = parseInt(this.value);
                const maxCantidad = parseInt(this.getAttribute('max'));
                const minCantidad = parseInt(this.getAttribute('min'));
                
                if (isNaN(cantidad) || cantidad < minCantidad) {
                    this.value = minCantidad;
                    cantidad = minCantidad;
                } else if (cantidad > maxCantidad) {
                    this.value = maxCantidad;
                    cantidad = maxCantidad;
                    Swal.fire({
                        title: 'Stock máximo',
                        text: 'La cantidad máxima disponible es ' + maxCantidad,
                        icon: 'info',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
                
                actualizarEnlacePedido();
            });
        }
        
        const btnCrear = document.getElementById('btn-crear-pedido');
        if (btnCrear) {
            btnCrear.addEventListener('click', function(e) {
                e.preventDefault();
                const cantidad = document.getElementById('producto-cantidad').value;
                const maxCantidad = document.getElementById('producto-cantidad').getAttribute('max');
                
                if (parseInt(cantidad) <= 0) {
                    Swal.fire({
                        title: 'Cantidad inválida',
                        text: 'La cantidad debe ser mayor a 0',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                    return false;
                }
                
                if (parseInt(cantidad) > parseInt(maxCantidad)) {
                    Swal.fire({
                        title: 'Stock insuficiente',
                        text: 'Solo hay ' + maxCantidad + ' unidades disponibles',
                        icon: 'warning',
                        confirmButtonColor: '#ffc107'
                    });
                    return false;
                }
                
                const nombreProducto = document.getElementById('producto-nombre').textContent;
                const precioElement = document.getElementById('producto-precio-final') || document.getElementById('producto-precio');
                const precio = precioElement.textContent;
                
                Swal.fire({
                    title: '¿Crear pedido?',
                    html: `<div style="text-align: left; padding: 15px;">
                          <p><strong>${nombreProducto}</strong></p>
                          <p><strong>Cantidad:</strong> ${cantidad}</p>
                          <p><strong>Subtotal:</strong> ${precio}</p>
                          </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#7fad39',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, crear pedido',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `{{ route('vendedor.pedidos.create') }}?producto_id=${productoIdActual}&cantidad=${cantidad}`;
                    }
                });
            });
        }
        
        document.getElementById('producto-imagen').addEventListener('error', function() {
            const codigo = document.getElementById('producto-codigo').textContent.substring(0, 8);
            this.src = `data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22 viewBox=%220 0 400 300%22%3E%3Crect width=%22400%22 height=%22300%22 fill=%22%23f8f9fa%22/%3E%3Ctext x=%22200%22 y=%22150%22 font-family=%22Arial%22 font-size=%2216%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22 fill=%22%236c757d%22%3E${codigo}%3C/text%3E%3C/svg%3E`;
        });
    });
</script>
@endsection