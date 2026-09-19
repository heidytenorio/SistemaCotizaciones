<?php
// No mostrar errores en pantalla
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Incluir config (BD, URL, etc.)
include(__DIR__ . '/../app/config.php');

// ------------------------------------------
// 1. Validar sesión
// ------------------------------------------
if (!isset($_SESSION['sesion_email'])) {
    header("Location: " . $URL . "/login/index.php");
    exit();
}

$email_sesion = $_SESSION['sesion_email'];

// ------------------------------------------
// 2. Consultar datos del usuario
// ------------------------------------------
$sql = "SELECT 
            us.id_usuario,
            us.nombres,
            us.email,
            rol.rol
        FROM tb_usuarios AS us
        INNER JOIN tb_roles AS rol ON us.id_rol = rol.id_rol
        WHERE us.email = :email
        LIMIT 1";

$query = $pdo->prepare($sql);
$query->bindParam(':email', $email_sesion, PDO::PARAM_STR);
$query->execute();
$usuario = $query->fetch(PDO::FETCH_ASSOC);

// ------------------------------------------
// 3. Validación de usuario
// ------------------------------------------
if (!$usuario) {
    session_destroy();
    header("Location: " . $URL . "/login/index.php");
    exit();
}

// ------------------------------------------
// 4. Registrar variables en la sesión
// ------------------------------------------
$_SESSION['id_usuario_sesion'] = $usuario['id_usuario'];
$_SESSION['nombres_sesion']    = $usuario['nombres'];
$_SESSION['rol_sesion']        = $usuario['rol'];
$_SESSION['URL']               = $URL;

// ------------------------------------------
// 5. Variables locales (opcionales)
// ------------------------------------------
$id_usuario_sesion = $usuario['id_usuario'];
$nombres_sesion    = $usuario['nombres'];
$rol_sesion        = $usuario['rol'];

?>
