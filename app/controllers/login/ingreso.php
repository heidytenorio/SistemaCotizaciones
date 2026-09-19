<?php
// ===============================
// ACTIVAR ERRORES (DEBUG CONTROLADO)
// ===============================
ini_set('display_errors', 0); // No mostrar en pantalla
ini_set('display_startup_errors', 0);
error_reporting(E_ALL); // Registrar errores en log

// INICIAR SESIÓN SIEMPRE PRIMERO
session_start();

// INCLUIR CONFIG
include('../../config.php');

// ===============================
// VALIDAR PETICIÓN
// ===============================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje'] = "Acceso no permitido.";
    header("Location: {$URL}/login");
    exit();
}

if (!isset($_POST['email']) || !isset($_POST['password_user'])) {
    $_SESSION['mensaje'] = "Complete todos los campos.";
    header("Location: {$URL}/login");
    exit();
}

$email = trim($_POST['email']);
$password_user = $_POST['password_user'];

error_log("Email recibido: $email");

// ===============================
// CONSULTA SQL
// ===============================
$sql = "SELECT u.*, r.rol
        FROM tb_usuarios u
        INNER JOIN tb_roles r ON u.id_rol = r.id_rol
        WHERE u.email = :email
        LIMIT 1";

try {
    $query = $pdo->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $usuario = $query->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("PDO ERROR: " . $e->getMessage());
    $_SESSION['mensaje'] = "Error interno.";
    header("Location: {$URL}/login");
    exit();
}

error_log("Resultado consulta: " . print_r($usuario, true));

// ===============================
// VALIDAR USUARIO
// ===============================
if (!$usuario) {
    $_SESSION['mensaje'] = "Datos incorrectos.";
    header("Location: {$URL}/login");
    exit();
}

if (!password_verify($password_user, $usuario['password_user'])) {
    error_log("password_verify: false");
    $_SESSION['mensaje'] = "Datos incorrectos.";
    header("Location: {$URL}/login");
    exit();
}

error_log("password_verify: true");
error_log("Ingreso exitoso usuario id: {$usuario['id_usuario']}");

// ===============================
// CREAR SESIÓN
// ===============================
$_SESSION['sesion_email'] = $usuario['email'];
$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['nombres_sesion'] = $usuario['nombres'];
$_SESSION['rol_sesion'] = $usuario['rol'];
$_SESSION['ultima_actividad'] = time();


// ❌ ESTA LÍNEA CAUSA EL ERROR (ELIMINADA)
// $_SESSION['URL'] = $URL;

// ===============================
// REDIRIGIR AL INDEX PRINCIPAL
// ===============================
header("Location: {$URL}/index.php");
exit();
