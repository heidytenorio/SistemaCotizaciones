<?php
include ('../../config.php');

$id_xcarrito = $_POST['id_xcarrito'] ?? null;
$nro_orden   = $_POST['nro_orden'] ?? null;

if ($id_xcarrito && $nro_orden) {
    $sentencia = $pdo->prepare("
        DELETE FROM tb_xcarrito 
        WHERE id_xcarrito = :id_xcarrito AND nro_orden = :nro_orden
    ");
    $sentencia->bindParam(':id_xcarrito', $id_xcarrito, PDO::PARAM_INT);
    $sentencia->bindParam(':nro_orden', $nro_orden, PDO::PARAM_INT);
    $sentencia->execute();

    if ($sentencia->rowCount() > 0) {
        ?>
        <script>
            location.href = "<?= $URL ?>/proforma/update.php?id=<?= $nro_orden ?>";
        </script>
        <?php
    } else {
        echo "⚠️ No se encontró el registro con id_xcarrito = $id_xcarrito para esta orden.";
    }
} else {
    echo "❌ No se recibió id_xcarrito o nro_orden por POST";
}
?>


