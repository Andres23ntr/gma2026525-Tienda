<?php
// Incluir el archivo de conexión a la base de datos
require_once '../includes/db.php'; // Verifica que la ruta sea correcta

// Definir variables y inicializar con valores vacíos
$name = $email = $password = $role = "";
$error = "";

// Procesar la solicitud cuando se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validar que el rol no esté vacío
    if (empty($role)) {
        $error = "El rol es requerido.";
    } else {
        // Comprobar si la conexión a la base de datos está establecida
        if (isset($pdo)) {
            try {
                // Verificar si el correo electrónico ya existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $error = "El correo electrónico ya está registrado.";
                } else {
                    // Preparar la declaración SQL
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
                    // Asignar los parámetros
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT)); // Asegúrate de hashear la contraseña
                    $stmt->bindParam(':role', $role);

                    // Ejecutar la declaración
                    if ($stmt->execute()) {
                        // Redirigir al usuario a index.php después del registro exitoso
                        header("Location: ../index.php");
                        exit;
                    } else {
                        $error = "Error al registrar el usuario.";
                    }
                }
            } catch (PDOException $e) {
                $error = "Error en la base de datos: " . $e->getMessage();
            }
        } else {
            $error = "Conexión a la base de datos no establecida.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Registro de Usuario</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input type="text" class="form-control" name="name" required placeholder="Nombre" value="<?php echo htmlspecialchars($name); ?>">
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" class="form-control" name="email" required placeholder="Correo Electrónico" value="<?php echo htmlspecialchars($email); ?>">
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" name="password" required placeholder="Contraseña">
        </div>
        <div class="form-group">
            <label for="role">Rol:</label>
            <select class="form-control" name="role" required>
                <option value="">Seleccione un rol</option>
                <option value="client" <?php echo ($role === 'client') ? 'selected' : ''; ?>>Cliente</option>
                <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
</div>
</body>
</html>
