<?php
include('../../config.php');

$codigo = $_POST['codigo'];
$descripcion = $_POST['descripcion'];
$id_marca = $_POST['id_marca'];
$id_categoria = $_POST['id_categoria'];
$costo_minorista = $_POST['costo_minorista'];
$porcentaje_minorista = $_POST['porcentaje_minorista'];
$precio_minorista = $_POST['precio_minorista'];
$moneda = $_POST['moneda'];
$cambio = $_POST['cambio'];
$info = $_POST['info'];

// 📸 Procesar imagen
$nombreDelArchivo = date("Y-m-d-H-i-s");
$filename = $nombreDelArchivo . "__" . $_FILES['image']['name'];
$location = "../../../palmacen/img_productos/" . $filename;
move_uploaded_file($_FILES['image']['tmp_name'], $location);

// 💾 Insertar en base de datos
$sentencia = $pdo->prepare("
    INSERT INTO tbp_almacen 
    (codigo, descripcion, imagen, id_marca, id_categoria, costo_minorista, porcentaje_minorista, precio_minorista, moneda, cambio, info)
    VALUES 
    (:codigo, :descripcion, :imagen, :id_marca, :id_categoria, :costo_minorista, :porcentaje_minorista, :precio_minorista, :moneda, :cambio, :info)
");

$sentencia->bindParam(':codigo', $codigo);
$sentencia->bindParam(':descripcion', $descripcion);
$sentencia->bindParam(':imagen', $filename);
$sentencia->bindParam(':id_marca', $id_marca);
$sentencia->bindParam(':id_categoria', $id_categoria);
$sentencia->bindParam(':costo_minorista', $costo_minorista);
$sentencia->bindParam(':porcentaje_minorista', $porcentaje_minorista);
$sentencia->bindParam(':precio_minorista', $precio_minorista);
$sentencia->bindParam(':moneda', $moneda);
$sentencia->bindParam(':cambio', $cambio);
$sentencia->bindParam(':info', $info);

session_start();

if ($sentencia->execute()) {
    $_SESSION['mensaje'] = "✅ Se registró el producto correctamente.";
    $_SESSION['icono'] = "success";
    header('Location: '.$URL.'/palmacen/');
    exit();
} else {
    $_SESSION['mensaje'] = "❌ Error: no se pudo registrar el producto.";
    $_SESSION['icono'] = "error";
    header('Location: '.$URL.'/palmacen/create.php');
    exit();
}
?>
