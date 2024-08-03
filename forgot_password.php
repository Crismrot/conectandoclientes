<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Conectando Clientes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            margin-top: 50px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #333;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            padding: 10px 20px;
            border-bottom: none;
        }
        .btn-primary {
            background-color: #d9534f;
            border: none;
        }
        .btn-primary:hover {
            background-color: #c9302c;
        }
        .alert-danger {
            background-color: #f2dede;
            border-color: #ebccd1;
            color: #a94442;
        }
        a {
            color: #d9534f;
        }
        a:hover {
            color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Recuperar Contraseña
            </div>
            <div class="card-body">
                <form method="POST" action="send_reset_link.php">
                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Enviar Enlace de Restablecimiento</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
