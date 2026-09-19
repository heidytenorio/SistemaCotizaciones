<?php
include('../../config.php');
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Método no permitido');
}

$detalle = json_decode($_POST['detalle'] ?? '', true);

if (!$detalle || count($detalle) === 0) {
    exit('Detalle vacío');
}

$id_almacen = $_POST['id_almacen'] ?? null;
if (!$id_almacen) {
    exit('Debe seleccionar un almacén');
}

try {
    $pdo->beginTransaction();

    /* ==========================
       DOCUMENTO DE SALIDA
    ========================== */
    $doc_salida = null;

    if (
        isset($_FILES['doc_salida']) &&
        $_FILES['doc_salida']['error'] !== UPLOAD_ERR_NO_FILE &&
        $_FILES['doc_salida']['error'] !== UPLOAD_ERR_OK
    ) {
        throw new Exception(
            'Error en la subida del archivo (código ' . $_FILES['doc_salida']['error'] . ')'
        );
    }

    if (
        isset($_FILES['doc_salida']) &&
        $_FILES['doc_salida']['error'] === UPLOAD_ERR_OK
    ) {

        $carpeta = __DIR__ . '/../../../inventario/docs_salida/';

        if (!is_dir($carpeta)) {
            if (!mkdir($carpeta, 0777, true) && !is_dir($carpeta)) {
                throw new Exception('No se pudo crear la carpeta de documentos');
            }
        }

        $ext = strtolower(pathinfo($_FILES['doc_salida']['name'], PATHINFO_EXTENSION));
        $permitidos = ['pdf','jpg','jpeg','png','doc','docx','xls','xlsx'];

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
    if (empty($_POST['fecha_salida'])) {
        throw new Exception('Fecha de salida requerida');
    }

    /* ==========================
       CABECERA tb_salida
    ========================== */
    $stmt = $pdo->prepare("
        INSERT INTO tb_salida
        (fecha_salida, tipo_salida, nro_factura, info_adicional, doc_salida)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['fecha_salida'],
        $_POST['tipo_salida'] ?? null,
        $_POST['nro_factura'] ?? null,
        $_POST['info_adicional'] ?? null,
        $doc_salida
    ]);

    $id_salida = $pdo->lastInsertId();

    /* ==========================
       PREPARES OPTIMIZADOS
    ========================== */

    $stmtVar = $pdo->prepare("
    SELECT id_variante
    FROM tb_variantes
    WHERE id_producto = ?
      AND color = ?
    LIMIT 1
");

    $stmtInsertStock = $pdo->prepare("
    INSERT INTO tb_stock_almacen
        (id_variante, id_almacen, stock_actual, costo_promedio, inversion_actual)
    VALUES (?, ?, 0, 0, 0)
    ON DUPLICATE KEY UPDATE id_stock = id_stock
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
        stock_actual = stock_actual - :cantidad,
       inversion_actual = inversion_actual - :subtotal

    WHERE id_almacen = :id_almacen
      AND id_variante = :id_variante
      AND stock_actual >= :cantidad
");

    /* ==========================
       DETALLE + VARIANTES + STOCK
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

        // Si no viene cantidad_real, calculamos con factor
        $cantidad_real = floatval($d['cantidad_real']);
        if ($cantidad_real <= 0) {
            $factor = floatval($d['factor_conversion'] ?? 1);
            $cantidad_real = floatval($d['cantidad']) * ($factor > 0 ? $factor : 1);
        }

        if ($cantidad_real <= 0) {
            throw new Exception("Cantidad real inválida (fila " . ($i + 1) . ")");
        }

        $formasPermitidas = ['litros', 'unidad', 'caja'];
        if (!in_array($d['forma_salida'], $formasPermitidas)) {
            throw new Exception("Forma de salida inválida (fila " . ($i + 1) . ")");
        }

        $color = mb_strtolower(trim($d['color']));

        $color = substr($color, 0, 50);

        /* ==========================
    OBTENER / CREAR VARIANTE
 ========================== */

        $stmtVar->execute([$d['id_producto'], $color]);
        $id_variante = $stmtVar->fetchColumn();

        if (!$id_variante) {
            throw new Exception("No existe stock para el color '{$color}' (fila " . ($i + 1) . ")");
        }

        /* ==========================
           ASEGURAR STOCK EN ALMACÉN
        ========================== */
        $stmtInsertStock->execute([$id_variante, $id_almacen]);


        /* ==========================
           OBTENER / CREAR STOCK ALMACÉN
        ========================== */

        $factor_conversion = isset($d['factor_conversion']) && $d['factor_conversion'] > 0
            ? $d['factor_conversion']
            : 1;

        /* ==========================
           INSERT DETALLE tb_salida_d
        ========================== */
        $precio = floatval($d['precio'] ?? 0);
        $subtotal = $precio * $cantidad_real;


        $stmtDet->execute([
            $id_salida,
            $id_almacen,
            $id_variante,
            $d['forma_salida'],
            $d['cantidad'],          // cantidad_ingresada
            $factor_conversion,
            $cantidad_real,
            $cantidad_real,          // campo cantidad
            $precio,
            $subtotal
        ]);


        /* ==========================
           ACTUALIZAR STOCK EN ALMACÉN
        ========================== */
        $stmtStockSalida->execute([
            ':id_almacen'  => $id_almacen,
            ':id_variante' => $id_variante,
            ':cantidad'    => $cantidad_real,
            ':subtotal'    => $subtotal
        ]);


        if ($stmtStockSalida->rowCount() === 0) {
            throw new Exception(
                "Stock insuficiente para el producto ID {$d['id_producto']} (fila " . ($i + 1) . ")"
            );
        }



    }

    $pdo->commit();
    echo "OK";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "ERROR: " . $e->getMessage();
}
