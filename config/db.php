<?php
// config/db.php

// Parámetros de conexión a la Capa de Datos (MySQL)
// Si pruebas desde tu máquina local (VS Code), usa la IP pública de la VM de MySQL (52.159.89.131).
// Si estás dentro de la VM 2 (Apache en Azure), coloca la IP privada interna de la VM de BD.
$host = '52.159.89.131'; 
$port = '3306';
$db   = 'gourmet_paradise';
$user = 'grupo3';
$pass = 'Grupo13253##';

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