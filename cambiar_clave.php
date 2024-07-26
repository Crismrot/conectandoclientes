<?php
session_start();
include 'config/database.php'; // Incluir la configuración de la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si el usuario necesita cambiar su clave
$stmt = $conn->prepare("SELECT debe_cambiar_clave, correo, nombre FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($debeCambiarClave, $correoUsuario, $nombreUsuario);
$stmt->fetch();
$stmt->close();

if ($debeCambiarClave == 0) {
    header("Location: panel-cliente.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevaClave = $_POST['nueva_clave'];
    $nuevaClaveConfirmacion = $_POST['nueva_clave_confirmacion'];

    if ($nuevaClave !== $nuevaClaveConfirmacion) {
        $error = "Las claves no coinciden.";
    } elseif (strlen($nuevaClave) < 8) {
        $error = "La clave debe tener al menos 8 caracteres.";
    } else {
        // Encriptar la nueva clave
        $claveHash = password_hash($nuevaClave, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE usuarios SET clave = ?, debe_cambiar_clave = 0 WHERE id = ?");
        $stmt->bind_param("si", $claveHash, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();

        // Enviar correo al usuario
        $subjectUsuario = "Cambio de clave - Conectando Clientes";
        $messageUsuario = "Hola $nombreUsuario,\n\nTu clave ha sido cambiada exitosamente. Si no solicitaste este cambio, por favor contacta con soporte inmediatamente.\n\nSaludos,\nEl equipo de Conectando Clientes";
        $headers = "From: soporte@conectandoclientes.com";

        mail($correoUsuario, $subjectUsuario, $messageUsuario, $headers);

        // Enviar correo al administrador
        $adminEmail = "registro@conectandoclientes.com";
        $subjectAdmin = "Cambio de clave del usuario $nombreUsuario";
        $messageAdmin = "El usuario $nombreUsuario (Correo: $correoUsuario) ha cambiado su clave por primera vez.";
        mail($adminEmail, $subjectAdmin, $messageAdmin, $headers);

        header("Location: panel-cliente.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Clave - Conectando Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Cambiar Clave</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="POST" action="cambiar_clave.php">
            <div class="form-group">
                <label for="nueva_clave">Nueva Clave</label>
                <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" required>
            </div>
            <div class="form-group">
                <label for="nueva_clave_confirmacion">Confirmar Nueva Clave</label>
                <input type="password" class="form-control" id="nueva_clave_confirmacion" name="nueva_clave_confirmacion" required>
            </div>
            <button type="submit" class="btn btn-primary">Cambiar Clave</button>
        </form>
    </div>
</body>
</html>
