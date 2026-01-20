-- Script para actualizar la base de datos existente
-- Ejecutar como usuario root o cert_user con permisos ALTER

USE certificados;

-- Agregar columna salario si no existe
ALTER TABLE empleados 
ADD COLUMN IF NOT EXISTS salario DECIMAL(12,2) DEFAULT NULL AFTER cargo;

-- Agregar columna tipo_contrato si no existe
ALTER TABLE empleados 
ADD COLUMN IF NOT EXISTS tipo_contrato VARCHAR(128) DEFAULT 'a término indefinido' AFTER salario;

-- Agregar columna fecha_retiro si no existe
ALTER TABLE empleados 
ADD COLUMN IF NOT EXISTS fecha_retiro DATE DEFAULT NULL AFTER fecha_ingreso;

-- Actualizar datos de ejemplo (opcional - ajusta según tus empleados reales)
UPDATE empleados SET salario = 2500000, tipo_contrato = 'a término indefinido' WHERE cedula = 'V12345678';
UPDATE empleados SET salario = 4500000, tipo_contrato = 'a término indefinido' WHERE cedula = 'E87654321';

-- Verificar estructura actualizada
DESCRIBE empleados;
