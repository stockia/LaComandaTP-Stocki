-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 21-11-2023 a las 22:41:06
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ComandaTP`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `idMozo` int(11) NOT NULL,
  `idPedido` int(11) NOT NULL,
  `estado` varchar(77) NOT NULL,
  `statusMesa` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `idMozo`, `idPedido`, `estado`, `statusMesa`) VALUES
(4, 14, 4, 'con cliente comiendo', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `idMozo` int(11) NOT NULL,
  `idMesa` int(11) NOT NULL,
  `estado` varchar(77) NOT NULL,
  `nombreCliente` varchar(77) NOT NULL,
  `codigoUnico` varchar(5) NOT NULL,
  `tiempoEstimado` varchar(77) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `statusPedido` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `idMozo`, `idMesa`, `estado`, `nombreCliente`, `codigoUnico`, `tiempoEstimado`, `foto`, `statusPedido`) VALUES
(4, 14, 1, 'listo para servir', 'Alex', 'QLMCN', 'listo', '1_Alex.jpeg', 'activo'),
(5, 0, 1, 'listo para servir', 'Juan', 'LNBUB', '15 minutos', 'http://example.com/foto1.jpg', 'activo'),
(6, 0, 2, 'entregado', 'Maria', 'XQMAC', '10 minutos', 'http://example.com/foto2.jpg', 'activo'),
(7, 0, 3, 'listo para servir', 'Pedro', 'BXLEE', '20 minutos', 'http://example.com/foto3.jpg', 'activo'),
(8, 0, 1, 'en preparacion', 'Juan', 'RKWEB', '15 minutos', 'http://example.com/foto1.jpg', 'activo'),
(9, 0, 2, 'en preparacion', 'Maria', 'KBQAP', '10 minutos', 'http://example.com/foto2.jpg', 'activo'),
(10, 0, 3, 'entregado', 'Pedro', 'RDEWH', '20 minutos', 'http://example.com/foto3.jpg', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_producto`
--

CREATE TABLE `pedido_producto` (
  `id` int(11) NOT NULL,
  `idPedido` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `estado` varchar(77) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(77) NOT NULL,
  `descripcion` varchar(77) NOT NULL,
  `precio` float NOT NULL,
  `tipo` varchar(77) NOT NULL,
  `statusProducto` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `tipo`, `statusProducto`) VALUES
(2, 'Pancho completo', 'papitas, morron, cebolla caramelizada', 2900, 'comida', 'activo'),
(3, 'Pizza super completa', 'En realidad es una pizza sin queso', 5000, 'comida', 'borrado'),
(4, 'Pizza de cancha', 'ajo, cebolla', 2000, 'comida', 'activo'),
(5, 'Pizza napolitana', 'a la piedra', 5000, 'comida', 'activo'),
(6, 'Cerveza', 'rubia', 1300, 'bebida', 'activo'),
(7, 'Flan', 'mixto', 2300, 'comida', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(77) NOT NULL,
  `rol` varchar(66) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(10) NOT NULL,
  `statusUsuario` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `rol`, `username`, `password`, `statusUsuario`) VALUES
(13, 'Lionel Messi', 'socio', 'lionel', 'qatar2022', 'activo'),
(14, 'Fernando Alonso', 'mozo', 'alonsofer', '12345', 'activo'),
(16, 'Carlos Tevez', 'bartender', 'apache', '12345', 'activo');

--
-- Índices para tablas volcadas
--

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
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
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
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
