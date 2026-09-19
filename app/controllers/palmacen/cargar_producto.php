<?php

$id_producto_get = $_GET['id_producto'];

$sql_pproductos = "SELECT a.id_producto, a.codigo, a.imagen, 
    a.descripcion, a.id_marca, m.nombre AS nombre_marca, 
    a.id_categoria, c.nombre_categoria AS nombre_categoria, 
    a.costo_minorista, a.porcentaje_minorista, a.precio_minorista, 
    a.costo_mayorista, a.porcentaje_mayorista, a.precio_mayorista, 
    a.competencia 
    FROM tbp_almacen a 
    INNER JOIN tbp_marca m ON a.id_marca = m.id_marca 
    INNER JOIN tbp_categoria c ON a.id_categoria = c.id_categoria 
    WHERE a.id_producto = :id_producto";

$query_pproductos = $pdo->prepare($sql_pproductos);
$query_pproductos->execute(['id_producto' => $id_producto_get]);

$pproductos_datos = $query_pproductos->fetchAll(PDO::FETCH_ASSOC);

foreach ($pproductos_datos as $pproductos_dato){
    $codigo = $pproductos_dato['codigo'];
    $descripcion = $pproductos_dato['descripcion'];
    $id_marca = $pproductos_dato['id_marca'];
    $id_categoria = $pproductos_dato['id_categoria'];
    $costo_minorista = $pproductos_dato['costo_minorista'];
    $porcentaje_minorista = $pproductos_dato['porcentaje_minorista'];
    $precio_minorista = $pproductos_dato['precio_minorista'];
    $costo_mayorista = $pproductos_dato['costo_mayorista'];
    $porcentaje_mayorista = $pproductos_dato['porcentaje_mayorista'];
    $precio_mayorista = $pproductos_dato['precio_mayorista'];
    $competencia = $pproductos_dato['competencia'];
    $imagen = $pproductos_dato['imagen'];
}
