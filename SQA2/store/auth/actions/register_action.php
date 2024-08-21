<?php
// Incluir el archivo de conexión a la base de datos
require_once '../../includes/db.php'; // Verifica que la ruta sea correcta

// Verificar si se está utilizando el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $name = $_POST['name'] ?? ''; // Usa el operador de coalescencia nula para evitar errores
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? ''; // Verifica si 'role' está definido

    // Validar que el rol no esté vacío
    if (empty($role)) {
        die("El rol es requerido.");
    }

    // Comprobar si la conexión a la base de datos está establecida
    if (isset($pdo)) {
        try {
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
                header("../../index.php"); // Cambia la ruta según tu estructura de directorios
                exit; // Termina el script después de la redirección
            } else {
                echo "Error al registrar el usuario.";
            }
        } catch (PDOException $e) {
            echo "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        echo "Conexión a la base de datos no establecida.";
    }
} else {
    echo "Método de solicitud no válido.";
}
?>
