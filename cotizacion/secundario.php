<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/cotizacion/listado_de_cotizacion1.php');
?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        Cotizaciones
                        <img src="<?php echo $URL; ?>/public/images/a.png" alt="Icono" style="width: 220px; height: 60px; margin-left: 10px;">
<!--                        <a href="--><?php //echo $URL; ?><!--/cotizacion/create.php" class="btn btn-primary">-->
<!--                            <i class="fa fa-plus"></i> Agregar-->
<!--                        </a>-->
                    </h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header"></div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th><center>N° Coti.</center></th>
                                    <th><center>Fecha Emisión</center></th>
                                    <th><center>RUC Cliente</center></th>
                                    <th><center>Razón Social</center></th>
                                    <th><center>Subtotal</center></th>
                                    <th><center>IGV</center></th>
                                    <th><center>Total</center></th>
                                    <th><center>Acciones</center></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($ventas_datos as $ventas_dato): ?>
                                    <tr>
                                        <td><?php echo '100' . $ventas_dato['nro_venta']; ?></td>
                                        <td><?php echo date("d/m/Y", strtotime($ventas_dato['fecha_emi'])); ?></td>
                                        <td><?php echo $ventas_dato['ruc']; ?></td>
                                        <td class="truncate" title="<?php echo $ventas_dato['razon_social']; ?>">
                                            <?php echo $ventas_dato['razon_social']; ?>
                                        </td>
                                        <td>S/. <?php echo number_format($ventas_dato['sub_total'], 2); ?></td>
                                        <td>S/. <?php echo number_format($ventas_dato['igv'], 2); ?></td>
                                        <td>S/. <?php echo number_format($ventas_dato['precio_final'], 2); ?></td>
                                        <td>
                                            <center>
                                                <div class="btn-group">
                                                    <a href="scotizacion.php?nro_venta=<?php echo $ventas_dato['nro_venta']; ?>"
                                                       class="btn btn-primary btn-sm shadow-sm btn-visualizar">
                                                        <i class="fas fa-file-pdf mr-1"></i> Visualizar
                                                    </a>

                                                    <button class="btn btn-outline-primary btn-sm shadow-sm btn-envio"
                                                            data-toggle="modal"
                                                            data-target="#modal-create2"
                                                            data-nroventa="<?php echo $ventas_dato['nro_venta']; ?>"
                                                            data-envio="<?php echo trim($ventas_dato['envio'] ?? ''); ?>">
                                                        <i class="fas fa-shipping-fast"></i>
                                                    </button>

                                                </div>
                                            </center>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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

<script>
    $(function () {
        $("#example1").DataTable({
            "pageLength": 10,
            "order": [["0", "desc"]], // Usar índice, no nombre
            "paging": true,         // Asegura que haya paginación
            "ordering": false,       // Permite ordenar
            "info": true,           // Muestra el texto "Mostrando X a Y"
            language: {
                "emptyTable": "No hay información",
                "decimal": "",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(Filtrado de _MAX_ registros totales)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });
    });
</script>

<!-- ===================== MODAL ===================== -->
<div class="modal fade" id="modal-create2">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header" style="background-color: #1d3Cb6; color:white">
                <h4 class="modal-title">Envios / Pedido</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <div class="form-group">
                            <label>Agregar detalle de envío</label>
                            <textarea id="envio" class="form-control" rows="5"></textarea>
                        </div>
                        <input type="hidden" id="nro_venta">
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                <button type="button" class="btn btn-primary" id="btn_create2">Registrar</button>

                <div id="respuesta"></div>
            </div>

        </div>
    </div>
</div>


<script>

    // Guardar envío
    $('#btn_create2').click(function () {
        var envio = $('#envio').val();
        var nroventa = $('#nro_venta').val();

        $.get("../app/controllers/cotizacion/registro_pedido.php",
            { envio: envio, nro_venta: nroventa },
            function (respuesta) {
                window.location.href = "<?php echo $URL; ?>/cotizacion/secundario.php";
            }
        );
    });

    // Cargar datos al abrir modal
    $(document).on('click', '[data-target="#modal-create2"]', function () {
        var nroventa = $(this).data('nroventa');
        var envio = $(this).data('envio');

        $('#envio').val(envio ? envio : '');
        $('#nro_venta').val(nroventa);
    });

    // Limpiar al cerrar modal
    $('#modal-create2').on('hidden.bs.modal', function () {
        $('#envio').val('');
        $('#nro_venta').val('');
    });

</script>


<style>
    td.truncate {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }
    /* Estilo general */
    .btn-visualizar,
    .btn-envio {
        transition: all 0.25s ease;
        border-radius: 8px;     /* forma más moderna */
        font-weight: 500;
    }

    /* Botón "Visualizar" */
    .btn-visualizar {
        background: linear-gradient(135deg, #007bff, #0056d2);
        color: #fff !important;
    }

    .btn-visualizar:hover {
        background: linear-gradient(135deg, #0056d2, #0041a8);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 92, 255, 0.3);
    }

    /* Botón de envío */
    .btn-envio {
        border-color: #007bff;
        color: #007bff;
    }

    .btn-envio:hover {
        background: #007bff;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 92, 255, 0.3);
    }

    /* Iconos */
    .btn i {
        transition: transform 0.2s ease;
    }

    .btn:hover i {
        transform: scale(1.15);
    }

</style>


