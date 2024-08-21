<?php
require_once '../includes/db.php'; // Asegúrate de que el archivo de conexión a la base de datos esté bien ubicado

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $genre = $_POST['genre'];
    $platform = $_POST['platform'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("INSERT INTO games (name, genre, platform, price) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $genre, $platform, $price])) {
        echo "Juego añadido exitosamente.";
        header("Location: ../modules/manage_games.php"); // Redirige a la página de gestión de juegos
    } else {
        echo "Ocurrió un error al añadir el juego. Inténtalo de nuevo.";
    }
} else {
    echo "Método de solicitud no válido.";
}
?>
