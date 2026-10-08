<?php
// catalogo.php
session_start();

// Control de acceso por roles (RBAC)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 1) {
    header("Location: login.php?error=no_autorizado");
    exit;
}

require_once __DIR__ . '/config/db.php';

try {
    // Consulta a la Capa de Datos: productos y sus variantes con stock activo
    $sql = "SELECT v.id_variante, p.nombre AS producto, c.nombre AS categoria, 
                   v.presentacion, v.precio_regular, v.stock, p.notas_de_cata
            FROM variantes_producto v
            INNER JOIN productos p ON v.id_producto = p.id_producto
            INNER JOIN categorias c ON p.id_categoria = c.id_categoria
            WHERE v.stock > 0
            ORDER BY c.nombre ASC, p.nombre ASC";

    $stmt = $pdo->query($sql);
    $productos = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error al consultar el catálogo: " . $e->getMessage());
}

// Invocar la Capa de Presentación
require_once __DIR__ . '/views/catalogo_vista.php';