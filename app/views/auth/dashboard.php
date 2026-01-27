<?php
// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=showLogin');
    exit;
}

// Obtener datos del empleado
$empleado = \App\Models\Empleado::findById($_SESSION['user_id']);
if (!$empleado) {
    header('Location: index.php?controller=auth&action=logout');
    exit;
}

// Obtener información de la empresa
$empresa = require __DIR__ . '/../../config/company.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — VINUS S.A.S</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="vinus-app">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark vinus-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-file-earmark-text"></i> VINUS <span>S.A.S</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear"></i> Administración
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="index.php">
                                    <i class="bi bi-search me-2"></i> Buscar Empleados
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="index.php?controller=auth&action=showRegister">
                                    <i class="bi bi-person-plus me-2"></i> Registrar Empleado
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="index.php?controller=plantilla&action=index">
                                    <i class="bi bi-file-word me-2"></i> Plantillas Word
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
                <span class="text-white me-3">
                    <i class="bi bi-person-circle"></i>
                    <?php echo htmlspecialchars($empleado['nombre_completo']); ?>
                </span>
                <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container vinus-container">
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger mt-4">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Información del Empleado -->
                <div class="vinus-card soft">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="m-0">
                            <i class="bi bi-person-badge"></i> Información del Empleado
                        </h3>
                        <span class="vinus-pill"><i class="bi bi-check-circle"></i> Activo</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Nombre Completo:</span>
                        <span class="info-value"><?php echo htmlspecialchars($empleado['nombre_completo']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Número de Documento:</span>
                        <span class="info-value"><?php echo htmlspecialchars($empleado['numero_documento']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Cargo:</span>
                        <span class="info-value"><?php echo htmlspecialchars($empleado['cargo']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Tipo de Contrato:</span>
                        <span class="info-value"><?php echo htmlspecialchars($empleado['tipo_contrato']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Fecha de Ingreso:</span>
                        <span class="info-value">
                            <?php
                                $fecha = new DateTime($empleado['fecha_ingreso']);
                                echo $fecha->format('d/m/Y');
                            ?>
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Empresa:</span>
                        <span class="info-value"><?php echo htmlspecialchars($empresa['name']); ?></span>
                    </div>

                    <?php if (isset($empleado['is_admin']) && $empleado['is_admin']): ?>
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-shield-check"></i> Cuenta de Administrador
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Opciones de Certificado -->
                <div class="vinus-card soft mt-4">
                    <h4 class="mb-3">
                        <i class="bi bi-file-earmark-pdf"></i> Generar Certificado Laboral
                    </h4>
                    <p class="text-muted mb-3">
                        Seleccione las opciones para su certificado y descárguelo en formato Word.
                    </p>

                    <form action="index.php" method="GET">
                        <input type="hidden" name="controller" value="certificado">
                        <input type="hidden" name="action" value="generar">
                        <input type="hidden" name="id" value="<?php echo $empleado['id_empleados']; ?>">

                        <div class="form-check mb-3">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="incluir_salario"
                                   value="1"
                                   id="incluir_salario">
                            <label class="form-check-label" for="incluir_salario">
                                Incluir información salarial en el certificado
                            </label>
                        </div>

                        <button type="submit" class="btn btn-vinus-accent btn-lg w-100">
                            <i class="bi bi-download"></i> Descargar Certificado
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            El certificado se descargará en formato PDF
                        </small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
