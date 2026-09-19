<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/pedidos/listado_lote.php');

// CATEGORÍAS
$sql_categoria = $pdo->prepare("
    SELECT DISTINCT nombre_categoria AS nombre
    FROM tb_categoria
    ORDER BY nombre
");
$sql_categoria->execute();
$categoria_datos = $sql_categoria->fetchAll(PDO::FETCH_ASSOC);

// MARCAS
$sql_marca = $pdo->prepare("
    SELECT DISTINCT nombre
    FROM tb_marca
    ORDER BY nombre
");
$sql_marca->execute();
$marca_datos = $sql_marca->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6 d-flex align-items-center">
                    <h1 class="m-0 mr-3">Control de Lote</h1>

                    <button type="button" class="btn btn-primary btn-sm px-3 py-2 shadow-sm"
                            data-toggle="modal" data-target="#modal-create" style="border-radius: 8px;">
                        <i class="fa fa-plus mr-1"></i>
                        Crear Nuevo
                    </button>
                </div>


            </div>
        </div>
    </div>
    <!-- /.content-header -->


    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">

                    <div class="card card-outline card-primary">

                        <div class="card-header">
                            <h3 class="card-title">INFORMACIÓN DE PRODUCCIÓN</h3>
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
                                    <th><center>Categoria</center></th>
                                    <th><center>Cont.</center></th>
                                    <th><center>Marca</center></th>
                                    <th><center>F. Fabricación</center></th>
                                    <th><center>F. Vencimiento</center></th>
                                    <th><center>Lote</center></th>
                                    <th><center>Cant.</center></th>
                                    <th><center>Entidad / Cliente</center></th>
                                    <th><center>Descripcion del Producto</center></th>
                                    <th><center>Acciones</center></th>
                                </tr>
                                </thead>

                                <tbody>

                                <?php
                                foreach ($lote_datos as $lote_dato) {

                                    $id_lote = $lote_dato['id_lote'];
                                    $nombre_categoria = $lote_dato['nombre_categoria'];
                                    $medida = $lote_dato['medida'];
                                    $nombre_marca = $lote_dato['nombre_marca'];
                                    $f_fabri = $lote_dato['f_fabri'];
                                    $f_venci = $lote_dato['f_venci'];
                                    $lote = $lote_dato['lote'];
                                    $uni = $lote_dato['uni'];
                                    $entidad = $lote_dato['entidad'];
                                    $descripcion = $lote_dato['descripcion'];
                                    ?>

                                    <tr>
                                        <td><?php echo $nombre_categoria; ?></td>
                                        <td><?php echo $medida; ?></td>
                                        <td><?php echo $nombre_marca; ?></td>
                                        <td><?php echo $f_fabri; ?></td>
                                        <td><?php echo $f_venci; ?></td>
                                        <td><?php echo $lote; ?></td>
                                        <td><?php echo $uni; ?></td>
                                        <td><?php echo $entidad; ?></td>
                                        <td><?php echo $descripcion; ?></td>

                                        <td>
                                            <center>
                                                <div class="btn-group">

                                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                                            data-target="#modal-update<?php echo $id_lote; ?>">
                                                        <i class="fa fa-pencil-alt"></i> Editar
                                                    </button>

                                                    <!-- Modal actualizar -->
                                                    <div class="modal fade" id="modal-update<?php echo $id_lote; ?>">
                                                        <div class="modal-dialog modal-med">
                                                            <div class="modal-content">

                                                                <div class="modal-header" style="background-color: #1f8236; color:white;">
                                                                    <h4 class="modal-title">Actualizar Nro. de Lote</h4>
                                                                    <button type="button" class="close" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <div class="row">

                                                                        <!-- Línea 1 -->
                                                                        <div class="col-md-8">
                                                                            <label>Nombre de Categoría</label>
                                                                            <select id="nombre_categoria<?php echo $id_lote; ?>" class="form-control">
                                                                                <?php foreach ($categoria_datos as $cat) { ?>
                                                                                    <option value="<?php echo $cat['nombre']; ?>"
                                                                                        <?php echo ($cat['nombre'] === $nombre_categoria) ? 'selected' : ''; ?>>
                                                                                        <?php echo $cat['nombre']; ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>



                                                                        <div class="col-md-4">
                                                                            <label>Cont.</label>

                                                                            <?php
                                                                            // Normalizar medida desde BD
                                                                            $medida_actual = isset($medida) ? trim(strtoupper($medida)) : '';

                                                                            $medidas = ["500 ML", "1 LT", "3.8 LT", "4 LT", "20 LT"];
                                                                            ?>

                                                                            <select id="medida<?php echo $id_lote; ?>" class="form-control">
                                                                                <?php foreach ($medidas as $m):
                                                                                    $m_norm = strtoupper(trim($m));
                                                                                    $selected = ($m_norm === $medida_actual) ? "selected" : "";
                                                                                    ?>
                                                                                    <option value="<?php echo $m; ?>" <?php echo $selected; ?>>
                                                                                        <?php echo $m; ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>


                                                                        <!-- Línea 2 -->
                                                                        <div class="col-md-4 mt-3">
                                                                            <label>Marca</label>
                                                                            <select id="nombre_marca<?php echo $id_lote; ?>" class="form-control">
                                                                                <?php foreach ($marca_datos as $marca) { ?>
                                                                                    <option value="<?php echo $marca['nombre']; ?>"
                                                                                        <?php echo ($marca['nombre'] === $nombre_marca) ? 'selected' : ''; ?>>
                                                                                        <?php echo $marca['nombre']; ?>
                                                                                    </option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>


                                                                        <div class="col-md-4 mt-3">
                                                                            <label>F. Fabricación</label>
                                                                            <input type="date" id="f_fabri<?php echo $id_lote; ?>"
                                                                                   class="form-control"
                                                                                   value="<?php echo $f_fabri; ?>">
                                                                        </div>

                                                                        <div class="col-md-4 mt-3">
                                                                            <label>F. Vencimiento</label>
                                                                            <input type="date" id="f_venci<?php echo $id_lote; ?>"
                                                                                   class="form-control"
                                                                                   value="<?php echo $f_venci; ?>">
                                                                        </div>

                                                                        <!-- Línea 3 -->
                                                                        <div class="col-md-6 mt-3">
                                                                            <label>Entidad / Cliente</label>
                                                                            <input type="text" id="entidad<?php echo $id_lote; ?>"
                                                                                   class="form-control"
                                                                                   value="<?php echo $entidad; ?>">
                                                                        </div>
                                                                        <div class="col-md-6 mt-3">
                                                                            <label>Descripcion</label>
                                                                            <input type="text" id="descripcion<?php echo $id_lote; ?>"
                                                                                   class="form-control"
                                                                                   value="<?php echo $descripcion; ?>">
                                                                        </div>

                                                                        <div class="col-md-2 mt-3">
                                                                            <label>Cantidad</label>
                                                                            <input type="number" id="uni<?php echo $id_lote; ?>"
                                                                                   class="form-control"
                                                                                   value="<?php echo $uni; ?>">
                                                                        </div>

                                                                        <div class="col-md-4 mt-3">
                                                                            <label>Lote</label>
                                                                            <input type="text" id="lote<?php echo $id_lote; ?>"
                                                                                   class="form-control"
                                                                                   value="<?php echo $lote; ?>">
                                                                        </div>

                                                                    </div>
                                                                </div>

                                                                <div class="modal-footer justify-content-between">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                    <button type="button" class="btn btn-success" id="btn_update<?php echo $id_lote;?>">Actualizar</button>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>


                                                    <script>
                                                        $('#btn_update<?php echo $id_lote; ?>').click(function () {

                                                            var id_lote = "<?php echo $id_lote; ?>";
                                                            $.get("../app/controllers/pedidos/update_lote.php", {

                                                                ajax: 1,
                                                                id_lote: id_lote,
                                                                nombre_categoria: $('#nombre_categoria<?php echo $id_lote; ?>').val(),
                                                                medida: $('#medida<?php echo $id_lote; ?>').val(),
                                                                nombre_marca: $('#nombre_marca<?php echo $id_lote; ?>').val(),
                                                                f_fabri: $('#f_fabri<?php echo $id_lote; ?>').val(),
                                                                f_venci: $('#f_venci<?php echo $id_lote; ?>').val(),
                                                                lote: $('#lote<?php echo $id_lote; ?>').val(),
                                                                uni: $('#uni<?php echo $id_lote; ?>').val(),
                                                                entidad: $('#entidad<?php echo $id_lote; ?>').val(),
                                                                descripcion: $('#descripcion<?php echo $id_lote; ?>').val()
                                                            }, function (respuesta) {

                                                                respuesta = respuesta.trim();

                                                                if (respuesta === "success") {
                                                                    location.reload();
                                                                } else {
                                                                    alert("Error al actualizar el lote: " + respuesta);
                                                                }

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
            "pageLength": 8,
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
            },
            "columnDefs": [
                {
                    // Columna 3 = fabricación
                    targets: 3,
                    render: function (data) {
                        return formatearFecha(data);
                    }
                },
                {
                    // Columna 4 = vencimiento
                    targets: 4,
                    render: function (data) {
                        return formatearFecha(data);
                    }
                }
            ],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false
        });
    });


</script>

<script>
    function formatearFecha(fecha) {
        if (!fecha) return "";
        const partes = fecha.split("-");
        return partes[2] + "/" + partes[1] + "/" + partes[0];
    }
</script>

<style>
    #example1 {
        border-radius: 8px !important;
        overflow: hidden;
    }

    #example1 thead th {
        background: #1f3bb3;
        color: white;
        text-align: center;
        font-weight: 600;
    }

    #example1 tbody tr:hover {
        background: #eef3ff !important;
    }

    #example1 td {
        vertical-align: middle;
        text-align: center;
    }

    .btn-success, .btn-primary {
        border-radius: 5px !important;
    }
</style>

<style>
    .modal-content {
        border-radius: 10px;
    }

    .modal-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .modal-body label {
        font-weight: 600;
    }

    .modal-dialog.modal-med {
        max-width: 650px;
    }
</style>


<style>
    .modal-dialog.modal-med {
        max-width: 650px;
    }
</style>


<!-- Modal crear lote -->
<div class="modal fade" id="modal-create">
    <div class="modal-dialog modal-med">
        <div class="modal-content">

            <div class="modal-header" style="background-color: #1d3cb6; color:white;">
                <h4 class="modal-title">Creación de Nuevo Lote</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <!-- Línea 1 -->
                    <div class="col-md-8">
                        <label>Nombre de Categoría</label>
                        <select id="nombre_categoria" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($categoria_datos as $categoria) { ?>
                                <option value="<?php echo $categoria['nombre']; ?>">
                                    <?php echo $categoria['nombre']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Cont.</label>
                        <select id="medida" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="400 ML">400 ML</option>
                            <option value="500 ML">500 ML</option>
                            <option value="1 LT">1 LT</option>
                            <option value="3.8 LT">3.8 LT</option>
                            <option value="4 LT">4 LT</option>
                            <option value="20 LT">20 LT</option>
                        </select>
                    </div>

                    <!-- Línea 2 -->
                    <div class="col-md-4 mt-3">
                        <label>Marca</label>
                        <select id="nombre_marca" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($marca_datos as $marca) { ?>
                                <option value="<?php echo $marca['nombre']; ?>">
                                    <?php echo $marca['nombre']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>F. Fabricación</label>
                        <input type="date" id="f_fabri" class="form-control">
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>F. Vencimiento</label>
                        <input type="date" id="f_venci" class="form-control">
                    </div>

                    <!-- Línea 3 -->
                    <div class="col-md-6 mt-3">
                        <label>Entidad / Cliente</label>
                        <input type="text" id="entidad" class="form-control">
                    </div>
                    <!-- Línea 3 -->
                    <div class="col-md-6 mt-3">
                        <label>Descripcion</label>
                        <input type="text" id="descripcion" class="form-control">
                    </div>

                    <div class="col-md-2 mt-3">
                        <label>Cantidad</label>
                        <input type="number" id="uni" class="form-control">
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>Lote</label>
                        <input type="text" id="lote" class="form-control">
                    </div>

                </div>

            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_create">Registrar</button>
                <div id="respuesta"></div>
            </div>

        </div>
    </div>
</div>


<script>
    $('#btn_create').click(function () {

        var nombre_categoria = $('#nombre_categoria').val();
        var medida = $('#medida').val();
        var nombre_marca = $('#nombre_marca').val();
        var f_fabri = $('#f_fabri').val();
        var f_venci = $('#f_venci').val();
        var lote = $('#lote').val();
        var uni = $('#uni').val();
        var entidad = $('#entidad').val();
        var descripcion = $('#descripcion').val();
        var url = "../app/controllers/pedidos/registro_lote.php";


        $.get(url, {
            ajax: 1,
            nombre_categoria: nombre_categoria,
            medida: medida,
            nombre_marca: nombre_marca,
            f_fabri: f_fabri,
            f_venci: f_venci,
            lote: lote,
            uni: uni,
            entidad: entidad,
            descripcion: descripcion
        }, function (datos) {
            datos = datos.trim();

            if (datos === "success") {
                location.reload();
            } else {
                alert("Error al registrar el lote: " + datos);
            }

        });

    });
</script>
