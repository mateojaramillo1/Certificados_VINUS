<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga Masiva — VINUS S.A.S</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css?v=20260202">
</head>
<body class="vinus-app auth-body">
    <nav class="navbar navbar-expand-lg navbar-dark vinus-navbar">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <a class="navbar-brand vinus-brand" href="index.php?controller=auth&action=dashboard">
                <img src="images/logo.png" alt="VINUS" class="navbar-logo" onerror="this.src='images/logo.svg'">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#vinusNavbar" aria-controls="vinusNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="vinusNavbar">
                <div class="ms-auto">
                    <a href="index.php?controller=auth&action=dashboard" class="btn btn-outline-light btn-sm">
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="auth-page">
        <div class="vinus-card auth-card p-0" style="max-width:860px;">
            <div class="auth-header text-center">
                <h2>VINUS S.A.S</h2>
                <p class="mb-0">Carga masiva de empleados</p>
            </div>
            <div class="p-4">
                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong> <?php echo htmlspecialchars($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['bulk_result'])): ?>
                    <?php $result = $_SESSION['bulk_result']; ?>
                    <div class="alert alert-success">
                        <strong>Resultado:</strong>
                        <?php echo htmlspecialchars($result['inserted']); ?> insertados,
                        <?php echo htmlspecialchars($result['skipped']); ?> duplicados omitidos.
                    </div>
                    <?php if (!empty($result['errors'])): ?>
                        <div class="alert alert-warning">
                            <strong>Errores encontrados:</strong>
                            <ul class="mb-0">
                                <?php foreach (array_slice($result['errors'], 0, 8) as $msg): ?>
                                    <li><?php echo htmlspecialchars($msg); ?></li>
                                <?php endforeach; ?>
                                <?php if (count($result['errors']) > 8): ?>
                                    <li>Se omitieron <?php echo count($result['errors']) - 8; ?> errores adicionales.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php unset($_SESSION['bulk_result']); ?>
                <?php endif; ?>

                <form action="index.php?controller=auth&action=bulkUpload" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Empresa por defecto</label>
                            <select class="form-select" id="id_empresa" name="id_empresa">
                                <option value="">Selecciona una empresa</option>
                                <?php if (!empty($empresas)): ?>
                                    <?php foreach ($empresas as $empresa): ?>
                                        <option value="<?php echo $empresa['id_empresa']; ?>">
                                            <?php echo htmlspecialchars($empresa['nombre_empresa']); ?>
                                            (<?php echo htmlspecialchars($empresa['nit']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Se usará si el archivo no trae empresa.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Archivo CSV/Excel</label>
                            <input class="form-control" type="file" name="archivo" accept=".csv,.xlsx,.xls" required>
                            <div class="form-text">CSV con separador , o ; y cabeceras.</div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <strong>Columnas soportadas</strong>
                        <div class="mt-2">
                            <div><strong>Obligatorias:</strong> numero_documento, nombre_completo</div>
                            <div><strong>Opcionales:</strong> cargo, tipo_contrato, salario_basico, fecha_ingreso, estado, is_admin, id_empresa, nombre_empresa, nit</div>
                            <div class="mt-2">Fechas aceptadas: dd/mm/yyyy o yyyy-mm-dd.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-vinus-accent btn-lg w-100">
                        Cargar empleados
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
