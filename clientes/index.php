<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/clientes/listado_de_clientes.php');



?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Clientes (Dueños de marca)
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
                <div class="col-md-12">
                    <div class="card card-outline  card-primary">
                        <div class="card-header">
                            <h3 class="card-title">LISTADO DE CLIENTES</h3>
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
                                    <th><center>Contacto 1</center></th>
                                    <th><center>Contacto 2</center></th>
                                    <th><center>Contacto 3</center></th>
                                    <th><center>Direccion</center></th>
                                    <th><center>Acciones</center></th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador=0;
                                foreach ($clientes_datos as $clientes_dato) {
                                    $id_cliente =$clientes_dato['id_cliente'];
                                    $ruc =$clientes_dato['ruc'];
                                    $razon_social =$clientes_dato ['razon_social'];
                                    $contacto1 =$clientes_dato ['contacto1'];
                                    $contacto2 =$clientes_dato ['contacto2'];
                                    $contacto3 =$clientes_dato ['contacto3'];
                                    $direccion =$clientes_dato ['direccion'];
                                    ?>
                                    <tr>
                                        <td><?php echo $clientes_dato['ruc'];?></td>
                                        <td><?php echo $clientes_dato['razon_social'];?></td>
                                        <td><?php echo $clientes_dato['contacto1'];?></td>
                                        <td><?php echo $clientes_dato['contacto2'];?></td>
                                        <td><?php echo $clientes_dato['contacto3'];?></td>
                                        <td class="detalle"><?php echo $clientes_dato['direccion'];?></td>
                                        <td>
                                            <center>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                                            data-target="#modal-update<?php echo $id_cliente;?>">
                                                        <i class="fa fa-pencil-alt"></i> Editar
                                                    </button>



                                                    <!-- /.Modal para modificar cliente -->
                                                    <div class="modal fade" id="modal-update<?php echo $id_cliente; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #1f8236; color:white">
                                                                    <h4 class="modal-title">Actualizar Cliente </h4>
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
                                                                                        <label for="ruc">RUC <span style="color: red;">*</span></label>
                                                                                        <input type="text" id="ruc<?php echo $id_cliente;?>"  value="<?php echo $ruc; ?>" class="form-control" required >
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <label for="razon_social">Razón Social <span style="color: red;">*</span></label>
                                                                                        <input type="text" id="razon_social<?php echo $id_cliente;?>" value="<?php echo $razon_social; ?>" class="form-control" required>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Fila 2: Contacto 1 y Contacto 2 -->
                                                                                <div class="row mt-3">
                                                                                    <div class="col-md-6">
                                                                                        <label for="contacto1">Contacto 1</label>
                                                                                        <input type="text" id="contacto1<?php echo $id_cliente;?>" class="form-control" value="<?php echo $contacto1; ?>">
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="contacto2">Contacto 2</label>
                                                                                        <input type="text" id="contacto2<?php echo $id_cliente;?>" class="form-control" value="<?php echo $contacto2; ?>">
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Fila 3: Contacto 3 y Dirección -->
                                                                                <div class="row mt-3">
                                                                                    <div class="col-md-4">
                                                                                        <label for="contacto3">Contacto 3</label>
                                                                                        <input type="text" id="contacto3<?php echo $id_cliente;?>" class="form-control" value="<?php echo $contacto3; ?>">
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <label for="direccion">Dirección</label>
                                                                                        <input type="text" id="direccion<?php echo $id_cliente;?>" value="<?php echo $direccion; ?>" class="form-control">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                    <button type="button" class="btn btn-success" id="btn_update<?php echo $id_cliente;?>">Actualizar</button>
                                                                    <div id="respuesta<?php echo $id_cliente; ?>"></div>
                                                                </div>
                                                            </div>
                                                            <!-- /.modal-content -->
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>

                                                    <script>
                                                        $('#btn_update<?php echo $id_cliente;?>').click(function () {
                                                            var id_cliente = '<?php echo $id_cliente; ?>';
                                                            var ruc = $('#ruc<?php echo $id_cliente;?>').val();
                                                            var razon_social= $('#razon_social<?php echo $id_cliente;?>').val();
                                                            var contacto1= $('#contacto1<?php echo $id_cliente;?>').val();
                                                            var contacto2= $('#contacto2<?php echo $id_cliente;?>').val();
                                                            var contacto3= $('#contacto3<?php echo $id_cliente;?>').val();
                                                            var direccion= $('#direccion<?php echo $id_cliente;?>').val();
                                                            var url = "../app/controllers/clientes/update_de_clientes.php";

                                                            $.get(url, {
                                                                id_cliente: id_cliente,
                                                                ruc: ruc,
                                                                razon_social:razon_social,
                                                                contacto1: contacto1,
                                                                contacto2: contacto2,
                                                                contacto3: contacto3,
                                                                direccion: direccion

                                                            }, function (datos) {
                                                                $('#respuesta<?php echo $id_cliente; ?>').html(datos);
                                                            });
                                                        });
                                                    </script>

                                                    <?php if ($rol_sesion === 'Administrador') { ?>
                                                        <button type="button" class="btn btn-danger" data-toggle="modal"
                                                                data-target="#modal-delete<?php echo $id_cliente;?>">
                                                            <i class="fa fa-pencil-alt"></i> Eliminar
                                                        </button>

                                                    <?php } ?>



                                                    <!-- modal para borrar cliente -->
                                                    <div class="modal fade" id="modal-delete<?php echo $id_cliente;?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #ca0a0b;color: white">
                                                                    <h4 class="modal-title">¿Esta seguro de eliminar al Cliente?</h4>
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
                                                                                        <input type="text" id="ruc<?php echo $id_cliente;?>" class="form-control" disabled
                                                                                               value="<?php echo $ruc; ?>">
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <label for="">Razón Social</label>
                                                                                        <input type="text" id="razon_social<?php echo $id_cliente;?>" class="form-control" disabled
                                                                                               value="<?php echo $razon_social; ?>">
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Fila 2: Contacto 1 y Contacto 2 -->
                                                                                <div class="row mt-3">
                                                                                    <div class="col-md-6">
                                                                                        <label for="contacto1">Contacto 1</label>
                                                                                        <input type="text" id="contacto1<?php echo $id_cliente;?>" class="form-control" disabled
                                                                                               value="<?php echo $contacto1; ?>">
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label for="contacto2">Contacto 2</label>
                                                                                        <input type="text" id="contacto2<?php echo $id_cliente;?>" class="form-control" disabled
                                                                                               value="<?php echo $contacto2; ?>">
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Fila 3: Contacto 3 y Dirección -->
                                                                                <div class="row mt-3">
                                                                                    <div class="col-md-4">
                                                                                        <label for="contacto3">Contacto 3</label>
                                                                                        <input type="text" id="contacto3<?php echo $id_cliente;?>" class="form-control"  disabled
                                                                                               value="<?php echo $contacto3; ?>">
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <label for="direccion">Dirección</label>
                                                                                        <input type="text" id="direccion<?php echo $id_cliente;?>"
                                                                                               value="<?php echo $direccion; ?>" class="form-control"disabled>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                    <button type="button" class="btn btn-danger" id="btn_delete<?php echo $id_cliente;?>">Eliminar</button>
                                                                </div>
                                                                <div id="respuesta_delete<?php echo $id_cliente;?>"></div>
                                                            </div>
                                                            <!-- /.modal-content -->
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>


                                                    <!-- /.Modal para eliminar cliente-->
                                                    <script>
                                                        $('#btn_delete<?php echo $id_cliente;?>').click(function () {

                                                            var id_cliente = '<?php echo $id_cliente;?>';

                                                            var url2 = "../app/controllers/clientes/delete_clientes.php";
                                                            $.get(url2,{id_cliente:id_cliente},function (datos) {
                                                                $('#respuesta_delete<?php echo $id_cliente;?>').html(datos);
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

<!-- /.Modal para crear marca -->
<div class="modal fade" id="modal-create" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1d3Cb6;color:white">
                <h4 class="modal-title">Creacion de nuevo Cliente</h4>
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
                                    <label for="ruc">RUC <span style="color: red;">*</span></label>
                                    <input type="text" id="ruc" class="form-control" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="razon_social">Razón Social <span style="color: red;">*</span></label>
                                    <input type="text" id="razon_social" class="form-control" required>
                                </div>
                            </div>

                            <!-- Fila 2: Contacto 1 y Contacto 2 -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="contacto1">Contacto 1</label>
                                    <input type="text" id="contacto1" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="contacto2">Contacto 2</label>
                                    <input type="text" id="contacto2" class="form-control">
                                </div>
                            </div>

                            <!-- Fila 3: Contacto 3 y Dirección -->
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="contacto3">Contacto 3</label>
                                    <input type="text" id="contacto3" class="form-control">
                                </div>
                                <div class="col-md-8">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" id="direccion" class="form-control">
                                </div>
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
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<script>
    $('#btn_create').click(function () {
        var ruc = $('#ruc').val().trim();
        var razon_social = $('#razon_social').val().trim();
        var contacto1 = $('#contacto1').val().trim();
        var contacto2 = $('#contacto2').val().trim();
        var contacto3 = $('#contacto3').val().trim();
        var direccion = $('#direccion').val().trim();

        // Validación: campos requeridos
        if (ruc === '' || razon_social === '') {
            $('#respuesta').html('<div class="text-danger">Los campos RUC y Razón Social son obligatorios.</div>');
            return;
        }

        // Si pasa validación, enviar por AJAX
        var url = "../app/controllers/clientes/registro_de_clientes.php";
        $.get(url, {
            ruc: ruc,
            razon_social: razon_social,
            contacto1: contacto1,
            contacto2: contacto2,
            contacto3: contacto3,
            direccion: direccion
        }, function (datos) {
            $('#respuesta').html(datos);
        });
    });
</script>
<style>
    td.detalle {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px; /* Puedes aumentar/reducir */
    }
</style>



