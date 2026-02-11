<?php
// Actualiza fecha_ingreso desde un CSV exportado de Excel.
// Uso:
//   php scripts/update_fecha_ingreso_from_csv.php --file=..\data\empleados.csv --apply
// Encabezados requeridos (cualquiera de estos nombres):
//   Documento / Cédula / Cedula / numero_documento
//   Ingreso / fecha_ingreso

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
    if (class_exists(Dotenv\Dotenv::class)) {
        Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
    }
}

$appTimezone = $_ENV['APP_TIMEZONE'] ?? 'America/Bogota';
date_default_timezone_set($appTimezone);

$file = null;
$apply = false;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--file=')) {
        $file = trim(substr($arg, strlen('--file=')));
    } elseif ($arg === '--apply') {
        $apply = true;
    }
}

if ($file === null) {
    fwrite(STDERR, "Uso: php scripts/update_fecha_ingreso_from_csv.php --file=RUTA\n");
    exit(1);
}

if (!file_exists($file)) {
    fwrite(STDERR, "No se encontró el archivo: {$file}\n");
    exit(1);
}

try {
    $db = App\Core\Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    fwrite(STDERR, "No se pudo conectar a la base de datos.\n");
    exit(1);
}

$fh = fopen($file, 'r');
if (!$fh) {
    fwrite(STDERR, "No se pudo abrir el archivo: {$file}\n");
    exit(1);
}

$headers = null;
while (($row = fgetcsv($fh, 0, ';')) !== false) {
    if (empty($row)) {
        continue;
    }
    $normalizedRow = array_map(function ($h) {
        $h = trim($h);
        $h = mb_strtolower($h, 'UTF-8');
        $h = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $h);
        $h = preg_replace('/\s+/', '_', $h);
        return $h;
    }, $row);

    if (in_array('ingreso', $normalizedRow, true) || in_array('fecha_ingreso', $normalizedRow, true)) {
        $headers = $row;
        break;
    }
}

if (!$headers) {
    fwrite(STDERR, "No se encontraron encabezados válidos en el CSV.\n");
    exit(1);
}

$normalize = function ($h) {
    $h = trim($h);
    $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
    $h = str_replace("\xEF\xBF\xBD", '', $h);
    $h = mb_strtolower($h, 'UTF-8');
    $h = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $h);
    $h = preg_replace('/[^a-z0-9\s]/u', ' ', $h);
    $h = preg_replace('/\s+/', '_', $h);
    return $h;
};

$headerMap = [];
foreach ($headers as $idx => $h) {
    $headerMap[$normalize($h)] = $idx;
}

$docKeys = ['documento', 'cedula', 'cédula', 'numero_documento', 'codigo_empleado', 'codigo'];
$dateKeys = ['ingreso', 'fecha_ingreso'];

$docIndex = null;
foreach ($docKeys as $k) {
    $key = $normalize($k);
    if (array_key_exists($key, $headerMap)) {
        $docIndex = $headerMap[$key];
        break;
    }
}

$dateIndex = null;
foreach ($dateKeys as $k) {
    $key = $normalize($k);
    if (array_key_exists($key, $headerMap)) {
        $dateIndex = $headerMap[$key];
        break;
    }
}

if ($docIndex === null) {
    foreach ($headerMap as $key => $idx) {
        $isDoc = str_contains($key, 'documento') || str_contains($key, 'cedula');
        $isCodigo = str_contains($key, 'codigo') || str_contains($key, 'cdigo') || str_contains($key, 'digo');
        if ($isDoc || ($isCodigo && str_contains($key, 'empleado'))) {
            $docIndex = $idx;
            break;
        }
    }
}

if ($dateIndex === null) {
    foreach ($headerMap as $key => $idx) {
        if (str_contains($key, 'ingreso') || str_contains($key, 'fecha')) {
            $dateIndex = $idx;
            break;
        }
    }
}

if ($docIndex === null || $dateIndex === null) {
    fwrite(STDERR, "No se encontraron columnas Documento/Cédula y/o Ingreso/fecha_ingreso en el CSV.\n");
    exit(1);
}

$rows = [];
while (($row = fgetcsv($fh, 0, ';')) !== false) {
    $documento = trim($row[$docIndex] ?? '');
    $fechaRaw = trim($row[$dateIndex] ?? '');
    if ($documento === '' || $fechaRaw === '') {
        continue;
    }

    // Excel exporta fechas como mm/dd/yyyy (según el caso reportado)
    $dt = DateTime::createFromFormat('m/d/Y', $fechaRaw);
    if (!$dt) {
        $dt = DateTime::createFromFormat('d/m/Y', $fechaRaw);
    }
    if (!$dt) {
        continue;
    }

    $rows[] = [
        'documento' => $documento,
        'fecha' => $dt->format('Y-m-d'),
    ];
}

fclose($fh);

if (empty($rows)) {
    fwrite(STDERR, "No se encontraron filas válidas en el CSV.\n");
    exit(1);
}

$stmtFind = $conn->prepare('SELECT id_empleados, fecha_ingreso FROM empleados WHERE numero_documento = ? LIMIT 1');
$stmtUpdate = $conn->prepare('UPDATE empleados SET fecha_ingreso = :fecha WHERE id_empleados = :id');

$total = 0;
$updated = 0;
$skipped = 0;

$conn->beginTransaction();
try {
    foreach ($rows as $item) {
        $total++;
        $stmtFind->execute([$item['documento']]);
        $empleado = $stmtFind->fetch(PDO::FETCH_ASSOC);
        if (!$empleado) {
            $skipped++;
            continue;
        }
        if ($empleado['fecha_ingreso'] === $item['fecha']) {
            $skipped++;
            continue;
        }

        if ($apply) {
            $stmtUpdate->execute([
                ':fecha' => $item['fecha'],
                ':id' => $empleado['id_empleados']
            ]);
            $updated += $stmtUpdate->rowCount();
        } else {
            $updated++;
        }
    }
    if ($apply) {
        $conn->commit();
    } else {
        $conn->rollBack();
    }
} catch (Exception $e) {
    $conn->rollBack();
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}

fwrite(STDOUT, "Total filas leídas: {$total}\n");
fwrite(STDOUT, "Actualizables: {$updated}\n");
fwrite(STDOUT, "Omitidas: {$skipped}\n");
if (!$apply) {
    fwrite(STDOUT, "\nEjecuta con --apply para aplicar los cambios.\n");
}
