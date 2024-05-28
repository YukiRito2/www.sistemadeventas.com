<?php
// Include the main TCPDF library (search for installation path).
require_once('../app/TCPDF-main/tcpdf.php');
include('../app/config.php');
//$id_venta_get = $_GET('id_venta');
//$nro_venta_get = $_GET('nro_venta');

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
<Table border="1" style="font-size: 10px">
<tr>
    <td style="text-align: center; width: 200px" >
    <b>SISTEMA DE VENTAS NALVARTE</b><br>
    Zona Lima, Este AV.xxxx<br>
    999 999 999 - 999 999 999 <br>
    Lima-Perú
    </td>
    <td style="width: 200px"></td>
    <td style="font-size: 16px; width:290px">
        <b>RUC: </b>01000000 <br>
        <b>Nro factura:</b> 0001 <br>
        <b>Nro de Autorizacion: </b>1001230011
    </td>
</tr>
</Table>
<p style= "text-align: center; font-size:25px"><b>FACTURA</b></p>
<div>
<tabla>
<tr><td><b> Fecha: </b>28/05/2024</td>
</tr>
</tabla>
</div>
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

$QR = 'Factura realizada por el sistema de paqueo Nalvarte WEB, Al cliente xxxxxxxxx';
$pdf->write2DBarcode($QR, 'QRCODE,L', 22, 105, 35, 35, $style);
// ---------------------------------------------------------

// Close and output PDF document
$pdf->Output('example_002.pdf', 'I');
//============================================================+
// END OF FILE
//============================================================+