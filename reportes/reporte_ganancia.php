<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Reporte de Ganancia</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="card card-outline card-primary">
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>N° Cotización</label>
                            <input type="number" id="nro_venta" class="form-control" placeholder="Ejemplo: 25">
                        </div>

                        <div class="col-md-3">
                            <label>Fecha desde</label>
                            <input type="date" id="fecha_inicio" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label>Fecha hasta</label>
                            <input type="date" id="fecha_fin" class="form-control">
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary mr-2" id="btn_buscar">
                                Buscar
                            </button>

                            <button class="btn btn-success" id="btn_excel" style="display:none;">
                                Excel
                            </button>
                        </div>
                    </div>

                    <div id="resumen"></div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="text-center bg-primary text-white">
                            <tr>
                                <th>N° Coti.</th>
                                <th>Fecha</th>
                                <th>RUC</th>
                                <th>Cliente</th>
                                <th>Flete</th>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Marca</th>
                                <th>Cant.</th>
                                <th>Costo Unit.</th>
                                <th>Precio Vendido</th>
                                <th>Costo Total</th>
                                <th>Venta Total</th>
                                <th>Ganancia Bruta</th>
                                <th>% Margen</th>
                            </tr>
                            </thead>
                            <tbody id="tbody_reporte"></tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include('../layout/parte2.php'); ?>

<script>
    $('#btn_buscar').click(function () {
        let nro_venta = $('#nro_venta').val();
        let fecha_inicio = $('#fecha_inicio').val();
        let fecha_fin = $('#fecha_fin').val();

        if (nro_venta === '' && (fecha_inicio === '' || fecha_fin === '')) {
            alert('Ingrese N° de cotización o seleccione rango de fechas.');
            return;
        }

        $.ajax({
            url: '../app/controllers/cotizacion/buscar_ganancia_cotizacion.php',
            type: 'GET',
            data: {
                nro_venta: nro_venta,
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin
            },
            dataType: 'json',
            success: function (response) {
                let tbody = $('#tbody_reporte');
                tbody.empty();

                if (response.estado === 'vacio') {
                    $('#resumen').html('<div class="alert alert-warning">No se encontraron datos.</div>');
                    $('#btn_excel').hide();
                    return;
                }

                response.productos.forEach(function (item) {
                    tbody.append(`
                    <tr>
                        <td>100${item.nro_venta}</td>
                        <td>${item.fecha_emi}</td>
                        <td>${item.ruc}</td>
                        <td>${item.razon_social}</td>
                        <td class="text-right">${item.flete === '' ? '' : 'S/. ' + item.flete}</td>
                        <td>${item.codigo}</td>
                        <td>${item.descripcion}</td>
                        <td>${item.marca}</td>
                        <td class="text-center">${item.cantidad}</td>
                        <td class="text-right">S/. ${item.costo_unitario}</td>
                        <td class="text-right">S/. ${item.precio_vendido}</td>
                        <td class="text-right">S/. ${item.costo_total}</td>
                        <td class="text-right">S/. ${item.venta_total}</td>
                        <td class="text-right font-weight-bold ${parseFloat(item.ganancia_bruta) >= 0 ? 'text-success' : 'text-danger'}">
                            S/. ${item.ganancia_bruta}
                        </td>
                        <td class="text-center">${item.margen}%</td>
                    </tr>
                `);
                });

                $('#resumen').html(`
                <div class="alert alert-success">
                    <b>Total vendido:</b> S/. ${response.total_venta}<br>
                    <b>Total costo productos:</b> S/. ${response.total_costo}<br>
                    <b>Total flete:</b> S/. ${response.total_flete}<br>
                    <b>Ganancia bruta:</b> S/. ${response.ganancia_bruta_total}<br>
                    <b>Ganancia neta:</b> S/. ${response.ganancia_neta}<br>
                    <b>Margen neto:</b> ${response.margen_neto}%
                </div>
            `);

                $('#btn_excel').show();
            }
        });
    });

    $('#btn_excel').click(function () {
        let nro_venta = $('#nro_venta').val();
        let fecha_inicio = $('#fecha_inicio').val();
        let fecha_fin = $('#fecha_fin').val();

        window.location.href =
            '../app/controllers/cotizacion/exportar_ganancia_excel.php?nro_venta=' + nro_venta +
            '&fecha_inicio=' + fecha_inicio +
            '&fecha_fin=' + fecha_fin;
    });
</script>