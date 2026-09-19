<?php
include('../../config.php');
session_start();

// Paso 1: Borrar carrito actual asociado al nro_oferta
if (isset($_SESSION['nro_oferta_actual'])) {
    $sentencia = $pdo->prepare("DELETE FROM tb_xcarrito WHERE nro_orden = :nro_orden");
    $sentencia->bindParam(':nro_orden', $_SESSION['nro_oferta_actual'], PDO::PARAM_INT);
    $sentencia->execute();
}

// Paso 2: Calcular siguiente nro_venta en base a las ventas registradas
$sql_max_oferta = "SELECT MAX(nro_orden) AS max_oferta FROM tb_oferta";
$query_max = $pdo->prepare($sql_max_oferta);
$query_max->execute();
$oferta = $query_max->fetch(PDO::FETCH_ASSOC);

// 🔧 CAMBIO: asegurar correlativo siempre
$nro_oferta_siguiente = ($oferta && $oferta['max_oferta']) ? $oferta['max_oferta'] + 1 : 1;

// Guardar nuevo nro en sesión
$_SESSION['nro_oferta_actual'] = $nro_oferta_siguiente;

// Resetear flags de sesión
unset($_SESSION['carrito_activo'], $_SESSION['index_iniciado']);

// Redirección
header("Location: {$URL}/proforma/create.php");
exit();
?>

