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

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevaClave = $_POST['nueva_clave'];
    $nuevaClaveConfirmacion = $_POST['nueva_clave_confirmacion'];

    // Validación de la nueva clave
    if (empty($nuevaClave) || empty($nuevaClaveConfirmacion)) {
        $error = "Todos los campos son obligatorios.";
    } elseif ($nuevaClave !== $nuevaClaveConfirmacion) {
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

        // Redirigir al panel del cliente
        header("Location: panel-cliente.php?clave_cambiada=1");
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
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            max-width: 500px;
        }
        .alert-danger {
            background-color: #f2dede;
            border-color: #ebccd1;
            color: #a94442;
        }
        .btn-primary {
            background-color: #d9534f;
            border-color: #d43f3a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cambiar Clave</h2>
        <?php if ($error) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="POST" action="cambiar_clave.php">
            <div class="form-group">
                <label for="nueva_clave">Nueva Clave</label>
                <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" required>
            </div>
            <div class="form-group">
                <label for="nueva_clave_confirmacion">Confirmar Nueva Clave</label>
                <input type="password" class="form-control" id="nueva_clave_confirmacion" name="nueva_clave_confirmacion" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Cambiar Clave</button>
        </form>
    </div>
</body>
</html>
