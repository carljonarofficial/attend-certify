<?php
	// Include Bulk Generator Model
	include "model/bulk-barcode-generator.php";

	// Fetch up an certificate config information
	$certConfigStmt = $conn->prepare("SELECT * FROM `certificate_config` WHERE `event_ID` = ?");
	$certConfigStmt->bind_param('i', $currentEventID);
    $certConfigStmt->execute();
    $certConfigInfo =  $certConfigStmt->get_result();
    $certConfigStmt->close();

	$certOrientation = "L";
	$certSize = "Letter";
	$certTextFont = "Helvetica";
	$certTextFontStyle = "";
	$certTextFontSize = 30;
	$certTextFontColorR = 0;
	$certTextFontColorG = 0;
	$certTextFontColorB = 0;
	$certTextPositionX = 130;
	$certTextPositionY = 79;
	$certBarcodePositionX = 20;
	$certBarcodePositionY = 169;

	// Get exisiting config information
	while ($row2 = $certConfigInfo->fetch_assoc()){
		// Certificate Layout
		$certLayout = explode("-",$row2["page_layout"]);
		$certOrientation = $certLayout[0];
		$certSize = $certLayout[1];
		// Certificate Text Style
		$certTextStyle = explode("-",$row2["text_style"]);
		$certTextFont = $certTextStyle[0];
		$certTextFontStyle = $certTextStyle[1];
		$certTextFontSize = $certTextStyle[2];
		// Certificate Text Color
		list($certTextFontColorR, $certTextFontColorG, $certTextFontColorB) = sscanf($row2["text_color"], "#%02x%02x%02x");
		// Certificate Text Position
		$certTextPosition = explode(",",$row2["text_position"]);
		$certTextPositionX = $certTextPosition[0];
		$certTextPositionY = $certTextPosition[1];
		// Certificate Barcode Position
		$certBarcodePosition = explode(",",$row2["barcode_position"]);
		$certBarcodePositionX = $certBarcodePosition[0];
		$certBarcodePositionY = $certBarcodePosition[1];
	}
?>