<?php

namespace App\Controllers;

use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\HistorialAcceso;
use App\Models\HistorialCertificado;

class AuthController
{
    private function sendNoCacheHeaders(): void
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    public function showLogin()
    {
        $this->sendNoCacheHeaders();
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

        if ($documento === '') {
            $error = 'El documento es requerido.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (!preg_match('/^\d+$/', $documento)) {
            $error = 'El documento debe contener sólo números.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        $user = Empleado::findByDocumento($documento);

        if (!$user) {
            $error = 'Credenciales inválidas.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id_empleados'];
        $_SESSION['user_name'] = $user['nombre_completo'];
        $_SESSION['is_admin'] = $user['is_admin'];
        $_SESSION['last_activity'] = time();

        try {
            HistorialAcceso::crear([
                'id_empleado' => (int)$user['id_empleados'],
                'nombre_completo' => $user['nombre_completo'],
                'numero_documento' => $user['numero_documento'],
                'ip' => $this->getClientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (\Exception $e) {
            // No interrumpir el login si falla el historial.
        }

        if (!empty($user['is_admin'])) {
            header('Location: index.php?controller=auth&action=adminDashboard');
        } else {
            header('Location: index.php?controller=auth&action=dashboard');
        }
        exit;
    }

    public function showRegister()
    {
        $this->sendNoCacheHeaders();
        $empresas = Empresa::getAll();
        require __DIR__ . '/../views/auth/register.php';
    }

    public function showBulkUpload()
    {
        $this->sendNoCacheHeaders();
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=showLogin');
            exit;
        }

        if (empty($_SESSION['is_admin'])) {
            $_SESSION['error'] = 'No tienes permisos para acceder a la carga masiva.';
            header('Location: index.php?controller=auth&action=dashboard');
            exit;
        }

        $empresas = Empresa::getAll();
        require __DIR__ . '/../views/auth/bulk_upload.php';
    }

    public function bulkUpload()
    {
        $this->sendNoCacheHeaders();
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=showLogin');
            exit;
        }

        if (empty($_SESSION['is_admin'])) {
            $_SESSION['error'] = 'No tienes permisos para cargar empleados.';
            header('Location: index.php?controller=auth&action=dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=auth&action=showBulkUpload');
            exit;
        }

        if (empty($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Debes seleccionar un archivo válido.';
            header('Location: index.php?controller=auth&action=showBulkUpload');
            exit;
        }

        $defaultEmpresa = trim($_POST['id_empresa'] ?? '');
        $archivoNombre = $_FILES['archivo']['name'] ?? '';
        $extension = strtolower(pathinfo($archivoNombre, PATHINFO_EXTENSION));

        try {
            if ($extension === 'csv') {
                $rows = $this->parseCsvFile($_FILES['archivo']['tmp_name']);
            } elseif (in_array($extension, ['xlsx', 'xls'], true)) {
                if (!class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
                    throw new \RuntimeException('Para archivos Excel debes instalar phpoffice/phpspreadsheet.');
                }
                $rows = $this->parseExcelFile($_FILES['archivo']['tmp_name']);
            } else {
                throw new \RuntimeException('Formato no soportado. Usa CSV o Excel (XLSX).');
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: index.php?controller=auth&action=showBulkUpload');
            exit;
        }

        if (empty($rows)) {
            $_SESSION['error'] = 'No se encontraron filas válidas en el archivo.';
            header('Location: index.php?controller=auth&action=showBulkUpload');
            exit;
        }

        $empresas = Empresa::getAll();
        $empresaById = [];
        $empresaByNit = [];
        $empresaByName = [];

        foreach ($empresas as $empresa) {
            $empresaById[(string)$empresa['id_empresa']] = $empresa;
            if (!empty($empresa['nit'])) {
                $empresaByNit[$this->normalizeKey($empresa['nit'])] = $empresa;
            }
            $empresaByName[$this->normalizeKey($empresa['nombre_empresa'])] = $empresa;
        }

        $inserted = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 1;
            $documento = trim($row['numero_documento'] ?? '');
            $nombre = trim($row['nombre_completo'] ?? '');

            if ($documento === '' || $nombre === '') {
                $errors[] = "Fila {$lineNumber}: nombre y documento son obligatorios.";
                continue;
            }

            if (Empleado::findByDocumento($documento)) {
                $skipped++;
                continue;
            }

            $idEmpresa = '';
            $rowEmpresaId = trim($row['id_empresa'] ?? '');
            $rowEmpresaNit = trim($row['nit'] ?? '');
            $rowEmpresaName = trim($row['nombre_empresa'] ?? '');

            if ($rowEmpresaId !== '' && isset($empresaById[$rowEmpresaId])) {
                $idEmpresa = $rowEmpresaId;
            } elseif ($rowEmpresaNit !== '' && isset($empresaByNit[$this->normalizeKey($rowEmpresaNit)])) {
                $idEmpresa = $empresaByNit[$this->normalizeKey($rowEmpresaNit)]['id_empresa'];
            } elseif ($rowEmpresaName !== '' && isset($empresaByName[$this->normalizeKey($rowEmpresaName)])) {
                $idEmpresa = $empresaByName[$this->normalizeKey($rowEmpresaName)]['id_empresa'];
            } elseif ($defaultEmpresa !== '' && isset($empresaById[$defaultEmpresa])) {
                $idEmpresa = $defaultEmpresa;
            }

            if ($idEmpresa === '') {
                $errors[] = "Fila {$lineNumber}: no se pudo resolver la empresa.";
                continue;
            }

            $fechaIngreso = $this->normalizeDate($row['fecha_ingreso'] ?? '');
            if ($fechaIngreso === '') {
                $fechaIngreso = date('Y-m-d');
            }

            $estado = trim($row['estado'] ?? 'Activo');
            $isAdmin = $this->normalizeBoolean($row['is_admin'] ?? 0);

            $created = Empleado::create([
                'id_empresa'        => (int)$idEmpresa,
                'numero_documento'  => $documento,
                'nombre_completo'   => $nombre,
                'cargo'             => trim($row['cargo'] ?? ''),
                'tipo_contrato'     => trim($row['tipo_contrato'] ?? 'Término Indefinido'),
                'salario_basico'    => $this->normalizeNumber($row['salario_basico'] ?? ''),
                'fecha_ingreso'     => $fechaIngreso,
                'estado'            => $estado === '' ? 'Activo' : $estado,
                'is_admin'          => $isAdmin
            ]);

            if ($created) {
                $inserted++;
            } else {
                $errors[] = "Fila {$lineNumber}: no se pudo guardar el empleado.";
            }
        }

        $_SESSION['bulk_result'] = [
            'inserted' => $inserted,
            'skipped' => $skipped,
            'errors' => $errors
        ];

        header('Location: index.php?controller=auth&action=showBulkUpload');
        exit;
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
            $_SESSION['last_activity'] = time();
            header('Location: index.php?controller=auth&action=dashboard');
            exit;
        } else {
            $error = 'Error al registrar el empleado.';
            require __DIR__ . '/../views/auth/register.php';
        }
    }

    public function dashboard()
    {
        $this->sendNoCacheHeaders();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=showLogin');
            exit;
        }

        $empleado = Empleado::findById($_SESSION['user_id']);
        if (!$empleado) {
            header('Location: index.php?controller=auth&action=logout');
            exit;
        }

        $empresa = require __DIR__ . '/../config/company.php';

        require __DIR__ . '/../views/auth/dashboard.php';
    }

    public function adminDashboard()
    {
        $this->sendNoCacheHeaders();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=showLogin');
            exit;
        }

        if (empty($_SESSION['is_admin'])) {
            header('Location: index.php?controller=auth&action=dashboard');
            exit;
        }

        $empleado = Empleado::findById($_SESSION['user_id']);
        if (!$empleado) {
            header('Location: index.php?controller=auth&action=logout');
            exit;
        }

        $historialAccesos = [];
        $historialCertificados = [];
        $chartAccesos = [];
        $chartCertificados = [];
        $chartCertificadosMes = [];
        $empleadosActivos = 0;
        $filtroDesde = '';
        $filtroHasta = '';
        $rangoActivo = false;

        $filtroDesde = $this->normalizeDateInput($_GET['desde'] ?? '');
        $filtroHasta = $this->normalizeDateInput($_GET['hasta'] ?? '');
        if ($filtroDesde !== '' && $filtroHasta !== '' && $filtroDesde > $filtroHasta) {
            $tmp = $filtroDesde;
            $filtroDesde = $filtroHasta;
            $filtroHasta = $tmp;
        }
        $rangoActivo = $filtroDesde !== '' && $filtroHasta !== '';

        try {
            if ($rangoActivo) {
                $historialAccesos = HistorialAcceso::ultimosPorRango($filtroDesde, $filtroHasta, 15);
                $historialCertificados = HistorialCertificado::ultimosPorRango($filtroDesde, $filtroHasta, 15);
                $chartAccesos = HistorialAcceso::conteoPorDiaRango($filtroDesde, $filtroHasta);
                $chartCertificados = HistorialCertificado::conteoPorDiaRango($filtroDesde, $filtroHasta);
                $chartCertificadosMes = HistorialCertificado::conteoPorMesRango($filtroDesde, $filtroHasta);
            } else {
                $historialAccesos = HistorialAcceso::ultimos(15);
                $historialCertificados = HistorialCertificado::ultimos(15);
                $chartAccesos = HistorialAcceso::conteoPorDia(7);
                $chartCertificados = HistorialCertificado::conteoPorDia(7);
                $chartCertificadosMes = HistorialCertificado::conteoPorMes(6);
            }
            $empleadosActivos = Empleado::contarActivos();
        } catch (\Exception $e) {
            // No interrumpir el panel si falla el historial.
        }

        require __DIR__ . '/../views/auth/admin_dashboard.php';
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

    private function getClientIp(): string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        if (strpos($ip, ',') !== false) {
            $parts = explode(',', $ip);
            $ip = trim($parts[0]);
        }
        return $ip;
    }

    private function normalizeKey(string $value): string
    {
        $value = trim(mb_strtolower($value));
        $value = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $value);
        return preg_replace('/[^a-z0-9]+/u', '', $value);
    }

    private function normalizeNumber($value): string
    {
        $value = trim((string)$value);
        if ($value === '') return '';
        $value = str_replace(['.', ' '], '', $value);
        $value = str_replace(',', '.', $value);
        return $value;
    }

    private function normalizeBoolean($value): int
    {
        $value = trim(mb_strtolower((string)$value));
        if ($value === '1' || $value === 'si' || $value === 'sí' || $value === 'true' || $value === 'yes') {
            return 1;
        }
        return 0;
    }

    private function normalizeDate($value): string
    {
        $value = trim((string)$value);
        if ($value === '') return '';

        if (is_numeric($value)) {
            $base = new \DateTime('1899-12-30');
            $base->modify('+' . (int)$value . ' days');
            return $base->format('Y-m-d');
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $value);
            if ($dt instanceof \DateTime) {
                return $dt->format('Y-m-d');
            }
        }

        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return '';
    }

    private function normalizeDateInput($value): string
    {
        $value = trim((string)$value);
        if ($value === '') return '';
        $dt = \DateTime::createFromFormat('Y-m-d', $value);
        if ($dt instanceof \DateTime) {
            return $dt->format('Y-m-d');
        }
        return '';
    }

    private function detectDelimiter(string $line): string
    {
        $delimiters = [',', ';', '\t'];
        $bestDelimiter = ',';
        $bestCount = 0;
        foreach ($delimiters as $delimiter) {
            $count = count(str_getcsv($line, $delimiter));
            if ($count > $bestCount) {
                $bestCount = $count;
                $bestDelimiter = $delimiter;
            }
        }
        return $bestDelimiter;
    }

    private function buildHeaderMap(array $row): array
    {
        $map = [];
        $aliases = [
            'numero_documento' => ['numerodocumento', 'documento', 'cedula', 'cedula', 'identificacion', 'codigoempleado', 'codigo'],
            'nombre_completo' => ['nombrecompleto', 'nombre', 'nombres', 'empleado', 'nombredempleado', 'nombredelempleado'],
            'cargo' => ['cargo', 'puesto'],
            'tipo_contrato' => ['tipocontrato', 'tipodecontrato', 'contrato'],
            'salario_basico' => ['salariobasico', 'salario', 'sueldo'],
            'fecha_ingreso' => ['fechaingreso', 'ingreso', 'fechadeingreso'],
            'estado' => ['estado'],
            'is_admin' => ['isadmin', 'admin', 'administrador'],
            'id_empresa' => ['idempresa', 'empresaid'],
            'nombre_empresa' => ['empresa', 'nombreempresa'],
            'nit' => ['nit']
        ];

        foreach ($row as $index => $value) {
            $key = $this->normalizeKey((string)$value);
            if ($key === '') continue;
            foreach ($aliases as $target => $keys) {
                if (in_array($key, $keys, true)) {
                    $map[$index] = $target;
                    break;
                }
            }
        }

        return $map;
    }

    private function parseCsvFile(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException('No se pudo leer el archivo CSV.');
        }

        $rows = [];
        $headerMap = [];
        $delimiter = ',';
        $firstLine = fgets($handle);
        if ($firstLine !== false) {
            $delimiter = $this->detectDelimiter($firstLine);
            $headerMap = $this->buildHeaderMap(str_getcsv($firstLine, $delimiter));
        }

        if (empty($headerMap)) {
            rewind($handle);
        }

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $filtered = array_filter($data, fn($v) => trim((string)$v) !== '');
            if (empty($filtered)) continue;

            if (empty($headerMap)) {
                $headerMap = $this->buildHeaderMap($data);
                if (!empty($headerMap)) {
                    continue;
                }
                continue;
            }

            $row = [];
            foreach ($headerMap as $index => $key) {
                $row[$key] = $data[$index] ?? '';
            }
            $rows[] = $row;
        }

        fclose($handle);
        return $rows;
    }

    private function parseExcelFile(string $path): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rawRows = $sheet->toArray(null, true, true, false);

        $rows = [];
        $headerMap = [];

        foreach ($rawRows as $data) {
            $filtered = array_filter($data, fn($v) => trim((string)$v) !== '');
            if (empty($filtered)) continue;

            if (empty($headerMap)) {
                $headerMap = $this->buildHeaderMap($data);
                if (!empty($headerMap)) {
                    continue;
                }
                continue;
            }

            $row = [];
            foreach ($headerMap as $index => $key) {
                $row[$key] = $data[$index] ?? '';
            }
            $rows[] = $row;
        }

        return $rows;
    }
}
