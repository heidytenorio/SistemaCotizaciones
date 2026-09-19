<?php
include('../../config.php');
session_start();

// Validar que existan los parámetros y NO estén vacíos
if (
    !isset($_GET['nro_venta']) ||
    !isset($_GET['envio']) ||
    trim($_GET['nro_venta']) === ''
) {
    $_SESSION['mensaje'] = "❌ Faltan datos requeridos para actualizar";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href="<?php echo $URL; ?>/app/controllers/cotizacion";
    </script>
    <?php
    exit();
}

$nro_venta = $_GET['nro_venta'];
$envio     = $_GET['envio'];

try {

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "UPDATE tb_venta 
            SET envio = :envio
            WHERE nro_venta = :nro_venta";

    $sentencia = $pdo->prepare($sql);
    $sentencia->bindParam(':envio', $envio);
    $sentencia->bindParam(':nro_venta', $nro_venta);

    if ($sentencia->execute()) {

        if ($sentencia->rowCount() > 0) {
            // ✔ Sí se actualizó
            $_SESSION['mensaje'] = "✔ El estado de envío se actualizó correctamente";
            $_SESSION['icono']   = "success";
        } else {
            // ✔ No hubo cambios (mismo valor)
            $_SESSION['mensaje'] = "⚠ No se realizaron cambios en el envío";
            $_SESSION['icono']   = "warning";
        }

        ?>
        <script>
            location.href="<?php echo $URL; ?>/app/controllers/cotizacion";
        </script>
        <?php
        exit();

    } else {
        $_SESSION['mensaje'] = "❌ Error al actualizar el envío";
        $_SESSION['icono'] = "error";
        ?>
        <script>
            location.href="<?php echo $URL; ?>/app/controllers/cotizacion";
        </script>
        <?php
    }

} catch (Exception $e) {

    $_SESSION['mensaje'] = "❌ ERROR: " . $e->getMessage();
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href="<?php echo $URL; ?>/app/controllers/cotizacion";
    </script>
    <?php
}
?>
