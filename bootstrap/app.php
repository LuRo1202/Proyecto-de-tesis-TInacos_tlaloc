<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // AQUÍ ES DONDE SE EXCLUYE LA RUTA EN LARAVEL 11
        $middleware->validateCsrfTokens(except: [
            'pago/webhook', 
            'pago/api/process-payment' // También esta por si la llamas desde fuera
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();