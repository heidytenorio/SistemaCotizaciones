<?php
include('../app/config.php');
include('../layout/sesion.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: gestion.php');
    exit;
}

$id_salida = (int)$_GET['id'];

try {

    $pdo->beginTransaction();

    /* ===============================
       OBTENER DETALLE
    =============================== */
    $stmt = $pdo->prepare("
        SELECT id_variante, cantidad, id_almacen, subtotal
        FROM tb_salida_d
        WHERE id_salida = ?
    ");
    $stmt->execute([$id_salida]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$detalles) {
        throw new Exception("No se encontraron productos en esta salida.");
    }

    /* ===============================
       DEVOLVER STOCK
    =============================== */
    foreach ($detalles as $d) {

        $upd = $pdo->prepare("
            UPDATE tb_stock_almacen
            SET
                stock_actual = stock_actual + :cantidad,
                inversion_actual = inversion_actual + :subtotal
            WHERE id_variante = :id_variante
              AND id_almacen = :id_almacen
        ");

        $upd->execute([
            ':cantidad'    => $d['cantidad'],
            ':subtotal'    => $d['subtotal'],
            ':id_variante' => $d['id_variante'],
            ':id_almacen'  => $d['id_almacen']
        ]);

        if ($upd->rowCount() === 0) {
            throw new Exception("No se pudo devolver stock de la variante {$d['id_variante']}");
        }
    }

    /* ===============================
       ELIMINAR DETALLE
    =============================== */
    $pdo->prepare("
        DELETE FROM tb_salida_d
        WHERE id_salida = ?
    ")->execute([$id_salida]);

    /* ===============================
       ELIMINAR CABECERA
    =============================== */
    $pdo->prepare("
        DELETE FROM tb_salida
        WHERE id_salida = ?
    ")->execute([$id_salida]);

    $pdo->commit();

    header('Location: gestion.php?msg=salida_eliminada');
    exit;

} catch (Exception $e) {

    $pdo->rollBack();
    die("Error al eliminar salida: " . $e->getMessage());
}