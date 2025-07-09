-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gestion_practicas
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
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `rut` varchar(15) DEFAULT NULL,
  `rubro` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `rut` (`rut`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,'MOLYMETNOS S.A.','76845690-1','Industria química','Av. Las Industrias 123, Santiago','22223333','contacto@moly.cl','2025-07-06 00:04:34'),(2,'UNIVERSIDAD ANDRES BELLO','60803000-0','Educación superior','Av. República 239, Santiago','226123456','info@unab.cl','2025-07-06 00:04:34');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entrevistas`
--

DROP TABLE IF EXISTS `entrevistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entrevistas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `hito_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `modalidad` varchar(50) DEFAULT NULL,
  `evidencia_url` text DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `tipo_supervisor` enum('interno','externo') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `estudiante_id` (`estudiante_id`),
  KEY `hito_id` (`hito_id`),
  KEY `supervisor_id` (`supervisor_id`),
  CONSTRAINT `entrevistas_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entrevistas_ibfk_2` FOREIGN KEY (`hito_id`) REFERENCES `hitos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entrevistas_ibfk_3` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entrevistas`
--

LOCK TABLES `entrevistas` WRITE;
/*!40000 ALTER TABLE `entrevistas` DISABLE KEYS */;
INSERT INTO `entrevistas` VALUES (1,1,1,'2025-06-20','presencial','https://uandresbelloedu.sharepoint.com/.../entrevista_hito1.pdf','Primera entrevista completada.',1,'externo');
/*!40000 ALTER TABLE `entrevistas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudiantes`
--

DROP TABLE IF EXISTS `estudiantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rut` varchar(12) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `carrera` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `programa` varchar(20) DEFAULT NULL,
  `asignatura` varchar(50) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `rut` (`rut`),
  KEY `empresa_id` (`empresa_id`),
  CONSTRAINT `estudiantes_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiantes`
--

LOCK TABLES `estudiantes` WRITE;
/*!40000 ALTER TABLE `estudiantes` DISABLE KEYS */;
INSERT INTO `estudiantes` VALUES (1,'20981169-3','ZAPATA GUEVARA, CONSTANZA BELÉN','c.zapataguevara@uandresbello.edu','Ingeniería Civil Industrial','912345678','UNAB12100','PRACTICA II',1,'2025-06-02','2025-08-20','2025-07-06 00:04:34'),(2,'20269725-9','BAEZA PEREIRA, NICOLÁS ANDRÉS','n.baezapereira@uandresbello.edu','Ingeniería Civil Industrial','911223344','UNAB12100','PRACTICA I',2,'2025-03-10','2025-06-02','2025-07-06 00:04:34');
/*!40000 ALTER TABLE `estudiantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluaciones`
--

DROP TABLE IF EXISTS `evaluaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evaluaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `hito_id` int(11) DEFAULT NULL,
  `supervisor` text DEFAULT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `fecha_evaluacion` date DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `estudiante_id` (`estudiante_id`),
  KEY `hito_id` (`hito_id`),
  CONSTRAINT `evaluaciones_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evaluaciones_ibfk_2` FOREIGN KEY (`hito_id`) REFERENCES `hitos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluaciones`
--

LOCK TABLES `evaluaciones` WRITE;
/*!40000 ALTER TABLE `evaluaciones` DISABLE KEYS */;
INSERT INTO `evaluaciones` VALUES (1,2,3,'Oscar Zúñiga',6.50,'Buen cierre de práctica, presentación clara.',NULL,'2025-06-10','2025-07-06 00:36:28'),(2,2,1,'Óscar Zúñiga',5.80,'Buen trabajo.','https://uandresbelloedu.sharepoint.com/:b:/s/PracticasUNAB/EXHwPrSAF8ZBr4hC3m2bhtgBXbj1mYuw4izPvqwo_KtTcA?e=yfrctg','2025-07-06','2025-07-06 18:42:10');
/*!40000 ALTER TABLE `evaluaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hitos`
--

DROP TABLE IF EXISTS `hitos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hitos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hitos`
--

LOCK TABLES `hitos` WRITE;
/*!40000 ALTER TABLE `hitos` DISABLE KEYS */;
INSERT INTO `hitos` VALUES (1,'Hito 1','Entrega inicial del plan de trabajo'),(2,'Hito 2','Avance intermedio de la práctica'),(3,'Evaluación Final','Evaluación del desempeño al finalizar la práctica');
/*!40000 ALTER TABLE `hitos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `informes`
--

DROP TABLE IF EXISTS `informes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `informes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `hito_id` int(11) NOT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `archivo` varchar(200) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `estudiante_id` (`estudiante_id`),
  KEY `hito_id` (`hito_id`),
  CONSTRAINT `informes_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `informes_ibfk_2` FOREIGN KEY (`hito_id`) REFERENCES `hitos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `informes`
--

LOCK TABLES `informes` WRITE;
/*!40000 ALTER TABLE `informes` DISABLE KEYS */;
INSERT INTO `informes` VALUES (2,1,1,'2025-06-15','https://uandresbelloedu.sharepoint.com/:b:/s/PracticasUNAB/EXHwPrSAF8ZBr4hC3m2bhtgBXbj1mYuw4izPvqwo_KtTcA?e=n1t9mF','Entregado puntualmente con plan claro.','2025-07-06 17:49:31');
/*!40000 ALTER TABLE `informes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supervisores`
--

DROP TABLE IF EXISTS `supervisores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supervisores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `tipo` enum('interno','externo') NOT NULL DEFAULT 'externo',
  `empresa_id` int(11) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`),
  CONSTRAINT `supervisores_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supervisores`
--

LOCK TABLES `supervisores` WRITE;
/*!40000 ALTER TABLE `supervisores` DISABLE KEYS */;
INSERT INTO `supervisores` VALUES (1,'Armando Tamponi','Docente UNAB / Supervisor Externo','arm.munoz@uandresbello.edu','+56993997982','externo',2,'2025-07-06 00:04:34');
/*!40000 ALTER TABLE `supervisores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-09 12:34:29
