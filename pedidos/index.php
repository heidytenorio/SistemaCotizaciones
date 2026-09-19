<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/pedidos/listado_de_pedidos.php');
?>
<?php
$sql_diasregion = "SELECT id, region, dias FROM tb_diasregion ORDER BY region ASC";
$query_diasregion = $pdo->prepare($sql_diasregion);
$query_diasregion->execute();
$diasregion_datos = $query_diasregion->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Pedidos Registrados
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create">
                            <i class="fa fa-plus"></i> Agregar
                        </button>
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
                        <div class="card-header">
                            <h3 class="card-title">GESTION DE ALMACEN</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body" style="display: block;">

                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th><center>N° Coti.</center></th>
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
                                <?php foreach ($pedidos_datos as $pedidos_dato) {

                                    $id_pedido = $pedidos_dato['id_pedido'];
                                    $nro_coti = $pedidos_dato['nro_coti'];
                                    $fecha_venci = $pedidos_dato['fecha_venci'];
                                    $razon_social = $pedidos_dato['razon_social'];
                                    $responsable = $pedidos_dato['responsable'];
                                    $descripcion = $pedidos_dato['descripcion'];
                                    $estado_original = $pedidos_dato['estado'];
                                    $estado = strtolower($pedidos_dato['estado']);
                                    $fecha_emi = $pedidos_dato['fecha_emi'];
                                    $estado_pago = $pedidos_dato['estado_pago'];
                                    $flete = $pedidos_dato['flete'] ?? '';
                                    $id_region = $pedidos_dato['id_region'] ?? null;
                                    ?>
                                    <tr>
                                        <td><?php echo $nro_coti; ?></td>
                                        <td><?php echo date("d/m/Y", strtotime($fecha_venci)); ?></td>
                                        <td class="col-razon"><?php echo $razon_social; ?></td>
                                        <td><?php echo $responsable; ?></td>

                                        <td>
                                            <?php
                                            $archivo = $descripcion;
                                            $ruta = $URL . "/pedidos/docs_pedidoss/" . $archivo;
                                            $extension = pathinfo($archivo, PATHINFO_EXTENSION);

                                            $icono = '<i class="fa fa-file-alt text-secondary fa-lg"></i>';

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
                                        $claseEstado = '';

                                        if ($estado == 'subido') {
                                            $claseEstado = 'bg-success text-white text-center';
                                        } elseif ($estado == 'vencido') {
                                            $claseEstado = 'bg-danger text-white text-center';
                                        } elseif ($estado == 'despachado') {
                                            $claseEstado = 'bg-primary text-white text-center';
                                        } elseif ($estado == 'sale de lima') {
                                            $claseEstado = 'bg-warning text-white text-center';
                                        } else {
                                            $claseEstado = 'bg-secondary text-white text-center';
                                        }
                                        ?>

                                        <td class="<?php echo $claseEstado; ?>">
                                            <?php echo strtoupper($estado); ?>
                                        </td>

                                        <td><?php echo date("d/m/Y", strtotime($fecha_emi)); ?></td>

                                        <?php
                                        $pago = strtolower($estado_pago);
                                        $badgeClass = '';

                                        if ($pago == 'pendiente') {
                                            $badgeClass = 'badge badge-light border border-dark';
                                        } elseif ($pago == 'parcial') {
                                            $badgeClass = 'badge badge-secondary';
                                        } elseif ($pago == 'pagado') {
                                            $badgeClass = 'badge badge-dark';
                                        }
                                        ?>

                                        <td>
                                            <span class="<?php echo $badgeClass; ?>">
                                                <?php echo strtoupper($estado_pago); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <center>
                                                <div class="btn-group">

                                                    <button type="button" class="btn btn-outline-success btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#modal-update<?php echo $id_pedido; ?>">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>

                                                    <!-- Modal Editar Pedido -->
                                                    <div class="modal fade" id="modal-update<?php echo $id_pedido; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">

                                                                <form id="formUpdate<?php echo $id_pedido; ?>"
                                                                      action="../app/controllers/pedidos/update.php"
                                                                      method="post"
                                                                      enctype="multipart/form-data">

                                                                    <div class="modal-header bg-success text-white">
                                                                        <h4 class="modal-title">Editar Pedido</h4>
                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">

                                                                        <div class="row">

                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Nº de Cotización</label>
                                                                                <input type="number" name="nro_coti" class="form-control"
                                                                                       value="<?php echo $nro_coti; ?>" required>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Responsable</label>
                                                                                <select name="responsable" class="form-control" required>
                                                                                    <option value="Clientes" <?php echo ($responsable == 'Clientes') ? 'selected' : ''; ?>>CLIENTES</option>
                                                                                    <option value="Peru Compras" <?php echo ($responsable == 'Peru Compras') ? 'selected' : ''; ?>>PERU COMPRAS</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Fecha de Vencimiento</label>
                                                                                <input type="datetime-local" name="fecha_venci" class="form-control"
                                                                                       value="<?php echo $fecha_venci; ?>" required>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Fecha de Emisión</label>
                                                                                <input type="datetime-local" name="fecha_emi" class="form-control"
                                                                                       value="<?php echo $fecha_emi; ?>" required>
                                                                            </div>

                                                                            <div class="col-md-12 mb-3">
                                                                                <label>Razón Social</label>
                                                                                <input type="text" name="razon_social" class="form-control"
                                                                                       value="<?php echo $razon_social; ?>" required>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Estado</label>
                                                                                <select name="estado" class="form-control" required>
                                                                                    <option value="Subido" <?php echo ($estado == 'subido') ? 'selected' : ''; ?>>SUBIDO</option>
                                                                                    <option value="Preparado" <?php echo ($estado == 'preparado') ? 'selected' : ''; ?>>PREPARADO</option>
                                                                                    <option value="Vencido" <?php echo ($estado == 'vencido') ? 'selected' : ''; ?>>VENCIDO</option>
                                                                                    <option value="Despachado" <?php echo ($estado == 'despachado') ? 'selected' : ''; ?>>DESPACHADO</option>
                                                                                    <option value="Sale de lima" <?php echo ($estado == 'sale de lima') ? 'selected' : ''; ?>>SALE DE LIMA</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Estado de Pago</label>
                                                                                <select name="estado_pago" class="form-control" required>
                                                                                    <option value="PENDIENTE" <?php echo ($estado_pago == 'PENDIENTE') ? 'selected' : ''; ?>>PENDIENTE</option>
                                                                                    <option value="PARCIAL" <?php echo ($estado_pago == 'PARCIAL') ? 'selected' : ''; ?>>PARCIAL</option>
                                                                                    <option value="PAGADO" <?php echo ($estado_pago == 'PAGADO') ? 'selected' : ''; ?>>PAGADO</option>
                                                                                </select>
                                                                            </div>

                                                                            <!-- CAMPO FLETE -->
                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Flete</label>
                                                                                <select name="flete" class="form-control">
                                                                                    <option value="">SELECCIONAR</option>
                                                                                    <option value="si" <?php echo ($flete == 'si') ? 'selected' : ''; ?>>SI</option>
                                                                                    <option value="no" <?php echo ($flete == 'no') ? 'selected' : ''; ?>>NO</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-6 mb-3">
                                                                                <label>Región</label>
                                                                                <select name="id_region" class="form-control">
                                                                                    <option value="">SIN REGIÓN</option>
                                                                                    <?php foreach ($diasregion_datos as $region_dato) { ?>
                                                                                        <option value="<?php echo $region_dato['id']; ?>"
                                                                                            <?php echo ($id_region == $region_dato['id']) ? 'selected' : ''; ?>>
                                                                                            <?php echo $region_dato['region']; ?>
                                                                                        </option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-md-12 mb-3">
                                                                                <label>Documento del Pedido</label>
                                                                                <input type="file" name="descripcion" class="form-control"
                                                                                       id="descripcion<?php echo $id_pedido; ?>"
                                                                                       accept=".pdf,.doc,.docx,.xls,.xlsx">

                                                                                <input type="hidden" name="descripcion_text" value="<?php echo $descripcion; ?>">

                                                                                <?php if (!empty($descripcion)) {
                                                                                    $ruta_doc = $URL . "/pedidos/docs_pedidoss/" . $descripcion;
                                                                                    $ext = pathinfo($descripcion, PATHINFO_EXTENSION);
                                                                                    $icono = '<i class="fa fa-file text-secondary"></i>';

                                                                                    if ($ext === 'pdf') {
                                                                                        $icono = '<i class="fa fa-file-pdf text-danger"></i>';
                                                                                    } elseif (in_array($ext, ['doc', 'docx'])) {
                                                                                        $icono = '<i class="fa fa-file-word text-primary"></i>';
                                                                                    } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                                                                        $icono = '<i class="fa fa-file-excel text-success"></i>';
                                                                                    }
                                                                                    ?>
                                                                                    <div class="mt-2">
                                                                                        <strong>Documento actual:</strong><br>
                                                                                        <a href="<?php echo $ruta_doc; ?>" target="_blank">
                                                                                            <?php echo $icono; ?> <?php echo $descripcion; ?>
                                                                                        </a>
                                                                                    </div>
                                                                                <?php } ?>

                                                                                <div class="mt-2" id="previewDoc<?php echo $id_pedido; ?>"></div>
                                                                                <small class="text-muted">Formatos permitidos: PDF, Word, Excel</small>
                                                                            </div>

                                                                        </div>
                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                        <button type="button" class="btn btn-primary" id="btn_update<?php echo $id_pedido; ?>">
                                                                            Actualizar
                                                                        </button>
                                                                    </div>

                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

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

                                                            document.getElementById('descripcion<?php echo $id_pedido; ?>').addEventListener('change', function (e) {
                                                                const preview = document.getElementById('previewDoc<?php echo $id_pedido; ?>');
                                                                preview.innerHTML = '';

                                                                const file = e.target.files[0];

                                                                if (file) {
                                                                    const ext = file.name.split('.').pop().toLowerCase();
                                                                    let icon = '<i class="fa fa-file text-secondary"></i>';

                                                                    if (ext === 'pdf') icon = '<i class="fa fa-file-pdf text-danger"></i>';
                                                                    else if (['doc', 'docx'].includes(ext)) icon = '<i class="fa fa-file-word text-primary"></i>';
                                                                    else if (['xls', 'xlsx'].includes(ext)) icon = '<i class="fa fa-file-excel text-success"></i>';

                                                                    preview.innerHTML = `<strong>Nuevo documento:</strong><br>${icon} ${file.name}`;
                                                                }
                                                            });
                                                        });
                                                    </script>

                                                    <?php if ($rol_sesion != "Empleado Estandar") { ?>
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                                data-toggle="modal"
                                                                data-target="#modal-delete<?php echo $id_pedido; ?>">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    <?php } ?>

                                                    <!-- Modal Eliminar Pedido -->
                                                    <div class="modal fade" id="modal-delete<?php echo $id_pedido; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">

                                                                <form id="formDelete<?php echo $id_pedido; ?>" method="post">

                                                                    <div class="modal-header bg-danger text-white">
                                                                        <h4 class="modal-title">Eliminar Pedido</h4>
                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>

                                                                    <div class="modal-body text-left">
                                                                        ¿Estás seguro de que deseas eliminar este pedido?
                                                                        <br><br>

                                                                        <strong>N° Cotización:</strong> <?php echo $nro_coti; ?><br>
                                                                        <strong>Razón Social:</strong> <?php echo $razon_social; ?><br>
                                                                        <strong>Flete:</strong>
                                                                        <?php echo !empty($flete) ? strtoupper($flete) : 'NO REGISTRADO'; ?>

                                                                        <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">
                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                                        <button type="button" class="btn btn-danger" id="btn_delete<?php echo $id_pedido; ?>">
                                                                            Eliminar
                                                                        </button>
                                                                    </div>

                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <script>
                                                        $(document).ready(function () {
                                                            $('#btn_delete<?php echo $id_pedido; ?>').click(function () {
                                                                $('#formDelete<?php echo $id_pedido; ?>').submit();
                                                            });

                                                            $('#formDelete<?php echo $id_pedido; ?>').on('submit', function (e) {
                                                                e.preventDefault();

                                                                var formData = new FormData(this);

                                                                $.ajax({
                                                                    url: '../app/controllers/pedidos/delete.php',
                                                                    type: 'POST',
                                                                    data: formData,
                                                                    contentType: false,
                                                                    processData: false,
                                                                    success: function () {
                                                                        $('#modal-delete<?php echo $id_pedido; ?>').modal('hide');
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

<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>

<script>
    $(function () {
        $("#example1").DataTable({
            "pageLength": 12,
            "order": [["0", "desc"]],
            "paging": true,
            "ordering": false,
            "info": true,
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

<!-- Modal para crear Pedido -->
<div class="modal fade" id="modal-create">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formProducto" method="post" enctype="multipart/form-data" novalidate>

                <div class="modal-header" style="background-color: #1d3Cb6; color: white;">
                    <h4 class="modal-title">Creación de Nuevo Pedido</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nro_coti" class="form-label">N° de Cotización</label>
                                    <input type="number" name="nro_coti" id="nro_coti" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="responsable" class="form-label">
                                        Responsable <span class="text-danger">*</span>
                                    </label>
                                    <select name="responsable" id="responsable" class="form-control" required>
                                        <option value="Clientes">CLIENTES</option>
                                        <option value="Peru Compras">PERU COMPRAS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_venci" class="form-label">
                                        Fecha de Vencimiento <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" name="fecha_venci" id="fecha_venci" class="form-control" required>
                                    <div class="invalid-feedback">Este campo es obligatorio.</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="fecha_emi" class="form-label">Fecha de Emisión</label>
                                    <input type="datetime-local" name="fecha_emi" id="fecha_emi" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="razon_social" class="form-label">
                                    Razón Social <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="razon_social" id="razon_social" class="form-control"
                                       placeholder="Ej. Empresa XYZ SAC" required>
                                <div class="invalid-feedback">Este campo es obligatorio.</div>
                            </div>

                            <div class="row">

                                <div class="col-md-4 mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select name="estado" id="estado" class="form-control" required>
                                        <option value="Subido">SUBIDO</option>
                                        <option value="Vencido">VENCIDO</option>
                                        <option value="Preparado">PREPARADO</option>
                                        <option value="Despachado">DESPACHADO</option>
                                        <option value="Sale de lima">SALE DE LIMA</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="estado_pago">Estado de Pago</label>
                                    <select name="estado_pago" id="estado_pago" class="form-control" required>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="PARCIAL">PARCIAL</option>
                                        <option value="PAGADO">PAGADO</option>
                                    </select>
                                </div>

                                <!-- CAMPO FLETE -->
                                <div class="col-md-4 mb-3">
                                    <label for="flete">Flete</label>
                                    <select name="flete" id="flete" class="form-control">
                                        <option value="">SELECCIONAR</option>
                                        <option value="si">SI</option>
                                        <option value="no">NO</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="id_region">Región</label>
                                    <select name="id_region" id="id_region" class="form-control">
                                        <option value="">SIN REGIÓN</option>
                                        <?php foreach ($diasregion_datos as $region_dato) { ?>
                                            <option value="<?php echo $region_dato['id']; ?>">
                                                <?php echo $region_dato['region']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="descripcion" class="form-label">Documento del Pedido</label>
                                    <input type="file" name="descripcion" id="descripcion"
                                           class="form-control"
                                           accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                                    <small class="text-muted">Formatos permitidos: PDF, Word, Excel</small>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_create">Registrar</button>
                    <div id="respuesta"></div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#btn_create').click(function () {
            const btn = $(this);
            btn.prop('disabled', true);

            $('#fecha_venci, #razon_social').removeClass('is-invalid');

            let valid = true;

            if (!$('#fecha_venci').val().trim()) {
                $('#fecha_venci').addClass('is-invalid');
                valid = false;
            }

            if (!$('#razon_social').val().trim()) {
                $('#razon_social').addClass('is-invalid');
                valid = false;
            }

            if (!valid) {
                btn.prop('disabled', false);
                return;
            }

            const formData = new FormData($('#formProducto')[0]);

            $.ajax({
                url: '../app/controllers/pedidos/create.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    try {
                        let res = JSON.parse(response);
                        if (res.status === 'success') {
                            $('#modal-create').modal('hide');
                            location.reload();
                        }
                    } catch (err) {
                        // Silenciado
                    }

                    btn.prop('disabled', false);
                },
                error: function () {
                    btn.prop('disabled', false);
                }
            });
        });

        $('#formProducto').on('submit', function (e) {
            e.preventDefault();
        });
    });
</script>

<style>
    #example1 td {
        vertical-align: middle;
        text-align: center;
    }

    #example1 td:not(:nth-child(1)) {
        white-space: nowrap;
    }

    .col-razon {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: left;
    }
</style>