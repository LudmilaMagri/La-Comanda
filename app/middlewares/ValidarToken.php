<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response; 
require_once './middlewares/AutentificadorJWT.php';

class ValidarToken{

    public static function ValidarSocio(Request $request, RequestHandler $handler) : Response{

        $header = $request->getHeaderLine (("Authorization")); //aca esta el token
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response();

        try{
          //  json_encode(array("Token" => AutentificadorJWT::VerificarToken($token)));
          $payload = AutentificadorJWT::ObtenerData($token);
          if($payload->rol == 'socio'){
              
              $request = $request->withAttribute('userData', $payload);
              $response = $handler->handle($request);
          }
          else{
            $response->getBody()->write(json_encode(array('Error' => "Accion solo para los socios")));
          }
        }
        catch(Exception $ex){
            $response->getBody()->write(json_encode(array("Error"=> $ex->getMessage())));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarMozo(Request $request, RequestHandler $handler) : Response{

        $header = $request->getHeaderLine (("Authorization")); //aca esta el token
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response();

        try{
          $payload = AutentificadorJWT::ObtenerData($token);
          if($payload->rol == 'mozo'){
              
            $request = $request->withAttribute('userData', $payload);
            $response = $handler->handle($request);
          }
          else{
            $response->getBody()->write(json_encode(array('Error' => "Accion solo para los mozos")));
          }
        }
        catch(Exception $ex){
            $response->getBody()->write(json_encode(array("Error"=> $ex->getMessage())));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public static function ValidarCocinero(Request $request, RequestHandler $handler) : Response{

        $header = $request->getHeaderLine (("Authorization")); //aca esta el token
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response();

        try{
          $payload = AutentificadorJWT::ObtenerData($token);
          if($payload->rol == 'cocinero'){
            
              $request = $request->withAttribute('userData', $payload);
              $response = $handler->handle($request);
          }
          else{
            $response->getBody()->write(json_encode(array('Error' => "Accion solo para los cocineros")));
          }
        }
        catch(Exception $ex){
            $response->getBody()->write(json_encode(array("Error"=> $ex->getMessage())));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    
   
}

?>