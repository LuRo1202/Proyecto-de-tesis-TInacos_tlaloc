<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeContactoController;
use App\Http\Controllers\ProyectoController;
use App\Livewire\Actions\Logout;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\NoCachePublicPages; // 👈 IMPORTAR EL NUEVO MIDDLEWARE
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===== RUTA PRINCIPAL =====
Route::get('/', [HomeController::class, 'index'])
    ->name('home')
    ->middleware([NoCachePublicPages::class]);  // 👈 ASÍ SE USA

// ===== RUTA DE LOGIN =====
Route::get('/login', function() {
    return view('pages.auth.login');
})
    ->name('login')
    ->middleware([NoCachePublicPages::class]);  // 👈 ASÍ SE USA

// ===== RUTA DE LOGOUT ÚNICA (para TODOS) =====
Route::post('/logout', Logout::class)->name('logout');

// ===== RUTAS DE TIENDA =====
Route::get('/tienda', [TiendaController::class, 'index'])
    ->name('tienda')
    ->middleware([NoCachePublicPages::class]);  // 👈 ASÍ SE USA

Route::get('/tienda/categoria/{id}', [TiendaController::class, 'porCategoria'])
    ->name('tienda.categoria')
    ->middleware([NoCachePublicPages::class]);  // 👈 ASÍ SE USA

Route::get('/producto/{id}', [ProductoController::class, 'show'])
    ->name('producto')
    ->middleware([NoCachePublicPages::class]);  // 👈 ASÍ SE USA

// ===== RUTAS DE CARRITO =====
Route::get('/carrito', [CarritoController::class, 'index'])
    ->name('carrito')
    ->middleware([NoCachePublicPages::class]);  // 👈 ASÍ SE USA

Route::prefix('carrito')->name('carrito.')->group(function () {
    Route::post('/agregar', [CarritoController::class, 'agregar'])->name('agregar');
    Route::post('/actualizar', [CarritoController::class, 'actualizar'])->name('actualizar');
    Route::get('/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('eliminar');
    Route::get('/vaciar', [CarritoController::class, 'vaciar'])->name('vaciar');
});

// ===== RUTAS DE CONTACTO =====
Route::get('/contacto', [ContactoController::class, 'index'])
    ->name('contacto')
    ->middleware([NoCachePublicPages::class]);  // 👈 ASÍ SE USA

Route::post('/proyecto/enviar', [ProyectoController::class, 'enviar'])->name('proyecto.enviar');
Route::post('/home/contacto/enviar', [HomeContactoController::class, 'enviar'])->name('home.contacto.enviar');

// ===== RUTA DE GRACIAS =====
Route::get('/gracias', function () {
    return view('gracias');
})
    ->name('gracias')
    ->middleware([NoCachePublicPages::class]);  // 👈 ASÍ SE USA

// ===== RUTAS PARA USUARIOS (ADMIN/GERENTE/VENDEDOR) =====
Route::prefix('usuario')->name('usuario.')->group(function () {
    // RESET PASSWORD - SOLICITUD (GET - formulario para pedir email)
    Route::get('/reset-password', function () {
        return view('pages.auth.usuario-reset-password');
    })->name('reset')
        ->middleware([NoCachePublicPages::class]);
    
    // RESET PASSWORD - ENVIAR CORREO (POST)
    Route::post('/reset-password', [App\Http\Controllers\Auth\UsuarioForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('reset.send')
        ->middleware([NoCachePublicPages::class]);
    
    // RESET PASSWORD - FORMULARIO CON TOKEN (GET)
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])
        ->name('reset.form')
        ->middleware([NoCachePublicPages::class]);
    
    // RESET PASSWORD - ACTUALIZAR CONTRASEÑA (POST) - MISMA URL DEL TOKEN
    Route::post('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
        ->name('reset.update')
        ->middleware([NoCachePublicPages::class]);
});
// ===== RUTAS PÚBLICAS DE REGISTRO Y RESET PASSWORD (CON NO-CACHE) =====
Route::prefix('cliente')->name('cliente.')->group(function () {
    // REGISTRO
    Route::get('/registro', function () {
        return view('pages.auth.register');
    })
        ->name('registro')
        ->middleware([NoCachePublicPages::class]);
    
    Route::post('/registro', [App\Http\Controllers\ClienteAuthController::class, 'register'])
        ->name('register.store');
    
    // RESET PASSWORD - SOLICITUD (GET - formulario para pedir email)
    Route::get('/reset-password', function () {
        return view('pages.auth.reset-password');
    })->name('reset')
        ->middleware([NoCachePublicPages::class]);
    
    // RESET PASSWORD - ENVIAR CORREO (POST)
    Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('reset.send')
        ->middleware([NoCachePublicPages::class]);
    
    // RESET PASSWORD - FORMULARIO CON TOKEN (GET)
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])
        ->name('reset.form')
        ->middleware([NoCachePublicPages::class]);
    
    // RESET PASSWORD - ACTUALIZAR CONTRASEÑA (POST) - MISMA URL DEL TOKEN
    Route::post('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
        ->name('reset.update')
        ->middleware([NoCachePublicPages::class]);
});

// ===== RUTAS PROTEGIDAS PARA CLIENTES =====
Route::prefix('cliente')
    ->name('cliente.')
    ->middleware(['auth:cliente', PreventBackHistory::class])  // 👈 ESTO QUEDA IGUAL
    ->group(function () {
    
    Route::get('/dashboard', [App\Http\Controllers\Cliente\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/cliente/pedidos/{id}', [DashboardController::class, 'verPedido'])->name('cliente.pedido.ver');
    // Perfil y dirección
    Route::get('/completar-perfil', [App\Http\Controllers\Cliente\DashboardController::class, 'showCompletarPerfil'])->name('completar-perfil');
    Route::post('/actualizar-direccion', [App\Http\Controllers\Cliente\DashboardController::class, 'actualizarDireccion'])->name('actualizar-direccion');   
   
    // Pedidos
    Route::get('/pedidos', [App\Http\Controllers\Cliente\DashboardController::class, 'pedidos'])->name('pedidos');
    Route::get('/pedido/{id}', [App\Http\Controllers\Cliente\DashboardController::class, 'verPedido'])->name('pedido.ver');
    Route::post('/pedido/{id}/cancelar', [App\Http\Controllers\Cliente\DashboardController::class, 'cancelarPedido'])->name('pedido.cancelar');
    Route::get('/reordenar/{id}', [App\Http\Controllers\Cliente\DashboardController::class, 'reordenarPedido'])
    ->name('cliente.reordenar'); 
    
    
    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/verificar-cobertura', [CheckoutController::class, 'verificarCobertura'])->name('checkout.verificar-cobertura');
    Route::post('/checkout/procesar', [CheckoutController::class, 'procesar'])->name('checkout.procesar');
    Route::post('/checkout/limpiar-cobertura', [CheckoutController::class, 'limpiarCobertura'])->name('checkout.limpiar-cobertura');
    
});

// ===== RUTA DE GRACIAS POR PEDIDO =====
Route::get('/pedido-gracias/{folio?}', [App\Http\Controllers\CheckoutController::class, 'pedidoGracias'])
    ->name('pedido.gracias')
    ->middleware([NoCachePublicPages::class]);  // 👈 ASÍ SE USA


// ===== RUTAS DE ADMIN (PROTEGIDAS) =====
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])
    ->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index']); // ✅ Sin nombre para que no choque
    // PEDIDOS
    Route::get('/pedidos', [App\Http\Controllers\Admin\PedidoController::class, 'index'])->name('pedidos');
    Route::get('/pedidos/{id}', [App\Http\Controllers\Admin\PedidoController::class, 'show'])->name('pedidos.ver');
    Route::get('/pedidos/{id}/editar', [App\Http\Controllers\Admin\PedidoController::class, 'edit'])->name('pedidos.editar');
    Route::put('/pedidos/{id}', [App\Http\Controllers\Admin\PedidoController::class, 'update'])->name('pedidos.update');
    Route::get('/pedidos/{id}/eliminar', [App\Http\Controllers\Admin\PedidoController::class, 'destroy'])->name('pedidos.eliminar');
    Route::get('/pedidos/procesar/{accion}/{id}', [App\Http\Controllers\Admin\PedidoController::class, 'procesar'])->name('pedidos.procesar');
    Route::post('/pedidos/asignar-responsable', [App\Http\Controllers\Admin\PedidoController::class, 'asignarResponsable'])->name('pedidos.asignar-responsable');
    Route::post('/pedidos/remover-responsable', [App\Http\Controllers\Admin\PedidoController::class, 'removerResponsable'])->name('pedidos.remover-responsable');
    Route::post('/cobertura/verificar', [App\Http\Controllers\Admin\CoberturaController::class, 'verificar'])->name('cobertura.verificar');
    Route::post('/cobertura/limpiar', [App\Http\Controllers\Admin\CoberturaController::class, 'limpiar'])->name('cobertura.limpiar');
    Route::get('/cobertura/obtener', [App\Http\Controllers\Admin\CoberturaController::class, 'obtener'])->name('cobertura.obtener');



    // PRODUCTOS
    Route::get('/productos', [App\Http\Controllers\Admin\ProductoController::class, 'index'])->name('productos');
    Route::get('/productos/nuevo', [App\Http\Controllers\Admin\ProductoController::class, 'create'])->name('productos.nuevo');
    Route::post('/productos', [App\Http\Controllers\Admin\ProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{id}/editar', [App\Http\Controllers\Admin\ProductoController::class, 'edit'])->name('productos.editar');
    Route::put('/productos/{id}', [App\Http\Controllers\Admin\ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{id}', [App\Http\Controllers\Admin\ProductoController::class, 'destroy'])->name('productos.destroy');

    // CATEGORÍAS
    Route::get('/ofertas', [App\Http\Controllers\Admin\OfertaController::class, 'index'])->name('ofertas');
    Route::get('/ofertas/nuevo', [App\Http\Controllers\Admin\OfertaController::class, 'create'])->name('ofertas.nuevo');
    Route::post('/ofertas', [App\Http\Controllers\Admin\OfertaController::class, 'store'])->name('ofertas.store');
    Route::get('/ofertas/{id}/editar', [App\Http\Controllers\Admin\OfertaController::class, 'edit'])->name('ofertas.editar');
    Route::put('/ofertas/{id}', [App\Http\Controllers\Admin\OfertaController::class, 'update'])->name('ofertas.update');
    Route::delete('/ofertas/{id}', [App\Http\Controllers\Admin\OfertaController::class, 'destroy'])->name('ofertas.destroy');
    Route::get('/ofertas/{id}/toggle', [App\Http\Controllers\Admin\OfertaController::class, 'toggle'])->name('ofertas.toggle');

    
    // CATEGORÍAS
    Route::get('/categorias', [App\Http\Controllers\Admin\CategoriaController::class, 'index'])->name('categorias');
    Route::post('/categorias', [App\Http\Controllers\Admin\CategoriaController::class, 'store'])->name('categorias.store');
    Route::put('/categorias', [App\Http\Controllers\Admin\CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias', [App\Http\Controllers\Admin\CategoriaController::class, 'destroy'])->name('categorias.destroy');
   
    // SUCURSALES
    Route::get('/sucursales', [App\Http\Controllers\Admin\SucursalController::class, 'index'])->name('sucursales');
    Route::post('/sucursales', [App\Http\Controllers\Admin\SucursalController::class, 'store'])->name('sucursales.store');
    Route::put('/sucursales', [App\Http\Controllers\Admin\SucursalController::class, 'update'])->name('sucursales.update');
    Route::delete('/sucursales', [App\Http\Controllers\Admin\SucursalController::class, 'destroy'])->name('sucursales.destroy');    
    
    // USUARIOS
    Route::get('/usuarios', [App\Http\Controllers\Admin\UsuarioController::class, 'index'])->name('usuarios');
    Route::post('/usuarios', [App\Http\Controllers\Admin\UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios', [App\Http\Controllers\Admin\UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios', [App\Http\Controllers\Admin\UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    Route::get('/usuarios/toggle', [App\Http\Controllers\Admin\UsuarioController::class, 'toggle'])->name('usuarios.toggle');
    
    // REPORTES
    Route::get('/reportes', [App\Http\Controllers\Admin\ReporteController::class, 'index'])->name('reportes');
});


// ===== RUTAS DE GERENTE (PROTEGIDAS) =====
Route::prefix('gerente')
    ->name('gerente.')
    ->middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])
    ->group(function () {
    
    // ===== DASHBOARD =====
    Route::get('/dashboard', [App\Http\Controllers\Gerente\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [App\Http\Controllers\Gerente\DashboardController::class, 'index']); 
    // ===== PEDIDOS =====
    Route::get('/pedidos', [App\Http\Controllers\Gerente\PedidoController::class, 'index'])->name('pedidos');
    Route::get('/pedidos/nuevo', [App\Http\Controllers\Gerente\PedidoController::class, 'create'])->name('pedidos.nuevo');
    Route::post('/pedidos', [App\Http\Controllers\Gerente\PedidoController::class, 'store'])->name('pedidos.store');
    Route::get('/pedidos/{id}', [App\Http\Controllers\Gerente\PedidoController::class, 'show'])->name('pedidos.ver');
    Route::get('/pedidos/{id}/editar', [App\Http\Controllers\Gerente\PedidoController::class, 'edit'])->name('pedidos.editar');
    Route::put('/pedidos/{id}', [App\Http\Controllers\Gerente\PedidoController::class, 'update'])->name('pedidos.update');    
    
    Route::delete('/pedidos/eliminar/{id}', [App\Http\Controllers\Gerente\PedidoController::class, 'destroy'])->name('pedidos.eliminar');
    Route::get('/pedidos/procesar/{accion}/{id}', [App\Http\Controllers\Gerente\PedidoController::class, 'procesarAccion'])->name('pedidos.procesar');
    Route::post('/pedidos/asignar-responsable', [App\Http\Controllers\Gerente\PedidoController::class, 'asignarResponsable'])->name('pedidos.asignar-responsable');
    Route::post('/pedidos/remover-responsable', [App\Http\Controllers\Gerente\PedidoController::class, 'removerResponsable'])->name('pedidos.remover-responsable');
    
        
    // ===== PRODUCTOS =====
    Route::get('/productos', [App\Http\Controllers\Gerente\ProductoController::class, 'index'])->name('productos');
    Route::get('/productos/nuevo', [App\Http\Controllers\Gerente\ProductoController::class, 'create'])->name('productos.nuevo');
    Route::post('/productos', [App\Http\Controllers\Gerente\ProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{id}/editar', [App\Http\Controllers\Gerente\ProductoController::class, 'edit'])->name('productos.editar');
    Route::put('/productos/{id}', [App\Http\Controllers\Gerente\ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/eliminar/{id}', [App\Http\Controllers\Gerente\ProductoController::class, 'destroy'])->name('productos.eliminar');
    Route::post('/productos/verificar-oferta', [App\Http\Controllers\Gerente\PedidoController::class, 'verificarOferta'])->name('productos.verificar-oferta');
    Route::post('/clientes/buscar', [App\Http\Controllers\Gerente\PedidoController::class, 'buscar'])->name('clientes.buscar');

     // OFERTAS 
    Route::get('/ofertas', [App\Http\Controllers\Gerente\OfertaController::class, 'index'])->name('ofertas');
    Route::get('/ofertas/nuevo', [App\Http\Controllers\Gerente\OfertaController::class, 'create'])->name('ofertas.nuevo');
    Route::post('/ofertas', [App\Http\Controllers\Gerente\OfertaController::class, 'store'])->name('ofertas.store');
    Route::get('/ofertas/{id}/editar', [App\Http\Controllers\Gerente\OfertaController::class, 'edit'])->name('ofertas.editar');
    Route::put('/ofertas/{id}', [App\Http\Controllers\Gerente\OfertaController::class, 'update'])->name('ofertas.update');
    Route::get('/ofertas/{id}/toggle', [App\Http\Controllers\Gerente\OfertaController::class, 'toggle'])->name('ofertas.toggle');
   
    // ===== VENDEDORES =====
    Route::get('/vendedores', [App\Http\Controllers\Gerente\VendedorController::class, 'index'])->name('vendedores');
    Route::post('/vendedores', [App\Http\Controllers\Gerente\VendedorController::class, 'store'])->name('vendedores.store');
    Route::put('/vendedores', [App\Http\Controllers\Gerente\VendedorController::class, 'update'])->name('vendedores.update');
    Route::post('/vendedores/toggle', [App\Http\Controllers\Gerente\VendedorController::class, 'toggleEstado'])->name('vendedores.toggle');
   

    
    // ===== REPORTES =====
    Route::get('/reportes', [App\Http\Controllers\Gerente\ReporteController::class, 'index'])->name('reportes');
    Route::post('/reportes/exportar', [App\Http\Controllers\Gerente\ReporteController::class, 'exportarExcel'])->name('reportes.exportar');    
    

   // ===== CLIENTES =====
    Route::get('/clientes', [App\Http\Controllers\Gerente\ClienteController::class, 'index'])->name('clientes');
    Route::get('/clientes/historial', [App\Http\Controllers\Gerente\ClienteController::class, 'historial'])->name('clientes.historial');
    Route::get('/clientes/reporte', [App\Http\Controllers\Gerente\ClienteController::class, 'reporteExcel'])->name('clientes.reporte');
    
    // ===== COBERTURA =====
    Route::post('/cobertura/verificar', [App\Http\Controllers\Gerente\CoberturaController::class, 'verificar'])->name('cobertura.verificar');
});




Route::prefix('vendedor')
    ->name('vendedor.')
    ->middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])
    ->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Vendedor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [App\Http\Controllers\Vendedor\DashboardController::class, 'index']); // ✅ Sin nombre
    // Asignar pedido (AJAX)
    Route::post('/pedidos/asignar', [App\Http\Controllers\Vendedor\DashboardController::class, 'asignarPedido'])->name('pedidos.asignar');
    
    // 👇 NUEVA RUTA PARA ACTUALIZAR CONTADORES
    Route::get('/contadores/actualizar', [App\Http\Controllers\Vendedor\DashboardController::class, 'actualizarContadores'])->name('contadores.actualizar');
    
    // PEDIDOS
    Route::get('/pedidos', [App\Http\Controllers\Vendedor\PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/hoy', [App\Http\Controllers\Vendedor\PedidoController::class, 'hoy'])->name('pedidos.hoy');
    Route::get('/pedidos/create', [App\Http\Controllers\Vendedor\PedidoController::class, 'create'])->name('pedidos.create');
    Route::post('/pedidos', [App\Http\Controllers\Vendedor\PedidoController::class, 'store'])->name('pedidos.store');
    Route::get('/pedidos/{id}', [App\Http\Controllers\Vendedor\PedidoController::class, 'show'])->name('pedidos.show');
    Route::get('/pedidos/{id}/seguimiento', [App\Http\Controllers\Vendedor\PedidoController::class, 'seguimiento'])->name('pedidos.seguimiento');
    Route::put('/pedidos/{id}', [App\Http\Controllers\Vendedor\PedidoController::class, 'update'])->name('pedidos.update');
    Route::post('/pedidos/{id}/guardar-seguimiento', [App\Http\Controllers\Vendedor\PedidoController::class, 'guardarSeguimiento'])->name('pedidos.guardar-seguimiento');
    Route::post('/clientes/buscar', [App\Http\Controllers\Vendedor\ClienteController::class, 'buscar'])->name('clientes.buscar'); // ← NUEVA RUTA

    Route::post('/productos/verificar-oferta', [App\Http\Controllers\Vendedor\PedidoController::class, 'verificarOferta'])
        ->name('productos.verificar-oferta'); 

    // COBERTURA
    Route::post('/cobertura/verificar', [App\Http\Controllers\Vendedor\CoberturaController::class, 'verificar'])->name('cobertura.verificar');
    Route::post('/cobertura/limpiar', [App\Http\Controllers\Vendedor\CoberturaController::class, 'limpiar'])->name('cobertura.limpiar');
    
    // VENTAS
    Route::get('/ventas', [App\Http\Controllers\Vendedor\VentaController::class, 'index'])->name('ventas.index');
    
    // CLIENTES
    Route::get('/clientes', [App\Http\Controllers\Vendedor\ClienteController::class, 'index'])->name('clientes.index');
    
    // CATÁLOGO
    Route::get('/catalogo', [App\Http\Controllers\Vendedor\CatalogoController::class, 'index'])->name('catalogo.index');
    Route::get('/producto/{id}/detalles', [App\Http\Controllers\Vendedor\ProductoController::class, 'detalles'])->name('producto.detalles');
});


// Pago
Route::prefix('pago')->name('pago.')->group(function () {
    Route::get('/', [App\Http\Controllers\PagoController::class, 'index'])->name('index');
    Route::post('/webhook', [App\Http\Controllers\PagoController::class, 'webhook'])->name('webhook');
    Route::get('/success', [App\Http\Controllers\PagoController::class, 'success'])->name('success');
    Route::get('/failure', [App\Http\Controllers\PagoController::class, 'failure'])->name('failure');
    Route::get('/pending', [App\Http\Controllers\PagoController::class, 'pending'])->name('pending');
});