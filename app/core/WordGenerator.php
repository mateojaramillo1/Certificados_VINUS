<?php

namespace App\Core;

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use App\Models\PlantillaWord;
use App\Core\NumeroALetras;

class WordGenerator
{
    private $plantilla;
    private $templateProcessor;

    public function __construct()
    {
        $this->plantilla = PlantillaWord::getActiva();
        if (!$this->plantilla) {
            throw new \Exception('No hay ninguna plantilla Word activa en el sistema.');
        }
    }

    public function generarCertificado($empleado, $incluirSalario = true)
    {
        $rutaPlantilla = $this->plantilla->getRutaCompleta();
        
        if (!file_exists($rutaPlantilla)) {
            throw new \Exception('El archivo físico de la plantilla no existe en el servidor.');
        }

        try {
            $this->templateProcessor = new TemplateProcessor($rutaPlantilla);
        } catch (\Exception $e) {
            throw new \Exception('Error al abrir el .docx: ' . $e->getMessage());
        }

        $company = require __DIR__ . '/../config/company.php';

        // --- PREPARACIÓN DE FECHAS ---
        $meses = ['', 'enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        
        $fechaIngObj = new \DateTime($empleado->fecha_ingreso);
        $diaIngreso = $fechaIngObj->format('d');
        $mesIngreso = $meses[(int)$fechaIngObj->format('n')];
        $anioIngreso = $fechaIngObj->format('Y');
        $f_ingreso_texto = $diaIngreso . " de " . $mesIngreso . " de " . $anioIngreso;

        // --- REEMPLAZO DE VARIABLES ---
        // Variables de empleado
        $this->templateProcessor->setValue('nombre', mb_strtoupper($empleado->nombre_completo, 'UTF-8'));
        $this->templateProcessor->setValue('cedula', $empleado->numero_documento);
        $this->templateProcessor->setValue('cargo', $empleado->cargo);
        $this->templateProcessor->setValue('tipo_contrato', $empleado->tipo_contrato ?? 'término indefinido');
        
        // Variables de fecha de ingreso
        $this->templateProcessor->setValue('fecha_ingreso', $f_ingreso_texto);
        $this->templateProcessor->setValue('dia_ingreso', $diaIngreso);
        $this->templateProcessor->setValue('mes_ingreso', $mesIngreso);
        $this->templateProcessor->setValue('anio_ingreso', $anioIngreso);

        // Salario
        if ($incluirSalario && !empty($empleado->salario_basico)) {
            $salarioFormateado = number_format($empleado->salario_basico, 0, ',', '.');
            $salarioLetras = NumeroALetras::convertir($empleado->salario_basico);
            $this->templateProcessor->setValue('salario', '$' . $salarioFormateado);
            $this->templateProcessor->setValue('salario_letras', mb_strtoupper($salarioLetras, 'UTF-8') . ' PESOS M/CTE');
        } else {
            $this->templateProcessor->setValue('salario', '');
            $this->templateProcessor->setValue('salario_letras', '');
        }

        // Datos Empresa
        $this->templateProcessor->setValue('empresa_nombre', $company['name']);
        $this->templateProcessor->setValue('empresa_nit', $company['nit']);
        $this->templateProcessor->setValue('ciudad', $company['city']);
        
        // Fecha actual (expedición del certificado)
        $this->templateProcessor->setValue('dia', date('j'));
        $this->templateProcessor->setValue('dia_letras', NumeroALetras::convertirDia(date('j')));
        $this->templateProcessor->setValue('mes', $meses[(int)date('n')]);
        $this->templateProcessor->setValue('anio', date('Y'));

        return $this->templateProcessor;
    }

    public function descargar($nombreArchivo = 'Certificado_Laboral')
    {
        $tempWordFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nombreArchivo . '_' . time() . '.docx';
        $this->templateProcessor->saveAs($tempWordFile);

        // Intentar convertir a PDF si LibreOffice está disponible
        $pdfFile = $this->convertirConLibreOffice($tempWordFile);

        if ($pdfFile && file_exists($pdfFile)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $nombreArchivo . '.pdf"');
            readfile($pdfFile);
            unlink($tempWordFile);
            unlink($pdfFile);
        } else {
            // Si falla PDF, descargar Word
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . $nombreArchivo . '.docx"');
            readfile($tempWordFile);
            unlink($tempWordFile);
        }
        exit;
    }

    private function convertirConLibreOffice($wordFile)
    {
        $soffice = 'C:\Program Files\LibreOffice\program\soffice.exe'; // Ruta estándar en Windows
        if (!file_exists($soffice)) return false;

        $outputDir = sys_get_temp_dir();
        $cmd = "\"$soffice\" --headless --convert-to pdf --outdir \"$outputDir\" \"$wordFile\"";
        exec($cmd);

        $pdfFile = $outputDir . DIRECTORY_SEPARATOR . pathinfo($wordFile, PATHINFO_FILENAME) . '.pdf';
        return file_exists($pdfFile) ? $pdfFile : false;
    }
}

