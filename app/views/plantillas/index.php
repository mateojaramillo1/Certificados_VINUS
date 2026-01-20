<?php
// Asegúrate de que la sesión esté iniciada para mostrar los mensajes de alerta
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestión de Plantillas Word - VINUS S.A.S</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .badge { font-size: 0.85rem; padding: 0.5em 0.8em; }
        .table-responsive { background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="m-0">Plantillas de Certificados Word</h2>
                <p class="text-muted small">Administración de formatos oficiales para VINUS S.A.S</p>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php?controller=plantilla&action=crear" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nueva Plantilla
                </a>
                <a href="index.php?controller=auth&action=dashboard" class="btn btn-outline-secondary">Volver al Dashboard</a>
            </div>
        </div>

        <?php 
        $alertas = [
            'mensaje' => 'alert-success', 
            'mensaje_conversion' => 'alert-info', 
            'error' => 'alert-danger'
        ];
        foreach ($alertas as $key => $class): 
            if (isset($_SESSION[$key])): ?>
                <div class="alert <?php echo $class; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION[$key]); unset($_SESSION[$key]); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; 
        endforeach; ?>

        <?php if (empty($plantillas)): ?>
            <div class="alert alert-info border-0 shadow-sm text-center py-5">
                <i class="bi bi-file-earmark-word display-4 d-block mb-3"></i>
                <strong>No hay plantillas registradas.</strong><br>
                Cree una nueva plantilla para comenzar a generar certificados automáticos.
            </div>
        <?php else: ?>
            <div class="table-responsive shadow-sm">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">ID</th>
                            <th>Nombre de Formato</th>
                            <th>Descripción</th>
                            <th>Archivo Fuente</th>
                            <th>Estado</th>
                            <th>Creación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plantillas as $p): ?>
                            <tr>
                                <td class="fw-bold text-muted"><?php echo $p->id; ?></td>
                                <td><strong><?php echo htmlspecialchars($p->nombre); ?></strong></td>
                                <td class="small text-muted"><?php echo htmlspecialchars($p->descripcion ?: 'Sin descripción'); ?></td>
                                <td>
                                    <a href="public/plantillas/<?php echo htmlspecialchars($p->archivo); ?>" class="btn btn-sm btn-light border" download>
                                        <i class="bi bi-download"></i> Descargar .docx
                                    </a>
                                </td>
                                <td>
                                    <?php if ($p->activa == 1): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactiva</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small"><?php echo date('d/m/Y', strtotime($p->fecha_creacion)); ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <?php if ($p->activa != 1): ?>
                                            <a href="index.php?controller=plantilla&action=activar&id=<?php echo $p->id; ?>" 
                                               class="btn btn-sm btn-outline-success" 
                                               onclick="return confirm('¿Activar esta plantilla? El sistema usará este formato para todos los nuevos certificados.')">
                                                Activar
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="index.php?controller=plantilla&action=editar&id=<?php echo $p->id; ?>" 
                                           class="btn btn-sm btn-warning">
                                            Editar
                                        </a>
                                        
                                        <a href="index.php?controller=plantilla&action=eliminar&id=<?php echo $p->id; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('¿Está seguro de eliminar esta plantilla? Esta acción borrará el archivo físico del servidor.')">
                                            Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="alert alert-warning mt-4 border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill"></i> 
            <strong>Regla de Negocio:</strong> El sistema solo permite <strong>una (1)</strong> plantilla activa. Al activar una nueva, el resto se marcarán como inactivas automáticamente para garantizar la integridad de los certificados expedidos.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
