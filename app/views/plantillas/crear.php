<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Plantilla — VINUS S.A.S</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="vinus-app">
    <nav class="navbar navbar-expand-lg navbar-dark vinus-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?controller=auth&action=dashboard">
                <i class="bi bi-award"></i> VINUS <span>S.A.S</span>
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
                </span>
                <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="container vinus-container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="vinus-card p-0">
                    <div class="auth-header">
                        <h4 class="mb-0">
                            <i class="bi bi-file-earmark-word"></i> Nueva Plantilla de Certificado
                        </h4>
                    </div>
                    <div class="p-4">
                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger">
                                <strong>Se encontraron errores:</strong>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="index.php?controller=plantilla&action=crear" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="bi bi-tag"></i> Nombre de la Plantilla *
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="nombre"
                                       name="nombre"
                                       placeholder="Ej: Certificado Laboral Estándar"
                                       value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"
                                       required>
                                <div class="form-text">Nombre descriptivo para identificar esta plantilla.</div>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">
                                    <i class="bi bi-card-text"></i> Descripción
                                </label>
                                <textarea class="form-control"
                                          id="descripcion"
                                          name="descripcion"
                                          rows="3"
                                          placeholder="Descripción opcional de la plantilla..."><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="archivo" class="form-label">
                                    <i class="bi bi-file-earmark-arrow-up"></i> Archivo Word (.docx) *
                                </label>
                                <input type="file"
                                       class="form-control"
                                       id="archivo"
                                       name="archivo"
                                       accept=".doc,.docx"
                                       required>
                                <div class="form-text">
                                    Selecciona un archivo Word (.doc o .docx) con las variables del certificado.
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="activa"
                                       name="activa"
                                       <?php echo (isset($_POST['activa']) && $_POST['activa']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="activa">
                                    <i class="bi bi-check-circle"></i> Activar esta plantilla automáticamente
                                </label>
                                <div class="form-text">
                                    Al activar, esta plantilla se usará por defecto para generar certificados.
                                    Las demás se desactivarán automáticamente.
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Variables disponibles:</strong>
                                <div class="mt-2">
                                    <code>${'{'}nombre}</code>,
                                    <code>${'{'}cedula}</code>,
                                    <code>${'{'}cargo}</code>,
                                    <code>${'{'}tipo_contrato}</code>,
                                    <code>${'{'}fecha_ingreso}</code>,
                                    <code>${'{'}salario_numero}</code>,
                                    <code>${'{'}salario_letras}</code>,
                                    <code>${'{'}empresa_nombre}</code>,
                                    <code>${'{'}empresa_nit}</code>,
                                    <code>${'{'}ciudad}</code>,
                                    <code>${'{'}dia}</code>,
                                    <code>${'{'}dia_letras}</code>,
                                    <code>${'{'}mes}</code>,
                                    <code>${'{'}anio}</code>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="index.php?controller=plantilla&action=index" class="btn btn-outline-vinus">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                                <button type="submit" class="btn btn-vinus-accent">
                                    <i class="bi bi-save"></i> Guardar Plantilla
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="vinus-card">
                            <h6 class="card-title"><i class="bi bi-lightbulb"></i> Instrucciones</h6>
                            <ol class="mb-0">
                                <li>Crea tu plantilla en Microsoft Word usando las variables listadas arriba</li>
                                <li>Las variables deben escribirse exactamente como se muestran: <code>${'{'}variable}</code></li>
                                <li>Guarda el archivo como .docx (no .doc antiguo)</li>
                                <li>Sube el archivo usando este formulario</li>
                                <li>Si marcas "Activar", esta plantilla se usará automáticamente</li>
                            </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
