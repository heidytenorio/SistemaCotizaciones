<?php
// Conexión con la base de datos
include(__DIR__ . '/../../config.php');

try {
    $sql_pproductos = "
       SELECT
    i.id_producto,
    i.codigo,
    i.descripcion,
    i.imagen,
    i.tipo_liquido,
    i.factor_conversion,
    i.id_proveedor,
    i.id_categoria,
    p.razon_social AS proveedor,
    c.nombre_categoria AS categoria
FROM tb_inventario i
INNER JOIN tb_proveedor p ON p.id_proveedor = i.id_proveedor
INNER JOIN tb_categoria c ON c.id_categoria = i.id_categoria
ORDER BY i.codigo
    ";

    $query_pproductos = $pdo->prepare($sql_pproductos);
    $query_pproductos->execute();
    $pproductos_datos = $query_pproductos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "❌ Error al obtener productos: " . $e->getMessage();
    $pproductos_datos = [];
}
