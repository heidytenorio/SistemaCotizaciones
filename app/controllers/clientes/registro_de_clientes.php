<?php

include('../../config.php');

$ruc = $_GET['ruc'];
$razon_social = $_GET['razon_social'];
$contacto1=$_GET['contacto1'];
$contacto2=$_GET['contacto2'];
$contacto3=$_GET['contacto3'];
$direccion=$_GET['direccion'];

$sentencia = $pdo->prepare("INSERT INTO tb_clientes
  (ruc,razon_social,contacto1,contacto2,contacto3,direccion)
  VALUES (:ruc,:razon_social,:contacto1,:contacto2,:contacto3,:direccion)");

$sentencia->bindParam(':ruc', $ruc);
$sentencia->bindParam(':razon_social', $razon_social);
$sentencia->bindParam(':contacto1', $contacto1);
$sentencia->bindParam(':contacto2', $contacto2);
$sentencia->bindParam(':contacto3', $contacto3);
$sentencia->bindParam(':direccion', $direccion);

if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = "Se registró el cliente de la manera correcta";
    $_SESSION['icono'] = "success";
    //header("Location: " . $URL . "/categorias/");
    ?>
    <script>
        location.href="<?php echo $URL;?>/clientes";
    </script>
    <?php

} else {
    session_start();
    $_SESSION['mensaje'] = "Error no se pudo registrar en la base de datos";
    $_SESSION['icono'] = "error";

}