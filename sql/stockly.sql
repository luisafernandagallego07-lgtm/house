CREATE DATABASE IF NOT EXISTS stockly CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE stockly;

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: stockly
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping routines for database 'stockly'
--

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','editor') NOT NULL DEFAULT 'editor',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `intentos_fallidos` int(11) NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','admin@stockly.local','$2y$10$SuaPsLpb2DB8766KjmRMeu3W0tFAJOmKxKo1ILRQdYceGJeUSYBFC','admin',1,0,NULL,'2026-11-23 13:41:50','2026-11-17 19:16:49');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'General',NULL,'2026-11-17 19:16:49'),(2,'Bebidas',NULL,'2026-11-17 19:16:49'),(3,'Limpieza',NULL,'2026-11-17 19:16:49'),(6,'Papitas','Crocanticas.','2026-11-17 19:59:45'),(7,'Lacteos','Lechita.','2026-11-17 20:10:46');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,'colanta','','3148573757','colanta@cola.com','Calle 49 # 28 - 70, el campin - persia','2026-11-17 20:10:26'),(2,'juguito hit','hit','3521795561','juguito@hit.com','calle 17#28-16','2026-11-17 20:14:52');
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `precio_compra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_venta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_actual` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 5,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `categoria_id` (`categoria_id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (3,'110822-P','Papitas de lim├│n margaritas','Crocantes con un sabor a lim├│n delicioso.',6,NULL,2200.00,3500.00,55,15,1,'2026-11-17 20:04:55'),(4,'8008-L','Lechita blanquita blanquita','Lechita rica.',7,1,3800.00,4800.00,60,10,1,'2026-11-17 20:12:11'),(5,'23071820-H','Juguito hit','hiiiiit.',2,2,2800.00,4000.00,35,12,1,'2026-11-17 20:15:56');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos`
--

DROP TABLE IF EXISTS `movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos`
--

LOCK TABLES `movimientos` WRITE;
/*!40000 ALTER TABLE `movimientos` DISABLE KEYS */;
INSERT INTO `movimientos` VALUES (1,3,1,'salida',35,'','2026-11-17 20:05:21'),(2,3,1,'entrada',50,'','2026-11-17 20:12:42');
/*!40000 ALTER TABLE `movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_errores`
--

DROP TABLE IF EXISTS `log_errores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_errores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mensaje` text NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_errores`
--

LOCK TABLES `log_errores` WRITE;
/*!40000 ALTER TABLE `log_errores` DISABLE KEYS */;
INSERT INTO `log_errores` VALUES (1,'SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`stockly`.`productos`, CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL)',1,'2026-11-17 20:01:43'),(2,'SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`stockly`.`productos`, CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL)',1,'2026-11-17 20:01:52');
/*!40000 ALTER TABLE `log_errores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_uso_modulos`
--

DROP TABLE IF EXISTS `log_uso_modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_uso_modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_uso_modulos`
--

LOCK TABLES `log_uso_modulos` WRITE;
/*!40000 ALTER TABLE `log_uso_modulos` DISABLE KEYS */;
INSERT INTO `log_uso_modulos` VALUES (1,1,'dashboard','2026-11-17 19:53:29'),(2,1,'dashboard','2026-11-17 19:55:18'),(3,1,'proveedores','2026-11-17 19:55:22'),(4,1,'categorias','2026-11-17 19:55:24'),(5,1,'usuarios','2026-11-17 19:55:28'),(6,1,'productos','2026-11-17 19:55:33'),(7,1,'categorias','2026-11-17 19:55:42'),(8,1,'categorias','2026-11-17 19:56:14'),(9,1,'categorias','2026-11-17 19:59:08'),(10,1,'categorias','2026-11-17 19:59:14'),(11,1,'categorias','2026-11-17 19:59:14'),(12,1,'categorias','2026-11-17 19:59:45'),(13,1,'categorias','2026-11-17 19:59:45'),(14,1,'productos','2026-11-17 19:59:48'),(15,1,'productos','2026-11-17 20:03:34'),(16,1,'productos','2026-11-17 20:03:37'),(17,1,'movimientos','2026-11-17 20:05:03'),(18,1,'movimientos','2026-11-17 20:05:21'),(19,1,'movimientos','2026-11-17 20:05:21'),(20,1,'productos','2026-11-17 20:05:24'),(21,1,'productos','2026-11-17 20:09:12'),(22,1,'categorias','2026-11-17 20:09:47'),(23,1,'proveedores','2026-11-17 20:09:49'),(24,1,'proveedores','2026-11-17 20:10:26'),(25,1,'proveedores','2026-11-17 20:10:26'),(26,1,'categorias','2026-11-17 20:10:29'),(27,1,'categorias','2026-11-17 20:10:46'),(28,1,'categorias','2026-11-17 20:10:46'),(29,1,'productos','2026-11-17 20:10:50'),(30,1,'categorias','2026-11-17 20:12:26'),(31,1,'movimientos','2026-11-17 20:12:28'),(32,1,'movimientos','2026-11-17 20:12:41'),(33,1,'movimientos','2026-11-17 20:12:42'),(34,1,'productos','2026-11-17 20:12:45'),(35,1,'reportes','2026-11-17 20:12:48'),(36,1,'usuarios','2026-11-17 20:12:53'),(37,1,'usuarios','2026-11-17 20:13:52'),(38,1,'reportes','2026-11-17 20:13:58'),(39,1,'movimientos','2026-11-17 20:14:07'),(40,1,'proveedores','2026-11-17 20:14:15'),(41,1,'categorias','2026-11-17 20:14:18'),(42,1,'proveedores','2026-11-17 20:14:20'),(43,1,'proveedores','2026-11-17 20:14:52'),(44,1,'proveedores','2026-11-17 20:14:52'),(45,1,'productos','2026-11-17 20:14:54'),(46,1,'categorias','2026-11-17 20:16:04'),(47,1,'productos','2026-11-17 20:16:08'),(48,1,'movimientos','2026-11-17 20:16:17'),(49,1,'movimientos','2026-11-17 20:27:24'),(50,1,'movimientos','2026-11-17 20:27:25'),(51,1,'movimientos','2026-11-17 20:27:26'),(52,1,'dashboard','2026-11-17 20:27:43'),(53,1,'dashboard','2026-11-17 20:31:15'),(54,1,'dashboard','2026-11-17 20:31:18'),(55,1,'dashboard','2026-11-17 20:32:38'),(56,1,'dashboard','2026-11-17 20:32:41'),(57,1,'dashboard','2026-11-17 20:36:00'),(58,1,'productos','2026-11-23 18:34:54'),(59,1,'productos','2026-11-23 18:35:58'),(60,1,'dashboard','2026-11-23 18:40:09'),(61,1,'productos','2026-11-23 18:40:10'),(62,1,'dashboard','2026-11-23 18:41:50'),(63,1,'productos','2026-11-23 18:41:51');
/*!40000 ALTER TABLE `log_uso_modulos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-23 13:42:26
