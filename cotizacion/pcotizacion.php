<?php
require_once('../app/TCPDF-main/tcpdf.php');
include('../app/config.php');

// Obtener datos desde la URL
$nro_venta = $_GET['nro_venta'];

// Consultar los datos de la venta
$sentencia = $pdo->prepare("SELECT * FROM tb_venta WHERE nro_venta = :nro_venta");
$sentencia->execute(['nro_venta' => $nro_venta]);
$venta = $sentencia->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    die("No se encontró la venta.");
}

$fecha_emi = date("d/m/Y", strtotime($venta['fecha_emi']));
$sub_total = $venta['sub_total'];
$igv = $venta['igv'];
$precio_final = $venta['precio_final'];
$id_cliente = $venta['id_cliente'];
$envio = $venta['envio'];

// Consultar datos del cliente
$consulta_cliente = $pdo->prepare("SELECT * FROM tb_clientes WHERE id_cliente = :id_cliente");
$consulta_cliente->execute(['id_cliente' => $id_cliente]);
$cliente = $consulta_cliente->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    die("No se encontró el cliente.");
}

$razon_social = $cliente['razon_social'];
$ruc = $cliente['ruc'];
$contacto1 = $cliente['contacto1'];

$sentencia = $pdo->prepare("
    SELECT 
        c.cantidad,
        c.precio_final,
        (c.cantidad * c.precio_final) AS total,
        p.codigo,
        p.descripcion,
        m.nombre AS marca
    FROM tb_carrito c
    INNER JOIN tb_almacen p ON c.id_producto = p.id_producto
    INNER JOIN tb_marca m ON p.id_marca = m.id_marca
    WHERE c.nro_venta = :nro_venta
");
$sentencia->bindParam(':nro_venta', $nro_venta);
$sentencia->execute();
$productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF



$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(210,297), true, 'UTF-8', false);
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Sistema Sercosnlimp');
$pdf->setTitle("CotizacionSD_Nro $nro_venta - $razon_social");
$pdf->setSubject("CotizacionSD_Nro $nro_venta - $razon_social");
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

$pdf->setPrintHeader(false);
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->setMargins(5, 5, 5);
$pdf->SetAutoPageBreak(true, 4);
$pdf->setPrintFooter(false);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->setFont('Helvetica', '', 8);
$pdf->AddPage();

// Contenido HTML
$html = '
<!-- ================= HEADER PRO ================= -->
<table width="100%" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
<tr>

<!-- LOGO -->
<td width="20%">
  <img src="principal.png" style="height:70px;">
</td>

<!-- INFO EMPRESA -->
<!-- INFO EMPRESA COMPACTA -->
<td width="55%"  align="center">
  <table cellpadding="0" cellspacing="0" style="line-height:1.05;">
    
    <tr>
      <td style="font-size:15px; font-weight:bold; padding:0;">
       EMPRESA EIRL
      </td>
    </tr>

    <tr>
      <td style="font-size:10px; color:#2e7d32; font-style:italic; padding:0;">
        En expansión con proveedores nacionales e internacionales
      </td>
    </tr>

    <tr>
      <td style="font-size:9.5px; padding:0;">
        RUC: 0000000000000
      </td>
    </tr>

    <tr>
      <td style="font-size:9.5px; padding:0;">
       Dirección: LAMBAYEQUE - CHICLAYO
      </td>
    </tr>

    <tr>
      <td style="font-size:9.5px; padding:0;">
      Telefono: 00000000000 | Correo: xxxxxxx@gmail.com
      </td>
    </tr>

  </table>
</td>




<!-- CAJA DOCUMENTO -->
<td width="25%" align="right">
  <table cellpadding="2" cellspacing="0" style="border:1px solid #78bf30; font-size:10px;">
    <tr>
      <td align="center" style="font-weight:bold; background-color:#78bf30; color:white;">
        COTIZACIÓN
      </td>
    </tr>
    <tr>
    <td align="center" style="font-size:11px; font-weight:bold;">
  N° 100' . $nro_venta . '
</td>
    </tr>
    <tr>
      <td align="center">Fecha de Emision: ' . $fecha_emi . '</td>
    </tr>
  </table>
</td>

</tr>
<br>
<!-- ===== RESPALDO DE MARCAS ===== -->
<table width="100%" cellpadding="3" cellspacing="0"
       style="margin-top:10px; border-collapse:collapse; background-color:#ffffff;">

  <tr>
    <!-- TEXTO LATERAL -->
    <td width="22%" valign="middle"
        style="font-size:9px; font-weight:bold; color:#333;">
      REPRESENTANTES DE MARCAS
    </td>

    <!-- LOGOS -->
    <td width="78%" align="left" valign="middle">
      <img src="22.png" height="21" style="margin-left:6px;">
      <img src="1.png"  height="21" style="margin-left:6px;">
      <img src="6.png"  height="21" style="margin-left:6px;">
      <img src="5.png"  height="21" style="margin-left:6px;">
      <img src="4.png"  height="21" style="margin-left:6px;">
      <img src="3.png"  height="21" style="margin-left:6px;">
      <img src="777.png" height="21" style="margin-left:6px;">
      <img src="88.png" height="21" style="margin-left:6px;">
      <img src="49.png" height="21" style="margin-left:6px;">
      <img src="96.png" height="21" style="margin-left:6px;">
    </td>
  </tr>

  <!-- LÍNEA SUTIL -->
  <tr>
    <td colspan="2" style="border-top:0.6px solid #cfd8dc;"></td>
  </tr>

</table>



<table border="0" width="98%" style="margin-bottom: 18px; font-size: 10px; border-collapse: collapse;">
 
  <tr>
    <td colspan="2" style="padding: 0; line-height: 1.3;"><b>RUC: </b>  ' . $ruc . '</td>
  </tr>
  <tr>
    <td colspan="2" style="padding: 0; line-height: 1.3;"><b>CLIENTE: </b>' . $razon_social . '</td>
  </tr>
  <tr>
    <td colspan="2" style="padding: 0; line-height: 1.3;"><b>CONTACTO: </b>' . $contacto1 . '</td>
  </tr>
</table>


<p style="font-size: 12px; font-weight: bold;"><strong>LE PRESENTAMOS NUESTRA OFERTA:</strong></p>


<table border="0" cellspacing="0" cellpadding="5" width="100%"
style="font-size:10px; border-collapse:collapse; font-family:Helvetica, Arial, sans-serif;">

<thead>
<tr style="background-color:#78bf30; color:#ffffff; font-weight:bold; text-align:center;">
    <th style="width:5%; border:0.6px solid #78bf30;">ITEM</th>
    <th style="width:13%; border:0.6px solid #78bf30;">CÓDIGO</th>
    <th style="width:8%; border:0.6px solid #78bf30;">CANT.</th>
    <th style="width:14%; border:0.6px solid #78bf30;">MARCA</th>
    <th style="width:37%; border:0.6px solid #78bf30; text-align:left;">DESCRIPCIÓN</th>
    <th style="width:11%; border:0.6px solid #78bf30;">V. UNIT</th>
    <th style="width:12%; border:0.6px solid #78bf30;">TOTAL</th>
</tr>
</thead>

<tbody>
';

$item = 1;
foreach ($productos as $producto) {
    $cantidad = $producto["cantidad"];
    $codigo = $producto["codigo"];
    $marca = $producto["marca"];
    $descripcion = $producto["descripcion"];
    $precio = number_format($producto["precio_final"], 2);
    $total = number_format($producto["total"], 2);

    $html .= '
<tr style="background-color:' . (($item % 2 == 0) ? '#f8fbf6' : '#ffffff') . ';">
  <td align="center" style="width:5%; border:0.5px solid #d9e2d6;">' . $item . '</td>
  <td align="center" style="width:13%; border:0.5px solid #d9e2d6;">' . $codigo . '</td>
  <td align="center" style="width:8%; border:0.5px solid #d9e2d6; font-weight:bold;">' . $cantidad . '</td>
  <td align="center" style="width:14%; border:0.5px solid #d9e2d6;">' . $marca . '</td>
  <td align="left" style="width:37%; border:0.5px solid #d9e2d6;">' . $descripcion . '</td>
  <td align="right" style="width:11%; border:0.5px solid #d9e2d6;">S/ ' . $precio . '</td>
  <td align="right" style="width:12%; border:0.5px solid #d9e2d6; font-weight:bold;">S/ ' . $total . '</td>
</tr>';
    $item++;
}


$html .= '</tbody>
</table>

<br><br>

<table width="100%" border="0" cellspacing="0" cellpadding="4" style="border-collapse:collapse; font-family:Helvetica, Arial, sans-serif;">
<tr>

<td width="70%" valign="top">
  <table width="96%" cellpadding="5" cellspacing="0" style="border-collapse:collapse; font-size:9.6px;">
    <tr>
      <td style="color:#2e7d32; font-weight:bold; border-bottom:0.7px solid #78bf30;">
        CONDICIONES DE VENTA
      </td>
    </tr>
    <tr>
      <td style="color:#333; line-height:1.38; padding-top:6px;">
        • Las entregas se realizan como máximo en un primer piso en la dirección indicada por el cliente.<br>
        • Despacho entre 2 y 4 días hábiles, previo depósito.<br>
        • Los despachos se coordinan con 1 día de anticipación.<br>
        • El stock no estará comprometido hasta efectuado el depósito.
      </td>
    </tr>
  </table>
</td>

<td width="30%" valign="top">
  <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse:collapse; font-size:10px; border:0.5px solid #dfe5dc;">
    <tr>
      <td width="50%" style="font-weight:bold; border-bottom:0.5px solid #d9e2d6;">SUBTOTAL</td>
      <td width="50%" align="right" style="border-bottom:0.5px solid #d9e2d6;">S/ ' . number_format($sub_total, 2) . '</td>
    </tr>
    <tr>
      <td style="font-weight:bold; border-bottom:0.5px solid #d9e2d6;">IGV</td>
      <td align="right" style="border-bottom:0.5px solid #d9e2d6;">S/ ' . number_format($igv, 2) . '</td>
    </tr>
    <tr>
      <td style="font-weight:bold; background-color:#78bf30; color:#ffffff;">TOTAL</td>
      <td align="right" style="font-weight:bold; background-color:#78bf30; color:#ffffff;">S/ ' . number_format($precio_final, 2) . '</td>
    </tr>
  </table>
</td>

</tr>
</table>
';

// Renderizar PDF
$pdf->writeHTML($html, true, false, true, false, '');
// Ir a la última página
$pdf->lastPage();

// Posicionar footer
$pdf->SetY(-35);

// Dibujar footer
$pdf->writeHTML($footer_html, true, false, true, false, '');
$footer_html = '<!-- ================= FOOTER BANCARIO COMPACTO ================= -->
<!-- ================= FOOTER BANCARIO CORPORATIVO ================= -->

<table
    width="100%"
    cellpadding="0"
    cellspacing="0"
    style="
        width:100%;
        font-family:Arial, Helvetica, sans-serif;
        border-collapse:collapse;
        background:#ffffff;
        border-top:1.4px solid #78bf30;
        border-bottom:0.7px solid #d8d8d8;
    "
>

    <!-- TÍTULO -->
    <tr>
        <td
            colspan="5"
            align="left"
            style="
                padding:2px 6px;
                font-size:8px;
                font-weight:bold;
                color:#3d3d3d;
                border-bottom:0.7px solid #e6e6e6;
            "
        >
           
        </td>
    </tr>

    <tr>

        <!-- ================= BCP ================= -->
        <td
            width="20%"
            align="center"
            valign="top"
            style="
                padding:3px 3px;
                border-right:0.6px solid #ededed;
            "
        >

            <img
                src="bcp.JPG"
                height="25"
                style="display:block;margin:0 auto 2px auto;"
            >

            <div
                style="
                    font-size:7.2px;
                    color:#7b7b7b;
                    line-height:1.05;
                    margin-bottom:2px;
                "
            >
                Cuenta Corriente
            </div>

            <div
                style="
                    line-height:1.25;
                    color:#111111;
                "
            >
                <span style="font-size:9px;">
                    <b>CTA:</b> <b>000000000</b>
                </span>
                <br>

                <span style="font-size:9px;">
                    <b>CCI:</b> <b>000000000</b>
                </span>
            </div>

        </td>

        <!-- ================= SCOTIABANK ================= -->
        <td
            width="20%"
            align="center"
            valign="top"
            style="
                padding:3px 3px;
                border-right:0.6px solid #ededed;
            "
        >

            <img
                src="oo.png"
                height="25"
                style="display:block;margin:0 auto 2px auto;"
            >

            <div
                style="
                    font-size:7.2px;
                    color:#7b7b7b;
                    line-height:1.05;
                    margin-bottom:2px;
                "
            >
                Cuenta Corriente
            </div>

            <div
                style="
                    line-height:1.25;
                    color:#111111;
                "
            >
                <span style="font-size:9px;">
                    <b>CTA:</b> <b>0000000000000</b>
                </span>
                <br>

                <span style="font-size:9px;">
                    <b>CCI:</b> <b>0000000000000</b>
                </span>
            </div>

        </td>

        <!-- ================= BBVA ================= -->
        <td
            width="20%"
            align="center"
            valign="top"
            style="
                padding:3px 3px;
                border-right:0.6px solid #ededed;
            "
        >

            <img
                src="300.png"
                height="25"
                style="display:block;margin:0 auto 2px auto;"
            >

            <div
                style="
                    font-size:7px;
                    color:#7b7b7b;
                    line-height:1.05;
                    margin-bottom:2px;
                "
            >
                Código de recaudo:
                <b style="color:#444;">0000</b>
            </div>

            <div
                style="
                    line-height:1.25;
                    color:#111111;
                "
            >
                <span style="font-size:9px;">
                    <b>CTA:</b> <b>000000</b>
                </span>
                <br>

                <span style="font-size:9px;">
                    <b>CCI:</b> <b>0000000</b>
                </span>
            </div>

        </td>

        <!-- ================= BANCO DE LA NACIÓN ================= -->
        <td
            width="20%"
            align="center"
            valign="top"
            style="
                padding:3px 3px;
                border-right:0.6px solid #ededed;
            "
        >

            <img
                src="BN.png"
                height="25"
                style="display:block;margin:0 auto 2px auto;"
            >

            <div
                style="
                    font-size:7.2px;
                    color:#7b7b7b;
                    line-height:1.05;
                    margin-bottom:2px;
                "
            >
                Cuenta de Detracción
            </div>

            <div
                style="
                    line-height:1.25;
                    color:#111111;
                "
            >
                <span style="font-size:9px;">
                    <b>CTA:</b> <b>00000000</b>
                </span>
            </div>

        </td>

        <!-- ================= INTERBANK ================= -->
        <td
            width="20%"
            align="center"
            valign="top"
            style="
                padding:3px 3px;
            "
        >

            <img
                src="ik.jpg"
                height="25"
                style="display:block;margin:0 auto 2px auto;"
            >

            <div
                style="
                    font-size:7.2px;
                    color:#7b7b7b;
                    line-height:1.05;
                    margin-bottom:2px;
                "
            >
                Cuenta Corriente
            </div>

            <div
                style="
                    line-height:1.25;
                    color:#111111;
                "
            >
                <span style="font-size:9px;">
                    <b>CTA:</b> <b>000000000</b>
                </span>
                <br>

                <span style="font-size:9px;">
                    <b>CCI:</b> <b>000000000</b>
                </span>
            </div>

        </td>

    </tr>

</table>';




// Dibujar footer
$pdf->writeHTML($footer_html, true, false, true, false, '');





$nombre_archivo = preg_replace('/[^A-Za-z0-9]/', '_', $razon_social) . '_CotizacionSD_Nro_' . $nro_venta . '.pdf';
$pdf->Output($nombre_archivo, 'I');
