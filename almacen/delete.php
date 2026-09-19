<?php
include('../app/config.php');
include('../layout/sesion.php');
include('../layout/parte1.php');
include('../app/controllers/marcas/listado_de_marcas.php');
include('../app/controllers/proveedores/listado_de_proveedores.php');
include('../app/controllers/categorias/listado_de_categorias.php');

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
    $id_proveedor = $producto['id_proveedor'];
    $id_categoria = $producto['id_categoria'];
    $costo_mayorista = $producto['costo_mayorista'];
    $porcentaje_mayorista = $producto['porcentaje_mayorista'];
    $precio_mayorista = $producto['precio_mayorista'];
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
                    <h3 class="card-title">¿Está seguro de eliminar el producto?</h3>
                </div>

                <div class="card-body">
                    <form action="../app/controllers/almacen/delete.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_producto" value="<?php echo $id_producto_get; ?>">

                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Código:</label>
                                <input type="text" name="codigo" class="form-control" value="<?php echo $codigo; ?>" disabled>
                            </div>
                            <div class="col-md-5">
                                <label for="">Descripción del producto:</label>
                                <textarea name="descripcion" cols="20" rows="2" class="form-control" disabled><?php echo $descripcion; ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="">Categoría:</label>
                                <div class="d-flex">
                                    <select name="id_categoria" class="form-control" disabled>
                                        <?php foreach ($categorias_datos as $categorias_dato): ?>
                                            <option value="<?php echo $categorias_dato['id_categoria']; ?>"
                                                <?php if ($categorias_dato['id_categoria'] == $id_categoria) echo "selected"; ?>>
                                                <?php echo $categorias_dato['nombre_categoria']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="<?php echo $URL; ?>/categorias" class="btn btn-primary ml-1"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Proveedor -->
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="">Proveedores:</label>
                                <div class="d-flex">
                                    <select name="id_proveedor" class="form-control" disabled>
                                        <?php foreach ($proveedor_datos as $proveedor_dato): ?>
                                            <option value="<?php echo $proveedor_dato['id_proveedor']; ?>"
                                                <?php if ($proveedor_dato['id_proveedor'] == $id_proveedor) echo "selected"; ?>>
                                                <?php echo $proveedor_dato['razon_social']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="<?php echo $URL; ?>/proveedores" class="btn btn-primary ml-1"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>

                            <!-- Marca -->
                            <div class="col-md-6">
                                <label for="">Marca:</label>
                                <div class="d-flex">
                                    <select name="id_marca" class="form-control" disabled>
                                        <?php foreach ($marcas_datos as $marcas_dato): ?>
                                            <option value="<?php echo $marcas_dato['id_marca']; ?>"
                                                <?php if ($marcas_dato['id_marca'] == $id_marca) echo "selected"; ?>>
                                                <?php echo $marcas_dato['nombre']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <a href="<?php echo $URL; ?>/marcas" class="btn btn-primary ml-1"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Costos y Imagen -->
                        <div class="row mt-2">
                            <!-- Costo X Mayor -->
                            <div class="col-md-3">
                                <label for="costo_mayorista" class="form-label">Costo X Mayor:</label>
                                <input type="text" class="form-control" name="costo_mayorista" value="<?php echo $costo_mayorista; ?>">
                            </div>

                            <!-- % X Mayor -->
                            <div class="col-md-3">
                                <label for="porcentaje_mayorista" class="form-label">% X Mayor:</label>
                                <input type="text" class="form-control" name="porcentaje_mayorista" value="<?php echo $porcentaje_mayorista; ?>">
                            </div>

                            <!-- Precio X Mayor -->
                            <div class="col-md-3">
                                <label for="precio_mayorista" class="form-label">Precio X Mayor:</label>
                                <input type="text" class="form-control" name="precio_mayorista" value="<?php echo $precio_mayorista; ?>">
                            </div>

                            <!-- Imagen del producto -->
                            <div class="col-md-3">
                                <label for="image" class="form-label">Imagen del producto:</label>
                                <input type="file" class="form-control" name="image" id="image">
                                <br>
                                <img src="<?php echo $URL."/almacen/img_productos/".$imagen; ?>"
                                     alt="Imagen del producto"
                                     width="120" height="120">
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


