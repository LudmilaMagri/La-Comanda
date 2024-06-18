<?php

include_once './Models/Producto.php';
include_once './Models/Estado.php';


class Pedido{

    public $id;
    public $estado;
    public $tiempo;  
    public $codigo_mesa;
    public $codigo_pedido;
    public $precio_total;
    public $foto;
    public $nombre_cliente;


    public function __construct(){}


    public function getId()
    {
        return $this->id;
    }
    public function getEstado()
    {
        return $this->estado;
    }
    public function getTiempo()
    {
        return $this->tiempo;
    }
    public function getCodigoMesa()
    {
        return $this->codigo_mesa;
    }
    public function getCodigoPedido()
    {
        return $this->codigo_pedido;
    }
    public function getPrecioTotal()
    {
        return $this->precio_total;
    }
    public function getFoto()
    {
        return $this->foto;
    }
    public function getNombreCliente()
    {
        return $this->nombre_cliente;
    }

    public function setEstado($estado){
        $this->estado = $estado;
    }
    public function setTiempo($tiempo){
        $this->tiempo = $tiempo;
    }
    public function setFoto($foto){
        $this->foto = $foto;
    }
    public function setNombreCliente($nombre){
        $this->nombre_cliente = $nombre;
    }
    public function setCodigoMesa($mesa){
        $this->codigo_mesa = $mesa;
    }
    public function setCodigoPedido(){
        $this->codigo_pedido = self::crearCodigoPedido();
    }
    public function setPrecioTotal($precio){
        $this->precio_total = $precio;
    }


    public function crearCodigoPedido($longitud = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $codigoPedido = '';

        $existeCodigo = true;
        while ($existeCodigo) {
            for ($i = 0; $i < $longitud; $i++) {
                $codigoPedido .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_pedido FROM pedidos WHERE codigo_pedido = :codigoPedido");
            $consulta->bindValue(':codigoPedido', $codigoPedido, PDO::PARAM_STR);
            $consulta->execute();
            $existeCodigo = $consulta->fetchObject('Pedido');

            if ($existeCodigo === false) {
                return $codigoPedido;
            }
        }
        throw new Exception('No se pudo generar un código de pedido.');
    }

    
    public static function crearPedido($pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (estado, tiempo, codigo_mesa, codigo_pedido, precio_total, nombre_cliente) 
                                                    VALUES (:estado, :tiempo, :codigo_mesa, :codigo_pedido, :precio_total, :nombre_cliente)");
        $consulta->bindValue(':estado', $pedido->getEstado(), PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $pedido->getTiempo());
        $consulta->bindValue(':codigo_mesa', $pedido->getCodigoMesa());
        $consulta->bindValue(':codigo_pedido', $pedido->getCodigoPedido());
        $consulta->bindValue(':precio_total', $pedido->getPrecioTotal());
        $consulta->bindValue(':nombre_cliente', $pedido->getNombreCliente(), PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }



  

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT estado, tiempo, codigo_mesa, codigo_pedido, precio_total, nombre_cliente FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }


    public static function obtenerPorId($codigo_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, codigo_pedido FROM mesas WHERE codigo_pedido = :codigo_pedido");
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarEstado($pedido)
    {
        if($pedido->getEstado() == Estado::PREPARACION)
        {   
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = :estado,  WHERE id = :id");
            $consulta->bindValue(':estado', strtolower($pedido->getEstado()), PDO::PARAM_STR);
            $consulta->execute();

        }
    }

    public static function obtenerPendientesPorRol($rol)
    {
        $sector = '';

        switch ($rol) {
            case 'bartender':
                $sector = 'vinoteca';
                break;
            case 'cervecero':
                $sector = 'cerveceria';
                break;
            case 'cocinero':
                $sector = 'cocina';
                break;
            case 'candybar':
                $sector = 'candybar';
                break;            
        }
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_pedido, id_producto, producto_estado, nombre_producto,
                                                            P.sector, P.precio 
                                                        FROM  pedidos_productos PD
                                                        LEFT JOIN productos P ON P.id = PD.id_producto
                                                        WHERE PD.producto_estado = 'pendiente' 
                                                        AND P.sector = :sector");


        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();
        $array = $consulta->fetchAll(PDO::FETCH_OBJ);
        return $array;
    }





}






