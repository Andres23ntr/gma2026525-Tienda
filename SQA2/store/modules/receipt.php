<?php
include('../includes/header.php');
require_once('../includes/db.php');

session_start();
$order_games = $_SESSION['selected_games'] ?? [];
$total = $_SESSION['total'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $received = floatval($_POST['received']);
    $change = $received - $total;

    if ($received < $total) {
        echo "<div class='alert alert-danger'>El monto recibido es insuficiente.</div>";
    } else {
        try {
            $db->beginTransaction();

            foreach ($order_games as $game) {
                $game_id = $game['id'];
                $quantity = $game['quantity'];

                // Actualizar el inventario del juego
                $stmt = $db->prepare("UPDATE games SET stock = stock - :quantity WHERE id = :game_id");
                $stmt->execute([
                    ':quantity' => $quantity,
                    ':game_id' => $game_id
                ]);
            }

            $db->commit();

            // Limpiar la sesión después de la compra
            unset($_SESSION['selected_games']);
            unset($_SESSION['total']);

            echo "<div class='alert alert-success'>Compra realizada con éxito. Cambio: $change USD.</div>";

        } catch (Exception $e) {
            $db->rollBack();
            echo "<div class='alert alert-danger'>Error al procesar la compra: " . $e->getMessage() . "</div>";
        }
    }
}

if (empty($order_games)) {
    echo "<div class='alert alert-danger'>No hay juegos seleccionados para el recibo.</div>";
    exit;
}

if ($total <= 0) {
    echo "<div class='alert alert-danger'>El total es inválido.</div>";
    exit;
}
?>

<div class="container">
    <h2>Recibo de Compra</h2>
    <?php foreach ($order_games as $game): ?>
        <p><strong>Juego:</strong> <?php echo htmlspecialchars($game['name']); ?></p>
        <p><strong>Cantidad:</strong> <?php echo htmlspecialchars($game['quantity']); ?></p>
        <p><strong>Precio Unitario:</strong> $<?php echo htmlspecialchars($game['price']); ?></p>
        <p><strong>Total:</strong> $<?php echo htmlspecialchars($game['price'] * $game['quantity']); ?></p>
        <hr>
    <?php endforeach; ?>
    
    <h4>Total a Pagar: $<?php echo htmlspecialchars($total); ?></h4>

    <form id="payment-form" method="POST" onsubmit="return validateForm()">
        <div class="mb-3">
            <label for="received" class="form-label">Monto Recibido</label>
            <input type="number" class="form-control" id="received" name="received" min="0" step="0.01" required oninput="calculateChange()">
        </div>
        <div class="mb-3">
            <strong>Cambio:</strong> <span id="change">0</span> USD
        </div>
        <button type="submit" class="btn btn-success mt-3">Finalizar Compra</button>
    </form>

    <a href="index.php" class="btn btn-secondary mt-3">Volver</a>
</div>

<script>
function calculateChange() {
    const total = <?php echo $total; ?>;
    const received = parseFloat(document.getElementById('received').value) || 0;
    const change = received - total;

    document.getElementById('change').innerText = change >= 0 ? change.toFixed(2) : '0';
}

function validateForm() {
    const total = <?php echo $total; ?>;
    const received = parseFloat(document.getElementById('received').value) || 0;
    
    if (received < total) {
        alert('El monto recibido es insuficiente.');
        return false;
    }
    
    return true;
}
</script>

<?php
include('../includes/footer.php');
?>
