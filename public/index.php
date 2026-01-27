<?php

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
    if (class_exists(\Dotenv\Dotenv::class)) {
        \Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controllerParam = $_GET['controller'] ?? $_GET['c'] ?? 'auth';
$action = $_GET['action'] ?? $_GET['a'] ?? 'showLogin';

$isDownload = ($controllerParam === 'certificado' && in_array($action, ['generar', 'verCertificado'], true));
if (!$isDownload) {
    header('Content-Type: text/html; charset=UTF-8');
}

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative = str_replace('\\', '/', substr($class, $len));
    $file = $baseDir . $relative . '.php';
    if (file_exists($file)) require $file;
});

try {
    \App\Core\Database::getInstance()->getConnection();
} catch (\Exception $e) {
    die("Error crítico: No se pudo conectar a la base de datos de VINUS S.A.S.");
}

$rutasProtegidas = ['certificado', 'plantilla', 'empleado'];

if (in_array($controllerParam, $rutasProtegidas) && empty($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=showLogin');
    exit;
}

$controllerClass = 'App\\Controllers\\' . ucfirst($controllerParam) . 'Controller';

if (!class_exists($controllerClass)) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/../views/errors/404.php';
    exit;
}

$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    header('HTTP/1.0 404 Not Found');
    echo "Acción no encontrada: {$action}";
    exit;
}

$controller->{$action}();

