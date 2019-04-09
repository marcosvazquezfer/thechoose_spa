-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 08-01-2019 a las 15:46:27
-- Versión del servidor: 5.7.23-0ubuntu0.16.04.1-log
-- Versión de PHP: 5.6.37-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- -----------------------------------------------------
-- Esquema TheChoose_SPA
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `TheChoose_SPA` ;
CREATE SCHEMA IF NOT EXISTS `TheChoose_SPA` DEFAULT CHARACTER SET utf8 ;
USE `TheChoose_SPA` ;

--
-- Base de datos: `TheChoose_SPA`
--
CREATE DATABASE IF NOT EXISTS `TheChoose_SPA` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `TheChoose_SPA`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `holes`
--

DROP TABLE IF EXISTS `holes`;
CREATE TABLE `holes` (
  `pollId` int(11) NOT NULL,
  `date` date NOT NULL,
  `timeStart` time NOT NULL,
  `timeFinish` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participations`
--

DROP TABLE IF EXISTS `participations`;
CREATE TABLE `participations` (
  `pollId` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `anonymousId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `polls`
--

DROP TABLE IF EXISTS `polls`;
CREATE TABLE `polls` (
  `pollId` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `anonymous` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `selected`
--

DROP TABLE IF EXISTS `selected`;
CREATE TABLE `selected` (
  `pollId` int(11) NOT NULL,
  `date` date NOT NULL,
  `timeStart` time NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `anonymousId` int(11) DEFAULT NULL,
  `selection` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `email` varchar(255) NOT NULL,
  `completeName` varchar(255) NOT NULL,
  `passwd` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anonymous_users`
--

DROP TABLE IF EXISTS `anonymous_users`;
CREATE TABLE `anonymous_users` (
  `anonymousId` int(11) NOT NULL,
  `completeName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `holes`
--
ALTER TABLE `holes`
  ADD PRIMARY KEY (`pollId`,`date`,`timeStart`);

--
-- Indices de la tabla `participations`
--
ALTER TABLE `participations`
  ADD UNIQUE KEY `participation` (`pollId`,`email`, `anonymousId`) USING BTREE,
  ADD KEY `fk_participations_2_idx` (`email`) USING BTREE,
  ADD KEY `fk_participations_3_idx` (`anonymousId`) USING BTREE;

--
-- Indices de la tabla `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`pollId`),
  ADD UNIQUE KEY `link_UNIQUE` (`link`),
  ADD KEY `fk_polls_1_idx` (`email`);

--
-- Indices de la tabla `selected`
--
ALTER TABLE `selected`
  ADD UNIQUE KEY `selection` (`pollId`,`date`,`timeStart`,`email`, `anonymousId`, `selection`) USING BTREE,
  ADD KEY `fk_selected_2_idx` (`email`),
  ADD KEY `fk_selected_3_idx` (`anonymousId`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `anonymous_users`
--
ALTER TABLE `anonymous_users`
  ADD PRIMARY KEY (`anonymousId`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `polls`
--
ALTER TABLE `polls`
  MODIFY `pollId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `anonymous_users`
--
ALTER TABLE `anonymous_users`
  MODIFY `anonymousId` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `holes`
--
ALTER TABLE `holes`
  ADD CONSTRAINT `fk_holes_1` FOREIGN KEY (`pollId`) REFERENCES `polls` (`pollId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `participations`
--
ALTER TABLE `participations`
  ADD CONSTRAINT `fk_participations_1` FOREIGN KEY (`pollId`) REFERENCES `polls` (`pollId`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_participations_2` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_participations_3` FOREIGN KEY (`anonymousId`) REFERENCES `anonymous_users` (`anonymousId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `fk_polls_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `selected`
--
ALTER TABLE `selected`
  ADD CONSTRAINT `fk_selected_1` FOREIGN KEY (`pollId`,`date`,`timeStart`) REFERENCES `holes` (`pollId`, `date`, `timeStart`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_selected_2` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_selected_3` FOREIGN KEY (`anonymousId`) REFERENCES `anonymous_users` (`anonymousId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
