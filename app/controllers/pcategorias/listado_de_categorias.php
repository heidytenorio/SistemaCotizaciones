<?php

$sql_pcategorias="SELECT * FROM tbp_categoria";
$query_pcategorias= $pdo->prepare($sql_pcategorias);
$query_pcategorias->execute();
$pcategorias_datos=$query_pcategorias->fetchAll(PDO::FETCH_ASSOC);