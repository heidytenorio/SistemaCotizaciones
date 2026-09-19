<?php

$id_producto_get = $_GET['id_producto'];

$sql_productos = "SELECT a.imagen,a.codigo,
       a.descripcion, a.costo_mayorista, 
       a.porcentaje_mayorista, a.precio_mayorista ,
       m.nombre AS nombre_marca, 
       p.razon_social AS nombre_proveedor, 
       c.nombre_categoria AS nombre_categoria 
    FROM tb_almacen a 
    INNER JOIN tb_marca m ON a.id_marca = m.id_marca 
    INNER JOIN tb_proveedor p ON a.id_proveedor = p.id_proveedor 
    INNER JOIN tb_categoria c ON a.id_categoria = c.id_categoria";

$query_productos = $pdo->prepare($sql_productos);
$query_productos->execute(['id_producto' => $id_producto_get]);

$productos_datos = $query_productos->fetchAll(PDO::FETCH_ASSOC);

foreach ($productos_datos as $productos_dato){
    $imagen = $productos_dato['imagen'];
    $codigo = $productos_dato['codigo'];
    $descripcion = $productos_dato['descripcion'];
    $costo_mayorista = $productos_dato['costo_mayorista'];
    $porcentaje_mayorista = $productos_dato['porcentaje_mayorista'];
    $precio_mayorista = $productos_dato['precio_mayorista'];
    $id_marca = $productos_dato['id_marca'];
    $id_proveedor = $productos_dato['id_proveedor'];
    $id_categoria = $productos_dato['id_categoria'];

}

