<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/proforma/listado_de_proforma.php');

// ✅ Recibir nro_orden desde GET (parámetro id en la URL)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $nro_orden = intval($_GET['id']);
} else {
    echo "<div class='alert alert-danger'>Error: No se recibió el número de orden correctamente.</div>";
    include('../layout/parte2.php');
    exit();
}

// Guardar en sesión (si tu lógica anterior lo requería)
$_SESSION['nro_oferta_actual'] = $nro_orden;

// ✅ Cargar datos de la oferta (cliente, fechas, etc.)
$q_oferta = $pdo->prepare("SELECT * FROM tb_oferta WHERE nro_orden = :nro_orden LIMIT 1");
$q_oferta->bindParam(':nro_orden', $nro_orden, PDO::PARAM_INT);
$q_oferta->execute();
$oferta = $q_oferta->fetch(PDO::FETCH_ASSOC);

// si no existe, avisar
if (!$oferta) {
    echo "<div class='alert alert-warning'>No se encontró la orden N° {$nro_orden}.</div>";
    include('../layout/parte2.php');
    exit();
}

// ✅ Cargar items del carrito
$sql_carrito = "
    SELECT carr.id_xcarrito, pro.id_producto, pro.codigo AS codigo_producto, pro.descripcion,
           marca.nombre AS marca, carr.cantidad, carr.precio_final
    FROM tb_xcarrito AS carr
    INNER JOIN tbp_almacen AS pro ON carr.id_producto = pro.id_producto
    INNER JOIN tbp_marca AS marca ON pro.id_marca = marca.id_marca
    WHERE carr.nro_orden = :nro_orden
    ORDER BY carr.id_xcarrito ASC
";
$q_carrito = $pdo->prepare($sql_carrito);
$q_carrito->bindParam(':nro_orden', $nro_orden, PDO::PARAM_INT);
$q_carrito->execute();
$carrito_items = $q_carrito->fetchAll(PDO::FETCH_ASSOC);

// ✅ Calcular totales
$precio_total = 0.0;
foreach ($carrito_items as $it) {
    $precio_total += floatval($it['cantidad']) * floatval($it['precio_final']);
}
$igv = $precio_total * 0.18;
$total_con_igv = $precio_total + $igv;

?>
<!-- 🧱 Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Editar / Registrar Orden de Compra - N° <?= htmlspecialchars($nro_orden) ?></h1>
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
                            <h3 class="card-title">
                                <i class="fa fa-shopping-bag"></i> Venta Nro
                                <input type="text" style="text-align:center;" value="<?= htmlspecialchars($nro_orden); ?>" hidden>
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="mb-0">Carrito</h5>
                                <div style="width:20px;"></div>
                                <!-- Botón que abre el modal (misma UX que tenías) -->
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
                                    foreach ($carrito_items as $oferta_dato) {
                                        $contador_de_oferta++;
                                        $subtotal = floatval($oferta_dato['cantidad']) * floatval($oferta_dato['precio_final']);
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
                                                <!-- Mantengo tu acción de borrar (POST preferible, pero respeté tu estructura) -->

                                                <form method="POST" action="../app/controllers/proforma/delete_xcarritoo.php" style="display:inline;">
                                                    <input type="hidden" name="id_xcarrito" value="<?= $oferta_dato['id_xcarrito']; ?>">
                                                    <input type="hidden" name="nro_orden" value="<?= $nro_orden; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Borrar</button>
                                                </form>

                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- 🟧 MODAL BUSCAR PRODUCTO (REUTILIZADO EXACTAMENTE) -->
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
                            <!-- /.modal -->
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
                                            <input type="text" id="ruc" class="form-control" value="<?= htmlspecialchars($oferta['ruc'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="razon_social">Razón Social</label>
                                            <input type="text" id="razon_social" class="form-control" value="<?= htmlspecialchars($oferta['razon_social'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-11">
                                        <div class="form-group">
                                            <label for="direccion">Detalle</label>
                                            <textarea id="direccion" class="form-control" rows="3"><?= htmlspecialchars($oferta['detalle'] ?? '') ?></textarea>
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
                                        <option value="SOLES" <?= (isset($oferta['moneda']) && $oferta['moneda'] == 'SOLES') ? 'selected' : ''; ?>>SOLES</option>
                                        <option value="DOLARES" <?= (isset($oferta['moneda']) && $oferta['moneda'] == 'DOLARES') ? 'selected' : ''; ?>>DÓLARES</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="vigencia">Vigencia</label>
                                    <input type="number" id="vigencia" class="form-control" value="<?= htmlspecialchars($oferta['vigencia'] ?? '') ?>">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="plazo_entrega">Plazo de entrega</label>
                                    <input type="number" id="plazo_entrega" class="form-control" value="<?= htmlspecialchars($oferta['plazo_entrega'] ?? '') ?>">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="garantia">Garantia</label>
                                    <input type="number" id="garantia" class="form-control" value="<?= htmlspecialchars($oferta['garantia'] ?? '') ?>">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="fecha_emi" class="w-100 text-center">Fecha</label>
                                    <input type="date" class="form-control text-center" id="fecha_emi" name="fecha_emi"
                                           value="<?= !empty($oferta['fecha_emi']) ? htmlspecialchars(date('Y-m-d', strtotime($oferta['fecha_emi']))) : date('Y-m-d'); ?>">

                                </div>

                                <div class="form-group col-md-3">
                                    <label for="total_a_cancelar">Subtotal (S/.)</label>
                                    <input type="text" class="form-control" id="total_a_cancelar" style="text-align:center;"
                                           value="<?= number_format($precio_total, 2); ?>" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label for="total_igv">IGV (18%)</label>
                                    <input type="text" class="form-control" id="total_igv" style="text-align:center;"
                                           value="<?= number_format($igv, 2); ?>" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="total_con_igv">Total con IGV (S/.)</label>
                                    <input type="text" class="form-control" id="total_con_igv" style="text-align:center;"
                                           value="<?= number_format($total_con_igv, 2); ?>" readonly>
                                </div>
                            </div>

                            <div class="d-flex justify-content-right  gap-2 ">
                                <button id="btn_guardar_oferta" class="btn btn-success px-4 py-2">Guardar venta</button>
                                <button onclick="location.href='../proforma/index.php'" class="btn btn-danger px-4 py-2">Cancelar</button>
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

<script>
    $(document).ready(function() {
        // 🧮 Recalcular totales
        function recalcularTotales() {
            // 🧹 Limpiar el valor para eliminar comas o símbolos no numéricos
            const rawSubtotal = $('#total_a_cancelar').val().replace(/[^0-9.]/g, '');
            const subtotal = parseFloat(rawSubtotal) || 0;

            const igv = subtotal * 0.18;
            const total = subtotal + igv;

            // Mostrar los valores con dos decimales
            $('#total_igv').val(igv.toFixed(2));
            $('#total_con_igv').val(total.toFixed(2));
        }
        // 💾 Guardar oferta
        $('#btn_guardar_oferta').click(function() {
            // 🔁 Antes de enviar, aseguramos que los valores estén actualizados
            recalcularTotales();

            if ($('#razon_social').val().trim() === "") {
                alert("Debe ingresar una razón social");
                return;
            }

            const data = {
                nro_orden: '<?= $nro_orden; ?>',
                ruc: $('#ruc').val(),
                razon_social: $('#razon_social').val(),
                detalle: $('#direccion').val(),
                sub_total: parseFloat($('#total_a_cancelar').val().replace(/[^0-9.]/g, '')) || 0,
                igv: parseFloat($('#total_igv').val()) || 0,
                precio_final: parseFloat($('#total_con_igv').val()) || 0,
                moneda: $('#moneda').val(),
                vigencia: $('#vigencia').val(),
                garantia: $('#garantia').val(),
                plazo_entrega: $('#plazo_entrega').val(),
                fecha_emi: $('#fecha_emi').val()
            };

            if (razon_social === "") {
                alert("Debe ingresar una razón social");
                return;
            }


            $.get("../app/controllers/proforma/update_oferta.php", data, function(respuesta) {
                $('#respuesta_registro_oferta').html(respuesta);
            });
        });

        // 🟦 Cargar productos (DataTable)
        $('#example1').DataTable({
            pageLength: 5,      // 5 registros por página
            lengthMenu: [5, 10, 25, 50], // Opciones de cantidad por página
            serverSide: false,  // DESACTIVA el server-side para que cargue todos los datos de golpe
            ajax: {
                url: '../app/controllers/palmacen/listado_almacen_1.php',
                type: 'POST'
            },
            columns: [
                {
                    data: 'id_producto',
                    render: function(data, type, row) {
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

        // 🟢 Seleccionar producto del modal
        $(document).on('click', '.btn-seleccionar', function() {
            $('#id_producto').val($(this).data('id_producto'));
            $('#codigo').val($(this).data('codigo'));
            $('#descripcion').val($(this).data('descripcion'));
            $('#nombre_marca').val($(this).data('nombre_marca'));
            $('#precio_minorista').val($(this).data('precio_minorista'));
        });

        // 🟠 Agregar producto
        $('#btn_registrar_xcarrito').click(function() {
            const id_producto  = $('#id_producto').val();
            const cantidad     = $('#cantidad').val();
            const medida       = $('#medida').val();
            const precio_final = $('#precio_final').val();

            if (!id_producto || !cantidad || !precio_final || !medida) {
                $('#respuesta_xcarrito').html('<div class="alert alert-warning">⚠️ Faltan datos. Verifique los campos.</div>');
                return;
            }

            $.ajax({
                url: "../app/controllers/proforma/registrar_xcarrito.php",
                type: "GET",
                data: {
                    nro_orden: '<?= $nro_orden; ?>',
                    id_producto, cantidad, medida, precio_final
                },
                success: function(response) {
                    if (response.trim() === "OK") {
                        $('#respuesta_xcarrito').html('<div class="alert alert-success">✅ Producto agregado correctamente.</div>');
                        $('#modal-buscar_producto').modal('hide');
                        setTimeout(() => location.reload(), 400);
                    } else {
                        $('#respuesta_xcarrito').html('<div class="alert alert-danger">' + response + '</div>');
                    }
                },
                error: function() {
                    $('#respuesta_xcarrito').html('<div class="alert alert-danger">❌ Error de conexión.</div>');
                }
            });
        });
    });
</script>
