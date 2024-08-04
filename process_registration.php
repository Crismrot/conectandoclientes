<?php
session_start();
include 'config/database.php';
require __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

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

    // Simulación del pago (Monto a cobrar)
    $monto = 200.00; // Definir el monto a cobrar aquí

    // Simulación de pago exitoso
    $pagoExitoso = true; // Cambiar a false para simular un fallo en el pago

    if (!$pagoExitoso) {
        echo "Error en el procesamiento del pago.";
        exit();
    }

    // Generar URL única para el perfil
    $urlPerfil = strtolower($nombre . $apellido . $fechaHora);

    $clave = generatePassword();
    $claveHash = password_hash($clave, PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (nombre, apellido, profesion, empresa, direccion, telefono, correo, whatsapp, facebook, tiktok, instagram, youtube, linkedin, twitter, telegram, pagina_web, foto_perfil, logo, modelo_elegido, clave, url_perfil)
            VALUES ('$nombre', '$apellido', '$profesion', '$empresa', '$direccion', '$telefono', '$correo', '$whatsapp', '$facebook', '$tiktok', '$instagram', '$youtube', '$linkedin', '$twitter', '$telegram', '$pagina_web', '$fotoPerfilPath', '$logoPath', '$modelo', '$claveHash', '$urlPerfil')";

    if ($conn->query($sql) === TRUE) {
        // Generar el código QR
        $qrCode = new QrCode("http://localhost/app/perfiles/$urlPerfil");
        $writer = new SvgWriter();
        $qrCode->setSize(300);

        // Guardar el código QR como SVG
        $qrFileName = "assets/qrcodes/qr_" . strtolower($nombre . "_" . $apellido . "_" . $fechaHora) . ".svg";
        $qrSvg = $writer->write($qrCode)->getString();
        file_put_contents($qrFileName, $qrSvg);

        // Enviar correo al usuario
        $to = $correo;
        $subject = "Registro exitoso - Conectando Clientes";
        $message = "Hola $nombre,\n\nGracias por registrarte en Conectando Clientes. Aquí están tus detalles de acceso:\n\nCorreo: $correo\nClave: $clave\n\nPuedes acceder a tu perfil en el siguiente enlace:\nhttp://localhost/app/perfiles/$urlPerfil\n\nAdemás, adjuntamos un código QR con el enlace a tu perfil.\n\nSaludos,\nEl equipo de Conectando Clientes";
        $headers = "From: registro@conectandoclientes.com";

        // Adjuntar el código QR
        $filePath = realpath($qrFileName);
        $content = file_get_contents($filePath);
        $content = chunk_split(base64_encode($content));
        $uid = md5(uniqid(time()));
        $filename = basename($filePath);

        $boundary = "----=" . md5(uniqid(mt_rand()));
        $headers .= "\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"$boundary\"";
        $messageBody = "--$boundary\r\n";
        $messageBody .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
        $messageBody .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $messageBody .= $message . "\r\n";
        $messageBody .= "--$boundary\r\n";
        $messageBody .= "Content-Type: image/svg+xml; name=\"$filename\"\r\n";
        $messageBody .= "Content-Transfer-Encoding: base64\r\n";
        $messageBody .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
        $messageBody .= $content . "\r\n";
        $messageBody .= "--$boundary--";

        mail($to, $subject, $messageBody, $headers);

        // Enviar correo al administrador con los datos del usuario y el QR
        $adminEmail = "registro@conectandoclientes.com";
        $subjectAdmin = "Nuevo usuario registrado: $nombre $apellido";
        $messageAdmin = "Se ha registrado un nuevo usuario con los siguientes datos:\n\nNombre: $nombre $apellido\nCorreo: $correo\nTeléfono: $telefono\nURL del perfil: http://localhost/app/perfiles/$urlPerfil\n";

        mail($adminEmail, $subjectAdmin, $messageBody, $headers);

        header("Location: registro.php?success=1");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
