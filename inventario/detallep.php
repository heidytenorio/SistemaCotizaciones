<?php

include('../app/config.php');
include('../layout/sesion.php');

$nombre_producto = $_GET['nombre'] ?? '';
$movimientos = [];

if (!empty($nombre_producto)) {

    $sql = "

    SELECT 
        tipo_movimiento,
        id_movimiento,
        fecha,
        almacen,
        color,
        cantidad,
        factor_conversion
    FROM (

        /* ================= ENTRADAS ================= */
        SELECT 
            'ENTRADA' AS tipo_movimiento,
            e.id_entrada AS id_movimiento,
            e.fecha_ingreso AS fecha,
            al.nombre AS almacen,
            v.color AS color,
            d.cantidad_real AS cantidad,
            d.factor_conversion AS factor_conversion
        FROM tb_entrada_d d
        INNER JOIN tb_entrada e 
            ON e.id_entrada = d.id_entrada
        INNER JOIN tb_variantes v 
            ON v.id_variante = d.id_variante
        INNER JOIN tb_inventario i 
            ON i.id_producto = v.id_producto
        INNER JOIN tb_almacenn al 
            ON al.id_almacen = d.id_almacen
        WHERE i.descripcion LIKE ?

        UNION ALL

        /* ================= SALIDAS ================= */
        SELECT 
            'SALIDA' AS tipo_movimiento,
            s.id_salida AS id_movimiento,
            s.fecha_salida AS fecha,
            al.nombre AS almacen,
            v.color AS color,
            -d.cantidad_real AS cantidad,
            d.factor_conversion AS factor_conversion
        FROM tb_salida_d d
        INNER JOIN tb_salida s 
            ON s.id_salida = d.id_salida
        INNER JOIN tb_variantes v 
            ON v.id_variante = d.id_variante
        INNER JOIN tb_inventario i 
            ON i.id_producto = v.id_producto
        INNER JOIN tb_almacenn al 
            ON al.id_almacen = d.id_almacen
        WHERE i.descripcion LIKE ?

        UNION ALL

        /* ============== TRASLADO SALIDA ============== */
        SELECT
            'TRASLADO SALIDA' AS tipo_movimiento,
            t.id_traslado AS id_movimiento,
            t.fecha_traslado AS fecha,
            ao.nombre AS almacen,
            v.color AS color,
            -td.cantidad_real AS cantidad,
            td.factor_conversion AS factor_conversion
        FROM tb_traslado_d td
        INNER JOIN tb_traslado t 
            ON t.id_traslado = td.id_traslado
        INNER JOIN tb_variantes v 
            ON v.id_variante = td.id_variante
        INNER JOIN tb_inventario i 
            ON i.id_producto = v.id_producto
        INNER JOIN tb_almacenn ao 
            ON ao.id_almacen = t.id_almacen_origen
        WHERE i.descripcion LIKE ?

        UNION ALL

        /* ============== TRASLADO ENTRADA ============== */
        SELECT
            'TRASLADO ENTRADA' AS tipo_movimiento,
            t.id_traslado AS id_movimiento,
            t.fecha_traslado AS fecha,
            ad.nombre AS almacen,
            v.color AS color,
            td.cantidad_real AS cantidad,
            td.factor_conversion AS factor_conversion
        FROM tb_traslado_d td
        INNER JOIN tb_traslado t 
            ON t.id_traslado = td.id_traslado
        INNER JOIN tb_variantes v 
            ON v.id_variante = td.id_variante
        INNER JOIN tb_inventario i 
            ON i.id_producto = v.id_producto
        INNER JOIN tb_almacenn ad 
            ON ad.id_almacen = t.id_almacen_destino
        WHERE i.descripcion LIKE ?

    ) movimientos

    ORDER BY fecha DESC, id_movimiento DESC

    ";

    $stmt = $pdo->prepare($sql);

    $like = "%{$nombre_producto}%";

    $stmt->execute([
        $like,
        $like,
        $like,
        $like
    ]);

    $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include('../layout/parte1.php');

?>

    <div class="content-wrapper">

        <section class="content-header mb-3 d-flex justify-content-between align-items-center">

            <h1 class="text-primary font-weight-bold">
                <i class="fas fa-search"></i>
                Movimientos por Producto
            </h1>

            <a href="<?= $URL ?>/inventario/gestion.php"
               class="btn btn-secondary">

                <i class="fas fa-arrow-left"></i>
                Volver

            </a>

        </section>

        <section class="content">

            <!-- ================= BUSCADOR ================= -->
            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <form method="GET">

                        <div class="input-group input-group-lg">

                            <input
                                    type="text"
                                    name="nombre"
                                    class="form-control"
                                    placeholder="Buscar producto..."
                                    value="<?= htmlspecialchars($nombre_producto, ENT_QUOTES, 'UTF-8') ?>"
                                    autocomplete="off"
                            >

                            <div class="input-group-append">

                                <button
                                        type="submit"
                                        class="btn btn-primary">

                                    <i class="fas fa-search"></i>
                                    Buscar

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            <?php if (!empty($nombre_producto)): ?>

                <div class="card shadow">

                    <div class="card-header bg-dark text-white">

                        Resultados para:

                        <strong>
                            <?= htmlspecialchars($nombre_producto, ENT_QUOTES, 'UTF-8') ?>
                        </strong>

                    </div>

                    <div class="card-body table-responsive">

                        <table class="table table-bordered table-hover text-center">

                            <thead class="bg-light">

                            <tr>

                                <th>Tipo</th>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Almacén</th>
                                <th>Color</th>
                                <th>Cantidad</th>
                                <th>Factor</th>

                            </tr>

                            </thead>

                            <tbody>

                            <?php if (!empty($movimientos)): ?>

                                <?php foreach ($movimientos as $m): ?>

                                    <tr>

                                        <!-- TIPO DE MOVIMIENTO -->
                                        <td>

                                            <?php

                                            $tipo = $m['tipo_movimiento'];

                                            if ($tipo === 'ENTRADA') {

                                                echo "<span class='badge badge-success'>
                                                        ENTRADA
                                                      </span>";

                                            } elseif ($tipo === 'SALIDA') {

                                                echo "<span class='badge badge-danger'>
                                                        SALIDA
                                                      </span>";

                                            } elseif ($tipo === 'TRASLADO SALIDA') {

                                                echo "<span class='badge badge-warning'>
                                                        TRASLADO SALIDA
                                                      </span>";

                                            } else {

                                                echo "<span class='badge badge-info'>
                                                        TRASLADO ENTRADA
                                                      </span>";

                                            }

                                            ?>

                                        </td>

                                        <!-- ID -->
                                        <td>

                                            <strong>
                                                #<?= (int) $m['id_movimiento'] ?>
                                            </strong>

                                        </td>

                                        <!-- FECHA -->
                                        <td>

                                            <?php if (!empty($m['fecha'])): ?>

                                                <?= date(
                                                    'd/m/Y',
                                                    strtotime($m['fecha'])
                                                ) ?>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    —
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <!-- ALMACÉN -->
                                        <td>

                                            <?= htmlspecialchars(
                                                $m['almacen'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>

                                        <!-- COLOR -->
                                        <td>

                                            <?php if (!empty($m['color'])): ?>

                                                <span class="badge badge-secondary">

                                                    <?= htmlspecialchars(
                                                        $m['color'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </span>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    Sin color
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <!-- CANTIDAD -->
                                        <td>

                                            <?php

                                            $cantidad = (float) $m['cantidad'];

                                            if ($cantidad > 0) {

                                                echo "
                                                    <span class='text-success font-weight-bold'>
                                                        +" . number_format($cantidad, 2) . "
                                                    </span>
                                                ";

                                            } elseif ($cantidad < 0) {

                                                echo "
                                                    <span class='text-danger font-weight-bold'>
                                                        " . number_format($cantidad, 2) . "
                                                    </span>
                                                ";

                                            } else {

                                                echo "
                                                    <span class='text-muted font-weight-bold'>
                                                        " . number_format($cantidad, 2) . "
                                                    </span>
                                                ";

                                            }

                                            ?>

                                        </td>

                                        <!-- FACTOR DE CONVERSIÓN -->
                                        <td>

                                            <?php if (
                                                isset($m['factor_conversion']) &&
                                                $m['factor_conversion'] !== '' &&
                                                $m['factor_conversion'] !== null
                                            ): ?>

                                                <span class="badge badge-info">

                                                    x<?= htmlspecialchars(
                                                        $m['factor_conversion'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </span>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    —
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td colspan="7" class="text-muted py-4">

                                        <i class="fas fa-info-circle"></i>
                                        No se encontraron movimientos para este producto.

                                    </td>

                                </tr>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            <?php endif; ?>

        </section>

    </div>

<?php include('../layout/parte2.php'); ?>