<?php
include('../app/config.php');
include('../layout/sesion.php');

// 🔥 OBTENER ID DE SALIDA (AJUSTA SEGÚN TU RUTA)
$id_salida = $_GET['id'] ?? 0;


$sql_salida = "SELECT * FROM tb_salida WHERE id_salida = :id";
$stmt = $pdo->prepare($sql_salida);
$stmt->bindParam(':id', $id_salida);
$stmt->execute();
$salida = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$salida) {
    echo "Error: No se encontró la salida";
    exit;
}

/* ==========================
   CARGAR PRODUCTOS
========================== */
$sql = "
SELECT
    i.id_producto,
    i.codigo,
    i.descripcion,
    c.nombre_categoria AS categoria,
    p.razon_social AS proveedor,
    i.tipo_liquido,
    i.factor_conversion
FROM tb_inventario i
LEFT JOIN tb_categoria c ON i.id_categoria = c.id_categoria
LEFT JOIN tb_proveedor p ON i.id_proveedor = p.id_proveedor
ORDER BY i.descripcion
";
/* ==========================
   CARGAR ALMACENES
========================== */
$sql_almacen = "
    SELECT id_almacen, ciudad
    FROM tb_almacenn
    ORDER BY ciudad
";
$query_almacen = $pdo->prepare($sql_almacen);
$query_almacen->execute();
$almacenes = $query_almacen->fetchAll(PDO::FETCH_ASSOC);


$query = $pdo->prepare($sql);
$query->execute();
$productos = $query->fetchAll(PDO::FETCH_ASSOC);


// 🔥 DETALLE DE LA SALIDA
$sql_detalle = "
SELECT 
    v.id_producto,
    i.codigo,
    i.descripcion,
    p.razon_social AS proveedor,
    c.nombre_categoria AS categoria,
    i.tipo_liquido,
    i.factor_conversion,

    d.forma_salida,
    d.factor_conversion AS factor_salida,
    v.color,
    d.cantidad,
    d.cantidad_real

FROM tb_salida_d d
INNER JOIN tb_variantes v ON d.id_variante = v.id_variante
INNER JOIN tb_inventario i ON v.id_producto = i.id_producto
LEFT JOIN tb_proveedor p ON i.id_proveedor = p.id_proveedor
LEFT JOIN tb_categoria c ON i.id_categoria = c.id_categoria

WHERE d.id_salida = :id_salida
";

$query_detalle = $pdo->prepare($sql_detalle);
$query_detalle->bindParam(':id_salida', $id_salida);
$query_detalle->execute();
$detalle_bd = $query_detalle->fetchAll(PDO::FETCH_ASSOC);


include('../layout/parte1.php');
?>

<div class="content-wrapper">

    <!-- HEADER -->
    <section class="content-header mb-3">
        <h1 class="m-0">
            <i class="fas fa-arrow-up text-danger"></i>
            Modificar Salida
        </h1>
    </section>

    <section class="content">
        <div class="card card-outline card-danger shadow-sm">

            <div class="card-header d-flex justify-content-between align-items-center">
                <strong class="text-danger">
                    <i class="fas fa-dolly"></i> Detalle de salida
                </strong>

                <button class="btn btn-danger" data-toggle="modal" data-target="#modal-productos">
                    <i class="fa fa-search"></i> Buscar producto
                </button>
            </div>

            <div class="card-body">

                <!-- TABLA DETALLE -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="bg-light text-center">
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Proveedor</th>
                            <th>Categoría</th>
                            <th>Control</th>
                            <th>Forma salida</th>
                            <th>Color</th>
                            <th>Cantidad</th>
                            <th>Factor salida</th> <!-- ✅ NUEVO -->
                            <th>Acción</th>
                        </tr>
                        </thead>

                        <tbody id="detalle"></tbody>
                    </table>
                </div>

                <hr>

                <!-- CABECERA -->
                <div class="row">
                    <div class="col-md-2">
                        <label>Fecha salida</label>
                        <input type="date" id="fecha_salida" class="form-control" value="<?= $salida['fecha_salida'] ?>">
                    </div>

                    <div class="col-md-3">
                        <label>Almacén</label>

                        <?php
                        $sqlAlmacenActual = "
        SELECT id_almacen
        FROM tb_salida_d
        WHERE id_salida = :id_salida
        LIMIT 1
    ";

                        $stmtAlmacenActual = $pdo->prepare($sqlAlmacenActual);
                        $stmtAlmacenActual->bindParam(':id_salida', $id_salida);
                        $stmtAlmacenActual->execute();

                        $id_almacen_actual = (int)($stmtAlmacenActual->fetchColumn() ?? 0);
                        ?>

                        <select id="id_almacen" class="form-control">
                            <option value="">Seleccione</option>

                            <?php foreach ($almacenes as $a): ?>
                                <option value="<?= $a['id_almacen'] ?>"
                                    <?= ((int)$a['id_almacen'] === $id_almacen_actual) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($a['ciudad']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>



                    <div class="col-md-3">
                        <label>Tipo salida</label>
                        <select id="tipo_salida" class="form-control">
                            <option value="venta" <?= $salida['tipo_salida']=='venta'?'selected':'' ?>>Venta</option>
                            <option value="consumo" <?= $salida['tipo_salida']=='consumo'?'selected':'' ?>>Consumo interno</option>
                            <option value="merma" <?= $salida['tipo_salida']=='merma'?'selected':'' ?>>Merma</option>
                            <option value="donacion" <?= $salida['tipo_salida']=='donacion'?'selected':'' ?>>Donación</option>
                            <option value="otro" <?= $salida['tipo_salida']=='otro'?'selected':'' ?>>Otro</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Nro documento</label>
                        <input type="text" id="nro_factura" class="form-control"
                               value="<?= $salida['nro_factura'] ?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-8">
                        <label>Observaciones</label>
                        <textarea id="info_adicional" class="form-control"><?= $salida['info_adicional'] ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label>Documento de salida</label>

                        <?php if (!empty($salida['doc_salida'])): ?>
                            <a href="<?= $URL ?>/inventario/docs_salida/<?= $salida['doc_salida'] ?>" target="_blank">
                                Ver documento actual
                            </a>
                        <?php endif; ?>

                        <input type="file" id="doc_salida" class="form-control mt-2">

                        <small class="text-muted">
                            PDF, imagen, Word o Excel
                        </small>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-center gap-3">
                    <button id="btn_guardar" class="btn btn-danger px-5">
                        <i class="fa fa-save"></i> Guardar salida
                    </button>

                    <button id="btn_cancelar" class="btn btn-secondary px-5 ml-2">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <script>
                        $('#btn_cancelar').click(function () {
                            if (detalle.length > 0) {
                                if (!confirm('¿Desea cancelar la salida? Se perderán los datos.')) {
                                    return;
                                }
                            }
                            window.location.href = '<?= $URL ?>/inventario/gestion.php';
                        });
                    </script>

                </div>

                <div id="respuesta"></div>
            </div>
        </div>
    </section>
</div>

<!-- MODAL PRODUCTOS -->
<div class="modal fade" id="modal-productos">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h4>Seleccionar producto</h4>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table id="example3" class="table table-bordered table-striped table-sm w-100">
                        <thead class="text-center">
                        <tr>
                            <th>Agregar</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Proveedor</th>
                            <th>Categoría</th>
                            <th>Control</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td class="text-center">
                                    <button class="btn btn-danger btn-sm btn-agregar"
                                            data-id_producto="<?= $p['id_producto'] ?>"
                                            data-codigo="<?= htmlspecialchars($p['codigo']) ?>"
                                            data-descripcion="<?= htmlspecialchars($p['descripcion'], ENT_QUOTES) ?>"
                                            data-proveedor="<?= htmlspecialchars($p['proveedor'] ?? 'SIN PROVEEDOR', ENT_QUOTES) ?>"
                                            data-categoria="<?= htmlspecialchars($p['categoria'] ?? 'SIN CATEGORÍA', ENT_QUOTES) ?>"
                                            data-tipo_liquido="<?= strtoupper($p['tipo_liquido']) ?>"
                                            data-factor_conversion="<?= $p['factor_conversion'] ?? 1 ?>">
                                        +
                                    </button>
                                </td>
                                <td><?= htmlspecialchars($p['codigo']) ?></td>
                                <td><?= mb_strimwidth($p['descripcion'], 0, 40, '...') ?></td>
                                <td><?= $p['proveedor'] ?? 'SIN PROVEEDOR' ?></td>
                                <td><?= $p['categoria'] ?? 'SIN CATEGORÍA' ?></td>
                                <td class="text-center fw-bold">
                                    <?= ($p['tipo_liquido'] === 'GRANEL') ? 'VOLUMEN' : 'UNIDAD' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include('../layout/parte2.php'); ?>


<script>

    $(document).ready(function () {

        let detalleData = <?= json_encode($detalle_bd, JSON_UNESCAPED_UNICODE) ?>;

        detalle = detalleData.map(d => ({
            es_nuevo: false,

            id_producto: d.id_producto,
            codigo: d.codigo,
            descripcion: d.descripcion,
            proveedor: d.proveedor ?? '-',
            categoria: d.categoria ?? '-',

            tipo_liquido: d.tipo_liquido,
            forma_salida: d.forma_salida,

            factor_conversion: parseFloat(d.factor_conversion) || 1,
            factor_salida: parseFloat(d.factor_salida) || 1,

            unidad_control: (d.tipo_liquido === 'GRANEL') ? 'VOLUMEN' : 'UNIDAD',

            color: d.color || '',
            cantidad: parseFloat(d.cantidad) || 0,
            cantidad_real: parseFloat(d.cantidad_real) || 0
        }));

        render();
    });

    function render() {
        let html = '';

        detalle.forEach((d, i) => {
            html += `
<tr>
  <td class="col-codigo fw-bold text-center">${d.codigo}</td>
   <td class="col-descripcion desc-corta" title="${d.descripcion}">
        ${d.descripcion}
    </td>
   <td class="col-proveedor text-center">${d.proveedor ?? '-'}</td>
    <td class="col-categoria text-center">${d.categoria ?? '-'}</td>
    <td class="col-control text-center fw-bold">${d.unidad_control}</td>

    <td class="col-forma">
       <select class="form-control form-control-sm"
    onchange="
        detalle[${i}].forma_salida = this.value;
        recalcularCantidadReal(${i});
        render();
    ">

            ${
                d.tipo_liquido === 'GRANEL'
                    ? `
    <option value="litros" selected>Litros</option>
  `

                    : `
                    <option value="unidad" ${d.forma_salida==='unidad'?'selected':''}>Unidad</option>
                    <option value="caja" ${d.forma_salida==='caja'?'selected':''}>Caja</option>
                  `
            }
        </select>
    </td>


<td class="col-color">
    <select class="form-control form-control-sm"
            onchange="cambiarColor(${i}, this.value)">
        <option value="">Seleccione</option>
<option value="estandar" ${d.color==='estandar'?'selected':''}>Estándar</option>
    <option value="rojo" ${d.color==='rojo'?'selected':''}>Rojo</option>
    <option value="azul" ${d.color==='azul'?'selected':''}>Azul</option>
   <option value="azul noche" ${d.color==='azul noche'?'selected':''}>Azul Noche</option>
<option value="azul claro" ${d.color==='azul claro'?'selected':''}>Azul Claro</option>
    <option value="verde" ${d.color==='verde'?'selected':''}>Verde</option>
    <option value="negro" ${d.color==='negro'?'selected':''}>Negro</option>
    <option value="blanco" ${d.color==='blanco'?'selected':''}>Blanco</option>
    <option value="amarillo" ${d.color==='amarillo'?'selected':''}>Amarillo</option>
    <option value="gris" ${d.color==='gris'?'selected':''}>Gris</option>
    <option value="celeste" ${d.color==='celeste'?'selected':''}>Celeste</option>
 <option value="coral" ${d.color==='coral'?'selected':''}>Coral</option>
<option value="verde claro" ${d.color==='verde claro'?'selected':''}>Verde Claro</option>
    <option value="rosado" ${d.color==='rosado'?'selected':''}>Rosado</option>
    <option value="turquesa" ${d.color==='turquesa'?'selected':''}>Turquesa</option>
    <option value="anaranjado" ${d.color==='anaranjado'?'selected':''}>Anaranjado</option>
    <option value="morado" ${d.color==='morado'?'selected':''}>Morado</option>
    <option value="marron" ${d.color==='marron'?'selected':''}>Marrón</option>
 <option value="beige" ${d.color==='beige'?'selected':''}>Beige</option>
 <option value="crema" ${d.color==='crema'?'selected':''}>Crema</option>
 <option value="dorado" ${d.color==='dorado'?'selected':''}>Dorado</option>
 <option value="fucsia" ${d.color==='fucsia'?'selected':''}>Fucsia</option>
<option value="rosado barbie" ${d.color==='rosado barbie'?'selected':''}>Rosado Barbie</option>
<option value="rosado claro" ${d.color==='rosado claro'?'selected':''}>Rosado Claro</option>
 <option value="lila" ${d.color==='lila'?'selected':''}>Lila</option>
 <option value="transparente" ${d.color==='transparente'?'selected':''}>Transparente</option>
 <option value="tricolor" ${d.color==='tricolor'?'selected':''}>Tricolor</option>
    </select>
</td>


       <td class="col-cantidad">
        <input type="number" min="1"
            class="form-control form-control-sm text-center"
            value="${d.cantidad}"
            onchange="
                detalle[${i}].cantidad = parseFloat(this.value) || 0;
                recalcularCantidadReal(${i});
            ">
       <small class="text-muted d-block text-center">
    Cantidad real: <strong>${d.cantidad_real}</strong>
</small>

    </td>
<td class="col-factor">
    <input type="number"
           step="0.01"
           min="0.01"
           class="form-control form-control-sm text-center"
           value="${d.factor_salida}"
           ${(
                d.tipo_liquido !== 'GRANEL' &&
                d.forma_salida === 'unidad'
            ) ? 'disabled' : ''}
           onchange="
                detalle[${i}].factor_salida = parseFloat(this.value) || 1;
                recalcularCantidadReal(${i});
           ">
    <small class="text-muted d-block text-center">
        Factor de salida
    </small>
</td>



    <td class="text-center">
      ${d.es_nuevo
                ? `<button class="btn btn-danger btn-sm" onclick="eliminar(${i})">
         <i class="fa fa-trash"></i>
       </button>`
                : `<button class="btn btn-secondary btn-sm" disabled title="No se puede eliminar">
         <i class="fa fa-lock"></i>
       </button>`
            }
    </td>
</tr>`;
        });

        $('#detalle').html(html);
    }
    function cambiarColor(i, color) {
        detalle[i].color = color;
    }

    function recalcularCantidadReal(i) {
        const d = detalle[i];

        if (d.tipo_liquido === 'GRANEL') {
            d.cantidad_real = d.cantidad * d.factor_salida;
        } else {
            d.cantidad_real = (d.forma_salida === 'caja')
                ? d.cantidad * d.factor_salida
                : d.cantidad;
        }

        $('#detalle tr').eq(i)
            .find('small strong')
            .text(d.cantidad_real);
    }


    /* AGREGAR PRODUCTO */
    $(document).on('click', '.btn-agregar', function () {

        const tipoLiquido = String($(this).data('tipo_liquido')).toUpperCase();
        const factor = parseFloat($(this).data('factor_conversion')) || 1;

        detalle.push({
            es_nuevo: true, // 🔥 CLAVE
            id_producto: $(this).data('id_producto'),
            codigo: $(this).data('codigo'),
            descripcion: $(this).data('descripcion'),
            proveedor: $(this).data('proveedor') || '—',
            categoria: $(this).data('categoria') || '—',

            tipo_liquido: tipoLiquido,
            forma_salida: (tipoLiquido === 'GRANEL') ? 'litros' : 'unidad',

            factor_conversion: factor,
            factor_salida: factor,

            unidad_control: (tipoLiquido === 'GRANEL') ? 'VOLUMEN' : 'UNIDAD',

            color: '',
            cantidad: 1,
            cantidad_real: (tipoLiquido === 'GRANEL') ? factor : 1
        });

        $('#modal-productos').modal('hide');
        render();
    });

    $('#btn_guardar').on('click', function () {

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

        if (detalle.length === 0) {
            alert('Debe agregar productos');
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Guardar salida');
            return;
        }
        if (!$('#id_almacen').val()) {
            alert('Debe seleccionar un almacén');
            btn.prop('disabled', false).html('<i class="fa fa-save"></i> Guardar salida');
            return;
        }


        let error = false;

        detalle.forEach((d, i) => {
            if (d.tipo_liquido !== 'GRANEL' && !d.color) {
                error = true;

                $('#detalle tr').eq(i).css('background', '#ffe5e5');
            }
        });

        if (error) {
            if (!confirm('Hay productos sin color. ¿Deseas continuar de todas formas?')) {
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Guardar salida');
                return;
            }
        }


        let formData = new FormData();
        formData.append('detalle', JSON.stringify(detalle));
        formData.append('fecha_salida', $('#fecha_salida').val());
        formData.append('id_almacen', $('#id_almacen').val());
        formData.append('tipo_salida', $('#tipo_salida').val());
        formData.append('nro_factura', $('#nro_factura').val());
        formData.append('info_adicional', $('#info_adicional').val());

        // 🔥 NUEVO
        formData.append('id_salida', <?= $id_salida ?>);

        if ($('#doc_salida')[0].files.length > 0) {
            formData.append('doc_salida', $('#doc_salida')[0].files[0]);
        }

        $.ajax({
            url: '<?= $URL ?>/app/controllers/inventario/edit_guardar_salida.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (resp) {
                if (resp.trim() === 'OK') {
                    window.location.href = '<?= $URL ?>/inventario/gestion.php';
                } else {
                    alert(resp);
                    btn.prop('disabled', false).html('<i class="fa fa-save"></i> Guardar salida');
                }
            },
            error: function () {
                alert('Error de conexión');
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Guardar salida');
            }
        });
    });


    function eliminar(i) {
        if (!detalle[i].es_nuevo) {
            alert('Este producto no se puede eliminar');

            return;
        }
        detalle.splice(i, 1);
        render();
    }

    /* DATATABLE */
    $('#example3').DataTable({
        pageLength: 5,
        autoWidth: false,
        responsive: false,
        lengthChange: false,

        language: {
            search: "Buscar producto:",
            zeroRecords: "No se encontraron productos",
            info: "Mostrando _START_ a _END_ de _TOTAL_ productos",
            infoEmpty: "No hay productos",
            infoFiltered: "(filtrado de _MAX_ productos)",
            paginate: {
                next: "Siguiente",
                previous: "Anterior"
            }
        },

        dom: '<"d-flex justify-content-center mb-3"f>rt<"d-flex justify-content-between mt-2"ip>'
    });
</script>

<style>
    .desc-corta{
        max-width:260px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }
    .table tbody tr:hover{
        background:#f8f9fa;
    }
    .card, .modal-content{
        border-radius:12px;
    }
    /* =============================
   ANCHOS DE COLUMNAS
============================= */

    /* Código */
    .col-codigo {
        width: 90px;
        white-space: nowrap;
    }

    /* Descripción (MÁS ANCHA) */
    .col-descripcion {
        min-width: 320px;
        max-width: 420px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Proveedor / Categoría */
    .col-proveedor,
    .col-categoria {
        width: 140px;
    }

    /* Control */
    .col-control {
        width: 90px;
    }
    /* =============================
       BUSCADOR MODAL PRODUCTOS
    ============================= */


    /* Forma salida */
    .col-forma {
        width: 120px;
    }

    /* Color */
    .col-color {
        width: 130px;
    }

    /* Cantidad (MÁS ANGOSTA) */
    .col-cantidad {
        width: 95px;
    }

    /* Factor salida (MÁS ANGOSTA) */
    .col-factor {
        width: 95px;
    }

    /* Acción */
    .col-accion {
        width: 70px;
    }

    /* Inputs compactos */
    .col-cantidad input,
    .col-factor input,
    .col-forma select,
    .col-color select {
        padding: 2px 6px;
        font-size: 13px;
    }

    /* Hover */
    .table tbody tr:hover {
        background: #f8f9fa;
    }

    /* ==========================
    BUSCADOR MODAL PRODUCTOS
 ========================== */
    #modal-productos .dataTables_filter {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-bottom: 15px;
    }

    #modal-productos .dataTables_filter label {
        width: 100%;
        max-width: 900px;
        font-weight: 600;
        color: #6c757d;
    }

    #modal-productos .dataTables_filter input {
        width: 100%;
        padding: 14px 22px;
        font-size: 1.05rem;
        border-radius: 30px;
        border: 1px solid #ced4da;
        transition: all 0.25s ease;
    }

    /* Focus rojo (Salida) */
    #modal-productos .dataTables_filter input:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25);
        outline: none;
    }


</style>

