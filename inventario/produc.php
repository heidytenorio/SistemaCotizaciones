<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../app/controllers/inventario/listado_almacen2.php');
include('../layout/parte1.php');
?>
<?php
// =====================
// LISTADO DE PROVEEDORES
// =====================
$sql_proveedores = "  SELECT MIN(id_proveedor) AS id_proveedor, razon_social
    FROM tb_proveedor
    GROUP BY razon_social
    ORDER BY razon_social";
$query_proveedores = $pdo->prepare($sql_proveedores);
$query_proveedores->execute();
$proveedores_datos = $query_proveedores->fetchAll(PDO::FETCH_ASSOC);


// =====================
// LISTADO DE CATEGORÍAS
// =====================
$sql_categorias = "SELECT MIN(id_categoria) AS id_categoria, nombre_categoria
    FROM tb_categoria
    GROUP BY nombre_categoria
    ORDER BY nombre_categoria";
$query_categorias = $pdo->prepare($sql_categorias);
$query_categorias->execute();
$categorias_datos = $query_categorias->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <style>

        /* ===== TABLA GENERAL ===== */
        #example1 {
            border-collapse: separate !important;
            border-spacing: 0 6px;
        }

        table.dataTable {
            table-layout: fixed; /* respeta anchos */
        }
        .img-tabla-producto {
            transition: transform 0.25s ease;
            cursor: zoom-in;
        }

        .img-tabla-producto:hover {
            transform: scale(2);
            z-index: 1000;
            position: relative;
        }

        #example1 thead th {
            background-color: #f1f3f5;
            color: #343a40;
            font-weight: 600;
            font-size: 0.95rem;
            text-align: center;
            vertical-align: middle;
            padding: 8px 10px;
            border-bottom: 2px solid #dee2e6;
        }

        #example1 thead tr.filters input {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 5px 8px;
            font-size: 0.88rem;
            text-align: center;
            width: 100%;
        }

        #example1 tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* ===== ANCHOS DE COLUMNAS ===== */

        /* IMAGEN */
        #example1 th:nth-child(1),
        #example1 td:nth-child(1) {
            width: 12%;
        }

        /* DESCRIPCIÓN (más ancha) */
        #example1 th:nth-child(2),
        #example1 td:nth-child(2) {
            width: 35%;
            text-align: left;
        }

        /* CONTROL (más corto) */
        #example1 th:nth-child(3),
        #example1 td:nth-child(3) {
            width: 10%;
            text-align: center;
            font-weight: 600;
        }

        /* PROVEEDOR (más corto) */
        #example1 th:nth-child(4),
        #example1 td:nth-child(4) {
            width: 18%;
        }

        /* CATEGORÍA */
        #example1 th:nth-child(5),
        #example1 td:nth-child(5) {
            width: 15%;
        }

        /* ===== TRUNCADO DE TEXTO ===== */


        td.truncates {
            max-width: 60px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: center;
        }

        /* ===== IMAGEN GRANDE EN TABLA ===== */
        .img-tabla-producto {
            width: 130px;
            height: 90px;
            object-fit: contain;
            background: #f8f9fa;
            padding: 6px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }


        .table img {
            max-width: 80px;
            max-height: 60px;
            object-fit: contain;
        }

        /* ===== MODAL EDITAR ===== */
        .modal-edit .img-preview-card {
            width: 100%;
            max-height: 220px;
            overflow: hidden;
        }

        .modal-edit .img-preview-card img {
            max-width: 100%;
            max-height: 200px;
            object-fit: contain;
        }

        /* ===== BOTONES ===== */
        #clear-filters {
            border-radius: 6px;
        }
        /* SOLO PRIMERA FILA DEL THEAD (TÍTULOS) */
        table.dataTable thead tr:first-child th {
            position: relative;
            cursor: pointer;
        }

        table.dataTable thead tr:first-child th:after {
            content: "⇅";
            font-size: 0.7rem;
            color: #999;
            position: absolute;
            right: 8px;
        }

        table.dataTable thead tr:first-child th.sorting_asc:after {
            content: "↑";
            color: #0d6efd;
        }

        table.dataTable thead tr:first-child th.sorting_desc:after {
            content: "↓";
            color: #0d6efd;
        }
        .no-sort {
            pointer-events: none;
        }
        .no-sort input {
            pointer-events: auto;
        }


    </style>

</head>

<body>
<div class="content-wrapper">

    <div class="content-header mb-3">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <h1>
                <i class="fas fa-warehouse"></i> Productos de Almacen
            </h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">

                    <!-- BOTONES SUPERIORES -->
                    <div class="d-flex justify-content-between mb-2">
                        <button id="clear-filters" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eraser"></i> Limpiar filtros
                        </button>

                        <?php if ($rol_sesion === 'Administrador') { ?>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-create">
                                <i class="fas fa-plus"></i> Agregar producto
                            </button>

                        <?php } ?>

                    </div>

                    <table id="example1" class="table table-hover table-striped table-sm">


                        <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Descripción</th>
                            <th>Control</th>
                            <th>Proveedor</th>
                            <th>Categoría</th>
                            <th class="no-sort"><center>Acciones</center></th>
                        </tr>
                        <tr class="filters no-sort">
                            <th></th>
                            <th><input type="text" placeholder="Descripción"></th>
                            <th><input type="text" placeholder="Control"></th>
                            <th><input type="text" placeholder="Proveedor"></th>
                            <th><input type="text" placeholder="Categoría"></th>
                            <th></th>
                        </tr>
                        </thead>


                        <tbody>
                        <?php if (!empty($pproductos_datos)) :
                            foreach ($pproductos_datos as $pproductos_dato) :
                                $id_producto = $pproductos_dato['id_producto'];
                                ?>
                                <tr>

                                    <td class="text-center">
    <span style="display:none;">
        <?= empty($pproductos_dato['imagen']) ? '0' : '1' ?>
    </span>

                                        <?php if (!empty($pproductos_dato['imagen'])): ?>
                                            <img class="img-tabla-producto"
                                                 src="<?= $URL ?>/inventario/img_pproductos/<?= htmlspecialchars($pproductos_dato['imagen']) ?>">
                                        <?php else: ?>
                                            <img class="img-tabla-producto"
                                                 src="<?= $URL ?>/public/images/no-image.png">
                                        <?php endif; ?>
                                    </td>


                                    <!-- DESCRIPCIÓN -->
                                    <td title="<?= htmlspecialchars($pproductos_dato['descripcion']) ?>">
                                        <?= htmlspecialchars($pproductos_dato['descripcion']) ?>
                                    </td>

                                    <!-- CONTROL -->
                                    <td class="text-center fw-bold">
                                        <?= $pproductos_dato['tipo_liquido'] === 'GRANEL' ? 'GRANEL' : 'UNIDAD' ?>
                                    </td>

                                    <!-- PROVEEDOR -->
                                    <td class="truncates" title="<?= htmlspecialchars($pproductos_dato['proveedor']) ?>">
                                        <?= htmlspecialchars($pproductos_dato['proveedor']) ?>
                                    </td>

                                    <!-- CATEGORÍA -->
                                    <td class="truncates" title="<?= htmlspecialchars($pproductos_dato['categoria']) ?>">
                                        <?= htmlspecialchars($pproductos_dato['categoria']) ?>
                                    </td>



                                    <!-- ACCIONES -->
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success btn-editar"

                                                    data-id="<?= $id_producto ?>"
                                                    data-codigo="<?= htmlspecialchars($pproductos_dato['codigo']) ?>"
                                                    data-descripcion="<?= htmlspecialchars($pproductos_dato['descripcion']) ?>"
                                                    data-id_categoria="<?= $pproductos_dato['id_categoria'] ?>"
                                                    data-id_proveedor="<?= $pproductos_dato['id_proveedor'] ?>"
                                                    data-tipo_liquido="<?= $pproductos_dato['tipo_liquido'] ?>"
                                                    data-factor_conversion="<?= $pproductos_dato['factor_conversion'] ?>"
                                                    data-imagen="<?= $pproductos_dato['imagen'] ?>"

                                                    data-toggle="modal"
                                                    data-target="#modal-update">

                                                <i class="fas fa-edit"></i>

                                            </button>

                                            <?php if ($rol_sesion === 'Administrador') { ?>
                                                <a href="../app/controllers/inventario/delete.php?id_producto=<?= $id_producto ?>"
                                                   class="btn btn-danger"
                                                   onclick="return confirm('¿Eliminar producto?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php } ?>

                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                        </table>



                    <!-- 🔽 AQUÍ VAN LOS MODALES EDITAR 🔽 -->
                    <?php if (!empty($pproductos_datos)) : ?>

                        <div class="modal fade modal-edit" id="modal-update" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content shadow-lg">

                                        <form action="../app/controllers/inventario/update.php"
                                              method="POST"
                                              enctype="multipart/form-data">

                                            <input type="hidden" name="id_producto" id="edit_id">

                                            <!-- HEADER -->
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-edit"></i> Editar producto
                                                </h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>

                                            <!-- BODY -->
                                            <div class="modal-body">
                                                <div class="container-fluid">

                                                    <!-- FILA 1 -->
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Código</label>
                                                            <input type="text" name="codigo" class="form-control"
                                                                   id="edit_codigo" required>
                                                        </div>

                                                        <div class="col-md-8">
                                                            <label>Descripción</label>
                                                            <textarea name="descripcion" class="form-control"
                                                                      id="edit_descripcion" rows="2"></textarea>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <!-- FILA 2 -->
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label>Categoría</label>
                                                            <select name="id_categoria"
                                                                    id="edit_id_categoria"
                                                                    class="form-control"
                                                                    required>
                                                                <?php foreach ($categorias_datos as $categoria) { ?>
                                                                    <option value="<?= $categoria['id_categoria']; ?>"
                                                                        <?= $categoria['id_categoria'] == $pproductos_dato['id_categoria'] ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($categoria['nombre_categoria']); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label>Proveedor</label>
                                                            <select name="id_proveedor"
                                                                    id="edit_id_proveedor"
                                                                    class="form-control"
                                                                    required>
                                                                <?php foreach ($proveedores_datos as $proveedor) { ?>
                                                                    <option value="<?= $proveedor['id_proveedor']; ?>"
                                                                        <?= $proveedor['id_proveedor'] == $pproductos_dato['id_proveedor'] ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($proveedor['razon_social']); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <!-- FILA 3 -->
                                                    <div class="row align-items-end">
                                                        <div class="col-md-6">
                                                            <label>Tipo de control</label>
                                                            <select name="tipo_liquido"
                                                                    id="edit_tipo_liquido"
                                                                    class="form-control tipo_liquido_edit">
                                                                <option value="NO" <?= $pproductos_dato['tipo_liquido']=='NO'?'selected':'' ?>>
                                                                    UNIDAD
                                                                </option>
                                                                <option value="GRANEL" <?= $pproductos_dato['tipo_liquido']=='GRANEL'?'selected':'' ?>>
                                                                    GRANEL (Líquido)
                                                                </option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6"
                                                             id="grupo_factor_edit"
                                                             style="<?= $pproductos_dato['tipo_liquido']==='GRANEL'?'':'display:none;' ?>">
                                                            <label>Factor de conversión</label>
                                                            <input type="number"
                                                                   name="factor_conversion"
                                                                   id="edit_factor_conversion"
                                                                   class="form-control">
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <!-- IMAGEN -->
                                                    <div class="row">
                                                        <div class="col-md-7 text-center">
                                                            <label>Imagen actual</label>
                                                            <div class="img-preview-card">
                                                                <img id="edit_preview"
                                                                     src="<?= !empty($pproductos_dato['imagen'])
                                                                         ? $URL.'/inventario/img_pproductos/'.$pproductos_dato['imagen']
                                                                         : $URL.'/public/images/no-image.png' ?>">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <label>Cambiar imagen</label>
                                                            <input type="file"
                                                                   name="image"
                                                                   class="form-control img-edit-input"
                                                                   id="edit_image"
                                                                   accept="image/*">
                                                            <small class="text-muted">
                                                                JPG, PNG o WEBP — máx 2MB
                                                            </small>
                                                        </div>
                                                    </div>

                                                    <input type="hidden"
                                                           name="imagen_actual"
                                                           id="edit_imagen_actual">

                                                </div>
                                            </div>

                                            <!-- FOOTER -->
                                            <div class="modal-footer bg-light">
                                                <button type="submit" class="btn btn-success px-4">
                                                    <i class="fas fa-save"></i> Actualizar
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal">
                                                    Cancelar
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/mensajes.php'); ?>
<div class="modal fade" id="modal-create">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="../app/controllers/inventario/create.php"
                  method="POST"
                  enctype="multipart/form-data">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-box"></i> Nuevo producto
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <div class="container-fluid">

                        <!-- FILA 1 -->
                        <div class="row">
                            <div class="col-md-4">
                                <label>Código *</label>
                                <input type="text" name="codigo" class="form-control" required>
                            </div>

                            <div class="col-md-8">
                                <label>Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <hr>

                        <!-- FILA 2 -->
                        <div class="row">
                            <div class="col-md-6">
                                <label>Categoría *</label>
                                <select name="id_categoria" class="form-control" required>
                                    <option value="">Seleccione categoría</option>
                                    <?php foreach ($categorias_datos as $categoria) { ?>
                                        <option value="<?= $categoria['id_categoria']; ?>">
                                            <?= htmlspecialchars($categoria['nombre_categoria']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Proveedor *</label>
                                <select name="id_proveedor" class="form-control" required>
                                    <option value="">Seleccione proveedor</option>
                                    <?php foreach ($proveedores_datos as $proveedor) { ?>
                                        <option value="<?= $proveedor['id_proveedor']; ?>">
                                            <?= htmlspecialchars($proveedor['razon_social']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <!-- FILA 3 -->
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tipo de control *</label>
                                <select name="tipo_liquido"
                                        id="tipo_liquido_create"
                                        class="form-control"
                                        required>
                                    <option value="NO">UNIDAD</option>
                                    <option value="GRANEL">GRANEL (Líquido)</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="grupo_factor_create" style="display:none;">
                                <label>Factor de conversión</label>
                                <input type="number"
                                       step="0.0001"
                                       min="0.0001"
                                       name="factor_conversion"
                                       class="form-control"
                                       placeholder="Ej: 20">
                                <small class="text-muted">
                                    Ejemplo: Bidón 20L → factor = 20
                                </small>
                            </div>
                        </div>

                        <hr>

                        <!-- FILA 4 IMAGEN -->
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <label>Imagen</label>
                                <div class="border rounded p-2 mb-2">
                                    <img id="preview-img"
                                         src="<?= $URL ?>/public/images/no-image.png"
                                         class="img-fluid"
                                         style="max-height:150px;">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label>Seleccionar imagen</label>
                                <input type="file"
                                       name="image"
                                       class="form-control"
                                       id="file"
                                       accept="image/*">
                                <small class="text-muted">
                                    JPG, PNG — máx 2MB
                                </small>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>


<?php include('../layout/parte2.php'); ?>

<script>
    $(function () {

        let table = $('#example1').DataTable({
            deferRender: true,
            pageLength: 10,
            autoWidth: false,
            orderCellsTop: true,
            fixedHeader: false,
            responsive: false,
            dom: 'lrtip',
            stateSave: true,
            stateDuration: -1,
            ordering: true,
            order: [],

            columnDefs: [
                { orderable: false, targets: [0,5] } // Imagen y Acciones sin orden
            ],

            language: {
                emptyTable: "No hay información",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros)",
                lengthMenu: "Mostrar _MENU_ registros",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });

        // 🔹 Quitar iconos de orden en fila de filtros
        $('#example1 thead tr.filters th').removeClass('sorting sorting_asc sorting_desc');

        // 🔹 Restaurar filtros guardados al recargar
        let state = table.state.loaded();
        if (state) {
            table.columns().every(function (i) {
                let colSearch = state.columns[i].search.search;
                if (colSearch) {
                    $('#example1 thead tr.filters th:eq(' + i + ') input')
                        .val(colSearch);
                }
            });
        }

        // 🔹 Filtros con debounce (más fluido)
        let searchTimeout;
        $('#example1 thead tr.filters input').on('input', function () {
            let self = this;
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function () {
                let colIndex = $(self).closest('th').index();
                table.column(colIndex).search(self.value).draw();
            }, 300);
        });

        // 🔹 Botón limpiar filtros
        $('#clear-filters').click(function () {
            $('#example1 thead input').val('');
            table.state.clear();
            table.columns().search('').draw();
        });

    });
</script>


<script>
    document.getElementById('tipo_liquido_create').addEventListener('change', function () {
        const grupo = document.getElementById('grupo_factor_create');
        if (this.value === 'GRANEL') {
            grupo.style.display = 'block';
        } else {
            grupo.style.display = 'none';
            grupo.querySelector('input').value = 1;
        }
    });
</script>
<script>

    $("#edit_tipo_liquido").change(function(){

        if($(this).val()=="GRANEL"){

            $("#grupo_factor_edit").show();

        }else{

            $("#grupo_factor_edit").hide();

            $("#edit_factor_conversion").val(1);

        }

    });

</script>
<script>
    document.getElementById('file').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = function (evt) {
            document.getElementById('preview-img').src = evt.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>

<script>

    $(document).on("click",".btn-editar",function(){

        let btn=$(this);

        $("#edit_id").val(btn.data("id"));

        $("#edit_codigo").val(btn.data("codigo"));

        $("#edit_descripcion").val(btn.data("descripcion"));

        $("#edit_id_categoria").val(btn.data("id_categoria"));

        $("#edit_id_proveedor").val(btn.data("id_proveedor"));

        $("#edit_tipo_liquido").val(btn.data("tipo_liquido"));

        $("#edit_factor_conversion").val(btn.data("factor_conversion"));

        $("#edit_imagen_actual").val(btn.data("imagen"));


        let imagen=btn.data("imagen");

        if(imagen){

            $("#edit_preview").attr("src",
                "<?= $URL ?>/inventario/img_pproductos/"+imagen);

        }else{

            $("#edit_preview").attr("src",
                "<?= $URL ?>/public/images/no-image.png");

        }

    });

</script>
<script>

    $("#edit_image").change(function(){

        let reader=new FileReader();

        reader.onload=function(e){

            $("#edit_preview").attr("src",e.target.result);

        }

        reader.readAsDataURL(this.files[0]);

    });

</script>
</body>
</html>
