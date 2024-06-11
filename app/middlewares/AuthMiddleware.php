<?php
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Psr7\Response;
use Slim\Psr7\Response as ResponseClass;

use Firebase\JWT\JWT;



class AuthMiddleware
{


    private static $claveSecreta = 'clave1234';
    private static $tipoEncriptacion = ['HS256'];

    private $_perfiles=array();

    public function __construct($perfiles)
    {
        $this->_perfiles = $perfiles;
    }

    public function __invoke(IRequest $request, IRequestHandler $requestHandler)
    {
        $response = new ResponseClass();
        echo "entro al authMW".PHP_EOL;

        $params = $request->getQueryParams();


        if(isset($params["credenciales"]))
        {
            $credenciales = $params ["credenciales"];

            if(in_array($credenciales,$this->_perfiles))
            {
                $response = $requestHandler ->handle($request);
            }
            else
            {
                $response->getBody()->write(json_encode(array("error"=>"No es ". $this->_perfiles[0])));
                
            }
        }
        else
        {
            $response->getBody()->write(json_encode(array("error"=>"No hay credenciales")));

        }
        echo "salgo del authMW".PHP_EOL;
        return $response->withHeader('Content-Type','application/json');    
    }
    


/*

    public static function CrearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (60000),
            'aud' => self::Aud(),
            'data' => $datos,
            'app' => "Test JWT"
        );
        return JWT::encode($payload, self::$claveSecreta);

    }
    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
        } catch (Exception $e) {
            throw $e;
            return false;
        }
        if ($decodificado->aud !== self::Aud()) {
            throw new Exception("No es el usuario valido");
        }
    }

    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }


    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud); //devuelve cadena de texto unica
    }
*/
}