<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../app/controllers/inventario/listado_almacen3.php');
include('../layout/parte1.php');


/* seguridad */
if (!isset($datos)) {
    $datos = [];
}
foreach ($datos as &$r) {

    $r['stock_actual'] = $r['stock_actual'] ?? 0;
    $r['costo_promedio'] = $r['costo_promedio'] ?? 0;
    $r['ultimo_costo'] = $r['ultimo_costo'] ?? 0;
    $r['inversion_actual'] = $r['inversion_actual'] ?? 0;

}
unset($r);
?>
<style>

    /* ===== HEADER ===== */

    .content-header h1{
        font-size: 1.6rem;
        font-weight: 600;
        color:#343a40;
    }


    /* ===== CARD ===== */

    .card-primary{
        border-radius:12px;
        border:none;
        box-shadow:0 4px 14px rgba(0,0,0,0.08);
    }



    /* ===== TABLA ===== */

    #example1{

        font-size:1.02rem;   /* antes 0.92 */
    }


    #example1 tbody td{

        font-size:1.02rem;

        vertical-align: middle;

    }



    /* encabezado un poco más grande */

    #example1 thead th{

        font-size:1.05rem;

    }

    #example1 thead th{

        background: linear-gradient(180deg,#ffffff,#f1f3f5);

        color:#495057;

        border:none;

        font-weight:600;

        text-align:center;
    }


    #example1 tbody tr{

        transition: all .15s ease;
    }


    #example1 tbody tr:hover{

        transform: scale(1.002);

        background:#f8f9fa;
    }


    /* ===== IMAGEN ===== */

    .img-producto{

        width:55px;

        height:55px;

        object-fit:contain;

        border-radius:10px;

        background:white;

        padding:4px;

        box-shadow:0 2px 6px rgba(0,0,0,0.1);

    }


    /* ===== BADGES ===== */

    .badge-stock{

        background:#e8f8f0;
        color:#198754;
        font-size:.85rem;
        padding:6px 10px;
        border-radius:8px;

    }


    .badge-variante{

        background:#e7f1ff;
        color:#0d6efd;
        font-weight:500;
        padding:6px 10px;
        border-radius:8px;

    }


    /* ===== COSTOS ===== */

    .text-costo{

        font-weight:600;

        color:#0d6efd;

    }


    .text-inversion{

        background:#fff3cd;
        color:#856404;

        padding:6px 10px;

        border-radius:8px;

    }


    /* ===== FILTROS ===== */

    .filters input{

        border-radius:8px;

        border:1px solid #dee2e6;

        text-align:center;

        font-size:.85rem;

    }


    /* ===== SOMBRA SUAVE ===== */

    .table{

        border-radius:10px;

        overflow:hidden;

    }

    /* ===== COLUMNA IMAGEN MAS GRANDE ===== */

    #example1 td:nth-child(1),
    #example1 th:nth-child(1){

        width: 90px;
        min-width: 90px;
    }


    /* imagen grande */

    #example1 td:nth-child(1) img{

        width:80px;
        height:80px;
        object-fit:contain;
        border-radius:10px;
    }



    /* ===== COLUMNA CODIGO MAS ESTRECHA ===== */

    #example1 td:nth-child(2),
    #example1 th:nth-child(2){

        width: 90px;
        max-width: 90px;
        min-width: 70px;

        white-space: nowrap;

    }



    /* badge codigo compacto */

    #example1 td:nth-child(2) .badge{

        font-size: 0.75rem;
        padding:4px 6px;

    }
</style>

<div class="content-wrapper">

    <div class="content-header mb-3">
        <div class="container-fluid d-flex justify-content-between">
            <h1>
                <i class="fas fa-warehouse"></i>
                Consultar Costos
            </h1>
        </div>
    </div>


    <div class="content">

        <div class="container-fluid">

            <div class="card card-outline card-primary">

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="example1"
                               class="table table-hover table-striped table-bordered table-sm">

                            <thead class="thead-dark">

                            <tr>

                                <th>Imagen</th>

                                <th>Código</th>

                                <th>Descripcion del Producto</th>

                                <th>Color</th>

                                <th>Proveedor</th>

                                <th class="text-center">Stock</th>

                                <th class="text-center">Costo Promedio</th>

                                <th class="text-center">Último Costo</th>

                                <th class="text-center">Última Compra</th>

                                <th class="text-center">Inversión</th>

                            </tr>


                            <tr class="filters">

                                <th></th>

                                <th><input class="form-control form-control-sm"></th>

                                <th><input class="form-control form-control-sm"></th>

                                <th><input class="form-control form-control-sm"></th>

                                <th><input class="form-control form-control-sm"></th>

                                <th><input class="form-control form-control-sm"></th>

                                <th><input class="form-control form-control-sm"></th>

                                <th><input class="form-control form-control-sm"></th>

                                <th><input class="form-control form-control-sm"></th>

                                <th><input class="form-control form-control-sm"></th>

                            </tr>

                            </thead>


                            <tbody>

                            <?php if (!empty($datos)): ?>

                                <?php foreach ($datos as $row): ?>

                                    <tr>

                                        <!-- IMAGEN -->
                                        <td class="text-center">

                                            <?php if (!empty($row['imagen'])): ?>

                                                <img src="<?= $URL ?>/inventario/img_pproductos/<?= htmlspecialchars(basename($row['imagen'])) ?>"

                                                     class="img-thumbnail">

                                            <?php else: ?>

                                                <img src="<?= $URL ?>/public/images/no-image.png"

                                                     class="img-thumbnail">

                                            <?php endif; ?>

                                        </td>


                                        <!-- CODIGO -->
                                        <td>

<span class="badge badge-light">

<?= htmlspecialchars($row['codigo'] ?? '-') ?>

</span>

                                        </td>


                                        <!-- PRODUCTO -->
                                        <td>

                                            <?= htmlspecialchars($row['descripcion'] ?? '-') ?>

                                        </td>


                                        <!-- VARIANTE -->
                                        <td>

<span class="badge badge-info">

<?= htmlspecialchars($row['variante'] ?? 'SIN VARIANTE') ?>

</span>

                                        </td>


                                        <!-- PROVEEDOR -->
                                        <td>

                                            <?= htmlspecialchars($row['proveedor'] ?? 'SIN PROVEEDOR') ?>

                                        </td>


                                        <!-- STOCK -->
                                        <td class="text-center">

<span class="badge badge-success">

<?= number_format((float)$row['stock_actual'],0) ?>

</span>

                                        </td>


                                        <!-- COSTO PROMEDIO -->
                                        <td class="text-center text-primary font-weight-bold">

                                            S/ <?= number_format((float)$row['costo_promedio'],2) ?>

                                        </td>


                                        <!-- ULTIMO COSTO -->
                                        <td class="text-center font-weight-bold">

                                            S/ <?= number_format((float)$row['ultimo_costo'],2) ?>

                                        </td>


                                        <!-- FECHA -->
                                        <td class="text-center text-muted">

                                            <?= !empty($row['fecha_ultima_compra'])
                                                ? date('d/m/Y', strtotime($row['fecha_ultima_compra']))
                                                : '-' ?>

                                        </td>


                                        <!-- INVERSION -->
                                        <td class="text-center">

<span class="badge badge-warning">

S/ <?= number_format((float)$row['inversion_actual'],2) ?>

</span>

                                        </td>


                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td colspan="10" class="text-center text-danger">

                                        No hay datos para mostrar

                                    </td>

                                </tr>

                            <?php endif; ?>

                            </tbody>


                        </table>


                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>


<script>

    $(function () {

        let table = $("#example1").DataTable({

            responsive: true,
            pageLength: 25,
            stateSave: true,
            orderCellsTop: true,
            fixedHeader: true,

            dom:
                "<'row'<'col-sm-6'l><'col-sm-6 text-right'>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",

            // 🔥 ACTIVAR ORDENAMIENTO CONTROLADO

            columnDefs: [

                { orderable: false, targets: 0 }, // imagen no ordenar

                { orderable: true, targets: 1 }, // codigo

                { orderable: true, targets: 2 }, // descripcion

                { orderable: true, targets: 3 }, // color

                { orderable: true, targets: 4 }, // proveedor

                { orderable: true, targets: 5, type: "num" }, // stock

                { orderable: true, targets: 6, type: "num" }, // costo promedio

                { orderable: true, targets: 7, type: "num" }, // ultimo costo

                { orderable: true, targets: 8, type: "date" }, // fecha

                { orderable: true, targets: 9, type: "num" } // inversion

            ],

            order: [[2, 'asc']], // orden inicial por producto

            language: {

                lengthMenu: "Mostrar _MENU_ registros",
                emptyTable: "No hay datos",
                info: "Mostrando _START_ a _END_ de _TOTAL_",

                paginate: {
                    next: "Siguiente",
                    previous: "Anterior"
                }

            },

            initComplete: function () {

                let api = this.api();

                api.columns().every(function (i) {

                    let column = this;

                    $('input',
                        $('#example1 thead tr.filters th').eq(i)
                    ).on('keyup change',
                        function () {

                            column.search(this.value).draw();

                        });

                });

            }

        });

    });

</script>