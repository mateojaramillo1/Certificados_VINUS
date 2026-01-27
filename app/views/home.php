<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>VINUS S.A.S — Generar Certificado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="vinus-app">
    <nav class="navbar navbar-expand-lg navbar-dark vinus-navbar">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <a class="navbar-brand vinus-brand" href="index.php">
                <img src="images/logo.png" alt="VINUS" class="navbar-logo" onerror="this.src='images/logo.svg'">
            </a>
            <a href="index.php?controller=auth&action=dashboard" class="btn btn-outline-light btn-sm">
                Volver al Dashboard
            </a>
        </div>
    </nav>

    <div class="container vinus-container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="vinus-card vinus-hero">
                    <div class="text-center mb-4">
                        <img src="images/logo.png" alt="VINUS logo" style="height:60px;" onerror="this.src='images/logo.svg'">
                        <h1 class="h3 mt-3 mb-2">Generar Certificado Laboral</h1>
                        <p class="vinus-muted mb-0">Ingrese los datos del colaborador para buscar en la base de datos.</p>
                    </div>

                    <form action="index.php" method="get">
                        <input type="hidden" name="controller" value="certificado">
                        <input type="hidden" name="action" value="buscar">

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Buscar por nombre, cédula o cargo</label>
                            <div class="input-group">
                                <input name="q" type="search"
                                       class="form-control form-control-lg"
                                       placeholder="Ej: Juan Pérez, 10203040 o Analista"
                                       required
                                       autofocus>
                                <button type="submit" class="btn btn-vinus btn-lg">
                                    <i class="bi bi-search"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <small class="text-muted">El sistema buscará coincidencias exactas y parciales en la base de datos.</small>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php?controller=plantilla&action=index" class="btn btn-sm btn-outline-vinus">
                        Gestionar Plantillas Word
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.vinus-navbar .navbar-brand').forEach((brand) => {
            brand.addEventListener('mousemove', (event) => {
                const rect = brand.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) * 100;
                brand.style.setProperty('--hover-x', `${Math.max(0, Math.min(100, x))}%`);
            });
            brand.addEventListener('mouseleave', () => {
                brand.style.removeProperty('--hover-x');
            });
        });
    </script>
</body>
</html>
