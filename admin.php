<?php
// admin.php
session_start();

// 1. Control de Acceso por Roles (RBAC): Solo Administrador (id_rol = 2)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 2) {
    header("Location: login.php?error=no_autorizado");
    exit;
}

require_once __DIR__ . '/config/db.php';

try {
    // 2. Consulta de inventario completo (Lectura / Read)
    $sqlInventario = "SELECT v.id_variante, p.nombre AS producto, c.nombre AS categoria, 
                             v.sku, v.presentacion, v.precio_regular, v.stock 
                      FROM variantes_producto v 
                      INNER JOIN productos p ON v.id_producto = p.id_producto 
                      INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
                      ORDER BY v.id_variante DESC";
    $stmtInv = $pdo->query($sqlInventario);
    $inventario = $stmtInv->fetchAll();

    // 3. Consulta de productos base para el formulario de registro
    $stmtProd = $pdo->query("SELECT id_producto, nombre FROM productos ORDER BY nombre ASC");
    $lista_productos = $stmtProd->fetchAll();

} catch (PDOException $e) {
    die("Error al consultar el inventario: " . $e->getMessage());
}

// 4. Delegar la interfaz a la Capa de Presentación
require_once __DIR__ . '/views/admin_vista.php';