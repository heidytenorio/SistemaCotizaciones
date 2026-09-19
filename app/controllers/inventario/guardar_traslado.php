<?php
include(__DIR__ . '/../../config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Método no permitido');
}

$fecha    = $_POST['fecha_traslado'] ?? null;
$origen   = $_POST['almacen_origen'] ?? null;
$destino  = $_POST['almacen_destino'] ?? null;
$obs      = $_POST['observaciones'] ?? null;
$detalles = json_decode($_POST['detalle'] ?? '', true);

if (!$fecha || !$origen || !$destino) {
    exit('Datos incompletos');
}

if ($origen == $destino) {
    exit('El almacén origen y destino no pueden ser iguales');
}

if (!$detalles || count($detalles) === 0) {
    exit('Detalle vacío');
}

try {

    $pdo->beginTransaction();

    /* ==========================
       INSERT CABECERA
    ========================== */

    $stmt = $pdo->prepare("
        INSERT INTO tb_traslado
        (fecha_traslado, id_almacen_origen, id_almacen_destino, observacion, estado)
        VALUES (?, ?, ?, ?, 'CONFIRMADO')
    ");

    $stmt->execute([$fecha, $origen, $destino, $obs]);

    $idTraslado = $pdo->lastInsertId();

    /* ==========================
       RECORRER DETALLE
    ========================== */

    foreach ($detalles as $i => $d) {

        if (empty($d['id_producto'])) {
            throw new Exception("Producto inválido (fila " . ($i + 1) . ")");
        }

        if (!isset($d['cantidad_real']) || $d['cantidad_real'] <= 0) {
            throw new Exception("Cantidad inválida (fila " . ($i + 1) . ")");
        }

        $color = ($d['tipo_liquido'] === 'GRANEL')
            ? 'granel'
            : trim(strtolower($d['color'] ?? ''));

        /* ==========================
           OBTENER VARIANTE
        ========================== */

        $stmtVar = $pdo->prepare("
            SELECT id_variante
            FROM tb_variantes
            WHERE id_producto = ? AND color = ?
            LIMIT 1
        ");

        $stmtVar->execute([$d['id_producto'], $color]);

        $idVariante = $stmtVar->fetchColumn();

        if (!$idVariante) {
            throw new Exception("No existe variante para el producto ID {$d['id_producto']} (fila " . ($i + 1) . ")");
        }

        /* ==========================
           BLOQUEAR STOCK ORIGEN
        ========================== */

        $stmtStock = $pdo->prepare("
            SELECT stock_actual, costo_promedio, inversion_actual
            FROM tb_stock_almacen
            WHERE id_almacen = ? AND id_variante = ?
            FOR UPDATE
        ");

        $stmtStock->execute([$origen, $idVariante]);

        $stockData = $stmtStock->fetch(PDO::FETCH_ASSOC);

        if (!$stockData) {
            throw new Exception("No existe stock en almacén origen para variante {$idVariante}");
        }

        if ($stockData['stock_actual'] < $d['cantidad_real']) {
            throw new Exception("Stock insuficiente para producto ID {$d['id_producto']}");
        }

        $cantidadReal = $d['cantidad_real'];

        /* ==========================
           CALCULAR VALOR TRASLADO
        ========================== */

        $costoUnitario = $stockData['costo_promedio'];
        $valorMovimiento = $costoUnitario * $cantidadReal;

        /* ==========================
           INSERT DETALLE
        ========================== */

        $stmtDet = $pdo->prepare("
            INSERT INTO tb_traslado_d
            (id_traslado, id_variante, cantidad, cantidad_real, factor_conversion)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmtDet->execute([
            $idTraslado,
            $idVariante,
            $d['cantidad'],
            $cantidadReal,
            $d['factor_conversion'] ?? 1
        ]);

        /* ==========================
           RESTAR STOCK ORIGEN
        ========================== */

        $stmtStockO = $pdo->prepare("
            UPDATE tb_stock_almacen
            SET
                stock_actual = stock_actual - :cantidad,
                inversion_actual = inversion_actual - :valor
            WHERE id_almacen = :almacen
            AND id_variante = :variante
        ");

        $stmtStockO->execute([
            ':cantidad' => $cantidadReal,
            ':valor' => $valorMovimiento,
            ':almacen' => $origen,
            ':variante' => $idVariante
        ]);

        /* ==========================
           SUMAR STOCK DESTINO
        ========================== */

        $stmtStockD = $pdo->prepare("
            INSERT INTO tb_stock_almacen
            (id_almacen, id_variante, stock_actual, inversion_actual)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                stock_actual = stock_actual + VALUES(stock_actual),
                inversion_actual = inversion_actual + VALUES(inversion_actual)
        ");

        $stmtStockD->execute([
            $destino,
            $idVariante,
            $cantidadReal,
            $valorMovimiento
        ]);
    }

    $pdo->commit();

    echo "OK";

} catch (Exception $e) {

    $pdo->rollBack();

    echo "ERROR: " . $e->getMessage();
}

