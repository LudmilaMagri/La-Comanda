<?php

class PedidoProducto
{

    public $id;
    public $codigo_pedido;
    public $id_producto;
    // public $id_usuario;
    public $producto_estado;
    public $nombre_producto;
    public $tiempo_estimado;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }
    public function getIdProducto()
    {
        return $this->id_producto;
    }
    /* public function getIdUsuario(){
        return $this->id_usuario;
    }*/
    public function getCodigoPedido()
    {
        return $this->codigo_pedido;
    }
    public function getProductoEstado()
    {
        return $this->producto_estado;
    }
    public function getNombreProducto()
    {
        return $this->nombre_producto;
    }
    public function getTiempoEstimado()
    {
        return $this->tiempo_estimado;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setIdProducto($idProducto)
    {
        $this->id_producto = $idProducto;
    }
    //public function setIdUsuario($idUsuario){
    //    $this->id_usuario = $idUsuario;
    // }
    public function setCodigoPedido($codigoPedido)
    {
        $this->codigo_pedido = $codigoPedido;
    }
    public function setProductoEstado($estado)
    {
        if (self::ValidarEstado($estado)) {
            $this->producto_estado = $estado;
        } else {
            http_response_code(400);
            echo 'Estado de pedido no valido. (pendiente / en preparacion / listo para servir / entregado)';
            exit();
        }
    }
    public function setNombreProducto($nombreProducto)
    {
        $this->nombre_producto = $nombreProducto;
    }
    public function setTiempoEstimado($tiempoEstimado)
    {
        $this->tiempo_estimado = $tiempoEstimado;
    }

    public static function ValidarEstado($estado)
    {
        if ($estado != Estado::PEND && $estado != Estado::PREPARACION && $estado != Estado::LISTO && $estado != Estado::ENTREGADO && $estado != 'cancelado') {
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
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos_productos SET producto_estado = :producto_estado, codigo_pedido = :codigo_pedido,
                                                    id_producto = :id_producto WHERE id = :id");
        $consulta->bindValue(':producto_estado', $obj->getProductoEstado(), PDO::PARAM_STR);
        $consulta->bindValue(':codigo_pedido', $obj->getCodigoPedido(), PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $obj->getIdProducto(), PDO::PARAM_INT);
        $consulta->bindValue(':id', $obj->getId(), PDO::PARAM_INT);
        $consulta->execute();
    }


    public static function modificarEstado($estado, $tiempo_estimado, $id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos_productos SET producto_estado = :producto_estado, tiempo_estimado = :tiempo_estimado  
                                                        WHERE id = :id ");
        $consulta->bindValue(':producto_estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado', $tiempo_estimado, PDO::PARAM_INT);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);

        $consulta->execute();


        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT  id_producto, codigo_pedido, producto_estado, nombre_producto FROM pedidos_productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function obtenerTodosPorCodigoPedido($codigo_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT  id_producto, codigo_pedido, producto_estado, nombre_producto, tiempo_estimado FROM pedidos_productos
                                                        WHERE codigo_pedido = :codigo_pedido");
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }
    public static function obtenerIdProductoCodigoPedido($id, $codigo_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_pedido, id_producto FROM pedidos_productos WHERE codigo_pedido = :codigo_pedido AND id= :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);


        $consulta->execute();
        return $consulta->fetchObject('PedidoProducto');
    }
    
    public static function obtenerTodosListos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos_productos
                                                        WHERE producto_estado = 'listo para servir'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }

    public static function obtenerTodosEnPreparacion()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos_productos
                                                        WHERE producto_estado = 'en preparacion'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }


    public static function verificarTodosEntregados($codigo_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) AS total_productos, SUM(CASE WHEN producto_estado = 'entregado' THEN 1 ELSE 0 END)
                                                        AS entregados FROM pedidos_productos
                                                        WHERE codigo_pedido = :codigo_pedido 
                                                        HAVING total_productos = entregados");
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->execute();

        if($consulta->fetchColumn() > 0){
            return true;
        }
        return false;
    }
}
