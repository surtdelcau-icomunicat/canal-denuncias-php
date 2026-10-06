<?php
session_start();

// Incluir el archivo de configuración para la conexión a la base de datos
require_once __DIR__ . '/config.php';

// Verificar si la conexión fue establecida correctamente
if (!isset($conn) || $conn->connect_error) {
    die("Error: No se pudo establecer la conexión a la base de datos. " . $conn->connect_error);
}

// Verificar si se recibieron los datos del formulario de consulta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ticket_id = $_POST['ticket_id'] ?? '';

    if (!empty($ticket_id)) {
        // Preparar la consulta para obtener el ticket
        $stmt = $conn->prepare("SELECT estado FROM tickets WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $ticket_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $ticket = $result->fetch_assoc();
                $estado = htmlspecialchars($ticket['estado']);
            } else {
                $estado = "No se encontró ningún ticket con el ID proporcionado.";
            }

            $stmt->close();
        } else {
            die("Error al preparar la consulta: " . $conn->error);
        }
    } else {
        $estado = "Por favor, ingrese un ID de ticket válido.";
    }
} else {
    $estado = "";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Estado Denuncia</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .navbar {
        border-radius: 8px;
    }
</style>
</head>
<body>
    <div class="container mt-5">
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
                <a class="nav-link" href="#">Consultar Denuncia</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_dashboard.php">Administración</a>
            </li>
        </ul>
    </div>
</nav>
<br>
    <br>
    <br>
        <h3>Consultar Estado de la denuncia</h3>
        <form action="consult_ticket.php" method="post" class="mb-3">
            <div class="form-group">
                <label for="ticket_id">ID de la denuncia:</label>
                <input type="text" class="form-control" id="ticket_id" name="ticket_id" required>
            </div>
            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>

        <?php if (!empty($estado)): ?>
            <div class="alert alert-info">
                <strong>Estado del Ticket:</strong> <?php 
                if ($estado === "en_proceso") echo "En Proceso";
                else echo $estado; 
                ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>