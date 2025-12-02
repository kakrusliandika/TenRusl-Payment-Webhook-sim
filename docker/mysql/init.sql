-- Inisialisasi database & user (opsional, karena environment mysql container
-- biasanya sudah membuat DB & user bila MYSQL_DATABASE/MYSQL_USER diset)

CREATE DATABASE IF NOT EXISTS `tenrusl_webhook`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Pastikan user ada (aman walau sudah dibuat oleh entrypoint)
CREATE USER IF NOT EXISTS 'tenrusl'@'%' IDENTIFIED BY 'secret';

GRANT ALL PRIVILEGES ON `tenrusl_webhook`.* TO 'tenrusl'@'%';
FLUSH PRIVILEGES;

-- Opsional: set timezone UTC (biasa dipakai untuk konsistensi timestamp)
SET GLOBAL time_zone = '+00:00';
