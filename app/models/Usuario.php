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


public function getId() {
        return $this->id;
    }

    public function getRol() {
        return $this->rol;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getClave() {
        return $this->clave;
    }

    public function setClave() {
        $this->clave = self::crearClave();
    }

    public function getBaja() {
        return $this->baja;
    }

    public function getFecha_alta() {
        return $this->fecha_alta;
    }

    public function getFecha_baja() {
        return $this->fecha_baja;
    }

    public function setRol($rol) {
        if(self::ValidarRol($rol)){
            $this->rol = $rol;
        }else{
            http_response_code(400);
            echo 'Rol Invalido.';
            exit();
        }
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function setBaja($baja) {
        $this->baja = $baja;
    }

    public function setFecha_alta($fechaAlta) {
        $this->fecha_alta = $fechaAlta;
    }

    public function setFecha_baja($fechaBaja) {
        $this->fecha_baja = $fechaBaja;
    }
   

    public static function ValidarRol($rol)
    {
        if($rol != 'socio' && $rol != 'bartender' && $rol != 'cervecero' && $rol != 'cocinero' && $rol != 'mozo' && $rol != 'candybar'){
            return false;
        }
        return true;
    }

    public function crearClave($longitud = 5)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $clave = '';

        $existeClave = true;
        while ($existeClave) {
            for ($i = 0; $i < $longitud; $i++) {
                $clave .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT clave FROM usuarios WHERE clave = :clave");
            $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
            $consulta->execute();
            $existeClave = $consulta->fetchObject('Usuario');

            if ($existeClave === false) {
                return $clave;
            }
        }
        throw new Exception('No se pudo generar un código de pedido.');
    }

    public static function crearUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Usuarios (usuario, clave, rol, baja) VALUES (:usuario, :clave, :rol, :baja)");
        $claveHash = password_hash($usuario->getClave(), PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $usuario->getUsuario(), PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':rol', $usuario->getRol(), PDO::PARAM_STR);
        $consulta->bindValue(':baja', $usuario->getBaja(), PDO::PARAM_BOOL);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, rol, baja, fecha_alta, fecha_baja FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, rol, baja, fecha_alta, fecha_baja FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }


    public static function obtenerPorId($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, rol, usuario, clave, baja, fecha_alta, fecha_baja FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificar($obj)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave, rol = :rol, baja = :baja,
                                                                            fecha_alta = :fecha_alta, fecha_baja = :fecha_baja WHERE id = :id");
        $consulta->bindValue(':usuario', $obj->getUsuario(), PDO::PARAM_STR);
        $consulta->bindValue(':clave', $obj->getClave(), PDO::PARAM_STR);
        $consulta->bindValue(':id', $obj->getId(), PDO::PARAM_INT);
        $consulta->bindValue(':rol', $obj->getRol(), PDO::PARAM_INT);
        $consulta->bindValue(':baja', $obj->getBaja(), PDO::PARAM_BOOL);
        $consulta->bindValue(':fecha_alta', $obj->getFecha_alta());
        $consulta->bindValue(':fecha_baja', $obj->getFecha_baja());

        $consulta->execute();
    }

    public static function borrar($obj)
    {   
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET baja = 1, fecha_baja = :fecha_baja WHERE id = :id");
        $consulta->bindValue(':id', $obj, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_baja', date('Y-m-d H:i:s'));

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
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

    public static function obtenerPorClave($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT id, usuario, clave, rol FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Usuario');
    }

    
    public static function obtenerCantidadOperacionesRol($rol)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT usuarios.rol, COUNT(ingresos.id_usuario) AS cantidad_operaciones 
                                                    FROM ingresos
                                                    INNER JOIN usuarios ON ingresos.id_usuario = usuarios.id 
                                                    WHERE usuarios.rol = :rol 
                                                    GROUP BY usuarios.rol DESC ");
        $consulta->bindValue(':rol', $rol, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC)['cantidad_operaciones'];
    }

    public static function obtenerCantidadOperacionesPorRolUsuario($rol, $nombreUsuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT usuarios.rol, usuarios.usuario, COUNT(ingresos.id_usuario) AS cantidad_operaciones 
                                                        FROM ingresos
                                                        INNER JOIN usuarios ON ingresos.id_usuario = usuarios.id 
                                                        WHERE usuarios.rol = :rol 
                                                        AND usuarios.usuario = :nombreUsuario
                                                        GROUP BY usuarios.rol DESC ");  
        $consulta->bindValue(':rol', $rol, PDO::PARAM_STR);
        $consulta->bindValue(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerCantidadOperacionesPorSeparado()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT usuarios.rol, usuarios.usuario, COUNT(ingresos.id_usuario) AS cantidad_operaciones 
                                                        FROM ingresos
                                                        INNER JOIN usuarios ON ingresos.id_usuario = usuarios.id 
                                                        GROUP BY usuarios.id
                                                        ORDER BY cantidad_operaciones DESC ");  
       // $consulta->bindValue(':rol', $rol, PDO::PARAM_STR);
        //$consulta->bindValue(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerOperacionesPorRol($rol)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT fecha, rol
                                                    FROM ingresos
                                                    WHERE rol = :rol 
                                                    AND tipo_operacion = 'Login'");
        $consulta->bindValue(':rol', $rol, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

}

