<?php
include('../app/config.php');
include('../layout/sesion.php');

$idTraslado = $_GET['id'] ?? null;

if (!$idTraslado || !is_numeric($idTraslado)) {
    header('Location: gestion.php');
    exit;
}

try {

    $pdo->beginTransaction();

    /* ===============================
       OBTENER TRASLADO
    =============================== */
    $stmt = $pdo->prepare("
        SELECT *
        FROM tb_traslado
        WHERE id_traslado = ?
        FOR UPDATE
    ");
    $stmt->execute([$idTraslado]);
    $traslado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$traslado) {
        throw new Exception("Traslado no encontrado");
    }

    if ($traslado['estado'] === 'ANULADO') {
        throw new Exception("El traslado ya está anulado");
    }

    /* ===============================
       OBTENER DETALLES
    =============================== */
    $stmtDet = $pdo->prepare("
        SELECT id_variante, cantidad_real
        FROM tb_traslado_d
        WHERE id_traslado = ?
    ");
    $stmtDet->execute([$idTraslado]);
    $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

    if (!$detalles) {
        throw new Exception("No hay detalles en este traslado");
    }

    /* ===============================
       VALIDAR STOCK DESTINO
    =============================== */
    foreach ($detalles as $d) {

        $stmtCheck = $pdo->prepare("
            SELECT stock_actual
            FROM tb_stock_almacen
            WHERE id_almacen = ? AND id_variante = ?
        ");

        $stmtCheck->execute([
            $traslado['id_almacen_destino'],
            $d['id_variante']
        ]);

        $stockDestino = $stmtCheck->fetchColumn();

        if ($stockDestino === false) {
            throw new Exception("No existe stock en almacén destino");
        }

        if ($stockDestino < $d['cantidad_real']) {
            throw new Exception("Stock insuficiente para revertir traslado");
        }
    }

    /* ===============================
       REVERTIR STOCK
    =============================== */
    foreach ($detalles as $d) {

        $idVariante = $d['id_variante'];
        $cantidad   = $d['cantidad_real'];

        /* Obtener costo promedio del destino */
        $stmtCosto = $pdo->prepare("
            SELECT costo_promedio
            FROM tb_stock_almacen
            WHERE id_almacen = ? AND id_variante = ?
        ");

        $stmtCosto->execute([
            $traslado['id_almacen_destino'],
            $idVariante
        ]);

        $costoPromedio = $stmtCosto->fetchColumn();

        $valor = $costoPromedio * $cantidad;

        /* ===============================
           SUMAR STOCK AL ORIGEN
        =============================== */
        $stmtOrigen = $pdo->prepare("
            INSERT INTO tb_stock_almacen
            (id_almacen, id_variante, stock_actual, inversion_actual, costo_promedio)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                stock_actual = stock_actual + VALUES(stock_actual),
                inversion_actual = inversion_actual + VALUES(inversion_actual),
                costo_promedio =
                    CASE
                        WHEN stock_actual + VALUES(stock_actual) > 0
                        THEN (inversion_actual + VALUES(inversion_actual))
                             / (stock_actual + VALUES(stock_actual))
                        ELSE costo_promedio
                    END
        ");

        $stmtOrigen->execute([
            $traslado['id_almacen_origen'],
            $idVariante,
            $cantidad,
            $valor,
            $costoPromedio
        ]);

        /* ===============================
           RESTAR STOCK DESTINO
        =============================== */
        $stmtDestino = $pdo->prepare("
            UPDATE tb_stock_almacen
            SET
                stock_actual = stock_actual - :cantidad,
                inversion_actual = inversion_actual - :valor,
                costo_promedio =
                    CASE
                        WHEN stock_actual - :cantidad > 0
                        THEN (inversion_actual - :valor) / (stock_actual - :cantidad)
                        ELSE 0
                    END
            WHERE id_almacen = :almacen
            AND id_variante = :variante
        ");

        $stmtDestino->execute([
            ':cantidad' => $cantidad,
            ':valor' => $valor,
            ':almacen' => $traslado['id_almacen_destino'],
            ':variante' => $idVariante
        ]);
    }

    /* ===============================
       ANULAR TRASLADO
    =============================== */
    $stmtUpdate = $pdo->prepare("
        UPDATE tb_traslado
        SET estado = 'ANULADO'
        WHERE id_traslado = ?
    ");
    $stmtUpdate->execute([$idTraslado]);

    $pdo->commit();

    header('Location: gestion.php?msg=traslado_anulado');
    exit;

} catch (Exception $e) {

    $pdo->rollBack();
    echo "ERROR: " . $e->getMessage();
}
