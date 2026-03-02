<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sucursal;
use App\Helpers\CoberturaHelper;
use Illuminate\Support\Facades\Log;

class CoberturaController extends Controller
{
    public function __construct()
    {
        // Verificar que el usuario es gerente
        $user = auth()->user();
        if (!$user || $user->rol !== 'gerente') {
            abort(403, 'Acceso no autorizado');
        }
    }

    /**
     * Verificar cobertura para el gerente
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
            // Verificar que la sucursal pertenece al gerente
            $usuarioSucursal = auth()->user()->sucursales()
                ->where('sucursal_id', $request->sucursal_id)
                ->exists();

            if (!$usuarioSucursal) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para verificar cobertura en esta sucursal'
                ], 403);
            }

            $sucursal = Sucursal::findOrFail($request->sucursal_id);
            
            // Usar el helper para verificar cobertura
            $resultado = CoberturaHelper::verificarCobertura(
                $sucursal,
                $request->direccion,
                $request->ciudad,
                $request->estado
            );

            if ($resultado['valido']) {
                // Guardar en sesión del gerente
                session()->put('cobertura_verificada', [
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
            Log::error('Error en Gerente Cobertura: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar cobertura: ' . $e->getMessage()
            ], 500);
        }
    }
}