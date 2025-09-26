<?php
// /rol1/medicos_eliminar.php
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

if (empty($_POST['id'])) go_back(['err'=>'ID_invalido']);
$id = (int)$_POST['id'];

try {
  $stmt = $conn->prepare("DELETE FROM tb_anestesiologos WHERE ID_MEDICO = ?");
  if (!$stmt) go_back(['err'=>'Prep_fail']);
  $stmt->bind_param("i", $id);
  if (!$stmt->execute()) throw new Exception($stmt->error, $stmt->errno);
  $stmt->close();
  go_back(['ok'=>'del']);
} catch (Exception $e) {
  $code = $e->getCode();
  if ($code == 1451) {
    // tiene hijos (vacaciones, productividad, etc.)
    go_back(['err'=>'No_se_puede_eliminar_por_registros_relacionados']);
  } else {
    go_back(['err'=>"Error_$code"]);
  }
}