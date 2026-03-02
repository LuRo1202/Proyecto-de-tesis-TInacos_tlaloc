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
        
        // USAR EL HELPER PARA AGREGAR
        $resultado = CarritoHelper::agregar(
            $request->producto_id, 
            $request->cantidad ?? 1
        );
        
        // 🔴 SI EL HELPER DICE QUE NO FUE EXITOSO (STOCK INSUFICIENTE)
        if (!$resultado['success']) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message']
                ], 400); // CÓDIGO 400 = BAD REQUEST
            }
            
            return redirect()->back()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => $resultado['message']
            ]);
        }
        
        // ✅ SI FUE EXITOSO
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
        
        $carrito = CarritoHelper::getCarrito();
        $nuevoCarrito = [];
        
        foreach ($request->cantidad as $id => $cantidad) {
            if ($cantidad > 0 && isset($carrito[$id])) {
                $nuevoCarrito[$id] = $carrito[$id];
                $nuevoCarrito[$id]['cantidad'] = (int)$cantidad;
            }
        }
        
        session()->put('carrito', $nuevoCarrito);
        session()->save();
        
        return redirect()->route('carrito')->with('swal', [
            'type' => 'success',
            'title' => '¡Carrito actualizado!',
            'message' => 'Las cantidades se actualizaron correctamente.'
        ]);
    }

    public function eliminar($id)
    {
        $carrito = CarritoHelper::getCarrito();
        $nombreProducto = isset($carrito[$id]) ? $carrito[$id]['nombre'] : 'Producto';
        
        if (isset($carrito[$id])) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
            session()->save();
        }
        
        return redirect()->route('carrito')->with('swal', [
            'type' => 'success',
            'title' => '¡Producto eliminado!',
            'message' => "$nombreProducto se eliminó del carrito."
        ]);
    }

    public function vaciar()
    {
        session()->forget('carrito');
        session()->save();
        
        return redirect()->route('carrito')->with('swal', [
            'type' => 'success',
            'title' => '¡Carrito vaciado!',
            'message' => 'Todos los productos han sido eliminados del carrito.'
        ]);
    }
}