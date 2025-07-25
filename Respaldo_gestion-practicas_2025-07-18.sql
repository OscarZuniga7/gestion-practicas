-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-07-2025 a las 17:56:51
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestion_practicas`
--
CREATE DATABASE IF NOT EXISTS `gestion_practicas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `gestion_practicas`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `criterios`
--

DROP TABLE IF EXISTS `criterios`;
CREATE TABLE `criterios` (
  `id` int(11) NOT NULL,
  `rubrica_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `puntaje_max` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `criterios`
--

INSERT INTO `criterios` (`id`, `rubrica_id`, `nombre`, `descripcion`, `orden`, `puntaje_max`) VALUES
(1, 1, 'Introducción', 'Explica cómo se relaciona la práctica con la formación profesional y describe el contexto de la empresa', 1, 6),
(2, 1, 'Antecedentes de la organización y unidad de trabajo', 'Incluye historia, misión, visión, estructura, productos, clientes, y unidad de trabajo', 2, 6),
(3, 1, 'Actividades desarrolladas', 'Describe actividades, resultados, indicadores y herramientas utilizadas', 3, 12),
(4, 1, 'Oportunidades de mejora', 'Identifica y describe oportunidades de mejora, contexto, consecuencias y causas', 4, 12),
(5, 1, 'Bitácora', 'Incluye resumen semanal detallado de actividades y modalidad', 5, 12),
(6, 1, 'Anexo', 'Incluye evidencias claras, explicaciones y objetivos específicos', 6, 6),
(7, 1, 'Aspectos formales', 'Uso de formato, redacción, ortografía y límites de caracteres', 7, 6),
(8, 6, 'Descripción de la Empresa', 'Describe y analiza la empresa donde realiza su práctica profesional presentando su estructura societaria, organigrama, principales productos o servicios que ofrece, principales proveedores y clientes', 1, 60),
(9, 6, 'Identificación de las Actividades', 'Identifica, describe y evidencia los procesos/tareas realizadas en la práctica y su relación con el plan de acción', 2, 30),
(10, 6, 'Ortografía y Gramática', 'Elabora informe de práctica profesional con correcto uso de las habilidades comunicacionales', 3, 10),
(11, 7, 'Descripción de la Empresa', 'Describe y analiza la empresa donde realiza su práctica profesional presentando su estructura societaria, organigrama, principales productos o servicios que ofrece, principales proveedores y clientes', 1, 30),
(12, 7, 'Identificación de las Actividades', 'Identifica, describe y evidencia los procesos/tareas realizadas en la práctica y su relación con el plan de acción', 2, 30),
(13, 7, 'Descripción de las Actividades', 'Descripción detallada de las actividades desarrolladas, análisis crítico y evidencia de impacto o utilidad', 3, 20),
(14, 7, 'Recomendaciones', 'Recomendaciones al jefe de carrera y al supervisor directo; incluye conclusiones finales de la práctica', 4, 10),
(15, 7, 'Ortografía y Gramática', 'Elabora informe de práctica profesional con correcto uso de las habilidades comunicacionales', 5, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `criterios_niveles`
--

DROP TABLE IF EXISTS `criterios_niveles`;
CREATE TABLE `criterios_niveles` (
  `id` int(11) NOT NULL,
  `criterio_id` int(11) NOT NULL,
  `nivel_logro_id` int(11) NOT NULL,
  `puntaje` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `criterios_niveles`
--

INSERT INTO `criterios_niveles` (`id`, `criterio_id`, `nivel_logro_id`, `puntaje`) VALUES
(1, 1, 1, 6),
(2, 1, 2, 4),
(3, 1, 3, 2),
(4, 1, 4, 0),
(5, 2, 1, 6),
(6, 2, 2, 4),
(7, 2, 3, 2),
(8, 2, 4, 0),
(9, 3, 1, 12),
(10, 3, 2, 8),
(11, 3, 3, 4),
(12, 3, 4, 0),
(13, 4, 1, 12),
(14, 4, 2, 8),
(15, 4, 3, 4),
(16, 4, 4, 0),
(17, 5, 1, 12),
(18, 5, 2, 8),
(19, 5, 3, 4),
(20, 5, 4, 0),
(21, 6, 1, 6),
(22, 6, 2, 4),
(23, 6, 3, 2),
(24, 6, 4, 0),
(25, 7, 1, 6),
(26, 7, 2, 4),
(27, 7, 3, 2),
(28, 7, 4, 0),
(34, 8, 11, 100),
(35, 8, 12, 75),
(36, 8, 13, 60),
(37, 8, 14, 25),
(38, 8, 15, 0),
(39, 8, 16, 0),
(40, 9, 11, 100),
(41, 9, 12, 75),
(42, 9, 13, 60),
(43, 9, 14, 25),
(44, 9, 15, 0),
(45, 9, 16, 0),
(46, 10, 11, 100),
(47, 10, 12, 75),
(48, 10, 13, 60),
(49, 10, 14, 25),
(50, 10, 15, 0),
(51, 10, 16, 0),
(52, 11, 11, 100),
(53, 11, 12, 75),
(54, 11, 13, 60),
(55, 11, 14, 25),
(56, 11, 15, 0),
(57, 11, 16, 0),
(58, 12, 11, 100),
(59, 12, 12, 75),
(60, 12, 13, 60),
(61, 12, 14, 25),
(62, 12, 15, 0),
(63, 12, 16, 0),
(64, 13, 11, 100),
(65, 13, 12, 75),
(66, 13, 13, 60),
(67, 13, 14, 25),
(68, 13, 15, 0),
(69, 13, 16, 0),
(70, 14, 11, 100),
(71, 14, 12, 75),
(72, 14, 13, 60),
(73, 14, 14, 25),
(74, 14, 15, 0),
(75, 14, 16, 0),
(76, 15, 11, 100),
(77, 15, 12, 75),
(78, 15, 13, 60),
(79, 15, 14, 25),
(80, 15, 15, 0),
(81, 15, 16, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

DROP TABLE IF EXISTS `empresas`;
CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rut` varchar(15) DEFAULT NULL,
  `rubro` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `nombre`, `rut`, `rubro`, `direccion`, `telefono`, `email_contacto`, `fecha_registro`) VALUES
(1, 'MOLYMETNOS S.A.', '76845690-1', 'Industria química', 'Av. Las Industrias 123, Santiago', '22223333', 'contacto@moly.cl', '2025-07-06 00:04:34'),
(2, 'UNIVERSIDAD ANDRES BELLO', '60803000-0', 'Educación superior', 'Av. República 239, Santiago', '226123456', 'info@unab.cl', '2025-07-06 00:04:34'),
(3, 'PYMES SPA', '', '', '', '', '', '2025-07-09 17:00:56'),
(7, 'BANCO BCI', NULL, '', '', '', '', '2025-07-09 18:15:50'),
(8, 'TRITEC CENTER SPA', NULL, '', '', '', '', '2025-07-09 23:30:35'),
(9, 'CELULA NET SPA', NULL, '', '', '', '', '2025-07-12 18:07:29'),
(10, 'MAESTRANZA DIESEL S.A.', NULL, '', '', '', '', '2025-07-12 18:36:56'),
(11, 'PATRICIO LIOI Y CIA SPA.', NULL, '', '', '', '', '2025-07-18 15:00:28'),
(12, 'ORISK SOLUCIONES SPA', NULL, '', '', '', '', '2025-07-18 15:07:26'),
(13, 'LABORATORIOS GARDEN HOUSE FARMACEUTICA S.A.', NULL, '', '', '', '', '2025-07-18 15:10:49'),
(14, 'WALMART CHILE S.A.', NULL, '', '', '', '', '2025-07-18 15:14:02'),
(15, 'INDRA SISTEMAS CHILE S.A.', NULL, '', '', '', '', '2025-07-18 15:16:56'),
(16, 'BRAVIUM CHILE SPA', NULL, '', '', '', '', '2025-07-18 15:19:49'),
(17, 'ALTRAD RMD KWIKFORM', NULL, '', '', '', '', '2025-07-18 15:22:51'),
(18, 'FULL FIERROS  MAQUINARIAS SPA', NULL, '', '', '', '', '2025-07-18 15:25:45'),
(19, 'ARIDOS TRANSPORTES MARIBEL YUCE PEREZ PEREZ', NULL, '', '', '', '', '2025-07-18 15:28:43'),
(20, 'DOLPHIN MEDICAL SPA', NULL, '', '', '', '', '2025-07-18 15:31:19'),
(21, 'SOCIEDAD FARMACEUTICA TMF SPA', NULL, '', '', '', '', '2025-07-18 15:37:19'),
(22, 'COMERCIAL EL BOSQUE SPA', NULL, '', '', '', '', '2025-07-18 15:40:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrevistas`
--

DROP TABLE IF EXISTS `entrevistas`;
CREATE TABLE `entrevistas` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `hito_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `modalidad` varchar(50) DEFAULT NULL,
  `evidencia_url` text DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `tipo_supervisor` enum('interno','externo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

DROP TABLE IF EXISTS `estudiantes`;
CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL,
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
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id`, `rut`, `nombre`, `email`, `carrera`, `telefono`, `programa`, `asignatura`, `empresa_id`, `fecha_inicio`, `fecha_fin`, `fecha_registro`) VALUES
(3, '21444322-8', 'ACHUI BURISCH, EDUARDO ABRAHAM', 'e.achuiburisch@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA I', 3, '2025-05-08', '2025-07-24', '2025-07-09 17:04:41'),
(4, '21784324-3', 'ALEGRÍA RUIZ, SEBASTIÁN ALBERTO', 's.alegraruiz@uandresbello.edu', '', '', 'UNAB22100', 'PRACTICA I', 7, '2025-05-15', '2025-07-15', '2025-07-09 18:05:48'),
(5, '21120568-7', 'SEGURA ALTAMIRANO, CRISTOPHER ADAM', 'c.seguraaltamirano@uandresbello.edu', 'Ingeniería Civil Industrial', '', 'UNAB12100', 'PRACTICA I', 8, '2025-06-02', '2025-08-15', '2025-07-09 23:34:01'),
(6, '21551345-9', 'BUSTAMANTE CONCHA, SEBASTIÁN', 's.bustamanteconcha@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA I', 9, '2025-06-16', '2025-08-20', '2025-07-12 18:10:15'),
(7, '20829014-2', 'TRUJILLO POZO, MATÍAS', 'm.trujillopozo@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA II', 10, '2025-04-14', '2025-06-19', '2025-07-12 18:39:32'),
(8, '20999324-4', 'ARAYA MUÑOZ, JAVIER IGNACIO', 'j.arayamuoz3@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA I', 11, '2025-06-17', '2025-08-01', '2025-07-18 15:03:16'),
(9, '20269725-9', 'BAEZA PEREIRA, NICOLÁS ANDRÉS', 'n.baezapereira@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA I', 2, '2025-03-10', '2025-06-02', '2025-07-18 15:06:21'),
(10, '20807668-K', 'CASTILLO MOYANO, JOSÉ', 'j.castillomoyano@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA I', 12, '2025-04-16', '2025-07-31', '2025-07-18 15:09:36'),
(11, '20403949-6', 'CORREA MORENO, JAVIERA IGNACIA', 'j.correamoreno@uandresbello.edu', '', '', 'UNAB22100', 'PRACTICA I', 13, '2025-06-09', '2025-07-17', '2025-07-18 15:12:52'),
(12, '20955618-9', 'CORTEZ FERNÁNDEZ, DIEGO CARLOS', 'd.cortezfernndez@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA II', 14, '2025-05-05', '2025-08-20', '2025-07-18 15:15:55'),
(13, '20552129-1', 'HENRÍQUEZ FIGUEROA, ANTONELLA VALENTINA', 'a.henrquezfigueroa@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA II', 15, '2025-04-21', '2025-07-18', '2025-07-18 15:19:02'),
(14, '19389787-8', 'LOPEZ MORALES, EDGARDO', 'e.lopezmorales@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA I', 16, '2025-04-08', '2025-05-14', '2025-07-18 15:22:03'),
(15, '23096033-K', 'NOVA BARDESIO, LUCIA', 'l.novabardesio@uandresbello.edu', '', '', 'UNAB22100', 'PRACTICA II', 17, '2025-05-12', '2025-07-08', '2025-07-18 15:24:53'),
(16, '17338923-K', 'OLGUÍN ÁLVAREZ, MARCELO ARIEL', 'm.olguinalvarez@uandresbello.edu', '', '', 'UNAB22100', 'PRACTICA II', 18, '2025-05-12', '2025-07-09', '2025-07-18 15:27:33'),
(17, '18082770-6', 'PAILLALEF PEREZ, ALAN', 'a.paillalefperez@uandresbello.edu', '', '', 'UNAB22100', 'PRACTICA I', 19, '2025-06-01', '2025-07-30', '2025-07-18 15:30:33'),
(18, '19803029-5', 'PASTÉN ARGANDOÑA, VALENTINA FERNANDA', 'v.pastnargandoa@uandresbello.edu', '', '', 'UNAB22100', 'PRACTICA I', 20, '2025-06-04', '2025-07-11', '2025-07-18 15:33:25'),
(19, '21469148-5', 'QUINTEROS VILLAR, FELIPE', 'f.quinterosvillar@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA I', 2, '2025-03-18', '2025-06-03', '2025-07-18 15:36:36'),
(20, '21474595-K', 'ROBLES MÉNDEZ, FRANCO', 'f.roblesmndez@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA I', 21, '2025-04-20', '2025-06-20', '2025-07-18 15:39:16'),
(21, '20591809-4', 'SALAZAR TAPIA, GONZALO ANDRÉS', 'g.salazartapia@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA I', 22, '2025-05-12', '2025-06-20', '2025-07-18 15:42:06'),
(22, '20981169-3', 'ZAPATA GUEVARA, CONSTANZA BELÉN', 'c.zapataguevara@uandresbello.edu', '', '', 'UNAB12100', 'PRACTICA II', 1, '2025-06-02', '2025-08-20', '2025-07-18 15:46:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones`
--

DROP TABLE IF EXISTS `evaluaciones`;
CREATE TABLE `evaluaciones` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `hito_id` int(11) DEFAULT NULL,
  `supervisor` text DEFAULT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `fecha_evaluacion` date DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evaluaciones`
--

INSERT INTO `evaluaciones` (`id`, `estudiante_id`, `hito_id`, `supervisor`, `nota`, `observaciones`, `archivo`, `fecha_evaluacion`, `fecha_registro`) VALUES
(3, 5, 1, 'ÓSCAR EDUARDO ZUÑIGA LARA', 54.00, 'Informe bien estructurado, con análisis claro de tareas y oportunidades. La bitácora podría tener más detalle semanal. Aspectos formales como ortografía y formato pueden mejorarse levemente. Se valora la proactividad del estudiante y la pertinencia de las mejoras propuestas.', NULL, '2025-07-12', '2025-07-12 17:56:16'),
(4, 6, 1, 'ÓSCAR EDUARDO ZUÑIGA LARA', 44.00, 'Buen informe, redactado con claridad y orden. Se valora el esfuerzo en identificar una mejora real para la empresa. Las actividades fueron descritas adecuadamente desde una perspectiva formativa. Se recomienda profundizar en la argumentación técnica y fortalecer la evidencia documental en futuros entregables.', NULL, '2025-07-12', '2025-07-12 18:22:05'),
(5, 7, 1, 'ÓSCAR EDUARDO ZUÑIGA LARA', 99.99, 'Desempeño sobresaliente en todos los criterios evaluados:\r\nDescripción de la Empresa: Excelente desarrollo de todos los elementos requeridos. El informe presenta una visión clara y estructurada de la empresa Maestranza Diesel S.A., incluyendo su misión, visión, estructura organizacional y rol estratégico del área donde se desempeñó el estudiante.\r\nIdentificación de las Actividades: Precisión y detalle en las tareas realizadas. Se describe con claridad el proceso de análisis de bases de licitación, coordinación interna y elaboración de propuestas, evidenciando participación activa y comprensión del contexto operativo.\r\nOrtografía y Gramática: Redacción clara, sin errores ortográficos ni gramaticales. Se utilizó adecuadamente el formato formal de informe, con una presentación limpia y profesional.\r\n\r\nFelicitaciones por el trabajo entregado. Se refleja un alto nivel de compromiso y competencia profesional.', NULL, '2025-07-13', '2025-07-12 23:12:39'),
(6, 7, 2, 'ÓSCAR EDUARDO ZUÑIGA LARA', 99.99, 'El estudiante demuestra un excelente nivel de madurez profesional, análisis crítico y aplicación de conocimientos técnicos. El informe refleja un alto compromiso, autonomía y un importante valor agregado a la organización.', NULL, '2025-07-13', '2025-07-13 01:09:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones_criterios`
--

DROP TABLE IF EXISTS `evaluaciones_criterios`;
CREATE TABLE `evaluaciones_criterios` (
  `id` int(11) NOT NULL,
  `evaluacion_id` int(11) NOT NULL,
  `criterio_id` int(11) NOT NULL,
  `nivel_logro_id` int(11) NOT NULL,
  `puntaje_obtenido` int(11) NOT NULL,
  `comentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evaluaciones_criterios`
--

INSERT INTO `evaluaciones_criterios` (`id`, `evaluacion_id`, `criterio_id`, `nivel_logro_id`, `puntaje_obtenido`, `comentario`) VALUES
(1, 3, 1, 1, 6, NULL),
(2, 3, 2, 1, 6, NULL),
(3, 3, 3, 1, 12, NULL),
(4, 3, 4, 1, 12, NULL),
(5, 3, 5, 2, 8, NULL),
(6, 3, 6, 1, 6, NULL),
(7, 3, 7, 2, 4, NULL),
(8, 4, 1, 1, 6, NULL),
(9, 4, 2, 1, 6, NULL),
(10, 4, 3, 2, 8, NULL),
(11, 4, 4, 2, 8, NULL),
(12, 4, 5, 2, 8, NULL),
(13, 4, 6, 2, 4, NULL),
(14, 4, 7, 2, 4, NULL),
(15, 5, 8, 11, 100, NULL),
(16, 5, 9, 11, 100, NULL),
(17, 5, 10, 11, 100, NULL),
(18, 6, 11, 11, 100, NULL),
(19, 6, 12, 11, 100, NULL),
(20, 6, 13, 11, 100, NULL),
(21, 6, 14, 11, 100, NULL),
(22, 6, 15, 11, 100, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hitos`
--

DROP TABLE IF EXISTS `hitos`;
CREATE TABLE `hitos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `hitos`
--

INSERT INTO `hitos` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Hito 1', 'Entrega inicial del plan de trabajo'),
(2, 'Hito 2', 'Avance intermedio de la práctica'),
(3, 'Evaluación Final', 'Evaluación del desempeño al finalizar la práctica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informes`
--

DROP TABLE IF EXISTS `informes`;
CREATE TABLE `informes` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `hito_id` int(11) NOT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `archivo` varchar(200) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `informes`
--

INSERT INTO `informes` (`id`, `estudiante_id`, `hito_id`, `fecha_entrega`, `archivo`, `comentarios`, `fecha_registro`) VALUES
(4, 5, 1, '2025-07-09', 'https://uandresbelloedu.sharepoint.com/:b:/s/PracticasUNAB/EWKWxBEP8OROux-EIKTi4egBhOQkvt23jCcQF9aXtBxchA?e=10ZQWc', 'Informe recibido con firma del supervisor. Se acepta la entrega fuera de plazo por razones justificadas. Pendiente revisión y retroalimentación.', '2025-07-11 21:32:16'),
(5, 6, 1, '2025-07-07', 'https://uandresbelloedu.sharepoint.com/:b:/s/PracticasUNAB/EVnm9KCmCCBBoSKokShKv3UBAoYvN4VMZh5mOCaWSOyTcg?e=ErZSwV', '', '2025-07-12 18:17:03'),
(6, 7, 1, '2025-06-03', 'https://uandresbelloedu.sharepoint.com/:b:/s/PracticasUNAB/EcFDOgz4cB5Ar3D8PtnEw6EB45yfcLNwQU82y5NJJviilQ?e=08RzV5', '', '2025-07-12 18:52:29'),
(7, 7, 2, '2025-07-10', 'https://uandresbelloedu.sharepoint.com/:b:/s/PracticasUNAB/EW_McQDkpvdKreXIkLi1znEBcSFvdMPwdLsFueYfp3Swbg?e=0dMqT4', '', '2025-07-13 01:01:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles_logro`
--

DROP TABLE IF EXISTS `niveles_logro`;
CREATE TABLE `niveles_logro` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `niveles_logro`
--

INSERT INTO `niveles_logro` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Excelente', 'Cumple con todos los elementos requeridos'),
(2, 'Bueno', 'Cumple con al menos el 60% de los elementos requeridos'),
(3, 'Deficiente', 'Cumple con menos del 60% de los elementos requeridos'),
(4, 'Insuficiente', 'No cumple o no entrega evidencia'),
(11, 'Excelente', 'Cumple con todos los elementos requeridos'),
(12, 'Bueno', 'Cumple con al menos el 75% de los elementos requeridos'),
(13, 'Aceptable', 'Cumple con al menos el 60% de los elementos requeridos'),
(14, 'Insuficiente', 'Cumple con al menos el 25% de los elementos requeridos'),
(15, 'No cumple', 'No presenta / No cumple con los elementos requeridos'),
(16, 'No Aplica', 'No corresponde evaluar el atributo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rubricas`
--

DROP TABLE IF EXISTS `rubricas`;
CREATE TABLE `rubricas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `hito_id` int(11) DEFAULT NULL,
  `tipo_practica` enum('interno','externo','común') DEFAULT 'común'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rubricas`
--

INSERT INTO `rubricas` (`id`, `nombre`, `descripcion`, `hito_id`, `tipo_practica`) VALUES
(1, 'Práctica I - Hito I', 'Evaluación del Informe del Hito I de la Práctica I', 1, 'común'),
(2, 'Práctica I - Hito II - Interno', 'Evaluación Interna del Hito II de la Práctica I', 2, 'interno'),
(3, 'Práctica I - Hito II - Externo', 'Evaluación Externa del Hito II de la Práctica I', 2, 'externo'),
(4, 'Práctica II - Evaluación Interna', 'Evaluación Interna del desempeño en Práctica II', 3, 'interno'),
(5, 'Práctica II - Evaluación Externa', 'Evaluación Externa del desempeño en Práctica II', 3, 'externo'),
(6, 'Práctica II - Hito I - Interno', 'Evaluación Interna del Informe del Hito I de la Práctica II', 1, 'interno'),
(7, 'Práctica II - Hito II - Interno', 'Evaluación Interna del Informe del Hito II de la Práctica II', 2, 'interno');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervisores`
--

DROP TABLE IF EXISTS `supervisores`;
CREATE TABLE `supervisores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `tipo` enum('interno','externo') NOT NULL DEFAULT 'externo',
  `empresa_id` int(11) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `supervisores`
--

INSERT INTO `supervisores` (`id`, `nombre`, `cargo`, `email`, `telefono`, `tipo`, `empresa_id`, `fecha_registro`) VALUES
(2, 'Armando Tamponi', 'Docente UNAB / Supervisor Externo', 'arm.munoz@uandresbello.edu', '+56993997982', 'externo', 2, '2025-07-10 17:43:01'),
(3, 'ÓSCAR EDUARDO ZUÑIGA LARA', 'Docente UNAB / Supervisor Interno', 'o.zunigalara@uandresbello.edu', '+569 44212755', 'interno', NULL, '2025-07-10 17:46:53');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `criterios`
--
ALTER TABLE `criterios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rubrica_id` (`rubrica_id`);

--
-- Indices de la tabla `criterios_niveles`
--
ALTER TABLE `criterios_niveles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `criterio_id` (`criterio_id`),
  ADD KEY `nivel_logro_id` (`nivel_logro_id`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rut` (`rut`);

--
-- Indices de la tabla `entrevistas`
--
ALTER TABLE `entrevistas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `hito_id` (`hito_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rut` (`rut`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- Indices de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `hito_id` (`hito_id`);

--
-- Indices de la tabla `evaluaciones_criterios`
--
ALTER TABLE `evaluaciones_criterios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluacion_id` (`evaluacion_id`),
  ADD KEY `criterio_id` (`criterio_id`),
  ADD KEY `nivel_logro_id` (`nivel_logro_id`);

--
-- Indices de la tabla `hitos`
--
ALTER TABLE `hitos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `informes`
--
ALTER TABLE `informes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `hito_id` (`hito_id`);

--
-- Indices de la tabla `niveles_logro`
--
ALTER TABLE `niveles_logro`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rubricas`
--
ALTER TABLE `rubricas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `supervisores`
--
ALTER TABLE `supervisores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `criterios`
--
ALTER TABLE `criterios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `criterios_niveles`
--
ALTER TABLE `criterios_niveles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `entrevistas`
--
ALTER TABLE `entrevistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `evaluaciones_criterios`
--
ALTER TABLE `evaluaciones_criterios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `hitos`
--
ALTER TABLE `hitos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `informes`
--
ALTER TABLE `informes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `niveles_logro`
--
ALTER TABLE `niveles_logro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `rubricas`
--
ALTER TABLE `rubricas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `supervisores`
--
ALTER TABLE `supervisores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `criterios`
--
ALTER TABLE `criterios`
  ADD CONSTRAINT `criterios_ibfk_1` FOREIGN KEY (`rubrica_id`) REFERENCES `rubricas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `criterios_niveles`
--
ALTER TABLE `criterios_niveles`
  ADD CONSTRAINT `criterios_niveles_ibfk_1` FOREIGN KEY (`criterio_id`) REFERENCES `criterios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `criterios_niveles_ibfk_2` FOREIGN KEY (`nivel_logro_id`) REFERENCES `niveles_logro` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `entrevistas`
--
ALTER TABLE `entrevistas`
  ADD CONSTRAINT `entrevistas_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `entrevistas_ibfk_2` FOREIGN KEY (`hito_id`) REFERENCES `hitos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `entrevistas_ibfk_3` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisores` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `estudiantes_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD CONSTRAINT `evaluaciones_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluaciones_ibfk_2` FOREIGN KEY (`hito_id`) REFERENCES `hitos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `evaluaciones_criterios`
--
ALTER TABLE `evaluaciones_criterios`
  ADD CONSTRAINT `evaluaciones_criterios_ibfk_1` FOREIGN KEY (`evaluacion_id`) REFERENCES `evaluaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluaciones_criterios_ibfk_2` FOREIGN KEY (`criterio_id`) REFERENCES `criterios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluaciones_criterios_ibfk_3` FOREIGN KEY (`nivel_logro_id`) REFERENCES `niveles_logro` (`id`);

--
-- Filtros para la tabla `informes`
--
ALTER TABLE `informes`
  ADD CONSTRAINT `informes_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `informes_ibfk_2` FOREIGN KEY (`hito_id`) REFERENCES `hitos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `supervisores`
--
ALTER TABLE `supervisores`
  ADD CONSTRAINT `supervisores_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
