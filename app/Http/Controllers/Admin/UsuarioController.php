<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Sucursal;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
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

        // Sucursales activas
        $sucursales = Sucursal::where('activa', true)->orderBy('nombre')->get();

        // Paginación
        $registros_por_pagina = 5;
        $pagina_actual = $request->get('pagina', 1);
        $offset = ($pagina_actual - 1) * $registros_por_pagina;

        // Total usuarios
        $total_usuarios = Usuario::count();
        $total_paginas = ceil($total_usuarios / $registros_por_pagina);

        // Usuarios con sucursal
        $usuarios = Usuario::select('usuarios.*', 's.nombre as sucursal_nombre', 's.id as sucursal_id')
            ->leftJoin('usuario_sucursal as us', 'usuarios.id', '=', 'us.usuario_id')
            ->leftJoin('sucursales as s', 'us.sucursal_id', '=', 's.id')
            ->orderBy('usuarios.activo', 'desc')
            ->orderBy('usuarios.rol')
            ->orderBy('usuarios.nombre')
            ->offset($offset)
            ->limit($registros_por_pagina)
            ->get();

        // Estadísticas
        $total_activos = Usuario::where('activo', true)->count();
        $total_admins = Usuario::where('rol', 'admin')->count();
        $total_gerentes = Usuario::where('rol', 'gerente')->count();
        $total_vendedores = Usuario::where('rol', 'vendedor')->count();

        return view('admin.usuarios.index', compact(
            'usuarios',
            'sucursales',
            'total_usuarios',
            'total_activos',
            'total_admins',
            'total_gerentes',
            'total_vendedores',
            'total_paginas',
            'pagina_actual'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario' => 'required|unique:usuarios,usuario',
            'nombre' => 'required',
            'contrasena' => 'required|min:6',
            'email' => 'nullable|email',
            'rol' => 'required|in:admin,gerente,vendedor',
            'sucursal_id' => 'nullable|exists:sucursales,id'
        ]);

        try {
            DB::beginTransaction();

            // Crear usuario CON CONTRASEÑA CIFRADA
            $usuario = Usuario::create([
                'usuario' => $request->usuario,
                'nombre' => $request->nombre,
                'email' => $request->email,
                'contrasena_hash' => Hash::make($request->contrasena), // 👈 CIFRAR
                'rol' => $request->rol,
                'activo' => true
            ]);

            // Asignar sucursal si es necesario
            if (($request->rol === 'vendedor' || $request->rol === 'gerente') && $request->sucursal_id) {
                DB::table('usuario_sucursal')->insert([
                    'usuario_id' => $usuario->id,
                    'sucursal_id' => $request->sucursal_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('admin.usuarios')->with('swal', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'message' => 'Usuario creado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:usuarios,id',
            'nombre' => 'required',
            'email' => 'nullable|email',
            'contrasena' => 'nullable|min:6',
            'rol' => 'required|in:admin,gerente,vendedor',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'activa' => 'nullable'
        ]);

        try {
            DB::beginTransaction();

            $usuario = Usuario::findOrFail($request->id);

            // Actualizar usuario
            $data = [
                'nombre' => $request->nombre,
                'email' => $request->email,
                'rol' => $request->rol,
                'activo' => $request->has('activa')
            ];

            // Solo cifrar y actualizar si se proporcionó nueva contraseña
            if (!empty($request->contrasena)) {
                $data['contrasena_hash'] = Hash::make($request->contrasena); // 👈 CIFRAR
            }

            $usuario->update($data);

            // Eliminar asignaciones existentes
            DB::table('usuario_sucursal')->where('usuario_id', $usuario->id)->delete();

            // Asignar nueva sucursal si es necesario
            if (($request->rol === 'vendedor' || $request->rol === 'gerente') && $request->sucursal_id) {
                DB::table('usuario_sucursal')->insert([
                    'usuario_id' => $usuario->id,
                    'sucursal_id' => $request->sucursal_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('admin.usuarios')->with('swal', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'message' => 'Usuario actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ]);
        }
    }

    public function toggle(Request $request)
    {
        $id = $request->get('id');
        
        // Proteger propia cuenta
        if ((int)$id === (int)auth()->id()) {
            return redirect()->route('admin.usuarios')->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'No puedes desactivar tu propia cuenta'
            ]);
        }
        
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->update([
                'activo' => !$usuario->activo
            ]);

            return redirect()->route('admin.usuarios')->with('swal', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'message' => 'Estado del usuario actualizado'
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ]);
        }
    }
    
    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:usuarios,id'
        ]);

        $id = $request->id;

        if ((int)$id === (int)auth()->id()) {
            return redirect()->route('admin.usuarios')->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'No puedes eliminar tu propia cuenta'
            ]);
        }

        try {
            DB::beginTransaction();

            // Eliminar asignaciones de sucursal
            DB::table('usuario_sucursal')->where('usuario_id', $id)->delete();

            // Eliminar usuario
            Usuario::findOrFail($id)->delete();

            DB::commit();

            return redirect()->route('admin.usuarios')->with('swal', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'message' => 'Usuario eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('swal', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ]);
        }
    }
}