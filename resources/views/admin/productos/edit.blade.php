@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
        
        .header-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
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
        
        .input-group-text {
            background: var(--light);
            border: 1px solid var(--light-gray);
            color: var(--gray);
            font-weight: 500;
        }
        
        .form-text {
            font-size: 0.8rem;
            color: var(--gray);
        }
        
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
        
        .badge-sucursal {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid rgba(25, 118, 210, 0.2);
        }
        
        .sucursal-selector {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid var(--light-gray);
        }
        
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
            
            .preview-imagen {
                height: 160px;
            }
        }
        
        .required::after {
            content: " *";
            color: var(--danger);
        }
    </style>
</head>
<body>
    @include('admin.layouts.sidebar')
    
    <div class="main-content">
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-edit me-2"></i>Editar Producto
                </h1>
                <p class="text-muted mb-0 small">Bienvenido, {{ auth()->user()->nombre ?? 'Administrador' }}</p>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('admin.productos', $queryParams) }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('admin.productos.nuevo', ['sucursal_id' => $sucursal_id]) }}" class="btn-custom btn-primary-custom">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="card-title">
                        <i class="fas fa-box"></i> Editar Producto: {{ $producto->nombre }}
                    </h5>
                    <span class="badge-sucursal">
                        <i class="fas fa-store"></i> 
                        @php
                            $sucursal_actual = \App\Models\Sucursal::find($sucursal_id);
                        @endphp
                        {{ $sucursal_actual->nombre ?? 'Sucursal no especificada' }}
                    </span>
                </div>
                <span class="badge badge-primary">
                    <i class="fas fa-hashtag"></i> {{ $producto->codigo }}
                </span>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.productos.update', $producto->id) }}" enctype="multipart/form-data" id="formEditarProducto">
                    @csrf
                    @method('PUT')
                    
                    <!-- Pasar los parámetros de filtro actuales para mantenerlos después de la redirección -->
                    @foreach($queryParams as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <!-- Sucursal seleccionada -->
                    <input type="hidden" name="sucursal_id" value="{{ $sucursal_id }}">
                    
                    <div class="sucursal-selector">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-store text-primary"></i>
                            <span class="fw-semibold">Gestionando inventario para:</span>
                            <span class="badge bg-info text-white">
                                {{ $sucursal_actual->nombre ?? 'Sucursal no especificada' }}
                            </span>
                        </div>
                        <div class="form-text mt-2">
                            <i class="fas fa-info-circle"></i> Las existencias se actualizarán para esta sucursal.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Código del Producto</label>
                                    <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror" 
                                           value="{{ old('codigo', $producto->codigo) }}" maxlength="50" required
                                           placeholder="Ej: TIN-225">
                                    @error('codigo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Código único para identificar el producto</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Nombre del Producto</label>
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                           value="{{ old('nombre', $producto->nombre) }}" required
                                           placeholder="Ej: Tinaco 225 lts">
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Nombre descriptivo del producto</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Capacidad (litros)</label>
                                    <input type="number" name="litros" class="form-control @error('litros') is-invalid @enderror" 
                                           value="{{ old('litros', $producto->litros) }}" min="0" step="1"
                                           placeholder="Ej: 225">
                                    @error('litros')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Capacidad en litros (0 para accesorios)</div>
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
                                    @error('categoria_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Existencias</label>
                                    <input type="number" name="existencias" class="form-control @error('existencias') is-invalid @enderror" 
                                           value="{{ old('existencias', $existencias) }}" min="0" step="1"
                                           placeholder="Ej: 10">
                                    @error('existencias')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Cantidad disponible en inventario para esta sucursal</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="precio" class="form-control @error('precio') is-invalid @enderror" 
                                               value="{{ old('precio', $producto->precio) }}" min="0" step="0.01" required
                                               placeholder="0.00">
                                    </div>
                                    @error('precio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Precio de venta en pesos mexicanos</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Estado del Producto</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="activo" 
                                                   id="activo" value="1" {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="activo">Activo</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="destacado" 
                                                   id="destacado" value="1" {{ old('destacado', $producto->destacado) ? 'checked' : '' }}>
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
                        
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label class="form-label">Imagen del Producto</label>
                                <div class="imagen-container">
                                    <div class="preview-imagen mb-3" id="previewImagen">
                                        @php
                                            $imagen_path = public_path('assets/img/productos/' . $producto->codigo . '.jpg');
                                            $imagen_existe = file_exists($imagen_path);
                                        @endphp
                                        
                                        @if($imagen_existe)
                                        <img src="{{ asset('assets/img/productos/' . $producto->codigo . '.jpg') }}?t={{ time() }}" 
                                             alt="{{ $producto->nombre }}" 
                                             class="img-fluid">
                                        <button type="button" class="btn-eliminar-imagen" onclick="marcarEliminarImagen()" 
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
                                @error('imagen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Solo se aceptan imágenes JPG. Tamaño máximo: 2MB
                                </div>
                                <input type="hidden" name="eliminar_imagen" id="eliminarImagen" value="0">
                            </div>
                            
                            <div class="info-box bg-light p-3 rounded">
                                <h6 class="mb-2"><i class="fas fa-lightbulb me-2"></i>Recomendaciones</h6>
                                <ul class="mb-0 small text-muted">
                                    <li>Usa imágenes claras y de buena calidad</li>
                                    <li>Formato recomendado: JPG 600x400px</li>
                                    <li>Fondo blanco o transparente</li>
                                    <li>Muestra el producto completo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="{{ route('admin.productos', $queryParams) }}" class="btn-custom btn-secondary-custom">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        
                        <div class="d-flex gap-2">
                            <button type="button" class="btn-custom btn-danger-custom" 
                                    onclick="confirmarEliminacion({{ $producto->id }}, '{{ $producto->nombre }}')">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <script>
        // Preview de imagen
        document.getElementById('inputImagen').addEventListener('change', function(e) {
            const preview = document.getElementById('previewImagen');
            const file = e.target.files[0];
            
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        title: 'Archivo muy grande',
                        text: 'La imagen no puede superar los 2MB',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                    this.value = '';
                    return;
                }
                
                const extension = file.name.split('.').pop().toLowerCase();
                if (!['jpg', 'jpeg'].includes(extension)) {
                    Swal.fire({
                        title: 'Formato no válido',
                        text: 'Solo se permiten imágenes JPG (.jpg, .jpeg)',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Previsualización" class="img-fluid">
                        <button type="button" class="btn-eliminar-imagen" onclick="marcarEliminarImagen()" 
                                title="Eliminar imagen">
                            <i class="fas fa-times"></i>
                        </button>`;
                    document.getElementById('eliminarImagen').value = "0";
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Marcar para eliminar imagen
        function marcarEliminarImagen() {
            Swal.fire({
                title: '¿Eliminar imagen?',
                text: '¿Estás seguro de eliminar la imagen del producto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
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
                        confirmButtonColor: '#7fad39'
                    });
                }
            });
        }
        
        // Confirmar eliminación del producto
        function confirmarEliminacion(id, nombre) {
            Swal.fire({
                title: '¿Eliminar producto?',
                html: `¿Estás seguro de eliminar el producto <strong>${nombre}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    var url = '{{ route("admin.productos.destroy", ":id") }}';
                    url = url.replace(':id', id);
                    
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#7fad39'
                            }).then(() => {
                                window.location.href = '{{ route("admin.productos") }}' + window.location.search;
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.error || 'No se pudo eliminar el producto',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error de Conexión',
                            text: 'No se pudo conectar con el servidor',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        }
        
        // Envío del formulario
        document.getElementById('formEditarProducto').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const codigo = document.querySelector('input[name="codigo"]').value.trim();
            const nombre = document.querySelector('input[name="nombre"]').value.trim();
            const precio = parseFloat(document.querySelector('input[name="precio"]').value);
            
            let errores = [];
            
            if (codigo === '') errores.push('El código del producto es obligatorio');
            if (nombre === '') errores.push('El nombre del producto es obligatorio');
            if (isNaN(precio) || precio <= 0) errores.push('El precio debe ser mayor a $0.00');
            
            if (errores.length > 0) {
                Swal.fire({
                    title: 'Error en el formulario',
                    html: errores.map(e => `• ${e}`).join('<br>'),
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
            
            Swal.fire({
                title: '¿Guardar cambios?',
                text: 'Se actualizará la información del producto',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
        
        {{-- ✅ CORREGIDO: Mensajes específicos para productos --}}
        @if(session('swal_producto'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '{{ session('swal_producto')['type'] }}',
                title: '{{ session('swal_producto')['title'] }}',
                text: '{{ session('swal_producto')['message'] }}',
                confirmButtonColor: '#7fad39'
            });
        });
        @endif

        {{-- Mantener swal genérico solo por compatibilidad --}}
        @if(session('swal'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '{{ session('swal')['type'] }}',
                title: '{{ session('swal')['title'] }}',
                text: '{{ session('swal')['message'] }}',
                confirmButtonColor: '#7fad39'
            });
        });
        @endif
    </script>
</body>
</html>