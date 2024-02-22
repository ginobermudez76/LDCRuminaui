CREATE DATABASE  IF NOT EXISTS `liga` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `liga`;
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

-- Dump completed on 2024-02-22 13:14:22
