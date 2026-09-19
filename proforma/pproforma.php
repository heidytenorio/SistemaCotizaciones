<?php
require_once('../app/TCPDF-main/tcpdf.php');
include('../app/config.php');
// === FUNCIÓN PARA CONVERTIR NÚMEROS A LETRAS (INTL SI ESTÁ, SINO FALLBACK) ===
function numeroALetras($numero) {
    // normalizar a número float
    $numero = floatval($numero);
    $entero = floor($numero);
    $decimal = round(($numero - $entero) * 100);

    // Si NumberFormatter está disponible, usarlo (más preciso con reglas de idioma)
    if (class_exists('NumberFormatter')) {
        $formatterES = new NumberFormatter("es", NumberFormatter::SPELLOUT);
        $texto_entero = $formatterES->format($entero);
        $texto_entero = strtoupper($texto_entero);
    } else {
        // Fallback: convertir entero a letras en español (soporta hasta cientos de millones)
        $unidad = function($n) {
            $u = ["","uno","dos","tres","cuatro","cinco","seis","siete","ocho","nueve","diez",
                "once","doce","trece","catorce","quince","dieciseis","diecisiete","dieciocho","diecinueve"];
            return $u[$n] ?? "";
        };

        $decena = function($n) use (&$unidad) {
            $d = intval($n/10);
            $r = $n % 10;
            $t = ["", "", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];
            if ($n < 20) return $unidad($n);
            if ($d == 2 && $r > 0) return "veinti" . $unidad($r);
            if ($r == 0) return $t[$d];
            return $t[$d] . " y " . $unidad($r);
        };

        $centena = function($n) use (&$unidad, &$decena) {
            $c = intval($n/100);
            $r = $n % 100;
            $h = ["", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos",
                "seiscientos", "setecientos", "ochocientos", "novecientos"];
            if ($n == 0) return "";
            if ($n == 100) return "cien";
            if ($c == 0) return $decena($r);
            if ($r == 0) return $h[$c];
            return $h[$c] . " " . $decena($r);
        };

        $miles = function($n) use (&$centena) {
            if ($n < 1000) return $centena($n);
            $m = intval($n / 1000);
            $r = $n % 1000;
            $pref = ($m == 1) ? "mil" : numeroALetras_entero($m) . " mil";
            return trim($pref . ($r > 0 ? " " . $centena($r) : ""));
        };

        // Helper para convertir el entero por partes (hasta millones)
        function numeroALetras_entero($n) {
            $n = intval($n);
            if ($n == 0) return "cero";
            $parts = [];
            if ($n >= 1000000) {
                $millones = intval($n / 1000000);
                $n = $n % 1000000;
                $parts[] = ($millones == 1) ? "un millon" : numeroALetras_entero($millones) . " millones";
            }
            if ($n >= 1000) {
                $m = intval($n / 1000);
                $n = $n % 1000;
                if ($m == 1) $parts[] = "mil";
                else $parts[] = numeroALetras_entero($m) . " mil";
            }
            if ($n > 0) {
                // centena/decena/unidad usando la lógica anterior (reutilizamos subfunciones)
                // recreamos funciones pequeñas aquí para evitar scope issues
                $unidadArr = ["","uno","dos","tres","cuatro","cinco","seis","siete","ocho","nueve","diez",
                    "once","doce","trece","catorce","quince","dieciseis","diecisiete","dieciocho","diecinueve"];
                $decenasArr = ["", "", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];
                if ($n < 20) $parts[] = $unidadArr[$n];
                else if ($n < 100) {
                    $d = intval($n/10);
                    $r = $n % 10;
                    if ($d == 2 && $r > 0) $parts[] = "veinti" . $unidadArr[$r];
                    else if ($r == 0) $parts[] = $decenasArr[$d];
                    else $parts[] = $decenasArr[$d] . " y " . $unidadArr[$r];
                } else {
                    $c = intval($n/100);
                    $r = $n % 100;
                    $centArr = ["", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos",
                        "seiscientos", "setecientos", "ochocientos", "novecientos"];
                    if ($n == 100) $parts[] = "cien";
                    else if ($r == 0) $parts[] = $centArr[$c];
                    else {
                        $partCent = $centArr[$c];
                        if ($r < 20) $partCent .= " " . $unidadArr[$r];
                        else {
                            $d = intval($r/10);
                            $rr = $r % 10;
                            if ($d == 2 && $rr > 0) $partCent .= " veinti" . $unidadArr[$rr];
                            else if ($rr == 0) $partCent .= " " . $decenasArr[$d];
                            else $partCent .= " " . $decenasArr[$d] . " y " . $unidadArr[$rr];
                        }
                        $parts[] = $partCent;
                    }
                }
            }
            return trim(implode(" ", $parts));
        }

        $texto_entero = strtoupper(numeroALetras_entero($entero));
    }

    // agregar la parte decimal en formato "CON XX/100"
    if ($decimal > 0) {
        $texto_entero .= " CON " . str_pad($decimal, 2, "0", STR_PAD_LEFT) . "/100";
    } else {
        $texto_entero .= " CON 00/100";
    }

    $texto_entero .= " SOLES";
    return $texto_entero;
}


// === OBTENER DATOS DESDE URL ===
$nro_orden = $_GET['nro_orden'] ?? null;
if (!$nro_orden) die("Falta el número de orden en la URL.");

// === CONSULTAR CABECERA ===
$sentencia = $pdo->prepare("SELECT * FROM tb_oferta WHERE nro_orden = :nro_orden");
$sentencia->execute(['nro_orden' => $nro_orden]);
$orden = $sentencia->fetch(PDO::FETCH_ASSOC);
if (!$orden) die("No se encontró la orden con el número proporcionado.");

// === VARIABLES ===
$fecha_emi     = date("d/m/Y", strtotime($orden['fecha_emi']));
$sub_total     = $orden['sub_total'];
$igv           = $orden['igv'];
$precio_final  = $orden['precio_final'];
$razon_social  = $orden['razon_social'];
$ruc           = $orden['ruc'];
$detalle       = $orden['detalle'];
$plazo_entrega = $orden['plazo_entrega'] ?? '—';
$garantia      = $orden['garantia'] ?? '—';
$vigencia      = $orden['vigencia'] ?? '—';

$total_letras = numeroALetras($precio_final);


// === CONSULTAR PRODUCTOS ===
$sentencia = $pdo->prepare("
    SELECT 
        c.cantidad,
        c.precio_final,
        c.medida,
        (c.cantidad * c.precio_final) AS total,
        p.codigo,
        p.descripcion,
    m.nombre AS marca
    FROM tb_xcarrito c
    INNER JOIN tbp_almacen p ON c.id_producto = p.id_producto
    INNER JOIN tbp_marca m ON p.id_marca = m.id_marca
    WHERE c.nro_orden = :nro_orden
");
$sentencia->bindParam(':nro_orden', $nro_orden);
$sentencia->execute();
$productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);



// === CONFIGURAR PDF ===
class MYPDF extends TCPDF {

}

$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('ooooooo');
$pdf->SetAuthor('oooooooo');
$pdf->SetTitle('Cotización - oooooo');
$pdf->SetMargins(12, 12, 12);
$pdf->SetAutoPageBreak(true, 15);

$pdf->setPrintFooter(false);

// 🔽 AGREGA ESTAS DOS LÍNEAS AQUÍ
$pdf->setDrawColor(255, 255, 255);  // elimina la línea negra entre páginas
$pdf->SetLineStyle(['width' => 0]); // desactiva cualquier borde residual

$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 9);

// === HTML Y ESTILOS ===
$html = '
<style>
body {
  font-family: Helvetica, Arial, sans-serif;
  color: #333;
  font-size: 9px;
}

/* ==== CABECERA ==== */
.header {
  background-color: #78BF30;
  color: white;
  padding: 0px 10px;          /* menos espacio arriba/abajo */
  border-radius: 6px;
  margin-bottom: 4px;         /* reduce espacio inferior */
}

.header td {
  border: none;
  vertical-align: middle;     /* centra verticalmente logo y texto */
  padding: 2px 6px;
}

.header-logo img {
  width: 135px;
  display: block;
  margin: 3px auto;           /* centrado dentro de su celda */
}

.header-info {
  text-align: right;
  font-size: 9px;
  line-height: 1.25;
  padding-top: 2px;
}

.header-info .empresa {
  font-size: 11px;
  font-weight: bold;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}



.sub-lema {
  text-align: center;
  font-style: italic;
  color: #555;
  margin-top: 8px;     /* margen superior pequeño */
  margin-bottom: 8px;  /* margen inferior pequeño */
  font-size: 9px;
}

/* ==== BLOQUE CLIENTE (Optimizado) ==== */
.section {
  border: 1px solid #cde6d3;
  border-radius: 6px;
  background-color: #f9fff9;
  padding: 2px 6px;       /* menos espacio interno */
  margin-bottom: 0;
}

.section.compacto {
  padding-top: 2px;
  padding-bottom: 2px;
  margin-bottom: 0;
}

.section p {
  margin: 1px 0;          /* reduce espacio entre líneas */
  line-height: 0.7;        /* menor altura entre líneas */
  font-size: 8.8px;        /* texto ligeramente más pequeño */
  padding: 0;
}

.section-title {
  background-color: #78BF30;
  color: white;
  font-weight: bold;
  font-size: 10px;
  padding: 2px 4px;
  border-radius: 4px;
  letter-spacing: 0.4px;
  text-transform: uppercase;
}

/* ==== TABLA PRODUCTOS ==== */
table.productos {
  border-collapse: collapse;
  width: 100%;
  margin-top: 1px;            /* menos espacio antes de tabla */
}

table.productos th {
  background-color: #78BF30;
  color: #fff;
  padding: 6px 4px;
  text-align: center;
  font-weight: bold;
  font-size: 9px;
  letter-spacing: 0.3px;
}

table.productos td {
  border: 0.4px solid #ccc;
  padding: 4px;
  font-size: 9px;
}

table.productos tr:nth-child(even) {
  background-color: #f2fbf5;
}
/* ==== TABLA DE TOTALES (alineada a la derecha) ==== */
.tabla-totales {
  border-collapse: collapse;
  margin-top: 8px;
  width: 38%;
  font-size: 9.5px;
  float: right;
  border: 0.4px solid #cde6d3;
  border-radius: 6px;
  overflow: hidden;
  background-color: #f9fff9;
}

.tabla-totales th {
  background-color: #eaf8ed;
  text-align: left;
  padding: 6px 8px;
  font-weight: bold;
  color: #333;
  width: 55%;
  border-bottom: 0.4px solid #cde6d3;
}

.tabla-totales td {
  text-align: right;
  padding: 6px 8px;
  border-bottom: 0.4px solid #cde6d3;
}

.tabla-totales tr:last-child td,
.tabla-totales tr:last-child th {
  border-bottom: none;
}

.tabla-totales .total-final th,
.tabla-totales .total-final td {
  background-color: #d6f3db;
  font-weight: bold;
  font-size: 10px;
  border-top: 0.4px solid #cde6d3;
}

/* ==== TABLA INFO FINAL ==== */
.tabla-info {
  width: 85%;
  margin: 15px auto;
  border-collapse: collapse;
  font-size: 9px;
  border-radius: 6px;
  overflow: hidden;
  border: 0.4px solid #ccc;
}

.tabla-info th {
  background-color: #78BF30;  /* verde institucional */
  color: #fff;
  padding: 6px;
  text-align: left;
  width: 40%;
  font-weight: bold;
}

.tabla-info td {
  background-color: #f4fff6;  /* verde muy claro */
  padding: 6px 8px;
  border-bottom: 0.4px solid #ccc;
  font-weight: bold;          /* texto en negrita */
  color: #333;                /* gris oscuro para mejor contraste */
}

.tabla-info tr:nth-child(even) td {
  background-color: #eaf8ed;  /* alternancia suave */
}

.tabla-info .resaltado td {
  background-color: #d6f3db;  /* tono más fuerte para destacar */
  font-weight: bold;
}





.tabla-info .resaltado td {
  background-color: #e3f7e6;
 font-weight: bold;
}

/* ==== PIE ==== */
.footer {
  text-align: center;
  font-size: 9px;
  color: #555;
  margin-top: 18px;
  border-top: 0.6px solid #ccc;
  padding-top: 5px;
  font-style: italic;
}

.celda-naranja {
  background-color: #78BF30; /* fondo anaranjado */
  color: #ffffff;            /* texto blanco */
  font-weight: bold;
  text-align: center;        /* centra el texto dentro de la celda */
  border-radius: 3px;
  padding: 4px;
}
</style>



<!-- CABECERA -->
<table class="header" width="100%">
  <tr>
    <td width="30%" class="header-logo"><img src="logo.png"></td>
    <td width="70%" class="header-info">
       <br>
        <br>
       <div class="empresa" style="font-size: 13px";><strong>EMPRESA E.I.R.L.</strong></div>
      RUC: 0000000000 <br>
      DIRECCION: <br> -LAMBAYEQUE - CHICLAYO
      <br><strong>BIENES, SERVICIOS Y OBRAS</strong>
      <br>
    </td>
  </tr>
</table>

<div class="sub-lema">“Año de la Recuperación y Consolidación de la Economía Peruana”</div>


<!-- BLOQUE DE CELDA NARANJA -->
<table width="100%" style="font-size:10px; border:none; margin-top:5px;">
  <tr>
    <td width="70%"><b></b></td>
    <td width="30%" class="celda-naranja"> COTIZACIÓN N° 300' . htmlspecialchars($nro_orden) . '      </td>
  </tr>
</table>
    
    
  <table width="100%" style="font-size:9px; border:none;">
    <tr>
      <td width="75%"><b>NOMBRE / RAZÓN SOCIAL: </b> ' . htmlspecialchars($razon_social) . '</td>
       <td width="25%"><b>DNI / RUC: </b> ' . htmlspecialchars($ruc) . '</td>
    </tr>
    <tr>
      <td width="75%"><b>DETALLE: </b> ' . htmlspecialchars($detalle) . '</td>
      <td width="25%"><b>FECHA: </b> ' . htmlspecialchars($fecha_emi) . '</td>
    </tr>
  </table>
</div>
<br>

<!-- PRODUCTOS  <div class="section compacto" style="margin-top:5px;">-->
<table class="productos">
  <thead>
    <tr>
      <th width="6%" style="font-weight:bold; font-size:10px;">Item</th>
      <th width="48%" style="font-weight:bold; font-size:10px;">Descripción</th>
      <th width="12%" style="font-weight:bold; font-size:10px;">Marca</th>
      <th width="7%" style="font-weight:bold; font-size:10px;">Med.</th>
      <th width="7%" style="font-weight:bold; font-size:10px;">Cant.</th>
      <th width="10%" style="font-weight:bold; font-size:10px;">P. Unit.</th>
      <th width="10%" style="font-weight:bold; font-size:10px;">Total</th>
    </tr>
  </thead>
  <tbody>';

$item = 1;
if (count($productos) > 0) {
    foreach ($productos as $producto) {
        $descripcion = htmlspecialchars($producto["descripcion"]);
        $marca = htmlspecialchars($producto["marca"]);
        $medida      = htmlspecialchars($producto["medida"]);
        $cantidad    = htmlspecialchars($producto["cantidad"]);
        $precio      = number_format($producto["precio_final"], 2);
        $total       = number_format($producto["total"], 2);

        $html .= '
        <tr align="center">
          <td style="width:6%; padding:5px;">' . $item . '</td>
          <td style="width:48%; text-align:left; padding:5px;">' . $descripcion . '</td>
           <td style="width:12%; text-align:left; padding:5px;">' . $marca . '</td>
          <td style="width:7%; padding:5px;">'. $medida . '</td>
          <td style="width:7%; padding:5px;">' . $cantidad . '</td>
          <td style="width:10%; text-align:right; padding:5px;">S/ ' . $precio . '</td>
          <td style="width:10%; text-align:right; font-weight:bold; padding:5px;"><b>S/ ' . $total . '</b></td>
        </tr>';
        $item++;
    }
} else {
    $html .= '<tr><td colspan="6" align="center" style="color:#999;">No hay productos registrados.</td></tr>';
}

$html .= '
  </tbody>
</table>

<br>
<br>
<!-- TABLA DE TOTALES: TEXTO EN LETRAS ALINEADO EXACTAMENTE CON EL "TOTAL" -->
<table width="100%" style="font-size:9px; border:none; margin-top:8px;">
  <tr>
    <!-- COLUMNA IZQUIERDA: TEXTO "SON:" alineado con TOTAL -->
    <td width="70%" style="vertical-align:top;">
      <table width="100%" style="border:none; border-collapse:collapse; font-size:9px; height:100%;">
        <tr style="height:33%;"><td></td></tr>
        <tr style="height:33%;"><td></td></tr>
        <tr style="height:34%; vertical-align:middle;">
          <td style="padding:6px 8px; text-transform:uppercase;">
            <b>SON:</b> ' . $total_letras . '
          </td>
        </tr>
      </table>
    </td>

    <!-- COLUMNA DERECHA: TABLA DE TOTALES -->
    <td width="30%" align="right" style="vertical-align:top;">
      <table style="border-collapse:collapse; font-size:9px; width:100%; border:0.5px solid #8def86; border-radius:6px; background-color:#f9fff9;">
        <tr>
          <td style="padding:5px 8px; text-align:left; font-weight:bold; background-color:#eaf8ed;">Subtotal:</td>
          <td style="padding:5px 8px; text-align:right;">S/ ' . number_format($sub_total, 2) . '</td>
        </tr>
        <tr>
          <td style="padding:5px 8px; text-align:left; font-weight:bold; background-color:#eaf8ed;">IGV (18%):</td>
          <td style="padding:5px 8px; text-align:right;">S/ ' . number_format($igv, 2) . '</td>
        </tr>
        <tr>
          <td style="padding:6px 8px; text-align:left; font-weight:bold; background-color:#d6f3db;">Total:</td>
          <td style="padding:6px 8px; text-align:right; font-weight:bold; background-color:#d6f3db;">S/ ' . number_format($precio_final, 2) . '</td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<br>
<br>
<div style="clear:both; margin-top:40px; font-size:9.5px; text-align:justify; line-height:1.5;">
Nos complace presentarle la siguiente cotización, reafirmando nuestro compromiso con la calidad, 
puntualidad y responsabilidad. Estamos a su disposición para cualquier consulta o ajuste requerido.
</div>
<br>
<!-- INFORMACIÓN FINAL -->
<table class="tabla-info">
  <tr><th>Precio Total (IGV Incluido): </th><td>S/ ' . number_format($precio_final, 2) . '</td></tr>
  <tr class="resaltado"><th>CCI BCP: </th><td>00000000000000</td></tr>
  <tr class="resaltado"><th>Cuenta Corriente:</th><td>00000000000000</td></tr>
  <tr><th>Plazo de entrega: </th><td>A ' . $plazo_entrega . ' Días de recepción</td></tr>
  <tr><th>Forma de pago: </th><td>Contra entrega</td></tr>
  <tr><th>Garantia: </th><td>' . $garantia . ' Meses</td></tr>
  <tr class="resaltado"><th>Vigencia: </th><td>' . $vigencia . ' DÍAS CALENDARIO</td></tr>
  <tr><th>Representante: </th><td>OOOOOOOO</td></tr>
  <tr><th>Contacto: </th><td>00000000000000</td></tr>
</table>


<br>
<br>

<table  width="100%" style="width:100%; text-align:center;">
  <tr>
    <td style="width:100%; text-align:center;">
      <img src="logo1.png" style="width:160px; height:auto; display:block; margin:0 auto;">
    </td>
  </tr>
</table>

<div class="footer">
Gracias por confiar en <b>OOOOOOO</b> — Comprometidos con la limpieza y la construcción responsable
</div>
';

// === GENERAR PDF ===
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Cotizacion_MarcaAgua.pdf', 'I');
?>
