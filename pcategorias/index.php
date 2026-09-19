<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/pcategorias/listado_de_categorias.php');



?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Categorias
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create2">
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
                            <h3 class="card-title">LISTADO DE CATEGORIAS</h3>
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
                                    <th><center>Nombre de la Categoria</center></th>
                                    <th><center>Marca</center></th>
                                    <th><center>Acciones</center></th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contador=0;
                                foreach ($pcategorias_datos as $pcategorias_dato) {
                                    $id_categoria =$pcategorias_dato['id_categoria'];
                                    $nombre_categoria =$pcategorias_dato['nombre_categoria'];
                                    $marca =$pcategorias_dato ['marca'];
                                    ?>
                                    <tr>
                                        <td><center> <?php echo $contador=$contador+1;?></center></td>
                                        <td><?php echo $pcategorias_dato['nombre_categoria'];?></td>
                                        <td><?php echo $pcategorias_dato['marca'];?></td>
                                        <td>
                                            <center>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                                            data-target="#modal-update<?php echo $id_categoria;?>">
                                                        <i class="fa fa-pencil-alt"></i> Editar
                                                    </button>
                                                    <!-- /.Modal para modificar categorias -->
                                                    <div class="modal fade" id="modal-update<?php echo $id_categoria; ?>">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background-color: #1f8236; color:white">
                                                                    <h4 class="modal-title">Actualizar Categoria</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="">Nombre de categoria</label>
                                                                                <input type="text" id="nombre_categoria<?php echo $id_categoria;?>" class="form-control"
                                                                                       value="<?php echo $nombre_categoria; ?>">
                                                                                <label for="">Marca</label>
                                                                                <input type="text" id="marca<?php echo $id_categoria;?>" class="form-control"
                                                                                       value="<?php echo $marca; ?>">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                    <button type="button" class="btn btn-success" id="btn_update2<?php echo $id_categoria;?>">Actualizar</button>
                                                                </div>
                                                            </div>
                                                            <!-- /.modal-content -->
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>

                                                    <script>

                                                        $('#btn_update2<?php echo $id_categoria;?>').click(function () {
                                                            var nombre_categoria = $('#nombre_categoria<?php echo $id_categoria;?>').val();
                                                            var marca = $('#marca<?php echo $id_categoria;?>').val();
                                                            var id_categoria = <?php echo $id_categoria; ?>;
                                                            var url = "../app/controllers/pcategorias/update_de_categorias.php";

                                                            $.get(url, {
                                                                nombre_categoria: nombre_categoria,
                                                                marca: marca,
                                                                id_categoria: id_categoria
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

<!-- /.Modal para crear categorias -->
<div class="modal fade" id="modal-create2" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1d3Cb6;color:white">
                <h4 class="modal-title">Creacion de nueva Categoria</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="">Nombre de categoria</label>
                            <input type="text" id="nombre_categoria" class="form-control">
                            <label for="">Marca</label>
                            <input type="text" id="marca" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_create2">Registrar</button>
                <div id="respuesta"></div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<script>
    $('#btn_create2').click(function () {
        var nombre_categoria = $('#nombre_categoria').val();
        var marca = $('#marca').val();
        var url = "../app/controllers/pcategorias/registro_de_categorias.php";
        $.get(url, {nombre_categoria: nombre_categoria, marca:marca}, function (datos) {
            $('#respuesta').html(datos);
        });
    });
</script>


