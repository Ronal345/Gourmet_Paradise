<?php
// Prevenir advertencias de VS Code inicializando variables si la vista se abre sola
$inventario      = $inventario ?? [];
$lista_productos = $lista_productos ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo — Gourmet Paradise</title>
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        .grid-admin {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 25px;
            margin-top: 20px;
        }
        @media (max-width: 900px) {
            .grid-admin { grid-template-columns: 1fr; }
        }
        .panel-crear {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            height: fit-content;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <header>
        <div>
            <h1>Panel Administrativo (CRUD)</h1>
            <p class="subtitulo">Sesión de Administrador: <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin'); ?></strong></p>
        </div>
        <div class="nav-links">
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </header>

    <!-- Alertas dinámicas -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alerta alerta-exito">
            <?php 
                $m = $_GET['msg'];
                if ($m === 'creado') echo 'Nueva variante registrada con éxito en la base de datos.';
                elseif ($m === 'actualizado') echo 'Precio y stock actualizados correctamente.';
                elseif ($m === 'eliminado') echo 'Variante eliminada exitosamente del inventario.';
            ?>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alerta alerta-error">
            <strong>Atención:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="grid-admin">
        <!-- 1. FORMULARIO CREATE: Nueva Variante -->
        <div class="panel-crear">
            <h3 style="margin-bottom: 15px; color: #0f172a;">Agregar Presentación</h3>
            <form action="controllers/admin_crud.php" method="POST">
                <input type="hidden" name="accion" value="crear">

                <div class="form-group">
                    <label for="id_producto">Producto Base:</label>
                    <select name="id_producto" id="id_producto" class="form-control" required>
                        <option value="">-- Seleccionar Producto --</option>
                        <?php foreach ($lista_productos as $lp): ?>
                            <option value="<?php echo $lp['id_producto']; ?>">
                                <?php echo htmlspecialchars($lp['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sku">SKU (Código único):</label>
                    <input type="text" id="sku" name="sku" class="form-control" placeholder="Ej: CAF-ESP-250G" required>
                </div>

                <div class="form-group">
                    <label for="presentacion">Presentación:</label>
                    <input type="text" id="presentacion" name="presentacion" class="form-control" placeholder="Ej: Bolsa Grano 250g" required>
                </div>

                <div class="form-group">
                    <label for="precio">Precio Regular (S/):</label>
                    <input type="number" step="0.50" id="precio" name="precio" class="form-control" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock Inicial:</label>
                    <input type="number" id="stock" name="stock" class="form-control" value="10" min="0" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">+ Guardar Variante</button>
            </form>
        </div>

        <!-- 2. TABLA READ / UPDATE / DELETE -->
        <div>
            <h3 style="margin-bottom: 15px; color: #0f172a;">Inventario de Variantes (<?php echo count($inventario); ?> registros)</h3>
            
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto / Categoría</th>
                            <th>SKU / Presentación</th>
                            <th>Precio (S/)</th>
                            <th>Stock</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inventario)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 25px; color: #64748b;">No hay variantes registradas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inventario as $row): 
                                $form_id = "form_edit_" . $row['id_variante'];
                            ?>
                                <tr>
                                    <!-- Formulario invisible vinculado por ID -->
                                    <td>
                                        <form action="controllers/admin_crud.php" method="POST" id="<?php echo $form_id; ?>">
                                            <input type="hidden" name="accion" value="editar">
                                            <input type="hidden" name="id_variante" value="<?php echo $row['id_variante']; ?>">
                                        </form>
                                        <?php echo $row['id_variante']; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['producto']); ?></strong><br>
                                        <small style="color: #64748b;"><?php echo htmlspecialchars($row['categoria']); ?></small>
                                    </td>
                                    <td>
                                        <code><?php echo htmlspecialchars($row['sku']); ?></code><br>
                                        <small><?php echo htmlspecialchars($row['presentacion']); ?></small>
                                    </td>
                                    <td>
                                        <input type="number" step="0.50" name="precio" value="<?php echo $row['precio_regular']; ?>" 
                                               form="<?php echo $form_id; ?>" class="input-inline" required>
                                    </td>
                                    <td>
                                        <input type="number" name="stock" value="<?php echo $row['stock']; ?>" 
                                               form="<?php echo $form_id; ?>" class="input-inline" min="0" required>
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        <button type="submit" form="<?php echo $form_id; ?>" class="btn btn-primary" style="padding: 5px 8px; font-size: 11px;">
                                            Actualizar
                                        </button>
                                        <a href="controllers/admin_crud.php?accion=eliminar&id_variante=<?php echo $row['id_variante']; ?>" 
                                           class="btn btn-danger" style="padding: 5px 8px; font-size: 11px;">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>