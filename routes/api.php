<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagoController; // 👈 Asegúrate de importar tu controlador

// Esta ruta ya viene por defecto, déjala ahí
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ===== NUEVA RUTA PARA MERCADO PAGO =====
// Las rutas en api.php NO usan CSRF por defecto
// Cambiamos POST por ANY para diagnosticar
Route::any('/pago/webhook', [App\Http\Controllers\PagoController::class, 'webhook']);