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
        $usr->crearUsuario($usr);

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function Modificar($request, $response, $args)
    {
        $id = $args['id'];
        $usuario = Usuario::obtenerPorId($id);
        var_dump($usuario);

        if($usuario != false)
        {
            $parametros = $request->getParsedBody();
            if(isset($parametros['rol']) && isset($parametros['usuario']))
            {
                $usuario->rol = $parametros['rol'];
                $usuario->usuario = $parametros['usuario'];

                Usuario::modificar($usuario);
                $payload = json_encode(array("mensaje" =>"Usuario modificado exitosamente"));
            }else{
                $payload = json_encode(array("mensaje" =>"No pudo ser modificado. Verifique todos los campos"));
            }
        }else{
            $payload = json_encode(array("mensaje" =>"No se encontro usuario para ese ID"));
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function Borrar($request, $response, $args)
    {
        $id = $args['id'];

        if(Usuario::obtenerPorId($id))
        {
            Usuario::borrar($id);
            $payload = json_encode(array("mensaje" =>"Se ha borrado correctamente el usuario: " . $id));
        }else{
            $payload = json_encode(array("mensaje" =>"El id " . $id . " no se encuentra en la base de datos"));
        }
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

    public static function Importar($request, $response, $args)
    {
        $archivo = $_FILES['archivo']['tmp_name'];
        if($archivo)
        {
            $lineas = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $primeraLinea = true;
            foreach ($lineas as $linea) {
                if(!$primeraLinea)
                {
                    $columnas = explode(',', $linea);
                    $usuario = new Usuario();
                    $usuario->setUsuario($columnas[1]);
                    $usuario->setClave($columnas[2]);
                    $usuario->setRol($columnas[3]);
                    $usuario->setBaja($columnas[4]);
                    $usuario->setFecha_alta($columnas[5]);
                    $usuario->setFecha_baja($columnas[6]);

                    var_dump($usuario);
                    Usuario::crearUsuario($usuario);
                }
                $primeraLinea = false;
            }
            $payload = json_encode(array("mensaje" => "Se han importado los usuarios con exito"));
            $response->getBody()->write($payload);
        }else{
            $payload = json_encode(array("mensaje" => "Error: no se han podido importar los usuarios"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function Exportar($request, $response, $args)
    {
        $pathFile = "./Archivos";
        $nombreArchivo = 'UsuariosExportados.csv';
        if(!is_dir($pathFile))
        {
            if(!mkdir($pathFile, 0755, true)) // Intentar crear el directorio con permisos de escritura
            {
                die('Error al crear el directorio');
            }
        }
        $rutaCompleta = $pathFile . '/' . $nombreArchivo;

        $archivo = fopen($rutaCompleta, 'w');
        fputcsv($archivo, ['id', 'rol', 'nombre', 'clave', 'baja', 'fecha_alta', 'fecha_baja']);

        $usuarios = Usuario::obtenerTodos();

        foreach ($usuarios as $usuario) {
            fputcsv($archivo, (array)$usuario);
        }

        fclose($archivo);
        
        $payload = json_encode(array("mensaje"=> "Los usuarios se exportaron correctamente"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

    }



    public static function LogIn($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $nombre = $parametros['nombre'];
      $clave = $parametros['clave'];
  
      $usuario = Usuario::obtenerPorClave($nombre, $clave);

  
      $data = array('nombre' => $usuario->usuario, 'rol' => $usuario->rol, 'clave' => $usuario->clave);
      $creacionToken = AutentificadorJWT::CrearToken($data);

      $response = $response->withHeader('Content-Type', 'application/json');


      $payload = json_encode(array("mensaje" => "Usuario logueado correctamente, Token: ". $creacionToken));
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }



}



