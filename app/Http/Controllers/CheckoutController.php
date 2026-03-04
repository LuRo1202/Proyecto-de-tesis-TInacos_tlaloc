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
            // Verificar si el item es array (nuevo formato) o simple (formato antiguo)
            if (is_array($item)) {
                $cantidad = $item['cantidad'];
                $precio = $item['precio']; // Precio ya con descuento
            } else {
                $cantidad = $item;
                $precio = null; // Se obtendrá del producto
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
                        // 🔥 AQUÍ EL CAMBIO: round() elimina los decimales
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

            // Usar el método verificarCobertura del modelo
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
            return redirect()->route('cliente.login')
                ->with('error', 'Debes iniciar sesión para continuar con la compra');
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

        $cobertura = session('cobertura_verificada');
        $cart = session()->get('carrito', []);

        if (empty($cart)) {
            return redirect()->route('carrito')->with('swal', [
                'type' => 'error',
                'title' => 'Carrito vacío',
                'message' => 'Tu carrito está vacío'
            ]);
        }

        $sucursal = SucursalHelper::getSucursalActual();
        $total = 0;

        // PRIMERO: Verificar stock
        foreach ($cart as $id => $item) {
            $cantidad = is_array($item) ? $item['cantidad'] : $item;
            $producto = Producto::find($id);
            
            if (!$producto) {
                return redirect()->route('carrito')->with('swal', [
                    'type' => 'error',
                    'title' => 'Producto no encontrado',
                    'message' => "El producto con ID {$id} no existe"
                ]);
            }

            $stock = $sucursal->productos()
                ->where('productos.id', $id)
                ->withPivot('existencias')
                ->first();
            
            if (!$stock || $stock->pivot->existencias < $cantidad) {
                return redirect()->route('carrito')->with('swal', [
                    'type' => 'error',
                    'title' => 'Stock insuficiente',
                    'message' => "Stock insuficiente para {$producto->nombre}"
                ]);
            }
        }

        try {
            DB::beginTransaction();

            $folio = 'PED-' . date('ymd') . '-' . rand(1000, 9999);

            $pedido = Pedido::create([
                'cliente_id' => $cliente->id,
                'folio' => $folio,
                'cliente_nombre' => $validated['nombre'],
                'cliente_telefono' => $validated['telefono'],
                'cliente_direccion' => $validated['direccion'],
                'cliente_ciudad' => $validated['ciudad'],
                'cliente_estado' => $validated['estado'],
                'codigo_postal' => $validated['codigo_postal'],
                'total' => $total, // Se actualizará después
                'metodo_pago' => 'en_linea',
                'pago_confirmado' => false,
                'estado' => 'pendiente',
                'notas' => $validated['notas'] ?? null,
                'sucursal_id' => $cobertura['sucursal_id'],
                'distancia_km' => $cobertura['distancia'],
                'cobertura_verificada' => true
            ]);

            // SEGUNDO: Crear items con el precio del carrito (ya con descuento)
            foreach ($cart as $id => $item) {
                // Verificar si el item es array (nuevo formato) o simple (formato antiguo)
                if (is_array($item)) {
                    $cantidad = $item['cantidad'];
                    $precio = $item['precio']; // ← ESTE YA TIENE EL DESCUENTO APLICADO
                    $producto = Producto::find($id);
                } else {
                    $cantidad = $item;
                    $producto = Producto::find($id);
                    $precio = $producto->precio; // Fallback para formato antiguo
                }
                
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'producto_nombre' => $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio' => $precio // ← USAMOS EL PRECIO DEL CARRITO
                ]);

                $sucursal->productos()->updateExistingPivot($id, [
                    'existencias' => DB::raw('existencias - ' . $cantidad)
                ]);
                
                // Recalcular total
                $total += $precio * $cantidad;
            }

            // Actualizar el total del pedido
            $pedido->update(['total' => $total]);

            session()->forget('carrito');
            session()->put('pedido_pendiente', [
                'pedido_id' => $pedido->id,
                'folio' => $folio,
                'total' => $total,
                'sucursal' => [
                    'id' => $cobertura['sucursal_id'],
                    'nombre' => $cobertura['sucursal_nombre'],
                    'direccion' => $cobertura['sucursal_direccion'],
                    'distancia' => $cobertura['distancia']
                ]
            ]);

            session()->forget('cobertura_verificada');
            DB::commit();

            return redirect()->route('cliente.pago')->with('swal', [
                'type' => 'success',
                'title' => '¡Pedido creado!',
                'message' => "Tu pedido {$folio} ha sido registrado correctamente."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en checkout: ' . $e->getMessage());
            
            return redirect()->route('checkout')->with('swal', [
                'type' => 'error',
                'title' => 'Error al procesar',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function pago()
    {
        if (!session()->has('pedido_pendiente')) {
            return redirect()->route('tienda')->with('swal', [
                'type' => 'error',
                'title' => 'Sin pedido',
                'message' => 'No hay un pedido pendiente para pagar'
            ]);
        }

        $pedido = session('pedido_pendiente');
        return view('cliente.pago', compact('pedido'));
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
    
    public function pedidoGracias(Request $request)
    {
        if (!session()->has('pedido_pendiente')) {
            return redirect()->route('home')->with('swal', [
                'type' => 'error',
                'title' => 'Acceso denegado',
                'message' => 'No puedes acceder directamente a esta página.'
            ]);
        }
        
        $pedido = session('pedido_pendiente');
        $folio = $request->get('folio', $pedido['folio']);
        
        if ($folio !== $pedido['folio']) {
            return redirect()->route('home')->with('swal', [
                'type' => 'error',
                'title' => 'Error de verificación',
                'message' => 'Los datos del pedido no coinciden.'
            ]);
        }
        
        session()->forget('pedido_pendiente');
        
        return view('pedido-gracias', compact('folio'));
    }
}