<?php

$sql_pmarcas="SELECT * FROM tbp_marca";
$query_pmarcas= $pdo->prepare($sql_pmarcas);
$query_pmarcas->execute();
$pmarcas_datos=$query_pmarcas->fetchAll(PDO::FETCH_ASSOC);

