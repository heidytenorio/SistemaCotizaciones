<?php

include('../../config.php');

$nombre = $_GET['nombre'];
$nro_contacto=$_GET['nro_contacto'];

$sentencia = $pdo->prepare("INSERT INTO tbp_marca
  (nombre,nro_contacto)
  VALUES (:nombre,:nro_contacto)");

$sentencia->bindParam(':nombre', $nombre);
$sentencia->bindParam(':nro_contacto', $nro_contacto);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = "Se registró la marca de la manera correcta";
    $_SESSION['icono'] = "success";
    //header("Location: " . $URL . "/categorias/");
    ?>
    <script>
        location.href="<?php echo $URL;?>/pmarcas";
    </script>
    <?php

} else {
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo registrar en la base de datos";
    $_SESSION['icono'] = "error";
    //header("Location: " . $URL . "/categorias/");
}


