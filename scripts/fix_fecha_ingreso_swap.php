<?php
// Corrige fechas de ingreso con día/mes invertidos en la tabla empleados.
// Uso:
//   php scripts/fix_fecha_ingreso_swap.php         (solo muestra el resumen)
//   php scripts/fix_fecha_ingreso_swap.php --apply (aplica el UPDATE)

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
    if (class_exists(\Dotenv\Dotenv::class)) {
        \Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
    }
}

$appTimezone = $_ENV['APP_TIMEZONE'] ?? 'America/Bogota';
date_default_timezone_set($appTimezone);

$apply = in_array('--apply', $argv ?? [], true);

try {
    $db = \App\Core\Database::getInstance();
    $conn = $db->getConnection();
} catch (\Exception $e) {
    fwrite(STDERR, "No se pudo conectar a la base de datos.\n");
    exit(1);
}

$where = "fecha_ingreso IS NOT NULL\n"
    . "AND DAY(fecha_ingreso) <= 12\n"
    . "AND MONTH(fecha_ingreso) <= 12\n"
    . "AND DAY(fecha_ingreso) <> MONTH(fecha_ingreso)";

$countStmt = $conn->query("SELECT COUNT(*) AS total FROM empleados WHERE {$where}");
$total = (int)$countStmt->fetchColumn();

$sampleStmt = $conn->query(
    "SELECT id_empleados, numero_documento, nombre_completo, fecha_ingreso\n"
    . "FROM empleados\n"
    . "WHERE {$where}\n"
    . "ORDER BY fecha_ingreso ASC\n"
    . "LIMIT 15"
);
$samples = $sampleStmt->fetchAll(\PDO::FETCH_ASSOC);

fwrite(STDOUT, "Registros candidatos a corregir: {$total}\n");
if (!empty($samples)) {
    fwrite(STDOUT, "Muestra (antes -> después):\n");
    foreach ($samples as $row) {
        $original = $row['fecha_ingreso'];
        $dt = \DateTime::createFromFormat('Y-m-d', $original);
        $swapDate = $original;
        if ($dt) {
            $year = $dt->format('Y');
            $month = $dt->format('m');
            $day = $dt->format('d');
            $swap = \DateTime::createFromFormat('Y-m-d', "{$year}-{$day}-{$month}");
            if ($swap) {
                $swapDate = $swap->format('Y-m-d');
            }
        }
        fwrite(
            STDOUT,
            sprintf(
                "- #%s | %s | %s | %s -> %s\n",
                $row['id_empleados'],
                $row['numero_documento'],
                $row['nombre_completo'],
                $original,
                $swapDate
            )
        );
    }
}

if (!$apply) {
    fwrite(STDOUT, "\nEjecuta con --apply para aplicar el UPDATE.\n");
    exit(0);
}

$allStmt = $conn->query(
    "SELECT id_empleados, fecha_ingreso\n"
    . "FROM empleados\n"
    . "WHERE {$where}\n"
    . "ORDER BY id_empleados ASC"
);
$rows = $allStmt->fetchAll(\PDO::FETCH_ASSOC);

$updateStmt = $conn->prepare(
    "UPDATE empleados SET fecha_ingreso = :fecha WHERE id_empleados = :id"
);

$conn->beginTransaction();
try {
    $affected = 0;
    foreach ($rows as $row) {
        $original = $row['fecha_ingreso'];
        $dt = \DateTime::createFromFormat('Y-m-d', $original);
        if (!$dt) {
            continue;
        }
        $year = $dt->format('Y');
        $month = $dt->format('m');
        $day = $dt->format('d');
        $swap = \DateTime::createFromFormat('Y-m-d', "{$year}-{$day}-{$month}");
        if (!$swap) {
            continue;
        }
        $newDate = $swap->format('Y-m-d');
        if ($newDate === $original) {
            continue;
        }
        $updateStmt->execute([
            ':fecha' => $newDate,
            ':id' => $row['id_empleados']
        ]);
        $affected += $updateStmt->rowCount();
    }
    $conn->commit();
    fwrite(STDOUT, "UPDATE aplicado. Filas afectadas: {$affected}\n");
} catch (\Exception $e) {
    $conn->rollBack();
    fwrite(STDERR, "Error aplicando UPDATE: " . $e->getMessage() . "\n");
    exit(1);
}
