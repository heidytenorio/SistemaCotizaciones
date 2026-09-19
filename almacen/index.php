<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 d-flex align-items-center">
                    <h1 class="m-0">Productos</h1>

                    <?php if ($rol_sesion === 'Administrador'): ?>
                        <a href="<?php echo $URL;?>/almacen/create.php" class="btn btn-success d-flex align-items-center ml-3">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Agregar Producto
                        </a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>



    <div class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="table-responsive">
                        <button id="clear-filters" class="btn btn-secondary btn-sm mb-2">Limpiar filtros</button>
                        <table id="example1" class="table table-bordered table-striped text-center nowrap" style="width:100%">
                            <thead class="text-center">
                            <tr>
                                <th>Código</th>
                                <th>Imagen</th>
                                <th>Descripción</th>
                                <th>Costo</th>
                                <th>% Ganancia</th>
                                <th>Precio Final</th>
                                <th>Marca</th>
                                <th>Proveedor</th>
                                <th>Categoría</th>
                                <th>Acciones</th>
                            </tr>
                            <tr class="filters">
                                <th><input type="text" class="form-control form-control-sm text-center" placeholder="Buscar" /></th>
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm text-center" placeholder="Buscar" /></th>
                                <th><input type="text" class="form-control form-control-sm text-center" placeholder="Buscar" /></th>
                                <th><input type="text" class="form-control form-control-sm text-center" placeholder="Buscar" /></th>
                                <th><input type="text" class="form-control form-control-sm text-center" placeholder="Buscar" /></th>
                                <th><input type="text" class="form-control form-control-sm text-center" placeholder="Marca" /></th>
                                <th><input type="text" class="form-control form-control-sm text-center" placeholder="Proveedor" /></th>
                                <th><input type="text" class="form-control form-control-sm text-center" placeholder="Categoría" /></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Se llenará dinámicamente desde AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>

<!-- JS y DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
    const ROL_USUARIO = "<?php echo $rol_sesion; ?>";
</script>
<script>
    $(function () {
        var table = $('#example1').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,          // guarda estado
            stateDuration: -1,        // que no caduque
            // 🔹 Guarda también tus inputs personalizados dentro del estado
            stateSaveParams: function (settings, data) {
                data.customFilters = [];
                $('#example1 thead tr.filters th').each(function (i) {
                    var $input = $(this).find('input');
                    data.customFilters[i] = $input.length ? ($input.val() || '') : '';
                });
            },
            // 🔹 Restaura los inputs y aplica búsqueda de columnas ANTES del primer draw
            stateLoadParams: function (settings, data) {
                if (data.customFilters && data.customFilters.length) {
                    var api = new $.fn.dataTable.Api(settings);
                    data.customFilters.forEach(function (val, i) {
                        var $input = $('#example1 thead tr.filters th').eq(i).find('input');
                        if ($input.length) {
                            $input.val(val);
                            // Sin regex ni smart, para server-side
                            api.column(i).search(val, false, false);
                            // Además asegura que el request inicial lleve el filtro
                            if (data.columns && data.columns[i] && data.columns[i].search) {
                                data.columns[i].search.search = val;
                            }
                        }
                    });
                }
            },

            ajax: {
                url: "<?php echo $URL; ?>/app/controllers/almacen/listado_de_almacen.php",
                type: 'POST',
                data: function (d) {
                    // Enviar SIEMPRE los valores actuales de los inputs al servidor
                    $('#example1 thead tr.filters th').each(function (i) {
                        var $input = $(this).find('input');
                        if ($input.length) {
                            d.columns[i].search.value = $input.val();
                        }
                    });
                }
            },

            responsive: true,
            autoWidth: false,
            orderCellsTop: true,
            fixedHeader: true,
            pageLength: 15,

            columns: [
                { data: 'codigo' },
                { data: 'imagen', render: function (data) {
                        return '<img src="<?php echo $URL; ?>/almacen/img_pproductos/' + data + '" width="90px" alt="producto">';
                    }},
                { data: 'descripcion', render: function (data) {
                        return '<span class="truncate" title="'+data+'">'+data+'</span>';
                    }},
                { data: 'costo_mayorista', render: function (data) {
                        return 'S/. ' + parseFloat(data).toFixed(2);
                    }},
                { data: null, render: function (data) {
                        let costo = parseFloat(data.costo_mayorista);
                        let porc = parseFloat(data.porcentaje_mayorista);
                        let ganancia = (costo * porc) / 100;
                        return porc + '% <span class="resaltado">S/. ' + ganancia.toFixed(2) + '</span>';
                    }},
                { data: 'precio_mayorista', render: function (data) {
                        return '<span class="resaltado_mayor">S/. ' + parseFloat(data).toFixed(2) + '</span>';
                    }},
                { data: 'nombre_marca' },
                { data: 'nombre_proveedor', render: function (data) {
                        return '<span class="truncates" title="'+data+'">'+data+'</span>';
                    }},
                { data: 'nombre_categoria', render: function (data) {
                        return '<span class="truncates" title="'+data+'">'+data+'</span>';
                    }},
                {
                    data: 'id_producto',
                    render: function (data) {

                        let btnEditar = `
            <a href="update.php?id=${data}" class="btn btn-success btn-sm">
                <i class="fa fa-pencil-alt"></i> Editar
            </a>`;

                        let btnEliminar = '';

                        if (ROL_USUARIO === 'Administrador') {
                            btnEliminar = `
                <a href="delete.php?id=${data}" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> Borrar
                </a>`;
                        }

                        return `
            <div class="btn-group">
                ${btnEditar}
                ${btnEliminar}
            </div>`;
                    }
                }

            ],

            language: {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros en total)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "zeroRecords": "No se encontraron resultados",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });

        // 🔹 Al escribir en un input, usa la API de DataTables (para que se guarde en el estado)
        $('#example1 thead tr.filters input').on('input', function () {
            var i = $(this).closest('th').index();
            table.column(i).search(this.value, false, false); // sin regex ni smart
            table.draw();
        });

        // 🔹 Botón limpiar: limpia inputs, búsquedas y estado guardado
        $('#clear-filters').on('click', function () {
            $('#example1 thead tr.filters input').val('');
            table.columns().search('');
            table.search('');
            table.state.clear();
            table.draw();
        });
    });
</script>

<!-- Estilos personalizados -->
<style>
    div.dataTables_filter { display: none; }
    .truncate {
        display: block;        /* ocupa todo el ancho de la celda */
        width: 100%;           /* se adapta al ancho de la columna */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .truncates {
        display: inline-block; /* para que max-width funcione */
        max-width: 100px;      /* ancho máximo deseado */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
    }
    .resaltado {
        background-color: #0f0f0f;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.85rem;
        margin-left: 4px;
        display: inline-block;
    }
    .resaltado_mayor {
        background-color: #0055a5;
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.85rem;
        margin-left: 4px;
        display: inline-block;
    }
    .filters input {
        width: 100%;
        font-size: 12px;
        padding: 2px 4px;
        text-align: center;
    }
    .table-responsive { overflow-x: auto; }
    table.dataTable { width: 100% !important; }

    /* ===== CONTROL DE ANCHOS DE COLUMNAS ===== */

    /* Código */
    #example1 th:nth-child(1),
    #example1 td:nth-child(1) {
        width: 90px;
    }

    /* Imagen */
    #example1 th:nth-child(2),
    #example1 td:nth-child(2) {
        width: 90px;
    }

    /* 🔹 DESCRIPCIÓN MÁS ANCHA */
    #example1 th:nth-child(3),
    #example1 td:nth-child(3) {
        width: 380px;
        max-width: 380px;
        text-align: left;
        white-space: nowrap;
    }


    /* 🔹 COSTO MÁS ESTRECHO */
    #example1 th:nth-child(4),
    #example1 td:nth-child(4) {
        width: 110px;
        white-space: nowrap;
    }

    /* % Menor */
    #example1 th:nth-child(5),
    #example1 td:nth-child(5) {
        width: 110px;
    }

    /* 🔹 PRECIO FINAL MÁS ESTRECHO */
    #example1 th:nth-child(6),
    #example1 td:nth-child(6) {
        width: 120px;
        white-space: nowrap;
    }

    /* Marca */
    #example1 th:nth-child(7),
    #example1 td:nth-child(7) {
        width: 140px;
    }

    /* Categoría */
    #example1 th:nth-child(8),
    #example1 td:nth-child(8) {
        width: 140px;
    }

    /* Acciones */
    #example1 th:nth-child(9),
    #example1 td:nth-child(9) {
        width: 120px;
    }

</style>
