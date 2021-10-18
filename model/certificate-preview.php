<?php
	use setasign\Fpdi\Fpdi;

	require_once('model/fpdf/fpdf.php');
	require_once('model/fpdi/autoload.php');

	// Include Barcode Generator Model
	include 'model/barcode-encoded.php';

	// Barocode Image
	$image = "data:image/png;base64,".$base64Encoded;

	// Certificate Layout
	$certLayout = explode("-",$_POST['certLayout']);
	$certOrientation = $certLayout[0];
	$certSize = $certLayout[1];
	// Certificate Text Style
	$certTextStyle = explode("-",$_POST['certTextStyle']);
	$certTextFont = $certTextStyle[0];
	$certTextFontStyle = $certTextStyle[1];
	$certTextFontSize = $certTextStyle[2];
	// Certificate Text Color
	list($certTextFontColorR, $certTextFontColorG, $certTextFontColorB) = sscanf($_POST['certTextFontColor'], "#%02x%02x%02x");
	// Certificate Text Position
	$certTextPosition = explode(",",$_POST['certTextPosition']);
	$certTextPositionX = $certTextPosition[0];
	$certTextPositionY = $certTextPosition[1];
	// Certificate Barcode Position
	$certBarcodePosition = explode(",",$_POST['certBarcodePosition']);
	$certBarcodePositionX = $certBarcodePosition[0];
	$certBarcodePositionY = $certBarcodePosition[1];

	// initiate FPDI
	$pdf = new Fpdi();
	// add a page
	$pdf->AddPage($certOrientation, $certSize);
	// set the source file
	$pdf->setSourceFile('certificate-templates/'.$certificateFile);
	// import page 1
	$tplIdx = $pdf->importPage(1);
	// use the imported page and place it at position 0, 0
	$pdf->useTemplate($tplIdx, 0, 0);

	// now write some text above the imported page
	$pdf->SetFont($certTextFont, $certTextFontStyle, $certTextFontSize);
	// Set text Color
	$pdf->SetTextColor($certTextFontColorR, $certTextFontColorG, $certTextFontColorB);
	// Set text position
	$pdf->SetXY($certTextPositionX, $certTextPositionY);
	// Centered text in a framed 20*10 mm cell and line break
	$pdf->Cell(20,10,$name,0,0,'C');

	$pdf->Image($image, $certBarcodePositionX,$certBarcodePositionY,70,0,'png'); // X start, Y start, X width, Y width in mm

	$str = $pdf->Output('S', 'generated.pdf');

	$strBase64 =  base64_encode($str);
?>