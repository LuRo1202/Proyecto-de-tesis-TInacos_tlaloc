<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Oferta;
use App\Helpers\SucursalHelper;
use App\Helpers\ProductoHelper;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener sucursal actual (Ecatepec por defecto)
        $sucursal = SucursalHelper::getSucursalActual();
        
        // Si no hay sucursal, usamos lógica por defecto
        if (!$sucursal) {
            $productosDestacados = Producto::with(['categoria', 'color', 'ofertas'])
                ->where('destacado', true)
                ->where('activo', true)
                ->limit(12)
                ->get();
                
            $productosEnOferta = Producto::with(['categoria', 'color', 'ofertas'])
                ->enOferta()
                ->where('activo', true)
                ->limit(8)
                ->get();
                
            $categorias = Categoria::all();
            $ofertasActivas = Oferta::vigente()->get();
            
            $cart = session()->get('carrito', []);
            $cartCount = count($cart);
            
            return view('home', compact(
                'productosDestacados', 
                'productosEnOferta',
                'categorias', 
                'cartCount',
                'ofertasActivas'
            ));
        }

        // Productos destacados de esta sucursal con stock
        $productosDestacados = $sucursal->productos()
            ->where('destacado', true)
            ->where('activo', true)
            ->wherePivot('existencias', '>', 0)
            ->withPivot('existencias')
            ->with(['categoria', 'color', 'ofertas'])
            ->limit(12)
            ->get();

        // Productos en oferta (de cualquier sucursal o filtrados)
        $productosEnOferta = Producto::with(['categoria', 'color', 'ofertas'])
            ->enOferta()
            ->where('activo', true)
            ->whereHas('sucursales', function($query) use ($sucursal) {
                $query->where('sucursal_id', $sucursal->id)
                      ->where('existencias', '>', 0);
            })
            ->limit(8)
            ->get();

        // Ofertas activas para mostrar banners
        $ofertasActivas = Oferta::vigente()->get();

        // Todas las categorías
        $categorias = Categoria::all();

        // Carrito
        $cart = session()->get('carrito', []);
        $cartCount = count($cart);

        return view('home', compact(
            'productosDestacados',
            'productosEnOferta',
            'categorias',
            'cartCount',
            'sucursal',
            'ofertasActivas'
        ));
    }

    /**
     * Procesar formulario de contacto
     */
    public function enviarContacto(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'mensaje' => 'required|string'
        ]);

        // Aquí puedes enviar email o guardar en BD
        // Por ahora solo retornamos éxito
        
        return back()->with('success', '¡Mensaje enviado correctamente! Te contactaremos pronto.');
    }
}