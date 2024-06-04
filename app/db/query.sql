SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";



--
-- Tabla Usuarios
--

CREATE TABLE Usuarios (
  'id' int(11) NOT NULL,
  'usuario' varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  'clave' varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  'rol' varchar(250) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Tabla Productos
--

  CREATE TABLE Productos(
        'id' int AUTO_INCREMENT PRIMARY KEY,
        'nombre' VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
        'sector' VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
        'precio' DECIMAL(10,2) NOT NULL,
        'tiempo_estimado' TIME
    );

--
-- Tabla Mesas
--

 CREATE TABLE Mesas(
        'id' INT AUTO_INCREMENT PRIMARY KEY,
        'estado' VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
        'codigo_mesa' VARCHAR(5) NOT NULL
    );

--
-- Tabla Pedido
--

 CREATE TABLE Pedidos(
        'id' INT AUTO_INCREMENT PRIMARY KEY,
        'estado' VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
        'tiempo_estimado' TIME,
        'codigo_mesa' VARCHAR(5) NOT NULL,
        'codigo_pedido' VARCHAR(5) NOT NULL,
        'precio_total' DECIMAL(10,2) NOT NULL,
        'foto' BLOB,
        'nombre_cliente' VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL
    );




--
-- Volcado de datos para la tabla 'usuarios'
--

INSERT INTO 'Usuarios' ('id', 'usuario', 'clave', 'fechaBaja') VALUES
(1, 'franco', 'Hsu23sDsjseWs', NULL),
(2, 'pedro', 'dasdqsdw2sd23', NULL),
(3, 'jorge', 'sda2s2f332f2', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla 'usuarios'
--
ALTER TABLE 'usuarios'
  ADD PRIMARY KEY ('id');

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla 'usuarios'
--
ALTER TABLE 'usuarios'
  MODIFY 'id' int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;
