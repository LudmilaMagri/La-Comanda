<?php


include_once './Models/Producto.php';
include_once './interfaces/IApiUsable.php';

class ProductoController implements IApiUsable{

    
    /*
    * Se genera la carga del producto
    *
    */
    public function Cargar($request, $response, $args)
    {   
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $sector = $parametros['sector'];
        $precio = $parametros['precio'];
  
        
        // Creamos el estado
        $prod = new Producto();
        $prod->nombre = $nombre;
        $prod->sector = $sector;
        $prod->precio = $precio;
        $prod->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        

        $usuario = $request->getAttribute('userData');
        $usuarioId = Usuario::obtenerUsuario($usuario->nombre);
        $logueo = new Logueo();
        $logueo->id_usuario = $usuarioId->id;
        $logueo->fecha = date('Y-m-d H:i:s');
        $logueo->tipo_operacion = 'CargarProducto';
        $logueo->rol = $usuario->rol;
        Logueo::crear($logueo);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    /*
    * Modifica el producto
    *
    */
    public static function Modificar($request, $response, $args)
    {
        $id = $args['id'];

        $producto = Producto::obtenerPorId($id);

        if ($producto == false) {
            $payload = json_encode(array("mensaje" => "No existe Id con esa producto"));
        } else {
            $parametros = $request->getParsedBody();
            if (isset($parametros['sector']) && isset($parametros['nombre']) && isset($parametros['precio'])) 
            {
                $sector = $parametros['sector'];
                $nombre = $parametros['nombre'];
                $precio = $parametros['precio'];

                $producto->setSector($sector);
                $producto->setNombre($nombre);
                $producto->setPrecio($precio);

                Producto::modificarProducto($producto);
                $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
            }else{

                $payload = json_encode(array("mensaje" => "Complete todos los campos"));

            }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /*
    * Borra el producto
    *
    */

    public static function Borrar($request, $response, $args)
    {
        $id = $args['id'];
        $producto = Producto::obtenerPorId($id);

        if($producto != false)
        {
            Producto::borrarProducto($producto);
            $payload = json_encode(array("mensaje" => "Producto borrada con exito"));
        }
        else{
            $payload = json_encode(array("mensaje" => "No se encontro Producto con ese codigo"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /*
    * Trae todos los productos
    *
    */
    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    /*
    * Trae un producto
    *
    */
    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $producto = Producto::obtenerPorId($id);

        if($producto === false){
            $payload = json_encode("No se encontro el producto");
        }else{
            $payload = json_encode($producto);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    
}

