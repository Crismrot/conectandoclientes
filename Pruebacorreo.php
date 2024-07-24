<?php
$to = "test@example.com"; // Dirección de correo de prueba
$subject = "Prueba de correo electrónico";
$message = "Hola, este es un mensaje de prueba desde MailHog.";
$headers = "From: registro@conectandoclientes.com";

if (mail($to, $subject, $message, $headers)) {
    echo "Correo enviado exitosamente.";
} else {
    echo "Error al enviar el correo.";
}
?>
