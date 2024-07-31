<?php
include 'config/database.php';

$urlPerfil = $_GET['url'];
$stmt = $conn->prepare("SELECT nombre, apellido, correo, telefono, profesion, empresa, direccion, whatsapp, facebook, tiktok, instagram, youtube, linkedin, twitter, telegram, pagina_web, foto_perfil, logo FROM usuarios WHERE url_perfil = ?");
$stmt->bind_param("s", $urlPerfil);
$stmt->execute();
$stmt->bind_result($nombre, $apellido, $correo, $telefono, $profesion, $empresa, $direccion, $whatsapp, $facebook, $tiktok, $instagram, $youtube, $linkedin, $twitter, $telegram, $pagina_web, $foto_perfil, $logo);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($nombre) . " " . htmlspecialchars($apellido); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .profile-container {
            text-align: center;
            padding: 20px;
        }
        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .profile-info {
            margin-top: 10px;
        }
        .contact-button {
            margin: 10px 0;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            color: white;
        }
        .contact-button.whatsapp { background-color: #25d366; }
        .contact-button.telegram { background-color: #0088cc; }
        .contact-button.email { background-color: #FF0000; }
        .contact-button.phone { background-color: #000000; }
    </style>
</head>
<body>
    <div class="profile-container">
        <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de perfil" class="profile-photo">
        <h2><?php echo htmlspecialchars($nombre) . " " . htmlspecialchars($apellido); ?></h2>
        <p><?php echo htmlspecialchars($profesion); ?></p>
        <p><?php echo htmlspecialchars($empresa); ?></p>
        <p><?php echo htmlspecialchars($direccion); ?></p>
        <p><i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($telefono); ?></p>
        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($correo); ?></p>
        <div class="profile-info">
            <p><?php echo htmlspecialchars($linkedin); ?></p>
        </div>
        <div>
            <a href="https://wa.me/<?php echo htmlspecialchars($whatsapp); ?>" class="contact-button whatsapp">Escríbeme por WhatsApp</a>
            <a href="https://t.me/<?php echo htmlspecialchars($telegram); ?>" class="contact-button telegram">Escríbeme por Telegram</a>
            <a href="mailto:<?php echo htmlspecialchars($correo); ?>" class="contact-button email">Escríbeme por Email</a>
            <a href="tel:<?php echo htmlspecialchars($telefono); ?>" class="contact-button phone">Llamar por Teléfono</a>
        </div>
    </div>
</body>
</html>
