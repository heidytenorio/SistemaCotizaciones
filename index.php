<?php
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/controllers/login/validar_sesion.php';
include('layout/sesion.php');
include('layout/parte1.php');
include('app/controllers/pedidos/listado_de_pedidos.php');
include('app/controllers/roles/listado_de_roles.php');
include('app/controllers/proveedores/listado_de_proveedores.php');
include('app/controllers/clientes/listado_de_clientes.php');
?>


<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0">Bienvenido - <?php echo $rol_sesion; ?></h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">

            <!-- Imagen de bienvenida -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center p-0">
                            <img src="<?php echo $URL; ?>/public/images/fondo11.jpg" alt="Bienvenido" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="row g-3">

                <!-- Clientes Satisfechos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-success shadow">
                        <div class="inner">
                            <?php $contador_de_clientes = count($clientes_datos); ?>
                            <h3><?php echo $contador_de_clientes; ?></h3>
                            <p>Clientes Satisfechos</p>
                        </div>
                        <a href="#" class="icon">
                            <i class="fas fa-users"></i>
                        </a>
                        <a href="#" class="small-box-footer">
                            Más detalles <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Pedidos Registrados -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-info shadow">
                        <div class="inner">
                            <?php $contador_de_pedidos = count($pedidos_datos); ?>
                            <h3><?php echo $contador_de_pedidos; ?></h3>
                            <p>Pedidos Registrados</p>
                        </div>
                        <a href="#" class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                        <a href="#" class="small-box-footer">
                            Más detalles <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Proveedores Estratégicos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-warning shadow">
                        <div class="inner">
                            <?php $contador_de_proveedor = count($proveedor_datos); ?>
                            <h3><?php echo $contador_de_proveedor; ?></h3>
                            <p>Proveedores Estratégicos</p>
                        </div>
                        <a href="#" class="icon">
                            <i class="fas fa-truck-loading"></i>
                        </a>
                        <a href="#" class="small-box-footer">
                            Más detalles <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Roles Registrados -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-primary shadow">
                        <div class="inner">
                            <?php $contador_de_roles = count($roles_datos); ?>
                            <h3><?php echo $contador_de_roles; ?></h3>
                            <p>Roles Registrados</p>
                        </div>
                        <a href="#" class="icon">
                            <i class="fas fa-users-cog"></i>
                        </a>
                        <a href="<?php echo $URL; ?>/roles" class="small-box-footer">
                            Más detalles <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

            </div>
            <!-- /.row -->

        </div>
    </div>
    <!-- /.content -->

</div>
<!-- /.content-wrapper -->

<?php include('layout/parte2.php'); ?>
