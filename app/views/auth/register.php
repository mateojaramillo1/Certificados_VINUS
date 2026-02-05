<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — VINUS S.A.S</title>
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
                        <div class="floating-field">
                            <input type="text"
                                   class="form-control"
                                   id="nombre_completo"
                                   name="nombre_completo"
                                   placeholder=" "
                                   value="<?php echo htmlspecialchars($_POST['nombre_completo'] ?? ''); ?>"
                                   required>
                            <label for="nombre_completo" class="floating-label">Nombre Completo *</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="floating-field">
                            <input type="text"
                                   class="form-control"
                                   id="numero_documento"
                                   name="numero_documento"
                                   placeholder=" "
                                   value="<?php echo htmlspecialchars($_POST['numero_documento'] ?? ''); ?>"
                                   required>
                            <label for="numero_documento" class="floating-label">Número de Documento *</label>
                        </div>
                        <div class="form-text">Este será tu usuario para ingresar</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="floating-field">
                            <select class="form-select floating-select" id="id_empresa" name="id_empresa" required>
                                <option value=""> </option>
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
                            <label for="id_empresa" class="floating-label">Empresa *</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="floating-field">
                            <input type="text"
                                   class="form-control"
                                   id="cargo"
                                   name="cargo"
                                   placeholder=" "
                                   value="<?php echo htmlspecialchars($_POST['cargo'] ?? ''); ?>">
                            <label for="cargo" class="floating-label">Cargo</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="floating-field">
                            <select class="form-select floating-select" id="tipo_contrato" name="tipo_contrato" required>
                                <option value="Término Indefinido" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] == 'Término Indefinido') ? 'selected' : 'selected'; ?>>Término Indefinido</option>
                                <option value="Término Fijo" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] == 'Término Fijo') ? 'selected' : ''; ?>>Término Fijo</option>
                                <option value="Obra o Labor" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] == 'Obra o Labor') ? 'selected' : ''; ?>>Obra o Labor</option>
                                <option value="Prestación de Servicios" <?php echo (isset($_POST['tipo_contrato']) && $_POST['tipo_contrato'] == 'Prestación de Servicios') ? 'selected' : ''; ?>>Prestación de Servicios</option>
                            </select>
                            <label for="tipo_contrato" class="floating-label">Tipo de Contrato</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="floating-field">
                            <input type="number"
                                   class="form-control"
                                   id="salario_basico"
                                   name="salario_basico"
                                   placeholder=" "
                                   value="<?php echo htmlspecialchars($_POST['salario_basico'] ?? ''); ?>"
                                step="any">
                            <label for="salario_basico" class="floating-label">Salario Básico</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="floating-field">
                        <input type="date"
                               class="form-control floating-date"
                               id="fecha_ingreso"
                               name="fecha_ingreso"
                               value="<?php echo htmlspecialchars($_POST['fecha_ingreso'] ?? date('Y-m-d')); ?>">
                        <label for="fecha_ingreso" class="floating-label">Fecha de Ingreso</label>
                    </div>
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
