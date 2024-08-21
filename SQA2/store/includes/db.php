<?php
// includes/db.php

$host = '127.0.0.1'; // Cambia si tu servidor de base de datos no está en localhost
$dbname = 'game_store'; // Reemplaza con el nombre de tu base de datos
$username = 'root'; // Reemplaza con tu usuario de base de datos
$password = ''; // Reemplaza con tu contraseña de base de datos

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configurar el modo de errores de PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Manejo de errores
    die("Conexión fallida: " . $e->getMessage());
}
?>
