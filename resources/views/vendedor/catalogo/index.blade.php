@extends('vendedor.layouts.app')

@section('title', 'Catálogo de Productos')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="header-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-box me-2"></i>Catálogo de Productos
                    <span class="sucursal-badge">
                        <i class="fas fa-store"></i> {{ $sucursal->nombre }}
                    </span>
                </h1>
                <p class="text-muted mb-0 small">Consulta precios y disponibilidad</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('vendedor.pedidos.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-cart-plus me-1"></i> Nuevo Pedido
                </a>
            </div>
        </div>
    </div>

    <!-- Alerta de bajo stock -->
    @if($bajo_stock > 0)
    <div class="alerta-stock">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-lg me-3" style="color: #fd7e14;"></i>
            <div>
                <strong>¡Atención!</strong>
                <p class="mb-0">Hay {{ $bajo_stock }} producto(s) con bajo stock.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Filtros -->
        <div class="col-lg-3">
            <div class="filtros-card">
                <!-- Buscador -->
                <div class="search-box mb-4">
                    <i class="fas fa-search"></i>
                    <form method="GET" action="{{ route('vendedor.catalogo.index') }}" id="searchForm">
                        @if($categoria_id > 0)
                        <input type="hidden" name="categoria" value="{{ $categoria_id }}">
                        @endif
                        @if($orden != 'nombre')
                        <input type="hidden" name="orden" value="{{ $orden }}">
                        @endif
                        <input type="text" name="busqueda" class="form-control" 
                               placeholder="Buscar producto..." 
                               value="{{ $busqueda }}"
                               autocomplete="off">
                    </form>
                </div>
                
                <!-- Categorías -->
                <h6 class="mb-3">Categorías</h6>
                <div class="mb-4">
                    <a href="{{ route('vendedor.catalogo.index', array_merge(request()->except('categoria'), ['categoria' => 0])) }}" 
                       class="categoria-item {{ $categoria_id == 0 ? 'active' : '' }}">
                        <span>Todos los productos</span>
                        <span class="badge bg-secondary">{{ count($productosAgrupados) }}</span>
                    </a>
                    @foreach($categorias as $categoria)
                    <a href="{{ route('vendedor.catalogo.index', array_merge(request()->except('categoria'), ['categoria' => $categoria->id])) }}" 
                       class="categoria-item {{ $categoria_id == $categoria->id ? 'active' : '' }}">
                        <span>{{ $categoria->nombre }}</span>
                        <span class="badge bg-secondary">{{ $contadores[$categoria->id] ?? 0 }}</span>
                    </a>
                    @endforeach
                </div>
                
                <!-- Ordenar -->
                <h6 class="mb-3">Ordenar por</h6>
                <select class="form-select mb-4" id="ordenSelect" onchange="cambiarOrden()">
                    <option value="nombre" {{ $orden == 'nombre' ? 'selected' : '' }}>Nombre (A-Z)</option>
                    <option value="precio_asc" {{ $orden == 'precio_asc' ? 'selected' : '' }}>Precio (Menor a Mayor)</option>
                    <option value="precio_desc" {{ $orden == 'precio_desc' ? 'selected' : '' }}>Precio (Mayor a Menor)</option>
                    <option value="litros_desc" {{ $orden == 'litros_desc' ? 'selected' : '' }}>Capacidad (Mayor a Menor)</option>
                    <option value="stock" {{ $orden == 'stock' ? 'selected' : '' }}>Stock (Bajo a Alto)</option>
                    <option value="destacados" {{ $orden == 'destacados' ? 'selected' : '' }}>Productos destacados</option>
                </select>
                
                <!-- Reset filtros -->
                <button class="btn btn-outline-secondary w-100 mt-3" onclick="resetFiltros()">
                    <i class="fas fa-redo me-1"></i> Limpiar Filtros
                </button>
            </div>
        </div>
        
        <!-- Productos -->
        <div class="col-lg-9">
            <!-- Título de categoría -->
            @if(!empty($tituloCategoria) && $tituloCategoria != "Todos los productos")
            <div class="mb-3">
                <h2 class="h5 mb-2">{{ $tituloCategoria }}</h2>
                <p class="text-muted small mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Mostrando {{ count($productosAgrupados) }} producto(s) de {{ collect($productosAgrupados)->sum(function($item) { return count($item['variantes']); }) }} variantes
                </p>
            </div>
            @endif
            
            <div class="row g-3" id="productosContainer">
                @forelse($productosAgrupados as $familia => $datos)
                    @php
                        $productoPrincipal = $datos['principal'];
                        $variantes = $datos['variantes'];
                        $esAccesorio = (strpos($productoPrincipal->codigo, 'ACC-') === 0);
                        
                        // Determinar clase de stock
                        $existencias = $productoPrincipal->pivot->existencias ?? 0;
                        $stock_class = '';
                        $stock_text = '';
                        if ($existencias <= 0) {
                            $stock_class = 'agotado';
                            $stock_text = 'Agotado';
                        } elseif ($existencias <= 5) {
                            $stock_class = 'bajo';
                            $stock_text = 'Stock: ' . $existencias;
                        } elseif ($existencias <= 10) {
                            $stock_class = 'medio';
                            $stock_text = 'Stock: ' . $existencias;
                        } else {
                            $stock_class = 'alto';
                            $stock_text = 'Stock: ' . $existencias;
                        }
                        
                        // Obtener imagen
                        $imagenPrincipal = \App\Helpers\ProductoHelper::obtenerImagenProducto($productoPrincipal->codigo);
                        
                        // Verificar si está en oferta
                        $enOferta = $productoPrincipal->en_oferta;
                        $precioOriginal = $productoPrincipal->precio;
                        $precioFinal = $productoPrincipal->precio_final;
                        $porcentajeDescuento = $productoPrincipal->porcentaje_descuento;
                        
                        // FORMATEAR DESCUENTO PARA MOSTRAR COMO ENTERO
                        $descuentoFormateado = \App\Helpers\ProductoHelper::formatoPorcentaje($porcentajeDescuento);
                    @endphp
                    
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="product-card" id="producto-{{ $familia }}">
                            <!-- Badges -->
                            <div class="product-badges">
                                @if($enOferta)
                                <span class="badge-oferta">
                                    <i class="fas fa-tag me-1"></i>-{{ $descuentoFormateado }}%
                                </span>
                                @endif
                                @if($productoPrincipal->destacado && !$enOferta)
                                <span class="badge-destacado">
                                    <i class="fas fa-star me-1"></i>Destacado
                                </span>
                                @endif
                                <span class="badge-categoria">
                                    {{ $productoPrincipal->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                            </div>
                            
                            <!-- Imagen -->
                            <div class="product-img">
                                <img src="{{ $imagenPrincipal }}" 
                                     alt="{{ $productoPrincipal->nombre }}"
                                     id="img-{{ $familia }}"
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22150%22 viewBox=%220 0 200 150%22%3E%3Crect width=%22200%22 height=%22150%22 fill=%22%23f8f9fa%22/%3E%3Ctext x=%22100%22 y=%2275%22 font-family=%22Arial%22 font-size=%2212%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22 fill=%22%236c757d%22%3E{{ substr($productoPrincipal->codigo, 0, 8) }}%3C/text%3E%3C/svg%3E';">
                            </div>
                            
                            <!-- Nombre y código -->
                            <div class="product-name" id="nombre-{{ $familia }}">
                                {{ $productoPrincipal->nombre }}
                                @if($productoPrincipal->color)
                                <small class="d-block text-muted" id="color-text-{{ $familia }}">Color: {{ $productoPrincipal->color->nombre }}</small>
                                @endif
                            </div>
                            
                            <div class="product-code" id="codigo-{{ $familia }}">
                                <i class="fas fa-barcode me-1"></i>
                                {{ $productoPrincipal->codigo }}
                            </div>
                            
                            <!-- Capacidad -->
                            @if($productoPrincipal->litros > 0)
                            <div class="product-capacity">
                                <i class="fas fa-tint me-1"></i>
                                {{ number_format($productoPrincipal->litros) }} litros
                            </div>
                            @endif
                            
                            <!-- Selector de variantes -->
                            @if(count($variantes) > 1 && !$esAccesorio)
                            <div class="selector-variantes">
                                <small><i class="fas fa-list-alt me-1"></i> Opciones:</small>
                                <div class="variantes-container">
                                    @foreach($variantes as $index => $variante)
                                        @php
                                            $sinExistencia = ($variante->pivot->existencias <= 0);
                                            $info = \App\Helpers\ProductoHelper::obtenerInfoVariante($variante);
                                            $esPrincipal = ($variante->id == $productoPrincipal->id);
                                            $claseTipo = 'tipo-' . $info['tipo'];
                                            $claseActivo = $esPrincipal ? 'active' : '';
                                            $claseDisabled = $sinExistencia ? 'disabled' : '';
                                            $imagenVariante = \App\Helpers\ProductoHelper::obtenerImagenProducto($variante->codigo);
                                            $varianteEnOferta = $variante->en_oferta;
                                            
                                            // Formatear descuento de la variante
                                            $varianteDescuento = $variante->porcentaje_descuento ?? 0;
                                            $varianteDescuentoFormateado = \App\Helpers\ProductoHelper::formatoPorcentaje($varianteDescuento);
                                        @endphp
                                        <button type="button" 
                                                class="btn-variante {{ $claseTipo }} {{ $claseActivo }} {{ $claseDisabled }}"
                                                data-variante-id="{{ $variante->id }}"
                                                data-familia="{{ $familia }}"
                                                data-imagen="{{ $imagenVariante }}"
                                                data-codigo="{{ $variante->codigo }}"
                                                data-precio="{{ $variante->precio }}"
                                                data-precio-final="{{ $variante->precio_final }}"
                                                data-en-oferta="{{ $varianteEnOferta ? 'true' : 'false' }}"
                                                data-descuento="{{ $varianteDescuento }}"
                                                data-descuento-formateado="{{ $varianteDescuentoFormateado }}"
                                                data-stock="{{ $variante->pivot->existencias }}"
                                                data-color="{{ $info['nombre'] }}"
                                                data-color-hex="{{ $info['hex'] }}"
                                                onclick="cambiarVariante(this, '{{ $familia }}')"
                                                {{ $sinExistencia ? 'disabled' : '' }}
                                                title="{{ $variante->nombre }} - {{ $info['nombre'] }}">
                                            
                                            @if($info['tipo'] === 'color')
                                            <span class="variante-color" style="background-color: {{ $info['hex'] }}"></span>
                                            @else
                                            <i class="{{ $info['icono'] }} small"></i>
                                            @endif
                                            
                                        
                                            
                                            @if($sinExistencia)
                                            <span class="variante-agotado">✗</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Precio y stock -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="product-price-container" id="price-container-{{ $familia }}">
                                    @if($enOferta)
                                    <div class="d-flex flex-column">
                                        <small class="text-muted text-decoration-line-through">
                                            {{ \App\Helpers\ProductoHelper::formatoPrecio($precioOriginal) }}
                                        </small>
                                        <span class="product-price text-danger fw-bold" id="precio-{{ $familia }}">
                                            {{ \App\Helpers\ProductoHelper::formatoPrecio($precioFinal) }}
                                        </span>
                                    </div>
                                    @else
                                    <span class="product-price" id="precio-{{ $familia }}">
                                        {{ \App\Helpers\ProductoHelper::formatoPrecio($precioOriginal) }}
                                    </span>
                                    @endif
                                </div>
                                
                                <span class="badge-stock {{ $stock_class }}" id="stock-{{ $familia }}">
                                    <i class="fas fa-boxes me-1"></i>
                                    {{ $stock_text }}
                                </span>
                            </div>
                            
                            <!-- Botón de acciones -->
                            <div class="d-flex gap-2">
                                <a href="#" class="btn-detalles flex-grow-1" onclick="verDetalles({{ $productoPrincipal->id }})">
                                    <i class="fas fa-info-circle me-1"></i> Detalles
                                </a>
                                <form action="{{ route('vendedor.pedidos.create') }}" method="GET" class="d-inline">
                                    <input type="hidden" name="producto_id" value="{{ $productoPrincipal->id }}" id="input-{{ $familia }}">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" class="btn btn-success" style="width: 45px;" onclick="event.stopPropagation();" title="Agregar al pedido">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Estado vacío -->
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No se encontraron productos</h5>
                            <p class="text-muted small">
                                @if(!empty($busqueda))
                                No hay productos que coincidan con "{{ $busqueda }}"
                                @elseif($categoria_id > 0)
                                No hay productos en esta categoría.
                                @else
                                No hay productos disponibles en el catálogo.
                                @endif
                            </p>
                            <button class="btn btn-primary" onclick="resetFiltros()">
                                <i class="fas fa-redo me-1"></i> Ver Todos los Productos
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <!-- Contador de productos -->
            @if(count($productosAgrupados) > 0)
            <div class="text-center text-muted small mt-3">
                Mostrando {{ count($productosAgrupados) }} producto(s) de {{ collect($productosAgrupados)->sum(function($item) { return count($item['variantes']); }) }} variantes
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    :root {
        --primary: #7fad39;
        --primary-dark: #5a8a20;
    }
    
    .header-bar {
        background: white;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
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
    
    .alerta-stock {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-left: 4px solid #fd7e14;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 15px;
    }
    
    .filtros-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        position: sticky;
        top: 15px;
    }
    
    .search-box {
        position: relative;
    }
    
    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
    }
    
    .search-box .form-control {
        padding-left: 40px;
    }
    
    .categoria-item {
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none;
        color: #212529;
    }
    
    .categoria-item:hover {
        background: #f8f9fa;
    }
    
    .categoria-item.active {
        background: var(--primary);
        color: white;
    }
    
    .categoria-item.active .badge {
        background: white !important;
        color: var(--primary) !important;
    }
    
    .product-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(127, 173, 57, 0.15);
        border: 1px solid var(--primary);
    }
    
    .product-badges {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        z-index: 1;
    }
    
    .badge-categoria {
        background: var(--primary);
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    
    .badge-destacado {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    
    .badge-oferta {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
    }
    
    .variante-oferta-badge {
        background: #dc3545;
        color: white;
        padding: 2px 4px;
        border-radius: 4px;
        font-size: 0.6rem;
        margin-left: 4px;
        font-weight: 600;
    }
    
    .product-img {
        width: 100%;
        height: 150px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        overflow: hidden;
    }
    
    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
        transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-img img {
        transform: scale(1.05);
    }
    
    .product-name {
        font-size: 0.95rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 5px;
        line-height: 1.3;
        min-height: 2.6rem;
    }
    
    .product-code {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 10px;
    }
    
    .product-capacity {
        font-size: 0.85rem;
        color: var(--primary);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .product-price-container {
        display: flex;
        align-items: baseline;
    }
    
    .product-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary);
    }
    
    .product-price.text-danger {
        color: #dc3545 !important;
    }
    
    .badge-stock {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 500;
    }
    
    .badge-stock.alto { background: #d4edda; color: #155724; }
    .badge-stock.medio { background: #fff3cd; color: #856404; }
    .badge-stock.bajo { background: #f8d7da; color: #721c24; }
    .badge-stock.agotado { background: #e9ecef; color: #6c757d; }
    
    .selector-variantes {
        margin: 10px 0;
    }
    
    .selector-variantes small {
        display: block;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .variantes-container {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .btn-variante {
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .btn-variante:hover {
        border-color: var(--primary);
        background: #f8f9fa;
    }
    
    .btn-variante.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    
    .btn-variante.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .variante-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid #ddd;
    }
    
    .variante-agotado {
        color: #dc3545;
        font-weight: bold;
        margin-left: 2px;
    }
    
    .btn-detalles {
        background: var(--primary);
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: 0.85rem;
    }
    
    .btn-detalles:hover {
        background: var(--primary-dark);
        color: white;
    }
    
    .btn-success {
        background: #28a745;
        border: none;
        transition: all 0.2s ease;
    }
    
    .btn-success:hover {
        background: #218838;
        transform: translateY(-2px);
    }
    
    .empty-state {
        background: white;
        border-radius: 8px;
        padding: 60px 20px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    
    .empty-state i {
        font-size: 3.5rem;
        color: #dee2e6;
        margin-bottom: 20px;
    }
    
    .text-decoration-line-through {
        text-decoration: line-through;
    }
    
    @media (max-width: 992px) {
        .filtros-card {
            position: static;
        }
    }
    
    @media (max-width: 768px) {
        .product-card { padding: 12px; }
        .product-img { height: 120px; }
        .product-price { font-size: 1.2rem; }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Datos de variantes
    const variantesData = @json($productosAgrupados);

    // Función para cambiar orden
    function cambiarOrden() {
        const orden = document.getElementById('ordenSelect').value;
        const params = new URLSearchParams(window.location.search);
        params.set('orden', orden);
        window.location.href = '{{ route("vendedor.catalogo.index") }}?' + params.toString();
    }

    // Función para resetear filtros
    function resetFiltros() {
        window.location.href = '{{ route("vendedor.catalogo.index") }}';
    }

    // Búsqueda en tiempo real
    let searchTimeout;
    const searchInput = document.querySelector('input[name="busqueda"]');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 500);
        });
    }

    // Función para formatear porcentaje (para JavaScript)
    function formatoPorcentajeJS(valor) {
        return Math.round(parseFloat(valor));
    }

    // Función para cambiar variante (CORREGIDA para manejar ofertas)
    function cambiarVariante(btn, familia) {
        const varianteId = btn.getAttribute('data-variante-id');
        const datosVariante = JSON.parse(JSON.stringify(variantesData[familia]?.variantes?.find(v => v.id == varianteId)));
        
        if (!datosVariante) return;
        
        // Actualizar botones activos
        document.querySelectorAll(`[data-familia="${familia}"]`).forEach(b => {
            b.classList.remove('active');
        });
        btn.classList.add('active');
        
        // Actualizar imagen
        const imgElement = document.getElementById(`img-${familia}`);
        if (imgElement) {
            imgElement.src = btn.getAttribute('data-imagen') || datosVariante.imagen;
        }
        
        // Actualizar nombre
        const nombreElement = document.getElementById(`nombre-${familia}`);
        if (nombreElement) {
            const nombreText = btn.getAttribute('data-nombre') || datosVariante.nombre;
            nombreElement.innerHTML = nombreText;
            if (btn.getAttribute('data-color')) {
                const colorSpan = document.createElement('small');
                colorSpan.className = 'd-block text-muted';
                colorSpan.id = `color-text-${familia}`;
                colorSpan.textContent = `Color: ${btn.getAttribute('data-color')}`;
                nombreElement.appendChild(colorSpan);
            }
        }
        
        // Actualizar código
        const codigoElement = document.getElementById(`codigo-${familia}`);
        if (codigoElement) {
            codigoElement.innerHTML = `<i class="fas fa-barcode me-1"></i> ${btn.getAttribute('data-codigo') || datosVariante.codigo}`;
        }
        
        // Actualizar precio (considerando oferta)
        const precioContainer = document.getElementById(`price-container-${familia}`);
        const precioFinal = parseFloat(btn.getAttribute('data-precio-final') || btn.getAttribute('data-precio') || datosVariante.precio_final || datosVariante.precio);
        const precioOriginal = parseFloat(btn.getAttribute('data-precio') || datosVariante.precio);
        const enOferta = btn.getAttribute('data-en-oferta') === 'true' || datosVariante.en_oferta;
        
        if (precioContainer) {
            if (enOferta) {
                precioContainer.innerHTML = `
                    <div class="d-flex flex-column">
                        <small class="text-muted text-decoration-line-through">${formatoPrecioJS(precioOriginal)}</small>
                        <span class="product-price text-danger fw-bold" id="precio-${familia}">${formatoPrecioJS(precioFinal)}</span>
                    </div>
                `;
            } else {
                precioContainer.innerHTML = `<span class="product-price" id="precio-${familia}">${formatoPrecioJS(precioOriginal)}</span>`;
            }
        }
        
        // Actualizar stock
        const stockElement = document.getElementById(`stock-${familia}`);
        if (stockElement) {
            const stock = parseInt(btn.getAttribute('data-stock') || datosVariante.pivot?.existencias || 0);
            let claseStock = '';
            let textoStock = '';
            
            if (stock <= 0) {
                claseStock = 'agotado';
                textoStock = 'Agotado';
            } else if (stock <= 5) {
                claseStock = 'bajo';
                textoStock = 'Stock: ' + stock;
            } else if (stock <= 10) {
                claseStock = 'medio';
                textoStock = 'Stock: ' + stock;
            } else {
                claseStock = 'alto';
                textoStock = 'Stock: ' + stock;
            }
            
            stockElement.className = `badge-stock ${claseStock}`;
            stockElement.innerHTML = `<i class="fas fa-boxes me-1"></i> ${textoStock}`;
        }
        
        // Actualizar badge de oferta
        const badgeOferta = document.querySelector(`#producto-${familia} .badge-oferta`);
        if (badgeOferta) {
            if (enOferta) {
                const descuento = btn.getAttribute('data-descuento-formateado') || formatoPorcentajeJS(btn.getAttribute('data-descuento') || 0);
                badgeOferta.style.display = 'flex';
                badgeOferta.innerHTML = `<i class="fas fa-tag me-1"></i>-${descuento}%`;
            } else {
                badgeOferta.style.display = 'none';
            }
        }
        
        // Actualizar input del formulario
        const inputElement = document.getElementById(`input-${familia}`);
        if (inputElement) {
            inputElement.value = varianteId;
        }
        
        // Efecto visual
        btn.style.transform = 'scale(1.1)';
        setTimeout(() => {
            btn.style.transform = 'scale(1)';
        }, 200);
    }

    // Función para formatear precio
    function formatoPrecioJS(precio) {
        return '$' + parseFloat(precio).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    // Función para ver detalles
    function verDetalles(productoId) {
        window.location.href = '{{ url("vendedor/producto") }}/' + productoId + '/detalles';
    }

    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
        // Efectos de hover para tarjetas
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Manejar errores de imágenes
        document.querySelectorAll('.product-img img').forEach(img => {
            img.addEventListener('error', function() {
                const codigo = this.alt.split(' ')[0] || 'PRODUCTO';
                this.src = `data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22150%22 viewBox=%220 0 200 150%22%3E%3Crect width=%22200%22 height=%22150%22 fill=%22%23f8f9fa%22/%3E%3Ctext x=%22100%22 y=%2275%22 font-family=%22Arial%22 font-size=%2212%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22 fill=%22%236c757d%22%3E${codigo.substring(0,8)}%3C/text%3E%3C/svg%3E`;
            });
        });
        
        // Atajo de teclado para búsqueda (Ctrl+F)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                document.querySelector('input[name="busqueda"]').focus();
                document.querySelector('input[name="busqueda"]').select();
            }
        });
        
        // Verificar si hay un producto en la URL (para mostrar mensaje)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('agregado')) {
            Swal.fire({
                icon: 'success',
                title: 'Producto seleccionado',
                text: 'El producto se ha agregado para crear el pedido',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
</script>
@endsection