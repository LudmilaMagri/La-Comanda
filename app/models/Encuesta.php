<?php

class Encuesta{

    public $id;
    public $codigo_pedido;
    public $puntuacion_mesa;
    public $puntuacion_restaurante;
    public $puntuacion_mozo;
    public $puntuacion_cocinero;
    public $comentario;
    public $fecha;


    public function __construct() {
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
    public function getCodigoPedido() {
        return $this->codigo_pedido;
    }
    public function setCodigoPedido($codigo_pedido) {
        $this->codigo_pedido = $codigo_pedido;
    }
    public function getPuntuacionMesa() {
        return $this->puntuacion_mesa;
    }

    public function setPuntuacionMesa($puntuacion_mesa) {
        if(self::ValidarPuntuacion($puntuacion_mesa)){
            $this->puntuacion_mesa = $puntuacion_mesa;
        }else{
            http_response_code(400);
            echo 'Puntuacion del mozo no valida, tiene que ser de 0 a 10';
            exit();
        }
    }

    public function getPuntuacionRestaurante() {
        return $this->puntuacion_restaurante;
    }

    public function setPuntuacionRestaurante($puntuacion_restaurante) {
        if(self::ValidarPuntuacion($puntuacion_restaurante)){
            $this->puntuacion_restaurante = $puntuacion_restaurante;
        }else{
            http_response_code(400);
            echo 'Puntuacion del mozo no valida, tiene que ser de 0 a 10';
            exit();
        }
    }

    public function getPuntuacionMozo() {
        return $this->puntuacion_mozo;
    }
    public function setPuntuacionMozo($puntuacion_mozo) {
        if(self::ValidarPuntuacion($puntuacion_mozo)){
            $this->puntuacion_mozo = $puntuacion_mozo;
        }else{
            http_response_code(400);
            echo 'Puntuacion del mozo no valida, tiene que ser de 0 a 10';
            exit();
        }
    }

    public function getPuntuacionCocinero() {
        return $this->puntuacion_cocinero;
    }
    public function setPuntuacionCocinero($puntuacion_cocinero) {
        if(self::ValidarPuntuacion($puntuacion_cocinero)){
        $this->puntuacion_cocinero = $puntuacion_cocinero;
        }else{
            http_response_code(400);
            echo 'Puntuacion de la cocinero no valida, tiene que ser de 0 a 10';
            exit();
        }
    }
    public function getComentario() {
        return $this->comentario;
    }
    public function setComentario($comentario) {
        if(strlen($comentario) < 66){
            $this->comentario = $comentario;
            }else{
                http_response_code(400);
                echo 'Comentario demaciado largo. Solo puede contener 66 caracteres';
                exit();
            }
    }
    public function getFecha() {
        return $this->fecha;
    }
    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }



    public static function ValidarPuntuacion($puntuacion){
        if($puntuacion < 0 && $puntuacion > 10){
            return false;
        }
        return true;
    }


    public static function crear($obj){
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (codigo_pedido, puntuacion_mesa, puntuacion_restaurante, puntuacion_mozo, puntuacion_cocinero, comentario, fecha) 
                                VALUES (:codigo_pedido, :puntuacion_mesa, :puntuacion_restaurante, :puntuacion_mozo, :puntuacion_cocinero, :comentario, :fecha)");
        
        $consulta->bindValue(":codigo_pedido", $obj -> getCodigoPedido());
        $consulta->bindValue(":puntuacion_mesa", $obj -> getPuntuacionMesa());
        $consulta->bindValue(":puntuacion_restaurante", $obj -> getPuntuacionRestaurante());
        $consulta->bindValue(":puntuacion_mozo", $obj -> getPuntuacionMozo());
        $consulta->bindValue(":puntuacion_cocinero", $obj -> getPuntuacionCocinero());
        $consulta->bindValue(":comentario", $obj -> getComentario());
        $consulta->bindValue(":fecha", $obj -> getFecha());
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }
    
    public static function obtenerMejorPuntuacionMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas ORDER BY puntuacion_mesa DESC LIMIT 10");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function obtenerPeorPuntuacionMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas ORDER BY puntuacion_mesa ASC LIMIT 10");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

}