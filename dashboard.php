<?php
session_start();
if (!isset($_SESSION['ID_USUARIO'])) {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';

// Obtener estadísticas
$stats = [
    'medicos_total' => 0,
    'medicos_por_turno' => [],
    'salas_total' => 0,
    'salas_activas' => 0,
    'servicios_total' => 0,
    'medicos_por_servicio' => []
];

// Contar médicos total
$res = $conn->query("SELECT COUNT(*) as total FROM tb_anestesiologos");
if ($res) {
    $stats['medicos_total'] = $res->fetch_assoc()['total'];
}

// Médicos por turno
$res = $conn->query("SELECT TURNO, COUNT(*) as cantidad FROM tb_anestesiologos WHERE TURNO IS NOT NULL AND TURNO != '' GROUP BY TURNO ORDER BY cantidad DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $stats['medicos_por_turno'][] = $row;
    }
}

// Médicos por servicio
$res = $conn->query("SELECT SERVICIO, COUNT(*) as cantidad FROM tb_anestesiologos WHERE SERVICIO IS NOT NULL AND SERVICIO != '' GROUP BY SERVICIO ORDER BY cantidad DESC LIMIT 5");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $stats['medicos_por_servicio'][] = $row;
    }
}

// Contar salas
$res = $conn->query("SELECT COUNT(*) as total, SUM(ACTIVA) as activas FROM tb_salas");
if ($res) {
    $row = $res->fetch_assoc();
    $stats['salas_total'] = $row['total'];
    $stats['salas_activas'] = $row['activas'] ?: 0;
}

// Contar servicios
$res = $conn->query("SELECT COUNT(*) as total FROM tb_servicios");
if ($res) {
    $stats['servicios_total'] = $res->fetch_assoc()['total'];
}

// Obtener salas por servicio
$salas_por_servicio = [];
$res = $conn->query("
    SELECT 
        s.NOMBRE AS servicio_nombre, 
        sa.NOMBRE AS sala_nombre, 
        sa.ACTIVA,
        sa.EQUIPAMIENTO
    FROM tb_servicios s
    LEFT JOIN tb_salas sa ON sa.ID_SERVICIO = s.ID_SERVICIO 
    ORDER BY s.NOMBRE, sa.NOMBRE
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $servicio = $row['servicio_nombre'];
        if (!isset($salas_por_servicio[$servicio])) {
            $salas_por_servicio[$servicio] = [];
        }
        if ($row['sala_nombre']) {
            $salas_por_servicio[$servicio][] = $row;
        }
    }
}

// Últimos médicos registrados
$ultimos_medicos = [];
$res = $conn->query("
    SELECT 
        NOMBRE_MEDICO, 
        APELLIDOS_MEDICO, 
        SERVICIO, 
        TURNO,
        DATE_FORMAT(FECHA_INGRESO, '%d/%m/%Y') as fecha_ingreso
    FROM tb_anestesiologos 
    ORDER BY ID_MEDICO DESC 
    LIMIT 5
");
if ($res) {
    $ultimos_medicos = $res->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Torre de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/dashboard.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dashboard">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-activity me-2"></i>Torre de Control
        </a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">
                <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['NOMBRE_USUARIO']) ?>
            </span>
            <a class="nav-link text-white" href="logout.php">
                <i class="bi bi-box-arrow-right me-1"></i>Salir
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!-- Sección de bienvenida -->
    <section class="welcome">
        <div class="welcome-card">
            <div class="welcome-inner">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>¡Bienvenido, <?= htmlspecialchars($_SESSION['NOMBRE_USUARIO']) ?>!</h1>
                        <p class="mb-2 opacity-75">
                            <i class="bi bi-shield-check me-1"></i>
                            Rol: <?= htmlspecialchars($_SESSION['ROL_NOMBRE']) ?> 
                            <span class="badge bg-light text-dark ms-2">#<?= $_SESSION['ID_ROL'] ?></span>
                        </p>
                        <p class="mb-0 opacity-75">
                            <i class="bi bi-clock me-1"></i>
                            Última conexión: <?= date('d/m/Y H:i') ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <?php if ((int)$_SESSION['ID_ROL'] === 1): ?>
                            <a href="rol1/index.php" class="btn btn-light btn-lg">
                                <i class="bi bi-gear me-2"></i>Gestión del Sistema
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill fs-1 mb-3"></i>
                    <h3 class="mb-2"><?= number_format($stats['medicos_total']) ?></h3>
                    <p class="mb-0">Médicos Registrados</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card success h-100">
                <div class="card-body text-center">
                    <i class="bi bi-door-open-fill fs-1 mb-3"></i>
                    <h3 class="mb-2"><?= $stats['salas_activas'] ?> / <?= $stats['salas_total'] ?></h3>
                    <p class="mb-0">Salas Activas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card warning h-100">
                <div class="card-body text-center">
                    <i class="bi bi-diagram-3-fill fs-1 mb-3"></i>
                    <h3 class="mb-2"><?= number_format($stats['servicios_total']) ?></h3>
                    <p class="mb-0">Servicios Disponibles</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card info h-100">
                <div class="card-body text-center">
                    <i class="bi bi-percent fs-1 mb-3"></i>
                    <h3 class="mb-2"><?= $stats['salas_total'] > 0 ? round(($stats['salas_activas'] / $stats['salas_total']) * 100) : 0 ?>%</h3>
                    <p class="mb-0">Ocupación de Salas</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Médicos por turno -->
        <div class="col-lg-4 mb-4">
            <div class="chart-container">
                <h5 class="mb-4"><i class="bi bi-clock-history me-2"></i>Médicos por Turno</h5>
                <?php if (!empty($stats['medicos_por_turno'])): ?>
                    <?php foreach($stats['medicos_por_turno'] as $turno): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium"><?= htmlspecialchars($turno['TURNO']) ?></span>
                                <span class="text-muted"><?= $turno['cantidad'] ?> médicos</span>
                            </div>
                            <div class="progress progress-custom">
                                <div class="progress-bar" style="width: <?= $stats['medicos_total'] > 0 ? ($turno['cantidad'] / $stats['medicos_total']) * 100 : 0 ?>%; background: linear-gradient(90deg, var(--brand-pink), var(--brand-fuchsia));"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state text-center py-3">
                        <i class="bi bi-clock fs-1"></i>
                        <p class="text-muted mt-2">No hay datos de turnos disponibles</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Servicios principales -->
        <div class="col-lg-4 mb-4">
            <div class="chart-container">
                <h5 class="mb-4"><i class="bi bi-hospital me-2"></i>Servicios Principales</h5>
                <?php if (!empty($stats['medicos_por_servicio'])): ?>
                    <?php foreach($stats['medicos_por_servicio'] as $servicio): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium"><?= htmlspecialchars($servicio['SERVICIO']) ?></span>
                                <span class="text-muted"><?= $servicio['cantidad'] ?> médicos</span>
                            </div>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-success" style="width: <?= $stats['medicos_total'] > 0 ? ($servicio['cantidad'] / $stats['medicos_total']) * 100 : 0 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state text-center py-3">
                        <i class="bi bi-hospital fs-1"></i>
                        <p class="text-muted mt-2">No hay datos de servicios disponibles</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Estado de salas -->
        <div class="col-lg-4 mb-4">
            <div class="chart-container">
                <h5 class="mb-4"><i class="bi bi-building me-2"></i>Estado de Salas</h5>
                <?php if (!empty($salas_por_servicio)): ?>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <?php foreach($salas_por_servicio as $servicio => $salas): ?>
                            <div class="mb-3">
                                <h6 class="mb-2" style="color: var(--brand-fuchsia); font-weight: 700;"><?= htmlspecialchars($servicio) ?></h6>
                                <?php foreach($salas as $sala): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-1 ps-3">
                                        <small class="text-muted"><?= htmlspecialchars($sala['sala_nombre']) ?></small>
                                        <span class="badge <?= $sala['ACTIVA'] ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $sala['ACTIVA'] ? 'Activa' : 'Inactiva' ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state text-center py-3">
                        <i class="bi bi-building fs-1"></i>
                        <p class="text-muted mt-2">No hay salas registradas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Últimos médicos registrados -->
    <div class="row">
        <div class="col-12">
            <div class="table-modern">
                <div class="card-header bg-primary">
                    <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Últimos Médicos Registrados</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Médico</th>
                                <th>Servicio</th>
                                <th>Turno</th>
                                <th>Fecha Ingreso</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ultimos_medicos)): ?>
                                <?php foreach($ultimos_medicos as $medico): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="icon-circle me-2">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium"><?= htmlspecialchars($medico['NOMBRE_MEDICO'] . ' ' . $medico['APELLIDOS_MEDICO']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark"><?= htmlspecialchars($medico['SERVICIO'] ?: 'No asignado') ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($medico['TURNO'] ?: 'No asignado') ?></td>
                                        <td><?= htmlspecialchars($medico['fecha_ingreso'] ?: 'No registrada') ?></td>
                                        <td><span class="badge bg-success">Activo</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                            <p class="text-muted">No hay médicos registrados</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas para admin -->
    <?php if ((int)$_SESSION['ID_ROL'] === 1): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-outer">
                <div class="card-inner">
                    <div class="card-header bg-primary">
                        <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Acciones Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="rol1/index.php" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-person-plus me-2"></i>Registrar Médico
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="rol1/index.php#pane-servicios" class="btn btn-outline-success w-100">
                                    <i class="bi bi-diagram-3 me-2"></i>Gestionar Servicios
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="rol1/index.php#pane-salas" class="btn btn-outline-warning w-100">
                                    <i class="bi bi-door-closed me-2"></i>Gestionar Salas
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-outline-info w-100" onclick="location.reload()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Actualizar Datos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<footer class="mt-5 py-4 footer-dashboard text-center">
    <div class="container">
        <p class="mb-0">&copy; 2025 Sistema Torre de Control - Gestión Hospitalaria</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>