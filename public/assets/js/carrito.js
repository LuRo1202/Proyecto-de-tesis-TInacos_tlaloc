// public/assets/js/carrito.js - VERSIÓN 100% FUNCIONAL

document.addEventListener('DOMContentLoaded', function() {
    
    // Verificar que SweetAlert2 existe
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 no está cargado');
        return;
    }
    
    // ===== ANIMACIONES DE ENTRADA =====
    const cartRows = document.querySelectorAll('.cart-table tbody tr, .border-bottom');
    if (cartRows.length > 0) {
        cartRows.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, index * 100);
        });
    }
    
    // ===== FUNCIÓN PARA VALIDAR QUE SOLO SEAN NÚMEROS =====
    function soloNumeros(e) {
        const teclasPermitidas = [
            'Backspace', 'Tab', 'Enter', 'Escape',
            'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
            'Delete', 'Home', 'End'
        ];
        
        if (teclasPermitidas.includes(e.key)) return true;
        if (e.ctrlKey && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase())) return true;
        if (/^[0-9]$/.test(e.key)) return true;
        
        e.preventDefault();
        return false;
    }
    
    // ===== FUNCIÓN PARA VALIDAR CANTIDAD =====
    function validarCantidad(input) {
        const max = parseInt(input.getAttribute('max')) || 999;
        const min = parseInt(input.getAttribute('min')) || 1;
        
        // Si está vacío, poner valor mínimo
        if (input.value === '' || input.value === null) {
            input.value = min;
            return min;
        }
        
        let value = parseInt(input.value);
        
        // Si no es número válido, poner mínimo
        if (isNaN(value)) {
            input.value = min;
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad inválida',
                text: 'La cantidad debe ser un número',
                timer: 1500,
                showConfirmButton: false
            });
            return min;
        }
        
        // Validar límites
        if (value < min) {
            input.value = min;
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad mínima',
                text: 'La cantidad mínima es ' + min,
                timer: 1500,
                showConfirmButton: false
            });
            return min;
        } else if (value > max) {
            input.value = max;
            Swal.fire({
                icon: 'warning',
                title: 'Stock máximo',
                text: 'Stock máximo disponible: ' + max,
                timer: 1500,
                showConfirmButton: false
            });
            return max;
        }
        
        return value;
    }
    
    // ===== APLICAR VALIDACIÓN A TODOS LOS INPUTS =====
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('keydown', soloNumeros);
        input.addEventListener('change', function() { validarCantidad(this); });
        input.addEventListener('blur', function() { validarCantidad(this); });
    });
    
    // ===== ELIMINAR PRODUCTO =====
    const removeButtons = document.querySelectorAll('.btn-remove, .btn-remove-mobile');
    removeButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productName = this.getAttribute('data-producto') || 'este producto';
            const removeUrl = this.getAttribute('href');
            
            Swal.fire({
                title: '¿Eliminar producto?',
                html: `¿Estás seguro de eliminar <strong>${productName}</strong> del carrito?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = removeUrl;
                }
            });
        });
    });
    
    // ===== VACIAR CARRITO =====
    const clearBtn = document.getElementById('btnVaciarCarrito');
    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Vaciar carrito?',
                text: '¿Estás seguro de vaciar el carrito completo?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, vaciar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = this.getAttribute('href');
                }
            });
        });
    }
    
    // ===== ACTUALIZAR CARRITO =====
    const updateForm = document.getElementById('formActualizarCarrito');
    if (updateForm) {
        updateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar todas las cantidades antes de enviar
            let isValid = true;
            const inputs = this.querySelectorAll('.quantity-input');
            inputs.forEach(input => {
                const valor = validarCantidad(input);
                if (valor === false) isValid = false;
            });
            
            if (!isValid) return false;
            
            // Preguntar confirmación
            Swal.fire({
                title: '¿Actualizar carrito?',
                text: 'Se actualizarán las cantidades de los productos',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7fad39',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Cambiar texto del botón mientras se procesa
                    const submitBtn = updateForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
                        submitBtn.disabled = true;
                    }
                    updateForm.submit();
                }
            });
        });
    }
    
    // ===== DESHABILITAR INPUTS SEGÚN MODO (DESKTOP/MÓVIL) =====
    function deshabilitarInputsSegunModo() {
        const esDesktop = window.innerWidth >= 992;
        
        document.querySelectorAll('.desktop-input').forEach(input => {
            input.disabled = !esDesktop;
        });
        
        document.querySelectorAll('.mobile-input').forEach(input => {
            input.disabled = esDesktop;
        });
    }
    
    deshabilitarInputsSegunModo();
    window.addEventListener('resize', deshabilitarInputsSegunModo);
    
});