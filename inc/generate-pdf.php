<?php 


require_once 'dompdf/autoload.inc.php';

$pdf_file_name =  $first_name . $last_name;
$pdf_file_name_translit = translit_sef($pdf_file_name);
$pdf_name_times = date("mdyGis");
$pdf_file_path = $_SERVER['DOCUMENT_ROOT'] .  '/wp-content/uploads/uets-tests/' . $pdf_file_name_translit . $pdf_name_times . '.pdf';

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($pdf_html);

$dompdf->set_option('enable_remote', TRUE);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
//$dompdf->stream();

$mypdf = $dompdf->output(); // Запишет наш pdf файл в переменную строкой

file_put_contents($pdf_file_path, $mypdf);