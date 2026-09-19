<?php
$sql = "SELECT
            v.id_variante,
            i.codigo,
            i.descripcion,
            p.razon_social AS proveedor,
            c.nombre_categoria AS categoria,
            v.color,
            v.material,
            v.stock_actual,
            v.costo_unitario
        FROM tb_variantes v
        INNER JOIN tb_inventario i ON v.id_producto = i.id_producto
        LEFT JOIN tb_proveedor p ON i.id_proveedor = p.id_proveedor
        LEFT JOIN tb_categoria c ON i.id_categoria = c.id_categoria
        ORDER BY i.descripcion, v.color, v.material";

$query = $pdo->prepare($sql);
$query->execute();
$inventario_datos = $query->fetchAll(PDO::FETCH_ASSOC);
