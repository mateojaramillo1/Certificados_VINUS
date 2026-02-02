<?php

namespace App\Controllers;

use App\Models\Empleado;
use App\Models\PlantillaWord;
use App\Models\Empresa;
use App\Core\WordGenerator;
use App\Core\PdfGenerator;

class CertificadoController
{
    
    public function index()
    {
        require __DIR__ . '/../views/home.php';
    }

    public function buscar()
    {
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'empresa' => trim($_GET['empresa'] ?? ''),
            'fecha_desde' => trim($_GET['fecha_desde'] ?? ''),
            'fecha_hasta' => trim($_GET['fecha_hasta'] ?? '')
        ];

        $termino = $filters['q'];
        $results = $this->filtrarEmpleados($filters);
        $empresas = Empresa::getAll();

        require __DIR__ . '/../views/certificados/list.php';
    }

    public function exportar()
    {
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'empresa' => trim($_GET['empresa'] ?? ''),
            'fecha_desde' => trim($_GET['fecha_desde'] ?? ''),
            'fecha_hasta' => trim($_GET['fecha_hasta'] ?? '')
        ];

        $results = $this->filtrarEmpleados($filters);

        $filename = 'empleados_' . date('Ymd_His') . '.csv';
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Nombre', 'Documento', 'Cargo', 'Empresa', 'Fecha de Ingreso'], ';');

        foreach ($results as $row) {
            fputcsv($output, [
                $row['nombre_completo'] ?? '',
                $row['numero_documento'] ?? '',
                $row['cargo'] ?? '',
                $row['nombre_empresa'] ?? '',
                $row['fecha_ingreso'] ?? ''
            ], ';');
        }

        fclose($output);
        exit;
    }


    public function verCertificado()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['error'] = 'ID de empleado no válido';
            header('Location: index.php?controller=auth&action=dashboard');
            exit;
        }

        $empleado = Empleado::findById($id);

        if (!$empleado) {
            $_SESSION['error'] = 'Empleado no encontrado';
            header('Location: index.php?controller=auth&action=dashboard');
            exit;
        }

        $empleado = (object)$empleado;

        $valorIncluir = $_GET['incluir_salario'] ?? null;
        $incluirSalario = filter_var($valorIncluir, FILTER_VALIDATE_BOOLEAN);

        require __DIR__ . '/../views/certificados/certificado.php';
    }

    private function filtrarEmpleados($filters)
    {
        $db = \App\Core\Database::getInstance();
        $conn = $db->getConnection();

        $sql = "
            SELECT e.*, emp.nombre_empresa
            FROM empleados e
            LEFT JOIN empresas emp ON emp.id_empresa = e.id_empresa
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['q'])) {
            $sql .= " AND (e.numero_documento LIKE :query OR e.nombre_completo LIKE :query OR e.cargo LIKE :query OR e.id_empleados = :id)";
            $params[':query'] = "%{$filters['q']}%";
            $params[':id'] = is_numeric($filters['q']) ? intval($filters['q']) : 0;
        }

        if (!empty($filters['empresa'])) {
            $sql .= " AND e.id_empresa = :empresa";
            $params[':empresa'] = $filters['empresa'];
        }

        if (!empty($filters['fecha_desde'])) {
            $sql .= " AND e.fecha_ingreso >= :fecha_desde";
            $params[':fecha_desde'] = $filters['fecha_desde'];
        }

        if (!empty($filters['fecha_hasta'])) {
            $sql .= " AND e.fecha_ingreso <= :fecha_hasta";
            $params[':fecha_hasta'] = $filters['fecha_hasta'];
        }

        $sql .= " ORDER BY e.nombre_completo ASC";

        $stmt = $conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function generar()
    {
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['error'] = 'ID de empleado no válido';
            header('Location: index.php');
            exit;
        }

        $empleado = Empleado::findById($id);

        if (!$empleado) {
            $_SESSION['error'] = 'Empleado no encontrado';
            header('Location: index.php');
            exit;
        }

        $valorIncluir = $_GET['incluir_salario'] ?? $_POST['incluir_salario'] ?? null;
        $incluirSalario = filter_var($valorIncluir, FILTER_VALIDATE_BOOLEAN);

        $plantillaActiva = PlantillaWord::getActiva();

        if ($plantillaActiva) {
            $this->generarWord($empleado, $incluirSalario);
        } else {
            $this->generarPdf($empleado, $incluirSalario);
        }
    }

    private function generarWord($empleado, $incluirSalario = false)
    {
        try {
            $wordGen = new WordGenerator();
            $wordGen->generarCertificado((object)$empleado, $incluirSalario);

            $nombreArchivo = 'Certificado_' . preg_replace('/[^a-zA-Z0-9]/', '_', $empleado['nombre_completo']);
            $wordGen->descargar($nombreArchivo);

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error al generar certificado Word: ' . $e->getMessage();
            header('Location: index.php?controller=auth&action=dashboard');
            exit;
        }
    }

    private function generarPdf($empleado, $incluirSalario = false)
    {
        try {
            $pdfGen = new PdfGenerator();
            $pdfGen->generarCertificado((object)$empleado, $incluirSalario);

            $nombreArchivo = 'Certificado_' . preg_replace('/[^a-zA-Z0-9]/', '_', $empleado['nombre_completo']) . '.pdf';
            $pdfGen->descargar($nombreArchivo);

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error al generar certificado PDF: ' . $e->getMessage();
            header('Location: index.php?controller=auth&action=dashboard');
            exit;
        }
    }
}
