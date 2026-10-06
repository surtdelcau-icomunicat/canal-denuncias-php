<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Incluir el archivo de configuración para la conexión a la base de datos
require_once __DIR__ . '/config.php';

// Verificar si la conexión fue establecida correctamente
if (!isset($conn) || $conn->connect_error) {
    die("Error: No se pudo establecer la conexión a la base de datos. " . $conn->connect_error);
}

// Manejar la actualización del estado del ticket
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $ticket_id = $_POST['ticket_id'] ?? '';
    $new_status = $_POST['new_status'] ?? '';

    if (!empty($ticket_id) && !empty($new_status)) {
        $stmt = $conn->prepare("UPDATE tickets SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $ticket_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Estado del ticket actualizado correctamente.";
        } else {
            $_SESSION['message'] = "No se pudo actualizar el estado del ticket.";
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "Por favor, seleccione un estado válido.";
    }

    header("Location: admin_dashboard.php");
    exit();
}

// Manejar el borrado del ticket individual
if (isset($_GET['delete_ticket_id'])) {
    $ticket_id = $_GET['delete_ticket_id'];

    $stmt = $conn->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Ticket borrado correctamente.";
    } else {
        $_SESSION['message'] = "No se pudo borrar el ticket.";
    }

    $stmt->close();

    header("Location: admin_dashboard.php");
    exit();
}

// Obtener todos los tickets
$result = $conn->query("SELECT * FROM tickets");
$tickets = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Sistema de Tickets</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
                <a class="nav-link" href="consult_ticket.php">Consultar Denuncia</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Administración</a>
            </li>
        </ul>
    </div>
</nav>
<br>
    <br>
    <br>
        <h3>Panel de Administración</h3>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <form action="admin_dashboard.php" method="post">
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Persona o Empresa Denunciada</th>
                        <th>Relación</th>
                        <th>Tipo de Denuncia</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                        <td><a href="ticket_detail.php?id=<?php echo htmlspecialchars($ticket['id']); ?>" class="btn btn-link"><?php echo htmlspecialchars($ticket['id']); ?></a></td>
                            <td><?php echo htmlspecialchars($ticket['lugar_incidente']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['relacion']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['tipo_denuncia']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['fecha_creacion']); ?></td>
                            <td>
                                <select name="new_status" class="form-control" onchange="updateStatus(<?php echo $ticket['id']; ?>, this.value)">
                                    <option value="pendiente"  <?php echo ($ticket['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="en_proceso"  <?php echo ($ticket['estado'] == 'en_proceso') ? 'selected' : ''; ?>>En Proceso</option>
                                    <option value="resuelto"  <?php echo ($ticket['estado'] == 'resuelto') ? 'selected' : ''; ?>>Resuelto</option>
                                </select>
                            </td>
                            <td>
                                <a href="admin_dashboard.php?delete_ticket_id=<?php echo $ticket['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea borrar este ticket?');">
                                    <i class="fas fa-trash-alt"></i> Borrar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </div>

    <script>
        function updateStatus(ticketId, newStatus) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin_dashboard.php';

            const ticketIdInput = document.createElement('input');
            ticketIdInput.type = 'hidden';
            ticketIdInput.name = 'ticket_id';
            ticketIdInput.value = ticketId;
            form.appendChild(ticketIdInput);

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'new_status';
            statusInput.value = newStatus;
            form.appendChild(statusInput);

            const updateStatusInput = document.createElement('input');
            updateStatusInput.type = 'hidden';
            updateStatusInput.name = 'update_status';
            updateStatusInput.value = 'true';
            form.appendChild(updateStatusInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
    



    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>