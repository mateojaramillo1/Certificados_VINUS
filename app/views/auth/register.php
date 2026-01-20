Para completar la integración de tu sistema de certificados para VINUS S.A.S, aquí tienes el código definitivo que une el formulario de registro con la base de datos y la lógica de negocio.

1. El Enrutador Maestro (index.php)
Este archivo es el cerebro del proyecto. Recibe las peticiones del formulario y decide qué controlador llamar. Colócalo en la raíz de tu proyecto.

PHP

<?php
session_start();

// Autocarga de clases (ajusta según tu estructura o usa Composer)
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) require $file;
});

// Captura de parámetros de la URL
$controllerName = $_GET['controller'] ?? 'auth';
$actionName = $_GET['action'] ?? 'showLogin';

// Enrutamiento básico
try {
    switch ($controllerName) {
        case 'auth':
            $controller = new \App\Controllers\AuthController();
            break;
        case 'certificado':
            $controller = new \App\Controllers\CertificadoController();
            break;
        case 'home':
            // Si no está logueado, mandarlo al login
            if (!isset($_SESSION['user_id'])) {
                header('Location: index.php?controller=auth&action=showLogin');
                exit;
            }
            require __DIR__ . '/App/Views/dashboard.php';
            exit;
        default:
            die("Controlador no encontrado.");
    }

    // Ejecutar la acción
    if (method_exists($controller, $actionName)) {
        $controller->$actionName();
    } else {
        die("La acción no existe.");
    }
} catch (\Exception $e) {
    echo "Error en el sistema: " . $e->getMessage();
}

