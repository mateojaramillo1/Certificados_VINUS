<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — VINUS S.A.S</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="vinus-app auth-body">
    <nav class="navbar navbar-expand-lg navbar-dark vinus-navbar">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <a class="navbar-brand vinus-brand" href="index.php?controller=auth&action=dashboard">
                <img src="images/logo.png" alt="VINUS" class="navbar-logo" onerror="this.src='images/logo.svg'">
            </a>
            <a href="index.php?controller=auth&action=dashboard" class="btn btn-outline-light btn-sm">
                Volver al Dashboard
            </a>
        </div>
    </nav>
    <div class="auth-page">
        <div class="vinus-card auth-card p-0" style="max-width:720px;">
        <div class="auth-header text-center">
            <h2>VINUS S.A.S</h2>
            <p class="mb-0">Registro de Usuario</p>
        </div>
        <div class="p-4">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="index.php?controller=auth&action=register" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                        <input type="text"
                               class="form-control"
                               id="nombre_completo"
                               name="nombre_completo"
                               placeholder="Ej: Juan Pérez García"
                               value="<?php echo htmlspecialchars($_POST['nombre_completo'] ?? ''); ?>"
                               required
                               autofocus>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="numero_documento" class="form-label">Número de Documento *</label>
                        <input type="text"
                               class="form-control"
                               id="numero_documento"
                               name="numero_documento"
                               placeholder="Ej: 1234567890"
                               value="<?php echo htmlspecialchars($_POST['numero_documento'] ?? ''); ?>"
                               required>
                        <div class="form-text">Este será tu usuario para ingresar</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="id_empresa" class="form-label">Empresa *</label>
                        <select class="form-select" id="id_empresa" name="id_empresa" required>
                            <option value="">Seleccione una empresa...</option>
                            <?php
                            if (isset($empresas) && is_array($empresas)):
                                foreach ($empresas as $empresa):
                            ?>
                                <option value="<?php echo $empresa['id_empresa']; ?>"
                                    <?php echo (isset($_POST['id_empresa']) && $_POST['id_empresa'] == $empresa['id_empresa']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($empresa['nombre_empresa']); ?>
                                    (<?php echo htmlspecialchars($empresa['nit']); ?>)
                                </option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cargo" class="form-label">Cargo</label>
                        <input type="text"
                               class="form-control"
                               id="cargo"
                               name="cargo"
                               placeholder="Ej: Analista"
                               value="<?php echo htmlspecialchars($_POST['cargo'] ?? ''); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tipo_contrato" class="form-label">Tipo de Contrato</label>
                        <select class="form-select" id="tipo_contrato" name="tipo_contrato">
                            <option value="Término Indefinido" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] == 'Término Indefinido') ? 'selected' : 'selected'; ?>>Término Indefinido</option>
                            <option value="Término Fijo" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] == 'Término Fijo') ? 'selected' : ''; ?>>Término Fijo</option>
                            <option value="Obra o Labor" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] == 'Obra o Labor') ? 'selected' : ''; ?>>Obra o Labor</option>
                            <option value="Prestación de Servicios" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] == 'Prestación de Servicios') ? 'selected' : ''; ?>>Prestación de Servicios</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="salario_basico" class="form-label">Salario Básico</label>
                        <input type="number"
                               class="form-control"
                               id="salario_basico"
                               name="salario_basico"
                               placeholder="Ej: 1500000"
                               value="<?php echo htmlspecialchars($_POST['salario_basico'] ?? ''); ?>"
                               step="1000"
                               min="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                    <input type="date"
                           class="form-control"
                           id="fecha_ingreso"
                           name="fecha_ingreso"
                           value="<?php echo htmlspecialchars($_POST['fecha_ingreso'] ?? date('Y-m-d')); ?>">
                </div>

                <button type="submit" class="btn btn-vinus-accent btn-lg w-100">
                    Registrarse
                </button>
            </form>

            <div class="text-center mt-3">
                <p class="mb-0">
                    ¿Ya tienes cuenta?
                    <a href="index.php?controller=auth&action=showLogin" class="text-decoration-none">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
