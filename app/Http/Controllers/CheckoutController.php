<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\PagoPendiente;
use App\Helpers\SucursalHelper;
use App\Helpers\CarritoHelper;  // 👈 YA ESTÁ IMPORTADO
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

        // 🔧 CAMBIO: Usar CarritoHelper en lugar de session() directamente
        $datosCarrito = CarritoHelper::getProductosCarrito($sucursal);
        $productosCarrito = $datosCarrito['productos'];
        $total = $datosCarrito['total'];
        $cartCount = $datosCarrito['cartCount'];

        if ($productosCarrito->isEmpty()) {
            return redirect()->route('carrito')->with('error', 'Tu carrito está vacío');
        }

        // 🔧 NUEVO: Verificar stock de cada producto (ya lo hace getProductosCarrito, pero validamos)
        foreach ($productosCarrito as $item) {
            if ($item['existencias'] < $item['cantidad']) {
                return redirect()->route('carrito')->with('error', 
                    "No hay suficiente stock de {$item['nombre']}. Disponible: {$item['existencias']}");
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
        // ✅ ESTE MÉTODO NO CAMBIA, no usa carrito directamente
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

    /**
     * 🔧 MODIFICADO: Ahora usa CarritoHelper en lugar de session()
     */
    private function calcularTotal($cart)
    {
        // Este método ya no se usa directamente, pero lo dejamos por si acaso
        return CarritoHelper::calcularTotal($cart);
    }

    /**
     * 🔧 PROCESAR - Cambio importante: obtener carrito del helper
     */
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

        // 🔧 CAMBIO IMPORTANTE: Obtener carrito del HELPER, no de session()
        $carrito = CarritoHelper::getCarrito();
        $total = CarritoHelper::calcularTotal($carrito);

        if (empty($carrito)) {
            return redirect()->route('carrito')->with('swal', [
                'type' => 'error',
                'title' => 'Carrito vacío',
                'message' => 'Tu carrito está vacío'
            ]);
        }

        // Crear registro de pago pendiente
        $folio = 'PED-' . date('ymd') . '-' . rand(1000, 9999);
        
        $pagoPendiente = PagoPendiente::create([
            'folio' => $folio,
            'cliente_id' => $cliente->id,
            'checkout_data' => [
                'cliente_id' => $cliente->id,
                'datos' => $validated,
                'cobertura' => session('cobertura_verificada'),
                'carrito' => $carrito,
                'total' => $total
            ],
            'status' => 'pendiente'
        ]);

        session()->forget('cobertura_verificada');

        return redirect()->route('pago.index', ['folio' => $folio])->with('swal', [
            'type' => 'success',
            'title' => '¡Datos confirmados!',
            'message' => 'Ahora procede al pago.'
        ]);
    }

    public function limpiarCobertura(Request $request) 
    {
        // ✅ ESTE MÉTODO NO CAMBIA
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