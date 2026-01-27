# Variables Disponibles para Plantillas Word

## Cómo usar las variables en tu documento Word

En tu plantilla de Word, escribe las variables exactamente así: `${nombre_variable}`

Por ejemplo:
```
El señor o la señora ${nombre} identificado(a) con cédula de ciudadanía No. ${cedula}
```

## Lista completa de variables

### Información del Empleado
- `${nombre}` - Nombre completo del empleado (EN MAYÚSCULAS)
- `${cedula}` - Número de documento de identidad
- `${cargo}` - Cargo desempeñado

### Fechas de Ingreso
- `${dia_ingreso}` - Día de ingreso (Ej: 15)
- `${mes_ingreso}` - Mes de ingreso en texto (Ej: enero)
- `${anio_ingreso}` - Año de ingreso (Ej: 2023)
- `${fecha_ingreso}` - Fecha completa de ingreso (Ej: 15 de enero de 2023)

### Información Salarial
- `${salario}` - Salario formateado con símbolo de pesos (Ej: $2.500.000)
- `${salario_letras}` - Salario en letras (Ej: DOS MILLONES QUINIENTOS MIL PESOS M/CTE)
- `${clausula_salario}` - Cláusula completa de salario (vacía si no se incluye salario)

### Tipo de Contrato
- `${tipo_contrato}` - Tipo de contrato (Ej: término indefinido)

### Información de la Empresa
- `${empresa_nombre}` - Nombre de la empresa (Ej: VINUS S.A.S)
- `${empresa_nit}` - NIT de la empresa (Ej: 900.920.562-1)
- `${ciudad}` - Ciudad de la empresa (Ej: Medellín)

### Fecha de Expedición del Certificado
- `${dia}` - Día actual en número (Ej: 20)
- `${dia_letras}` - Día actual en letras (Ej: veinte)
- `${mes}` - Mes actual en texto (Ej: enero)
- `${anio}` - Año actual (Ej: 2026)

## Ejemplo de texto completo

```
Que el señor o la señora ${nombre} identificado(a) con cédula de ciudadanía
No. ${cedula}, está vinculado(a) por la empresa ${empresa_nombre} con NIT
${empresa_nit}, mediante un contrato ${tipo_contrato} desde el ${dia_ingreso}
de ${mes_ingreso} de ${anio_ingreso} a la fecha; desempeñándose en el cargo
de ${cargo} para la ejecución del Proyecto Vías del Nus${clausula_salario}.

El presente certificado se expide en la ciudad de ${ciudad}, a los ${dia}
(${dia_letras}) días del mes de ${mes} de ${anio}.
```

## Notas importantes

1. **Formato exacto**: Las variables deben escribirse exactamente como se muestran, incluyendo el símbolo `$` y las llaves `{}`
2. **Mayúsculas/Minúsculas**: Son sensibles a mayúsculas y minúsculas
3. **Espacios**: No dejes espacios dentro de las llaves: `${nombre}` ✅ `${ nombre }` ❌
4. **Salario opcional**: Si no se selecciona incluir salario, las variables `${salario}` y `${salario_letras}` quedarán vacías

## Configuración de la Empresa

Para cambiar la información de la empresa (nombre, NIT, ciudad), edita el archivo:
`app/config/company.php`
