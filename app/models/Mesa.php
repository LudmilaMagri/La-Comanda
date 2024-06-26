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
        if(Mesa::ValidarEstado($estado)){
            $this->estado = $estado;
        }
    }

    public function getCodigoMesa() {
        return $this->codigo_mesa;
    }

    public function setCodigoMesa() {
        $this->codigo_mesa = self::crearCodigoMesa();
    }
    

    
    public static function crearMesa($obj)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (estado, codigo_mesa) VALUES (:estado, :codigo_mesa)");
        $consulta->bindValue(':estado', $obj->getEstado(), PDO::PARAM_STR);
        $consulta->bindValue(':codigo_mesa', $obj->getCodigoMesa(), PDO::PARAM_STR);
        $consulta->execute();
        
        return $objAccesoDatos->obtenerUltimoId();
    }
    
    public static function modificar($obj)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', strtolower($obj->getEstado()), PDO::PARAM_STR);
        $consulta->bindValue(':id', $obj->getId(), PDO::PARAM_STR);
        
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }
    
        
    public static function borrarMesa($mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM mesas WHERE codigo_mesa = :codigo_mesa");
        $consulta->bindValue(':codigo_mesa', $mesa->getCodigoMesa(), PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
        
    }

    
    public static function ValidarEstado($estado)
    {
        if ($estado != Estado::ESPERANDO && $estado != Estado::COMIENDO && $estado != Estado::PAGANDO && $estado != Estado::CERRADA) {
            return false;
        }
        return true;
    }


    public function crearCodigoMesa($long = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwyxz';
        $existeCodigo = true;
    
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
        while($existeCodigo) {
            $codigo = '';
            for ($i = 0; $i < $long; $i++) { 
                $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }
    
            $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_mesa FROM mesas WHERE codigo_mesa = :codigo");
            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->execute();
            $existeCodigo = $consulta->fetchObject('Mesa') !== false;
        }
    
        return $codigo;
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, codigo_mesa FROM mesas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($codigo_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, codigo_mesa FROM mesas WHERE codigo_mesa = :codigo_mesa");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function obtenerPorId($codigo_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, codigo_mesa FROM mesas WHERE codigo_mesa = :codigo_mesa");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }


    public static function obtenerMesaPorPrecioMayor()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mesas.codigo_mesa, SUM(pedidos.precio_total) AS total
                                                        FROM mesas INNER JOIN pedidos ON mesas.codigo_mesa = pedidos.codigo_mesa
                                                        GROUP BY mesas.codigo_mesa
                                                        ORDER BY total DESC");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC)['codigo_mesa'];
    }

    public static function obtenerMesaPorPrecioMenor()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT mesas.codigo_mesa, SUM(pedidos.precio_total) AS total
                                                        FROM mesas INNER JOIN pedidos ON mesas.codigo_mesa = pedidos.codigo_mesa
                                                        GROUP BY mesas.codigo_mesa
                                                        ORDER BY total ASC");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC)['codigo_mesa'];
    }

    public static function obtenerMesaPorMayorImporte()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_mesa, codigo_pedido
                                                        FROM pedidos
                                                        ORDER BY precio_total DESC
                                                        LIMIT 1");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerMesaPorMenorImporte()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_mesa, codigo_pedido
                                                        FROM pedidos
                                                        ORDER BY precio_total ASC
                                                        LIMIT 1");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }






}




