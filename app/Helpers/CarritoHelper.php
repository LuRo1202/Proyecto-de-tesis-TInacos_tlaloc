<?php
// app/Helpers/CarritoHelper.php

namespace App\Helpers;

use App\Models\Producto;
use App\Helpers\SucursalHelper;
use App\Helpers\ProductoHelper;
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
        return '$' . number_format($precio, 2, '.', ',');
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
        
        // Verificar si tiene oferta activa
        $ofertaActiva = $producto->ofertas()
            ->where('activa', true)
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->first();
        
        $tieneOferta = !is_null($ofertaActiva);
        $precioOriginal = (float)$producto->precio;
        $precioFinal = $precioOriginal;
        
        if ($tieneOferta) {
            if ($ofertaActiva->tipo === 'porcentaje') {
                $precioFinal = $precioOriginal * (1 - $ofertaActiva->valor / 100);
            } else {
                $precioFinal = $precioOriginal - $ofertaActiva->valor;
            }
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
                'precio' => $precioFinal,
                'precio_original' => $precioOriginal,
                'cantidad' => $cantidad,
                'color' => $variante['nombre'] ?? 'Sin color',
                'color_hex' => $variante['hex'] ?? '#000000',
                'imagen' => self::obtenerImagenProducto($producto->codigo),
                'tiene_oferta' => $tieneOferta,
                'tipo_oferta' => $tieneOferta ? $ofertaActiva->tipo : null,
                'valor_oferta' => $tieneOferta ? $ofertaActiva->valor : null,
                'descuento_texto' => $tieneOferta ? 
                    ($ofertaActiva->tipo === 'porcentaje' ? '-' . $ofertaActiva->valor . '%' : '-$' . number_format($ofertaActiva->valor, 0)) : null,
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
            $precio = $item['precio'] ?? 0;
            $cantidad = $item['cantidad'] ?? 0;
            $total += $precio * $cantidad;
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
     * ✅ VERSIÓN ÚNICA Y CORREGIDA
     */
    public static function getProductosCarrito($sucursal = null)
    {
        $cart = self::getCarrito();
        $productos = collect();
        $total = 0;
        $cartCount = 0;
        
        foreach ($cart as $id => $item) {
            $cantidad = is_array($item) ? ($item['cantidad'] ?? 1) : $item;
            
            // Buscar producto con sus relaciones
            $producto = Producto::with(['categoria', 'color'])->find($id);
            
            if (!$producto) {
                // Si el producto no existe, lo eliminamos del carrito
                self::eliminar($id);
                continue;
            }
            
            // Verificar stock en sucursal
            $existencias = 0;
            if ($sucursal) {
                $stock = $sucursal->productos()
                    ->where('productos.id', $id)
                    ->withPivot('existencias')
                    ->first();
                $existencias = $stock ? (int)$stock->pivot->existencias : 0;
            }
            
            // Verificar si tiene oferta activa
            $ofertaActiva = $producto->ofertas()
                ->where('activa', true)
                ->where('fecha_inicio', '<=', now())
                ->where('fecha_fin', '>=', now())
                ->first();
            
            $tieneOferta = !is_null($ofertaActiva);
            $precioOriginal = (float)$producto->precio;
            $precioFinal = $precioOriginal;
            $descuentoTexto = null;
            $ahorro = 0;
            
            if ($tieneOferta) {
                $tipoOferta = $ofertaActiva->tipo;
                $valorOferta = (float)$ofertaActiva->valor;
                
                if ($tipoOferta === 'porcentaje') {
                    $precioFinal = $precioOriginal * (1 - $valorOferta / 100);
                    $descuentoTexto = '-' . $valorOferta . '%';
                } else { // fijo
                    $precioFinal = $precioOriginal - $valorOferta;
                    $descuentoTexto = '-$' . number_format($valorOferta, 0, '.', ',');
                }
                
                $ahorro = $precioOriginal - $precioFinal;
            }
            
            // Usar el precio del carrito si existe (por si ya tenía descuento)
            $precioMostrar = $precioFinal;
            if (is_array($item) && isset($item['precio'])) {
                $precioMostrar = (float)$item['precio'];
            }
            
            $subtotal = $precioMostrar * $cantidad;
            
            $productos->push([
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $precioMostrar,
                'precio_original' => $precioOriginal,
                'codigo' => $producto->codigo,
                'litros' => $producto->litros,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
                'existencias' => $existencias,
                // Campos de oferta
                'tiene_oferta' => $tieneOferta,
                'tipo_oferta' => $tieneOferta ? $ofertaActiva->tipo : null,
                'valor_oferta' => $tieneOferta ? (float)$ofertaActiva->valor : null,
                'descuento_texto' => $descuentoTexto,
                'ahorro' => $ahorro,
                'porcentaje_ahorro' => $tieneOferta && $ofertaActiva->tipo === 'porcentaje' ? 
                    (float)$ofertaActiva->valor : 
                    ($ahorro > 0 ? round(($ahorro / $precioOriginal) * 100) : 0)
            ]);
            
            $total += $subtotal;
            $cartCount += $cantidad;
        }
        
        return [
            'productos' => $productos,
            'total' => $total,
            'cartCount' => $cartCount
        ];
    }
}