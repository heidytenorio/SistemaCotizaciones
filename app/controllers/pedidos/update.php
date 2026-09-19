<?php
include('../../config.php');
session_start();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$id_pedido = $_POST['id_pedido'] ?? null;
if (!$id_pedido) {
    $mensaje = "ID del pedido no proporcionado.";
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'message' => $mensaje]);
        exit;
    }
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['icono'] = "error";
    header('Location: ' . $URL . '/pedidos/');
    exit;
}

$nro_coti        = $_POST['nro_coti'] ?? '';
$fecha_venci     = $_POST['fecha_venci'] ?? '';
$razon_social    = $_POST['razon_social'] ?? '';
$responsable     = $_POST['responsable'] ?? '';
$estado          = $_POST['estado'] ?? '';
$fecha_emi       = $_POST['fecha_emi'] ?? '';
$descripcion_ant = $_POST['descripcion_text'] ?? '';
$estado_pago     = $_POST['estado_pago'] ?? '';
$flete           = $_POST['flete'] ?? null;
$id_region = !empty($_POST['id_region']) ? $_POST['id_region'] : null;
$nombreDelArchivo = $descripcion_ant;

if (isset($_FILES['descripcion']) && $_FILES['descripcion']['error'] === UPLOAD_ERR_OK) {
    $nombreSeguro = preg_replace("/[^a-zA-Z0-9.\-_]/", "_", basename($_FILES['descripcion']['name']));
    $nombreDelArchivo = date("Y-m-d-H-i-s") . "__" . $nombreSeguro;
    $rutaNueva = "../../../pedidos/docs_pedidoss/" . $nombreDelArchivo;

    if (move_uploaded_file($_FILES['descripcion']['tmp_name'], $rutaNueva)) {
        if (!empty($descripcion_ant)) {
            $rutaAnterior = "../../../pedidos/docs_pedidoss/" . $descripcion_ant;
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }
    } else {
        $mensaje = "Error al guardar el nuevo archivo.";
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => $mensaje]);
            exit;
        }
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['icono'] = "error";
        header('Location: ' . $URL . '/pedidos/update.php?id=' . $id_pedido);
        exit;
    }
}

$sentencia = $pdo->prepare("UPDATE tb_pedido SET
    nro_coti = :nro_coti,
    fecha_venci = :fecha_venci,
    razon_social = :razon_social,
    responsable = :responsable,
    descripcion = :descripcion,
    estado = :estado,
    fecha_emi = :fecha_emi,
    estado_pago = :estado_pago,
  flete = :flete,
id_region = :id_region
WHERE id_pedido = :id_pedido");

$sentencia->bindParam(':nro_coti', $nro_coti);
$sentencia->bindParam(':fecha_venci', $fecha_venci);
$sentencia->bindParam(':razon_social', $razon_social);
$sentencia->bindParam(':responsable', $responsable);
$sentencia->bindParam(':descripcion', $nombreDelArchivo);
$sentencia->bindParam(':estado', $estado);
$sentencia->bindParam(':fecha_emi', $fecha_emi);
$sentencia->bindParam(':id_pedido', $id_pedido);
$sentencia->bindParam(':estado_pago', $estado_pago);
$sentencia->bindParam(':flete', $flete);
$sentencia->bindParam(':id_region', $id_region);
if ($sentencia->execute()) {
    $mensaje = "Se actualizó el pedido correctamente.";
    if ($isAjax) {
        echo json_encode(['status' => 'success', 'message' => $mensaje]);
        exit;
    }
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['icono'] = "success";
    header('Location: ' . $URL . '/pedidos/');
    exit;
} else {
    $mensaje = "Error al actualizar en la base de datos.";
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'message' => $mensaje]);
        exit;
    }
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['icono'] = "error";
    header('Location: ' . $URL . '/pedidos/update.php?id=' . $id_pedido);
    exit;
}