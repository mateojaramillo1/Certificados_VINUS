# Cómo Preparar la Plantilla Word

Para que el sistema genere certificados con los datos del empleado automáticamente, debes preparar tu plantilla Word con **marcadores de variables**.

## ⚠️ IMPORTANTE: Solo archivos .docx

El sistema **SOLO acepta archivos en formato .docx** (Word 2007 o superior).

**NO se aceptan archivos .doc** (formato antiguo de Word 97-2003).

### ¿Cómo guardar en formato .docx?

1. En Microsoft Word: **Archivo → Guardar como → Tipo: Documento de Word (.docx)**
2. En Google Docs: **Archivo → Descargar → Microsoft Word (.docx)**
3. En LibreOffice: **Archivo → Guardar como → Tipo: Microsoft Word 2007-2019 (.docx)**

## Variables Disponibles

Usa estas variables en tu documento Word exactamente como se muestra (con `${}` alrededor):

### Datos del Empleado
- `${nombre}` - Nombre completo del empleado
- `${cedula}` - Número de cédula
- `${cargo}` - Cargo del empleado
- `${fecha_ingreso}` - Fecha de ingreso completa
- `${dia_ingreso}` - Día de ingreso (número)
- `${mes_ingreso}` - Mes de ingreso (en letras)
- `${anio_ingreso}` - Año de ingreso
- `${fecha_retiro}` - Fecha de retiro (o "la fecha" si aún trabaja)
- `${dia_retiro}` - Día de retiro
- `${mes_retiro}` - Mes de retiro (en letras)
- `${anio_retiro}` - Año de retiro
- `${tipo_contrato}` - Tipo de contrato (ej: "a término indefinido")
- `${salario}` - Salario en números (ej: $2.000.000)
- `${salario_letras}` - Salario escrito en letras (ej: Dos millones de pesos)

### Datos de la Empresa
- `${empresa_nombre}` - Nombre de la empresa (VINUS S.A.S)
- `${empresa_nit}` - NIT de la empresa
- `${empresa_ciudad}` - Ciudad
- `${empresa_direccion}` - Dirección

### Fecha Actual
- `${dia}` - Día en número (ej: 15)
- `${mes}` - Mes en letras (ej: enero)
- `${anio}` - Año (ej: 2026)
- `${fecha_actual}` - Fecha completa (ej: "15 de enero de 2026")

**Puedes usar las variables por separado:**
```
Expedido a los ${dia} días del mes de ${mes} del año ${anio}
```
**O usar la fecha completa:**
```
Expedido el ${fecha_actual}
```

## Ejemplo de Plantilla

Abre Microsoft Word y escribe exactamente esto (puedes cambiar el formato, colores, fuentes, etc.):

```
CERTIFICADO LABORAL

La empresa ${empresa_nombre}, con NIT ${empresa_nit}, certifica que:

${nombre}, identificado(a) con cédula de ciudadanía No. ${cedula}, 
labora en nuestra empresa desde el ${fecha_ingreso} hasta ${fecha_retiro}, 
desempeñando el cargo de ${cargo}.

Su tipo de contrato es ${tipo_contrato}.

El salario devengado es de ${salario}.

Se expide la presente certificación a los ${dia} días del mes de 
${mes} de ${anio} en la ciudad de ${empresa_ciudad}.

Atentamente,

_____________________________
Gerencia General
${empresa_nombre}
```

### ¿Cómo funciona automáticamente?

1. **El administrador sube esta plantilla** con las variables `${nombre}`, `${cedula}`, etc.

2. **Juan Pérez (cédula 12345678) inicia sesión y hace clic en "Generar certificado"**

3. **El sistema automáticamente:**
   - Toma los datos de Juan Pérez de la base de datos
   - Reemplaza `${nombre}` por "Juan Pérez"
   - Reemplaza `${cedula}` por "12345678"
   - Reemplaza `${cargo}` por su cargo
   - Y así con todas las variables

4. **Juan descarga un Word que dice:**
   ```
   CERTIFICADO LABORAL

   La empresa VINUS S.A.S, con NIT 900123456-7, certifica que:

   Juan Pérez, identificado(a) con cédula de ciudadanía No. 12345678, 
   labora en nuestra empresa desde el 2020-01-15 hasta la fecha, 
   desempeñando el cargo de Ingeniero.

   Su tipo de contrato es a término indefinido.

   El salario devengado es de $4.500.000.

   Se expide la presente certificación a los 15 días del mes de 
   enero de 2026 en la ciudad de Bogotá.

   Atentamente,

   _____________________________
   Gerencia General
   VINUS S.A.S
   ```

**¡Juan NO tiene que editar nada! El documento ya viene con todos sus datos.**

## Pasos para Crear la Plantilla

1. **Abre Microsoft Word** (o LibreOffice Writer, Google Docs)

2. **Diseña tu certificado** con el formato que desees:
   - Logo de la empresa
   - Encabezados
   - Pie de página
   - Firmas

3. **Coloca las variables** donde quieras que aparezcan los datos:
   - Escribe exactamente `${nombre}` donde quieres el nombre
   - Escribe `${cedula}` donde quieres la cédula
   - Y así con todas las variables

4. **Guarda el documento** como `.docx`:
   - **Archivo → Guardar como**
   - **Tipo de archivo: Documento de Word (.docx)**
   - ⚠️ **NO guardes como .doc** (formato antiguo no soportado)

5. **Sube la plantilla** en el sistema:
   - Inicia sesión como administrador
   - Ve a "Gestionar Plantillas Word"
   - Crea una nueva plantilla
   - Sube tu archivo Word

6. **Activa la plantilla** para que se use en la generación de certificados

## Consejos

- ✅ Usa **solo archivos .docx** (Word 2007 o superior)
- ✅ Usa `${}` alrededor de cada variable
- ✅ Escribe las variables exactamente como se muestran (respeta mayúsculas/minúsculas)
- ✅ Puedes formatear el texto normalmente (negritas, colores, tamaños)
- ✅ Puedes agregar imágenes, tablas, etc.
- ❌ NO uses archivos .doc (formato antiguo de Word 97-2003)
- ❌ NO cambies el nombre de las variables
- ❌ NO uses espacios dentro de `${}` (ej: `${ nombre }` no funcionará)

## Si tienes un archivo .doc antiguo

1. Ábrelo en Microsoft Word
2. Ve a **Archivo → Guardar como**
3. Selecciona **Tipo de archivo: Documento de Word (.docx)**
4. Guarda el archivo
5. Sube el nuevo archivo .docx al sistema

## Resultado

Cuando un empleado genere su certificado:
1. El sistema tomará tu plantilla Word
2. Reemplazará todas las variables con los datos reales del empleado
3. Descargará el documento Word ya completado
4. El empleado podrá abrirlo y verlo con todos sus datos

## Ejemplo Visual

**Antes (en la plantilla):**
```
${nombre}, identificado con C.C. ${cedula}
```

**Después (documento generado):**
```
Juan Pérez, identificado con C.C. 12345678
```

## Soporte para Salario Confidencial

Si el empleado genera el certificado **sin salario**, la variable `${salario}` se reemplazará por:
```
[CONFIDENCIAL]
```

Esto te permite incluir la línea del salario en la plantilla siempre, pero solo se mostrará el valor real cuando se solicite explícitamente.
