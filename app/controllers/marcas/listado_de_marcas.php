<?php

$sql_marcas="SELECT * FROM tb_marca";
$query_marcas= $pdo->prepare($sql_marcas);
$query_marcas->execute();
$marcas_datos=$query_marcas->fetchAll(PDO::FETCH_ASSOC);
