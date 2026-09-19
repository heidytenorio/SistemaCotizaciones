<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../app/controllers/inventario/listado_almacen2.php');
include('../layout/parte1.php');
?>

<?php
/* ==================================================
   MÉTRICAS DEL DASHBOARD DE INVENTARIO
================================================== */

/* ---------- Productos con stock bajo ---------- */
$sql_stock_bajo = "
    SELECT i.descripcion, SUM(v.stock_actual) AS stock
    FROM tb_variantes v
    INNER JOIN tb_inventario i ON v.id_producto = i.id_producto
    GROUP BY i.descripcion
    HAVING stock <= 5
    ORDER BY stock ASC
";
$stock_bajo = $pdo->query($sql_stock_bajo)->fetchAll(PDO::FETCH_ASSOC);

/* ---------- Top 5 productos con mayor movimiento ---------- */
$sql_top = "
    SELECT i.descripcion, SUM(d.cantidad) AS total
    FROM tb_entrada_d d
    INNER JOIN tb_variantes v ON d.id_variante = v.id_variante
    INNER JOIN tb_inventario i ON v.id_producto = i.id_producto
    GROUP BY i.descripcion
    ORDER BY total DESC
    LIMIT 5
";
$top_productos = $pdo->query($sql_top)->fetchAll(PDO::FETCH_ASSOC);

/* ---------- Entradas y salidas por mes ---------- */
$sql_movimientos = "
    SELECT mes,
           SUM(entradas) AS entradas,
           SUM(salidas) AS salidas
    FROM (
        SELECT DATE_FORMAT(fecha_ingreso, '%Y-%m') AS mes, COUNT(*) AS entradas, 0 AS salidas
        FROM tb_entrada
        GROUP BY mes
        UNION ALL
        SELECT DATE_FORMAT(fecha_salida, '%Y-%m') AS mes, 0 AS entradas, COUNT(*) AS salidas
        FROM tb_salida
        GROUP BY mes
    ) t
    GROUP BY mes
    ORDER BY mes
";
$mov = $pdo->query($sql_movimientos)->fetchAll(PDO::FETCH_ASSOC);

/* ---------- Preparación de datos para gráficos ---------- */
$meses = [];
$entradas = [];
$salidas = [];

foreach ($mov as $m) {
    $meses[]    = $m['mes'];
    $entradas[] = (int)$m['entradas'];
    $salidas[]  = (int)$m['salidas'];
}

/* ---------- Métricas generales ---------- */

// Total de productos
$sql_total_productos = "SELECT COUNT(*) AS total_productos FROM tb_inventario";
$total_productos = $pdo->query($sql_total_productos)->fetch(PDO::FETCH_ASSOC)['total_productos'];

// Stock total
$sql_stock_total = "SELECT SUM(stock_actual) AS stock_total FROM tb_variantes";
$stock_total = (int)$pdo->query($sql_stock_total)->fetch(PDO::FETCH_ASSOC)['stock_total'];

// Total entradas
$sql_total_entradas = "SELECT COUNT(*) AS total_entradas FROM tb_entrada";
$total_entradas = $pdo->query($sql_total_entradas)->fetch(PDO::FETCH_ASSOC)['total_entradas'];

// Total salidas
$sql_total_salidas = "SELECT COUNT(*) AS total_salidas FROM tb_salida";
$total_salidas = $pdo->query($sql_total_salidas)->fetch(PDO::FETCH_ASSOC)['total_salidas'];

/* ---------- Stock por categoría ---------- */
$sql_stock_categoria = "
    SELECT c.id_categoria, c.nombre_categoria,
           COALESCE(SUM(v.stock_actual), 0) AS total_stock
    FROM tb_variantes v
    INNER JOIN tb_inventario i ON v.id_producto = i.id_producto
    INNER JOIN tb_categoria c ON i.id_categoria = c.id_categoria
";
$data_categoria = $pdo->query($sql_stock_categoria)->fetchAll(PDO::FETCH_ASSOC);

$cat_labels = [];
$cat_data = [];

foreach ($data_categoria as $row) {
    $cat_labels[] = $row['nombre_categoria'];
    $cat_data[]   = (int)$row['total_stock'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas de Inventario</title>
</head>

<body>
<div class="content-wrapper">

    <!-- HEADER -->
    <div class="content-header mb-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-warehouse"></i> Estadísticas de Inventario</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">

                    <!-- MÉTRICAS RÁPIDAS -->
                    <div class="row mb-4">

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3><?= $total_productos ?></h3>
                                    <p>Productos</p>
                                </div>
                                <div class="icon"><i class="fas fa-boxes"></i></div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= $stock_total ?></h3>
                                    <p>Stock Total</p>
                                </div>
                                <div class="icon"><i class="fas fa-layer-group"></i></div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= $total_entradas ?></h3>
                                    <p>Entradas</p>
                                </div>
                                <div class="icon"><i class="fas fa-arrow-down"></i></div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?= $total_salidas ?></h3>
                                    <p>Salidas</p>
                                </div>
                                <div class="icon"><i class="fas fa-arrow-up"></i></div>
                            </div>
                        </div>

                    </div>

                    <!-- FILTROS -->
                    <form method="GET" class="row mb-4">
                        <div class="col-md-3">
                            <select name="proveedor" class="form-control">
                                <option value="">Proveedor</option>
                                <?php foreach ($proveedores_datos as $p) { ?>
                                    <option value="<?= $p['id_proveedor'] ?>">
                                        <?= $p['razon_social'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="categoria" class="form-control">
                                <option value="">Categoría</option>
                                <?php foreach ($categorias_datos as $c) { ?>
                                    <option value="<?= $c['id_categoria'] ?>">
                                        <?= $c['nombre_categoria'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                        </div>
                    </form>

                    <!-- GRÁFICOS -->
                    <div class="row mb-4">

                        <div class="col-md-6">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Stock por Categoría</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="chartCategoria"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Entradas vs Salidas</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="chartMovimientos"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- TOP PRODUCTOS Y STOCK BAJO -->
                    <div class="row">

                        <!-- TOP PRODUCTOS -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary h-100">
                                <div class="card-header">
                                    <h3 class="card-title">🔥 Top 5 Productos con Mayor Movimiento</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <?php foreach ($top_productos as $t) { ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                                <span style="max-width:75%;word-break:break-word;">
                                                    <?= $t['descripcion'] ?>
                                                </span>
                                                <span class="badge badge-primary badge-pill">
                                                    <?= (int)$t['total'] ?>
                                                </span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- STOCK BAJO -->
                        <div class="col-md-6">
                            <div class="card card-outline card-danger h-100">
                                <div class="card-header">
                                    <h3 class="card-title">⚠ Productos con Stock Bajo</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered text-center mb-0">
                                        <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Stock</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($stock_bajo as $s) { ?>
                                            <tr>
                                                <td><?= $s['descripcion'] ?></td>
                                                <td class="text-danger font-weight-bold">
                                                    <?= (int)$s['stock'] ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    new Chart(document.getElementById('chartCategoria'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($cat_labels) ?>,
            datasets: [{
                label: 'Stock',
                data: <?= json_encode($cat_data) ?>,
                backgroundColor: '#17a2b8'
            }]
        }
    });
</script>

<script>
    new Chart(document.getElementById('chartMovimientos'), {
        type: 'line',
        data: {
            labels: <?= json_encode($meses) ?>,
            datasets: [
                {
                    label: 'Entradas',
                    data: <?= json_encode($entradas) ?>,
                    borderColor: '#28a745',
                    fill: false
                },
                {
                    label: 'Salidas',
                    data: <?= json_encode($salidas) ?>,
                    borderColor: '#dc3545',
                    fill: false
                }
            ]
        }
    });
</script>

</body>
</html>
