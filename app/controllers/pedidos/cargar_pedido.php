<?php

$id_pedido_get = $_GET['id_pedido'];

// Consulta con WHERE
$sql_pedido = "SELECT * FROM tb_pedido WHERE id_pedido = :id_pedido";

$query_pedidos = $pdo->prepare($sql_pedido);
$query_pedidos->execute(['id_pedido' => $id_pedido_get]);

$pedidos_datos = $query_pedidos->fetchAll(PDO::FETCH_ASSOC);

// Puedes acceder con un solo fetch (ya que es 1 registro)
if (count($pedidos_datos) > 0) {
    $pedidos_dato = $pedidos_datos[0];

    $nro_coti = $pedidos_dato['nro_coti'];
    $fecha_venci = $pedidos_dato['fecha_venci'];
    $razon_social = $pedidos_dato['razon_social'];
    $responsable = $pedidos_dato['responsable'];
    $descripcion = $pedidos_dato['descripcion'];
    $estado = $pedidos_dato['estado'];
    $fecha_emi = $pedidos_dato['fecha_emi'];
} else {
    echo "Pedido no encontrado.";
}
