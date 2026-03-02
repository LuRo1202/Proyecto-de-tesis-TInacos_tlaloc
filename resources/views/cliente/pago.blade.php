{{-- resources/views/pago.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Pago | Tanques Tlaloc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    <style>
        body {
            background: linear-gradient(135deg, #7fad39 0%, #5a8c29 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            background: linear-gradient(135deg, #7fad39, #5a8c29) !important;
        }
        .info-pedido {
            background: #f8f9fa;
            border-left: 4px solid #7fad39;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .info-pedido p {
            margin-bottom: 8px;
            font-size: 1.1rem;
        }
        .info-pedido strong {
            color: #7fad39;
            font-size: 1.2rem;
        }
        .btn-success {
            background: linear-gradient(135deg, #7fad39, #5a8c29);
            border: none;
            padding: 15px;
            font-size: 1.2rem;
            border-radius: 10px;
            transition: transform 0.3s;
        }
        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(127, 173, 57, 0.4);
        }
        .btn-success:disabled {
            background: #6c757d;
            transform: none;
            cursor: not-allowed;
        }
        .btn-secondary {
            padding: 12px;
            border-radius: 10px;
        }
        .metodo-pago {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .metodo-pago:hover {
            border-color: #7fad39;
            background: #f8f9fa;
        }
        .metodo-pago.selected {
            border-color: #7fad39;
            background: #f0f9f0;
            box-shadow: 0 5px 15px rgba(127, 173, 57, 0.2);
        }
        .metodo-pago i {
            font-size: 2rem;
            margin-right: 15px;
            color: #7fad39;
        }
        .badge-pendiente {
            background: #ffc107;
            color: #856404;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: normal;
        }
        .sucursal-info {
            background: #e8f5e9;
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .folio-badge {
            background: #6c757d;
            color: white;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 1rem;
        }
        @media (max-width: 768px) {
            .card-body {
                padding: 15px !important;
            }
            .metodo-pago {
                padding: 15px;
            }
            .metodo-pago i {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Procesar Pago
                            @if(isset($pedido) && $pedido)
                            <span class="folio-badge float-end">
                                <i class="fas fa-hashtag me-1"></i>{{ $pedido['folio'] }}
                            </span>
                            @endif
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        
                        @if(session('swal'))
                        <script>
                            Swal.fire({
                                icon: '{{ session('swal')['type'] }}',
                                title: '{{ session('swal')['title'] }}',
                                text: '{{ session('swal')['message'] }}',
                                confirmButtonColor: '#7fad39'
                            });
                        </script>
                        @endif

                        {{-- VERIFICAR QUE HAYA DATOS DEL PEDIDO --}}
                        @if(isset($pedido) && $pedido)
                        
                        <div class="info-pedido">
                            <h5 class="mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Detalles del Pedido
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Folio:</strong> <span class="badge bg-secondary">{{ $pedido['folio'] }}</span></p>
                                    <p><strong>Total a pagar:</strong> <span class="text-success fw-bold fs-4">${{ number_format($pedido['total'], 2) }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                                    <p><strong>Estado:</strong> <span class="badge bg-warning text-dark">Pendiente de pago</span></p>
                                </div>
                            </div>
                            
                            @if(isset($pedido['sucursal']))
                            <div class="sucursal-info">
                                <i class="fas fa-store me-2"></i>
                                <strong>Sucursal:</strong> {{ $pedido['sucursal']['nombre'] }} 
                                @if(isset($pedido['sucursal']['distancia']))
                                    <span class="ms-2">| <i class="fas fa-road me-1"></i>{{ $pedido['sucursal']['distancia'] }} km</span>
                                @endif
                            </div>
                            @endif
                        </div>

                        {{-- MÉTODOS DE PAGO --}}
                        <h5 class="mb-3">Selecciona método de pago:</h5>
                        
                        <div class="metodo-pago" onclick="seleccionarMetodo('tarjeta', this)">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-credit-card"></i>
                                <div>
                                    <h6 class="mb-1">Tarjeta de Crédito/Débito</h6>
                                    <small class="text-muted">Visa, MasterCard, American Express</small>
                                </div>
                            </div>
                        </div>

                        <div class="metodo-pago" onclick="seleccionarMetodo('efectivo', this)">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-money-bill-wave"></i>
                                <div>
                                    <h6 class="mb-1">Pago en Efectivo</h6>
                                    <small class="text-muted">Paga al recibir tu pedido</small>
                                </div>
                            </div>
                        </div>

                        <div class="metodo-pago" onclick="seleccionarMetodo('transferencia', this)">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-university"></i>
                                <div>
                                    <h6 class="mb-1">Transferencia Bancaria</h6>
                                    <small class="text-muted">Recibirás datos para transferir</small>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="metodo_seleccionado" value="">

                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-success btn-lg" onclick="procesarPago()" id="btnPagar" disabled>
                                <i class="fas fa-lock me-2"></i>Pagar ${{ number_format($pedido['total'], 2) }}
                            </button>
                            <a href="{{ route('cliente.checkout') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Regresar
                            </a>
                        </div>

                        <p class="text-center text-muted mt-3 small">
                            <i class="fas fa-shield-alt me-1"></i>
                            Pago 100% seguro. No guardamos datos de tu tarjeta.
                        </p>

                        @else
                        {{-- NO HAY PEDIDO PENDIENTE --}}
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-circle fa-4x text-warning mb-3"></i>
                            <h4>No hay un pedido pendiente</h4>
                            <p class="text-muted mb-4">Debes completar el checkout primero</p>
                            <a href="{{ route('checkout') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i>Ir al Checkout
                            </a>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let metodoPago = '';
        const btnPagar = document.getElementById('btnPagar');

        function seleccionarMetodo(metodo, elemento) {
            metodoPago = metodo;
            
            // Remover selección anterior
            document.querySelectorAll('.metodo-pago').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Marcar el seleccionado
            elemento.classList.add('selected');
            
            document.getElementById('metodo_seleccionado').value = metodo;
            
            // Habilitar botón de pago
            btnPagar.disabled = false;
        }

        function procesarPago() {
            if (!metodoPago) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona un método',
                    text: 'Debes elegir un método de pago para continuar',
                    confirmButtonColor: '#7fad39'
                });
                return;
            }

            // Deshabilitar botón para evitar doble clic
            btnPagar.disabled = true;
            btnPagar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';

            // Simular procesamiento de pago
            Swal.fire({
                title: 'Procesando pago...',
                html: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Simular delay de pago
            setTimeout(() => {
                Swal.close();
                
                // ✅ REDIRECCIÓN CORRECTA A pedido-gracias con folio
                window.location.href = '{{ route("pedido.gracias", ["folio" => $pedido["folio"] ?? ""]) }}';
            }, 2000);
        }

        // Verificar si hay pedido pendiente al cargar la página
        @if(!isset($pedido) || !$pedido)
        Swal.fire({
            icon: 'error',
            title: 'Sin pedido pendiente',
            text: 'No hay un pedido pendiente para pagar',
            confirmButtonColor: '#7fad39'
        });
        @endif
    </script>
</body>
</html>