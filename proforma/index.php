<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/proforma/listado_de_proforma.php');
?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="display-5 fw-bold text-primary">
                        <i class="fa fa-file-invoice-dollar me-2"></i> Proformas y Cotizaciones
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
                            <input type="text" id="id_oferta" hidden>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th><center>N° Coti.</center></th>
                                    <th><center>Fecha Emisión</center></th>
                                    <th><center>RUC</center></th>
                                    <th><center>Razón Social</center></th>
                                    <th><center>Subtotal</center></th>
                                    <th><center>IGV</center></th>
                                    <th><center>Total</center></th>
                                    <th><center>Moneda</center></th>
                                    <th><center>Acciones</center></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($oferta_datos as $oferta_dato): ?>
                                    <tr>
                                        <td><?php echo '300' . $oferta_dato['nro_orden']; ?></td>
                                        <td><?php echo date("d/m/Y", strtotime($oferta_dato['fecha_emi'])); ?></td>

                                        <td><?php echo $oferta_dato['ruc']; ?></td>
                                        <td class="truncate" title="<?php echo $oferta_dato['razon_social']; ?>">
                                            <?php echo $oferta_dato['razon_social']; ?>
                                        </td>

                                        <td>S/. <?php echo number_format($oferta_dato['sub_total'], 2); ?></td>

                                        <td>S/. <?php echo number_format($oferta_dato['igv'], 2); ?></td>
                                        <td>S/. <?php echo number_format($oferta_dato['precio_final'], 2); ?></td>

                                        <td class="truncate" title="<?php echo $oferta_dato['moneda']; ?>">
                                            <?php echo $oferta_dato['moneda']; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="update.php?id=<?= $oferta_dato['nro_orden']; ?>" class="btn btn-dark btn-sm" title="Editar">
                                                    <i class="fa fa-pencil-alt me-1"></i> Editar
                                                </a>
                                                <a href="pproforma.php?nro_orden=<?= $oferta_dato['nro_orden']; ?>" class="btn btn-success btn-sm" title="Ver">
                                                    <i class="fa fa-eye me-1"></i> Ver
                                                </a>
                                            </div>
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

<style>
    td.truncate {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }
</style>


