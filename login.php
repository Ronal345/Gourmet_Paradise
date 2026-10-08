<?php
// login.php
session_start();

// Si ya existe una sesión activa, redirigir según el rol asignado
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 2) {
        header("Location: admin.php");
    } else {
        header("Location: catalogo.php");
    }
    exit;
}

// Cargar la Capa de Presentación
require_once __DIR__ . '/views/login_vista.php';