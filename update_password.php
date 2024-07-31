<?php
session_start();
include 'config/database.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $nuevaClave = $_POST['nueva_clave'];
    $confirmarClave = $_POST['confirmar_clave'];

    if ($nuevaClave !== $confirmarClave) {
        $message = "Las contraseñas no coinciden.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->bind_result($userId);
        $stmt->fetch();
        $stmt->close();

        if (!$userId) {
            $message = "Token inválido o expirado.";
        } else {
            $hashedPassword = password_hash($nuevaClave, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE usuarios SET clave = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();
            $stmt->close();

            // Eliminar el token de restablecimiento
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->close();

            $message = "Contraseña actualizada exitosamente. Puede iniciar sesión con su nueva contraseña.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Conectando Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Restablecer Contraseña</h2>
        <form method="POST" action="update_password.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="form-group">
                <label for="nueva_clave">Nueva Contraseña</label>
                <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" required>
            </div>
            <div class="form-group">
                <label for="confirmar_clave">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
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
                    <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
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
