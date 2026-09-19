<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/proveedores/listado_de_proveedores.php');

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Proveedores(Dueños de marca)
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create">
                            <i class="fa fa-plus"></i> Agregar
                        </button>
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
                <div class="col-md-10">
                    <div class="card card-outline  card-primary">
                        <div class="card-header">
                            <h3 class="card-title">LISTADO DE PROVEEDORES</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                            </div>

                        </div>

                        <div class="card-body" style="display: block;">

                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th><center>RUC</center></th>
                                    <th><center>Razon Social</center></th>
                                    <th><center>Contacto</center></th>
                                    <th><center>Marca</center></th>
                                    <th><center>Acciones</center></th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador=0;
                                foreach ($proveedor_datos as $proveedor_dato) {
                                    $id_proveedor =$proveedor_dato['id_proveedor'];
                                    $ruc =$proveedor_dato['ruc'];
                                    $razon_social =$proveedor_dato ['razon_social'];
                                    $contacto =$proveedor_dato ['contacto'];
                                    $marca =$proveedor_dato ['marca'];
                                    ?>
                                    <tr>
                                        <td><?php echo $proveedor_dato['ruc'];?></td>
                                        <td><?php echo $proveedor_dato['razon_social'];?></td>
                                        <td><?php echo $proveedor_dato['contacto'];?></td>
                                        <td><?php echo $proveedor_dato['marca'];?></td>

                                        <td>
                                            <center>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                                            data-target="#modal-update<?php echo $id_proveedor;?>">
                                                        <i class="fa fa-pencil-alt"></i> Editar
                                                    </button>


                                                    <?php if ($rol_sesion === 'Administrador') { ?>

                                                        <button type="button" class="btn btn-danger" data-toggle="modal"
                                                                data-target="#modal-delete<?php echo $id_proveedor;?>">
                                                            <i class="fa fa-pencil-alt"></i> Eliminar
                                                        </button>
                                                    <?php } ?>
                                                    <!-- /.Modal para modificar Proveedor -->

                                                    <div class="modal fade" id="modal-update<?php echo $id_proveedor; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #1f8236; color:white">
                                                                    <h4 class="modal-title">Actualizar Proveedores</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <!-- Fila 1: RUC y Razón Social -->
                                                                                <div class="row">
                                                                                    <div class="col-md-4">
                                                                                        <label for="">RUC</label>
                                                                                        <input type="text" id="ruc<?php echo $id_proveedor;?>" class="form-control"
                                                                                               value="<?php echo $ruc; ?>">
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <label for="">Razón Social</label>
                                                                                        <input type="text" id="razon_social<?php echo $id_proveedor;?>" class="form-control"
                                                                                               value="<?php echo $razon_social; ?>">
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Fila 2: Contacto y Marca -->
                                                                                <div class="row mt-2">
                                                                                    <div class="col-md-6">
                                                                                        <label for="">Contacto</label>
                                                                                        <input type="text" id="contacto<?php echo $id_proveedor;?>" class="form-control"
                                                                                               value="<?php echo $contacto; ?>">
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="">Marca</label>
                                                                                        <input type="text" id="marca<?php echo $id_proveedor;?>" class="form-control"
                                                                                               value="<?php echo $marca; ?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                    <button type="button" class="btn btn-success" id="btn_update<?php echo $id_proveedor;?>">Actualizar</button>
                                                                    <div id="respuesta<?php echo $id_proveedor; ?>"></div>
                                                                </div>
                                                            </div>
                                                            <!-- /.modal-content -->
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>


                                                    <!-- /.Modal para modificar Proveedor -->
                                                    <script>
                                                        $('#btn_update<?php echo $id_proveedor;?>').click(function () {
                                                            var id_proveedor = '<?php echo $id_proveedor; ?>';
                                                            var ruc = $('#ruc<?php echo $id_proveedor;?>').val();
                                                            var razon_social = $('#razon_social<?php echo $id_proveedor;?>').val();
                                                            var contacto = $('#contacto<?php echo $id_proveedor;?>').val();
                                                            var marca = $('#marca<?php echo $id_proveedor;?>').val();
                                                            var url = "../app/controllers/proveedores/update_proveedores.php";

                                                            $.get(url, {
                                                                id_proveedor: id_proveedor,
                                                                ruc: ruc,
                                                                razon_social: razon_social,
                                                                contacto: contacto,
                                                                marca: marca
                                                            }, function (datos) {
                                                                $('#respuesta<?php echo $id_proveedor; ?>').html(datos);
                                                            });
                                                        });
                                                    </script>

                                                    <!-- /.Modal para Eliminar Proveedor -->

                                                    <!-- modal para borrar proveedore -->
                                                    <div class="modal fade" id="modal-delete<?php echo $id_proveedor;?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #ca0a0b;color: white">
                                                                    <h4 class="modal-title">¿Esta seguro de eliminar al proveedor?</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <!-- Fila 1: RUC y Razón Social -->
                                                                                <div class="row">
                                                                                    <div class="col-md-4">
                                                                                        <label for="">RUC</label>
                                                                                        <input type="text" id="ruc<?php echo $id_proveedor;?>" class="form-control" disabled
                                                                                               value="<?php echo $ruc; ?>">
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <label for="">Razón Social</label>
                                                                                        <input type="text" id="razon_social<?php echo $id_proveedor;?>" class="form-control" disabled
                                                                                               value="<?php echo $razon_social; ?>">
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Fila 2: Contacto y Marca -->
                                                                                <div class="row mt-2">
                                                                                    <div class="col-md-6">
                                                                                        <label for="">Contacto</label>
                                                                                        <input type="text" id="contacto<?php echo $id_proveedor;?>" class="form-control" disabled
                                                                                               value="<?php echo $contacto; ?>">
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="">Marca</label>
                                                                                        <input type="text" id="marca<?php echo $id_proveedor;?>" class="form-control" disabled
                                                                                               value="<?php echo $marca; ?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                    <button type="button" class="btn btn-danger" id="btn_delete<?php echo $id_proveedor;?>">Eliminar</button>
                                                                </div>
                                                                <div id="respuesta_delete<?php echo $id_proveedor;?>"></div>
                                                            </div>
                                                            <!-- /.modal-content -->
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>

                                                    <!-- /.Modal para eliminar Proveedor -->
                                                    <script>
                                                        $('#btn_delete<?php echo $id_proveedor;?>').click(function () {

                                                            var id_proveedor = '<?php echo $id_proveedor;?>';

                                                            var url2 = "../app/controllers/proveedores/delete_proveedores.php";
                                                            $.get(url2,{id_proveedor:id_proveedor},function (datos) {
                                                                $('#respuesta_delete<?php echo $id_proveedor;?>').html(datos);
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
                                <!--                            <tfoot>-->
                                <!--                            <tr>-->
                                <!--                                <th><center>Nro</center></th>-->
                                <!--                                <th><center>Nombres</center></th>-->
                                <!--                                <th><center>Correo Electronico</center></th>-->
                                <!--                            </tr>-->
                                <!--                            </tfoot>-->
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
            "responsive": true, "lengthChange": true, "autoWidth": false,
            buttons: [{
                extend: 'collection',
                text: 'Reportes',
                orientation: 'landscape',
                buttons: [{
                    text: 'Copiar',
                    extend: 'copy'
                }, {
                    extend: 'pdf',
                }, {
                    extend: 'csv',
                }, {
                    extend: 'excel',
                }, {
                    text: 'Imprimir',
                    extend: 'print'
                }
                ]
            },
                {
                    extend: 'colvis',
                    text: 'Visor de columnas'
                }
            ],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    });
</script>

<!-- /.Modal para crear proveedor -->
<div class="modal fade" id="modal-create">
    <div class="modal-dialog">
        <form id="form_create" method="post"> <!-- Agregado form para validación -->
            <div class="modal-content">
                <div class="modal-header" style="background-color: #1d3Cb6;color:white">
                    <h4 class="modal-title">Creación de nuevo Proveedor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="ruc">RUC <span style="color: red;">*</span></label>
                                        <input type="text" id="ruc" class="form-control" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="razon_social">Razón Social <span style="color: red;">*</span></label>
                                        <input type="text" id="razon_social" class="form-control" required>
                                    </div>
                                </div>

                                <!-- Contacto y Marca -->
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="contacto">Contacto</label>
                                        <input type="text" id="contacto" name="contacto" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="marca">Marca</label>
                                        <input type="text" id="marca" name="marca" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn_create">Registrar</button>
                    <div id="respuesta"></div>
                </div>
            </div>
        </form>
    </div>
</div>



<script>
    $('#btn_create').click(function () {
        var ruc = $('#ruc').val().trim();
        var razon_social = $('#razon_social').val().trim();
        var contacto = $('#contacto').val().trim();
        var marca = $('#marca').val().trim();

        // Validación: campos requeridos
        if (ruc === '' || razon_social === '') {
            $('#respuesta').html('<div class="text-danger">Los campos RUC y Razón Social son obligatorios.</div>');
            return;
        }

        // Si pasa validación, enviar por AJAX
        var url = "../app/controllers/proveedores/registro_de_proveedores.php";
        $.get(url, {
            ruc: ruc,
            razon_social: razon_social,
            contacto: contacto,
            marca: marca
        }, function (datos) {
            $('#respuesta').html(datos);
        });
    });
</script>




