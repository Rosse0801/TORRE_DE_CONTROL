<?php
// /perfil.php
session_start();
if (!isset($_SESSION['ID_USUARIO'])) { header("Location: /login.php"); exit(); }

require_once __DIR__ . '/conexion.php';

$nombre = $_SESSION['NOMBRE_USUARIO'] ?? 'Usuario';
$id     = (int)($_SESSION['ID_USUARIO'] ?? 0);
$rol    = (int)($_SESSION['ID_ROL'] ?? 0);
$rol_nombre = $_SESSION['ROL_NOMBRE'] ?? 'Sin rol';

// Obtener información adicional del usuario desde la BD si existe
$usuario_info = [
    'fecha_registro' => null,
    'ultimo_acceso' => null,
    'email' => null,
    'accesos_totales' => 0
];

// Intentar consultar información adicional del usuario solo si la tabla existe
try {
    $tabla_existe = $conn->query("SHOW TABLES LIKE 'usuarios'");
    if ($tabla_existe && $tabla_existe->num_rows > 0) {
        $stmt = $conn->prepare("SELECT DATE_FORMAT(fecha_registro, '%d/%m/%Y') as fecha_registro, 
                                       DATE_FORMAT(ultimo_acceso, '%d/%m/%Y %H:%i') as ultimo_acceso,
                                       email, accesos_totales 
                                FROM usuarios WHERE id_usuario = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $row = $result->fetch_assoc()) {
                $usuario_info = array_merge($usuario_info, array_filter($row));
            }
            $stmt->close();
        }
    }
} catch (Exception $e) {
    // Si hay error con la tabla usuarios, continuar sin esa información
}

// Obtener estadísticas del usuario si es administrador
$stats_usuario = null;
if ($rol === 1) {
    $stats_usuario = [];
    
    // Total de médicos registrados por este admin (si hay un campo created_by)
    $res = $conn->query("SELECT COUNT(*) as total FROM tb_anestesiologos");
    if ($res) {
        $stats_usuario['medicos_total'] = $res->fetch_assoc()['total'];
    }
    
    // Total de servicios
    $res = $conn->query("SELECT COUNT(*) as total FROM tb_servicios");
    if ($res) {
        $stats_usuario['servicios_total'] = $res->fetch_assoc()['total'];
    }
    
    // Total de salas
    $res = $conn->query("SELECT COUNT(*) as total FROM tb_salas");
    if ($res) {
        $stats_usuario['salas_total'] = $res->fetch_assoc()['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Torre de Control</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet"/>
    <link href="assets/dashboard.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/includes/nav.php'; ?>

<div class="container py-4">
    
    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <strong>¡Éxito!</strong> <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Error:</strong> <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Header con navegación -->
    <div class="welcome">
        <div class="welcome-card">
            <div class="welcome-inner">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <h1 class="mb-1">Mi Perfil</h1>
                                <p class="mb-0 opacity-75">
                                    <i class="bi bi-shield-check me-1"></i>
                                    <?= htmlspecialchars($nombre) ?> - <?= htmlspecialchars($rol_nombre) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a class="btn btn-light" href="dashboard.php">
                            <i class="bi bi-house me-2"></i>Dashboard
                        </a>
                        <?php if ($rol === 1): ?>
                            <a class="btn btn-outline-primary ms-2" href="rol1/index.php">
                                <i class="bi bi-gear me-2"></i>Gestión
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información del usuario -->
        <div class="col-lg-8 mb-4">
            <div class="card-outer">
                <div class="card-inner">
                    <div class="card-header bg-primary">
                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Información Personal</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Nombre de usuario</label>
                                    <div class="bg-light p-2 rounded">
                                        <strong><?= htmlspecialchars($nombre) ?></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">ID de Usuario</label>
                                    <div class="bg-light p-2 rounded">
                                        <span class="badge bg-light text-dark border">#<?= $id ?></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Rol del Sistema</label>
                                    <div class="bg-light p-2 rounded">
                                        <span class="badge" style="background: var(--brand-fuchsia);"><?= htmlspecialchars($rol_nombre) ?></span>
                                        <small class="text-muted ms-2">(Nivel <?= $rol ?>)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php if ($usuario_info['email']): ?>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Correo Electrónico</label>
                                    <div class="bg-light p-2 rounded">
                                        <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($usuario_info['email']) ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($usuario_info['fecha_registro']): ?>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Fecha de Registro</label>
                                    <div class="bg-light p-2 rounded">
                                        <i class="bi bi-calendar-plus me-2"></i><?= $usuario_info['fecha_registro'] ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted">Estado de la Cuenta</label>
                                    <div class="bg-light p-2 rounded">
                                        <span class="badge bg-success">Activa</span>
                                        <i class="bi bi-shield-check text-success ms-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de acciones y estadísticas -->
        <div class="col-lg-4 mb-4">
            <!-- Acciones rápidas -->
            <div class="chart-container mb-4">
                <h5 class="mb-3"><i class="bi bi-lightning me-2"></i>Acciones Rápidas</h5>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cambiarPasswordModal">
                        <i class="bi bi-key me-2"></i>Cambiar Contraseña
                    </button>
                    <?php if ($rol === 1): ?>
                    <a href="rol1/index.php" class="btn btn-outline-success">
                        <i class="bi bi-gear me-2"></i>Panel de Gestión
                    </a>
                    <?php endif; ?>
                    <a href="dashboard.php" class="btn btn-outline-info">
                        <i class="bi bi-house me-2"></i>Ir al Dashboard
                    </a>
                </div>
            </div>

            <!-- Estadísticas del usuario -->
            <?php if ($stats_usuario): ?>
            <div class="chart-container">
                <h5 class="mb-3"><i class="bi bi-graph-up me-2"></i>Mis Estadísticas</h5>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="fs-4 fw-bold" style="color: var(--brand-fuchsia);"><?= $stats_usuario['medicos_total'] ?? 0 ?></div>
                            <small class="text-muted">Médicos</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="fs-4 fw-bold" style="color: var(--brand-fuchsia);"><?= $stats_usuario['servicios_total'] ?? 0 ?></div>
                            <small class="text-muted">Servicios</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="fs-4 fw-bold" style="color: var(--brand-fuchsia);"><?= $stats_usuario['salas_total'] ?? 0 ?></div>
                            <small class="text-muted">Salas</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <div class="fs-4 fw-bold" style="color: var(--brand-fuchsia);"><?= $usuario_info['accesos_totales'] ?? 0 ?></div>
                            <small class="text-muted">Accesos</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Información de sesión -->
    <div class="row">
        <div class="col-12">
            <div class="card-outer">
                <div class="card-inner">
                    <div class="card-header bg-primary">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Información de Sesión</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="bi bi-calendar-event fs-1 mb-2" style="color: var(--brand-fuchsia);"></i>
                                    <h6>Sesión Actual</h6>
                                    <p class="text-muted mb-0"><?= date('d/m/Y H:i:s') ?></p>
                                </div>
                            </div>
                            <?php if ($usuario_info['ultimo_acceso']): ?>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="bi bi-clock fs-1 mb-2" style="color: var(--brand-fuchsia);"></i>
                                    <h6>Último Acceso</h6>
                                    <p class="text-muted mb-0"><?= $usuario_info['ultimo_acceso'] ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="bi bi-shield-check fs-1 mb-2" style="color: var(--brand-fuchsia);"></i>
                                    <h6>Seguridad</h6>
                                    <p class="text-muted mb-0">Sesión Segura</p>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <form action="logout.php" method="post" class="d-inline">
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Estás seguro de que deseas cerrar sesión?')">
                                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar contraseña -->
<div class="modal fade" id="cambiarPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, var(--brand-pink), var(--brand-fuchsia)); color: white;">
                <h5 class="modal-title"><i class="bi bi-key me-2"></i>Cambiar Contraseña</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="cambiar_password.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password_actual" class="form-label">Contraseña Actual</label>
                        <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_nueva" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="password_nueva" name="password_nueva" required minlength="6">
                        <div class="form-text">Mínimo 6 caracteres</div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmar" class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2 me-2"></i>Cambiar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Validar que las contraseñas coincidan
document.getElementById('cambiarPasswordModal').addEventListener('submit', function(e) {
    const nueva = document.getElementById('password_nueva').value;
    const confirmar = document.getElementById('password_confirmar').value;
    
    if (nueva !== confirmar) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        document.getElementById('password_confirmar').focus();
    }
});
</script>

</body>
</html>