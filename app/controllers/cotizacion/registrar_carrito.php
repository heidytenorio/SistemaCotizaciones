<?php
include ('../../config.php');
session_start(); // 🔑 Necesario para usar la sesión

// 🔧 El nro_venta SIEMPRE se obtiene de la sesión
$nro_venta = $_SESSION['nro_venta_actual'] ?? null;

$id_producto  = $_GET['id_producto']  ?? null;
$cantidad     = $_GET['cantidad']     ?? null;
$precio_final = $_GET['precio_final'] ?? null;

if ($nro_venta && $id_producto && $cantidad && $precio_final) {
    try {
        $sentencia = $pdo->prepare("INSERT INTO tb_carrito (nro_venta, id_producto, cantidad, precio_final)
                                    VALUES (:nro_venta, :id_producto, :cantidad, :precio_final)");

        $sentencia->bindParam(':nro_venta', $nro_venta, PDO::PARAM_INT);
        $sentencia->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $sentencia->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $sentencia->bindParam(':precio_final', $precio_final);

        if ($sentencia->execute()) {
            echo "OK"; // Esto es lo que espera tu AJAX
        } else {
            echo "❌ Error al insertar en el carrito";
        }
    } catch (Exception $e) {
        echo "⚠️ Error: " . $e->getMessage();
    }
} else {
    echo "❌ Faltan datos para registrar en el carrito";
}


