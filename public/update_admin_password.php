<?php
// update_admin_password.php

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once __DIR__ . '/config.php';

$update_error = '';
$update_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar datos
    if (!empty($_POST['usuario']) && !empty($_POST['contraseña']) && !empty($_POST['confirmar_contraseña'])) {
        if ($_POST['contraseña'] === $_POST['confirmar_contraseña']) {
            $usuario = $_POST['usuario'];
            $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);

            $sql = "UPDATE admins SET contraseña = ? WHERE usuario = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('ss', $contraseña, $usuario);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $update_success = "Contraseña actualizada correctamente.";
                    } else {
                        $update_error = "No se encontró el usuario.";
                    }
                } else {
                    $update_error = "Error al ejecutar la consulta.";
                }
                $stmt->close();
            } else {
                $update_error = "Error al preparar la consulta.";
            }

            $conn->close();
        } else {
            $update_error = "Las contraseñas no coinciden.";
        }
    } else {
        $update_error = "Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Contraseña de Administrador</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Actualizar Contraseña</h1>
        <?php if (!empty($update_error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($update_error); ?></div>
        <?php endif; ?>
        <?php if (!empty($update_success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($update_success); ?></div>
        <?php endif; ?>
        <form action="update_admin_password.php" method="post">
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="contraseña">Nueva Contraseña</label>
                <input type="password" class="form-control" id="contraseña" name="contraseña" required>
            </div>
            <div class="form-group">
                <label for="confirmar_contraseña">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirmar_contraseña" name="confirmar_contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
        </form>
    </div>
</body>
</html>
