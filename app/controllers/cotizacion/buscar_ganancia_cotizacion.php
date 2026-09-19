<?php
include('../../config.php');

header('Content-Type: application/json; charset=utf-8');

$nro_venta = $_GET['nro_venta'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

$where = "";
$params = [];

if ($nro_venta != '') {
    $where = "WHERE v.nro_venta = :nro_venta";
    $params[':nro_venta'] = $nro_venta;
} else {
    if ($fecha_inicio == '' || $fecha_fin == '') {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Faltan fechas']);
        exit;
    }

    $where = "WHERE DATE(v.fecha_emi) BETWEEN :fecha_inicio AND :fecha_fin";
    $params[':fecha_inicio'] = $fecha_inicio;
    $params[':fecha_fin'] = $fecha_fin;
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

if (!$datos) {
    echo json_encode(['estado' => 'vacio']);
    exit;
}

$productos = [];
$total_venta = 0;
$total_costo = 0;
$ganancia_bruta_total = 0;
$fletes_por_coti = [];

foreach ($datos as $dato) {
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

    $productos[] = [
        'nro_venta' => $dato['nro_venta'],
        'fecha_emi' => $dato['fecha_emi'],
        'ruc' => $dato['ruc'],
        'razon_social' => $dato['razon_social'],
        'direccion' => $dato['direccion'],
        'flete' => ($dato['flete'] === '' || $dato['flete'] === null) ? '' : number_format(floatval($dato['flete']), 2, '.', ''),
        'codigo' => $dato['codigo'],
        'descripcion' => $dato['descripcion'],
        'marca' => $dato['marca'],
        'cantidad' => number_format($cantidad, 2, '.', ''),
        'costo_unitario' => number_format($costo_unitario, 2, '.', ''),
        'precio_vendido' => number_format($precio_vendido, 2, '.', ''),
        'costo_total' => number_format($costo_total, 2, '.', ''),
        'venta_total' => number_format($venta_total, 2, '.', ''),
        'ganancia_bruta' => number_format($ganancia_bruta, 2, '.', ''),
        'margen' => number_format($margen, 2, '.', '')
    ];
}

$total_flete = array_sum($fletes_por_coti);
$ganancia_neta = $ganancia_bruta_total - $total_flete;
$margen_neto = ($total_venta > 0) ? ($ganancia_neta / $total_venta) * 100 : 0;

echo json_encode([
    'estado' => 'ok',
    'productos' => $productos,
    'total_venta' => number_format($total_venta, 2, '.', ''),
    'total_costo' => number_format($total_costo, 2, '.', ''),
    'total_flete' => number_format($total_flete, 2, '.', ''),
    'ganancia_bruta_total' => number_format($ganancia_bruta_total, 2, '.', ''),
    'ganancia_neta' => number_format($ganancia_neta, 2, '.', ''),
    'margen_neto' => number_format($margen_neto, 2, '.', '')
]);