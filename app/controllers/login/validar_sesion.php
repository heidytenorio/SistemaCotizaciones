<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ===============================
// VALIDAR SESIÓN E INACTIVIDAD
// ===============================

// Iniciar sesión solo si no existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar config SIEMPRE aquí
require_once __DIR__ . '/../../config.php';

// ⚠️ EVITAR LOOP EN LOGIN
if (strpos($_SERVER['REQUEST_URI'], '/login') !== false) {
    return;
}

// ❌ No hay sesión
if (!isset($_SESSION['sesion_email'])) {
    header("Location: {$URL}/login");
    exit();
}

// ⏱️ Validar inactividad
if (isset($_SESSION['ultima_actividad'])) {

    $tiempo_inactivo = time() - $_SESSION['ultima_actividad'];

    if ($tiempo_inactivo > TIEMPO_INACTIVIDAD) {

        session_unset();
        session_destroy();

        header("Location: {$URL}/login?mensaje=Sesion_expirada");
        exit();
    }
}

// ✅ Actualizar tiempo
$_SESSION['ultima_actividad'] = time();
