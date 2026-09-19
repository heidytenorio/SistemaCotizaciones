<?php

$sql_ventas="SELECT v.nro_venta,v.fecha_emi,c.ruc,
       c.razon_social,v.sub_total, v.igv , v.precio_final,v.envio
FROM tb_venta v INNER JOIN tb_clientes c ON v.id_cliente = c.id_cliente WHERE v.tipo_marca = 'Principal' ORDER BY nro_venta DESC;";
$query_ventas= $pdo->prepare($sql_ventas);
$query_ventas->execute();
$ventas_datos=$query_ventas->fetchAll(PDO::FETCH_ASSOC);
