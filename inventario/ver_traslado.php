<?php
include('../app/config.php');
include('../layout/sesion.php');

/* ============================
   VALIDAR ID TRASLADO
============================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: gestion.php');
    exit;
}

$id_traslado = (int)$_GET['id'];
if ($id_traslado <= 0) {
    header('Location: gestion.php');
    exit;
}

/* ============================
   CABECERA TRASLADO
============================ */
$sql_cabecera = "
SELECT
    t.id_traslado,
    t.fecha_traslado,
    t.observacion,
    t.estado,
    a_origen.nombre AS almacen_origen,
    a_destino.nombre AS almacen_destino
FROM tb_traslado t
INNER JOIN tb_almacenn a_origen ON t.id_almacen_origen = a_origen.id_almacen
INNER JOIN tb_almacenn a_destino ON t.id_almacen_destino = a_destino.id_almacen
WHERE t.id_traslado = :id
LIMIT 1
";
$q = $pdo->prepare($sql_cabecera);
$q->execute(['id' => $id_traslado]);
$traslado = $q->fetch(PDO::FETCH_ASSOC);

if (!$traslado) {
    header('Location: gestion.php');
    exit;
}

/* ============================
   DETALLE TRASLADO
============================ */
$sql_detalle = "
SELECT
    i.codigo,
    i.descripcion,
    COALESCE(c.nombre_categoria, '—') AS categoria,
    COALESCE(p.razon_social, '—') AS proveedor,
    v.color,
    v.material,
    d.cantidad,
    d.cantidad_real,
    COALESCE(d.factor_conversion,1) AS factor_conversion

FROM tb_traslado_d d
INNER JOIN tb_variantes v   ON v.id_variante = d.id_variante
INNER JOIN tb_inventario i ON i.id_producto = v.id_producto
LEFT JOIN tb_categoria c   ON c.id_categoria = i.id_categoria
LEFT JOIN tb_proveedor p   ON p.id_proveedor = i.id_proveedor

WHERE d.id_traslado = :id

ORDER BY i.descripcion, v.color
";

$q = $pdo->prepare($sql_detalle);
$q->execute(['id' => $id_traslado]);
$detalle = $q->fetchAll(PDO::FETCH_ASSOC);

include('../layout/parte1.php');
?>

<div class="content-wrapper">

    <section class="content-header mb-3">
        <h1 class="text-primary">
            <i class="fas fa-exchange-alt"></i> Detalle de Traslado
        </h1>
    </section>

    <section class="content">

        <!-- CABECERA -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Información General
            </div>

            <div class="card-body row">

                <div class="col-md-3">
                    <label>Fecha Traslado</label>
                    <input class="form-control" value="<?= htmlspecialchars($traslado['fecha_traslado']) ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>Estado</label>
                    <input class="form-control" value="<?= strtoupper($traslado['estado']) ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>Almacén Origen</label>
                    <input class="form-control" value="<?= htmlspecialchars($traslado['almacen_origen']) ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>Almacén Destino</label>
                    <input class="form-control" value="<?= htmlspecialchars($traslado['almacen_destino']) ?>" readonly>
                </div>

                <div class="col-md-12 mt-3">
                    <label>Observación</label>
                    <textarea class="form-control" readonly><?= htmlspecialchars($traslado['observacion']) ?></textarea>
                </div>

            </div>
        </div>

        <!-- DETALLE -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Detalle de Productos
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-sm text-center">
                    <thead class="bg-light">
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Proveedor</th>
                        <th>Color</th>
                        <th>Material</th>
                        <th>Cantidad</th>
                        <th>Cantidad Real</th>
                        <th>Factor</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php if ($detalle): ?>
                        <?php foreach ($detalle as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['codigo']) ?></td>
                                <td class="text-left"><?= htmlspecialchars($d['descripcion']) ?></td>
                                <td><?= htmlspecialchars($d['categoria']) ?></td>
                                <td><?= htmlspecialchars($d['proveedor']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($d['color'])) ?></td>
                                <td><?= htmlspecialchars(ucfirst($d['material'])) ?></td>

                                <td><?= number_format($d['cantidad'], 2) ?></td>
                                <td><?= number_format($d['cantidad_real'], 2) ?></td>
                                <td><?= number_format($d['factor_conversion'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">No hay productos registrados en este traslado</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <a href="gestion.php" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-left"></i> Volver
        </a>

    </section>
</div>

<?php include('../layout/parte2.php'); ?>
