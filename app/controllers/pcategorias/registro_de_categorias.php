<?php

include('../../config.php');

$nombre_categoria = $_GET['nombre_categoria'];
$marca=$_GET['marca'];

$sentencia = $pdo->prepare("INSERT INTO tbp_categoria 
  (nombre_categoria,marca,fyh_creacion)
  VALUES (:nombre_categoria,:marca,:fyh_creacion)");

$sentencia->bindParam(':nombre_categoria', $nombre_categoria);
$sentencia->bindParam(':marca', $marca);
$sentencia->bindParam(':fyh_creacion', $fechaHora);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = "Se registró la categoría de la manera correcta";
    $_SESSION['icono'] = "success";
    //header("Location: " . $URL . "/categorias/");
    ?>
    <script>
        location.href="<?php echo $URL;?>/pcategorias";
    </script>
    <?php

} else {
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo registrar en la base de datos";
    $_SESSION['icono'] = "error";
    //header("Location: " . $URL . "/pcategorias/");
}


