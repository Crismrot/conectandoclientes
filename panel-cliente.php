<?php
session_start();
include 'config/database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener los detalles del usuario
$stmt = $conn->prepare("SELECT nombre, correo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($nombre, $correo);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Conectando Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .panel-heading {
            margin-bottom: 20px;
        }
        .panel-heading h2 {
            margin: 0;
        }
        .option-card {
            transition: all 0.3s ease;
        }
        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .card-body p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="panel-heading text-center">
            <h2>Bienvenido, <?php echo htmlspecialchars($nombre); ?></h2>
            <p><?php echo htmlspecialchars($correo); ?></p>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card option-card">
                    <div class="card-body text-center">
                        <h4 class="card-title">Editar Perfil</h4>
                        <p class="card-text">Actualiza tu información personal y preferencias.</p>
                        <a href="editar_perfil.php" class="btn btn-primary">Editar Perfil</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card option-card">
                    <div class="card-body text-center">
                        <h4 class="card-title">Cambiar Contraseña</h4>
                        <p class="card-text">Cambia tu contraseña para mejorar la seguridad.</p>
                        <a href="cambiar_clave.php" class="btn btn-warning">Cambiar Contraseña</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card option-card">
                    <div class="card-body text-center">
                        <h4 class="card-title">Cerrar Sesión</h4>
                        <p class="card-text">Finaliza tu sesión de manera segura.</p>
                        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
