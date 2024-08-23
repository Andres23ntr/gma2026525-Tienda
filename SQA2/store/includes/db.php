<?php
// includes/db.php

$host = '127.0.0.1';
$dbname = 'game_store';
$username = 'root';
$password = '';

try {
    // Crear una nueva conexión PDO
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password); // Cambiado de $pdo a $db
    
    // Configurar el modo de errores de PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Manejo de errores
    die("Conexión fallida: " . $e->getMessage());
}
?>
