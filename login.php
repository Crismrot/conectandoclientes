<?php
session_start();
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    $stmt = $conn->prepare("SELECT id, clave, debe_cambiar_clave FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->bind_result($userId, $hashedPassword, $debeCambiarClave);
    $stmt->fetch();

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
        $error = "Correo o clave incorrectos.";
    }

    $stmt->close();
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
