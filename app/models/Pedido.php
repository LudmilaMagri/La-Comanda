<?php

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
    public function setCodigoPedido($pedido){
        $this->codigo_pedido = $pedido;
    }
    public function setPrecioTotal($precio){
        $this->precio_total = $precio;
    }



    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (estado, tiempo, codigo_mesa, codigo_pedido, precio_total, nombre_cliente) 
                                                    VALUES (:estado, :tiempo, :codigo_mesa, :codigo_pedido, :precio_total, :nombre_cliente)");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $this->tiempo, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_mesa', $this->codigo_mesa);
        $consulta->bindValue(':codigo_pedido', $this->codigo_pedido);
        $consulta->bindValue(':precio_total', $this->precio_total);
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
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








}






