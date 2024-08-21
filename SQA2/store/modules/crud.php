<?php
// Incluir el archivo de cabecera y conexión a la base de datos
include('../includes/header.php');
require_once('../includes/db.php');

// Inicializar variables
$error = "";
$success = "";

// Procesar la solicitud de agregar un juego
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_game'])) {
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
            // Preparar la consulta para agregar el juego
            $stmt = $pdo->prepare("INSERT INTO games (name, genre, platform, price, stock) VALUES (:name, :genre, :platform, :price, :stock)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':genre', $genre);
            $stmt->bindParam(':platform', $platform);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':stock', $stock);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $success = "Juego agregado exitosamente.";
            } else {
                $error = "Error al agregar el juego.";
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Procesar la solicitud de eliminación de un juego
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    try {
        // Eliminar el juego
        $stmt = $pdo->prepare("DELETE FROM games WHERE id = :id");
        $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $success = "Juego eliminado exitosamente.";
        } else {
            $error = "Error al eliminar el juego.";
        }
    } catch (PDOException $e) {
        $error = "Error en la base de datos: " . htmlspecialchars($e->getMessage());
    }
}

// Obtener la lista de juegos
$query = "SELECT * FROM games";
$stmt = $pdo->prepare($query);
$stmt->execute();
?>

<div class="container">
    <h2>Gestión de Juegos</h2>

    <!-- Mostrar mensajes de éxito o error -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input type="text" class="form-control" name="name" required placeholder="Nombre">
        </div>
        <div class="form-group">
            <label for="genre">Género:</label>
            <input type="text" class="form-control" name="genre" required placeholder="Género">
        </div>
        <div class="form-group">
            <label for="platform">Plataforma:</label>
            <input type="text" class="form-control" name="platform" required placeholder="Plataforma">
        </div>
        <div class="form-group">
            <label for="price">Precio:</label>
            <input type="number" class="form-control" name="price" required placeholder="Precio">
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" class="form-control" name="stock" required placeholder="Stock" min="0">
        </div>
        <button type="submit" name="add_game" class="btn btn-success">Añadir Juego</button>
    </form>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Género</th>
                <th>Plataforma</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Aquí se llenan los juegos desde la base de datos -->
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['genre']); ?></td>
                    <td><?php echo htmlspecialchars($row['platform']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?> USD</td>
                    <td><?php echo htmlspecialchars($row['stock']); ?></td>
                    <td>
                        <a href='edit_game.php?id=<?php echo $row['id']; ?>' class='btn btn-warning'>Editar</a>
                        <a href='index.php?delete_id=<?php echo $row['id']; ?>' class='btn btn-danger' onclick="return confirm('¿Está seguro de que desea eliminar este juego?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
include('../includes/footer.php');
?>
