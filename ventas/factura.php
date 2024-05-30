<?php
// Include the main TCPDF library (search for installation path).
require_once('../app/TCPDF-main/tcpdf.php');
include('../app/config.php');
include('../app/controllers/ventas/literal.php');


$id_venta_get = $_GET['id_venta'];
$nro_venta_get = $_GET['nro_venta'];


$sql_ventas = "SELECT *,cli.nombre_cliente as nombre_cliente, cli.nit_ci_cliente as nit_ci_cliente
                FROM tb_ventas as ve INNER JOIN tb_clientes as cli ON cli.id_cliente = ve.id_cliente where ve.id_venta = '$id_venta_get'";
$query_ventas = $pdo->prepare($sql_ventas);
$query_ventas->execute();
$ventas_datos = $query_ventas->fetchAll(PDO::FETCH_ASSOC);


foreach ($ventas_datos as $ventas_dato) {
    $fyh_creacion = $ventas_dato['fyh_creacion'];
    $nit_ci_cliente = $ventas_dato['nit_ci_cliente'];
    $nombre_cliente = $ventas_dato['nombre_cliente'];
    $total_pagado = $ventas_dato['total_pagado'];
}
//precio total a literal
$monto_literal = numtoletras($total_pagado);

$fecha = date("d/m/y", strtotime($fyh_creacion));


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(215, 279), true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nalvarte');
$pdf->setTitle('Factura de Venta');
$pdf->setSubject('Factura de Venta');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

//remmove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);


// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(15, 15, 15);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// Set font
$pdf->setFont('helvetica', '', 12);

// Add a page
$pdf->AddPage();


// Set some content to print
$html = '
<table border="0" style="font-size: 10px">
<tr>
    <td style="text-align: center; width: 230px">
    <img src="../public/images/logo.jpg" width="80px" alt=""><br><br>
    <b>SISTEMA DE VENTAS NALVARTE</b><br>
    Zona Lima, Este AV.xxxx<br>
    999 999 999 - 999 999 999 <br>
    Lima-Perú
    </td>
    <td style="width: 150px"></td>
    <td style="font-size: 16px; width: 290px"><br><br><br><br><br><br>
        <b>RUC: </b>0000000000000000<br>
        <b>Nro factura:</b>' . $id_venta_get . '<br>
        <b>Nro de Autorizacion: </b>1001230011
    </td>
</tr>
</table>

<p style="text-align: center; font-size: 25px"><b>FACTURA</b></p>

<div style="border: 1px solid #000000">
<table border="0" cellpadding="6px">
<tr>
    <td><b>Fecha:</b>' . $fecha . '</td>
    <td></td>
    <td><b>DNI: </b>' . $nit_ci_cliente . '</td>
</tr>
<tr>
    <td colspan="3"><b>Sr: </b>' . $nombre_cliente . '</td>
</tr>
</table>
</div>

<br><br>

<table border="1" cellpadding="5" style="font-size: 12px">
<tr style="text-align: center; background-color: #d6d6d6">
    <th style="width: 40px"><b>Nro</b></th>
    <th style="width: 150px"><b>Producto</b></th>
    <th style="width: 270px"><b>Descripcion</b></th>
    <th style="width: 40px"><b>Cant</b></th>
    <th style="width: 95px"><b>Precio Unitario</b></th>
    <th style="width: 70px"><b>Sub Total</b></th>
</tr>
';

$contador_de_carrito = 0;
$cantidad_total = 0;
$precio_unitario_total = 0;
$precio_total = 0;


$sql_carrito = "SELECT *,pro.nombre as nombre_producto, pro.descripcion as descripcion, pro.precio_venta as precio_venta,
pro.stock as stock, pro.id_producto as id_producto  FROM tb_carrito AS carr INNER JOIN tb_almacen as pro ON carr.id_producto = pro.id_producto 
WHERE nro_venta ='$nro_venta_get'ORDER BY id_carrito ASC ";

$query_carrito = $pdo->prepare($sql_carrito);
$query_carrito->execute();
$carrito_datos = $query_carrito->fetchAll(PDO::FETCH_ASSOC);
foreach ($carrito_datos as $carrito_dato) {
    $id_carrito = $carrito_dato['id_carrito'];
    $contador_de_carrito = $contador_de_carrito + 1;
    $cantidad_total = $cantidad_total + $carrito_dato['cantidad'];
    $precio_unitario_total = $precio_unitario_total +  floatval($carrito_dato['precio_venta']);
    $subtotal =  $carrito_dato['cantidad'] * $carrito_dato['precio_venta'];
    $precio_total = $precio_total + $subtotal;

    $html .= '
    <tr>
        <td style="text-align: center">1</td>
        <td>' . $carrito_dato['nombre_producto'] . '</td>
        <td>' . $carrito_dato['descripcion'] . '</td>
        <td style ="text-align: center">' . $carrito_dato['cantidad'] . '</td>
        <td style ="text-align: center">S/.' . $carrito_dato['precio_venta'] . '</td>
        <td style ="text-align: center">S/' . $subtotal . '</td>
</tr>
    
    
    ';
}

$html .= '
<tr>
    <td colspan="3" style="text-align: right; background-color: #d6d6d6"><b>Total</b></td>
    <td style="text-align: center;background-color: #d6d6d6">' . $cantidad_total . '</td>
    <td style="text-align: center;background-color: #d6d6d6">S/' . $precio_unitario_total . '</td>
    <td style="text-align: center;background-color: #d6d6d6">S/' . $precio_total . '</td>
</tr>
</table>

<p style="text-align: right">
        <b>Monto Total:</b>S/' . $precio_total . '
        </p>
        <p>
            <b>Son: </b>' . $monto_literal . '
        </p>
        <br>
        ----------------------------------------------------------------------------------<br>
        <b>USUARIO: </b>Sistema de Ventas Nalvarte<br>

        <p style="text-align: center"><b>"GRACIAS POR SU PREFERENCIA"</b></p>
        ';

//output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

$style = array(
    'border' => '0',
    'vpadding' => '3',
    'hpadding' => '3',
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => false,
    'module_width' => 1,
    'module_heigth' => 1
);

$QR = 'Factura realizada por el sistema de ventas Nalvarte WEB, Al cliente ' . $nombre_cliente . 'con dni : ' . $nit_ci_cliente . '
en fecha: ' . $fecha . ' con el monto TOTAL de ' . $precio_total . '';
$pdf->write2DBarcode($QR, 'QRCODE,L', 170, 220, 40, 40, $style);
// ---------------------------------------------------------

// Close and output PDF document
$pdf->Output('example_002.pdf', 'I');
//============================================================+
// END OF FILE
//============================================================+