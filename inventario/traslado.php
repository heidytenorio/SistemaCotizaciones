<?php
include('../app/config.php');
include('../layout/sesion.php');

$fecha_hoy = date('Y-m-d');

/* ==========================
   PRODUCTOS
========================== */
$sql = "
SELECT
    i.id_producto,
    i.codigo,
    i.descripcion,
    i.tipo_liquido
FROM tb_inventario i
ORDER BY i.descripcion
";
$q = $pdo->prepare($sql);
$q->execute();
$productos = $q->fetchAll(PDO::FETCH_ASSOC);

/* ==========================
   ALMACENES
========================== */
$sqlAlm = "
SELECT id_almacen, ciudad, nombre
FROM tb_almacenn
WHERE estado = 1
ORDER BY ciudad
";
$qAlm = $pdo->prepare($sqlAlm);
$qAlm->execute();
$almacenes = $qAlm->fetchAll(PDO::FETCH_ASSOC);


include('../layout/parte1.php');
?>

<div class="content-wrapper">
    <section class="content-header mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <h1 class="mb-0">
                <i class="fas fa-exchange-alt text-warning mr-2"></i>
                <span class="font-weight-bold">Registro de Traslado</span>
            </h1>
            <span class="badge badge-warning px-3 py-2">
            Movimiento interno
        </span>
        </div>
    </section>


    <section class="content">
        <div class="card shadow-sm border-0">

        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <strong class="text-primary">
                    <i class="fas fa-boxes mr-1"></i> Detalle de productos
                </strong>
                <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modal-productos">
                    <i class="fa fa-search mr-1"></i> Buscar producto
                </button>
            </div>

            <div class="card-body">

                <!-- ==========================
                     TABLA DETALLE
                ========================== -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light text-center">
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Control</th>
                            <th>Color</th>
                            <th>Cantidad</th>
                            <th>Acción</th>
                        </tr>
                        </thead>
                        <tbody id="detalle"></tbody>
                    </table>
                </div>

                <hr>

                <!-- ==========================
                     CABECERA
                ========================== -->
                <div class="row">
                    <div class="col-md-4">
                        <label>Almacén Origen</label>
                        <select id="almacen_origen" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($almacenes as $a): ?>
                                <option value="<?= $a['id_almacen'] ?>">
                                    <?= $a['ciudad'].' - '.$a['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Almacén Destino</label>
                        <select id="almacen_destino" class="form-control">
                            <option value="">Seleccione...</option>
                            <?php foreach ($almacenes as $a): ?>
                                <option value="<?= $a['id_almacen'] ?>">
                                    <?= $a['ciudad'].' - '.$a['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Fecha</label>
                        <input type="date" id="fecha_traslado" class="form-control" value="<?= $fecha_hoy ?>" readonly>
                    </div>
                    <div class="col-md-12 mt-3">
                        <label>Observaciones</label>
                        <textarea id="observaciones" class="form-control" rows="3" placeholder="Escribe aquí alguna observación..."></textarea>
                    </div>

                </div>

                <hr>

                <div class="text-center">
                    <button id="btn_guardar" class="btn btn-success px-5">
                        <i class="fa fa-save"></i> Guardar traslado
                    </button>
                    <button id="btn_cancelar" class="btn btn-secondary px-5 ml-2">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                </div>

            </div>
        </div>
    </section>
</div>

<!-- ==========================
     MODAL PRODUCTOS
========================== -->
<div class="modal fade" id="modal-productos">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h4>Seleccionar producto</h4>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">



                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="tabla-productos">
                        <thead>

                        <tr>
                            <th>Agregar</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Control</th>
                        </tr>
                        </thead>
                        <tbody id="body-productos">
                        <?php foreach ($productos as $p): ?>
                            <tr class="fila-producto">
                                <td class="text-center">
                                    <button class="btn btn-info btn-sm btn-agregar"
                                            data-id="<?= $p['id_producto'] ?>"
                                            data-codigo="<?= $p['codigo'] ?>"
                                            data-desc="<?= htmlspecialchars($p['descripcion'], ENT_QUOTES) ?>"
                                            data-tipo="<?= $p['tipo_liquido'] ?>">
                                        +
                                    </button>
                                </td>
                                <td><?= $p['codigo'] ?></td>
                                <td><?= htmlspecialchars($p['descripcion']) ?></td>
                                <td class="text-center">
                                    <?= ($p['tipo_liquido']==='GRANEL')?'VOLUMEN':'UNIDAD' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>


        </div>
    </div>
</div>

<?php include('../layout/parte2.php'); ?>

<script>
    let detalle = [];

    /* ==========================
       AGREGAR PRODUCTO
    ========================== */
    $(document).on('click','.btn-agregar',function(){

        const id = $(this).data('id');

        detalle.push({
            id_producto: id,
            codigo: $(this).data('codigo'),
            descripcion: $(this).data('desc'),
            tipo_liquido: $(this).data('tipo'),
            color: null,
            cantidad: 1,
            cantidad_real: 1,
            factor_conversion: 1
        });

        $('#modal-productos').modal('hide');
        render();
    });



    /* ==========================
       RENDER
    ========================== */
    function render(){
        let html='';
        detalle.forEach((d,i)=>{
            html+=`
        <tr>
            <td>${d.codigo}</td>
            <td>${d.descripcion}</td>
            <td class="text-center">${d.tipo_liquido==='GRANEL'?'VOLUMEN':'UNIDAD'}</td>
      <td>
    ${
                d.tipo_liquido === 'GRANEL'
                    ? '<span class="badge badge-secondary">GRANEL</span>'
                    : `<select class="form-control form-control-sm"
              onchange="detalle[${i}].color=this.value">

                    <option value="">Seleccione</option>
                    <option value="estandar" ${d.color==='estandar'?'selected':''}>Estándar</option>
                    <option value="rojo" ${d.color==='rojo'?'selected':''}>Rojo</option>
                    <option value="azul" ${d.color==='azul'?'selected':''}>Azul</option>
                    <option value="azul noche" ${d.color==='azul noche'?'selected':''}>Azul Noche</option>
                    <option value="azul claro" ${d.color==='azul claro'?'selected':''}>Azul Claro</option>
                    <option value="verde" ${d.color==='verde'?'selected':''}>Verde</option>
                    <option value="verde claro" ${d.color==='verde claro'?'selected':''}>Verde Claro</option>
                    <option value="negro" ${d.color==='negro'?'selected':''}>Negro</option>
                    <option value="blanco" ${d.color==='blanco'?'selected':''}>Blanco</option>
                    <option value="amarillo" ${d.color==='amarillo'?'selected':''}>Amarillo</option>
                    <option value="gris" ${d.color==='gris'?'selected':''}>Gris</option>
                    <option value="celeste" ${d.color==='celeste'?'selected':''}>Celeste</option>
                    <option value="coral" ${d.color==='coral'?'selected':''}>Coral</option>
                    <option value="rosado" ${d.color==='rosado'?'selected':''}>Rosado</option>
                    <option value="rosado barbie" ${d.color==='rosado barbie'?'selected':''}>Rosado Barbie</option>
                    <option value="rosado claro" ${d.color==='rosado claro'?'selected':''}>Rosado Claro</option>
                    <option value="lila" ${d.color==='lila'?'selected':''}>Lila</option>
                    <option value="transparente" ${d.color==='transparente'?'selected':''}>Transparente</option>
                    <option value="tricolor" ${d.color==='tricolor'?'selected':''}>Tricolor</option>
                </select>`
            }
</td>

            <td>
                <input type="number" min="0.01" step="0.01"
                       class="form-control form-control-sm text-center"
                       value="${d.cantidad}"
                       onchange="
detalle[${i}].cantidad = parseFloat(this.value) || 0;
detalle[${i}].cantidad_real = detalle[${i}].cantidad * detalle[${i}].factor_conversion;
"

            </td>
            <td class="text-center">
                <button class="btn btn-danger btn-sm" onclick="eliminar(${i})">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`;
        });
        $('#detalle').html(html);
    }


    function eliminar(i){
        detalle.splice(i,1);
        render();
    }

    /* ==========================
       GUARDAR
    ========================== */
    $('#btn_guardar').click(function(){
        for (let i = 0; i < detalle.length; i++) {
            detalle[i].cantidad_real =
                parseFloat(detalle[i].cantidad) * parseFloat(detalle[i].factor_conversion);
        }


        if(!$('#almacen_origen').val() || !$('#almacen_destino').val()){
            alert('Seleccione almacenes');
            return;
        }
        for (let i = 0; i < detalle.length; i++) {
            if (detalle[i].tipo_liquido !== 'GRANEL' && !detalle[i].color) {
                alert('Seleccione color en todos los productos');
                return;
            }
            if (detalle[i].cantidad_real <= 0) {
                alert('Cantidad inválida');
                return;
            }
        }


        if($('#almacen_origen').val()===$('#almacen_destino').val()){
            alert('Origen y destino no pueden ser iguales');
            return;
        }

        if(detalle.length===0){
            alert('Agregue productos');
            return;
        }

        let fd=new FormData();
        fd.append('detalle',JSON.stringify(detalle));
        fd.append('almacen_origen',$('#almacen_origen').val());
        fd.append('almacen_destino',$('#almacen_destino').val());
        fd.append('fecha_traslado', $('#fecha_traslado').val());
        fd.append('observaciones', $('#observaciones').val().trim());


        $.ajax({
            url:'<?= $URL ?>/app/controllers/inventario/guardar_traslado.php',
            type:'POST',
            data:fd,
            processData:false,
            contentType:false,
            success:function(r){
                if(r.trim()==='OK'){
                    alert('Traslado registrado');
                    location.href='<?= $URL ?>/inventario/gestion.php';
                }else{
                    alert(r);
                }
            }
        });
    });

    $('#btn_cancelar').click(()=>{
        if(confirm('¿Cancelar?')) location.href='<?= $URL ?>/inventario/gestion.php';
    });


    $(document).ready(function () {

        $('#tabla-productos').DataTable({
            "pageLength": 10,
            "lengthMenu": [5,10,25,50],
            "ordering": true,
            "language": {
                "search": "Buscar:",
                "lengthMenu": "Mostrar _MENU_ registros",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "paginate": {
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "zeroRecords": "No se encontraron registros"
            }
        });

        // Ajuste al abrir modal
        $('#modal-productos').on('shown.bs.modal', function () {
            $('#tabla-productos').DataTable().columns.adjust().draw();
        });

    });

</script>

