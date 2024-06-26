<?php

function crearPdf($titulo, $datos)
{
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Restaurante');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'RESTAURANTE');
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('times', 'BI', 14);

// Start First Page Group
$pdf->startPageGroup();

// add a page
$pdf->AddPage();

// set some text to print
$txt = $titulo.PHP_EOL;
foreach ($datos as $dato) {
    foreach ($dato as $key => $value) {
        $txt .= $key.': '. $value.PHP_EOL;
    }
    $txt .= PHP_EOL;
}

// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'L', true, 0, false, false, 0);

// add second page
$pdf->AddPage();
$pdf->Cell(0, 10, 'This is the second page of group 1', 0, 1, 'L');

// Start Second Page Group
$pdf->startPageGroup();

// add some pages
$pdf->AddPage();
$pdf->Cell(0, 10, 'This is the first page of group 2', 0, 1, 'L');
$pdf->AddPage();
$pdf->Cell(0, 10, 'This is the second page of group 2', 0, 1, 'L');
$pdf->AddPage();
$pdf->Cell(0, 10, 'This is the third page of group 2', 0, 1, 'L');
$pdf->AddPage();
$pdf->Cell(0, 10, 'This is the fourth page of group 2', 0, 1, 'L');

// ---------------------------------------------------------
$ruta = 'C:\xampp\htdocs\la-comanda\app\Archivos';

//Close and output PDF document
$pdf->Output($ruta .'/'. $titulo. '.pdf' , 'F');

}
