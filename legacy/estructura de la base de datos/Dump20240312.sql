-- MySQL dump 10.13  Distrib 8.0.32, for Win64 (x86_64)
--
-- Host: localhost    Database: liga
-- ------------------------------------------------------
-- Server version	8.0.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `carta_condolencias`
--

DROP TABLE IF EXISTS `carta_condolencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carta_condolencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mensaje` varchar(700) NOT NULL,
  `imagen` varchar(1000) DEFAULT NULL,
  `fecha_eliminar` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deportes`
--

DROP TABLE IF EXISTS `deportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deportes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(300) DEFAULT NULL,
  `imagen` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deportistas_destacados`
--

DROP TABLE IF EXISTS `deportistas_destacados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deportistas_destacados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_deportista` varchar(200) NOT NULL,
  `deporte_id` int DEFAULT NULL,
  `imagen` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deporte_id` (`deporte_id`),
  CONSTRAINT `deportistas_destacados_ibfk_1` FOREIGN KEY (`deporte_id`) REFERENCES `deportes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eventos`
--

DROP TABLE IF EXISTS `eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `fecha_eliminar` datetime DEFAULT NULL,
  `imagen` varchar(1000) DEFAULT NULL,
  `descripcion` varchar(300) DEFAULT NULL,
  `deporte_id` int DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `inscripciones` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deporte_id` (`deporte_id`),
  CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`deporte_id`) REFERENCES `deportes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_antesdeinsertar` BEFORE INSERT ON `eventos` FOR EACH ROW BEGIN
    -- Actualizar la fecha_eliminar basado en la fecha de fin + 3 días
    SET NEW.fecha_eliminar = DATE_ADD(NEW.fecha_fin, INTERVAL 3 DAY);

    -- Actualizar el estado basado en las condiciones especificadas
    SET NEW.estado = CASE
        WHEN NEW.fecha_fin < CURDATE() THEN 'Finalizado'
        WHEN NEW.fecha_inicio <= CURDATE() AND NEW.fecha_fin >= CURDATE() THEN 'En curso'
        WHEN NEW.fecha_inicio > CURDATE() THEN 'Proximamente'
        ELSE NEW.estado
    END;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_despuesdeinsertar` AFTER INSERT ON `eventos` FOR EACH ROW BEGIN
    -- Lógica adicional después de la inserción si es necesario
    -- Puedes realizar acciones adicionales aquí si es necesario
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_before_update_evento` BEFORE UPDATE ON `eventos` FOR EACH ROW BEGIN
    -- Verificar si la fecha de fin ha cambiado
    IF NEW.fecha_fin <> OLD.fecha_fin THEN
        -- Actualizar la fecha de eliminación basado en la nueva fecha de fin + 3 días
        SET NEW.fecha_eliminar = DATE_ADD(NEW.fecha_fin, INTERVAL 3 DAY);
    END IF;

    -- Actualizar el estado basado en las condiciones especificadas
    SET NEW.estado = CASE
        WHEN NEW.fecha_fin < CURDATE() THEN 'Finalizado'
        WHEN NEW.fecha_inicio <= CURDATE() AND NEW.fecha_fin >= CURDATE() THEN 'En curso'
        WHEN NEW.fecha_inicio > CURDATE() THEN 'Próximamente'
        ELSE NEW.estado
    END;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `external`
--

DROP TABLE IF EXISTS `external`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `external` (
  `id_ext` int NOT NULL AUTO_INCREMENT,
  `ext_nombre` varchar(45) DEFAULT NULL,
  `ext_snombre` varchar(45) DEFAULT NULL,
  `ext_apellido` varchar(45) DEFAULT NULL,
  `ext_sapellido` varchar(45) DEFAULT NULL,
  `ext_email` varchar(45) DEFAULT NULL,
  `ext_celular` varchar(45) DEFAULT NULL,
  `cedula` varchar(45) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  PRIMARY KEY (`id_ext`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `galeria_imagenes`
--

DROP TABLE IF EXISTS `galeria_imagenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `galeria_imagenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) NOT NULL,
  `id_tipo` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ruta_imagenes` varchar(255) DEFAULT NULL,
  `ruta_carpeta` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipo_id_tipo_index` (`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inscripciones_eventos`
--

DROP TABLE IF EXISTS `inscripciones_eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inscripciones_eventos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `evento_id` int DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evento_id` (`evento_id`),
  CONSTRAINT `inscripciones_eventos_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logros`
--

DROP TABLE IF EXISTS `logros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `deporte_id` int DEFAULT NULL,
  `imagen` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deporte_id` (`deporte_id`),
  CONSTRAINT `logros_ibfk_1` FOREIGN KEY (`deporte_id`) REFERENCES `deportes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `noticias`
--

DROP TABLE IF EXISTS `noticias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `noticias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) DEFAULT NULL,
  `imagen` varchar(1000) DEFAULT NULL,
  `cuerpo` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `rol_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solicitud`
--

DROP TABLE IF EXISTS `solicitud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitud` (
  `s_id` int NOT NULL AUTO_INCREMENT,
  `s_fecha` datetime DEFAULT NULL,
  `s_doc` varchar(255) DEFAULT NULL,
  `s_valor` double DEFAULT NULL,
  `tipo` int DEFAULT NULL,
  `solicitante` int DEFAULT NULL,
  `encargado` int DEFAULT NULL,
  `solicitantext` int DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` int DEFAULT NULL,
  `departamento_encargado` int DEFAULT NULL,
  PRIMARY KEY (`s_id`),
  UNIQUE KEY `s_id_UNIQUE` (`s_id`),
  KEY `fk_solicitante` (`solicitante`),
  KEY `fk_encargado` (`encargado`),
  KEY `fk_estado` (`estado`),
  KEY `fk_solicitantext` (`solicitantext`),
  KEY `fk_tipo` (`tipo`),
  KEY `fk_departamento_encargado` (`departamento_encargado`),
  CONSTRAINT `fk_departamento_encargado` FOREIGN KEY (`departamento_encargado`) REFERENCES `roles` (`id_rol`),
  CONSTRAINT `fk_encargado` FOREIGN KEY (`encargado`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_estado` FOREIGN KEY (`estado`) REFERENCES `solicitud_estado` (`id_estado`),
  CONSTRAINT `fk_solicitante` FOREIGN KEY (`solicitante`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_solicitantext` FOREIGN KEY (`solicitantext`) REFERENCES `external` (`id_ext`),
  CONSTRAINT `fk_tipo` FOREIGN KEY (`tipo`) REFERENCES `solicitud_tipo` (`id_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solicitud_estado`
--

DROP TABLE IF EXISTS `solicitud_estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitud_estado` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `estado_nombre` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solicitud_tipo`
--

DROP TABLE IF EXISTS `solicitud_tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitud_tipo` (
  `id_tipo` int NOT NULL AUTO_INCREMENT,
  `name_tipo` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `primer_nombre` varchar(45) DEFAULT NULL,
  `segundo_nombre` varchar(45) DEFAULT NULL,
  `primer_apellido` varchar(45) DEFAULT NULL,
  `segundo_apellido` varchar(45) DEFAULT NULL,
  `cedula` varchar(10) DEFAULT NULL,
  `celular` varchar(10) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `nombre_usuario` varchar(150) DEFAULT NULL,
  `contrasena` varchar(1000) DEFAULT NULL,
  `rol` int DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_rol` (`rol`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'liga'
--

--
-- Dumping routines for database 'liga'
--
/*!50003 DROP PROCEDURE IF EXISTS `ActualizarEstadoEventos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ActualizarEstadoEventos`()
BEGIN
    DECLARE fecha_actual DATE;
    SET fecha_actual = CURDATE();

    UPDATE eventos
    SET estado = 
        CASE 
            WHEN fecha_actual BETWEEN fecha_inicio AND fecha_fin THEN 'En curso'
            WHEN fecha_actual > fecha_fin THEN 'Finalizado'
            WHEN fecha_actual < fecha_inicio THEN 'Proximamente'
        END;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `actualizar_departamento_encargado_proc` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_departamento_encargado_proc`(
    IN tipo_param INT,
    IN s_id_param INT
)
BEGIN
    DECLARE departamento_id INT;
    DECLARE usuario_id INT;

    -- Determina el departamento encargado basado en el tipo de solicitud
    IF tipo_param = 1 THEN
        SET departamento_id = 2;
    ELSEIF tipo_param = 2 OR tipo_param = 3 OR tipo_param = 4 THEN
        SET departamento_id = 4;
    END IF;

    -- Encuentra el usuario con el rol adecuado y el menor número de solicitudes asignadas con estado 1
    SELECT u.id INTO usuario_id
    FROM usuarios u
    LEFT JOIN (
        SELECT encargado, COUNT(*) as cnt
        FROM solicitud
        WHERE estado = 1
        GROUP BY encargado
    ) as s ON u.id = s.encargado
    WHERE u.rol = departamento_id
    ORDER BY s.cnt ASC, u.id ASC -- Prioriza el usuario con menos cargas de trabajo y luego por el id más bajo
    LIMIT 1;

    -- Actualiza el departamento encargado y el encargado en la tabla solicitud
    UPDATE solicitud
    SET departamento_encargado = departamento_id,
        encargado = usuario_id, -- Asumiendo que siempre encontramos un usuario_id
        estado = '1'
    WHERE s_id = s_id_param;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `info_deportistas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `info_deportistas`()
SELECT
        d.id AS id,
        d.nombre_deportista AS nombre,
        dp.nombre AS deporte,
        d.imagen
    FROM
        deportistas_destacados d
    LEFT JOIN
        deportes dp ON d.deporte_id = dp.id ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `info_logros` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `info_logros`()
SELECT
        l.id AS id,
        l.titulo AS titulo,
        dp.nombre AS deporte,
        l.imagen
    FROM
        logros l
    LEFT JOIN
        deportes dp ON l.deporte_id = dp.id ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `info_usuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `info_usuario`(IN usuario_id INT)
SELECT 
        u.id,
        CONCAT(u.primer_nombre, ' ', u.segundo_nombre, ' ', u.primer_apellido, ' ', u.segundo_apellido) AS persona,
        r.rol_name AS rol,
        u.cedula as cedula,
        u.celular as celular,
		u.fecha_nac as cumpleanos,
        u.correo as correo,
        u.nombre_usuario as usuario
    FROM 
        usuarios u
    LEFT JOIN 
        roles r ON u.rol = r.id_rol
    WHERE
        u.id = usuario_id ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `mostrar_solicitudes` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `mostrar_solicitudes`(IN usuario_id INT)
BEGIN
    SELECT 
        s.s_id as id,
        s.s_fecha as fecha,
        s.s_doc as doc,
        s.s_valor as valor,
        st.name_tipo AS tipo,
        u1.nombre_usuario AS solicitante,
        u2.nombre_usuario AS encargado,
        e.cedula AS solicitantext,
        se.estado_nombre AS estado,
        r.rol_name AS departamento_encargado,
        s.descripcion
    FROM 
        solicitud s
    LEFT JOIN 
        solicitud_tipo st ON s.tipo = st.id_tipo
    LEFT JOIN 
        usuarios u1 ON s.solicitante = u1.id
    LEFT JOIN 
        usuarios u2 ON s.encargado = u2.id
    LEFT JOIN 
        external e ON s.solicitantext = e.id_ext
    LEFT JOIN 
        solicitud_estado se ON s.estado = se.id_estado
    LEFT JOIN 
        roles r ON s.departamento_encargado = r.id_rol
    WHERE
        s.solicitante = usuario_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ObtenerInfoEventos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ObtenerInfoEventos`()
BEGIN
    SELECT 
        e.id AS evento_id,
        e.nombre AS nombre_evento,
        e.fecha_inicio,
        e.fecha_fin,
        e.imagen,
        e.descripcion,
        e.estado,
        e.inscripciones,
        d.nombre AS nombre_deporte
    FROM 
        eventos e
    INNER JOIN 
        deportes d ON e.deporte_id = d.id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `solicitudes_asignadas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `solicitudes_asignadas`(IN usuario_id INT)
BEGIN
    SELECT 
        s.s_id,
        s.s_fecha,
        s.s_doc,
        s.s_valor,
        st.name_tipo AS tipo,
        u1.nombre_usuario AS solicitante,
        u2.nombre_usuario AS encargado,
        e.cedula AS solicitantext,
        se.estado_nombre AS estado,
        r.rol_name AS departamento_encargado,
        s.descripcion
    FROM 
        solicitud s
    LEFT JOIN 
        solicitud_tipo st ON s.tipo = st.id_tipo
    LEFT JOIN 
        usuarios u1 ON s.solicitante = u1.id
    LEFT JOIN 
        usuarios u2 ON s.encargado = u2.id
    LEFT JOIN 
        external e ON s.solicitantext = e.id_ext
    LEFT JOIN 
        solicitud_estado se ON s.estado = se.id_estado
    LEFT JOIN 
        roles r ON s.departamento_encargado = r.id_rol
    WHERE
        s.encargado = usuario_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-03-12 15:11:41
