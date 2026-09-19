<?php
$sql_oferta = "SELECT nro_orden, fecha_emi, ruc, razon_social,moneda, sub_total, igv, precio_final 
FROM tb_oferta ORDER BY nro_orden DESC";
$query_oferta = $pdo->prepare($sql_oferta);
$query_oferta->execute();
$oferta_datos = $query_oferta->fetchAll(PDO::FETCH_ASSOC);
?>