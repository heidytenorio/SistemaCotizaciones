<?php
$sql_productos = "
    SELECT 
    a.id_producto,
    a.imagen,
    a.codigo,
    a.descripcion,
    a.costo_mayorista,
    a.porcentaje_mayorista,
    a.precio_mayorista,
    m.nombre AS nombre_marca,
    p.razon_social AS nombre_proveedor,
    c.nombre_categoria AS nombre_categoria
FROM tb_almacen a
LEFT JOIN tb_marca m ON a.id_marca = m.id_marca
LEFT JOIN tb_proveedor p ON a.id_proveedor = p.id_proveedor
LEFT JOIN tb_categoria c ON a.id_categoria = c.id_categoria

";

    $query_productos = $pdo->prepare($sql_productos);
    $query_productos->execute();
    $productos_datos = $query_productos->fetchAll(PDO::FETCH_ASSOC);
