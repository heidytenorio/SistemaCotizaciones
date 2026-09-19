<?php
include('../app/config.php');
include('../layout/sesion.php');

/* ============================
   VALIDAR ID
============================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: gestion.php');
    exit;
}

$id_salida = (int)$_GET['id'];
if ($id_salida <= 0) {
    header('Location: gestion.php');
    exit;
}

/* ============================
   CABECERA SALIDA
============================ */
$sql_cabecera = "
SELECT
    id_salida,
    fecha_salida,
    tipo_salida,
    nro_factura,
    info_adicional,
    doc_salida
FROM tb_salida
WHERE id_salida = :id
LIMIT 1
";
$q = $pdo->prepare($sql_cabecera);
$q->execute(['id' => $id_salida]);
$salida = $q->fetch(PDO::FETCH_ASSOC);

if (!$salida) {
    header('Location: gestion.php');
    exit;
}

/* ============================
   DETALLE SALIDA
   (SALIDA → DETALLE → VARIANTE → INVENTARIO)
============================ */
$sql_detalle = "
SELECT
    i.codigo,
    i.descripcion,
    COALESCE(c.nombre_categoria,'—') AS categoria,
    COALESCE(p.razon_social,'—') AS proveedor,
    v.color,

    CASE
        WHEN i.tipo_liquido = 'GRANEL' THEN 'VOLUMEN'
        ELSE 'UNIDAD'
    END AS tipo_control,

    d.cantidad,
    d.cantidad_real,
    d.forma_salida,

    COALESCE(d.factor_conversion,1) AS factor_conversion

FROM tb_salida_d d
INNER JOIN tb_variantes v   ON v.id_variante = d.id_variante
INNER JOIN tb_inventario i ON i.id_producto = v.id_producto
LEFT JOIN tb_categoria c   ON c.id_categoria = i.id_categoria
LEFT JOIN tb_proveedor p   ON p.id_proveedor = i.id_proveedor

WHERE d.id_salida = :id

ORDER BY i.descripcion, v.color
";

$q = $pdo->prepare($sql_detalle);
$q->execute(['id' => $id_salida]);
$detalle = $q->fetchAll(PDO::FETCH_ASSOC);

include('../layout/parte1.php');
?>

<div class="content-wrapper">

    <section class="content-header mb-3">
        <h1 class="text-danger">
            <i class="fas fa-arrow-up"></i> Detalle de Salida
        </h1>
    </section>

    <section class="content">

        <!-- CABECERA -->
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                Información General
            </div>

            <div class="card-body row">

                <div class="col-md-3">
                    <label>Fecha Salida</label>
                    <input class="form-control" value="<?= htmlspecialchars($salida['fecha_salida']) ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>Tipo de Salida</label>
                    <input class="form-control" value="<?= strtoupper($salida['tipo_salida']) ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>N° Documento</label>
                    <input class="form-control" value="<?= htmlspecialchars($salida['nro_factura']) ?>" readonly>
                </div>

                <div class="col-md-12 mt-3">
                    <label>Información Adicional</label>
                    <textarea class="form-control" readonly><?= htmlspecialchars($salida['info_adicional']) ?></textarea>
                </div>

                <div class="col-md-12 mt-3">
                    <label>Documento de Salida</label><br>
                    <?php if (!empty($salida['doc_salida'])):
                        $ruta = $URL . '/inventario/docs_salida/' . urlencode($salida['doc_salida']);
                        ?>
                        <a href="<?= $ruta ?>" target="_blank" class="btn btn-info btn-sm">
                            <i class="fas fa-file-alt"></i> Ver / Descargar
                        </a>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
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
                        <th>Control</th>
                        <th>Cantidad</th>
                        <th>Cantidad Real</th>
                        <th>Forma</th>
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
                                <td><?= $d['tipo_control'] ?></td>
                                <td><?= number_format($d['cantidad'],2) ?></td>
                                <td><?= number_format($d['cantidad_real'],2) ?></td>
                                <td><?= strtoupper($d['forma_salida']) ?></td>
                                <td><?= number_format($d['factor_conversion'],2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">No hay productos registrados</td>
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
