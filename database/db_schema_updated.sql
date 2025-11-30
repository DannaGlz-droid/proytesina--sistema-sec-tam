-- ===============================================
-- SCHEMA ACTUALIZADO - Sistema SEC-TAM
-- Base de datos: MySQL
-- Última actualización: basado en migraciones hasta nov 2025
-- ===============================================

-- TABLAS BASE (sin dependencias)
CREATE TABLE `roles` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `positions` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jurisdictions` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `activity_types` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `death_causes` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `death_locations` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `usage_count` int DEFAULT 0,
  `is_active` boolean DEFAULT true,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLAS DEL SISTEMA LARAVEL
CREATE TABLE `cache` (
  `key` varchar(255) PRIMARY KEY,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) PRIMARY KEY,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `uuid` varchar(255) UNIQUE NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) PRIMARY KEY,
  `user_id` bigint UNSIGNED NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  INDEX `sessions_user_id_index` (`user_id`),
  INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) PRIMARY KEY,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLA DE IMPORTACIONES (maatwebsite/excel)
CREATE TABLE `imports` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `completed_at` timestamp NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `importer` varchar(255) NOT NULL,
  `processed_rows` int UNSIGNED NOT NULL DEFAULT 0,
  `total_rows` int UNSIGNED NOT NULL,
  `successful_rows` int UNSIGNED NOT NULL DEFAULT 0,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLAS CON DEPENDENCIAS NIVEL 1
CREATE TABLE `municipalities` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `jurisdiction_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`jurisdiction_id`) REFERENCES `jurisdictions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `first_last_name` varchar(255) NOT NULL,
  `second_last_name` varchar(255),
  `email` varchar(255) UNIQUE NOT NULL,
  `phone` varchar(20),
  `username` varchar(255) UNIQUE NOT NULL,
  `email_verified_at` timestamp NULL,
  `password` varchar(512) NOT NULL,
  `is_active` boolean DEFAULT true,
  `registration_date` date,
  `last_session` datetime,
  `timezone` varchar(255) DEFAULT 'America/Mexico_City',
  `position_id` bigint UNSIGNED NOT NULL,
  `jurisdiction_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `remember_token` varchar(255),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`jurisdiction_id`) REFERENCES `jurisdictions` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLAS CON DEPENDENCIAS NIVEL 2
CREATE TABLE `publications` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `publication_type` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `description` text,
  `publication_date` date NOT NULL,
  `activity_date` date NOT NULL,
  `status` varchar(255) DEFAULT 'borrador',
  `approved_by` bigint UNSIGNED NULL,
  `approved_at` timestamp NULL,
  `rejected_by` bigint UNSIGNED NULL,
  `rejected_at` timestamp NULL,
  `rejection_reason` text NULL,
  `deleted_at` timestamp NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_publications_user` (`user_id`),
  INDEX `idx_publications_date` (`publication_date`),
  INDEX `idx_publications_status` (`status`),
  INDEX `publications_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `deaths` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `first_last_name` varchar(255) NOT NULL,
  `second_last_name` varchar(255),
  `age` int NOT NULL,
  `age_years` int UNSIGNED NULL,
  `age_months` int UNSIGNED NULL,
  `sex` enum('M', 'F', 'Otro') NOT NULL,
  `death_date` date NOT NULL,
  `gov_folio` varchar(255) NULL,
  `residence_municipality_id` bigint UNSIGNED NOT NULL,
  `jurisdiction_id` bigint UNSIGNED NOT NULL,
  `death_municipality_id` bigint UNSIGNED NOT NULL,
  `death_location_id` bigint UNSIGNED NOT NULL,
  `death_cause_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`residence_municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`jurisdiction_id`) REFERENCES `jurisdictions` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`death_municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`death_location_id`) REFERENCES `death_locations` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`death_cause_id`) REFERENCES `death_causes` (`id`) ON DELETE CASCADE,
  INDEX `idx_deaths_date` (`death_date`),
  INDEX `idx_deaths_municipality` (`death_municipality_id`),
  CONSTRAINT `check_age` CHECK (`age` >= 0 AND `age` <= 150)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLAS CON DEPENDENCIAS NIVEL 3
CREATE TABLE `publication_files` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `publication_id` bigint UNSIGNED NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_size` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  INDEX `idx_files_publication` (`publication_id`),
  CONSTRAINT `check_file_size` CHECK (`file_size` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `road_safety_reports` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `publication_id` bigint UNSIGNED NOT NULL,
  `activity_type_id` bigint UNSIGNED NOT NULL,
  `participants` int NOT NULL,
  `location` varchar(255) NOT NULL,
  `promoter` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`activity_type_id`) REFERENCES `activity_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `check_participants_positive` CHECK (`participants` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `injury_observatory_reports` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `publication_id` bigint UNSIGNED NOT NULL,
  `municipality_id` bigint UNSIGNED NOT NULL,
  `jurisdiction_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`jurisdiction_id`) REFERENCES `jurisdictions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `breathalyzer_reports` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `publication_id` bigint UNSIGNED NOT NULL,
  `checkpoints` int NOT NULL,
  `tests_performed` int NOT NULL,
  `drivers_not_fit` int NOT NULL,
  `women` int NOT NULL,
  `men` int NOT NULL,
  `cars_trucks` int NOT NULL,
  `motorcycles` int NOT NULL,
  `public_transport_collective` int NOT NULL,
  `public_transport_individual` int NOT NULL,
  `cargo_transport` int NOT NULL,
  `emergency_vehicles` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `check_stats_positive` CHECK (`checkpoints` >= 0 AND `tests_performed` >= 0 AND `drivers_not_fit` >= 0),
  CONSTRAINT `check_gender_positive` CHECK (`women` >= 0 AND `men` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `publication_comments` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `publication_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `seen` boolean DEFAULT false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_comments_publication` (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comment_reads` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `publication_comment_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `seen_at` timestamp NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`publication_comment_id`) REFERENCES `publication_comments` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `comment_reads_unique` (`publication_comment_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `recipient_user_id` bigint UNSIGNED NOT NULL,
  `sender_user_id` bigint UNSIGNED NULL,
  `publication_id` bigint UNSIGNED NULL,
  `comment_id` bigint UNSIGNED NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `read` boolean DEFAULT false,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`sender_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE,
  INDEX `idx_notifications_recipient` (`recipient_user_id`),
  INDEX `idx_notifications_read` (`read`),
  INDEX `notifications_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `publication_audits` (
  `id` bigint UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `publication_id` bigint UNSIGNED NOT NULL,
  `action` varchar(32) NOT NULL,
  `actor_user_id` bigint UNSIGNED NULL,
  `reason` text NULL,
  `snapshot` json NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `publication_audits_publication_id_index` (`publication_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- NOTAS SOBRE EL SCHEMA:
-- ===============================================
-- 1. Este schema incluye:
--    - Soft deletes en tabla publications (deleted_at)
--    - Campos de aprobación/rechazo en publications (approved_by, rejected_by, etc.)
--    - Tabla publication_audits para auditoría de cambios
--    - Tabla comment_reads para tracking de lectura de comentarios
--    - Campos age_years y age_months en deaths para mayor precisión
--    - Campo gov_folio en deaths para folio gubernamental
--    - Campo description en roles
--    - Campo timezone en users
--    - Campo comment_id en notifications
--    - Tabla imports para importaciones Excel/CSV
--
-- 2. Todas las foreign keys incluyen ON DELETE CASCADE o SET NULL según corresponda
--
-- 3. Se preservan índices para optimización de consultas
--
-- 4. Se incluyen CHECK constraints para validaciones a nivel de BD
--
-- 5. Charset y collation: utf8mb4_unicode_ci para soporte completo de caracteres
--
-- 6. Engine: InnoDB para soporte de transacciones y foreign keys
