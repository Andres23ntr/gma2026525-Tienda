<?php
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "game_store";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Preparar y ejecutar la consulta para encontrar al usuario por su email
    $stmt = $conn->prepare('SELECT id, name, password, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró el usuario
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Iniciar sesión y guardar información del usuario en la sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirigir a crud.php
            header('Location:../modules/crud.php');
            exit;
        } else {
            // Contraseña incorrecta
            $error = "Contraseña incorrecta";
        }
    } else {
        // Usuario no encontrado
        $error = "Correo electrónico no encontrado";
    }
}

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['user_id'])) {
    echo "<h1>Bienvenido, " . htmlspecialchars($_SESSION['user_name']) . "</h1>";
    echo "<p>Rol: " . htmlspecialchars($_SESSION['user_role']) . "</p>";
    // Aquí puedes mostrar las órdenes o la lógica que necesitas
} else {
    // Mostrar el formulario de login si el usuario no ha iniciado sesión o si hubo un error
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Flopper</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <style>
            .form-container {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
            }
            .card {
                border-radius: 10px;
            }
            .image-container {
                margin-right: 0; /* Sin espacios entre imagen y formulario */
            }
            .img-fluid {
                border-radius: 50%;
            }
            body {
                background: linear-gradient(to right, #f5f5dc, #d2b48c); /* Degradado de beige claro a beige más oscuro */
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0;
}

        </style>
    </head>
    <body>
    <div class="container form-container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="d-flex align-items-center mt-5">
            <div class="image-container">
                <img src="../assets/flop.jpeg" alt="Flopper Logo"  class="d-block w-100 rounded"width="420" height="320">
            </div>
            <div class="card shadow p-4 w-100"> <!-- Ajuste de ancho horizontal -->
            <h2 class="text-center mb-4">Login</h2>
                <form action="" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Por favor, ingresa un correo electrónico válido.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Por favor, ingresa tu contraseña.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Validaciones de formulario en el frontend
        (function () {
            'use strict'
            var forms = document.querySelectorAll('form')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
    </html>
    <?php
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
