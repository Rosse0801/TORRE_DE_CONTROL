<?php
// /cambiar_password.php
session_start();
if (!isset($_SESSION['ID_USUARIO'])) { 
    header("Location: login.php"); 
    exit(); 
}

require_once __DIR__ . '/conexion.php';

function go_back($params = []) {
    $back = $_SERVER['HTTP_REFERER'] ?? 'perfil.php';
    $sep = (strpos($back, '?') !== false) ? '&' : '?';
    if ($params) $back .= $sep . http_build_query($params);
    header("Location: $back");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    go_back(['error' => 'Método no permitido']);
}

// Obtener datos del formulario
$password_actual = $_POST['password_actual'] ?? '';
$password_nueva = $_POST['password_nueva'] ?? '';
$password_confirmar = $_POST['password_confirmar'] ?? '';
$user_id = (int)$_SESSION['ID_USUARIO'];

// Validaciones básicas
if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
    go_back(['error' => 'Todos los campos son obligatorios']);
}

if (strlen($password_nueva) < 6) {
    go_back(['error' => 'La nueva contraseña debe tener al menos 6 caracteres']);
}

if ($password_nueva !== $password_confirmar) {
    go_back(['error' => 'Las nuevas contraseñas no coinciden']);
}

if ($password_actual === $password_nueva) {
    go_back(['error' => 'La nueva contraseña debe ser diferente a la actual']);
}

try {
    // Usar tu tabla tb_usuarios con los campos correctos
    $tabla_auth = 'tb_usuarios';
    $campo_id = 'ID_USUARIO';
    $campo_password = 'CONTRASENA';
    
    // Verificar que la tabla existe
    $result = $conn->query("SHOW TABLES LIKE '$tabla_auth'");
    if (!$result || $result->num_rows === 0) {
        go_back(['error' => 'No se encontró la tabla de usuarios']);
    }
    
    // Obtener contraseña actual de la BD
    $stmt = $conn->prepare("SELECT $campo_password FROM $tabla_auth WHERE $campo_id = ?");
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result || $result->num_rows === 0) {
        $stmt->close();
        go_back(['error' => 'Usuario no encontrado']);
    }
    
    $usuario = $result->fetch_assoc();
    $stmt->close();
    
    // Verificar contraseña actual
    $password_bd = $usuario[$campo_password];
    
    // Intentar verificar tanto con hash como texto plano (para migración)
    $password_valida = false;
    
    if (strlen($password_bd) === 60 && substr($password_bd, 0, 4) === '$2y$') {
        // Es un hash de password_hash()
        $password_valida = password_verify($password_actual, $password_bd);
    } else {
        // Podría ser texto plano (sistema antiguo)
        $password_valida = ($password_actual === $password_bd);
    }
    
    if (!$password_valida) {
        go_back(['error' => 'La contraseña actual es incorrecta']);
    }
    
    // Generar hash seguro para la nueva contraseña
    $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
    
    if (!$password_hash) {
        go_back(['error' => 'Error al generar hash de contraseña']);
    }
    
    // Actualizar contraseña y último acceso en la BD
    $stmt = $conn->prepare("UPDATE $tabla_auth SET $campo_password = ?, ULTIMO_ACCESO = NOW() WHERE $campo_id = ?");
    if (!$stmt) {
        throw new Exception("Error preparando actualización: " . $conn->error);
    }
    
    $stmt->bind_param("si", $password_hash, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando actualización: " . $stmt->error);
    }
    
    $stmt->close();
    
    // Registrar cambio en log (opcional - crear tabla si no existe)
    try {
        $conn->query("CREATE TABLE IF NOT EXISTS log_cambios (
            id INT PRIMARY KEY AUTO_INCREMENT,
            usuario_id INT,
            accion VARCHAR(50),
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ip VARCHAR(45),
            FOREIGN KEY (usuario_id) REFERENCES tb_usuarios(ID_USUARIO)
        )");
        
        $log_stmt = $conn->prepare("INSERT INTO log_cambios (usuario_id, accion, fecha, ip) VALUES (?, 'cambio_password', NOW(), ?)");
        if ($log_stmt) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $log_stmt->bind_param("is", $user_id, $ip);
            $log_stmt->execute();
            $log_stmt->close();
        }
    } catch (Exception $e) {
        // Ignorar errores de log, no es crítico
    }
    
    go_back(['success' => 'Contraseña cambiada exitosamente']);
    
} catch (Exception $e) {
    error_log("Error cambio contraseña usuario $user_id: " . $e->getMessage());
    go_back(['error' => 'Error interno del servidor. Intenta nuevamente.']);
}