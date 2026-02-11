-- Crear tablas de historial

CREATE TABLE IF NOT EXISTS historial_accesos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    numero_documento VARCHAR(50) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_historial_accesos_empleado (id_empleado),
    INDEX idx_historial_accesos_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS historial_certificados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    numero_documento VARCHAR(50) NOT NULL,
    incluir_salario TINYINT(1) NOT NULL DEFAULT 0,
    tipo VARCHAR(20) NOT NULL DEFAULT 'pdf',
    generado_por INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_historial_cert_empleado (id_empleado),
    INDEX idx_historial_cert_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
