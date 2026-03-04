<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Fallido - Tanques Tlaloc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="text-center">
            <div class="mb-4">
                <i class="fas fa-times-circle text-danger fa-5x"></i>
            </div>
            <h1 class="mb-3">¡Pago No Procesado!</h1>
            <p class="lead">{{ $message ?? 'Hubo un problema con tu pago.' }}</p>
            <p>Puedes intentar nuevamente o contactar a soporte.</p>
            
            <div class="mt-4">
                <a href="{{ route('carrito') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-cart me-2"></i>Volver al carrito
                </a>
                <a href="{{ route('contacto') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-headset me-2"></i>Contactar
                </a>
            </div>
        </div>
    </div>
</body>
</html>