php
<?php
//rol1/salas_guardar.php
session_start();
if (!isset($_SESSION['ID_USUARIO']) || (int)$_SESSION['ID_ROL'] !== 1) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../conexion.php';

function go_back(array $params = []) {
    $back = $_POST['_back'] ?? 'index.php';
    $sep = (strpos($back, '?') !== false) ? '&' : '?';
    if ($params) $back .= $sep . http_build_query($params);
    header("Location: $back");
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$id_servicio = isset($_POST['ID_SERVICIO']) ? (int)$_POST['ID_SERVICIO'] : 0;
$nombre = trim($_POST['NOMBRE'] ?? '');
$equipamiento = trim($_POST['EQUIPAMIENTO'] ?? '');

// CORRECCIÓN: Manejo correcto del checkbox
$activa = isset($_POST['ACTIVA']) ? 1 : 0;

if ($nombre === '' || $id_servicio === 0) {
    go_back(['error' => 'Faltan campos obligatorios']);
}

try {
    if ($id > 0) {
        // Actualizar sala existente
        $sql = "UPDATE tb_salas SET ID_SERVICIO=?, NOMBRE=?, EQUIPAMIENTO=?, ACTIVA=? WHERE ID_SALAS=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('issii', $id_servicio, $nombre, $equipamiento, $activa, $id);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();
        go_back(['success' => 'Sala actualizada exitosamente']);
        
    } else {
        // Insertar nueva sala
        $sql = "INSERT INTO tb_salas (ID_SERVICIO, NOMBRE, EQUIPAMIENTO, ACTIVA) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('issi', $id_servicio, $nombre, $equipamiento, $activa);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();
        go_back(['success' => 'Sala registrada exitosamente']);
    }
    
} catch (Exception $e) {
    error_log("Error guardando sala: " . $e->getMessage());
    go_back(['error' => 'Error al guardar la sala']);
}
?>