// assets/js/contacto.js

$(document).ready(function() {
    console.log('Contacto.js cargado correctamente');
    
    // ===== VALIDACIÓN EN TIEMPO REAL - SOLO LETRAS PARA NOMBRE =====
    $('#nombre').on('input', function() {
        // Permitir solo letras y espacios mientras escribe
        const soloLetras = $(this).val().replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        $(this).val(soloLetras);
    });
    
    // ===== VALIDACIÓN EN TIEMPO REAL - SOLO NÚMEROS PARA TELÉFONO =====
    $('#telefono').on('input', function() {
        // Permitir solo números mientras escribe
        const soloNumeros = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(soloNumeros);
        
        // Limitar a 10 dígitos
        if ($(this).val().length > 10) {
            $(this).val($(this).val().substring(0, 10));
        }
    });
    
    // ===== VALIDACIÓN DEL FORMULARIO AL ENVIAR =====
    $('form').on('submit', function(e) {
        let isValid = true;
        const $form = $(this);
        
        // Validar nombre (solo letras)
        const nombre = $('#nombre').val().trim();
        const nombreRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        if (!nombre) {
            isValid = false;
            showFieldError($('#nombre'), 'El nombre es obligatorio');
        } else if (!nombreRegex.test(nombre)) {
            isValid = false;
            showFieldError($('#nombre'), 'El nombre solo puede contener letras y espacios');
        } else if (nombre.length < 3) {
            isValid = false;
            showFieldError($('#nombre'), 'El nombre debe tener al menos 3 caracteres');
        } else {
            clearError($('#nombre'));
        }
        
        // Validar email
        const email = $('#email').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            isValid = false;
            showFieldError($('#email'), 'El correo es obligatorio');
        } else if (!emailRegex.test(email)) {
            isValid = false;
            showFieldError($('#email'), 'Ingresa un correo electrónico válido');
        } else {
            clearError($('#email'));
        }
        
        // Validar teléfono (solo números)
        const telefono = $('#telefono').val().trim();
        const soloNumeros = telefono.replace(/\D/g, '');
        if (!telefono) {
            isValid = false;
            showFieldError($('#telefono'), 'El teléfono es obligatorio');
        } else if (soloNumeros.length !== 10) {
            isValid = false;
            showFieldError($('#telefono'), 'El teléfono debe tener 10 dígitos');
        } else {
            clearError($('#telefono'));
        }
        
        // Validar comentarios
        const comentarios = $('#comentarios').val().trim();
        if (!comentarios) {
            isValid = false;
            showFieldError($('#comentarios'), 'La descripción del proyecto es obligatoria');
        } else if (comentarios.length < 10) {
            isValid = false;
            showFieldError($('#comentarios'), 'La descripción debe tener al menos 10 caracteres');
        } else {
            clearError($('#comentarios'));
        }
        
        // VALIDACIÓN DE ARCHIVOS
        const archivoField = $('#archivo');
        if (archivoField.length > 0 && archivoField[0].files.length > 0) {
            const archivo = archivoField[0].files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            const extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
            const extension = archivo.name.split('.').pop().toLowerCase();
            
            if (archivo.size > maxSize) {
                isValid = false;
                showFieldError(archivoField, 'El archivo es demasiado grande. Máximo 5 MB.');
            } else if (!extensionesPermitidas.includes(extension)) {
                isValid = false;
                showFieldError(archivoField, 'Formato no permitido. Solo PDF, JPG, JPEG, PNG.');
            } else {
                clearError(archivoField);
            }
        }
        
        // Validar checkbox de privacidad
        const privacidadCheckbox = $('#privacidad');
        if (privacidadCheckbox.length > 0 && !privacidadCheckbox.prop('checked')) {
            isValid = false;
            showFieldError(privacidadCheckbox, 'Debes aceptar la política de privacidad');
        } else {
            clearError(privacidadCheckbox);
        }
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                title: 'Campos incompletos',
                text: 'Por favor completa todos los campos correctamente',
                icon: 'warning',
                confirmButtonColor: '#7fad39'
            });
        } else {
            // Deshabilitar botón de envío para evitar doble envío
            const $submitBtn = $form.find('button[type="submit"]');
            $submitBtn.prop('disabled', true);
            $submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Enviando...');
        }
    });
    
    // ===== FUNCIONES DE VALIDACIÓN =====
    function highlightError(element) {
        element.addClass('is-invalid');
        element.removeClass('is-valid');
        
        if (element.attr('type') === 'checkbox') {
            element.closest('.form-check').addClass('is-invalid');
        }
    }
    
    function clearError(element) {
        element.removeClass('is-invalid');
        element.addClass('is-valid');
        
        if (element.attr('type') === 'checkbox') {
            element.closest('.form-check').removeClass('is-invalid');
        }
        
        element.next('.invalid-feedback').remove();
    }
    
    function showFieldError(element, message) {
        highlightError(element);
        element.next('.invalid-feedback').remove();
        
        if (element.attr('type') === 'checkbox') {
            element.closest('.form-check').append('<div class="invalid-feedback">' + message + '</div>');
        } else {
            element.after('<div class="invalid-feedback">' + message + '</div>');
        }
    }
    
    // ===== VALIDACIÓN EN TIEMPO REAL (AL PERDER FOCO) =====
    $('#nombre, #email, #telefono, #comentarios').on('blur', function() {
        const $element = $(this);
        const value = $element.val().trim();
        const id = $element.attr('id');
        
        if (!value) {
            showFieldError($element, 'Este campo es obligatorio');
            return;
        }
        
        if (id === 'nombre') {
            const nombreRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
            if (!nombreRegex.test(value)) {
                showFieldError($element, 'Solo letras y espacios');
            } else if (value.length < 3) {
                showFieldError($element, 'Mínimo 3 caracteres');
            } else {
                clearError($element);
            }
        } else if (id === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                showFieldError($element, 'Email inválido');
            } else {
                clearError($element);
            }
        } else if (id === 'telefono') {
            const soloNumeros = value.replace(/\D/g, '');
            if (soloNumeros.length !== 10) {
                showFieldError($element, 'Debe tener 10 dígitos');
            } else {
                clearError($element);
            }
        } else if (id === 'comentarios') {
            if (value.length < 10) {
                showFieldError($element, 'Mínimo 10 caracteres');
            } else {
                clearError($element);
            }
        }
    });
    
    // Validar archivo en tiempo real
    $('#archivo').on('change', function() {
        const $element = $(this);
        
        if (this.files.length > 0) {
            const archivo = this.files[0];
            const maxSize = 5 * 1024 * 1024;
            const extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
            const extension = archivo.name.split('.').pop().toLowerCase();
            
            if (archivo.size > maxSize) {
                showFieldError($element, 'El archivo es demasiado grande. Máximo 5 MB.');
            } else if (!extensionesPermitidas.includes(extension)) {
                showFieldError($element, 'Formato no permitido. Solo PDF, JPG, JPEG, PNG.');
            } else {
                clearError($element);
                // Mostrar nombre del archivo
                const $small = $element.next('small');
                if ($small.length) {
                    $small.html('<i class="fas fa-check-circle text-success me-1"></i> Archivo válido: ' + archivo.name);
                }
            }
        } else {
            clearError($element);
        }
    });
    
    // Validar checkbox
    $('#privacidad').on('change', function() {
        if ($(this).prop('checked')) {
            clearError($(this));
        } else {
            showFieldError($(this), 'Debes aceptar la política de privacidad');
        }
    });
    
    // ===== ANIMACIONES =====
    $('.contact-widget').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(20px)'
        });
        
        setTimeout(() => {
            $(this).animate({
                opacity: 1,
                transform: 'translateY(0)'
            }, 600);
        }, index * 200);
    });
    
    // ===== MAPA INTERACTIVO =====
    $('.map-wrapper').hover(
        function() {
            $(this).css('transform', 'scale(1.02)');
            $(this).css('transition', 'transform 0.3s ease');
        },
        function() {
            $(this).css('transform', 'scale(1)');
        }
    );
    
    // ===== WHATSAPP BUTTON ANIMATION =====
    $('.whatsapp-btn').hover(
        function() {
            $(this).css('transform', 'scale(1.05)');
            $(this).css('box-shadow', '0 10px 25px rgba(37, 211, 102, 0.3)');
        },
        function() {
            $(this).css('transform', 'scale(1)');
            $(this).css('box-shadow', 'none');
        }
    );
    
    // ===== COPY PHONE NUMBER =====
    $('.phone-list p').click(function() {
        const phoneNumber = $(this).find('strong').text().trim();
        
        navigator.clipboard.writeText(phoneNumber).then(() => {
            const notification = $(this).find('.copy-notification');
            if (notification.length === 0) {
                $(this).append('<span class="copy-notification text-success small">Copiado!</span>');
                setTimeout(() => {
                    $(this).find('.copy-notification').remove();
                }, 2000);
            }
            
            Swal.fire({
                title: 'Número copiado',
                text: phoneNumber + ' copiado al portapapeles',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        });
    });
    
    // ===== FORM AUTO-FOCUS =====
    if ($('#nombre').length) {
        setTimeout(() => {
            $('#nombre').focus();
        }, 500);
    }
    
    // ===== MOSTRAR ERRORES DESDE LA URL =====
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        const error = urlParams.get('error');
        let mensaje = '';
        
        switch(error) {
            case 'archivo_demasiado_grande':
                mensaje = 'El archivo es demasiado grande. Máximo 5 MB.';
                break;
            case 'tipo_archivo_no_valido':
                mensaje = 'Formato de archivo no permitido. Solo PDF, JPG, PNG.';
                break;
            default:
                mensaje = 'Hubo un error al enviar el formulario. Por favor intenta nuevamente.';
        }
        
        if (mensaje) {
            Swal.fire({
                title: 'Error',
                text: mensaje,
                icon: 'error',
                confirmButtonColor: '#7fad39'
            });
        }
    }
});