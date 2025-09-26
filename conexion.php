<?php
$servername = "localhost";
$username   = "root";
$password   = ""; // pon tu pass si aplica
$dbname     = "programa_torre_de_control";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die("Error de conexión a BD: " . $e->getMessage());
}
?>