<?php
// /rol1/medicos_guardar.php
session_start();
if (!isset($_SESSION['ID_USUARIO'])) { header("Location: ../login.php"); exit(); }
if ((int)$_SESSION['ID_ROL'] !== 1) { header("Location: ../dashboard.php"); exit(); }

require_once __DIR__ . '/../conexion.php';

function go_back(array $params = []) {
  $back = $_POST['_back'] ?? ($_SERVER['HTTP_REFERER'] ?? '/rol1/index.php');
  $sep  = (strpos($back, '?') !== false) ? '&' : '?';
  if ($params) $back .= $sep . http_build_query($params);
  header("Location: $back");
  exit();
}

function field_or_null($name) {
  if (!isset($_POST[$name])) return null;
  $v = trim((string)$_POST[$name]);
  return $v === '' ? null : $v;
}
function int_or_null($name) {
  if (!isset($_POST[$name]) || $_POST[$name] === '') return null;
  return (int)$_POST[$name];
}

// Captura
$id                  = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
$NUM_EMPLEADO        = field_or_null('NUM_EMPLEADO');
$NOMBRE_MEDICO       = field_or_null('NOMBRE_MEDICO');
$APELLIDOS_MEDICO    = field_or_null('APELLIDOS_MEDICO');
$ESPECIALIDAD        = field_or_null('ESPECIALIDAD');
$SERVICIO            = field_or_null('SERVICIO');
$TELEFONO            = field_or_null('TELEFONO');
$CORREO              = field_or_null('CORREO');
$FECHA_INGRESO       = field_or_null('FECHA_INGRESO');
$CEDULA_MEDICO       = field_or_null('CEDULA_MEDICO');
$CEDULA_ANESTESIOLOGO= field_or_null('CEDULA_ANESTESIOLOGO');
$HORARIO_ENT         = field_or_null('HORARIO_ENT');
$HORARIO_SAL         = field_or_null('HORARIO_SAL');
$TURNO               = field_or_null('TURNO');
$DIAS_CUBRE          = field_or_null('DIAS_CUBRE');
$TIPO_CONTRATO       = field_or_null('TIPO_CONTRATO');
$ID_TIPOCON          = int_or_null('ID_TIPOCON');
$CURP                = field_or_null('CURP');
$RFC                 = field_or_null('RFC');

// Reglas mínimas
if (!$NOMBRE_MEDICO || !$APELLIDOS_MEDICO || !$ID_TIPOCON) {
  go_back(['err'=>'Faltan_campos_obligatorios']);
}

// Normaliza
if ($CURP) $CURP = mb_strtoupper($CURP, 'UTF-8');
if ($RFC)  $RFC  = mb_strtoupper($RFC,  'UTF-8');

// RFC válido: 12 (moral o PF antigua) o 13 (física)
if ($RFC) {
  $rfc_ok = preg_match('/^([A-ZÑ&]{3,4})(\d{2})(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[A-Z0-9]{2}[0-9A-Z]?$/i', $RFC) === 1;
  if (!$rfc_ok) go_back(['err'=>'RFC_invalido']);
}

try {
  if ($id === null) {
    // INSERT
    $sql = "INSERT INTO tb_anestesiologos
      (NUM_EMPLEADO, NOMBRE_MEDICO, APELLIDOS_MEDICO, ESPECIALIDAD, SERVICIO, TELEFONO, CORREO,
       FECHA_INGRESO, CEDULA_MEDICO, CEDULA_ANESTESIOLOGO, HORARIO_ENT, HORARIO_SAL,
       TURNO, DIAS_CUBRE, TIPO_CONTRATO, ID_TIPOCON, CURP, RFC)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) go_back(['err'=>'Prep_insert']);
    $stmt->bind_param(
      "sssssssssssssssiss",
      $NUM_EMPLEADO, $NOMBRE_MEDICO, $APELLIDOS_MEDICO, $ESPECIALIDAD, $SERVICIO, $TELEFONO, $CORREO,
      $FECHA_INGRESO, $CEDULA_MEDICO, $CEDULA_ANESTESIOLOGO, $HORARIO_ENT, $HORARIO_SAL,
      $TURNO, $DIAS_CUBRE, $TIPO_CONTRATO, $ID_TIPOCON, $CURP, $RFC
    );
    if (!$stmt->execute()) throw new Exception($stmt->error, $stmt->errno);
    $stmt->close();
  } else {
    // UPDATE
    $sql = "UPDATE tb_anestesiologos SET
        NUM_EMPLEADO=?, NOMBRE_MEDICO=?, APELLIDOS_MEDICO=?, ESPECIALIDAD=?, SERVICIO=?, TELEFONO=?, CORREO=?,
        FECHA_INGRESO=?, CEDULA_MEDICO=?, CEDULA_ANESTESIOLOGO=?, HORARIO_ENT=?, HORARIO_SAL=?,
        TURNO=?, DIAS_CUBRE=?, TIPO_CONTRATO=?, ID_TIPOCON=?, CURP=?, RFC=?
      WHERE ID_MEDICO=?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) go_back(['err'=>'Prep_update']);
    $stmt->bind_param(
      "ssssssssssssssisssi",
      $NUM_EMPLEADO, $NOMBRE_MEDICO, $APELLIDOS_MEDICO, $ESPECIALIDAD, $SERVICIO, $TELEFONO, $CORREO,
      $FECHA_INGRESO, $CEDULA_MEDICO, $CEDULA_ANESTESIOLOGO, $HORARIO_ENT, $HORARIO_SAL,
      $TURNO, $DIAS_CUBRE, $TIPO_CONTRATO, $ID_TIPOCON, $CURP, $RFC, $id
    );
    if (!$stmt->execute()) throw new Exception($stmt->error, $stmt->errno);
    $stmt->close();
  }

  go_back(['ok'=>1]);

} catch (Exception $e) {
  $msg = $e->getMessage(); $code = $e->getCode();
  if ($code == 1062) { // Duplicado
    if (stripos($msg, 'uk_num_empleado') !== false) $dup = 'NUM_EMPLEADO';
    elseif (stripos($msg, 'uk_correo') !== false) $dup = 'CORREO';
    elseif (stripos($msg, 'uk_curp') !== false) $dup = 'CURP';
    elseif (stripos($msg, 'uk_rfc') !== false) $dup = 'RFC';
    elseif (stripos($msg, 'uk_cedula_medico') !== false) $dup = 'CEDULA_MEDICO';
    elseif (stripos($msg, 'uk_cedula_anestesiologo') !== false) $dup = 'CEDULA_ANESTESIOLOGO';
    else $dup = 'registro';
    go_back(['err'=>"Duplicado_en_$dup"]);
  } else {
    go_back(['err'=>"Error_$code"]);
  }
}