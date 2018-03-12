CREATE DATABASE sepomex;
USE sepomex;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `cp` (
  `ID` int(255) NOT NULL,
  `cp` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `colonia` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `localidad` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `ciudad` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `estado` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

ALTER TABLE `cp`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `cp` (`cp`);

ALTER TABLE `cp`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT;
COMMIT;
