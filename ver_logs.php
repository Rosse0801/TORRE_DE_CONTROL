<?php
// /ver_logs.php
session_start();

// Solo permitir acceso a administradores
if (!isset($_SESSION['ID_USUARIO']) || (int)$_SESSION['ID_ROL'] !== 1) {
    header("Location: login.php");
    exit();
}

$logs_dir = __DIR__ . '/logs';
$mes_actual = date('Y-m');
$mes_seleccionado = $_GET['mes'] ?? $mes_actual;

// Obtener lista de archivos de log disponibles
$archivos_log = [];
if (is_dir($logs_dir)) {
    $archivos = scandir($logs_dir);
    foreach ($archivos as $archivo) {
        if (preg_match('/^soporte_(\d{4}-\d{2})\.log$/', $archivo, $matches)) {
            $archivos_log[] = $matches[1];
        }
    }
    rsort($archivos_log); // Más recientes primero
}

// Leer log del mes seleccionado
$logs = [];
$archivo_log = "$logs_dir/soporte_$mes_seleccionado.log";
$total_tickets = 0;
$tickets_con_email_ok = 0;
$tickets_con_email_error = 0;

if (file_exists($archivo_log)) {
    $lineas = file($archivo_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        if (strpos($linea, 'TICKET:') !== false) {
            // Parsear línea de ticket
            preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) \| TICKET: ([A-Z0-9-]+) \| (\w+) \| (.+) \((.+)\) \| (.+)/', $linea, $matches);
            if ($matches) {
                $logs[] = [
                    'tipo' => 'ticket',
                    'fecha' => $matches[1],
                    'numero' => $matches[2],
                    'urgencia' => $matches[3],
                    'nombre' => $matches[4],
                    'email' => $matches[5],
                    'asunto' => $matches[6]
                ];
                $total_tickets++;
            }
        } elseif (strpos($linea, 'RESULTADO:') !== false) {
            // Parsear línea de resultado
            preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) \| RESULTADO: ([A-Z0-9-]+) \| Usuario: (\w+) \| Soporte: (\w+)/', $linea, $matches);
            if ($matches) {
                $logs[] = [
                    'tipo' => 'resultado',
                    'fecha' => $matches[1],
                    'numero' => $matches[2],
                    'email_usuario' => $matches[3],
                    'email_soporte' => $matches[4]
                ];
                
                if ($matches[3] === 'EMAIL_OK' && $matches[4] === 'EMAIL_OK') {
                    $tickets_con_email_ok++;
                } else {
                    $tickets_con_email_error++;
                }
            }
        }
    }
}

// Función para formatear urgencia
function formatear_urgencia($urgencia) {
    $clases = [
        'baja' => 'bg-success',
        'media' => 'bg-warning', 
        'alta' => 'bg-danger',
        'critica' => 'bg-dark'
    ];
    $clase = $clases[$urgencia] ?? 'bg-secondary';
    return "<span class='badge $clase'>" . ucfirst($urgencia) . "</span>";
}

// Función para formatear resultado de email
function formatear_email_resultado($usuario, $soporte) {
    if ($usuario === 'EMAIL_OK' && $soporte === 'EMAIL_OK') {
        return '<i class="bi bi-check-circle text-success me-1"></i> Ambos enviados';
    } elseif ($usuario === 'EMAIL_ERROR' && $soporte === 'EMAIL_ERROR') {
        return '<i class="bi bi-x-circle text-danger me-1"></i> Ambos fallaron';
    } elseif ($usuario === 'EMAIL_OK') {
        return '<i class="bi bi-exclamation-triangle text-warning me-1"></i> Solo usuario';
    } elseif ($soporte === 'EMAIL_OK') {
        return '<i class="bi bi-exclamation-triangle text-warning me-1"></i> Solo soporte';
    }
    return '<i class="bi bi-question-circle text-secondary me-1"></i> Desconocido';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Soporte - Torre de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/dashboard.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/includes/nav.php'; ?>

<div class="container py-4">
    
    <!-- Header -->
    <section class="welcome">
        <div class="welcome-card">
            <div class="welcome-inner">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>Logs de Soporte Técnico</h1>
                        <p class="mb-0 opacity-75">
                            <i class="bi bi-file-text me-1"></i>
                            Registro detallado de todos los tickets de soporte
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="dashboard.php" class="btn btn-light">
                            <i class="bi bi-house me-2"></i>Dashboard
                        </a>
                        <a href="soporte.php" class="btn btn-outline-primary ms-2">
                            <i class="bi bi-plus me-2"></i>Nuevo Ticket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Estadísticas del mes -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-ticket-detailed fs-1 mb-3"></i>
                    <h3 class="mb-2"><?= $total_tickets ?></h3>
                    <p class="mb-0">Total Tickets</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card success h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle fs-1 mb-3"></i>
                    <h3 class="mb-2"><?= $tickets_con_email_ok ?></h3>
                    <p class="mb-0">Emails OK</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card warning h-100">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle fs-1 mb-3"></i>
                    <h3 class="mb-2"><?= $tickets_con_email_error ?></h3>
                    <p class="mb-0">Emails Error</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card info h-100">
                <div class="card-body text-center">
                    <i class="bi bi-percent fs-1 mb-3"></i>
                    <h3 class="mb-2"><?= $total_tickets > 0 ? round(($tickets_con_email_ok / $total_tickets) * 100) : 0 ?>%</h3>
                    <p class="mb-0">Éxito Email</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Selector de mes y controles -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="chart-container">
                <h5 class="mb-3">
                    <i class="bi bi-calendar3 me-2" style="color: var(--brand-fuchsia);"></i>
                    Seleccionar Mes
                </h5>
                <form method="get" class="d-flex gap-2">
                    <select name="mes" class="form-select" onchange="this.form.submit()">
                        <?php foreach ($archivos_log as $mes): ?>
                            <option value="<?= $mes ?>" <?= $mes === $mes_seleccionado ? 'selected' : '' ?>>
                                <?= date('F Y', strtotime($mes . '-01')) ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if (empty($archivos_log)): ?>
                            <option value="<?= $mes_actual ?>" selected>
                                <?= date('F Y') ?> (Sin logs aún)
                            </option>
                        <?php endif; ?>
                    </select>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h5 class="mb-3">
                    <i class="bi bi-search me-2" style="color: var(--brand-fuchsia);"></i>
                    Buscar en Logs
                </h5>
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" id="buscarLog" class="form-control" 
                           placeholder="Buscar por ticket, nombre, email...">
                </div>
            </div>
        </div>
    </div>

    <!-- Logs -->
    <div class="row">
        <div class="col-12">
            <div class="table-modern">
                <div class="card-header bg-primary">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>
                        Logs del mes: <?= date('F Y', strtotime($mes_seleccionado . '-01')) ?>
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablaLogs">
                        <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Tipo</th>
                                <th>Ticket #</th>
                                <th>Usuario</th>
                                <th>Detalle</th>
                                <th>Estado Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($log['fecha'])) ?><br>
                                                <?= date('H:i:s', strtotime($log['fecha'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($log['tipo'] === 'ticket'): ?>
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-plus-circle me-1"></i>Nuevo
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-envelope me-1"></i>Email
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <code style="font-size: 0.85em;"><?= htmlspecialchars($log['numero']) ?></code>
                                        </td>
                                        <td>
                                            <?php if ($log['tipo'] === 'ticket'): ?>
                                                <div>
                                                    <strong><?= htmlspecialchars($log['nombre']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($log['email']) ?></small>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($log['tipo'] === 'ticket'): ?>
                                                <div>
                                                    <?= formatear_urgencia($log['urgencia']) ?>
                                                    <div class="mt-1">
                                                        <small><?= htmlspecialchars($log['asunto']) ?></small>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">Resultado de envío</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($log['tipo'] === 'resultado'): ?>
                                                <?= formatear_email_resultado($log['email_usuario'], $log['email_soporte']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Pendiente...</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                            <h5>No hay logs para este mes</h5>
                                            <p class="text-muted">
                                                <?php if (!file_exists($archivo_log)): ?>
                                                    El archivo de log no existe aún. Los tickets aparecerán aquí cuando se creen.
                                                <?php else: ?>
                                                    El archivo existe pero está vacío.
                                                <?php endif; ?>
                                            </p>
                                            <a href="soporte.php" class="btn btn-primary">
                                                <i class="bi bi-plus me-2"></i>Crear Primer Ticket
                                            </a>
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

    <!-- Información técnica -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="chart-container">
                <h5 class="mb-3">
                    <i class="bi bi-info-circle me-2" style="color: var(--brand-fuchsia);"></i>
                    Información Técnica
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Ubicación de Logs:</h6>
                        <code><?= htmlspecialchars($logs_dir) ?></code>
                        
                        <h6 class="mt-3">Archivo Actual:</h6>
                        <code><?= htmlspecialchars(basename($archivo_log)) ?></code>
                        
                        <h6 class="mt-3">Estado:</h6>
                        <?php if (file_exists($archivo_log)): ?>
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Archivo existe (<?= number_format(filesize($archivo_log)) ?> bytes)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Archivo no existe (se creará con el primer ticket)
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6>Archivos Disponibles:</h6>
                        <?php if (!empty($archivos_log)): ?>
                            <ul class="list-unstyled">
                                <?php foreach ($archivos_log as $mes): ?>
                                    <li>
                                        <i class="bi bi-file-text me-2"></i>
                                        soporte_<?= $mes ?>.log
                                        <?= $mes === $mes_seleccionado ? '<span class="badge bg-primary ms-2">Actual</span>' : '' ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No hay archivos de log creados aún.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5 py-4 footer-dashboard text-center">
    <div class="container">
        <p class="mb-0">&copy; 2025 Hospital General de México - Sistema de Logs</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Función de búsqueda en logs
document.getElementById('buscarLog').addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    const filas = document.querySelectorAll('#tablaLogs tbody tr');
    
    filas.forEach(fila => {
        if (fila.cells.length === 1) return; // Ignorar fila de "no hay datos"
        
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
});

// Auto-refresh cada 30 segundos para ver nuevos logs
setInterval(() => {
    if (document.visibilityState === 'visible') {
        location.reload();
    }
}, 30000);
</script>

</body>
</html>