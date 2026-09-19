<?php

define('SERVIDOR', '');
define('USUARIO', '');
define('PASSWORD', '');
define('BD', 's');

$servidor = "mysql:dbname=" . BD . ";host=" . SERVIDOR;

try {
    $pdo = new PDO($servidor, USUARIO, PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    // echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error al conectar base de datos: " . $e->getMessage(); // Agrega este mensaje para ver el detalle
}

$URL = "";
date_default_timezone_set("America/Lima");
$fechaHora = date("Y-m-d H:i:s");
define('TIEMPO_INACTIVIDAD', 900); // 15 minutos
define('URL_DOCS_ENTRADA', $URL . '/inventario/docs_entrada');
define('URL_DOCS_SALIDA',  $URL . '/inventario/docs_salida');




