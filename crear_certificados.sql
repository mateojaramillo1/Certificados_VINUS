-- Crear base de datos y usarla
CREATE DATABASE IF NOT EXISTS certificados CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE certificados;

-- Crear usuario y conceder permisos — ejecuta esto como usuario root
-- Cambia 'tu_password_segura' por una contraseña segura antes de ejecutar
CREATE USER IF NOT EXISTS 'cert_user'@'localhost' IDENTIFIED BY 's9P@x7Kz!4BqR2vWm6Ld';
GRANT ALL PRIVILEGES ON certificados.* TO 'cert_user'@'localhost';
FLUSH PRIVILEGES;

-- Tabla empleados
CREATE TABLE IF NOT EXISTS empleados (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  cedula VARCHAR(64) NOT NULL,
  password VARCHAR(255) DEFAULT NULL,
  cargo VARCHAR(128) DEFAULT NULL,
  fecha_ingreso DATE DEFAULT NULL,
  fecha_retiro DATE DEFAULT NULL,
  tipo_contrato VARCHAR(128) DEFAULT 'a término indefinido',
  salario DECIMAL(12,2) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY ux_empleados_cedula (cedula)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registros de ejemplo
INSERT INTO empleados (nombre, cedula, cargo, fecha_ingreso, tipo_contrato, salario) VALUES
('María Pérez', 'V12345678', 'Administrativa', '2018-04-15', 'a término indefinido', 2500000),
('Juan Gómez', 'E87654321', 'Ingeniero', '2020-09-01', 'a término indefinido', 4500000);
