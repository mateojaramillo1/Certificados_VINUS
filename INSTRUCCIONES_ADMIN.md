# Configuración de Administradores

## Paso 1: Ejecutar el script SQL

Debes ejecutar el script `agregar_admin.sql` para agregar el campo `is_admin` a la base de datos.

### Opción A: Usando phpMyAdmin (XAMPP)

1. Abre tu navegador y ve a: `http://localhost/phpmyadmin`
2. Selecciona la base de datos `certificados` en el panel izquierdo
3. Haz clic en la pestaña "SQL" en la parte superior
4. Abre el archivo `agregar_admin.sql` con un editor de texto
5. Copia y pega todo el contenido en el cuadro de texto
6. Haz clic en el botón "Continuar" o "Go"

### Opción B: Usando línea de comandos

```bash
# Desde el directorio raíz del proyecto
mysql -u cert_user -p certificados < agregar_admin.sql
# Ingresa la contraseña: s9P@x7Kz!4BqR2vWm6Ld
```

## Paso 2: Configurar el primer administrador

El script automáticamente hace administrador al empleado con cédula `V12345678`.

Si quieres hacer administrador a otro empleado:

```sql
-- Reemplaza 'TU_CEDULA_AQUI' con la cédula del empleado
UPDATE empleados SET is_admin = 1 WHERE cedula = 'TU_CEDULA_AQUI';
```

## Verificar que todo funcionó

Puedes verificar qué empleados son administradores ejecutando:

```sql
SELECT id, nombre, cedula, cargo, is_admin FROM empleados;
```

Los empleados con `is_admin = 1` son administradores.

## Cambios implementados

### 1. Base de datos
- Se agregó el campo `is_admin` a la tabla `empleados`
- Valor por defecto: `0` (no es admin)
- Valor `1` significa que es administrador

### 2. Modelo Empleado
- Se agregó la propiedad `$is_admin`
- Se actualiza en todas las consultas SQL

### 3. Controlador de Autenticación
- Al hacer login, se guarda `$_SESSION['is_admin']` con el valor del usuario

### 4. Dashboard
- El botón "Gestionar Plantillas Word" solo se muestra a administradores
- Se cambió el texto de "PDF" a "Word"

### 5. Controlador de Plantillas
- Todos los métodos (index, crear, editar, eliminar, activar) validan que el usuario sea administrador
- Si no es admin, redirige al dashboard con mensaje de error

## Iniciar sesión como administrador

1. Cierra tu sesión actual (si está abierta)
2. Inicia sesión con la cédula del empleado que marcaste como admin
3. Ahora verás el botón "Gestionar Plantillas Word" en el dashboard
4. Solo los administradores pueden acceder a esa sección

## Importante

- **Usuarios normales**: Solo pueden generar sus certificados
- **Administradores**: Pueden generar certificados Y gestionar las plantillas Word
