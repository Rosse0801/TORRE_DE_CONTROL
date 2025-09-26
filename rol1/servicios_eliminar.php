<?php
// /rol1/servicios_eliminar.php
session_start();
if (!isset($_SESSION['ID_USUARIO']) || (int)$_SESSION['ID_ROL'] !== 1) { header("Location: ../login.php"); exit(); }
require_once __DIR__ . '/../conexion.php';

$back = $_POST['_back'] ?? 'index.php';
$id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id > 0) {
  // Nota: si hay FK en Tb_salas, esto fallará si tiene salas asociadas.
  // Puedes optar por ON DELETE RESTRICT o primero reubicar/eliminar salas.
  $stmt = $conn->prepare("DELETE FROM tb_servicios WHERE ID_SERVICIO=?");
  $stmt->bind_param('i', $id);
  @$stmt->execute();
  $stmt->close();
}

header("Location: $back"); exit();

