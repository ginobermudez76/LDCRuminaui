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
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `primer_nombre` varchar(50) DEFAULT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) DEFAULT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `cedula` varchar(10) DEFAULT NULL,
  `celular` varchar(10) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `nombre_usuario` varchar(45) DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `rol` int DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_rol` (`rol`),
  CONSTRAINT `fk_rol` FOREIGN KEY (`rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (3,'Gino','Maximiliano','Bermúdez','Santos','2450118084','0978678671','bginomaximiliano@gmail.com','gino.bermudez8084@2024.LDCR','$2y$10$Dn5.BlbKK.6Vy1qA/c/PWOaYLiYNR/WxADtmhegug2UFRofpAMAp6',7,NULL),(4,'Emily','Selene','Bermúdez','Santos','2450120114','0987654321','bemilyselene@hotmail.com','emily.bermudez0114@2024.LDCR','$2y$10$6EfFkFLYyGQfMhgBbxwbYO68qAfQNV64VdahPgAA.bJkY/VJ4IGgO',5,NULL),(5,'Juan','Carlos','Pérez','Gómez','1234567890','0987654321','juan@example.com','juancarlos1','$2y$10$goxguEAn8.a./Mmv1kNvbuydNpXtC3QdlF/UHJtV6kUizAAZmNPlG',1,'1990-05-15'),(6,'Juan','Carlos','Pérez','Gómez','1234567890','0987654321','juan@example.com','juancarlos2','$2y$10$goxguEAn8.a./Mmv1kNvbuydNpXtC3QdlF/UHJtV6kUizAAZmNPlG',2,'1990-05-15'),(7,'Juan','Carlos','Pérez','Gómez','1234567890','0987654321','juan@example.com','juancarlos3','$2y$10$goxguEAn8.a./Mmv1kNvbuydNpXtC3QdlF/UHJtV6kUizAAZmNPlG',3,'1990-05-15'),(8,'Juan','Carlos','Pérez','Gómez','1234567890','0987654321','juan@example.com','juancarlos4','$2y$10$goxguEAn8.a./Mmv1kNvbuydNpXtC3QdlF/UHJtV6kUizAAZmNPlG',4,'1990-05-15'),(9,'Juan','Carlos','Pérez','Gómez','1234567890','0987654321','juan@example.com','juancarlos5','$2y$10$goxguEAn8.a./Mmv1kNvbuydNpXtC3QdlF/UHJtV6kUizAAZmNPlG',6,'1990-05-15');
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

-- Dump completed on 2024-02-22 13:14:22
