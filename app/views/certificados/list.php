<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Resultados de búsqueda — VINUS S.A.S</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="vinus-app">
    <nav class="navbar navbar-expand-lg navbar-dark vinus-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">VINUS <span>S.A.S</span></a>
        </div>
    </nav>

    <div class="container vinus-container">
        <div class="d-flex align-items-center gap-3 mb-4">
            <img src="images/logo.png" alt="VINUS logo" style="height:50px;" onerror="this.src='images/logo.svg'">
            <div class="h4 m-0">Gestión de Empleados</div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item active">Resultados de búsqueda</li>
            </ol>
        </nav>

        <h1 class="h3 mb-4">Resultados para: "<?php echo htmlspecialchars($termino); ?>"</h1>

        <?php if (empty($results)): ?>
            <div class="alert alert-warning">
                No se encontraron empleados que coincidan con "<?php echo htmlspecialchars($termino); ?>".
            </div>
            <a href="index.php" class="btn btn-outline-vinus">Nueva búsqueda</a>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle vinus-table">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Cédula</th>
                            <th>Cargo</th>
                            <th class="text-center">Acciones del Certificado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($results as $r): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($r['nombre_completo']); ?></strong></td>
                            <td><?php echo htmlspecialchars($r['numero_documento']); ?></td>
                            <td><?php echo htmlspecialchars($r['cargo']); ?></td>
                            <td>
                                          <div class="d-flex justify-content-center flex-wrap gap-2">
                                    <a href="index.php?controller=certificado&action=verCertificado&id=<?php echo $r['id_empleados']; ?>&incluir_salario=1"
                                                    class="btn btn-sm btn-outline-vinus" title="Vista para imprimir">
                                       Ver PDF
                                    </a>

                                    <a href="index.php?controller=certificado&action=generar&id=<?php echo $r['id_empleados']; ?>&incluir_salario=0"
                                                    class="btn btn-sm btn-vinus">
                                       Word
                                    </a>

                                    <a href="index.php?controller=certificado&action=generar&id=<?php echo $r['id_empleados']; ?>&incluir_salario=1"
                                                    class="btn btn-sm btn-vinus-accent">
                                       Word + Salario
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
