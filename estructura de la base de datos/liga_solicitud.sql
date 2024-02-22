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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud`
--

LOCK TABLES `solicitud` WRITE;
/*!40000 ALTER TABLE `solicitud` DISABLE KEYS */;
INSERT INTO `solicitud` VALUES (1,NULL,NULL,108,1,3,NULL,NULL,'fgdfg',NULL,NULL),(2,NULL,NULL,126.13,1,3,NULL,NULL,'sASa',NULL,NULL),(3,NULL,NULL,8.06,2,3,NULL,NULL,'sdfsd',NULL,NULL),(4,NULL,'../uploads/documentos/Tutorial en Formato PDF.pdf',126,2,3,NULL,NULL,'asd',NULL,NULL),(5,'2024-02-21 07:14:12','../uploads/documentos/Dialnet-CiberseguridadEnPlataformasEducativasInstitucional-8091394.pdf',50,4,3,NULL,NULL,'sfsdf',NULL,NULL),(10,'2024-02-21 14:46:16','',126,2,3,NULL,NULL,'hnhb',NULL,4),(13,'2024-02-21 16:40:20','../uploads/documentos/TripleTen_Workbook.pdf.pdf',126,2,3,8,NULL,'sdfsdf',1,4),(14,'2024-02-21 16:44:21','../uploads/documentos/TAREA_1_ASINCRONICA_U3_BERMUDEZ_GINO.pdf',126.45,3,3,8,NULL,'hjgjhjhg',1,4);
/*!40000 ALTER TABLE `solicitud` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-02-22 13:14:21
