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

// Fecha actual
date_default_timezone_set('America/Lima');
$fechaHora = date("Y-m-d H:i:s");

try {
    $pdo->beginTransaction();

    // 🔁 AHORA ES UPDATE (antes era INSERT)
    $sentencia = $pdo->prepare("
        UPDATE tb_oferta SET
            detalle        = :detalle,
            razon_social   = :razon_social,
            ruc            = :ruc,
            fecha_emi      = :fecha_emi,
            sub_total      = :sub_total,
            igv            = :igv,
            moneda         = :moneda,
            precio_final   = :precio_final,
            garantia       = :garantia,
            vigencia       = :vigencia,
            plazo_entrega  = :plazo_entrega,
            forma_pago     = :forma_pago,
            cuenta         = :cuenta,
            cci            = :cci
        WHERE nro_orden = :nro_orden
    ");

    $sentencia->bindParam(':nro_orden', $nro_orden);
    $sentencia->bindParam(':detalle', $detalle);
    $sentencia->bindParam(':razon_social', $razon_social);
    $sentencia->bindParam(':ruc', $ruc);
    $sentencia->bindParam(':fecha_emi', $fechaHora);
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

        $_SESSION['mensaje'] = "Se actualizó la orden correctamente";
        $_SESSION['icono'] = "success";
        ?>
        <script>
            location.href = "<?php echo $URL; ?>/proforma";
        </script>
        <?php
    } else {
        throw new Exception("No se ejecutó el UPDATE.");
    }

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?php echo $URL; ?>/proforma/update.php";
    </script>
    <?php
}
?>
