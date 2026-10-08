<?php
// controllers/admin_crud.php
session_start();
require_once __DIR__ . '/../config/db.php';

// Control de acceso: solo Administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 2) {
    header("Location: ../login.php?error=no_autorizado");
    exit;
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

// ========================================================
// 1. CREATE: Insertar una nueva variante de producto
// ========================================================
if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto  = (int)($_POST['id_producto'] ?? 0);
    $sku          = trim($_POST['sku'] ?? '');
    $presentacion = trim($_POST['presentacion'] ?? '');
    $precio       = (float)($_POST['precio'] ?? 0);
    $stock        = (int)($_POST['stock'] ?? 0);

    if ($id_producto <= 0 || empty($sku) || empty($presentacion) || $precio <= 0 || $stock < 0) {
        header("Location: ../admin.php?error=" . urlencode("Todos los campos son obligatorios y deben ser válidos."));
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO variantes_producto (id_producto, sku, presentacion, precio_regular, stock) 
                               VALUES (:prod, :sku, :pres, :precio, :stock)");
        $stmt->execute([
            'prod'   => $id_producto,
            'sku'    => $sku,
            'pres'   => $presentacion,
            'precio' => $precio,
            'stock'  => $stock
        ]);

        header("Location: ../admin.php?msg=creado");
        exit;
    } catch (PDOException $e) {
        header("Location: ../admin.php?error=" . urlencode("Error al insertar: " . $e->getMessage()));
        exit;
    }
}

// ========================================================
// 2. UPDATE: Actualizar precio regular y stock
// ========================================================
if ($accion === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_variante = (int)($_POST['id_variante'] ?? 0);
    $precio      = (float)($_POST['precio'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);

    if ($id_variante <= 0 || $precio <= 0 || $stock < 0) {
        header("Location: ../admin.php?error=" . urlencode("Valores de precio o stock no válidos."));
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE variantes_producto 
                               SET precio_regular = :precio, stock = :stock 
                               WHERE id_variante = :id");
        $stmt->execute([
            'precio' => $precio,
            'stock'  => $stock,
            'id'     => $id_variante
        ]);

        header("Location: ../admin.php?msg=actualizado");
        exit;
    } catch (PDOException $e) {
        header("Location: ../admin.php?error=" . urlencode("Error al actualizar: " . $e->getMessage()));
        exit;
    }
}

// ========================================================
// 3. DELETE: Eliminar una variante de producto
// ========================================================
if ($accion === 'eliminar') {
    $id_variante = (int)($_GET['id_variante'] ?? 0);

    if ($id_variante <= 0) {
        header("Location: ../admin.php?error=" . urlencode("ID de variante no válido."));
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM variantes_producto WHERE id_variante = :id");
        $stmt->execute(['id' => $id_variante]);

        header("Location: ../admin.php?msg=eliminado");
        exit;
    } catch (PDOException $e) {
        // Código 23000: restricción de clave foránea (ya tiene pedidos asociados en detalle_pedidos)
        if ($e->getCode() == '23000') {
            header("Location: ../admin.php?error=" . urlencode("No se puede eliminar la variante porque ya cuenta con historial de compras registradas."));
        } else {
            header("Location: ../admin.php?error=" . urlencode("Error al eliminar: " . $e->getMessage()));
        }
        exit;
    }
}

header("Location: ../admin.php");
exit;