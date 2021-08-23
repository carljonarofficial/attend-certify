<?php
	use setasign\Fpdi\Fpdi;

	require_once('model/fpdf/fpdf.php');
	require_once('model/fpdi/autoload.php');

	// Include Barcode Generator Model
	include 'model/barcode-encoded.php';

	// Barocode Image
	$image = "data:image/png;base64,".$base64Encoded;

	// initiate FPDI
	$pdf = new Fpdi();
	// add a page
	$pdf->AddPage("L", "Letter");
	// set the source file
	$pdf->setSourceFile('certificate-templates/'.$certificateFile);
	// import page 1
	$tplIdx = $pdf->importPage(1);
	// use the imported page and place it at position 10,10 with a width of 100 mm
	$pdf->useTemplate($tplIdx, 0, 0);

	// // now write some text above the imported page
	// $pdf->SetFont('Helvetica', '', 20);
	// $pdf->SetTextColor(0, 0, 0);
	// $pdf->SetXY(70, 85);
	// $pdf->Write(0, 'Carl Jonar Navarro Palado');
	// Set font
	$pdf->SetFont('Helvetica', 'B', 30);
	// Move to 8 cm to the right
	$pdf->SetXY(130, 79);
	// $pdf->Cell(80);
	// Centered text in a framed 20*10 mm cell and line break
	$pdf->Cell(20,10,$name,0,0,'C');

	$pdf->Image($image, 20,169,70,0,'png'); // X start, Y start, X width, Y width in mm

	$str = $pdf->Output('S', 'generated.pdf');

	$strBase64 =  base64_encode($str);
?>