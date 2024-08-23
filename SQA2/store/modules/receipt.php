<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Verificar si la sesión está activa
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

include('../includes/header.php');
require_once('../includes/db.php');

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

            // Actualizar el inventario del juego
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

            // Notificación de éxito
            echo "<div class='alert alert-success'>Compra realizada con éxito. Cambio: " . number_format($change, 2) . " USD.</div>";

        } catch (Exception $e) {
            $db->rollBack();
            echo "<div class='alert alert-danger'>Error al procesar la compra: " . htmlspecialchars($e->getMessage()) . "</div>";
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
        <p><strong>Precio Unitario:</strong> $<?php echo number_format($game['price'], 2); ?></p>
        <p><strong>Total:</strong> $<?php echo number_format($game['price'] * $game['quantity'], 2); ?></p>
        <hr>
    <?php endforeach; ?>
    
    <h4>Total a Pagar: $<?php echo number_format($total, 2); ?></h4>

    <form id="payment-form" method="POST" onsubmit="return validateForm()">
        <div class="mb-3">
            <label for="received" class="form-label">Monto Recibido</label>
            <input type="number" class="form-control" id="received" name="received" min="0" step="0.01" required oninput="calculateChange()">
        </div>
        <div class="mb-3">
            <strong>Cambio:</strong> <span id="change">0.00</span> USD
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

    document.getElementById('change').innerText = change >= 0 ? change.toFixed(2) : '0.00';
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
