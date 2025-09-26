<?php
session_start();
require_once __DIR__ . '/conexion.php';

// Sanitiza inputs
$input_user = trim($_POST['usuario'] ?? '');
$input_pass = $_POST['contrasena'] ?? '';

if ($input_user === '' || $input_pass === '') {
    header("Location: login.php?error=1");
    exit();
}

// Detecta si es correo o usuario
$esCorreo = filter_var($input_user, FILTER_VALIDATE_EMAIL) !== false;

$sql = $esCorreo
    ? "SELECT u.ID_USUARIO, u.NOMBRE_USUARIO, u.CORREO, u.CONTRASENA, u.ID_ROL, r.NOMBRE AS ROL_NOMBRE
       FROM tb_usuarios u
       LEFT JOIN tb_roles r ON r.ID_ROL = u.ID_ROL
       WHERE u.CORREO = ? LIMIT 1"
    : "SELECT u.ID_USUARIO, u.NOMBRE_USUARIO, u.CORREO, u.CONTRASENA, u.ID_ROL, r.NOMBRE AS ROL_NOMBRE
       FROM tb_usuarios u
       LEFT JOIN tb_roles r ON r.ID_ROL = u.ID_ROL
       WHERE u.NOMBRE_USUARIO = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $input_user);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    header("Location: login.php?error=1");
    exit();
}

// Valida password (ahora que son contraseñas simples)
$ok = false;

// Si la contraseña en BD está hasheada
if (password_verify($input_pass, $user['CONTRASENA'])) {
    $ok = true;
} 
// Si la contraseña en BD es texto plano
else if ($user['CONTRASENA'] === $input_pass) {
    $ok = true;
}

if (!$ok) {
    header("Location: login.php?error=1");
    exit();
}

// Sesión
$_SESSION['ID_USUARIO']    = (int)$user['ID_USUARIO'];
$_SESSION['NOMBRE_USUARIO']= $user['NOMBRE_USUARIO'];
$_SESSION['CORREO']        = $user['CORREO'];
$_SESSION['ID_ROL']        = (int)$user['ID_ROL'];
$_SESSION['ROL_NOMBRE']    = $user['ROL_NOMBRE'];
$_SESSION['loggedin']      = true;

// Marca último acceso (si la columna existe)
$upd = $conn->prepare("UPDATE tb_usuarios SET ULTIMO_ACCESO = NOW() WHERE ID_USUARIO = ?");
$upd->bind_param("i", $_SESSION['ID_USUARIO']);
$upd->execute();

// Redirección por rol
switch ($_SESSION['ID_ROL']) {
    case 1: // admin
        header("Location: rol1/index.php");
        break;
    case 2: // médico
        header("Location: dashboard.php");
        break;
    case 3: // super_usuario
        header("Location: dashboard.php");
        break;
    default:
        header("Location: dashboard.php");
}
exit();
?>