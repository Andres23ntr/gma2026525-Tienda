<?php
// Incluir el archivo de cabecera y conexión a la base de datos
include('../includes/header.php');
require_once('../includes/db.php');

// Inicializar variables
$error = "";
$success = "";

// Verificar si se ha proporcionado el ID del juego
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener los datos actuales del juego
    $query = "SELECT * FROM games WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$game) {
        $error = "Juego no encontrado.";
    }
} else {
    $error = "ID de juego no proporcionado.";
}

// Procesar la solicitud de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_game'])) {
    $name = $_POST['name'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $platform = $_POST['platform'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';

    // Validar que todos los campos estén llenos
    if (empty($name) || empty($genre) || empty($platform) || empty($price) || empty($stock)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            // Preparar la consulta para actualizar el juego
            $stmt = $pdo->prepare("UPDATE games SET name = :name, genre = :genre, platform = :platform, price = :price, stock = :stock WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':genre', $genre);
            $stmt->bindParam(':platform', $platform);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':stock', $stock);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $success = "Juego actualizado exitosamente.";
            } else {
                $error = "Error al actualizar el juego.";
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<div class="container">
    <h2>Editar Juego</h2>

    <!-- Mostrar mensajes de éxito o error -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($game)): ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>" method="POST">
            <div class="form-group">
                <label for="name">Nombre:</label>
                <input type="text" class="form-control" name="name" required value="<?php echo htmlspecialchars($game['name']); ?>">
            </div>
            <div class="form-group">
                <label for="genre">Género:</label>
                <input type="text" class="form-control" name="genre" required value="<?php echo htmlspecialchars($game['genre']); ?>">
            </div>
            <div class="form-group">
                <label for="platform">Plataforma:</label>
                <input type="text" class="form-control" name="platform" required value="<?php echo htmlspecialchars($game['platform']); ?>">
            </div>
            <div class="form-group">
                <label for="price">Precio:</label>
                <input type="number" class="form-control" name="price" required value="<?php echo htmlspecialchars($game['price']); ?>">
            </div>
            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" class="form-control" name="stock" required min="0" value="<?php echo htmlspecialchars($game['stock']); ?>">
            </div>
            <button type="submit" name="update_game" class="btn btn-success">Actualizar Juego</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    <?php endif; ?>
</div>

<?php
include('../includes/footer.php');
?>
