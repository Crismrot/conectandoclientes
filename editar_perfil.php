<?php
session_start();
include 'config/database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener los detalles actuales del usuario
$stmt = $conn->prepare("SELECT nombre, apellido, profesion, empresa, direccion, telefono, correo, whatsapp, facebook, tiktok, instagram, youtube, linkedin, twitter, telegram, pagina_web, foto_perfil, logo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($nombre, $apellido, $profesion, $empresa, $direccion, $telefono, $correoActual, $whatsapp, $facebook, $tiktok, $instagram, $youtube, $linkedin, $twitter, $telegram, $pagina_web, $fotoPerfilPath, $logoPath);
$stmt->fetch();
$stmt->close();

$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Verificar si el nuevo correo ya existe en la base de datos
    if ($correo !== $correoActual) {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "El correo electrónico ya está en uso. Por favor, elija otro.";
        }

        $stmt->close();
    }

    if (!isset($error)) {
        // Manejar la subida de nuevas imágenes
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (!empty($_FILES['foto_perfil']['name'])) {
            $fotoPerfilExtension = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
            if (in_array($fotoPerfilExtension, $allowedExtensions)) {
                // Eliminar la imagen de perfil anterior si existe
                if ($fotoPerfilPath && file_exists($fotoPerfilPath)) {
                    unlink($fotoPerfilPath);
                }
                $fechaHora = date('YmdHis');
                $fotoPerfilPath = "assets/uploads/perfil_" . str_replace('@', '', str_replace('.', '', $correo)) . "_" . $fechaHora . "." . $fotoPerfilExtension;
                move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $fotoPerfilPath);
            }
        }

        if (!empty($_FILES['logo']['name'])) {
            $logoExtension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            if (in_array($logoExtension, $allowedExtensions)) {
                // Eliminar el logo anterior si existe
                if ($logoPath && file_exists($logoPath)) {
                    unlink($logoPath);
                }
                $fechaHora = date('YmdHis');
                $logoPath = "assets/uploads/logo_" . str_replace('@', '', str_replace('.', '', $correo)) . "_" . $fechaHora . "." . $logoExtension;
                move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath);
            }
        }

        // Actualizar los datos del usuario en la base de datos
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, profesion = ?, empresa = ?, direccion = ?, telefono = ?, correo = ?, whatsapp = ?, facebook = ?, tiktok = ?, instagram = ?, youtube = ?, linkedin = ?, twitter = ?, telegram = ?, pagina_web = ?, foto_perfil = ?, logo = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssssssssssi", $nombre, $apellido, $profesion, $empresa, $direccion, $telefono, $correo, $whatsapp, $facebook, $tiktok, $instagram, $youtube, $linkedin, $twitter, $telegram, $pagina_web, $fotoPerfilPath, $logoPath, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();

        // Establecer el indicador de éxito para mostrar el modal
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Conectando Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            max-width: 800px;
        }
        .img-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .error {
            border-color: red;
        }
        .error-message {
            color: red;
            display: none;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script>
        function previewImage(inputId, previewId) {
            var input = document.getElementById(inputId);
            var preview = document.getElementById(previewId);
            var file = input.files[0];
            var reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
            };

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
                preview.style.display = 'none';
            }
        }

        $(document).ready(function() {
            var input = document.querySelector("#telefono");
            window.intlTelInput(input, {
                initialCountry: "pe",
                onlyCountries: ["pe"],
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
            });

            <?php if ($success): ?>
            $('#successModal').modal('show');
            <?php endif; ?>
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Editar Perfil</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="POST" action="editar_perfil.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre" class="required-field">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                <div id="error-nombre" class="error-message">Este campo es obligatorio.</div>
            </div>
            <div class="form-group">
                <label for="apellido" class="required-field">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
                <div id="error-apellido" class="error-message">Este campo es obligatorio.</div>
            </div>
            <div class="form-group">
                <label for="profesion">Profesión / Ocupación / Cargo</label>
                <input type="text" class="form-control" id="profesion" name="profesion" value="<?php echo htmlspecialchars($profesion); ?>">
            </div>
            <div class="form-group">
                <label for="empresa">Nombre de tu Empresa</label>
                <input type="text" class="form-control" id="empresa" name="empresa" value="<?php echo htmlspecialchars($empresa); ?>">
            </div>
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>">
            </div>
            <div class="form-group">
                <label for="telefono" class="required-field">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
                <div id="error-telefono" class="error-message">Este campo es obligatorio y debe contener solo números.</div>
            </div>
            <div class="form-group">
                <label for="correo" class="required-field">Correo Electrónico (Usuario de inicio de sesión)</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($correoActual); ?>" required>
                <div id="error-correo" class="error-message">Este campo es obligatorio y debe contener un '@'.</div>
            </div>
            <div class="form-group">
                <label for="whatsapp">WhatsApp</label>
                <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($whatsapp); ?>">
            </div>
            <div class="form-group">
                <label for="facebook">Facebook</label>
                <input type="text" class="form-control" id="facebook" name="facebook" value="<?php echo htmlspecialchars($facebook); ?>">
            </div>
            <div class="form-group">
                <label for="tiktok">TikTok</label>
                <input type="text" class="form-control" id="tiktok" name="tiktok" value="<?php echo htmlspecialchars($tiktok); ?>">
            </div>
            <div class="form-group">
                <label for="instagram">Instagram</label>
                <input type="text" class="form-control" id="instagram" name="instagram" value="<?php echo htmlspecialchars($instagram); ?>">
            </div>
            <div class="form-group">
                <label for="youtube">YouTube</label>
                <input type="text" class="form-control" id="youtube" name="youtube" value="<?php echo htmlspecialchars($youtube); ?>">
            </div>
            <div class="form-group">
                <label for="linkedin">LinkedIn</label>
                <input type="text" class="form-control" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($linkedin); ?>">
            </div>
            <div class="form-group">
                <label for="twitter">Twitter</label>
                <input type="text" class="form-control" id="twitter" name="twitter" value="<?php echo htmlspecialchars($twitter); ?>">
            </div>
            <div class="form-group">
                <label for="telegram">Telegram</label>
                <input type="text" class="form-control" id="telegram" name="telegram" value="<?php echo htmlspecialchars($telegram); ?>">
            </div>
            <div class="form-group">
                <label for="pagina_web">Página Web</label>
                <input type="text" class="form-control" id="pagina_web" name="pagina_web" value="<?php echo htmlspecialchars($pagina_web); ?>">
            </div>
            <div class="form-group">
                <label for="foto_perfil">Foto de Perfil</label>
                <img id="preview_foto_perfil" class="img-preview" src="<?php echo $fotoPerfilPath ? $fotoPerfilPath : '#'; ?>" alt="Vista previa de la foto de perfil" style="display: <?php echo $fotoPerfilPath ? 'block' : 'none'; ?>;">
                <input type="file" class="form-control-file" id="foto_perfil" name="foto_perfil" accept="image/png, image/jpeg" onchange="previewImage('foto_perfil', 'preview_foto_perfil')">
            </div>
            <div class="form-group">
                <label for="logo">Logo</label>
                <img id="preview_logo" class="img-preview" src="<?php echo $logoPath ? $logoPath : '#'; ?>" alt="Vista previa del logo" style="display: <?php echo $logoPath ? 'block' : 'none'; ?>;">
                <input type="file" class="form-control-file" id="logo" name="logo" accept="image/png, image/jpeg" onchange="previewImage('logo', 'preview_logo')">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Guardar Cambios</button>
            <a href="panel-cliente.php" class="btn btn-secondary btn-block">Cancelar</a>
        </form>
    </div>

    <!-- Modal de éxito -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Perfil Actualizado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tus cambios han sido guardados exitosamente.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="redirectToPanel()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function redirectToPanel() {
            window.location.href = 'panel-cliente.php';
        }
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
