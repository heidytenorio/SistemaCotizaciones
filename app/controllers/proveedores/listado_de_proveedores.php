<?php
$sql_proveedor="SELECT * FROM tb_proveedor ";
$query_proveedor= $pdo->prepare($sql_proveedor);
$query_proveedor->execute();
$proveedor_datos=$query_proveedor->fetchAll(PDO::FETCH_ASSOC);
