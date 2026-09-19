<?php
include ('../../config.php');

// Paso 1: Obtener id_carrito desde POST
$id_carrito = $_POST['id_carrito'] ?? null;

if ($id_carrito) {
    // Paso 2: Eliminar el producto específico del carrito
    $sentencia = $pdo->prepare("DELETE FROM tb_carrito WHERE id_carrito = :id_carrito");
    $sentencia->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
    $sentencia->execute();

    if ($sentencia->rowCount() > 0) {
        // ✅ Eliminado correctamente
        ?>
        <script>
            location.href = "<?php echo $URL; ?>/cotizacion/create.php";
        </script>
        <?php
    } else {
        echo "⚠️ No se encontró el registro con id_carrito = $id_carrito";
    }
} else {
    echo "❌ No se recibió id_carrito por POST";
}
?>

