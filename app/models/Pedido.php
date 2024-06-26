<?php

include_once './Models/Producto.php';
include_once './Models/Estado.php';


class Pedido
{

    public $id;
    public $estado;
    public $tiempo;
    public $codigo_mesa;
    public $codigo_pedido;
    public $precio_total;
    public $foto;
    public $nombre_cliente;


    public function __construct()
    {
    }


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

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
    public function setTiempo($tiempo)
    {
        $this->tiempo = $tiempo;
    }
    public function setFoto($foto)
    {
        $this->foto = $foto;
    }
    public function setNombreCliente($nombre)
    {
        $this->nombre_cliente = $nombre;
    }
    public function setCodigoMesa($mesa)
    {
        $this->codigo_mesa = $mesa;
    }
    public function setCodigoPedido()
    {
        $this->codigo_pedido = self::crearCodigoPedido();
    }
    public function setPrecioTotal($precio)
    {
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, tiempo, codigo_mesa, codigo_pedido, precio_total, nombre_cliente FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }


    public static function obtenerPorId($codigo_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, estado, codigo_mesa, codigo_pedido, tiempo, precio_total FROM pedidos WHERE codigo_pedido = :codigo_pedido");
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarEstadoTiempo($pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = :estado, tiempo = :tiempo WHERE id = :id");
        $consulta->bindValue(':estado', strtolower($pedido->getEstado()), PDO::PARAM_STR);
        $consulta->bindValue(':id', $pedido->getId(), PDO::PARAM_INT);
        $consulta->bindValue(':tiempo', $pedido->getTiempo(), PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPendientesPorRol($rol)
    {
        $sector = '';

        switch ($rol) {
            case 'bartender':
                $sector = 'bar';
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT PD.id, codigo_pedido, id_producto, producto_estado, nombre_producto,
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


    static public function tiempoFinal($codigoPedido)
    {
        $pedidosProductos = PedidoProducto::obtenerTodosPorCodigoPedido($codigoPedido);
        $tiempo = 0;

        foreach ($pedidosProductos as $pedido) 
        {
            if($pedido->tiempo_estimado > $tiempo)
            {
                $tiempo = $pedido->tiempo_estimado;
            }
        }
        return $tiempo;
    }

    public static function agregarFoto($foto, $codigo_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        try {
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET foto = :foto WHERE codigo_mesa = :codigo_mesa");
            $consulta->bindValue(':foto', $foto);
            $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_INT);
            $consulta->execute();
        } catch (Exception $e) {
            echo 'Error al subir imagen' . $e->getMessage();
        }
    }

    public static function obtenerPedidoMesaMasUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_mesa, COUNT(codigo_mesa) AS cantidad FROM pedidos GROUP BY codigo_mesa ORDER BY cantidad DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC)['codigo_mesa'];
    }
    public static function obtenerPedidoMesaMenosUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_mesa, COUNT(codigo_mesa) AS cantidad FROM pedidos GROUP BY codigo_mesa ORDER BY cantidad ASC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC)['codigo_mesa'];
    }

    
    public static function obtenerProductoMasVendido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, productos.nombre, COUNT(id_producto) AS cantidad 
                                                    FROM pedidos_productos
                                                    INNER JOIN productos ON pedidos_productos.id_producto = productos.id 
                                                    GROUP BY id_producto 
                                                    ORDER BY cantidad DESC LIMIT 1");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerProductoMenosVendido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, productos.nombre, COUNT(id_producto) AS cantidad 
                                                    FROM pedidos_productos
                                                    INNER JOIN productos ON pedidos_productos.id_producto = productos.id 
                                                    GROUP BY id_producto 
                                                    ORDER BY cantidad ASC LIMIT 1");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function modificarPedidoCancelado($codigo_pedido)
    {   
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = 'cancelado' WHERE codigo_pedido = :codigo_pedido");
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);
        $consulta->execute();
        return true;
    }

    public static function obtenerTodosCancelados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos
                                                        WHERE estado = 'cancelado'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerTodosEnPreparacion()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos
                                                        WHERE estado = 'en preparacion'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }
}
