SET FOREIGN_KEY_CHECKS=0;

-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour total2854515_2seq1n
CREATE DATABASE IF NOT EXISTS `total2854515_2seq1n` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `total2854515_2seq1n`;

-- Listage de la structure de table absences
CREATE TABLE IF NOT EXISTS `absences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `justification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `absences_agent_id_foreign` (`agent_id`),
  CONSTRAINT `absences_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table absences : ~0 rows (environ)

-- Listage de la structure de table affectations
CREATE TABLE IF NOT EXISTS `affectations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `direction_id` bigint unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affectations_agent_id_foreign` (`agent_id`),
  KEY `affectations_direction_id_foreign` (`direction_id`),
  CONSTRAINT `affectations_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`),
  CONSTRAINT `affectations_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table affectations : ~0 rows (environ)
INSERT INTO `affectations` (`id`, `agent_id`, `direction_id`, `date_debut`, `date_fin`, `created_at`, `updated_at`) VALUES
	(1, 8, 3, '2026-08-01', NULL, '2026-08-30 09:20:10', '2026-08-30 09:20:10');

-- Listage de la structure de table affiliations
CREATE TABLE IF NOT EXISTS `affiliations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postnom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relation` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliations_agent_id_foreign` (`agent_id`),
  CONSTRAINT `affiliations_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table affiliations : ~0 rows (environ)
INSERT INTO `affiliations` (`id`, `agent_id`, `nom`, `postnom`, `prenom`, `date_naissance`, `lieu_naissance`, `etat`, `relation`, `statut`, `created_at`, `updated_at`) VALUES
	(2, 8, 'BONGONDA', 'PATISA', 'Pierre', '1970-10-01', 'MBANDAKA', 'mort', 'pere', 1, '2026-08-30 09:05:55', '2026-08-30 09:14:27');

-- Listage de la structure de table agents
CREATE TABLE IF NOT EXISTS `agents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `matricule` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade_id` bigint unsigned NOT NULL,
  `fonction_id` bigint unsigned NOT NULL,
  `direction_id` bigint unsigned NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postnom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexe` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat_civil` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT '1',
  `reference_engagement` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_engagement` date NOT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `territoire` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `village` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agents_email_unique` (`email`),
  KEY `agents_grade_id_foreign` (`grade_id`),
  KEY `agents_fonction_id_foreign` (`fonction_id`),
  KEY `agents_direction_id_foreign` (`direction_id`),
  CONSTRAINT `agents_direction_id_foreign` FOREIGN KEY (`direction_id`) REFERENCES `directions` (`id`),
  CONSTRAINT `agents_fonction_id_foreign` FOREIGN KEY (`fonction_id`) REFERENCES `fonctions` (`id`),
  CONSTRAINT `agents_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table agents : ~4 rows (environ)
INSERT INTO `agents` (`id`, `matricule`, `grade_id`, `fonction_id`, `direction_id`, `nom`, `postnom`, `prenom`, `sexe`, `date_naissance`, `email`, `telephone`, `etat_civil`, `statut`, `reference_engagement`, `date_engagement`, `province`, `territoire`, `village`, `photo`, `created_at`, `updated_at`) VALUES
	(1, 'MAT001', 1, 1, 1, 'Papy', 'Pierre', 'Admin', 'M', '1990-01-01', 'pierrpapy@gmail.com', '+243800000000', 'Celibataire', 1, 'REF001', '2024-01-01', 'Kinshasa', 'Lingwala', 'Kinshasa', NULL, '2026-08-29 13:27:30', '2026-08-29 13:27:30'),
	(3, 'MAT003', 1, 1, 1, 'MWAMBA', 'LUKUSA', 'Marie', 'F', '1988-05-20', 'mwamba@test.com', '0698765432', 'celibataire', 1, 'REF002', '2023-06-15', 'Haut-Katanga', 'Lubumbashi', 'Kampemba', NULL, '2026-08-30 07:32:02', '2026-08-30 08:23:41'),
	(8, 'NU', 5, 4, 3, 'BONGONDA', 'PALO', 'Jean', 'M', '2026-08-01', 'bola@gmail.com', '0898596501', 'celibataire', 1, 'REF-52147', '2019-10-17', 'KONGO CENTRAL', 'KINSANTU', 'MAYOMBE', '/storage/agents/photos/a49z9vAYHKMgSPV6HDsJd7Fys9uj0Pkp2bvJESHL.jpg', '2026-08-30 08:27:17', '2026-08-30 08:49:14'),
	(10, 'NU', 5, 2, 2, 'TAMBOLONGA', 'NGONGA', 'Helene ', 'F', '2002-12-12', 'tambo@gmail.com', '0898564124', 'celibataire', 1, 'REF-5478', '2025-10-16', 'MANIEMA', 'KIBOMBO', 'MANGA', '/storage/agents/photos/E50oVXPxGZXmBB1IZUH1koHdAXx7brB6YBTgoC0v.jpg', '2026-08-30 12:29:19', '2026-08-30 12:29:19');

-- Listage de la structure de table agent_formations
CREATE TABLE IF NOT EXISTS `agent_formations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `formation_id` bigint unsigned NOT NULL,
  `resultat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agent_formations_agent_id_foreign` (`agent_id`),
  KEY `agent_formations_formation_id_foreign` (`formation_id`),
  CONSTRAINT `agent_formations_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`),
  CONSTRAINT `agent_formations_formation_id_foreign` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table agent_formations : ~1 rows (environ)
INSERT INTO `agent_formations` (`id`, `agent_id`, `formation_id`, `resultat`, `observation`, `created_at`, `updated_at`) VALUES
	(1, 3, 2, 'En cours', 'RAS', '2026-08-30 11:00:13', '2026-08-30 11:00:38'),
	(2, 10, 2, 'En cours', NULL, '2026-08-30 13:11:59', '2026-08-30 13:11:59');

-- Listage de la structure de table conges
CREATE TABLE IF NOT EXISTS `conges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `type_conge_id` bigint unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nombre_jours` int NOT NULL,
  `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_ATTENTE',
  `observation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_demande` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conges_agent_id_foreign` (`agent_id`),
  KEY `conges_type_conge_id_foreign` (`type_conge_id`),
  CONSTRAINT `conges_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`),
  CONSTRAINT `conges_type_conge_id_foreign` FOREIGN KEY (`type_conge_id`) REFERENCES `types_conges` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table conges : ~1 rows (environ)
INSERT INTO `conges` (`id`, `agent_id`, `type_conge_id`, `date_debut`, `date_fin`, `nombre_jours`, `motif`, `statut`, `observation`, `date_demande`, `created_at`, `updated_at`) VALUES
	(2, 10, 1, '2026-08-30', '2026-09-30', 32, NULL, 'en_attente', NULL, '2026-08-30 15:19:03', '2026-08-30 13:19:03', '2026-08-30 13:19:03');

-- Listage de la structure de table contrats
CREATE TABLE IF NOT EXISTS `contrats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `type_contrat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contrats_agent_id_foreign` (`agent_id`),
  CONSTRAINT `contrats_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table contrats : ~0 rows (environ)
INSERT INTO `contrats` (`id`, `agent_id`, `type_contrat`, `reference`, `date_debut`, `date_fin`, `statut`, `created_at`, `updated_at`) VALUES
	(1, 3, 'CDI', 'REF-4587', '2026-08-29', '2027-04-24', 'ACTIF', '2026-08-30 11:01:15', '2026-08-30 11:01:30');

-- Listage de la structure de table directions
CREATE TABLE IF NOT EXISTS `directions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sigle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `directions_sigle_unique` (`sigle`),
  UNIQUE KEY `directions_nom_unique` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table directions : ~3 rows (environ)
INSERT INTO `directions` (`id`, `sigle`, `nom`, `statut`, `created_at`, `updated_at`) VALUES
	(1, 'DG', 'Direction Generale', 1, '2026-08-29 13:27:30', '2026-08-29 13:27:30'),
	(2, 'RH', 'Direction des Ressources Humaines', 1, '2026-08-30 08:28:09', '2026-08-30 08:28:09'),
	(3, 'DAF', 'Direction de Finances', 1, '2026-08-30 08:28:29', '2026-08-30 08:28:29');

-- Listage de la structure de table documents
CREATE TABLE IF NOT EXISTS `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `intitule` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_agent_id_foreign` (`agent_id`),
  CONSTRAINT `documents_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table documents : ~0 rows (environ)
INSERT INTO `documents` (`id`, `agent_id`, `intitule`, `chemin_fichier`, `created_at`, `updated_at`) VALUES
	(1, 8, 'dipl', 'uploads/documents/1788088216_aaa.pdf', '2026-08-30 09:10:16', '2026-08-30 09:10:16');

-- Listage de la structure de table droits
CREATE TABLE IF NOT EXISTS `droits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_droit` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `droits_nom_droit_unique` (`nom_droit`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table droits : ~47 rows (environ)
INSERT INTO `droits` (`id`, `nom_droit`, `description`, `module`, `date_creation`, `created_at`, `updated_at`) VALUES
	(1, 'ALL_DASHBOARD', 'Tous droits sur dashboard', 'dashboard', '2026-08-29 15:28:02', '2026-08-29 13:28:02', '2026-08-29 13:28:02'),
	(2, 'ALL_AGENTS', 'Tous droits sur agents', 'agents', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(3, 'ALL_GRADES', 'Tous droits sur grades', 'grades', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(4, 'ALL_FONCTIONS', 'Tous droits sur fonctions', 'fonctions', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(5, 'ALL_DIRECTIONS', 'Tous droits sur directions', 'directions', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(6, 'ALL_CONGES', 'Tous droits sur conges', 'conges', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(7, 'ALL_ABSENCES', 'Tous droits sur absences', 'absences', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(8, 'ALL_PERMISSIONS', 'Tous droits sur permissions', 'permissions', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(9, 'ALL_PRESENCES', 'Tous droits sur presences', 'presences', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(10, 'ALL_POINTAGES', 'Tous droits sur pointages', 'pointages', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(11, 'ALL_SANCTIONS', 'Tous droits sur sanctions', 'sanctions', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(12, 'ALL_FORMATIONS', 'Tous droits sur formations', 'formations', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(13, 'ALL_MISSIONS', 'Tous droits sur missions', 'missions', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(14, 'ALL_EVALUATIONS', 'Tous droits sur evaluations', 'evaluations', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(15, 'ALL_PRIMES', 'Tous droits sur primes', 'primes', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(16, 'ALL_RETRAITES', 'Tous droits sur retraites', 'retraites', '2026-08-29 15:28:34', '2026-08-29 13:28:34', '2026-08-29 13:28:34'),
	(17, 'ALL_CONTRATS', 'Tous droits sur contrats', 'contrats', '2026-08-29 15:28:35', '2026-08-29 13:28:35', '2026-08-29 13:28:35'),
	(18, 'ALL_DOCUMENTS', 'Tous droits sur documents', 'documents', '2026-08-29 15:28:35', '2026-08-29 13:28:35', '2026-08-29 13:28:35'),
	(19, 'ALL_NOTIFICATIONS', 'Tous droits sur notifications', 'notifications', '2026-08-29 15:28:35', '2026-08-29 13:28:35', '2026-08-29 13:28:35'),
	(20, 'ALL_ROLES', 'Tous droits sur roles', 'roles', '2026-08-29 15:28:35', '2026-08-29 13:28:35', '2026-08-29 13:28:35'),
	(21, 'ALL_USERS', 'Tous droits sur users', 'users', '2026-08-29 15:28:35', '2026-08-29 13:28:35', '2026-08-29 13:28:35'),
	(22, 'ALL_DROITS', 'Tous droits sur droits', 'droits', '2026-08-29 15:28:35', '2026-08-29 13:28:35', '2026-08-29 13:28:35'),
	(23, 'ALL_LOGS', 'Tous droits sur logs', 'logs', '2026-08-29 15:28:35', '2026-08-29 13:28:35', '2026-08-29 13:28:35'),
	(24, 'ALL_CONFIGURATION', 'Tous droits sur configuration', 'configuration', '2026-08-29 15:28:35', '2026-08-29 13:28:35', '2026-08-29 13:28:35'),
	(25, 'READ_AGENT', 'Consulter un agent', 'AGENTS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(26, 'CREATE_AGENT', 'Créer un agent', 'AGENTS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(27, 'UPDATE_AGENT', 'Modifier un agent', 'AGENTS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(28, 'DELETE_AGENT', 'Supprimer un agent', 'AGENTS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(29, 'READ_ABSENCE', 'Consulter les absences', 'ABSENCES', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(30, 'CREATE_ABSENCE', 'Créer une absence', 'ABSENCES', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(31, 'READ_MISSION', 'Consulter les missions', 'MISSIONS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(32, 'READ_EVALUATION', 'Consulter les évaluations', 'EVALUATIONS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(33, 'READ_CONTRAT', 'Consulter les contrats', 'CONTRATS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(34, 'READ_PRIME', 'Consulter les primes', 'PRIMES', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(35, 'READ_PERMISSION', 'Consulter les permissions', 'PERMISSIONS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(36, 'READ_SANCTION', 'Consulter les sanctions', 'SANCTIONS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(37, 'READ_FORMATION', 'Consulter les formations', 'FORMATIONS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(38, 'READ_DOCUMENT', 'Consulter les documents', 'DOCUMENTS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(39, 'READ_USER', 'Consulter les utilisateurs', 'UTILISATEURS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(40, 'READ_ROLE', 'Consulter les rôles', 'ROLES', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(41, 'READ_DROIT', 'Consulter les droits', 'DROITS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(42, 'ALL_CONFIGURATIONS', 'Accès complet aux configurations', 'CONFIGURATIONS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(43, 'READ_CONFIGURATION', 'Consulter les configurations', 'CONFIGURATIONS', '2026-08-30 14:41:42', '2026-08-30 12:41:42', '2026-08-30 12:41:42'),
	(44, 'VIEW_AGENTS', 'Voir les agents', 'AGENTS', '2026-08-30 16:50:43', '2026-08-30 14:50:43', '2026-08-30 14:50:43'),
	(45, 'VIEW_CATALOGUE_FORMATIONS', 'Voir le catalogue des formations', 'FORMATIONS', '2026-08-30 17:07:20', '2026-08-30 15:07:20', '2026-08-30 15:07:20'),
	(46, 'MANAGE_INSCRIPTIONS', 'Gerer les inscriptions aux formations', 'FORMATIONS', '2026-08-30 17:07:20', '2026-08-30 15:07:20', '2026-08-30 15:07:20'),
	(47, 'VIEW_FORMATIONS', 'Voir les formations', 'FORMATIONS', '2026-08-30 17:07:20', '2026-08-30 15:07:20', '2026-08-30 15:07:20');

-- Listage de la structure de table etudes
CREATE TABLE IF NOT EXISTS `etudes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `nombre_annee` int NOT NULL,
  `lieu` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etablissement` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `etudes_agent_id_foreign` (`agent_id`),
  CONSTRAINT `etudes_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table etudes : ~0 rows (environ)
INSERT INTO `etudes` (`id`, `agent_id`, `nombre_annee`, `lieu`, `etablissement`, `created_at`, `updated_at`) VALUES
	(1, 8, 6, 'Kinshasa', 'Groupe Scolaire Tumba', '2026-08-30 09:21:52', '2026-08-30 09:21:52');

-- Listage de la structure de table evaluations
CREATE TABLE IF NOT EXISTS `evaluations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `date_evaluation` date NOT NULL,
  `note` decimal(5,2) NOT NULL,
  `appreciation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evaluateur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evaluations_agent_id_foreign` (`agent_id`),
  CONSTRAINT `evaluations_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table evaluations : ~0 rows (environ)
INSERT INTO `evaluations` (`id`, `agent_id`, `date_evaluation`, `note`, `appreciation`, `evaluateur`, `created_at`, `updated_at`) VALUES
	(1, 8, '2026-08-31', 11.00, NULL, 'MUKUBWA', '2026-08-30 11:12:42', '2026-08-30 11:12:53');

-- Listage de la structure de table fonctions
CREATE TABLE IF NOT EXISTS `fonctions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fonctions_nom_unique` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table fonctions : ~5 rows (environ)
INSERT INTO `fonctions` (`id`, `nom`, `statut`, `created_at`, `updated_at`) VALUES
	(1, 'Directeur General', 1, '2026-08-29 13:27:30', '2026-08-29 13:27:30'),
	(2, 'Informaticien', 1, '2026-08-30 07:51:28', '2026-08-30 07:51:28'),
	(3, 'Secrétaire', 1, '2026-08-30 07:51:51', '2026-08-30 07:51:51'),
	(4, 'Encodeur', 1, '2026-08-30 07:52:07', '2026-08-30 07:52:07'),
	(5, 'Aviseur', 1, '2026-08-30 11:45:10', '2026-08-30 11:45:10');

-- Listage de la structure de table formations
CREATE TABLE IF NOT EXISTS `formations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organisme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table formations : ~0 rows (environ)
INSERT INTO `formations` (`id`, `intitule`, `organisme`, `lieu`, `date_debut`, `date_fin`, `description`, `statut`, `created_at`, `updated_at`) VALUES
	(2, 'Système d\'Information Géographique', 'Université de Paris', 'Paris', '2026-08-05', '2026-08-20', NULL, 1, '2026-08-30 10:53:31', '2026-08-30 10:54:23');

-- Listage de la structure de table grades
CREATE TABLE IF NOT EXISTS `grades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sigle` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grades_sigle_unique` (`sigle`),
  UNIQUE KEY `grades_nom_unique` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table grades : ~5 rows (environ)
INSERT INTO `grades` (`id`, `sigle`, `nom`, `statut`, `created_at`, `updated_at`) VALUES
	(1, 'DG', 'Administrateur', 1, '2026-08-29 13:27:30', '2026-08-30 11:44:39'),
	(2, 'DIR', 'Directeur', 1, '2026-08-30 07:50:03', '2026-08-30 07:50:03'),
	(3, 'CD', 'Chef de Division', 1, '2026-08-30 07:50:25', '2026-08-30 07:50:25'),
	(4, 'CB', 'Chef des Bureaux', 1, '2026-08-30 07:51:01', '2026-08-30 07:51:01'),
	(5, 'ATA2', 'Assistant attaché', 1, '2026-08-30 07:53:51', '2026-08-30 07:53:51');

-- Listage de la structure de table historiques_connexions
CREATE TABLE IF NOT EXISTS `historiques_connexions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `date_connexion` datetime NOT NULL,
  `adresse_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `navigateur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historiques_connexions_user_id_foreign` (`user_id`),
  CONSTRAINT `historiques_connexions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table historiques_connexions : ~0 rows (environ)

-- Listage de la structure de table horaires_travail
CREATE TABLE IF NOT EXISTS `horaires_travail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned DEFAULT NULL,
  `jour_semaine` int NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `debut_fenetre_pointage` time NOT NULL,
  `fin_fenetre_pointage` time NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `horaires_travail_agent_id_foreign` (`agent_id`),
  CONSTRAINT `horaires_travail_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table horaires_travail : ~0 rows (environ)
INSERT INTO `horaires_travail` (`id`, `agent_id`, `jour_semaine`, `heure_debut`, `heure_fin`, `debut_fenetre_pointage`, `fin_fenetre_pointage`, `actif`, `created_at`, `updated_at`) VALUES
	(1, NULL, 1, '08:00:00', '16:30:00', '07:30:00', '09:00:00', 1, '2026-08-30 10:21:20', '2026-08-30 10:21:20');

-- Listage de la structure de table jours_feries
CREATE TABLE IF NOT EXISTS `jours_feries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table jours_feries : ~0 rows (environ)
INSERT INTO `jours_feries` (`id`, `nom`, `date`, `actif`, `created_at`, `updated_at`) VALUES
	(1, 'Independance', '2026-06-30', 1, '2026-08-30 10:22:07', '2026-08-30 11:49:48');

-- Listage de la structure de table logs
CREATE TABLE IF NOT EXISTS `logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_user_id_foreign` (`user_id`),
  CONSTRAINT `logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table logs : ~50 rows (environ)
INSERT INTO `logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
	(1, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 15:35:10'),
	(2, 1, 'LOGOUT', 'Deconnexion: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 15:44:52'),
	(3, NULL, 'UPDATE', 'Modification de User #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 15:45:09'),
	(4, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 15:45:09'),
	(5, 1, 'CREATE', 'Creation de Permission #4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 15:46:13'),
	(6, 1, 'DELETE', 'Suppression de Permission #3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 15:46:40'),
	(7, 1, 'LOGOUT', 'Deconnexion: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 15:57:49'),
	(8, NULL, 'UPDATE', 'Modification de User #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 15:58:00'),
	(9, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 15:58:00'),
	(10, NULL, 'UPDATE', 'Modification de User #1', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:15:10'),
	(11, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:15:10'),
	(12, NULL, 'UPDATE', 'Modification de User #1', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:15:31'),
	(13, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:15:31'),
	(14, NULL, 'UPDATE', 'Modification de User #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 16:15:51'),
	(15, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 16:15:51'),
	(16, NULL, 'UPDATE', 'Modification de User #1', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:21:32'),
	(17, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:21:32'),
	(18, NULL, 'UPDATE', 'Modification de User #1', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:21:49'),
	(19, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:21:49'),
	(20, NULL, 'UPDATE', 'Modification de User #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 16:25:04'),
	(21, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 16:25:04'),
	(22, 1, 'LOGIN_FAILED', 'Tentative de connexion echouee pour pierrpapy@gmail.com', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:37:40'),
	(23, NULL, 'UPDATE', 'Modification de User #1', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:37:58'),
	(24, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:37:58'),
	(25, NULL, 'UPDATE', 'Modification de User #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 16:40:04'),
	(26, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 16:40:04'),
	(27, NULL, 'UPDATE', 'Modification de User #1', '10.94.226.117', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; fr-FR) WindowsPowerShell/5.1.26100.9168', '2026-08-30 16:44:58'),
	(28, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '10.94.226.117', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; fr-FR) WindowsPowerShell/5.1.26100.9168', '2026-08-30 16:44:58'),
	(29, NULL, 'UPDATE', 'Modification de User #1', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:45:55'),
	(30, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:45:55'),
	(31, NULL, 'UPDATE', 'Modification de User #1', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:50:01'),
	(32, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '10.94.226.168', 'Dart/3.9 (dart:io)', '2026-08-30 16:50:01'),
	(33, NULL, 'UPDATE', 'Modification de User #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 16:50:44'),
	(34, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 16:50:44'),
	(35, 1, 'LOGOUT', 'Deconnexion: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-30 19:57:27'),
	(36, NULL, 'LOGIN_FAILED', 'Tentative de connexion echouee pour famba@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:12:42'),
	(37, NULL, 'UPDATE', 'Modification de User #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:13:00'),
	(38, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:13:00'),
	(39, NULL, 'UPDATE', 'Modification de User #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; fr-FR) WindowsPowerShell/5.1.26100.9168', '2026-08-31 07:13:23'),
	(40, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; fr-FR) WindowsPowerShell/5.1.26100.9168', '2026-08-31 07:13:23'),
	(41, 1, 'LOGOUT', 'Deconnexion: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:31:25'),
	(42, 2, 'LOGIN', 'Connexion reussie: mwamba@test.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; fr-FR) WindowsPowerShell/5.1.26100.9168', '2026-08-31 07:35:48'),
	(43, 3, 'LOGIN', 'Connexion reussie: tambo@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:36:46'),
	(44, 3, 'LOGOUT', 'Deconnexion: tambo@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:36:59'),
	(45, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:37:07'),
	(46, 1, 'UPDATE', 'Modification de User #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:38:53'),
	(47, 1, 'LOGOUT', 'Deconnexion: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:39:22'),
	(48, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:39:42'),
	(49, 1, 'LOGOUT', 'Deconnexion: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:41:31'),
	(50, 2, 'LOGIN', 'Connexion reussie: mwamba@test.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:41:42'),
	(51, 2, 'LOGOUT', 'Deconnexion: mwamba@test.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:155.0) Gecko/20100101 Firefox/155.0', '2026-08-31 07:59:30'),
	(52, 1, 'LOGIN', 'Connexion reussie: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-31 08:01:28'),
	(53, 1, 'LOGOUT', 'Deconnexion: pierrpapy@gmail.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-31 08:01:36');

-- Listage de la structure de table migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table migrations : ~0 rows (environ)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(2, '2024_01_01_000001_create_grades_table', 1),
	(3, '2024_01_01_000002_create_fonctions_table', 1),
	(4, '2024_01_01_000003_create_directions_table', 1),
	(5, '2024_01_01_000004_create_agents_table', 1),
	(6, '2024_01_01_000005_create_types_conges_table', 1),
	(7, '2024_01_01_000006_create_roles_table', 1),
	(8, '2024_01_01_000007_create_droits_table', 1),
	(9, '2024_01_01_000008_create_role_droits_table', 1),
	(10, '2024_01_01_000009_create_users_table', 1),
	(11, '2024_01_01_000010_create_user_roles_table', 1),
	(12, '2024_01_01_000011_create_absences_table', 1),
	(13, '2024_01_01_000012_create_affectations_table', 1),
	(14, '2024_01_01_000013_create_affiliations_table', 1),
	(15, '2024_01_01_000014_create_formations_table', 1),
	(16, '2024_01_01_000015_create_agent_formations_table', 1),
	(17, '2024_01_01_000016_create_conges_table', 1),
	(18, '2024_01_01_000017_create_contrats_table', 1),
	(19, '2024_01_01_000018_create_documents_table', 1),
	(20, '2024_01_01_000019_create_etudes_table', 1),
	(21, '2024_01_01_000020_create_evaluations_table', 1),
	(22, '2024_01_01_000021_create_historiques_connexions_table', 1),
	(23, '2024_01_01_000022_create_horaires_travail_table', 1),
	(24, '2024_01_01_000023_create_jours_feries_table', 1),
	(25, '2024_01_01_000024_create_logs_table', 1),
	(26, '2024_01_01_000025_create_missions_table', 1),
	(27, '2024_01_01_000026_create_notifications_table', 1),
	(28, '2024_01_01_000027_create_permissions_table', 1),
	(29, '2024_01_01_000028_create_zones_travail_table', 1),
	(30, '2024_01_01_000029_create_pointages_table', 1),
	(31, '2024_01_01_000030_create_presences_table', 1),
	(32, '2024_01_01_000031_create_primes_table', 1),
	(33, '2024_01_01_000032_create_promotions_table', 1),
	(34, '2024_01_01_000033_create_retraites_table', 1),
	(35, '2024_01_01_000034_create_sanctions_table', 1),
	(36, '2026_08_30_141834_create_type_conges_table', 2);

-- Listage de la structure de table missions
CREATE TABLE IF NOT EXISTS `missions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_depart` date NOT NULL,
  `date_retour` date DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `missions_agent_id_foreign` (`agent_id`),
  CONSTRAINT `missions_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table missions : ~0 rows (environ)
INSERT INTO `missions` (`id`, `agent_id`, `lieu`, `motif`, `date_depart`, `date_retour`, `reference`, `created_at`, `updated_at`) VALUES
	(1, 3, 'LIKASI', 'Réparation de cable  reseau', '2026-08-19', '2026-09-08', 'REF-1992', '2026-08-30 11:11:50', '2026-08-30 11:12:02');

-- Listage de la structure de table notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  `date_notification` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_agent_id_foreign` (`agent_id`),
  CONSTRAINT `notifications_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table notifications : ~1 rows (environ)
INSERT INTO `notifications` (`id`, `agent_id`, `message`, `lu`, `date_notification`, `created_at`, `updated_at`) VALUES
	(1, 8, 'Tu es attendu au bureau le mardi', 0, '2026-08-30 13:13:39', '2026-08-30 11:13:39', '2026-08-30 11:13:50');

-- Listage de la structure de table permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `date_permission` date NOT NULL,
  `heure_sortie` time NOT NULL,
  `heure_retour` time DEFAULT NULL,
  `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EN_ATTENTE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permissions_agent_id_foreign` (`agent_id`),
  CONSTRAINT `permissions_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table permissions : ~1 rows (environ)
INSERT INTO `permissions` (`id`, `agent_id`, `date_permission`, `heure_sortie`, `heure_retour`, `motif`, `statut`, `created_at`, `updated_at`) VALUES
	(4, 8, '2026-08-08', '17:45:00', NULL, NULL, 'EN_ATTENTE', '2026-08-30 13:46:13', '2026-08-30 13:46:13');

-- Listage de la structure de table personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table personal_access_tokens : ~12 rows (environ)
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(123, 'App\\Models\\User', 1, 'auth-token', '26da5e5abab5b7e5207662ffa8a4f09bca0bef8ab1645dab8d0c925a91e0cb1e', '["*"]', '2026-08-30 14:48:27', NULL, '2026-08-30 14:45:55', '2026-08-30 14:48:27'),
	(124, 'App\\Models\\User', 1, 'auth-token', 'ad3226ff3943f107205c47f68c7da2709a187b04f6815ed3649443caad108773', '["*"]', '2026-08-30 14:52:00', NULL, '2026-08-30 14:50:01', '2026-08-30 14:52:00'),
	(127, 'App\\Models\\User', 1, 'auth-token', '7fd9dd339baee60c241611c0fdc3b9c3914431e92066c06ac05def3d9dcb745c', '["*"]', '2026-08-31 05:13:24', NULL, '2026-08-31 05:13:23', '2026-08-31 05:13:24'),
	(128, 'App\\Models\\User', 3, 'auth-token', '6c919b328af50537961c62a8b7f47f17621b75e847fe3f9c1d0844e2f1197856', '["*"]', NULL, NULL, '2026-08-31 05:31:38', '2026-08-31 05:31:38'),
	(129, 'App\\Models\\User', 3, 'auth-token', '15ac2693cd01106eeb1e9d5c7531f8894226ecd4ca06401285995fe6a0be5353', '["*"]', NULL, NULL, '2026-08-31 05:31:43', '2026-08-31 05:31:43'),
	(130, 'App\\Models\\User', 3, 'auth-token', '9e0b1c6c5f576fbf2a613052026006022b29f11252c11f9a68c460b6e64ca562', '["*"]', NULL, NULL, '2026-08-31 05:32:12', '2026-08-31 05:32:12'),
	(131, 'App\\Models\\User', 3, 'auth-token', 'e5fcdbe9d652000ee8c0bb8f3f794a00bb2826aa1472731fa7658cbc2dd7d2e0', '["*"]', NULL, NULL, '2026-08-31 05:32:14', '2026-08-31 05:32:14'),
	(132, 'App\\Models\\User', 3, 'auth-token', 'cf4144292c0ffc470bc15e3e1980542090a3cce49652d7cbb94791da9f992e95', '["*"]', NULL, NULL, '2026-08-31 05:34:26', '2026-08-31 05:34:26'),
	(133, 'App\\Models\\User', 3, 'auth-token', 'abfbb4a8026cfc8d6ee63d5b12f04e372dd3b55a8ffaaacb9df726369c10129d', '["*"]', NULL, NULL, '2026-08-31 05:35:11', '2026-08-31 05:35:11'),
	(134, 'App\\Models\\User', 2, 'auth-token', '199d7b947eb7cdc1753889c12598ea9f2343458379955e69138444220a3e0bed', '["*"]', NULL, NULL, '2026-08-31 05:35:16', '2026-08-31 05:35:16'),
	(135, 'App\\Models\\User', 2, 'auth-token', '4aa32e5e2a181dda5be43ba3e728e24931d6e2bcf01b5be601b5c65917782395', '["*"]', NULL, NULL, '2026-08-31 05:35:48', '2026-08-31 05:35:48');

-- Listage de la structure de table pointages
CREATE TABLE IF NOT EXISTS `pointages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VALIDE',
  `horodatage` datetime NOT NULL,
  `date_presence` date NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `precision_gps` double DEFAULT NULL,
  `chemin_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `infos_appareil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_appareil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zone_travail_id` bigint unsigned DEFAULT NULL,
  `motif_rejet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `justification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minutes_retard` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pointages_agent_id_foreign` (`agent_id`),
  KEY `pointages_zone_travail_id_foreign` (`zone_travail_id`),
  CONSTRAINT `pointages_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`),
  CONSTRAINT `pointages_zone_travail_id_foreign` FOREIGN KEY (`zone_travail_id`) REFERENCES `zones_travail` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table pointages : ~2 rows (environ)
INSERT INTO `pointages` (`id`, `agent_id`, `type`, `statut`, `horodatage`, `date_presence`, `latitude`, `longitude`, `precision_gps`, `chemin_photo`, `infos_appareil`, `id_appareil`, `adresse_ip`, `zone_travail_id`, `motif_rejet`, `justification`, `minutes_retard`, `created_at`, `updated_at`) VALUES
	(1, 1, 'ARRIVEE', 'RETARD', '2026-08-30 16:22:03', '2026-08-30', -4.36500960, 15.18675650, 100, NULL, 'android', NULL, NULL, NULL, NULL, 'ok', 487, '2026-08-30 14:22:03', '2026-08-30 14:22:03'),
	(2, 1, 'DEPART', 'VALIDE', '2026-08-30 16:51:59', '2026-08-30', -4.36502470, 15.18602530, 100, NULL, 'android', NULL, NULL, 1, NULL, NULL, 0, '2026-08-30 14:51:59', '2026-08-30 14:51:59');

-- Listage de la structure de table presences
CREATE TABLE IF NOT EXISTS `presences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `date_presence` date NOT NULL,
  `heure_arrivee` time DEFAULT NULL,
  `heure_depart` time DEFAULT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PRESENT',
  `observation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `presences_agent_id_foreign` (`agent_id`),
  CONSTRAINT `presences_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table presences : ~0 rows (environ)

-- Listage de la structure de table primes
CREATE TABLE IF NOT EXISTS `primes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `libelle` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` decimal(18,2) NOT NULL,
  `date_prime` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `primes_agent_id_foreign` (`agent_id`),
  CONSTRAINT `primes_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table primes : ~0 rows (environ)
INSERT INTO `primes` (`id`, `agent_id`, `libelle`, `montant`, `date_prime`, `created_at`, `updated_at`) VALUES
	(1, 3, 'prime pour une mission de service', 100.00, '2026-08-19', '2026-08-30 11:06:35', '2026-08-30 11:10:52');

-- Listage de la structure de table promotions
CREATE TABLE IF NOT EXISTS `promotions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `grade_id` bigint unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotions_agent_id_foreign` (`agent_id`),
  KEY `promotions_grade_id_foreign` (`grade_id`),
  CONSTRAINT `promotions_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`),
  CONSTRAINT `promotions_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table promotions : ~0 rows (environ)
INSERT INTO `promotions` (`id`, `agent_id`, `grade_id`, `date_debut`, `date_fin`, `reference`, `created_at`, `updated_at`) VALUES
	(1, 8, 4, '2025-08-13', NULL, 'REF-45211', '2026-08-30 09:20:57', '2026-08-30 09:21:12');

-- Listage de la structure de table retraites
CREATE TABLE IF NOT EXISTS `retraites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `date_retraite` date DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `retraites_agent_id_foreign` (`agent_id`),
  CONSTRAINT `retraites_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table retraites : ~1 rows (environ)
INSERT INTO `retraites` (`id`, `agent_id`, `date_retraite`, `reference`, `observation`, `created_at`, `updated_at`) VALUES
	(2, 3, '2026-11-12', 'REF-4785', 'RAS', '2026-08-30 11:05:48', '2026-08-30 11:06:08');

-- Listage de la structure de table roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nom_role_unique` (`nom_role`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table roles : ~3 rows (environ)
INSERT INTO `roles` (`id`, `nom_role`, `description`, `date_creation`, `created_at`, `updated_at`) VALUES
	(1, 'ADMIN', 'Administrateur avec tous les droits', '2026-08-29 15:28:02', '2026-08-29 13:28:02', '2026-08-29 13:28:02'),
	(2, 'RH', 'Responsable Ressources Humaines', '2026-08-30 13:22:57', '2026-08-30 11:22:57', '2026-08-30 11:22:57'),
	(3, 'AGENT', 'AGENT', '2026-08-30 14:24:22', '2026-08-30 12:24:22', '2026-08-30 12:24:22');

-- Listage de la structure de table role_droits
CREATE TABLE IF NOT EXISTS `role_droits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `droit_id` bigint unsigned NOT NULL,
  `date_attribution` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_droits_role_id_droit_id_unique` (`role_id`,`droit_id`),
  KEY `role_droits_droit_id_foreign` (`droit_id`),
  CONSTRAINT `role_droits_droit_id_foreign` FOREIGN KEY (`droit_id`) REFERENCES `droits` (`id`),
  CONSTRAINT `role_droits_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table role_droits : ~55 rows (environ)
INSERT INTO `role_droits` (`id`, `role_id`, `droit_id`, `date_attribution`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '2026-08-29 15:28:34', NULL, NULL),
	(2, 1, 2, '2026-08-29 15:28:34', NULL, NULL),
	(3, 1, 3, '2026-08-29 15:28:34', NULL, NULL),
	(4, 1, 4, '2026-08-29 15:28:34', NULL, NULL),
	(5, 1, 5, '2026-08-29 15:28:34', NULL, NULL),
	(6, 1, 6, '2026-08-29 15:28:34', NULL, NULL),
	(7, 1, 7, '2026-08-29 15:28:34', NULL, NULL),
	(8, 1, 8, '2026-08-29 15:28:34', NULL, NULL),
	(9, 1, 9, '2026-08-29 15:28:34', NULL, NULL),
	(10, 1, 10, '2026-08-29 15:28:34', NULL, NULL),
	(11, 1, 11, '2026-08-29 15:28:34', NULL, NULL),
	(12, 1, 12, '2026-08-29 15:28:34', NULL, NULL),
	(13, 1, 13, '2026-08-29 15:28:34', NULL, NULL),
	(14, 1, 14, '2026-08-29 15:28:34', NULL, NULL),
	(15, 1, 15, '2026-08-29 15:28:34', NULL, NULL),
	(16, 1, 16, '2026-08-29 15:28:34', NULL, NULL),
	(17, 1, 17, '2026-08-29 15:28:35', NULL, NULL),
	(18, 1, 18, '2026-08-29 15:28:35', NULL, NULL),
	(19, 1, 19, '2026-08-29 15:28:35', NULL, NULL),
	(20, 1, 20, '2026-08-29 15:28:35', NULL, NULL),
	(21, 1, 21, '2026-08-29 15:28:35', NULL, NULL),
	(22, 1, 22, '2026-08-29 15:28:35', NULL, NULL),
	(23, 1, 23, '2026-08-29 15:28:35', NULL, NULL),
	(24, 1, 24, '2026-08-29 15:28:35', NULL, NULL),
	(25, 2, 7, '2026-08-30 13:38:13', NULL, NULL),
	(26, 2, 2, '2026-08-30 13:38:13', NULL, NULL),
	(27, 2, 24, '2026-08-30 13:38:13', NULL, NULL),
	(28, 2, 6, '2026-08-30 13:38:13', NULL, NULL),
	(29, 2, 17, '2026-08-30 13:38:13', NULL, NULL),
	(30, 2, 1, '2026-08-30 13:38:13', NULL, NULL),
	(31, 2, 5, '2026-08-30 13:38:13', NULL, NULL),
	(32, 2, 18, '2026-08-30 13:38:13', NULL, NULL),
	(33, 2, 22, '2026-08-30 13:38:13', NULL, NULL),
	(34, 2, 14, '2026-08-30 13:38:13', NULL, NULL),
	(35, 2, 4, '2026-08-30 13:38:13', NULL, NULL),
	(36, 2, 12, '2026-08-30 13:38:13', NULL, NULL),
	(37, 2, 3, '2026-08-30 13:38:13', NULL, NULL),
	(38, 2, 23, '2026-08-30 13:38:13', NULL, NULL),
	(39, 2, 13, '2026-08-30 13:38:13', NULL, NULL),
	(40, 2, 19, '2026-08-30 13:38:13', NULL, NULL),
	(41, 2, 8, '2026-08-30 13:38:13', NULL, NULL),
	(42, 2, 10, '2026-08-30 13:38:13', NULL, NULL),
	(43, 2, 9, '2026-08-30 13:38:13', NULL, NULL),
	(44, 2, 15, '2026-08-30 13:38:13', NULL, NULL),
	(45, 2, 16, '2026-08-30 13:38:13', NULL, NULL),
	(46, 2, 20, '2026-08-30 13:38:13', NULL, NULL),
	(47, 2, 11, '2026-08-30 13:38:13', NULL, NULL),
	(48, 2, 21, '2026-08-30 13:38:13', NULL, NULL),
	(50, 3, 1, '2026-08-30 15:11:05', NULL, NULL),
	(51, 3, 25, '2026-08-30 15:11:05', NULL, NULL),
	(53, 3, 44, '2026-08-30 15:11:05', '2026-08-30 14:51:02', '2026-08-30 14:51:02'),
	(54, 3, 6, '2026-08-30 15:11:05', '2026-08-30 14:57:44', '2026-08-30 14:57:44'),
	(56, 3, 19, '2026-08-30 15:11:05', '2026-08-30 14:57:44', '2026-08-30 14:57:44'),
	(59, 3, 47, '2026-08-30 15:11:05', '2026-08-30 15:07:45', '2026-08-30 15:07:45'),
	(60, 3, 46, '2026-08-30 15:11:05', NULL, NULL);

-- Listage de la structure de table sanctions
CREATE TABLE IF NOT EXISTS `sanctions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `type_sanction` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_sanction` date DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sanctions_agent_id_foreign` (`agent_id`),
  CONSTRAINT `sanctions_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table sanctions : ~1 rows (environ)
INSERT INTO `sanctions` (`id`, `agent_id`, `type_sanction`, `motif`, `date_sanction`, `reference`, `created_at`, `updated_at`) VALUES
	(1, 8, 'AVERTISSEMENT', 'Bagare', '2026-08-23', NULL, '2026-08-30 10:48:30', '2026-08-30 10:48:57');

-- Listage de la structure de table types_conges
CREATE TABLE IF NOT EXISTS `types_conges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_jours` int NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table types_conges : ~5 rows (environ)
INSERT INTO `types_conges` (`id`, `nom`, `nombre_jours`, `description`, `statut`, `created_at`, `updated_at`) VALUES
	(1, 'Conge annuel', 30, 'Conge annuel paye', 1, '2026-08-30 15:18:27', '2026-08-30 15:18:27'),
	(2, 'Conge maladie', 15, 'Conge pour raison de sante', 1, '2026-08-30 15:18:27', '2026-08-30 15:18:27'),
	(3, 'Conge maternite', 90, 'Conge de maternite', 1, '2026-08-30 15:18:27', '2026-08-30 15:18:27'),
	(4, 'Conge paternite', 15, 'Conge de paternite', 1, '2026-08-30 15:18:27', '2026-08-30 15:18:27'),
	(5, 'Conge sans solde', 30, 'Conge non remunere', 1, '2026-08-30 15:18:27', '2026-08-30 15:18:27');

-- Listage de la structure de table type_conges
CREATE TABLE IF NOT EXISTS `type_conges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duree_max_jours` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_conges_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table type_conges : ~5 rows (environ)
INSERT INTO `type_conges` (`id`, `libelle`, `code`, `duree_max_jours`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'Conge annuel', 'ANN', 30, 'Conge annuel paye', '2026-08-30 12:19:20', '2026-08-30 12:19:20'),
	(2, 'Conge maladie', 'MAL', 15, 'Conge pour raison de sante', '2026-08-30 12:19:20', '2026-08-30 12:19:20'),
	(3, 'Conge maternite', 'MAT', 90, 'Conge de maternite', '2026-08-30 12:19:20', '2026-08-30 12:19:20'),
	(4, 'Conge paternite', 'PAT', 15, 'Conge de paternite', '2026-08-30 12:19:20', '2026-08-30 12:19:20'),
	(5, 'Conge sans solde', 'CSS', 30, 'Conge non remunere', '2026-08-30 12:19:20', '2026-08-30 12:19:20');

-- Listage de la structure de table users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` bigint unsigned NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_agent_id_unique` (`agent_id`),
  UNIQUE KEY `users_username_unique` (`username`),
  CONSTRAINT `users_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table users : ~3 rows (environ)
INSERT INTO `users` (`id`, `agent_id`, `username`, `password_hash`, `actif`, `last_login`, `date_creation`, `created_at`, `updated_at`) VALUES
	(1, 1, 'pierrpapy@gmail.com', '$2y$12$lCELM1QpXkZl5OhvY48iMuYqN21e2100i1Tq7tkUcC5rJCu1rtae2', 1, '2026-08-31 08:01:28', '2026-08-29 15:28:35', '2026-08-29 13:28:35', '2026-08-31 06:01:28'),
	(2, 3, 'mwamba@test.com', '$2y$12$R1P2.97P7jb9oWOFC/VemOJqZNE4xsPo7VhjNITDJBJH3F9FN/9fG', 1, '2026-08-31 07:41:42', '2026-08-30 13:28:39', '2026-08-30 11:28:40', '2026-08-31 05:41:42'),
	(3, 10, 'tambo@gmail.com', '$2y$12$PC6.zrlUgzarHvyLR0FyWeVIDLvXBesMYHndXA24AB.XKhRrLdnJC', 1, '2026-08-31 07:36:46', '2026-08-30 14:38:43', '2026-08-30 12:38:43', '2026-08-31 05:36:46');

-- Listage de la structure de table user_roles
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `date_attribution` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  KEY `user_roles_role_id_foreign` (`role_id`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table user_roles : ~3 rows (environ)
INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `date_attribution`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '2026-08-29 15:28:35', NULL, NULL),
	(2, 2, 2, '2026-08-30 13:28:40', NULL, NULL),
	(3, 3, 3, '2026-08-30 14:38:43', NULL, NULL);

-- Listage de la structure de table zones_travail
CREATE TABLE IF NOT EXISTS `zones_travail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `rayon` int NOT NULL DEFAULT '100',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table zones_travail : ~0 rows (environ)
INSERT INTO `zones_travail` (`id`, `nom`, `adresse`, `latitude`, `longitude`, `rayon`, `actif`, `created_at`, `updated_at`) VALUES
	(1, 'MBUDI', 'Mengi 45', -4.32000000, 15.31000000, 17000, 1, '2026-08-30 10:21:51', '2026-08-30 14:51:49');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

SET FOREIGN_KEY_CHECKS=1;
