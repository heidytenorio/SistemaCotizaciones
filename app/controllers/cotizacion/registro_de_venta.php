<?php
include('../../config.php');

session_start();
$nro_venta = $_SESSION['nro_venta_actual'];

$id_cliente = $_GET['id_cliente'];
$precio_final = $_GET['precio_final'];
$sub_total = $_GET['sub_total'];
$igv = $_GET['igv'];
$tipo_marca =  $_GET['tipo_marca'];

// Asignar fecha actual
date_default_timezone_set('America/Lima');
$fechaHora = date("Y-m-d H:i:s");

try {
    $pdo->beginTransaction();

    $sentencia = $pdo->prepare("INSERT INTO tb_venta
        (nro_venta, id_cliente, fecha_emi, sub_total, igv, tipo_marca, precio_final)
        VALUES (:nro_venta, :id_cliente, :fecha_emi, :sub_total, :igv, :tipo_marca, :precio_final)");

    $sentencia->bindParam(':nro_venta', $nro_venta);
    $sentencia->bindParam(':id_cliente', $id_cliente);
    $sentencia->bindParam(':fecha_emi', $fechaHora);
    $sentencia->bindParam(':sub_total', $sub_total);
    $sentencia->bindParam(':igv', $igv);
    $sentencia->bindParam(':tipo_marca', $tipo_marca);
    $sentencia->bindParam(':precio_final', $precio_final);

    if ($sentencia->execute()) {
        $pdo->commit();

        // ✅ Mensaje de éxito
        $_SESSION['mensaje'] = "Se registró la venta correctamente";
        $_SESSION['icono'] = "success";

        // ✅ Calcular el siguiente nro_venta
        $sentencia_max = $pdo->prepare("SELECT MAX(nro_venta) as ultimo FROM tb_venta");
        $sentencia_max->execute();
        $resultado = $sentencia_max->fetch(PDO::FETCH_ASSOC);
        $_SESSION['nro_venta_actual'] = $resultado['ultimo'] + 1;
        ?>
        <script>
            location.href = "<?php echo $URL; ?>/cotizacion";
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
        location.href = "<?php echo $URL; ?>/cotizacion/create.php";
    </script>
    <?php
}
?>
