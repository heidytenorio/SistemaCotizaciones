<?php
include('../../config.php');
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ==========================
   VALIDAR MÉTODO
========================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Error: Método no permitido";
    exit;
}

/* ==========================
   VALIDAR CABECERA
========================== */
if (empty($_POST['fecha_emision'])) {
    echo "Error: Fecha de emisión requerida";
    exit;
}

if (empty($_POST['fecha_ingreso'])) {
    echo "Error: Fecha de ingreso requerida";
    exit;
}
if (empty($_POST['id_almacen'])) {
    echo "Error: Almacén requerido";
    exit;
}

$id_almacen = (int) $_POST['id_almacen'];

/* ==========================
   PROCESAR ARCHIVO
========================== */
$doc_entrada_nombre = null;

if (!empty($_FILES['doc_entrada']) && $_FILES['doc_entrada']['error'] === UPLOAD_ERR_OK) {

    $archivo = $_FILES['doc_entrada'];

    $nombreSeguro = preg_replace(
        "/[^a-zA-Z0-9.\-_]/",
        "_",
        basename($archivo['name'])
    );

    $nombreFinal = date("Y-m-d-H-i-s") . "__" . $nombreSeguro;
    $rutaDestino = "../../../inventario/docs_entrada/" . $nombreFinal;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        echo "Error al guardar el documento de entrada";
        exit;
    }

    $doc_entrada_nombre = $nombreFinal;
}

/* ==========================
   DECODIFICAR DETALLE
========================== */
$detalle = json_decode($_POST['detalle'] ?? '', true);

if (!$detalle || count($detalle) === 0) {
    echo "Error: Detalle vacío";
    exit;
}

/* ==========================
   VALIDAR DETALLE
========================== */
foreach ($detalle as $i => $d) {

    if (empty($d['id_producto'])) {
        echo "Error: Producto inválido en fila " . ($i + 1);
        exit;
    }

    if (empty($d['color'])) {
        echo "Error: Color inválido en fila " . ($i + 1);
        exit;
    }

    if ($d['cantidad'] <= 0) {
        echo "Error: Cantidad inválida en fila " . ($i + 1);
        exit;
    }

    if ($d['costo_unitario'] < 0) {
        echo "Error: Costo inválido en fila " . ($i + 1);
        exit;
    }
}

/* ==========================
   TRANSACCIÓN
========================== */
try {

    $pdo->beginTransaction();

    /* ==========================
       INSERT CABECERA
    ========================== */
    $stmt = $pdo->prepare("
        INSERT INTO tb_entrada
        (fecha_emision, fecha_ingreso, tipo_factura, nro_factura, observaciones, doc_entrada)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['fecha_emision'],
        $_POST['fecha_ingreso'],
        $_POST['tipo_factura'] ?? null,
        $_POST['nro_factura'] ?? null,
        $_POST['observaciones'] ?? null,
        $doc_entrada_nombre
    ]);

    $id_entrada = $pdo->lastInsertId();

    /* ==========================
       DETALLE + STOCK
    ========================== */
    foreach ($detalle as $d) {

        /* ==========================
           OBTENER FACTOR
        ========================== */
        $factor = null;

        if (
            isset($d['tipo_liquido']) &&
            $d['tipo_liquido'] === 'GRANEL' &&
            isset($d['factor_conversion']) &&
            $d['factor_conversion'] !== '' &&
            $d['factor_conversion'] > 0
        ) {
            $factor = (float)$d['factor_conversion'];
        }

        /* ==========================
           CANTIDAD REAL
        ========================== */
        $cantidad_real = $d['cantidad'];

        if ($factor !== null) {
            $cantidad_real = $d['cantidad'] * $factor;
        }

        /* ==========================
           ACTUALIZAR FACTOR EN INVENTARIO
           (solo si estaba vacío)
        ========================== */
        if ($factor !== null) {
            $stmtUpdFactor = $pdo->prepare("
                UPDATE tb_inventario
                SET factor_conversion = ?
                WHERE id_producto = ?
                  AND (factor_conversion IS NULL OR factor_conversion = 0)
            ");
            $stmtUpdFactor->execute([
                $factor,
                $d['id_producto']
            ]);
        }

        /* ==========================
           BUSCAR VARIANTE
        ========================== */
        $stmtVar = $pdo->prepare("
            SELECT id_variante
            FROM tb_variantes
            WHERE id_producto = ? AND color = ?
        ");
        $stmtVar->execute([
            $d['id_producto'],
            $d['color']
        ]);

        $var = $stmtVar->fetch(PDO::FETCH_ASSOC);

        /* ==========================
           CREAR VARIANTE
        ========================== */
        if (!$var) {

            $stmtNewVar = $pdo->prepare("
    INSERT INTO tb_variantes (id_producto, color)
    VALUES (?, ?)
");
            $stmtNewVar->execute([
                $d['id_producto'],
                $d['color']
            ]);
            $id_variante = $pdo->lastInsertId();


        } else {
            $id_variante = $var['id_variante'];
        }


        /* ==========================
               INSERT DETALLE
            ========================== */
        $stmtDet = $pdo->prepare("
INSERT INTO tb_entrada_d
(id_entrada, id_variante, cantidad, factor_conversion, cantidad_real, costo_unitario, id_almacen)
VALUES (?, ?, ?, ?, ?, ?, ?)
");

        $stmtDet->execute([
            $id_entrada,
            $id_variante,
            $d['cantidad'],
            $factor,
            $cantidad_real,
            $d['costo_unitario'],
            $id_almacen
        ]);


        /* ==========================
           ACTUALIZAR STOCK EN ALMACÉN
        ========================== */
        /* ==========================
    ACTUALIZAR / CREAR STOCK
 ========================== */
        $sqlStock = "
INSERT INTO tb_stock_almacen
    (id_variante, id_almacen, stock_actual, costo_promedio, inversion_actual)
VALUES
    (:id_variante, :id_almacen, :cantidad, :costo, :inversion)
ON DUPLICATE KEY UPDATE
    stock_actual = stock_actual + :cantidad,
    inversion_actual = inversion_actual + :inversion,
    costo_promedio =
        CASE
            WHEN stock_actual + :cantidad > 0
            THEN (inversion_actual + :inversion) / (stock_actual + :cantidad)
            ELSE costo_promedio
        END
";

        $stmtStock = $pdo->prepare($sqlStock);
        $stmtStock->execute([
            ':id_variante' => $id_variante,
            ':id_almacen'  => $id_almacen,
            ':cantidad'    => $cantidad_real,
            ':costo'       => $d['costo_unitario'],
            ':inversion'   => $cantidad_real * $d['costo_unitario']
        ]);




    }


    $pdo->commit();
    echo "OK";

} catch (Exception $e) {

    $pdo->rollBack();
    echo "ERROR: " . $e->getMessage();
}
