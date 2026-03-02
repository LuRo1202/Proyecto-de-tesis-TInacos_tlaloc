@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Sucursal {{ session('sucursal_nombre') }}</title>
    
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
            padding: 15px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Header Compacto */
        .header-bar {
            background: white;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .header-title {
            margin: 0;
            color: var(--dark);
            font-weight: 600;
            font-size: 1.2rem;
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
        
        .header-subtitle {
            font-size: 0.85rem;
            color: var(--gray);
            margin-top: 4px;
        }
        
        .header-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        /* Botones Compactos */
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
        
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, var(--primary-dark), #4a7a18);
            color: white;
        }
        
        .btn-secondary-custom {
            background: white;
            color: var(--gray);
            border: 1px solid var(--light-gray);
        }
        
        .btn-secondary-custom:hover {
            background: var(--light);
            color: var(--dark);
            border-color: var(--gray);
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--success), #218838);
            color: white;
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, var(--danger), #c82333);
            color: white;
        }
        
        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning), #e0a800);
            color: #000;
        }
        
        /* Encabezado del Card */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            margin-bottom: 15px;
            background: white;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--light-gray);
            padding: 12px 15px;
            border-radius: 8px 8px 0 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-title i {
            color: var(--primary);
        }
        
        /* Formulario */
        .form-label {
            font-weight: 500;
            color: var(--dark);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            border: 1px solid var(--light-gray);
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(127, 173, 57, 0.1);
        }
        
        /* Preview de imagen */
        .preview-imagen {
            width: 100%;
            height: 200px;
            border: 2px dashed var(--light-gray);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: var(--light);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .preview-imagen:hover {
            border-color: var(--primary);
        }
        
        .preview-imagen img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .preview-imagen-texto {
            color: var(--gray);
            text-align: center;
            padding: 20px;
        }
        
        .preview-imagen-texto i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--light-gray);
        }
        
        .btn-eliminar-imagen {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.2s ease;
        }
        
        .btn-eliminar-imagen:hover {
            background: var(--danger);
            transform: scale(1.1);
        }
        
        /* Checkboxes */
        .form-check {
            margin-bottom: 5px;
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .form-check-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(127, 173, 57, 0.1);
        }
        
        .form-check-label {
            font-size: 0.9rem;
            color: var(--dark);
            font-weight: 500;
        }
        
        /* Input group */
        .input-group-text {
            background: var(--light);
            border: 1px solid var(--light-gray);
            color: var(--gray);
            font-weight: 500;
        }
        
        /* Info text */
        .form-text {
            font-size: 0.8rem;
            color: var(--gray);
        }
        
        /* Acciones del formulario */
        .form-actions {
            background: var(--light);
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            border-top: 1px solid var(--light-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        /* Badges */
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        
        .badge-primary {
            background: rgba(127, 173, 57, 0.1);
            color: var(--primary);
            border: 1px solid rgba(127, 173, 57, 0.2);
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 70px;
                padding: 12px;
            }
        }
        
        @media (max-width: 992px) {
            .header-bar {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            
            .header-actions {
                justify-content: center;
            }
            
            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .form-actions > div {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            
            .btn-custom {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
                padding: 10px;
            }
            
            .header-title {
                font-size: 1.1rem;
            }
            
            .card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
            
            .preview-imagen {
                height: 180px;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                margin-left: 0;
                padding: 8px;
            }
            
            .header-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-custom {
                width: 100%;
                justify-content: center;
            }
            
            .form-actions > div .btn-custom {
                width: 100%;
                justify-content: center;
            }
            
            .preview-imagen {
                height: 160px;
            }
        }
        
        /* Animaciones Suaves */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Scrollbar Personalizado */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--light-gray);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }
        
        /* Hover effects para formularios */
        .form-control:hover, .form-select:hover {
            border-color: var(--primary-light);
        }
        
        /* Required field indicator */
        .required::after {
            content: " *";
            color: var(--danger);
        }
        
        /* SweetAlert2 Estilos Mejorados */
        .swal2-popup {
            border-radius: 12px !important;
            padding: 2rem !important;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif !important;
        }
        
        .swal2-title {
            font-size: 1.3rem !important;
            font-weight: 600 !important;
            color: var(--dark) !important;
            margin-bottom: 1rem !important;
        }
        
        .swal2-html-container {
            font-size: 1rem !important;
            color: var(--gray) !important;
            line-height: 1.5 !important;
        }
        
        .swal2-icon {
            margin-bottom: 1rem !important;
            transform: scale(1.2) !important;
        }
        
        .swal2-confirm, .swal2-cancel {
            border-radius: 6px !important;
            padding: 0.6rem 1.5rem !important;
            font-weight: 500 !important;
            font-size: 0.9rem !important;
            transition: all 0.2s ease !important;
            border: none !important;
        }
        
        .swal2-confirm {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
        }
        
        .swal2-confirm:hover {
            background: linear-gradient(135deg, var(--primary-dark), #4a7a18) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1) !important;
        }
        
        .swal2-cancel {
            background: white !important;
            color: var(--gray) !important;
            border: 1px solid var(--light-gray) !important;
        }
        
        .swal2-cancel:hover {
            background: var(--light) !important;
            color: var(--dark) !important;
            border-color: var(--gray) !important;
            transform: translateY(-1px) !important;
        }
        
        .swal2-success {
            border-color: var(--success) !important;
            color: var(--success) !important;
        }
        
        .swal2-error {
            border-color: var(--danger) !important;
            color: var(--danger) !important;
        }
        
        .swal2-warning {
            border-color: var(--warning) !important;
            color: var(--warning) !important;
        }
        
        .swal2-info {
            border-color: var(--info) !important;
            color: var(--info) !important;
        }
        
        /* Animaciones para SweetAlert */
        @keyframes swal2-show {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        @keyframes swal2-hide {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(0.9);
                opacity: 0;
            }
        }
        
        .swal2-show {
            animation: swal2-show 0.3s ease-out !important;
        }
        
        .swal2-hide {
            animation: swal2-hide 0.3s ease-out !important;
        }
    </style>
</head>
<body>
    @include('gerente.layouts.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-edit me-2"></i>Editar Producto
                    <span class="sucursal-badge">
                        <i class="fas fa-store"></i> {{ session('sucursal_nombre') }}
                    </span>
                </h1>
                <div class="header-subtitle">
                    Gerente: {{ auth()->user()->nombre ?? 'Gerente' }}
                </div>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('gerente.productos') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('gerente.productos.nuevo') }}" class="btn-custom btn-primary-custom">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-box"></i> Editar Producto
                </h5>
                <span class="badge badge-primary">
                    <i class="fas fa-hashtag"></i> {{ $producto->codigo }}
                </span>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('gerente.productos.update', $producto->id) }}" enctype="multipart/form-data" id="formEditarProducto">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Columna izquierda - Información del producto -->
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Código del Producto</label>
                                    <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror" required 
                                           value="{{ old('codigo', $producto->codigo) }}" maxlength="50"
                                           placeholder="Ej: TANQ-1000">
                                    <div class="form-text">Código único para identificar el producto</div>
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Nombre del Producto</label>
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" required 
                                           value="{{ old('nombre', $producto->nombre) }}"
                                           placeholder="Ej: Tanque Rotoplas 1000L">
                                    <div class="form-text">Nombre descriptivo del producto</div>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Capacidad (litros)</label>
                                    <input type="number" name="litros" class="form-control @error('litros') is-invalid @enderror" 
                                           value="{{ old('litros', $producto->litros) }}" min="0" step="1"
                                           placeholder="Ej: 1000">
                                    <div class="form-text">Capacidad en litros</div>
                                    @error('litros')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror">
                                        <option value="">Sin categoría</option>
                                        @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" 
                                            {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Selecciona una categoría</div>
                                    @error('categoria_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Existencias</label>
                                    <input type="number" name="existencias" class="form-control @error('existencias') is-invalid @enderror" 
                                           value="{{ old('existencias', $producto->existencias) }}" min="0" step="1"
                                           placeholder="Ej: 10">
                                    <div class="form-text">Cantidad disponible en inventario de tu sucursal</div>
                                    @error('existencias')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="precio" class="form-control @error('precio') is-invalid @enderror" required 
                                               value="{{ old('precio', $producto->precio) }}" min="0" step="0.01"
                                               placeholder="0.00">
                                    </div>
                                    <div class="form-text">Precio de venta en pesos mexicanos</div>
                                    @error('precio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Estado del Producto</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="activo" 
                                                   id="activo" {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="activo">Activo</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="destacado" 
                                                   id="destacado" {{ old('destacado', $producto->destacado) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="destacado">Destacado</label>
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> 
                                        <small>Activo: disponible para venta | Destacado: aparece en página principal</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna derecha - Imagen del producto -->
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Imagen del Producto</label>
                                <div class="imagen-container">
                                    <div class="preview-imagen mb-3" id="previewImagen">
                                        @if($imagen_existe)
                                        <img src="{{ asset('assets/img/productos/' . $producto->codigo . '.jpg') }}?t={{ time() }}" 
                                             alt="{{ $producto->nombre }}" class="img-fluid">
                                        <button type="button" class="btn-eliminar-imagen" onclick="eliminarImagen()" 
                                                title="Eliminar imagen">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @else
                                        <div class="preview-imagen-texto">
                                            <i class="fas fa-image fa-2x"></i>
                                            <p class="mb-1 mt-2">Sin imagen</p>
                                            <small>Selecciona una imagen JPG</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <input type="file" name="imagen" class="form-control @error('imagen') is-invalid @enderror" id="inputImagen" 
                                       accept=".jpg,.jpeg">
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Solo se aceptan imágenes JPG. Tamaño máximo: 2MB
                                </div>
                                <input type="hidden" name="eliminar_imagen" id="eliminarImagen" value="0">
                                @error('imagen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="info-box bg-light p-3 rounded">
                                <h6 class="mb-2"><i class="fas fa-lightbulb me-2"></i>Recomendaciones</h6>
                                <ul class="mb-0 small text-muted">
                                    <li>Usa imágenes claras y de buena calidad</li>
                                    <li>Formato recomendado: JPG 600x400px</li>
                                    <li>Fondo blanco o transparente</li>
                                    <li>Muestra el producto completo</li>
                                    <li>La imagen se guardará como: <strong id="ejemploImagen">{{ $producto->codigo }}.jpg</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones del formulario -->
                    <div class="form-actions">
                        <a href="{{ route('gerente.productos') }}" class="btn-custom btn-secondary-custom">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        
                        <div class="d-flex gap-2">
                            <button type="button" class="btn-custom btn-danger-custom" 
                                    onclick="confirmarEliminacion()">
                                <i class="fas fa-trash"></i> Eliminar Producto
                            </button>
                            
                            <button type="submit" class="btn-custom btn-success-custom" id="btnGuardar">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Preview de imagen al seleccionar archivo
        document.getElementById('inputImagen').addEventListener('change', function(e) {
            const preview = document.getElementById('previewImagen');
            const file = e.target.files[0];
            
            if (file) {
                // Validar tamaño (2MB máximo)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        title: 'Archivo muy grande',
                        text: 'La imagen no puede superar los 2MB',
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Aceptar',
                        allowOutsideClick: false
                    });
                    this.value = '';
                    return;
                }
                
                // Validar extensión (solo JPG)
                const extension = file.name.split('.').pop().toLowerCase();
                if (!['jpg', 'jpeg'].includes(extension)) {
                    Swal.fire({
                        title: 'Formato no válido',
                        text: 'Solo se permiten imágenes JPG (.jpg, .jpeg)',
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Aceptar',
                        allowOutsideClick: false
                    });
                    this.value = '';
                    return;
                }
                
                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Previsualización" class="img-fluid">
                        <button type="button" class="btn-eliminar-imagen" onclick="eliminarImagen()" 
                                title="Eliminar imagen">
                            <i class="fas fa-times"></i>
                        </button>`;
                    document.getElementById('eliminarImagen').value = "0";
                    
                    // Actualizar ejemplo de nombre de imagen
                    const codigo = document.querySelector('input[name="codigo"]').value;
                    document.getElementById('ejemploImagen').textContent = codigo ? codigo + '.jpg' : 'CODIGO.jpg';
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Eliminar imagen
        function eliminarImagen() {
            Swal.fire({
                title: '¿Eliminar imagen?',
                text: '¿Estás seguro de eliminar la imagen del producto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('previewImagen').innerHTML = `
                        <div class="preview-imagen-texto">
                            <i class="fas fa-image fa-2x"></i>
                            <p class="mb-1 mt-2">Sin imagen</p>
                            <small>Selecciona una imagen JPG</small>
                        </div>`;
                    document.getElementById('eliminarImagen').value = "1";
                    document.getElementById('inputImagen').value = "";
                    
                    Swal.fire({
                        title: 'Imagen eliminada',
                        text: 'La imagen se ha eliminado correctamente',
                        icon: 'success',
                        confirmButtonColor: '#7fad39',
                        confirmButtonText: 'Aceptar',
                        allowOutsideClick: false
                    });
                }
            });
        }
        
        // Actualizar ejemplo de nombre de imagen cuando cambia el código
        document.querySelector('input[name="codigo"]').addEventListener('input', function() {
            const codigo = this.value.trim();
            document.getElementById('ejemploImagen').textContent = codigo ? codigo + '.jpg' : 'CODIGO.jpg';
        });
        
        // Confirmar eliminación del producto
        function confirmarEliminacion() {
            Swal.fire({
                title: '¿Eliminar producto?',
                html: `¿Estás seguro de eliminar el producto <strong>{{ $producto->nombre }}</strong>?<br><small>Esta acción no se puede deshacer.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            
                            fetch(`/gerente/productos/eliminar/{{ $producto->id }}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: '¡Eliminado!',
                                        text: data.message || 'Producto eliminado permanentemente.',
                                        icon: 'success',
                                        confirmButtonColor: '#7fad39',
                                        confirmButtonText: 'Ir a productos'
                                    }).then(() => {
                                        window.location.href = '{{ route("gerente.productos") }}';
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: data.error || 'No se pudo eliminar el producto',
                                        icon: 'error',
                                        confirmButtonColor: '#dc3545',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error de Conexión',
                                    text: 'No se pudo conectar con el servidor.',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545',
                                    confirmButtonText: 'Aceptar'
                                });
                            });
                        }
                    });
                }
            });
        }
        
        // Validar formulario antes de enviar
        document.getElementById('formEditarProducto').addEventListener('submit', function(e) {
            const precio = document.querySelector('input[name="precio"]').value;
            const codigo = document.querySelector('input[name="codigo"]').value;
            const nombre = document.querySelector('input[name="nombre"]').value;
            
            let errores = [];
            
            // Validar campos requeridos
            if (codigo.trim() === '') {
                errores.push('El código del producto es obligatorio');
            }
            
            if (nombre.trim() === '') {
                errores.push('El nombre del producto es obligatorio');
            }
            
            if (parseFloat(precio) <= 0) {
                errores.push('El precio debe ser mayor a $0.00');
            }
            
            // Validar formato del código (solo letras, números y guiones)
            const codigoRegex = /^[A-Za-z0-9-]+$/;
            if (codigo.trim() !== '' && !codigoRegex.test(codigo)) {
                errores.push('El código solo puede contener letras, números y guiones');
            }
            
            // Si hay errores, mostrar alerta y prevenir envío
            if (errores.length > 0) {
                e.preventDefault();
                
                let mensajeError = '';
                errores.forEach(error => {
                    mensajeError += `• ${error}<br>`;
                });
                
                Swal.fire({
                    title: 'Error en el formulario',
                    html: mensajeError,
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Corregir',
                    allowOutsideClick: false
                });
                return false;
            }
        });
        
        // Mostrar errores de validación del servidor
        @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            let mensajeError = '';
            @foreach($errors->all() as $error)
                mensajeError += `• {{ $error }}<br>`;
            @endforeach
            
            Swal.fire({
                title: 'Error en el formulario',
                html: mensajeError,
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Corregir',
                allowOutsideClick: false
            });
        });
        @endif
        
        // Mostrar mensaje de éxito si existe
        @if(session('swal'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '{{ session('swal.title') }}',
                text: '{{ session('swal.message') }}',
                icon: '{{ session('swal.type') }}',
                confirmButtonColor: '#7fad39',
                confirmButtonText: 'Aceptar',
                allowOutsideClick: false
            });
        });
        @endif
        
        // Al cargar la página, mostrar datos del producto
        document.addEventListener('DOMContentLoaded', function() {
            // Si el producto está inactivo, mostrar advertencia
            @if(!$producto->activo)
            setTimeout(() => {
                Swal.fire({
                    title: 'Producto Inactivo',
                    text: 'Este producto está marcado como inactivo y no aparece en el catálogo.',
                    icon: 'warning',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Entendido',
                    allowOutsideClick: false,
                    toast: true,
                    position: 'top-end',
                    timer: 4000,
                    showConfirmButton: false
                });
            }, 500);
            @endif
        });
    </script>
</body>
</html>