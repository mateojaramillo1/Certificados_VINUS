<?php
// Aseguramos que el controlador haya pasado las variables necesarias
if (!isset($empleado)) {
    die("Error: No se han cargado los datos del empleado.");
}

// Cargar configuración de la empresa
$company = require __DIR__ . '/../../config/company.php';
use App\Core\NumeroALetras;

// Lógica de fechas para la expedición (hoy)
$meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$diaNum = date('j');
$diaLetras = NumeroALetras::convertirDia($diaNum);
$mesNombre = $meses[date('n')-1];
$anioNum = date('Y');

// Formatear fechas del empleado de la BD
$fechaIngresoFormateada = date('d/m/Y', strtotime($empleado->fecha_ingreso));
$fechaRetiroTexto = ($empleado->fecha_retiro) ? date('d/m/Y', strtotime($empleado->fecha_retiro)) : 'la fecha';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Certificado - <?php echo htmlspecialchars($empleado->nombre); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .certificado {
            background: #fff;
            padding: 2.5cm;
            margin: 1rem auto;
            max-width: 21cm;
            min-height: 27cm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            line-height: 1.6;
            font-size: 12pt;
            color: #000;
        }
        @media print {
            body { background: none; }
            .certificado { box-shadow: none; margin: 0; padding: 1.5cm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="certificado">
            <div class="d-flex align-items-center gap-3 mb-4">
                <img src="images/logo.png" alt="logo" style="height:70px" onerror="this.src='images/logo.svg'">
                <div>
                    <h2 class="m-0 h4"><?php echo htmlspecialchars($company['name']); ?></h2>
                    <small class="text-muted">
                        NIT: <?php echo htmlspecialchars($company['nit']); ?> | 
                        <?php echo htmlspecialchars($company['address']); ?>
                    </small>
                </div>
            </div>

            <h3 class="text-center my-5">CERTIFICACIÓN LABORAL</h3>

            <p>La empresa <strong><?php echo htmlspecialchars($company['name']); ?></strong>, identificada con NIT <strong><?php echo htmlspecialchars($company['nit']); ?></strong>, certifica que:</p>

            <p class="text-justify">
                El(La) señor(a) <strong><?php echo htmlspecialchars($empleado->nombre); ?></strong>, 
                identificado(a) con cédula de ciudadanía No. <strong><?php echo htmlspecialchars($empleado->cedula); ?></strong>, 
                labora (o laboró) en esta organización desde el <strong><?php echo $fechaIngresoFormateada; ?></strong> 
                hasta <strong><?php echo $fechaRetiroTexto; ?></strong>, desempeñando el cargo de 
                <strong><?php echo htmlspecialchars($empleado->cargo); ?></strong>, mediante un contrato de trabajo 
                <strong><?php echo htmlspecialchars($empleado->tipo_contrato); ?></strong><?php 
                if (isset($incluirSalario) && $incluirSalario && !empty($empleado->salario)): ?>, 
                devengando un salario mensual de <strong><?php echo '$' . number_format($empleado->salario, 0, ',', '.'); ?></strong> 
                (<?php echo mb_strtoupper(NumeroALetras::convertir($empleado->salario)); ?> PESOS)<?php endif; ?>.
            </p>

            <p>Durante su vinculación, el(la) trabajador(a) ha demostrado cumplimiento en sus funciones y compromiso con las políticas de la organización.</p>

            <p>El presente certificado se expide a solicitud del interesado en la ciudad de <strong><?php echo htmlspecialchars($company['city']); ?></strong>, a los <strong><?php echo $diaNum; ?> (<?php echo $diaLetras; ?>)</strong> días del mes de <strong><?php echo $mesNombre; ?></strong> de <strong><?php echo $anioNum; ?></strong>.</p>

            <div class="mt-5">
                <p>Atentamente,</p>
                <?php
                    // Lógica para la firma
                    $rutaFirma = 'images/firma.png';
                    if (file_exists(__DIR__ . '/../../public/' . $rutaFirma)): ?>
                        <div class="mb-2">
                            <img src="<?php echo $rutaFirma; ?>" alt="Firma" style="height:80px">
                        </div>
                <?php endif; ?>
                
                <p class="lh-sm">
                    <strong><?php echo htmlspecialchars($company['responsible_name']); ?></strong><br>
                    <?php echo htmlspecialchars($company['responsible_title']); ?><br>
                    <small><?php echo htmlspecialchars($company['email']); ?> - <?php echo htmlspecialchars($company['phone']); ?></small>
                </p>
            </div>

            <div class="no-print mt-5 pt-4 border-top">
                <button onclick="window.print()" class="btn btn-primary">Imprimir / Guardar PDF</button>
                <a class="btn btn-outline-secondary" href="index.php?controller=home&action=index">Volver al Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>

