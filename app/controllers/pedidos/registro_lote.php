<?php
include('../../config.php');

if (!isset($_GET['ajax'])) {
    die("Acceso no válido");
}

$nombre_categoria = $_GET['nombre_categoria'];
$medida = $_GET['medida'];
$nombre_marca = $_GET['nombre_marca'];
$f_fabri = $_GET['f_fabri'];
$f_venci = $_GET['f_venci'];
$lote = $_GET['lote'];
$uni = $_GET['uni'];
$entidad = $_GET['entidad'];
$descripcion = $_GET['descripcion'];


$sentencia = $pdo->prepare("INSERT INTO tb_lote
(nombre_categoria, medida, nombre_marca, f_fabri, f_venci, lote, uni, entidad, descripcion)
VALUES (:nombre_categoria, :medida, :nombre_marca, :f_fabri, :f_venci, :lote, :uni, :entidad,:descripcion)");

$sentencia->bindParam(':nombre_categoria', $nombre_categoria);
$sentencia->bindParam(':medida', $medida);
$sentencia->bindParam(':nombre_marca', $nombre_marca);
$sentencia->bindParam(':f_fabri', $f_fabri);
$sentencia->bindParam(':f_venci', $f_venci);
$sentencia->bindParam(':lote', $lote);
$sentencia->bindParam(':uni', $uni);
$sentencia->bindParam(':entidad', $entidad);
$sentencia->bindParam(':descripcion', $descripcion);

if ($sentencia->execute()) {
    echo "success";
} else {
    $error = $sentencia->errorInfo();
    echo "error: " . $error[2];
}
