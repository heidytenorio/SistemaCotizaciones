<?php

include ('../../config.php');

$id_pedido = $_POST['id_pedido'];

$sentencia = $pdo->prepare("DELETE FROM tb_pedido WHERE id_pedido=:id_pedido");

$sentencia->bindParam('id_pedido',$id_pedido);
if($sentencia->execute()){
    session_start();
    $_SESSION['mensaje'] = "Se elimino el pedido de la manera correcta";
    $_SESSION['icono'] = "success";
    header('Location: '.$URL.'/pedidos/');
}else{
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo eliminar el registro en la base de datos";
    $_SESSION['icono'] = "error";
    header('Location: '.$URL.'/pedidos/delete.php?id='.$id_pedido);
}