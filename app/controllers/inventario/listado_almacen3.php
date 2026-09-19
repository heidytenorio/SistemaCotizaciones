<?php

include(__DIR__ . '/../../config.php');

$datos = [];

try {

    $sql = "

SELECT

i.imagen,

i.codigo,

i.descripcion,


/* VARIANTE */
IFNULL(
CONCAT(v.color,' ',IFNULL(v.material,'')),
'SIN VARIANTE'
) AS variante,


/* PROVEEDOR */
IFNULL(p.razon_social,'SIN PROVEEDOR') AS proveedor,


/* STOCK TOTAL */
IFNULL((
SELECT SUM(stock_actual)
FROM tb_stock_almacen sa2
WHERE sa2.id_variante = v.id_variante
),0) AS stock_actual,


/* COSTO PROMEDIO */
IFNULL((
SELECT ROUND(SUM(ed.cantidad*ed.costo_unitario)
/ SUM(ed.cantidad),2)
FROM tb_entrada_d ed
WHERE ed.id_variante = v.id_variante
),0) AS costo_promedio,


/* ULTIMO COSTO */
IFNULL((
SELECT ed.costo_unitario
FROM tb_entrada_d ed
INNER JOIN tb_entrada e
ON e.id_entrada = ed.id_entrada
WHERE ed.id_variante = v.id_variante
ORDER BY e.fecha_ingreso DESC, ed.id_entrada_detalle DESC
LIMIT 1
),0) AS ultimo_costo,


/* FECHA ULTIMA COMPRA */
(
SELECT e.fecha_ingreso
FROM tb_entrada_d ed
INNER JOIN tb_entrada e
ON e.id_entrada = ed.id_entrada
WHERE ed.id_variante = v.id_variante
ORDER BY e.fecha_ingreso DESC
LIMIT 1
) AS fecha_ultima_compra,


/* INVERSION */
ROUND(

IFNULL((
SELECT SUM(stock_actual)
FROM tb_stock_almacen sa3
WHERE sa3.id_variante = v.id_variante
),0)

*

IFNULL((
SELECT ed.costo_unitario
FROM tb_entrada_d ed
INNER JOIN tb_entrada e
ON e.id_entrada = ed.id_entrada
WHERE ed.id_variante = v.id_variante
ORDER BY e.fecha_ingreso DESC
LIMIT 1
),0)

,2) AS inversion_actual



FROM tb_inventario i

LEFT JOIN tb_variantes v
ON v.id_producto = i.id_producto

LEFT JOIN tb_proveedor p
ON p.id_proveedor = i.id_proveedor


ORDER BY i.codigo ASC

";

    $query = $pdo->prepare($sql);

    $query->execute();

    $datos = $query->fetchAll(PDO::FETCH_ASSOC);



} catch (PDOException $e) {

    echo $e->getMessage();

}
?>