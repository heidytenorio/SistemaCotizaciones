<?php
include('../../../app/config.php');

// ======================
// DATOS
// ======================
$codigo        = $_POST['codigo'] ?? '';
$descripcion   = $_POST['descripcion'] ?? '';
$id_proveedor  = $_POST['id_proveedor'] ?? null;
$id_categoria  = $_POST['id_categoria'] ?? null;
$tipo_liquido  = $_POST['tipo_liquido'] ?? 'NO';

// Factor de conversión
if ($tipo_liquido === 'GRANEL') {
    $factor_conversion = floatval($_POST['factor_conversion'] ?? 1);
    if ($factor_conversion <= 0) {
        $factor_conversion = 1;
    }
} else {
    $factor_conversion = 1;
}

// ======================
// IMAGEN
// ======================
$imagen = null;

if (
    isset($_FILES['image']) &&
    is_uploaded_file($_FILES['image']['tmp_name']) &&
    !empty($_FILES['image']['name'])
) {
    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    // Validar extensión
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($extension, $extensiones_permitidas)) {

        $imagen = uniqid('prod_', true) . '.' . $extension;
        $ruta = '../../../inventario/img_pproductos/' . $imagen;

        move_uploaded_file($_FILES['image']['tmp_name'], $ruta);
    }
}

// ======================
// INSERT
// ======================
$sql = "INSERT INTO tb_inventario
(codigo, descripcion, id_proveedor, id_categoria, tipo_liquido, factor_conversion, imagen)
VALUES
(:codigo, :descripcion, :id_proveedor, :id_categoria, :tipo_liquido, :factor_conversion, :imagen)";

$query = $pdo->prepare($sql);
$query->bindParam(':codigo', $codigo);
$query->bindParam(':descripcion', $descripcion);
$query->bindParam(':id_proveedor', $id_proveedor);
$query->bindParam(':id_categoria', $id_categoria);
$query->bindParam(':tipo_liquido', $tipo_liquido);
$query->bindParam(':factor_conversion', $factor_conversion);
$query->bindParam(':imagen', $imagen);

$query->execute();

// ======================
// REDIRECCIÓN
// ======================
header('Location: ../../../inventario/produc.php');
exit;
