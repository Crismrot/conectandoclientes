<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "conectandoclientes";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed. Please try again later.");
}

// Configurar el conjunto de caracteres
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $conn->error);
    die("Character set error. Please try again later.");
}
?>
