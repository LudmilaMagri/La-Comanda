<?php


include_once './Models/Mesa.php';
include_once './Models/Usuario.php';
include_once './interfaces/IApiUsable.php';

class MesaController implements IApiUsable
{


    /*
    * Se genera la carga de la mesa
    *
    */
    public function Cargar($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $estado = $parametros['estado'];

        // Creamos el estado
        $newMesa = new Mesa();
        $newMesa->setEstado($estado);
        $newMesa->setCodigoMesa();
        Mesa::crearMesa($newMesa);

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $usuario = $request->getAttribute('userData');
        $usuarioId = Usuario::obtenerUsuario($usuario->nombre);
        $logueo = new Logueo();
        $logueo->id_usuario = $usuarioId->id;
        $logueo->fecha = date('Y-m-d H:i:s');
        $logueo->tipo_operacion = 'CargarMesa';
        $logueo->rol = $usuario->rol;
        Logueo::crear($logueo);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*
    * Se modifica la mesa
    *
    */
    public static function Modificar($request, $response, $args)
    {
        $codigo_mesa = $args['codigo_mesa'];

        $mesa = Mesa::obtenerPorId($codigo_mesa);

        if ($mesa == false) {
            $payload = json_encode(array("mensaje" => "No existe Id con esa mensa"));
        } else {
            $parametros = $request->getParsedBody();
            if (isset($parametros['estado'])) {
                $estado = $parametros['estado'];
                $header = $request->getHeaderLine("Authorization");
                $token = trim(explode("Bearer", $header)[1]);
                $data = AutentificadorJWT::ObtenerData(($token));

                // Verifico los roles y el estado en cerrada
                if (($data->rol == "mozo" && strtolower($estado) !== Estado::CERRADA) ||
                    ($data->rol == "socio" && strtolower($estado) === Estado::CERRADA)
                ) {
                    $mesa->setEstado($estado);
                    Mesa::modificar($mesa);
                    $payload = json_encode(array("mensaje" => "Estado de la mesa modificado con éxito"));
                } else {
                    $payload = json_encode(array("mensaje" => "Acceso no autorizado para modificar el estado de la mesa"));
                }
            } else {
                $payload = json_encode(array("mensaje" => "Falta el campo 'estado' para modificar el estado de la mesa"));
            }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /*
    * Se borra la mesa
    *
    */
    public static function Borrar($request, $response, $args)
    {
        $codigo_mesa = $args['codigo_mesa'];
        $mesa = Mesa::obtenerPorId($codigo_mesa);

        if($mesa != false)
        {
            Mesa::borrarMesa($mesa);
            $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));
        }
        else{
            $payload = json_encode(array("mensaje" => "No se encontro mesa con ese codigo"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /*
    * Se obtienen todas las mesas
    *
    */
    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*
    * Se obtiene una mesa por id (codigo_mesa)
    *
    */
    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $mesa = Mesa::obtenerPorId($id);

        if ($mesa === false) {
            $payload = json_encode("No se encontro la mesa");
        } else {
            $payload = json_encode($mesa);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
