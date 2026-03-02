<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sucursal;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SucursalController extends Controller
{
    public function index()
    {
        // Contadores para el sidebar
        $pedidos_pendientes_count = Pedido::where('estado', 'pendiente')->count();
        $productos_bajos_count = DB::table('producto_sucursal')
            ->where('existencias', '<=', 5)
            ->distinct('producto_id')
            ->count('producto_id');

        session([
            'pedidos_pendientes_count' => $pedidos_pendientes_count,
            'productos_bajos_count' => $productos_bajos_count
        ]);

        // Obtener sucursales
        $sucursales = Sucursal::orderBy('activa', 'desc')
            ->orderBy('nombre')
            ->get();

        // ✅ CORREGIDO: Usar sucursal_id en lugar de sucursal_asignada
        $estadisticas = [
            'sucursales_activas' => Sucursal::where('activa', true)->count(),
            'total_pedidos' => Pedido::count(),
            'pedidos_asignados' => Pedido::whereNotNull('sucursal_id')->count()
        ];

        return view('admin.sucursales.index', compact('sucursales', 'estadisticas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:sucursales,nombre',
            'direccion' => 'required',
            'telefono' => 'required',
            'email' => 'nullable|email',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'radio_cobertura' => 'required|integer|min:1|max:50'
        ]);

        try {
            Sucursal::create([
                'nombre' => $request->nombre,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'radio_cobertura_km' => $request->radio_cobertura,
                'activa' => true,
                'horario' => $request->horario ?? 'Lunes a Viernes 9:00 - 18:00' // Valor por defecto
            ]);

            return redirect()->route('admin.sucursales')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Éxito!',
                    'message' => 'Sucursal creada correctamente'
                ]);

        } catch (\Exception $e) {
            Log::error('Error al crear sucursal: ' . $e->getMessage());
            
            return redirect()->back()->withInput()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al crear la sucursal: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sucursales,id',
            'nombre' => 'required|unique:sucursales,nombre,' . $request->id,
            'direccion' => 'required',
            'telefono' => 'required',
            'email' => 'nullable|email',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'radio_cobertura' => 'required|integer|min:1|max:50',
            'activa' => 'nullable'
        ]);

        try {
            $sucursal = Sucursal::findOrFail($request->id);
            
            $sucursal->update([
                'nombre' => $request->nombre,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'radio_cobertura_km' => $request->radio_cobertura,
                'activa' => $request->has('activa'),
                'horario' => $request->horario ?? $sucursal->horario
            ]);

            return redirect()->route('admin.sucursales')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Éxito!',
                    'message' => 'Sucursal actualizada correctamente'
                ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar sucursal: ' . $e->getMessage());
            
            return redirect()->back()->withInput()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al actualizar la sucursal: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sucursales,id'
        ]);

        try {
            $sucursal = Sucursal::findOrFail($request->id);
            
            // ✅ CORREGIDO: Verificar pedidos por sucursal_id
            $pedidos = Pedido::where('sucursal_id', $sucursal->id)->count();
            
            if ($pedidos > 0) {
                return redirect()->route('admin.sucursales')->with('swal', [
                    'type' => 'error',
                    'title' => 'No se puede eliminar',
                    'message' => "La sucursal tiene {$pedidos} pedidos asignados"
                ]);
            }

            // Verificar productos asociados
            $productos = DB::table('producto_sucursal')
                ->where('sucursal_id', $sucursal->id)
                ->count();
            
            if ($productos > 0) {
                return redirect()->route('admin.sucursales')->with('swal', [
                    'type' => 'error',
                    'title' => 'No se puede eliminar',
                    'message' => "La sucursal tiene {$productos} productos en inventario"
                ]);
            }

            // Verificar usuarios asignados
            $usuarios = DB::table('usuario_sucursal')
                ->where('sucursal_id', $sucursal->id)
                ->count();
            
            if ($usuarios > 0) {
                return redirect()->route('admin.sucursales')->with('swal', [
                    'type' => 'error',
                    'title' => 'No se puede eliminar',
                    'message' => "La sucursal tiene {$usuarios} usuarios asignados"
                ]);
            }

            $sucursal->delete();

            return redirect()->route('admin.sucursales')
                ->with('swal', [
                    'type' => 'success',
                    'title' => '¡Éxito!',
                    'message' => 'Sucursal eliminada correctamente'
                ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar sucursal: ' . $e->getMessage());
            
            return redirect()->back()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al eliminar la sucursal: ' . $e->getMessage()
            ]);
        }
    }
}