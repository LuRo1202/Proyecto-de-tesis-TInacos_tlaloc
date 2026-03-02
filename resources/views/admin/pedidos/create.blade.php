@php
    use App\Models\Sucursal;
    $sucursales = Sucursal::where('activa', true)->get();
    $sucursalActual = session('sucursal_activa') ?? $sucursales->first();
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Pedido Manual - Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
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
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Header Compacto */
        .header-bar {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .header-title {
            margin: 0;
            color: var(--dark);
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .header-title i {
            color: var(--primary);
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
        
        /* Verificar Cobertura Botón */
        .verificar-cobertura-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .verificar-cobertura-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }
        
        .verificar-cobertura-btn.loading {
            background: var(--gray);
        }
        
        .verificar-cobertura-btn.loading i {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Cobertura Verificada Box */
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
        
        /* Productos */
        .producto-item {
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            background: white;
            position: relative;
            transition: all 0.2s ease;
        }
        
        .producto-item:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(127, 173, 57, 0.1);
        }
        
        .btn-remove-producto {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.7rem;
            transition: all 0.2s ease;
        }
        
        .btn-remove-producto:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        
        .resumen-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-align: right;
        }
        
        /* Card */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            margin-bottom: 15px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid var(--light-gray);
            font-weight: 600;
            padding: 12px 15px;
            border-radius: 8px 8px 0 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .card-header h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark);
            font-size: 0.85rem;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .form-label i {
            color: var(--primary);
            width: 16px;
        }
        
        .form-control, .form-select {
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            padding: 8px 12px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(127, 173, 57, 0.1);
            outline: none;
        }
        
        .badge-sucursal {
            background: rgba(127, 173, 57, 0.1);
            color: var(--primary-dark);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-left: 10px;
        }
        
        .sucursal-selector {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid var(--light-gray);
        }
        
        @media (max-width: 1200px) {
            .main-content {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
            }
            
            .header-bar {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            
            .header-actions {
                justify-content: center;
            }
            
            .card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                margin-left: 0;
            }
            
            .header-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-custom {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    @include('admin.layouts.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        <div class="header-bar">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-cart-plus me-2"></i>Crear Pedido Manual
                </h1>
                <p class="text-muted mb-0 small">Bienvenido, {{ auth()->user()->nombre ?? 'Administrador' }}</p>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('admin.pedidos') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Selector de Sucursal (solo admin) -->
        @if(auth()->user()->rol === 'admin')
        <div class="sucursal-selector">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-store text-primary"></i>
                <span class="fw-semibold">Sucursal para verificar cobertura:</span>
                <select class="form-select form-select-sm" style="width: auto;" id="sucursal-selector">
                    @foreach($sucursales as $s)
                        <option value="{{ $s->id }}" {{ $s->id == $sucursalActual->id ? 'selected' : '' }}>
                            {{ $s->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.pedidos.store') }}" id="form-pedido">
            @csrf
            <input type="hidden" name="sucursal_id" id="sucursal_id" value="{{ $sucursalActual->id }}">
            
            <!-- Información del Cliente y Cobertura -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-truck"></i> Información de Envío y Cobertura</h5>
                    <span class="badge-sucursal" id="badge-sucursal">
                        <i class="fas fa-store"></i> <span id="sucursal-nombre-badge">{{ $sucursalActual->nombre }}</span>
                    </span>
                </div>
                <div class="card-body">
                    <!-- Indicador de cobertura verificada -->
                    <div id="cobertura-verificada-box" class="cobertura-verificada-box" style="display: none;">
                        <h5><i class="fas fa-check-circle"></i> Cobertura verificada</h5>
                        <div class="cobertura-detail-item">
                            <i class="fas fa-store"></i>
                            <div><strong>Sucursal:</strong> <span id="sucursal-nombre"></span></div>
                        </div>
                        <div class="cobertura-detail-item">
                            <i class="fas fa-location-dot"></i>
                            <div><strong>Dirección sucursal:</strong> <span id="sucursal-direccion"></span></div>
                        </div>
                        <div class="cobertura-detail-item">
                            <i class="fas fa-road"></i>
                            <div><strong>Distancia:</strong> <span id="distancia"></span> km</div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-sm btn-outline-success" id="cambiar-direccion">
                                <i class="fas fa-pen me-1"></i>Cambiar dirección
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_nombre"><i class="fas fa-user"></i> Nombre completo *</label>
                                <input type="text" class="form-control @error('cliente_nombre') is-invalid @enderror" 
                                       id="cliente_nombre" name="cliente_nombre" required
                                       placeholder="Ej: Jose Pérez" value="{{ old('cliente_nombre') }}">
                                @error('cliente_nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_telefono"><i class="fas fa-phone"></i> Teléfono *</label>
                                <input type="tel" class="form-control @error('cliente_telefono') is-invalid @enderror" 
                                       id="cliente_telefono" name="cliente_telefono" required
                                       placeholder="55 1234 5678" value="{{ old('cliente_telefono') }}">
                                @error('cliente_telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label for="cliente_direccion"><i class="fas fa-map-marker-alt"></i> Dirección completa *</label>
                                <input type="text" class="form-control @error('cliente_direccion') is-invalid @enderror" 
                                       id="cliente_direccion" name="cliente_direccion" required
                                       placeholder="Calle, número, colonia" value="{{ old('cliente_direccion') }}">
                                @error('cliente_direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cliente_ciudad"><i class="fas fa-city"></i> Ciudad *</label>
                                <input type="text" class="form-control @error('cliente_ciudad') is-invalid @enderror" 
                                       id="cliente_ciudad" name="cliente_ciudad" required
                                       placeholder="Ej: Ecatepec" value="{{ old('cliente_ciudad') }}">
                                @error('cliente_ciudad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cliente_estado"><i class="fas fa-flag"></i> Estado *</label>
                                <select class="form-select @error('cliente_estado') is-invalid @enderror" 
                                        id="cliente_estado" name="cliente_estado" required>
                                    <option value="">Seleccionar estado</option>
                                    <option value="Estado de México" {{ old('cliente_estado') == 'Estado de México' ? 'selected' : '' }}>Estado de México</option>
                                    <option value="Ciudad de México" {{ old('cliente_estado') == 'Ciudad de México' ? 'selected' : '' }}>Ciudad de México</option>
                                    <option value="San Luis Potosí" {{ old('cliente_estado') == 'San Luis Potosí' ? 'selected' : '' }}>San Luis Potosí</option>
                                    <option value="Nuevo León" {{ old('cliente_estado') == 'Nuevo León' ? 'selected' : '' }}>Nuevo León</option>
                                </select>
                                @error('cliente_estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="codigo_postal"><i class="fas fa-mail-bulk"></i> Código Postal *</label>
                                <input type="text" class="form-control @error('codigo_postal') is-invalid @enderror" 
                                       id="codigo_postal" name="codigo_postal" required
                                       placeholder="Ej: 55000" maxlength="5" value="{{ old('codigo_postal') }}">
                                @error('codigo_postal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-grid">
                                <button type="button" id="verificar-cobertura" class="verificar-cobertura-btn">
                                    <i class="fas fa-search-location"></i> Verificar Cobertura de Envío
                                </button>
                                <small class="text-muted mt-2 text-center">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Verificamos si entregamos en tu zona (radio de 5-8km de nuestras sucursales)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <div class="form-group">
                                <label for="notas"><i class="fas fa-sticky-note"></i> Notas adicionales</label>
                                <textarea class="form-control" id="notas" name="notas" rows="3" 
                                          placeholder="Instrucciones especiales para la entrega...">{{ old('notas') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos del Pedido -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-boxes"></i> Productos del Pedido</h5>
                    <button type="button" class="btn-custom btn-primary-custom" onclick="agregarProducto()">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                </div>
                <div class="card-body">
                    <div id="productos-container"></div>
                    
                    <div class="row align-items-center mt-4">
                        <div class="col-md-6">
                            <h5><i class="fas fa-receipt me-2"></i>Total del Pedido</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="resumen-total" id="total-pedido">$0.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="d-flex justify-content-between gap-2 mt-3">
                <a href="{{ route('admin.pedidos') }}" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" name="crear_pedido" id="btn-crear-pedido" class="btn-custom btn-success-custom" disabled>
                    <i class="fas fa-save"></i> Crear Pedido
                </button>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Variables globales
        let productoCount = 0;
        let coberturaVerificada = false;
        const productos = @json($productos);
        const csrfToken = '{{ csrf_token() }}';
        const sucursalId = document.getElementById('sucursal_id').value;

        // Inicializar autocomplete de Google Maps
        function initAutocomplete() {
            const direccionInput = document.getElementById('cliente_direccion');
            if (direccionInput) {
                const autocomplete = new google.maps.places.Autocomplete(direccionInput, {
                    types: ['address'],
                    componentRestrictions: {country: 'mx'}
                });
                
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place.formatted_address) {
                        direccionInput.value = place.formatted_address;
                        
                        if (place.address_components) {
                            place.address_components.forEach(component => {
                                if (component.types.includes('locality')) {
                                    document.getElementById('cliente_ciudad').value = component.long_name;
                                }
                                if (component.types.includes('administrative_area_level_1')) {
                                    const estadoSelect = document.getElementById('cliente_estado');
                                    for (let i = 0; i < estadoSelect.options.length; i++) {
                                        if (estadoSelect.options[i].text === component.long_name) {
                                            estadoSelect.value = estadoSelect.options[i].value;
                                            break;
                                        }
                                    }
                                }
                                if (component.types.includes('postal_code')) {
                                    document.getElementById('codigo_postal').value = component.long_name;
                                }
                            });
                        }
                    }
                });
            }
        }

        // Verificar cobertura
        $('#verificar-cobertura').click(function() {
            const direccion = $('#cliente_direccion').val().trim();
            const ciudad = $('#cliente_ciudad').val().trim();
            const estado = $('#cliente_estado').val().trim();
            const codigo_postal = $('#codigo_postal').val().trim();
            
            if (!direccion || !ciudad || !estado) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos requeridos',
                    text: 'Complete dirección, ciudad y estado antes de verificar cobertura',
                    confirmButtonColor: '#7fad39'
                });
                return;
            }
            
            const btn = $(this);
            btn.addClass('loading').html('<i class="fas fa-spinner fa-spin"></i> Verificando...');
            
            $.ajax({
                url: '{{ route("admin.cobertura.verificar") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    direccion: direccion,
                    ciudad: ciudad,
                    estado: estado,
                    codigo_postal: codigo_postal,
                    sucursal_id: $('#sucursal_id').val()
                },
                success: function(response) {
                    console.log('Respuesta:', response);
                    
                    if (response.valido) {
                        coberturaVerificada = true;
                        $('#btn-crear-pedido').prop('disabled', false);
                        
                        // Mostrar información de cobertura
                        $('#sucursal-nombre').text(response.sucursal_nombre);
                        $('#sucursal-direccion').text(response.sucursal_direccion);
                        $('#distancia').text(response.distancia);
                        $('#cobertura-verificada-box').show();
                        
                        // ✅ ACTUALIZAR EL BADGE CON LA SUCURSAL VERIFICADA
                        $('#sucursal-nombre-badge').text(response.sucursal_nombre);
                        
                        // Agregar campos ocultos
                        $('#form-pedido').append(`
                            <input type="hidden" name="sucursal_nombre" value="${response.sucursal_nombre}">
                            <input type="hidden" name="sucursal_direccion" value="${response.sucursal_direccion}">
                            <input type="hidden" name="distancia_km" value="${response.distancia}">
                        `);
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Cobertura verificada!',
                            html: `
                                <p><strong>Sucursal:</strong> ${response.sucursal_nombre}</p>
                                <p><strong>Distancia:</strong> ${response.distancia} km</p>
                            `,
                            confirmButtonColor: '#7fad39'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sin cobertura',
                            text: response.message,
                            confirmButtonColor: '#7fad39'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseJSON);
                    
                    let errorMessage = 'No se pudo verificar la cobertura';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonColor: '#7fad39'
                    });
                },
                complete: function() {
                    btn.removeClass('loading').html('<i class="fas fa-search-location"></i> Verificar Cobertura de Envío');
                }
            });
        });

        // Cambiar dirección
        $('#cambiar-direccion').click(function() {
            coberturaVerificada = false;
            $('#btn-crear-pedido').prop('disabled', true);
            $('#cobertura-verificada-box').hide();
            
            // Eliminar campos ocultos
            $('input[name="sucursal_nombre"], input[name="sucursal_direccion"], input[name="distancia_km"]').remove();
        });

        // Cambiar sucursal (solo admin) - ✅ CORREGIDO
        $('#sucursal-selector').change(function() {
            const nuevaSucursalId = $(this).val();
            const nuevaSucursalNombre = $(this).find('option:selected').text();
            
            $('#sucursal_id').val(nuevaSucursalId);
            
            // ✅ ACTUALIZAR EL BADGE CON LA NUEVA SUCURSAL
            $('#sucursal-nombre-badge').text(nuevaSucursalNombre);
            
            // Si ya había cobertura verificada, resetear
            if (coberturaVerificada) {
                $('#cambiar-direccion').click();
            }
        });

        // Funciones de productos
        function agregarProducto() {
            const container = document.getElementById('productos-container');
            const index = productoCount++;
            
            let options = '<option value="">Seleccionar producto</option>';
            productos.forEach(p => {
                options += `<option value="${p.id}" data-precio="${p.precio}" data-existencias="${p.existencias}" data-nombre="${p.nombre}">${p.codigo} - ${p.nombre} (${p.litros}L)</option>`;
            });
            
            const html = `
                <div class="producto-item" id="producto-${index}">
                    <button type="button" class="btn-remove-producto" onclick="eliminarProducto(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                    
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label">Producto *</label>
                            <select name="productos[${index}]" class="form-select select-producto" 
                                    onchange="actualizarProducto(${index})" required>
                                ${options}
                            </select>
                            <small class="text-muted" id="existencias-${index}"></small>
                        </div>
                        
                        <div class="col-lg-2">
                            <label class="form-label">Cantidad *</label>
                            <input type="number" name="cantidades[${index}]" class="form-control cantidad" 
                                   id="cantidad-${index}" value="1" min="1" 
                                   onchange="calcularSubtotal(${index})" required>
                        </div>
                        
                        <div class="col-lg-2">
                            <label class="form-label">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control precio-unitario" 
                                       id="precio-${index}" readonly value="0.00">
                            </div>
                        </div>
                        
                        <div class="col-lg-2">
                            <label class="form-label">Subtotal</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control subtotal" 
                                       id="subtotal-${index}" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
            calcularTotal();
        }

        function actualizarProducto(index) {
            const select = document.querySelector(`#producto-${index} .select-producto`);
            const precioInput = document.getElementById(`precio-${index}`);
            const existenciasSpan = document.getElementById(`existencias-${index}`);
            
            if (select.value) {
                const option = select.options[select.selectedIndex];
                const precio = option.dataset.precio;
                const existencias = option.dataset.existencias;
                
                precioInput.value = parseFloat(precio).toFixed(2);
                
                if (existencias <= 5) {
                    existenciasSpan.innerHTML = `<span class="text-danger">⚠️ Solo ${existencias} disponibles</span>`;
                } else {
                    existenciasSpan.innerHTML = `${existencias} disponibles`;
                }
            } else {
                precioInput.value = '0.00';
                existenciasSpan.innerHTML = '';
            }
            
            calcularSubtotal(index);
        }

        function calcularSubtotal(index) {
            const precio = parseFloat(document.getElementById(`precio-${index}`).value) || 0;
            const cantidad = parseInt(document.getElementById(`cantidad-${index}`).value) || 0;
            const subtotal = precio * cantidad;
            
            document.getElementById(`subtotal-${index}`).value = subtotal.toFixed(2);
            calcularTotal();
        }

        function calcularTotal() {
            let total = 0;
            
            for (let i = 0; i < productoCount; i++) {
                const subtotal = parseFloat(document.getElementById(`subtotal-${i}`)?.value) || 0;
                total += subtotal;
            }
            
            document.getElementById('total-pedido').textContent = '$' + total.toFixed(2);
        }

        function eliminarProducto(index) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`producto-${index}`).remove();
                    calcularTotal();
                }
            });
        }

        // Inicializar
        $(document).ready(function() {
            if (typeof google !== 'undefined') {
                initAutocomplete();
            }
            
            // Agregar primer producto
            agregarProducto();
            
            @if(session('swal'))
                Swal.fire({
                    icon: '{{ session('swal')['type'] }}',
                    title: '{{ session('swal')['title'] }}',
                    text: '{{ session('swal')['message'] }}',
                    confirmButtonColor: '#7fad39'
                });
            @endif
        });
    </script>
</body>
</html>