<?php
session_start();

// Manejar el proceso de cierre de sesión
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destruir la sesión y borrar la cookie de sesión
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, 
            $params["path"], 
            $params["domain"], 
            $params["secure"], 
            $params["httponly"]
        );
    }
    session_destroy();
    
    // Redirigir a la página de inicio
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Juegos Físicos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(to right, #f5f5dc, #d2b48c); /* Degradado beige claro a beige oscuro */
        }
        .navbar {
            border-radius: 0 0 10px 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Game Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../modules/crud.php">CRUD Juegos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../modules/order.php">Realizar Orden</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../modules/receipt.php">Generar Recibo</a>
                        </li>
                        <li class="nav-item">
                            <a href="?action=logout" class="btn btn-danger">Cerrar sesión</a>
                        </li>
                    <?php else: ?>
                        <!-- Opciones para usuarios no autenticados pueden ir aquí -->
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido de la página va aquí -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
