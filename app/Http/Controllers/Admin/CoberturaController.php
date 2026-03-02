<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sucursal;
use App\Helpers\CoberturaHelper;
use Illuminate\Support\Facades\Log;

class CoberturaController extends Controller
{
    /**
     * Verificar cobertura desde el panel de admin
     */
    public function verificar(Request $request)
    {
        $request->validate([
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'sucursal_id' => 'required|exists:sucursales,id'
        ]);

        try {
            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            
            // Usar el helper para verificar cobertura
            $resultado = CoberturaHelper::verificarCobertura(
                $sucursal,
                $request->direccion,
                $request->ciudad,
                $request->estado
            );

            if ($resultado['valido']) {
                // Guardar en sesión de admin
                session()->put('admin_cobertura', [
                    'valido' => true,
                    'sucursal_id' => $sucursal->id,
                    'sucursal_nombre' => $sucursal->nombre,
                    'sucursal_direccion' => $sucursal->direccion,
                    'distancia' => $resultado['distancia'],
                    'direccion_cliente' => $request->direccion,
                    'ciudad' => $request->ciudad,
                    'estado' => $request->estado
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
                    'message' => $resultado['mensaje']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error en Admin Cobertura: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar cobertura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Limpiar cobertura de la sesión
     */
    public function limpiar(Request $request)
    {
        session()->forget('admin_cobertura');
        
        return response()->json([
            'success' => true,
            'message' => 'Cobertura limpiada'
        ]);
    }

    /**
     * Obtener datos de cobertura de la sesión
     */
    public function obtener(Request $request)
    {
        $cobertura = session('admin_cobertura');
        
        if ($cobertura) {
            return response()->json([
                'success' => true,
                'data' => $cobertura
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No hay cobertura verificada'
        ]);
    }
}