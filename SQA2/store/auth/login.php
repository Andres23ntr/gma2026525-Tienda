// auth/login.php
<?php
include('../includes/header.php');
?>
<div class="container">
    <h2>Login</h2>
    <form action="login_action.php" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
<?php
include('../includes/footer.php');
?>
