<?php

class Mesa{

    public $id;
    public $estado;
    public $codigo_mesa;

    public function __construct(){}

    public function getId() {
        return $this->id;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {

        if(self::ValidarEstado($estado)){
            $this->estado = $estado;
        }
    }

    public function getCodigoMesa() {
        return $this->codigo_mesa;
    }

    public function setCodigoMesa() {
        $this->codigo_mesa = self::crearCodigoMesa();
    }
    

    public static function ValidarEstado($estado)
    {
        if ($estado != Estado::ESPERANDO && $estado != Estado::COMIENDO && $estado != Estado::PAGANDO && $estado != Estado::CERRADA) {
            return false;
        }
        return true;
    }

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (estado, codigo_mesa) VALUES (:estado, :codigo_mesa)");
        $consulta->bindValue(':estado', $objAccesoDatos->estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_mesa', $objAccesoDatos->getCodigoMesa(), PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }


    public function crearCodigoMesa($long = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwyxz';
        $codigo = '';
        $existeCodigo = true;
        while($existeCodigo){
            for ($i=0; $i < $long; $i++) { 
                $codigo .= $caracteres[rand(0, strlen($caracteres) - 1 )];
            }
        }
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararQuery("SELECT codigo_mesa FROM mesas WHERE codigo_mesa = :codigo");
        $consulta ->blindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        $existeCodigo = $consulta->fetchObject('Mesa');
        
        if($existeCodigo === false){
            return $codigo;
        }
        
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, codigo_mesa FROM mesas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, codigo_mesa FROM mesas WHERE codigo_mesa = :codigo_mesa");
        $consulta->bindValue(':codigo_mesa', $mesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function obtenerPorId($codigo_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, codigo_mesa FROM mesas WHERE codigo_mesa = :codigo_mesa");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificar($obj)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', strtolower($obj->getEstado()), PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }







}




