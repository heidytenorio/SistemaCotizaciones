<?php
include('../../config.php');
session_start(); // Para usar $_SESSION

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$nro_coti      = $_POST['nro_coti'] ?? '';
$fecha_venci   = $_POST['fecha_venci'] ?? '';
$razon_social  = $_POST['razon_social'] ?? '';
$responsable   = $_POST['responsable'] ?? '';
$estado        = $_POST['estado'] ?? '';
$fecha_emi     = $_POST['fecha_emi'] ?? '';
$estado_pago   = $_POST['estado_pago'] ?? '';
$flete         = $_POST['flete'] ?? null;
$id_region = !empty($_POST['id_region']) ? $_POST['id_region'] : null;
$filename = '';
$descripcion = $_FILES['descripcion'] ?? null;

if ($descripcion && $descripcion['error'] === UPLOAD_ERR_OK) {
    $nombreSeguro = preg_replace("/[^a-zA-Z0-9.\-_]/", "_", basename($descripcion['name']));
    $nombreDelArchivo = date("Y-m-d-H-i-s") . "__" . $nombreSeguro;
    $rutaDestino = "../../../pedidos/docs_pedidoss/" . $nombreDelArchivo;

    if (move_uploaded_file($descripcion['tmp_name'], $rutaDestino)) {
        $filename = $nombreDelArchivo;
    } else {
        $mensaje = "Error al guardar el archivo del pedido.";
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => $mensaje]);
            exit;
        }
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['icono'] = "error";
        header('Location: ' . $URL . '/pedidos/create.php');
        exit;
    }
}

$sentencia = $pdo->prepare("INSERT INTO tb_pedido 
    (nro_coti, fecha_venci, razon_social, responsable, descripcion, estado, fecha_emi, estado_pago, flete, id_region)
    VALUES 
    (:nro_coti, :fecha_venci, :razon_social, :responsable, :descripcion, :estado, :fecha_emi, :estado_pago, :flete, :id_region)");

$sentencia->bindParam(':nro_coti', $nro_coti);
$sentencia->bindParam(':fecha_venci', $fecha_venci);
$sentencia->bindParam(':razon_social', $razon_social);
$sentencia->bindParam(':responsable', $responsable);
$sentencia->bindParam(':descripcion', $filename);
$sentencia->bindParam(':estado', $estado);
$sentencia->bindParam(':fecha_emi', $fecha_emi);
$sentencia->bindParam(':estado_pago', $estado_pago);
$sentencia->bindParam(':flete', $flete);
$sentencia->bindParam(':id_region', $id_region);


if ($sentencia->execute()) {
    $mensaje = "Se registró el pedido correctamente.";
    if ($isAjax) {
        echo json_encode(['status' => 'success', 'message' => $mensaje]);
        exit;
    }
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['icono'] = "success";
    header('Location: ' . $URL . '/pedidos/');
    exit;
} else {
    $mensaje = "Error al registrar el pedido en la base de datos.";
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'message' => $mensaje]);
        exit;
    }
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['icono'] = "error";
    header('Location: ' . $URL . '/pedidos/create.php');
    exit;
}