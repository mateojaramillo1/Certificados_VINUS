# Instrucciones para configurar la base de datos con soporte de empresas

## 1. Ejecutar el script SQL

Abre XAMPP Control Panel y asegúrate de que MySQL esté corriendo, luego ejecuta:

```bash
# Opción 1: Desde línea de comandos
C:\xampp\mysql\bin\mysql.exe -u root < crear_bd_completa.sql

# Opción 2: Desde phpMyAdmin
# 1. Abre http://localhost/phpmyadmin
# 2. Clic en "SQL" en el menú superior
# 3. Copia y pega el contenido de crear_bd_completa.sql
# 4. Clic en "Continuar"
```

## 2. Estructura de la base de datos

### Tabla: empresas
- `id_empresa` (PK, AUTO_INCREMENT)
- `nombre_empresa` (VARCHAR 255)
- `nit` (VARCHAR 64, UNIQUE)
- `direccion`, `telefono`, `email`
- `representante_legal`
- `created_at`, `updated_at`

### Tabla: empleados
- `id_empleados` (PK, AUTO_INCREMENT)
- `id_empresa` (FK → empresas.id_empresa)
- `numero_documento` (VARCHAR 64, UNIQUE)
- `nombre_completo` (VARCHAR 255)
- `cargo`, `tipo_contrato`
- `salario_basico` (DECIMAL 12,2)
- `fecha_ingreso`, `fecha_retiro`
- `estado` (VARCHAR 32)
- `is_admin` (TINYINT 1)
- `created_at`, `updated_at`

## 3. Datos de prueba

El script crea automáticamente:
- **Empresa:** VINUS S.A.S (NIT: 900123456-7)
- **Usuario Admin:** 
  - Documento: 1234567890
  - Contraseña: 1234567890 (mismo documento)
  - Es administrador: Sí

## 4. Funcionalidad del registro

El formulario de registro ahora incluye:
- Campo de selección de empresa (obligatorio)
- Información completa del empleado
- La contraseña inicial es el número de documento

## 5. Modelos actualizados

- **Empresa.php**: Modelo para gestionar empresas
  - `getAll()`: Obtener todas las empresas
  - `findById($id)`: Buscar por ID
  - `findByNit($nit)`: Buscar por NIT
  - `create($data)`: Crear nueva empresa

- **Empleado.php**: Modelo actualizado con:
  - `authenticate()`: Autenticación
  - `findById()`: Buscar por ID
  - `findByDocumento()`: Buscar por documento
  - `create($data)`: Crear nuevo empleado con empresa

## 6. Próximos pasos

Para usar el sistema:
1. Ejecuta el script SQL
2. Abre http://localhost/certificados/
3. Registra un nuevo usuario seleccionando la empresa
4. Inicia sesión con tu número de documento
