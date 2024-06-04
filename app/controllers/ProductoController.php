<?php


include_once './Models/Producto.php';
include_once './interfaces/IApiUsable.php';

class ProductoController implements IApiUsable{

    
    public function Cargar($request, $response, $args)
    {   
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $sector = $parametros['sector'];
        $precio = $parametros['precio'];
        $tiempo_estimado = $parametros['tiempo_estimado'];
  
        
        // Creamos el estado
        $prod = new Producto();
        $prod->nombre = $nombre;
        $prod->sector = $sector;
        $prod->precio = $precio;
        $prod->tiempo_estimado = $tiempo_estimado;
        $prod->crearProducto();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $producto = Producto::GetById($id);

        if($producto === false){
            $payload = json_encode("No se encontro el producto");
        }else{
            $payload = json_encode($producto);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    
}

