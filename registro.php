<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function getParamValue($param) {
    return isset($_GET[$param]) ? htmlspecialchars($_GET[$param]) : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Conectando Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css" />
    <style>
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
        .btn-whatsapp {
            background-color: #25d366;
            color: white;
        }
        .icon-color {
            margin-right: 8px;
        }
        .fa-whatsapp { color: #25d366; }
        .fa-facebook { color: #3b5998; }
        .fa-tiktok { color: #000000; }
        .fa-instagram { color: #E1306C; }
        .fa-youtube { color: #FF0000; }
        .fa-linkedin { color: #0077B5; }
        .fa-x-twitter { color: #1F140F; }
        .fa-telegram { color: #0088cc; }
        .fa-globe { color: #000000; }
        .img-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .stepper {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 8px;
        }
        .stepper .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
        }
        .stepper .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -50%;
            width: 100%;
            height: 2px;
            background-color: #ccc;
            z-index: -1;
        }
        .stepper .step .circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stepper .step.active .circle {
            background-color: #28a745;
        }
        .stepper .step .label {
            font-size: 14px;
            font-weight: bold;
            display: none;
        }
        .stepper .step.active .label {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro</h2>
        <div class="stepper">
            <div class="step active" id="step1Indicator">
                <div class="circle">1</div>
                <div class="label">Datos Personales</div>
            </div>
            <div class="step" id="step2Indicator">
                <div class="circle">2</div>
                <div class="label">Redes Sociales</div>
            </div>
            <div class="step" id="step3Indicator">
                <div class="circle">3</div>
                <div class="label">Multimedia</div>
            </div>
            <div class="step" id="step4Indicator">
                <div class="circle">4</div>
                <div class="label">Escoger Modelo</div>
            </div>
        </div>
        <form id="registrationForm" action="process_registration.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div id="step1" class="step-content active">
                <div class="form-group">
                    <label for="nombre" class="required-field">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo getParamValue('nombre'); ?>" required>
                    <div id="error-nombre" class="error-message">Este campo es obligatorio.</div>
                </div>
                <div class="form-group">
                    <label for="apellido" class="required-field">Apellido</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo getParamValue('apellido'); ?>" required>
                    <div id="error-apellido" class="error-message">Este campo es obligatorio.</div>
                </div>
                <div class="form-group">
                    <label for="profesion">Profesión / Ocupación / Cargo</label>
                    <input type="text" class="form-control" id="profesion" name="profesion" value="<?php echo getParamValue('profesion'); ?>">
                </div>
                <div class="form-group">
                    <label for="empresa">Nombre de tu Empresa</label>
                    <input type="text" class="form-control" id="empresa" name="empresa" value="<?php echo getParamValue('empresa'); ?>">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo getParamValue('direccion'); ?>">
                </div>
                <div class="form-group">
                    <label for="telefono" class="required-field">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo getParamValue('telefono'); ?>" required>
                    <div id="error-telefono" class="error-message">Este campo es obligatorio y debe contener solo números.</div>
                </div>
                <div class="form-group">
                    <label for="correo" class="required-field">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="<?php echo getParamValue('correo'); ?>" required>
                    <div id="error-correo" class="error-message">Este campo es obligatorio y debe contener un '@'.</div>
                </div>
                <div id="step1Error" class="error-message">Por favor, complete todos los campos obligatorios.</div>
                <button type="button" class="btn btn-primary" onclick="validateStep1()">Siguiente</button>
            </div>
            <div id="step2" class="step-content" style="display: none;">
                <div class="form-group">
                    <label for="whatsapp"><i class="fab fa-whatsapp icon-color"></i> WhatsApp</label>
                    <input type="tel" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo getParamValue('whatsapp'); ?>">
                </div>
                <div class="form-group">
                    <label for="facebook"><i class="fab fa-facebook icon-color"></i> Facebook</label>
                    <input type="text" class="form-control" id="facebook" name="facebook" value="<?php echo getParamValue('facebook'); ?>">
                </div>
                <div class="form-group">
                    <label for="tiktok"><i class="fab fa-tiktok icon-color"></i> TikTok</label>
                    <input type="text" class="form-control" id="tiktok" name="tiktok" value="<?php echo getParamValue('tiktok'); ?>">
                </div>
                <div class="form-group">
                    <label for="instagram"><i class="fab fa-instagram icon-color"></i> Instagram</label>
                    <input type="text" class="form-control" id="instagram" name="instagram" value="<?php echo getParamValue('instagram'); ?>">
                </div>
                <div class="form-group">
                    <label for="youtube"><i class="fab fa-youtube icon-color"></i> YouTube</label>
                    <input type="text" class="form-control" id="youtube" name="youtube" value="<?php echo getParamValue('youtube'); ?>">
                </div>
                <div class="form-group">
                    <label for="linkedin"><i class="fab fa-linkedin icon-color"></i> LinkedIn</label>
                    <input type="text" class="form-control" id="linkedin" name="linkedin" value="<?php echo getParamValue('linkedin'); ?>">
                </div>
                <div class="form-group">
                    <label for="twitter"><i class="fab fa-x-twitter icon-color"></i> Twitter</label>
                    <input type="text" class="form-control" id="twitter" name="twitter" value="<?php echo getParamValue('twitter'); ?>">
                </div>
                <div class="form-group">
                    <label for="telegram"><i class="fab fa-telegram icon-color"></i> Telegram</label>
                    <input type="text" class="form-control" id="telegram" name="telegram" value="<?php echo getParamValue('telegram'); ?>">
                </div>
                <div class="form-group">
                    <label for="pagina_web"><i class="fas fa-globe icon-color"></i> Página Web</label>
                    <input type="text" class="form-control" id="pagina_web" name="pagina_web" value="<?php echo getParamValue('pagina_web'); ?>">
                </div>
                <button type="button" class="btn btn-secondary" onclick="previousStep(1)">Volver a Datos Personales</button>
                <button type="button" class="btn btn-primary" onclick="validateStep2()">Siguiente</button>
            </div>
            <div id="step3" class="step-content" style="display: none;">
                <div class="form-group">
                    <label for="foto_perfil">Foto de Perfil</label>
                    <input type="file" class="form-control-file" id="foto_perfil" name="foto_perfil" accept="image/png, image/jpeg" onchange="previewImage('foto_perfil', 'preview_foto_perfil')">
                    <img id="preview_foto_perfil" class="img-preview" src="#" alt="Vista previa de la foto de perfil" style="display: none;">
                    <div id="error-foto_perfil" class="error-message">Solo se aceptan archivos JPG y PNG.</div>
                </div>
                <div class="form-group">
                    <label for="logo">Logo</label>
                    <input type="file" class="form-control-file" id="logo" name="logo" accept="image/png, image/jpeg" onchange="previewImage('logo', 'preview_logo')">
                    <img id="preview_logo" class="img-preview" src="#" alt="Vista previa del logo" style="display: none;">
                    <div id="error-logo" class="error-message">Solo se aceptan archivos JPG y PNG.</div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="previousStep(2)">Volver a Redes Sociales</button>
                <button type="button" class="btn btn-primary" onclick="validateStep3()">Siguiente</button>
            </div>
            <div id="step4" class="step-content" style="display: none;">
                <div class="form-group">
                    <label for="modelo">Elige un modelo:</label>
                    <div class="row">
                        <?php
                        for ($i = 1; $i <= 6; $i++) {
                            echo '<div class="col-md-4">
                                    <label>
                                        <input type="radio" name="modelo" value="modelo'.$i.'.jpg" ' . (getParamValue('modelo') == 'modelo'.$i.'.jpg' ? 'checked' : '') . ' required>
                                        <img src="assets/images/modelo'.$i.'.jpg" class="img-fluid">
                                    </label>
                                  </div>';
                        }
                        ?>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="previousStep(3)">Volver a Multimedia</button>
                <button type="submit" class="btn btn-success" onclick="finalizeRegistration()">Finalizar</button>
            </div>
        </form>
    </div>

    <!-- Modal para formato de archivo incorrecto -->
    <div class="modal fade" id="fileFormatModal" tabindex="-1" aria-labelledby="fileFormatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileFormatModalLabel">Formato de archivo no permitido</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Solo se aceptan archivos en formato JPG y PNG. Por favor, suba un archivo con el formato correcto.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para correo ya registrado -->
    <div class="modal fade" id="emailExistsModal" tabindex="-1" aria-labelledby="emailExistsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailExistsModalLabel">Correo ya registrado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    El correo ya se encuentra registrado. Por favor, comuníquese con soporte al número +51962171195 por WhatsApp.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-whatsapp" onclick="redirectToWhatsApp()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para formato de correo incorrecto -->
    <div class="modal fade" id="emailFormatModal" tabindex="-1" aria-labelledby="emailFormatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailFormatModalLabel">Formato de correo no válido</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    El correo electrónico ingresado no tiene un formato válido. Por favor, ingrese un correo electrónico válido.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para dominio de correo no válido -->
    <div class="modal fade" id="emailDomainModal" tabindex="-1" aria-labelledby="emailDomainModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailDomainModalLabel">Dominio de correo no válido</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    El dominio del correo electrónico ingresado no existe. Por favor, ingrese un correo electrónico con un dominio válido.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para registro exitoso -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Registro Exitoso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tu registro se ha completado exitosamente. Revisa tu correo para más detalles.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="window.location.href='login.php'">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script>
        var iti, itiWhatsapp;

        // Función para validar el primer paso del formulario
        function validateStep1() {
            var valid = true;
            $('#step1 input[required]').each(function() {
                if ($(this).val() === '') {
                    valid = false;
                    $(this).addClass('error');
                    $('#error-' + $(this).attr('id')).show();
                } else if ($(this).attr('id') === 'telefono' && !/^\d+$/.test(iti.getNumber().replace(/\D/g, ''))) {
                    valid = false;
                    $(this).addClass('error');
                    $('#error-' + $(this).attr('id')).show();
                } else if ($(this).attr('id') === 'correo' && !$(this).val().includes('@')) {
                    valid = false;
                    $(this).addClass('error');
                    $('#error-' + $(this).attr('id')).show();
                } else {
                    $(this).removeClass('error');
                    $('#error-' + $(this).attr('id')).hide();
                }
            });

            if (valid) {
                nextStep(2);
                $('#step1Error').hide();
            } else {
                $('#step1Error').show();
            }
        }

        // Función para validar el segundo paso del formulario
        function validateStep2() {
            nextStep(3);
        }

        // Función para validar el tercer paso del formulario
        function validateStep3() {
            var valid = true;
            var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

            var fotoPerfil = $('#foto_perfil').val();
            var logo = $('#logo').val();

            if (fotoPerfil && !allowedExtensions.exec(fotoPerfil)) {
                valid = false;
                $('#foto_perfil').addClass('error');
                $('#error-foto_perfil').show();
                showFileFormatModal();
            } else {
                $('#foto_perfil').removeClass('error');
                $('#error-foto_perfil').hide();
            }

            if (logo && !allowedExtensions.exec(logo)) {
                valid = false;
                $('#logo').addClass('error');
                $('#error-logo').show();
                showFileFormatModal();
            } else {
                $('#logo').removeClass('error');
                $('#error-logo').hide();
            }

            if (valid) {
                nextStep(4);
            }
        }

        // Función para avanzar al siguiente paso del formulario
        function nextStep(step) {
            $('.step-content').hide();
            $('#step' + step).show();
            $('.step').removeClass('active');
            $('#step' + step + 'Indicator').addClass('active');
        }

        // Función para retroceder al paso anterior del formulario
        function previousStep(step) {
            $('.step-content').hide();
            $('#step' + step).show();
            $('.step').removeClass('active');
            $('#step' + step + 'Indicator').addClass('active');
        }

        // Función para mostrar el modal de formato de archivo incorrecto
        function showFileFormatModal() {
            $('#fileFormatModal').modal('show');
        }

        // Función para mostrar el modal de correo ya registrado
        function showEmailExistsModal() {
            $('#emailExistsModal').modal('show');
        }

        // Función para mostrar el modal de formato de correo incorrecto
        function showEmailFormatModal() {
            $('#emailFormatModal').modal('show');
        }

        // Función para mostrar el modal de dominio de correo no válido
        function showEmailDomainModal() {
            $('#emailDomainModal').modal('show');
        }

        // Función para redirigir a WhatsApp
        function redirectToWhatsApp() {
            window.location.href = 'https://wa.me/51962171195';
        }

        // Función para validar todo el formulario
        function validateForm() {
            var valid = true;

            // Validar el primer paso del formulario
            $('#step1 input[required]').each(function() {
                if ($(this).val() === '') {
                    valid = false;
                    $(this).addClass('error');
                    $('#error-' + $(this).attr('id')).show();
                } else if ($(this).attr('id') === 'telefono' && !/^\d+$/.test(iti.getNumber().replace(/\D/g, ''))) {
                    valid = false;
                    $(this).addClass('error');
                    $('#error-' + $(this).attr('id')).show();
                } else if ($(this).attr('id') === 'correo' && !$(this).val().includes('@')) {
                    valid = false;
                    $(this).addClass('error');
                    $('#error-' + $(this).attr('id')).show();
                } else {
                    $(this).removeClass('error');
                    $('#error-' + $(this).attr('id')).hide();
                }
            });

            if (!valid) {
                nextStep(1);
                return false;
            }

            return valid;
        }

        // Función para finalizar el registro y enviar el formulario
        function finalizeRegistration() {
            // Asegurarse de que todos los pasos estén validados antes de enviar
            if (!validateForm()) {
                return false;
            }

            // Obtener el número de teléfono completo incluyendo el código del país
            var fullPhoneNumber = iti.getNumber();
            $('#telefono').val(fullPhoneNumber);

            // Obtener el número de WhatsApp completo incluyendo el código del país
            var fullWhatsappNumber = itiWhatsapp.getNumber().replace(/^\+/, ''); // Remover el signo '+'
            $('#whatsapp').val(fullWhatsappNumber);

            // Enviar el formulario
            $('#registrationForm').submit();
        }

        // Función para mostrar una vista previa de la imagen seleccionada
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
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                const error = urlParams.get('error');
                if (error == 'email_format') {
                    showEmailFormatModal();
                } else if (error == 'email_domain') {
                    showEmailDomainModal();
                } else if (error == '1') {
                    showEmailExistsModal();
                } else if (error == '2') {
                    showFileFormatModal();
                }
            } else if (urlParams.has('success')) {
                $('#successModal').modal('show');
            }

            var input = document.querySelector("#telefono");
            iti = window.intlTelInput(input, {
                initialCountry: "pe",
                onlyCountries: ["pe"],
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
            });

            var inputWhatsapp = document.querySelector("#whatsapp");
            itiWhatsapp = window.intlTelInput(inputWhatsapp, {
                initialCountry: "pe",
                onlyCountries: ["pe"],
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
            });

            // Evitar el envío del formulario con la tecla Enter excepto en el último paso
            $('#registrationForm').on('keypress', function(e) {
                if (e.key === 'Enter') {
                    var activeStep = $('.step-content:visible').attr('id');
                    if (activeStep !== 'step4') {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
</body>
</html>
