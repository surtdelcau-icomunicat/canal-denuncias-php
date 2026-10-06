<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Incluir el archivo de configuración para la conexión a la base de datos
require_once __DIR__ . '/config.php';

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$ticket_id = $_GET['id'];

// Obtener los detalles del ticket
$stmt = $conn->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

// Si no existe el ticket, redirigir al dashboard
if (!$ticket) {
    header("Location: admin_dashboard.php");
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Denuncia - Sistema de Tickets</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        .navbar {
            border-radius: 8px;
        }
        .detail-card {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .back-button {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Sistema de Denuncias</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">Registrar Denuncia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="consult_ticket.php">Consultar Denuncia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Administración</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="back-button">
            <a href="admin_dashboard.php" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>

        <h3>Detalle de la Denuncia #<?php echo htmlspecialchars($ticket['id']); ?></h3>
        
        <div class="row">
    <div class="col-md-12">
        <div class="detail-card">
            <!-- Información básica -->
            <h4 class="mb-4">Información Básica</h4>
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><span class="detail-label">ID:</span> <?php echo htmlspecialchars($ticket['id']); ?></p>
                    <?php if (!empty($ticket['nombre'])): ?>
                        <p><span class="detail-label">Nombre:</span> <?php echo htmlspecialchars($ticket['nombre']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['apellidos'])): ?>
                        <p><span class="detail-label">Apellidos:</span> <?php echo htmlspecialchars($ticket['apellidos']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['email'])): ?>
                        <p><span class="detail-label">Email:</span> <?php echo htmlspecialchars($ticket['email']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['telefono'])): ?>
                        <p><span class="detail-label">Teléfono:</span> <?php echo htmlspecialchars($ticket['telefono']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <p><span class="detail-label">Estado:</span> 
                        <span class="badge badge-<?php 
                            echo $ticket['estado'] == 'pendiente' ? 'warning' : 
                                ($ticket['estado'] == 'en_proceso' ? 'info' : 'success'); 
                        ?>">
                            <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $ticket['estado']))); ?>
                        </span>
                    </p>
                    <p><span class="detail-label">Fecha de Creación:</span> <?php echo htmlspecialchars($ticket['fecha_creacion']); ?></p>
                    <?php if (!empty($ticket['tipo_denuncia'])): ?>
                        <p><span class="detail-label">Tipo de Denuncia:</span> <?php echo htmlspecialchars($ticket['tipo_denuncia']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['relacion'])): ?>
                        <p><span class="detail-label">Relación:</span> <?php echo htmlspecialchars($ticket['relacion']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información laboral -->
            <?php if (!empty($ticket['puesto_trabajo']) || !empty($ticket['centro_trabajo'])): ?>
            <h4 class="mb-4">Información Laboral</h4>
            <div class="row mb-4">
                <div class="col-md-12">
                    <?php if (!empty($ticket['puesto_trabajo'])): ?>
                        <p><span class="detail-label">Puesto de Trabajo:</span> <?php echo htmlspecialchars($ticket['puesto_trabajo']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['centro_trabajo'])): ?>
                        <p><span class="detail-label">Centro de Trabajo:</span> <?php echo htmlspecialchars($ticket['centro_trabajo']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Información del incidente -->
            <h4 class="mb-4">Detalles del Incidente</h4>
            <div class="row mb-4">
                <div class="col-md-12">
                    <?php if (!empty($ticket['lugar_incidente'])): ?>
                        <p><span class="detail-label">Lugar del Incidente:</span> <?php echo htmlspecialchars($ticket['lugar_incidente']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['direccion'])): ?>
                        <p><span class="detail-label">Dirección:</span> <?php echo htmlspecialchars($ticket['direccion']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['localidad'])): ?>
                        <p><span class="detail-label">Localidad:</span> <?php echo htmlspecialchars($ticket['localidad']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['provincia'])): ?>
                        <p><span class="detail-label">Provincia:</span> <?php echo htmlspecialchars($ticket['provincia']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['cp'])): ?>
                        <p><span class="detail-label">Código Postal:</span> <?php echo htmlspecialchars($ticket['cp']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información del delito -->
            <h4 class="mb-4">Detalles de la Denuncia</h4>
            <div class="row mb-4">
                <div class="col-md-12">
                    <?php if (!empty($ticket['tipo_delito'])): ?>
                        <p><span class="detail-label">Tipo de Delito:</span> <?php echo htmlspecialchars($ticket['tipo_delito']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['otros_delito'])): ?>
                        <p><span class="detail-label">Otros Delitos:</span> <?php echo htmlspecialchars($ticket['otros_delito']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['fecha_hechos'])): ?>
                        <p><span class="detail-label">Fecha de los Hechos:</span> <?php echo htmlspecialchars($ticket['fecha_hechos']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['descripcion'])): ?>
                        <p><span class="detail-label">Descripción:</span></p>
                        <div class="border p-3 bg-light mb-3"><?php echo nl2br(htmlspecialchars($ticket['descripcion'])); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($ticket['afectados'])): ?>
                        <p><span class="detail-label">Personas Afectadas:</span></p>
                        <div class="border p-3 bg-light mb-3"><?php echo nl2br(htmlspecialchars($ticket['afectados'])); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información adicional -->
            <?php if (!empty($ticket['identidad_conocida']) || !empty($ticket['nombre_cargo'])): ?>
            <h4 class="mb-4">Información Adicional</h4>
            <div class="row mb-4">
                <div class="col-md-12">
                    <?php if (!empty($ticket['identidad_conocida'])): ?>
                        <p><span class="detail-label">Identidad Conocida:</span> <?php echo htmlspecialchars($ticket['identidad_conocida']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['nombre_cargo'])): ?>
                        <p><span class="detail-label">Nombre/Cargo:</span> <?php echo htmlspecialchars($ticket['nombre_cargo']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Documentación -->
            <?php if (!empty($ticket['documento_acreditativo']) || !empty($ticket['descripcion_documento'])): ?>
            <h4 class="mb-4">Documentación</h4>
            <div class="row">
                <div class="col-md-12">
                    <?php if (!empty($ticket['documento_acreditativo'])): ?>
                        <p><span class="detail-label">Documento Acreditativo:</span> <?php echo htmlspecialchars($ticket['documento_acreditativo']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($ticket['descripcion_documento'])): ?>
                        <p><span class="detail-label">Descripción del Documento:</span></p>
                        <div class="border p-3 bg-light"><?php echo nl2br(htmlspecialchars($ticket['descripcion_documento'])); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>