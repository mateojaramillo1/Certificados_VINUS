<?php

namespace App\Controllers;

use App\Models\Empleado;

class AuthController
{
    public function showLogin()
    {
    require __DIR__ . '/../views/auth/login.php';
    

    }

    public function login()
    {
        // Usando 'numero_documento' que coincide con el formulario y la DB
        $documento = trim($_POST['numero_documento'] ?? ''); 
        $password = $_POST['password'] ?? '';

        if ($documento === '' || $documento === '') {
            $error = 'Documento y contraseña son requeridos.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (!preg_match('/^\d+$/', $documento)) {
            $error = 'El documento debe contener sólo números.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // El modelo ahora debe buscar por numero_documento
        $user = Empleado::authenticate($documento, $documento);

        if (!$user) {
            $error = 'Credenciales inválidas.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // Login exitoso - $user es un array, no un objeto
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id_empleados'];
        $_SESSION['user_name'] = $user['nombre_completo'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        header('Location: index.php?controller=auth&action=dashboard');
        exit;
    }

    public function showRegister()
    {
        require __DIR__ . '/../views/auth/register.php';
    }

    public function register()
    {
        // Mapeo de datos según tu nueva tabla 'empleados'
        $nombre = trim($_POST['nombre_completo'] ?? '');
        $documento = trim($_POST['numero_documento'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $tipo_contrato = trim($_POST['tipo_contrato'] ?? 'Término Indefinido');
        $salario = trim($_POST['salario_basico'] ?? 0);
        $fecha_ingreso = trim($_POST['fecha_ingreso'] ?? date('Y-m-d'));
        $id_empresa = $_POST['id_empresa'] ?? 1; // Por defecto la primera empresa

        if ($nombre === '' || $documento === '') {
            $error = 'Nombre y documento son obligatorios.';
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        if (Empleado::findByDocumento($documento)) {
            $error = 'Ya existe un empleado con este documento.';
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        // Crear registro con la estructura exacta de tu base de datos
        $user = Empleado::create([
            'id_empresa'        => $id_empresa,
            'numero_documento'  => $documento,
            'nombre_completo'   => $nombre,
            'cargo'             => $cargo,
            'tipo_contrato'     => $tipo_contrato,
            'salario_basico'    => $salario,
            'fecha_ingreso'     => $fecha_ingreso,
            'estado'            => 'Activo',
            'is_admin'          => 0 // Por defecto no es admin al registrarse solo
        ]);

        if ($user) {
            $_SESSION['user_id'] = $user['id_empleados'];
            $_SESSION['user_name'] = $user['nombre_completo'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header('Location: index.php?controller=auth&action=dashboard');
            exit;
        } else {
            $error = 'Error al registrar el empleado.';
            require __DIR__ . '/../views/auth/register.php';
        }
    }

    public function dashboard()
    {
        // Verificar que haya sesión activa
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=showLogin');
            exit;
        }

        require __DIR__ . '/../views/auth/dashboard.php';
    }

    public function logout()
    {
        // Limpiar sesión completamente
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
        header('Location: index.php?controller=auth&action=showLogin');
        exit;
    }
}
