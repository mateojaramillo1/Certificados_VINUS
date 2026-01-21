# 📋 Sistema de Certificados Laborales - VINUS S.A.S

Sistema web PHP para generar certificados laborales automáticos en formato Word utilizando plantillas personalizables con variables dinámicas.

## 🚀 Características

- ✅ Generación automática de certificados laborales en Word
- ✅ Sistema de plantillas Word (.docx) personalizables
- ✅ Gestión de múltiples empresas y empleados
- ✅ Variables dinámicas para certificados
- ✅ Búsqueda avanzada de empleados
- ✅ Panel de administración completo
- ✅ Sistema de autenticación y registro

## 📋 Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache/XAMPP
- Composer
- Extensiones PHP: mbstring, pdo_mysql, zip

## 🔧 Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/mateojaramillo1/Certificados_VINUS.git
cd Certificados_VINUS
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar base de datos

**Desde terminal:**
```bash
C:\xampp\mysql\bin\mysql.exe -u root < crear_bd_completa.sql
```

**Desde phpMyAdmin:**
- Abre: http://localhost/phpmyadmin
- Importa el archivo `crear_bd_completa.sql`

### 4. Acceder al sistema

Abre en tu navegador: `http://localhost/certificados/`

## 👥 Credenciales por Defecto

**Usuario Administrador:**
- Documento: `1234567890`
- Contraseña: `1234567890`

## 📂 Estructura del Proyecto

```
Certificados/
├── app/
│   ├── config/              # Configuración (DB, empresa)
│   ├── controllers/         # Controladores MVC
│   │   ├── AuthController.php
│   │   ├── CertificadoController.php
│   │   └── PlantillaController.php
│   ├── core/               # Clases principales
│   │   ├── Database.php
│   │   ├── WordGenerator.php
│   │   ├── PdfGenerator.php
│   │   └── NumeroALetras.php
│   ├── models/             # Modelos de datos
│   │   ├── Empleado.php
│   │   ├── Empresa.php
│   │   └── PlantillaWord.php
│   └── views/              # Vistas HTML/PHP
│       ├── auth/           # Login, registro, dashboard
│       ├── certificados/   # Búsqueda y generación
│       └── plantillas/     # Gestión de plantillas
├── public/
│   ├── css/                # Estilos
│   ├── images/             # Logos e imágenes
│   ├── plantillas/         # Archivos .docx (generados)
│   └── index.php           # Punto de entrada
├── vendor/                 # Dependencias Composer
├── crear_bd_completa.sql   # Script de base de datos
├── VARIABLES_PLANTILLAS.md # Documentación de variables
└── README.md              # Este archivo
```

## 📖 Uso del Sistema

### Para Usuarios Regulares
1. Registrarse seleccionando la empresa
2. Iniciar sesión con número de documento
3. Generar certificado desde el dashboard

### Para Administradores
1. Ir a "Gestionar Plantillas"
2. Subir plantilla Word con variables
3. Activar la plantilla principal
4. Buscar empleados y generar certificados

## 🔤 Variables para Plantillas Word

En tu documento Word, escribe las variables así: `${nombre_variable}`

### Variables de Empleado
- `${nombre}` - Nombre completo (EN MAYÚSCULAS)
- `${cedula}` - Número de documento
- `${cargo}` - Cargo del empleado

### Variables de Fecha de Ingreso
- `${dia_ingreso}` - Día de ingreso (15)
- `${mes_ingreso}` - Mes de ingreso (enero, febrero...)
- `${anio_ingreso}` - Año de ingreso (2023)
- `${fecha_ingreso}` - Fecha completa (15 de enero de 2023)

### Variables de Salario
- `${salario}` - Salario formateado ($2.500.000)
- `${salario_letras}` - Salario en letras (DOS MILLONES... PESOS M/CTE)

### Variables de Empresa
- `${empresa_nombre}` - Nombre de la empresa
- `${empresa_nit}` - NIT de la empresa
- `${ciudad}` - Ciudad de la empresa

### Variables de Fecha de Expedición
- `${dia}` - Día actual (20)
- `${dia_letras}` - Día en letras (veinte)
- `${mes}` - Mes actual (enero)
- `${anio}` - Año actual (2026)

**Ver lista completa en:** `VARIABLES_PLANTILLAS.md`

## ⚙️ Configuración

### Configurar Base de Datos
Edita: `app/config/database.php`

### Configurar Información de la Empresa
Edita: `app/config/company.php`

## 🗄️ Base de Datos

El sistema crea 3 tablas principales:

1. **empresas** - Información de empresas
2. **empleados** - Datos de empleados (con FK a empresas)
3. **plantillas** - Plantillas Word subidas

## 🔐 Seguridad

- Las contraseñas iniciales son el número de documento
- Se recomienda cambiarlas después del primer acceso
- Solo administradores pueden gestionar plantillas
- Los usuarios regulares solo ven su propia información

## 🛠️ Tecnologías

- PHP (patrón MVC personalizado)
- MySQL
- Bootstrap 5
- PHPWord (para manipular archivos Word)
- PDO para base de datos

## 📝 Documentación Adicional

- `VARIABLES_PLANTILLAS.md` - Guía completa de variables disponibles
- `CONFIGURACION_BD_EMPRESAS.md` - Detalles de configuración de base de datos
- `.env.example` - Ejemplo de configuración de entorno

## 📧 Soporte

Desarrollado para VINUS S.A.S

---
⭐ Sistema de Gestión de Certificados Laborales
