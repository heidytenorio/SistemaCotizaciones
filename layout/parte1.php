<?php
session_start();

if (empty($_SESSION['sesion_email'])) {
    header("Location: /login/TRYRTYR.php");
    exit;
}

$nombres_sesion = $_SESSION['nombres_sesion'] ?? '';
$rol_sesion     = $_SESSION['rol_sesion'] ?? '';
$URL            = $_SESSION['URL'] ?? '';

include 'app/controllers/usuarios/listado_de_usuarios.php';
include 'app/controllers/roles/listado_de_roles.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Serconslimp</title>

    <!-- GOOGLE FONT -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,500,600&display=fallback">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="<?= $URL ?>/public/templeates/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">

    <!-- ADMINLTE -->
    <link rel="stylesheet" href="<?= $URL ?>/public/templeates/AdminLTE-3.2.0/dist/css/adminlte.min.css">

    <!-- JQUERY -->
    <script src="<?= $URL ?>/public/templeates/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>

    <style>
        /* =========================================================
           VARIABLES DE COLOR (TEMA PROFESIONAL)
           ========================================================= */
        :root {
            --primary: #2563eb;                 /* Azul principal */
            --primary-soft: rgba(37,99,235,.12);
            --primary-active: rgba(37,99,235,.22);
            --primary-border: #3b82f6;

            --sidebar-bg: #0f172a;
            --sidebar-bg-2: #020617;

            --text-main: #e5e7eb;
            --text-muted: #9ca3af;
        }

        /* =========================================================
           SIDEBAR BASE
           ========================================================= */
        .main-sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg), var(--sidebar-bg-2));
        }

        /* =========================================================
           LOGO
           ========================================================= */
        .brand-link {
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .brand-text {
            font-weight: 600;
            letter-spacing: .3px;
        }

        /* =========================================================
           USUARIO
           ========================================================= */
        .user-panel {
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .user-panel img {
            border: 2px solid rgba(255,255,255,.18);
        }
        .user-panel .info a {
            color: var(--text-main);
            font-weight: 500;
        }

        /* =========================================================
           MENÚ PRINCIPAL
           ========================================================= */
        .nav-sidebar .nav-item {
            margin-bottom: 1px;
        }

        .nav-sidebar .nav-link {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 9px 14px;
            border-radius: 6px;
            color: var(--text-main);
            font-size: 13.8px;
            font-weight: 500;
            transition: background-color .12s ease, color .12s ease;
        }

        /* Hover */
        .nav-sidebar .nav-link:hover:not(.active) {
            background: rgba(255,255,255,.05);
            color: #fff;
        }

        /* Activo principal */
        .nav-sidebar .nav-link.active {
            background: var(--primary-active);
            color: #f8fafc;
            border-left: 3px solid var(--primary-border);
        }

        /* Iconos */
        .nav-sidebar .nav-icon {
            font-size: 14px;
            width: 20px;
            opacity: .85;
        }

        /* =========================================================
           SUBMENÚ
           ========================================================= */
        .nav-treeview {
            padding-left: 0 !important;
        }

        .nav-treeview .nav-link {
            font-size: 13px;
            font-weight: 400;
            color: var(--text-muted);
            padding: 7px 14px 7px 34px !important;
        }

        /* Hover submenú */
        .nav-treeview .nav-link:hover:not(.active) {
            background: rgba(255,255,255,.04);
            color: #fff;
        }

        /* Submenú activo */
        .nav-treeview .nav-link.active {
            background: rgba(37,99,235,.14);
            color: #e0f2fe;
            border-left: 3px solid var(--primary-border);
        }

        /* =========================================================
           MENÚ ABIERTO (PADRE)
           ========================================================= */
        .nav-item.menu-open > .nav-link:not(.active) {
            background: rgba(255,255,255,.03);
            color: var(--text-main);
        }

        /* =========================================================
           HEADERS
           ========================================================= */
        .nav-header {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-muted);
            margin: 14px 12px 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        /* Headers personalizados */
        .nav-header.panel-general {
            color: #60a5fa;
            border-left: 3px solid #2563eb;
            padding-left: 10px;
        }

        .nav-header.ventas-directas {
            color: #4ade80;
            border-left: 3px solid #22c55e;
            padding-left: 10px;
            background: rgba(34,197,94,.06);
        }

        .nav-header.duenos-marca {
            color: #facc15;
            border-left: 3px solid #f59e0b;
            padding-left: 10px;
            background: rgba(245,158,11,.05);
        }

        /* =========================================================
           CERRAR SESIÓN
           ========================================================= */
        .nav-item a[href*="cerrar_sesion"] {
            background: #991b1b !important;
            color: #fff !important;
            border-radius: 6px;
            margin-top: 12px;
        }

        .nav-item a[href*="cerrar_sesion"]:hover {
            background: #b91c1c !important;
        }
        .nav-header.inventario {
            color: #60a5fa;            /* Azul inventario */
            border-left: 3px solid #2563eb;
            padding-left: 10px;
            background: rgba(37, 99, 235, 0.05);
        }


    </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- NAVBAR -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">

        <!-- ICONO MENU -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>

            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link">SISTEMA SERCONSLIMP</a>
            </li>
        </ul>

        <!-- FULLSCREEN -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>

    </nav>

    <!-- SIDEBAR -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <!-- LOGO -->
        <a href="<?php echo $URL;?>" class="brand-link">
            <img src="<?php echo $URL;?>/public/images/logo2.png" class="brand-image img-circle elevation-3">
            <span class="brand-text font-weight-light">SERCONSLIMP</span>
        </a>

        <div class="sidebar">

            <!-- USUARIO -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="<?php echo $URL;?>/public/templeates/AdminLTE-3.2.0/dist/img/user1-128x128.jpg" class="img-circle elevation-2">
                </div>
                <div class="info">
                    <a class="d-block"><?php echo $nombres_sesion; ?></a>
                </div>
            </div>

            <!-- MENÚ COMPLETO (SIN CAMBIAR NADA TUYO) -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column"
                    data-widget="treeview"
                    data-accordion="false">
                <!-- Gerente-->
                    <?php if ($rol_sesion == "Gerente") { ?>

                        <li class="nav-header panel-general mt-3">
                            <i class="fas fa-sliders-h mr-1"></i> PANEL DE CONTROL
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo $URL;?>/seccion" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>General<i class="right fas fa-angle-left"></i></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $URL;?>/pedidos/alertas_despacho.php" class="nav-link">
                                <i class="fas fa-bell nav-icon"></i>
                                <p>Alertas de Despacho</p>
                            </a>
                        </li>

                        <!-- ALMACEN -->
                        <!-- INVENTARIO-->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Inventario<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/inventario/costos.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Costos</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/inventario/index.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Stock Productos</a></li>
                            </ul>
                        </li>


                        <li class="nav-header ventas-directas mt-4">
                            <i class="fas fa-cash-register mr-1"></i> VENTAS DIRECTAS
                        </li>


                        <!-- PRODUCTOS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Productos<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/palmacen" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Productos</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/palmacen/create.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Creación de Productos</a></li>
                            </ul>
                        </li>

                        <!-- PROFORMAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Proformas<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/proforma" class="nav-link"><i class="far fa-circle nav-icon"></i>Lista de Proformas</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/proforma/create.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Creación de Proformas</a></li>
                            </ul>
                        </li>

                        <li class="nav-header duenos-marca mt-4">
                            <i class="fas fa-crown mr-1"></i> DUEÑOS DE MARCA
                        </li>


                        <li class="nav-item">
                            <a href="#" class="nav-link "><!-- Pon active al final para subrayar-->
                                <i class="nav-icon fas fa-list"></i>
                                <p>
                                    Productos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL;?>/almacen" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de Productos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link"><!-- Pon active al final para subrayar-->
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>
                                    Cotizaciones
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL;?>/cotizacion/create.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Cotizacion</p>
                                    </a>
                                    <a href="<?php echo $URL;?>/cotizacion" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Principales</p>
                                    </a>
                                    <a href="<?php echo $URL;?>/cotizacion/secundario.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Vianfortpro</p>
                                    </a>
                                    <a href="<?php echo $URL;?>/pedidos" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Gestion de Pedidos</p>
                                    </a>
                                </li>

                            </ul>
                        </li>


                    <?php } ?>

                    <?php if ($rol_sesion == "Administrador") { ?>

                        <li class="nav-header panel-general mt-3">
                            <i class="fas fa-sliders-h mr-1"></i> PANEL DE CONTROL
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo $URL;?>/seccion" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>General<i class="right fas fa-angle-left"></i></p>
                            </a>
                        </li>

                        <!-- ALMACEN -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-truck nav-icon"></i>
                                <p>Almacén<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/pedidos/gestion.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Pedidos</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/lote" class="nav-link"><i class="far fa-circle nav-icon"></i>Gestion de Lote</a></li>
                                <li class="nav-item">
                                    <a href="<?php echo $URL;?>/pedidos/alertas_despacho.php" class="nav-link">
                                        <i class="fas fa-bell nav-icon"></i>
                                        <p>Alertas de Despacho</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- INVENTARIO-->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Inventario<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/inventario/gestion.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Operaciones</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/inventario/index.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Stock</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/inventario/produc.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Productos</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/inventario/costos.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Costos</a></li>
                            </ul>
                        </li>

                        <!-- REPORTE DE COTIZACIONES -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Reporte de Cotizaciones<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL;?>/reportes/reporte_ganancia.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Reporte General
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-header ventas-directas mt-4">
                            <i class="fas fa-cash-register mr-1"></i> VENTAS DIRECTAS
                        </li>
                        <!-- MARCAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Marcas<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/pmarcas" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Marcas</a></li>
                            </ul>
                        </li>

                        <!-- CATEGORIAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p>Categorias<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/pcategorias" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Categorias</a></li>
                            </ul>
                        </li>

                        <!-- PRODUCTOS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Productos<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/palmacen" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Productos</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/palmacen/create.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Creación de Productos</a></li>
                            </ul>
                        </li>

                        <!-- PROFORMAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Proformas<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/proforma" class="nav-link"><i class="far fa-circle nav-icon"></i>Lista de Proformas</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/proforma/create.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Creación de Proformas</a></li>
                            </ul>
                        </li>

                        <li class="nav-header duenos-marca mt-4">
                            <i class="fas fa-crown mr-1"></i> DUEÑOS DE MARCA
                        </li>
                        <!-- PROVEEDORES -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-handshake"></i>
                                <p>Proveedores<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/proveedores" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Proveedores</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link "><!-- Pon active al final para subrayar-->
                                <i class="nav-icon fas fa-list"></i>
                                <p>
                                    Productos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL;?>/almacen" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Listado de Productos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link"><!-- Pon active al final para subrayar-->
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>
                                    Cotizaciones
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL;?>/cotizacion/create.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Cotizacion</p>
                                    </a>
                                    <a href="<?php echo $URL;?>/cotizacion" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Principales</p>
                                    </a>
                                    <a href="<?php echo $URL;?>/cotizacion/secundario.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Vianfortpro</p>
                                    </a>
                                    <a href="<?php echo $URL;?>/pedidos" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Gestion de Pedidos</p>
                                    </a>
                                </li>

                            </ul>
                        </li>


                        <!-- MARCAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Marcas<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/marcas" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Marcas</a></li>
                            </ul>
                        </li>

                        <!-- CATEGORIAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p>Categorias<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/categorias" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Categorias</a></li>
                            </ul>
                        </li>



                        <!-- CLIENTES -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-briefcase"></i>
                                <p>Clientes<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/clientes" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Clientes</a></li>
                            </ul>
                        </li>
                    <?php } ?>


                    <?php if ($rol_sesion == "Empleado Estandar") { ?>

                        { ?>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Reporte de Cotizaciones<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL;?>/reportes/reporte_ganancia.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Reporte General
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-header ventas-directas mt-4">
                            <i class="fas fa-cash-register mr-1"></i> VENTAS DIRECTAS
                        </li>
                        <!-- MARCAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Marcas<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/pmarcas" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Marcas</a></li>
                            </ul>
                        </li>

                        <!-- CATEGORIAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p>Categorias<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/pcategorias" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Categorias</a></li>
                            </ul>
                        </li>
                        <!-- PRODUCTOS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Productos<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/palmacen" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Productos</a></li>
                            </ul>
                        </li>

                        <!-- PROFORMAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Proformas<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/proforma" class="nav-link"><i class="far fa-circle nav-icon"></i>Lista de Proformas</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/proforma/create.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Creación de Proformas</a></li>
                            </ul>
                        </li>


                        <li class="nav-header duenos-marca mt-4">
                            <i class="fas fa-crown mr-1"></i> DUEÑOS DE MARCA
                        </li>
                        <!-- AJUSTES -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>Ajustes<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/clientes" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Clientes</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/proveedores" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Proveedores</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/categorias" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Categorias</a></li>

                            </ul>
                        </li>

                        <!-- PRODUCTOS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Productos<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/almacen" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Productos</a></li>
                            </ul>
                        </li>

                        <!-- COTIZACIONES -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Cotizaciones<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/cotizacion/create.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Crear Cotización</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/cotizacion" class="nav-link"><i class="far fa-circle nav-icon"></i>Principales</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/cotizacion/secundario.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Vianfortpro</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Pedidos<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/pedidos" class="nav-link"><i class="far fa-circle nav-icon"></i>Gestión de Pedidos</a></li>
                            </ul>
                        <li class="nav-item">
                            <a href="<?php echo $URL;?>/pedidos/alertas_despacho.php" class="nav-link">
                                <i class="fas fa-bell nav-icon"></i>
                                <p>Alertas de Despacho</p>
                            </a>
                        </li>
                        </li>
                        <li class="nav-header inventario mt-4">
                            <i class="fas fa-boxes mr-1"></i>INVENTARIO
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $URL;?>/inventario/index.php" class="nav-link">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>Consultar Stock</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo $URL;?>/inventario/produc.php" class="nav-link">
                                <i class="nav-icon fas fa-box-open"></i>
                                <p>Consultar Producto</p>
                            </a>
                        </li>


                    <?php } ?>

                    <!-- EMPLEADO / VENDEDOR -->
                    <?php if ($rol_sesion == "Vendedor" || $rol_sesion == "Empleado" ) { ?>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Reporte de Cotizaciones<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL;?>/reportes/reporte_ganancia.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Reporte General
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-header ventas-directas mt-4">
                            <i class="fas fa-cash-register mr-1"></i> VENTAS DIRECTAS
                        </li>
                        <!-- MARCAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Marcas<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/pmarcas" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Marcas</a></li>
                            </ul>
                        </li>

                        <!-- CATEGORIAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-receipt"></i>
                                <p>Categorias<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/pcategorias" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Categorias</a></li>
                            </ul>
                        </li>
                        <!-- PRODUCTOS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Productos<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/palmacen" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Productos</a></li>
                            </ul>
                        </li>

                        <!-- PROFORMAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Proformas<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/proforma" class="nav-link"><i class="far fa-circle nav-icon"></i>Lista de Proformas</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/proforma/create.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Creación de Proformas</a></li>
                            </ul>
                        </li>


                        <li class="nav-header duenos-marca mt-4">
                            <i class="fas fa-crown mr-1"></i> DUEÑOS DE MARCA
                        </li>
                        <!-- AJUSTES -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>Ajustes<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/clientes" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Clientes</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/proveedores" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Proveedores</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/categorias" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Categorias</a></li>

                            </ul>
                        </li>

                        <!-- PRODUCTOS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Productos<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/almacen" class="nav-link"><i class="far fa-circle nav-icon"></i>Listado de Productos</a></li>
                            </ul>
                        </li>

                        <!-- COTIZACIONES -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Cotizaciones<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/cotizacion/create.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Crear Cotización</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/cotizacion" class="nav-link"><i class="far fa-circle nav-icon"></i>Principales</a></li>
                                <li class="nav-item"><a href="<?php echo $URL;?>/cotizacion/secundario.php" class="nav-link"><i class="far fa-circle nav-icon"></i>Vianfortpro</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Pedidos<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="<?php echo $URL;?>/pedidos" class="nav-link"><i class="far fa-circle nav-icon"></i>Gestión de Pedidos</a></li>
                            </ul>
                        <li class="nav-item">
                            <a href="<?php echo $URL;?>/pedidos/alertas_despacho.php" class="nav-link">
                                <i class="fas fa-bell nav-icon"></i>
                                <p>Alertas de Despacho</p>
                            </a>
                        </li>
                        </li>
                        <li class="nav-header inventario mt-4">
                            <i class="fas fa-boxes mr-1"></i>INVENTARIO
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $URL;?>/inventario/index.php" class="nav-link">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>Consultar Stock</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo $URL;?>/inventario/produc.php" class="nav-link">
                                <i class="nav-icon fas fa-box-open"></i>
                                <p>Consultar Producto</p>
                            </a>
                        </li>


                    <?php } ?>

                    <!-- EMPLEADO DE ALMACÉN -->
                    <?php if ($rol_sesion == "Empleado Almacen") { ?>
                        <!-- INVENTARIO -->
                        <li class="nav-header inventario mt-4">
                            <i class="fas fa-boxes mr-1"></i> INVENTARIO
                        </li>

                        <!-- OPERACIONES DE ALMACÉN -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-warehouse"></i>
                                <p>
                                    Almacen
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL; ?>/pedidos/gestion.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Lista de Pedidos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo $URL;?>/pedidos/alertas_despacho.php" class="nav-link">
                                        <i class="fas fa-bell nav-icon"></i>
                                        <p>Alertas de Despacho</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- TRAZABILIDAD -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-barcode"></i>
                                <p>
                                    Lotes
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL; ?>/lote" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Control de Lotes
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- CONSULTAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-search"></i>
                                <p>
                                    Consultas
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL; ?>/inventario/index.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Consultar Stock
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="<?php echo $URL; ?>/inventario/produc.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Consultar Productos
                                    </a>
                                </li>
                            </ul>
                        </li>


                    <?php } ?>

                    <!-- EMPLEADO DE ALMACÉN -->
                    <?php if ($rol_sesion == "Encargado Almacen") { ?>


                        <!-- OPERACIONES DE ALMACÉN -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-truck"></i>
                                <p>
                                    Almacen
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL; ?>/pedidos/gestion.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Lista de Pedidos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo $URL;?>/pedidos/alertas_despacho.php" class="nav-link">
                                        <i class="fas fa-bell nav-icon"></i>
                                        <p>Alertas de Despacho</p>
                                    </a>
                                </li>


                            </ul>

                        </li>
                        <!-- TRAZABILIDAD -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-barcode"></i>
                                <p>
                                    Lotes
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL; ?>/lote" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Control de Lotes
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- INVENTARIO -->
                        <li class="nav-header inventario mt-4">
                            <i class="fas fa-boxes mr-1"></i> INVENTARIO
                        </li>



                        <!-- CONSULTAS -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-search"></i>
                                <p>
                                    Consultas
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL; ?>/inventario/index.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Consultar Stock
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="<?php echo $URL; ?>/inventario/produc.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Consultar Productos
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-warehouse"></i>
                                <p>
                                    Operaciones
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="<?php echo $URL; ?>/inventario/gestion.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        Gestion de Inventario
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>

                    <!-- CERRAR SESIÓN -->
                    <li class="nav-item">
                        <a href="<?php echo $URL;?>/app/controllers/login/cerrar_sesion.php" style="background-color:#ca0a0b" class="nav-link">
                            <i class="nav-icon fas fa-door-closed"></i>
                            <p>Cerrar Sesión</p>
                        </a>
                    </li>

                </ul>
            </nav>

        </div>
    </aside>

</div>

<!-- BOOTSTRAP -->
<!-- BOOTSTRAP -->
<script src="<?= $URL ?>/public/templeates/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- ADMINLTE -->
<script src="<?= $URL ?>/public/templeates/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>

<script>
    $(function () {
        $('.nav-sidebar').Treeview({
            animationSpeed: 120,
            accordion: false
        });
    });
</script>



</body>
</html>
