<?php
// /recuperar_password.php
session_start();
if (isset($_SESSION['ID_USUARIO'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperar Contraseña - Área de Anestesiología</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/login.css">
</head>
<body>

<div class="card-outer">
    <div class="login-card">
        <div class="top-accent"></div>

        <div class="logo-wrap">
            <img class="logo" src="assets/img/logo.png" alt="Logo Hospital">
            <span class="logo-text">HOSPITAL GENERAL DE MÉXICO</span>
        </div>

        <div class="title">RECUPERAR CONTRASEÑA</div>
        <div class="subtitle">ingresa tu correo electrónico</div>

        <!-- Mensajes de estado -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success my-3">
                <i class="bi bi-check-circle me-2"></i>
                Se ha enviado un correo con las instrucciones para recuperar tu contraseña.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="login-alert my-3">
                <?php 
                $error = $_GET['error'];
                if ($error === 'not_found') {
                    echo 'No se encontró una cuenta con ese correo electrónico.';
                } elseif ($error === 'email_error') {
                    echo 'Error al enviar el correo. Intenta nuevamente.';
                } else {
                    echo 'Ocurrió un error. Intenta nuevamente.';
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="user-icon">
            <i class="bi bi-envelope-at" aria-hidden="true"></i>
        </div>

        <form action="procesar_recuperacion.php" method="post" novalidate>
            <div class="mb-3 text-start">
                <label class="form-label" for="email">Correo Electrónico:</label>
                <input id="email" type="email" class="form-control" name="email" 
                       placeholder="tu-email@hospital.com" required 
                       value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" />
                <div class="form-text">
                    Ingresa el correo asociado a tu cuenta para recibir las instrucciones de recuperación.
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-send me-2"></i>ENVIAR INSTRUCCIONES
            </button>

            <div class="help-links">
                <a href="login.php">
                    <i class="bi bi-arrow-left me-1"></i>Volver al Login
                </a>
                <span class="sep">|</span>
                <a href="soporte.php">Contactar Soporte</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>