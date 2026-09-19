<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/pmarcas/listado_de_marcas.php');
include('../app/controllers/pcategorias/listado_de_categorias.php');


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

                                    <form action="../app/controllers/palmacen/create.php" method="post" enctype="multipart/form-data">
                                        <div class="row">
                                            <!-- Columna principal con campos -->
                                            <div class="col-md-9">
                                                <!-- Primera fila: Código, Descripción, Competencia -->
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="">Código:</label>
                                                        <input type="text" name="codigo" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="">Descripción del producto:</label>
                                                        <textarea name="descripcion" cols="20" rows="2" class="form-control"></textarea>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label for="moneda">Moneda</label>
                                                        <select class="form-control" id="moneda" name="moneda" style="text-align:center;">
                                                            <option value="SOLES" <?php echo (isset($moneda) && $moneda == 'SOLES') ? 'selected' : ''; ?>>SOLES</option>
                                                            <option value="DOLARES" <?php echo (isset($moneda) && $moneda == 'DOLARES') ? 'selected' : ''; ?>>DÓLARES</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Segunda fila: Categoría y Marca -->
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <label for="">Categoría:</label>
                                                        <div class="d-flex">
                                                            <select name="id_categoria" class="form-control" required>
                                                                <?php foreach ($pcategorias_datos as $pcategorias_dato): ?>
                                                                    <option value="<?php echo $pcategorias_dato['id_categoria']; ?>">
                                                                        <?php echo $pcategorias_dato['nombre_categoria']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <a href="<?php echo $URL; ?>/pcategorias" class="btn btn-primary ml-1"><i class="fa fa-plus"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="">Marca:</label>
                                                        <div class="d-flex">
                                                            <select name="id_marca" class="form-control" required>
                                                                <?php foreach ($pmarcas_datos as $pmarcas_dato): ?>
                                                                    <option value="<?php echo $pmarcas_dato['id_marca']; ?>">
                                                                        <?php echo $pmarcas_dato['nombre']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <a href="<?php echo $URL; ?>/pmarcas" class="btn btn-primary ml-1"><i class="fa fa-plus"></i></a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tercera fila: Precios -->
                                                <div class="row mt-3">
                                                    <!-- Menor -->
                                                    <div class="col-md-2">
                                                        <label for="">Costo X Menor:</label>
                                                        <input type="number" step="0.01" name="costo_minorista" id="costo_minorista" class="form-control" oninput="calcularPrecioMinorista()">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="">% X Menor:</label>
                                                        <input type="number" step="0.01" name="porcentaje_minorista" id="porcentaje_minorista" class="form-control" oninput="calcularPrecioMinorista()">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="">Precio X Menor:</label>
                                                        <input type="number" step="0.01" name="precio_minorista" id="precio_minorista" class="form-control" readonly>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label for="">Cambio:</label>
                                                        <input type="number" step="0.01" name="cambio" id="cambio" class="form-control">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="">Info Adicional:</label>
                                                        <textarea name="info" cols="20" rows="2" class="form-control"></textarea>
                                                    </div>
<!--                                                  -->
<!--                                                    <div class="col-md-2">-->
<!--                                                        <label for="">Costo X Mayor:</label>-->
<!--                                                        <input type="number" step="0.01" name="costo_mayorista" id="costo_mayorista" class="form-control" oninput="calcularPrecioMayorista()">-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-2">-->
<!--                                                        <label for="">% X Mayor:</label>-->
<!--                                                        <input type="number" step="0.01" name="porcentaje_mayorista" id="porcentaje_mayorista" class="form-control" oninput="calcularPrecioMayorista()">-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-2">-->
<!--                                                        <label for="">Precio X Mayor:</label>-->
<!--                                                        <input type="number" step="0.01" name="precio_mayorista" id="precio_mayorista" class="form-control" readonly>-->
<!--                                                    </div>-->
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

                                        <script>
                                            // --- Función para calcular el precio minorista normal ---
                                            function calcularPrecioMinorista() {
                                                const costo = parseFloat(document.getElementById('costo_minorista').value) || 0;
                                                const porcentaje = parseFloat(document.getElementById('porcentaje_minorista').value) || 0;
                                                const moneda = document.getElementById('moneda').value;
                                                const cambio = parseFloat(document.getElementById('cambio').value) || 1;

                                                // Precio base según costo + porcentaje
                                                let precio = costo + (costo * porcentaje / 100);

                                                // ✅ Si la moneda es DÓLARES, aplicar tipo de cambio
                                                if (moneda === 'DOLARES') {
                                                    precio = precio * cambio;
                                                }

                                                document.getElementById('precio_minorista').value = precio.toFixed(2);
                                            }

                                            // --- Activar o desactivar el campo "Cambio" según moneda seleccionada ---
                                            document.getElementById('moneda').addEventListener('change', function() {
                                                const cambioInput = document.getElementById('cambio');
                                                const moneda = this.value;

                                                if (moneda === 'SOLES') {
                                                    cambioInput.value = '';
                                                    cambioInput.disabled = true;
                                                } else {
                                                    cambioInput.disabled = false;
                                                }

                                                // recalcular automáticamente al cambiar la moneda
                                                calcularPrecioMinorista();
                                            });

                                            // --- Detectar cambios en campos para recalcular automáticamente ---
                                            document.getElementById('cambio').addEventListener('input', calcularPrecioMinorista);
                                            document.getElementById('costo_minorista').addEventListener('input', calcularPrecioMinorista);
                                            document.getElementById('porcentaje_minorista').addEventListener('input', calcularPrecioMinorista);

                                            // --- Inicializar estado al cargar la página ---
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const moneda = document.getElementById('moneda').value;
                                                const cambioInput = document.getElementById('cambio');

                                                if (moneda === 'SOLES') {
                                                    cambioInput.disabled = true;
                                                } else {
                                                    cambioInput.disabled = false;
                                                }
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


