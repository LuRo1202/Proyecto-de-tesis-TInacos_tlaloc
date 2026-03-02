@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Producto - Sucursal {{ session('sucursal_nombre') }}</title>
    
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
        
        /* Info box */
        .info-box {
            background: var(--light);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary);
        }
        
        /* Required field indicator */
        .required::after {
            content: " *";
            color: var(--danger);
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
        
        /* Placeholder styling */
        ::placeholder {
            color: #adb5bd !important;
            opacity: 1;
        }
        
        /* Estilo para switches */
        .form-switch .form-check-input {
            height: 1.25em;
            width: 2.5em;
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
                    <i class="fas fa-plus me-2"></i>Nuevo Producto
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
            </div>
        </div>

        <!-- Información del formulario -->
        <div class="info-box">
            <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Información importante</h6>
            <p class="mb-0 small">Todos los campos marcados con <span class="text-danger">*</span> son obligatorios. Los productos se mostrarán en el catálogo de ventas de tu sucursal.</p>
        </div>

        <!-- Formulario principal -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-box"></i> Información del Producto
                </h5>
                <span class="badge bg-light text-dark">
                    <i class="fas fa-asterisk text-danger fa-xs"></i> Campos obligatorios
                </span>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('gerente.productos.store') }}" enctype="multipart/form-data" id="formNuevoProducto">
                    @csrf
                    
                    <div class="row">
                        <!-- Columna izquierda - Información del producto -->
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Código del Producto</label>
                                    <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror" required 
                                           maxlength="50" id="codigo" value="{{ old('codigo') }}"
                                           placeholder="Ej: TANQ-1000, ACC-TAPA">
                                    <div class="form-text">Código único para identificar el producto</div>
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Nombre del Producto</label>
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" required 
                                           id="nombre" value="{{ old('nombre') }}"
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
                                           id="litros" min="0" step="1" value="{{ old('litros', 0) }}"
                                           placeholder="Ej: 1000">
                                    <div class="form-text">Capacidad en litros (opcional)</div>
                                    @error('litros')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" id="categoria_id">
                                        <option value="">Sin categoría</option>
                                        @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Selecciona una categoría (opcional)</div>
                                    @error('categoria_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Existencias</label>
                                    <input type="number" name="existencias" class="form-control @error('existencias') is-invalid @enderror" 
                                           id="existencias" value="{{ old('existencias', 0) }}" min="0" step="1"
                                           placeholder="Ej: 10">
                                    <div class="form-text">Cantidad inicial en inventario de tu sucursal</div>
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
                                               id="precio" min="0.01" step="0.01" value="{{ old('precio') }}"
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
                                                   id="activo" {{ old('activo', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="activo">Activo</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="destacado" 
                                                   id="destacado" {{ old('destacado') ? 'checked' : '' }}>
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
                                        <div class="preview-imagen-texto">
                                            <i class="fas fa-image fa-2x"></i>
                                            <p class="mb-1 mt-2">Sin imagen</p>
                                            <small>Selecciona una imagen JPG</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="file" name="imagen" class="form-control @error('imagen') is-invalid @enderror" id="inputImagen" 
                                       accept=".jpg,.jpeg">
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Solo se aceptan imágenes JPG. Tamaño máximo: 2MB
                                </div>
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
                                    <li>La imagen se guardará como: <strong id="ejemploImagen">CODIGO.jpg</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones del formulario -->
                    <div class="form-actions">
                        <a href="{{ route('gerente.productos') }}" class="btn-custom btn-secondary-custom">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        
                        <div>
                            <button type="reset" class="btn-custom btn-secondary-custom me-2">
                                <i class="fas fa-redo"></i> Limpiar
                            </button>
                            
                            <button type="submit" class="btn-custom btn-success-custom" id="btnGuardar">
                                <i class="fas fa-save"></i> Crear Producto
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
                        confirmButtonText: 'Aceptar'
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
                        confirmButtonText: 'Aceptar'
                    });
                    this.value = '';
                    return;
                }
                
                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Previsualización" class="img-fluid">
                        <div class="preview-imagen-texto" style="display: none;">
                            <i class="fas fa-image fa-2x"></i>
                            <p class="mb-1 mt-2">Sin imagen</p>
                            <small>Selecciona una imagen JPG</small>
                        </div>`;
                }
                reader.readAsDataURL(file);
            } else {
                // Restaurar vista sin imagen
                preview.innerHTML = `
                    <div class="preview-imagen-texto">
                        <i class="fas fa-image fa-2x"></i>
                        <p class="mb-1 mt-2">Sin imagen</p>
                        <small>Selecciona una imagen JPG</small>
                    </div>`;
            }
        });
        
        // Auto-generar código sugerido basado en el nombre
        document.getElementById('nombre').addEventListener('input', function() {
            const nombre = this.value.trim();
            const codigoInput = document.getElementById('codigo');
            const ejemploImagen = document.getElementById('ejemploImagen');
            
            // Si el código está vacío, sugerir uno
            if (codigoInput.value === '' && nombre !== '') {
                // Convertir a mayúsculas, eliminar espacios y caracteres especiales
                let codigoSugerido = nombre.toUpperCase()
                    .replace(/[^A-Z0-9\s]/g, '')  // Eliminar caracteres especiales
                    .replace(/\s+/g, '-')         // Reemplazar espacios con guiones
                    .substring(0, 20);            // Limitar longitud
                
                // Si el código sugerido es válido, mostrarlo
                if (codigoSugerido.length >= 3) {
                    codigoInput.value = codigoSugerido;
                    if (ejemploImagen) {
                        ejemploImagen.textContent = codigoSugerido + '.jpg';
                    }
                }
            }
        });
        
        // Mostrar ejemplo de cómo se guardará la imagen
        document.getElementById('codigo').addEventListener('input', function() {
            const codigo = this.value.trim();
            const ejemploImagen = document.getElementById('ejemploImagen');
            if (ejemploImagen) {
                ejemploImagen.textContent = codigo ? codigo + '.jpg' : 'CODIGO.jpg';
            }
        });
        
        // Validar formulario antes de enviar
        document.getElementById('formNuevoProducto').addEventListener('submit', function(e) {
            const precio = document.getElementById('precio').value;
            const codigo = document.getElementById('codigo').value;
            const nombre = document.getElementById('nombre').value;
            
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
                    confirmButtonText: 'Corregir'
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
                confirmButtonText: 'Corregir'
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
                confirmButtonText: 'Aceptar'
            });
        });
        @endif
        
        // Restaurar botón si hay error de validación
        document.addEventListener('DOMContentLoaded', function() {
            const btnGuardar = document.getElementById('btnGuardar');
            // No hay necesidad de restaurar porque no lo deshabilitamos
        });
    </script>
</body>
</html>