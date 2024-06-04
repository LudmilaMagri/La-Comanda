<?php

include_once './models/Usuario.php';
include_once './interfaces/IApiUsable.php';

class UsuarioController implements IApiUsable{

    public function Cargar($request, $response, $args)
    {   
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];

        var_dump($parametros);

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->rol = $rol;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $usuario = $args['usuario'];
        $usuarioEncontrado = Usuario::ObtenerUsuario($usuario);

        if($usuarioEncontrado === false){
            $payload = json_encode("No se encontro el usuario");
        }else{
            $payload = json_encode($usuarioEncontrado);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
        
    }




}



