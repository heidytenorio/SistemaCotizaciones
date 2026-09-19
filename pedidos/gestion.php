<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/pedidos/listado_de_pedidos.php');
?>
<?php
$sql_diasregion = "SELECT id, region FROM tb_diasregion ORDER BY region ASC";
$query_diasregion = $pdo->prepare($sql_diasregion);
$query_diasregion->execute();
$diasregion_datos = $query_diasregion->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Lista de Pedidos
                    </h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline  card-primary">
                        <div class="card-header">
                            <h3 class="card-title">SECCION DE ALMACEN</h3>
                        </div>

                        <div class="card-body" style="display: block;">

                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th><center>N° Coti. </center></th>
                                    <th><center>Vence</center></th>
                                    <th><center>RUC Razon Social</center></th>
                                    <th><center>Responsable</center></th>
                                    <th><center>Descripcion del pedido</center></th>
                                    <th><center>Estado</center></th>
                                    <th><center>Emitido</center></th>
                                    <th><center>Pago</center></th>
                                    <th><center>Acciones</center></th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($pedidos_datos as $pedidos_dato) {
                                    $id_pedido =$pedidos_dato['id_pedido'];
                                    $nro_coti =$pedidos_dato['nro_coti'];
                                    $fecha_venci=$pedidos_dato['fecha_venci'];
                                    $razon_social=$pedidos_dato['razon_social'];
                                    $responsable=$pedidos_dato['responsable'];
                                    $descripcion=$pedidos_dato['descripcion'];
                                    $estado=$pedidos_dato['estado'];
                                    $fecha_emi=$pedidos_dato['fecha_emi'];
                                    $estado_pago=$pedidos_dato['estado_pago'];
                                    $id_region = $pedidos_dato['id_region'] ?? null;

                                    ?>
                                    <tr>
                                        <td><?php echo $pedidos_dato['nro_coti'];?></td>
                                        <td><?php echo date("d/m/Y", strtotime($pedidos_dato['fecha_venci'])); ?></td>
                                        <td class="col-razon"><?php echo $pedidos_dato['razon_social']; ?></td>
                                        <td><?php echo $pedidos_dato['responsable'];?></td>
                                        <td>
                                            <?php
                                            $archivo = $pedidos_dato['descripcion']; // nombre del archivo
                                            $ruta = $URL . "/pedidos/docs_pedidoss/" . $archivo;
                                            $extension = pathinfo($archivo, PATHINFO_EXTENSION);

                                            // Puedes personalizar íconos según el tipo
                                            $icono = '<i class="fa fa-file-alt text-secondary fa-lg"></i>'; // por defecto

                                            if ($extension == 'pdf') {
                                                $icono = '<i class="fa fa-file-pdf text-danger fa-lg"></i>';
                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                $icono = '<i class="fa fa-file-word text-primary fa-lg"></i>';
                                            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                $icono = '<i class="fa fa-file-excel text-success fa-lg"></i>';
                                            }

                                            echo '<a href="' . $ruta . '" target="_blank">' . $icono . ' Ver Archivo</a>';
                                            ?>
                                        </td>

                                        <?php
                                        $estadoOriginal = $pedidos_dato['estado'];
                                        $estado = strtolower(trim($estadoOriginal));
                                        $claseEstado = '';

                                        if ($estado == 'subido') {
                                            $claseEstado = 'bg-success text-white text-center';
                                        } elseif ($estado == 'en espera') {
                                            $claseEstado = 'bg-danger text-white text-center';
                                        } elseif ($estado == 'despachado') {
                                            $claseEstado = 'bg-primary text-white text-center';
                                        } elseif ($estado == 'sale de lima') {
                                            $claseEstado = 'bg-warning text-white text-center';
                                        } elseif ($estado == 'preparado') {
                                            $claseEstado = 'bg-info text-white text-center';
                                        } else {
                                            $claseEstado = 'bg-secondary text-white text-center';
                                        }
                                        ?>
                                        <td class="<?php echo $claseEstado; ?>">
                                            <?php echo strtoupper($estadoOriginal); ?>
                                        </td>
                                        <td><?php echo date("d/m/Y", strtotime($pedidos_dato['fecha_emi'])); ?></td>
                                        <?php
                                        $pagoOriginal = $pedidos_dato['estado_pago'];
                                        $pago = strtolower(trim($pagoOriginal));
                                        $badgeClass = '';

                                        if ($pago == 'pendiente') {
                                            $badgeClass = 'badge badge-pill badge-light border border-dark text-dark';
                                        } elseif ($pago == 'parcial') {
                                            $badgeClass = 'badge badge-pill badge-secondary';
                                        } elseif ($pago == 'pagado') {
                                            $badgeClass = 'badge badge-pill badge-dark';
                                        } else {
                                            $badgeClass = 'badge badge-pill badge-light';
                                        }
                                        ?>

                                        <td>
    <span class="<?php echo $badgeClass; ?>">
        <?php echo strtoupper($pagoOriginal); ?>
    </span>
                                        </td>
                                        <td>
                                            <center>
                                                <div class="btn-group">
                                                    <!-- Botones -->
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-block btn-outline-dark"
                                                                data-toggle="modal" data-target="#modal-update<?php echo $id_pedido; ?>">
                                                            <i class="fa fa-pencil-alt"></i>
                                                        </button>
                                                    </div>
                                                    <!-- Modal Editar Pedido -->
                                                    <div class="modal fade" id="modal-update<?php echo $id_pedido; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">

                                                                <form id="formUpdate<?php echo $id_pedido; ?>" action="../app/controllers/pedidos/update.php" method="post" enctype="multipart/form-data">

                                                                    <div class="modal-header bg-dark text-white">
                                                                        <h4 class="modal-title">Actualizar Pedido</h4>
                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>

                                                                    <div class="modal-body">

                                                                        <!-- OCULTOS IMPORTANTES -->
                                                                        <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">
                                                                        <input type="hidden" name="nro_coti" value="<?php echo $nro_coti; ?>">
                                                                        <input type="hidden" name="responsable" value="<?php echo $responsable; ?>">
                                                                        <input type="hidden" name="fecha_venci" value="<?php echo $fecha_venci; ?>">
                                                                        <input type="hidden" name="razon_social" value="<?php echo $razon_social; ?>">
                                                                        <input type="hidden" name="id_region" value="<?php echo $id_region; ?>">
                                                                        <input type="hidden" name="estado_pago" value="<?php echo $estado_pago; ?>">
                                                                        <input type="hidden" name="fecha_emi" value="<?php echo $fecha_emi; ?>">
                                                                        <input type="hidden" name="descripcion_text" value="<?php echo $descripcion; ?>">

                                                                        <!-- INFORMACIÓN DEL PEDIDO -->
                                                                        <div class="card card-outline card-secondary mb-3">
                                                                            <div class="card-header py-2">
                                                                                <h6 class="mb-0"><i class="fa fa-info-circle"></i> Información del Pedido</h6>
                                                                            </div>

                                                                            <div class="card-body">
                                                                                <div class="row">

                                                                                    <div class="col-md-4 mb-3">
                                                                                        <label>N° Cotización</label>
                                                                                        <input type="text" class="form-control" value="<?php echo $nro_coti; ?>" disabled>
                                                                                    </div>

                                                                                    <div class="col-md-4 mb-3">
                                                                                        <label>Responsable</label>
                                                                                        <input type="text" class="form-control" value="<?php echo $responsable; ?>" disabled>
                                                                                    </div>

                                                                                    <div class="col-md-4 mb-3">
                                                                                        <label>Vencimiento</label>
                                                                                        <input type="text" class="form-control" value="<?php echo date("d/m/Y", strtotime($fecha_venci)); ?>" disabled>
                                                                                    </div>

                                                                                    <div class="col-md-8 mb-3">
                                                                                        <label>Razón Social</label>
                                                                                        <input type="text" class="form-control" value="<?php echo $razon_social; ?>" disabled>
                                                                                    </div>

                                                                                    <div class="col-md-4 mb-3">
                                                                                        <label>Región</label>
                                                                                        <select class="form-control" disabled>
                                                                                            <option value="">SIN REGIÓN</option>
                                                                                            <?php foreach ($diasregion_datos as $region_dato) { ?>
                                                                                                <option value="<?php echo $region_dato['id']; ?>"
                                                                                                    <?php echo ($id_region == $region_dato['id']) ? 'selected' : ''; ?>>
                                                                                                    <?php echo $region_dato['region']; ?>
                                                                                                </option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- ARCHIVO DEL PEDIDO SOLO VISUAL -->
                                                                        <div class="card card-outline card-warning mb-3">
                                                                            <div class="card-header py-2">
                                                                                <h6 class="mb-0"><i class="fa fa-file"></i> Archivo del Pedido</h6>
                                                                            </div>

                                                                            <div class="card-body">
                                                                                <label>Archivo cargado</label><br>

                                                                                <?php if (!empty($descripcion)) { ?>
                                                                                    <a href="<?php echo $URL . '/pedidos/docs_pedidoss/' . $descripcion; ?>" target="_blank" class="btn btn-outline-dark btn-sm">
                                                                                        <i class="fa fa-eye"></i> Ver archivo
                                                                                    </a>
                                                                                <?php } else { ?>
                                                                                    <span class="text-muted">Sin archivo cargado</span>
                                                                                <?php } ?>

                                                                                <small class="form-text text-muted">
                                                                                    Este archivo solo se puede visualizar. No se puede modificar desde esta sección.
                                                                                </small>
                                                                            </div>
                                                                        </div>

                                                                        <!-- ACTUALIZAR ESTADO -->
                                                                        <div class="card card-outline card-primary mb-3">
                                                                            <div class="card-header py-2">
                                                                                <h6 class="mb-0"><i class="fa fa-edit"></i> Actualizar Estado</h6>
                                                                            </div>

                                                                            <div class="card-body">
                                                                                <div class="row">

                                                                                    <div class="col-md-6 mb-3">
                                                                                        <label>Estado de Almacén</label>
                                                                                        <select name="estado" class="form-control" required>
                                                                                            <option value="Subido" <?php echo ($estadoOriginal == 'Subido') ? 'selected' : ''; ?>>SUBIDO</option>
                                                                                            <option value="Preparado" <?php echo ($estadoOriginal == 'Preparado') ? 'selected' : ''; ?>>PREPARADO</option>
                                                                                            <option value="Despachado" <?php echo ($estadoOriginal == 'Despachado') ? 'selected' : ''; ?>>DESPACHADO</option>
                                                                                            <option value="Sale de lima" <?php echo ($estadoOriginal == 'Sale de lima') ? 'selected' : ''; ?>>SALE DE LIMA</option>
                                                                                            <option value="En espera" <?php echo ($estadoOriginal == 'En espera') ? 'selected' : ''; ?>>EN ESPERA</option>
                                                                                        </select>
                                                                                    </div>

                                                                                    <div class="col-md-6 mb-3">
                                                                                        <label>Estado de Pago</label>
                                                                                        <input type="text" class="form-control" value="<?php echo $estado_pago; ?>" disabled>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                        <button type="button" class="btn btn-primary" id="btn_update<?php echo $id_pedido; ?>">Actualizar</button>
                                                                    </div>

                                                                </form>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Script para enviar -->
                                                    <script>
                                                        $(document).ready(function () {
                                                            $('#btn_update<?php echo $id_pedido; ?>').click(function () {
                                                                $('#formUpdate<?php echo $id_pedido; ?>').submit();
                                                            });

                                                            $('#formUpdate<?php echo $id_pedido; ?>').on('submit', function (e) {
                                                                e.preventDefault();

                                                                var formData = new FormData(this);

                                                                $.ajax({
                                                                    url: '../app/controllers/pedidos/update.php',
                                                                    type: 'POST',
                                                                    data: formData,
                                                                    contentType: false,
                                                                    processData: false,
                                                                    success: function () {
                                                                        $('#modal-update<?php echo $id_pedido; ?>').modal('hide');
                                                                        location.reload();
                                                                    }
                                                                });
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                            </center>
                                        </td>
                                    </tr>

                                    <?php
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


<script>
    $(function () {
        $("#example1").DataTable({
            "pageLength": 20,
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
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>


<style>
    /* Centra el texto en todas las celdas excepto la de acciones */
    #example1 td {
        vertical-align: middle;
        text-align: center;
    }

    /* Ajuste de ancho automático para celdas */
    #example1 td:not(:nth-child(1)) {
        white-space: nowrap;
    }

    /* Colapsar y truncar texto en "Razon Social" */
    .col-razon {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: left;
    }
</style>
