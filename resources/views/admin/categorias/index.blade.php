<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Tanques Tláloc</title>
    
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
        
        .categoria-card {
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-left: 4px solid var(--primary);
        }
        
        .categoria-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-color: var(--primary-light);
        }
        
        .categoria-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .categoria-nombre {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.1rem;
            margin: 0;
        }
        
        .badge-count {
            background: var(--primary);
            color: white;
            border-radius: 12px;
            padding: 4px 10px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .categoria-info {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 15px;
        }
        
        .categoria-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 8px 8px 0 0;
        }
        
        .modal-title {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .form-control {
            border-radius: 6px;
            border: 1px solid var(--light-gray);
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(127, 173, 57, 0.1);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
                }
        
        /* Scrollbar Personalizado -  */
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
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    
        
        .empty-state-icon {
            font-size: 3rem;
            color: var(--light-gray);
            margin-bottom: 15px;
        }
        
        .empty-state-title {
            color: var(--gray);
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .empty-state-text {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 20px;
            max-width: 300px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .swal2-popup {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif !important;
            border-radius: 12px !important;
        }
        
        .swal2-title {
            color: var(--dark) !important;
            font-size: 1.3rem !important;
            font-weight: 600 !important;
        }
        
        .swal2-html-container {
            color: var(--gray) !important;
            font-size: 0.95rem !important;
        }
        
        .swal2-confirm {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
            border: none !important;
            border-radius: 6px !important;
            padding: 0.6rem 1.5rem !important;
            font-weight: 500 !important;
        }
        
        .swal2-cancel {
            background: white !important;
            color: var(--gray) !important;
            border: 1px solid var(--light-gray) !important;
            border-radius: 6px !important;
            padding: 0.6rem 1.5rem !important;
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
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
                padding: 10px;
            }
            
            .header-title {
                font-size: 1.1rem;
            }
            
            .categoria-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .categoria-actions {
                flex-direction: column;
            }
            
            .categoria-actions .btn-custom {
                width: 100%;
                justify-content: center;
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
            
            .empty-state {
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>
    @include('admin.layouts.sidebar')
    
    <div class="main-content">
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-tags me-2"></i>Gestión de Categorías
                </h1>
            </div>
            
            <div class="header-actions">
                <button class="btn-custom btn-primary-custom" data-bs-toggle="modal" data-bs-target="#nuevaCategoriaModal">
                    <i class="fas fa-plus"></i> Nueva Categoría
                </button>
            </div>
        </div>

        <div class="row">
            @forelse($categorias as $categoria)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="categoria-card">
                    <div class="categoria-header">
                        <h5 class="categoria-nombre">{{ $categoria->nombre }}</h5>
                        <span class="badge-count">
                            {{ $categoria->productos_count }} productos
                        </span>
                    </div>
                    
                    <div class="categoria-info">
                        <i class="fas fa-box me-1"></i>
                        {{ $categoria->productos_count }} productos activos
                    </div>
                    
                    <div class="categoria-actions">
                        <button class="btn-custom btn-secondary-custom btn-editar-categoria" 
                                data-id="{{ $categoria->id }}"
                                data-nombre="{{ $categoria->nombre }}">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn-custom btn-eliminar-categoria"
                                style="background: linear-gradient(135deg, var(--danger), #c82333); color: white;"
                                data-id="{{ $categoria->id }}"
                                data-nombre="{{ $categoria->nombre }}"
                                data-productos="{{ $categoria->productos_count }}">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h5 class="empty-state-title">No hay categorías registradas</h5>
                    <p class="empty-state-text">
                        Crea tu primera categoría para organizar tus productos
                    </p>
                    <button class="btn-custom btn-primary-custom" data-bs-toggle="modal" data-bs-target="#nuevaCategoriaModal">
                        <i class="fas fa-plus"></i> Crear Primera Categoría
                    </button>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Nueva Categoría -->
    <div class="modal fade" id="nuevaCategoriaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Nueva Categoría
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.categorias.store') }}" id="formNuevaCategoria">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Categoría</label>
                            <input type="text" name="nombre" class="form-control" required 
                                   placeholder="Ej: Tinacos, Cisternas, Accesorios...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-custom btn-secondary-custom" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn-custom btn-success-custom">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Categoría -->
    <div class="modal fade" id="editarCategoriaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Editar Categoría
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.categorias.update') }}" id="formEditarCategoria">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editCategoriaId">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Categoría</label>
                            <input type="text" name="nombre" id="editCategoriaNombre" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-custom btn-secondary-custom" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn-custom btn-success-custom">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('swal'))
                Swal.fire({
                    title: '{{ session('swal')['title'] }}',
                    html: '{{ session('swal')['message'] }}',
                    icon: '{{ session('swal')['type'] }}',
                    confirmButtonText: 'Aceptar'
                });
            @endif

            // Configurar botones de editar
            document.querySelectorAll('.btn-editar-categoria').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    
                    document.getElementById('editCategoriaId').value = id;
                    document.getElementById('editCategoriaNombre').value = nombre;
                    
                    const modal = new bootstrap.Modal(document.getElementById('editarCategoriaModal'));
                    modal.show();
                });
            });

            // Configurar botones de eliminar
            document.querySelectorAll('.btn-eliminar-categoria').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const productos = parseInt(this.getAttribute('data-productos'));
                    
                    if (productos > 0) {
                        Swal.fire({
                            title: 'No se puede eliminar',
                            html: `La categoría <strong>"${nombre}"</strong> tiene <strong>${productos}</strong> productos asociados.<br><br>Primero debes eliminar o reasignar los productos antes de eliminar la categoría.`,
                            icon: 'error',
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }
                    
                    Swal.fire({
                        title: '¿Eliminar Categoría?',
                        html: `¿Estás seguro de eliminar la categoría <strong>"${nombre}"</strong>?<br><br>Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Enviar formulario de eliminación
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("admin.categorias.destroy") }}';
                            
                            const csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            
                            const method = document.createElement('input');
                            method.type = 'hidden';
                            method.name = '_method';
                            method.value = 'DELETE';
                            
                            const idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.name = 'id';
                            idInput.value = id;
                            
                            form.appendChild(csrf);
                            form.appendChild(method);
                            form.appendChild(idInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

            // Validar formulario de nueva categoría
            const formNuevaCategoria = document.getElementById('formNuevaCategoria');
            if (formNuevaCategoria) {
                formNuevaCategoria.addEventListener('submit', function(e) {
                    const nombreInput = this.querySelector('input[name="nombre"]');
                    if (nombreInput.value.trim() === '') {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Campo Requerido',
                            text: 'Por favor ingresa un nombre para la categoría',
                            icon: 'warning',
                            confirmButtonText: 'Aceptar'
                        });
                        nombreInput.focus();
                    }
                });
            }

            // Validar formulario de editar categoría
            const formEditarCategoria = document.getElementById('formEditarCategoria');
            if (formEditarCategoria) {
                formEditarCategoria.addEventListener('submit', function(e) {
                    const nombreInput = this.querySelector('input[name="nombre"]');
                    if (nombreInput.value.trim() === '') {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Campo Requerido',
                            text: 'Por favor ingresa un nombre para la categoría',
                            icon: 'warning',
                            confirmButtonText: 'Aceptar'
                        });
                        nombreInput.focus();
                    } else {
                        e.preventDefault();
                        const nombre = nombreInput.value;
                        
                        Swal.fire({
                            title: '¿Guardar Cambios?',
                            html: `¿Deseas guardar los cambios en la categoría?<br><br>Nuevo nombre: <strong>"${nombre}"</strong>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, guardar',
                            cancelButtonText: 'Cancelar',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                formEditarCategoria.submit();
                            }
                        });
                    }
                });
            }

            // Resetear modales al cerrar
            const nuevaCategoriaModal = document.getElementById('nuevaCategoriaModal');
            if (nuevaCategoriaModal) {
                nuevaCategoriaModal.addEventListener('hidden.bs.modal', function () {
                    const form = this.querySelector('form');
                    if (form) form.reset();
                });
            }
            
            const editarCategoriaModal = document.getElementById('editarCategoriaModal');
            if (editarCategoriaModal) {
                editarCategoriaModal.addEventListener('hidden.bs.modal', function () {
                    const form = this.querySelector('form');
                    if (form) form.reset();
                });
            }
        });
    </script>
</body>
</html>