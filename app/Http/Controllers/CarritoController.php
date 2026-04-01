<?php
// app/Http/Controllers/CarritoController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Helpers\SucursalHelper;
use App\Helpers\CarritoHelper;
use App\Helpers\ProductoHelper; 

class CarritoController extends Controller
{
    public function index()
    {
        $sucursal = SucursalHelper::getSucursalActual();
        
        // USAR EL HELPER PARA OBTENER LOS PRODUCTOS
        $datosCarrito = CarritoHelper::getProductosCarrito($sucursal);
        
        return view('carrito', [
            'sucursal' => $sucursal,
            'productosCarrito' => $datosCarrito['productos'],
            'total' => $datosCarrito['total'],
            'cartCount' => $datosCarrito['cartCount']
        ]);
    }
    
    public function agregar(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer',
            'cantidad' => 'nullable|integer|min:1'
        ]);
        
        // USAR EL HELPER PARA AGREGAR (✅ YA ESTÁ BIEN, usa CarritoHelper::agregar)
        $resultado = CarritoHelper::agregar(
            $request->producto_id, 
            $request->cantidad ?? 1
        );
        
        if (!$resultado['success']) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message']
                ], 400);
            }
            
            return redirect()->back()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => $resultado['message']
            ]);
        }
        
        $cartCount = CarritoHelper::getCartCount();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'cartCount' => $cartCount,
                'message' => 'Producto agregado al carrito'
            ]);
        }
        
        return redirect()->back()->with('swal', [
            'type' => 'success',
            'title' => '¡Producto agregado!',
            'message' => 'El producto se agregó al carrito correctamente.'
        ]);
    }

    public function actualizar(Request $request)
    {
        $request->validate([
            'cantidad' => 'required|array',
            'cantidad.*' => 'integer|min:0'
        ]);
        
        // 🔧 CAMBIO: Usar CarritoHelper::getCarrito() en lugar de session()
        $carrito = CarritoHelper::getCarrito();
        $nuevoCarrito = [];
        
        foreach ($request->cantidad as $id => $cantidad) {
            if ($cantidad > 0 && isset($carrito[$id])) {
                $nuevoCarrito[$id] = $carrito[$id];
                $nuevoCarrito[$id]['cantidad'] = (int)$cantidad;
            }
        }
        
        // 🔧 CAMBIO: Usar CarritoHelper::guardarCarrito() (pero es privado, usamos agregar/eliminar)
        // Mejor: vaciar y volver a agregar, o crear un método actualizarCarrito()
        // Por ahora, usamos la lógica directa del helper:
        CarritoHelper::vaciar(); // Vaciamos primero
        foreach ($nuevoCarrito as $id => $item) {
            CarritoHelper::agregar($id, $item['cantidad']);
        }
        
        return redirect()->route('carrito')->with('swal', [
            'type' => 'success',
            'title' => '¡Carrito actualizado!',
            'message' => 'Las cantidades se actualizaron correctamente.'
        ]);
    }

    public function eliminar($id)
    {
        // 🔧 CAMBIO: Usar CarritoHelper::getCarrito() y CarritoHelper::eliminar()
        $carrito = CarritoHelper::getCarrito();
        $nombreProducto = isset($carrito[$id]) ? $carrito[$id]['nombre'] : 'Producto';
        
        // Usar el helper para eliminar (✅ YA USA EL HELPER INTERNAMENTE)
        CarritoHelper::eliminar($id);
        
        return redirect()->route('carrito')->with('swal', [
            'type' => 'success',
            'title' => '¡Producto eliminado!',
            'message' => "$nombreProducto se eliminó del carrito."
        ]);
    }

    public function vaciar()
    {
        // 🔧 CAMBIO: Usar CarritoHelper::vaciar() en lugar de session()
        CarritoHelper::vaciar();
        
        return redirect()->route('carrito')->with('swal', [
            'type' => 'success',
            'title' => '¡Carrito vaciado!',
            'message' => 'Todos los productos han sido eliminados del carrito.'
        ]);
    }
}