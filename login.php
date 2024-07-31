<?php
session_start();
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    // Verificar el estado de bloqueo de la cuenta
    $stmt = $conn->prepare("SELECT id, clave, debe_cambiar_clave, intentos_fallidos, tiempo_bloqueo FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->bind_result($userId, $hashedPassword, $debeCambiarClave, $intentosFallidos, $tiempoBloqueo);
    $stmt->fetch();
    $stmt->close();

    if ($tiempoBloqueo && strtotime($tiempoBloqueo) > time()) {
        $error = "Tu cuenta está bloqueada temporalmente. Inténtalo de nuevo más tarde.";
    } else {
        // Restablecer intentos fallidos si el tiempo de bloqueo ha pasado
        if ($tiempoBloqueo && strtotime($tiempoBloqueo) <= time()) {
            $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = 0, tiempo_bloqueo = NULL WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();
            $intentosFallidos = 0;
        }

        if ($hashedPassword && password_verify($clave, $hashedPassword)) {
            session_regenerate_id();
            $_SESSION['user_id'] = $userId;

            if ($debeCambiarClave == 1) {
                header("Location: cambiar_clave.php");
                exit();
            } else {
                header("Location: panel-cliente.php");
                exit();
            }
        } else {
            $intentosFallidos++;
            if ($intentosFallidos >= 3) {
                $tiempoBloqueo = date("Y-m-d H:i:s", strtotime("+15 minutes"));
                $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = ?, tiempo_bloqueo = ? WHERE correo = ?");
                $stmt->bind_param("iss", $intentosFallidos, $tiempoBloqueo, $correo);
                $stmt->execute();
                $stmt->close();
                $error = "Tu cuenta ha sido bloqueada temporalmente por múltiples intentos fallidos. Inténtalo de nuevo en 15 minutos.";
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = ? WHERE correo = ?");
                $stmt->bind_param("is", $intentosFallidos, $correo);
                $stmt->execute();
                $stmt->close();
                $error = "Correo o clave incorrectos. Intento fallido $intentosFallidos de 3.";
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
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 400px;
            margin-top: 50px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #333;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            padding: 10px 20px;
            border-bottom: none;
        }
        .btn-primary {
            background-color: #d9534f;
            border: none;
        }
        .btn-primary:hover {
            background-color: #c9302c;
        }
        .alert-danger {
            background-color: #f2dede;
            border-color: #ebccd1;
            color: #a94442;
        }
        a {
            color: #d9534f;
        }
        a:hover {
            color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Iniciar Sesión
            </div>
            <div class="card-body">
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
                    <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="forgot_password.php">¿Olvidaste tu clave?</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
