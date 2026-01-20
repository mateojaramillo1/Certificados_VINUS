<?php

// Lee configuración de la base de datos desde variables de entorno.
// Variables soportadas: DB_HOST, DB_NAME, DB_USER, DB_PASS
// Puedes crear un archivo ".env" y usar vlucas/phpdotenv, o configurar
// las variables en el entorno de Apache/PHP.

$host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
$dbname = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'certificados vinus');
$user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
$pass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? '');

return [
    'host' => $host,
    'dbname' => $dbname,
    'user' => $user,
    'pass' => $pass,
];
