<?php

include('../../config.php');


$id_cliente = $_GET['id_cliente'];
$ruc = $_GET['ruc'];
$razon_social = $_GET['razon_social'];
$contacto1 = $_GET['contacto1'];
$contacto2 = $_GET['contacto2'];
$contacto3 = $_GET['contacto3'];
$direccion = $_GET['direccion'];

$sentencia = $pdo->prepare("UPDATE tb_clientes
    SET ruc = :ruc, 
        razon_social = :razon_social, 
        contacto1 = :contacto1,
        contacto2 = :contacto2,
        contacto3 = :contacto3,
        direccion = :direccion
    WHERE id_cliente= :id_cliente");

$sentencia->bindParam(':ruc', $ruc);
$sentencia->bindParam(':razon_social', $razon_social);
$sentencia->bindParam(':contacto1', $contacto1);
$sentencia->bindParam(':contacto2', $contacto2);
$sentencia->bindParam(':contacto3', $contacto3);
$sentencia->bindParam(':direccion', $direccion);
$sentencia->bindParam(':id_cliente', $id_cliente);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = "Se actualizó el cliente de la manera correcta";
    $_SESSION['icono'] = "success";
    ?>
    <script>
        location.href = "<?= $URL; ?>/clientes";
    </script>
    <?php
} else {
    session_start();
    $_SESSION['mensaje'] = "Error, no se pudo actualizar en la base de datos";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?= $URL; ?>/clientes";
    </script>
    <?php
}
?>
