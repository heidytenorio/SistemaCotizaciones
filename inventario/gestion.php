<?php
include('../app/config.php');
include('../layout/sesion.php');

/* ============================
   CONSULTA ENTRADAS CORREGIDA
============================ */
$sql_entradas = "
SELECT
    e.id_entrada,
    e.fecha_ingreso,
    e.nro_factura,
    e.doc_entrada,

    -- Almacén físico (si hay varios, muestra uno)
    MIN(al.ciudad) AS ciudad_almacen,
    MIN(al.nombre) AS nombre_almacen,

    -- Proveedor (si hay varios productos)
    GROUP_CONCAT(DISTINCT pr.razon_social SEPARATOR ', ') AS proveedor,

    COUNT(DISTINCT d.id_entrada_detalle) AS total_items,
    SUM(d.cantidad) AS total_cantidad

FROM tb_entrada e

INNER JOIN tb_entrada_d d
        ON e.id_entrada = d.id_entrada

INNER JOIN tb_almacenn al
        ON d.id_almacen = al.id_almacen

INNER JOIN tb_variantes v
        ON d.id_variante = v.id_variante

INNER JOIN tb_inventario i
        ON v.id_producto = i.id_producto

LEFT JOIN tb_proveedor pr
        ON i.id_proveedor = pr.id_proveedor

GROUP BY
    e.id_entrada

ORDER BY e.fecha_ingreso DESC;




";

$query_entradas = $pdo->prepare($sql_entradas);
$query_entradas->execute();
$entradas = $query_entradas->fetchAll(PDO::FETCH_ASSOC);

/* ============================
   CONSULTA SALIDAS
============================ */
$sql_salidas = "
SELECT
    s.id_salida,
    s.fecha_salida,
    s.tipo_salida,
    s.nro_factura,
    s.doc_salida,

    MIN(al.ciudad) AS ciudad_almacen,
    MIN(al.nombre) AS nombre_almacen,

    GROUP_CONCAT(DISTINCT pr.razon_social SEPARATOR ', ') AS proveedor,

    COUNT(DISTINCT d.id_salida_d) AS total_items,
    SUM(d.cantidad) AS total_cantidad

FROM tb_salida s

INNER JOIN tb_salida_d d
        ON s.id_salida = d.id_salida

INNER JOIN tb_almacenn al
        ON d.id_almacen = al.id_almacen

INNER JOIN tb_variantes v
        ON d.id_variante = v.id_variante

INNER JOIN tb_inventario i
        ON v.id_producto = i.id_producto

LEFT JOIN tb_proveedor pr
        ON i.id_proveedor = pr.id_proveedor

GROUP BY
    s.id_salida

ORDER BY s.fecha_salida DESC;




";

$query_salidas = $pdo->prepare($sql_salidas);
$query_salidas->execute();
$salidas = $query_salidas->fetchAll(PDO::FETCH_ASSOC);

$sql_traslados = "
SELECT
    t.id_traslado,
    t.fecha_traslado,
    t.observacion,
    t.estado,

    -- Almacén origen
    ao.ciudad AS ciudad_origen,
    ao.nombre AS nombre_origen,

    -- Almacén destino
    ad.ciudad AS ciudad_destino,
    ad.nombre AS nombre_destino,

    COUNT(DISTINCT td.id_traslado_d) AS total_items,
    SUM(td.cantidad_real) AS total_cantidad

FROM tb_traslado t

INNER JOIN tb_traslado_d td ON t.id_traslado = td.id_traslado

INNER JOIN tb_almacenn ao ON t.id_almacen_origen = ao.id_almacen
INNER JOIN tb_almacenn ad ON t.id_almacen_destino = ad.id_almacen

GROUP BY t.id_traslado

ORDER BY t.fecha_traslado DESC
";
$query_traslados = $pdo->prepare($sql_traslados);
$query_traslados->execute();
$traslados = $query_traslados->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Operaciones</title>
    <?php include('../layout/parte1.php'); ?>
</head>

<body>

<div class="content-wrapper">

    <!-- HEADER -->
    <div class="content-header mb-4 bg-white rounded shadow-sm px-4 py-3 border-left-primary">
        <div class="d-flex justify-content-between align-items-center flex-wrap">

            <div>
                <h4 class="font-weight-bold text-dark mb-1">
                    <i class="fas fa-warehouse text-primary mr-2"></i>
                    Operaciones de Almacén
                </h4>
                <small class="text-muted">Gestión completa de movimientos de inventario</small>
            </div>

            <div class="mt-3 mt-md-0 d-flex flex-wrap">

                <a href="<?= $URL;?>/inventario/detallep.php"
                   class="btn btn-outline-primary btn-sm mr-2 mb-2">
                    <i class="fas fa-list-alt mr-1"></i> Detalle
                </a>

                <a href="<?= $URL;?>/inventario/create.php"
                   class="btn btn-outline-success btn-sm mr-2 mb-2">
                    <i class="fas fa-arrow-down mr-1"></i> Entrada
                </a>

                <a href="<?= $URL;?>/inventario/salida.php"
                   class="btn btn-outline-danger btn-sm mr-2 mb-2">
                    <i class="fas fa-arrow-up mr-1"></i> Salida
                </a>

                <a href="<?= $URL;?>/inventario/traslado.php"
                   class="btn btn-outline-warning btn-sm mb-2">
                    <i class="fas fa-exchange-alt mr-1"></i> Traslado
                </a>

            </div>

        </div>
    </div>



    <!-- CONTENIDO -->
    <div class="content">
        <div class="container-fluid">

            <!-- ================= ENTRADAS ================= -->
            <div class="card shadow-sm border-0 mb-4 rounded-lg">
            <div class="card-header bg-success text-white d-flex align-items-center">
                    <i class="fas fa-arrow-down fa-lg mr-2"></i>
                    <h3 class="card-title m-0 font-weight-bold">Entradas de Inventario</h3>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped table-hover text-center nowrap table-hidden" style="width:100%">

                            <thead class="bg-light text-dark">
                            <tr>
                            <tr>
                                <th>N° Entrada</th>
                                <th>Fecha</th>
                                <th>Almacén</th>
                                <th>N° Factura</th>
                                <th>Documento</th>
                                <th>Proveedor</th>
                                <th>Total Ítems</th>
                                <th>Total Cantidad</th>
                                <th>Acciones</th>
                            </tr>

                            <tr class="filters">

                                <th><input class="form-control form-control-sm text-center" placeholder="Nro"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Fecha"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Almacen"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Factura"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Documento"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Proveedor"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>


                            <tbody>
                            <?php foreach ($entradas as $row) { ?>
                                <tr>
                                    <td>
                                        <?= 'EN-' . str_pad($row['id_entrada'],5,'0',STR_PAD_LEFT); ?>
                                    </td>

                                    <td data-order="<?= $row['fecha_ingreso'] ?>">
                                        <?= date("d/m/Y", strtotime($row['fecha_ingreso'])) ?>
                                    </td>


                                    <td>
                                        <?= htmlspecialchars($row['ciudad_almacen'].'-'.$row['nombre_almacen']) ?>
                                    </td>


                                    <td><?= htmlspecialchars($row['nro_factura']); ?></td>

                                    <td>
                                        <?php
                                        if (!empty($row['doc_entrada'])) {

                                            $archivo = $row['doc_entrada'];
                                            $ruta = URL_DOCS_ENTRADA . '/' . rawurlencode($archivo);
                                            $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                                            // Ícono por defecto
                                            $icono = '<i class="fa fa-file-alt text-secondary fa-lg"></i>';

                                            if ($extension === 'pdf') {
                                                $icono = '<i class="fa fa-file-pdf text-danger fa-lg"></i>';
                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                $icono = '<i class="fa fa-file-word text-primary fa-lg"></i>';
                                            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                $icono = '<i class="fa fa-file-excel text-success fa-lg"></i>';
                                            }

                                            echo '<a href="' . $ruta . '" target="_blank" class="text-decoration-none">';
                                            echo $icono . ' Ver archivo';
                                            echo '</a>';

                                        } else {
                                            echo '<span class="text-muted">—</span>';
                                        }
                                        ?>
                                    </td>


                                    <td><?= htmlspecialchars($row['proveedor']); ?></td>

                                    <td>
                                        <span class="badge badge-info"><?= (int)$row['total_items']; ?></span>
                                    </td>

                                    <td>
                                        <span class="badge badge-success"><?= (int)$row['total_cantidad']; ?></span>
                                    </td>
                                    <td>
                                        <!-- VER DETALLE -->
                                        <a href="ver_entrada.php?id=<?= $row['id_entrada'] ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>


                                        <!-- ENTRADA -->
                                        <button class="btn btn-sm btn-danger"
                                                onclick="eliminarEntrada(<?= $row['id_entrada']; ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <script>
                                            function eliminarEntrada(id) {
                                                if (!confirm('¿Seguro que deseas eliminar esta entrada?')) return;
                                                window.location.href = "delete_entrada.php?id=" + id;
                                            }
                                        </script>

                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>

                        </table>
                    </div>

                    <script>

                        // Filtros individuales con debounce
                        let searchTimeout;
                        $('#example1 thead tr.filters input').on('input', function() {
                            clearTimeout(searchTimeout);
                            let self = this;
                            searchTimeout = setTimeout(() => {
                                let colIndex = $(self).closest('th').index();
                                table.column(colIndex).search(self.value).draw();
                            }, 400);
                        });

                        // Botón limpiar filtros
                        $('#clear-filters').on('click', function() {
                            $('#example1 thead tr.filters input').val('');
                            table.columns().search('').draw();
                        });

                    </script>

                </div>
            </div>



            <div class="card shadow-sm border-0 mb-4 rounded-lg">
            <div class="card-header bg-danger text-white d-flex align-items-center">
                    <i class="fas fa-arrow-up fa-lg mr-2"></i>
                    <h3 class="card-title m-0 font-weight-bold">Salidas de Inventario</h3>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example2"
                               class="table table-bordered table-striped table-hover text-center nowrap table-hidden"
                               style="width:100%">

                            <thead class="bg-light text-dark">
                            <tr>
                                <th>N° Salida</th>
                                <th>Fecha</th>
                                <th>Almacén</th>
                                <th>Tipo</th>
                                <th>N° Documento</th>
                                <th>Documento</th>
                                <th>Proveedor</th>
                                <th>Ítems</th>
                                <th>Cantidad</th>
                                <th>Acciones</th>
                            </tr>
                            <tr class="filters">
                                <th><input class="form-control form-control-sm text-center" placeholder="Nro"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Fecha"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Almacen"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Tipo"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="N° Documento"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Documento"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Proveedor"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($salidas as $row) { ?>
                                <tr>
                                    <td>
                                        <?= 'SA-' . str_pad($row['id_salida'],5,'0',STR_PAD_LEFT); ?>
                                    </td>

                                    <td data-order="<?= $row['fecha_salida'] ?>">
                                        <?= date("d/m/Y", strtotime($row['fecha_salida'])) ?>
                                    </td>


                                    <td>
                                        <?= htmlspecialchars($row['ciudad_almacen'].' - '.$row['nombre_almacen']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['tipo_salida']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($row['nro_factura']) ?: '<span class="text-muted">—</span>' ?>
                                    </td>

                                    <td>
                                        <?php if (!empty($row['doc_salida'])):
                                            $archivo = $row['doc_salida'];
                                            $ruta = URL_DOCS_SALIDA . '/' . rawurlencode($archivo);
                                            $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                                            $icono = '<i class="fa fa-file-alt text-secondary"></i>';
                                            if ($ext === 'pdf') $icono = '<i class="fa fa-file-pdf text-danger"></i>';
                                            elseif (in_array($ext, ['doc','docx'])) $icono = '<i class="fa fa-file-word text-primary"></i>';
                                            elseif (in_array($ext, ['xls','xlsx'])) $icono = '<i class="fa fa-file-excel text-success"></i>';
                                            ?>
                                            <a href="<?= $ruta ?>" target="_blank" class="text-decoration-none">
                                                <?= $icono ?> Ver archivo
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= htmlspecialchars($row['proveedor']) ?></td>

                                    <td>
                                        <span class="badge badge-info"><?= (int)$row['total_items'] ?></span>
                                    </td>

                                    <td>
                                        <span class="badge badge-danger"><?= (int)$row['total_cantidad'] ?></span>
                                    </td>

                                    <td>

                                        <!-- VER -->
                                        <a href="ver_salida.php?id=<?= $row['id_salida'] ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- EDITAR -->
                                        <a href="edit_salida.php?id=<?= $row['id_salida'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- ELIMINAR -->
                                        <button class="btn btn-sm btn-danger"
                                                onclick="eliminarSalida(<?= $row['id_salida']; ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                    </td>

                                    <script>
                                        function eliminarSalida(id) {
                                            if (!confirm('¿Seguro que deseas eliminar esta salida?')) return;
                                            window.location.href = "delete_salida.php?id=" + id;
                                        }
                                    </script>
                                </tr>
                            <?php } ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4 rounded-lg">

            <div class="card-header bg-warning text-white d-flex align-items-center">
                    <i class="fas fa-exchange-alt fa-lg mr-2"></i>
                    <h3 class="card-title m-0 font-weight-bold">Traslados de Inventario</h3>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example3"
                               class="table table-bordered table-striped table-hover text-center nowrap table-hidden"
                               style="width:100%">

                            <thead class="bg-light text-dark">
                            <tr>
                                <th>N° Traslado</th>
                                <th>Fecha</th>
                                <th>Almacén Origen</th>
                                <th>Almacén Destino</th>
                                <th>Observación</th>
                                <th>Estado</th>
                                <th>Ítems</th>
                                <th>Cantidad</th>
                                <th>Acciones</th>
                            </tr>
                            <tr class="filters">
                                <th><input class="form-control form-control-sm text-center" placeholder="Nro"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Fecha"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Origen"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Destino"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Observación"></th>
                                <th><input class="form-control form-control-sm text-center" placeholder="Estado"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($traslados as $row) { ?>
                                <tr>
                                    <td>
                                        <?= 'TR-' . str_pad($row['id_traslado'],5,'0',STR_PAD_LEFT); ?>
                                    </td>

                                    <td data-order="<?= $row['fecha_traslado'] ?>">
                                        <?= date("d/m/Y", strtotime($row['fecha_traslado'])) ?>
                                    </td>


                                    <td><?= htmlspecialchars($row['ciudad_origen'] . ' - ' . $row['nombre_origen']) ?></td>
                                    <td><?= htmlspecialchars($row['ciudad_destino'] . ' - ' . $row['nombre_destino']) ?></td>
                                    <td><?= htmlspecialchars($row['observacion']) ?: '<span class="text-muted">—</span>' ?></td>
                                    <td>
                                        <?php
                                        $estado = strtoupper($row['estado']);
                                        $badgeClass = 'badge-secondary';
                                        if ($estado === 'CONFIRMADO') $badgeClass = 'badge-success';
                                        elseif ($estado === 'PENDIENTE') $badgeClass = 'badge-warning';
                                        elseif ($estado === 'CANCELADO') $badgeClass = 'badge-danger';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= $estado ?></span>
                                    </td>
                                    <td><span class="badge badge-info"><?= (int)$row['total_items'] ?></span></td>
                                    <td><span class="badge badge-warning"><?= (int)$row['total_cantidad'] ?></span></td>
                                    <!-- EN LA TABLA DE TRASLADOS, fila de acciones -->
                                    <td>
                                        <a href="ver_traslado.php?id=<?= $row['id_traslado'] ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger"
                                                onclick="eliminarTraslado(<?= $row['id_traslado'] ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>

                                </tr>
                            <?php } ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            <script>
                function eliminarTraslado(id) {
                    if (!confirm('¿Seguro que deseas eliminar este traslado?')) return;
                    window.location.href = "delete_traslado.php?id=" + id;
                }


            </script>


        </div>
    </div>
</div>

<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>

<!-- ===============================
     DATATABLE
================================= -->

<script>
    $(document).ready(function() {

        let table = $('#example1').DataTable({
            pageLength: 5,
            responsive: true,
            autoWidth: false,
            fixedHeader: true,
            order: [[1, 'desc']],
            searching: true, // 👈 DESACTIVA SEARCH GLOBAL
            initComplete: function () {
                $('#example1').css('visibility', 'visible');
            },
            language: {
                emptyTable: "No hay información",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                paginate: {
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });


        let searchTimeout;
        $('#example1 thead tr.filters input').on('input', function () {
            clearTimeout(searchTimeout);
            let colIndex = $(this).parent().index();
            let value = this.value;

            searchTimeout = setTimeout(() => {
                table.column(colIndex).search(value).draw();
            }, 400);
        });

    });
    </script>
<script>
    $(document).ready(function () {

        let tableSalida = $('#example2').DataTable({
            pageLength: 5,
            responsive: true,
            autoWidth: false,
            fixedHeader: true,
            order: [[1, 'desc']],
            searching: true, // 👈 DESACTIVA SEARCH GLOBAL
            initComplete: function () {
                $('#example2').css('visibility', 'visible');
            },
            language: {
                emptyTable: "No hay información",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                paginate: {
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });


        let searchTimeoutSalida;
        $('#example2 thead tr.filters input').on('input', function () {
            clearTimeout(searchTimeoutSalida);
            let colIndex = $(this).parent().index();
            let value = this.value;

            searchTimeoutSalida = setTimeout(() => {
                tableSalida.column(colIndex).search(value).draw();
            }, 400);
        });

    });
</script>
<script>
    $(document).ready(function () {
        let tableTraslado = $('#example3').DataTable({
            pageLength: 5,
            responsive: true,
            autoWidth: false,
            fixedHeader: true,
            order: [[1, 'desc']],
            searching: true,
            initComplete: function () {
                $('#example3').css('visibility', 'visible');
            },
            language: {
                emptyTable: "No hay información",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                paginate: {
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });

        let searchTimeoutTraslado;
        $('#example3 thead tr.filters input').on('input', function () {
            clearTimeout(searchTimeoutTraslado);
            let colIndex = $(this).parent().index();
            let value = this.value;

            searchTimeoutTraslado = setTimeout(() => {
                tableTraslado.column(colIndex).search(value).draw();
            }, 400);
        });
    });

</script>
<script>
    function eliminarEntrada(id) {
        if (!confirm('¿Seguro que deseas eliminar esta entrada?')) return;

        window.location.href = "delete_entrada.php?id=" + id;
    }
</script>

<script>
    function eliminarSalida(id) {
        if (!confirm('¿Seguro que deseas eliminar esta salida?')) return;
        window.location.href = "delete_salida.php?id=" + id;
    }

</script>

<style>
    .border-left-primary {
        border-left: 5px solid #007bff;
    }

    .content-header .btn {
        transition: all 0.2s ease;
    }

    .content-header .btn:hover {
        transform: translateY(-1px);
    }


    .table-hidden {
        visibility: hidden;
    }

    /* Tabla más compacta */
    .table td,
    .table th {
        padding: 0.45rem;
        font-size: 0.85rem;
        vertical-align: middle;
    }
    /* Mostrar registros más pequeño */
    .dataTables_length label {
        font-size: 0.75rem;
    }

    .dataTables_length select {
        font-size: 0.75rem;
        padding: 2px 6px;
        height: auto;
    }
    /* Filtros de columnas más pequeños */
    thead tr.filters input {
        font-size: 0.75rem;
        padding: 3px;
        height: 28px;
    }
    /* Botones de acciones más compactos */
    .btn-sm {
        padding: 0.25rem 0.45rem;
        font-size: 0.75rem;
    }
    td:first-child {
        white-space: nowrap;
    }
    #example1 tbody tr:hover {
        background-color: rgba(40,167,69,0.05);
    }

    #example2 tbody tr:hover {
        background-color: rgba(220,53,69,0.05);
    }

</style>
</body>
</html>
