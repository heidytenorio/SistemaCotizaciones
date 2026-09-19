<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../app/controllers/inventario/listado_de_almacen.php');
include('../layout/parte1.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Stock</title>


    <style>
        /* TABLA Y FILTROS */
        #example1 { border-collapse: separate !important; border-spacing: 0 6px; }
        #example1 thead th {
            background-color: #f1f3f5;
            color: #343a40;
            font-weight: 600;
            font-size: 0.95rem;
            text-align: center;
            vertical-align: middle;
            padding: 8px 10px;
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            border-radius: 4px 4px 0 0;
        }
        #example1 thead tr.filters input {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 5px 8px;
            font-size: 0.88rem;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
            transition: all 0.2s;
            text-align: center;
        }
        #example1 thead tr.filters input:focus {
            border-color: #80bdff;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }

        /* FILAS */
        #example1 tbody tr { background-color: #fff; border-radius: 4px; transition: all 0.2s; }
        #example1 tbody tr:hover { background-color: #f8f9fa; transform: translateY(-1px); }

        /* CELDAS */
        #example1 td { vertical-align: middle; font-size: 0.92rem; padding: 8px 10px; }
        td.truncate { max-width: 280px; text-align: center;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        td.truncates { max-width: 100px; text-align: center;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        td.truncatess { max-width: 120px; text-align: center;white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-align: center; font-family: monospace; }

        /* STOCK */
        .stock-indicator { display: inline-flex; align-items: center; justify-content: center; padding: 4px 8px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; min-width: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stock-zero { background: #f8d7da; color: #721c24; }
        .stock-low { background: #fff3cd; color: #856404; }
        .stock-ok { background: #d4edda; color: #155724; }

        #example1 th:nth-child(6),
        #example1 td:nth-child(6),
        #example1 th:nth-child(7),
        #example1 td:nth-child(7),
        #example1 th:nth-child(8),
        #example1 td:nth-child(8) {

            width: 115px;
            max-width: 115px;
            text-align: center;

        }
        /* ===== INPUTS DE FILTRO AJUSTADOS A COLUMNAS DE STOCK ===== */
        #example1 thead tr.filters th:nth-child(6) input,
        #example1 thead tr.filters th:nth-child(7) input,
        #example1 thead tr.filters th:nth-child(8) input {

            width: 100%;
            min-width: 95px;
            max-width: 110px;

        }
        /* CENTRAR COLOR Y STOCK */
        td > .color-tag,
        td > .stock-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* COLOR TAGS */
        .color-tag { padding: 4px 10px; border-radius: 12px; text-align: center; font-size: 0.85rem; font-weight: 600; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05); }
        .color-bg-rojo { background: #e74c3c; color: #fff; }
        .color-bg-azul { background: #0327b5; color: #fff; }
        .color-bg-verde { background: #17984e; color: #fff; }
        .color-bg-negro { background: #0d1012; color: #fff; }
        .color-bg-amarillo { background: #f1c40f; color:#000; }
        .color-bg-gris { background: #95a5a6; color:#fff; }
        .color-bg-celeste { background: #2b73a3; color:#fff; }
        .color-bg-rosado { background: #f63fde; color:#fff; }
        .color-bg-turquesa { background: #30a6a2; color:#fff; }
        .color-bg-anaranjado { background: #df622b; color:#fff; }
        .color-bg-morado { background: #732bdf; color:#fff; }
        .color-bg-marron { background: #3e2a0a; color:#fff; }
        .color-bg-beige { background: #faf2bd; color: #333; border: 1px solid #ccc; }
        .color-bg-blanco { background: #fff; color:#333; border:1px solid #ccc; }

        /* BOTÓN LIMPIAR */
        #clear-filters { border-radius: 6px; padding: 5px 12px; font-size: 0.88rem; transition: all 0.2s; }
        #clear-filters:hover { transform: translateY(-2px); box-shadow: 0 2px 5px rgba(0,0,0,0.15); }

        /* TITULO ESTILIZADO */
        .content-header h1 { font-size:2rem; font-weight:700; color:#17a2b8; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); display:flex; align-items:center; }
        .content-header h1 i { font-size:2.2rem; margin-right:10px; transition: all 0.2s ease-in-out; }
        .content-header h1:hover { transform: translateY(-1px); }

        /* ===== CATEGORÍA MÁS ESTRECHA ===== */
        #example1 th:nth-child(3),
        #example1 td:nth-child(3) {

            width: 12%;
            max-width: 120px;

        }


        /* ===== DESCRIPCIÓN MÁS ANCHA ===== */
        #example1 th:nth-child(4),
        #example1 td:nth-child(4) {

            width: 45%;
            text-align: left;

        }


        /* INPUT DESCRIPCIÓN */
        #example1 thead tr.filters th:nth-child(4) input {

            width: 100%;
            min-width: 300px;
            text-align: left;

        }
        /* ===== INPUT CATEGORÍA AJUSTADO ===== */
        #example1 thead tr.filters th:nth-child(3) input {

            width: 100%;
            min-width: 100px;
            max-width: 120px;

        }
        
        /* IMAGEN PRODUCTO */

        .img-producto{

            width:80px;
            height:80px;

            object-fit:contain;

            border-radius:8px;

            background:white;

            padding:3px;

            box-shadow:0 2px 5px rgba(0,0,0,0.15);

        }
        /* ===== MEJORAS VISUALES PRO ===== */

        /* Filas alternadas (mejor lectura) */
        #example1 tbody tr:nth-child(even) {
            background-color: #f9fbfc;
        }

        /* Hover más suave (sin movimiento) */
        #example1 tbody tr:hover {
            background-color: #eef3f7;
        }

        /* Mejor alineación del stock */
        .stock-indicator {
            min-width: 50px;
        }

        /* Imagen más limpia y profesional */
        .img-producto{
            width:80px;
            height:80px;
            object-fit:contain;
            border-radius:10px;
            background:white;
            padding:4px;
            border:1px solid #e0e0e0;
        }

        /* Descripción alineada correctamente (clave) */
        td.truncate {
            text-align: left;
        }

        /* Inputs más claros al enfocar */
        #example1 thead tr.filters input:focus {
            background-color: #ffffff;
        }

        /* Header fijo más limpio */
        #example1 thead th {
            position: sticky;
            top: 0;
            z-index: 2;
        }
    </style>

</head>
<body>

<div class="content-wrapper">

    <!-- HEADER -->
    <div class="content-header mb-3">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <h1>
                <i class="fas fa-warehouse"></i>
                Consultar Stock
            </h1>
        </div>
    </div>

    <!-- CONTENIDO -->
    <div class="content">
        <div class="container-fluid">

            <div class="card card-outline card-primary">
                <div class="card-body">

                    <button id="clear-filters" class="btn btn-secondary btn-sm mb-2">Limpiar filtros</button>

                    <table id="example1" class="table table-hover table-striped table-sm nowrap" style="width:100%">
                        <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Proveedor</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Color</th>
                            <th>Chiclayo</th>
                            <th>Lima </th>
                            <th>Lima</th>

                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th><input placeholder="Proveedor"></th>
                            <th><input placeholder="Categoría"></th>
                            <th><input placeholder="Descripción"></th>
                            <th><input placeholder="Color"></th>
                            <th><input placeholder="Chiclayo"></th>
                            <th><input placeholder="Yenire"></th>
                            <th><input placeholder="LC.GROUP"></th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $productos = [];

                        foreach ($pproductos_datos as $row) {
                            $key = $row['id_variante'];

                            if (!isset($productos[$key])) {
                                $productos[$key] = $row;

                                // inicializar almacenes (1,2,3)
                                $productos[$key]['almacenes'] = [
                                    1 => 0,
                                    2 => 0,
                                    3 => 0
                                ];
                            }

                            $id_almacen = $row['id_almacen'];
                            $productos[$key]['almacenes'][$id_almacen] = (int)$row['stock'];
                        }
                        ?>
                        <?php if (!empty($pproductos_datos)) :
                            foreach ($productos as $pproductos_dato) :

                                $stock_1 = $pproductos_dato['almacenes'][1] ?? 0;
                                $stock_2 = $pproductos_dato['almacenes'][2] ?? 0;
                                $stock_3 = $pproductos_dato['almacenes'][3] ?? 0;

                                $color = strtolower(trim($pproductos_dato['color'] ?? ''));


                                $color = strtolower(trim($pproductos_dato['color'] ?? ''));
                                ?>
                                <tr>

                                    <!-- IMAGEN -->
                                    <td class="text-center">

                                        <?php if (!empty($pproductos_dato['imagen'])): ?>

                                            <img src="<?= $URL ?>/inventario/img_pproductos/<?= htmlspecialchars(basename($pproductos_dato['imagen'])) ?>"
                                                 class="img-producto">

                                        <?php else: ?>

                                            <img src="<?= $URL ?>/public/images/no-image.png"
                                                 class="img-producto">

                                        <?php endif; ?>

                                    </td>


                                    <!-- PROVEEDOR -->
                                    <td class="truncates">

                                        <?= htmlspecialchars($pproductos_dato['proveedor'] ?? '—') ?>

                                    </td>
                                    <td class="truncates"><?= htmlspecialchars($pproductos_dato['categoria'] ?? '—') ?></td>
<!--                                    <td class="truncatess">--><?php //= htmlspecialchars($pproductos_dato['codigo']) ?><!--</td>-->
                                    <td class="truncate" title="<?= htmlspecialchars($pproductos_dato['descripcion']) ?>">
                                        <?= htmlspecialchars($pproductos_dato['descripcion']) ?>
                                    </td>
                                    <td>
                                        <span class="color-tag color-bg-<?= $color ?>">
                                            <?= htmlspecialchars($pproductos_dato['color'] ?? '—') ?>
                                        </span>
                                    </td>
                                    <td>
    <span class="stock-indicator <?= $stock_1 == 0 ? 'stock-zero' : 'stock-ok' ?>">
        <?= $stock_1 ?>
    </span>
                                    </td>

                                    <td>
    <span class="stock-indicator <?= $stock_2 == 0 ? 'stock-zero' : 'stock-ok' ?>">
        <?= $stock_2 ?>
    </span>
                                    </td>

                                    <td>
    <span class="stock-indicator <?= $stock_3 == 0 ? 'stock-zero' : 'stock-ok' ?>">
        <?= $stock_3 ?>
    </span>
                                    </td>

                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay productos registrados</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>

<script>
    $(function () {

        let table = $('#example1').DataTable({
            deferRender: true,
            processing: true,

            pageLength: 10,
            responsive: true,
            autoWidth: false,
            orderCellsTop: true,
            fixedHeader: true,
            dom: 'lrtip',


            stateSave: true,
            stateDuration: -1,

            language: {
                emptyTable: "No hay información",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                paginate: {
                    next: "Siguiente",
                    previous: "Anterior"
                }
            },

            /* 🔴 CLAVE: restaurar filtros al iniciar */
            initComplete: function () {
                let api = this.api();

                api.columns().every(function (index) {
                    let column = this;
                    let search = column.search();

                    if (search) {
                        $('#example1 thead tr.filters th:eq(' + index + ') input')
                            .val(search);
                    }
                });
            }
        });

        /* ==========================
           FILTROS POR COLUMNA
        ========================== */
        $('#example1 thead tr.filters input').on('keyup change clear', function () {
            let colIndex = $(this).closest('th').index();
            table.column(colIndex).search(this.value).draw();
        });

        /* ==========================
           LIMPIAR FILTROS (REAL)
        ========================== */
        $('#clear-filters').on('click', function () {
            table.state.clear();
            table.columns().search('');
            $('#example1 thead tr.filters input').val('');
            table.draw();
        });

    });
</script>


</body>
</html>
