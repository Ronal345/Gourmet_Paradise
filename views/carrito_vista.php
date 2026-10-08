<?php
// Prevenir advertencias de VS Code (Intelephense) inicializando valores por defecto si no vienen del controlador
$items_carrito = $items_carrito ?? [];
$direcciones   = $direcciones ?? [];
$gran_total    = $gran_total ?? 0.0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito — Gourmet Paradise</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="contenedor">
    <header>
        <div>
            <h1>Carrito de Compras</h1>
            <p class="subtitulo">Revisa tus productos gourmet antes de confirmar el pedido</p>
        </div>
        <div class="nav-links">
            <a href="catalogo.php" class="btn btn-secondary">&larr; Seguir Comprando</a>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </header>

    <?php if (isset($_GET['error'])): ?>
        <div class="alerta alerta-error">
            <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($items_carrito)): ?>
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <h2>Tu carrito está vacío</h2>
            <p style="margin: 15px 0;">Explora el catálogo y añade presentaciones a tu pedido.</p>
            <a href="catalogo.php" class="btn btn-primary">Ir al Catálogo</a>
        </div>
    <?php else: ?>
        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Presentación</th>
                        <th>Precio Unit.</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th style="text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items_carrito as $item): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($item['producto']); ?></strong><br>
                                <small style="color: #64748b;"><?php echo htmlspecialchars($item['categoria']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($item['presentacion']); ?></td>
                            <td>S/ <?php echo number_format($item['precio_regular'], 2); ?></td>
                            <td><strong><?php echo $item['cantidad']; ?></strong></td>
                            <td>S/ <?php echo number_format($item['subtotal'], 2); ?></td>
                            <td style="text-align: center;">
                                <a href="controllers/carrito.php?accion=quitar&id_variante=<?php echo $item['id_variante']; ?>" 
                                   class="btn btn-danger" style="padding: 4px 8px; font-size: 11px;">Quitar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 15px; margin-bottom: 25px;">
            <a href="controllers/carrito.php?accion=vaciar" class="btn btn-danger" style="font-size: 12px;">Vaciar Carrito Completo</a>
        </div>

        <!-- Resumen y checkout hacia controllers/compra.php -->
        <div class="resumen-pago">
            <h3 style="margin-bottom: 15px;">Detalles de Entrega y Pago</h3>
            <form action="controllers/compra.php" method="POST">
                <div class="form-group">
                    <label for="id_direccion">Dirección de Envío:</label>
                    <select name="id_direccion" id="id_direccion" class="form-control" required>
                        <?php foreach ($direcciones as $d): ?>
                            <option value="<?php echo $d['id_direccion']; ?>">
                                <?php echo htmlspecialchars($d['direccion_linea1'] . ' (' . $d['distrito'] . ', ' . $d['ciudad'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select name="metodo_pago" id="metodo_pago" class="form-control">
                        <option value="TARJETA">Tarjeta de Débito / Crédito</option>
                        <option value="YAPE">Yape</option>
                        <option value="PLIN">Plin</option>
                        <option value="TRANSFERENCIA">Transferencia Bancaria</option>
                    </select>
                </div>

                <div style="border-top: 1px solid #cbd5e1; padding-top: 15px; margin-top: 15px;">
                    <p style="font-size: 14px; margin-bottom: 5px;"><strong>Subtotal:</strong> S/ <?php echo number_format($gran_total, 2); ?></p>
                    <p style="font-size: 14px; margin-bottom: 10px;"><strong>Envío:</strong> S/ 0.00 (Gratis)</p>
                    <h2 style="color: #0f172a; margin-bottom: 15px;">Total: S/ <?php echo number_format($gran_total, 2); ?></h2>

                    <button type="submit" class="btn btn-success" style="width: 100%; font-size: 15px; padding: 12px;">
                        Confirmar y Procesar Compra Segura
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>