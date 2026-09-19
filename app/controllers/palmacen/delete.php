<?php

include ('../../config.php');

$id_producto = $_POST['id_producto'];

$sentencia = $pdo->prepare("DELETE FROM tbp_almacen WHERE id_producto=:id_producto ");

$sentencia->bindParam('id_producto',$id_producto);
if($sentencia->execute()){
    session_start();
    $_SESSION['mensaje'] = "Se elimino el producto de la manera correcta";
    $_SESSION['icono'] = "success";
    header('Location: '.$URL.'/palmacen/');
}else{
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo eliminar el registro en la base de datos";
    $_SESSION['icono'] = "error";
    header('Location: '.$URL.'/palmacen/delete.php?id='.$id_producto);
}