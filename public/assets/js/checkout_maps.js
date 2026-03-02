// assets/js/checkout_maps.js - VERSIÓN COMPLETA Y CORREGIDA

$(document).ready(function() {
    console.log('✅ checkout_maps.js cargado');

    // ===== 1. DECLARAR TODAS LAS VARIABLES =====
    const $btnVerificar = $('#verificar-cobertura');
    const $form = $('#form-checkout');
    const $btnFinalizar = $('#btn-finalizar');

    // Campos del formulario
    const $nombre = $('#nombre');
    const $telefono = $('#telefono');
    const $direccion = $('#direccion');
    const $ciudad = $('#ciudad');
    const $estado = $('#estado');
    const $codigoPostal = $('#codigo_postal');

    // ===== 2. FUNCIÓN DE LIMPIEZA =====
    function limpiarCoberturaSiEsNecesario() {
        const hayDatosFormulario = $nombre.val() || $telefono.val() || $direccion.val();
        
        if (!hayDatosFormulario && window.coberturaData && window.coberturaData.valido) {
            $.ajax({
                url: '/checkout/limpiar-cobertura',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    window.coberturaData = null;
                    $('#btn-finalizar').prop('disabled', true);
                },
                error: function(xhr) {
                    console.warn('⚠️ No se pudo limpiar cobertura:', xhr.status);
                    window.coberturaData = null;
                    $('#btn-finalizar').prop('disabled', true);
                }
            });
        }
    }

    // ===== 3. EJECUTAR LIMPIEZA =====
    limpiarCoberturaSiEsNecesario();

    // ===== 4. MOSTRAR COBERTURA DESDE SESIÓN =====
    if (typeof window.coberturaData !== 'undefined' && window.coberturaData && window.coberturaData.valido) {
        const hayDatosFormulario = $nombre.val() || $telefono.val() || $direccion.val();
        
        if (hayDatosFormulario) {
            Swal.fire({
                icon: 'success',
                title: '¡Cobertura pre-verificada!',
                html: `
                    <div class="text-start">
                        <p class="mb-2">Tu dirección ya fue verificada anteriormente.</p>
                        <p class="mb-1"><strong>Sucursal:</strong> ${window.coberturaData.sucursal_nombre || 'Ecatepec'}</p>
                        <p class="mb-1"><strong>Distancia:</strong> ${window.coberturaData.distancia || ''} km</p>
                    </div>
                `,
                confirmButtonColor: '#7fad39',
                timer: 4000
            });
            
            $('#btn-finalizar').prop('disabled', false);
        }
    }

    // ===== 5. VALIDACIÓN EN TIEMPO REAL =====
    $nombre.on('input', function() {
        let valor = $(this).val();
        let soloLetras = valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        $(this).val(soloLetras);
    });

    $telefono.on('input', function() {
        let valor = $(this).val();
        let soloNumeros = valor.replace(/\D/g, '');
        if (soloNumeros.length > 10) {
            soloNumeros = soloNumeros.substring(0, 10);
        }
        $(this).val(soloNumeros);
    });

    $direccion.on('input', function() {
        let valor = $(this).val();
        let direccionValida = valor.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\.,#\-]/g, '');
        $(this).val(direccionValida);
    });

    $ciudad.on('input', function() {
        let valor = $(this).val();
        let soloLetras = valor.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        $(this).val(soloLetras);
    });

    $codigoPostal.on('input', function() {
        let valor = $(this).val();
        let soloNumeros = valor.replace(/\D/g, '');
        if (soloNumeros.length > 5) {
            soloNumeros = soloNumeros.substring(0, 5);
        }
        $(this).val(soloNumeros);
    });

    // ===== 6. FUNCIÓN PARA VALIDAR CAMPOS =====
    function validarCamposDireccion() {
        let isValid = true;
        const campos = [
            { element: $direccion, nombre: 'dirección' },
            { element: $ciudad, nombre: 'ciudad' },
            { element: $estado, nombre: 'estado' },
            { element: $codigoPostal, nombre: 'código postal' }
        ];

        campos.forEach(campo => {
            const $el = campo.element;
            if (!$el.val() || $el.val().trim() === '') {
                $el.addClass('is-invalid');
                isValid = false;
            } else {
                $el.removeClass('is-invalid');
            }
        });

        return isValid;
    }

    // ===== 7. FUNCIÓN DE AUTOCOMPLETADO DE GOOGLE =====
    async function initGoogleAutocomplete() {
        const input = document.getElementById('direccion');
        if (!input) return;

        if (typeof google === 'undefined' || !google.maps) {
            console.warn('⏳ Google Maps no está listo, reintentando en 1s...');
            setTimeout(initGoogleAutocomplete, 1000);
            return;
        }

        try {
            const { Autocomplete } = await google.maps.importLibrary("places");
            
            const autocomplete = new Autocomplete(input, {
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

                if (ciudad) $ciudad.val(ciudad);
                if (estado) $estado.val(estado);
                if (cp) $codigoPostal.val(cp);
            });

            console.log('✅ Autocompletado de Google inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar autocomplete:', error);
        }
    }

    initGoogleAutocomplete();

    // ===== 8. EVENTO CLICK VERIFICAR COBERTURA =====
    $btnVerificar.on('click', function() {
        if (!validarCamposDireccion()) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor completa todos los campos de dirección',
                confirmButtonColor: '#7fad39'
            });
            return;
        }

        $(this).addClass('loading');
        $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>Verificando...');

        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (!csrfToken) {
            console.error('❌ No se encontró token CSRF');
            Swal.fire({
                icon: 'error',
                title: 'Error de seguridad',
                text: 'Recarga la página o contacta a soporte',
                confirmButtonColor: '#7fad39'
            });
            resetButton();
            return;
        }

        const data = {
            direccion: $direccion.val().trim(),
            ciudad: $ciudad.val().trim(),
            estado: $estado.val(),
            codigo_postal: $codigoPostal.val().trim()
        };

        $.ajax({
            url: '/cliente/checkout/verificar-cobertura',
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    if (response.valido) {
                        $('#btn-finalizar').prop('disabled', false);
                        
                        Swal.fire({
                            icon: 'success',
                            title: '✅ ¡Cobertura confirmada!',
                            html: `
                                <div class="text-start" style="font-size: 0.95rem;">
                                    <p class="mb-3">${response.message || 'Tu dirección está dentro de nuestra zona de cobertura'}</p>
                                    <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; border-left: 4px solid #7fad39;">
                                        <p class="mb-2"><i class="fas fa-store text-success me-2"></i><strong>Sucursal:</strong> ${response.sucursal_nombre}</p>
                                        <p class="mb-2"><i class="fas fa-road text-success me-2"></i><strong>Distancia:</strong> ${response.distancia} km</p>
                                        <p class="mb-0"><i class="fas fa-map-marker-alt text-success me-2"></i><strong>Dirección:</strong> ${response.sucursal_direccion}</p>
                                    </div>
                                </div>
                            `,
                            confirmButtonColor: '#7fad39',
                            confirmButtonText: 'Continuar con el pago',
                            timer: 5000,
                            timerProgressBar: true
                        });
                    } else {
                        $('#btn-finalizar').prop('disabled', true);
                        
                        Swal.fire({
                            icon: 'error',
                            title: '❌ Sin cobertura de envío',
                            html: `
                                <div class="text-start">
                                    <p class="mb-3">${response.message || 'No hay cobertura en esta dirección'}</p>
                                    <div style="background: #fff3cd; padding: 12px; border-radius: 8px; border-left: 4px solid #ffc107;">
                                        <p class="mb-1"><i class="fas fa-store text-warning me-2"></i><strong>Sucursal más cercana:</strong> Ecatepec</p>
                                        <p class="mb-0"><i class="fas fa-road text-warning me-2"></i><strong>Distancia:</strong> ${response.distancia} km</p>
                                    </div>
                                    <p class="mt-3 text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Radio de cobertura: 8 km
                                    </p>
                                </div>
                            `,
                            confirmButtonColor: '#7fad39',
                            confirmButtonText: 'Entendido'
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al verificar cobertura',
                        confirmButtonColor: '#7fad39'
                    });
                }
            },
            error: function(xhr) {
                console.error('❌ Error:', xhr);
                let mensaje = 'Error al verificar cobertura';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                } else if (xhr.status === 419) {
                    mensaje = 'Sesión expirada. Recarga la página.';
                    setTimeout(() => window.location.reload(), 2000);
                } else if (xhr.status === 422) {
                    mensaje = 'Datos de dirección incompletos';
                } else if (xhr.status === 500) {
                    mensaje = 'Error en el servidor. Intenta más tarde';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensaje,
                    confirmButtonColor: '#7fad39'
                });
                
                $('#btn-finalizar').prop('disabled', true);
            },
            complete: function() {
                resetButton();
            }
        });
        
        function resetButton() {
            $btnVerificar.removeClass('loading');
            $btnVerificar.html('<i class="fas fa-search-location me-2"></i>Verificar Cobertura de Envío');
        }
    });

    // ===== 9. VALIDACIÓN AL ENVIAR FORMULARIO =====
    $form.on('submit', function(e) {
        const btnDisabled = $('#btn-finalizar').prop('disabled');
        
        if (btnDisabled) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Cobertura no verificada',
                text: 'Debes verificar la cobertura de envío antes de continuar',
                confirmButtonColor: '#7fad39'
            });
        }
    });

    // ===== 10. BOTÓN CAMBIAR DIRECCIÓN =====
    $('#cambiar-direccion').on('click', function() {
        $.ajax({
            url: '/cliente/checkout/limpiar-cobertura',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                location.reload();
            }
        });
    });

    console.log('✅ checkout_maps.js inicializado correctamente');
});