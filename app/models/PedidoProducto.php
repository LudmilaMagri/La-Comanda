<?php

class PedidoProducto{

    public $id;
    public $id_producto;
    public $id_usuario;
    public $codigo_pedido;
    public $producto_estado;

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

    public static function ValidarEstado($estado){
        if($estado != 'pendiente' && $estado != 'en preparacion' && $estado != 'listo para servir' && $estado != 'entregado'){
            return false;
        } 
        return true;
    } 

    public function crearPedidoProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos_productos (codigo_pedido, id_producto, producto_estado) VALUES (:codigo_pedido, :id_producto, :producto_estado)");
        $consulta->bindValue(':codigo_pedido', $this->codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':producto_estado', $this->producto_estado, PDO::PARAM_STR);

        $consulta->execute();

        //return $objAccesoDatos->obtenerUltimoId();
    }
    public static function modificarPedidoProducto()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos_productos SET producto_estado = :producto_estado, codigo_pedido = :codigo_pedido, id_usuario = :id_usuario, 
                                                    id_producto = :id_producto WHERE id = :id");
        $consulta->bindValue(':producto_estado', $this->producto_estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_pedido', $this->codigo_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT  id_producto, id_usuario, codigo_pedido, producto_estado, FROM pedidos_productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProducto');
    }
    

    static public function altaPedidoProducto($idMesa,$nombreCliente,$nombreProducto,$cantidadProducto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        #==============================PEDIDOS======================================
        
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM pedidos WHERE id_mesa = :id_mesa");
       
        try
        {
            //echo $idMesa;
            $consulta -> bindValue(':id_mesa', $idMesa, PDO::PARAM_STR);
            $consulta -> execute();
            $consulta -> setFetchMode(PDO::FETCH_CLASS,'Pedido');
            $pedido = $consulta -> fetch();         
            // $pedido = $consulta -> fetchObject('Pedido');
            
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }


        #==============================PRODUCTOS======================================

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE nombre_producto = :nombre_producto");

       
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(':nombre_producto', $nombreProducto, PDO::PARAM_STR);
            $consulta -> execute();
            $consulta -> setFetchMode(PDO::FETCH_CLASS,'Producto');
            $producto = $consulta -> fetch();
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }       


        #==============================Pedido Producto======================================
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO servicio (id_mesa, nombre_cliente, nombre_producto, cantidad_producto, id_pedido, id_producto, precio_producto, sector_producto,estado_producto) 
                                                                VALUES (:id_mesa, :nombre_cliente, :nombre_producto, :cantidad_producto, :id_pedido, :id_producto, :precio_producto, :sector_producto,'pendiente')");

       
        try
        {
            $consulta = $pdo->prepare($query);
            $consulta -> bindValue(':id_mesa', $idMesa, PDO::PARAM_STR);
            $consulta -> bindValue(':nombre_cliente', $nombreCliente, PDO::PARAM_STR);
            $consulta -> bindValue(':nombre_producto', $nombreProducto, PDO::PARAM_STR);
            $consulta -> bindValue(':cantidad_producto', $cantidadProducto, PDO::PARAM_INT);
            $consulta -> bindValue(':id_pedido', $pedido->getId(), PDO::PARAM_STR);
            $consulta -> bindValue(':id_producto', $producto->getId(), PDO::PARAM_INT);
            $consulta -> bindValue(':precio_producto', $producto->getPrecio(), PDO::PARAM_STR);
            $consulta -> bindValue(':sector_producto', $producto->getSector(), PDO::PARAM_STR);

            $consulta -> execute();
        }
        catch(PDOException $e)
        {
            echo "Error al crear elemento: ".$e->getMessage();
        }
    }
}
