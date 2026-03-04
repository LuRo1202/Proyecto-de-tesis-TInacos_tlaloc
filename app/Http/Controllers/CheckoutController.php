<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Helpers\SucursalHelper;
use App\Helpers\CarritoHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        $sucursal = SucursalHelper::getSucursalActual();
        
        if (!auth('cliente')->check()) {
            return redirect()->route('cliente.login')
                ->with('error', 'Debes iniciar sesión para continuar con la compra');
        }
        
        $cliente = auth('cliente')->user();
        
        if (!$sucursal) {
            return redirect()->route('tienda')->with('error', 'No se pudo determinar la sucursal.');
        }

        $cart = session()->get('carrito', []);
        
        $productosCarrito = collect();
        $total = 0;
        $cartCount = 0;

        if (empty($cart)) {
            return redirect()->route('carrito')->with('error', 'Tu carrito está vacío');
        }

        foreach ($cart as $id => $item) {
            if (is_array($item)) {
                $cantidad = $item['cantidad'];
                $precio = $item['precio'];
            } else {
                $cantidad = $item;
                $precio = null;
            }
            
            $producto = Producto::with(['categoria', 'color'])->find($id);
            
            if ($producto) {
                $stock = $sucursal->productos()
                    ->where('productos.id', $id)
                    ->withPivot('existencias')
                    ->first();
                
                $existencias = $stock ? $stock->pivot->existencias : 0;
                
                if ($existencias < $cantidad) {
                    return redirect()->route('carrito')->with('error', 
                        "No hay suficiente stock de {$producto->nombre}. Disponible: {$existencias}");
                }
                
                $ofertaActiva = $producto->ofertas()
                    ->where('activa', true)
                    ->where('fecha_inicio', '<=', now())
                    ->where('fecha_fin', '>=', now())
                    ->first();

                $tieneOferta = !is_null($ofertaActiva);
                $precioOriginal = (float)$producto->precio;
                $precioFinal = $precio ?? $precioOriginal;
                $ahorro = 0;
                $descuentoTexto = null;

                if ($tieneOferta && $precioFinal < $precioOriginal) {
                    $ahorro = $precioOriginal - $precioFinal;
                    if ($ofertaActiva->tipo === 'porcentaje') {
                        $descuentoTexto = '-' . round($ofertaActiva->valor) . '%';
                    } else {
                        $descuentoTexto = '-$' . number_format($ofertaActiva->valor, 0);
                    }
                }
                
                $subtotal = $precioFinal * $cantidad;
                
                $productosCarrito->push([
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $precioFinal,
                    'precio_original' => $precioOriginal,
                    'tiene_oferta' => $tieneOferta,
                    'descuento_texto' => $descuentoTexto,
                    'ahorro' => $ahorro,
                    'codigo' => $producto->codigo,
                    'litros' => $producto->litros,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal,
                    'existencias' => $existencias
                ]);
                
                $total += $subtotal;
                $cartCount += $cantidad;
            } else {
                session()->forget("cart.{$id}");
            }
        }

        $coberturaVerificada = session()->has('cobertura_verificada') && session('cobertura_verificada.valido');
        $coberturaData = $coberturaVerificada ? session('cobertura_verificada') : null;

        return view('checkout', compact(
            'sucursal',
            'productosCarrito',
            'total',
            'cartCount',
            'coberturaVerificada',
            'coberturaData',
            'cliente'
        ));
    }

    public function verificarCobertura(Request $request)
    {
        $request->validate([
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:5'
        ]);

        try {
            $sucursal = SucursalHelper::getSucursalActual();
            
            if (!$sucursal) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo determinar la sucursal.'
                ]);
            }

            $resultado = $sucursal->verificarCobertura(
                $request->direccion,
                $request->ciudad,
                $request->estado
            );

            if ($resultado['valido']) {
                session()->put('cobertura_verificada', [
                    'valido' => true,
                    'sucursal_id' => $sucursal->id,
                    'sucursal_nombre' => $sucursal->nombre,
                    'sucursal_direccion' => $sucursal->direccion,
                    'sucursal_telefono' => $sucursal->telefono,
                    'distancia' => $resultado['distancia'],
                    'direccion_cliente' => $request->direccion,
                    'ciudad' => $request->ciudad,
                    'estado' => $request->estado,
                    'codigo_postal' => $request->codigo_postal,
                    'coords' => $resultado['coords'] ?? null
                ]);

                return response()->json([
                    'success' => true,
                    'valido' => true,
                    'message' => $resultado['mensaje'],
                    'distancia' => $resultado['distancia'],
                    'sucursal_nombre' => $sucursal->nombre,
                    'sucursal_direccion' => $sucursal->direccion
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'valido' => false,
                    'message' => $resultado['mensaje'],
                    'distancia' => $resultado['distancia']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error verificando cobertura: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar cobertura. Intenta de nuevo.'
            ]);
        }
    }

    public function procesar(Request $request)
    {
        if (!auth('cliente')->check()) {
            return redirect()->route('cliente.login')->with('error', 'Debes iniciar sesión');
        }
        
        $cliente = auth('cliente')->user();

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:5',
            'notas' => 'nullable|string|max:500',
            'aceptoTerminos' => 'required|accepted'
        ]);

        if (!session()->has('cobertura_verificada') || !session('cobertura_verificada.valido')) {
            return redirect()->route('cliente.checkout')->with('swal', [
                'type' => 'error',
                'title' => 'Cobertura no verificada',
                'message' => 'Debes verificar la cobertura de envío primero.'
            ]);
        }

        $cart = session()->get('carrito', []);

        if (empty($cart)) {
            return redirect()->route('carrito')->with('swal', [
                'type' => 'error',
                'title' => 'Carrito vacío',
                'message' => 'Tu carrito está vacío'
            ]);
        }

        // Guardar todo en sesión (NO crear pedido aún)
        session()->put('checkout_data', [
            'cliente_id' => $cliente->id,
            'datos' => $validated,
            'cobertura' => session('cobertura_verificada'),
            'carrito' => $cart,
            'total' => $this->calcularTotal($cart)
        ]);

        session()->forget('cobertura_verificada');

        return redirect()->route('pago.index')->with('swal', [
            'type' => 'success',
            'title' => '¡Datos confirmados!',
            'message' => 'Ahora procede al pago.'
        ]);
    }

    private function calcularTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $precio = is_array($item) ? $item['precio'] : 0;
            $cantidad = is_array($item) ? $item['cantidad'] : $item;
            $total += $precio * $cantidad;
        }
        return $total;
    }

    public function limpiarCobertura(Request $request) 
    {
        try {
            if ($request->ajax()) {
                session()->forget('cobertura_verificada');
                return response()->json([
                    'success' => true,
                    'message' => 'Cobertura limpiada'
                ]);
            }
            return response()->json([
                'success' => false, 
                'message' => 'Petición no válida'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error limpiando cobertura: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en el servidor'
            ], 500);
        }
    }
}