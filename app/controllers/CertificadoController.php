<?php

namespace App\Controllers;

use App\Models\Empleado;
use App\Models\PlantillaWord;
use App\Core\WordGenerator;
use App\Core\PdfGenerator;

class CertificadoController
{
    public function index()
    {
        // Mostrar vista principal de búsqueda
        require __DIR__ . '/../views/home.php';
    }

    public function buscar()
    {
        $q = trim($_GET['q'] ?? '');
        $results = [];
        
        if ($q !== '') {
            $results = $this->buscarEmpleados($q);
        }
        
        require __DIR__ . '/../views/certificados/list.php';
    }

    private function buscarEmpleados($query)
    {
        $db = \App\Core\Database::getInstance();
        $conn = $db->getConnection();
        
        // Buscar por nombre, documento o ID
        $stmt = $conn->prepare("
            SELECT * FROM empleados 
            WHERE numero_documento LIKE :query 
            OR nombre_completo LIKE :query 
            OR id_empleados = :id
            ORDER BY nombre_completo ASC
        ");
        
        $searchParam = "%{$query}%";
        $idParam = is_numeric($query) ? intval($query) : 0;
        
        $stmt->bindParam(':query', $searchParam, \PDO::PARAM_STR);
        $stmt->bindParam(':id', $idParam, \PDO::PARAM_INT);
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

        $incluirSalario = isset($_GET['incluir_salario']) || isset($_POST['incluir_salario']);
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
            header('Location: index.php');
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
            header('Location: index.php');
            exit;
        }
    }
}
