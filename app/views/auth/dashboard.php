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
    <title>Dashboard - <?php echo htmlspecialchars($empleado['nombre_completo']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .user-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-top: 2rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
        .info-value {
            color: #212529;
        }
        .btn-certificate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
        }
        .btn-certificate:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8b 100%);
            color: white;
        }
        .certificate-options {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-file-earmark-text"></i> Sistema de Certificados
            </a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">
                    <i class="bi bi-person-circle"></i> 
                    <?php echo htmlspecialchars($empleado['nombre_completo']); ?>
                </span>
                <a href="index.php?controller=auth&action=logout" class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Información del Empleado -->
                <div class="user-card">
                    <h3 class="mb-4">
                        <i class="bi bi-person-badge"></i> Información del Empleado
                    </h3>
                    
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
                <div class="certificate-options">
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

                        <button type="submit" class="btn btn-certificate btn-lg w-100">
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

                <?php if (isset($empleado['is_admin']) && $empleado['is_admin']): ?>
                    <!-- Panel de Administración -->
                    <div class="user-card mt-4">
                        <h4 class="mb-3">
                            <i class="bi bi-gear"></i> Panel de Administración
                        </h4>
                        <div class="d-grid gap-2">
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Buscar Empleados
                            </a>
                            <a href="index.php?controller=plantilla&action=index" class="btn btn-outline-secondary">
                                <i class="bi bi-file-word"></i> Gestionar Plantillas Word
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
