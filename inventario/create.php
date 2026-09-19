<?php
include('../app/config.php');
include('../layout/sesion.php');



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
ORDER BY i.descripcion;

";

/* ==========================
   CARGAR ALMACENES
========================== */
$sqlAlm = "SELECT id_almacen, ciudad, nombre FROM tb_almacenn WHERE estado = 1 ORDER BY ciudad";
$qAlm = $pdo->prepare($sqlAlm);
$qAlm->execute();
$almacenes = $qAlm->fetchAll(PDO::FETCH_ASSOC);

$query = $pdo->prepare($sql);
$query->execute();
$productos = $query->fetchAll(PDO::FETCH_ASSOC);

include('../layout/parte1.php');
?>

<div class="content-wrapper">
    <section class="content-header mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <h1 class="m-0">
                <i class="fas fa-arrow-down text-primary"></i>
                Registro de Entrada
            </h1>
        </div>
    </section>


    <section class="content">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong class="text-primary">
                    <i class="fas fa-boxes"></i> Detalle de productos
                </strong>

                <button class="btn btn-primary" data-toggle="modal" data-target="#modal-productos">
                    <i class="fa fa-search"></i> Buscar producto
                </button>
            </div>

            <div class="card-body">

                <!-- TABLA DETALLE -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="bg-light text-center">
                        <tr>
                            <th style="width: 8%">Código</th>
                            <th style="width: 22%">Descripción</th>
                            <th style="width: 14%">Proveedor</th>
                            <th style="width: 14%">Categoría</th>
                            <th style="width: 8%">Control</th>
                            <th style="width: 10%">Color</th>
                            <th style="width: 8%">Cantidad</th>
                            <th style="width: 8%">Factor Conv.</th>
                            <th style="width: 8%">Costo U.</th>

                            <th style="width: 8%">Acción</th>
                        </tr>
                        </thead>
                        <tbody id="detalle"></tbody>
                    </table>
                </div>

                <hr>

                <!-- DATOS CABECERA -->
                <!-- DATOS CABECERA -->
                <div class="row">

                    <!-- FILA 1 -->
                    <div class="col-md-3">
                        <label>Fecha emisión</label>
                        <input type="date" id="fecha_emision" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Fecha ingreso</label>
                        <input type="date" id="fecha_ingreso" class="form-control">

                    </div>

                    <div class="col-md-3">
                        <label>Tipo de Doc.</label>
                        <select id="tipo_factura" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="factura">Factura</option>
                            <option value="boleta">Boleta</option>
                            <option value="ordencompra">Orden de Compra</option>
                            <option value="otro">Otros</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Nro Documento</label>
                        <input type="text" id="nro_factura" class="form-control">
                    </div>

                    <!-- FILA 2 -->
                    <div class="col-md-4 mt-3">
                        <label>Almacén</label>
                        <select id="id_almacen" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($almacenes as $a): ?>
                                <option value="<?= $a['id_almacen'] ?>">
                                    <?= htmlspecialchars($a['ciudad'].' - '.$a['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>Documento de Entrada</label>
                        <input type="file" name="doc_entrada" id="doc_entrada" class="form-control"
                               accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <small class="text-muted">PDF, Word o Excel</small>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>Responsable</label>
                        <input type="text" id="responsable" class="form-control"
                               value="<?= $_SESSION['nombre_usuario'] ?? '' ?>" readonly>
                    </div>

                    <!-- FILA 3 -->
                    <div class="col-md-12 mt-3">
                        <label>Observaciones</label>
                        <textarea id="observaciones" class="form-control" rows="2"></textarea>
                    </div>

                </div>




                <hr>
                <div class="d-flex justify-content-center gap-3">
                    <button id="btn_guardar" class="btn btn-success px-5">

                    <i class="fa fa-save"></i> Guardar entrada
                </button>

                    <button id="btn_cancelar" class="btn btn-secondary px-5 ml-2">

                    <i class="fa fa-times"></i> Cancelar
                </button>


                <div id="respuesta"></div>

            </div>
        </div>
    </section>
</div>

<!-- ==========================
      MODAL PRODUCTOS
========================== -->
<div class="modal fade" id="modal-productos">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

    <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h4>Seleccionar producto</h4>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table id="example3" class="table table-bordered table-striped table-sm w-100">

                    <thead>
                    <tr>
                        <th>Agregar</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th style="width: 25%">Proveedor</th>
                        <th style="width: 20%">Categoría</th>

                        <th>Control</th> <!-- 👈 NUEVO -->
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($productos as $p): ?>
                        <tr>
                            <td class="text-center">
                                <button class="btn btn-info btn-sm btn-agregar"
                                        data-id_producto="<?= $p['id_producto'] ?>"
                                        data-codigo="<?= $p['codigo'] ?>"
                                        data-descripcion="<?= htmlspecialchars($p['descripcion'], ENT_QUOTES) ?>"
                                        data-proveedor="<?= $p['proveedor'] ?>"
                                        data-categoria="<?= $p['categoria'] ?>"
                                        data-tipo_liquido="<?= $p['tipo_liquido'] ?>"

                                        data-factor_conversion="<?= ($p['factor_conversion'] !== null && $p['factor_conversion'] > 0) ? $p['factor_conversion'] : '' ?>">

                                +
                                </button>
                            </td>
                            <td><?= $p['codigo'] ?></td>
                            <td class="desc-modal"><?= htmlspecialchars($p['descripcion']) ?></td>

                            <td class="text-truncate" title="<?= $p['proveedor'] ?? 'SIN PROVEEDOR' ?>">
                                <?= $p['proveedor'] ?? 'SIN PROVEEDOR' ?>
                            </td>
                            <td class="text-truncate" title="<?= $p['categoria'] ?? 'SIN CATEGORÍA' ?>">
                                <?= $p['categoria'] ?? 'SIN CATEGORÍA' ?>
                            </td>

                            <td class="text-center">
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
    let detalle = [];

    /* ==========================
       AGREGAR PRODUCTO
    ========================== */
    $(document).on('click', '.btn-agregar', function () {
        const tipoLiquido = String($(this).data('tipo_liquido')).toUpperCase();

        detalle.push({
            id_producto: $(this).data('id_producto'),
            codigo: $(this).data('codigo'),
            descripcion: $(this).data('descripcion'),
            proveedor: $(this).data('proveedor'),
            categoria: $(this).data('categoria'),
            tipo_liquido: tipoLiquido,
            factor_conversion: $(this).data('factor_conversion')
                ? parseFloat($(this).data('factor_conversion'))
                : null,



            unidad_control: (tipoLiquido === 'GRANEL') ? 'VOLUMEN' : 'UNIDAD',

            color: '',
            cantidad: 1,
            costo_unitario: 0.00
        });


        $('#modal-productos').modal('hide');
        render();
    });


    /* ==========================
       RENDER TABLA
    ========================== */
    function render() {
        let html = '';

        detalle.forEach((d, i) => {
            html += `
        <tr>
            <td class="text-center fw-bold">${d.codigo}</td>
            <td class="desc-corta" title="${d.descripcion}">
    ${d.descripcion}
</td>
            <td class="text-center">${d.proveedor ?? '—'}</td>
            <td class="text-center">${d.categoria ?? '—'}</td>
<td class="text-center fw-bold">
    ${d.unidad_control}
</td>

            <td>
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

        <input type="number" min="0.01" step="0.01"
       class="form-control form-control-sm text-center"
       value="${d.factor_conversion ?? ''}"
       onchange="
           let v = parseFloat(this.value);
           detalle[${i}].factor_conversion = (v > 0) ? v : null;
       ">

<td>
    <input type="number" min="0.01" step="0.01"
           class="form-control form-control-sm text-center"
           value="${d.cantidad}"
           onchange="detalle[${i}].cantidad = parseFloat(this.value) || 0">
</td>

<td>
    <input type="number" min="0.01" step="0.01"
           class="form-control form-control-sm text-center"
           value="${d.factor_conversion ?? ''}"
           ${d.tipo_liquido !== 'GRANEL' ? 'disabled' : ''}
           onchange="
               let v = parseFloat(this.value);
               detalle[${i}].factor_conversion = (v > 0) ? v : null;
           ">
</td>

<td>
    <input type="number" min="0.01" step="0.01"
           class="form-control form-control-sm text-center"
           value="${d.costo_unitario}"
           onchange="detalle[${i}].costo_unitario = parseFloat(this.value) || 0">
</td>


            <td class="text-center">
                <button class="btn btn-secondary btn-sm"
                        onclick="duplicar(${i})">
                    <i class="fa fa-clone"></i>
                </button>
                <button class="btn btn-danger btn-sm"
                        onclick="eliminar(${i})">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`;
        });

        $('#detalle').html(html);
    }
    function cambiarColor(index, color) {

        let actual = detalle[index];

        let repetido = detalle.some((d, i) =>
            i !== index &&
            d.id_producto === actual.id_producto &&
            d.color === color
        );

        if (repetido) {
            alert('Este producto ya tiene ese color');
            return;
        }

        detalle[index].color = color;
    }



    function eliminar(i) {
        detalle.splice(i, 1);
        render();
    }

    function duplicar(i) {
        let base = detalle[i];

        detalle.push({
            ...base,
            color: '',
            cantidad: 1,
            costo_unitario: 0.00
        });

        render();
    }


    /* ==========================
       GUARDAR
    ========================== */
    let guardando = false;

    $('#btn_guardar').click(function () {

        if (guardando) return; // 🔒 evita doble envío

        if (!$('#fecha_emision').val()) {
            alert('Ingrese fecha de emisión');
            return;
        }
        if (!$('#fecha_ingreso').val()) {
            alert('Ingrese la fecha de ingreso');
            return;
        }

        if (!$('#id_almacen').val()) {
            alert('Seleccione un almacén');
            return;
        }

        if (detalle.length === 0) {
            alert('Debe agregar productos');
            return;
        }

        for (let d of detalle) {
            if (!d.color) {
                alert('Seleccione un color válido');
                return;
            }
            if (d.cantidad <= 0) {
                alert('Cantidad inválida');
                return;
            }
            if (d.costo_unitario < 0) {
                alert('Costo inválido');
                return;
            }
        }

        // 🔒 bloquear botón
        guardando = true;
        $('#btn_guardar')
            .prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

        let formData = new FormData();
        formData.append('detalle', JSON.stringify(detalle));
        formData.append('fecha_emision', $('#fecha_emision').val());
        formData.append('fecha_ingreso', $('#fecha_ingreso').val());
        formData.append('id_almacen', $('#id_almacen').val());
        formData.append('tipo_factura', $('#tipo_factura').val());
        formData.append('nro_factura', $('#nro_factura').val());
        formData.append('observaciones', $('#observaciones').val());


        if ($('#doc_entrada')[0].files.length > 0) {
            formData.append('doc_entrada', $('#doc_entrada')[0].files[0]);
        }

        $.ajax({
            url: '<?= $URL ?>/app/controllers/inventario/guardar.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (resp) {
                if (resp.trim() === 'OK') {

                    // ✅ MENSAJE + REDIRECCIÓN INMEDIATA
                    alert('La entrada fue registrada correctamente');

                    window.location.href = '<?= $URL ?>/inventario/gestion.php';

                } else {
                    guardando = false;
                    $('#btn_guardar')
                        .prop('disabled', false)
                        .html('<i class="fa fa-save"></i> Guardar entrada');

                    alert('Error:\n' + resp);
                }
            },

            error: function (xhr) {
                guardando = false;
                $('#btn_guardar')
                    .prop('disabled', false)
                    .html('<i class="fa fa-save"></i> Guardar entrada');

                alert('Error del servidor:\n' + xhr.responseText);
            }
        });
    });


    /* ==========================
       DATATABLE
    ========================== */
    $(function () {
        $('#example3').DataTable({
            pageLength: 8,
            autoWidth: false,
            responsive: false,
            ordering: true,
            lengthChange: false,

            language: {
                search: "Buscar producto:",
                zeroRecords: "No se encontraron productos",
                info: "Mostrando _START_ a _END_ de _TOTAL_ productos",
                infoEmpty: "No hay productos disponibles",
                infoFiltered: "(filtrado de _MAX_ productos)",
                paginate: {
                    next: "Siguiente",
                    previous: "Anterior"
                }
            },

            dom: '<"d-flex justify-content-center mb-3"f>rt<"d-flex justify-content-between mt-2"ip>'
        });
    });



    $('#btn_cancelar').click(function () {
        if (confirm('¿Desea cancelar el registro?')) {
        window.location.href = '<?= $URL ?>/inventario/gestion.php';
    }
    });


</script>
<style>
    .desc-corta {
        max-width: 280px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    /* ==========================
    MEJORAS VISUALES SIN LÓGICA
 ========================== */

    .card {
        border-radius: 10px;
    }

    .card-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .table thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .table tbody td {
        vertical-align: middle;
    }

    input.form-control,
    select.form-control,
    textarea.form-control {
        border-radius: 6px;
        font-size: 0.85rem;
    }

    input.form-control:focus,
    select.form-control:focus,
    textarea.form-control:focus {
        box-shadow: 0 0 0 0.15rem rgba(0,123,255,.25);
    }

    .btn {
        border-radius: 6px;
    }

    .modal-content {
        border-radius: 12px;
    }

    .modal-header {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .desc-corta {
        font-size: 0.85rem;
    }

    #detalle tr:hover {
        background-color: #f8f9fa;
    }

    #btn_guardar {
        font-weight: 600;
    }

    #btn_cancelar {
        font-weight: 500;
    }

    .table td.text-truncate {
        max-width: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ==========================
   BUSCADOR AMPLIO EN MODAL
========================== */

    /* Contenedor del search */
    #modal-productos .dataTables_filter {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-bottom: 15px;
    }

    /* Input search */
    #modal-productos .dataTables_filter input {
        width: 85%;              /* 🔥 mucho más grande */
        max-width: 900px;        /* 🔥 ocupa el modal */
        min-width: 500px;
        padding: 14px 20px;      /* 🔥 más alto */
        font-size: 1.05rem;      /* 🔥 texto grande */
        border-radius: 30px;
        border: 1px solid #ced4da;
        transition: all 0.25s ease;
    }

    /* Focus elegante */
    #modal-productos .dataTables_filter input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    /* Texto del label */
    #modal-productos .dataTables_filter label {
        font-weight: 600;
        color: #495057;
    }
    /* ==========================
   DESCRIPCIÓN COMPLETA EN MODAL
========================== */

    #modal-productos td.desc-modal {
        white-space: normal;        /* permite saltos */
        word-break: break-word;     /* rompe palabras largas */
        max-width: 420px;           /* ancho cómodo */
        font-size: 0.85rem;
        line-height: 1.3;
    }

    /* Columna descripción más ancha */
    #modal-productos th:nth-child(3),
    #modal-productos td:nth-child(3) {
        width: 40%;
    }

    /* Mejor lectura al pasar el mouse */
    #modal-productos tbody tr:hover td.desc-modal {
        background-color: #f8f9fa;
    }
    /* ==========================
       TABLA ADAPTADA AL MODAL
    ========================== */

    #modal-productos table {
        table-layout: fixed;
        width: 100%;
    }

    /* Permitir texto en varias líneas */
    #modal-productos th,
    #modal-productos td {
        white-space: normal;
        word-break: break-word;
        vertical-align: middle;
        font-size: 0.85rem;
    }

    /* Distribución equilibrada de columnas */
    #modal-productos th:nth-child(1),
    #modal-productos td:nth-child(1) { width: 8%; }

    #modal-productos th:nth-child(2),
    #modal-productos td:nth-child(2) { width: 14%; }

    #modal-productos th:nth-child(3),
    #modal-productos td:nth-child(3) { width: 32%; }

    #modal-productos th:nth-child(4),
    #modal-productos td:nth-child(4) { width: 18%; }

    #modal-productos th:nth-child(5),
    #modal-productos td:nth-child(5) { width: 18%; }

    #modal-productos th:nth-child(6),
    #modal-productos td:nth-child(6) { width: 10%; }


</style>
