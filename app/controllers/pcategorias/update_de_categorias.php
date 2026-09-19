<?php

include('../../config.php');

$nombre_categoria = $_GET['nombre_categoria'];
$marca = $_GET['marca'];
$id_categoria = $_GET['id_categoria'];

$sentencia = $pdo->prepare("UPDATE tbp_categoria
    SET nombre_categoria = :nombre_categoria, 
        marca = :marca, 
        fyh_actualizacion = :fyh_actualizacion 
    WHERE id_categoria = :id_categoria");

$sentencia->bindParam(':nombre_categoria', $nombre_categoria);
$sentencia->bindParam(':marca', $marca);
$sentencia->bindParam(':fyh_actualizacion', $fechaHora);
$sentencia->bindParam(':id_categoria', $id_categoria);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = "Se actualizó la categoria de la manera correcta";
    $_SESSION['icono'] = "success";
    ?>
    <script>
        location.href = "<?= $URL; ?>/pcategorias";
    </script>
    <?php
} else {
    session_start();
    $_SESSION['mensaje'] = "Error, no se pudo actualizar en la base de datos";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?= $URL; ?>/pcategorias";
    </script>
    <?php
}
?>


