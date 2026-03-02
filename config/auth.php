<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // 👇 AGREGAR ESTE GUARD PARA CLIENTES
        'cliente' => [
            'driver' => 'session',
            'provider' => 'clientes',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class,
        ],

        // 👇 AGREGAR ESTE PROVIDER PARA CLIENTES
        'clientes' => [
            'driver' => 'eloquent',
            'model' => App\Models\Cliente::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // 👇 AGREGAR PARA RECUPERACIÓN DE CONTRASEÑA DE CLIENTES
        'clientes' => [
            'provider' => 'clientes',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];