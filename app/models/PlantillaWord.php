<?php

namespace App\Core;

use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\PlantillaWord;
use App\Core\NumeroALetras;

class WordGenerator
{
    private $plantilla;
    private $templateProcessor;

    public function __construct()
    {
        // Busca en la tabla 'plantillas_pdf' la que tenga activa = 1
        $this->plantilla = PlantillaWord::getActiva();
        
        if (!$this->plantilla) {
            throw new \Exception('No se encontró ninguna plantilla activa en la base de datos.');
        }
    }

    public function generarCertificado($empleado, $incluirSalario = true)
    {
        $rutaPlantilla = $this->plantilla->getRutaCompleta();
        
        if (!file_exists($rutaPlantilla)) {
            throw new \Exception('El archivo físico .docx no existe en: ' . $rutaPlantilla);
        }

        $this->templateProcessor = new TemplateProcessor($rutaPlantilla);

        // --- DATOS DE LA EMPRESA (Configuración manual o archivo config) ---
        $company = [
            'nombre' => 'VINUS S.A.S',
            'nit' => '900.123.456-7',
            'ciudad' => 'Medellín'
        ];

        // --- PROCESAMIENTO DE FECHAS ---
        $meses = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $fechaIngreso = new \DateTime($empleado->fecha_ingreso);
        
        // --- REEMPLAZO DE VARIABLES EN EL WORD ---
        // Estas etiquetas deben ir así en tu Word: ${nombre}, ${cedula}, etc.
        $this->templateProcessor->setValue('nombre', mb_strtoupper($empleado->nombre, 'UTF-8'));
        $this->templateProcessor->setValue('cedula', $empleado->cedula);
        $this->templateProcessor->setValue('cargo', $empleado->cargo);
        $this->templateProcessor->setValue('tipo_contrato', $empleado->tipo_contrato ?? 'término indefinido');
        $this->templateProcessor->setValue('fecha_ingreso', $fechaIngreso->format('d/m/Y'));

        // Manejo de Salario
        if ($incluirSalario && !empty($empleado->salario)) {
            $salarioLetras = NumeroALetras::convertir($empleado->salario);
            $this->templateProcessor->setValue('salario_numero', number_format($empleado->salario, 0, ',', '.'));
            $this->templateProcessor->setValue('salario_letras', mb_strtoupper($salarioLetras, 'UTF-8'));
        } else {
            $this->templateProcessor->setValue('salario_numero', 'N/A');
            $this->templateProcessor->setValue('salario_letras', '(Sueldo no revelado)');
        }

        // Datos de expedición (Hoy)
        $this->templateProcessor->setValue('dia', date('j'));
        $this->templateProcessor->setValue('dia_letras', NumeroALetras::convertirDia(date('j')));
        $this->templateProcessor->setValue('mes', $meses[(int)date('n')]);
        $this->templateProcessor->setValue('anio', date('Y'));
        $this->templateProcessor->setValue('empresa_nombre', $company['nombre']);
        $this->templateProcessor->setValue('empresa_nit', $company['nit']);
        $this->templateProcessor->setValue('ciudad', $company['ciudad']);

        return $this->templateProcessor;
    }

    public function descargar($nombreSugerido = 'Certificado')
    {
        // Crear archivo temporal
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nombreSugerido . '_' . time() . '.docx';
        $this->templateProcessor->saveAs($tempFile);

        // Forzar descarga del navegador
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $nombreSugerido . '.docx"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($tempFile));
        
        readfile($tempFile);
        unlink($tempFile); // Elimina el temporal después de enviar
        exit;
    }
}

