<?php

class PedidoProducto{

    public $id;
    public $codigo_pedido;
    public $id_producto;
    public $id_usuario;
    public $producto_estado;
    public $nombre_producto;
    public $tiempo_estimado;

    public function __construct(){}

    public function getId(){
        return $this->id;
    }
    public function getIdProducto(){
        return $this->id_producto;
    }
    public function getIdUsuario(){
        return $this->id_usuario;
    }
    public function getCodigoPedido(){
        return $this->codigo_pedido;
    }
    public function getProductoEstado(){
        return $this->producto_estado;
    } 
    public function getNombreProducto(){
        return $this->nombre_producto;
    }
    public function getTiempoEstimado(){
        return $this->tiempo_estimado;
    }

    public function setId($id){
        $this->id = $id;
    }
    public function setIdProducto($idProducto){
        $this->id_producto = $idProducto;
    }
    public function setIdUsuario($idUsuario){
        $this->id_usuario = $idUsuario;
    }
    public function setCodigoPedido($codigoPedido){
        $this->codigo_pedido = $codigoPedido;
    }
    public function setProductoEstado($estado){
        if(self::ValidarEstado($estado)){
            $this->producto_estado = $estado;
        }else{
            http_response_code(400);
            echo 'Estado de pedido no valido. (pendiente / en preparacion / listo para servir / entregado)';
            exit();
        }
    }
    public function setNombreProducto($nombreProducto){
        $this->nombre_producto = $nombreProducto;
    }
    public function setTiempoEstimado($tiempoEstimado){
        $this->tiempo_estimado = $tiempoEstimado;
    }

    public static function ValidarEstado($estado){
        if($estado != Estado::PEND && $estado != Estado::PREPARACION && $estado != Estado::LISTO && $estado != Estado::ENTREGADO){
            return false;
        } 
        return true;
    } 

    public static function crearPedidoProducto($obj)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos_productos (codigo_pedido, id_producto, producto_estado, nombre_producto) VALUES (:codigo_pedido, :id_producto, :producto_estado, :nombre_producto)");
        $consulta->bindValue(':codigo_pedido', $obj->getCodigoPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $obj->getIdProducto(), PDO::PARAM_INT);
        $consulta->bindValue(':producto_estado', $obj->getProductoEstado(), PDO::PARAM_STR);
        $consulta->bindValue(':nombre_producto', $obj->getNombreProducto(), PDO::PARAM_STR);

        $consulta->execute();

        //return $objAccesoDatos->obtenerUltimoId();
    }

    
    public static function modificar($obj)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos_productos SET producto_estado = :producto_estado, codigo_pedido = :codigo_pedido, id_usuario = :id_usuario, 
                                                    id_producto = :id_producto WHERE id = :id");
        $consulta->bindValue(':producto_estado', $obj->getProductoEstado(), PDO::PARAM_STR);
        $consulta->bindValue(':codigo_pedido', $obj->getCodigoPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':id_usuario', $obj->getIdUsuario(), PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $obj->getIdProducto(), PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT  id_producto, id_usuario, codigo_pedido, producto_estado, FROM pedidos_productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }
    
    public static function obtenerIdProductoCodigoPedido($id_producto, $codigo_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_pedido, id_producto, producto_estado, id_empleado FROM pedidos_productos WHERE codigo_pedido = :codigo_pedido AND id_producto= :id_producto");
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);

        $consulta->execute();
        return $consulta->fetchObject('PedidoProducto');
    }

    
}
