<?php


include_once './models/Usuario.php';
include_once './models/Encuesta.php';

class RegistroController{



    

    public static function TraerMesaMasUsada($request, $response, $args)
    {
        $codigo_mesa = Pedido::obtenerPedidoMesaMasUsada();
        $mesa = Mesa::obtenerMesa($codigo_mesa);

        if ($mesa === false) {
            $payload = json_encode("No se encontro la mesa");
        } else {
            $payload = json_encode($mesa);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }
    public static function TraerMesaMenosUsada($request, $response, $args)
    {
        $codigo_mesa = Pedido::obtenerPedidoMesaMenosUsada();
        $mesa = Mesa::obtenerMesa($codigo_mesa);

        if ($mesa === false) {
            $payload = json_encode("No se encontro la mesa");
        } else {
            $payload = json_encode($mesa);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerCantidadOperacionesRol($request, $response, $args)
    {
        $rol = $args['rol'];
        $usuario = Usuario::obtenerCantidadOperacionesRol($rol);

        if ($usuario === false) {
            $payload = json_encode("No se encontro el usuario");
        } else {
            $payload = json_encode(array("cantidad de operaciones del rol: " => $rol . ' :'. $usuario));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public static function TraerCantidadOperacionesUsuarioRol($request, $response, $args)
    {
        $rol = $args['rol'];
        $nombreUsuario = $args['nombreUsuario'];
        $usuario = Usuario::obtenerCantidadOperacionesPorRolUsuario($rol, $nombreUsuario);

        if ($usuario === false) {
            $payload = json_encode("No se encontro el usuario");
        } else {
            $payload = json_encode(array("cantidad operaciones del rol: " => $rol , "usuarios"=> $usuario));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerCantidadOperacionesPorSeparado($request, $response)
    {
       // $rol = $args['rol'];
       // $nombreUsuario = $args['nombreUsuario'];
        $usuario = Usuario::obtenerCantidadOperacionesPorSeparado();

        if ($usuario === false) {
            $payload = json_encode("No se encontro el usuario");
        } else {
            $payload = json_encode(array("usuarios"=> $usuario));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function TraerLogueosPorSector($request, $response, $args)
    {
        $rol = $args['rol'];
        $usuario = Usuario::obtenerOperacionesPorRol($rol);

        if ($usuario === false) {
            $payload = json_encode("No se encontro el usuario");
        } else {
            $payload = json_encode(array("operaciones del rol: " => $rol , "usuarios"=> $usuario));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    

    public static function TraerMesaConMayorFacturacion($request, $response, $args)
    {
        $mesa = Mesa::obtenerMesaPorPrecioMayor();
        if ($mesa === false) {
            $payload = json_encode("No se encontro el usuario");
        } else {
            $payload = json_encode(array("codigo de mesa con mayor facturacion: " => $mesa));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerMesaConMenorFacturacion($request, $response, $args)
    {
        $mesa = Mesa::obtenerMesaPorPrecioMenor();
        if ($mesa === false) {
            $payload = json_encode("No se encontro el usuario");
        } else {
            $payload = json_encode(array("codigo de mesa con menor facturacion: " => $mesa));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerProductoMasVendido($request, $response, $args)
    {
        $producto = Pedido::obtenerProductoMasVendido();
        if ($producto === false) {
            $payload = json_encode("No se encontro el producto");
        } else {
            $payload = json_encode(array("Producto mas vendido" =>$producto));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerProductoMenosVendido($request, $response, $args)
    {
        $producto = Pedido::obtenerProductoMenosVendido();
        if ($producto === false) {
            $payload = json_encode("No se encontro el producto");
        } else {
            $payload = json_encode(array("Producto menos vendido" =>$producto));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function TraerProductoEntregadoTarde($request, $response, $args)
    {
        $producto = Producto::obtenerProductoEntregadoTarde();
        if ($producto === false) {
            $payload = json_encode("No se encontro el producto");
        } else {
            $payload = json_encode(array("Productos entregados tarde:" =>$producto));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    

    public static function TraerMesaConMayorImporte($request, $response, $args)
    {
        $mesa = Mesa::obtenerMesaPorMayorImporte();
        if ($mesa === false) {
            $payload = json_encode("No se encontro la mesa");
        } else {
            $payload = json_encode(array("codigo de mesa con el mayor importe: " => $mesa));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerMesaConMenorImporte($request, $response, $args)
    {
        $mesa = Mesa::obtenerMesaPorMenorImporte();
        if ($mesa === false) {
            $payload = json_encode("No se encontro la mesa");
        } else {
            $payload = json_encode(array("codigo de mesa con el menor importe: " => $mesa));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerMejoresComentarios($request, $response, $args)
    {
        $lista = Encuesta::obtenerMejorPuntuacionMesa();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    } 
    public static function TraerPeoresComentarios($request, $response, $args)
    {
        $lista = Encuesta::obtenerPeorPuntuacionMesa();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    public static function TraerFacturaEntreDosFechas($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $fecha1 = $parametros['fecha1'];
        $fecha2 = $parametros['fecha2'];
        $mesas = Factura::obtenerPedidosEntreFechas($fecha1, $fecha2);
        if($mesas)
        {
            $payload = json_encode(array("Total importe entre las dos fechas: " => $mesas));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}

?>