<?php

class Producto{

    public $id;
    public $nombre;
    public $sector;
    public $precio;
    public $tiempo_estimado;


    public function __construct(){}

    public function getId()
    {
        return $this->id;
    }
    public function getSector()
    {
        return $this->sector;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function getPrecio()
    {
        return $this->precio;
    }

    public function setSector($sector)
    {

        if (self::validarSector($sector)) {
            $this->sector = $sector;
        } else {
            http_response_code(400);
            echo 'Sector no valido. (Vinoteca / Cerveceria/ Cocina/ CandyBar)';
            exit();
        }
    }

    public function getTiempoEstimado()
    {
        return $this->tiempo_estimado;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    public function setTiempoEstimado($tiempoEstimado)
    {
        $this->tiempo_estimado = $tiempoEstimado;
    }

    public function validarSector($sector)
    {
        if($sector != 'cerveceria' && $sector != 'cocina' && $sector != 'candybar'){
            return false;
        }
        return true;
    }

    public function validarSectorRol($rol)
    {
        if ($this->getSector() == 'cerveceria' && $rol == 'bartender'){
            return true;
        }
        else if ($this->getSector() == 'cocina' && $rol == 'cocinero'){
            return true;
        }
        else if ($this->getSector() == 'candybar' && $rol == 'candybar'){
            return true;
        }else{
            if ($rol == 'mozo'){
                return true;
            }
            return false;
        }
    }

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Productos (nombre, sector, precio, tiempo_estimado) VALUES (:nombre, :sector, :precio, :tiempo_estimado)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado', $this->tiempo_estimado, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, sector, precio, tiempo_estimado FROM Productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerPorNombre($producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, sector, precio, tiempo_estimado FROM Productos WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $producto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function obtenerPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, sector, precio, tiempo_estimado FROM Productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }


    public static function modificarProducto($producto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET nombre = :nombre, sector = :sector, precio = :precio, 
                                                    tiempo_estimado = :tiempo_estimado WHERE id = :id");
        $consulta->bindValue(':nombre', $producto->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':sector', $producto->getSector(), PDO::PARAM_STR);
        $consulta->bindValue(':id', $producto->getId(), PDO::PARAM_INT);
        $consulta->bindValue(':precio', $producto->getPrecio());
        $consulta->bindValue(':tiempo_estimado', $producto->getTiempoEstimado());
        $consulta->execute();
    }



}








