<?php
// /rol1/index.php
session_start();
if (!isset($_SESSION['ID_USUARIO'])) { header("Location: ../login.php"); exit(); }
if ((int)$_SESSION['ID_ROL'] !== 1) { header("Location: ../dashboard.php"); exit(); }

require_once __DIR__ . '/../conexion.php';

/* ===========================
   Catálogos
   =========================== */
$turnos = $tipos = $servicios = $salas = [];

/* Turnos */
if ($res = $conn->query("SELECT ID_TURNO, DESCRIPCION FROM tb_turnos ORDER BY DESCRIPCION")) {
  $turnos = $res->fetch_all(MYSQLI_ASSOC); $res->free();
}

/* Tipos de contrato */
if ($res = $conn->query("SELECT ID_TIPOCON, TIPO_CONTRATO FROM tb_tipo_contrato ORDER BY TIPO_CONTRATO")) {
  $tipos = $res->fetch_all(MYSQLI_ASSOC); $res->free();
}

/* Servicios */
if ($res = $conn->query("SELECT ID_SERVICIO, NOMBRE, CAPACIDAD_SALAS FROM tb_servicios ORDER BY NOMBRE")) {
  $servicios = $res->fetch_all(MYSQLI_ASSOC); $res->free();
}

/* Salas */
$sqlSalas = "SELECT s.ID_SALAS, s.NOMBRE, s.EQUIPAMIENTO, s.ACTIVA,
                    sv.ID_SERVICIO, sv.NOMBRE AS SERVICIO_NOMBRE
             FROM tb_salas s
             LEFT JOIN tb_servicios sv ON sv.ID_SERVICIO = s.ID_SERVICIO
             ORDER BY sv.NOMBRE, s.NOMBRE";
if ($res = $conn->query($sqlSalas)) {
  $salas = $res->fetch_all(MYSQLI_ASSOC); $res->free();
}

/* ===========================
   Médicos
   =========================== */
$sql = "SELECT
  a.ID_MEDICO,
  a.NUM_EMPLEADO,
  a.NOMBRE_MEDICO,
  a.APELLIDOS_MEDICO,
  a.ESPECIALIDAD,
  a.SERVICIO,
  a.TELEFONO,
  a.CORREO,
  DATE_FORMAT(a.FECHA_INGRESO, '%Y-%m-%d') AS FECHA_INGRESO,
  a.CEDULA_MEDICO,
  a.CEDULA_ANESTESIOLOGO,
  a.HORARIO_ENT,
  a.HORARIO_SAL,
  a.TURNO,
  a.DIAS_CUBRE,
  a.TIPO_CONTRATO,
  a.ID_TIPOCON,
  c.TIPO_CONTRATO AS TIPO_CONTRATO_NOM,
  a.CURP,
  a.RFC
FROM tb_anestesiologos a
LEFT JOIN tb_tipo_contrato c  ON c.ID_TIPOCON = a.ID_TIPOCON
ORDER BY a.NOMBRE_MEDICO, a.APELLIDOS_MEDICO";

$res = $conn->query($sql);
$medicos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Rol 1 · Gestión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="../assets/rol1.css">
</head>
<body class="bg-light">

<?php include __DIR__ . '/../includes/nav.php'; ?>

<section class="welcome">
  <div class="container">
    <div class="welcome-card">
      <div class="welcome-inner">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-collection fs-4"></i>
          <h1 class="m-0">Gestión del servicio</h1>
        </div>
        <p class="m-0 text-muted">Médicos, Servicios y Salas en un solo lugar.</p>
      </div>
    </div>
  </div>
</section>

<main class="container my-3">
  <div class="card-outer">
    <div class="card-inner p-3 p-md-4">

      <ul class="nav nav-tabs mb-3" id="tabsRegistro" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="tab-medicos" data-bs-toggle="tab" data-bs-target="#pane-medicos" type="button" role="tab">
            <i class="bi bi-people"></i> Médicos
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="tab-servicios" data-bs-toggle="tab" data-bs-target="#pane-servicios" type="button" role="tab">
            <i class="bi bi-diagram-3"></i> Servicios
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="tab-salas" data-bs-toggle="tab" data-bs-target="#pane-salas" type="button" role="tab">
            <i class="bi bi-door-closed"></i> Salas
          </button>
        </li>
      </ul>

      <div class="tab-content">

        <!-- ========= MÉDICOS ========= -->
        <div class="tab-pane fade show active" id="pane-medicos" role="tabpanel" aria-labelledby="tab-medicos">
          <?php if (!empty($medicos)): ?>
            <div class="row g-2">
              <?php foreach($medicos as $m): ?>
                <div class="col-12">
                  <div class="list-group-item d-flex justify-content-between align-items-center p-2 border rounded">
                    <div class="d-flex flex-column">
                      <span class="fw-bold">
                        <?= htmlspecialchars($m['NOMBRE_MEDICO'].' '.$m['APELLIDOS_MEDICO']) ?>
                        <span class="text-muted">· <?= htmlspecialchars($m['NUM_EMPLEADO'] ?: 's/n') ?></span>
                      </span>
                      <small class="text-muted">
                        <?= htmlspecialchars($m['SERVICIO'] ?: '—') ?> ·
                        <?= htmlspecialchars($m['TURNO'] ?: '—') ?> ·
                        <?= htmlspecialchars($m['TIPO_CONTRATO_NOM'] ?: '—') ?>
                      </small>
                    </div>
                    <div class="d-flex gap-1">
                      <button
                        class="btn btn-light btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalEditarMedico"
                        title="Editar"
                        data-id="<?= (int)$m['ID_MEDICO'] ?>"
                        data-num="<?= htmlspecialchars($m['NUM_EMPLEADO']) ?>"
                        data-nom="<?= htmlspecialchars($m['NOMBRE_MEDICO']) ?>"
                        data-ape="<?= htmlspecialchars($m['APELLIDOS_MEDICO']) ?>"
                        data-esp="<?= htmlspecialchars($m['ESPECIALIDAD']) ?>"
                        data-ser="<?= htmlspecialchars($m['SERVICIO']) ?>"
                        data-turno="<?= htmlspecialchars($m['TURNO']) ?>"
                        data-idtcon="<?= (int)$m['ID_TIPOCON'] ?>"
                        data-tel="<?= htmlspecialchars($m['TELEFONO']) ?>"
                        data-cor="<?= htmlspecialchars($m['CORREO']) ?>"
                        data-ing="<?= htmlspecialchars($m['FECHA_INGRESO']) ?>"
                        data-cm="<?= htmlspecialchars($m['CEDULA_MEDICO']) ?>"
                        data-ca="<?= htmlspecialchars($m['CEDULA_ANESTESIOLOGO']) ?>"
                        data-he="<?= htmlspecialchars($m['HORARIO_ENT']) ?>"
                        data-hs="<?= htmlspecialchars($m['HORARIO_SAL']) ?>"
                        data-dc="<?= htmlspecialchars($m['DIAS_CUBRE']) ?>"
                        data-curp="<?= htmlspecialchars($m['CURP']) ?>"
                        data-rfc="<?= htmlspecialchars($m['RFC']) ?>"
                      >
                        <i class="bi bi-pencil"></i>
                      </button>
                      <form action="medicos_eliminar.php" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar este registro?');">
                        <input type="hidden" name="id" value="<?= (int)$m['ID_MEDICO'] ?>">
                        <input type="hidden" name="_back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                        <button class="btn btn-light btn-sm" type="submit" title="Eliminar"><i class="bi bi-trash"></i></button>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-state text-center py-5">
              <i class="bi bi-people fs-1"></i>
              <p class="mt-2 mb-1">Aún no hay médicos registrados.</p>
              <small class="text-muted">Ve a <strong>Registrar</strong> para añadir el primero.</small>
            </div>
          <?php endif; ?>

          <hr class="my-4">
          <h5 class="mb-3"><i class="bi bi-plus-circle"></i> Registrar médico</h5>
          <form class="row g-3" action="medicos_guardar.php" method="post" novalidate id="formRegistrarMedico">
            <input type="hidden" name="_back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
            <div class="col-md-4"><label class="form-label">Número de empleado</label><input type="text" class="form-control" name="NUM_EMPLEADO" required></div>
            <div class="col-md-4"><label class="form-label">Nombre</label><input type="text" class="form-control" name="NOMBRE_MEDICO" required></div>
            <div class="col-md-4"><label class="form-label">Apellidos</label><input type="text" class="form-control" name="APELLIDOS_MEDICO" required></div>

            <div class="col-md-4"><label class="form-label">Especialidad</label><input type="text" class="form-control" name="ESPECIALIDAD" value="Anestesiología"></div>
            <div class="col-md-4"><label class="form-label">Servicio</label><input type="text" class="form-control" name="SERVICIO" placeholder="p.ej. Torre, Pediatría, Cardio…"></div>
            <div class="col-md-4">
              <label class="form-label">Tipo de contrato</label>
              <select class="form-select" name="ID_TIPOCON" required>
                <option value="">— Selecciona —</option>
                <?php foreach($tipos as $t): ?>
                  <option value="<?= (int)$t['ID_TIPOCON'] ?>"><?= htmlspecialchars($t['TIPO_CONTRATO']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Teléfono</label>
              <input type="text" class="form-control" name="TELEFONO" id="rTel" inputmode="numeric" maxlength="10" pattern="^\d{10}$" placeholder="10 dígitos">
              <div class="invalid-feedback">Debe tener 10 dígitos.</div>
            </div>
            <div class="col-md-4"><label class="form-label">Correo</label><input type="email" class="form-control" name="CORREO" id="rCorreo"></div>
            <div class="col-md-4"><label class="form-label">Fecha ingreso</label><input type="date" class="form-control" name="FECHA_INGRESO"></div>

            <div class="col-md-6"><label class="form-label">Cédula Médico</label><input type="text" class="form-control" name="CEDULA_MEDICO"></div>
            <div class="col-md-6"><label class="form-label">Cédula Anestesiólogo</label><input type="text" class="form-control" name="CEDULA_ANESTESIOLOGO"></div>

            <div class="col-md-3"><label class="form-label">Entrada</label><input type="time" class="form-control" name="HORARIO_ENT"></div>
            <div class="col-md-3"><label class="form-label">Salida</label><input type="time" class="form-control" name="HORARIO_SAL"></div>
            <div class="col-md-3">
              <label class="form-label">Turno</label>
              <input type="text" class="form-control" name="TURNO" placeholder="Ej. Matutino, Vespertino, Nocturno">
            </div>
            <div class="col-md-3"><label class="form-label">Días que cubre</label><input type="text" class="form-control" name="DIAS_CUBRE" placeholder="Ej. L-V, fines, guardias"></div>

            <div class="col-md-6"><label class="form-label">CURP</label><input type="text" class="form-control" name="CURP" id="rCurp" maxlength="18"><div class="invalid-feedback">CURP no válida.</div></div>
            <div class="col-md-6"><label class="form-label">RFC</label><input type="text" class="form-control" name="RFC" id="rRfc" maxlength="13"><div class="invalid-feedback">RFC no válido.</div></div>

            <div class="col-12 d-flex justify-content-end">
              <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle"></i> Guardar</button>
            </div>
          </form>

          <hr class="my-4">
          <h5 class="mb-3"><i class="bi bi-search"></i> Buscar</h5>
          <div class="row g-2 align-items-end mb-3">
            <div class="col-md-6">
              <label class="form-label">Buscar</label>
              <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input id="buscadorPane" class="form-control" placeholder="Cualquier campo: nombre, servicio, turno, cédula, CURP, RFC…">
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle" id="tablaBuscar">
              <thead>
                <tr>
                  <th>#Empleado</th><th>Nombre</th><th>Apellidos</th><th>Especialidad</th><th>Servicio</th><th>Contrato</th><th>Teléfono</th><th>Correo</th><th>Ingreso</th><th>Cédula Méd.</th><th>Cédula Anes.</th><th>Entrada</th><th>Salida</th><th>Turno</th><th>Días cubre</th><th>CURP</th><th>RFC</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($medicos as $m): ?>
                  <tr>
                    <td><?= htmlspecialchars($m['NUM_EMPLEADO']) ?></td>
                    <td><?= htmlspecialchars($m['NOMBRE_MEDICO']) ?></td>
                    <td><?= htmlspecialchars($m['APELLIDOS_MEDICO']) ?></td>
                    <td><?= htmlspecialchars($m['ESPECIALIDAD']) ?></td>
                    <td><?= htmlspecialchars($m['SERVICIO']) ?></td>
                    <td><?= htmlspecialchars($m['TIPO_CONTRATO_NOM']) ?></td>
                    <td><?= htmlspecialchars($m['TELEFONO']) ?></td>
                    <td><?= htmlspecialchars($m['CORREO']) ?></td>
                    <td><?= htmlspecialchars($m['FECHA_INGRESO']) ?></td>
                    <td><?= htmlspecialchars($m['CEDULA_MEDICO']) ?></td>
                    <td><?= htmlspecialchars($m['CEDULA_ANESTESIOLOGO']) ?></td>
                    <td><?= htmlspecialchars($m['HORARIO_ENT']) ?></td>
                    <td><?= htmlspecialchars($m['HORARIO_SAL']) ?></td>
                    <td><?= htmlspecialchars($m['TURNO']) ?></td>
                    <td><?= htmlspecialchars($m['DIAS_CUBRE']) ?></td>
                    <td><?= htmlspecialchars($m['CURP']) ?></td>
                    <td><?= htmlspecialchars($m['RFC']) ?></td>
                  </tr>
                <?php endforeach; if(empty($medicos)): ?>
                  <tr><td colspan="17" class="text-center text-muted">Sin resultados</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div><!-- /pane-medicos -->

        <!-- ========= SERVICIOS ========= -->
        <div class="tab-pane fade" id="pane-servicios" role="tabpanel" aria-labelledby="tab-servicios">
          <div class="row g-3">
            <div class="col-lg-6">
              <h5 class="mb-3"><i class="bi bi-diagram-3"></i> Servicios registrados</h5>
              <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                  <thead><tr><th>ID</th><th>Nombre</th><th>Capacidad</th><th></th></tr></thead>
                  <tbody>
                    <?php foreach($servicios as $s): ?>
                    <tr>
                      <td><?= (int)$s['ID_SERVICIO'] ?></td>
                      <td><?= htmlspecialchars($s['NOMBRE']) ?></td>
                      <td><?= (int)$s['CAPACIDAD_SALAS'] ?></td>
                      <td class="text-end">
                        <button class="btn btn-light btn-sm"
                          data-bs-toggle="modal" data-bs-target="#modalServicio"
                          data-id="<?= (int)$s['ID_SERVICIO'] ?>"
                          data-nombre="<?= htmlspecialchars($s['NOMBRE']) ?>"
                          data-capacidad="<?= (int)$s['CAPACIDAD_SALAS'] ?>">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <form action="servicios_eliminar.php" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar servicio?');">
                          <input type="hidden" name="id" value="<?= (int)$s['ID_SERVICIO'] ?>">
                          <input type="hidden" name="_back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                          <button class="btn btn-light btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; if(empty($servicios)): ?>
                    <tr><td colspan="4" class="text-muted text-center">Sin servicios</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-lg-6">
              <h5 class="mb-3"><i class="bi bi-plus-circle"></i> Nuevo servicio</h5>
              <form class="row g-3" action="servicios_guardar.php" method="post" id="formServicio">
                <input type="hidden" name="_back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                <div class="col-12"><label class="form-label">Nombre</label><input class="form-control" name="NOMBRE" required></div>
                <div class="col-12"><label class="form-label">Capacidad de salas</label><input class="form-control" type="number" name="CAPACIDAD_SALAS" min="0" value="0" required></div>
                <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle"></i> Guardar</button></div>
              </form>
            </div>
          </div>
        </div><!-- /pane-servicios -->

        <!-- ========= SALAS ========= -->
        <div class="tab-pane fade" id="pane-salas" role="tabpanel" aria-labelledby="tab-salas">
          <div class="row g-3">
            <div class="col-lg-7">
              <h5 class="mb-3"><i class="bi bi-door-closed"></i> Salas registradas</h5>
              <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                  <thead><tr><th>Servicio</th><th>Nombre</th><th>Equipamiento</th><th>Activa</th><th></th></tr></thead>
                  <tbody>
                    <?php foreach($salas as $sa): ?>
                    <tr>
                      <td><?= htmlspecialchars($sa['SERVICIO_NOMBRE']) ?></td>
                      <td><?= htmlspecialchars($sa['NOMBRE']) ?></td>
                      <td><?= htmlspecialchars($sa['EQUIPAMIENTO']) ?></td>
                      <td><?= ((int)$sa['ACTIVA'] ? 'Sí' : 'No') ?></td>
                      <td class="text-end">
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalSala"
                          data-id="<?= (int)$sa['ID_SALAS'] ?>"
                          data-idservicio="<?= (int)$sa['ID_SERVICIO'] ?>"
                          data-nombre="<?= htmlspecialchars($sa['NOMBRE']) ?>"
                          data-equipamiento="<?= htmlspecialchars($sa['EQUIPAMIENTO']) ?>"
                          data-activa="<?= (int)$sa['ACTIVA'] ?>">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <form action="salas_eliminar.php" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar sala?');">
                          <input type="hidden" name="id" value="<?= (int)$sa['ID_SALAS'] ?>">
                          <input type="hidden" name="_back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                          <button class="btn btn-light btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach; if(empty($salas)): ?>
                    <tr><td colspan="5" class="text-muted text-center">Sin salas</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-lg-5">
              <h5 class="mb-3"><i class="bi bi-plus-circle"></i> Nueva sala</h5>
              <form class="row g-3" action="salas_guardar.php" method="post" id="formSala">
                <input type="hidden" name="_back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                <div class="col-12">
                  <label class="form-label">Servicio</label>
                  <select class="form-select" name="ID_SERVICIO" required>
                    <option value="">— Selecciona —</option>
                    <?php foreach($servicios as $s): ?>
                      <option value="<?= (int)$s['ID_SERVICIO'] ?>"><?= htmlspecialchars($s['NOMBRE']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12"><label class="form-label">Nombre de la sala</label><input class="form-control" name="NOMBRE" required></div>
                <div class="col-12"><label class="form-label">Equipamiento</label><textarea class="form-control" name="EQUIPAMIENTO" rows="2"></textarea></div>
                <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="ACTIVA" id="sActiva" checked><label class="form-check-label" for="sActiva">Activa</label></div></div>
                <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle"></i> Guardar</button></div>
              </form>
            </div>
          </div>
        </div><!-- /pane-salas -->

      </div><!-- /tab-content -->

    </div>
  </div>
</main>

<!-- ========= MODALES ========= -->
<!-- Editar Médico -->
<div class="modal fade" id="modalEditarMedico" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <form class="modal-content" action="medicos_guardar.php" method="post" novalidate id="formEditarMedico">
      <input type="hidden" name="_back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
      <div class="modal-header">
        <h5 class="modal-title">Editar médico</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="eId">
        <div class="row g-3">
          <div class="col-md-3"><label class="form-label">#Empleado</label><input type="text" class="form-control" name="NUM_EMPLEADO" id="eNum"></div>
          <div class="col-md-3"><label class="form-label">Nombre</label><input type="text" class="form-control" name="NOMBRE_MEDICO" id="eNom" required></div>
          <div class="col-md-3"><label class="form-label">Apellidos</label><input type="text" class="form-control" name="APELLIDOS_MEDICO" id="eApe" required></div>
          <div class="col-md-3"><label class="form-label">Especialidad</label><input type="text" class="form-control" name="ESPECIALIDAD" id="eEsp" value="Anestesiología"></div>
          <div class="col-md-3"><label class="form-label">Servicio</label><input type="text" class="form-control" name="SERVICIO" id="eSer"></div>
          <div class="col-md-3">
            <label class="form-label">Contrato</label>
            <select class="form-select" name="ID_TIPOCON" id="eTcon" required>
              <option value="">— Selecciona —</option>
              <?php foreach($tipos as $t): ?>
                <option value="<?= (int)$t['ID_TIPOCON'] ?>"><?= htmlspecialchars($t['TIPO_CONTRATO']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3"><label class="form-label">Teléfono</label><input type="text" class="form-control" name="TELEFONO" id="eTel" inputmode="numeric" maxlength="10" pattern="^\d{10}$"><div class="invalid-feedback">Debe tener 10 dígitos.</div></div>
          <div class="col-md-3"><label class="form-label">Correo</label><input type="email" class="form-control" name="CORREO" id="eCor"><div class="invalid-feedback">Correo no válido.</div></div>
          <div class="col-md-3"><label class="form-label">Ingreso</label><input type="date" class="form-control" name="FECHA_INGRESO" id="eIng"></div>
          <div class="col-md-3"><label class="form-label">Cédula Médico</label><input type="text" class="form-control" name="CEDULA_MEDICO" id="eCm"></div>
          <div class="col-md-3"><label class="form-label">Cédula Anestesiólogo</label><input type="text" class="form-control" name="CEDULA_ANESTESIOLOGO" id="eCa"></div>
          <div class="col-md-3">
            <label class="form-label">Turno</label>
            <input type="text" class="form-control" name="TURNO" id="eTur" placeholder="Ej. Matutino, Vespertino, Nocturno">
          </div>
          <div class="col-md-3"><label class="form-label">Entrada</label><input type="time" class="form-control" name="HORARIO_ENT" id="eHe"></div>
          <div class="col-md-3"><label class="form-label">Salida</label><input type="time" class="form-control" name="HORARIO_SAL" id="eHs"></div>
          <div class="col-md-3"><label class="form-label">Días cubre</label><input type="text" class="form-control" name="DIAS_CUBRE" id="eDc"></div>
          <div class="col-md-3"><label class="form-label">CURP</label><input type="text" class="form-control" name="CURP" id="eCurp" maxlength="18"><div class="invalid-feedback">CURP no válida.</div></div>
          <div class="col-md-3"><label class="form-label">RFC</label><input type="text" class="form-control" name="RFC" id="eRfc" maxlength="13"><div class="invalid-feedback">RFC no válido.</div></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-primary" type="submit"><i class="bi bi-save2"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Editar Servicio -->
<div class="modal fade" id="modalServicio" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="servicios_guardar.php" method="post" id="formServicioEditar">
      <input type="hidden" name="_back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
      <input type="hidden" name="id" id="svId">
      <div class="modal-header"><h5 class="modal-title">Editar servicio</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">Nombre</label><input class="form-control" name="NOMBRE" id="svNombre" required></div>
        <div class="mb-2"><label class="form-label">Capacidad de salas</label><input class="form-control" type="number" name="CAPACIDAD_SALAS" id="svCapacidad" min="0" required></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-primary" type="submit"><i class="bi bi-save2"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Editar Sala -->
<div class="modal fade" id="modalSala" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="salas_guardar.php" method="post" id="formSalaEditar">
      <input type="hidden" name="_back" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
      <input type="hidden" name="id" id="saId">
      <div class="modal-header"><h5 class="modal-title">Editar sala</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">Servicio</label>
          <select class="form-select" name="ID_SERVICIO" id="saIdServicio" required>
            <option value="">— Selecciona —</option>
            <?php foreach($servicios as $s): ?>
              <option value="<?= (int)$s['ID_SERVICIO'] ?>"><?= htmlspecialchars($s['NOMBRE']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-2"><label class="form-label">Nombre</label><input class="form-control" name="NOMBRE" id="saNombre" required></div>
        <div class="mb-2"><label class="form-label">Equipamiento</label><textarea class="form-control" name="EQUIPAMIENTO" id="saEquipamiento" rows="2"></textarea></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="ACTIVA" id="saActiva"><label class="form-check-label" for="saActiva">Activa</label></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-primary" type="submit"><i class="bi bi-save2"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS modular -->
<script type="module" src="../assets/js/medicos.js"></script>
<script type="module" src="../assets/js/servicios.js"></script>
<script type="module" src="../assets/js/salas.js"></script>
</body>
</html>