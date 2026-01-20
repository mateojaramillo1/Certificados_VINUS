-- Script completo para crear/actualizar la base de datos con soporte de empresas
-- Ejecutar como usuario root

CREATE DATABASE IF NOT EXISTS `certificados vinus` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `certificados vinus`;

-- Crear usuario y conceder permisos
CREATE USER IF NOT EXISTS 'cert_user'@'localhost' IDENTIFIED BY 's9P@x7Kz!4BqR2vWm6Ld';
GRANT ALL PRIVILEGES ON `certificados vinus`.* TO 'cert_user'@'localhost';
FLUSH PRIVILEGES;

-- Tabla empresas
CREATE TABLE IF NOT EXISTS empresas (
  id_empresa INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_empresa VARCHAR(255) NOT NULL,
  nit VARCHAR(64) NOT NULL,
  direccion VARCHAR(255) DEFAULT NULL,
  telefono VARCHAR(64) DEFAULT NULL,
  email VARCHAR(128) DEFAULT NULL,
  representante_legal VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_empresa),
  UNIQUE KEY ux_empresas_nit (nit)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla empleados (actualizada con relación a empresas)
CREATE TABLE IF NOT EXISTS empleados (
  id_empleados INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_empresa INT UNSIGNED NOT NULL,
  numero_documento VARCHAR(64) NOT NULL,
  nombre_completo VARCHAR(255) NOT NULL,
  cargo VARCHAR(128) DEFAULT NULL,
  tipo_contrato VARCHAR(128) DEFAULT 'Término Indefinido',
  salario_basico DECIMAL(12,2) DEFAULT NULL,
  fecha_ingreso DATE DEFAULT NULL,
  fecha_retiro DATE DEFAULT NULL,
  estado VARCHAR(32) DEFAULT 'Activo',
  is_admin TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_empleados),
  UNIQUE KEY ux_empleados_documento (numero_documento),
  KEY idx_empresa (id_empresa),
  CONSTRAINT fk_empleados_empresa FOREIGN KEY (id_empresa) REFERENCES empresas(id_empresa) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla plantillas (si no existe)
CREATE TABLE IF NOT EXISTS plantillas (
  id_plantilla INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT DEFAULT NULL,
  tipo_documento VARCHAR(64) DEFAULT 'Certificado Laboral',
  ruta_archivo VARCHAR(512) NOT NULL,
  activa TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_plantilla)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar empresa de ejemplo
INSERT INTO empresas (nombre_empresa, nit, direccion, telefono, email, representante_legal) VALUES
('VINUS S.A.S', '900123456-7', 'Calle 123 #45-67, Bogotá', '601-2345678', 'contacto@vinus.com.co', 'Juan Carlos Administrador')
ON DUPLICATE KEY UPDATE nombre_empresa = nombre_empresa;

-- Insertar empleado administrador de ejemplo
INSERT INTO empleados (id_empresa, numero_documento, nombre_completo, cargo, tipo_contrato, salario_basico, fecha_ingreso, estado, is_admin) VALUES
(1, '1234567890', 'Admin Sistema', 'Administrador', 'Término Indefinido', 5000000, '2024-01-01', 'Activo', 1)
ON DUPLICATE KEY UPDATE numero_documento = numero_documento;

-- Verificar estructura
SHOW TABLES;
SELECT 'Empresas registradas:' as Info;
SELECT * FROM empresas;
SELECT 'Empleados registrados:' as Info;
SELECT id_empleados, numero_documento, nombre_completo, cargo, estado FROM empleados;
