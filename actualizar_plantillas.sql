-- Tabla para gestionar plantillas de certificados en PDF
CREATE TABLE IF NOT EXISTS plantillas_pdf (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    archivo VARCHAR(255) NOT NULL,
    activa TINYINT(1) DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para almacenar configuración de campos en el PDF
CREATE TABLE IF NOT EXISTS plantilla_campos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plantilla_id INT NOT NULL,
    campo VARCHAR(100) NOT NULL COMMENT 'nombre, cedula, cargo, fecha_ingreso, etc.',
    pos_x FLOAT NOT NULL,
    pos_y FLOAT NOT NULL,
    fuente VARCHAR(50) DEFAULT 'Arial',
    tamano_fuente INT DEFAULT 12,
    color VARCHAR(7) DEFAULT '#000000',
    ancho_max FLOAT DEFAULT NULL,
    alineacion VARCHAR(10) DEFAULT 'L' COMMENT 'L=left, C=center, R=right',
    FOREIGN KEY (plantilla_id) REFERENCES plantillas_pdf(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices
CREATE INDEX idx_plantilla_activa ON plantillas_pdf(activa);
CREATE INDEX idx_campo_plantilla ON plantilla_campos(plantilla_id);
