<?php
$sql_entradas = "SELECT 
    e.fecha_ingreso,
    e.nro_factura,
    e.doc_entrada,
    i.descripcion,
    d.costo_unitario,
    SUM(d.cantidad) AS stock_total,
    i.id_proveedor,
    i.id_categoria
FROM tb_entrada e
INNER JOIN tb_entrada_d d ON e.id_entrada = d.id_entrada
INNER JOIN tb_variantes v ON d.id_variante = v.id_variante
INNER JOIN tb_inventario i ON v.id_producto = i.id_producto
GROUP BY 
    e.fecha_ingreso,
    e.nro_factura,
    e.doc_entrada,
    i.descripcion,
    d.costo_unitario,
    i.id_proveedor,
    i.id_categoria
ORDER BY e.fecha_ingreso DESC";

$query_entradas = $pdo->prepare($sql_entradas);
$query_entradas->execute();
$entradas = $query_entradas->fetchAll(PDO::FETCH_ASSOC);
