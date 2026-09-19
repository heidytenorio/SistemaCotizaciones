<?php
include('../../config.php');
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Método no permitido');
}

/* ==========================
   VALIDACIONES INICIALES
========================== */

$id_salida = $_POST['id_salida'] ?? null;

if (!$id_salida) {
    exit('ID de salida requerido');
}

$detalle = json_decode($_POST['detalle'] ?? '', true);

if (!$detalle || count($detalle) === 0) {
    exit('Detalle vacío');
}

$id_almacen = $_POST['id_almacen'] ?? null;

if (!$id_almacen) {
    exit('Debe seleccionar un almacén');
}

if (empty($_POST['fecha_salida'])) {
    exit('Fecha de salida requerida');
}

try {

    $pdo->beginTransaction();

    /* ==========================
       OBTENER DETALLE ANTERIOR
    ========================== */

    $stmtOld = $pdo->prepare("
        SELECT
            id_variante,
            id_almacen,
            cantidad_real,
            subtotal
        FROM tb_salida_d
        WHERE id_salida = ?
    ");

    $stmtOld->execute([$id_salida]);
    $oldItems = $stmtOld->fetchAll(PDO::FETCH_ASSOC);

    /* ==========================
       RESTAURAR STOCK ANTERIOR
    ========================== */

    $stmtRestore = $pdo->prepare("
        UPDATE tb_stock_almacen
        SET
            stock_actual = stock_actual + ?,
            inversion_actual = inversion_actual + ?
        WHERE id_almacen = ?
          AND id_variante = ?
    ");

    foreach ($oldItems as $old) {
        $stmtRestore->execute([
            $old['cantidad_real'],
            $old['subtotal'],
            $old['id_almacen'],
            $old['id_variante']
        ]);
    }

    /* ==========================
       SUBIR NUEVO DOCUMENTO
    ========================== */

    $doc_salida = null;

    if (
        isset($_FILES['doc_salida']) &&
        $_FILES['doc_salida']['error'] !== UPLOAD_ERR_NO_FILE &&
        $_FILES['doc_salida']['error'] !== UPLOAD_ERR_OK
    ) {
        throw new Exception(
            'Error en subida de archivo (código ' . $_FILES['doc_salida']['error'] . ')'
        );
    }

    if (
        isset($_FILES['doc_salida']) &&
        $_FILES['doc_salida']['error'] === UPLOAD_ERR_OK
    ) {

        $carpeta = __DIR__ . '/../../../inventario/docs_salida/';

        if (!is_dir($carpeta)) {
            if (!mkdir($carpeta, 0777, true) && !is_dir($carpeta)) {
                throw new Exception('No se pudo crear carpeta de documentos');
            }
        }

        $ext = strtolower(pathinfo($_FILES['doc_salida']['name'], PATHINFO_EXTENSION));
        $permitidos = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];

        if (!in_array($ext, $permitidos)) {
            throw new Exception('Tipo de archivo no permitido');
        }

        $nombreArchivo = 'salida_' . date('Ymd_His') . '.' . $ext;
        $rutaFinal = $carpeta . $nombreArchivo;

        if (!move_uploaded_file($_FILES['doc_salida']['tmp_name'], $rutaFinal)) {
            throw new Exception('Error al guardar el documento');
        }

        $doc_salida = $nombreArchivo;
    }

    /* ==========================
       ACTUALIZAR CABECERA
    ========================== */

    if ($doc_salida) {

        $stmtCab = $pdo->prepare("
            UPDATE tb_salida
            SET
                fecha_salida = ?,
                tipo_salida = ?,
                nro_factura = ?,
                info_adicional = ?,
                doc_salida = ?
            WHERE id_salida = ?
        ");

        $stmtCab->execute([
            $_POST['fecha_salida'],
            $_POST['tipo_salida'] ?? null,
            $_POST['nro_factura'] ?? null,
            $_POST['info_adicional'] ?? null,
            $doc_salida,
            $id_salida
        ]);

    } else {

        $stmtCab = $pdo->prepare("
            UPDATE tb_salida
            SET
                fecha_salida = ?,
                tipo_salida = ?,
                nro_factura = ?,
                info_adicional = ?
            WHERE id_salida = ?
        ");

        $stmtCab->execute([
            $_POST['fecha_salida'],
            $_POST['tipo_salida'] ?? null,
            $_POST['nro_factura'] ?? null,
            $_POST['info_adicional'] ?? null,
            $id_salida
        ]);
    }

    /* ==========================
       PREPARES
    ========================== */

    $stmtVar = $pdo->prepare("
    SELECT id_variante
    FROM tb_variantes
    WHERE id_producto = ?
      AND LOWER(TRIM(color)) = ?
    LIMIT 1
");

    $stmtCheckStock = $pdo->prepare("
        SELECT stock_actual
        FROM tb_stock_almacen
        WHERE id_almacen = ?
          AND id_variante = ?
    ");

    /* ==========================
       VALIDAR STOCK NUEVO
    ========================== */

    foreach ($detalle as $i => $d) {

        if (empty($d['id_producto'])) {
            throw new Exception("Producto inválido (fila " . ($i + 1) . ")");
        }

        if (!isset($d['cantidad']) || $d['cantidad'] <= 0) {
            throw new Exception("Cantidad inválida (fila " . ($i + 1) . ")");
        }

        if ($d['tipo_liquido'] !== 'GRANEL' && empty($d['color'])) {
            throw new Exception("Debe seleccionar color (fila " . ($i + 1) . ")");
        }

        $cantidad_real = floatval($d['cantidad_real']);

        if ($cantidad_real <= 0) {
            throw new Exception("Cantidad real inválida (fila " . ($i + 1) . ")");
        }

        $color = mb_strtolower(trim($d['color'] ?? ''));

        $stmtVar->execute([
            $d['id_producto'],
            $color
        ]);

        $id_variante = $stmtVar->fetchColumn();

        if (!$id_variante) {
            throw new Exception("No existe variante (fila " . ($i + 1) . ")");
        }

        $stmtCheckStock->execute([
            $id_almacen,
            $id_variante
        ]);

        $stock = floatval($stmtCheckStock->fetchColumn() ?? 0);

        if ($stock < $cantidad_real) {
            throw new Exception("Stock insuficiente (fila " . ($i + 1) . ")");
        }
    }

    /* ==========================
       ELIMINAR DETALLE ANTERIOR
    ========================== */

    $stmtDelete = $pdo->prepare("
        DELETE FROM tb_salida_d
        WHERE id_salida = ?
    ");

    $stmtDelete->execute([$id_salida]);

    /* ==========================
       PREPARES NUEVO DETALLE
    ========================== */

    $stmtInsertStock = $pdo->prepare("
        INSERT INTO tb_stock_almacen
        (
            id_variante,
            id_almacen,
            stock_actual,
            costo_promedio,
            inversion_actual
        )
        VALUES (?, ?, 0, 0, 0)
        ON DUPLICATE KEY UPDATE
            id_stock = id_stock
    ");

    $stmtDet = $pdo->prepare("
        INSERT INTO tb_salida_d
        (
            id_salida,
            id_almacen,
            id_variante,
            forma_salida,
            cantidad_ingresada,
            factor_conversion,
            cantidad_real,
            cantidad,
            precio,
            subtotal
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmtStockSalida = $pdo->prepare("
        UPDATE tb_stock_almacen
        SET
            stock_actual = stock_actual - ?,
            inversion_actual = inversion_actual - ?
        WHERE id_almacen = ?
          AND id_variante = ?
    ");

    /* ==========================
       INSERTAR NUEVO DETALLE
    ========================== */

    foreach ($detalle as $i => $d) {

        $color = substr(
            mb_strtolower(trim($d['color'] ?? '')),
            0,
            50
        );

        $stmtVar->execute([
            $d['id_producto'],
            $color
        ]);

        $id_variante = $stmtVar->fetchColumn();

        if (!$id_variante) {
            throw new Exception("Variante no encontrada (fila " . ($i + 1) . ")");
        }

        $stmtInsertStock->execute([
            $id_variante,
            $id_almacen
        ]);

        $cantidad_real = floatval($d['cantidad_real']);

        /* ==========================
           CORRECCIÓN IMPORTANTE
        ========================== */

        $precio = 0;
        $subtotal = 0;

        $factor_conversion = isset($d['factor_salida']) && $d['factor_salida'] > 0
            ? floatval($d['factor_salida'])
            : 1;

        $stmtDet->execute([
            $id_salida,
            $id_almacen,
            $id_variante,
            $d['forma_salida'],
            $d['cantidad'],          // cantidad_ingresada
            $factor_conversion,      // factor real usado
            $cantidad_real,
            $d['cantidad'],          // AQUÍ estaba el error
            $precio,
            $subtotal
        ]);

        $stmtStockSalida->execute([
            $cantidad_real,
            $subtotal,
            $id_almacen,
            $id_variante
        ]);
    }

    $pdo->commit();

    echo "OK";

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "ERROR: " . $e->getMessage();
}
