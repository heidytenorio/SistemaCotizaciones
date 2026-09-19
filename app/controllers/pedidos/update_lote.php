<?php
include('../../config.php');

if (!isset($_GET['ajax'])) {
    die("Acceso no válido");
}

$id_lote          = $_GET['id_lote'];
$nombre_categoria = $_GET['nombre_categoria'];
$medida           = $_GET['medida'];
$nombre_marca     = $_GET['nombre_marca'];
$f_fabri          = $_GET['f_fabri'];
$f_venci          = $_GET['f_venci'];
$lote             = $_GET['lote'];
$uni              = $_GET['uni'];
$entidad          = $_GET['entidad'];
$descripcion         = $_GET['descripcion'];

$sentencia = $pdo->prepare("
    UPDATE tb_lote SET
        nombre_categoria = :nombre_categoria,
        medida = :medida,
        nombre_marca = :nombre_marca,
        f_fabri = :f_fabri,
        f_venci = :f_venci,
        lote = :lote,
        uni = :uni,
        entidad = :entidad,
        descripcion = :descripcion
    WHERE id_lote = :id_lote
");

$sentencia->bindParam(':nombre_categoria', $nombre_categoria);
$sentencia->bindParam(':medida', $medida);
$sentencia->bindParam(':nombre_marca', $nombre_marca);
$sentencia->bindParam(':f_fabri', $f_fabri);
$sentencia->bindParam(':f_venci', $f_venci);
$sentencia->bindParam(':lote', $lote);
$sentencia->bindParam(':uni', $uni);
$sentencia->bindParam(':entidad', $entidad);
$sentencia->bindParam(':descripcion', $descripcion);
$sentencia->bindParam(':id_lote', $id_lote);

if ($sentencia->execute()) {
    echo "success";
} else {
    $error = $sentencia->errorInfo();
    echo "error: " . $error[2];
}
