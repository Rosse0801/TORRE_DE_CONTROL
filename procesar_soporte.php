<?php
/**
 * /procesar_soporte.php
 * Sistema de tickets de soporte técnico
 * Versión optimizada y limpia
 */

session_start();

// =========================================
// CONFIGURACIÓN Y CONSTANTES
// =========================================
define('LOG_DIR', __DIR__ . '/logs');
define('SUPPORT_EMAIL', 'escobarsuarezs148b@gmail.com');
define('SUPPORT_PHONE', '5555-1234 Ext. 4567');

// =========================================
// FUNCIONES AUXILIARES
// =========================================
function redirect_with_message($type, $message) {
    header("Location: soporte.php?$type=" . urlencode($message));
    exit();
}

function generar_numero_ticket() {
    $fecha = date('Ymd');
    $random = rand(100, 999);
    return "HGM-$fecha-$random";
}

function validar_formulario($datos) {
    $errores = [];
    
    // Validaciones básicas
    if (empty($datos['nombre'])) {
        $errores[] = 'El nombre es obligatorio';
    }
    
    if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Correo electrónico inválido';
    }
    
    if (empty($datos['area'])) {
        $errores[] = 'Área es obligatoria';
    }
    
    if (!in_array($datos['urgencia'], ['baja', 'media', 'alta', 'critica'])) {
        $errores[] = 'Nivel de urgencia inválido';
    }
    
    if (empty($datos['asunto']) || strlen($datos['asunto']) < 5) {
        $errores[] = 'Asunto debe tener al menos 5 caracteres';
    }
    
    if (empty($datos['descripcion']) || strlen($datos['descripcion']) < 20) {
        $errores[] = 'Descripción debe tener al menos 20 caracteres';
    }
    
    // Validar teléfono si se proporciona
    if (!empty($datos['telefono'])) {
        $telefono_limpio = preg_replace('/\D/', '', $datos['telefono']);
        if (strlen($telefono_limpio) < 10) {
            $errores[] = 'Teléfono debe tener al menos 10 dígitos';
        }
    }
    
    return $errores;
}

function crear_directorio_logs() {
    if (!is_dir(LOG_DIR)) {
        if (!mkdir(LOG_DIR, 0755, true)) {
            throw new Exception('No se pudo crear el directorio de logs');
        }
    }
}

function guardar_log_ticket($numero_ticket, $datos) {
    crear_directorio_logs();
    
    $log_file = LOG_DIR . '/soporte_' . date('Y-m') . '.log';
    $log_entry = sprintf(
        "%s | TICKET: %s | %s | %s (%s) | %s\n",
        date('Y-m-d H:i:s'),
        $numero_ticket,
        $datos['urgencia'],
        $datos['nombre'],
        $datos['email'],
        $datos['asunto']
    );
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    return $log_file;
}

function guardar_log_email($numero_ticket, $email_usuario, $email_soporte) {
    $log_file = LOG_DIR . '/soporte_' . date('Y-m') . '.log';
    $log_entry = sprintf(
        "%s | RESULTADO: %s | Usuario: %s | Soporte: %s\n",
        date('Y-m-d H:i:s'),
        $numero_ticket,
        $email_usuario ? 'EMAIL_OK' : 'EMAIL_ERROR',
        $email_soporte ? 'EMAIL_OK' : 'EMAIL_ERROR'
    );
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

function obtener_config_urgencia($urgencia) {
    $config = [
        'baja' => ['label' => 'Baja', 'color' => '#22c55e'],
        'media' => ['label' => 'Media', 'color' => '#f59e0b'],
        'alta' => ['label' => 'Alta', 'color' => '#f97316'],
        'critica' => ['label' => 'CRÍTICA', 'color' => '#dc2626']
    ];
    
    return $config[$urgencia] ?? $config['media'];
}

function crear_email_usuario($numero_ticket, $datos, $urgencia_config) {
    $asunto = "Ticket #$numero_ticket creado - " . $datos['asunto'];
    
    $mensaje = "
    <html>
    <head><meta charset='UTF-8'></head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(90deg, #ff69b4, #a72bd4); color: white; padding: 20px; text-align: center; border-radius: 8px;'>
                <h1>Hospital General de México</h1>
                <h2>Soporte Técnico</h2>
            </div>
            <div style='background: #f9f9f9; padding: 30px; border-radius: 8px; margin-top: 20px;'>
                <h2>Ticket Creado Exitosamente</h2>
                <p>Hola <strong>{$datos['nombre']}</strong>,</p>
                <p>Tu ticket de soporte ha sido creado con el número:</p>
                
                <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='margin-top: 0; color: #a72bd4;'>Ticket #$numero_ticket</h3>
                    <p><strong>Asunto:</strong> {$datos['asunto']}</p>
                    <p><strong>Área:</strong> {$datos['area']}</p>
                    <p><strong>Urgencia:</strong> <span style='background: {$urgencia_config['color']}; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold;'>{$urgencia_config['label']}</span></p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y H:i') . "</p>
                </div>
                
                <h3>¿Qué sigue?</h3>
                <ul>
                    <li>Nuestro equipo revisará tu solicitud</li>
                    <li>Recibirás respuesta en las próximas 2 horas</li>
                    <li>Te contactaremos por email o teléfono</li>
                    <li>Guarda este número: <strong>#$numero_ticket</strong></li>
                </ul>
                
                <p>Gracias por contactarnos.</p>
                <p><strong>Equipo de Soporte Técnico<br>Hospital General de México</strong></p>
            </div>
            <div style='text-align: center; margin-top: 30px; font-size: 12px; color: #666;'>
                <p>Para consultas: " . SUPPORT_EMAIL . " | Tel: " . SUPPORT_PHONE . "</p>
            </div>
        </div>
    </body>
    </html>";
    
    return ['asunto' => $asunto, 'mensaje' => $mensaje];
}

function crear_email_soporte($numero_ticket, $datos, $urgencia_config) {
    $asunto = "[{$urgencia_config['label']}] Nuevo Ticket #$numero_ticket - " . $datos['asunto'];
    
    $mensaje = "
    <html>
    <head><meta charset='UTF-8'></head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 700px; margin: 0 auto; padding: 20px;'>
            <div style='background: {$urgencia_config['color']}; color: white; padding: 20px; text-align: center; border-radius: 8px;'>
                <h1>🚨 NUEVO TICKET DE SOPORTE</h1>
                <h2>#$numero_ticket</h2>
            </div>
            <div style='background: #f9f9f9; padding: 30px; border-radius: 8px; margin-top: 20px;'>
                <div style='background: white; padding: 20px; border-radius: 8px;'>
                    <h3>Información del Ticket</h3>
                    <p><strong>Número:</strong> #$numero_ticket</p>
                    <p><strong>Urgencia:</strong> <span style='background: {$urgencia_config['color']}; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold;'>{$urgencia_config['label']}</span></p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
                    
                    <h3>Información del Usuario</h3>
                    <p><strong>Nombre:</strong> {$datos['nombre']}</p>
                    <p><strong>Email:</strong> {$datos['email']}</p>
                    <p><strong>Teléfono:</strong> " . ($datos['telefono'] ?: 'No proporcionado') . "</p>
                    <p><strong>Área:</strong> {$datos['area']}</p>
                    
                    <h3>Problema Reportado</h3>
                    <p><strong>Asunto:</strong> {$datos['asunto']}</p>
                    <div style='background: #e9ecef; padding: 15px; border-radius: 8px; border-left: 4px solid {$urgencia_config['color']};'>
                        <strong>Descripción:</strong><br>
                        " . nl2br(htmlspecialchars($datos['descripcion'])) . "
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>";
    
    return ['asunto' => $asunto, 'mensaje' => $mensaje];
}

function enviar_emails($numero_ticket, $datos) {
    $urgencia_config = obtener_config_urgencia($datos['urgencia']);
    
    // Preparar emails
    $email_usuario = crear_email_usuario($numero_ticket, $datos, $urgencia_config);
    $email_soporte = crear_email_soporte($numero_ticket, $datos, $urgencia_config);
    
    // Headers HTML
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: Soporte Técnico HGM <' . SUPPORT_EMAIL . '>'
    ];
    $headers_str = implode("\r\n", $headers);
    
    // Enviar emails
    $email_usuario_ok = mail($datos['email'], $email_usuario['asunto'], $email_usuario['mensaje'], $headers_str);
    $email_soporte_ok = mail(SUPPORT_EMAIL, $email_soporte['asunto'], $email_soporte['mensaje'], $headers_str);
    
    return [$email_usuario_ok, $email_soporte_ok];
}

function generar_mensaje_resultado($numero_ticket, $email_usuario_ok, $email_soporte_ok) {
    if ($email_usuario_ok && $email_soporte_ok) {
        return "✅ Ticket #$numero_ticket creado exitosamente. Confirmación enviada a tu email y notificación enviada al equipo de soporte. Recibirás respuesta en las próximas 2 horas.";
    } elseif (!$email_usuario_ok && $email_soporte_ok) {
        return "⚠️ Ticket #$numero_ticket creado correctamente y el equipo de soporte fue notificado. Sin embargo, hubo un problema enviando la confirmación a tu email. Te contactaremos directamente.";
    } elseif ($email_usuario_ok && !$email_soporte_ok) {
        return "⚠️ Ticket #$numero_ticket creado y confirmación enviada a tu email. Hubo un problema notificando al equipo automáticamente, pero tu solicitud está registrada. Si es urgente, llama al " . SUPPORT_PHONE . ".";
    } else {
        return "❌ Ticket #$numero_ticket creado y guardado correctamente, pero hubo problemas con el envío de emails. Para atención inmediata, llama al " . SUPPORT_PHONE . " y menciona tu número de ticket.";
    }
}

// =========================================
// PROCESAMIENTO PRINCIPAL
// =========================================

// Validar método de request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_message('error', 'Método no permitido');
}

// Capturar y limpiar datos del formulario
$datos = [
    'nombre' => trim($_POST['nombre'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'area' => trim($_POST['area'] ?? ''),
    'urgencia' => trim($_POST['urgencia'] ?? ''),
    'asunto' => trim($_POST['asunto'] ?? ''),
    'descripcion' => trim($_POST['descripcion'] ?? '')
];

// Validar formulario
$errores = validar_formulario($datos);
if (!empty($errores)) {
    redirect_with_message('error', 'Errores: ' . implode(', ', $errores));
}

// Procesar ticket
try {
    // Generar número de ticket
    $numero_ticket = generar_numero_ticket();
    
    // Guardar log del ticket
    guardar_log_ticket($numero_ticket, $datos);
    
    // Enviar emails
    list($email_usuario_ok, $email_soporte_ok) = enviar_emails($numero_ticket, $datos);
    
    // Guardar log del resultado de emails
    guardar_log_email($numero_ticket, $email_usuario_ok, $email_soporte_ok);
    
    // Generar mensaje de resultado
    $mensaje = generar_mensaje_resultado($numero_ticket, $email_usuario_ok, $email_soporte_ok);
    
    // Redirigir con resultado apropiado
    $tipo = ($email_usuario_ok || $email_soporte_ok) ? 'success' : 'error';
    redirect_with_message($tipo, $mensaje);
    
} catch (Exception $e) {
    error_log("Error procesando ticket de soporte: " . $e->getMessage());
    redirect_with_message('error', 'Error interno del servidor. Intenta nuevamente o contacta por teléfono.');
}
?>