-- Users table for authentication
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) UNIQUE NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') DEFAULT 'user',
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sites table for storing monitored websites
CREATE TABLE IF NOT EXISTS `sites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `check_interval` int NOT NULL DEFAULT '5',
  `ssl_check_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `ssl_check_interval` int NOT NULL DEFAULT '86400',
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Monitoring results table
CREATE TABLE IF NOT EXISTS `monitoring_results` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `site_id` INT NOT NULL,
    `check_type` ENUM('uptime', 'ssl') NOT NULL,
    `status` ENUM('up', 'down', 'warning') NOT NULL,
    `response_time` INT, -- milliseconds
    `status_code` INT,
    `ssl_expiry_date` DATE,
    `error_message` TEXT,
    `checked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`site_id`) REFERENCES `sites`(`id`) ON DELETE CASCADE
);

-- Sessions table for managing user sessions
CREATE TABLE IF NOT EXISTS `user_sessions` (
    `id` VARCHAR(128) PRIMARY KEY,
    `user_id` INT NOT NULL,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO `users` (username, email, password_hash, role) VALUES 
('admin', 'admin@example.com', '$2y$10$oYt8iJYGlNwQYa1folUdHu/Z3WtIOyhJhoJIjQGny3lKZHvbHxVOm', 'admin')
ON DUPLICATE KEY UPDATE email = email; -- Do nothing if user already exists
