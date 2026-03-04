<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso - Tanques Tlaloc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="text-center">
            <div class="mb-4">
                <i class="fas fa-check-circle text-success fa-5x"></i>
            </div>
            <h1 class="mb-3">¡Pago Exitoso!</h1>
            <p class="lead">Tu pedido <strong>{{ $pedido->folio ?? '' }}</strong> ha sido confirmado.</p>
            <p>Te enviaremos un correo con los detalles de tu compra.</p>
            
            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>Volver al inicio
                </a>
                <a href="{{ route('cliente.dashboard') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-user me-2"></i>Mis pedidos
                </a>
            </div>
        </div>
    </div>
</body>
</html>