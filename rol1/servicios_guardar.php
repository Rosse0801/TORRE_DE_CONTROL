<?php
//rol1/servicios_guardar.php
session_start();
if (!isset($_SESSION['ID_USUARIO']) || (int)$_SESSION['ID_ROL'] !== 1) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../conexion.php';

$back = $_POST['_back'] ?? 'index.php';
$id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;

$nombre = trim($_POST['NOMBRE'] ?? '');
$cap    = isset($_POST['CAPACIDAD_SALAS']) ? (int)$_POST['CAPACIDAD_SALAS'] : 0;

if ($nombre === '') {
    header("Location: $back");
    exit();
}

if ($id > 0) {
    // Actualizar servicio existente
    $sql = "UPDATE tb_servicios SET NOMBRE=?, CAPACIDAD_SALAS=? WHERE ID_SERVICIO=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $nombre, $cap, $id);
    $stmt->execute();
    $stmt->close();
} else {
    // Insertar nuevo servicio
    $sql = "INSERT INTO tb_servicios (NOMBRE, CAPACIDAD_SALAS) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $nombre, $cap);
    $stmt->execute();
    $stmt->close();
}

header("Location: $back");
exit();
