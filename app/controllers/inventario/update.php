<?php
include('../../../app/config.php');

// ======================
// DATOS
// ======================
$id_producto   = $_POST['id_producto'] ?? null;
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
// OBTENER IMAGEN ACTUAL
// ======================
$stmt_img = $pdo->prepare("SELECT imagen FROM tb_inventario WHERE id_producto = ?");
$stmt_img->execute([$id_producto]);
$imagen = $stmt_img->fetchColumn(); // puede ser NULL

// ======================
// NUEVA IMAGEN
// ======================
if (
    isset($_FILES['image']) &&
    is_uploaded_file($_FILES['image']['tmp_name']) &&
    !empty($_FILES['image']['name'])
) {
    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($extension, $extensiones_permitidas)) {

        // Borrar imagen anterior si existe
        if (!empty($imagen)) {
            $ruta_anterior = '../../../inventario/img_pproductos/' . $imagen;
            if (file_exists($ruta_anterior)) {
                unlink($ruta_anterior);
            }
        }

        // Guardar nueva imagen
        $imagen = uniqid('prod_', true) . '.' . $extension;
        $ruta = '../../../inventario/img_pproductos/' . $imagen;
        move_uploaded_file($_FILES['image']['tmp_name'], $ruta);
    }
}

// ======================
// UPDATE
// ======================
$sql = "UPDATE tb_inventario SET
    codigo = :codigo,
    descripcion = :descripcion,
    id_proveedor = :id_proveedor,
    id_categoria = :id_categoria,
    tipo_liquido = :tipo_liquido,
    factor_conversion = :factor_conversion,
    imagen = :imagen
WHERE id_producto = :id_producto";

$query = $pdo->prepare($sql);
$query->bindParam(':codigo', $codigo);
$query->bindParam(':descripcion', $descripcion);
$query->bindParam(':id_proveedor', $id_proveedor);
$query->bindParam(':id_categoria', $id_categoria);
$query->bindParam(':tipo_liquido', $tipo_liquido);
$query->bindParam(':factor_conversion', $factor_conversion);
$query->bindParam(':imagen', $imagen);
$query->bindParam(':id_producto', $id_producto);

$query->execute();

// ======================
// REDIRECCIÓN
// ======================
header('Location: ../../../inventario/produc.php');
exit;
