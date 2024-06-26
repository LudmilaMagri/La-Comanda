-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-06-2024 a las 23:04:26
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` int(11) NOT NULL,
  `codigo_pedido` varchar(5) NOT NULL,
  `puntuacion_mesa` int(10) NOT NULL,
  `puntuacion_restaurante` int(10) NOT NULL,
  `puntuacion_mozo` int(10) NOT NULL,
  `puntuacion_cocinero` int(10) NOT NULL,
  `comentario` varchar(66) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin7 COLLATE=latin7_general_ci;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id`, `codigo_pedido`, `puntuacion_mesa`, `puntuacion_restaurante`, `puntuacion_mozo`, `puntuacion_cocinero`, `comentario`, `fecha`) VALUES
(1, 'h3nwr', 6, 8, 4, 10, 'Bueno', '2024-06-23 03:00:00'),
(2, 'P5tWW', 1, 3, 9, 5, 'Mala mesa toda sucia', '2024-06-23 03:00:00'),
(3, 'yAaQ6', 2, 3, 3, 5, 'Mala mesa ', '2024-06-23 03:00:00'),
(4, 'ATnFG', 1, 1, 2, 3, 'Espantoso', '2024-06-25 03:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `id` int(11) NOT NULL,
  `codigo_pedido` varchar(5) NOT NULL,
  `codigo_mesa` varchar(5) NOT NULL,
  `importe` decimal(10,0) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin7 COLLATE=latin7_general_ci;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`id`, `codigo_pedido`, `codigo_mesa`, `importe`, `fecha`) VALUES
(8, 'hhC2g', 'LHdF7', 5000, '2024-06-10'),
(9, '1BUqs', 'LHdF7', 1200, '2024-05-28'),
(10, 'frFQp', 'vJA0R', 1200, '2024-06-01'),
(12, 'SV2Y4', 'LHdF7', 1200, '2024-02-24'),
(13, 'jggkZ', 'LHdF7', 1200, '2024-03-24'),
(14, 'PpAzk', 'LHdF7', 1200, '2024-06-24'),
(15, 'ATnFG', 'vJA0R', 5000, '2024-06-25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `tipo_operacion` varchar(250) NOT NULL,
  `rol` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin7 COLLATE=latin7_general_ci;

--
-- Volcado de datos para la tabla `ingresos`
--

INSERT INTO `ingresos` (`id`, `id_usuario`, `fecha`, `tipo_operacion`, `rol`) VALUES
(258, 27, '2024-06-24 20:54:55.000000', 'CobrarPedido', 'mozo'),
(259, 27, '2024-06-24 20:55:03.000000', 'CobrarPedido', 'mozo'),
(260, 27, '2024-06-24 20:55:07.000000', 'CobrarPedido', 'mozo'),
(261, 26, '2024-06-24 21:44:05.000000', 'Login', 'socio'),
(262, 26, '2024-06-24 22:00:57.000000', 'Login', 'socio'),
(263, 29, '2024-06-24 22:08:57.000000', 'Login', 'bartender'),
(264, 26, '2024-06-24 22:09:11.000000', 'Login', 'socio'),
(265, 29, '2024-06-24 22:45:32.000000', 'Login', 'bartender'),
(266, 30, '2024-06-24 22:46:00.000000', 'Login', 'cocinero'),
(267, 30, '2024-06-24 22:51:11.000000', 'ModificarPedido', 'cocinero'),
(268, 26, '2024-06-24 22:58:06.000000', 'Login', 'socio'),
(269, 26, '2024-06-25 13:21:46.000000', 'Login', 'socio'),
(270, 63, '2024-06-25 14:28:08.000000', 'Login', 'socio'),
(271, 64, '2024-06-25 14:30:24.000000', 'Login', 'mozo'),
(272, 64, '2024-06-25 14:31:18.000000', 'CargarPedido', 'mozo'),
(273, 67, '2024-06-25 14:33:29.000000', 'Login', 'cocinero'),
(274, 67, '2024-06-25 14:34:59.000000', 'ModificarPedido', 'cocinero'),
(275, 67, '2024-06-25 14:35:25.000000', 'ModificarPedido', 'cocinero'),
(276, 67, '2024-06-25 14:35:30.000000', 'ModificarPedido', 'cocinero'),
(277, 65, '2024-06-25 14:35:39.000000', 'Login', 'bartender'),
(278, 65, '2024-06-25 14:36:34.000000', 'ModificarPedido', 'bartender'),
(279, 66, '2024-06-25 14:36:45.000000', 'Login', 'cervecero'),
(280, 66, '2024-06-25 14:37:04.000000', 'ModificarPedido', 'cervecero'),
(281, 63, '2024-06-25 14:40:55.000000', 'Login', 'socio'),
(282, 67, '2024-06-25 15:50:54.000000', 'Login', 'cocinero'),
(283, 67, '2024-06-25 15:51:27.000000', 'ModificarPedido', 'cocinero'),
(284, 67, '2024-06-25 15:51:40.000000', 'ModificarPedido', 'cocinero'),
(285, 67, '2024-06-25 15:51:46.000000', 'ModificarPedido', 'cocinero'),
(286, 65, '2024-06-25 15:52:19.000000', 'Login', 'bartender'),
(287, 65, '2024-06-25 15:52:33.000000', 'ModificarPedido', 'bartender'),
(288, 66, '2024-06-25 15:53:38.000000', 'Login', 'cervecero'),
(289, 66, '2024-06-25 15:54:08.000000', 'Login', 'cervecero'),
(290, 66, '2024-06-25 15:54:50.000000', 'ModificarPedido', 'cervecero'),
(291, 64, '2024-06-25 16:02:17.000000', 'Login', 'mozo'),
(292, 64, '2024-06-25 16:02:46.000000', 'ModificarPedido', 'mozo'),
(293, 64, '2024-06-25 16:02:52.000000', 'ModificarPedido', 'mozo'),
(294, 64, '2024-06-25 16:03:14.000000', 'ModificarPedido', 'mozo'),
(295, 64, '2024-06-25 16:03:21.000000', 'ModificarPedido', 'mozo'),
(296, 64, '2024-06-25 16:03:25.000000', 'ModificarPedido', 'mozo'),
(297, 64, '2024-06-25 16:03:29.000000', 'ModificarPedido', 'mozo'),
(298, 63, '2024-06-25 16:04:40.000000', 'Login', 'socio'),
(299, 64, '2024-06-25 16:06:26.000000', 'Login', 'mozo'),
(300, 64, '2024-06-25 16:06:29.000000', 'CobrarPedido', 'mozo'),
(301, 63, '2024-06-25 16:08:07.000000', 'Login', 'socio'),
(302, 63, '2024-06-25 16:32:29.000000', 'Login', 'socio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `estado` varchar(250) NOT NULL,
  `codigo_mesa` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin7 COLLATE=latin7_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `estado`, `codigo_mesa`) VALUES
(4, 'cerrada', 'LHdF7'),
(7, 'cerrada', 'vJA0R'),
(8, 'cerrada', 'yA0ab');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `estado` varchar(250) NOT NULL,
  `tiempo` int(6) NOT NULL,
  `codigo_mesa` varchar(5) NOT NULL,
  `codigo_pedido` varchar(5) NOT NULL,
  `precio_total` decimal(10,2) NOT NULL,
  `nombre_cliente` varchar(250) NOT NULL,
  `foto` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin7 COLLATE=latin7_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `estado`, `tiempo`, `codigo_mesa`, `codigo_pedido`, `precio_total`, `nombre_cliente`, `foto`) VALUES
(54, 'entregado', 15, 'vJA0R', 'ATnFG', 5000.00, 'marta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_productos`
--

CREATE TABLE `pedidos_productos` (
  `id` int(11) NOT NULL,
  `codigo_pedido` varchar(5) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `producto_estado` varchar(250) NOT NULL,
  `nombre_producto` varchar(250) NOT NULL,
  `tiempo_estimado` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin7 COLLATE=latin7_general_ci;

--
-- Volcado de datos para la tabla `pedidos_productos`
--

INSERT INTO `pedidos_productos` (`id`, `codigo_pedido`, `id_producto`, `producto_estado`, `nombre_producto`, `tiempo_estimado`) VALUES
(86, 'ATnFG', 1, 'entregado', 'milanesa a caballo', 5),
(87, 'ATnFG', 2, 'entregado', 'hamburguesa de garbanzo', 10),
(88, 'ATnFG', 2, 'entregado', 'hamburguesa de garbanzo', 15),
(89, 'ATnFG', 3, 'entregado', 'cerveza corona', 2),
(90, 'ATnFG', 4, 'entregado', 'daikiri', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `sector` varchar(250) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin7 COLLATE=latin7_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `sector`, `precio`) VALUES
(1, 'milanesa a caballo', 'cocina', 1200.00),
(2, 'hamburguesa de garbanzo', 'cocina', 1000.00),
(3, 'cerveza corona', 'cerveceria', 800.00),
(4, 'daikiri', 'bar', 1000.00),
(5, 'flan', 'cocina', 2000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiempo_pedido`
--

CREATE TABLE `tiempo_pedido` (
  `id` int(11) NOT NULL,
  `id_pedido_producto` int(100) NOT NULL,
  `codigo_pedido` varchar(5) NOT NULL,
  `tiempo_ini` datetime NOT NULL DEFAULT current_timestamp(),
  `tiempo_fin` datetime DEFAULT NULL,
  `entrega_estimada` datetime NOT NULL,
  `entrega_final` datetime DEFAULT NULL,
  `entrega_tarde` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin7 COLLATE=latin7_general_ci;

--
-- Volcado de datos para la tabla `tiempo_pedido`
--

INSERT INTO `tiempo_pedido` (`id`, `id_pedido_producto`, `codigo_pedido`, `tiempo_ini`, `tiempo_fin`, `entrega_estimada`, `entrega_final`, `entrega_tarde`) VALUES
(20, 86, 'ATnFG', '2024-06-25 14:34:59', '2024-06-25 15:51:46', '2024-06-25 14:39:59', '2024-06-25 16:03:29', 1),
(21, 87, 'ATnFG', '2024-06-25 14:35:25', '2024-06-25 15:51:40', '2024-06-25 14:45:25', '2024-06-25 16:03:25', 1),
(22, 88, 'ATnFG', '2024-06-25 14:35:30', '2024-06-25 15:51:27', '2024-06-25 14:50:30', '2024-06-25 16:03:21', 1),
(23, 90, 'ATnFG', '2024-06-25 14:36:34', '2024-06-25 15:52:33', '2024-06-25 14:51:34', '2024-06-25 16:02:52', 1),
(24, 89, 'ATnFG', '2024-06-25 14:37:04', '2024-06-25 15:54:50', '2024-06-25 14:39:04', '2024-06-25 16:03:14', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(250) NOT NULL,
  `clave` varchar(250) NOT NULL,
  `rol` varchar(250) NOT NULL,
  `baja` tinyint(1) DEFAULT NULL,
  `fecha_alta` date NOT NULL DEFAULT current_timestamp(),
  `fecha_baja` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin7 COLLATE=latin7_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `clave`, `rol`, `baja`, `fecha_alta`, `fecha_baja`) VALUES
(63, 'lud', '$2y$10$weXWGjGq8LIqh3BOSjZX9e7pNn2JoJxBBuGt0.7E7zats5p0qZ1W2', 'socio', 0, '2024-06-25', NULL),
(64, 'sofi', '$2y$10$icVhHjnxnMtYMaxaEVbtAu7U1XMjl7yNnRkYMK4AfZb/AI7KFjq3y', 'mozo', 0, '2024-06-25', NULL),
(65, 'fede', '$2y$10$JPlq7gTDMmRK2bM5nBAf.eBmW3Mx/lBvLFSfNh6LbuSok.3jJJmWm', 'bartender', 0, '2024-06-25', NULL),
(66, 'juan', '$2y$10$iLrKAHpip8fUP9RjkIijDO2sx9ajaDHf3iOm6IJkFfhO4PuS6kWbG', 'cervecero', 0, '2024-06-25', NULL),
(67, 'pepe', '$2y$10$BpIMggGi/qeXO1Pi4Du3V.GjhCfoFTiD6dRd4ND3NZ81ni1qK.peO', 'cocinero', 0, '2024-06-25', NULL),
(68, 'jose', '$2y$10$LrWkyNWgszITZI2oeqG2..4XAJ.d2NS4fgdhyXmuogM3n/3PACRYm', 'cocinero', 0, '2024-06-25', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos_productos`
--
ALTER TABLE `pedidos_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tiempo_pedido`
--
ALTER TABLE `tiempo_pedido`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `pedidos_productos`
--
ALTER TABLE `pedidos_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tiempo_pedido`
--
ALTER TABLE `tiempo_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
