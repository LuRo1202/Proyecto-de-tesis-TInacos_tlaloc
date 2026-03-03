<?php
// app/Http/Controllers/Cliente/DashboardController.php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard del cliente
     */
    public function index()
    {
        $cliente = Auth::guard('cliente')->user();
        
        $pedidos = $cliente->pedidos()
            ->with(['items.producto'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $estadisticas = [
            'total_pedidos' => $pedidos->count(),
            'pendientes' => $pedidos->where('estado', 'pendiente')->count(),
            'enviados' => $pedidos->where('estado', 'enviado')->count(),
            'entregados' => $pedidos->where('estado', 'entregado')->count(),
            'cancelados' => $pedidos->where('estado', 'cancelado')->count(),
            'total_gastado' => $pedidos->where('estado', '!=', 'cancelado')->sum('total')
        ];
        
        $pedidosRecientes = $pedidos->take(5);
        
        // Calcular el contador del carrito
        $cart = session()->get('carrito', []);
        $cartCount = count($cart);
        
        return view('cliente.dashboard', compact(
            'cliente', 
            'pedidos', 
            'estadisticas', 
            'pedidosRecientes',
            'cartCount'
        ));
    }

    /**
     * Mostrar formulario para completar perfil
     */
    public function showCompletarPerfil()
    {
        $cliente = Auth::guard('cliente')->user();
        
        // Calcular el contador del carrito
        $cart = session()->get('carrito', []);
        $cartCount = count($cart);
        
        return view('cliente.completar-perfil', compact('cliente', 'cartCount'));
    }

    /**
     * Actualizar datos del cliente
     * SOLO el teléfono se actualiza en los pedidos
     * La dirección SOLO se actualiza en la tabla de clientes
     */
    public function actualizarDireccion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telefono' => 'nullable|string|max:10',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:10'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $cliente = Auth::guard('cliente')->user();
        
        // Guardar teléfono anterior para el mensaje
        $telefonoAnterior = $cliente->telefono;
        
        // ===== 1. ACTUALIZAR DATOS DEL CLIENTE (TODOS LOS CAMPOS) =====
        $cliente->update([
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'estado' => $request->estado,
            'codigo_postal' => $request->codigo_postal
        ]);

        // ===== 2. ACTUALIZAR SOLO EL TELÉFONO EN LOS PEDIDOS =====
        // La dirección NO se actualiza en los pedidos para mantener el histórico
        $pedidosActualizados = $cliente->pedidos()
            ->update([
                'cliente_telefono' => $request->telefono
                // 👇 NO se actualizan: cliente_direccion, cliente_ciudad, cliente_estado, codigo_postal
            ]);

        // Mensaje personalizado
        $mensaje = 'Datos actualizados correctamente';
        
        // Solo mencionar pedidos si realmente se actualizó el teléfono y hay cambios
        if ($request->telefono && $request->telefono !== $telefonoAnterior && $pedidosActualizados > 0) {
            $mensaje .= " y se actualizó el teléfono";
        } elseif ($request->telefono && $request->telefono === $telefonoAnterior) {
            $mensaje .= "";
        }

        return redirect()->route('cliente.dashboard')
            ->with('success', $mensaje);
    }

    /**
     * Mostrar todos los pedidos del cliente (con filtros)
     */
    public function pedidos(Request $request)
    {
        $cliente = Auth::guard('cliente')->user();
        
        $query = $cliente->pedidos()->with('items');
        
        if ($request->has('estado') && $request->estado != 'todos') {
            $query->where('estado', $request->estado);
        }
        
        $pedidos = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Calcular el contador del carrito
        $cart = session()->get('carrito', []);
        $cartCount = count($cart);
        
        return view('cliente.pedidos', compact('pedidos', 'cartCount'));
    }

    /**
     * Mostrar detalle de un pedido
     */
    public function verPedido($id)
    {
        $cliente = Auth::guard('cliente')->user();
        
        $pedido = $cliente->pedidos()
            ->with(['items.producto', 'sucursal', 'historial'])
            ->findOrFail($id);

        // Calcular el contador del carrito
        $cart = session()->get('carrito', []);
        $cartCount = count($cart);

        return view('cliente.pedido-detalle', compact('pedido', 'cartCount'));
    }

    /**
     * Cancelar un pedido (solo si está pendiente)
     */
    public function cancelarPedido($id)
    {
        $cliente = Auth::guard('cliente')->user();
        
        $pedido = $cliente->pedidos()
            ->where('estado', 'pendiente')
            ->findOrFail($id);

        $pedido->update(['estado' => 'cancelado']);

        $pedido->historial()->create([
            'accion' => 'cancelado',
            'detalles' => 'Pedido cancelado por el cliente',
            'fecha' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pedido cancelado correctamente'
        ]);
    }

    /**
     * Reordenar un pedido (agregar todos los productos al carrito)
     */
    public function reordenarPedido($id)
    {
        $cliente = Auth::guard('cliente')->user();
        
        $pedido = $cliente->pedidos()
            ->with(['items.producto', 'items.producto.color'])
            ->findOrFail($id);

        // Obtener carrito actual de la sesión
        $carrito = session()->get('carrito', []);
        
        // Agregar cada producto del pedido al carrito
        foreach ($pedido->items as $item) {
            $productoId = $item->producto_id;
            
            if (isset($carrito[$productoId])) {
                // Si ya existe, aumentar cantidad
                $carrito[$productoId]['cantidad'] += $item->cantidad;
            } else {
                // Si no existe, crear nuevo item
                $carrito[$productoId] = [
                    'id' => $item->producto_id,
                    'codigo' => $item->producto->codigo ?? '',
                    'nombre' => $item->producto_nombre,
                    'precio' => $item->precio,
                    'cantidad' => $item->cantidad,
                    'litros' => $item->producto->litros ?? 0,
                    'color' => $item->producto->color->nombre ?? '',
                    'color_hex' => $item->producto->color->codigo_hex ?? '',
                    'imagen' => $item->producto->imagen ?? ''
                ];
            }
        }
        
        // Guardar carrito actualizado en sesión
        session(['carrito' => $carrito]);
        
        // Actualizar contador para el badge
        $cartCount = count($carrito);
        session(['cartCount' => $cartCount]);

        // Redirigir al carrito con mensaje de éxito
        return redirect()->route('carrito')
            ->with('success', '¡Productos agregados al carrito correctamente!');
    }
}