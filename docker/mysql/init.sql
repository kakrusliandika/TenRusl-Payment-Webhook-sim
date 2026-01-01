-- Optional bootstrap. Aman walau MYSQL_DATABASE/MYSQL_USER sudah diset.
-- (Script ini dieksekusi saat init data-dir pertama kali.)

CREATE DATABASE IF NOT EXISTS `tenrusl_webhook`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'tenrusl'@'%' IDENTIFIED BY 'secret';

GRANT ALL PRIVILEGES ON `tenrusl_webhook`.* TO 'tenrusl'@'%';
FLUSH PRIVILEGES;
