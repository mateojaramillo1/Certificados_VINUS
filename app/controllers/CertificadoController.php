He modificado tu controlador CertificadoController para que se sincronice perfectamente con la estructura de base de datos que creamos (usando id_empleados, numero_documento, etc.).

También he añadido una lógica para que el nombre del archivo descargado sea más limpio y profesional.

PHP

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
        // Solo administradores o usuarios logueados deberían ver esto
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=showLogin');
            exit;
        }
        require __DIR__ . '/../views/home.php';
    }

    public function search()
    {
        $q = trim($_GET['q'] ?? '');
        $results = [];
        if ($q !== '') {
            // Buscamos usando el nuevo método que debe apuntar a numero_documento o nombre_completo
            $results = Empleado::findByNameOrId($q);
        }
        require __DIR__ . '/../views/certificados/list.php';
    }

    public function generar()
    {
        // Usamos id_empleados que es tu PK
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php');
            exit;
        }

        $empleado = Empleado::findById($id);
        if (!$empleado) {
            echo "Empleado no encontrado.";
            return;
        }

        $incluirSalario = isset($_GET['incluir_salario']) && $_GET['incluir_salario'] == '1';
        $plantillaActiva = PlantillaWord::getActiva();
        
        if ($plantillaActiva) {
            $this->generarWord($empleado, $incluirSalario, $plantillaActiva);
        } else {
            $this->generarPdf($empleado, $incluirSalario);
        }
    }

    private function generarWord($empleado, $incluirSalario = false, $plantilla = null)
    {
        try {
            $wordGen = new WordGenerator();
            // Asegúrate de que WordGenerator use $empleado->nombre_completo y $empleado->numero_documento
            $wordGen->generarCertificado($empleado, $incluirSalario);
            
            // Nombre del archivo basado en numero_documento
            $nombreArchivo = 'Certificado_Laboral_' . $empleado->numero_documento;
            $wordGen->descargar($nombreArchivo);
            
        } catch (\Exception $e) {
            echo "Error al generar el certificado Word: " . htmlspecialchars($e->getMessage());
            echo "<br><a href='index.php'>Volver</a>";
        }
    }
    
    private function generarPdf($empleado, $incluirSalario = false)
    {
        try {
            $pdfGen = new PdfGenerator();
            // El generador de PDF ahora recibirá el objeto con los campos corregidos
            $pdfGen->generarCertificado($empleado, $incluirSalario);
            
            $nombreArchivo = 'Certificado_Laboral_' . $empleado->numero_documento . '.pdf';
            $pdfGen->descargar($nombreArchivo);
            
        } catch (\Exception $e) {
            echo "Error al generar el certificado PDF: " . htmlspecialchars($e->getMessage());
            echo "<br><a href='index.php'>Volver</a>";
        }
    }

    // El método descargarPlantillaDoc se mantiene igual, 
    // pero asegúrate de usar $empleado->numero_documento
}
