<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mejora UPBC</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

    <div class="card-form login-card">
        <h2 class="card-title text-center">Iniciar Sesión</h2>

        <form action="login.php" method="POST">
            <label class="form-label">Matrícula</label>
            <input type="text" name="matricula" class="form-input" placeholder="Ingresa tu matrícula" required>

            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-input" placeholder="Ingresa tu contraseña" required>

            <button type="submit" class="btn-submit btn-block">Iniciar sesión</button>
        </form>
    </div>

</body>
</html>