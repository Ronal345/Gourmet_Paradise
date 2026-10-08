<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Gourmet — Gourmet Paradise</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="contenedor">
    <header>
        <div>
            <h1>Gourmet Paradise</h1>
            <p class="subtitulo">Sesión de Cliente: <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></p>
        </div>
        <div class="nav-links">
            <?php 
                $total_items = isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; 
            ?>
            <a href="carrito.php" class="btn btn-primary">
                🛒 Ver Carrito (<?php echo $total_items; ?>)
            </a>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </header>

    <!-- Notificaciones de estado -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'agregado'): ?>
        <div class="alerta alerta-exito">
            Producto añadido al carrito correctamente.
        </div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'compra_exitosa'): ?>
        <div class="alerta alerta-exito">
            ¡Compra procesada con éxito! La transacción se guardó y el inventario se actualizó en la base de datos.
        </div>
    <?php endif; ?>

    <!-- Cuadrícula de productos -->
    <div class="grid-catalogo">
        <?php if (empty($productos)): ?>
            <p style="color: #64748b;">No hay productos con existencias disponibles en este momento.</p>
        <?php else: ?>
            <?php foreach ($productos as $p): ?>
                <div class="card-producto">
                    <div>
                        <span class="card-categoria"><?php echo htmlspecialchars($p['categoria']); ?></span>
                        <h2 class="card-titulo"><?php echo htmlspecialchars($p['producto']); ?></h2>
                        <div class="card-presentacion"><?php echo htmlspecialchars($p['presentacion']); ?></div>

                        <?php if (!empty($p['notas_de_cata'])): ?>
                            <div class="card-notas">
                                <strong>Cata:</strong> <?php echo htmlspecialchars($p['notas_de_cata']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="card-precio">S/ <?php echo number_format($p['precio_regular'], 2); ?></div>
                        <div style="margin-bottom: 12px;">
                            <span class="stock-tag">Stock: <?php echo $p['stock']; ?> unids.</span>
                        </div>
                    </div>

                    <!-- Formulario puro para agregar al carrito sin JavaScript -->
                    <form action="controllers/carrito.php" method="POST" style="display: flex; gap: 8px;">
                        <input type="hidden" name="accion" value="agregar">
                        <input type="hidden" name="id_variante" value="<?php echo $p['id_variante']; ?>">
                        <input type="number" name="cantidad" value="1" min="1" max="<?php echo $p['stock']; ?>" 
                               class="form-control" style="width: 70px; text-align: center;">
                        <button type="submit" class="btn btn-success" style="flex: 1;">+ Añadir</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>