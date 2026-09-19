<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../app/controllers/inventario/listado_almacen2.php');
include('../layout/parte1.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ajustes</title>

    <!-- Animate.css -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>

<body>
<div class="content-wrapper">

    <!-- ================= HEADER ================= -->
    <div class="content-header mb-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-warehouse"></i> Configuración</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <!-- ================= OPCIONES ================= -->
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <div class="row">

                        <!-- USUARIOS -->
                        <?php if ($rol_sesion === 'Administrador') { ?>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-header text-center">
                                        <h4><i class="fas fa-users text-primary"></i> Usuarios</h4>
                                    </div>
                                    <div class="card-body text-center">
                                        <a href="<?= $URL ?>/usuarios" class="btn btn-primary btn-block mb-2">
                                            <i class="fas fa-list"></i> Listar
                                        </a>
                                        <a href="<?= $URL ?>/usuarios/create.php" class="btn btn-success btn-block">
                                            <i class="fas fa-user-plus"></i> Crear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- ROLES -->
                        <?php if ($rol_sesion === 'Administrador') { ?>
                            <div class="col-md-4">
                                <div class="card bg-light ">
                                    <div class="card-header text-center">
                                        <h4><i class="fas fa-id-card text-warning"></i> Roles</h4>
                                    </div>
                                    <div class="card-body text-center">
                                        <a href="<?= $URL ?>/roles" class="btn btn-warning btn-block mb-2">
                                            <i class="fas fa-list"></i> Listar
                                        </a>
                                        <a href="<?= $URL ?>/roles/create.php" class="btn btn-success btn-block">
                                            <i class="fas fa-plus-circle"></i> Crear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- ESTADÍSTICAS -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header text-center">
                                    <h4><i class="fas fa-chart-bar text-info"></i> Estadísticas</h4>
                                </div>
                                <div class="card-body text-center">
                                    <a href="<?= $URL ?>/inventario/general.php" class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-warehouse"></i> Inventario
                                    </a>
                                    <a href="<?= $URL ?>/estadisticas/usuarios.php" class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-users"></i> Usuarios
                                    </a>
                                    <a href="<?= $URL ?>/estadisticas/ventas.php" class="btn btn-info btn-block">
                                        <i class="fas fa-dollar-sign"></i> Ventas
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ================= RESUMEN DEL SISTEMA ================= -->
            <div class="card mt-4 animate__animated animate__fadeIn">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram"></i> Resumen del Sistema
                    </h3>
                </div>

                <div class="card-body">

                    <!-- ===== ESTADO OPERATIVO ===== -->
                    <div class="row text-center mb-4">

                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h5>Usuarios</h5>
                                    <p>Configurado</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h5>Roles</h5>
                                    <p>Definidos</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h5>Inventario</h5>
                                    <p>Operativo</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h5>Estadísticas</h5>
                                    <p>Disponibles</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ===== MAPA DEL SISTEMA ===== -->
                    <div class="text-center animate__animated animate__fadeInUp">
                        <i class="fas fa-users fa-2x text-primary"></i>
                        <i class="fas fa-arrow-right mx-2"></i>
                        <i class="fas fa-id-card fa-2x text-warning"></i>
                        <i class="fas fa-arrow-right mx-2"></i>
                        <i class="fas fa-warehouse fa-2x text-info"></i>
                        <i class="fas fa-arrow-right mx-2"></i>
                        <i class="fas fa-chart-bar fa-2x text-success"></i>
                    </div>

                    <p class="text-center text-muted mt-2">
                        Flujo operativo del sistema
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>

</body>
</html>
