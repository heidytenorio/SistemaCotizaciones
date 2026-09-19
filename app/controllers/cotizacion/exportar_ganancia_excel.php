<?php
include('../../config.php');

$nro_venta = $_GET['nro_venta'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

$where = "";
$params = [];

$titulo = "REPORTE DE GANANCIA";

if ($nro_venta != '') {
    $where = "WHERE v.nro_venta = :nro_venta";
    $params[':nro_venta'] = $nro_venta;

    $titulo .= " - COTIZACIÓN 100" . $nro_venta;
    $archivo = "reporte_ganancia_cotizacion_100" . $nro_venta . ".xls";
} else {
    if ($fecha_inicio == '' || $fecha_fin == '') {
        die('Debe seleccionar rango de fechas');
    }

    $where = "WHERE DATE(v.fecha_emi) BETWEEN :fecha_inicio AND :fecha_fin";
    $params[':fecha_inicio'] = $fecha_inicio;
    $params[':fecha_fin'] = $fecha_fin;

    $titulo .= " - DESDE $fecha_inicio HASTA $fecha_fin";
    $archivo = "reporte_ganancia_" . $fecha_inicio . "_a_" . $fecha_fin . ".xls";
}

$sql = "SELECT 
            v.nro_venta,
            DATE_FORMAT(v.fecha_emi, '%d/%m/%Y') AS fecha_emi,
            cli.ruc,
            cli.razon_social,
            cli.direccion,
            COALESCE(ped.flete, '') AS flete,
            carr.cantidad,
            carr.precio_final AS precio_vendido,
            pro.codigo,
            pro.descripcion,
            pro.costo_mayorista,
            marca.nombre AS marca
        FROM tb_venta AS v
        INNER JOIN tb_clientes AS cli 
            ON v.id_cliente = cli.id_cliente
        INNER JOIN tb_carrito AS carr 
            ON v.nro_venta = carr.nro_venta
        INNER JOIN tb_almacen AS pro 
            ON carr.id_producto = pro.id_producto
        INNER JOIN tb_marca AS marca 
            ON pro.id_marca = marca.id_marca
        LEFT JOIN (
            SELECT nro_coti, MAX(flete) AS flete
            FROM tb_pedido
            GROUP BY nro_coti
        ) AS ped
            ON ped.nro_coti = v.nro_venta
        $where
        ORDER BY v.nro_venta DESC, carr.id_carrito ASC";

$query = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $query->bindValue($key, $value);
}

$query->execute();
$datos = $query->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$archivo");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF";
?>

<table border="1">
    <tr>
        <th colspan="16" style="background:#1d3cb6;color:white;font-size:16px;">
            <?php echo $titulo; ?>
        </th>
    </tr>

    <tr style="background:#e7e7e7;font-weight:bold;">
        <th>N° Coti.</th>
        <th>Fecha</th>
        <th>RUC</th>
        <th>Cliente</th>
        <th>Dirección</th>
        <th>Flete</th>
        <th>Código</th>
        <th>Descripción</th>
        <th>Marca</th>
        <th>Cantidad</th>
        <th>Costo Unitario</th>
        <th>Precio Vendido</th>
        <th>Costo Total</th>
        <th>Venta Total</th>
        <th>Ganancia Bruta</th>
        <th>% Margen</th>
    </tr>

    <?php
    $total_venta = 0;
    $total_costo = 0;
    $ganancia_bruta_total = 0;
    $fletes_por_coti = [];

    foreach ($datos as $dato):

        $cantidad = floatval($dato['cantidad']);
        $precio_vendido = floatval($dato['precio_vendido']);
        $costo_unitario = floatval($dato['costo_mayorista']);

        $venta_total = $cantidad * $precio_vendido;
        $costo_total = $cantidad * $costo_unitario;
        $ganancia_bruta = $venta_total - $costo_total;
        $margen = ($venta_total > 0) ? ($ganancia_bruta / $venta_total) * 100 : 0;

        $total_venta += $venta_total;
        $total_costo += $costo_total;
        $ganancia_bruta_total += $ganancia_bruta;

        if ($dato['flete'] !== '' && $dato['flete'] !== null) {
            $fletes_por_coti[$dato['nro_venta']] = floatval($dato['flete']);
        }
        ?>
        <tr>
            <td>100<?php echo $dato['nro_venta']; ?></td>
            <td><?php echo $dato['fecha_emi']; ?></td>
            <td><?php echo $dato['ruc']; ?></td>
            <td><?php echo $dato['razon_social']; ?></td>
            <td><?php echo $dato['direccion']; ?></td>
            <td>
                <?php
                echo ($dato['flete'] === '' || $dato['flete'] === null)
                    ? ''
                    : number_format(floatval($dato['flete']), 2, '.', '');
                ?>
            </td>
            <td><?php echo $dato['codigo']; ?></td>
            <td><?php echo $dato['descripcion']; ?></td>
            <td><?php echo $dato['marca']; ?></td>
            <td><?php echo number_format($cantidad, 2, '.', ''); ?></td>
            <td><?php echo number_format($costo_unitario, 2, '.', ''); ?></td>
            <td><?php echo number_format($precio_vendido, 2, '.', ''); ?></td>
            <td><?php echo number_format($costo_total, 2, '.', ''); ?></td>
            <td><?php echo number_format($venta_total, 2, '.', ''); ?></td>
            <td><?php echo number_format($ganancia_bruta, 2, '.', ''); ?></td>
            <td><?php echo number_format($margen, 2, '.', ''); ?>%</td>
        </tr>
    <?php endforeach; ?>

    <?php
    $total_flete = array_sum($fletes_por_coti);
    $ganancia_neta = $ganancia_bruta_total - $total_flete;
    $margen_neto = ($total_venta > 0) ? ($ganancia_neta / $total_venta) * 100 : 0;
    ?>

    <tr style="font-weight:bold;background:#d9edf7;">
        <td colspan="12">TOTALES</td>
        <td><?php echo number_format($total_costo, 2, '.', ''); ?></td>
        <td><?php echo number_format($total_venta, 2, '.', ''); ?></td>
        <td><?php echo number_format($ganancia_bruta_total, 2, '.', ''); ?></td>
        <td></td>
    </tr>

    <tr style="font-weight:bold;background:#fce4d6;">
        <td colspan="14">TOTAL FLETE</td>
        <td><?php echo number_format($total_flete, 2, '.', ''); ?></td>
        <td></td>
    </tr>

    <tr style="font-weight:bold;background:#c6efce;">
        <td colspan="14">GANANCIA NETA</td>
        <td><?php echo number_format($ganancia_neta, 2, '.', ''); ?></td>
        <td><?php echo number_format($margen_neto, 2, '.', ''); ?>%</td>
    </tr>
</table>