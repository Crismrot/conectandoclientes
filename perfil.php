<?php
include 'config/database.php';

$usuario = htmlspecialchars($_GET['usuario'] ?? '', ENT_QUOTES, 'UTF-8');

// Buscar el usuario en la base de datos usando la URL de perfil
$stmt = $conn->prepare("SELECT nombre, apellido, profesion, empresa, direccion, telefono, correo, whatsapp, facebook, tiktok, instagram, youtube, linkedin, twitter, telegram, pagina_web, foto_perfil, logo FROM usuarios WHERE url_perfil = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($nombre, $apellido, $profesion, $empresa, $direccion, $telefono, $correo, $whatsapp, $facebook, $tiktok, $instagram, $youtube, $linkedin, $twitter, $telegram, $pagina_web, $foto_perfil, $logo);
    $stmt->fetch();
} else {
    echo "Perfil no encontrado.";
    exit();
}

// Asegurar que las rutas de las imágenes sean correctas
$foto_perfil = !empty($foto_perfil) ? '../assets/uploads/' . basename($foto_perfil) : null;
$logo = !empty($logo) ? '../assets/uploads/' . basename($logo) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($nombre); ?> - Conectando Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-header {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .profile-header img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .profile-header h1 {
            font-size: 1.5em;
            font-weight: bold;
        }
        .profile-header .profession {
            font-size: 1.2em;
        }
        .profile-header .company {
            font-weight: bold;
            margin-top: 0px;
        }
        .profile-details {
            margin-top: 20px;
        }
        .profile-details .row {
            margin-bottom: 10px;
        }
        .icon-buttons a {
            width: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 5px;
            border: 1px solid #ccc;
            color: #333;
        }
        .icon-buttons a i {
            font-size: 1.5em;
        }
        .icon-buttons a.map-icon {
            background-color: #007bff;
            color: white;
        }
        .icon-buttons a.web-icon {
            background-color: #28a745;
            color: white;
        }
        .icon-buttons a.call-icon {
            background-color: #17a2b8;
            color: white;
        }
        .social-buttons .btn {
            margin-bottom: 20px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 25px;
        }
        .vcard-button, .email-button {
            margin-bottom: 10px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 25px;
        }
        .social-buttons .btn i, .vcard-button i, .email-button i {
            margin-right: auto;
            margin-left: 10px;
        }
        .social-buttons .btn span, .vcard-button span, .email-button span {
            flex-grow: 1;
            text-align: center;
        }
        .contact-info i {
            margin-right: 10px;
        }
        .logo img {
            max-width: 200px;
            max-height: 200px;
        }
        .whatsapp-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #25d366;
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-decoration: none;
            z-index: 1000;
        }
        .whatsapp-button i {
            font-size: 2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header text-center">
            <?php if ($foto_perfil): ?>
                <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de perfil" class="img-thumbnail">
            <?php endif; ?>
            <h1><?php echo htmlspecialchars("$nombre $apellido"); ?></h1>
            <p class="profession"><?php echo htmlspecialchars($profesion); ?></p>
            <?php if (!empty($empresa)) { ?>
                <p class="company"><?php echo htmlspecialchars($empresa); ?></p>
            <?php } ?>
        </div>
        <div class="profile-details text-center">
            <div class="icon-buttons mb-4">
                <?php if (!empty($direccion)) { ?>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($direccion); ?>" target="_blank" class="map-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </a>
                <?php } ?>
                <?php if (!empty($pagina_web)) { ?>
                    <a href="<?php echo htmlspecialchars($pagina_web); ?>" target="_blank" class="web-icon">
                        <i class="fas fa-globe"></i>
                    </a>
                <?php } ?>
                <?php if (!empty($telefono)) { ?>
                    <a href="tel:<?php echo htmlspecialchars($telefono); ?>" class="call-icon">
                        <i class="fas fa-phone-alt"></i>
                    </a>
                <?php } ?>
            </div>

            <?php if (!empty($telefono)) { ?>
            <div class="row">
                <div class="col-md-9 offset-md-3">
                    <a href="../generar_vcard.php?nombre=<?php echo urlencode($nombre); ?>&apellido=<?php echo urlencode($apellido); ?>&telefono=<?php echo urlencode($telefono); ?>&profesion=<?php echo urlencode($profesion); ?>" class="btn btn-primary vcard-button">
                        <i class="fas fa-address-card"></i> <span>Agrégame a tus contactos</span>
                    </a>
                </div>
            </div>
            <?php } ?>

            <?php if (!empty($correo)) { ?>
            <div class="row">
                <div class="col-md-9 offset-md-3">
                    <a href="mailto:<?php echo htmlspecialchars($correo); ?>" class="btn btn-warning email-button">
                        <i class="fas fa-envelope"></i> <span>Escríbeme por email</span>
                    </a>
                </div>
            </div>
            <?php } ?>

            <?php if (!empty($whatsapp) || !empty($facebook) || !empty($tiktok) || !empty($instagram) || !empty($youtube) || !empty($linkedin) || !empty($twitter) || !empty($telegram)) { ?>
            <div class="row">
                <div class="col-md-9 offset-md-3 social-buttons">
                    <?php if (!empty($facebook)) { echo "<a href='". htmlspecialchars($facebook) ."' class='btn btn-primary'><i class='fab fa-facebook'></i> <span>Facebook</span></a>"; } ?>
                    <?php if (!empty($tiktok)) { echo "<a href='". htmlspecialchars($tiktok) ."' class='btn btn-dark'><i class='fab fa-tiktok'></i> <span>TikTok</span></a>"; } ?>
                    <?php if (!empty($instagram)) { echo "<a href='". htmlspecialchars($instagram) ."' class='btn btn-danger'><i class='fab fa-instagram'></i> <span>Instagram</span></a>"; } ?>
                    <?php if (!empty($youtube)) { echo "<a href='". htmlspecialchars($youtube) ."' class='btn btn-danger'><i class='fab fa-youtube'></i> <span>YouTube</span></a>"; } ?>
                    <?php if (!empty($linkedin)) { echo "<a href='". htmlspecialchars($linkedin) ."' class='btn btn-info'><i class='fab fa-linkedin'></i> <span>LinkedIn</span></a>"; } ?>
                    <?php if (!empty($twitter)) { echo "<a href='". htmlspecialchars($twitter) ."' class='btn btn-dark'><i class='fab fa-x-twitter'></i> <span>Twitter</span></a>"; } ?>
                    <?php if (!empty($telegram)) { echo "<a href='". htmlspecialchars($telegram) ."' class='btn btn-primary'><i class='fab fa-telegram'></i> <span>Telegram</span></a>"; } ?>
                </div>
            </div>
            <?php } ?>

            <?php if ($logo): ?>
            <div class="row logo">
                <div class="col-md-9 offset-md-3">
                    <img src="<?php echo htmlspecialchars($logo); ?>" alt="Logo de la empresa">
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty($whatsapp)) { ?>
        <a href="https://wa.me/<?php echo htmlspecialchars($whatsapp); ?>" class="whatsapp-button" target="_blank">
            <i class="fab fa-whatsapp"></i>
        </a>
    <?php } ?>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
