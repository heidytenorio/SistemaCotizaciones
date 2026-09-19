<?php
$sql_lote = "SELECT * FROM tb_lote";

$query_lote = $pdo->prepare($sql_lote);
$query_lote->execute();
$lote_datos = $query_lote->fetchAll(PDO::FETCH_ASSOC);
