<?php
include('../../../app/config.php');

$id_producto = $_GET['id_producto'];

$sql = "DELETE FROM tb_inventario WHERE id_producto = :id_producto";
$query = $pdo->prepare($sql);
$query->bindParam(':id_producto', $id_producto);
$query->execute();

header('Location: ../../../inventario/produc.php');
