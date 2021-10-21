<?php
	namespace CertificatePot;

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	//Load Composer's autoloader
	require 'model/Exception.php';
	require 'model/PHPMailer.php';
	require 'model/SMTP.php';

	/**
	 *
	*/
	class CertificateMember
	{
		/**
		 * To fetch up the certificate list
		*/
		public function listCertificate($selectedEventID)
		{
			// Using database connection file here
    		include 'dbConnection.php';

    		// SQL Query
    		$sqlQuery = "SELECT (ROW_NUMBER() OVER(ORDER BY `certificate`.`datetime_generated` ASC)) AS `row_num`, CONCAT(`invitees`.lastname, ', ', `invitees`.firstname, ' ', `invitees`.`middlename`) AS `invitee_name`, `certificate`.`invitee_code`, `certificate`.`certificate_code`, `certificate`.`datetime_generated` FROM `invitees`, `certificate` WHERE `certificate`.`invitee_code` = `invitees`.`invitee_code` AND `certificate`.`event_ID` = ? ";

    		// For Search Query
    		if(!empty($_POST["search"]["value"])){
    			$sqlQuery .= "AND (`invitees`.`firstname` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `invitees`.`middlename` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `invitees`.`lastname` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `certificate`.`invitee_code` LIKE '".$_POST["search"]["value"]."%' ";
    			$sqlQuery .= "OR `certificate`.`certificate_code` LIKE '".$_POST["search"]["value"]."%') ";
    		}

    		// Order Query
    		if (!empty($_POST["order"])) {
    			$columnIndex = $_POST['order'][0]['column']; // Column index
    			$sqlQuery .= 'ORDER BY '.$_POST['columns'][$columnIndex]['data'].' '.$_POST['order']['0']['dir'].' ';
    		} else{
    			$sqlQuery .= 'ORDER BY `row_num` ASC ';
    		}

    		// Limit the Query
    		if($_POST["length"] != -1){
				$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
			}

			// Fetch up a list of certificates
			$certificateStmt = $conn->prepare($sqlQuery);
		    $certificateStmt->bind_param('i', $selectedEventID);
		    $certificateStmt->execute();
		    $certificateList =  $certificateStmt->get_result();
		    $certificateStmt->close();

			// Fetch up number of rows
			$sqlNumQuery = "SELECT * FROM `certificate` WHERE `event_ID` = $selectedEventID";
			$numAttendance = $conn->query($sqlNumQuery);
			$numRows = mysqli_num_rows($numAttendance);

			// Number of Filtered Records
			$numFiltered = 0;

			// Save into array
			$certificateData = array();
	        while ($row = $certificateList->fetch_assoc()) {
	        	$ivtRows = array(
	        		"view" => '<button type="button" name="viewCertificate" id="'.$row["invitee_code"].'" class="btn btn-primary btn-xs viewCertificate w-100"><i class="fas fa-eye"></i> View</button>',
		        	"row_num" => $row["row_num"],
		        	"invitee_name" => $row["invitee_name"],
		        	"invitee_code" => $row["invitee_code"],
		        	"certificate_code" => $row["certificate_code"],
		        	"datetime_generated" => $row["datetime_generated"],
		        	"send" => '<button type="button" name="sendEmailCertificate" id="'.$row["invitee_code"].'" class="btn btn-success btn-xs sendCertificate w-100"><i class="fas fa-envelope"></i> Send</button>',
	        	);
	        	$certificateData[] = $ivtRows;
	        	$numFiltered++;
	        }
	        $output = array(
				"draw"				=>	intval($_POST["draw"]),
				"recordsTotal"  	=>  $numRows,
				"recordsFiltered" 	=> 	$numFiltered,
				"data"    			=> 	$certificateData
			);
			echo json_encode($output);
		}

		/**
		 * To validate up the certificate
		*/
		public function validateCertificate($selectedEventID, $scannedCertificateCode)
		{
			// Check if the Certificate Code Matches Pattern
			if (preg_match("/^((\d){14})-CERT-(([A-Z0-9]){6})$/", $scannedCertificateCode)) {
				// Using database connection file here
	    		include 'dbConnection.php';

	    		// SQL Query to scan if the certificate code is valid
	    		$sqlScanQuery = "SELECT * FROM `certificate` WHERE `event_ID` = ? AND `certificate_code` = ?";

	    		// Fetch up the scan result
	    		$scanStmt = $conn->prepare($sqlScanQuery);
		        $scanStmt->bind_param('is', $selectedEventID, $scannedCertificateCode);
		        $scanStmt->execute();
		        $scanResult =  $scanStmt->get_result();
		        $scanStmt->close();

	    		// Check if the query is successful
	    		if ($scanResult->num_rows > 0) { // If the invitee code registered
	    			// It will out success message
	    			$output = array("scanStatus" => "success");
	    		} else { // If the invitee code not registered
					// It will output invalid code message
					$output = array("scanStatus" => "invalid");
				}
			} else {
				// It will output invalid code message
				$output = array("scanStatus" => "invalid");
			}
			
			echo json_encode($output);
		}

		/**
		 * To get certificate template file
		*/
		public function getCertificateTemplateFile($currentEventID)
		{
			// Database Connection
			include 'dbConnection.php';

			// Fetch up an certificate information
			$certificateFileStmt = $conn->prepare("SELECT * FROM `events` WHERE `ID` = ?");
	        $certificateFileStmt->bind_param('i', $currentEventID);
	        $certificateFileStmt->execute();
	        $certificateFileInfo =  $certificateFileStmt->get_result();
	        $certificateFileStmt->close();

			while ($row = $certificateFileInfo->fetch_assoc()){
				$certificateFile = $row["certificate_template"];
			}

			return $certificateFile;
		}

		/**
		 * To get the selected certificate
		*/
		public function getCertificate($selectedInviteeCode, $currentEventID)
		{
			// Get Certificate File
			$certificateFile =  $this->getCertificateTemplateFile($currentEventID);

			// Database Connection
			include 'dbConnection.php';

			// Fetch up an certificate information
			$certificateStmt = $conn->prepare("SELECT `certificate`.*, CONCAT(`invitees`.firstname, ' ', `invitees`.`middlename`, ' ', `invitees`.lastname) AS `invitee_name` FROM `certificate`, `invitees` WHERE `certificate`.`invitee_code` = ? AND `invitees`.`invitee_code` = ?");
	        $certificateStmt->bind_param('ss', $selectedInviteeCode, $selectedInviteeCode);
	        $certificateStmt->execute();
	        $certificateInfo =  $certificateStmt->get_result();
	        $certificateStmt->close();

			while ($row = $certificateInfo->fetch_assoc()){
				// Get Certificate Code
				$text = $row["certificate_code"];

				// Get Name
				$name = $row["invitee_name"];

				include "model/certificate-generator.php";

				echo json_encode(array_merge($row, array("base64CERT" => $strBase64)));
			}
		}

		/**
		 * To send a generated certificate
		*/
		public function sendCertificate($inviteeCode, $currentEventID)
		{
			// passing true in constructor enables exceptions in PHPMailer
			$mail = new PHPMailer(true);

			// Set Default Time Zone and Date Today
			date_default_timezone_set("Asia/Manila");
			$dateToday = date("F d, Y");

			// Validate if the admin logged in
    		include 'validateLogin.php';

			// Database Connection
			include 'dbConnection.php';

			// Fetch up an event information
			$eventStmt = $conn->prepare("SELECT * FROM `events` WHERE `admin_ID` = ? AND `ID` = ?");
	        $eventStmt->bind_param('ii', $id, $currentEventID);
	        $eventStmt->execute();
	        $eventInfo =  $eventStmt->get_result();
	        $eventStmt->close();
	        while ($row = $eventInfo->fetch_assoc()) {
	            $eventTitle = $row["event_title"];
	            $eventDate = date_format(date_create($row["date"]),"F d, Y");
	            $eventTimeInclusive = date_format(date_create($row["time_inclusive"]),"h:iA");
	            $eventTimeConclusive = date_format(date_create($row["time_conclusive"]),"h:iA");
	            $eventVenue = $row["venue"];
	            $certTemplate = $row["certificate_template"];
	        }

	        // Fetch up a certificate information
	        $certificateStmt = $conn->prepare("SELECT CONCAT(`invitees`.`firstname`, ' ', `invitees`.`middlename`, ' ', `invitees`.`lastname`) AS `invitee_name`, `invitees`.`email`, `certificate`.`certificate_code` FROM `invitees`, `certificate` WHERE `invitees`.`event_ID` = ? AND `certificate`.`event_ID` = ? AND `invitees`.`invitee_code` = ? AND `certificate`.`invitee_code` = ?");
	        $certificateStmt->bind_param('iiss', $currentEventID, $currentEventID, $inviteeCode, $inviteeCode);
	        $certificateStmt->execute();
	        $certificateInfo =  $certificateStmt->get_result();
	        $certificateStmt->close();
	        while ($row = $certificateInfo->fetch_assoc()){
	        	$certNameInvitee = $row["invitee_name"];
	        	$certEmailInvitee = $row["email"];
	        	$certCode = $row["certificate_code"];
	        }

	        try {
	        	// Assign Certificate Code for encoding into base64
				$text = $certCode;

				// Assign Invitee Name for embedding to certificate
				$name = $certNameInvitee;

				// Assign Certificate Template File
				$certificateFile = $certTemplate;

				// Get Generated Certificate
			    include "model/certificate-generator.php";
				
				// Get Certificate Base64
			    $certificateFileData = 'data:application/pdf;base64,'.$strBase64;

			    // Server settings
			    $mail->SMTPDebug = SMTP::DEBUG_OFF; // for detailed debug output
			    $mail->isSMTP();
			    $mail->Host = 'smtp.gmail.com';
			    $mail->SMTPAuth = true;
			    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
			    $mail->Port = 587;

			    $mail->Username = 'attend.certify@gmail.com'; // YOUR gmail email
			    $mail->Password = 'k0rnb33f19'; // YOUR gmail password

			    // Sender and recipient settings
			    $mail->setFrom('attend.certify@gmail.com', 'Attend and Certify');
			    $mail->addAddress($certEmailInvitee, $certNameInvitee);

			    // Setting the email content
			    $mail->IsHTML(true);
			    $mail->Subject = "Certificate: ". $eventTitle ." | Attend and Certify";
			    $mail->Body = include 'model/html-email-template-for-certificate.php';

			    // Add Static Attachment
				$mail->AddStringAttachment(base64_decode(substr($certificateFileData, strpos($certificateFileData, ","))), $certNameInvitee.' - '.$eventTitle.' - Certificate.pdf', 'base64', 'application/pdf');

			    $mail->send();
			    // echo "Email message sent.";

			    $response = array('Status' => "success");
				echo json_encode($response);

	        } catch(Exception $e) {
	        	$response = array('Status' => "error");
				echo json_encode($response);
	        }
		}

		/**
		 * To get a certificate config
		*/
		public function getCertConfig($currentEventID){
			// Database Connection
			include 'dbConnection.php';

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
			$certTextFontColor = "#000000";
			$certTextPositionX = 130;
			$certTextPositionY = 79;
			$certBarcodePositionX = 20;
			$certBarcodePositionY = 169;

			// Get exisiting config information
			while ($row = $certConfigInfo->fetch_assoc()){
				// Certificate Layout
				$certLayout = explode("-",$row["page_layout"]);
				$certOrientation = $certLayout[0];
				$certSize = $certLayout[1];
				// Certificate Text Style
				$certTextStyle = explode("-",$row["text_style"]);
				$certTextFont = $certTextStyle[0];
				$certTextFontStyle = $certTextStyle[1];
				$certTextFontSize = $certTextStyle[2];
				// Certificate Text Color
				$certTextFontColor = $row["text_color"];
				// Certificate Text Position
				$certTextPosition = explode(",",$row["text_position"]);
				$certTextPositionX = $certTextPosition[0];
				$certTextPositionY = $certTextPosition[1];
				// Certificate Barcode Position
				$certBarcodePosition = explode(",",$row["barcode_position"]);
				$certBarcodePositionX = $certBarcodePosition[0];
				$certBarcodePositionY = $certBarcodePosition[1];
			}

			echo json_encode(array(
				"certOrientation" => $certOrientation,
				"certSize" => $certSize,
				"certTextFont" => $certTextFont,
				"certTextFontStyle" => $certTextFontStyle,
				"certTextFontSize" => $certTextFontSize,
				"certTextFontColor" => $certTextFontColor,
				"certTextPositionX" => $certTextPositionX,
				"certTextPositionY" => $certTextPositionY,
				"certBarcodePositionX" => $certBarcodePositionX,
				"certBarcodePositionY" => $certBarcodePositionY
			));
		}

		/**
		 * To get a preview certificate
		*/
		public function getPreviewCertificate($currentEventID)
		{
			// Get Certificate File
			$certificateFile =  $this->getCertificateTemplateFile($currentEventID);

			// Sample Certificate Code
			$text = "20210101003025-CERT-CCCCCC";

			// Sample Name
			$name = "Sample Name and Barcode";

			include "model/certificate-preview.php";

			echo json_encode(array("base64CERT" => $strBase64));
		}

		/**
		 * To save a certificate config
		*/
		public function saveCertConfig($currentEventID)
		{
			// Cert Config Details
			$certLayout = $_POST["certLayout"];
			$certTextStyle = $_POST["certTextStyle"];
			$certTextFontColor = $_POST["certTextFontColor"];
			$certTextPosition = $_POST["certTextPosition"];
			$certBarcodePosition = $_POST["certBarcodePosition"];

			// Database Connection
			include 'dbConnection.php';

			// Fetch up an certificate config information
			$certConfigStmt = $conn->prepare("SELECT * FROM `certificate_config` WHERE `event_ID` = ?");
			$certConfigStmt->bind_param('i', $currentEventID);
		    $certConfigStmt->execute();
		    $certConfigInfo =  $certConfigStmt->get_result();
		    $certConfigStmt->close();

			// Check if the config exists
			if ($certConfigInfo->num_rows > 0) {
				// Update Existing Config
				$updateStmt = $conn->prepare("UPDATE `certificate_config` SET `page_layout`=?, `text_style`=?, `text_color`=?, `text_position`=?, `barcode_position`=? WHERE `event_ID` = ?");
				$updateStmt->bind_param('sssssi', $certLayout, $certTextStyle, $certTextFontColor, $certTextPosition, $certBarcodePosition, $currentEventID);
				// Validate if Update Query is successful or not
				if ($updateStmt->execute()) {
					$updateStmt->close();
					$response = array('Status' => "success");
					echo json_encode($response);
				}else{
					$response = array('Status' => "error");
					echo json_encode($response);
				}
			} else {
				// Insert New Config
				$insertStmt = $conn->prepare("INSERT INTO `certificate_config`(`event_ID`, `page_layout`, `text_style`, `text_color`, `text_position`, `barcode_position`) VALUES (?,?,?,?,?,?)");
				$insertStmt->bind_param("isssss", $currentEventID, $certLayout, $certTextStyle, $certTextFontColor, $certTextPosition, $certBarcodePosition);
				// Validate if Insert Query is successful or not
				if ($insertStmt->execute()) {
					$insertStmt->close();
					$response = array('Status' => "success");
					echo json_encode($response);
				}else{
					$response = array('Status' => "error");
					echo json_encode($response);
				}
			}
		}
	}
?>