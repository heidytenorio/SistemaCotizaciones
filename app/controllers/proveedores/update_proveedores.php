<?php

include('../../config.php');


$id_proveedor = $_GET['id_proveedor'];
$ruc = $_GET['ruc'];
$razon_social = $_GET['razon_social'];
$contacto = $_GET['contacto'];
$marca = $_GET['marca'];

$sentencia = $pdo->prepare("UPDATE tb_proveedor
    SET ruc = :ruc, 
        razon_social = :razon_social, 
        contacto = :contacto,
        marca = :marca
    WHERE id_proveedor = :id_proveedor");

$sentencia->bindParam(':ruc', $ruc);
$sentencia->bindParam(':razon_social', $razon_social);
$sentencia->bindParam(':contacto', $contacto);
$sentencia->bindParam(':marca', $marca);
$sentencia->bindParam(':id_proveedor', $id_proveedor);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = "Se actualizó la Proveedores de la manera correcta";
    $_SESSION['icono'] = "success";
    ?>
    <script>
        location.href = "<?= $URL; ?>/proveedores";
    </script>
    <?php
} else {
    session_start();
    $_SESSION['mensaje'] = "Error, no se pudo actualizar en la base de datos";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?= $URL; ?>/proveedores";
    </script>
    <?php
}
?>



