-- TABLAS BASE (sin dependencias)
CREATE TABLE `roles` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `positions` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `jurisdictions` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `activity_types` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `death_causes` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `death_locations` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `usage_count` int DEFAULT 0,
  `is_active` boolean DEFAULT true,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

-- TABLAS CON DEPENDENCIAS NIVEL 1
CREATE TABLE `municipalities` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `jurisdiction_id` int NOT NULL,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `users` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
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
  `position_id` int NOT NULL,
  `jurisdiction_id` int NOT NULL,
  `role_id` int NOT NULL,
  `remember_token` varchar(255),
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

-- TABLAS CON DEPENDENCIAS NIVEL 2
CREATE TABLE `publications` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `publication_type` varchar(255) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `description` text,
  `publication_date` date NOT NULL,
  `activity_date` date NOT NULL,
  `status` varchar(255) DEFAULT 'borrador',
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `deaths` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `first_last_name` varchar(255) NOT NULL,
  `second_last_name` varchar(255),
  `age` int NOT NULL,
  `sex` enum('M', 'F', 'Otro') NOT NULL,
  `death_date` date NOT NULL,
  `residence_municipality_id` int NOT NULL,
  `jurisdiction_id` int NOT NULL,
  `death_municipality_id` int NOT NULL,
  `death_location_id` int NOT NULL,
  `death_cause_id` int NOT NULL,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

-- TABLAS CON DEPENDENCIAS NIVEL 3
CREATE TABLE `publication_files` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `publication_id` int NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_size` bigint NOT NULL,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `road_safety_reports` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `publication_id` int NOT NULL,
  `activity_type_id` int NOT NULL,
  `participants` int NOT NULL,
  `location` varchar(255) NOT NULL,
  `promoter` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `injury_observatory_reports` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `publication_id` int NOT NULL,
  `municipality_id` int NOT NULL,
  `jurisdiction_id` int NOT NULL,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `breathalyzer_reports` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `publication_id` int NOT NULL,
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
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `publication_comments` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `publication_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment` text NOT NULL,
  `seen` boolean DEFAULT false,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

CREATE TABLE `notifications` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `recipient_user_id` int NOT NULL,
  `sender_user_id` int,
  `publication_id` int,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `read` boolean DEFAULT false,
  `created_at` timestamp DEFAULT (now()),
  `updated_at` timestamp DEFAULT (now())
);

-- FOREIGN KEYS
ALTER TABLE `municipalities` ADD FOREIGN KEY (`jurisdiction_id`) REFERENCES `jurisdictions` (`id`);

ALTER TABLE `users` ADD FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`);
ALTER TABLE `users` ADD FOREIGN KEY (`jurisdiction_id`) REFERENCES `jurisdictions` (`id`);
ALTER TABLE `users` ADD FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

ALTER TABLE `publications` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `deaths` ADD FOREIGN KEY (`residence_municipality_id`) REFERENCES `municipalities` (`id`);
ALTER TABLE `deaths` ADD FOREIGN KEY (`jurisdiction_id`) REFERENCES `jurisdictions` (`id`);
ALTER TABLE `deaths` ADD FOREIGN KEY (`death_municipality_id`) REFERENCES `municipalities` (`id`);
ALTER TABLE `deaths` ADD FOREIGN KEY (`death_location_id`) REFERENCES `death_locations` (`id`);
ALTER TABLE `deaths` ADD FOREIGN KEY (`death_cause_id`) REFERENCES `death_causes` (`id`);

ALTER TABLE `publication_files` ADD FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`);

ALTER TABLE `road_safety_reports` ADD FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`);
ALTER TABLE `road_safety_reports` ADD FOREIGN KEY (`activity_type_id`) REFERENCES `activity_types` (`id`);

ALTER TABLE `injury_observatory_reports` ADD FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`);
ALTER TABLE `injury_observatory_reports` ADD FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`);
ALTER TABLE `injury_observatory_reports` ADD FOREIGN KEY (`jurisdiction_id`) REFERENCES `jurisdictions` (`id`);

ALTER TABLE `breathalyzer_reports` ADD FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`);

ALTER TABLE `publication_comments` ADD FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`);
ALTER TABLE `publication_comments` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `notifications` ADD FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`);
ALTER TABLE `notifications` ADD FOREIGN KEY (`sender_user_id`) REFERENCES `users` (`id`);
ALTER TABLE `notifications` ADD FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`);

-- INDICES PARA MEJORAR RENDIMIENTO
CREATE INDEX idx_publications_user ON publications(user_id);
CREATE INDEX idx_publications_date ON publications(publication_date);
CREATE INDEX idx_publications_status ON publications(status);
CREATE INDEX idx_deaths_date ON deaths(death_date);
CREATE INDEX idx_deaths_municipality ON deaths(death_municipality_id);
CREATE INDEX idx_notifications_recipient ON notifications(recipient_user_id);
CREATE INDEX idx_notifications_read ON notifications(`read`);
CREATE INDEX idx_comments_publication ON publication_comments(publication_id);
CREATE INDEX idx_files_publication ON publication_files(publication_id);

-- VALIDACIONES CHECK CONSTRAINTS
ALTER TABLE deaths ADD CONSTRAINT check_age CHECK (age >= 0 AND age <= 150);
ALTER TABLE publications ADD CONSTRAINT check_status CHECK (status IN ('borrador', 'publicado', 'archivado', 'revision'));
ALTER TABLE publication_files ADD CONSTRAINT check_file_size CHECK (file_size > 0);
ALTER TABLE breathalyzer_reports ADD CONSTRAINT check_stats_positive CHECK (checkpoints >= 0 AND tests_performed >= 0 AND drivers_not_fit >= 0);
ALTER TABLE breathalyzer_reports ADD CONSTRAINT check_gender_positive CHECK (women >= 0 AND men >= 0);
ALTER TABLE road_safety_reports ADD CONSTRAINT check_participants_positive CHECK (participants > 0);