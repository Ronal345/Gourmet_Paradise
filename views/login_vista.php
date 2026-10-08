<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Gourmet Paradise</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="formulario-login">
    <div style="text-align: center; margin-bottom: 20px;">
        <h1>Gourmet Paradise</h1>
        <p class="subtitulo">Ingreso al Sistema (Demostración 3 Capas)</p>
    </div>

    <!-- Alertas dinámicas de estado -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alerta alerta-error">
            <?php 
                $err = $_GET['error'];
                if ($err === 'vacio') {
                    echo 'Debe ingresar su correo y contraseña.';
                } elseif ($err === 'invalido') {
                    echo 'Credenciales incorrectas o usuario no registrado.';
                } elseif ($err === 'no_autorizado') {
                    echo 'Acceso denegado: No cuenta con permisos para esa sección.';
                } else {
                    echo 'Error al procesar el ingreso.';
                }
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logout'): ?>
        <div class="alerta alerta-exito">
            Sesión cerrada correctamente.
        </div>
    <?php endif; ?>

    <!-- Formulario hacia la Capa de Aplicación -->
    <form action="controllers/auth.php" method="POST">
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" class="form-control" list="cuentas_demo" required placeholder="correo@ejemplo.com">
            <datalist id="cuentas_demo">
                <option value="admin.toro@gourmetparadise.pe">Administrador (Ronal Toro)</option>
                <option value="cmendoza@gmail.com">Cliente (Carla Mendoza)</option>
                <option value="mateo.b@outlook.com">Cliente (Mateo Benavides)</option>
            </datalist>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Ingresar a la Plataforma</button>
    </form>

    <div style="margin-top: 20px; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 12px;">
        <strong>Cuentas disponibles en la Base de Datos:</strong><br>
        • Admin: <code>admin.toro@gourmetparadise.pe</code><br>
        • Cliente: <code>cmendoza@gmail.com</code><br>
        • Contraseña de demostración: <code>123456</code>
    </div>
</div>

</body>
</html>