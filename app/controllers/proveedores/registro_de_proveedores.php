<?php

include('../../config.php');

$ruc = $_GET['ruc'];
$razon_social = $_GET['razon_social'];
$contacto=$_GET['contacto'];
$marca=$_GET['marca'];

$sentencia = $pdo->prepare("INSERT INTO tb_proveedor
  (ruc,razon_social,contacto,marca)
  VALUES (:ruc,:razon_social,:contacto,:marca)");

$sentencia->bindParam(':ruc', $ruc);
$sentencia->bindParam(':razon_social', $razon_social);
$sentencia->bindParam(':contacto', $contacto);
$sentencia->bindParam(':marca', $marca);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = "Se registró el proveedor de la manera correcta";
    $_SESSION['icono'] = "success";
    //header("Location: " . $URL . "/categorias/");
    ?>
    <script>
        location.href="<?php echo $URL;?>/proveedores";
    </script>
    <?php

} else {
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo registrar en la base de datos";
    $_SESSION['icono'] = "error";

}