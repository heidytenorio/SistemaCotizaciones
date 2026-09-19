<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/marcas/listado_de_marcas.php');
include('../app/controllers/categorias/listado_de_categorias.php');
include('../app/controllers/proveedores/listado_de_proveedores.php');


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Registro de Nuevo Producto</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Llene los datos con cuidado</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body" style="display: block;">
                            <div class="row">
                                <div class="col-md-12">

                                    <form action="../app/controllers/almacen/create.php" method="post" enctype="multipart/form-data">
                                        <div class="row">
                                            <!-- Columna principal con campos -->
                                            <div class="col-md-9">
                                                <!-- Primera fila: Código, Descripción, Competencia -->
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="">Código:</label>
                                                        <input type="text" name="codigo" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label for="">Descripción del producto:</label>
                                                        <textarea name="descripcion" cols="20" rows="2" class="form-control"></textarea>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="">Categoría:</label>
                                                        <div class="d-flex">
                                                        <select name="id_categoria" class="form-control" required>
                                                            <?php foreach ($categorias_datos as $categorias_dato): ?>
                                                                <option value="<?php echo $categorias_dato['id_categoria']; ?>">
                                                                    <?php echo $categorias_dato['nombre_categoria']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <a href="<?php echo $URL; ?>/categorias" class="btn btn-primary ml-1"><i class="fa fa-plus"></i></a>
                                                    </div>
                                                    </div>
                                                </div>

                                                <!-- Segunda fila: Categoría y Marca -->
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <label for="">Proveedores:</label>
                                                        <div class="d-flex">
                                                            <select name="id_proveedor" class="form-control" required>
                                                                <?php foreach ($proveedor_datos as $proveedor_dato): ?>
                                                                    <option value="<?php echo $proveedor_dato['id_proveedor']; ?>">
                                                                        <?php echo $proveedor_dato['razon_social']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <a href="<?php echo $URL; ?>/proveedores" class="btn btn-primary ml-1"><i class="fa fa-plus"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="">Marca:</label>
                                                        <div class="d-flex">
                                                            <select name="id_marca" class="form-control" required>
                                                                <?php foreach ($marcas_datos as $marcas_dato): ?>
                                                                    <option value="<?php echo $marcas_dato['id_marca']; ?>">
                                                                        <?php echo $marcas_dato['nombre']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <a href="<?php echo $URL; ?>/marcas" class="btn btn-primary ml-1"><i class="fa fa-plus"></i></a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tercera fila: Precios -->
                                                <div class="row mt-3">

                                                    <!-- Mayor -->
                                                    <div class="col-md-2">
                                                        <label for="">Costo X Mayor:</label>
                                                        <input type="number" step="0.01" name="costo_mayorista" id="costo_mayorista" class="form-control" oninput="calcularPrecioMayorista()">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="">% X Mayor:</label>
                                                        <input type="number" step="0.01" name="porcentaje_mayorista" id="porcentaje_mayorista" class="form-control" oninput="calcularPrecioMayorista()">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="">Precio X Mayor:</label>
                                                        <input type="number" step="0.01" name="precio_mayorista" id="precio_mayorista" class="form-control" readonly>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="">Info Adicional:</label>
                                                        <textarea name="info" cols="20" rows="2" class="form-control"></textarea>
                                                    </div>
                                                </div>


                                            </div>

                                            <!-- Columna lateral para imagen -->
                                            <div class="col-md-3">
                                                <label for="">Imagen del producto:</label>
                                                <input type="file" name="image" class="form-control" id="file">
                                                <br>
                                                <output id="list"></output>
                                                <script>
                                                    function archivo(evt) {
                                                        var files = evt.target.files;
                                                        for (var i = 0, f; f = files[i]; i++) {
                                                            if (!f.type.match('image.*')) continue;
                                                            var reader = new FileReader();
                                                            reader.onload = (function (theFile) {
                                                                return function (e) {
                                                                    document.getElementById("list").innerHTML =
                                                                        '<img class="img-fluid rounded border" src="' + e.target.result + '" title="' + escape(theFile.name) + '">';
                                                                };
                                                            })(f);
                                                            reader.readAsDataURL(f);
                                                        }
                                                    }
                                                    document.getElementById('file').addEventListener('change', archivo, false);
                                                </script>
                                            </div>
                                        </div>

                                        <!-- Scripts de cálculo -->
                                        <script>

                                            function calcularPrecioMayorista() {
                                                const costo = parseFloat(document.getElementById('costo_mayorista').value) || 0;
                                                const porcentaje = parseFloat(document.getElementById('porcentaje_mayorista').value) || 0;
                                                const precio = costo + (costo * porcentaje / 100);
                                                document.getElementById('precio_mayorista').value = precio.toFixed(2);
                                            }
                                        </script>
                                        <script>
                                            document.querySelector('form').addEventListener('submit', function(e) {
                                                const inputsNumericos = this.querySelectorAll('input[type="number"]');
                                                inputsNumericos.forEach(input => {
                                                    if (input.value.trim() === '') {
                                                        input.value = 0;
                                                    }
                                                });
                                            });
                                        </script>

                                        <hr>
                                        <div class="form-group">
                                            <a href="index.php" class="btn btn-secondary">Cancelar</a>
                                            <button type="submit" class="btn btn-primary">Guardar producto</button>
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
