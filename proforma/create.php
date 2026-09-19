<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/proforma/listado_de_proforma.php');
?>

<!-- 🧱 Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Registrar Orden de Compra</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <!-- 🟩 CARD: Carrito -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <?php
                            // ✅ Buscar si hay una oferta pendiente existente
                            $sql_oferta = "SELECT nro_orden FROM tb_oferta WHERE estado = 'PENDIENTE' ORDER BY nro_orden DESC LIMIT 1";
                            $query_oferta = $pdo->prepare($sql_oferta);
                            $query_oferta->execute();
                            $oferta = $query_oferta->fetch(PDO::FETCH_ASSOC);

                            if ($oferta) {
                                // Usar la oferta pendiente actual
                                $nro_oferta_actual = $oferta['nro_orden'];
                            } else {
                                // Crear nueva oferta si no hay pendiente
                                $sql_max = "SELECT IFNULL(MAX(nro_orden), 0) + 1 AS nuevo_nro FROM tb_oferta";
                                $q_max = $pdo->query($sql_max);
                                $nuevo = $q_max->fetch(PDO::FETCH_ASSOC);
                                $nro_oferta_actual = $nuevo['nuevo_nro'];

                                $crear = $pdo->prepare("INSERT INTO tb_oferta (nro_orden, fecha_registro, estado) VALUES (?, NOW(), 'PENDIENTE')");
                                $crear->execute([$nro_oferta_actual]);
                            }

                            $_SESSION['nro_oferta_actual'] = $nro_oferta_actual;
                            ?>
                            <h3 class="card-title">
                                <i class="fa fa-shopping-bag"></i> Venta Nro
                                <input type="text" style="text-align:center;" value="<?php echo $nro_oferta_actual; ?>" hidden>
                            </h3>
                        </div>


                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="mb-0">Carrito</h5>
                                <div style="width:20px;"></div>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-buscar_producto">
                                    <i class="fa fa-search"></i> Buscar producto
                                </button>
                            </div>

                            <!-- 🟦 TABLA CARRITO -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-hover table-striped mt-4">
                                    <thead>
                                    <tr>
                                        <th class="text-center bg-light">Nro</th>
                                        <th class="text-center bg-light">Código</th>
                                        <th class="text-center bg-light">Descripción</th>
                                        <th class="text-center bg-light">Marca</th>
                                        <th class="text-center bg-light">Cantidad</th>
                                        <th class="text-center bg-light">Precio</th>
                                        <th class="text-center bg-light">Total</th>
                                        <th class="text-center bg-light">Acción</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $contador_de_oferta = 0;
                                    $precio_total = 0;
                                    $sql_oferta = "
                                            SELECT carr.id_xcarrito, pro.codigo AS codigo_producto, pro.descripcion, marca.nombre AS marca,
                                                   carr.cantidad, carr.precio_final
                                            FROM tb_xcarrito AS carr
                                            INNER JOIN tbp_almacen AS pro ON carr.id_producto = pro.id_producto
                                            INNER JOIN tbp_marca AS marca ON pro.id_marca = marca.id_marca
                                            WHERE carr.nro_orden = :nro_orden
                                            ORDER BY carr.id_xcarrito ASC;
                                        ";
                                    $query_oferta = $pdo->prepare($sql_oferta);
                                    $query_oferta->bindParam(':nro_orden', $nro_oferta_actual, PDO::PARAM_INT);
                                    $query_oferta->execute();
                                    $oferta_datos = $query_oferta->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($oferta_datos as $oferta_dato) {
                                        $contador_de_oferta++;
                                        $subtotal = floatval($oferta_dato['cantidad']) * floatval($oferta_dato['precio_final']);
                                        $precio_total += $subtotal;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $contador_de_oferta; ?></td>
                                            <td><?= htmlspecialchars($oferta_dato['codigo_producto']); ?></td>
                                            <td><?= htmlspecialchars($oferta_dato['descripcion']); ?></td>
                                            <td class="text-center"><?= htmlspecialchars($oferta_dato['marca']); ?></td>
                                            <td class="text-center"><?= $oferta_dato['cantidad']; ?></td>
                                            <td class="text-center"><?= number_format($oferta_dato['precio_final'], 2); ?></td>
                                            <td class="text-center"><?= number_format($subtotal, 2); ?></td>
                                            <td class="text-center">
                                                <form method="POST" action="../app/controllers/proforma/delete_xcarrito.php" style="display:inline;">
                                                    <input type="hidden" name="id_xcarrito" value="<?= $oferta_dato['id_xcarrito']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Borrar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- 🟧 MODAL BUSCAR PRODUCTO -->
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
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table id="example1" class="table table-bordered table-striped table-sm" style="width:100%;">
                                                            <thead>
                                                            <tr>
                                                                <th>Seleccionar</th>
                                                                <th>Código</th>
                                                                <th>Descripción</th>
                                                                <th>Precio</th>
                                                                <th>Marca</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <input type="hidden" id="id_producto">
                                                <div class="col-md-3">
                                                    <label>Código:</label>
                                                    <input type="text" id="codigo" class="form-control" disabled>
                                                </div>
                                                <div class="col-md-5">
                                                    <label>Descripción:</label>
                                                    <textarea id="descripcion" class="form-control" disabled></textarea>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Marca:</label>
                                                    <input type="text" id="nombre_marca" class="form-control" disabled>
                                                </div>

                                                <div class="col-md-2 ">
                                                    <div class="form-group">
                                                        <label>Precio:</label>
                                                        <input type="text" id="precio_minorista" class="form-control" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 mt-2">
                                                    <div class="form-group">
                                                        <label>Precio Final:</label>
                                                        <input type="number" id="precio_final" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 mt-2">
                                                    <label>Cantidad:</label>
                                                    <input type="number" id="cantidad" class="form-control">
                                                </div>
                                                <div class="col-md-2 mt-2">
                                                    <label for="medida">Medida:</label>
                                                    <select id="medida" name="medida" class="form-control text-center">
                                                        <option value="UND">UND</option>
                                                        <option value="PAR">PAR</option>
                                                        <option value="GALON">GALON</option>
                                                        <option value="PAQ.">PAQ.</option>
                                                        <option value="KILO">KILO</option>
                                                        <option value="MILLAR">MILLAR</option>
                                                        <option value="KL">KL</option>
                                                        <option value="DOCENA">DOCENA</option>
                                                        <option value="MTS">MTS</option>
                                                        <option value="CIENTO">CIENTO</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <button id="btn_registrar_xcarrito" class="btn btn-success mt-3 float-right">Agregar</button>
                                            <div id="respuesta_xcarrito" class="mt-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>
            </div>

            <!-- 🟪 DATOS CLIENTE Y REGISTRO -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-user-check"></i> Datos del Cliente</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="container-fluid" style="font-size:14px;">
                                <div class="row">
                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label for="ruc">RUC</label>
                                            <input type="text" id="ruc" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="razon_social">Razón Social</label>
                                            <input type="text" id="razon_social" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-11">
                                        <div class="form-group">
                                            <label for="direccion">Detalle</label>
                                            <textarea id="direccion" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>

                <div class="col-md-6">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-shopping-bag"></i> Registrar</h3>
                        </div>

                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="moneda">Moneda</label>
                                    <select class="form-control" id="moneda" name="moneda" style="text-align:center;">
                                        <option value="SOLES" <?php echo (isset($moneda) && $moneda == 'SOLES') ? 'selected' : ''; ?>>SOLES</option>
                                        <option value="DOLARES" <?php echo (isset($moneda) && $moneda == 'DOLARES') ? 'selected' : ''; ?>>DÓLARES</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="vigencia">Vigencia</label>
                                    <input type="number" id="vigencia" class="form-control">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="plazo_entrega">Plazo de entrega</label>
                                    <input type="number" id="plazo_entrega" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="garantia">Garantia</label>
                                    <input type="number" id="garantia" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="fecha_emi" class="w-100 text-center">Fecha</label>
                                    <input type="date" class="form-control text-center" id="fecha_emi" name="fecha_emi"
                                           value="<?php echo date('Y-m-d'); ?>" readonly>
                                </div>


                                <div class="form-group col-md-3">
                                <label for="total_a_cancelar">Subtotal (S/.)</label>
                                <input type="text" class="form-control" id="total_a_cancelar" style="text-align:center;" value="<?php echo number_format($precio_total, 2); ?>" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label for="total_igv">IGV (18%)</label>
                                    <input type="text" class="form-control" id="total_igv" style="text-align:center;" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="total_con_igv">Total con IGV (S/.)</label>
                                    <input type="text" class="form-control" id="total_con_igv" style="text-align:center;" readonly>
                                </div>
                            </div>

                            <div class="d-flex justify-content-right  gap-2 ">
                                <button id="btn_guardar_oferta" class="btn btn-success px-4 py-2">Guardar Orden</button>
                                <button onclick="location.href='../app/controllers/proforma/vaciar_xcarrito.php'" class="btn btn-danger px-4 py-2">Cancelar</button>
                            </div>

                            <div id="respuesta_registro_oferta" class="mt-2"></div>



                        </div><!-- /.card-body -->
                    </div><!-- /.card -->
                </div>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>

<!-- 🧠 JAVASCRIPT FINAL -->
<script>
    $(document).ready(function () {

        // 🧮 Función para recalcular IGV y Total con IGV dinámicamente
        function recalcularTotales() {
            // Normalizar el texto: quitar comas o símbolos no numéricos
            let rawSubtotal = $('#total_a_cancelar').val().replace(/[^0-9.]/g, '');
            let subtotal = parseFloat(rawSubtotal) || 0;

            let igv = subtotal * 0.18;
            let total_con_igv = subtotal + igv;

            $('#total_igv').val(igv.toFixed(2));
            $('#total_con_igv').val(total_con_igv.toFixed(2));
        }

        // 🔹 Calcular al cargar la página
        setTimeout(recalcularTotales, 200);

        // 🔹 Recalcular automáticamente si cambia el subtotal
        $('#total_a_cancelar').on('input change', function() {
            recalcularTotales();
        });

        // 🔹 Recalcular también después de llamadas AJAX (por si cambia subtotal)
        $(document).ajaxComplete(function() {
            recalcularTotales();
        });

        // 🟩 Botón "Guardar venta"
        $('#btn_guardar_oferta').click(function () {
            recalcularTotales(); // asegurar valores actualizados

            // ⚠️ Verificar razón social correctamente
            if ($('#razon_social').val().trim() === "") {
                alert("Debe ingresar una razón social");
                return;
            }

            const nro_orden = '<?php echo $nro_oferta_actual; ?>';
            const ruc = $('#ruc').val();
            const razon_social = $('#razon_social').val();
            const direccion = $('#direccion').val();

            // 🧮 Normalizar subtotal por si tiene comas
            const sub_total = parseFloat($('#total_a_cancelar').val().replace(/[^0-9.]/g, '')) || 0;
            const igv = parseFloat($('#total_igv').val()) || 0;
            const total = parseFloat($('#total_con_igv').val()) || 0;

            const moneda = $('#moneda').val();
            const garantia = $('#garantia').val();
            const vigencia = $('#vigencia').val();
            const plazo_entrega = $('#plazo_entrega').val();
            const fecha_emi = $('#fecha_emi').val();

            $.get("../app/controllers/proforma/registro_de_oferta.php", {
                nro_orden,
                ruc,
                razon_social,
                detalle: direccion,
                sub_total,
                igv,
                precio_final: total,
                moneda,
                vigencia,
                garantia,
                plazo_entrega,
                fecha_emi
            }, function (respuesta) {
                $('#respuesta_registro_oferta').html(respuesta);
            });
        });

        // 🟦 Inicializa DataTable del modal
        $('#example1').DataTable({
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
            serverSide: false,
            ajax: {
                url: '../app/controllers/palmacen/listado_almacen_1.php',
                type: 'POST'
            },
            columns: [
                {
                    data: 'id_producto',
                    render: function (data, type, row) {
                        return `
                        <button class="btn btn-info btn-seleccionar"
                            data-id_producto="${row.id_producto}"
                            data-codigo="${row.codigo}"
                            data-descripcion="${row.descripcion}"
                            data-precio_minorista="${row.precio_minorista}"
                            data-nombre_marca="${row.nombre_marca}">
                            Seleccionar
                        </button>`;
                    }
                },
                { data: 'codigo' },
                { data: 'descripcion' },
                { data: 'precio_minorista' },
                { data: 'nombre_marca' }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json' }
        });

        // 🟡 Seleccionar producto
        $(document).on('click', '.btn-seleccionar', function () {
            $('#id_producto').val($(this).data('id_producto'));
            $('#codigo').val($(this).data('codigo'));
            $('#descripcion').val($(this).data('descripcion'));
            $('#nombre_marca').val($(this).data('nombre_marca'));
            $('#precio_minorista').val($(this).data('precio_minorista'));
        });

        // 🟠 Agregar producto al carrito
        $('#btn_registrar_xcarrito').click(function() {
            let id_producto  = $('#id_producto').val();
            let cantidad     = $('#cantidad').val();
            let medida       = $('#medida').val();
            let precio_final = $('#precio_final').val();

            if (!id_producto || !cantidad || !precio_final || !medida) {
                $('#respuesta_xcarrito').html('<div class="alert alert-warning">⚠️ Faltan datos. Verifique los campos.</div>');
                return;
            }

            $.ajax({
                url: "../app/controllers/proforma/registrar_xcarrito.php",
                type: "GET",
                data: {
                    nro_orden: '<?php echo $nro_oferta_actual; ?>',
                    id_producto: id_producto,
                    cantidad: cantidad,
                    medida: medida,
                    precio_final: precio_final
                },
                success: function(response) {
                    if (response.trim() === "OK") {
                        $('#respuesta_xcarrito').html('<div class="alert alert-success">✅ Producto agregado correctamente.</div>');
                        $('#modal-buscar_producto').modal('hide');

                        // 🔁 Recargar subtotal después de agregar producto
                        setTimeout(() => {
                            location.reload();
                        }, 300);
                    } else {
                        $('#respuesta_xcarrito').html('<div class="alert alert-danger">' + response + '</div>');
                    }
                },
                error: function() {
                    $('#respuesta_xcarrito').html('<div class="alert alert-danger">❌ Error de conexión con el servidor.</div>');
                }
            });
        });
    });
</script>

