<?php
include ('../../config.php');
session_start(); // 🔑 Necesario para usar la sesión

// 🔧 El nro_orden SIEMPRE se obtiene de la sesión
$nro_orden = $_SESSION['nro_oferta_actual'] ?? null;

// 🔹 Parámetros del producto recibidos por GET
$id_producto  = $_GET['id_producto']  ?? null;
$cantidad     = $_GET['cantidad']     ?? null;
$medida       = $_GET['medida']       ?? null;
$precio_final = $_GET['precio_final'] ?? null;

if ($nro_orden && $id_producto && $cantidad && $precio_final && $medida) {
    try {
        // ✅ Insertar producto en el carrito (tb_xcarrito)
        $sentencia = $pdo->prepare("INSERT INTO tb_xcarrito (nro_orden, id_producto, cantidad, medida, precio_final)
                                    VALUES (:nro_orden, :id_producto, :cantidad, :medida, :precio_final)");

        $sentencia->bindParam(':nro_orden', $nro_orden, PDO::PARAM_INT);
        $sentencia->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $sentencia->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $sentencia->bindParam(':medida', $medida);
        $sentencia->bindParam(':precio_final', $precio_final);

        if ($sentencia->execute()) {
            echo "OK"; // Respuesta esperada por AJAX
        } else {
            echo "❌ Error al insertar en el carrito";
        }
    } catch (Exception $e) {
        echo "⚠️ Error: " . $e->getMessage();
    }
} else {
    echo "❌ Faltan datos para registrar en el carrito";
}
?>
