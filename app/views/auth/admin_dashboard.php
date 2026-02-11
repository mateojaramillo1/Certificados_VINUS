<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=showLogin');
    exit;
}

$empleado = $empleado ?? null;
$historialAccesos = $historialAccesos ?? [];
$historialCertificados = $historialCertificados ?? [];
$chartAccesos = $chartAccesos ?? [];
$chartCertificados = $chartCertificados ?? [];
$chartCertificadosMes = $chartCertificadosMes ?? [];
$empleadosActivos = $empleadosActivos ?? 0;
$filtroDesde = $filtroDesde ?? '';
$filtroHasta = $filtroHasta ?? '';
$rangoActivo = $rangoActivo ?? false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Estadísticas — VINUS S.A.S</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/estilos.css?v=20260202">
</head>
<body class="vinus-app">
    <nav class="navbar navbar-expand-lg navbar-dark vinus-navbar">
        <div class="container-fluid">
            <a class="navbar-brand vinus-brand" href="index.php?controller=auth&action=adminDashboard">
                <img src="images/logo.png" alt="VINUS" class="navbar-logo" onerror="this.src='images/logo.svg'">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#vinusNavbar" aria-controls="vinusNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="vinusNavbar">
                <ul class="navbar-nav me-auto align-items-lg-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link vinus-nav-link" href="index.php?controller=auth&action=adminDashboard">
                            <i class="bi bi-graph-up"></i> Estadísticas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link vinus-nav-link" href="index.php?controller=certificado&action=buscar">
                            <i class="bi bi-search"></i> Buscar Empleados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link vinus-nav-link" href="index.php?controller=auth&action=showRegister">
                            <i class="bi bi-person-plus"></i> Registrar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link vinus-nav-link" href="index.php?controller=auth&action=showBulkUpload">
                            <i class="bi bi-upload"></i> Carga Masiva
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link vinus-nav-link" href="index.php?controller=plantilla&action=index">
                            <i class="bi bi-file-word"></i> Plantillas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link vinus-nav-link" href="index.php?controller=auth&action=dashboard">
                            <i class="bi bi-house"></i> Mi Panel
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-white fw-semibold">
                        <i class="bi bi-person-circle"></i>
                        <?php echo htmlspecialchars($empleado['nombre_completo'] ?? ''); ?>
                    </span>
                    <a href="index.php?controller=auth&action=logout" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </div>
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

        <div class="vinus-card soft mb-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-2">
                <div>
                    <h5 class="mb-1"><i class="bi bi-calendar3"></i> Filtrar por fecha</h5>
                    <div class="text-muted">Aplica a accesos, certificados y graficas.</div>
                </div>
                <?php if ($rangoActivo): ?>
                    <span class="vinus-pill"><i class="bi bi-funnel"></i> Rango activo</span>
                <?php endif; ?>
            </div>
            <form class="row g-2 align-items-end mt-3" method="GET" action="index.php">
                <input type="hidden" name="controller" value="auth">
                <input type="hidden" name="action" value="adminDashboard">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Desde</label>
                    <input type="date" class="form-control" name="desde" value="<?php echo htmlspecialchars($filtroDesde); ?>">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Hasta</label>
                    <input type="date" class="form-control" name="hasta" value="<?php echo htmlspecialchars($filtroHasta); ?>">
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-vinus-accent flex-fill">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a class="btn btn-outline-vinus flex-fill" href="index.php?controller=auth&action=adminDashboard">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="row g-4 mt-2 mb-4">
            <div class="col-12 col-lg-4">
                <div class="vinus-card soft h-100">
                    <h5 class="mb-2"><i class="bi bi-people"></i> Empleados activos</h5>
                    <div class="display-6 fw-bold">
                        <?php echo htmlspecialchars((string)$empleadosActivos); ?>
                    </div>
                    <div class="text-muted">Estado: Activo</div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="vinus-card soft h-100">
                    <h5 class="mb-3"><i class="bi bi-graph-up"></i> Accesos últimos 7 días</h5>
                    <canvas id="chartAccesos" height="160"></canvas>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="vinus-card soft h-100">
                    <h5 class="mb-3"><i class="bi bi-graph-up-arrow"></i> Certificados últimos 7 días</h5>
                    <canvas id="chartCertificados" height="160"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="vinus-card soft">
                    <h5 class="mb-3"><i class="bi bi-bar-chart"></i> Certificados por mes</h5>
                    <canvas id="chartCertificadosMes" height="180"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="vinus-card soft">
                    <h5 class="mb-3"><i class="bi bi-person-check"></i> Últimos accesos</h5>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Documento</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($historialAccesos)): ?>
                                    <tr><td colspan="4" class="text-muted">Sin registros.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($historialAccesos as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['nombre_completo'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['numero_documento'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['created_at'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="vinus-card soft">
                    <h5 class="mb-3"><i class="bi bi-file-earmark-text"></i> Últimos certificados emitidos</h5>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Documento</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($historialCertificados)): ?>
                                    <tr><td colspan="4" class="text-muted">Sin registros.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($historialCertificados as $row): ?>
                                        <?php
                                            $tipo = 'PDF';
                                            $incluyeSalario = !empty($row['incluir_salario']);
                                            $tipoLabel = $incluyeSalario ? ($tipo . ' + SALARIO') : $tipo;
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['nombre_completo'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['numero_documento'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($tipoLabel); ?></td>
                                            <td><?php echo htmlspecialchars($row['created_at'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const accesosData = <?php echo json_encode($chartAccesos ?? []); ?>;
            const certificadosData = <?php echo json_encode($chartCertificados ?? []); ?>;
            const certificadosMesData = <?php echo json_encode($chartCertificadosMes ?? []); ?>;

            const labelsAccesos = accesosData.map(item => item.fecha);
            const valuesAccesos = accesosData.map(item => Number(item.total));

            const labelsCert = certificadosData.map(item => item.fecha);
            const valuesCert = certificadosData.map(item => Number(item.total));

            const accCtx = document.getElementById('chartAccesos');
            const certCtx = document.getElementById('chartCertificados');
            const certMesCtx = document.getElementById('chartCertificadosMes');

            if (accCtx) {
                new Chart(accCtx, {
                    type: 'line',
                    data: {
                        labels: labelsAccesos,
                        datasets: [{
                            label: 'Accesos',
                            data: valuesAccesos,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.2)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, precision: 0 } }
                    }
                });
            }

            if (certCtx) {
                new Chart(certCtx, {
                    type: 'bar',
                    data: {
                        labels: labelsCert,
                        datasets: [{
                            label: 'Certificados',
                            data: valuesCert,
                            backgroundColor: '#10b981'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, precision: 0 } }
                    }
                });
            }

            if (certMesCtx) {
                const labelsMes = certificadosMesData.map(item => item.periodo);
                const valuesMes = certificadosMesData.map(item => Number(item.total));
                new Chart(certMesCtx, {
                    type: 'bar',
                    data: {
                        labels: labelsMes,
                        datasets: [{
                            label: 'Certificados por mes',
                            data: valuesMes,
                            backgroundColor: '#6366f1'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, precision: 0 } }
                    }
                });
            }
        })();
    </script>
</body>
</html>
