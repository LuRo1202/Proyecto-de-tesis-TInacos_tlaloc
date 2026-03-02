<?php
// app/Helpers/CarritoHelper.php

namespace App\Helpers;

use App\Models\Producto;
use Illuminate\Support\Facades\Session;

class CarritoHelper
{
    /**
     * Obtiene el carrito de la sesión
     */
    public static function getCarrito()
    {
        return Session::get('carrito', []);
    }

    /**
     * Formatea un precio a moneda mexicana
     */
    public static function formatoPrecio($precio)
    {
        return '$' . number_format($precio, 2);
    }

    /**
     * Obtiene la imagen de un producto
     */
    public static function obtenerImagenProducto($codigo)
    {
        return asset('assets/img/productos/' . $codigo . '.jpg');
    }

    /**
     * Agrega un producto al carrito con validación de stock
     */
    public static function agregar($productoId, $cantidad = 1)
    {
        $carrito = self::getCarrito();
        $sucursal = SucursalHelper::getSucursalActual();
        
        // Verificar que el producto existe
        $producto = Producto::with('color')->find($productoId);
        
        if (!$producto) {
            return [
                'success' => false,
                'message' => 'Producto no encontrado'
            ];
        }
        
        // OBTENER STOCK REAL EN SUCURSAL
        $stockInfo = $sucursal->productos()
            ->where('productos.id', $productoId)
            ->withPivot('existencias')
            ->first();
            
        $existencias = $stockInfo ? (int)$stockInfo->pivot->existencias : 0;
        
        // Calcular cantidad actual en carrito
        $cantidadEnCarrito = isset($carrito[$productoId]) ? $carrito[$productoId]['cantidad'] : 0;
        $nuevaCantidad = $cantidadEnCarrito + $cantidad;
        
        // VALIDAR CONTRA EL STOCK REAL
        if ($nuevaCantidad > $existencias) {
            return [
                'success' => false,
                'message' => "Solo hay $existencias unidades disponibles. Ya tienes $cantidadEnCarrito en tu carrito."
            ];
        }

        // Agregar o actualizar carrito
        if (isset($carrito[$productoId])) {
            $carrito[$productoId]['cantidad'] = $nuevaCantidad;
        } else {
            $variante = ProductoHelper::obtenerInfoVariante($producto);
            
            $carrito[$productoId] = [
                'id' => $productoId,
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio_final,
                'precio_original' => $producto->precio,
                'cantidad' => $cantidad,
                'color' => $variante['nombre'],
                'color_hex' => $variante['hex'],
                'imagen' => ProductoHelper::obtenerImagenProducto($producto->codigo),
                'en_oferta' => $producto->en_oferta,
                'descuento' => $producto->porcentaje_descuento,
                'litros' => $producto->litros
            ];
        }

        Session::put('carrito', $carrito);
        
        return [
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'carrito' => $carrito,
            'total' => self::calcularTotal($carrito)
        ];
    }

    /**
     * Calcula el total del carrito
     */
    public static function calcularTotal($carrito = null)
    {
        $carrito = $carrito ?? self::getCarrito();
        $total = 0;
        
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        return $total;
    }

    /**
     * Obtiene el total formateado
     */
    public static function totalFormateado()
    {
        return self::formatoPrecio(self::calcularTotal());
    }

    /**
     * Vacía el carrito
     */
    public static function vaciar()
    {
        Session::forget('carrito');
    }

    /**
     * Elimina un producto del carrito
     */
    public static function eliminar($productoId)
    {
        $carrito = self::getCarrito();
        
        if (isset($carrito[$productoId])) {
            unset($carrito[$productoId]);
            Session::put('carrito', $carrito);
        }
        
        return $carrito;
    }

    /**
     * Obtiene el contador de items del carrito
     */
    public static function getCartCount()
    {
        $carrito = self::getCarrito();
        
        if (empty($carrito)) {
            return 0;
        }
        
        $total = 0;
        foreach ($carrito as $item) {
            $total += isset($item['cantidad']) ? (int)$item['cantidad'] : 0;
        }
        
        return $total;
    }

    /**
     * Obtiene los productos del carrito con datos completos para la vista
     */
    public static function getProductosCarrito($sucursal)
    {
        $carrito = self::getCarrito();
        $productosCarrito = collect();
        $total = 0;
        
        if (!empty($carrito)) {
            foreach ($carrito as $id => $item) {
                $producto = Producto::with(['categoria', 'color', 'ofertas'])->find($id);
                
                if ($producto) {
                    $stock = $sucursal->productos()
                        ->where('productos.id', $id)
                        ->withPivot('existencias')
                        ->first();
                    
                    $existencias = $stock ? (int)$stock->pivot->existencias : 0;
                    
                    $precio_original = (float)$producto->precio;
                    $en_oferta = $producto->en_oferta;
                    $precio_final = $producto->precio_final;
                    $porcentaje_descuento = $producto->porcentaje_descuento;
                    
                    $precio_a_usar = $en_oferta ? $precio_final : $precio_original;
                    $subtotal = $precio_a_usar * $item['cantidad'];
                    
                    $productosCarrito->push([
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'precio' => $precio_a_usar,
                        'precio_original' => $precio_original,
                        'en_oferta' => $en_oferta,
                        'porcentaje_descuento' => $porcentaje_descuento,
                        'codigo' => $producto->codigo,
                        'litros' => $producto->litros,
                        'existencias' => $existencias,
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $subtotal
                    ]);
                    
                    $total += $subtotal;
                }
            }
        }
        
        return [
            'productos' => $productosCarrito,
            'total' => $total,
            'cartCount' => self::getCartCount()
        ];
    }
}