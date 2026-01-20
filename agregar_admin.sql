-- Script para agregar campo de administrador
-- Ejecutar como usuario root o cert_user con permisos ALTER

USE certificados;

-- Agregar columna is_admin si no existe
ALTER TABLE empleados 
ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) DEFAULT 0 AFTER password;

-- Hacer que el primer empleado sea administrador (ajusta según tu necesidad)
-- Puedes cambiar 'V12345678' por la cédula del empleado que quieres hacer admin
UPDATE empleados SET is_admin = 1 WHERE cedula = 'V12345678' LIMIT 1;

-- Verificar estructura actualizada
DESCRIBE empleados;

-- Verificar administradores
SELECT id, nombre, cedula, is_admin FROM empleados WHERE is_admin = 1;
