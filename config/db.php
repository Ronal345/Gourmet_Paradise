<?php
// config/db.php

// Parámetros de conexión a la Capa de Datos (MySQL)

$host = 'IP_PUBLICA'; 
$port = '3306';
$db   = 'gourmet_paradise';
$user = 'USER_NAME';
$pass = 'PASSWORD';

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en fallos de consulta
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devolver registros como arreglos asociativos
        PDO::ATTR_EMULATE_PREPARES   => false                   // Forzar consultas preparadas reales a nivel de motor
    ]);
} catch (PDOException $e) {
    // Si la conexión falla, se detiene la ejecución con un mensaje claro
    die("Error crítico en la Capa de Datos: " . $e->getMessage());
}
