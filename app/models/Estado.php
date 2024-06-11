<?php

    class Estado
    {
        //Estados de los pedidos
        const PENDIENTE = 'pendiente';
        const PREPARACION = 'en preparacion';
        const LISTO = 'listo para servir';
        const ENTREGADO = 'entregado';
        
        //Estados de la mesa
        const ESPERANDO = 'Con cliente esperando pedido';
        const COMIENDO = 'Con cliente comiendo';
        const PAGANDO = 'Con cliente pagando';
        const CERRADA = 'cerrada';

    }
?>