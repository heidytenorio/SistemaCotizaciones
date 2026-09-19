<?php
include('../app/config.php');
include('../layout/sesion.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: gestion.php');
    exit;
}

$id_entrada = (int)$_GET['id'];

try {

    $pdo->beginTransaction();

    /* ===============================
       OBTENER DETALLE DE LA ENTRADA
    =============================== */
    $stmt = $pdo->prepare("
        SELECT id_variante, cantidad_real, id_almacen
        FROM tb_entrada_d
        WHERE id_entrada = ?
    ");
    $stmt->execute([$id_entrada]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$detalles) {
        throw new Exception("No se encontraron productos en esta entrada.");
    }

    /* ===============================
       REVERTIR STOCK
    =============================== */
    foreach ($detalles as $d) {

        $cantidad_real = $d['cantidad_real'];

        $upd = $pdo->prepare("
            UPDATE tb_stock_almacen
            SET
                stock_actual = stock_actual - :cantidad,
                inversion_actual = inversion_actual - (costo_promedio * :cantidad),
                costo_promedio =
                    CASE
                        WHEN stock_actual - :cantidad > 0
                        THEN (inversion_actual - (costo_promedio * :cantidad))
                             / (stock_actual - :cantidad)
                        ELSE 0
                    END
            WHERE id_variante = :id_variante
              AND id_almacen = :id_almacen
        ");

        $upd->execute([
            ':cantidad'    => $cantidad_real,
            ':id_variante' => $d['id_variante'],
            ':id_almacen'  => $d['id_almacen']
        ]);

        if ($upd->rowCount() === 0) {
            throw new Exception("No se pudo revertir el stock de la variante ID {$d['id_variante']}");
        }
    }

    /* ===============================
       ELIMINAR DETALLE
    =============================== */
    $stmtDelDet = $pdo->prepare("
        DELETE FROM tb_entrada_d
        WHERE id_entrada = ?
    ");
    $stmtDelDet->execute([$id_entrada]);

    /* ===============================
       ELIMINAR CABECERA
    =============================== */
    $stmtDelCab = $pdo->prepare("
        DELETE FROM tb_entrada
        WHERE id_entrada = ?
    ");
    $stmtDelCab->execute([$id_entrada]);

    $pdo->commit();

    header('Location: gestion.php?msg=entrada_eliminada');
    exit;

} catch (Exception $e) {

    $pdo->rollBack();

    echo "<h3>Error al eliminar entrada</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
