<?php


include_once './Models/Pedido.php';
include_once './Models/Producto.php';
include_once './Models/Usuario.php';
include_once './Models/PedidoProducto.php';
include_once './Models/Mesa.php';
include_once './Models/Estado.php';
include_once './Models/Factura.php';
include_once './Models/TiempoPedido.php';
include_once './interfaces/IApiUsable.php';

class PedidoController implements IApiUsable
{


    /*
    * Se genera la carga del pedido
    *
    */
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

        if ($arrayProductos != null) {
            foreach ($arrayProductos as $productoBuscado) {
                $productoPorId = Producto::obtenerPorId(($productoBuscado));

                if ($productoPorId != null) {
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


        $pedido = new Pedido();
        $pedido->setCodigoMesa($codigo_mesa);
        $pedido->setNombreCliente($nombre_cliente);
        $pedido->setCodigoPedido();
        $pedido->setEstado($estado);
        $pedido->setTiempo($tiempo);
        $pedido->setPrecioTotal($precio_total);

        Pedido::crearPedido($pedido);

    //------------insert en la tabla pedidos_productos------------
    
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



        //------------guardo tipo de operacion por usuario------------
        
        $usuario = $request->getAttribute('userData');
        $usuarioId = Usuario::obtenerUsuario($usuario->nombre);
        $logueo = new Logueo();
        $logueo->id_usuario = $usuarioId->id;
        $logueo->fecha = date('Y-m-d H:i:s');
        $logueo->tipo_operacion = 'CargarPedido';
        $logueo->rol = $usuario->rol;

        Logueo::crear($logueo);

        return $response->withHeader('Content-Type', 'application/json');
    }

    /*
    * Se carga la foto de la mesa 
    *
    */

    public static function tomarFoto($request, $response, $args)
    {
        $foto = $request->getUploadedFiles()['foto'];
        $parametros = $request->getParsedBody();
        $codigo_mesa = $parametros['codigo_mesa'];
        Pedido::agregarFoto($foto->getStream()->getContents(), $codigo_mesa);
        $payload = json_encode(array("mensaje" => "Foto agregada exitosamente"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /*
    * Modifica el pedido, 
    * setea el tiempo estimado y modifica el estado
    *
    */
    public static function Modificar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo_pedido = $args['codigo_pedido'];
        $id = $parametros['id'];
        $estado = $parametros['estado'];
        $tiempo_estimado = $parametros['tiempo_estimado'];

        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        $pedido = Pedido::obtenerPorId($codigo_pedido);
        $usuario = Usuario::obtenerUsuario($data->nombre);
        $pedidoProducto = PedidoProducto::obtenerIdProductoCodigoPedido($id, $codigo_pedido);

        if ($pedido != false) {
            if (!$pedidoProducto) {
                $payload = json_encode(array("mensaje" => "No se encontro ese producto para ese codigo de pedido."));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            if (!$usuario->getBaja()) {
                $producto = Producto::obtenerPorId($pedidoProducto->id_producto);

                if (!$producto->validarSectorRol($usuario->getRol())) {
                    $payload = json_encode(array("mensaje" => "El usuario que intenta modificar el pedido es de un sector distinto al producto. El sector del empleado es: " . $usuario->getRol() . " y el sector del producto es: " . $producto->getSector()));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
            } else {
                $payload = json_encode(array("mensaje" => "El empleado que intenta tomar el pedido esta dado de baja. En la fecha: " . $usuario->get_fecha_baja()));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            //------------ modifica el estado de la mesa ------------
            $mesa = Mesa::obtenerPorId($pedido->getCodigoMesa());
            if ($estado == Estado::PREPARACION || ($estado == Estado::LISTO)) {
                $mesa->setEstado(Estado::ESPERANDO);
                Mesa::modificar($mesa);
            }
            if ($estado == Estado::ENTREGADO && $usuario->rol != "mozo") {
                $payload = json_encode(array("mensaje" => "El estado del pedido 'Entregado' solo puede ser modificado por el mozo"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            //------------modifica el estado y tiempo de tabla pedidos_productos------------
            $pedidoProducto->setProductoEstado($estado);
            $pedidoProducto->setTiempoEstimado($tiempo_estimado);
            PedidoProducto::modificarEstado($estado, $tiempo_estimado, $pedidoProducto->id);

            //------------ modifica el estado y tiempo de tabla pedidos------------
            $tiempo = Pedido::tiempoFinal($codigo_pedido);
            $pedido->setTiempo($tiempo);
            $pedido->setEstado($estado);
            Pedido::modificarEstadoTiempo($pedido);

            //------------se da el alta del tiempo en tabla tiempo_pedido------------
            $tiempoActual = new DateTime();
            $tiempoActual->add(new DateInterval('PT' . $tiempo_estimado . 'M'));
            if ($estado == ESTADO::PREPARACION) {
                $entregaEstimada = $tiempoActual->format('Y-m-d H:i:s');
                TiempoPedido::crearTiempoEspera($pedidoProducto->id, $entregaEstimada, $codigo_pedido);
            }else if($estado == 'cancelado')
            {
                Pedido::modificarPedidoCancelado($codigo_pedido);
            }

            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Codigo del pedido no coincide con ningun Pedido"));
        }


        //------------guardo tipo de operacion por usuario------------
        $usuarioId = Usuario::obtenerUsuario($data->nombre);
        $logueo = new Logueo();
        $logueo->id_usuario = $usuarioId->id;
        $logueo->fecha = date('Y-m-d H:i:s');
        $logueo->tipo_operacion = 'ModificarPedido';
        $logueo->rol = $usuario->rol;
        Logueo::crear($logueo);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /*
    * Modifica el pedido a "listo o "entregado" 
    *
    */
    public static function ModificarListoEntregado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigo_pedido = $args['codigo_pedido'];
        $id = $parametros['id'];
        $estado = $parametros['estado'];

        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        $pedido = Pedido::obtenerPorId($codigo_pedido);
        $usuario = Usuario::obtenerUsuario($data->nombre);
        $pedidoProducto = PedidoProducto::obtenerIdProductoCodigoPedido($id, $codigo_pedido);

        if ($pedido != false) {
            if (!$pedidoProducto) {
                $payload = json_encode(array("mensaje" => "No se encontro ese producto para ese codigo de pedido."));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            if (!$usuario->getBaja()) {
                $producto = Producto::obtenerPorId($pedidoProducto->id_producto);

                if (!$producto->validarSectorRol($usuario->getRol())) {
                    $payload = json_encode(array("mensaje" => "El usuario que intenta modificar el pedido es de un sector distinto al producto. El sector del empleado es: " . $usuario->getRol() . " y el sector del producto es: " . $producto->getSector()));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
            } else {
                $payload = json_encode(array("mensaje" => "El empleado que intenta tomar el pedido esta dado de baja. En la fecha: " . $usuario->getFechaBaja()));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            //------------modifica el estado de tabla pedidos_productos------------
            $pedidoProducto->setProductoEstado($estado);
            PedidoProducto::modificar($pedidoProducto);

            //------------ modifica el estado y tiempo de tabla pedidos------------
            $tiempo = Pedido::tiempoFinal($codigo_pedido);
            $pedido->setTiempo($tiempo);
            Pedido::modificarEstadoTiempo($pedido);

            //------------ modifica el tiempo en tabla tiempo_pedido y el estado de la mesa ------------
            $tiempoActual = new DateTime();
            $tiempoActualFormat = $tiempoActual->format('Y-m-d H:i:s');

            if ($estado == ESTADO::LISTO) {
                TiempoPedido::modificarTiempoEsperaListo($pedidoProducto->id, $tiempoActualFormat);
            } else if ($estado == ESTADO::ENTREGADO) {

                TiempoPedido::modificarTiempoEsperaEntregado($pedidoProducto->id, $tiempoActualFormat);
                TiempoPedido::modificarTiempoEsperaTarde();

                $mesa = Mesa::obtenerPorId($pedido->getCodigoMesa());
                $mesa->setEstado(Estado::COMIENDO);
                Mesa::modificar($mesa);

                if (PedidoProducto::verificarTodosEntregados($codigo_pedido)) {
                    $pedido->setEstado(Estado::ENTREGADO);
                    Pedido::modificarEstadoTiempo($pedido);
                }
            }else if($estado == 'cancelado')
            {
                Pedido::modificarPedidoCancelado($codigo_pedido);
            }


            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        } else {
            $payload = json_encode(array("mensaje" => "Codigo del pedido no coincide con ningun Pedido"));
        }


        //------------guardo tipo de operacion por usuario------------
        $usuarioId = Usuario::obtenerUsuario($data->nombre);
        $logueo = new Logueo();
        $logueo->id_usuario = $usuarioId->id;
        $logueo->fecha = date('Y-m-d H:i:s');
        $logueo->tipo_operacion = 'ModificarPedido';
        $logueo->rol = $usuario->rol;
        Logueo::crear($logueo);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /*
    * El cliente consulta el tiempo del pedido ingresando codigo_pedido
    *
    */

    public static function ConsultarTiempoPedido($request, $response, $args)
    {
        $codigo_pedido = $args['codigo_pedido'];
        $codigo_mesa = $args['codigo_mesa'];

        $tiempoEstimado = TiempoPedido::traerTiempoEntregaEstimada($codigo_pedido);

        $tiempoEstimadoDateTime = new DateTime($tiempoEstimado['entrega_estimada']);
        $tiempoActual = new DateTime();

        $intervalo = $tiempoActual->diff($tiempoEstimadoDateTime);

        if ($tiempoActual > $tiempoEstimadoDateTime) {
            $tiempoRestante = '-' . $intervalo->format('%H:%I:%S');
        } else {
            $tiempoRestante = $intervalo->format('%H:%I:%S');
        }

        $payload = json_encode(array("Numero de pedido: " => $codigo_pedido .' de la mesa: ' .$codigo_mesa . '. Su tiempo de espera es: '. $tiempoRestante));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /*
    * Se obtiene todos los pedidos con estado "en preparacion" y el tiempo restante.
    * Accion solo para socios.
    *
    */
    public static function TraerPedidosEnPreparacionYTiempo($request, $response, $args)
    {
        $pedidos = Pedido::obtenerTodos();
        $tiempoArray = [];
        
        foreach ($pedidos as $pedido) {
            $tiempoEstimado = (int)$pedido->tiempo;
            
            $tiempoActual = new DateTime();
            
            $tiempoEstimadoDateTime = clone $tiempoActual;
            $tiempoEstimadoDateTime->modify('-' .$tiempoEstimado . 'minutes');
            
            $intervalo = $tiempoActual->diff($tiempoEstimadoDateTime);
            
            if ($intervalo > $tiempoEstimadoDateTime) {
                $tiempoRestante = '-' . $intervalo->format('%H:%I:%S');
            } else {
                $tiempoRestante = $intervalo->format('%H:%I:%S');
            }
            $tiempoArray[] = [
            'codigo_pedido' => $pedido->codigo_pedido,
            'tiempo restante' => $tiempoRestante
        ];
    }
        
        $payload = json_encode(array("tiempo de espera estimado del pedido: " => $tiempoArray));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');



        /*
        $listaPedido = PedidoProducto::obtenerTodosEnPreparacion();

        $pedidosConTiempoEspera = [];
        foreach ($listaPedido as $pedido) {
            $tiempoEstimado = TiempoPedido::traerTiempoEntregaEstimada($pedido->codigo_pedido);
            if ($tiempoEstimado) {
                $tiempoEstimadoDateTime = new DateTime($tiempoEstimado['entrega_estimada']);
                $tiempoActual = new DateTime();

                $intervalo = $tiempoActual->diff($tiempoEstimadoDateTime);
                if ($tiempoActual > $tiempoEstimadoDateTime) {
                    $tiempoRestante = '-' . $intervalo->format('%H:%I:%S');
                } else {
                    $tiempoRestante = $intervalo->format('%H:%I:%S');
                }
                echo $tiempoRestante;
                echo $pedido->tiempo_restante;

                $pedido->tiempo_restante = $tiempoRestante;
                $pedidosConTiempoEspera[] = $pedido;
            } else {

                $pedidosConTiempoEspera[] = $pedido;
            }
        }

        $payload = json_encode(array("listaPedidos" => $pedidosConTiempoEspera));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');*/
    }


    /*
    * El cliente consulta el tiempo restante de su pedido ingresando codigo_pedido
    *
    */
    public static function traerPedidosPendientes($request, $response, $args)
    {
        $header = $request->getHeaderLine("Authorization");
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        if ($data->rol == 'socio') {
            $listaPedido = Pedido::obtenerTodos();
            $payload = json_encode(array("listaPedidos" => $listaPedido));
        } else {
            $listaPedido = Pedido::obtenerPendientesPorRol($data->rol);
            $payload = json_encode(array("lista de Pedidos pendientes" => $listaPedido));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /*
    * Se listan todos los pedidos con estado "listo para servir"
    *
    */
    public static function TraerPedidosListos($request, $response, $args)
    {
        $header = $request->getHeaderLine("Authorization");
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT::ObtenerData($token);

        if ($data->rol == 'mozo') {
            $listaPedido = PedidoProducto::obtenerTodosListos();
            $payload = json_encode(array("listaPedidos" => $listaPedido));
        } else {
            $payload = json_encode(array("mensaje" => 'Accion solo para mozos'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /*
    * Se listan todos los pedidos 
    *
    */
    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
   }

    /*
    * Se lista el pedido buscado por ID
    *
    */

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $pedido = Pedido::obtenerPorId($id);

        if ($pedido === false) {
            $payload = json_encode("No se encontro el pedido");
        } else {
            $payload = json_encode($pedido);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /*
    * Se consultan los pedidos con 30 dias de antiguedad.
    *
    */
    public static function TraerPedidosTreintaDias($request, $response, $args)
    {
        $fecha = new DateTime();
        $fechaStr = $fecha->format('Y-m-d');
        $pedidos = Factura::obtenerPedidosPorFecha($fechaStr);
        if($pedidos)
        {
            $payload = json_encode(array("Pedidos con 30 dias" => $pedidos));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    

    public static function TraerPedidosCancelados($request, $response, $args)
    {
        $lista = Pedido::obtenerTodosCancelados();
        $payload = json_encode(array("Pedidos cancelados:" => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
   }
}
