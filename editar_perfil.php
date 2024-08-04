<?php
session_start();
include 'config/database.php';

$user_id = $_SESSION['user_id'];

// Obtener los datos del usuario de la base de datos
$stmt = $conn->prepare("SELECT nombre, apellido, profesion, empresa, direccion, telefono, correo, whatsapp, facebook, tiktok, instagram, youtube, linkedin, twitter, telegram, pagina_web, foto_perfil, logo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nombre, $apellido, $profesion, $empresa, $direccion, $telefono, $correo, $whatsapp, $facebook, $tiktok, $instagram, $youtube, $linkedin, $twitter, $telegram, $pagina_web, $foto_perfil, $logo);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Conectando Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <style>
        .img-preview {
            width: 300px;
            height: 300px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Perfil</h2>
        <form action="update_perfil.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
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
                <label for="telefono">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
            </div>
            <div class="form-group">
                <label for="whatsapp">WhatsApp</label>
                <input type="tel" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo htmlspecialchars($whatsapp); ?>">
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
                <?php if ($foto_perfil): ?>
                    <img id="preview_foto_perfil" src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Vista previa de la foto de perfil" class="img-thumbnail img-preview">
                <?php else: ?>
                    <img id="preview_foto_perfil" src="#" alt="Vista previa de la foto de perfil" class="img-thumbnail img-preview" style="display: none;">
                <?php endif; ?>
                <input type="file" class="form-control-file" id="foto_perfil" name="foto_perfil" accept="image/png, image/jpeg" onchange="previewImage('foto_perfil', 'preview_foto_perfil')">
                <input type="hidden" name="existing_foto_perfil" value="<?php echo htmlspecialchars($foto_perfil); ?>">
            </div>
            <div class="form-group">
                <label for="logo">Logo</label>
                <?php if ($logo): ?>
                    <img id="preview_logo" src="<?php echo htmlspecialchars($logo); ?>" alt="Vista previa del logo" class="img-thumbnail img-preview">
                <?php else: ?>
                    <img id="preview_logo" src="#" alt="Vista previa del logo" class="img-thumbnail img-preview" style="display: none;">
                <?php endif; ?>
                <input type="file" class="form-control-file" id="logo" name="logo" accept="image/png, image/jpeg" onchange="previewImage('logo', 'preview_logo')">
                <input type="hidden" name="existing_logo" value="<?php echo htmlspecialchars($logo); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Perfil Actualizado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Su perfil ha sido actualizado exitosamente.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="window.location.href='panel-cliente.php'">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
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
                preview.src = "#";
                preview.style.display = 'none';
            }
        }

        var telefonoInput = document.querySelector("#telefono");
        var whatsappInput = document.querySelector("#whatsapp");

        var itiTelefono = intlTelInput(telefonoInput, {
            onlyCountries: ["pe"],
            initialCountry: "pe",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });

        var itiWhatsapp = intlTelInput(whatsappInput, {
            onlyCountries: ["pe"],
            initialCountry: "pe",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        $(document).ready(function() {
            $('#successModal').modal('show');
        });
        <?php endif; ?>
    </script>
</body>
</html>
