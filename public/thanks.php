<?php
// Archivo: thanks.php

// Conexión a la base de datos
require __DIR__ . '/config.php';

// Comprobar si se ha proporcionado un ID en la URL
if (isset($_GET['id'])) {
    $ticket_id = htmlspecialchars($_GET['id']);

    // Obtener los detalles del ticket desde la base de datos
    $stmt = $conn->prepare("SELECT id FROM tickets WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($ticket_id);
            $stmt->fetch();
        } else {
            $ticket_id = "No disponible";
        }
        $stmt->close();
    } else {
        $ticket_id = "No disponible";
    }
} else {
    $ticket_id = "No disponible";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gracias por su Denuncia</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>
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
                    <a class="nav-link" href="admin_dashboard.php">Administración</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <p class="text-justify">
            Su comunicación ha sido <strong>recibida correctamente</strong> y <strong>registrada en el Sistema Interno de Información</strong>, con el <strong>código de referencia [<span style="color: red;"><?php echo $ticket_id; ?></span>]</strong>.
        </p>
        <p class="text-justify">
            Será gestionada de forma <strong>confidencial</strong>, <strong>imparcial</strong> y <strong>segura</strong> conforme a la <strong>Ley 2/2023</strong>, garantizando en todo momento la protección de su identidad y el respeto a los <strong>derechos de las personas afectadas</strong>.
        </p>
        <p class="text-justify">
            Conserve este código para el <strong>seguimiento de la comunicación</strong> a través de la plataforma.
        </p>
        <div class="text-center mt-4">
            <a href="index.html" class="btn btn-primary">Volver a la página principal</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
