<?php
$sql_pedidos = "
SELECT * 
FROM tb_pedido
ORDER BY 
    CASE 
        WHEN estado = 'Subido' THEN 1
        WHEN estado = 'En espera' THEN 2
        WHEN estado = 'Preparado' THEN 3
        WHEN estado = 'Sale de lima' THEN 4
        WHEN estado = 'Despachado' THEN 5
        ELSE 6
    END ASC,
    id_pedido DESC
";

$query_pedidos = $pdo->prepare($sql_pedidos);
$query_pedidos->execute();
$pedidos_datos = $query_pedidos->fetchAll(PDO::FETCH_ASSOC);

