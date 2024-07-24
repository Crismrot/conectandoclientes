<?php
session_start();
include 'database.php';

// Función para generar una contraseña aleatoria
function generatePassword() {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, 8);
}

// Función para verificar si el formato del correo electrónico es válido
function isEmailFormatValid($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para comprobar si el dominio del correo electrónico existe
function doesEmailDomainExist($email) {
    $domain = substr(strrchr($email, "@"), 1);
    return checkdnsrr($domain, "MX");
}

function redirectWithParams($params) {
    $query = http_build_query($params);
    header("Location: registro.php?$query");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: registro.php?error=csrf");
        exit();
    }

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

    $params = [
        'nombre' => $nombre,
        'apellido' => $apellido,
        'profesion' => $profesion,
        'empresa' => $empresa,
        'direccion' => $direccion,
        'telefono' => $telefono,
        'correo' => $correo,
        'whatsapp' => $whatsapp,
        'facebook' => $facebook,
        'tiktok' => $tiktok,
        'instagram' => $instagram,
        'youtube' => $youtube,
        'linkedin' => $linkedin,
        'twitter' => $twitter,
        'telegram' => $telegram,
        'pagina_web' => $pagina_web,
        'modelo' => $modelo,
        'error' => ''
    ];

    // Validar el formato del correo electrónico
    if (!isEmailFormatValid($correo)) {
        $params['error'] = 'email_format';
        redirectWithParams($params);
    }

    // Comprobar si el dominio del correo electrónico existe
    if (!doesEmailDomainExist($correo)) {
        $params['error'] = 'email_domain';
        redirectWithParams($params);
    }

    // Validar que el teléfono solo contenga números
    if (!preg_match('/^\+\d+$/', $telefono)) {
        $params['error'] = '3';
        redirectWithParams($params);
    }

    // Validar que el correo no esté registrado
    $checkEmail = $conn->query("SELECT * FROM usuarios WHERE correo = '$correo'");
    if ($checkEmail->num_rows > 0) {
        $params['error'] = '1';
        redirectWithParams($params);
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if ($foto_perfil['name']) {
        $fotoPerfilExtension = pathinfo($foto_perfil['name'], PATHINFO_EXTENSION);
        if (!in_array($fotoPerfilExtension, $allowedExtensions)) {
            $params['error'] = '2';
            redirectWithParams($params);
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
            $params['error'] = '2';
            redirectWithParams($params);
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

        // Redirigir con parámetro de éxito
        header("Location: registro.php?status=success");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
