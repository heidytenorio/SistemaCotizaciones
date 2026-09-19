<?php
// Conexión con la base de datos
include(__DIR__ . '/../../config.php'); // ✅ ruta absoluta y segura

try {
    $sql_pproductos = "
    SELECT 
        a.id_producto, 
        a.codigo,
        a.imagen, 
        a.descripcion, 
        m.nombre AS nombre_marca, 
        c.nombre_categoria AS nombre_categoria, 
        a.costo_minorista,
        a.porcentaje_minorista, 
        a.precio_minorista,
        a.costo_mayorista,
        a.porcentaje_mayorista, 
        a.precio_mayorista,
        a.moneda,
        a.cambio,
        a.info
    FROM tbp_almacen a 
    LEFT JOIN tbp_marca m ON a.id_marca = m.id_marca 
    LEFT JOIN tbp_categoria c ON a.id_categoria = c.id_categoria
    ORDER BY a.id_producto DESC
";


    $query_pproductos = $pdo->prepare($sql_pproductos);
    $query_pproductos->execute();
    $pproductos_datos = $query_pproductos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "❌ Error al obtener productos: " . $e->getMessage();
    $pproductos_datos = [];
}
?>

