<?php


include_once './Models/Pedido.php';
include_once './Models/Producto.php';
include_once './Models/Usuario.php';
include_once './Models/PedidoProducto.php';
include_once './Models/Mesa.php';
include_once './interfaces/IApiUsable.php';

class PedidoController implements IApiUsable{

    
    public function Cargar($request, $response, $args)
    {   
        $parametros = $request->getParsedBody();
        $estado = $parametros['estado'];
        $tiempo = $parametros['tiempo'];
        $codigo_mesa = $parametros['codigo_mesa'];
        $codigo_pedido = $parametros['codigo_pedido'];
        $precio_total = $parametros['precio_total'];
        $nombre_cliente = $parametros['nombre_cliente'];
        
        // Creamos el estado
        $pedido = new Pedido();
        $pedido->estado = $estado;
        $pedido->tiempo = $tiempo;
        $pedido->codigo_mesa = $codigo_mesa;
        $pedido->codigo_pedido = $codigo_pedido;
        $pedido->precio_total = $precio_total;
        $pedido->nombre_cliente = $nombre_cliente;
        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
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
        $pedido = Pedido::GetById($id);

        if($pedido === false){
            $payload = json_encode("No se encontro el pedido");
        }else{
            $payload = json_encode($pedido);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    
}

