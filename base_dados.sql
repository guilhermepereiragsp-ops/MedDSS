-- --------------------------------------------------------
-- Anfitrião:                    127.0.0.1
-- Versão do servidor:           8.4.3 - MySQL Community Server - GPL
-- SO do servidor:               Win64
-- HeidiSQL Versão:              12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- A despejar estrutura da base de dados para dss_hospitalar
CREATE DATABASE IF NOT EXISTS `dss_hospitalar` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `dss_hospitalar`;

-- A despejar estrutura para tabela dss_hospitalar.equipamento
CREATE TABLE IF NOT EXISTS `equipamento` (
  `id_equip` int NOT NULL AUTO_INCREMENT,
  `id_esp` int NOT NULL,
  `id_hosp` int DEFAULT NULL,
  `tipo` varchar(100) NOT NULL,
  `fabricante` varchar(100) NOT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `data_install` date DEFAULT NULL,
  `custo_aq` decimal(14,2) DEFAULT NULL,
  `custo_man` decimal(14,2) DEFAULT NULL,
  `prazo_ent` int DEFAULT NULL,
  `t_exame` int DEFAULT NULL,
  `t_vida_util` int DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  KEY `id_esp` (`id_esp`),
  KEY `id_hosp` (`id_hosp`),
  CONSTRAINT `equipamento_ibfk_1` FOREIGN KEY (`id_esp`) REFERENCES `especialidades` (`id_esp`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `equipamento_ibfk_2` FOREIGN KEY (`id_hosp`) REFERENCES `hospitais` (`id_hosp`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.equipamento: ~96 rows (aproximadamente)
INSERT INTO `equipamento` (`id_equip`, `id_esp`, `id_hosp`, `tipo`, `fabricante`, `modelo`, `referencia`, `data_install`, `custo_aq`, `custo_man`, `prazo_ent`, `t_exame`, `t_vida_util`) VALUES
	(1, 2, NULL, 'TAC', 'Siemens Healthcare', 'TAC 128 cortes', NULL, NULL, 399700.00, 28000.00, 120, 15, 12),
	(2, 2, NULL, 'TAC', 'Philips', 'Incisive CT', NULL, NULL, 450000.00, 32000.00, 120, 14, 12),
	(3, 2, NULL, 'RX', 'Philips', 'DigitalDiagnost C90', NULL, NULL, 150000.00, 9000.00, 90, 8, 10),
	(4, 2, NULL, 'RX Portátil Digital', 'FUJIFILM', 'FDR nano', NULL, NULL, 71895.00, 6500.00, 60, 10, 8),
	(5, 2, NULL, 'RX Arco em C', 'Siemens Healthcare', 'Cios Select', NULL, NULL, 219999.00, 14000.00, 100, 12, 10),
	(6, 2, NULL, 'Angiógrafo', 'Philips', 'Azurion 7', NULL, NULL, 975000.00, 55000.00, 180, 45, 12),
	(7, 2, NULL, 'Angiógrafo', 'Philips', '7B20 Philips', NULL, NULL, 67023.00, 12000.00, 90, 50, 8),
	(8, 1, NULL, 'Monitor de Sinais Vitais', 'Philips', 'IntelliVue MX450', NULL, NULL, 8764.00, 900.00, 30, 0, 8),
	(9, 1, NULL, 'Monitor de Sinais Vitais', 'Dräger', 'Vista 120', NULL, NULL, 9650.00, 950.00, 30, 0, 8),
	(10, 1, NULL, 'Holter', 'Speculum', 'CardioMem CM 4000', NULL, NULL, 3200.00, 480.00, 3, 0, 10),
	(11, 1, NULL, 'Holter', 'Medtronic Portugal', 'SEER 1000', NULL, NULL, 1200.00, 180.00, 45, 0, 5),
	(12, 1, NULL, 'Eletrocardiógrafo', 'BLUESTREAM', 'ECG 1200G', NULL, NULL, 5700.00, 450.00, 30, 5, 8),
	(13, 1, NULL, 'Eletrocardiógrafo', 'Nosbet', 'ECG 12 canais', NULL, NULL, 4012.00, 400.00, 30, 5, 8),
	(14, 1, NULL, 'Eletrocardiógrafo', 'Philips', 'PageWriter TC50', NULL, NULL, 7892.60, 600.00, 45, 5, 8),
	(15, 1, NULL, 'Desfibrilhador', 'Clinifar', 'AED Pro', NULL, NULL, 3500.00, 280.00, 7, 0, 5),
	(16, 1, NULL, 'Desfibrilhador', 'Stryker', 'LIFEPAK 15', NULL, NULL, 6394.75, 350.00, 30, 0, 12),
	(17, 1, NULL, 'Desfibrilhador', 'LusoPalex', 'Defibtech Lifeline', NULL, NULL, 9500.00, 850.00, 60, 0, 8),
	(18, 4, NULL, 'Ecógrafo', 'CANON MEDICAL SYSTEMS', 'Aplio a450', NULL, NULL, 95000.00, 9000.00, 90, 20, 12),
	(19, 4, NULL, 'Ecógrafo', 'FUJIFILM', 'Arietta 750 DeepInsight', 'CDM93790660', NULL, 52000.00, 4200.00, 21, 20, 8),
	(20, 4, NULL, 'Ecógrafo', 'General Electric Healthcare', 'LOGIQ E10 Series', NULL, NULL, 75000.00, 5800.00, 45, 20, 14),
	(21, 4, NULL, 'Cardiotocógrafo', 'SPECULLUM', 'Edan F9', NULL, NULL, 7677.50, 700.00, 30, 0, 8),
	(22, 4, NULL, 'Cardiotocógrafo', 'Lusopalex', 'Gemelar CTG', NULL, NULL, 8161.64, 750.00, 30, 0, 8),
	(23, 3, NULL, 'Ventilador', 'Dräger Portugal', 'Savina 300 Select', NULL, NULL, 36379.79, 3500.00, 60, 0, 10),
	(24, 3, NULL, 'Ventilador Portátil', 'Clinifar', 'Ventway Sparrow', NULL, NULL, 10500.00, 1200.00, 45, 0, 8),
	(25, 3, NULL, 'Broncoscópio', 'Hemicare', 'Broncoflex Vortex', NULL, NULL, 9400.00, 1000.00, 45, 15, 7),
	(26, 3, NULL, 'Broncoscópio', 'FUJIFILM', 'EB-580S', NULL, NULL, 63660.00, 5500.00, 75, 15, 8),
	(27, 3, NULL, 'Broncoscópio', 'Boston Scientific Portugal', 'EXALT Model B', NULL, NULL, 204000.00, 9000.00, 90, 15, 8),
	(28, 3, NULL, 'Broncoscópio', 'OLYMPUS IBERIA', 'BF-Q190', NULL, NULL, 23539.90, 2500.00, 60, 15, 8),
	(29, 2, 1, 'TAC', 'Siemens Healthcare', 'TAC 128 cortes', NULL, '2019-04-12', 399700.00, 30000.00, 120, 15, 12),
	(30, 2, 3, 'RM', 'Siemens Healthcare', 'MAGNETOM Aera 1.5T', NULL, '2018-06-20', 850000.00, 45000.00, 150, 35, 15),
	(31, 1, 1, 'Eletrocardiógrafo', 'Philips', 'PageWriter TC50', NULL, '2021-02-15', 7892.60, 600.00, 45, 5, 8),
	(32, 1, 2, 'Monitor de Sinais Vitais', 'Dräger', 'Vista 120', NULL, '2022-09-10', 9650.00, 950.00, 30, 0, 8),
	(33, 4, 5, 'Ecógrafo', 'General Electric Healthcare', 'LOGIQ E10 Series', NULL, '2020-11-05', 75000.00, 6200.00, 75, 20, 9),
	(34, 3, 6, 'Ventilador', 'Dräger Portugal', 'Savina 300 Select', NULL, '2021-01-20', 36379.79, 3500.00, 60, 0, 10),
	(35, 1, 1, 'Monitor de Sinais Vitais', 'Philips', 'IntelliVue MX450', NULL, '2017-05-10', 8764.00, 1450.00, 30, 0, 8),
	(36, 1, 1, 'Desfibrilhador', 'Stryker', 'LIFEPAK 15', NULL, '2016-03-22', 6394.75, 900.00, 45, 0, 8),
	(37, 3, 1, 'Ventilador Portátil', 'Clinifar', 'Ventway Sparrow', NULL, '2018-07-15', 10500.00, 1900.00, 45, 0, 8),
	(38, 4, 1, 'Cardiotocógrafo', 'SPECULLUM', 'Edan F9', NULL, '2019-09-10', 7677.50, 900.00, 30, 0, 8),
	(39, 5, 1, 'Arco em C Ortopédico', 'Siemens Healthcare', 'Cios Select', NULL, '2017-02-18', 219999.00, 18000.00, 100, 12, 10),
	(40, 2, 2, 'RX Portátil Digital', 'FUJIFILM', 'FDR nano', NULL, '2020-04-08', 71895.00, 8000.00, 60, 10, 8),
	(41, 1, 2, 'Holter', 'Medtronic Portugal', 'SEER 1000', NULL, '2019-11-20', 1785.00, 450.00, 20, 0, 6),
	(42, 3, 2, 'Broncoscópio', 'OLYMPUS IBERIA', 'BF-Q190', NULL, '2018-01-14', 23539.90, 3300.00, 60, 15, 8),
	(43, 4, 2, 'Ecógrafo', 'CANON MEDICAL SYSTEMS', 'Aplio a450', NULL, '2017-06-12', 79000.00, 8500.00, 75, 20, 9),
	(44, 5, 2, 'Mesa Cirúrgica Ortopédica', 'Maquet', 'Alphamaxx', NULL, '2016-10-03', 45000.00, 5200.00, 60, 0, 12),
	(45, 2, 3, 'Angiógrafo', 'Philips', 'Azurion 7', NULL, '2019-03-11', 975000.00, 65000.00, 180, 45, 12),
	(46, 1, 3, 'Eletrocardiógrafo', 'Philips', 'PageWriter TC50', NULL, '2020-01-30', 7892.60, 800.00, 45, 5, 8),
	(47, 3, 3, 'Ventilador', 'Dräger Portugal', 'Savina 300 Select', NULL, '2017-09-25', 36379.79, 5200.00, 60, 0, 10),
	(48, 4, 3, 'Ecógrafo', 'FUJIFILM', 'Arietta 750 DeepInsight', 'CDM93790660', '2018-12-05', 70000.00, 7600.00, 70, 20, 9),
	(49, 5, 3, 'RX Arco em C', 'Siemens Healthcare', 'Cios Select', NULL, '2018-08-17', 219999.00, 16000.00, 100, 12, 10),
	(50, 2, 4, 'RX', 'Philips', 'DigitalDiagnost C90', NULL, '2016-05-19', 150000.00, 13000.00, 90, 8, 10),
	(51, 1, 4, 'Monitor de Sinais Vitais', 'Dräger', 'Vista 120', NULL, '2019-02-11', 9650.00, 1200.00, 30, 0, 8),
	(52, 3, 4, 'Broncoscópio', 'Hemicare', 'Broncoflex Vortex', NULL, '2020-07-09', 9400.00, 1300.00, 45, 15, 7),
	(53, 4, 4, 'Cardiotocógrafo', 'Lusopalex', 'Gemelar CTG', NULL, '2021-04-18', 8161.64, 850.00, 30, 0, 8),
	(54, 5, 4, 'Serra Ortopédica', 'Stryker', 'System 8', NULL, '2017-12-02', 18500.00, 2200.00, 45, 0, 8),
	(55, 2, 5, 'TAC', 'Philips', 'Incisive CT', NULL, '2018-02-22', 450000.00, 38000.00, 120, 14, 12),
	(56, 1, 5, 'Desfibrilhador', 'LusoPalex', 'Defibtech Lifeline', NULL, '2017-10-15', 7500.00, 950.00, 45, 0, 8),
	(57, 3, 5, 'Ventilador', 'Dräger Portugal', 'Savina 300 Select', NULL, '2016-06-30', 36379.79, 6000.00, 60, 0, 10),
	(58, 4, 5, 'Cardiotocógrafo', 'SPECULLUM', 'Edan F9', NULL, '2020-03-16', 7677.50, 750.00, 30, 0, 8),
	(59, 5, 5, 'Arco em C Ortopédico', 'Siemens Healthcare', 'Cios Select', NULL, '2019-01-25', 219999.00, 15000.00, 100, 12, 10),
	(60, 2, 6, 'RX Portátil Digital', 'FUJIFILM', 'FDR nano', NULL, '2019-05-10', 71895.00, 7200.00, 60, 10, 8),
	(61, 1, 6, 'Eletrocardiógrafo', 'Nosbet', 'ECG 12 canais', NULL, '2018-04-14', 4012.00, 650.00, 30, 5, 8),
	(62, 3, 6, 'Broncoscópio', 'FUJIFILM', 'EB-580S', NULL, '2017-08-08', 63660.00, 7000.00, 75, 15, 8),
	(63, 4, 6, 'Ecógrafo', 'General Electric Healthcare', 'LOGIQ E10 Series', NULL, '2021-01-13', 75000.00, 6200.00, 75, 20, 9),
	(64, 5, 6, 'Mesa Cirúrgica Ortopédica', 'Maquet', 'Alphamaxx', NULL, '2018-11-21', 45000.00, 4700.00, 60, 0, 12),
	(65, 2, 7, 'RX', 'Philips', 'DigitalDiagnost C90', NULL, '2017-07-07', 150000.00, 12500.00, 90, 8, 10),
	(66, 1, 7, 'Monitor de Sinais Vitais', 'Philips', 'IntelliVue MX450', NULL, '2018-09-19', 8764.00, 1300.00, 30, 0, 8),
	(67, 3, 7, 'Ventilador Portátil', 'Clinifar', 'Ventway Sparrow', NULL, '2020-10-05', 10500.00, 1500.00, 45, 0, 8),
	(68, 4, 7, 'Ecógrafo', 'CANON MEDICAL SYSTEMS', 'Aplio a450', NULL, '2019-06-24', 79000.00, 7100.00, 75, 20, 9),
	(69, 5, 7, 'Serra Ortopédica', 'Stryker', 'System 8', NULL, '2016-12-12', 18500.00, 2600.00, 45, 0, 8),
	(70, 2, 8, 'TAC', 'Siemens Healthcare', 'TAC 128 cortes', NULL, '2017-03-09', 399700.00, 42000.00, 120, 15, 12),
	(71, 1, 8, 'Holter', 'Speculum', 'CardioMem CM 4000', NULL, '2019-08-15', 2260.00, 500.00, 20, 0, 6),
	(72, 3, 8, 'Broncoscópio', 'OLYMPUS IBERIA', 'BF-Q190', NULL, '2018-10-29', 23539.90, 3500.00, 60, 15, 8),
	(73, 4, 8, 'Cardiotocógrafo', 'Lusopalex', 'Gemelar CTG', NULL, '2020-02-02', 8161.64, 820.00, 30, 0, 8),
	(74, 5, 8, 'RX Arco em C', 'Siemens Healthcare', 'Cios Select', NULL, '2018-05-27', 219999.00, 17000.00, 100, 12, 10),
	(75, 5, NULL, 'Mesa Cirúrgica Ortopédica', 'Trumpf Medical', 'TruSystem 7500', NULL, NULL, 85000.00, 6000.00, 90, 0, 12),
	(76, 1, NULL, 'Holter', 'Schiller', 'MT-200', NULL, NULL, 1450.00, 200.00, 30, 0, 7),
	(77, 1, NULL, 'Holter', 'GE Healthcare', 'SEER 12', NULL, NULL, 3800.00, 420.00, 14, 0, 9),
	(78, 1, NULL, 'Holter', 'Contec Medical', 'TLC9803', NULL, NULL, 890.00, 120.00, 60, 0, 5),
	(79, 1, NULL, 'Desfibrilhador', 'Zoll Medical', 'R Series', NULL, NULL, 12500.00, 700.00, 5, 0, 10),
	(80, 1, NULL, 'Desfibrilhador', 'Physio-Control', 'LIFEPAK 20e', NULL, NULL, 8900.00, 480.00, 35, 0, 9),
	(81, 1, NULL, 'Desfibrilhador', 'HeartSine', 'Samaritan PAD 360P', NULL, NULL, 2200.00, 150.00, 45, 0, 6),
	(82, 4, NULL, 'Ecógrafo', 'Samsung Medison', 'WS80A Elite', NULL, NULL, 88000.00, 7800.00, 60, 20, 11),
	(83, 4, NULL, 'Ecógrafo', 'Mindray', 'DC-80 Exp', NULL, NULL, 45000.00, 3500.00, 30, 20, 8),
	(84, 4, NULL, 'Ecógrafo', 'Siemens Healthineers', 'ACUSON Juniper', NULL, NULL, 62000.00, 5100.00, 45, 20, 10),
	(85, 2, NULL, 'TAC', 'GE Healthcare', 'Revolution Apex', NULL, NULL, 520000.00, 38000.00, 150, 12, 14),
	(86, 2, NULL, 'TAC', 'Canon Medical', 'Aquilion ONE', NULL, NULL, 480000.00, 35000.00, 135, 13, 13),
	(87, 2, NULL, 'TAC', 'United Imaging', 'uCT 960+', NULL, NULL, 310000.00, 22000.00, 90, 16, 10),
	(88, 4, NULL, 'Cardiotocógrafo', 'GE Healthcare', 'Corometrics 250cx', NULL, NULL, 12500.00, 1100.00, 45, 0, 10),
	(89, 4, NULL, 'Cardiotocógrafo', 'Philips', 'Avalon FM50', NULL, NULL, 9800.00, 850.00, 30, 0, 9),
	(90, 4, NULL, 'Cardiotocógrafo', 'Bionet', 'FC1400', NULL, NULL, 4200.00, 400.00, 14, 0, 6),
	(91, 3, NULL, 'Ventilador', 'Hamilton Medical', 'HAMILTON-C6', NULL, NULL, 52000.00, 4800.00, 75, 0, 12),
	(92, 3, NULL, 'Ventilador', 'Mindray', 'SV800', NULL, NULL, 28000.00, 2600.00, 45, 0, 10),
	(93, 3, NULL, 'Ventilador', 'Getinge', 'Servo-u', NULL, NULL, 44000.00, 4200.00, 60, 0, 13),
	(94, 1, NULL, 'Monitor de Sinais Vitais', 'GE Healthcare', 'CARESCAPE B650', NULL, NULL, 14500.00, 1400.00, 40, 0, 10),
	(95, 1, NULL, 'Monitor de Sinais Vitais', 'Mindray', 'BeneVision N17', NULL, NULL, 6800.00, 700.00, 21, 0, 8),
	(96, 1, NULL, 'Monitor de Sinais Vitais', 'Nihon Kohden', 'BSM-6301', NULL, NULL, 11200.00, 1100.00, 35, 0, 9);

-- A despejar estrutura para tabela dss_hospitalar.eq_angio
CREATE TABLE IF NOT EXISTS `eq_angio` (
  `id_equip` int NOT NULL,
  `resolucao_imagem` decimal(4,2) DEFAULT NULL,
  `dose_radiacao` decimal(6,2) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_angio_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_angio: ~2 rows (aproximadamente)
INSERT INTO `eq_angio` (`id_equip`, `resolucao_imagem`, `dose_radiacao`) VALUES
	(6, 0.25, 8.50),
	(7, 0.35, 9.20),
	(45, 0.25, 8.50);

-- A despejar estrutura para tabela dss_hospitalar.eq_bronco
CREATE TABLE IF NOT EXISTS `eq_bronco` (
  `id_equip` int NOT NULL,
  `diametro_mm` decimal(4,1) DEFAULT NULL,
  `canal_trabalho_mm` decimal(4,1) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_bronco_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_bronco: ~8 rows (aproximadamente)
INSERT INTO `eq_bronco` (`id_equip`, `diametro_mm`, `canal_trabalho_mm`) VALUES
	(25, 5.8, 2.0),
	(26, 5.9, 2.2),
	(27, 6.0, 2.8),
	(28, 5.5, 2.0),
	(42, 5.5, 2.0),
	(52, 5.8, 2.0),
	(62, 5.9, 2.2),
	(72, 5.5, 2.0);

-- A despejar estrutura para tabela dss_hospitalar.eq_cardiotoc
CREATE TABLE IF NOT EXISTS `eq_cardiotoc` (
  `id_equip` int NOT NULL,
  `gemelar` tinyint(1) DEFAULT NULL,
  `autonomia_horas` decimal(4,1) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_cardiotoc_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_cardiotoc: ~9 rows (aproximadamente)
INSERT INTO `eq_cardiotoc` (`id_equip`, `gemelar`, `autonomia_horas`) VALUES
	(21, 0, 4.0),
	(22, 1, 5.0),
	(38, 0, 4.0),
	(53, 1, 5.0),
	(58, 0, 4.0),
	(73, 1, 5.0),
	(88, 1, 6.0),
	(89, 0, 5.0),
	(90, 0, 3.5);

-- A despejar estrutura para tabela dss_hospitalar.eq_desfibri
CREATE TABLE IF NOT EXISTS `eq_desfibri` (
  `id_equip` int NOT NULL,
  `energia_max_j` int DEFAULT NULL,
  `modo_dea` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_desfibri_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_desfibri: ~10 rows (aproximadamente)
INSERT INTO `eq_desfibri` (`id_equip`, `energia_max_j`, `modo_dea`) VALUES
	(15, 200, 1),
	(16, 360, 1),
	(17, 200, 1),
	(36, 360, 1),
	(56, 200, 1),
	(77, 360, 0),
	(78, 360, 1),
	(79, 360, 1),
	(80, 360, 1),
	(81, 200, 1);

-- A despejar estrutura para tabela dss_hospitalar.eq_ecg
CREATE TABLE IF NOT EXISTS `eq_ecg` (
  `id_equip` int NOT NULL,
  `canais` int DEFAULT NULL,
  `interpretacao_automatica` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_ecg_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_ecg: ~6 rows (aproximadamente)
INSERT INTO `eq_ecg` (`id_equip`, `canais`, `interpretacao_automatica`) VALUES
	(12, 12, 1),
	(13, 12, 0),
	(14, 12, 1),
	(31, 12, 1),
	(46, 12, 1),
	(61, 12, 0);

-- A despejar estrutura para tabela dss_hospitalar.eq_eco
CREATE TABLE IF NOT EXISTS `eq_eco` (
  `id_equip` int NOT NULL,
  `num_sondas` int DEFAULT NULL,
  `doppler` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_eco_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_eco: ~11 rows (aproximadamente)
INSERT INTO `eq_eco` (`id_equip`, `num_sondas`, `doppler`) VALUES
	(18, 3, 1),
	(19, 4, 1),
	(20, 4, 1),
	(33, 4, 1),
	(43, 3, 1),
	(48, 4, 1),
	(63, 4, 1),
	(68, 3, 1),
	(82, 3, 1);

-- A despejar estrutura para tabela dss_hospitalar.eq_holter
CREATE TABLE IF NOT EXISTS `eq_holter` (
  `id_equip` int NOT NULL,
  `duracao_gravacao` int DEFAULT NULL,
  `canais` int DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_holter_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_holter: ~7 rows (aproximadamente)
INSERT INTO `eq_holter` (`id_equip`, `duracao_gravacao`, `canais`) VALUES
	(10, 24, 3),
	(11, 48, 3),
	(41, 48, 3),
	(71, 24, 3),
	(74, 24, 3),
	(75, 12, 12),
	(76, 24, 2);

-- A despejar estrutura para tabela dss_hospitalar.eq_monitor
CREATE TABLE IF NOT EXISTS `eq_monitor` (
  `id_equip` int NOT NULL,
  `parametros` int DEFAULT NULL,
  `bateria_horas` decimal(4,1) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_monitor_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_monitor: ~9 rows (aproximadamente)
INSERT INTO `eq_monitor` (`id_equip`, `parametros`, `bateria_horas`) VALUES
	(8, 6, 4.0),
	(9, 7, 5.0),
	(32, 7, 5.0),
	(35, 6, 3.5),
	(51, 7, 4.5),
	(66, 6, 3.8),
	(94, 9, 6.0),
	(95, 8, 5.5),
	(96, 7, 4.5);

-- A despejar estrutura para tabela dss_hospitalar.eq_ortopedia
CREATE TABLE IF NOT EXISTS `eq_ortopedia` (
  `id_equip` int NOT NULL,
  `precisao_mm` decimal(4,2) DEFAULT NULL,
  `portatil` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_ortopedia_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_ortopedia: ~9 rows (aproximadamente)
INSERT INTO `eq_ortopedia` (`id_equip`, `precisao_mm`, `portatil`) VALUES
	(39, 0.80, 0),
	(44, 1.00, 0),
	(49, 0.75, 0),
	(54, 0.50, 1),
	(59, 0.80, 0),
	(64, 1.00, 0),
	(69, 0.50, 1),
	(74, 0.75, 0),
	(75, 0.50, 0);

-- A despejar estrutura para tabela dss_hospitalar.eq_rm
CREATE TABLE IF NOT EXISTS `eq_rm` (
  `id_equip` int NOT NULL,
  `campo_magnetico` decimal(3,1) DEFAULT NULL,
  `tempo_aquisicao` int DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_rm_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_rm: ~0 rows (aproximadamente)
INSERT INTO `eq_rm` (`id_equip`, `campo_magnetico`, `tempo_aquisicao`) VALUES
	(30, 1.5, 35);

-- A despejar estrutura para tabela dss_hospitalar.eq_rx
CREATE TABLE IF NOT EXISTS `eq_rx` (
  `id_equip` int NOT NULL,
  `digital` tinyint(1) DEFAULT NULL,
  `dose_radiacao` decimal(6,2) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_rx_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_rx: ~9 rows (aproximadamente)
INSERT INTO `eq_rx` (`id_equip`, `digital`, `dose_radiacao`) VALUES
	(3, 1, 2.50),
	(4, 1, 2.20),
	(5, 1, 3.10),
	(40, 1, 2.20),
	(49, 1, 3.10),
	(50, 1, 2.50),
	(60, 1, 2.20),
	(65, 1, 2.50),
	(74, 1, 3.10);

-- A despejar estrutura para tabela dss_hospitalar.eq_tac
CREATE TABLE IF NOT EXISTS `eq_tac` (
  `id_equip` int NOT NULL,
  `cortes` int DEFAULT NULL,
  `dose_radiacao` decimal(6,2) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_tac_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_tac: ~8 rows (aproximadamente)
INSERT INTO `eq_tac` (`id_equip`, `cortes`, `dose_radiacao`) VALUES
	(1, 128, 7.50),
	(2, 128, 7.20),
	(29, 128, 7.60),
	(55, 128, 7.20),
	(70, 128, 7.60),
	(85, 256, 6.80),
	(86, 320, 6.50),
	(87, 128, 8.10);

-- A despejar estrutura para tabela dss_hospitalar.eq_ventilad
CREATE TABLE IF NOT EXISTS `eq_ventilad` (
  `id_equip` int NOT NULL,
  `modos_ventilacao` int DEFAULT NULL,
  `autonomia_horas` decimal(4,1) DEFAULT NULL,
  PRIMARY KEY (`id_equip`),
  CONSTRAINT `eq_ventilad_ibfk_1` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id_equip`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.eq_ventilad: ~10 rows (aproximadamente)
INSERT INTO `eq_ventilad` (`id_equip`, `modos_ventilacao`, `autonomia_horas`) VALUES
	(23, 12, 2.0),
	(24, 8, 6.0),
	(34, 12, 2.0),
	(37, 8, 5.5),
	(47, 12, 2.0),
	(57, 12, 1.8),
	(67, 8, 6.0),
	(91, 18, 2.5),
	(92, 14, 3.0),
	(93, 16, 2.0);

-- A despejar estrutura para tabela dss_hospitalar.especialidades
CREATE TABLE IF NOT EXISTS `especialidades` (
  `id_esp` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  PRIMARY KEY (`id_esp`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.especialidades: ~5 rows (aproximadamente)
INSERT INTO `especialidades` (`id_esp`, `nome`) VALUES
	(1, 'Cardiologia'),
	(4, 'Ginecologia / Obstetrícia'),
	(2, 'Imagiologia e Radiologia'),
	(5, 'Ortopedia'),
	(3, 'Pneumologia');

-- A despejar estrutura para tabela dss_hospitalar.hospitais
CREATE TABLE IF NOT EXISTS `hospitais` (
  `id_hosp` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `id_regiao` int NOT NULL,
  PRIMARY KEY (`id_hosp`),
  KEY `id_regiao` (`id_regiao`),
  CONSTRAINT `hospitais_ibfk_1` FOREIGN KEY (`id_regiao`) REFERENCES `regioes` (`id_regiao`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.hospitais: ~8 rows (aproximadamente)
INSERT INTO `hospitais` (`id_hosp`, `nome`, `id_regiao`) VALUES
	(1, 'Hospital de Braga', 1),
	(2, 'Centro Hospitalar do Porto', 1),
	(3, 'Hospital São João', 1),
	(4, 'Hospital de Guimarães', 1),
	(5, 'Hospital de Coimbra (HUC)', 2),
	(6, 'Centro Hospitalar do Médio Tejo', 2),
	(7, 'Hospital Sousa Martins', 2),
	(8, 'Hospital Amato Lusitano', 2);

-- A despejar estrutura para tabela dss_hospitalar.indicadores_especialidade_regiao
CREATE TABLE IF NOT EXISTS `indicadores_especialidade_regiao` (
  `id_indicador` int NOT NULL AUTO_INCREMENT,
  `id_esp` int NOT NULL,
  `id_reg` int NOT NULL,
  `prev` decimal(10,2) DEFAULT NULL,
  `mort` decimal(10,2) DEFAULT NULL,
  `ind_env` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_indicador`),
  KEY `fk_indicador_especialidade` (`id_esp`),
  KEY `fk_indicador_regiao` (`id_reg`),
  CONSTRAINT `fk_indicador_especialidade` FOREIGN KEY (`id_esp`) REFERENCES `especialidades` (`id_esp`) ON DELETE CASCADE,
  CONSTRAINT `fk_indicador_regiao` FOREIGN KEY (`id_reg`) REFERENCES `regioes` (`id_regiao`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.indicadores_especialidade_regiao: ~10 rows (aproximadamente)
INSERT INTO `indicadores_especialidade_regiao` (`id_indicador`, `id_esp`, `id_reg`, `prev`, `mort`, `ind_env`) VALUES
	(1, 1, 1, 12.50, 3.20, 70.00),
	(2, 1, 2, 15.80, 4.10, 65.00),
	(3, 2, 1, 8.20, 1.10, 75.00),
	(4, 2, 2, 9.50, 1.40, 72.00),
	(5, 3, 1, 10.10, 2.50, 68.00),
	(6, 3, 2, 11.70, 2.90, 66.00),
	(7, 4, 1, 7.30, 0.80, 55.00),
	(8, 4, 2, 6.90, 0.70, 53.00),
	(9, 5, 1, 13.40, 1.90, 80.00),
	(10, 5, 2, 12.80, 1.70, 78.00);

-- A despejar estrutura para tabela dss_hospitalar.regioes
CREATE TABLE IF NOT EXISTS `regioes` (
  `id_regiao` int NOT NULL AUTO_INCREMENT,
  `nome_regiao` varchar(100) NOT NULL,
  PRIMARY KEY (`id_regiao`),
  UNIQUE KEY `nome_regiao` (`nome_regiao`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela dss_hospitalar.regioes: ~2 rows (aproximadamente)
INSERT INTO `regioes` (`id_regiao`, `nome_regiao`) VALUES
	(2, 'Centro'),
	(1, 'Norte');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
