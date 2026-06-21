CREATE DATABASE  IF NOT EXISTS `liga_legacy` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `liga_legacy`;
-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: liga_legacy
-- ------------------------------------------------------
-- Server version	8.0.37

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
  `mensaje` varchar(5000) NOT NULL,
  `imagen` varchar(1000) DEFAULT NULL,
  `fecha_eliminar` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carta_condolencias`
--

LOCK TABLES `carta_condolencias` WRITE;
/*!40000 ALTER TABLE `carta_condolencias` DISABLE KEYS */;
/*!40000 ALTER TABLE `carta_condolencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cursos`
--

DROP TABLE IF EXISTS `cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cursos` (
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
  CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`deporte_id`) REFERENCES `deportes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos`
--

LOCK TABLES `cursos` WRITE;
/*!40000 ALTER TABLE `cursos` DISABLE KEYS */;
/*!40000 ALTER TABLE `cursos` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deportes`
--

LOCK TABLES `deportes` WRITE;
/*!40000 ALTER TABLE `deportes` DISABLE KEYS */;
INSERT INTO `deportes` VALUES (111,'dasdas','dasdas','../uploads/deportes/WhatsApp Image 2024-04-18 at 8.40.24 PM.jpeg'),(112,'Deporte de prueba','',''),(113,'Deporte de prueba','',''),(114,'transistor','','../uploads/deportes/image.png'),(115,'gfdgf','','../uploads/deportes/irf640n-mosfets-transistors.jpg');
/*!40000 ALTER TABLE `deportes` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deportistas_destacados`
--

LOCK TABLES `deportistas_destacados` WRITE;
/*!40000 ALTER TABLE `deportistas_destacados` DISABLE KEYS */;
/*!40000 ALTER TABLE `deportistas_destacados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos`
--

DROP TABLE IF EXISTS `documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `descripcion` varchar(2000) DEFAULT NULL,
  `documento` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos`
--

LOCK TABLES `documentos` WRITE;
/*!40000 ALTER TABLE `documentos` DISABLE KEYS */;
INSERT INTO `documentos` VALUES (61,'sxASrterewrwerwe1111','asd','../uploads/documentos/ANEXO 1 (UIC) FORMULARIO PARA PRESENTACIÓN DE BIBLIOTECA[1].pdf');
/*!40000 ALTER TABLE `documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `escenarios`
--

DROP TABLE IF EXISTS `escenarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `escenarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `ubicacion` varchar(5000) DEFAULT NULL,
  `imagen` varchar(1000) DEFAULT NULL,
  `direccion` varchar(500) DEFAULT NULL,
  `telefono` varchar(10) DEFAULT NULL,
  `supervisor` varchar(250) DEFAULT NULL,
  `celular` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `escenarios`
--

LOCK TABLES `escenarios` WRITE;
/*!40000 ALTER TABLE `escenarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `escenarios` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos`
--

LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `external`
--

LOCK TABLES `external` WRITE;
/*!40000 ALTER TABLE `external` DISABLE KEYS */;
/*!40000 ALTER TABLE `external` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=449 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galeria_imagenes`
--

LOCK TABLES `galeria_imagenes` WRITE;
/*!40000 ALTER TABLE `galeria_imagenes` DISABLE KEYS */;
INSERT INTO `galeria_imagenes` VALUES (415,'Curso',11,'adasd','../uploads/cursos/adasd_11/crop.jpeg','../uploads/cursos/adasd_11'),(416,'Curso',11,'adasd','../uploads/cursos/adasd_11/peliculas-y-documentales-montanismo.jpg','../uploads/cursos/adasd_11'),(417,'Curso',11,'adasd','../uploads/cursos/adasd_11/Equipo-alpinismo.jpg','../uploads/cursos/adasd_11'),(418,'Curso',11,'adasd','../uploads/cursos/adasd_11/028_Senderismo_Montanismo_FrontCover_Michelangelo-Oprandi.jpg','../uploads/cursos/adasd_11'),(419,'Curso',11,'adasd','../uploads/cursos/adasd_11/lesiones_natacion.jpg','../uploads/cursos/adasd_11'),(420,'Curso',11,'adasd','../uploads/cursos/adasd_11/garota-nadando-em-piscina-olimpica.jpeg','../uploads/cursos/adasd_11'),(421,'Curso',11,'adasd','../uploads/cursos/adasd_11/natacion-e1562943144215.jpg','../uploads/cursos/adasd_11'),(422,'Curso',11,'adasd','../uploads/cursos/adasd_11/i.jpeg','../uploads/cursos/adasd_11'),(426,'Curso',12,'asdasd','../uploads/cursos/asdasd_12/3e705113-9644-447b-9c76-d225820136e8_16-9-aspect-ratio_default_0.jpg','../uploads/cursos/asdasd_12'),(427,'Curso',12,'asdasd','../uploads/cursos/asdasd_12/lesiones_natacion.jpg','../uploads/cursos/asdasd_12'),(428,'Curso',12,'asdasd','../uploads/cursos/asdasd_12/garota-nadando-em-piscina-olimpica.jpeg','../uploads/cursos/asdasd_12'),(429,'Curso',12,'asdasd','../uploads/cursos/asdasd_12/natacion-e1562943144215.jpg','../uploads/cursos/asdasd_12'),(430,'Curso',12,'asdasd','../uploads/cursos/asdasd_12/i.jpeg','../uploads/cursos/asdasd_12'),(431,'Curso',12,'asdasd','../uploads/cursos/asdasd_12/exvzqcvorticinejmmel.jpeg','../uploads/cursos/asdasd_12'),(432,'Curso',12,'asdasd','../uploads/cursos/asdasd_12/futbolfemenino-scaled.jpg','../uploads/cursos/asdasd_12'),(433,'Curso',12,'asdasd','../uploads/cursos/asdasd_12/637fcad95dd96.jpeg','../uploads/cursos/asdasd_12'),(434,'Curso',12,'asdasd','../uploads/cursos/asdasd_12/63eaf64168c8c.jpeg','../uploads/cursos/asdasd_12');
/*!40000 ALTER TABLE `galeria_imagenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_solicitud`
--

DROP TABLE IF EXISTS `historial_solicitud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_solicitud` (
  `id` int NOT NULL AUTO_INCREMENT,
  `solicitud_id` int NOT NULL,
  `fecha_asignacion` datetime DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT NULL,
  `departamento` int DEFAULT NULL,
  `responsable` int DEFAULT NULL,
  `estado` int DEFAULT NULL,
  `tipo` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estado` (`estado`),
  KEY `tipo` (`tipo`),
  KEY `departamento` (`departamento`),
  KEY `responsable` (`responsable`),
  CONSTRAINT `historial_solicitud_ibfk_2` FOREIGN KEY (`estado`) REFERENCES `solicitud_estado` (`id_estado`),
  CONSTRAINT `historial_solicitud_ibfk_3` FOREIGN KEY (`tipo`) REFERENCES `solicitud_tipo` (`id_tipo`),
  CONSTRAINT `historial_solicitud_ibfk_4` FOREIGN KEY (`departamento`) REFERENCES `roles` (`id_rol`),
  CONSTRAINT `historial_solicitud_ibfk_5` FOREIGN KEY (`responsable`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=208 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_solicitud`
--

LOCK TABLES `historial_solicitud` WRITE;
/*!40000 ALTER TABLE `historial_solicitud` DISABLE KEYS */;
INSERT INTO `historial_solicitud` VALUES (122,167,'2024-03-21 13:06:27','2024-03-21 13:06:42',9,121,1,1),(123,167,'2024-03-21 13:06:42','2024-03-21 13:06:52',2,118,2,1),(124,167,'2024-03-21 13:06:52','2024-03-21 13:07:06',3,116,3,1),(125,167,'2024-03-21 13:07:06','2024-03-21 13:07:11',3,116,5,1),(126,167,'2024-03-21 13:07:11','2024-03-21 13:07:11',3,116,6,1),(127,168,'2024-03-24 13:54:42','2024-03-24 16:25:56',9,121,1,1),(128,169,'2024-03-24 13:55:31','2024-03-24 16:04:20',9,121,1,4),(129,169,'2024-03-24 16:04:20','2024-03-24 16:08:50',2,118,2,1),(130,169,'2024-03-24 16:08:50','2024-03-24 16:08:52',9,121,5,1),(131,169,'2024-03-24 16:08:52','2024-03-24 16:08:52',9,121,6,4),(132,170,'2024-03-24 16:10:52','2024-03-24 16:11:15',9,121,1,4),(133,170,'2024-03-24 16:11:15','2024-03-24 16:12:59',9,121,5,1),(134,170,'2024-03-24 16:12:59','2024-03-24 16:12:59',9,121,6,4),(135,171,'2024-03-24 16:13:32','2024-03-24 16:15:39',9,121,1,4),(136,171,'2024-03-24 16:15:39','2024-03-24 16:15:45',9,121,4,1),(137,171,'2024-03-24 16:15:45','2024-03-24 16:15:45',9,121,6,4),(138,168,'2024-03-24 16:25:56','2024-03-24 16:35:56',9,121,1,4),(139,168,'2024-03-24 16:35:56','2024-03-30 12:43:58',NULL,NULL,1,NULL),(140,172,'2024-03-24 16:39:07','2024-03-24 16:39:25',9,121,1,4),(141,172,'2024-03-24 16:39:25',NULL,NULL,NULL,1,NULL),(142,173,'2024-03-24 16:41:32','2024-03-24 16:41:53',9,121,1,4),(143,174,'2024-03-24 16:48:02','2024-03-24 16:48:14',9,121,1,4),(144,175,'2024-03-24 16:52:15','2024-03-24 16:52:30',9,121,1,4),(145,176,'2024-03-24 16:53:30','2024-03-24 16:53:45',9,121,1,4),(146,177,'2024-03-24 16:56:42','2024-03-24 16:57:32',9,121,1,4),(147,177,'2024-03-24 16:57:32',NULL,2,118,2,4),(148,178,'2024-03-24 17:01:56','2024-03-24 17:23:17',9,121,1,4),(149,179,'2024-03-24 17:09:37','2024-03-24 17:28:35',9,121,1,4),(150,178,'2024-03-24 17:23:17','2024-03-24 17:24:07',9,121,1,2),(151,178,'2024-03-24 17:24:07','2024-03-24 17:24:28',9,121,1,4),(152,178,'2024-03-24 17:24:28','2024-03-24 17:30:35',9,121,1,2),(153,179,'2024-03-24 17:28:35','2024-03-24 17:32:52',9,121,1,1),(154,178,'2024-03-24 17:30:35','2024-03-24 18:02:54',9,121,1,1),(155,179,'2024-03-24 17:32:52','2024-03-24 17:47:39',9,121,1,4),(156,179,'2024-03-24 17:47:39','2024-03-24 17:47:47',9,121,1,1),(157,179,'2024-03-24 17:47:47','2024-03-24 17:51:55',9,121,1,4),(158,179,'2024-03-24 17:51:55','2024-03-24 17:53:36',9,121,1,2),(159,179,'2024-03-24 17:53:36','2024-03-24 18:15:53',9,121,1,4),(160,178,'2024-03-24 18:02:54','2024-03-24 19:55:36',9,121,1,2),(161,179,'2024-03-24 18:15:53','2024-03-24 18:16:06',9,121,1,2),(162,179,'2024-03-24 18:16:06','2024-03-24 19:55:21',9,121,1,4),(163,179,'2024-03-24 19:55:21',NULL,4,115,2,4),(164,178,'2024-03-24 19:55:36','2024-04-05 16:04:53',9,121,1,1),(165,168,'2024-03-30 12:43:58','2024-03-30 13:00:17',9,121,1,1),(166,180,'2024-03-30 12:45:12','2024-03-31 21:09:41',9,121,1,2),(167,168,'2024-03-30 13:00:17',NULL,2,118,2,1),(168,181,'2024-03-30 15:45:55',NULL,9,121,1,1),(169,182,'2024-03-31 21:09:02','2024-04-09 01:41:53',9,121,1,2),(170,180,'2024-03-31 21:09:41','2024-04-09 01:44:47',1,117,1,3),(171,183,'2024-04-01 18:34:30','2024-04-05 16:06:21',9,121,1,2),(172,184,'2024-04-05 16:04:28',NULL,9,121,1,2),(173,178,'2024-04-05 16:04:53',NULL,2,118,2,1),(174,183,'2024-04-05 16:06:21','2024-04-05 16:06:23',9,121,4,1),(175,183,'2024-04-05 16:06:23','2024-04-05 16:06:23',9,121,6,2),(176,182,'2024-04-09 01:41:53','2024-04-09 01:42:14',9,121,1,2),(177,182,'2024-04-09 01:42:14','2024-04-09 01:44:35',9,121,1,2),(178,182,'2024-04-09 01:44:35','2024-04-09 01:45:36',9,121,1,2),(179,180,'2024-04-09 01:44:47','2024-04-09 01:45:09',1,117,1,3),(180,180,'2024-04-09 01:45:09','2024-05-30 19:39:15',1,117,1,3),(181,182,'2024-04-09 01:45:36','2024-05-30 19:40:31',9,121,1,2),(182,185,'2024-04-10 21:14:09',NULL,9,121,1,1),(183,186,'2024-04-10 21:15:42',NULL,9,121,1,1),(184,187,'2024-04-10 21:16:29','2024-06-21 18:38:41',9,121,1,2),(185,188,'2024-04-11 16:28:34','2024-05-30 19:38:48',9,121,1,4),(186,189,'2024-04-16 18:28:08','2024-06-21 18:41:22',9,121,1,1),(187,190,'2024-04-16 18:28:21','2024-06-21 18:47:22',9,121,1,1),(188,191,'2024-05-30 18:22:09',NULL,9,121,1,1),(189,188,'2024-05-30 19:38:48','2024-05-30 19:41:01',1,117,1,3),(190,180,'2024-05-30 19:39:15','2024-05-30 19:39:18',1,117,5,3),(191,180,'2024-05-30 19:39:18','2024-05-30 19:39:18',1,117,6,3),(192,182,'2024-05-30 19:40:31','2024-05-30 19:40:58',1,117,1,3),(193,182,'2024-05-30 19:40:58','2024-05-30 19:41:02',1,117,5,3),(194,188,'2024-05-30 19:41:01','2024-05-30 19:41:03',1,117,5,3),(195,182,'2024-05-30 19:41:02','2024-05-30 19:41:02',1,117,6,3),(196,188,'2024-05-30 19:41:03','2024-05-30 19:41:03',1,117,6,3),(197,192,'2024-05-30 19:41:48','2024-05-30 19:42:08',1,117,1,3),(198,192,'2024-05-30 19:42:08','2024-05-30 19:42:09',1,117,5,3),(199,192,'2024-05-30 19:42:09','2024-05-30 19:42:09',1,117,6,3),(200,187,'2024-06-21 18:38:41',NULL,4,115,2,2),(201,189,'2024-06-21 18:41:22','2024-06-21 18:42:30',9,121,4,1),(202,189,'2024-06-21 18:42:30','2024-06-21 18:42:30',9,121,6,1),(203,190,'2024-06-21 18:47:22','2024-06-22 00:12:17',9,121,1,4),(204,190,'2024-06-22 00:12:17',NULL,4,115,2,4),(205,193,'2024-06-22 00:18:16','2025-02-26 15:30:49',9,121,1,1),(206,193,'2025-02-26 15:30:49',NULL,9,121,1,1),(207,194,'2025-02-26 15:31:02',NULL,9,121,1,1);
/*!40000 ALTER TABLE `historial_solicitud` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `inscripciones_eventos`
--

LOCK TABLES `inscripciones_eventos` WRITE;
/*!40000 ALTER TABLE `inscripciones_eventos` DISABLE KEYS */;
/*!40000 ALTER TABLE `inscripciones_eventos` ENABLE KEYS */;
UNLOCK TABLES;

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
  `tipologro` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deporte_id` (`deporte_id`),
  KEY `fk_tipo_logro` (`tipologro`),
  CONSTRAINT `logros_ibfk_1` FOREIGN KEY (`deporte_id`) REFERENCES `deportes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logros`
--

LOCK TABLES `logros` WRITE;
/*!40000 ALTER TABLE `logros` DISABLE KEYS */;
INSERT INTO `logros` VALUES (33,'saSa',111,'../uploads/deportes/logros/WhatsApp Image 2024-04-17 at 8.15.06 PM.jpeg','Medalla');
/*!40000 ALTER TABLE `logros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noticias`
--

DROP TABLE IF EXISTS `noticias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `noticias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `imagen` varchar(1000) DEFAULT NULL,
  `cuerpo` varchar(5000) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noticias`
--

LOCK TABLES `noticias` WRITE;
/*!40000 ALTER TABLE `noticias` DISABLE KEYS */;
/*!40000 ALTER TABLE `noticias` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Presidente'),(2,'Metodologo'),(3,'Tesoreria'),(4,'Coordinador general'),(5,'Deportista'),(6,'Entrenador'),(7,'Publicista'),(8,'Administrador'),(9,'Secretaría');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

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
  `descripcion` varchar(5000) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud`
--

LOCK TABLES `solicitud` WRITE;
/*!40000 ALTER TABLE `solicitud` DISABLE KEYS */;
INSERT INTO `solicitud` VALUES (167,'2024-03-21 13:06:27','',NULL,1,112,NULL,NULL,'',6,NULL),(168,'2024-03-24 13:54:42','../uploads/documentos/solicitudes/yngrid.melo7890@ldcruminahui.com/Sr. Bermudez (1).pdf',NULL,1,113,118,NULL,'',2,2),(169,'2024-03-24 13:55:31','../uploads/documentos/solicitudes/cecilia.santos4816@ldcruminahui.com/Horarios Software 2024-1.pdf',NULL,4,121,NULL,NULL,'',6,NULL),(170,'2024-03-24 16:10:52','',NULL,4,113,NULL,NULL,'',6,NULL),(171,'2024-03-24 16:13:32','',NULL,4,113,NULL,NULL,'',6,NULL),(174,'2024-03-24 16:48:02','',NULL,4,121,116,NULL,'',2,3),(175,'2024-03-24 16:52:15','',NULL,4,121,116,NULL,'',2,3),(176,'2024-03-24 16:53:30','',NULL,4,121,115,NULL,'',2,4),(177,'2024-03-24 16:56:42','',NULL,4,121,118,NULL,'',2,2),(178,'2024-03-24 17:01:56','',NULL,1,116,118,NULL,'',2,2),(179,'2024-03-24 17:09:37','',NULL,4,121,115,NULL,'',2,4),(180,'2024-03-30 12:45:12',NULL,NULL,3,113,NULL,NULL,'',6,NULL),(182,'2024-03-31 21:09:02','../uploads/documentos/solicitudes/yngrid.melo7890@ldcruminahui.com/Dialnet-DesarrolloDeLasHabilidadesTecnicasEnElBaloncesto-8383798.pdf',NULL,3,113,NULL,NULL,'asa ',6,NULL),(183,'2024-04-01 18:34:30','',NULL,2,121,NULL,NULL,'',6,NULL),(187,'2024-04-10 21:16:29','',NULL,2,113,115,NULL,'asd',2,4),(188,'2024-04-11 16:28:34','',NULL,3,121,NULL,NULL,'',6,NULL),(189,'2024-04-16 18:28:08','',NULL,1,113,NULL,NULL,'',6,NULL),(190,'2024-04-16 18:28:21','../uploads/documentos/solicitudes/yngrid.melo7890@ldcruminahui.com/tarea 1 (5).pdf',NULL,4,113,115,NULL,'',2,4),(192,'2024-05-30 19:41:48','',NULL,3,121,NULL,NULL,'',6,NULL),(193,'2024-06-22 00:18:16','../uploads/documentos/solicitudes/cecilia.santos4816@ldcruminahui.com/Tarea1P2_FormatoSRS_NombreEstudiante.pdf',10,1,121,121,NULL,'simon',1,9),(194,'2025-02-26 15:31:02','',NULL,1,121,121,NULL,'',1,9);
/*!40000 ALTER TABLE `solicitud` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `solicitud_estado`
--

LOCK TABLES `solicitud_estado` WRITE;
/*!40000 ALTER TABLE `solicitud_estado` DISABLE KEYS */;
INSERT INTO `solicitud_estado` VALUES (1,'En tramite'),(2,'Aceptada'),(3,'Preaprobada'),(4,'Rechazada'),(5,'Aprobada'),(6,'Cerrada');
/*!40000 ALTER TABLE `solicitud_estado` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `solicitud_tipo`
--

LOCK TABLES `solicitud_tipo` WRITE;
/*!40000 ALTER TABLE `solicitud_tipo` DISABLE KEYS */;
INSERT INTO `solicitud_tipo` VALUES (1,'Deportiva'),(2,'Administrativa/Alquiler'),(3,'Administrativa/Cultural'),(4,'Otro tipo');
/*!40000 ALTER TABLE `solicitud_tipo` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (112,'Gino','Maximiliano','Bermúdez','Santos','2450118084','0978678674','bginomaximiliano@gmail.com','gino.bermudez8084@ldcruminahui.com','$2y$10$.yN6jNIkoifn3GlnSWYOQ.JtTIcKhbucVVAqH36OficAkLm8d3Mly',8,'1999-05-25'),(113,'Yngrid','Josefina','Melo','Quintana','0987654321','1234567890','melo@gmail.com','yngrid.melo7890@ldcruminahui.com','$2y$10$ATYxTk690aq9jX1v5QYKwuSSSJHntCzKUxYZ8tXZDhUAnH01kzauK',7,'1985-02-15'),(114,'Juan','','Abeldaño','','0987654321','1234567890','example@gmail.com','juan.abeldano4321@ldcruminahui.com','$2y$10$33qoef2kY8bPSB7TudAjNuSFQks86sjpkmgEfgox/YBWWAw5sy0Dy',6,'1980-05-15'),(115,'Emily','Selene','Bermúdez','Santos','2450110114','1234567890','example@gmail.com','emily.bermudez0114@ldcruminahui.com','$2y$10$9kVbpmTiNhtJj1TQM6TZsOXT7TVd3AGpUSAA4u3l46qMkpQpoql7q',4,'2003-09-12'),(116,'Emiliano','Daniel','Bermúdez','Morales','0987654321','1234567890','example@gmail.com','emiliano.bermudez4321@ldcruminahui.com','$2y$10$F9x1niSFJTrAEW1K34esVe388bnII2lWJrtBSBYRs9xNA7pabfV7i',3,'2004-02-14'),(117,'Milena','Yomira','Cedeño','Bermúdez','1234567890','0978678671','example@gmail.com','milena.cedeno7890@ldcruminahui.com','$2y$10$aSaImkru.gJbJ8OsDc75sOzWmTWQCY/yAONnEZXx4foKdHpWcn4TW',1,'2008-08-12'),(118,'Adrian','Emilio','Lopez','Bermúdez','1234567890','0978678674','example@gmail.com','adrian.lopez7890@ldcruminahui.com','$2y$10$jPiomS.BVRr0iX0dy5uAuO9rOpvJOqapmWrdojRMBQBxXDgWWgxmK',2,'2010-05-18'),(119,'Steven','Benito','Bermúdez','Santos','1234567890','1234567890','example@gmail.com','steven.bermudez7890@ldcruminahui.com','$2y$10$8rTZ1MgsSlWgmU1uVKP4HOJr9cfUB6hRI2blRtiRWFr2eqgKZcTWy',5,'1992-04-07'),(120,'Benito','Daniel','Bermúdez','Santos','1234567890','1234567890','example@gmail.com','benito.bermudez7890@ldcruminahui.com','$2y$10$nIH1Vu9nwCNDmqC4tQ.1oO5FAG/ugQ3Z9FtPWgMbCt1KlbdF/hbUa',5,'1980-04-07'),(121,'Cecilia','Paulina','Santos','','0905934816','0985417570','scpaulina1956@gmail.com','cecilia.santos4816@ldcruminahui.com','$2y$10$HUZlTgOkFkfz4rhC95pEdOm70y5hvJohkxXhXR0RXbFLQaLizLI6i',9,'1959-06-06');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-20 12:18:35
