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

$id_entrada = (int)$_GET['id'];
if ($id_entrada <= 0) {
    header('Location: gestion.php');
    exit;
}

/* ============================
   CABECERA ENTRADA
============================ */
$sql_cabecera = "
SELECT 
    id_entrada,
    fecha_emision,
    fecha_ingreso,
    tipo_factura,
    nro_factura,
    observaciones,
    doc_entrada
FROM tb_entrada
WHERE id_entrada = :id
LIMIT 1
";
$q = $pdo->prepare($sql_cabecera);
$q->execute(['id' => $id_entrada]);
$entrada = $q->fetch(PDO::FETCH_ASSOC);

if (!$entrada) {
    header('Location: gestion.php');
    exit;
}

/* ============================
   DETALLE PRODUCTOS (CORRECTO)
============================ */
$sql_detalle = "
SELECT
    i.codigo,
    i.descripcion,
    COALESCE(c.nombre_categoria,'—') AS categoria,
    v.color,

    CASE 
        WHEN i.tipo_liquido = 'GRANEL' THEN 'VOLUMEN'
        ELSE 'UNIDAD'
    END AS tipo_control,

    d.cantidad,

    COALESCE(d.factor_conversion,1) AS factor_conversion,

    d.cantidad_real,

    d.costo_unitario,

    (d.cantidad_real * d.costo_unitario) AS subtotal

FROM tb_entrada_d d
INNER JOIN tb_variantes v   ON v.id_variante = d.id_variante
INNER JOIN tb_inventario i ON i.id_producto = v.id_producto
LEFT JOIN tb_categoria c   ON c.id_categoria = i.id_categoria
WHERE d.id_entrada = :id
ORDER BY i.descripcion
";



$q = $pdo->prepare($sql_detalle);
$q->execute(['id' => $id_entrada]);
$detalle = $q->fetchAll(PDO::FETCH_ASSOC);

include('../layout/parte1.php');
?>

<div class="content-wrapper">

    <section class="content-header mb-3">
        <h1 class="text-primary">
            <i class="fas fa-eye"></i> Detalle de Entrada
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
                    <label>Fecha Emisión</label>
                    <input class="form-control" value="<?= $entrada['fecha_emision'] ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>Fecha Ingreso</label>
                    <input class="form-control" value="<?= $entrada['fecha_ingreso'] ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>Documento</label>
                    <input class="form-control" value="<?= strtoupper($entrada['tipo_factura']) ?>" readonly>
                </div>

                <div class="col-md-3">
                    <label>N° Documento</label>
                    <input class="form-control" value="<?= $entrada['nro_factura'] ?>" readonly>
                </div>

                <div class="col-md-12 mt-3">
                    <label>Observaciones</label>
                    <textarea class="form-control" readonly><?= $entrada['observaciones'] ?></textarea>
                </div>
                <?php if (!empty($entrada['doc_entrada'])): ?>
                    <div class="col-md-12 mt-2">
                        <label>Archivo de Entrada</label><br>
                        <a href="<?= $URL . '/inventario/docs_entrada/' . urlencode($entrada['doc_entrada']) ?>"
                           target="_blank"
                           class="btn btn-info btn-sm">
                            <i class="fas fa-file-alt"></i> Ver / Descargar
                        </a>
                    </div>
                <?php endif; ?>


            </div>
        </div>

        <!-- DETALLE -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Detalle de Productos
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-sm text-center">
                    <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Color</th>
                        <th>Control</th>
                        <th>Cantidad</th>
                        <th>Factor</th>
                        <th>Cant. Real</th>
                        <th>Costo U.</th>
                        <th>Subtotal</th>

                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    $total = 0;
                    if ($detalle):
                        foreach ($detalle as $d):
                            $total += $d['subtotal'];
                            ?>
                            <tr>
                                <td><?= $d['codigo'] ?></td>
                                <td class="text-left"><?= $d['descripcion'] ?></td>
                                <td><?= $d['categoria'] ?></td>
                                <td><?= ucfirst($d['color']) ?></td>
                                <td><?= $d['tipo_control'] ?></td>
                                <td><?= number_format($d['cantidad'],2) ?></td>
                                <td><?= number_format($d['factor_conversion'],2) ?></td>
                                <td><?= number_format($d['cantidad_real'],2) ?></td>
                                <td><?= number_format($d['costo_unitario'],2) ?></td>

                                <td><strong><?= number_format($d['subtotal'],2) ?></strong></td>
                            </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="10">No hay productos registrados</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>

                    <tfoot>
                    <tr>
                        <th colspan="8" class="text-right">TOTAL</th>
                        <th><?= number_format($total,2) ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <a href="gestion.php" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-left"></i> Volver
        </a>

    </section>
</div>

<?php include('../layout/parte2.php'); ?>
