<?php
include('../../config.php');


// Recibir variables
$id_producto = $_POST['id_producto'];
$image_text = $_POST['image_text'];
$codigo = $_POST['codigo'];
$descripcion = $_POST['descripcion'];
$id_marca = $_POST['id_marca'];
$id_proveedor = $_POST['id_proveedor'];
$id_usuario = $_POST['id_usuario'];
$id_categoria = $_POST['id_categoria'];
$costo_mayorista = $_POST['costo_mayorista'] ;
$porcentaje_mayorista = $_POST['porcentaje_mayorista'];
$precio_mayorista = $_POST['precio_mayorista'];
$info = $_POST['info'];





if($_FILES['image']['name'] != null){
    //echo "hay imagen nueva";
    $nombreDelArchivo = date("Y-m-d-h-i-s");
    $image_text = $nombreDelArchivo."__".$_FILES['image']['name'];
    $location = "../../../almacen/img_pproductos/".$image_text;
    move_uploaded_file($_FILES['image']['tmp_name'],$location);
}else{
    // echo "no hay imagen";
}

// Sentencia preparada
$sentencia = $pdo->prepare("UPDATE tb_almacen 
SET
    imagen = :imagen,
    codigo = :codigo,
    descripcion = :descripcion,
    id_marca = :id_marca,
    id_proveedor = :id_proveedor,
    id_usuario = :id_usuario,
    id_categoria = :id_categoria,
    costo_mayorista = :costo_mayorista,
    porcentaje_mayorista = :porcentaje_mayorista,
    precio_mayorista = :precio_mayorista,
    info = :info
 WHERE id_producto = :id_producto ");


$sentencia->bindParam('imagen', $image_text);
$sentencia->bindParam('codigo', $codigo);
$sentencia->bindParam('descripcion', $descripcion);
$sentencia->bindParam('id_marca', $id_marca);
$sentencia->bindParam('id_categoria', $id_categoria);
$sentencia->bindParam('id_proveedor', $id_proveedor);
$sentencia->bindParam('id_usuario', $id_usuario);
$sentencia->bindParam('costo_mayorista', $costo_mayorista);
$sentencia->bindParam('porcentaje_mayorista', $porcentaje_mayorista);
$sentencia->bindParam('precio_mayorista', $precio_mayorista);
$sentencia->bindParam('id_producto', $id_producto);
$sentencia->bindParam('info', $info);

if($sentencia->execute()){
    session_start();
    $_SESSION['mensaje'] = "Se actualizo el producto de la manera correcta";
    $_SESSION['icono'] = "success";
    header('Location: '.$URL.'/almacen/');
}else{
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo actualizar en la base de datos";
    $_SESSION['icono'] = "error";
    header('Location: '.$URL.'/almacen/update.php?id='.$id_producto);
}
