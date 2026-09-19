<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/marcas/listado_de_marcas.php');
include('../app/controllers/categorias/listado_de_categorias.php');
include('../app/controllers/proveedores/listado_de_proveedores.php');

// Verificamos si existe el id_producto por GET
if (isset($_GET['id'])) {
    $id_producto_get = $_GET['id'];

    // Cargar datos del producto
    $sql = "SELECT * FROM tb_almacen WHERE id_producto = :id_producto";
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
    $id_proveedor = $producto['id_proveedor'];
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
                    <form action="../app/controllers/almacen/update.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_producto" value="<?php echo $id_producto_get; ?>">

                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <!-- Código -->
                                    <div class="col-md-3">
                                                <label for="">Código:</label>
                                                <input type="text" name="codigo" class="form-control" value="<?php echo $codigo; ?>">
                                            </div>
                                            <div class="col-md-5">
                                                <label for="">Descripción del producto:</label>
                                                <textarea name="descripcion" class="form-control" rows="2"><?php echo $descripcion; ?></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="">Categoría:</label>
                                                <div class="d-flex">
                                                    <select name="id_categoria" class="form-control">
                                                        <?php foreach ($categorias_datos as $categorias_dato): ?>
                                                            <option value="<?php echo $categorias_dato['id_categoria']; ?>"
                                                                <?php if ($categorias_dato['id_categoria'] == $id_categoria) echo 'selected'; ?>>
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
                                                    <select name="id_proveedor" class="form-control">
                                                        <?php foreach ($proveedor_datos as $proveedor_dato): ?>
                                                            <option value="<?php echo $proveedor_dato['id_proveedor']; ?>"
                                                                <?php if ($proveedor_dato['id_proveedor'] == $id_proveedor) echo 'selected'; ?>>
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
                                                    <select name="id_marca" class="form-control">
                                                        <?php foreach ($marcas_datos as $marcas_dato): ?>
                                                            <option value="<?php echo $marcas_dato['id_marca']; ?>"
                                                                <?php if ($marcas_dato['id_marca'] == $id_marca) echo 'selected'; ?>>
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
                                            <div class="col-md-2">
                                                <label>Costo X Mayor:</label>
                                                <input
                                                        type="number"
                                                        step="0.01"
                                                        name="costo_mayorista"
                                                        id="costo_mayorista"
                                                        class="form-control"
                                                        value="<?php echo $costo_mayorista; ?>"
                                                        oninput="<?php echo ($rol_sesion === 'Administrador') ? 'calcularPrecioMayorista()' : ''; ?>"
                                                    <?php echo ($rol_sesion !== 'Administrador') ? 'readonly' : ''; ?>
                                                >
                                            </div>

                                            <div class="col-md-2">
                                                <label>% X Mayor:</label>
                                                <input type="number" step="0.01" name="porcentaje_mayorista" id="porcentaje_mayorista" class="form-control" value="<?php echo $porcentaje_mayorista; ?>" oninput="calcularPrecioMayorista()">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Precio X Mayor:</label>
                                                <input type="number" step="0.01" name="precio_mayorista" id="precio_mayorista" class="form-control" value="<?php echo $precio_mayorista; ?>" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="">Info Adicional:</label>
                                                <textarea name="info" class="form-control" rows="2"><?php echo $info; ?></textarea>
                                            </div>
                                    </div>
                                </div>

                            <!-- Imagen -->
                            <div class="col-md-3">
                                <label>Imagen del producto:</label>
                                <input type="file" name="image" class="form-control" id="file">
                                <input type="hidden" name="image_text" value="<?php echo $imagen; ?>">
                                <output id="list">
                                    <img src="<?php echo $URL . "/almacen/img_pproductos/" . $imagen; ?>" class="img-fluid mt-2" alt="Imagen actual">
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
