<?php
include(__DIR__ . '/../../config.php');

try {

    $sql = "
SELECT
    v.id_variante,
    v.id_producto,

    i.descripcion,
    i.imagen,

    pr.razon_social AS proveedor,
    ca.nombre_categoria AS categoria,

    v.color,
    v.unidad_control,

    sa.id_almacen,

    /* =========================
       STOCK POR ALMACEN
    ========================= */
    CASE
        WHEN v.unidad_control = 'GRANEL' THEN
            COALESCE(mov.stock,0)
        ELSE
            COALESCE(sa.stock_actual,0)
    END AS stock

FROM tb_variantes v

INNER JOIN tb_inventario i
    ON i.id_producto = v.id_producto

INNER JOIN tb_proveedor pr
    ON pr.id_proveedor = i.id_proveedor

INNER JOIN tb_categoria ca
    ON ca.id_categoria = i.id_categoria

/* =========================
   STOCK NORMAL POR ALMACEN
========================= */
LEFT JOIN (
    SELECT id_variante, id_almacen, SUM(stock_actual) stock_actual
    FROM tb_stock_almacen
    GROUP BY id_variante, id_almacen
) sa ON sa.id_variante = v.id_variante

/* =========================
   MOVIMIENTOS GRANEL POR ALMACEN
========================= */
LEFT JOIN (
    SELECT id_variante, id_almacen, SUM(movimiento) stock
    FROM (

        SELECT id_variante, cantidad_real AS movimiento, id_almacen
        FROM tb_entrada_d

        UNION ALL

        SELECT id_variante, -cantidad_real AS movimiento, id_almacen
        FROM tb_salida_d

    ) m
    GROUP BY id_variante, id_almacen
) mov ON mov.id_variante = v.id_variante
    AND mov.id_almacen = sa.id_almacen

WHERE
    i.descripcion IS NOT NULL
    AND TRIM(i.descripcion) <> ''
    AND v.color IS NOT NULL
    AND TRIM(v.color) <> ''
  
AND (
    CASE
        WHEN v.unidad_control = 'GRANEL' THEN
            COALESCE(mov.stock,0)
        ELSE
            COALESCE(sa.stock_actual,0)
    END <> 0
)

ORDER BY
    i.descripcion ASC,
    v.color ASC;
";

    $query = $pdo->prepare($sql);
    $query->execute();

    $pproductos_datos = $query->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    echo '❌ Error: ' . $e->getMessage();
    $pproductos_datos = [];

}