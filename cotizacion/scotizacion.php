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
$direccion = $cliente['direccion'];
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
$pdf->setAuthor('Sistema ');
// Título y asunto con número de venta y cliente
$pdf->setTitle("Cotizacion_Nro $nro_venta - $razon_social");
$pdf->setSubject("Cotizacion_Nro $nro_venta - $razon_social");
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->setMargins(5, 5, 5);
$pdf->setAutoPageBreak(true, 5);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->setFont('helvetica', '', 8);
$pdf->AddPage();

// create some HTML content
$html = '
<table width="100%" cellpadding="0" cellspacing="0" style="font-family:helvetica,arial,sans-serif; font-size:10px;">
  <tr>
    <!-- LOGO a la izquierda -->
    <td width="40%" valign="middle">
      <img src="nuevo2026.png" style="width:240px;">
    </td>

    <!-- EMPRESA + DATOS a la derecha -->
    <td width="60%" valign="top">
      <!-- NOMBRE DE LA EMPRESA CENTRADO -->
      <div style="font-size:16px; font-weight:bold; letter-spacing:0.5px; color:#2e2e2e; line-height:1.1; text-align:center; margin-bottom:4px;">
        EMPRESA E.I.R.L.
      </div>

<!-- DATOS CENTRADOS CON TEL Y EMAIL AL COSTADO -->
<p style="text-align:center; color:#000000; font-size:11.5px; line-height:1; margin:0; padding:0;">
  <strong>RUC:</strong> 00000000000<br style="line-height:1.2;"> <!-- pequeño interlineado -->
  <img src="icon-telefono.png" style="width:10px; vertical-align:middle; margin-right:2px;"> 
  <strong>Tel:</strong> (+51) 00000000000 &nbsp;&nbsp;&nbsp;
  <img src="icon-email.png" style="width:10px; vertical-align:middle; margin-right:2px;"> 
  <strong>Email:</strong> xxxxxxxxx@gmail.com
</p>


    </td>
  </tr>
</table>


</td>

  </tr>
</table>
<!-- ===================== EMPRESA + CLIENTE + DOCUMENTO ===================== -->
<table width="100%" cellpadding="6" cellspacing="0"
  style="font-family: Helvetica, Arial, sans-serif; font-size: 10px;">

  <tr>

    <!-- ================= EMPRESA + CLIENTE ================= -->
<td width="62%" valign="top" style="border-left:4px solid #c6081a; padding:5px 0 5px 10px;">
  
  <!-- CLIENTE -->
  <table width="100%" cellpadding="2" cellspacing="0" style="font-size:10px; margin-top:4px;">
    
    <tr>
      <td width="20%" valign="top" style="color:#686363; font-weight:bold; letter-spacing:0.3px;">
        EMPRESA:
      </td>
      <td width="80%" style="color:#2e2e2e;">
        <strong>' . $razon_social . '</strong>
      </td>
    </tr>
    
    <tr>
      <td valign="top" style="color:#686363; font-weight:bold; letter-spacing:0.3px;">
        RUC:
      </td>
      <td style="color:#555;">
        <strong>' . $ruc . '</strong>
      </td>
    </tr>
    
    <tr>
      <td valign="top" style="color:#686363; font-weight:bold; letter-spacing:0.3px;">
        DIRECCIÓN:
      </td>
      <td style="color:#555;">
        <strong>' . $direccion . '</strong>
      </td>
    </tr>
    
    <tr>
      <td valign="top" style="color:#686363; font-weight:bold; letter-spacing:0.3px;">
        CONTACTO:
      </td>
      <td style="color:#555;">
       <strong>' . $contacto1 . '</strong>
        <!-- Aquí puedes agregar datos de contacto si quieres -->
      </td>
    </tr>
    
  </table>

</td>



    <!-- ================= DATOS DOCUMENTO ================= -->
    <td width="38%" valign="top">
  <table width="100%" cellpadding="6" cellspacing="0" style="border:1px solid #e5e5e5; border-radius:4px; font-size:10px; border-collapse:collapse; text-align:center;">
    <tr style="background-color:#f9f9f9;">
      <td colspan="2" style="font-weight:bold; color:#c6081a; letter-spacing:0.4px; padding:5px 6px;">
        COTIZACIÓN
      </td>
    </tr>

    <tr>
  <td colspan="2" style="color:#2e2e2e; font-weight:bold; padding:2px 6px; line-height:1.1;">
    Fecha emisión: ' . $fecha_emi . '
  </td>
</tr>

<tr>
  <td colspan="2" style="color:#2e2e2e; font-weight:bold; padding:2px 6px; line-height:1.1;">
    N° Cotización: 100' . $nro_venta . '
  </td>
</tr>

  </table>
</td>

  </tr>
</table>

<br>


<p style="font-size:10px;">
  <strong>
    A continuación presentamos nuestra oferta, la cual esperamos sea de su conformidad.
  </strong>
</p>

<table width="100%" cellspacing="0" cellpadding="10" style="font-family: Arial, sans-serif; font-size: 10px; border-collapse: collapse; text-align: left;">
  <thead>
    <tr style="background-color: #c6081a; color: white;font-weight: bold; text-align: center;">
        <th style="width: 13%; border: 1px solid #ffffff;">COD.</th>
        <th style="width: 9%; border: 1px solid #ffffff;">CANT.</th>
        <th style="width: 16%; border: 1px solid #fffdfd;">MARCA</th>
        <th style="width: 38%; border: 1px solid #ffffff;">DESCRIPCION</th>
        <th style="width: 12%; border: 1px solid #fdfdfd;">P. UND</th>
        <th style="width: 12%; border: 1px solid #fffdfd;">TOTAL</th>
    </tr>
  </thead>
  <tbody>
';

foreach ($productos as $producto) {
    $cantidad = $producto["cantidad"];
    $codigo = $producto["codigo"];
    $marca = $producto["marca"];
    $descripcion = $producto["descripcion"];
    $precio = number_format($producto["precio_final"], 2);
    $total = number_format($producto["total"], 2);

    $html .= '
    <tr style="background-color: #e4e2e2;">
       <td align="center" style="width: 13%; border: 1px solid #faf8f8;">' . $codigo . '</td>
      <td align="center" style="width: 9%; border: 1px solid #ffffff;">' . $cantidad . '</td>
     
      <td align="center" style="width: 16%; border: 1px solid #fdfbfb;">' . $marca . '</td>
      <td align="left" style="width: 38%; text-align: left; border: 1px solid #faf8f8;">' . $descripcion . '</td>
      <td align="center" style="width: 12%; border: 1px solid #f8f8f8;">S/ ' . $precio . '</td>
      <td align="center" style="width: 12%; border: 1px solid #ffffff;">S/ ' . $total . '</td>
    </tr>';
}

$html .= '
  </tbody>
</table>
<div style="margin-top: 20px;"></div>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="3" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10px;">
  <tr>
   <!-- Columna izquierda: Condiciones -->
<td width="74%" valign="top">

  <!-- Título sin separación -->
  <div style="
      text-align: left; 
      font-weight: bold; 
      font-size: 13px; 
      margin: 0; 
      padding: 0; 
      line-height: 1;
  ">
    OBSERVACIONES
  </div>

  <!-- Lista mejora visual -->
  <ul style="
      margin: 2px 0 0 0; 
      padding: 0 0 0 10px; 
      list-style-type: none; 
      line-height: 1.2;
  ">

    <li style="margin: 0; padding: 0;">
      <span style="color: #c6081a; font-weight: bold;">•</span>
      Impuesto: Los precios no incluyen IGV
    </li>

    <li style="margin: 0; padding: 0;">
      <span style="color: #c6081a; font-weight: bold;">•</span>
      Tiempo de Entrega: 05 días hábiles, contados desde el día siguiente de recibido el abono
    </li>

    <li style="margin: 0; padding: 0;">
      <span style="color: #c6081a; font-weight: bold;">•</span>
      Forma de Pago: Transferencia BANCARIA, si hay comisión por transacción la asume el cliente
    </li>

  </ul>

</td>


   <!-- Columna derecha: Precios -->
   <td width="28%" valign="top">
  <table width="93%" align="center" border="0" cellspacing="0" cellpadding="4" style="border-collapse: collapse; font-size: 12px; border: 1px solid #ffffff;">
    
    <!-- SUBTOTAL -->
    <tr>
      <td width="50%" style="font-weight: bold; border: 1px solid #ffffff;">SUBTOTAL</td>
      <td width="50%" style="text-align: right; font-weight: normal; border: 1px solid #fffefe;">
        S/ ' . number_format($sub_total, 2) . '
      </td>
    </tr>

    <!-- IGV -->
    <tr>
      <td style="font-weight: bold; border: 1px solid #fffdfd;">IGV</td>
      <td style="text-align: right; font-weight: normal; border: 1px solid #fffefe;">
        S/ ' . number_format($igv, 2) . '
      </td>
    </tr>

    <!-- TOTAL (único con estilos de fondo y color de texto) -->
    <tr>
      <td style="font-weight: bold; background-color: #c6081a; color: #fffefe;">
        TOTAL
      </td>
      <td style="text-align: right; font-weight: bold; background-color: #c6081a; color: #fffdfd;">
        S/ ' . number_format($precio_final, 2) . '
      </td>
    </tr>

  </table>
</td>



  </tr>
</table>



';

// 🔥 AQUI AGREGA LA TABLA (antes del footer)
$tabla_cuentas = '
<table border="0" cellspacing="0" cellpadding="6" width="100%" align="center" 
 style="font-size: 13.5px; text-align: center; vertical-align: middle; border-collapse: collapse;">
  <tr>
    <td width="40%" align="left" valign="middle" style="font-weight: bold; color: red;">
     ' . nl2br(htmlspecialchars($envio)) . '
    </td>
  </tr>
</table>
';

// Renderizar PDF
$pdf->writeHTML($html, true, false, true, false, '');

// ===================== TABLA ANTES DEL FOOTER =====================
$pdf->writeHTML($tabla_cuentas, true, false, true, false, '');



// Pie de página final
$footer_html = '
<table border="0" cellspacing="0" cellpadding="6" width="100%" align="center" style="font-size: 11px; text-align: center; vertical-align: middle; border-collapse: collapse;">
  <tr>
    <td width="15%" align="center" valign="middle">
      <img src="bcp.JPG" height="50" alt="Logo BCP">
    </td>
    
    <td width="58%" align="left" valign="middle" style="font-weight: bold;">
      <img src="" height="25" style="vertical-align: middle; margin-right: 8px;" alt="Icono Cuenta">
      N° CUENTA BCP SOLES: 00000000000000<br>
      CUENTA INTERBANCARIA (CCI): 000000000000000
    </td>
    
    <td width="12%" align="center" valign="middle" style="font-weight: bold;">
     
    </td>
    
    <td width="15%" align="center" valign="middle">
      <img src="pc.JPG" height="50" alt="Logo PC">
    </td>
  </tr>
</table>';

$pdf->SetY(-26);
$pdf->writeHTML($footer_html, true, false, false, false, '');


$nombre_archivo = 'Cotizacion_Nro_' . $nro_venta . '_' . preg_replace('/[^A-Za-z0-9]/', '_', $razon_social) . '.pdf';
$pdf->Output($nombre_archivo, 'I');