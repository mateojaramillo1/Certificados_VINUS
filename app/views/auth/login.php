<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Certificados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            width: 100%;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8b 100%);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h2 class="mb-0">Sistema de Certificados</h2>
            <p class="mb-0 mt-2">Iniciar Sesión</p>
        </div>
        <div class="login-body">
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

            <form action="index.php?controller=auth&action=login" method="POST">
                <div class="mb-3">
                    <label for="numero_documento" class="form-label">Número de Documento</label>
                    <input type="text" 
                           class="form-control form-control-lg" 
                           id="numero_documento" 
                           name="numero_documento" 
                           placeholder="Ingrese su número de documento" 
                           required 
                           autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" 
                           class="form-control form-control-lg" 
                           id="password" 
                           name="password" 
                           placeholder="Ingrese su contraseña" 
                           required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-login">
                    Ingresar
                </button>
            </form>

            <div class="text-center mt-3">
                <p class="mb-0">
                    ¿No tienes cuenta? 
                    <a href="index.php?controller=auth&action=showRegister" class="text-decoration-none">
                        Regístrate aquí
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


