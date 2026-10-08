<?php
// carrito.php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 1) {
    header("Location: login.php?error=no_autorizado");
    exit;
}

require_once __DIR__ . '/config/db.php';

$carrito = $_SESSION['carrito'] ?? [];
$items_carrito = [];
$gran_total = 0.0;
$id_usuario = $_SESSION['usuario_id'];

// 1. Si hay artículos en sesión, consultar detalles en la base de datos
if (!empty($carrito)) {
    $ids = array_keys($carrito);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "SELECT v.id_variante, p.nombre AS producto, c.nombre AS categoria, 
                   v.presentacion, v.precio_regular, v.stock 
            FROM variantes_producto v 
            INNER JOIN productos p ON v.id_producto = p.id_producto 
            INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
            WHERE v.id_variante IN ($placeholders)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $productos_bd = $stmt->fetchAll();

    foreach ($productos_bd as $prod) {
        $cant = $carrito[$prod['id_variante']];
        $subtotal = $prod['precio_regular'] * $cant;
        $gran_total += $subtotal;

        $items_carrito[] = [
            'id_variante'    => $prod['id_variante'],
            'producto'       => $prod['producto'],
            'categoria'      => $prod['categoria'],
            'presentacion'   => $prod['presentacion'],
            'precio_regular' => $prod['precio_regular'],
            'stock'          => $prod['stock'],
            'cantidad'       => $cant,
            'subtotal'       => $subtotal
        ];
    }
}

// 2. Consultar direcciones de entrega para el cliente
try {
    $stmtDir = $pdo->prepare("SELECT id_direccion, direccion_linea1, distrito, ciudad 
                              FROM direcciones 
                              WHERE id_usuario = :id");
    $stmtDir->execute(['id' => $id_usuario]);
    $direcciones = $stmtDir->fetchAll();

    // Si el usuario no tiene dirección registrada, traer una de muestra
    if (empty($direcciones)) {
        $stmtDef = $pdo->query("SELECT id_direccion, direccion_linea1, distrito, ciudad FROM direcciones LIMIT 1");
        $direcciones = $stmtDef->fetchAll();
    }
} catch (PDOException $e) {
    die("Error al consultar direcciones: " . $e->getMessage());
}

// Invocar la Capa de Presentación
require_once __DIR__ . '/views/carrito_vista.php';