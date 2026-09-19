<?php


include('../../config.php');

$image = $_POST['image'];
$codigo = $_POST['codigo'];
$descripcion = $_POST['descripcion'];
$id_marca = $_POST['id_marca'];
$id_proveedor = $_POST['id_proveedor'];
$id_categoria = $_POST['id_categoria'];
$costo_mayorista = $_POST['costo_mayorista'];
$porcentaje_mayorista = $_POST['porcentaje_mayorista'];
$precio_mayorista = $_POST['precio_mayorista'];
$info = $_POST['info'];


$nombreDelArchivo = date("Y-m-d-h-i-s");
$filename = $nombreDelArchivo . "__" . $_FILES['image']['name'];
$location = "../../../almacen/img_pproductos/" . $filename;

move_uploaded_file($_FILES['image']['tmp_name'], $location);


$sentencia = $pdo->prepare("INSERT INTO tb_almacen 
    (codigo, descripcion, imagen, id_marca, 
     id_categoria, id_proveedor,id_usuario, costo_mayorista, porcentaje_mayorista,
     precio_mayorista,info) VALUES (:codigo, :descripcion, 
                                :imagen,:id_marca, :id_categoria,
                               :id_proveedor,:id_usuario,
                               :costo_mayorista,
                               :porcentaje_mayorista,
                               :precio_mayorista,:info)");

$sentencia->bindParam('imagen', $filename);
$sentencia->bindParam('codigo', $codigo);
$sentencia->bindParam('descripcion', $descripcion);
$sentencia->bindParam('id_marca', $id_marca);
$sentencia->bindParam('id_categoria', $id_categoria);
$sentencia->bindParam('id_proveedor', $id_proveedor);
$sentencia->bindParam('id_usuario', $id_usuario);
$sentencia->bindParam('costo_mayorista', $costo_mayorista);
$sentencia->bindParam('porcentaje_mayorista', $porcentaje_mayorista);
$sentencia->bindParam('precio_mayorista', $precio_mayorista);
$sentencia->bindParam('info', $info);


if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = "Se registro el producto de la manera correcta";
    $_SESSION['icono'] = "success";
    header('Location: ' . $URL . '/almacen/');
} else {
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo registrar en la base de datos";
    $_SESSION['icono'] = "error";
    header('Location: ' . $URL . '/almacen/create.php');
}

