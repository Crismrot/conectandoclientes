<?php
session_start();
include 'config/database.php';

$user_id = $_SESSION['user_id'];

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$profesion = $_POST['profesion'];
$empresa = $_POST['empresa'];
$direccion = $_POST['direccion'];
$telefono = $_POST['telefono'];
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

$foto_perfil = $_FILES['foto_perfil'];
$logo = $_FILES['logo'];

$existing_foto_perfil = $_POST['existing_foto_perfil'];
$existing_logo = $_POST['existing_logo'];

$update_foto_perfil = $existing_foto_perfil;
$update_logo = $existing_logo;

$fechaHora = date('YmdHis');
$allowedExtensions = ['jpg', 'jpeg', 'png'];

// Manejo de la foto de perfil
if ($foto_perfil['name']) {
    $fotoPerfilExtension = pathinfo($foto_perfil['name'], PATHINFO_EXTENSION);
    if (in_array($fotoPerfilExtension, $allowedExtensions)) {
        $update_foto_perfil = "assets/uploads/perfil_" . $correo . "_" . $fechaHora . "." . $fotoPerfilExtension;
        move_uploaded_file($foto_perfil['tmp_name'], $update_foto_perfil);

        if (!empty($existing_foto_perfil) && file_exists($existing_foto_perfil)) {
            unlink($existing_foto_perfil);
        }
    }
}

// Manejo del logo
if ($logo['name']) {
    $logoExtension = pathinfo($logo['name'], PATHINFO_EXTENSION);
    if (in_array($logoExtension, $allowedExtensions)) {
        $update_logo = "assets/uploads/logo_" . $correo . "_" . $fechaHora . "." . $logoExtension;
        move_uploaded_file($logo['tmp_name'], $update_logo);

        if (!empty($existing_logo) && file_exists($existing_logo)) {
            unlink($existing_logo);
        }
    }
}

// Formatear el número de teléfono y WhatsApp
$telefonoFormatted = "+51" . ltrim($telefono, '0');
$whatsappFormatted = "51" . ltrim($whatsapp, '0');

$stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, profesion = ?, empresa = ?, direccion = ?, telefono = ?, correo = ?, whatsapp = ?, facebook = ?, tiktok = ?, instagram = ?, youtube = ?, linkedin = ?, twitter = ?, telegram = ?, pagina_web = ?, foto_perfil = ?, logo = ? WHERE id = ?");
$stmt->bind_param("ssssssssssssssssssi", $nombre, $apellido, $profesion, $empresa, $direccion, $telefonoFormatted, $correo, $whatsappFormatted, $facebook, $tiktok, $instagram, $youtube, $linkedin, $twitter, $telegram, $pagina_web, $update_foto_perfil, $update_logo, $user_id);

if ($stmt->execute()) {
    header("Location: editar_perfil.php?success=1");
} else {
    echo "Error al actualizar el perfil.";
}

$stmt->close();
$conn->close();
?>
