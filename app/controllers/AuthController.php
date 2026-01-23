<?php

namespace App\Controllers;

use App\Models\Empleado;
use App\Models\Empresa;

class AuthController
{
    public function showLogin()
    {
        if (!empty($_SESSION['user_id'])) {
            $_SESSION = [];

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
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login()
    {
        $documento = trim($_POST['numero_documento'] ?? ''); 
        $password = $_POST['password'] ?? '';

        if ($documento === '' || $password === '') {
            $error = 'Documento y contraseña son requeridos.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (!preg_match('/^\d+$/', $documento)) {
            $error = 'El documento debe contener sólo números.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        $user = Empleado::authenticate($documento, $password);

        if (!$user) {
            $error = 'Credenciales inválidas.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id_empleados'];
        $_SESSION['user_name'] = $user['nombre_completo'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        header('Location: index.php?controller=auth&action=dashboard');
        exit;
    }

    public function showRegister()
    {
        $empresas = Empresa::getAll();
        require __DIR__ . '/../views/auth/register.php';
    }

    public function register()
    {
        $nombre = trim($_POST['nombre_completo'] ?? '');
        $documento = trim($_POST['numero_documento'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $tipo_contrato = trim($_POST['tipo_contrato'] ?? 'Término Indefinido');
        $salario = trim($_POST['salario_basico'] ?? 0);
        $fecha_ingreso = trim($_POST['fecha_ingreso'] ?? date('Y-m-d'));
        $id_empresa = $_POST['id_empresa'] ?? 1;

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

        $user = Empleado::create([
            'id_empresa'        => $id_empresa,
            'numero_documento'  => $documento,
            'nombre_completo'   => $nombre,
            'cargo'             => $cargo,
            'tipo_contrato'     => $tipo_contrato,
            'salario_basico'    => $salario,
            'fecha_ingreso'     => $fecha_ingreso,
            'estado'            => 'Activo',
            'is_admin'          => 0
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
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=showLogin');
            exit;
        }

        require __DIR__ . '/../views/auth/dashboard.php';
    }

    public function logout()
    {
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
