<?php
// Incluir el archivo de cabecera y conexión a la base de datos
include('../includes/header.php');
require_once('../includes/db.php');

// Inicializar variables para errores y total
$error = "";
$total = 0;

// Variables para filtros
$filter_genre = $_GET['genre'] ?? '';
$filter_min_price = $_GET['min_price'] ?? '';
$filter_max_price = $_GET['max_price'] ?? '';
$filter_min_stock = $_GET['min_stock'] ?? '';
$filter_name = $_GET['name'] ?? '';

// Inicializar el array para los juegos seleccionados
$selected_games = [];

// Procesar el formulario de orden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los juegos seleccionados
    $games = $_POST['games'] ?? [];
    $received_amount = $_POST['received_amount'] ?? 0; // Monto recibido

    // Validar que se seleccionen juegos
    if (empty($games)) {
        $error = "Debes seleccionar al menos un juego.";
    } else {
        // Recorrer cada juego seleccionado para calcular el total
        foreach ($games as $game_id => $data) {
            $quantity = $data['quantity'] ?? 0;

            if (empty($quantity) || $quantity <= 0) continue; // Saltar si la cantidad es inválida

            // Comprobar stock disponible
            $stmt = $pdo->prepare("SELECT name, stock, price FROM games WHERE id = :game_id");
            $stmt->bindParam(':game_id', $game_id);
            $stmt->execute();
            $game = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($game && $game['stock'] >= $quantity) {
                // Calcular el total por este juego
                $total += $game['price'] * $quantity;
                // Almacenar los juegos seleccionados para pasarlos al recibo
                $selected_games[] = [
                    'id' => $game_id,
                    'name' => $game['name'],
                    'price' => $game['price'],
                    'quantity' => $quantity
                ];
            } else {
                $error = "No hay suficiente stock disponible para el juego: " . htmlspecialchars($game['name'] ?? 'Desconocido');
                break; // Salir del bucle en caso de error
            }
        }

        // Si no hubo errores, calcular el cambio y redirigir a la página de recibo
        if (empty($error)) {
            $change = $received_amount - $total; // Calcular el cambio
            session_start();
            $_SESSION['selected_games'] = $selected_games; // Guardar juegos seleccionados en sesión
            $_SESSION['total'] = $total; // Guardar total en sesión
            $_SESSION['received_amount'] = $received_amount; // Guardar monto recibido en sesión
            $_SESSION['change'] = $change; // Guardar el cambio en sesión
            header("Location: receipt.php"); // Cambia a la página de recibo
            exit;
        }
    }
}

// Construir la consulta SQL con filtros
$query = "SELECT * FROM games WHERE stock > 0"; // Solo juegos con stock > 0
$params = [];

if ($filter_name) {
    $query .= " AND name LIKE :name";
    $params[':name'] = "%" . $filter_name . "%";
}
if ($filter_genre) {
    $query .= " AND genre = :genre";
    $params[':genre'] = $filter_genre;
}
if ($filter_min_price) {
    $query .= " AND price >= :min_price";
    $params[':min_price'] = $filter_min_price;
}
if ($filter_max_price) {
    $query .= " AND price <= :max_price";
    $params[':max_price'] = $filter_max_price;
}
if ($filter_min_stock) {
    $query .= " AND stock >= :min_stock";
    $params[':min_stock'] = $filter_min_stock;
}

// Preparar y ejecutar la consulta
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener géneros únicos para el filtro
$genre_query = "SELECT DISTINCT genre FROM games";
$genre_stmt = $pdo->prepare($genre_query);
$genre_stmt->execute();
$genres = $genre_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container">
    <h2>Realizar Orden</h2>

    <!-- Mostrar mensaje de error si existe -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Barra de búsqueda y filtros -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="name" class="form-label">Buscar por Nombre</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($filter_name); ?>" placeholder="Nombre del juego">
            </div>
            <div class="col-md-2">
                <label for="genre" class="form-label">Género</label>
                <select id="genre" name="genre" class="form-select">
                    <option value="">Seleccione un género</option>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?php echo htmlspecialchars($genre); ?>" <?php echo ($filter_genre === $genre) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($genre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="min_price" class="form-label">Precio Mínimo</label>
                <input type="number" class="form-control" id="min_price" name="min_price" value="<?php echo htmlspecialchars($filter_min_price); ?>" placeholder="0">
            </div>
            <div class="col-md-2">
                <label for="max_price" class="form-label">Precio Máximo</label>
                <input type="number" class="form-control" id="max_price" name="max_price" value="<?php echo htmlspecialchars($filter_max_price); ?>" placeholder="0">
            </div>
            <div class="col-md-2">
                <label for="min_stock" class="form-label">Stock Mínimo</label>
                <input type="number" class="form-control" id="min_stock" name="min_stock" value="<?php echo htmlspecialchars($filter_min_stock); ?>" placeholder="0">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
    </form>

    <!-- Formulario de orden -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="mb-3">
            <label for="games" class="form-label">Selecciona los Juegos y Cantidades</label>
            <?php foreach ($games as $game): ?>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="game_<?php echo $game['id']; ?>" name="games[<?php echo $game['id']; ?>][quantity]" data-price="<?php echo $game['price']; ?>" onchange="updatePrice(this)">
                    <label class="form-check-label" for="game_<?php echo $game['id']; ?>">
                        <?php echo htmlspecialchars($game['name']); ?> - <?php echo htmlspecialchars($game['price']); ?> USD - Stock: <?php echo htmlspecialchars($game['stock']); ?>
                    </label>
                    <input type="number" class="form-control mt-1" name="games[<?php echo $game['id']; ?>][quantity]" min="1" value="1" placeholder="Cantidad" onchange="calculateTotal()" style="width: 100px; display: inline;">
                </div>
            <?php endforeach; ?>
        </div>

     

        <div class="mb-3">
            <strong>Total a Pagar: </strong>
            <span id="total-price">0</span> USD
        </div>

       

        <button type="submit" class="btn btn-success">Generar Recibo</button>
    </form>
</div>

<script>
// Función para actualizar el total
function calculateTotal() {
    let total = 0;
    document.querySelectorAll('input[type="checkbox"]:checked').forEach(function(checkbox) {
        let price = parseFloat(checkbox.getAttribute('data-price'));
        let quantity = parseInt(checkbox.nextElementSibling.nextElementSibling.value) || 0;
        total += price * quantity;
    });
    document.getElementById('total-price').textContent = total.toFixed(2);
}

// Función para calcular el cambio

// Función para habilitar/deshabilitar el campo de cantidad según la selección del juego
function updatePrice(checkbox) {
    checkbox.nextElementSibling.nextElementSibling.disabled = !checkbox.checked;
    calculateTotal();
}
</script>

<?php include('../includes/footer.php'); ?>
