<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

date_default_timezone_set('America/Lima');

function limpiar($txt) {
    return htmlspecialchars($txt ?? '', ENT_QUOTES, 'UTF-8');
}

function fechaCorta($fecha) {
    if (!$fecha || $fecha == '0000-00-00') return '-';

    $meses = [
        'Jan'=>'ene','Feb'=>'feb','Mar'=>'mar','Apr'=>'abr',
        'May'=>'may','Jun'=>'jun','Jul'=>'jul','Aug'=>'ago',
        'Sep'=>'sep','Oct'=>'oct','Nov'=>'nov','Dec'=>'dic'
    ];

    return strtr(strtolower(date('d M', strtotime($fecha))), $meses);
}

$sql = "SELECT 
            p.id_pedido,
            p.nro_coti,
            p.fecha_venci,
            p.fecha_emi,
            p.fecha_despacho,
            p.cumplimiento_despacho,
            p.razon_social,
            p.responsable,
            p.estado,
            p.estado_pago,
            p.id_region,
            r.region,
            r.dias
        FROM tb_pedido AS p
        INNER JOIN tb_diasregion AS r
            ON p.id_region = r.id
        WHERE p.id_region IS NOT NULL
          AND p.id_region <> ''
        ORDER BY p.fecha_venci ASC";

$query = $pdo->prepare($sql);
$query->execute();
$pedidos = $query->fetchAll(PDO::FETCH_ASSOC);

$hoy = date('Y-m-d');

$eventos = [];
$lista_alertas = [];

$total_preparar = 0;
$total_envio = 0;
$total_riesgo = 0;
$total_enviado = 0;
$total_sin_alerta = 0;

foreach ($pedidos as $pedido) {

    $estado = strtoupper(trim($pedido['estado']));
    $dias_region = (int)$pedido['dias'];

    if (!$pedido['fecha_venci'] || $dias_region <= 0) {
        continue;
    }

    $fecha_venci = date('Y-m-d', strtotime($pedido['fecha_venci']));
    $fecha_emi = date('Y-m-d', strtotime($pedido['fecha_emi']));

    $fecha_salida = date('Y-m-d', strtotime($fecha_venci . " - {$dias_region} days"));
    $fecha_prealerta = date('Y-m-d', strtotime($fecha_salida . " - 1 days"));

    $fecha_despacho = $pedido['fecha_despacho'];
    $cumplimiento = $pedido['cumplimiento_despacho'];

    $tipo_alerta = '';
    $nota = '';
    $color = '';
    $icono = '';
    $orden = 0;

    if ($estado == 'DESPACHADO') {

        if (!$fecha_despacho || $fecha_despacho == '0000-00-00') {
            $fecha_despacho = $hoy;

            $upd = $pdo->prepare("UPDATE tb_pedido 
                                  SET fecha_despacho = :fecha 
                                  WHERE id_pedido = :id");
            $upd->execute([
                ':fecha' => $fecha_despacho,
                ':id' => $pedido['id_pedido']
            ]);
        }

        if ($fecha_despacho <= $fecha_salida) {
            $cumplimiento = 'Salió a tiempo';
        } else {
            $cumplimiento = 'Salió fuera de fecha';
        }

        $upd2 = $pdo->prepare("UPDATE tb_pedido 
                               SET cumplimiento_despacho = :cumplimiento 
                               WHERE id_pedido = :id");
        $upd2->execute([
            ':cumplimiento' => $cumplimiento,
            ':id' => $pedido['id_pedido']
        ]);

        $tipo_alerta = 'Pedido enviado';
        $nota = $cumplimiento;
        $color = '#28a745';
        $icono = 'check-circle';
        $orden = 4;
        $total_enviado++;

    } elseif ($hoy > $fecha_venci) {

        $tipo_alerta = 'Posible penalidad';
        $nota = 'El pedido venció y aún no figura como despachado.';
        $color = '#dc3545';
        $icono = 'exclamation-triangle';
        $orden = 1;
        $total_riesgo++;

    } elseif ($hoy > $fecha_salida && $hoy <= $fecha_venci) {

        $tipo_alerta = 'Riesgo de atraso';
        $nota = 'Ya pasó la fecha máxima de salida según la región.';
        $color = '#dc3545';
        $icono = 'exclamation-circle';
        $orden = 1;
        $total_riesgo++;

    } elseif ($hoy == $fecha_salida) {

        $tipo_alerta = 'Enviar hoy';
        $nota = 'Debe salir hoy para llegar dentro del plazo.';
        $color = '#fd7e14';
        $icono = 'truck';
        $orden = 2;
        $total_envio++;

    } elseif ($hoy >= $fecha_prealerta && $hoy < $fecha_salida) {

        $tipo_alerta = 'Preparar despacho';
        $nota = 'Mañana inicia el tiempo de traslado de esta región.';
        $color = '#ffc107';
        $icono = 'box-open';
        $orden = 3;
        $total_preparar++;

    } else {

        $tipo_alerta = 'Sin alerta';
        $nota = 'Pedido dentro del plazo normal.';
        $color = '#6c757d';
        $icono = 'clock';
        $orden = 5;
        $total_sin_alerta++;
    }

    $cliente_corto = mb_strimwidth($pedido['razon_social'], 0, 32, '...');

    $eventos[] = [
        'id' => $pedido['id_pedido'],
        'title' => $pedido['nro_coti'] . ' · ' . $cliente_corto,
        'start' => ($estado == 'DESPACHADO') ? $fecha_despacho : $fecha_salida,
        'color' => $color,
        'extendedProps' => [
            'nro_coti' => $pedido['nro_coti'],
            'cliente' => $pedido['razon_social'],
            'region' => $pedido['region'],
            'dias_region' => $dias_region,
            'fecha_salida' => fechaCorta($fecha_salida),
            'fecha_venci' => fechaCorta($fecha_venci),
            'estado' => $pedido['estado'],
            'tipo_alerta' => $tipo_alerta,
            'cumplimiento' => $cumplimiento,
            'nota' => $nota
        ]
    ];

    $lista_alertas[] = [
        'orden' => $orden,
        'nro_coti' => $pedido['nro_coti'],
        'cliente' => $pedido['razon_social'],
        'responsable' => $pedido['responsable'],
        'region' => $pedido['region'],
        'dias_region' => $dias_region,
        'fecha_venci' => fechaCorta($fecha_venci),
        'fecha_salida' => fechaCorta($fecha_salida),
        'estado' => $pedido['estado'],
        'estado_pago' => $pedido['estado_pago'],
        'tipo_alerta' => $tipo_alerta,
        'cumplimiento' => $cumplimiento,
        'nota' => $nota,
        'color' => $color,
        'icono' => $icono
    ];
}

usort($lista_alertas, function($a, $b) {
    return $a['orden'] <=> $b['orden'];
});

$eventos_json = json_encode($eventos, JSON_UNESCAPED_UNICODE);
?>

<style>
    .box-alerta {
        border-radius: 16px;
        padding: 16px;
        color: white;
        min-height: 105px;
        box-shadow: 0 6px 16px rgba(0,0,0,.10);
    }

    .box-alerta h3 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }

    .box-alerta p {
        margin-bottom: 0;
        font-size: 13px;
    }

    .tabla-despacho {
        font-size: 13px;
        width: 100%;
    }

    .tabla-despacho th {
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
        background: #343a40;
        color: white;
    }

    .tabla-despacho td {
        vertical-align: middle !important;
    }

    .col-coti {
        width: 105px;
        white-space: nowrap;
    }

    .col-cliente {
        min-width: 230px;
        max-width: 280px;
    }

    .cliente-wrap {
        white-space: normal;
        line-height: 1.25;
        font-weight: 600;
        color: #343a40;
    }

    .texto-secundario {
        font-size: 11px;
        color: #6c757d;
        margin-top: 3px;
    }

    .col-region {
        width: 130px;
        text-align: center;
    }

    .fecha-card {
        width: 90px;
        padding: 6px 8px;
        border-radius: 10px;
        text-align: center;
        margin: auto;
        font-weight: 700;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
        color: #343a40;
        white-space: nowrap;
    }

    .fecha-label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 2px;
        text-transform: uppercase;
    }

    .col-estado {
        width: 100px;
        text-align: center;
    }

    .col-cumplimiento {
        width: 120px;
        text-align: center;
    }

    .col-alerta {
        min-width: 180px;
        max-width: 230px;
    }

    .badge-alerta {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        color: white;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .nota-alerta {
        font-size: 11px;
        color: #6c757d;
        margin-top: 5px;
        line-height: 1.25;
    }

    #calendario_alertas {
        background: white;
        border-radius: 14px;
        padding: 15px;
        border: 1px solid #e9ecef;
    }

    .fc-event-title {
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Alertas de Despacho</h1>
            <small class="text-muted">
                Control de salida y vencimiento según región.
            </small>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="row mb-3">

                <div class="col-lg-2 col-md-6 mb-3">
                    <div class="box-alerta" style="background:#ffc107;color:#212529;">
                        <h3><?php echo $total_preparar; ?></h3>
                        <p><i class="fas fa-box-open"></i> Preparar</p>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-3">
                    <div class="box-alerta" style="background:#fd7e14;">
                        <h3><?php echo $total_envio; ?></h3>
                        <p><i class="fas fa-truck"></i> Enviar hoy</p>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-3">
                    <div class="box-alerta" style="background:#dc3545;">
                        <h3><?php echo $total_riesgo; ?></h3>
                        <p><i class="fas fa-exclamation-triangle"></i> Riesgo</p>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-3">
                    <div class="box-alerta" style="background:#28a745;">
                        <h3><?php echo $total_enviado; ?></h3>
                        <p><i class="fas fa-check-circle"></i> Enviados</p>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-3">
                    <div class="box-alerta" style="background:#6c757d;">
                        <h3><?php echo $total_sin_alerta; ?></h3>
                        <p><i class="fas fa-clock"></i> Sin alerta</p>
                    </div>
                </div>

            </div>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Calendario de alertas
                    </h3>
                </div>

                <div class="card-body">
                    <div class="alert alert-light border">
                        <b>Regla:</b> Fecha de vencimiento - días de región = fecha máxima de salida.
                    </div>

                    <div id="calendario_alertas"></div>
                </div>
            </div>

            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Detalle de pedidos
                    </h3>
                </div>

                <div class="card-body table-responsive">
                    <table id="tabla_alertas" class="table table-bordered table-hover tabla-despacho">
                        <thead>
                        <tr>
                            <th>Cotización</th>
                            <th>Cliente</th>
                            <th>Región</th>
                            <th>Debe salir</th>
                            <th>Vence</th>
                            <th>Estado</th>
                            <th>Cumplimiento</th>
                            <th>Alerta</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($lista_alertas as $alerta) { ?>
                            <tr>
                                <td class="col-coti text-center">
                                    <b><?php echo limpiar($alerta['nro_coti']); ?></b>
                                    <div class="texto-secundario">
                                        <?php echo limpiar($alerta['responsable']); ?>
                                    </div>
                                </td>

                                <td class="col-cliente">
                                    <div class="cliente-wrap">
                                        <?php echo limpiar($alerta['cliente']); ?>
                                    </div>
                                </td>

                                <td class="col-region">
                                    <b><?php echo limpiar($alerta['region']); ?></b>
                                    <div class="texto-secundario">
                                        <?php echo $alerta['dias_region']; ?> días
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div class="fecha-card">
                                        <span class="fecha-label">Salida</span>
                                        <?php echo $alerta['fecha_salida']; ?>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div class="fecha-card">
                                        <span class="fecha-label">Vence</span>
                                        <?php echo $alerta['fecha_venci']; ?>
                                    </div>
                                </td>

                                <td class="col-estado">
                                    <span class="badge badge-secondary">
                                        <?php echo limpiar($alerta['estado']); ?>
                                    </span>
                                    <div class="texto-secundario">
                                        <?php echo limpiar($alerta['estado_pago']); ?>
                                    </div>
                                </td>

                                <td class="col-cumplimiento">
                                    <?php if ($alerta['cumplimiento'] == 'Salió a tiempo') { ?>
                                        <span class="badge badge-success">A tiempo</span>
                                    <?php } elseif ($alerta['cumplimiento'] == 'Salió fuera de fecha') { ?>
                                        <span class="badge badge-danger">Fuera de fecha</span>
                                    <?php } else { ?>
                                        <span class="badge badge-light">Pendiente</span>
                                    <?php } ?>
                                </td>

                                <td class="col-alerta">
                                    <span class="badge-alerta" style="background:<?php echo $alerta['color']; ?>;">
                                        <i class="fas fa-<?php echo $alerta['icono']; ?>"></i>
                                        <?php echo limpiar($alerta['tipo_alerta']); ?>
                                    </span>
                                    <div class="nota-alerta">
                                        <?php echo limpiar($alerta['nota']); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include('../layout/parte2.php'); ?>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        var calendarEl = document.getElementById('calendario_alertas');
        var eventos = <?php echo $eventos_json; ?>;

        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'dayGridMonth',
            height: 680,
            dayMaxEvents: 3,

            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },

            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                list: 'Lista'
            },

            events: eventos,

            eventClick: function(info) {
                alert(
                    info.event.extendedProps.tipo_alerta + '\n\n' +
                    'Cotización: ' + info.event.extendedProps.nro_coti + '\n' +
                    'Cliente: ' + info.event.extendedProps.cliente + '\n' +
                    'Región: ' + info.event.extendedProps.region + '\n' +
                    'Días región: ' + info.event.extendedProps.dias_region + '\n\n' +
                    'Debe salir: ' + info.event.extendedProps.fecha_salida + '\n' +
                    'Vencimiento: ' + info.event.extendedProps.fecha_venci + '\n\n' +
                    'Cumplimiento: ' + info.event.extendedProps.cumplimiento + '\n' +
                    'Nota: ' + info.event.extendedProps.nota
                );
            }
        });

        calendar.render();

        $("#tabla_alertas").DataTable({
            "pageLength": 10,
            "autoWidth": false,
            "responsive": true,
            "order": [],
            "columnDefs": [
                { "width": "90px", "targets": 0 },
                { "width": "250px", "targets": 1 },
                { "width": "110px", "targets": 2 },
                { "width": "100px", "targets": 3 },
                { "width": "100px", "targets": 4 },
                { "width": "95px", "targets": 5 },
                { "width": "110px", "targets": 6 },
                { "width": "190px", "targets": 7 }
            ],
            "language": {
                "emptyTable": "No hay pedidos con región asignada",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ pedidos",
                "infoEmpty": "Mostrando 0 a 0 de 0 pedidos",
                "lengthMenu": "Mostrar _MENU_ registros",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron resultados",
                "paginate": {
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });

    });
</script>