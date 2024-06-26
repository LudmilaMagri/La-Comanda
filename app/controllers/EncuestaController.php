<?php

include_once './models/Usuario.php';
include_once './models/Encuesta.php';

class EncuestaController{

    /*
    * Carga una encuesta con codigo_pedido y puntuando:
    * mesa, restaurante, mozo y cocinero.
    * Añade un comentario 
    *
    */

    public static function Cargar($request, $response, $args){
        
        $parametros = $request->getParsedBody();
        $codigo_pedido = $parametros['codigo_pedido'];
        $puntuacion_mesa = $parametros['puntuacion_mesa'];
        $puntuacion_restaurante = $parametros['puntuacion_restaurante'];
        $puntuacion_mozo = $parametros['puntuacion_mozo'];
        $puntuacion_cocinero = $parametros['puntuacion_cocinero'];
        $comentario = $parametros['comentario'];
        
        if(!empty($comentario) && !empty($codigo_pedido))
        {
            if(!Pedido::obtenerPorId($codigo_pedido))
            {
                $payload = json_encode(array("mensaje" => "El pedido no existe con ese codigo"));
            }else{
                $encuestaNew = new Encuesta();
                $encuestaNew->setCodigoPedido($codigo_pedido);
                $encuestaNew->setPuntuacionMesa($puntuacion_mesa);
                $encuestaNew->setPuntuacionRestaurante($puntuacion_restaurante);
                $encuestaNew->setPuntuacionMozo($puntuacion_mozo);
                $encuestaNew->setPuntuacionCocinero($puntuacion_cocinero);
                $encuestaNew->setComentario($comentario);
                $encuestaNew->setFecha(date("Y-m-d"));
                Encuesta::crear($encuestaNew);
                $payload = json_encode(array("Mensaje" => "Encuesta cargada con exito!"));
            }
        }else{
            $payload = json_encode(array("Error" => "Error al cargar"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader("Content-type", "application/json");
    }



}



?>