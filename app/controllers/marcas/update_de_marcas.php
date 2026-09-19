<?php

include('../../config.php');

$nombre = $_GET['nombre'];
$nro_contacto = $_GET['nro_contacto'];
$id_marca = $_GET['id_marca'];

$sentencia = $pdo->prepare("UPDATE tb_marca
    SET nombre= :nombre, 
        nro_contacto = :nro_contacto
    WHERE id_marca = :id_marca");

$sentencia->bindParam(':nombre', $nombre);
$sentencia->bindParam(':nro_contacto', $nro_contacto);
$sentencia->bindParam(':id_marca', $id_marca);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = "Se actualizó la marca de la manera correcta";
    $_SESSION['icono'] = "success";
    ?>
    <script>
        location.href = "<?= $URL; ?>/marcas";
    </script>
    <?php
} else {
    session_start();
    $_SESSION['mensaje'] = "Error, no se pudo actualizar en la base de datos";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?= $URL; ?>/marcas";
    </script>
    <?php
}
?>


