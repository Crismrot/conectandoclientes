<?php
include 'database.php';

function generatePassword() {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, 8);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $profesion = $_POST['profesion'];
    $empresa = $_POST['empresa'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];  // Ahora incluye el código del país
    $correo = $_POST['correo'];
    $whatsapp = $_POST['whatsapp'];
    $facebook = $_POST['facebook'];
    $tiktok = $_POST['tiktok'];
    $instagram = $_POST['instagram'];
    $youtube = $_POST['youtube'];
    $linkedin = $_POST['linkedin'];
    $twitter = $_POST['twitter'];
    $telegram = $_POST['telegram'];
    $pagina_web = $_POST['pagina_web'];
    $modelo = $_POST['modelo'];

    $foto_perfil = $_FILES['foto_perfil'];
    $logo = $_FILES['logo'];

    // Validar que el teléfono solo contenga números
    if (!preg_match('/^\+\d+$/', $telefono)) {
        header("Location: registro.php?error=3");
        exit();
    }

    // Validar que el correo no esté registrado
    $checkEmail = $conn->query("SELECT * FROM usuarios WHERE correo = '$correo'");
    if ($checkEmail->num_rows > 0) {
        header("Location: registro.php?error=1");
        exit();
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if ($foto_perfil['name']) {
        $fotoPerfilExtension = pathinfo($foto_perfil['name'], PATHINFO_EXTENSION);
        if (!in_array($fotoPerfilExtension, $allowedExtensions)) {
            header("Location: registro.php?error=2");
            exit();
        }
        $fechaHora = date('YmdHis');
        $fotoPerfilPath = "assets/uploads/perfil_" . $correo . "_" . $fechaHora . "." . $fotoPerfilExtension;
        move_uploaded_file($foto_perfil['tmp_name'], $fotoPerfilPath);
    } else {
        $fotoPerfilPath = null;
    }

    if ($logo['name']) {
        $logoExtension = pathinfo($logo['name'], PATHINFO_EXTENSION);
        if (!in_array($logoExtension, $allowedExtensions)) {
            header("Location: registro.php?error=2");
            exit();
        }
        $fechaHora = date('YmdHis');
        $logoPath = "assets/uploads/logo_" . $correo . "_" . $fechaHora . "." . $logoExtension;
        move_uploaded_file($logo['tmp_name'], $logoPath);
    } else {
        $logoPath = null;
    }

    $clave = generatePassword();
    $claveHash = password_hash($clave, PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (nombre, apellido, profesion, empresa, direccion, telefono, correo, whatsapp, facebook, tiktok, instagram, youtube, linkedin, twitter, telegram, pagina_web, foto_perfil, logo, modelo_elegido, clave)
            VALUES ('$nombre', '$apellido', '$profesion', '$empresa', '$direccion', '$telefono', '$correo', '$whatsapp', '$facebook', '$tiktok', '$instagram', '$youtube', '$linkedin', '$twitter', '$telegram', '$pagina_web', '$fotoPerfilPath', '$logoPath', '$modelo', '$claveHash')";

    if ($conn->query($sql) === TRUE) {
        $to = $correo;
        $subject = "Registro exitoso - Conectando Clientes";
        $message = "Hola $nombre,\n\nGracias por registrarte en Conectando Clientes. Aquí están tus detalles de acceso:\n\nCorreo: $correo\nClave: $clave\n\nSaludos,\nEl equipo de Conectando Clientes";
        $headers = "From: registro@conectandoclientes.com";

        mail($to, $subject, $message, $headers);

        echo "Registro exitoso. Revisa tu correo para más detalles.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
