<?php
include ('../app/config.php');
include ('../layout/sesion.php');

include ('../app/controllers/almacen/listado_de_almacen_1.php');
include ('../app/controllers/clientes/listado_de_clientes.php');
include ('../app/controllers/cotizacion/listado_de_cotizacion.php');

include('../layout/parte1.php');

?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Registro de una nueva Cotización</h1>
                </div>
            </div>
        </div>
    </div>
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <?php
                            // 🔧 CAMBIO: calcular siempre el correlativo desde tb_venta
                            $sql_ventas = "SELECT MAX(nro_venta) as max_venta FROM tb_venta";
                            $query_ventas = $pdo->prepare($sql_ventas);
                            $query_ventas->execute();
                            $venta = $query_ventas->fetch(PDO::FETCH_ASSOC);

                            $nro_venta_actual = ($venta && $venta['max_venta']) ? $venta['max_venta'] + 1 : 1;

                            // Guardamos en sesión para usarlo en carrito y venta
                            $_SESSION['nro_venta_actual'] = $nro_venta_actual;
                            ?>


                            <h3 class="card-title">
                                <i class="fa fa-shopping-bag"></i> Venta Nro
                                <input type="text" style="text-align: center" value="<?php echo $nro_venta_actual; ?>" hidden>
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="mb-0">Carrito</h5>
                                <div style="width: 20px"></div>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-buscar_producto">
                                    <i class="fa fa-search"></i> Buscar producto
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-hover table-striped mt-4">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="background-color: #e7e7e7;">Nro</th>
                                        <th class="text-center" style="background-color: #e7e7e7;">Codigo</th>
                                        <th class="text-center" style="background-color: #e7e7e7;">Descripcion</th>
                                        <th class="text-center" style="background-color: #e7e7e7;">Marca</th>
                                        <th class="text-center" style="background-color: #e7e7e7;">Precio</th>
                                        <th class="text-center" style="background-color: #e7e7e7;">Cantidad</th>
                                        <th class="text-center" style="background-color: #e7e7e7;">Total</th>
                                        <th class="text-center" style="background-color: #e7e7e7;">Acción</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $contador_de_carrito = 0;
                                    $cantidad_total=0;
                                    $precio_unitario_total=0;
                                    $precio_total=0;
                                    $sql_carrito = "SELECT carr.id_carrito, pro.codigo AS nombre_producto, pro.descripcion, marca.nombre AS marca, 
                       carr.cantidad, carr.precio_final 
                FROM tb_carrito AS carr 
                INNER JOIN tb_almacen AS pro ON carr.id_producto = pro.id_producto 
                INNER JOIN tb_marca AS marca ON pro.id_marca = marca.id_marca 
                WHERE nro_venta = $nro_venta_actual 
                ORDER BY id_carrito ASC;";

                                    $query_carrito = $pdo->prepare($sql_carrito);
                                    $query_carrito->execute();
                                    $carrito_datos = $query_carrito->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($carrito_datos as $carrito_dato) {
                                        $id_carrito = $carrito_dato['id_carrito'];
                                        $contador_de_carrito = $contador_de_carrito + 1;
                                        $cantidad_total=$cantidad_total+$carrito_dato['cantidad'];
                                        $precio_unitario_total=$precio_unitario_total+floatval($carrito_dato['precio_final']) ;
                                        ?>
                                        <tr>
                                            <td><center><?php echo $contador_de_carrito; ?></center></td>
                                            <td><?php echo $carrito_dato['nombre_producto']; ?></td>
                                            <td><?php echo $carrito_dato['descripcion']; ?></td>
                                            <td><?php echo $carrito_dato['marca']; ?></td>
                                            <td><center><?php echo $carrito_dato['precio_final']; ?></center></td>
                                            <td><center><?php echo $carrito_dato['cantidad']; ?></center></td>

                                            <td>
                                                <center>
                                                    <?php
                                                    $cantidad = floatval($carrito_dato['cantidad']);
                                                    $precio_final = floatval($carrito_dato['precio_final']);
                                                    $subtotal = $cantidad * $precio_final;
                                                    echo number_format($subtotal, 2, '.', ''); // Muestra solo 2 decimales
                                                    $precio_total = $precio_total + $subtotal; // cálculo interno sigue exacto

                                                    ?>
                                                </center>
                                            </td>
                                            <td>
                                                <center>
                                                    <form method="POST" action="../app/controllers/cotizacion/delete_carrito.php" style="display:inline;">
                                                        <input type="hidden" name="id_carrito" value="<?php echo $carrito_dato['id_carrito']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">Borrar</button>
                                                    </form>

                                                </center>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <!--                                    <tr>-->
                                    <!--                                        <th colspan="4" style="...">Total</th>-->
                                    <!--                                        <th><center>--><?php //echo $cantidad_total; ?><!--</center></th>-->
                                    <!--                                        <th><center>--><?php //echo $precio_unitario_total; ?><!--</center></th>-->
                                    <!--                                        <th style="background-color: #fff819"><center>--><?php //echo $precio_total; ?><!--</center></th>-->
                                    <!--                                    </tr>-->
                                    </tbody>
                                </table>
                            </div>

                            <!-- MODAL DE PRODUCTOS -->
                            <div class="modal fade" id="modal-buscar_producto">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h4 class="modal-title">Búsqueda de Productos</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table id="example1" class="table table-bordered table-striped table-sm">
                                                    <thead>
                                                    <tr>
                                                        <th>Seleccionar</th>
                                                        <th>Código</th>
                                                        <th>Descripción</th>
                                                        <th>Precio</th>
                                                        <th>Marca</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($productos_datos as $producto): ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <button class="btn btn-info btn-seleccionar"
                                                                        data-id_producto="<?= $producto['id_producto']; ?>"
                                                                        data-codigo="<?= $producto['codigo']; ?>"
                                                                        data-descripcion="<?= htmlspecialchars($producto['descripcion'], ENT_QUOTES); ?>"
                                                                        data-precio_mayorista="<?= $producto['precio_mayorista']; ?>"
                                                                        data-nombre_marca="<?= $producto['nombre_marca']; ?>">
                                                                    Seleccionar
                                                                </button>
                                                            </td>
                                                            <td><?= $producto['codigo']; ?></td>
                                                            <td><?= $producto['descripcion']; ?></td>
                                                            <td><?= $producto['precio_mayorista']; ?></td>
                                                            <td><?= $producto['nombre_marca']; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>

                                                <div class="row">
                                                    <input type="hidden" id="id_producto">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Código:</label>
                                                            <input type="text" id="codigo" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label>Descripción:</label>
                                                            <textarea id="descripcion" class="form-control"  disabled></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 ">
                                                        <div class="form-group">
                                                            <label>Marca:</label>
                                                            <input type="text" id="nombre_marca" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 ">
                                                        <div class="form-group">
                                                            <label>Precio Mayorista:</label>
                                                            <input type="text" id="precio_mayorista" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 ">
                                                        <div class="form-group">
                                                            <label>Precio Final:</label>
                                                            <input type="number" id="precio_final" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 ">
                                                        <div class="form-group">
                                                            <label>Cantidad:</label>
                                                            <input type="number" id="cantidad" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <button id="btn_registrar_carrito" class="btn btn-success mt-3 float-right">Agregar</button>

                                                <div id="respuesta_carrito" class="mt-2"></div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                $(document).on('click', '.btn-seleccionar', function () {
                                    $('#id_producto').val($(this).data('id_producto'));
                                    $('#codigo').val($(this).data('codigo'));
                                    $('#descripcion').val($(this).data('descripcion'));
                                    $('#precio_mayorista').val($(this).data('precio_mayorista'));
                                    $('#nombre_marca').val($(this).data('nombre_marca'));
                                });

                                $('#btn_registrar_carrito').click(function () {
                                    var nro_venta = '<?php echo $nro_venta_actual; ?>';
                                    var id_producto = $('#id_producto').val();
                                    var cantidad = $('#cantidad').val();
                                    var precio_final = $('#precio_final').val();

                                    if (id_producto === "") {
                                        alert("Debe seleccionar un producto.");
                                    } else if (cantidad === "") {
                                        alert("Debe ingresar cantidad.");
                                    } else {
                                        var url = "../app/controllers/cotizacion/registrar_carrito.php";
                                        $.get(url, {
                                            nro_venta: nro_venta,
                                            id_producto: id_producto,
                                            cantidad: cantidad,
                                            precio_final: precio_final
                                        }, function (datos) {
                                            if (datos.trim() === "OK") {
                                                // Recarga para ver la tabla actualizada
                                                window.location.reload();
                                            } else {
                                                $('#respuesta_carrito').html(datos);
                                            }
                                        }).fail(function(){
                                            alert("No se pudo agregar al carrito.");
                                        });
                                    }
                                });

                            </script>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <?php
                            $contador_de_ventas = count($ventas_datos);
                            ?>
                            <h3 class="card-title">
                                <i class="fa fa-user-check"></i> Datos del Cliente
                                <!--                                <input type="text" class="text-center" value="--><?php //echo $contador_de_ventas + 1; ?><!--" disabled>-->
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="p-3 d-flex align-items-center">
                                    <h5 class="mb-0">Cliente</h5>
                                    <div class="mx-2"></div>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-buscar_proveedor">
                                        <i class="fa fa-search"></i> Buscar Cliente
                                    </button>
                                </div>
                                <div class="container-fluid" style="font-size: 14px">
                                    <div class="row">

                                        <input type="text" id="id_cliente" hidden>
                                        <div class="col-md-3 ml-2"> <!-- Agregado ml-2 para separar -->
                                            <div class="form-group">
                                                <label for="ruc">RUC</label>
                                                <input type="text" id="ruc" class="form-control" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="razon_social">Razón Social</label>
                                                <input type="text" id="razon_social" class="form-control" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="direccion">Dirección</label>
                                                <textarea id="direccion" class="form-control" rows="3" disabled></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-shopping-bag"></i> Registrar
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <!---->
                                    <div class="form-row">

                                        <div class="form-group col-md-6">
                                            <label for="tipo_marca">MARCA</label>
                                            <select class="form-control" id="tipo_marca" name="tipo_marca" style="text-align: center;">
                                                <option value="Principal" <?php echo (isset($tipo_marca) && $tipo_marca == 'Principal') ? 'selected' : ''; ?>>PRINCIPAL</option>
                                                <option value="Vianfortpro" <?php echo (isset($tipo_marca) && $tipo_marca == 'Vianfortpro') ? 'selected' : ''; ?>>VIANFORTPRO</option>
                                            </select>
                                        </div>

                                        <!-- Subtotal -->
                                        <div class="form-group col-md-6">
                                            <label for="total_a_cancelar">Subtotal (S/.)</label>
                                            <input type="text" class="form-control" id="total_a_cancelar"
                                                   style="text-align: center;"
                                                   value="<?php echo number_format($precio_total, 2, '.', ''); ?>" readonly>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <!-- IGV -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="total_igv">IGV (18%)</label>
                                                <input type="text" class="form-control" id="total_igv"
                                                       style="text-align: center;" readonly>
                                            </div>
                                        </div>

                                        <!-- Total con IGV -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="total_con_igv">Total con IGV (S/.)</label>
                                                <input type="text" class="form-control" id="total_con_igv"
                                                       style="text-align: center;" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botón para guardar venta -->
                                    <div class="form-group">
                                        <button id="btn_guardar_venta" class="btn btn-primary btn-block">Guardar venta</button>
                                        <div id="respuesta_registro_venta"></div>
                                        <button onclick="location.href='../app/controllers/cotizacion/vaciar_carrito.php'"
                                                class="btn btn-danger btn-block">
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Script para cálculo de IGV -->
                        <script>
                            $(document).ready(function () {
                                const subtotal = parseFloat($('#total_a_cancelar').val());

                                if (!isNaN(subtotal)) {
                                    const igv = subtotal * 0.18;
                                    const total = subtotal + igv;

                                    $('#total_igv').val(igv.toFixed(2));
                                    $('#total_con_igv').val(total.toFixed(2));
                                } else {
                                    $('#total_igv').val('');
                                    $('#total_con_igv').val('');
                                }

                                // Guardar venta con AJAX
                                $('#btn_guardar_venta').click(function () {
                                    var nro_venta = '<?php echo $nro_venta_actual; ?>';
                                    var id_cliente = $('#id_cliente').val();
                                    var tipo_marca = $('#tipo_marca').val();
                                    var sub_total = $('#total_a_cancelar').val();
                                    var igv = $('#total_igv').val();
                                    var precio_final = $('#total_con_igv').val();

                                    if (id_cliente === "") {
                                        alert("Debe llenar los datos del cliente");
                                        return;
                                    }

                                    var url = "../app/controllers/cotizacion/registro_de_venta.php";

                                    $.get(url, {
                                        nro_venta: nro_venta,
                                        id_cliente: id_cliente,
                                        sub_total: sub_total,
                                        tipo_marca:tipo_marca,
                                        igv: igv,
                                        precio_final: precio_final
                                    }, function (respuesta) {
                                        $('#respuesta_registro_venta').html(respuesta);
                                    });
                                });
                            });
                        </script>



                        <!-- Modal para seleccionar cliente -->
                        <div class="modal fade" id="modal-buscar_proveedor">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h4 class="modal-title">Búsqueda de Clientes</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table id="example2" class="table table-bordered table-striped table-sm">
                                                <thead>
                                                <tr class="text-center">
                                                    <th>Nº</th>
                                                    <th>Seleccionar</th>
                                                    <th>RUC</th>
                                                    <th>Razón Social</th>
                                                    <th>Dirección</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $contador = 0;
                                                foreach ($clientes_datos as $clientes_dato) {
                                                    $id_cliente = $clientes_dato['id_cliente'];
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?php echo ++$contador; ?></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-info" id="btn_seleccionar_cliente<?php echo $id_cliente; ?>">
                                                                Seleccionar
                                                            </button>
                                                            <script>
                                                                $('#btn_seleccionar_cliente<?php echo $id_cliente; ?>').click(function () {
                                                                    $('#id_cliente').val('<?php echo $id_cliente; ?>');
                                                                    $('#ruc').val('<?php echo $clientes_dato['ruc']; ?>');
                                                                    $('#razon_social').val('<?php echo $clientes_dato['razon_social']; ?>');
                                                                    $('#direccion').val('<?php echo $clientes_dato['direccion']; ?>');
                                                                    $('#modal-buscar_proveedor').modal('toggle');
                                                                });
                                                            </script>
                                                        </td>
                                                        <td><?php echo $clientes_dato['ruc']; ?></td>
                                                        <td><?php echo $clientes_dato['razon_social']; ?></td>
                                                        <td><?php echo $clientes_dato['direccion']; ?></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Fin del modal -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<?php include ('../layout/mensajes.php'); ?>
<?php include ('../layout/parte2.php'); ?>



<script>
    $(function () {
        $("#example1").DataTable({
            "pageLength": 5,
            "language": {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Productos",
                "infoEmpty": "Mostrando 0 a 0 de 0 Productos",
                "infoFiltered": "(Filtrado de _MAX_ total Productos)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Productos",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "responsive": true, "lengthChange": true, "autoWidth": false,

        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    $(function () {
        $("#example2").DataTable({
            "pageLength": 5,
            "language": {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Productos",
                "infoEmpty": "Mostrando 0 a 0 de 0 Productos",
                "infoFiltered": "(Filtrado de _MAX_ total Productos)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Productos",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "responsive": true, "lengthChange": true, "autoWidth": false,

        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

</script>

