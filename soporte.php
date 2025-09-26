<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Soporte Técnico - Área de Anestesiología</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dashboard">
    <div class="container">
        <a class="navbar-brand" href="login.php">
            <i class="bi bi-activity me-2"></i>Torre de Control
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link text-white" href="login.php">
                <i class="bi bi-arrow-left me-1"></i>Volver al Login
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <strong>¡Éxito!</strong> <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Error:</strong> <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Header -->
    <section class="welcome">
        <div class="welcome-card">
            <div class="welcome-inner">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>Soporte Técnico</h1>
                        <p class="mb-0 opacity-75">
                            <i class="bi bi-headset me-1"></i>
                            Estamos aquí para ayudarte con cualquier problema técnico
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="icon-circle" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            <i class="bi bi-life-preserver"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row">
        <!-- Contacto directo -->
        <div class="col-lg-6 mb-4">
            <div class="chart-container h-100">
                <h5 class="mb-4">
                    <i class="bi bi-telephone me-2" style="color: var(--brand-fuchsia);"></i>
                    Contacto Directo
                </h5>
                
                <div class="mb-4">
                    <h6>Soporte 24/7</h6>
                    <p class="mb-2">
                        <i class="bi bi-telephone-fill me-2" style="color: var(--brand-fuchsia);"></i>
                        <strong>Tel: 5555-1234 Ext. 4567</strong>
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-envelope-fill me-2" style="color: var(--brand-fuchsia);"></i>
                        <strong>soporte.anestesia@hospital.gob.mx</strong>
                    </p>
                    <p class="text-muted">
                        Disponible las 24 horas para emergencias del sistema
                    </p>
                </div>

                <div class="mb-4">
                    <h6>Horarios de Atención</h6>
                    <div class="bg-light p-3 rounded">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Lunes a Viernes:</span>
                            <strong>7:00 AM - 8:00 PM</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Sábados:</span>
                            <strong>8:00 AM - 2:00 PM</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Emergencias:</span>
                            <strong>24/7</strong>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6>Ubicación</h6>
                    <p class="mb-1">
                        <i class="bi bi-geo-alt-fill me-2" style="color: var(--brand-fuchsia);"></i>
                        <strong>Sistemas Hospitalarios</strong>
                    </p>
                    <p class="text-muted">
                        Planta Baja, Ala Norte<br>
                        Oficina de Informática Médica
                    </p>
                </div>
            </div>
        </div>

        <!-- Problemas comunes -->
        <div class="col-lg-6 mb-4">
            <div class="chart-container h-100">
                <h5 class="mb-4">
                    <i class="bi bi-question-circle me-2" style="color: var(--brand-fuchsia);"></i>
                    Problemas Comunes
                </h5>

                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                No puedo iniciar sesión
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <strong>Pasos a seguir:</strong>
                                <ol>
                                    <li>Verifica que tu usuario y contraseña sean correctos</li>
                                    <li>Usa la opción "¿Olvidó su contraseña?" si es necesario</li>
                                    <li>Limpia las cookies de tu navegador</li>
                                    <li>Si persiste el problema, contacta soporte</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                No recibo el email de recuperación
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <strong>Revisa lo siguiente:</strong>
                                <ul>
                                    <li>Carpeta de spam o correo no deseado</li>
                                    <li>Que el email esté escrito correctamente</li>
                                    <li>Espera hasta 10 minutos para recibir el correo</li>
                                    <li>Contacta soporte si no llega después de 10 minutos</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                El sistema está muy lento
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <strong>Soluciones rápidas:</strong>
                                <ul>
                                    <li>Actualiza tu navegador web</li>
                                    <li>Cierra otras pestañas no necesarias</li>
                                    <li>Verifica tu conexión a internet</li>
                                    <li>Reinicia tu navegador</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                Error al guardar datos
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <strong>Acciones recomendadas:</strong>
                                <ol>
                                    <li>Verifica que todos los campos obligatorios estén llenos</li>
                                    <li>Intenta nuevamente en unos segundos</li>
                                    <li>Si el problema persiste, toma captura de pantalla</li>
                                    <li>Reporta el error a soporte técnico</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de contacto -->
    <div class="row">
        <div class="col-12">
            <div class="card-outer">
                <div class="card-inner">
                    <div class="card-header bg-primary">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-dots me-2"></i>
                            Reportar Problema
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <form action="procesar_soporte.php" method="post" novalidate>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nombre" class="form-label">Nombre Completo</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Correo Electrónico</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="telefono" class="form-label">Teléfono (opcional)</label>
                                            <input type="tel" class="form-control" id="telefono" name="telefono">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="area" class="form-label">Área/Departamento</label>
                                            <select class="form-select" id="area" name="area" required>
                                                <option value="">Selecciona tu área</option>
                                                <option value="Anestesiología">Anestesiología</option>
                                                <option value="Quirófanos">Quirófanos</option>
                                                <option value="Administrativo">Administrativo</option>
                                                <option value="Sistemas">Sistemas</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="urgencia" class="form-label">Nivel de Urgencia</label>
                                        <select class="form-select" id="urgencia" name="urgencia" required>
                                            <option value="">Selecciona el nivel</option>
                                            <option value="baja">Baja - Puede esperar</option>
                                            <option value="media">Media - Importante pero no crítico</option>
                                            <option value="alta">Alta - Necesito ayuda pronto</option>
                                            <option value="critica">Crítica - Emergencia del sistema</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="asunto" class="form-label">Asunto del Problema</label>
                                        <input type="text" class="form-control" id="asunto" name="asunto" 
                                               placeholder="Ej: Error al registrar médico" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción Detallada</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                                  rows="5" required 
                                                  placeholder="Describe el problema con el mayor detalle posible. Incluye qué estabas haciendo cuando ocurrió el error, mensajes que aparecieron, etc."></textarea>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-send me-2"></i>
                                            Enviar Reporte
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-4">
                                <div class="bg-light p-3 rounded">
                                    <h6>Información Importante</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="bi bi-info-circle me-2" style="color: var(--brand-fuchsia);"></i>
                                            Respuesta en menos de 2 horas
                                        </li>
                                        <li class="mb-2">
                                            <i class="bi bi-shield-check me-2" style="color: var(--brand-fuchsia);"></i>
                                            Tus datos están protegidos
                                        </li>
                                        <li class="mb-2">
                                            <i class="bi bi-clock me-2" style="color: var(--brand-fuchsia);"></i>
                                            Seguimiento del ticket por email
                                        </li>
                                        <li>
                                            <i class="bi bi-people me-2" style="color: var(--brand-fuchsia);"></i>
                                            Equipo especializado en sistemas hospitalarios
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado del sistema -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="chart-container">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="icon-circle me-2" style="background: #22c55e;">
                                <i class="bi bi-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Sistema Principal</div>
                                <small class="text-success">Operativo</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="icon-circle me-2" style="background: #22c55e;">
                                <i class="bi bi-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Base de Datos</div>
                                <small class="text-success">Operativo</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="icon-circle me-2" style="background: #22c55e;">
                                <i class="bi bi-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Email System</div>
                                <small class="text-success">Operativo</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="icon-circle me-2" style="background: #22c55e;">
                                <i class="bi bi-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Respaldos</div>
                                <small class="text-success">Operativo</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5 py-4 footer-dashboard text-center">
    <div class="container">
        <p class="mb-0">&copy; 2025 Hospital General de México - Soporte Técnico</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Validación del formulario
document.querySelector('form').addEventListener('submit', function(e) {
    const campos = ['nombre', 'email', 'area', 'urgencia', 'asunto', 'descripcion'];
    let valido = true;
    
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (!elemento.value.trim()) {
            elemento.classList.add('is-invalid');
            valido = false;
        } else {
            elemento.classList.remove('is-invalid');
            elemento.classList.add('is-valid');
        }
    });
    
    if (!valido) {
        e.preventDefault();
        alert('Por favor, completa todos los campos obligatorios.');
    }
});

// Actualizar el nivel de urgencia con colores
document.getElementById('urgencia').addEventListener('change', function() {
    const nivel = this.value;
    const colors = {
        'baja': '#22c55e',
        'media': '#f59e0b', 
        'alta': '#f97316',
        'critica': '#dc2626'
    };
    
    if (colors[nivel]) {
        this.style.borderColor = colors[nivel];
    }
});
</script>

</body>
</html>