<?php
include('../../config.php');

session_start();
$nro_orden = $_SESSION['nro_oferta_actual'];

$razon_social = $_GET['razon_social'];
$ruc = $_GET['ruc'];
$detalle = $_GET['detalle'];
$precio_final = $_GET['precio_final'];
$sub_total = $_GET['sub_total'];
$igv = $_GET['igv'];
$moneda =  $_GET['moneda'];
$garantia =  $_GET['garantia'];
$vigencia =  $_GET['vigencia'];
$plazo_entrega =  $_GET['plazo_entrega'];
$forma_pago =  $_GET['forma_pago'];
$cuenta=  $_GET['cuenta'];
$cci=  $_GET['cci'];

// Asignar fecha actual
date_default_timezone_set('America/Lima');
$fechaHora = date("Y-m-d H:i:s");

try {
    $pdo->beginTransaction();

    $sentencia = $pdo->prepare("INSERT INTO tb_oferta
        (nro_orden, detalle, razon_social, ruc, fecha_emi, sub_total, igv, moneda, precio_final, garantia,
         vigencia, plazo_entrega, forma_pago, cuenta, cci)
        VALUES (:nro_orden, :detalle, :razon_social, :ruc, :fecha_emi, :sub_total, :igv, :moneda, :precio_final, :garantia, :vigencia, :plazo_entrega, :forma_pago, :cuenta, :cci)");

    // ❗ ERROR original: te faltaba hacer bindParam(':fecha_emi', $fechaHora);
    $sentencia->bindParam(':nro_orden', $nro_orden);
    $sentencia->bindParam(':detalle', $detalle);
    $sentencia->bindParam(':razon_social', $razon_social);
    $sentencia->bindParam(':ruc', $ruc);
    $sentencia->bindParam(':fecha_emi', $fechaHora); // ✅ agregado (era el error real)
    $sentencia->bindParam(':sub_total', $sub_total);
    $sentencia->bindParam(':igv', $igv);
    $sentencia->bindParam(':moneda', $moneda);
    $sentencia->bindParam(':precio_final', $precio_final);
    $sentencia->bindParam(':garantia', $garantia);
    $sentencia->bindParam(':vigencia', $vigencia);
    $sentencia->bindParam(':plazo_entrega', $plazo_entrega);
    $sentencia->bindParam(':forma_pago', $forma_pago);
    $sentencia->bindParam(':cuenta', $cuenta);
    $sentencia->bindParam(':cci', $cci);

    if ($sentencia->execute()) {
        $pdo->commit();

        $_SESSION['mensaje'] = "Se registró la orden correctamente";
        $_SESSION['icono'] = "success";

        $sentencia_max = $pdo->prepare("SELECT MAX(nro_orden) as ultimo FROM tb_oferta");
        $sentencia_max->execute();
        $resultado = $sentencia_max->fetch(PDO::FETCH_ASSOC);
        $_SESSION['nro_oferta_actual'] = $resultado['ultimo'] + 1;
        ?>
        <script>
            location.href = "<?php echo $URL; ?>/proforma";
        </script>
        <?php
    } else {
        throw new Exception("No se ejecutó la consulta.");
    }

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?php echo $URL; ?>/proforma/create.php";
    </script>
    <?php
}
?>
