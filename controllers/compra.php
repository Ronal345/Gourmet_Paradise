<?php
// controllers/compra.php
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. Control de Acceso por Roles (RBAC)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 1) {
    header("Location: ../login.php?error=no_autorizado");
    exit;
}

// 2. Validación de carrito no vacío
if (empty($_SESSION['carrito'])) {
    header("Location: ../carrito.php?error=El+carrito+está+vacío.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../carrito.php");
    exit;
}

$id_usuario   = (int)$_SESSION['usuario_id'];
$id_direccion = (int)($_POST['id_direccion'] ?? 0);
$metodo_pago  = trim($_POST['metodo_pago'] ?? 'TARJETA');
$carrito      = $_SESSION['carrito']; // Formato: [id_variante => cantidad]

if ($id_direccion <= 0) {
    header("Location: ../carrito.php?error=Dirección+de+entrega+no+válida.");
    exit;
}

try {
    // =========================================================================
    // INICIO DE TRANSACCIÓN ACID (Atomicidad, Consistencia, Aislamiento, Durabilidad)
    // =========================================================================
    $pdo->beginTransaction();

    $subtotal_pedido = 0.0;
    $items_para_orden = [];

    // Paso A: Bloqueo pesimista y verificación de stock para cada artículo
    foreach ($carrito as $id_variante => $cantidad) {
        $id_variante = (int)$id_variante;
        $cantidad    = (int)$cantidad;

        // SELECT ... FOR UPDATE bloquea la fila en MySQL hasta que termine la transacción
        $stmtStock = $pdo->prepare("SELECT v.id_variante, p.nombre AS producto, v.presentacion, v.precio_regular, v.stock 
                                    FROM variantes_producto v 
                                    INNER JOIN productos p ON v.id_producto = p.id_producto 
                                    WHERE v.id_variante = :id FOR UPDATE");
        $stmtStock->execute(['id' => $id_variante]);
        $prod = $stmtStock->fetch();

        if (!$prod) {
            throw new Exception("El producto seleccionado ya no existe en el catálogo.");
        }

        if ($prod['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente para '{$prod['producto']} ({$prod['presentacion']})'. Disponibles: {$prod['stock']}.");
        }

        $subtotal_linea = $prod['precio_regular'] * $cantidad;
        $subtotal_pedido += $subtotal_linea;

        $items_para_orden[] = [
            'id_variante'     => $id_variante,
            'cantidad'        => $cantidad,
            'precio_unitario' => $prod['precio_regular'],
            'subtotal_linea'  => $subtotal_linea
        ];
    }

    $costo_envio = 0.00;
    $descuento   = 0.00;
    $total_final = $subtotal_pedido;

    // Paso B: Insertar cabecera de la orden en la tabla 'pedidos'
    $stmtPedido = $pdo->prepare("INSERT INTO pedidos (id_usuario, id_direccion_envio, estado_pedido, subtotal, descuento, costo_envio, total) 
                                 VALUES (:usuario, :direccion, 'PAGADO', :subtotal, :descuento, :envio, :total)");
    $stmtPedido->execute([
        'usuario'   => $id_usuario,
        'direccion' => $id_direccion,
        'subtotal'  => $subtotal_pedido,
        'descuento' => $descuento,
        'envio'     => $costo_envio,
        'total'     => $total_final
    ]);
    $id_pedido = (int)$pdo->lastInsertId();

    // Paso C: Registrar líneas en 'detalle_pedidos' y descontar stock de 'variantes_producto'
    $stmtDetalle = $pdo->prepare("INSERT INTO detalle_pedidos (id_pedido, id_variante, cantidad, precio_unitario, subtotal_linea) 
                                  VALUES (:pedido, :variante, :cant, :precio, :linea)");

    $stmtUpdateStock = $pdo->prepare("UPDATE variantes_producto 
                                      SET stock = stock - :cant 
                                      WHERE id_variante = :id");

    foreach ($items_para_orden as $item) {
        // Congela el precio unitario histórico en la orden
        $stmtDetalle->execute([
            'pedido'   => $id_pedido,
            'variante' => $item['id_variante'],
            'cant'     => $item['cantidad'],
            'precio'   => $item['precio_unitario'],
            'linea'    => $item['subtotal_linea']
        ]);

        // Reduce las existencias reales en la capa de datos
        $stmtUpdateStock->execute([
            'cant' => $item['cantidad'],
            'id'   => $item['id_variante']
        ]);
    }

    // Paso D: Registrar constancia en la tabla 'pagos'
    $txn_codigo = 'TXN-' . strtoupper($metodo_pago) . '-' . rand(100000, 999999);
    $stmtPago = $pdo->prepare("INSERT INTO pagos (id_pedido, metodo_pago, transaccion_id, monto, estado_pago) 
                               VALUES (:pedido, :metodo, :txn, :monto, 'APROBADO')");
    $stmtPago->execute([
        'pedido' => $id_pedido,
        'metodo' => $metodo_pago,
        'txn'    => $txn_codigo,
        'monto'  => $total_final
    ]);

    // =========================================================================
    // CONFIRMAR TRANSACCIÓN DEFINITIVA EN MYSQL
    // =========================================================================
    $pdo->commit();

    // Vaciar el carrito de la memoria de sesión
    $_SESSION['carrito'] = [];

    // Redirección con mensaje de éxito al catálogo
    header("Location: ../catalogo.php?msg=compra_exitosa");
    exit;

} catch (Exception $e) {
    // Si cualquier operación falla, se revierte todo a su estado original
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header("Location: ../carrito.php?error=" . urlencode($e->getMessage()));
    exit;
}