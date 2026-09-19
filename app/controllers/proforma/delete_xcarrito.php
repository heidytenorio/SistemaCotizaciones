<?php
include ('../../config.php');

// Paso 1: Obtener id_carrito desde POST
$id_xcarrito = $_POST['id_xcarrito'] ?? null;

if ($id_xcarrito) {
    // Paso 2: Eliminar el producto específico del carrito
    $sentencia = $pdo->prepare("DELETE FROM tb_xcarrito WHERE id_xcarrito = :id_xcarrito");
    $sentencia->bindParam(':id_xcarrito', $id_xcarrito, PDO::PARAM_INT);
    $sentencia->execute();

    if ($sentencia->rowCount() > 0) {
        // ✅ Eliminado correctamente
        ?>
        <script>
            location.href = "<?php echo $URL; ?>/proforma/create.php";
        </script>
        <?php
    } else {
        echo "⚠️ No se encontró el registro con id_xcarrito = $id_xcarrito";
    }
} else {
    echo "❌ No se recibió id_xcarrito por POST";
}
?>
