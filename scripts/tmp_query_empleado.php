<?php
require __DIR__ . '/../vendor/autoload.php';
if (class_exists(Dotenv\Dotenv::class)) {
    Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
}
$db = App\Core\Database::getInstance();
$conn = $db->getConnection();
$stmt = $conn->prepare('SELECT id_empleados, numero_documento, nombre_completo, fecha_ingreso FROM empleados WHERE numero_documento = ? LIMIT 1');
$stmt->execute(['8063455']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
var_export($row);
