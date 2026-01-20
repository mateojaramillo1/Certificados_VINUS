<?php

// 1. CARGA DE DEPENDENCIAS
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
    if (class_exists(\Dotenv\Dotenv::class)) {
        \Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->safeLoad();
    }
}

// 2. SESIÓN Y SEGURIDAD BÁSICA
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. AUTOLOAD PSR-4 (Conexión de clases con archivos)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative = str_replace('\\', '/', substr($class, $len));
    $file = $baseDir . $relative . '.php';
    if (file_exists($file)) require $file;
});

// 4. INICIALIZACIÓN DE CONEXIÓN A BASE DE DATOS
// Esto asegura que cualquier controlador pueda usar la DB inmediatamente
try {
    \App\Core\Database::getInstance()->getConnection();
} catch (\Exception $e) {
    die("Error crítico: No se pudo conectar a la base de datos de VINUS S.A.S.");
}

// 5. PROCESAMIENTO DE RUTA (Controller / Action)
$controllerParam = $_GET['controller'] ?? $_GET['c'] ?? 'auth';
$action = $_GET['action'] ?? $_GET['a'] ?? 'showLogin';

// 6. MIDDLEWARE DE AUTENTICACIÓN (Protección de Base de Datos)
// Lista de controladores que requieren que el usuario esté logueado
$rutasProtegidas = ['certificado', 'plantilla', 'empleado'];

if (in_array($controllerParam, $rutasProtegidas) && empty($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=showLogin');
    exit;
}

// Redirección si ya está logueado y va al login
if ($controllerParam === 'auth' && $action === 'showLogin' && !empty($_SESSION['user_id'])) {
    $action = 'dashboard';
}

// 7. INSTANCIACIÓN DINÁMICA
$controllerClass = 'App\\Controllers\\' . ucfirst($controllerParam) . 'Controller';

if (!class_exists($controllerClass)) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/../views/errors/404.php'; // Recomendado usar una vista
    exit;
}

$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    header('HTTP/1.0 404 Not Found');
    echo "Acción no encontrada: {$action}";
    exit;
}

// 8. EJECUCIÓN
$controller->{$action}();


