<?php

namespace App\Core;

require_once __DIR__ . '/../../vendor/setasign/fpdf/fpdf.php';

use App\Core\NumeroALetras;

class PdfGenerator
{
    private $pdf;

    public function __construct()
    {
        $this->pdf = new \FPDF('P', 'mm', 'Letter'); // Tamaño carta
        $this->pdf->AddPage();
        $this->pdf->SetMargins(25, 25, 25); // Márgenes más amplios para formalidad
        $this->pdf->SetAutoPageBreak(true, 25);
    }

    public function generarCertificado($empleado, $incluirSalario = false)
    {
        $company = require __DIR__ . '/../config/company.php';

        $dia = date('j');
        $diaLetras = NumeroALetras::convertirDia($dia);
        $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        $mes = $meses[(int)date('n')-1];
        $anio = date('Y');

        $nombre = mb_strtoupper($empleado->nombre_completo, 'UTF-8');
        $documento = $empleado->numero_documento;
        $cargo = $empleado->cargo;
        $tipoContrato = $empleado->tipo_contrato;
        $fechaIngreso = date('d/m/Y', strtotime($empleado->fecha_ingreso));

        $logoPath = __DIR__ . '/../../public/images/logo.png';
        if (file_exists($logoPath)) {
            $this->pdf->Image($logoPath, 25, 15, 40);
        }
        $this->pdf->Ln(25);

        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, utf8_decode('EL SUSCRITO REPRESENTANTE LEGAL DE'), 0, 1, 'C');
        $this->pdf->SetFont('Arial', 'B', 16);
        $this->pdf->Cell(0, 7, utf8_decode(mb_strtoupper($company['name'])), 0, 1, 'C');
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->Cell(0, 7, utf8_decode('NIT. ' . $company['nit']), 0, 1, 'C');
        $this->pdf->Ln(15);

        $this->pdf->SetFont('Arial', 'B', 14);
        $this->pdf->Cell(0, 10, utf8_decode('CERTIFICA:'), 0, 1, 'C');
        $this->pdf->Ln(10);

        // 3. Cuerpo del mensaje
        $this->pdf->SetFont('Arial', '', 12);

        $texto = "Que el(la) señor(a) " . $nombre . ", identificado(a) con cédula de ciudadanía No. " . $documento . ", ";
        $texto .= "labora en nuestra compañía desde el día " . $fechaIngreso . ", desempeñado el cargo de " . $cargo;
        $texto .= " mediante un contrato de trabajo " . $tipoContrato;

        if ($incluirSalario && !empty($empleado->salario_basico)) {
            $salarioNum = number_format($empleado->salario_basico, 0, ',', '.');
            $salarioLetras = NumeroALetras::convertir($empleado->salario_basico);
            $texto .= ", con una asignación salarial básica mensual de " . mb_strtoupper($salarioLetras) . " ($" . $salarioNum . ") y todas las prestaciones de Ley";
        }

        $texto = ".";

        $this->pdf->MultiCell(0, 8, utf8_decode($texto), 0, 'J');
        $this->pdf->Ln(10);

        $this->pdf->MultiCell(0, 8, utf8_decode("Para constancia de lo anterior, se firma en la ciudad de " . $company['city'] . " a los " . $dia . " (" . $diaLetras . ") días del mes de " . $mes . " de " . $anio . "."), 0, 'J');

        // 4. Firma
        $this->pdf->Ln(30);
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->Cell(0, 5, utf8_decode('__________________________'), 0, 1, 'L');
        $this->pdf->Cell(0, 7, utf8_decode($company['representative'] ?? 'Representante Legal'), 0, 1, 'L');
        $this->pdf->SetFont('Arial', '', 11);
        $this->pdf->Cell(0, 5, utf8_decode($company['name']), 0, 1, 'L');

        return $this->pdf;
    }

    public function descargar($nombreArchivo = 'certificado.pdf')
    {
        $this->pdf->Output('D', $nombreArchivo);
    }
}