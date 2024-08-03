<?php
include 'config/database.php';

// Obtener datos del usuario desde los parámetros GET
$nombre = $_GET['nombre'] ?? '';
$apellido = $_GET['apellido'] ?? '';
$telefono = $_GET['telefono'] ?? '';
$profesion = $_GET['profesion'] ?? '';

// Verificar que al menos el nombre y el apellido estén presentes
if (empty($nombre) || empty($apellido)) {
    echo "Perfil no encontrado.";
    exit();
}

// Construir la vCard
$vcard = "BEGIN:VCARD\n";
$vcard .= "VERSION:3.0\n";
$vcard .= "FN:" . htmlspecialchars($nombre) . " " . htmlspecialchars($apellido) . "\n";
$vcard .= "N:" . htmlspecialchars($apellido) . ";" . htmlspecialchars($nombre) . "\n";
if (!empty($profesion)) {
    $vcard .= "TITLE:" . htmlspecialchars($profesion) . "\n";
}
if (!empty($telefono)) {
    $vcard .= "TEL;TYPE=WORK,VOICE:" . htmlspecialchars($telefono) . "\n";
}
$vcard .= "END:VCARD";

// Configurar los encabezados para la descarga del archivo
header('Content-Type: text/vcard; charset=utf-8');
header('Content-Disposition: attachment; filename="' . htmlspecialchars($nombre) . '_' . htmlspecialchars($apellido) . '.vcf"');
echo $vcard;
?>
