<?php
session_start();
include 'config/database.php';

define('MAX_INTENTOS_FALLIDOS', 3);
define('TIEMPO_BLOQUEO_MINUTOS', 15);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    $stmt = $conn->prepare("SELECT id, clave, intentos_fallidos, tiempo_bloqueo, debe_cambiar_clave, nombre FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->bind_result($userId, $hashedPassword, $intentosFallidos, $tiempoBloqueo, $debeCambiarClave, $nombre);
    $stmt->fetch();
    $stmt->close();

    // Verificar si la cuenta está bloqueada
    if ($tiempoBloqueo && strtotime($tiempoBloqueo) > time()) {
        $error = "Tu cuenta está bloqueada. Intenta nuevamente más tarde.";
    } else {
        if ($hashedPassword && password_verify($clave, $hashedPassword)) {
            session_regenerate_id();
            $_SESSION['user_id'] = $userId;

            // Restablecer el contador de intentos fallidos y el tiempo de bloqueo
            $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = 0, tiempo_bloqueo = NULL WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            if ($debeCambiarClave == 1) {
                header("Location: cambiar_clave.php");
                exit();
            } else {
                header("Location: panel-cliente.php");
                exit();
            }
        } else {
            // Incrementar el contador de intentos fallidos
            $intentosFallidos++;
            if ($intentosFallidos >= MAX_INTENTOS_FALLIDOS) {
                // Bloquear la cuenta estableciendo el tiempo de bloqueo
                $tiempoBloqueo = date('Y-m-d H:i:s', strtotime("+".TIEMPO_BLOQUEO_MINUTOS." minutes"));
                $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = ?, tiempo_bloqueo = ? WHERE id = ?");
                $stmt->bind_param("isi", $intentosFallidos, $tiempoBloqueo, $userId);
                $stmt->execute();
                $stmt->close();

                // Enviar correos de notificación
                $subjectUsuario = "Cuenta Bloqueada - Conectando Clientes";
                $messageUsuario = "Hola $nombre,\n\nTu cuenta ha sido bloqueada debido a múltiples intentos fallidos de inicio de sesión. Podrás intentar nuevamente en " . TIEMPO_BLOQUEO_MINUTOS . " minutos.\n\nSaludos,\nEl equipo de Conectando Clientes";
                $headers = "From: soporte@conectandoclientes.com";

                mail($correo, $subjectUsuario, $messageUsuario, $headers);

                $adminEmail = "admin@conectandoclientes.com";
                $subjectAdmin = "Cuenta Bloqueada: $nombre";
                $messageAdmin = "La cuenta del usuario $nombre (Correo: $correo) ha sido bloqueada debido a múltiples intentos fallidos de inicio de sesión.";
                mail($adminEmail, $subjectAdmin, $messageAdmin, $headers);

                $error = "Tu cuenta ha sido bloqueada debido a múltiples intentos fallidos. Intenta nuevamente en " . TIEMPO_BLOQUEO_MINUTOS . " minutos.";
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = ? WHERE id = ?");
                $stmt->bind_param("ii", $intentosFallidos, $userId);
                $stmt->execute();
                $stmt->close();

                $error = "Correo o clave incorrectos. Intento $intentosFallidos de " . MAX_INTENTOS_FALLIDOS . ".";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Conectando Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="clave">Clave</label>
                <input type="password" class="form-control" id="clave" name="clave" required>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
