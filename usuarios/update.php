<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');

include('../app/controllers/usuarios/update_usuario.php');
include('../app/controllers/roles/listado_de_roles.php');


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Actualizar Usuario</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Llene los datos con cuidado</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body" style="display: block;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form action="../app/controllers/usuarios/update.php" method="post">
                                        <input type="text" name="id_usuario" value="<?php echo $id_usuario_get;?>" hidden>
                                        <div class="form-group">
                                            <label for="">Nombres </label>
                                            <input type="text"  name="nombres" class="form-control" value="<?php echo $nombres;?>" placeholder="Escriba aqui el nombre del nuevo usuario">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Email</label>
                                            <input type="email" name="email" class="form-control" value="<?php echo $email;?>" placeholder="Escriba aqui el correo del nuevo usuario">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Rol del Usuario</label>
                                            <select name="rol" id="" class="form-control">
                                                <?php
                                                foreach ($roles_datos as $roles_dato){
                                                    $rol_tabla=$roles_dato['rol'];
                                                    $id_rol=$roles_dato['id_rol'];?>
                                                    <option value="<?php echo $id_rol;?>"<?php if($rol_tabla==$rol){?>selected="selected"<?php }?>>
                                                        <?php echo $rol_tabla;?>
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Contraseña</label>
                                            <input type="text" name="password_user" class="form-control" >
                                        </div>
                                        <div class="form-group">
                                            <label for="">Repita la Contraseña</label>
                                            <input type="text" name="password_repeat" class="form-control" >
                                        </div>
                                        <div class="form-group">
                                            <a href="index.php" class="btn btn-secondary"> Cancelar</a>
                                            <button type="submit" class="btn btn-success">Actualizar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>






