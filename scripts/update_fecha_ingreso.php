<?php
// Actualiza fecha_ingreso por documento.
// Uso: php scripts/update_fecha_ingreso.php --documento=8063455 --fecha=2024-10-29

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
    if (class_exists(Dotenv\Dotenv::class)) {
        Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
    }
}

$appTimezone = $_ENV['APP_TIMEZONE'] ?? 'America/Bogota';
date_default_timezone_set($appTimezone);

$documento = null;
$fecha = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--documento=')) {
        $documento = trim(substr($arg, strlen('--documento=')));
    } elseif (str_starts_with($arg, '--fecha=')) {
        $fecha = trim(substr($arg, strlen('--fecha=')));
    }
}

if ($documento === null || $fecha === null) {
    fwrite(STDERR, "Uso: php scripts/update_fecha_ingreso.php --documento=8063455 --fecha=YYYY-MM-DD\n");
    exit(1);
}

$dt = DateTime::createFromFormat('Y-m-d', $fecha);
if (!$dt || $dt->format('Y-m-d') !== $fecha) {
    fwrite(STDERR, "Fecha inválida. Use formato YYYY-MM-DD.\n");
    exit(1);
}

try {
    $db = App\Core\Database::getInstance();
    $conn = $db->getConnection();
} catch (Exception $e) {
    fwrite(STDERR, "No se pudo conectar a la base de datos.\n");
    exit(1);
}

$stmt = $conn->prepare('SELECT id_empleados, numero_documento, nombre_completo, fecha_ingreso FROM empleados WHERE numero_documento = ? LIMIT 1');
$stmt->execute([$documento]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    fwrite(STDERR, "No se encontró el empleado con documento {$documento}.\n");
    exit(1);
}

$update = $conn->prepare('UPDATE empleados SET fecha_ingreso = :fecha WHERE id_empleados = :id');
$update->execute([
    ':fecha' => $fecha,
    ':id' => $row['id_empleados']
]);

fwrite(STDOUT, "Actualizado: {$row['nombre_completo']} ({$row['numero_documento']})\n");
fwrite(STDOUT, "Antes: {$row['fecha_ingreso']} | Ahora: {$fecha}\n");
