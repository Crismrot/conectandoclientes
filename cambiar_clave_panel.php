<?php
session_start();
include 'config/database.php'; // Incluir la configuración de la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $claveActual = $_POST['clave_actual'];
    $nuevaClave = $_POST['nueva_clave'];
    $nuevaClaveConfirmacion = $_POST['nueva_clave_confirmacion'];

    // Obtener la clave actual del usuario desde la base de datos
    $stmt = $conn->prepare("SELECT clave FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($claveHashActual);
    $stmt->fetch();
    $stmt->close();

    // Verificar la clave actual
    if (!password_verify($claveActual, $claveHashActual)) {
        $error = "La clave actual es incorrecta.";
    } elseif ($nuevaClave !== $nuevaClaveConfirmacion) {
        $error = "Las claves nuevas no coinciden.";
    } elseif (strlen($nuevaClave) < 8) {
        $error = "La nueva clave debe tener al menos 8 caracteres.";
    } else {
        // Encriptar la nueva clave
        $claveHash = password_hash($nuevaClave, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE usuarios SET clave = ? WHERE id = ?");
        $stmt->bind_param("si", $claveHash, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();

        // Obtener detalles del usuario para el correo
        $stmt = $conn->prepare("SELECT correo, nombre FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($correoUsuario, $nombreUsuario);
        $stmt->fetch();
        $stmt->close();

        // Enviar correo al usuario
        $subjectUsuario = "Cambio de clave - Conectando Clientes";
        $messageUsuario = "Hola $nombreUsuario,\n\nTu clave ha sido cambiada exitosamente. Si no solicitaste este cambio, por favor contacta con soporte inmediatamente.\n\nSaludos,\nEl equipo de Conectando Clientes";
        $headers = "From: soporte@conectandoclientes.com";

        mail($correoUsuario, $subjectUsuario, $messageUsuario, $headers);

        // Enviar correo al administrador
        $adminEmail = "registro@conectandoclientes.com";
        $subjectAdmin = "Cambio de clave del usuario $nombreUsuario";
        $messageAdmin = "El usuario $nombreUsuario (Correo: $correoUsuario) ha cambiado su clave.";
        mail($adminEmail, $subjectAdmin, $messageAdmin, $headers);

        // Marcar como éxito para mostrar el modal
        $success = true;
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
        <form method="POST" action="cambiar_clave_panel.php">
            <div class="form-group">
                <label for="clave_actual">Clave Actual</label>
                <input type="password" class="form-control" id="clave_actual" name="clave_actual" required>
            </div>
            <div class="form-group">
                <label for="nueva_clave">Nueva Clave</label>
                <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" required>
            </div>
            <div class="form-group">
                <label for="nueva_clave_confirmacion">Confirmar Nueva Clave</label>
                <input type="password" class="form-control" id="nueva_clave_confirmacion" name="nueva_clave_confirmacion" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Cambiar Clave</button>
            <button type="button" class="btn btn-secondary btn-block" onclick="window.location.href='panel-cliente.php'">Cancelar</button>
        </form>
    </div>

    <!-- Modal de éxito -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Cambio de Clave Exitoso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tu clave ha sido cambiada exitosamente. Serás redirigido al panel de cliente.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="redirectToPanel()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if ($success) { ?>
                $('#successModal').modal('show');
            <?php } ?>
        });

        function redirectToPanel() {
            window.location.href = 'panel-cliente.php';
        }
    </script>
</body>
</html>
