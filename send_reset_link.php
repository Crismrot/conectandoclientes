<?php
session_start();
include 'config/database.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];

    // Verificar si el correo existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->bind_result($userId);
    $stmt->fetch();
    $stmt->close();

    if ($userId) {
        // Generar un token de restablecimiento
        $token = bin2hex(random_bytes(50));
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $token);
        $stmt->execute();
        $stmt->close();

        // Enviar el correo con el enlace de restablecimiento
        $resetLink = "http://localhost/app/reset_password.php?token=$token";
        $subject = "Restablecimiento de Contraseña";
        $messageContent = "Haga clic en el siguiente enlace para restablecer su contraseña: $resetLink";
        $headers = "From: no-reply@conectandoclientes.com";
        mail($correo, $subject, $messageContent, $headers);

        $message = "Un enlace de restablecimiento ha sido enviado a su correo electrónico.";
    } else {
        $message = "Correo no encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Enlace de Restablecimiento - Conectando Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Recuperar Contraseña</h2>
        <form method="POST" action="send_reset_link.php">
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Enlace de Restablecimiento</button>
        </form>
    </div>

    <!-- Modal de mensaje -->
    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Información</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo $message; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if ($message) { ?>
                $('#messageModal').modal('show');
            <?php } ?>
        });
    </script>
</body>
</html>
