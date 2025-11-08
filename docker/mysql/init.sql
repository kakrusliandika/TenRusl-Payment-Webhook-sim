-- Inisialisasi database & user (opsional, environment sudah membuat DB & user)
CREATE DATABASE IF NOT EXISTS `tenrusl_webhook` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Pastikan user ada (jika environment tidak berhasil membuat)
CREATE USER IF NOT EXISTS 'tenrusl'@'%' IDENTIFIED BY 'secret';

GRANT ALL PRIVILEGES ON `tenrusl_webhook`.* TO 'tenrusl'@'%';
FLUSH PRIVILEGES;

-- Opsional: set timezone & mode yang ramah Laravel
SET GLOBAL time_zone = '+00:00';
