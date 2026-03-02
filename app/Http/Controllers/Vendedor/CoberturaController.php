<?php
namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sucursal;
use App\Helpers\CoberturaHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CoberturaController extends Controller
{
    public function verificar(Request $request)
    {
        $request->validate([
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100'
        ]);

        try {
            $usuario = Auth::user();
            
            // Obtener la sucursal del vendedor
            $sucursal = $usuario->sucursales()->first();
            
            if (!$sucursal) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una sucursal asignada.'
                ], 403);
            }

            // Usar el helper para verificar cobertura
            $resultado = CoberturaHelper::verificarCobertura(
                $sucursal,
                $request->direccion,
                $request->ciudad,
                $request->estado
            );

            if ($resultado['valido']) {
                // Guardar en sesión del vendedor
                session()->put('cobertura_verificada_vendedor', [
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
            Log::error('Error en Vendedor Cobertura: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar cobertura: ' . $e->getMessage()
            ], 500);
        }
    }

    public function limpiar(Request $request)
    {
        session()->forget('cobertura_verificada_vendedor');
        
        return response()->json([
            'success' => true,
            'message' => 'Cobertura limpiada'
        ]);
    }
}