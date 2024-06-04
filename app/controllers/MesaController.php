<?php 


include_once './Models/Mesa.php';
include_once './interfaces/IApiUsable.php';

class MesaController implements IApiUsable{

    
    public function Cargar($request, $response, $args)
    {   
        $parametros = $request->getParsedBody();
        $estado = $parametros['estado'];
        $codigo_mesa = $parametros['codigo_mesa'];
        
        // Creamos el estado
        $usr = new Mesa();
        $usr->estado = $estado;
        $usr->codigo_mesa = $codigo_mesa;
        $usr->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $mesa = Mesa::GetById($id);

        if($mesa === false){
            $payload = json_encode("No se encontro la mesa");
        }else{
            $payload = json_encode($mesa);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    
}

