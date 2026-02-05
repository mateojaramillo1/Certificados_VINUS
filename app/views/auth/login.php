<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VINUS S.A.S — Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css?v=20260202">
</head>
<body class="vinus-app auth-page auth-login">
    <div class="vinus-card auth-card p-0">
        <div class="auth-header text-center">
            <img src="images/logo.png" alt="VINUS" class="auth-logo mb-3" onerror="this.src='images/logo.svg'">
            <h2>VINUS S.A.S</h2>
            <p class="mb-0">Sistema de Certificados</p>
        </div>
        <div class="p-4">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong>
                    <?php
                        if ($_GET['error'] === 'credenciales') {
                            echo 'Número de documento o contraseña incorrectos';
                        } else {
                            echo 'Ocurrió un error. Por favor, intente nuevamente.';
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] === 'registro'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>¡Registro exitoso!</strong> Ahora puedes iniciar sesión.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form id="loginForm" action="index.php?controller=auth&action=login" method="POST">
                <div class="mb-4 floating-field">
                    <input type="text"
                           class="form-control form-control-lg"
                           id="numero_documento"
                           name="numero_documento"
                           placeholder=" "
                           required
                           autofocus>
                    <label for="numero_documento" class="floating-label">Ingrese su número de documento</label>
                </div>
                <button type="submit" class="btn btn-vinus btn-lg w-100">
                    Ingresar
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (event) => {
                if (loginForm.dataset.submitting === 'true') {
                    return;
                }
                event.preventDefault();
                loginForm.dataset.submitting = 'true';
                document.body.classList.add('is-loading');
                setTimeout(() => {
                    loginForm.submit();
                }, 150);
            });
        }
    </script>
</body>
</html>

