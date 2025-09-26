<?php
// /rol1/salas_eliminar.php
session_start();
if (!isset($_SESSION['ID_USUARIO']) || (int)$_SESSION['ID_ROL'] !== 1) { header("Location: ../login.php"); exit(); }
require_once __DIR__ . '/../conexion.php';

$back = $_POST['_back'] ?? 'index.php';
$id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id > 0) {
  $stmt = $conn->prepare("DELETE FROM tb_salas WHERE ID_SALA=?");
  $stmt->bind_param('i', $id);
  @$stmt->execute();
  $stmt->close();
}

header("Location: $back"); exit();
