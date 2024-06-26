<?php

class TiempoPedido{


    public $id;
    public $id_pedido_producto;
    public $codigo_pedido;
    public $tiempo_ini;
    public $tiempo_fin;
    public $entrega_estimada;
    public $entrega_fin;
    public $entrega_tarde;


    public function __construct(){}

    public function getId() {
        return $this->id;
    }
    public function getIdPedidoProducto() {
        return $this->id_pedido_producto;
    }
    public function getCodigoPedido() {
        return $this->codigo_pedido;
    }
    public function getTiempoIni() {
        return $this->tiempo_ini;
    }
    public function getTiempoFin() {
        return $this->tiempo_fin;
    }
    public function getEntregaEstimada() {
        return $this->entrega_estimada;
    }
    public function getEntregaFin() {
        return $this->entrega_fin;
    }
    public function getEntregaTarde() {
        return $this->entrega_tarde;
    }

    public function setTiempoIni($tiempo_ini) {
        $this->tiempo_ini = $tiempo_ini;
    }

    public function setIdPedidoProducto($id_pedido_producto) {
        $this->id_pedido_producto = $id_pedido_producto;
    }
    public function setCodigoPedido($codigo_pedido) {
        $this->codigo_pedido = $codigo_pedido;
    }
    public function setTiempoFin($tiempo_fin) {
        $this->tiempo_fin = $tiempo_fin;
    }
    public function setEntregaEstimada($entrega_estimada) {
        $this->entrega_estimada = $entrega_estimada;
    }
    public function setEntregaFin($entrega_fin) {
        $this->entrega_fin = $entrega_fin;
    }
    public function setEntregaTarde($entrega_tarde) {
        $this->entrega_tarde = $entrega_tarde;
    }



    //pedido en preparacion
    public static function crearTiempoEspera($id_pedido_producto, $entrega_estimada, $codigo_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO tiempo_pedido (id_pedido_producto, entrega_estimada, codigo_pedido) 
                                                    VALUES (:id_pedido_producto, :entrega_estimada, :codigo_pedido)");
        $consulta->bindValue(':id_pedido_producto', $id_pedido_producto, PDO::PARAM_INT);
        $consulta->bindValue(':entrega_estimada', $entrega_estimada, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();

    }

    //pedido listo
    public static function modificarTiempoEsperaListo($id_pedido_producto, $tiempo_fin)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tiempo_pedido SET tiempo_fin = :tiempo_fin
                                                        WHERE id_pedido_producto = :id_pedido_producto");
        $consulta->bindValue(':id_pedido_producto', $id_pedido_producto, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo_fin', $tiempo_fin);
        $consulta->execute();
    }

    //pedido entregado
    public static function modificarTiempoEsperaEntregado($id_pedido_producto, $entrega_final)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tiempo_pedido SET entrega_final = :entrega_final
                                                        WHERE id_pedido_producto = :id_pedido_producto");
        $consulta->bindValue(':id_pedido_producto', $id_pedido_producto, PDO::PARAM_INT);
        $consulta->bindValue(':entrega_final', $entrega_final);
        $consulta->execute();
    }

    //pedido tarde
    public static function modificarTiempoEsperaTarde()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE tiempo_pedido SET entrega_tarde = 1
                                                        WHERE entrega_final > entrega_estimada");
        $consulta->execute();
    }


    public static function traerTiempoEsperaTarde()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_pedido FROM tiempo_pedido 
                                                        WHERE entrega_tarde = 1 GROUP BY codigo_pedido DESC");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "TiempoPedido");
    }

    public static function traerTiempoEntregaEstimada($codigo_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(entrega_estimada) AS entrega_estimada
                                                        FROM tiempo_pedido 
                                                        WHERE codigo_pedido = :codigo_pedido ");
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }


}


?>