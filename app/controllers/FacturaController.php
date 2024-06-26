<?php

include_once './Models/Pedido.php';
include_once './Models/Factura.php';
include_once './Models/Producto.php';
include_once './Models/Usuario.php';
include_once './Models/Logueo.php';
include_once './Models/PedidoProducto.php';
include_once './Models/Mesa.php';


class FacturaController
{



    /*
    * Mozo cobra la cuenta: se genera el alta de la Factura
    *
    */
    public static function Cargar($request, $response, $args)
    {   
        $parametros = $request->getParsedBody();
        $codigo_pedido = $parametros['codigo_pedido'];
        $pedido = Pedido::obtenerPorId($codigo_pedido);
        if($pedido)
        {
            $factura = new Factura();
            $factura->setCodigoMesa($pedido->getCodigoMesa());
            $factura->setCodigoPedido($pedido->getCodigoPedido());
            $factura->setImporte($pedido->getPrecioTotal());
            $factura->setFecha(date('Y-m-d H:i:s'));
            Factura::crearFactura($factura);
    
            $payload = json_encode(array("mensaje" => "Factura creado con exito"));
            
            $usuario = $request->getAttribute('userData');
            $usuarioId = Usuario::obtenerUsuario($usuario->nombre);
            $logueo = new Logueo();
            $logueo->id_usuario = $usuarioId->id;
            $logueo->fecha = date('Y-m-d H:i:s');
            $logueo->tipo_operacion = 'CobrarPedido';
            $logueo->rol = $usuario->rol;
            Logueo::crear($logueo);

        }else{
            $payload = json_encode(array("mensaje" => "No existe ese codigo de pedido"));
        }

        
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


}


?>