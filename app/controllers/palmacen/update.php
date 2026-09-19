<?php
include('../../config.php');

// ==========================
// RECIBIR DATOS DEL FORMULARIO
// ==========================
$id_producto = $_POST['id_producto'];
$image_text = $_POST['image_text'];
$codigo = $_POST['codigo'];
$descripcion = $_POST['descripcion'];
$id_marca = $_POST['id_marca'];
$id_categoria = $_POST['id_categoria'];
$costo_minorista = $_POST['costo_minorista'];
$porcentaje_minorista = $_POST['porcentaje_minorista'];
$moneda = $_POST['moneda'];
$cambio = $_POST['cambio'];
$info = $_POST['info'];

// ==========================
// CALCULAR PRECIO MINORISTA (BACKEND)
// ==========================
$ganancia = $costo_minorista * $porcentaje_minorista / 100;
$precio_base = $costo_minorista + $ganancia;

if ($moneda === 'DOLARES' && $cambio > 0) {
    $precio_minorista = round($precio_base * $cambio, 2);
} else {
    $precio_minorista = round($precio_base, 2);
}

// ==========================
// PROCESAR IMAGEN
// ==========================
if ($_FILES['image']['name'] != null) {
    $nombreDelArchivo = date("Y-m-d-h-i-s");
    $image_text = $nombreDelArchivo . "__" . $_FILES['image']['name'];
    $location = "../../../palmacen/img_productos/" . $image_text;
    move_uploaded_file($_FILES['image']['tmp_name'], $location);
}

// ==========================
// SENTENCIA PREPARADA UPDATE
// ==========================
$sentencia = $pdo->prepare("UPDATE tbp_almacen 
SET
    imagen = :imagen,
    codigo = :codigo,
    descripcion = :descripcion,
    id_marca = :id_marca,
    id_categoria = :id_categoria,
    costo_minorista = :costo_minorista,
    porcentaje_minorista = :porcentaje_minorista,
    precio_minorista = :precio_minorista,
    moneda = :moneda,
    cambio = :cambio, 
    info = :info
WHERE id_producto = :id_producto
");

// ==========================
// BIND PARAMS
// ==========================
$sentencia->bindParam(':imagen', $image_text);
$sentencia->bindParam(':codigo', $codigo);
$sentencia->bindParam(':descripcion', $descripcion);
$sentencia->bindParam(':id_marca', $id_marca);
$sentencia->bindParam(':id_categoria', $id_categoria);
$sentencia->bindParam(':costo_minorista', $costo_minorista);
$sentencia->bindParam(':porcentaje_minorista', $porcentaje_minorista);
$sentencia->bindParam(':precio_minorista', $precio_minorista);
$sentencia->bindParam(':moneda', $moneda);
$sentencia->bindParam(':cambio', $cambio);
$sentencia->bindParam(':info', $info);
$sentencia->bindParam(':id_producto', $id_producto);

// ==========================
// EJECUTAR Y RESPUESTA
// ==========================
if ($sentencia->execute()) {
    session_start();
    $_SESSION['mensaje'] = "Se actualizó el producto correctamente";
    $_SESSION['icono'] = "success";
    header('Location: ' . $URL . '/palmacen/');
} else {
    session_start();
    $_SESSION['mensaje'] = "Error: no se pudo actualizar el producto";
    $_SESSION['icono'] = "error";
    header('Location: ' . $URL . '/palmacen/update.php?id=' . $id_producto);
}
