<?php
// app/Http/Controllers/Cliente/DashboardController.php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        
        $cart = session()->get('carrito', []);
        $cartCount = count($cart);
        
        return view('cliente.completar-perfil', compact('cliente', 'cartCount'));
    }

    /**
     * Actualizar datos del cliente
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
        
        $telefonoAnterior = $cliente->telefono;
        
        $cliente->update([
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'estado' => $request->estado,
            'codigo_postal' => $request->codigo_postal
        ]);

        $pedidosActualizados = $cliente->pedidos()
            ->update([
                'cliente_telefono' => $request->telefono
            ]);

        $mensaje = 'Datos actualizados correctamente';
        
        if ($request->telefono && $request->telefono !== $telefonoAnterior && $pedidosActualizados > 0) {
            $mensaje .= " y se actualizó el teléfono en tus pedidos";
        }

        return redirect()->route('cliente.dashboard')
            ->with('success', $mensaje);
    }

    /**
     * Mostrar todos los pedidos del cliente
     */
    public function pedidos(Request $request)
    {
        $cliente = Auth::guard('cliente')->user();
        
        $query = $cliente->pedidos()->with('items');
        
        if ($request->has('estado') && $request->estado != 'todos') {
            $query->where('estado', $request->estado);
        }
        
        $pedidos = $query->orderBy('created_at', 'desc')->paginate(10);
        
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

        $cart = session()->get('carrito', []);
        $cartCount = count($cart);

        return view('cliente.pedido-detalle', compact('pedido', 'cartCount'));
    }

    /**
     * Cancelar un pedido (solo si está pendiente)
     */
    public function cancelarPedido($id)
    {
        try {
            $cliente = Auth::guard('cliente')->user();
            
            $pedido = $cliente->pedidos()
                ->where('estado', 'pendiente')
                ->findOrFail($id);

            // Guardar datos antes de cancelar
            $folio = $pedido->folio;
            $clienteNombre = $cliente->nombre;
            $clienteEmail = $cliente->email;
            $clienteTelefono = $cliente->telefono;
            $total = $pedido->total;

            // Actualizar estado del pedido
            $pedido->update(['estado' => 'cancelado']);

            // Buscar el responsable del pedido (vendedor/gerente que lo creó o lo tiene asignado)
            $responsable = DB::table('pedido_responsables')
                ->where('pedido_id', $pedido->id)
                ->first();
            
            // Si hay responsable, usar su ID; si no, usar ID del admin (1)
            $usuario_id = $responsable ? $responsable->usuario_id : 1;

            // Registrar historial con el responsable real
            DB::table('pedido_historial')->insert([
                'pedido_id' => $pedido->id,
                'usuario_id' => $usuario_id,
                'accion' => 'cancelado',
                'detalles' => 'Pedido cancelado por el cliente: ' . $cliente->nombre,
                'fecha' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // ============================================================
            // ENVIAR CORREOS DE NOTIFICACIÓN
            // ============================================================
            
            // 1. Correo para el CLIENTE (confirmación de cancelación)
            $dataCliente = [
                'cliente_nombre' => $clienteNombre,
                'folio' => $folio,
                'total' => $total,
                'fecha_cancelacion' => now()->format('d/m/Y H:i:s')
            ];
            
            Mail::send('emails.pedido-cancelado', $dataCliente, function($message) use ($clienteEmail) {
                $message->to($clienteEmail)
                        ->subject('🚫 Tu pedido ha sido cancelado - Tanques Tláloc');
            });
            
            // 2. Correo para el ADMINISTRADOR (notificación de cancelación)
            $adminEmail = env('MAIL_NOTIFICACIONES', 'rogeliolucas173@gmail.com');
            
            $dataAdmin = [
                'cliente_nombre' => $clienteNombre,
                'cliente_email' => $clienteEmail,
                'cliente_telefono' => $clienteTelefono,
                'folio' => $folio,
                'total' => $total,
                'fecha_cancelacion' => now()->format('d/m/Y H:i:s')
            ];
            
            Mail::send('emails.pedido-cancelado-admin', $dataAdmin, function($message) use ($adminEmail) {
                $message->to($adminEmail)
                        ->subject('⚠️ ALERTA: Cliente canceló un pedido - Tanques Tláloc');
            });
            
            Log::info('Correos de cancelación enviados para pedido: ' . $folio . ' - Cliente: ' . $clienteEmail . ' - Admin: ' . $adminEmail);
            
            return response()->json([
                'success' => true,
                'message' => 'Pedido cancelado correctamente'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El pedido no existe o ya no está pendiente'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reordenar un pedido
     */
    public function reordenarPedido($id)
    {
        $cliente = Auth::guard('cliente')->user();
        
        $pedido = $cliente->pedidos()
            ->with(['items.producto'])
            ->findOrFail($id);

        $carrito = session()->get('carrito', []);
        
        foreach ($pedido->items as $item) {
            $productoId = $item->producto_id;
            
            if (isset($carrito[$productoId])) {
                $carrito[$productoId]['cantidad'] += $item->cantidad;
            } else {
                $producto = $item->producto;
                $carrito[$productoId] = [
                    'id' => $productoId,
                    'codigo' => $producto->codigo ?? '',
                    'nombre' => $item->producto_nombre,
                    'precio' => $item->precio,
                    'cantidad' => $item->cantidad,
                    'litros' => $producto->litros ?? 0,
                    'imagen' => $producto->imagen ?? ''
                ];
            }
        }
        
        session(['carrito' => $carrito]);

        return redirect()->route('carrito')
            ->with('success', '¡Productos agregados al carrito correctamente!');
    }
}