<?php

class Usuario{

    public $id;
    public $usuario;
    public $clave;
    public $rol;
    public $baja;
    public $fecha_alta;
    public $fecha_baja;
    

    public function __construct(){}


    public static function ValidarRol($rol)
    {
        if($rol != 'socio' && $rol != 'bartender' && $rol != 'cervecero' && $rol != 'cocinero' && $rol != 'mozo' && $rol != 'candybar'){
            return false;
        }
        return true;
    }


    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Usuarios (usuario, clave, rol, baja, fecha_alta) VALUES (:usuario, :clave, :rol, :baja, :fecha_alta)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':baja', $this->estado, PDO::PARAM_BOOL);
        $consulta->bindValue(':fecha_alta', $this->fecha_alta);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, rol, baja, fecha_alta, fecha_baja FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, rol, baja, fecha_alta, fecha_baja FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        echo (random_bytes(5));

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave, rol = :rol, baja = :baja,
                                                                            fecha_alta = :fecha_alta, fecha_baja = :fecha_baja WHERE id = :id");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_INT);
        $consulta->bindValue(':baja', $this->estado, PDO::PARAM_BOOL);
        $consulta->bindValue(':fecha_alta', $this->fecha_alta);
        $consulta->bindValue(':fecha_baja', $this->fecha_baja);

        $consulta->execute();
    }

    
    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fecha_baja = :fecha_baja, baja = 1 WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }
}

