<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/pmarcas/listado_de_marcas.php');
include('../app/controllers/pcategorias/listado_de_categorias.php');

// Verificamos si existe el id_producto por GET
if (isset($_GET['id'])) {
    $id_producto_get = $_GET['id'];

    // Cargar datos del producto
    $sql = "SELECT * FROM tbp_almacen WHERE id_producto = :id_producto";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_producto', $id_producto_get);
    $stmt->execute();

    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        echo "<script>alert('Producto no encontrado'); window.location.href='index.php';</script>";
        exit();
    }

    // Asignar variables
    $codigo = $producto['codigo'];
    $descripcion = $producto['descripcion'];
    $id_marca = $producto['id_marca'];
    $id_categoria = $producto['id_categoria'];

    $costo_minorista = $producto['costo_minorista'];
    $porcentaje_minorista = $producto['porcentaje_minorista'];
    $precio_minorista = $producto['precio_minorista'];
    $moneda = $producto['moneda'];
    $cambio = $producto['cambio'];

    $ganancia_minorista = round($costo_minorista * $porcentaje_minorista / 100, 2);
    $precio_base = round($costo_minorista + $ganancia_minorista, 2);

// Aplicar cambio si es dólares
    if ($moneda === 'DOLARES' && $cambio > 0) {
        $precio_calculado = round($precio_base * $cambio, 2);
    } else {
        $precio_calculado = $precio_base;
    }

// Corregir si BD tiene valor distinto
    if (abs($precio_minorista - $precio_calculado) > 0.01) {
        $precio_minorista = $precio_calculado;
    }


    $costo_mayorista = $producto['costo_mayorista'];
    $porcentaje_mayorista = $producto['porcentaje_mayorista'];
    $precio_mayorista = $producto['precio_mayorista'];
    $imagen = $producto['imagen'];


    $info = $producto['info'];
} else {
    echo "<script>alert('ID no válido'); window.location.href='index.php';</script>";
    exit();
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Actualizar Producto</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Llene los datos con cuidado</h3>
                </div>

                <div class="card-body">
                    <form action="../app/controllers/palmacen/update.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_producto" value="<?php echo $id_producto_get; ?>">

                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <!-- Código -->
                                    <div class="col-md-4">
                                        <label>Código:</label>
                                        <input type="text" name="codigo" class="form-control" value="<?php echo $codigo; ?>">
                                    </div>

                                    <!-- Descripción -->
                                    <div class="col-md-4">
                                        <label>Descripción del producto:</label>
                                        <textarea name="descripcion" class="form-control" rows="2"><?php echo $descripcion; ?></textarea>
                                    </div>


                                    <div class="form-group col-md-3">
                                        <label for="moneda">Moneda</label>
                                        <select class="form-control" id="moneda" name="moneda" style="text-align:center;">
                                            <option value="SOLES" <?php echo (isset($moneda) && $moneda == 'SOLES') ? 'selected' : ''; ?>>SOLES</option>
                                            <option value="DOLARES" <?php echo (isset($moneda) && $moneda == 'DOLARES') ? 'selected' : ''; ?>>DÓLARES</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="row">
                                    <!-- Categoría -->
                                    <div class="col-md-6">
                                        <label>Categoría:</label>
                                        <select name="id_categoria" class="form-control" required>
                                            <?php foreach ($pcategorias_datos as $cat): ?>
                                                <option value="<?php echo $cat['id_categoria']; ?>"
                                                    <?php echo ($cat['id_categoria'] == $id_categoria) ? 'selected' : ''; ?>>
                                                    <?php echo $cat['nombre_categoria']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Marca -->
                                    <div class="col-md-6">
                                        <label>Marca:</label>
                                        <select name="id_marca" class="form-control" required>
                                            <?php foreach ($pmarcas_datos as $marca): ?>
                                                <option value="<?php echo $marca['id_marca']; ?>"
                                                    <?php echo ($marca['id_marca'] == $id_marca) ? 'selected' : ''; ?>>
                                                    <?php echo $marca['nombre']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Precios -->
                                <div class="row mt-3">
<!--                                    <div class="col-md-2">-->
<!--                                        <label>Costo X Menor:</label>-->
<!--                                        <input type="number" step="0.01" name="costo_minorista" id="costo_minorista" class="form-control" value="--><?php //echo $costo_minorista; ?><!--" oninput="calcularPrecioMinorista()">-->
<!--                                    </div>-->
                                    <div class="col-md-2">
                                        <label>Costo X Menor:</label>
                                        <input
                                                type="number"
                                                step="0.01"
                                                name="costo_minorista"
                                                id="costo_minorista"
                                                class="form-control"
                                                value="<?php echo $costo_minorista; ?>"
                                                oninput="calcularPrecioMinorista()"
                                            <?php echo ($rol_sesion !== 'Administrador') ? 'readonly' : ''; ?>
                                        >
                                    </div>
                                    <div class="col-md-2">
                                        <label>% X Menor:</label>
                                        <input type="number" step="0.01" name="porcentaje_minorista" id="porcentaje_minorista" class="form-control" value="<?php echo $porcentaje_minorista; ?>" oninput="calcularPrecioMinorista()">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Precio X Menor:</label>
                                        <input type="number" step="0.01" name="precio_minorista" id="precio_minorista" class="form-control" value="<?php echo $precio_minorista; ?>" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">Cambio:</label>
                                        <input type="number" step="0.01" name="cambio" id="cambio" class="form-control" value="<?php echo $cambio; ?>">

                                    </div>
                                    <div class="col-md-4">
                                        <label for="">Info Adicional:</label>
                                        <textarea name="info" cols="20" rows="2" class="form-control"><?php echo $info; ?></textarea>

                                    </div>
                                </div>
                            </div>

                            <!-- Imagen -->
                            <div class="col-md-3">
                                <label>Imagen del producto:</label>
                                <input type="file" name="image" class="form-control" id="file">
                                <input type="hidden" name="image_text" value="<?php echo $imagen; ?>">
                                <output id="list">
                                    <img src="<?php echo $URL . "/palmacen/img_productos/" . $imagen; ?>" class="img-fluid mt-2" alt="Imagen actual">
                                </output>
                            </div>
                        </div>

                        <hr>
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">Guardar producto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>

<script>
    // --- Función para calcular el precio minorista normal ---
    function calcularPrecioMinorista() {
        const costo = parseFloat(document.getElementById('costo_minorista').value) || 0;
        const porcentaje = parseFloat(document.getElementById('porcentaje_minorista').value) || 0;
        const moneda = document.getElementById('moneda').value;
        const cambio = parseFloat(document.getElementById('cambio').value) || 1;

        let ganancia = costo * porcentaje / 100;
        let precio = costo + ganancia;

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
            cambioInput.value = 1;
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
        calcularPrecioMinorista(); // 🔥 ESTO FALTABA
    });
</script>

