<?php

class Factura{

    public $id;
    public $codigo_pedido;
    public $codigo_mesa;
    public $importe;
    public $fecha;


    public function __construct()
    {
        
    }

    public function getId()
    {
        return $this->id;
    }
    public function getCodigoMesa()
    {
        return $this->codigo_mesa;
    }
    public function getCodigoPedido()
    {
        return $this->codigo_pedido;
    }
    public function getImporte()
    {
        return $this->importe;
    }
    public function getFecha()
    {
        return $this->fecha;
    }
    

    public function setCodigoMesa($mesa)
    {
        $this->codigo_mesa = $mesa;
    }
    public function setCodigoPedido($codigo_pedido)
    {
        $this->codigo_pedido = $codigo_pedido;
    }
    public function setImporte($importe)
    {
        $this->importe = $importe;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public static function crearFactura($factura)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO factura (codigo_pedido, codigo_mesa, importe, fecha) 
                                                    VALUES (:codigo_pedido, :codigo_mesa, :importe, :fecha)");
        $consulta->bindValue(':codigo_pedido', $factura->getCodigoPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':codigo_mesa', $factura->getCodigoMesa(), PDO::PARAM_STR);
        $consulta->bindValue(':importe', $factura->getImporte());
        $consulta->bindValue(':fecha', $factura->getFecha());
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerPedidosPorFecha($fecha)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.codigo_pedido, pedidos.codigo_mesa, factura.importe, factura.fecha
                                                    FROM pedidos
                                                    INNER JOIN factura ON pedidos.codigo_pedido = factura.codigo_pedido
                                                    WHERE factura.fecha BETWEEN DATE_SUB(:fechaInicio, INTERVAL 30 DAY) AND :fechaFin");

        $consulta->bindValue(':fechaInicio', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':fechaFin', $fecha, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPedidosEntreFechas($fecha1, $fecha2)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT SUM(importe) AS importeTotal, fecha, codigo_mesa
                                                    FROM factura
                                                    WHERE fecha BETWEEN :fechaInicio AND :fechaFin
                                                    GROUP BY codigo_mesa");

        $consulta->bindValue(':fechaInicio', $fecha1, PDO::PARAM_STR);
        $consulta->bindValue(':fechaFin', $fecha2, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

}


?>