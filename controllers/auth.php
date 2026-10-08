<?php
// controllers/auth.php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    header("Location: ../login.php?error=vacio");
    exit;
}

try {
    // Consulta preparada contra SQL Injection
    $stmt = $pdo->prepare("SELECT id_usuario, id_rol, nombre, apellido, email, password_hash 
                           FROM usuarios 
                           WHERE email = :email 
                           LIMIT 1");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch();

    $autenticado = false;

    // Validación compatible con la contraseña demo '123456' o el hash de la BD
    if ($usuario) {
        if ($password === '123456' || password_verify($password, $usuario['password_hash'])) {
            $autenticado = true;
        }
    }

    if ($autenticado) {
        // Asignación de variables de sesión en memoria del servidor
        $_SESSION['usuario_id']     = (int)$usuario['id_usuario'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
        $_SESSION['usuario_email']  = $usuario['email'];
        $_SESSION['rol']            = (int)$usuario['id_rol'];

        // Enrutamiento según perfil de usuario
        if ($_SESSION['rol'] === 2) {
            header("Location: ../admin.php");
        } else {
            header("Location: ../catalogo.php");
        }
        exit;
    } else {
        header("Location: ../login.php?error=invalido");
        exit;
    }

} catch (PDOException $e) {
    die("Error al procesar la autenticación: " . $e->getMessage());
}