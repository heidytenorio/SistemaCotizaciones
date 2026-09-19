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
    $costo_mayorista = $producto['costo_mayorista'];
    $porcentaje_mayorista = $producto['porcentaje_mayorista'];
    $precio_mayorista = $producto['precio_mayorista'];
    $competencia = $producto['competencia'];
    $imagen = $producto['imagen'];
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
            <h1 class="m-0">Eliminar Producto</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">¿Esta seguro de eliminar el producto?</h3>
                </div>

                <div class="card-body">
                    <form action="../app/controllers/palmacen/delete.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_producto" value="<?php echo $id_producto_get; ?>">

                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <!-- Código -->
                                    <div class="col-md-4">
                                        <label>Código:</label>
                                        <input type="text" class="form-control" value="<?php echo $codigo; ?>" disabled>
                                        <input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
                                    </div>

                                    <!-- Descripción -->
                                    <div class="col-md-4">
                                        <label>Descripción del producto:</label>
                                        <textarea name="descripcion" class="form-control" rows="2" disabled><?php echo $descripcion; ?></textarea>
                                    </div>

                                    <!-- Competencia -->
                                    <div class="col-md-4">
                                        <label>Competencia:</label>
                                        <textarea name="competencia" class="form-control" rows="2" disabled><?php echo $competencia; ?></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Categoría -->
                                    <div class="col-md-6">
                                        <label>Categoría:</label>
                                        <select name="id_categoria" class="form-control" disabled>
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
                                        <select name="id_marca" class="form-control" disabled>
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
                                    <div class="col-md-2">
                                        <label>Costo X Menor:</label>
                                        <input type="number" step="0.01" name="costo_minorista" id="costo_minorista" class="form-control" value="<?php echo $costo_minorista; ?>" oninput="calcularPrecioMinorista()" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label>% X Menor:</label>
                                        <input type="number" step="0.01" name="porcentaje_minorista" id="porcentaje_minorista" class="form-control" value="<?php echo $porcentaje_minorista; ?>" oninput="calcularPrecioMinorista()" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Precio X Menor:</label>
                                        <input type="number" step="0.01" name="precio_minorista" id="precio_minorista" class="form-control" value="<?php echo $precio_minorista; ?>" readonly  disabled>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Costo X Mayor:</label>
                                        <input type="number" step="0.01" name="costo_mayorista" id="costo_mayorista" class="form-control" value="<?php echo $costo_mayorista; ?>" oninput="calcularPrecioMayorista()" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label>% X Mayor:</label>
                                        <input type="number" step="0.01" name="porcentaje_mayorista" id="porcentaje_mayorista" class="form-control" value="<?php echo $porcentaje_mayorista; ?>" oninput="calcularPrecioMayorista()" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Precio X Mayor:</label>
                                        <input type="number" step="0.01" name="precio_mayorista" id="precio_mayorista" class="form-control" value="<?php echo $precio_mayorista; ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Imagen -->
                            <div class="col-md-3">
                                <label>Imagen del producto:</label>
                                <input type="file" name="image" class="form-control" id="file" disabled>
                                <input type="hidden" name="image_text" value="<?php echo $imagen; ?>">
                                <output id="list">
                                    <img src="<?php echo $URL . "/palmacen/img_productos/" . $imagen; ?>" class="img-fluid mt-2" alt="Imagen actual">
                                </output>
                            </div>

                        </div>

                        <hr>
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>

<script>
    function calcularPrecioMinorista() {
        const costo = parseFloat(document.getElementById('costo_minorista').value) || 0;
        const porcentaje = parseFloat(document.getElementById('porcentaje_minorista').value) || 0;
        const precio = costo + (costo * porcentaje / 100);
        document.getElementById('precio_minorista').value = precio.toFixed(2);
    }

    function calcularPrecioMayorista() {
        const costo = parseFloat(document.getElementById('costo_mayorista').value) || 0;
        const porcentaje = parseFloat(document.getElementById('porcentaje_mayorista').value) || 0;
        const precio = costo + (costo * porcentaje / 100);
        document.getElementById('precio_mayorista').value = precio.toFixed(2);
    }

    document.getElementById('file').addEventListener('change', function(evt) {
        const files = evt.target.files;
        for (let i = 0, f; f = files[i]; i++) {
            if (!f.type.match('image.*')) continue;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('list').innerHTML = `<img class="img-fluid rounded border" src="${e.target.result}">`;
            };
            reader.readAsDataURL(f);
        }
    });
</script>

