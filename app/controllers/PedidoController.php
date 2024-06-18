<?php


include_once './Models/Pedido.php';
include_once './Models/Producto.php';
include_once './Models/Usuario.php';
include_once './Models/PedidoProducto.php';
include_once './Models/Mesa.php';
include_once './Models/Estado.php';
include_once './interfaces/IApiUsable.php';

class PedidoController implements IApiUsable{


    public static function Cargar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $nombre_cliente = $parametros['nombre_cliente'];
        $estado = Estado::PEND;
        $codigo_mesa = $parametros['codigo_mesa'];
        $arrayProductos = $parametros['productos'];
        $productos = [];
        $tiempo = 0;
        $precio_total = 0;

        if($arrayProductos != null) 
        {
            foreach ($arrayProductos as $productoBuscado) 
            {
               $productoPorId = Producto::obtenerPorId(($productoBuscado['id']));

               if($productoPorId != null)
               {    
                    $precio_total += $productoPorId->getPrecio();
                    $productos[] = $productoPorId;
               } else {

                $payload = json_encode(array("mensaje" => "El producto no existe"));
                $response->getBody()->write($payload);
                return $response
                  ->withHeader('Content-Type', 'application/json');   
                }
            }   
        }

        //genero pedido
        $pedido = new Pedido();
        $pedido->setCodigoMesa($codigo_mesa);
        $pedido->setNombreCliente($nombre_cliente);
        $pedido->setCodigoPedido();
        $pedido->setEstado($estado);
        $pedido->setTiempo($tiempo);
        $pedido->setPrecioTotal($precio_total);

        Pedido::crearPedido($pedido);

        //insert en la tabla pedidos_productos

        foreach ($productos as $producto) {
            $pedidos_productos = new PedidoProducto();
            $pedidos_productos->setCodigoPedido($pedido->getCodigoPedido());
            $pedidos_productos->setIdProducto($producto->getId());
            $pedidos_productos->setProductoEstado(Estado::PEND);
            $pedidos_productos->setNombreProducto($producto->getNombre());

            PedidoProducto::crearPedidoProducto($pedidos_productos);

        }

        $mesa = Mesa::obtenerPorId($codigo_mesa);
        $mesa->setEstado(Estado::ESPERANDO);
        Mesa::modificar(($mesa));

        $payload = json_encode(array("mensaje" => "Pedido creado correctamente. " . "El codigo de pedido es: " . $pedido->getCodigoPedido() . " y el codigo de mesa es: " . $codigo_mesa));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }

    //modifica el estado del pedido con codigo de pedido y verifica rol y estado del empleado
    
    public static function modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo_pedido = $args['codigo_pedido'];
        $id_producto = $args['id_producto'];
        $estado = $parametros['estado'];

        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        $pedido = Pedido::obtenerPorId($codigo_pedido);
        $usuario = Usuario::obtenerPorId($data->id);
        
        if($pedido != false)
        {
            $pedidoProducto = PedidoProducto::obtenerIdProductoCodigoPedido($id_producto, $codigo_pedido);
            if(!$pedidoProducto)
            {
                $payload = json_encode(array("mensaje" => "No se encontro ese producto para ese codigo de pedido."));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        
            if(!$usuario->getBaja())
            {
                $producto = Producto::obtenerPorId($id_producto);

                if(!$producto->validarSectorRol($usuario->getRol()))
                {
                    $payload = json_encode(array("mensaje" => "El usuario que intenta modificar el pedido es de un sector distinto al producto. El sector del empleado es: " . $usuario->getRol() . " y el sector del producto es: " . $producto->getSector()));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
            }else{
                $payload = json_encode(array("mensaje" => "El empleado que intenta tomar el pedido esta dado de baja. En la fecha: " . $usuario->get_fecha_baja()));
                $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
            }

            if($estado == Estado::ENTREGADO && $usuario->getRol() != "mozo")
            {
                $payload = json_encode(array("mensaje" => "El estado del pedido 'Entregado' solo puede ser modificado por el mozo"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

                $pedidoProducto->setIdUsuario($data->id);
                $pedidoProducto->setProductoEstado($estado);

                PedidoProducto::modificar($pedidoProducto);

                $pedido->setEstado($estado);
                Pedido::modificarEstado($pedido);

                $mesa = Mesa::obtenerPorId($pedido->getCodigoMesa());
                $mesa->setEstado(Estado::COMIENDO);
                Mesa::modificar($mesa);

                $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        } else{
             $payload = json_encode(array("mensaje" => "Codigo del pedido no coincide con ningun Pedido"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

     
    public static function traerPedidosPendientes($request, $response, $args)
    {
        $header = $request->getHeaderLine("Authorization");
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        if($data->rol == 'socio')
        {
            $listaPedido = Pedido::obtenerTodos();
            $payload =json_encode(array("listaPedidos" => $listaPedido));
        }
        else{
            $listaPedido = Pedido::obtenerPendientesPorRol($data->rol);
            $payload = json_encode(array("lista de Pedidos pendientes" => $listaPedido));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $pedido = Pedido::obtenerPorId($id);

        if($pedido === false){
            $payload = json_encode("No se encontro el pedido");
        }else{
            $payload = json_encode($pedido);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    
}

