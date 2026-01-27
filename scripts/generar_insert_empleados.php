<?php
// Genera un SQL de INSERT para la tabla empleados a partir de un archivo de texto.
// Uso: php scripts/generar_insert_empleados.php

$inputFile = __DIR__ . '/../data/empleados_raw.txt';
$outputFile = __DIR__ . '/../data/empleados_insert.sql';

if (!file_exists($inputFile)) {
    fwrite(STDERR, "No se encontró el archivo de entrada: {$inputFile}\n");
    exit(1);
}

$lines = file($inputFile, FILE_IGNORE_NEW_LINES);
$values = [];
$lineNumber = 0;

foreach ($lines as $line) {
    $lineNumber++;
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }

    // Formato esperado: documento  nombre  cargo  dd/mm/yyyy (o mm/dd/yyyy)
    $parts = preg_split('/\t+/', $line);
    if (count($parts) < 4) {
        $parts = preg_split('/\s{2,}/', $line);
    }

    if (count($parts) < 4) {
        fwrite(STDERR, "Línea {$lineNumber} no coincide con el formato esperado: {$line}\n");
        exit(1);
    }

    $documento = trim($parts[0]);
    $nombre = trim($parts[1]);
    $cargo = trim($parts[2]);
    $fecha = trim($parts[3]);

    // Convertir fecha a YYYY-MM-DD (acepta d/m/Y y m/d/Y)
    $dt = DateTime::createFromFormat('d/m/Y', $fecha);
    if (!$dt) {
        $dt = DateTime::createFromFormat('m/d/Y', $fecha);
    }
    if (!$dt) {
        fwrite(STDERR, "Fecha inválida en la línea {$lineNumber}: {$fecha}\n");
        exit(1);
    }
    $fechaSql = $dt->format('Y-m-d');

    // Escapar comillas simples
    $nombre = str_replace("'", "''", $nombre);
    $cargo = str_replace("'", "''", $cargo);

    $values[] = "(1, '{$documento}', '{$nombre}', '{$cargo}', 'Término Indefinido', 0, '{$fechaSql}', 'Activo', 0)";
}

if (empty($values)) {
    fwrite(STDERR, "No se encontraron líneas válidas para procesar.\n");
    exit(1);
}

$sql = "INSERT INTO empleados (id_empresa, numero_documento, nombre_completo, cargo, tipo_contrato, salario_basico, fecha_ingreso, estado, is_admin) VALUES\n";
$sql .= implode(",\n", $values);
$sql .= ";\n";

file_put_contents($outputFile, $sql);

fwrite(STDOUT, "SQL generado en: {$outputFile}\n");
