<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flopper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        body {
            padding-top: 20px;
        }

        nav {
            width: 100%;
            
            color: #ffffff; /* Color de texto claro */
        }

        .carousel-item img {
            width: 100%;
            height: 600px;
            object-fit: cover;
        }

        .description-section {
            background-color: #f8f9fa;
            padding: 40px 20px;
        }

        .description-section h2 {
            margin-bottom: 20px;
        }

        .footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container-fluid px-0">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h2 class="text-center">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
            <div class="text-center mb-3">
                <a href="modules/order.php" class="btn btn-success">Ver Órdenes</a>
                <a href="?action=logout" class="btn btn-danger">Cerrar sesión</a>
            </div>

            <?php
            // Manejar el proceso de cierre de sesión
            if (isset($_GET['action']) && $_GET['action'] == 'logout') {
                session_destroy();
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
            ?>

        <?php else: ?>
            <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="./index.php">
                        <img src="../store/assets/flop.jpeg" alt="" width="60" height="50" class="d-inline-block align-text-top rounded-circle"> Flopper
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a href="auth/login.php" class="btn btn-outline-success me-2">Login</a>
                            </li>
                            <br>
                            <li class="nav-item">
                                <a href="auth/register.php" class="btn btn-outline-warning">Registro</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        <?php endif; ?>

        <div id="carouselExampleCaptions" class="carousel slide mt-3" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://i.ytimg.com/vi/QJkGkfLBP2w/maxresdefault.jpg?sqp=-oaymwEmCIAKENAF8quKqQMa8AEB-AH-CYAC0AWKAgwIABABGH8gEygYMA8=&rs=AOn4CLAroAnF2FmKrIDnQkjgR3_9MTTvNg" class="d-block w-100 rounded" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>First slide label</h5>
                        <p>Disponible</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://img.asmedia.epimg.net/resizer/v2/BH54FVWW2BKE3FX7MX2TOXXVHM.jpg?auth=b0c5899607a7d4575e55bda10f7b6a3f7a2426a8d9c17fb9dddffb621ac0681e&width=1472&height=828&smart=true" class="d-block w-100 rounded" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Playstation</h5>
                        <p>Disponible</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://static0.gamerantimages.com/wordpress/wp-content/uploads/2024/02/xbox-logo-5.jpg" class="d-block w-100 rounded" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Xbox</h5>
                        <p>Disponible</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <!-- Sección de descripción de la tienda -->
        <div class="description-section text-center mx-auto" style="max-width: 800px; margin-top: 20px;">
    <h2>Sobre Flopper</h2>
    <p>Flopper es tu tienda de juegos físicos favorita, donde podrás encontrar una amplia variedad de títulos para todas las plataformas. Desde los clásicos hasta los lanzamientos más recientes, Flopper se dedica a ofrecer lo mejor en videojuegos a precios competitivos. Nuestra tienda física cuenta con un ambiente acogedor, ideal para todos los entusiastas de los videojuegos.</p>
</div>


        <!-- Módulo de contacto e informaciones -->
        <footer class="footer text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Contacto</h5>
                        <p>Email: contacto@flopper.com</p>
                        <p>Teléfono: +123 456 789</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Ubicación</h5>
                        <p>Calle Gamer, 123</p>
                        <p>Ciudad de los Videojuegos, VG 4567</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Síguenos</h5>
                        <a href="#">Facebook</a> | 
                        <a href="#">Twitter</a> | 
                        <a href="#">Instagram</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
