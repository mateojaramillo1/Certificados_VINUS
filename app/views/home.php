<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>VINUS S.A.S — Generar Certificado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        body { background-color: #f4f7f6; }
        .site-header { 
            background: #fff; 
            padding: 1rem 2rem; 
            display: flex; 
            align-items: center; 
            gap: 1.5rem; 
            border-bottom: 2px solid #0d6efd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .site-brand { font-weight: bold; font-size: 1.25rem; color: #333; }
        .search-container { 
            background: #fff; 
            padding: 2.5rem; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-top: 3rem;
        }
        .btn-search { background-color: #0d6efd; color: white; border: none; padding: 0.5rem 1.5rem; }
        .btn-search:hover { background-color: #0b5ed7; }
    </style>
</head>
<body>

    <div class="site-header">
        <img src="images/logo.png" alt="VINUS logo" style="height: 50px;" onerror="this.src='images/logo.svg'">
        <div class="site-brand">VINUS S.A.S — Talento Humano</div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="search-container">
                    <h1 class="h3 mb-4 text-center">Generar Certificado Laboral</h1>
                    <p class="text-muted text-center mb-4">Ingrese los datos del colaborador para buscar en la base de datos.</p>

                    <form action="index.php" method="get">
                        <input type="hidden" name="controller" value="empleado">
                        <input type="hidden" name="action" value="buscar">
                        
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Buscar por nombre, cédula o ID</label>
                            <div class="input-group">
                                <input name="q" type="search" 
                                       class="form-control form-control-lg" 
                                       placeholder="Ej: Juan Pérez o 10203040" 
                                       required 
                                       autofocus>
                                <button type="submit" class="btn btn-primary btn-lg">
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
                    <a href="index.php?controller=plantilla&action=index" class="btn btn-sm btn-outline-secondary">
                        Gestionar Plantillas Word
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
