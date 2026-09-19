<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/palmacen/listado_de_almacen.php');
?>

<div class="content-wrapper">
    <!-- Encabezado -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 d-flex align-items-center">
                    <h1 class="m-0">Productos</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-body">

                            <!-- Botón para limpiar filtros -->
                            <button id="clear-filters" class="btn btn-secondary btn-sm mb-2">Limpiar filtros</button>

                            <table id="example1" class="table table-bordered table-striped text-center nowrap table-hover" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Imagen</th>
                                    <th>Descripción</th>
                                    <th>Costo X Menor</th>
                                    <th>% X Menor</th>
                                    <th>Precio X Menor</th>
                                    <th>Marca</th>
                                    <th>Categoría</th>
                                    <th>Acciones</th>
                                </tr>
                                <!-- Filtros -->
                                <tr class="filters">
                                    <th><input type="text" class="form-control form-control-sm text-center" placeholder="Código" /></th>
                                    <th></th>
                                    <th><input type="text" class="form-control form-control-sm text-center" placeholder="Descripción" /></th>
                                    <th><input type="text" class="form-control form-control-sm text-center" placeholder="Costo Menor" /></th>
                                    <th><input type="text" class="form-control form-control-sm text-center" placeholder="% Menor" /></th>
                                    <th><input type="text" class="form-control form-control-sm text-center" placeholder="Precio Menor" /></th>
                                    <th><input type="text" class="form-control form-control-sm text-center" placeholder="Marca" /></th>
                                    <th><input type="text" class="form-control form-control-sm text-center" placeholder="Categoría" /></th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php
                                if (isset($pproductos_datos) && count($pproductos_datos) > 0) {
                                    foreach ($pproductos_datos as $pproductos_dato) {
                                        if (!isset($pproductos_dato['id_producto'])) continue;
                                        $moneda = $pproductos_dato['moneda'] ?? 'SOLES';
                                        $simbolo_moneda = ($moneda === 'SOLES') ? 'S/.' : '$ ';
                                        $id_producto = $pproductos_dato['id_producto'];
                                        $costo_menor = (float)$pproductos_dato['costo_minorista'];
                                        $porc_menor = (float)$pproductos_dato['porcentaje_minorista'];

                                        $precio_bd = (float)$pproductos_dato['precio_minorista']; // ya viene en soles si es dólar
                                        $cambio = (float)$pproductos_dato['cambio'];

                                        $ganancia_menor = round(($costo_menor * $porc_menor) / 100, 2);

// CONTROL DE MONEDA
                                        if ($moneda === 'DOLARES' && $cambio > 0) {
                                            // Costo y ganancia en dólares
                                            $simbolo_costo = '$';
                                            $simbolo_ganancia = '$';

                                            // Precio final en soles
                                            $precio_menor_calculado = round($precio_bd, 2);
                                            $simbolo_precio = 'S/.';
                                        } else {
                                            // Todo en soles
                                            $simbolo_costo = 'S/.';
                                            $simbolo_ganancia = 'S/.';
                                            $precio_menor_calculado = round($precio_bd, 2);
                                            $simbolo_precio = 'S/.';
                                        }

                                        ?>
                                        <tr class="align-middle text-center">
                                            <td><?php echo htmlspecialchars($pproductos_dato['codigo']); ?></td>
                                            <td>
                                                <img src="<?php echo $URL . "/palmacen/img_productos/" . htmlspecialchars($pproductos_dato['imagen']); ?>" width="80px" alt="producto">
                                            </td>


                                            <td class="truncate" title="<?php echo htmlspecialchars($pproductos_dato['descripcion']); ?>">
                                                <?php echo htmlspecialchars($pproductos_dato['descripcion']); ?>
                                            </td>
                                            <td><?php echo $simbolo_costo . ' ' . number_format($costo_menor, 2); ?></td>

                                            <td>
                                                <?php echo $porc_menor; ?> %
                                                <span class="resaltado">
      <?php echo $simbolo_ganancia . ' ' . number_format($ganancia_menor, 2); ?>

    </span>
                                            </td>

                                            <td>
                                                    <span class="resaltado_menor">
                                                      <?php echo $simbolo_precio . ' ' . number_format($precio_menor_calculado, 2); ?>

                                                    </span>
                                            </td>
                                            <td class="truncates"><?php echo htmlspecialchars($pproductos_dato['nombre_marca']); ?></td>
                                            <td class="truncates"><?php echo htmlspecialchars($pproductos_dato['nombre_categoria']); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="update.php?id=<?php echo $id_producto; ?>" class="btn btn-success btn-sm">
                                                        <i class="fa fa-pencil-alt"></i>Editar
                                                    </a>

                                                    <?php if ($rol_sesion === 'Administrador') { ?>
                                                        <a href="delete.php?id=<?php echo $id_producto; ?>" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="9">No hay productos registrados.</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include('../layout/mensajes.php'); ?>

<?php include('../layout/parte2.php'); ?>

<!-- SCRIPT DATATABLE -->
<script>
    $(document).ready(function() {
        let table = $('#example1').DataTable({
            pageLength: 12,
            responsive: true,
            autoWidth: false,
            orderCellsTop: true,
            fixedHeader: true,
            language: {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            buttons: [{
                extend: 'collection',
                text: 'Reportes',
                orientation: 'landscape',
                buttons: ['copy', 'pdf', 'csv', 'excel', 'print']
            },
                {
                    extend: 'colvis',
                    text: 'Visor de columnas'
                }
            ]
        });

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

        // Limpiar filtros
        $('#clear-filters').on('click', function() {
            $('#example1 thead tr.filters input').val('');
            table.columns().search('').draw();
        });
    });
</script>

<!-- ESTILOS -->
<style>
    td.truncate {
        max-width: 450px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }

    td.truncates {
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }

    .resaltado {
        background-color: #0f0f0f;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.85rem;
        margin-left: 4px;
        display: inline-block;
    }

    .resaltado_menor {
        background-color: #f39c12;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.85rem;
        margin-left: 4px;
        display: inline-block;
    }

    .filters input {
        width: 100%;
        font-size: 12px;
        padding: 2px 4px;
        text-align: center;
    }

    div.dataTables_filter {
        display: none;
    }

    .table-responsive {
        overflow-x: auto;
    }
    /* ===== TABLA GENERAL ===== */
    #example1 {
        font-size: 0.9rem;
    }

    #example1 thead th {
        background-color: #f4f6f9;
        color: #333;
        font-weight: 600;
        vertical-align: middle;
        border-bottom: 2px solid #dee2e6;
    }

    /* Hover elegante */
    #example1 tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease-in-out;
    }

    /* Centrado vertical perfecto */
    #example1 td {
        vertical-align: middle;
    }


    td.truncates {
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }

    /* ===== RESALTADOS ===== */
    .resaltado {
        background: linear-gradient(135deg, #111, #333);
        color: #fff;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8rem;
        margin-left: 6px;
        display: inline-block;
    }

    .resaltado_menor {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: #fff;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-block;
    }

    /* ===== FILTROS ===== */
    .filters input {
        width: 100%;
        font-size: 12px;
        padding: 4px 6px;
        text-align: center;
        border-radius: 4px;
    }

    /* Ocultar buscador global */
    div.dataTables_filter {
        display: none;
    }

    /* ===== BOTONES ACCIONES ===== */
    .btn-group .btn {
        padding: 4px 8px;
        font-size: 0.75rem;
    }

    .btn-group .btn i {
        margin-right: 3px;
    }

    /* ===== BOTÓN LIMPIAR ===== */
    #clear-filters {
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 0.8rem;
    }

    /* ===== RESPONSIVE ===== */
    .table-responsive {
        overflow-x: auto;
    }

</style>
