<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$perfilUrl = '/perfil.php';
$logoutUrl = '/logout.php';

// detectar “activo”
$current = basename($_SERVER['SCRIPT_NAME']);
function active($names){ global $current; return in_array($current, (array)$names) ? 'active' : ''; }
?>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/dashboard.php">
      <img src="/assets/img/logo.png" alt="HGM" class="rounded" style="width:32px;height:32px;object-fit:contain;">
      <span class="fw-semibold">HOSPITAL GENERAL DE MÉXICO</span>
      <small class="text-muted d-none d-md-inline">· Anestesiología</small>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBarMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navBarMain">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= active(['index.php']) ?>" href="/rol1/index.php">
            <i class="bi bi-people"></i> Médicos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= active(['dashboard.php']) ?>" href="/dashboard.php">
            <i class="bi bi-speedometer2"></i> Dashboard
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <a href="<?= htmlspecialchars($perfilUrl) ?>" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-person-badge"></i> Mi información
        </a>
        <div class="dropdown">
          <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
            <span class="avatar-circle">
              <?php
                $ini = 'US';
                if (!empty($_SESSION['NOMBRE_USUARIO'])) {
                  $p = preg_split('/\s+/', trim($_SESSION['NOMBRE_USUARIO']));
                  $ini = mb_strtoupper((mb_substr($p[0]??'',0,1).mb_substr($p[1]??'',0,1)) ?: 'US');
                }
                echo htmlspecialchars($ini);
              ?>
            </span>
            <span class="text-truncate" style="max-width:160px;">
              <?= htmlspecialchars($_SESSION['NOMBRE_USUARIO'] ?? 'Usuario') ?>
            </span>
            <span class="badge bg-primary-subtle text-primary ms-1">Rol <?= (int)($_SESSION['ID_ROL'] ?? 0) ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= htmlspecialchars($perfilUrl) ?>"><i class="bi bi-person"></i> Mi información</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form action="<?= htmlspecialchars($logoutUrl) ?>" method="post" class="px-3 py-1">
                <button type="submit" class="btn btn-danger btn-sm w-100"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>
<style>
  .avatar-circle{display:inline-grid;place-items:center;width:26px;height:26px;border-radius:999px;background:#e9ecef;font-weight:600;font-size:12px;}
</style>
