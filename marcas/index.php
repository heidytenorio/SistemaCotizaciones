<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/marcas/listado_de_marcas.php');



?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Marcas (Dueños de marca)
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
                <div class="col-md-7">
                    <div class="card card-outline  card-primary">
                        <div class="card-header">
                            <h3 class="card-title">LISTADO DE MARCAS</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                            </div>

                        </div>

                        <div class="card-body" style="display: block;">

                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th><center>Nro</center></th>
                                    <th><center>Nombre</center></th>
                                    <th><center>Nro de Contacto</center></th>
                                    <th><center>Acciones</center></th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador=0;
                                foreach ($marcas_datos as $marcas_dato) {
                                    $id_marca =$marcas_dato['id_marca'];
                                    $nombre =$marcas_dato['nombre'];
                                    $nro_contacto =$marcas_dato ['nro_contacto'];
                                    ?>
                                    <tr>
                                        <td><center> <?php echo $contador=$contador+1;?></center></td>
                                        <td><?php echo $marcas_dato['nombre'];?></td>
                                        <td><?php echo $marcas_dato['nro_contacto'];?></td>
                                        <td>
                                            <center>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                                            data-target="#modal-update<?php echo $id_marca;?>">
                                                        <i class="fa fa-pencil-alt"></i> Editar
                                                    </button>
                                                    <!-- /.Modal para modificar marca -->
                                                    <div class="modal fade" id="modal-update<?php echo $id_marca; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #1f8236; color:white">
                                                                    <h4 class="modal-title">Actualizar Marca</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="">Nombre</label>
                                                                                <input type="text" id="nombre<?php echo $id_marca;?>" class="form-control"
                                                                                       value="<?php echo $nombre; ?>">
                                                                                <label for="">Nro de Contacto</label>
                                                                                <input type="text" id="nro_contacto<?php echo $id_marca;?>" class="form-control"
                                                                                       value="<?php echo $nro_contacto; ?>">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                    <button type="button" class="btn btn-success" id="btn_update<?php echo $id_marca;?>">Actualizar</button>
                                                                </div>
                                                            </div>
                                                            <!-- /.modal-content -->
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>

                                                    <script>
                                                        $('#btn_update<?php echo $id_marca;?>').click(function () {
                                                            var nombre = $('#nombre<?php echo $id_marca;?>').val();
                                                            var nro_contacto = $('#nro_contacto<?php echo $id_marca;?>').val();
                                                            var id_marca = <?php echo $id_marca; ?>;
                                                            var url = "../app/controllers/marcas/update_de_marcas.php";

                                                            $.get(url, {
                                                                nombre: nombre,
                                                                nro_contacto: nro_contacto,
                                                                id_marca: id_marca
                                                            }, function (datos) {
                                                                $('#respuesta').html(datos);
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
                <h4 class="modal-title">Creacion de nueva Marca</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Nombre de marca</label>
                            <input type="text" id="nombre" class="form-control">
                            <label for="">Nro de contacto</label>
                            <input type="text" id="nro_contacto" class="form-control">
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
        var nombre = $('#nombre').val();
        var nro_contacto = $('#nro_contacto').val();
        var url = "../app/controllers/marcas/registro_de_marcas.php";
        $.get(url, {nombre: nombre, nro_contacto:nro_contacto}, function (datos) {
            $('#respuesta').html(datos);
        });
    });
</script>


