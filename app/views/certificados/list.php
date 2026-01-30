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
        <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
            <div class="d-flex align-items-center gap-3">
                <img src="images/logo.png" alt="VINUS logo" style="height:50px;" onerror="this.src='images/logo.svg'">
                <div class="h4 m-0 fw-bold text-uppercase">Gestión de Empleados</div>
            </div>
            <a id="exportExcel" href="index.php?controller=certificado&action=exportar" class="btn btn-outline-vinus">
                Exportar a Excel
            </a>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item active">Resultados de búsqueda</li>
            </ol>
        </nav>

        <div class="vinus-card mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-4">
                    <label class="form-label fw-bold">Buscar por nombre, cédula o cargo</label>
                    <input id="employeeSearch" type="search"
                           class="form-control form-control-lg"
                           placeholder="Ej: Ana Torres, 10203040 o Analista"
                           value="<?php echo htmlspecialchars($termino ?? ''); ?>">
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-bold">Empresa</label>
                    <select id="filterEmpresa" class="form-select form-select-lg">
                        <option value="">Todas</option>
                        <?php if (!empty($empresas)): ?>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?php echo htmlspecialchars($empresa['id_empresa']); ?>"
                                    <?php echo (isset($_GET['empresa']) && $_GET['empresa'] == $empresa['id_empresa']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($empresa['nombre_empresa']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label fw-bold">Desde</label>
                          <input id="filterDesde" type="date" class="form-control form-control-lg"
                              value="<?php echo htmlspecialchars($_GET['fecha_desde'] ?? ''); ?>">
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label fw-bold">Hasta</label>
                          <input id="filterHasta" type="date" class="form-control form-control-lg"
                              value="<?php echo htmlspecialchars($_GET['fecha_hasta'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <?php if (!empty($termino)): ?>
            <h1 class="h5 mb-3">Resultados para: "<?php echo htmlspecialchars($termino); ?>"</h1>
        <?php else: ?>
            <h1 class="h5 mb-3">Todos los empleados</h1>
        <?php endif; ?>

        <?php if (empty($results)): ?>
            <div class="alert alert-warning" id="noResults">
                <?php if (!empty($termino)): ?>
                    No se encontraron empleados que coincidan con "<?php echo htmlspecialchars($termino); ?>".
                <?php else: ?>
                    No hay empleados registrados.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle vinus-table">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Cédula</th>
                            <th>Cargo</th>
                            <th>Empresa</th>
                            <th class="text-center">Acciones del Certificado</th>
                        </tr>
                    </thead>
                    <tbody id="employeeRows">
                    <?php foreach ($results as $r): ?>
                        <tr data-search="<?php echo htmlspecialchars(strtolower($r['nombre_completo'] . ' ' . $r['numero_documento'] . ' ' . $r['cargo'] . ' ' . ($r['nombre_empresa'] ?? ''))); ?>"
                            data-empresa-id="<?php echo htmlspecialchars($r['id_empresa'] ?? ''); ?>"
                            data-fecha="<?php echo htmlspecialchars($r['fecha_ingreso'] ?? ''); ?>">
                            <td><strong><?php echo htmlspecialchars($r['nombre_completo']); ?></strong></td>
                            <td><?php echo htmlspecialchars($r['numero_documento']); ?></td>
                            <td><?php echo htmlspecialchars($r['cargo']); ?></td>
                            <td><?php echo htmlspecialchars($r['nombre_empresa'] ?? ''); ?></td>
                            <td>
                                          <div class="d-flex justify-content-center flex-wrap gap-2">
                                                <a href="index.php?controller=certificado&action=generar&id=<?php echo $r['id_empleados']; ?>&incluir_salario=0"
                                                    class="btn btn-sm btn-vinus">
                                                    PDF
                                    </a>

                                    <a href="index.php?controller=certificado&action=generar&id=<?php echo $r['id_empleados']; ?>&incluir_salario=1"
                                                    class="btn btn-sm btn-vinus-accent">
                                                    PDF + Salario
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-warning mt-3" id="noResults" style="display:none;">
                No se encontraron empleados con los filtros seleccionados.
            </div>
        <?php endif; ?>
    </div>
    <script>
        (function () {
            const input = document.getElementById('employeeSearch');
            const empresaSelect = document.getElementById('filterEmpresa');
            const fechaDesde = document.getElementById('filterDesde');
            const fechaHasta = document.getElementById('filterHasta');
            const exportLink = document.getElementById('exportExcel');
            const rowsContainer = document.getElementById('employeeRows');
            const noResults = document.getElementById('noResults');

            if (!input || !rowsContainer) return;

            const rows = Array.from(rowsContainer.querySelectorAll('tr'));

            const normalize = (value) => (value || '')
                .toString()
                .trim()
                .toLowerCase();

            const applyFilter = () => {
                const term = normalize(input.value);
                const empresaValue = (empresaSelect && empresaSelect.value) ? empresaSelect.value : '';
                const desdeValue = fechaDesde && fechaDesde.value ? fechaDesde.value : '';
                const hastaValue = fechaHasta && fechaHasta.value ? fechaHasta.value : '';
                let visibleCount = 0;

                rows.forEach((row) => {
                    const haystack = normalize(row.getAttribute('data-search'));
                    const empresaId = row.getAttribute('data-empresa-id') || '';
                    const fechaIngreso = row.getAttribute('data-fecha') || '';

                    const matchTerm = term === '' || haystack.includes(term);
                    const matchEmpresa = empresaValue === '' || empresaId === empresaValue;
                    const matchDesde = desdeValue === '' || (fechaIngreso && fechaIngreso >= desdeValue);
                    const matchHasta = hastaValue === '' || (fechaIngreso && fechaIngreso <= hastaValue);
                    const match = matchTerm && matchEmpresa && matchDesde && matchHasta;
                    row.style.display = match ? '' : 'none';
                    if (match) visibleCount += 1;
                });

                if (noResults) {
                    noResults.style.display = visibleCount === 0 ? '' : 'none';
                }
            };

            const updateExportLink = () => {
                if (!exportLink) return;
                const params = new URLSearchParams();
                if (input && input.value) params.set('q', input.value);
                if (empresaSelect && empresaSelect.value) params.set('empresa', empresaSelect.value);
                if (fechaDesde && fechaDesde.value) params.set('fecha_desde', fechaDesde.value);
                if (fechaHasta && fechaHasta.value) params.set('fecha_hasta', fechaHasta.value);
                exportLink.href = `index.php?controller=certificado&action=exportar${params.toString() ? '&' + params.toString() : ''}`;
            };

            input.addEventListener('input', () => {
                applyFilter();
                updateExportLink();
            });
            if (empresaSelect) {
                empresaSelect.addEventListener('change', () => {
                    applyFilter();
                    updateExportLink();
                });
            }
            if (fechaDesde) {
                fechaDesde.addEventListener('change', () => {
                    applyFilter();
                    updateExportLink();
                });
            }
            if (fechaHasta) {
                fechaHasta.addEventListener('change', () => {
                    applyFilter();
                    updateExportLink();
                });
            }
            applyFilter();
            updateExportLink();
        })();

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
