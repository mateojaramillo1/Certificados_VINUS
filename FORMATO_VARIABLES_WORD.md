# Instrucciones: Cómo escribir las variables en Word

## ⚠️ IMPORTANTE: Formato exacto de las variables

Las variables DEBEN escribirse exactamente así en tu documento Word:

```
${nombre}
${cedula}
${cargo}
```

## ❌ NO funcionará si escribes:

- `$nombre` (sin llaves)
- `{nombre}` (sin el símbolo $)
- `$ {nombre}` (con espacios)
- `nombre` (sin nada)

## ✅ Ejemplo correcto - Copia esto en Word:

```
CERTIFICADO LABORAL

LA EMPRESA VINUS S.A.S

Certifica que:

${nombre}

Identificado con C.C. ${cedula}

Trabaja en nuestra empresa como ${cargo}

Desde: ${fecha_ingreso}
Hasta: ${fecha_retiro}

Tipo de contrato: ${tipo_contrato}

Salario: ${salario}

Expedido en ${empresa_ciudad}, a los ${dia} días del mes de ${mes} de ${anio}.

Atentamente,
```

## 📝 Pasos para crear tu plantilla:

1. **Abre Microsoft Word** (o Google Docs, LibreOffice)

2. **Diseña tu certificado** con el logo, encabezado, etc.

3. **En los lugares donde quieres que aparezcan los datos**, escribe exactamente:
   - `${nombre}` donde va el nombre
   - `${cedula}` donde va la cédula
   - `${cargo}` donde va el cargo
   - etc.

4. **Guarda como .docx**

5. **Sube la plantilla** al sistema

## 🔍 Verifica que está correcto:

Después de escribir `${nombre}` en Word, deberías ver exactamente:

```
${nombre}
```

Con el símbolo de dólar `$`, las llaves `{}` y la palabra `nombre` dentro.

## Variables disponibles completas:

### Datos del Empleado
- `${nombre}` - Nombre del empleado
- `${cedula}` - Número de cédula
- `${cargo}` - Cargo
- `${fecha_ingreso}` - Fecha de ingreso completa (ej: 2020-01-15)
- `${dia_ingreso}` - Día de ingreso (ej: 15)
- `${mes_ingreso}` - Mes de ingreso en letras (ej: enero)
- `${anio_ingreso}` - Año de ingreso (ej: 2020)
- `${fecha_retiro}` - Fecha de retiro completa
- `${dia_retiro}` - Día de retiro
- `${mes_retiro}` - Mes de retiro en letras
- `${anio_retiro}` - Año de retiro
- `${tipo_contrato}` - Tipo de contrato
- `${salario}` - Salario en números (ej: $2.000.000)
- `${salario_letras}` - Salario en letras (ej: Dos millones de pesos)

### Datos de la Empresa
- `${empresa_nombre}` - Nombre de la empresa
- `${empresa_nit}` - NIT de la empresa
- `${empresa_ciudad}` - Ciudad
- `${empresa_direccion}` - Dirección

### Fecha Actual (Separada)
- `${dia}` - Día actual en número (ejemplo: 15)
- `${mes}` - Mes actual en letras (ejemplo: enero)
- `${anio}` - Año actual (ejemplo: 2026)
- `${fecha_actual}` - Fecha completa (ejemplo: "15 de enero de 2026")

## Ejemplos de uso de la fecha:

### Fecha Actual

#### Opción 1: Fecha completa (todo junto)
```
Expedido el ${fecha_actual}
```
**Resultado:** "Expedido el 15 de enero de 2026"

#### Opción 2: Fecha por partes (separada)
```
Expedido a los ${dia} días del mes de ${mes} de ${anio}
```
**Resultado:** "Expedido a los 15 días del mes de enero de 2026"

### Fecha de Ingreso (Contrato)

#### Fecha completa
```
Fecha de ingreso: ${fecha_ingreso}
```
**Resultado:** "Fecha de ingreso: 2020-01-15"

#### Fecha por partes
```
Ingresó el día ${dia_ingreso} de ${mes_ingreso} de ${anio_ingreso}
```
**Resultado:** "Ingresó el día 15 de enero de 2020"

#### Combinado
```
Labora desde el ${dia_ingreso} de ${mes_ingreso} del ${anio_ingreso} hasta ${fecha_retiro}
```
**Resultado:** "Labora desde el 15 de enero del 2020 hasta la fecha"

### Opción 3: Solo día y mes
```
Fecha: ${dia} de ${mes}
```
**Resultado:** "Fecha: 15 de enero"

### Opción 4: Formato libre
```
Día: ${dia}
Mes: ${mes}
Año: ${anio}
```
**Resultado:**
```
Día: 15
Mes: enero
Año: 2026
```

## Ejemplo completo de certificado:

```
CERTIFICADO LABORAL

La empresa VINUS S.A.S certifica que ${nombre}, identificado con 
cédula ${cedula}, labora en nuestra empresa desde el ${dia_ingreso} 
de ${mes_ingreso} de ${anio_ingreso} hasta la fecha, desempeñando 
el cargo de ${cargo}.

Salario: ${salario} (${salario_letras})

Expedido en ${empresa_ciudad}, a los ${dia} días del mes de 
${mes} de ${anio}.
```

**Resultado para Juan Pérez:**
```
CERTIFICADO LABORAL

La empresa VINUS S.A.S certifica que Juan Pérez, identificado con 
cédula 12345678, labora en nuestra empresa desde el 15 de enero 
de 2020 hasta la fecha, desempeñando el cargo de Ingeniero.

Salario: $2.000.000 (Dos millones de pesos)

Expedido en Bogotá, a los 15 días del mes de enero de 2026.
```
