<?php
include ('../../config.php');
session_start();

// Paso 1: Borrar carrito actual asociado al nro_venta
if (isset($_SESSION['nro_venta_actual'])) {
    $sentencia = $pdo->prepare("DELETE FROM tb_carrito WHERE nro_venta = :nro_venta");
    $sentencia->bindParam(':nro_venta', $_SESSION['nro_venta_actual']);
    $sentencia->execute();
}

// Paso 2: Calcular siguiente nro_venta en base a las ventas registradas
$sql_max_venta = "SELECT MAX(nro_venta) as max_venta FROM tb_venta";
$query_max = $pdo->prepare($sql_max_venta);
$query_max->execute();
$venta = $query_max->fetch(PDO::FETCH_ASSOC);

// 🔧 CAMBIO: asegurar correlativo siempre
$nro_venta_siguiente = ($venta && $venta['max_venta']) ? $venta['max_venta'] + 1 : 1;

// Guardar nuevo nro en sesión
$_SESSION['nro_venta_actual'] = $nro_venta_siguiente;

// Resetear flags de sesión
unset($_SESSION['carrito_activo'], $_SESSION['index_iniciado']);

// Redirección
header("Location: $URL/cotizacion/create.php");
exit();
