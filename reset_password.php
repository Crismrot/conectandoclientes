<?php
session_start();
include 'config/database.php';

$token = $_GET['token'];
$stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

if (!$userId) {
    echo "Token inválido o expirado.";
    exit();
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
</body>
</html>
