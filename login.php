<?php
$mostrar_error = isset($_GET['error']) && $_GET['error'] == 1;
$reset_success = isset($_GET['reset_success']) && $_GET['reset_success'] == 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Área de Anestesiología</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet" />
  <!-- Fuente -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <!-- Tu CSS -->
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

      <div class="title">ÁREA DE ANESTESIOLOGÍA</div>
      <div class="subtitle">inicio de sesión</div>

      <!-- Alerta de éxito por reset de contraseña -->
      <?php if ($reset_success): ?>
        <div class="alert alert-success my-3">
          <i class="bi bi-check-circle me-2"></i>
          <strong>¡Contraseña actualizada!</strong> Ya puedes iniciar sesión con tu nueva contraseña.
        </div>
      <?php endif; ?>

      <!-- Alerta si hay error -->
      <?php if ($mostrar_error): ?>
        <div class="login-alert my-3">
          Usuario o contraseña incorrectos. Respira hondo e intenta de nuevo.
        </div>
      <?php endif; ?>

      <div class="user-icon">
        <i class="bi bi-person-circle" aria-hidden="true"></i>
      </div>

      <form action="validar_login.php" method="post" novalidate>
        <div class="mb-3 text-start">
          <label class="form-label" for="usuario">Usuario:</label>
          <input id="usuario" type="email" class="form-control" name="usuario" placeholder="ejemplo@hospital.com" required />
        </div>

        <div class="mb-2 text-start">
          <label class="form-label" for="contrasena">Contraseña:</label>
          <input id="contrasena" type="password" class="form-control" name="contrasena" placeholder="••••••••" required />
        </div>

        <div class="remember-wrap mb-3">
          <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
          <label class="form-check-label" for="remember">Recordar usuario</label>
        </div>

        <button type="submit" class="btn btn-login">INGRESAR</button>

        <div class="help-links">
          <a href="recuperar_password.php">
            <i class="bi bi-key me-1"></i>¿Olvidó su contraseña?
          </a>
          <span class="sep">|</span>
          <a href="soporte.php">
            <i class="bi bi-headset me-1"></i>Soporte
          </a>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  // Auto-ocultar mensajes de éxito después de 5 segundos
  document.addEventListener('DOMContentLoaded', function() {
    const alert = document.querySelector('.alert-success');
    if (alert) {
      setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      }, 5000);
    }
  });
  </script>
</body>
</html>