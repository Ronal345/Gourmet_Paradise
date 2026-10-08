<?php
// controllers/carrito.php
session_start();

// Control de acceso: solo clientes autenticados
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 1) {
    header("Location: ../login.php?error=no_autorizado");
    exit;
}

// Inicializar el carrito en sesión si aún no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

// 1. Agregar productos o sumar cantidades
if ($accion === 'agregar') {
    $id_variante = (int)($_POST['id_variante'] ?? 0);
    $cantidad    = (int)($_POST['cantidad'] ?? 1);

    if ($id_variante > 0 && $cantidad > 0) {
        if (isset($_SESSION['carrito'][$id_variante])) {
            $_SESSION['carrito'][$id_variante] += $cantidad;
        } else {
            $_SESSION['carrito'][$id_variante] = $cantidad;
        }
    }
    header("Location: ../catalogo.php?msg=agregado");
    exit;
}

// 2. Quitar un producto específico
if ($accion === 'quitar') {
    $id_variante = (int)($_GET['id_variante'] ?? 0);
    if (isset($_SESSION['carrito'][$id_variante])) {
        unset($_SESSION['carrito'][$id_variante]);
    }
    header("Location: ../carrito.php");
    exit;
}

// 3. Vaciar todo el carrito
if ($accion === 'vaciar') {
    $_SESSION['carrito'] = [];
    header("Location: ../carrito.php");
    exit;
}

header("Location: ../catalogo.php");
exit;