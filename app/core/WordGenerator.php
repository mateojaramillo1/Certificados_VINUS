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
    private $incluirSalario = true;

    public function __construct()
    {
        $this->plantilla = PlantillaWord::getActiva();
        if (!$this->plantilla) {
            throw new \Exception('No hay ninguna plantilla Word activa en el sistema.');
        }
    }

    public function generarCertificado($empleado, $incluirSalario = true)
    {
        $this->incluirSalario = (bool)$incluirSalario;
        $rutaPlantilla = null;
        if (is_array($this->plantilla)) {
            $rutaPlantilla = PlantillaWord::getRutaCompleta($this->plantilla);
        } elseif (is_object($this->plantilla) && method_exists($this->plantilla, 'getRutaCompleta')) {
            $rutaPlantilla = $this->plantilla->getRutaCompleta();
        }
        
        if (!$rutaPlantilla) {
            throw new \Exception('No se pudo resolver la ruta de la plantilla activa.');
        }
        
        if (!file_exists($rutaPlantilla)) {
            throw new \Exception('El archivo físico de la plantilla no existe en el servidor.');
        }

        try {
            $this->templateProcessor = new TemplateProcessor($rutaPlantilla);
        } catch (\Exception $e) {
            throw new \Exception('Error al abrir el .docx: ' . $e->getMessage());
        }

        $company = require __DIR__ . '/../config/company.php';

        $meses = ['', 'enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        
        $fechaIngObj = new \DateTime($empleado->fecha_ingreso);
        $diaIngreso = $fechaIngObj->format('d');
        $mesIngreso = $meses[(int)$fechaIngObj->format('n')];
        $anioIngreso = $fechaIngObj->format('Y');
        $f_ingreso_texto = $diaIngreso . " de " . $mesIngreso . " de " . $anioIngreso;

        $this->templateProcessor->setValue('nombre', mb_strtoupper($empleado->nombre_completo, 'UTF-8'));
        $this->templateProcessor->setValue('cedula', $empleado->numero_documento);
        $this->templateProcessor->setValue('cargo', $empleado->cargo);
        $this->templateProcessor->setValue('tipo_contrato', $empleado->tipo_contrato ?? 'término indefinido');
        
        $this->templateProcessor->setValue('fecha_ingreso', $f_ingreso_texto);
        $this->templateProcessor->setValue('dia_ingreso', $diaIngreso);
        $this->templateProcessor->setValue('mes_ingreso', $mesIngreso);
        $this->templateProcessor->setValue('anio_ingreso', $anioIngreso);

        $clausulaSalario = '';
        if ($incluirSalario && !empty($empleado->salario_basico)) {
            $salarioFormateado = number_format($empleado->salario_basico, 0, ',', '.');
            $salarioLetras = NumeroALetras::convertir($empleado->salario_basico);
            $salarioTexto = mb_strtoupper($salarioLetras, 'UTF-8');
            if (mb_stripos($salarioTexto, 'PESOS') === false) {
                $salarioTexto .= ' PESOS';
            }

            $this->templateProcessor->setValue('salario', '$' . $salarioFormateado);
            $this->templateProcessor->setValue('salario_letras', $salarioTexto);

            $clausulaSalario = ", con una asignación salarial básica mensual de " . $salarioTexto . " ($" . $salarioFormateado . ") y todas las prestaciones de Ley";
        } else {
            $this->templateProcessor->setValue('salario', '');
            $this->templateProcessor->setValue('salario_letras', '');
        }

        $this->templateProcessor->setValue('clausula_salario', $clausulaSalario);

        $this->templateProcessor->setValue('empresa_nombre', $company['name']);
        $this->templateProcessor->setValue('empresa_nit', $company['nit']);
        $this->templateProcessor->setValue('ciudad', $company['city']);
        
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

        if (!$this->incluirSalario) {
            $this->removerClausulaSalario($tempWordFile);
        }

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

    private function removerClausulaSalario($docxPath)
    {
        if (!class_exists('ZipArchive')) {
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xmlPath = 'word/document.xml';
        $xml = $zip->getFromName($xmlPath);
        if ($xml === false) {
            $zip->close();
            return;
        }

        $patron = '/con(?:\s|<[^>]+>)+una(?:\s|<[^>]+>)+asignación(?:\s|<[^>]+>)+salarial(?:\s|<[^>]+>)+básica(?:\s|<[^>]+>)+mensual(?:\s|<[^>]+>)+de(?:\s|<[^>]+>)+[\s\S]*?prestaciones(?:\s|<[^>]+>)+de(?:\s|<[^>]+>)+Ley/iu';
        $xml = preg_replace($patron, '', $xml);

        $xml = str_replace('  ', ' ', $xml);
        $xml = str_replace(' ,', ',', $xml);
        $xml = str_replace(' .', '.', $xml);
        $xml = str_replace(' ;', ';', $xml);
        $xml = str_replace(' :', ':', $xml);
        $xml = str_replace(' ?', '?', $xml);
        $xml = str_replace(' !', '!', $xml);

        // Eliminar espacios antes de signos de puntuación incluso si están en nodos separados
        $xml = preg_replace('/\s+(?=(?:<[^>]+>)*[\.,;:!?])/u', '', $xml);

        $zip->deleteName($xmlPath);
        $zip->addFromString($xmlPath, $xml);
        $zip->close();
    }

    private function convertirConLibreOffice($wordFile)
    {
        $envPath = getenv('LIBREOFFICE_PATH') ?: ($_ENV['LIBREOFFICE_PATH'] ?? null);
        $candidatos = array_filter([
            $envPath,
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            'soffice'
        ]);

        $soffice = null;
        foreach ($candidatos as $ruta) {
            if ($ruta === 'soffice') {
                $soffice = $ruta;
                break;
            }
            if (file_exists($ruta)) {
                $soffice = $ruta;
                break;
            }
        }

        if (!$soffice) return false;

        $outputDir = sys_get_temp_dir();
        $cmd = sprintf(
            '"%s" --headless --convert-to pdf --outdir "%s" "%s"',
            $soffice,
            $outputDir,
            $wordFile
        );
        exec($cmd);

        $pdfFile = $outputDir . DIRECTORY_SEPARATOR . pathinfo($wordFile, PATHINFO_FILENAME) . '.pdf';
        return file_exists($pdfFile) ? $pdfFile : false;
    }
}

