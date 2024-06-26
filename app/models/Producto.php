<?php

class Producto{

    public $id;
    public $nombre;
    public $sector;
    public $precio;


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

    

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
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
        if ($this->getSector() == 'cerveceria' && $rol == 'cervecero'){
            return true;
        }
        else if ($this->getSector() == 'bar' && $rol == 'bartender'){
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
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Productos (nombre, sector, precio) VALUES (:nombre, :sector, :precio)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        
        $consulta->execute();
        
        return $objAccesoDatos->obtenerUltimoId();
    }
    
    public static function modificarProducto($producto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET nombre = :nombre, sector = :sector, precio = :precio
                                                     WHERE id = :id");
        $consulta->bindValue(':nombre', $producto->getNombre(), PDO::PARAM_STR);
        $consulta->bindValue(':sector', $producto->getSector(), PDO::PARAM_STR);
        $consulta->bindValue(':id', $producto->getId(), PDO::PARAM_INT);
        $consulta->bindValue(':precio', $producto->getPrecio());
        $consulta->execute();
    }

    public static function borrarProducto($producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $producto->getId(), PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
        
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, sector, precio FROM Productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerPorNombre($producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, sector, precio FROM productos WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $producto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function obtenerPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, sector, precio FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function obtenerProductoEntregadoTarde()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tiempo_pedido.id_pedido_producto, pedidos_productos.nombre_producto
                                                    FROM tiempo_pedido
                                                    INNER JOIN pedidos_productos ON tiempo_pedido.id_pedido_producto = pedidos_productos.id
                                                    WHERE tiempo_pedido.entrega_tarde = 1");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }



}








