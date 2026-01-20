# Sistema de Certificados con Plantillas PDF

## Instalación de Dependencias

Para instalar las librerías necesarias (FPDF y FPDI), ejecuta uno de los siguientes comandos según tu configuración:

### Opción 1: Con Composer instalado globalmente
```bash
composer update
```

### Opción 2: Con PHP de XAMPP
```bash
c:\xampp\php\php.exe composer.phar update
```

### Opción 3: Descargar composer.phar
Si no tienes composer, descarga composer.phar desde https://getcomposer.org/download/ y colócalo en la raíz del proyecto, luego ejecuta:
```bash
c:\xampp\php\php.exe composer.phar update
```

## Configuración de la Base de Datos

Ejecuta el script SQL para crear las tablas necesarias:

```sql
-- Ejecutar el archivo: actualizar_plantillas.sql
```

Este script creará:
- Tabla `plantillas_pdf`: Para almacenar las plantillas de certificados
- Tabla `plantilla_campos`: Para configurar posiciones de campos en el PDF (uso futuro)

## Uso del Sistema

### 1. Acceso al Panel de Administración
- Inicia sesión en el sistema
- En el dashboard, verás un botón "Gestionar Plantillas PDF"

### 2. Subir una Plantilla PDF
- Click en "Nueva Plantilla"
- Completa el formulario:
  - **Nombre**: Nombre descriptivo de la plantilla
  - **Descripción**: Información adicional sobre la plantilla
  - **Archivo PDF**: Selecciona tu plantilla PDF (máx. 10MB)
  - **Activar**: Marca si quieres activar inmediatamente esta plantilla
- Click en "Crear Plantilla"

### 3. Gestionar Plantillas
- Ver todas las plantillas registradas
- Ver el PDF de cada plantilla
- Activar/Desactivar plantillas (solo una puede estar activa)
- Editar información de plantillas
- Eliminar plantillas

### 4. Generar Certificados
- Una vez que tengas una plantilla activa, al generar un certificado se usará automáticamente
- El sistema superpondrá los datos del empleado sobre la plantilla PDF
- Si no hay plantilla activa, se generará el certificado HTML tradicional

## Ajustar Posiciones de Texto en el PDF

Las posiciones donde se escribe el texto sobre la plantilla PDF están definidas en el archivo:
`app/core/PdfGenerator.php`

Busca la sección con el comentario:
```php
// AQUÍ SE DEFINEN LAS POSICIONES DONDE SE ESCRIBIRÁ EL TEXTO
```

Ajusta las coordenadas X e Y (en milímetros) según el diseño de tu plantilla:
- **X**: Distancia desde el borde izquierdo
- **Y**: Distancia desde el borde superior

Ejemplo:
```php
// Nombre del empleado
$this->pdf->SetXY(60, 100);  // X=60mm, Y=100mm desde esquina superior izquierda
$this->pdf->SetFont('Arial', 'B', 14);
$this->pdf->Cell(0, 10, utf8_decode($empleado->nombre), 0, 1);
```

## Recomendaciones para la Plantilla PDF

1. **Diseño limpio**: Asegúrate de que tu plantilla tenga espacios claros donde se colocará el texto
2. **Tamaño carta**: Usa tamaño carta (Letter) o A4
3. **Colores de fondo claros**: Para que el texto negro sea legible
4. **Márgenes adecuados**: Deja espacio suficiente para el texto dinámico
5. **Fuentes simples**: El sistema usa Arial por defecto, compatible con la mayoría de PDFs

## Estructura de Archivos

```
app/
  core/
    PdfGenerator.php          # Clase para generar PDFs con plantilla
  models/
    PlantillaPdf.php          # Modelo para gestionar plantillas
  controllers/
    PlantillaController.php   # Controlador de plantillas
    CertificadoController.php # Modificado para usar plantillas
  views/
    plantillas/
      index.php               # Lista de plantillas
      crear.php               # Formulario de creación
      editar.php              # Formulario de edición
public/
  plantillas/                 # Directorio donde se almacenan los PDFs
```

## Troubleshooting

### Error: "No hay ninguna plantilla PDF activa"
- Solución: Crea y activa al menos una plantilla desde el panel de administración

### Error: "El archivo de plantilla no existe"
- Solución: Verifica que el archivo PDF existe en `public/plantillas/`

### Las posiciones del texto no coinciden
- Solución: Ajusta las coordenadas X, Y en `app/core/PdfGenerator.php`

### Error al subir archivo
- Verifica que `public/plantillas/` tenga permisos de escritura
- Verifica que el archivo sea PDF y menor a 10MB

## Próximas Mejoras

- Interfaz visual para configurar posiciones de campos
- Múltiples páginas en certificados
- Inserción de imágenes dinámicas
- Soporte para diferentes tipos de documentos
